<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\VisionSoftAPIController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class visionsoftmultiple extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:visionsoftexcel_multiple';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vision Soft Excel';

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
        // Set the start and end dates for September 2024
        $startDate = Carbon::create(2024, 7, 01)->startOfDay();
        $endDate = Carbon::create(2024, 8, 31)->endOfDay();

        // Loop through each day in September 2024
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {

            $st = Carbon::create($date)->format('Y-m-d 00:00:00');
            $end = Carbon::create($date)->format('Y-m-d 23:59:59');

            $cod_payment_excel = VisionSoftAPIController::cod_payable_excel($st,$end);
//            $cod_receivable_excel = VisionSoftAPIController::cod_receivable_excel($st,$end);
            $links = "";

//            if(!empty($cod_receivable_excel)) {
//                $links .= "<strong>COD Receivables: </strong> <br>" . "<a download='$cod_receivable_excel' href='$cod_receivable_excel' >$cod_receivable_excel</a>" . "<br>";
//            }
            if(!empty($cod_payment_excel)) {
                $links .= "<strong>COD Payable: </strong> <br>" . "<a download='$cod_payment_excel' href='$cod_payment_excel' >$cod_payment_excel</a>" . "<br>";
            }
//            if(!empty($cod_payment_excel) || !empty($cod_receivable_excel)) {
//                NotificationsController::send(213, $links);
//            }
            if(!empty($cod_payment_excel)) {
                NotificationsController::send(213, $links);
            }
        }


    }
}
