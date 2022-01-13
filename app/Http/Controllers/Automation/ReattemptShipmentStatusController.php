<?php

namespace App\Http\Controllers\Automation;

use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\ReattemptPercentageForShipper;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReattemptShipmentStatusController extends Controller
{
    static public function auto_reattempt_status_for_max_delivery_ratio($shipment_id){
        $global_admin = 346;

        $shipment = Shipment::find($shipment_id);
        if($shipment){
            $reattempt_percentage = ReattemptPercentageForShipper::where('user_id', $shipment->user_id);
            if($reattempt_percentage->exists()){
                $reattempt_percentage = $reattempt_percentage->first();
                $percentage = $reattempt_percentage->percentage;
                if($percentage < 60){
                    return true;
                }
                $shipment->shipper_status_id = 13;
                $shipment->consignee_status_id = 13;
                $shipment->save();

                $journey = ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 12)->latest('id')->first();

                if ($journey && ($journey->status_reason_id == 12)) {
                    $shipment->nsa_osa_status = 1;

                    $shipment->save();

                    ShipmentChargesController::nsa_osa_charges($shipment_id);
                }

                ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, 'Auto re-attempt status due to better Delivery Ratio', NULL, $global_admin);

                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_id)->latest()->first();
                if($return_assign_shipment){
                    $return_assign_shipment->status = 0;
                    $return_assign_shipment->save();

                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    $return_assign_log->status = 1;
                    $return_assign_log->assigned_by = $return_assign_shipment->assigned_by;
                    $return_assign_log->save();
                }

                NotificationsController::send(15, 0, $shipment_id);
                NotificationsController::send(16, 0, $shipment_id);
            }

        }


    }
}
