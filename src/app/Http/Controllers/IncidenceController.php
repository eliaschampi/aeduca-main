<?php

namespace App\Http\Controllers;

use App\Repositories\IncidenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IncidenceController extends Controller
{
    protected IncidenceRepository $instance;

    public function __construct(IncidenceRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchByMonth(string $month)
    {
        return response()->json([
            "values" => $this->instance->fetchByMonth($month),
        ]);
    }

    public function fetchByEntity(string $dni)
    {
        return response()->json([
            "values" => $this->instance->fetchByEntity($dni),
        ]);
    }

    private function beforeUpsert(array $request)
    {
        if ($request["type"] === "pe") {
            $pdate = $request["agreement"];

            if (count($request["persons"]) > 1) {
                throw new \Exception("Registra permiso individualmente.");
            }

            $person = $request["persons"][0];
            $etype = $person["entity_type"];
            $dni = $person["dni"];
            $pdays = is_numeric($request["extra"]) ? intval($request["extra"]) - 1 : 0;

            $ai = new \App\Repositories\AttendanceRepository();
            $ai->upsertBeforeInpect($pdate, $pdays, $etype, $dni);
        }
    }

    public function store(Request $request)
    {
        $fileHasBeenStored = false;
        $res = json_decode($request->data, true);
        Validator::make($res, [
            "type" => "required",
            "persons" => "",
            "title" => "required|max:100",
            "description" => "required|min:10|max:1000",
            "agreement" => "required|min:5|max:300",
            "created_at" => "required",
            "extra" => "",
        ]);
        $filename = "";
        $hasFile = $request->hasFile("file");

        if ($hasFile) {
            $this->validate($request, [
                "file" => "required|file|max:4096",
            ]);
            $file = $request->file("file");
            $ext = $file->getClientOriginalExtension();
            $filename = "ins_" . rand(10000, 99999) . ".$ext";
            $fileHasBeenStored = Storage::putFileAs("/main/", $file, $filename);
        }

        if (!$hasFile || $fileHasBeenStored !== false) {
            $this->beforeUpsert($res);
            $this->instance->store($res, $filename);
            return response()->json([
                "message" => "Correctamente registrado",
            ]);
        }

        return response()->json(false, 500);
    }

    public function update(Request $request, int $code)
    {
        $request->validate([
            "type" => "required",
            "title" => "required|max:100",
            "description" => "required|min:10|max:1000",
            "agreement" => "required|min:5|max:300",
            "created_at" => "",
            "extra" => ""
        ]);
        $this->instance->update($request->all(), $code);
        return response()->json([
            "message" => "Correctamente actualizado",
        ]);
    }

    public function destroy(int $code)
    {
        $incidence = $this->instance->fetchByCode($code);
        $fileHasBeenDeleted = true;

        $attached = $incidence->file_attached;

        if ($incidence->type === "pe") {
            $ai = new \App\Repositories\AttendanceRepository();
            $entity_identifier = $incidence->persons()->first()->dni;
            $ai->destroyInspecteds($entity_identifier, $incidence->agreement, $incidence->extra);
        }

        if (!empty($attached)) {
            $fileHasBeenDeleted = Storage::delete("/main/" . $attached);
        }

        if ($fileHasBeenDeleted) {
            $this->instance->destroy($incidence);
            return response()->json([
                "message" => "Correctamente eliminado",
            ]);
        }
        return response()->json(false, 500);
    }

    public function downloadAttached(int $code)
    {
        $incidence = $this->instance->fetchByCode($code);
        $filename = $incidence->file_attached;
        if (Storage::exists("/main/" . $filename)) {
            return Storage::download("/main/" . $filename, "adj.pdf", [
                "Content-Type" => "application/pdf",
            ]);
        }
        return response()->json(false, 404);
    }

    public function print($code)
    {
        $incidence = $this->instance->fetchByCode($code);
        $title = config("main.ins.$incidence->type");
        $pdf = \PDF::loadView("pdf.incidence", compact("incidence", "title"));
        $pdf->setPaper("A4", "portrait");
        return $pdf->download("adj.pdf");
    }

}
