<?php

namespace App\Http\Controllers;

use App\Repositories\SignHistoryRepository;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Token;

class SignHistoryController extends Controller
{
    protected SignHistoryRepository $instance;

    public function __construct(SignHistoryRepository $instance)
    {
        $this->instance = $instance;
    }

    public function index()
    {
        return response()->json([
            "values" => $this->instance->fetchByUser()
        ]);
    }

    public function destroyToken(Request $request, int $code)
    {
        try {
            $sign = $this->instance->fetchByCode($code);
            $this->instance->delete($sign);
            JWTAuth::manager()->invalidate(new Token($sign->token), true);
            return response()->json([
                "message" => "Token eliminado correctamente"
            ]);
        } catch (TokenExpiredException $exeption) {
            return response()->json([
                "message" => $exeption->getMessage()
            ], 200);
        }
    }
}
