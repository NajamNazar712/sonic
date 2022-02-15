<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TelenorEmailDailyAboutShipmentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telenor:shipmentStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Shipment Status To Telenor';

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
        $from = Carbon::now()->startOfDay()->subDays(1);
        $to = Carbon::now()->endOfDay()->subDays(1);
        NotificationsController::send(168,$from,$to);
    }
}
