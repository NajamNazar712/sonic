<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\AddV3PickupController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\Shipment;
use App\Http\Models\City;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3PickupRequestAttempt;
use App\Http\Models\V3Pickup\V3PickupRequestNotPickReason;
use App\Http\Models\V3Pickup\V3PickupRequestShipment;
use App\Http\Models\V3Pickup\V3PickupTimeRange;
use App\Http\Models\V3Pickup\V3PickupType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\UserShippingInfo;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class ShipperPickupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function pickup_index()
    {

        $permission = session('permissions');

        $case_nature = CrmRequestCaseNature::get();
        $row = array();
        if(session('user_type') !== 1){
            foreach($case_nature as $nature) {
                if (in_array(16,$permission) && ($nature->id == 1)) {
                    $row[] = $nature;
                }

                elseif (in_array(17,$permission) && ($nature->id == 2)) {
                    $row[] = $nature;
                }

                elseif (in_array(18,$permission) && ($nature->id == 3 || $nature->id == 4)) {
                    $row[] = $nature;
                }

            }
            $case_nature = $row;
        }
//        dd($case_nature);

//        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id', 1)->get();
        return view('client.pickups.index')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'case_permission'=>$permission]);
    }

    public function pickup_list()
    {
        $pickups = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v3_pickup_request_statuses as vprs', 'vprs.id', '=', 'v3_pickup_requests.status_id')
            ->select('v3_pickup_requests.id', 'v3_pickup_requests.id as pickup_request_id', 'v3_pickup_requests.pickup_date as requested_at', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v3_pickup_requests.booked', 'v3_pickup_requests.received', 'v3_pickup_requests.received_wo_scan', 'v3_pickup_requests.attempts', 'v3_pickup_requests.status_id', 'v3_pickup_requests.rider_status', 'usi.vendor', 'vprs.name as status', 'v3_pickup_requests.renew as renew', 'v3_pickup_requests.remarks')
            ->where('v3_pickup_requests.shipper_id', session('user_id'));


        return Datatables::of($pickups)
            ->editColumn('pickup_request_id', function ($pickup_request) {
                return str_pad($pickup_request->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('booked_button', function ($pickup_request) {
                return $pickup_request->booked;
            })
            ->editColumn('received_button', function ($pickup_request) {
                if ($pickup_request->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('scanned_button', function ($pickup_request) {
                if ($pickup_request->scanned != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received_wo_scan . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('remarks', function ($pickup_request) {
                return $pickup_request->remarks;
            })
            ->addColumn('attempt_date_time', function ($pickup_request) {
                $attempts = V3PickupRequestAttempt::where('pickup_request_id', $pickup_request->id);
                $all_attempt = '';
                if ($attempts->exists()) {
                    $attempts = $attempts->get();
                    foreach ($attempts as $attempt) {
                        if ($all_attempt == '') {
                            $all_attempt = $attempt->attempt_date;
                        }
                        else {
                            $all_attempt = $all_attempt . '<br/>' . $attempt->attempt_date;
                        }
                    }
                }
                return $all_attempt;
            })
            ->addColumn('action', function ($pickup_request) {
                $add_remarks = '<button type="button" class="dropdown-item add_remarks"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">Add Remarks</div></button>';
                $cancel_button = '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                $renew = '<button type="button" class="dropdown-item renew"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Renew</div></button>';

                $pickup_attempt = V3PickupRequestAttempt::where('pickup_request_id', $pickup_request->pickup_request_id);
                $dropdown = '';
                if ($pickup_attempt->exists() || ($pickup_request->rider_status == 1 && $pickup_request->status_id != 4) || ($pickup_request->status_id == 4 && $pickup_request->renew == 0)) {
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    if ($pickup_attempt->exists()) {
                        $dropdown .= $add_remarks;
                    }

                    if ($pickup_request->rider_status == 1 && $pickup_request->status_id != 4) {
                        $dropdown .= $cancel_button;
                    }
                    if ($pickup_request->status_id == 4 && $pickup_request->renew == 0) {
                        $dropdown .= $renew;
                    }
                    $dropdown .= '
                            </div>
                          </div>
                        ';
                }


                return $dropdown;
            })
            ->editColumn('view_details', function ($pickup_request) {
//                if ($pickup_request->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . 'View Details' . '</button>';
//                } else {
//                    return 0;
//                }
            })
            ->make(true);
    }

    public function view_details(Request $request)
    {

        $id = $request->pickup_request_id;
        $pickups = V3PickupRequest::find($id);

        $attempt_reasons = V3PickupRequestAttempt::where('pickup_request_id', $id)->first();
        $all_reason = array();
//        dd($attempt_reasons);
        if (!empty($attempt_reasons)) {
            $attempts = $attempt_reasons->reason_id;
            $v2 = V3PickupRequestNotPickReason::find($attempts);
            $all_reason['reason'] = isset($v2->name) ? $v2->name : '-';
            $all_reason['trax_remarks'] = isset($attempt_reasons->trax_remarks) ? $attempt_reasons->trax_remarks : '-';
            $all_reason['shipper_remarks'] = isset($attempt_reasons->shipper_remarks) ? $attempt_reasons->shipper_remarks : '-';
            $all_reason['attempt_date'] = isset($attempt_reasons->attempt_date) ? $attempt_reasons->attempt_date : '-';
            $all_reason['attempts'] = isset($pickups->attempts) ? $pickups->attempts : '-';

        }
        echo json_encode($all_reason);
    }

    public function shipments(Request $request)
    {
        $pickup_request_id = $request->input('pickup_request_id');
        $status = $request->input('status');
        if ($status == 1){
            $pickup_request_shipments = V3PickupRequestShipment::where('pickup_request_id', $pickup_request_id)->where('status', $status)->pluck('shipment_id')->toArray();
        }
        else if ($status == 2){
            $pickup_request_shipments = V3PickupRequestShipment::where('pickup_request_id', $pickup_request_id)->where('status', $status)->pluck('shipment_id')->toArray();
        }
        else if ($status == 3){
            $pickup_request_shipments = V3PickupRequestShipment::where('pickup_request_id', $pickup_request_id)->where('status', $status)->pluck('shipment_id')->toArray();
        }
        if (count($pickup_request_shipments) > 0) {

            $shipments = Shipment::where('id', $pickup_request_shipments)->pluck('tracking_number')->toArray();

            return ['status' => 0, 'success' => 'Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'error' => 'No Shipments', 'shipments' => FALSE];
        }
    }

    public function cancel(Request $request)
    {
        $pickup_request = V3PickupRequest::where('id', $request->pickup_request_id)->where('rider_status', 1)->where('status_id', 1);;

        if ($pickup_request->exists()) {
            $pickup_request = $pickup_request->first();
            $pickup_request->status_id = 4;
            $pickup_request->save();

            return ['status' => 1, 'success' => 'Pickup request cancelled successfully!'];
        } else {
            return ['status' => 0, 'error' => 'No pickup request found'];
        }
    }

    public function renew(Request $request)
    {
        $existing_pickup_request = V3PickupRequest::where('id', $request->pickup_request_id);
        if ($existing_pickup_request->exists()) {
                $existing_pickup_request = $existing_pickup_request->first();
            if ($existing_pickup_request->status_id == 4 && $existing_pickup_request->renew == 0) {

                $existing_pickup_request->renew = 1;
                $existing_pickup_request->save();


                $shipments_count = $existing_pickup_request->booked;
                $shipper_id = $existing_pickup_request->shipper_id;
                $pickup_address_id = $existing_pickup_request->pickup_address_id;
                $pickup_date = Carbon::today()->toDateTimeString();
                $city_id = $existing_pickup_request->city_id;
                $preferred_time_range = $existing_pickup_request->preferred_time_range;
                $pickup_type_id = $existing_pickup_request->pickup_type_id;
                $estimated_weight = $existing_pickup_request->estimated_weight;
                $remarks = $existing_pickup_request->remarks;
                $vendor = $existing_pickup_request->vendor;
                AddV3PickupController::add($shipper_id, $pickup_address_id, $pickup_date, $city_id, $preferred_time_range, $pickup_type_id, $estimated_weight, $shipments_count, $remarks, 0, $shipper_id, $vendor);

                    return ['status' => 1, 'success' => 'Pickup request renewed successfully!'];

            } else {
                return ['status' => 0, 'error' => 'Pickup Request can not be renewed'];
            }
        } else {
            return ['status' => 0, 'error' => 'No pickup request found'];
        }
    }

    public function add_remarks(Request $request)
    {
        $pickups_attempt = V2PickupRequestAttempt::where('pickup_request_id', $request->pickup_request_id);
        if ($pickups_attempt->exists()) {
            $pickups_attempt = $pickups_attempt->orderBy('id', 'DESC')->first();
            $pickups_attempt->shipper_remarks = $request->remark;
            $pickups_attempt->save();

            return ['status' => 1, 'success' => 'Remarks updated successfully against last attempt!'];
        } else {
            return ['status' => 0, 'error' => 'Pickup request is not attempted yet'];
        }
    }

    public function add_pickup()
    {
        $pickup_addresses = UserShippingInfo::with('city')->where('user_id', session('user_id'))->where('status', 1)->where('hidden', 0)->get();
        $pickup_types = V3PickupType::all();
        $time_ranges = V3PickupTimeRange::all();
        // $shipper_key = SaleTierTag::join('users as u','u.id','sale_tier_tags.user_id')->WhereNotNull('kam')->select('u.id','u.name')->get();
        // $shipper_non_key = SaleTierTag::join('users as u','u.id','sale_tier_tags.user_id')->WhereNull('kam')->select('u.id','u.name')->get();
        return view('client.pickups.add_pickup')->with(['pickup_addresses' => $pickup_addresses, 'pickup_types' => $pickup_types, 'time_ranges' => $time_ranges]);
    }

    public function add_pickup_submit(Request $request){
        $user_id = session('user_id');

        $pickup_date = $request->pickup_date_formatted;
        $pickup_date = Carbon::parse($pickup_date)->now()->toDateTimeString();
        $shipper_id = $user_id;
        $pickup_address_id = $request->pickup_address_id;
        if(V3PickupRequest::where('shipper_id', $shipper_id)->where('pickup_address_id', $pickup_address_id)->where('status_id', 1)->exists()){
            return redirect()->back()->with('error', 'Pickup request already in-process!');
        }

        $preferred_time_range = $request->preferred_time_range;
        $pickup_type_id = $request->pickup_type_id;
        $estimated_weight = $request->estimated_weight;
        $shipments_count = $request->shipments_count;
        $remarks = $request->remarks;

        $pickup_address = UserShippingInfo::where('id', $request->pickup_address_id)->where('user_id', $shipper_id)->first();

        if($pickup_address){
            $vendor = NULL;
            $city_id = $pickup_address->city->id;
            if($pickup_address->vendor !== null){
                $vendor = $pickup_address->vendor;
            }

            $pickup = V3PickupRequest::where(['shipper_id' => $shipper_id, 'pickup_address_id' => $pickup_address_id, 'status_id' => 1, 'pickup_date' => $pickup_date]);
            if($pickup->exists()){
                return redirect()->back()->with('error', 'Pickup request already in process!');
            }
            AddV3PickupController::add($shipper_id, $pickup_address_id, $pickup_date, $city_id, $preferred_time_range, $pickup_type_id, $estimated_weight, $shipments_count, $remarks, 0, $shipper_id, $vendor);

            if($request->has('pickup')){
                AddV3PickupController::add_regular_pickup($shipper_id, $pickup_address_id);
            }

            return redirect()->back()->with('success', 'Pickup request added successfully!');
        }
    }
}
