<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use Carbon\Carbon;
use Illuminate\Console\Command;


class RetailDonePaymentReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:retaildonepayment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retail Done Payment Report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::today()->subDay(1)->toDateString();
        AdminReportsEmailController::retail_done_payment($date);
    }
}
