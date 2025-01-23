<?php

namespace App\Console\Commands;

use App\ApolloCronJobLog;
use App\Http\Controllers\Admins\CronControllers\ApolloShipmentCronController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApolloShipmentFetchStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apollo:fetch-shipments-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches the shipment status for Apollo shipments.';

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
        ApolloShipmentCronController::fetch_shipment_statuses();
    }
}
