<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\SalesPersonNumbersReportController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KAEShipmentNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kae:numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'KAE Shipment Count Day Wise';

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
        $response = SalesPersonNumbersReportController::kae_numbers_overall($date . ' 00:00:00');
        NotificationsController::send(160, $date, $response);
    }
}
