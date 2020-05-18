<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NotificationsController;

class CancelledShipmentEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipmentemail:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancelled Shipment Email';

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
        $date = \Carbon\Carbon::yesterday()->format('Y-m-d');
        NotificationsController::send(62,$date, 0);
    }
}
