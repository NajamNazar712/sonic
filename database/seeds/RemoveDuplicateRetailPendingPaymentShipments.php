<?php

use App\Http\Models\RetailPendingPaymentShipment;
use Illuminate\Database\Seeder;

class RemoveDuplicateRetailPendingPaymentShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipment_ids = [
            28723381, 28728156, 28737250, 28751424, 28753923, 28765291, 28767267, 28773608, 28787083, 28799984, 28805395, 28805600, 28814998
        ];
        
        RetailPendingPaymentShipment::where('type', 0)
        ->whereIn('shipment_id', $shipment_ids)
        ->groupBy('shipment_id')
        ->having(DB::raw('count(shipment_id)'), '>', 1)
        ->delete();
    }
}
