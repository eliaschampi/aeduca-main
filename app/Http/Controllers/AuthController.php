<?php

namespace App\Http\Controllers;

use App\Mail\PasswordGeneratedMailable;
use App\Repositories\SignHistoryRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $instance;

    public function __construct(UserRepository $instance)
    {
        $this->instance = $instance;
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|max:100",
            "password" => "required|max:100",
        ]);

        $credentials = $request->only("email", "password");
        $user = $this->instance->fetchByEmail($request->input("email"));
        $token = null;

        if (!$user) {
            return response()->json([
                "message" => "Usuario no encontrado",
            ], 404);
        }

        if (($user->state === false)) {
            return response()->json([
                "message" => "Acceso no autorizado",
            ], 401);
        }

        try {
            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return response()->json([
                    "message" => "Contraseña Incorrecta",
                ], 422);
            }
            $signHistory = new SignHistoryRepository();
            $signHistory->store($request->userAgent(), $token);

        } catch (JWTException $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            "success" => true,
            "access_token" => $token,
            "token_type" => "bearer",
            "user" => $user,
            "expires_in" => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function logout(Request $request)
    {
        $signHistory = new SignHistoryRepository();
        $sign = $signHistory->fetchByCodeAndAgent($request->userAgent(), auth()->user()->code);
        if ($sign) {
            $signHistory->delete($sign);
        }
        auth()->logout();
        return response()->json(["success" => true]);
    }

    public function recover(Request $request)
    {
        $request->validate([
            "email" => "required|email|max:100",
            "dni" => "required|min:8|max:8",
        ]);

        $user = $this->instance->fetchByDni($request->dni);

        if ($request->input("email") === $user->email) {
            $newPass = Str::random(10);
            if ($this->instance->changePassword($user, $newPass)) {
                Mail::to($user->email)->send(new PasswordGeneratedMailable($newPass));
                return response()->json([
                    "message" => "Se ha enviado un correo de recuperacion de clave.",
                ]);
            }
        }
        return response()->json(false, 500);
    }

    public function check(Request $request)
    {
        return response()->json([
            "valid" => auth()->validate($request->all()),
        ]);
    }

    public function refresh(Request $request)
    {
        $response = [
            "user" => null,
            "valid" => false,
            "hasuser" => true
        ];

        $user = $this->instance->fetchByEmail($request->input("email"));
        $signHistory = new SignHistoryRepository();
        $sign = $signHistory->fetchByCodeAndAgent($request->userAgent(), $user->code);
        if ($sign) {
            if (auth()->validate($request->all())) {
                $token = auth()->refresh();
                $signHistory->updateToken($sign, $token);
                $user["access_token"] = $token;
                $response["user"] = $user;
                $response["valid"] = true;
            }
        }
        return response()->json($response);
    }

}
