<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class TelenorSalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:telenorsalesreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telenor Sales Report';

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
        $date = Carbon::yesterday()->format('Y-m-d');
        $response = AdminReportsEmailController::telenor_sales_report($date . ' 00:00:00');
        NotificationsController::send(96, $date, $response);
    }
}
