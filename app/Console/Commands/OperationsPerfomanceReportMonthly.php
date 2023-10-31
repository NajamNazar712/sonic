<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Traits\OperationReportTrait;

class OperationsPerfomanceReportMonthly extends Command
{
    use OperationReportTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:operations_perfomance_monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Cron will send records that had been created in previous month';

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
        $id = 225;
        $currentDate = Carbon::now();
        $previousMonth = $currentDate->subMonth();
        $startOfCurrentMonth = $previousMonth->startOfMonth();
        $from = $startOfCurrentMonth->toDateString();

        $currentDate = Carbon::now();
        $previousMonth = $currentDate->subMonth();
        $lastDayOfPreviousMonth = $previousMonth->endOfMonth();
        $to = $lastDayOfPreviousMonth->toDateString();

        $this->operations_performance_export_to_excel_automated($from, $to, $id);
    }
}
