<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminDailyVisitController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyVisitWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailyvisitweeklyreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weekly Report Send To Sale Person About Its Daily Visit';

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
        $from = Carbon::now()->subDays(6)->startOfDay()->format('Y-m-d H:i:s');
        $to = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
        $sales_persons = DB::connection('reports')->table('admins')->whereExists(function($query) {
            $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('admin_roles.department_id', '=', 7);
        })->where('admins.status', '=', 1)->select('admins.id', 'admins.name','admins.email')->get();
        if(count($sales_persons) > 0) {
            foreach ($sales_persons as $sales_person) {
                if($sales_person->email != null && $sales_person->email != "") {
                    $response = AdminDailyVisitController::daily_visit_sales_report($from, $to, $sales_person->id,str_replace(" ", "_", $sales_person->name));
                    if($response != null) {
                        NotificationsController::send(175, $sales_person->email, $response);
                    }
                }
            }
        }
    }
}
