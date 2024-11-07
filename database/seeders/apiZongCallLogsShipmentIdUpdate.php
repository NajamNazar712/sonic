<?php

namespace Database\Seeders;

use App\Http\Models\Shipment;
use App\Http\Models\Webhook\ApiZongLog;
use Illuminate\Database\Seeder;

class apiZongCallLogsShipmentIdUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        ApiZongLog::whereNull('shipment_id')->chunk(30000, function ($shipmentIds) {
            foreach ($shipmentIds as $shipment) {
                $shipmentIdDecoded = json_decode($shipment['api_request'])->tracking_number;
                $shipmentId = Shipment::where('tracking_number', $shipmentIdDecoded)->first()->id;
                $shipment->shipment_id = $shipmentId;
                $shipment->save();
            }
        });
    }
}
