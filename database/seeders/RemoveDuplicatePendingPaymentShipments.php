<?php

use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use Illuminate\Database\Seeder;

class RemoveDuplicatePendingPaymentShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pps = PendingPaymentShipment::where('type', 0)
        ->groupBy('shipment_id')
        ->having(DB::raw('count(shipment_id)'), '>', 1)
        ->get();

        foreach ($pps as $key => $shipment) {

            $new_pps = PendingPaymentShipment::where('type', 0)->where('shipment_id', $shipment->shipment_id)
            ->where('pending_payment_id', $shipment->pending_payment_id)->get();

            $count = count($new_pps);
            if($count> 1){

                foreach ($new_pps as $key => $value) {
                    if($key != 0){
                        $value->delete();
                        PendingPayment::where('id', $value->pending_payment_id)->decrement('total_shipments',1);
                        PendingPayment::where('id', $value->pending_payment_id)->decrement('delivered_shipments',1);
                    } 
                }
            }
        }
    }
}
