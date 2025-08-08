<?php

namespace App\Interfaces;

use App\Models\Cash;

interface CashInterface
{
    public function fetchByMonth(string $month);

    public function fetchChart();

    public function lastCash(): array;

    public function fetchSimple(): ?Cash;

    public function fetch(string $user_code, string $date): ?Cash;
    public function surrender(array $data, string $cash_code): bool;

    public function openCash(string $code, string $cash): Cash;

    public function toggleCash(string $code): bool;
}
