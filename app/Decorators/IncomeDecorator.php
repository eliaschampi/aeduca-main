<?php

namespace App\Decorators;

use App\Cache\CashCache;
use App\Cache\IncomeCache;
use App\Cache\RegisterCache;
use App\Interfaces\IncomeInterface;
use App\Jobs\ProcessPaymentDate;
use App\Models\Income;
use App\Repositories\IncomeDetailRepository;
use App\Repositories\IncomeRepository;
use App\Repositories\RegisterRepository;
use Illuminate\Support\Facades\DB;

class IncomeDecorator implements IncomeInterface
{
    protected $income;

    public function __construct(IncomeRepository $income)
    {
        $this->income = $income;
    }

    public function fetchByDates(string $from, string $to, string $user_code)
    {
        return $this->income->fetchByDates($from, $to, $user_code);
    }

    public function canceleds()
    {
        return $this->income->canceleds();
    }

    public function store(array $data): Income
    {
        try {
            DB::beginTransaction();

            $details = IncomeCache::fetchFromCache();
            $register = RegisterCache::fetchFromCache();
            $responseRegister = (new RegisterRepository())->storeOrUpdate($register);

            if ($responseRegister !== false) {
                $data["customer_identifier"] = $responseRegister;
            }

            $income = $this->income->store($data);
            $checkForDispatch = $data["mod"] === "student" && $register === false;

            if ($details !== null) {
                $incomeDetail = (new IncomeDetailRepository());
                foreach ($details as $value) {
                    $incomeDetail->store($value, $income->code);
                    if ($checkForDispatch) {
                        ProcessPaymentDate::dispatch(
                            $value["actiontype"]["code"],
                            $data["customer_identifier"],
                            $value["title"]
                        );
                    }
                }
            }
            IncomeCache::forget();
            CashCache::forgetCache();
            if (!empty($data["section_code"])) {
                RegisterCache::forgetCache();
            }
            DB::commit();
            return $income;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new \Exception("Error Procesando el Ingreso: " . $ex->getMessage());
        }
    }

    public function canceled(array $data, $code): bool
    {
        $can = $this->income->canceled($data, $code);
        CashCache::forgetCache();
        return $can;
    }
}
