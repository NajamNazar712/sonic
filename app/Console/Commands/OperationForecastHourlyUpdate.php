<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminOperationForecastController;
use Illuminate\Console\Command;

class OperationForecastHourlyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hourlyupdate:operationforecast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Operation Forecast Hourly Update';

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
        AdminOperationForecastController::update_operation_forecast();
    }
}
