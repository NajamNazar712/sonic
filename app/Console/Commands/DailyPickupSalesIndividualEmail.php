<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\DailyPickupSalesReportController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyPickupSalesIndividualEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailypickupsalesreportindividual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Pickup & Sales Report Email to Sales Persons';

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
        $sales_persons = DB::connection('reports')->table('admins')->whereExists(function($query) {
            $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('department_id', '=', 7);
        })->select('id', 'name')->get();
        if(count($sales_persons) > 0){
            foreach ($sales_persons as $sales_person){
                $response = DailyPickupSalesReportController::daily_pickup_sales_report_individual( $date . ' 00:00:00', $sales_person->id);
                NotificationsController::send(120, $sales_person->id, $response);
            }
        }
    }
}
