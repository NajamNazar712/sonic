<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Traits\CommonTrait;

class OperationsPerfomanceReportWeekly extends Command
{
    use CommonTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:operations_perfomance_weekly';

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
        $id = 224;
        $currentDate = Carbon::now();
        $from = $currentDate->copy()->previous(Carbon::MONDAY)->toDateString();
        $to = $currentDate->copy()->previous(Carbon::THURSDAY)->toDateString();
        $this->operations_performance_export_to_excel($from, $to, $id);
    }
}
