<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminNsaAccountShipmentController;
use Illuminate\Console\Command;

class NsaShipmentProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nsa:accountshipments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nsa Accounts shipments to be return confirmed';

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
        AdminNsaAccountShipmentController::nsa_account_shipment_process();
    }
}
