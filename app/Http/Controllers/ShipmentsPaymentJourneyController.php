<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Webhook\PaymentStatusWebhookController;
use App\Http\Models\RetailShipmentsPaymentJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\ShipmentsPaymentJourney;

class ShipmentsPaymentJourneyController extends Controller
{
    static public function add($shipment_id, $status_id, $admin_id, $payable_remarks = '', $done_payment_id = NULL, $retail = NULL) {
        if($retail != null){
            $shipment_payment_journey = new RetailShipmentsPaymentJourney();
        }
        else{
            $shipment_payment_journey = new ShipmentsPaymentJourney();
        }

		$shipment_payment_journey->shipment_id = $shipment_id;
		$shipment_payment_journey->status_id = $status_id;
		$shipment_payment_journey->admin_id = $admin_id;
		$shipment_payment_journey->payable_remarks = $payable_remarks;
		$shipment_payment_journey->payment_id = $done_payment_id;

		$shipment_payment_journey->save();
		PaymentStatusWebhookController::webhook_subscription($shipment_id, $status_id,$done_payment_id);
    }
}