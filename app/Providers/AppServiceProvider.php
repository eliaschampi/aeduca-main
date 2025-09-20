<?php

namespace App\Providers;

use App\Decorators\CashDecorator;
use App\Decorators\IncomeDecorator;
use App\Interfaces\CashInterface;
use App\Interfaces\IncomeInterface;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        setlocale(LC_TIME, "es_ES.UTF-8");
        Carbon::setLocale($this->app->getLocale());

        $this->app->bind(IncomeInterface::class, IncomeDecorator::class);
        $this->app->bind(CashInterface::class, CashDecorator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
