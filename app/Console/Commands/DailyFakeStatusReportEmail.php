<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyFakeStatusReportEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailyfakestatusreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Fake Status Report';

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
        $response = AdminReportsEmailController::daily_fake_status($date . ' 00:00:00');
    }
}
