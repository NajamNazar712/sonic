<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Models\Shipment;

class AutoShipmentArrival extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:shipmentarrival';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Shipment Arrival';

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
        $shipments = Shipment::where('user_id', 3324)->where('shipper_status_id', 1);   //4213

        if ($shipments->exists()) {
            $shipments = $shipments->get();

            foreach ($shipments as $shipment) {
//                AdminPickupsController::cancel($shipment->id);

                $status_id = 2;

                ShipmentsJourneyController::add($shipment->id, $status_id, $status_id, NULL, NULL, NULL, 70);

//                if ($shipment->pickup_address->city_id != $shipment->consignee_city_id) {
//                    $status_id = 4;
//
//                    ShipmentsJourneyController::add($shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);
//                }

                $shipment->shipper_status_id = $status_id;
                $shipment->consignee_status_id = $status_id;
                $shipment->actual_weight = 0.2; //0.5

                $shipment->save();

                ShipmentChargesController::weight($shipment->id);
                ShipmentChargesController::cash_handling($shipment->id);
                ShipmentChargesController::insurance($shipment->id);
                ShipmentChargesController::fuel_surcharge($shipment->id);
            }
        }
    }
}
