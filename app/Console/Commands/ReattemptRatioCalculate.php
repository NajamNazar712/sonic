<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShipperReattemptRatioController;
use Illuminate\Console\Command;

class ReattemptRatioCalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:reattemptpercentage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-attempt Percentage Calculate';

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
        ShipperReattemptRatioController::reattempt_ratio_calculate();
    }
}
