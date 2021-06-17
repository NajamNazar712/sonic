<?php

namespace App\Http\Controllers\CRM;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmPaymentShipment;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\DelayInDeliveryShipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentsPaymentJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CRMAutomationController extends Controller
{
    static public function automation_delay_in_delivery(){

        $rows = DelayInDeliveryShipment::all();
        if($rows){
            foreach ($rows as $row) {
                $delivered_shipment = ShipmentsJourney::where('shipment_id', $row->shipment_id)->whereIn('shipper_status_id', [14, 30, 36, 37])->where('verification', 1);
                if($delivered_shipment->exists()){
                    $crm_request = CrmRequest::find($row->crm_request_id);
                    if($crm_request && $crm_request->status_id == 2){
                        $crm_request->status_id = 3;
                        $crm_request->save();
                        CrmRequestStatusHistory::create([
                            'crm_request_id' => $row->crm_request_id,
                            'status_id' => 3,
                            'agent_id' => 306
                        ]);
                        $setting = GlobalSettings::where('type', 'crm_delay_in_delivery_message');
                        if($setting->exists()){
                            $setting = $setting->first();
                            CRMCommentController::add($row->crm_request_id,306,0,0, $setting->text);
                        }
                        $row->delete();
                    }
                }
            }
        }

    }

    static public function automation_payment_complains(){
        $rows = CrmPaymentShipment::all();
        if($rows){
            foreach ($rows as $row){
                $payment = ShipmentsPaymentJourney::where('shipment_id', $row->shipment_id)->where('status_id', 3);
                if($payment->exists()){
                    $crm_request = CrmRequest::find($row->crm_request_id);
                    if($crm_request && $crm_request->status_id == 2){
                        $crm_request->status_id = 3;
                        $crm_request->save();
                        CrmRequestStatusHistory::create([
                            'crm_request_id' => $row->crm_request_id,
                            'status_id' => 3,
                            'agent_id' => 306
                        ]);
                        $row->delete();
                    }
                }
            }
        }
    }
}
