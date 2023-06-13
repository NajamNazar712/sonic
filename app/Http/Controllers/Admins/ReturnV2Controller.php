<?php

namespace App\Http\Controllers\Admins;

use App\FakeStatusRemark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\ConsigneeRefusedReason;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\Admin\SubStatusCallFinding;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\ShipmentStatus;

class ReturnV2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    public function return_v2()
    {

        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id', 1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id', 1)->get();
        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->whereNotIn('shipment_status_reason_id', [2, 55])->pluck('shipment_status_reason_id')->toArray();
        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
        $consignee_refused_reasons = ConsigneeRefusedReason::where('status', 1)->select('id', 'reasons')->where('status', 1)->get();
        $sub_status_call_finding = SubStatusCallFinding::get();
        $shipment_statuses = ShipmentStatus::whereIn('id',[20,15,52,54])->get();
        $fake_status_remarks = FakeStatusRemark::get();
        return view('admin.return_v2.index')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims, 'return_confirm_reasons' => $return_confirm_reasons , 'consignee_refused_reasons'=> $consignee_refused_reasons, 'sub_status_call_finding' => $sub_status_call_finding,'shipment_statuses'=>$shipment_statuses, 'fake_status_remarks'=>$fake_status_remarks]);
    }


    public function get_shipment_reason(Request $request)
    {
        $ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', $request->id)->pluck('shipment_status_reason_id')->toArray();
        $reasons = DB::table('shipment_status_reason')->whereIn('id', $ids)->get();
        $unresponsive_reasons = SubStatusCallFinding::get();
        return response()->json(['reasons'=> $reasons, 'unresponsive_reasons'=>$unresponsive_reasons,'status'=>1]);
    }
}
