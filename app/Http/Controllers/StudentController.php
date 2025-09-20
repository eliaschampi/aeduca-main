<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Repositories\PersonRepository;
use App\Repositories\StudentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private StudentRepository $instance;

    public function __construct(StudentRepository $instance)
    {
        $this->instance = $instance;
    }

    public function search(string $branch_code, string $only_current_reg, string $name): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "values" => $this->instance->search((int) $branch_code, $only_current_reg === "true", $name),
        ]);
    }

    public function show(string $dni): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json([
                "value" => $this->instance->fetch($dni)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message" => "No hay resultados para el dni"
            ], 404);
        }
    }

    public function store(PersonRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->instance->store($request->all());
        return response()->json([
            "message" => "Correctamente guardado"
        ]);
    }

    public function duplicatedDetail(string $dni): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "values" => $this->instance->duplicatedDetail($dni)
        ]);
    }

    public function updatebranch(Request $request, string $dni): \Illuminate\Http\JsonResponse
    {
        $this->instance->updatebranch($dni, $request->all());
        return response()->json([
            "message" => "Correctamente cambiado"
        ]);
    }

    public function update(PersonRequest $request, string $dni): \Illuminate\Http\JsonResponse
    {
        $personInstance = new PersonRepository();
        $personInstance->update($request->all(), $dni);
        return response()->json([
            "message" => "Correctamente actualizado"
        ]);
    }

    public function printInfo(string $dni): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $student = $this->instance->fetchForPdf($dni);
        $rt = config("main.rt");
        $cycles = config("main.cycle");
        $status = config("main.state");

        $path = storage_path("app/public/temp/");
        (new Filesystem())->cleanDirectory($path);

        $pdf = \PDF::loadView('pdf.student', compact("student", "rt", "cycles", "status"));
        $pdf->setPaper("A4", "portrait");
        $pdf->save($path . "FU - Estudiante.pdf");

        $profile = $student->person->profile;
        if (!empty($profile)) {
            $profile_pdf = \PDF::loadView("pdf.aeduca", compact("profile"));
            $profile_pdf->save($path . "Ficha Aeduca.pdf");
        }

        foreach ($student->families as $family) {
            $family_pdf = \PDF::loadView("pdf.family", compact("family", "rt"));
            $fullname = $family->person->name . " " . $family->person->lastname;
            $family_pdf->save($path . "$fullname.pdf");
        }

        $zip_file = $path . "student.zip";
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($path));
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return response()->download($zip_file);
    }

    public function fetchLatest()
    {
        return response()->json([
            "values" => $this->instance->fetchLatest()
        ]);
    }
}
