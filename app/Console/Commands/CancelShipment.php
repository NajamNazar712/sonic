<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Admins\AdminCancelController;

class CancelShipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shipments Auto Cancellation';

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
        AdminCancelController::cancel();
    }
}
