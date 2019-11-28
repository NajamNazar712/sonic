<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SalePersonShipmentNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saleperson:numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sale Person Shipment Count Day Wise';

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
        $response = AdminReportsEmailController::sale_person_numbers($date . ' 00:00:00');
        NotificationsController::send(47, $date, $response);
    }
}
