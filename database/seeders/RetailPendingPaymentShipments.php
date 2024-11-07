<?php

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\RetailPendingPayment;
use App\Http\Models\RetailPendingPaymentShipment;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class RetailPendingPaymentShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shipmentId = [
            25122338065855, 20221034618104
        ];
        echo count($shipmentId);
        if ($shipmentId) {
            $shipmentId = Shipment::whereIn('tracking_number', $shipmentId)->get();
            foreach($shipmentId  as $shipment)
            {
                
                $retail_shipment = RetailShipment::where('shipment_id', $shipment->id)->first();
                $pending_payment = RetailPendingPayment::where('user_id', $retail_shipment->shipper_account_no)->first();
                $amount = 0;
                $payable = 0;
                if ($pending_payment) {
                    $pending_payment = $pending_payment->first();

                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                    $pending_payment->save();
                } else {
                    $pending_payment = new RetailPendingPayment();

                    $pending_payment->user_id = $retail_shipment->shipper_account_no;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 0;
                    $pending_payment->adjusted_shipments = 1;

                    $pending_payment->save();
                }
                // $pending_payment_shipment = new RetailPendingPaymentShipment();

                // $pending_payment_shipment->retail_pending_payment_id = $pending_payment->id;
                // $pending_payment_shipment->shipment_id = $shipment->id;
                // $pending_payment_shipment->type = 2;
                // $pending_payment_shipment->amount = $amount;
                // $pending_payment_shipment->payable = $payable;

                // $pending_payment_shipment->save();
                AdminFinanceController::add_pending_payment_charges($pending_payment->id, $amount, 0, 0, $payable, 1);

            }
        }

        //
    }
}
