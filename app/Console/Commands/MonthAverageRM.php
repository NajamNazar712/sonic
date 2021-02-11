<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\MonthAverateReportsController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MonthAverageRM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'month:averagerm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Average Shipments of Month Day Wise Regional Manager';

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
        $regional_managers = DB::connection('reports')->table('admins')->whereIn('role_id', [31,44])->where('status', 1)->select('id', 'name')->get();
        if(count($regional_managers) > 0){
            foreach ($regional_managers as $regional_manager){
                $response = MonthAverateReportsController::month_average_rm($date . ' 00:00:00', $regional_manager->id);
                NotificationsController::send(125, $regional_manager->id, $response);
            }
        }
    }
}
