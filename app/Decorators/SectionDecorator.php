<?php

namespace App\Decorators;

use App\Repositories\SectionRepository;

class SectionDecorator
{
    private function formatSectionName($item)
    {
        return sprintf(
            "%s(%s) de %s - %s",
            $item["degree"]["full_name"],
            $item["name"],
            $item["degree"]["cycle"]["full_name"],
            $item["degree"]["cycle"]["title"]
        );
    }

    public function decoratedSections()
    {
        $instance = new SectionRepository();
        $values = $instance->fetchByYearAndBranch();
        return $values->map(function ($item) {
            return [
                "code" => $item["code"],
                "full_name" => $this->formatSectionName($item),
                "monthly" => $item["degree"]["cycle"]["monthly"],
            ];
        });
    }

}
