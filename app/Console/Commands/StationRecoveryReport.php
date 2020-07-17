<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\StationRecoveryController;
use Illuminate\Console\Command;

class StationRecoveryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:stationrecovery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Station Recovery Report Data add cron';

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
        StationRecoveryController::station_recovery_data();
    }
}
