<?php

namespace App\Helpers;

use App\Cache\ConfigCache;

/**
 *  @author Elias
 */
class RegisterHelper
{
    public static function consv(string $section_code, string $student_name)
    {
        $cycle = (new \App\Repositories\CycleRepository())->fetchByCode(substr($section_code, 0, 8));
        $degree = intval(substr($section_code, -2, 1));
        $fulldegree = $degree . "ro";

        if ($degree > 3) {
            $fulldegree = $degree . "to";
        } elseif ($degree === 2) {
            $fulldegree = $degree . "do";
        }
        $ins_name = ConfigCache::getCompanyData()["name"];
        $data = [
            "name" => $ins_name,
            "student" => $student_name,
            "full_cycle" => $cycle->full_name,
            "full_degree" => $fulldegree,
            "modular_code" => $cycle->modular_code,
            "section" => substr($section_code, -1)
        ];

        $pdf = \PDF::loadView("pdf.consv", compact("data"));
        $pdf->setPaper("A4", "portrait");
        return $pdf->download("consv.pdf");
    }

}
