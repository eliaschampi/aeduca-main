<?php

namespace App\Http\Controllers;

use App\Helpers\MainHelper;
use App\Http\Requests\ProfileRequest;
use App\Repositories\ProfileRepository;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileRepository $instance;

    public function __construct(ProfileRepository $instance)
    {
        $this->instance = $instance;
    }

    public function store(ProfileRequest $request)
    {
        $original_password = $this->instance->store($request->dni, $request->image);
        return response()->json([
            "original_password" => $original_password,
            "message" => "Correctamente actualizado",
        ]);
    }

    public function changeImage(Request $request, $dni)
    {
        $request->validate([
            "image" => "required|file|mimes:png,jpg,jpeg|max:1024",
        ]);
        $file = $request->file("image");
        $profile = $this->instance->fetchByDni($dni);
        $stored = MainHelper::changeImage($profile->image, $file, $dni);
        if ($stored !== false) {
            if ($profile->image !== $stored) {
                $this->instance->changeImage($profile, $stored);
            }
            return response()->json([
                "message" => "Correctamente actualizado",
                "filename" => $stored,
            ]);
        }
        return response()->json(false, 500);
    }

    public function update($dni)
    {
        $original_password = $this->instance->update($dni);
        return response()->json([
            "original_password" => $original_password,
            "message" => "Correctamente actualizado",
        ]);
    }

    public function destroy($dni)
    {
        $this->instance->destroy($dni);
        return response()->json([
            "message" => "Correctamente eliminado",
        ]);
    }

    public function printInfo(string $dni)
    {
        $profile = $this->instance->fetchByDni($dni);
        $pdf = \PDF::loadView("pdf.aeduca", compact("profile"));
        $pdf->setPaper("A4", "portrait");
        return $pdf->download("aeduca.pdf");
    }
}
