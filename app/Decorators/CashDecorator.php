<?php

namespace App\Decorators;

use App\Cache\CashCache;
use App\Interfaces\CashInterface;
use App\Models\Cash;
use App\Repositories\CashRepository;

class CashDecorator implements CashInterface
{
    protected CashRepository $cash;

    public function __construct(CashRepository $cash)
    {
        $this->cash = $cash;
    }

    public function fetchByMonth(string $month)
    {
        return $this->cash->fetchByMonth($month);
    }

    public function lastCash(): array
    {
        return $this->cash->lastCash();
    }

    public function fetchSimple(): ?Cash
    {
        return CashCache::getCash(fn () => $this->cash->fetchSimple());
    }

    public function fetch(string $user_code, string $date): ?Cash
    {
        return $this->cash->fetch($user_code, $date);
    }

    public function fetchChart()
    {
        $data = $this->cash->fetchChart();
        $months = [];
        $acu_mount = 0;
        $acum = [];
        $surr = [];

        $monthnames = [
            "1" => "Enero",
            "2" => "Febrero",
            "3" => "Marzo",
            "4" => "Abril",
            "5" => "Mayo",
            "6" => "Junio",
            "7" => "Julio",
            "8" => "Agosto",
            "9" => "Setiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre",
        ];
        foreach ($data as $value) {
            $months[] = $monthnames[$value->month];
            $surr[] = $value->sum;
            $acu_mount += floatval($value->sum);
            $acum[] = $acu_mount;
        }
        return [
            "months" => $months,
            "acum" => $acum,
            "surr" => $surr,
        ];
    }

    public function surrender($data, $cash_code): bool
    {
        $val = $this->cash->surrender($data, $cash_code);
        CashCache::forgetCache();
        return $val;
    }

    public function openCash(string $code, string $cash): Cash
    {
        CashCache::forgetCache();
        return $this->cash->openCash($code, $cash);
    }

    public function toggleCash(string $code): bool
    {
        $val = $this->cash->toggleCash($code);
        CashCache::forgetCache();
        return $val;
    }

}
