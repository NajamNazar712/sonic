<?php

namespace App\Http\Controllers\CRM;

use App\Http\Models\CRM\CrmRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CRMController extends Controller
{
    static public function add_request($case_nature_id, $case_nature_type_id, $channel_id, $status_id = 1, $launched_by_id = NULL, $launched_by, $shipment_id = NULL, $agent_id = NULL){
        $crm_request = new CrmRequest();
        $crm_request->case_nature_id = $case_nature_id;
        $crm_request->case_nature_type_id = $case_nature_type_id;
        $crm_request->channel_id = $channel_id;
        $crm_request->status_id = $status_id;
        $crm_request->launched_by_id = $launched_by_id;
        $crm_request->launched_by = $launched_by;
        $crm_request->shipment_id = $shipment_id;
        $crm_request->agent_id = $agent_id;

        $crm_request->save();

    }
}
