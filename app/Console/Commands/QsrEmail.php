<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class QsrEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:qsrreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quality of Service Report';

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
        // $start_date = Carbon::yesterday()->startOfDay()->addHours(10)->toDateTimeString();
        // $end_date = Carbon::today()->startOfDay()->addHours(10)->toDateTimeString();

        $start_date = Carbon::yesterday()->subYear()->toDateTimeString();
        $end_date = Carbon::today()->toDateTimeString();
        
        // $adminReportsController = new AdminReportsController();
        // $response = $adminReportsController->qsr_list($request);

        //send report in email
        // Please download Quality of Service Report from the following link: [link].
        
        // $date = Carbon::yesterday()->format('Y-m-d');
        // NotificationsController::send(111, $date, $response);


        $response = AdminReportsEmailController::qsr_daily_report($start_date, $end_date);
        dd($response);
        
        NotificationsController::send(226, $response);

    }
}
