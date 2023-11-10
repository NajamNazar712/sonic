<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Shipment;
use Illuminate\Console\Command;

class CreatePaymentsForMissingShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:payments {shipment_ids}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Payment Command for missing payments in make payment';

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
        $shipment_ids = $this->argument('shipment_ids');
        dd($shipment_ids);
        $shipment_ids = explode(',', $shipment_ids);
        if(count($shipment_ids) > 0){
            foreach ($shipment_ids as $shipment_id){
                $shipment = Shipment::find($shipment_id);
                if($shipment){
                    if (in_array($shipment->shipper_status_id, [14, 16, 30, 31, 36, 37])) {

                        if ($shipment->booking_type_id == 2) {
                            ShipmentChargesController::replacement($shipment_id);
                        } else if ($shipment->booking_type_id == 3) {
                            ShipmentChargesController::try_and_buy($shipment_id);
                        }

                        if ($shipment->booking_type_id != 4) {
                            AdminFinanceController::add_payment($shipment_id, 0);
                        } else {
                            AdminFinanceController::done_payment($shipment_id, 0);
                        }

                    }
                }
            }
        }
    }
}
