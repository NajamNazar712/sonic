<?php

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\DonePaymentShipment;

use Illuminate\Database\Seeder;

class DonePaymentCalculationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('done_payment_calculations')->truncate();
        $done_payments = DonePayment::where('created_at', '>=', '2022-06-06 00:00:00')->pluck('id')->toArray();
        if (count($done_payments) > 0) {
            foreach ($done_payments as $payment_id){
                $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $payment_id)->selectRaw('SUM(amount) as total_amount, SUM(charges) as total_charges, SUM(gst) as total_gst, SUM(payable) as total_payable')->first();
                if ($done_payment_shipments) {
                    $done_payment_calculation = DonePaymentCalculation::where('done_payment_id', $payment_id);

                    if ($done_payment_calculation->exists()) {
                        $done_payment_calculation = $done_payment_calculation->first();

                        $done_payment_calculation->amount = $done_payment_shipments->total_amount;
                        $done_payment_calculation->charges = $done_payment_shipments->total_charges;
                        $done_payment_calculation->gst = $done_payment_shipments->total_gst;
                        $done_payment_calculation->payable = $done_payment_shipments->total_payable;

                        $done_payment_calculation->save();
                    }
                }

            }
        }
    }
}
