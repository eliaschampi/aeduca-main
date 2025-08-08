<?php

namespace App\Http\Controllers;

use App\Repositories\AttendanceRepository;
use App\Repositories\JustificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JustificationController extends Controller
{
    protected JustificationRepository $instance;

    public function __construct(JustificationRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchByEntity(string $dni)
    {
        return response()->json([
            "values" => $this->instance->fetchByEntity($dni),
        ]);
    }

    public function downloadAttached(int $code)
    {
        $attendance = (new AttendanceRepository())->fetchByCode($code);
        $filename = $attendance->justification->attached;
        if (Storage::exists("/main/" . $filename)) {
            return Storage::download("/main/" . $filename);
        }
        return response()->json(false, 404);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "code" => "required",
            "description" => "required",
            "file" => "nullable|file|max:4096",
        ]);
        $code = $request->input("code");
        $description = $request->input("description");
        $fileName = "";
        $putFile = false;
        $hasFile = $request->hasFile("file");

        if ($hasFile) {
            $file = $request->file("file");
            $ext = $file->getClientOriginalExtension();
            $fileName = "jus_" . rand(10000, 99999) . "_$code.$ext";
            $putFile = Storage::putFileAs("/main/", $file, $fileName);
        }
        if (!$hasFile || $putFile !== false) {
            $this->instance->store($code, $fileName, $description);
            return response()->json([
                "message" => "Correctamente guardado"
            ]);
        }
        return response()->json(false, 500);
    }

    public function toggle(int $code, string $aprove)
    {
        $this->instance->toggle($code, $aprove, function ($attached) {
            if ($attached && Storage::exists("/main/" . $attached)) {
                return Storage::delete("/main/" . $attached);
            }
            return true;
        });
        return response()->json([
            "message" => "Correctamente actualizado"
        ]);
    }
}
