<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminRetailReportController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RetailSalesReportRemainingDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:retailsalesreportremainingdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retail Sales Report By Arrival from 26th to 31st';

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
        $response = AdminRetailReportController::retail_sales_report(3);
        if($response){
            NotificationsController::send(206, $response);
        }
    }
}
