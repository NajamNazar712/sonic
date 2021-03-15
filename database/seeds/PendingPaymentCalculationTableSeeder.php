<?php


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
        DB::table('pending_payment_calculations')->truncate();
//        $pending_payments = PendingPayment::pluck('id')->toArray();
        $pending_payments = [15198,34327,26271,13982,20395,41558,35302,5746,41782,33016,5421,5746,13855,41627];
        if(count($pending_payments) > 0){
            foreach ($pending_payments as $payment_id){
                $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $payment_id)->selectRaw('SUM(amount) as total_amount, SUM(charges) as total_charges, SUM(gst) as total_gst, SUM(payable) as total_payable')->first();
                if($pending_payment_shipments){
                    PendingPaymentCalculation::where('pending_payment_id', $payment_id)->delete();
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
    }
}
