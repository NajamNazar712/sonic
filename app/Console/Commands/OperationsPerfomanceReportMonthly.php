<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Traits\CommonTrait;

class OperationsPerfomanceReportMonthly extends Command
{
    use CommonTrait;
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
        $id = 225;
        $currentDate = Carbon::now();
        $eleventhOfCurrentMonth = $currentDate->day(11);
        $to = $eleventhOfCurrentMonth->toDateString();

        $currentDate = Carbon::now();
        $previousMonth = $currentDate->subMonth();
        $eleventhOfPreviousMonth = $previousMonth->day(11);
        $from = $eleventhOfPreviousMonth->toDateString();

        $this->operations_performance_export_to_excel($from, $to, $id);
    }
}
