<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CRMController extends Controller
{
    //launched_by = 0 => Admin
    //launched_by = 1 => Shipper
    //launched_by = 2 => Substitute Shipper

    static public function add($case_nature_id, $case_nature_type_id = NULL, $channel_id, $status_id = 1, $launched_by_id = NULL, $launched_by, $shipment_id = NULL, $shipper_id = NULL, $agent_id = NULL,$description){
        $crm_request = new CrmRequest();
        $crm_request->case_nature_id = $case_nature_id;
        $crm_request->case_nature_type_id = $case_nature_type_id;
        $crm_request->channel_id = $channel_id;
        $crm_request->status_id = $status_id;
        $crm_request->launched_by_id = $launched_by_id;
        $crm_request->launched_by = $launched_by;
        $crm_request->shipment_id = $shipment_id;
        $crm_request->shipper_id = $shipper_id;
        $crm_request->agent_id = $agent_id;
        $crm_request->description = $description;

        $crm_request->save();

        $id = $crm_request->id;

        $crm_request_status_history = new CrmRequestStatusHistory();

        $crm_request_status_history->crm_request_id = $id;
        $crm_request_status_history->agent_id = $launched_by_id;
        $crm_request_status_history->status_id = 1;

        $crm_request_status_history->save();
        NotificationsController::send(31, $id);

    }
}
