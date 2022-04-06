<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\V2Dispute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
class V2AdminDisputeShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 521);
        return view('admin.v2_dispute.index');
    }
    public function list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 522);
        }
        $dispute = V2Dispute::join('shipments', 'shipments.id', '=', 'v2_disputes.shipment_id')
        ->join('users as u', 'shipments.user_id', '=', 'u.id')
        ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
        ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
        ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
        ->join('admins as ab', 'ab.id', '=', 'v2_disputes.added_by')
        ->join('v2_dispute_reasons as dr', 'dr.id', '=', 'v2_disputes.reason_id')
        ->join('v2_dispute_statuses as ds', 'ds.id', '=', 'v2_disputes.status_id')
        ->leftjoin('admins as ub', 'ub.id', '=', 'v2_disputes.updated_by')
        ->select(['v2_disputes.id as dispute_id','v2_disputes.shipment_id', 'v2_disputes.remarks', 'ab.name as added_by', 'ub.name as updated_by', 'v2_disputes.created_at', 'v2_disputes.updated_at', 'shipments.tracking_number', 'shipments.actual_weight', 'shipments.amount as cod_amount', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper']);

    }
}
