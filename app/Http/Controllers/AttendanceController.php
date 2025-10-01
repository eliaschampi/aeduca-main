<?php

namespace App\Http\Controllers;

use App\Cache\ConfigCache;
use App\Cache\CycleCache;
use App\Exports\AttendanceBySectionExport;
use App\Repositories\AttendanceRepository;
use App\Repositories\PersonRepository;
use App\Repositories\RegisterRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceRepository $instance;

    public function __construct(AttendanceRepository $instance)
    {
        $this->instance = $instance;
    }

    public function fetchBySection(string $section_code, string $date, string $priority)
    {
        return response()->json([
          "values" => $this->instance->fetchBySectionAndDate($section_code, $date, (int) $priority),
        ]);
    }

    public function fetchForTeacherByDate(string $date)
    {
        return response()->json([
          "values" => $this->instance->fetchForTeacherByDate($date),
        ]);
    }

    public function fetchByEntity(string $entity_identifier, string $from, string $to, string $priority)
    {
        return response()->json([
          "values" => $this->instance->fetchByEntity($entity_identifier, $from, $to, (int) $priority),
        ]);
    }

    public function fetchAbsences(string $cycle, string $date, string $priority)
    {
        return response()->json([
          "values" => $this->instance->absences($cycle, $date, (int) $priority),
        ]);
    }

    public function fetchForChart()
    {
        $data = $this->instance->fetchForChart();
        $days = [];
        $count = [];
        foreach ($data as $value) {
            if (intval($value->count) > 20) {
                $days[] = $value->mday;
                $count[] = $value->count;
            }
        }
        return response()->json([
          "days" => $days,
          "count" => $count,
        ]);
    }

    public function store(Request $request)
    {
        $state = $request->input("state");
        $entity_identifier = $request->input("entity_identifier");
        $entity_type = $request->input("entity_type");
        $priority = $request->input("priority");
        $time = $state === "falta" || $state === "permiso" ? null : $request->input("entry_time");
        if ($this->instance->todayIsAlreadyRegistered($entity_identifier, $priority)) {
            return response()->json([
              "message" => "Su asistencia ya ha sido registrado"
            ], 402);
        }
        $this->instance->store(
            $entity_identifier,
            $entity_type,
            $state,
            $time,
            $priority
        );
        return response()->json([
          "message" => "Correctamente registrado"
        ]);
    }

    public function auto(Request $request)
    {
        $dni = $request->input("dni");
        $type = $request->input("type");
        $priority = (int) $request->input("priority"); // CAST a INTEGER
    
        if (!preg_match("/^[0-9]{8}$/", $dni)) {
            return response()->json([
                "status" => false,
                "message" => "Su dni es incorrecto",
            ], 422);
        }
    
        $person = (new PersonRepository())->fetchSingle($dni);
    
        if (!$person) {
            return response()->json([
                "status" => false,
                "message" => "Usuario no encontrado",
            ], 422);
        }
    
        $attconfig = null;
        $register = null;
    
        if ($type === "s") {
            $register = (new RegisterRepository())->fetchCurrent($dni);
    
            if (empty($register)) {
                return response()->json([
                    "status" => false,
                    "message" => "Este año no está matrículado",
                ], 422);
            }
    
            $cycle_code = substr($register->section_code, 0, 8);
            $cacheds = CycleCache::attendanceVariablesShouldBeCached($cycle_code);
            
            // Buscar configuración por priority (ahora ambos son INT)
            $attconfig = collect($cacheds)->firstWhere('order', $priority);
    
            if (!$attconfig) {
                return response()->json([
                    "status" => false,
                  "message" => "Horario de ingreso no habilitado",
                ], 422);
            }
    
            // Validar turno del estudiante
            if ($register->priority !== 0 && $register->priority !== $priority) {
                return response()->json([
                    "status" => false,
                  "message" => "No pertenece a este turno",
                ], 422);
            }
        } else {
            if (!(new TeacherRepository())->teacherIsActive($dni)) {
                return response()->json([
                    "status" => false,
                    "message" => "Docente no habilitado",
                ], 422);
            }
            $attconfig = ConfigCache::getCoreConfig();
        }
    
        // check if today is already registered
        if ($this->instance->todayIsAlreadyRegistered($dni, $priority)) {
            return response()->json([
                "status" => "yet.mp3",
                "register" => $register,
                "person" => $person,
                "message" => "Su asistencia ya ha sido registrado",
            ]);
        }
    
        $message = "Registrado como ";
        $currentTime = now();
        $state = "presente";
        
        // Convertir array a objeto para consistencia
        $attconfig = is_array($attconfig) ? (object) $attconfig : $attconfig;
        
        $entry_time = today()->setTimeFromTimeString($attconfig->entry_time);
    
        if ($currentTime->greaterThanOrEqualTo($entry_time->addMinutes(intval($attconfig->tolerance) + 1))) {
            $state = "tarde";
        }
    
        $this->instance->store($dni, $type, $state, date('H:i:s'), $priority);
    
        $message .= $state;
    
        return response()->json([
            "status" => "ok.mp3",
            "message" => $message,
            "register" => $register,
            "person" => $person,
        ]);
    }

    public function update(Request $request, int $code)
    {
        $this->instance->update($request->entry_time, $request->state, $code);
        return response()->json([
          "message" => "Registro actualizado"
        ]);
    }

    public function exportCV(string $dni, string $from, string $to, string $priority)
    {
        $reg = (new RegisterRepository())->fetchLatest($dni);
        $attendances = $this->instance->fetchByEntity(
            $dni,
            $from,
            $to,
            $priority
        );
        $pdf = \PDF::loadView("pdf.attendance", compact("attendances", "from", "to", "reg"));
        $pdf->setPaper("A4", "portrait");
        return $pdf->download("adj.pdf");
    }

    public function exportToExcelBySection(string $section_code, string $date, string $priority)
    {
        return new AttendanceBySectionExport($section_code, $date, intval($priority));
    }
}
