<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Traits\OperationReportTrait;

class OperationsPerfomanceReportWeekly extends Command
{
    use OperationReportTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:operations_performance_weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Cron will send records that had been created in previous week (as mentioned in requirements)';

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
        $id = 224;
        $currentDate = Carbon::now();
        $from = $currentDate->copy()->previous(Carbon::FRIDAY)->previous(Carbon::FRIDAY);
        $to = $from->copy()->next(Carbon::THURSDAY)->toDateString();
        $from = $from->toDateString();
        $this->operations_performance_export_to_excel_automated($from, $to, $id);
    }
}
