<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentDate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private int $pcode;

    private string $register_code;

    private string|null $month;

    public function __construct(int $pcode, string $register_code, string|null $month)
    {
        $this->pcode = $pcode;
        $this->register_code = $register_code;
        $this->month = $month;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \App\Repositories\PaymentRepository::togglePaid($this->pcode, $this->register_code, $this->month);
    }
}
