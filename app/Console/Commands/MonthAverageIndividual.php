<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\MonthAverateReportsController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthAverageIndividual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'month:averageindividual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
                $response = MonthAverateReportsController::month_average_individual($date . ' 00:00:00', $sales_person->id);
                NotificationsController::send(124, $sales_person->id, $response);
            }
        }
    }
}
