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
    protected $signature = 'reports:operations_performance_monthly {mode?}';

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
        $mode = $this->argument('mode'); // Access the 'mode' argument
        $currentDate = Carbon::now();
        $previousMonth = $currentDate->subMonth(); 
        $startOfPreviousMonth = $previousMonth->startOfMonth();
        $from = $startOfPreviousMonth->toDateString();
        
        $lastDayOfPreviousMonth = $previousMonth->endOfMonth();
        $to = $lastDayOfPreviousMonth->toDateString();
        
        $this->operations_performance_export_to_excel_automated($from, $to, $id, $mode);
    }
}
