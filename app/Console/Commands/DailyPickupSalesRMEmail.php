<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\DailyPickupSalesReportController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyPickupSalesRMEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailypickupsalesreportrm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Pickup & Sales Report Email to Regional Managers';

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
        $reagional_managers = DB::connection('reports')->table('admins')->whereIn('role_id', [31,44])->where('status', 1)->select('id', 'name')->get();
        if(count($reagional_managers) > 0){
            foreach ($reagional_managers as $reagional_manager){
                $response = DailyPickupSalesReportController::daily_pickup_sales_report_rm( $date . ' 00:00:00', $reagional_manager->id);
                NotificationsController::send(121,$reagional_manager->id,$response);
            }
        }
    }
}
