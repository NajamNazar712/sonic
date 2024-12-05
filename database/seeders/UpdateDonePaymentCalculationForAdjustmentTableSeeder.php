<?php

namespace Database\Seeders;

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
use App\Http\Models\DonePaymentShipment;
use Illuminate\Database\Seeder;

class UpdateDonePaymentCalculationForAdjustmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $done_payments = DonePayment::where('adjusted_shipments', '>', 0)->pluck('id')->toArray();
        if(count($done_payments) > 0){
            foreach ($done_payments as $payment_id){
                $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $payment_id)->selectRaw('SUM(payable) as total_amount')->where('type', 2)->first();
                if($done_payment_shipments){
                    $done_payment_calculation = DonePaymentCalculation::where('done_payment_id', $payment_id);
                    if($done_payment_calculation->exists()){
                        $done_payment_calculation = $done_payment_calculation->first();
                        $done_payment_calculation->adjustment = $done_payment_shipments->total_amount;
                        $done_payment_calculation->save();
                    }

                }

            }
        }
    }
}
