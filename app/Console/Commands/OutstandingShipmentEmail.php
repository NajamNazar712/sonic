<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OutstandingShipmentEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:outstandingshipments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Outstanding Shipment Report';

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
        $start_date = Carbon::yesterday()->startOfDay()->toDateTimeString();
        $end_date = Carbon::yesterday()->endOfDay()->toDateTimeString();
        $response = AdminReportsEmailController::outstanding_shipments($start_date, $end_date);
    }
}
