<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ExtraInfoRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ExtraInfoController extends Controller
{
    protected ExtraInfoRepository $instance;

    public function __construct(ExtraInfoRepository $instance)
    {
        $this->instance = $instance;
    }

    public function show(string $student_dni)
    {
        $values = $this->instance->fetchByStudent($student_dni);
        return response()->json([
            "values" => $values,
        ]);
    }

    public function edit(string $code)
    {
        $instance = $this->instance->fetchOne((int) $code);
        if (!empty($instance->attached)) {
            if (Storage::exists("/main/" . $instance->attached)) {
                $headers = ["Content-Type" => "application/pdf"];
                return Storage::download("/main/" . $instance->attached, "adj.pdf", $headers);
            }
        }
        return response()->json(false, 404);
    }

    public function store(Request $request)
    {
        $fileHasBeenStored = false;
        $res = json_decode($request->data, true);
        Validator::make($res, [
            "student_dni" => "required",
            "type" => "required",
            "info" => "required|max:150",
            "attached" => "",
        ]);
        $filename = $res["attached"];
        $hasFile = $request->hasFile("file");
        if ($hasFile) {
            $this->validate($request, [
                "file" => "required|file|max:4096",
            ]);
            $file = $request->file("file");
            $ext = $file->getClientOriginalExtension();
            $filename = "doc_" . rand(10000, 99999) . ".$ext";
            $fileHasBeenStored = Storage::putFileAs("/main/", $file, $filename);
        }

        if (!$hasFile || $fileHasBeenStored !== false) {
            $res["attached"] = $filename;
            $this->instance->store($res);
            return response()->json([
                "message" => "Correctamente registrado"
            ]);
        }
    }

    public function update(Request $request, string $code)
    {
        $request->validate([
            "type" => "required",
            "info" => "required|max:150"
        ]);

        $info = $request->input("info");
        $attached = $request->input("attached");
        $this->instance->update((int) $code, $info, $attached);

        return response()->json([
            "message" => "Correctamente actualizado",
        ]);
    }

    public function destroy(string $code)
    {
        $deleted = false;
        $instance = $this->instance->fetchOne((int) $code);
        $attached = $instance->attached;
        if (!empty($attached)) {
            $deleted = Storage::delete("/main/" . $attached);
        }
        if (empty($attached) || $deleted) {
            $this->instance->destroy((int) $code);
            return response()->json([
                "message" => "Correctamente eliminado",
            ]);
        }
        return response()->json(false, 500);
    }

}
