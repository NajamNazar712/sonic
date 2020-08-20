<?php

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class UpdateDonePaymentCalculationForPackaging extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $done_payments = DonePaymentCalculation::all();
        if($done_payments){
            foreach ($done_payments as $payment){
                $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $payment->done_payment_id);
                if($done_payment_shipments->exists()){
                    $done_payment_shipment_ids = $done_payment_shipments->pluck('shipment_id')->toArray();
                    $charges = Shipment::whereIn('id', $done_payment_shipment_ids)->where('packaging_material_request', 1)->sum('packaging_charges');

                    $payment->packaging_charges = $charges;

                    $payment->save();
                }

            }
        }
    }
}
