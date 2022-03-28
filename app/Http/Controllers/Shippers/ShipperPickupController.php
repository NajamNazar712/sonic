<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\Shipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class ShipperPickupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function pickup_index()
    {
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id', 1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id', 1)->get();
        return view('client.pickups.index')->with(['case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims]);
    }

    public function pickup_list()
    {
        $pickups = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as vprs', 'vprs.id', '=', 'v2_pickup_requests.status_id')
            ->select('v2_pickup_requests.id', 'v2_pickup_requests.id as pickup_request_id', 'v2_pickup_requests.created_at as requested_at', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.received', 'v2_pickup_requests.attempts', 'v2_pickup_requests.status_id', 'v2_pickup_requests.rider_status', 'usi.vendor', 'vprs.name as status', 'v2_pickup_requests.renew as renew')
            ->where('v2_pickup_requests.shipper_id', session('user_id'));


        return Datatables::of($pickups)
            ->editColumn('pickup_request_id', function ($pickup_request) {
                return str_pad($pickup_request->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('booked_button', function ($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('received_button', function ($pickup_request) {
                if ($pickup_request->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('reason', function ($pickup_request) {

                $attempt_reasons = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereNotNull('reason_id');
                $all_reason = '';
                if ($attempt_reasons->exists()) {
                    $attempts = $attempt_reasons->pluck('reason_id')->toArray();
                    if (count($attempts) > 0) {
                        foreach ($attempts as $reason_id) {
                            $reason = V2PickupRequestNotPickReason::find($reason_id)->name;
                            $all_reason .= $reason . '. <br />';
                        }
                    }
                }
                return $all_reason;
            })
            ->addColumn('remarks', function ($pickup_request) {
                $remarks = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereNotNull('trax_remarks');;
                $all_remarks = '';
                if ($remarks->exists()) {
                    $remarks = $remarks->get();
                    foreach ($remarks as $remark) {
                        if ($all_remarks == '') {
                            $all_remarks = $remark->trax_remarks;
                        } else {
                            $all_remarks = $all_remarks . '<br/>' . $remark->trax_remarks;
                        }
                    }
                }
                return $all_remarks;
            })
            ->addColumn('shipper_remarks', function ($pickup_request) {
                $shipper_remarks = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id)->whereNotNull('shipper_remarks');
                $all_shipper_remarks = '';
                if ($shipper_remarks->exists()) {
                    $shipper_remarks = $shipper_remarks->get();
                    foreach ($shipper_remarks as $shipper_remark) {
                        if ($all_shipper_remarks == '') {
                            $all_shipper_remarks = $shipper_remark->shipper_remarks;
                        } else {
                            $all_shipper_remarks = $all_shipper_remarks . '<br/>' . $shipper_remark->shipper_remarks;
                        }
                    }
                }
                return $all_shipper_remarks;
            })
            ->addColumn('attempt_date_time', function ($pickup_request) {
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->id);
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

                $pickup_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request->pickup_request_id);
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
        $pickups = V2PickupRequest::find($id);


//        dd($pickups);
        $attempt_reasons = V2PickupRequestAttempt::where('pickup_request_id', $id)->first();
        $all_reason = array();
//        dd($attempt_reasons);
        if (!empty($attempt_reasons)) {
            $attempts = $attempt_reasons->reason_id;
            $v2 = V2PickupRequestNotPickReason::find($attempts);
            $all_reason['reason'] = isset($v2->name) ? $v2->name : '';
            $all_reason['trax_remarks'] = isset($attempt_reasons->trax_remarks) ? $attempt_reasons->trax_remarks : '';
            $all_reason['shipper_remarks'] = isset($attempt_reasons->shipper_remarks) ? $attempt_reasons->shipper_remarks : '';
            $all_reason['attempt_date'] = isset($attempt_reasons->attempt_date) ? $attempt_reasons->attempt_date : '';
            $all_reason['attempts'] = isset($pickups->attempts) ? $pickups->attempts : '';

        }

        echo json_encode($all_reason);
    }

    public
    function shipments(Request $request)
    {
        $pickup_request_id = $request->input('pickup_request_id');
        $status = $request->input('status');
        if ($status == 0) {
            $pickup_request_shipments = V2PickupRequestShipment::where('pickup_request_id', $pickup_request_id)->get();

        } else {
            $pickup_request_shipments = V2PickupRequestShipment::where('pickup_request_id', $pickup_request_id)->where('status', $status)->get();

        }
        if ($pickup_request_shipments) {
            $shipments = array();
            foreach ($pickup_request_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $shipments[] = $shipment_details->tracking_number;
            }
            return ['status' => 0, 'success' => 'Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'error' => 'No Shipments', 'shipments' => FALSE];
        }
    }

    public
    function cancel(Request $request)
    {
        $pickup_request = V2PickupRequest::where('id', $request->pickup_request_id)->where('rider_status', 1)->where('status_id', 1);;

        if ($pickup_request->exists()) {
            $pickup_request = $pickup_request->first();
            $pickup_request->status_id = 4;
            $pickup_request->save();

            $pickup_request_shipments = V2PickupRequestShipment::where('pickup_request_id', $pickup_request->id)->where('status', 0);
            if ($pickup_request_shipments->exists()) {
                $pickup_request_shipments = $pickup_request_shipments->get();
                foreach ($pickup_request_shipments as $pickup_request_shipment) {
                    ShipmentsPickupJourneyController::add($pickup_request_shipment->shipment_id, 4);
                }
            }

            return ['status' => 1, 'success' => 'Pickup request cancelled successfully!'];
        } else {
            return ['status' => 0, 'error' => 'No pickup request found'];
        }
    }

    public
    function renew(Request $request)
    {
        $existing_pickup_request = V2PickupRequest::where('id', $request->pickup_request_id)->orderBy('id', 'DESC')->first();
        if ($existing_pickup_request) {
            if ($existing_pickup_request->status_id == 4 && $existing_pickup_request->renew == 0) {
                $shipments = array();
                $shipments_count = 0;
                $existing_pickup_request_shipments = V2PickupRequestShipment::where('pickup_request_id', $existing_pickup_request->id)->where('status', 0)->get();
                if ($existing_pickup_request_shipments) {
                    foreach ($existing_pickup_request_shipments as $pickup_request_shipment) {
                        $existing_shipment = Shipment::where('id', $pickup_request_shipment->shipment_id)->first();
                        if ($existing_shipment->shipper_status_id == 1) {

                            $shipments[] = $existing_shipment->id;
                            $shipments_count++;
                        }
                    }
                }
                if (count($shipments) > 0) {
                    $existing_pickup_request->renew = 1;
                    $existing_pickup_request->save();

                    $pickup_request = new V2PickupRequest();

                    $pickup_request->shipper_id = $existing_pickup_request->shipper_id;
                    $pickup_request->pickup_address_id = $existing_pickup_request->pickup_address_id;
                    $pickup_request->city_id = $existing_pickup_request->pickup_address->city_id;
                    $pickup_request->booked = $shipments_count;
                    $pickup_request->save();
                    foreach ($shipments as $is_shipment) {

                        ShipmentsPickupJourneyController::add($is_shipment, 1, NULL, $pickup_request->id);

//                        $pickup_request_assigned_shipment = V2PickupRequestShipment::where('shipment_id', $is_shipment);

//                        if (!$pickup_request_assigned_shipment->exists()) {
                        $pickup_request_assigned_shipment = new V2PickupRequestShipment();

                        $pickup_request_assigned_shipment->pickup_request_id = $pickup_request->id;
                        $pickup_request_assigned_shipment->shipment_id = $is_shipment;
                        $pickup_request_assigned_shipment->status = 0;

                        $pickup_request_assigned_shipment->save();
//                        }
//                        else {
//                            $pickup_request_assigned_shipment = $pickup_request_assigned_shipment->first();
//
//                            $pickup_request_assigned_shipment->pickup_request_id = $pickup_request->id;
//                            $pickup_request_assigned_shipment->status = 0;
//
//                            $pickup_request_assigned_shipment->save();
//                        }
                    }

                    return ['status' => 1, 'success' => 'Pickup request renewed successfully!'];
                } else {
                    return ['status' => 0, 'error' => 'No shipments found for selected Pickup Request'];
                }
            } else {
                return ['status' => 0, 'error' => 'Pickup Request can not be renewed'];
            }
        } else {
            return ['status' => 0, 'error' => 'No pickup request found'];
        }
    }

    public
    function add_remarks(Request $request)
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
}
