<?php

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use Illuminate\Database\Seeder;


class PendingPaymentCalculationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pending_payments = PendingPayment::pluck('id')->toArray();
        if(count($pending_payments) > 0){
            foreach ($pending_payments as $payment_id){
                $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $payment_id)->selectRaw('SUM(amount) as total_amount, SUM(charges) as total_charges, SUM(gst) as total_gst, SUM(payable) as total_payable')->first();
                if($pending_payment_shipments){
                    $pending_payment_calculation = new PendingPaymentCalculation();
                    $pending_payment_calculation->pending_payment_id = $payment_id;
                    $pending_payment_calculation->amount = $pending_payment_shipments->total_amount;
                    $pending_payment_calculation->charges = $pending_payment_shipments->total_charges;
                    $pending_payment_calculation->gst = $pending_payment_shipments->total_gst;
                    $pending_payment_calculation->payable = $pending_payment_shipments->total_payable;
                    $pending_payment_calculation->save();
                }

            }
        }

        $done_payments = DonePayment::pluck('id')->toArray();
        if(count($done_payments) > 0){
            foreach ($done_payments as $payment_id){
                $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $payment_id)->selectRaw('SUM(amount) as total_amount, SUM(charges) as total_charges, SUM(gst) as total_gst, SUM(payable) as total_payable')->first();
                if($done_payment_shipments){
                    $done_payment_calculation = new DonePaymentCalculation();
                    $done_payment_calculation->done_payment_id = $payment_id;
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
