<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\RiderManagementController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RiderIncentiveCalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incentive:riders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Incentive Calculation Daily';

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

        RiderManagementController::riders_incentives_calculation($date. ' 00:00:00');

    }
}
