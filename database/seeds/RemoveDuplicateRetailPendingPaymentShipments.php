<?php

use App\Http\Models\RetailPendingPayment;
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
        $pps = RetailPendingPaymentShipment::where('type', 0)
        ->groupBy('shipment_id')
        ->having(DB::raw('count(shipment_id)'), '>', 1)
        ->get();

        foreach ($pps as $key => $shipment) {

            $new_pps = RetailPendingPaymentShipment::where('type', 0)->where('shipment_id', $shipment->shipment_id)
            ->where('retail_pending_payment_id', $shipment->retail_pending_payment_id)->get();

            $count = count($new_pps);
            if($count> 1){

                foreach ($new_pps as $key => $value) {
                    if($key != 0){
                        $value->delete();
                        RetailPendingPayment::where('id', $value->retail_pending_payment_id)->decrement('total_shipments',1);
                        RetailPendingPayment::where('id', $value->retail_pending_payment_id)->decrement('delivered_shipments',1);
                    } 
                }
            }
        }
    }
}
