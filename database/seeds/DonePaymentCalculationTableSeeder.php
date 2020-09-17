<?php
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class DonePaymentCalculationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $done_payments = DonePayment::pluck('id')->toArray();
        if(count($done_payments) > 0){
            foreach ($done_payments as $payment_id){
                $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $payment_id)->selectRaw('SUM(amount) as total_amount, SUM(charges) as total_charges, SUM(gst) as total_gst, SUM(payable) as total_payable')->first();
                if($done_payment_shipments){
                    $done_payment_shipment_ids = DonePaymentShipment::where('done_payment_id', $payment_id)->pluck('shipment_id')->toArray();
                    $packaging_charges = Shipment::whereIn('id', $done_payment_shipment_ids)->where('packaging_material_request', 1)->sum('packaging_material_charges');
                    $done_payment_calculation = DonePaymentCalculation::where('done_payment_id', $payment_id);
                    if($done_payment_calculation->exists()){
                        $done_payment_calculation = $done_payment_calculation->first();
                        $done_payment_calculation->amount = $done_payment_shipments->total_amount;
                        $done_payment_calculation->charges = $done_payment_shipments->total_charges;
                        $done_payment_calculation->gst = $done_payment_shipments->total_gst;
                        $done_payment_calculation->payable = $done_payment_shipments->total_payable;
                        $done_payment_calculation->packaging_charges = $packaging_charges;
                        $done_payment_calculation->save();
                    }else{
                        $done_payment_calculation = new DonePaymentCalculation();
                        $done_payment_calculation->done_payment_id = $payment_id;
                        $done_payment_calculation->amount = $done_payment_shipments->total_amount;
                        $done_payment_calculation->charges = $done_payment_shipments->total_charges;
                        $done_payment_calculation->gst = $done_payment_shipments->total_gst;
                        $done_payment_calculation->payable = $done_payment_shipments->total_payable;
                        $done_payment_calculation->packaging_charges = $packaging_charges;
                        $done_payment_calculation->save();
                    }

                }

            }
        }
    }
}
