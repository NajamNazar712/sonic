<?php

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Models\Admin\AdjustmentLog;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\RetailAdjustmentLog;
use App\Http\Models\RetailPendingPayment;
use App\Http\Models\RetailPendingPaymentShipment;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;

class CreateAdjustmentForReturnMissingChargesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //21933015
        $shipment_ids = [22099058,22112871,22118529,22170765,22180531,22248394,22251034,22257669,22289551,22295624,22297019,22312094,22330842,22339713,22340371,22344549,22347203,22348504,22355337,22363693,22386370,22388949,22394142,22405923,22410140,22418387,21900007,22093064,22099671,22135844,22141535,22141550,22154348,22179005,22187203,22192547,22209922,22222167,22226325,22239318,22243271,22243475,22252266,22257473,22263392,22274071,22276823,22283028,22283565,22283574,22283918,22286758,22290465,22293008,22296431,22296937,22298802,22301819,22302427,22306066,22306742,22309360,22313590,22314181,22317729,22318157,22321629,22332187,22335175,22338559,22340264,22340510,22341778,22343565,22347500,22353341,22356085,22362110,22362141,22362689,22365150,22365216,22384003,22390510,22405054,22288390,22290915,22292894,22318673,22321632,22335032,22343404,22344512,22353423,22237456,22285643,22305009,22321979,22349896,22353354,22355607,22395630];

        foreach ($shipment_ids as $shipment_id){
            ShipmentChargesController::return($shipment_id);

            $return_charges = Shipment::find($shipment_id)->return_charges;
            $payable = $return_charges;

//            AdminFinanceController::add_adjustment($shipment_id, $payable, "Due to system issue return charges not deducted", 4);

            $adjustment_type = 4;
            $payable_remarks = "Due to system issue return charges not deducted";

            $shipment = Shipment::find($shipment_id);
            $amount = 0;
            $charges = 0;
            $gst = 0;
            if ($shipment->shipment_type == 1) {
                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                if ($pending_payment->exists()) {
                    $pending_payment = $pending_payment->first();

                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                    $pending_payment->save();
                } else {
                    $pending_payment = new PendingPayment();

                    $pending_payment->user_id = $shipment->user_id;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 0;
                    $pending_payment->returned_shipments = 0;
                    $pending_payment->adjusted_shipments = 1;

                    $pending_payment->save();
                }

                $pending_payment_shipment = new PendingPaymentShipment();

                $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $shipment_id;
                $pending_payment_shipment->type = 2;
                $pending_payment_shipment->amount = $amount;
                $pending_payment_shipment->charges = $charges;
                $pending_payment_shipment->gst = $gst;
                $pending_payment_shipment->payable = $payable;

                $pending_payment_shipment->save();
                AdminFinanceController::add_pending_payment_charges($pending_payment->id, $amount, $charges, $gst, $payable);
                if ($adjustment_type) {

                    $adjustment_log = new AdjustmentLog();

                    $adjustment_log->shipment_id = $shipment_id;
                    $adjustment_log->adjustment_type_id = $adjustment_type;
                    $adjustment_log->admin_id = 346;
                    $adjustment_log->adjustment_amount = $payable;
                    $adjustment_log->remarks = $payable_remarks;
                    $adjustment_log->pending_id = $pending_payment_shipment->id;
                    $adjustment_log->type = 1;
                    $adjustment_log->save();

                }

                ShipmentsPaymentJourneyController::add($shipment_id, 4, 346, $payable_remarks);
            }

        }
    }
}
