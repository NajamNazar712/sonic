<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class ShipmentPieceOnHold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:onholdtoshipper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command To Send emails to shipper for last day multiple piece requests';

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
        NotificationsController::send(84, 0);
    }
}
