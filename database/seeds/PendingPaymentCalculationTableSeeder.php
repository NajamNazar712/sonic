<?php


use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentCalculation;
use App\Http\Models\PendingPaymentShipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


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
        $pending_payments = PendingPayment::pluck('id')->toArray();
        if (count($pending_payments) > 0) {
            foreach ($pending_payments as $payment_id) {
                $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $payment_id);

                if ($pending_payment_shipments->exists()) {
                    $pending_payment_shipments = $pending_payment_shipments->selectRaw('SUM(amount) as total_amount, SUM(charges) as total_charges, SUM(gst) as total_gst, SUM(payable) as total_payable')->first();

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
