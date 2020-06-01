<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\PickupNote;
use App\Http\Models\PickupRequest;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestRiderStatus;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\V2Pickup\V2PickupRequestStatus;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class V2AdminPickupsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');


        $this->middleware('Permission');
    }
    public function pending_index() {
        $riders = Rider::where('status',1)->select(['id', 'name']);
        $pickup_statuses = V2PickupRequestStatus::all();
        $rider_statuses = V2PickupRequestRiderStatus::all();
        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
        $not_pick_reasons = V2PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $legends = V2PickupRequestLegend::all();
        $cut_off_time = '17:30:00';
        $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        if($setting->exists()){
            $setting = $setting->first();
            $cut_off_time = $setting->setting_value;
        }

        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if($rider_settings->exists()){
            $rider_settings = $rider_settings->first();
            $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        }

        return view('admin.v2_pickups.pending')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);
    }

    public function pending_list(Request $request) {
        $today = Carbon::now()->startOfDay();
        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
            ->join('v2_pickup_request_rider_statuses as rs', 'rs.id', '=', 'v2_pickup_requests.rider_status')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v2_pickup_requests.current_rider_id')
            ->leftjoin('riders as lr', 'lr.id', '=', 'v2_pickup_requests.last_rider_id')
            ->select('v2_pickup_requests.id','v2_pickup_requests.id as pickup_request_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link' , 'v2_pickup_requests.received','v2_pickup_requests.received as received_link', 'usi.vendor', 'prs.name as pickup_status' , 'rs.name as rider_status', 'v2_pickup_requests.attempts', 'cr.name as current_rider', 'lr.name as last_rider', 'v2_pickup_requests.try_and_buy', 'v2_pickup_requests.vendor','v2_pickup_requests.status_id', 'v2_pickup_requests.after_cut_off_time', DB::raw('(SELECT SUM(`rs`.`shipments`) FROM `v2_rider_pickups` AS `rs` INNER JOIN `v2_pickup_requests` AS `vpr` ON `rs`.`pickup_request_id` = `vpr`.`id` WHERE `vpr`.`id` = `rs`.`pickup_request_id`) AS `shipments_rider_picked`'))
            ->where('v2_pickup_requests.status_id', '!=', 4);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        $datatables = Datatables::of($pickup_requests)
            ->setRowAttr([
                'class' => function ($pickup_request) use ($today) {
                    if ($pickup_request->vendor != null) {
                        return 'vendor_row';
                    }
                    else if($pickup_request->try_and_buy == 1){
                        return 'try_and_buy';
                    }else if(($pickup_request->status_id == 3)  && ($pickup_request->attempts == 1)){
                        return 'first_attempt';
                    }else if(($pickup_request->status_id == 3)  && ($pickup_request->attempts == 2)){
                        return 'second_attempt';
                    }else if(($pickup_request->status_id == 3)  && ($pickup_request->attempts > 2)){
                        return 'multiple_attempt';
                    }else if($pickup_request->after_cut_off_time){
                        return 'after_cut_off_time';
                    }else if (Carbon::parse($pickup_request->pickup_address_created_at)->startOfDay()->diffInDays($today) <= 6) {
                        return 'new_pickup';
                    }
                }
            ])
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('bookings_link', function($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('received_link', function($pickup_request) {
                if ($pickup_request->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->addColumn('trax_reason', function ($pickup_requests){
                $reasons = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('reason_id');
                if($attempts->exists()){
                    $reason_ids = $attempts->pluck('reason_id')->toArray();
                    if(count($reason_ids) > 0){
                        foreach ($reason_ids as $reason_id) {
                            $reasons .= V2PickupRequestNotPickReason::find($reason_id)->name;
                            $reasons .= '<br>';
                        }
                    }
                }
                return $reasons;
            })
            ->addColumn('trax_remarks', function ($pickup_requests){
                $trax_remarks = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('trax_remarks');
                if($attempts->exists()){
                    $trax_remarks_rows = $attempts->pluck('trax_remarks')->toArray();
                    if(count($trax_remarks_rows) > 0){
                        foreach ($trax_remarks_rows as $remark) {
                            $trax_remarks .= $remark;
                            $trax_remarks .= '<br>';
                        }
                    }
                }
                return $trax_remarks;
            })
            ->addColumn('shipper_remarks', function ($pickup_requests){
                $shipper_remarks = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('shipper_remarks');
                if($attempts->exists()){
                    $shipper_remarks_rows = $attempts->pluck('shipper_remarks')->toArray();
                    if(count($shipper_remarks_rows) > 0){
                        foreach ($shipper_remarks_rows as $remark) {
                            $shipper_remarks .= $remark;
                            $shipper_remarks .= '<br>';
                        }
                    }
                }
                return $shipper_remarks;
            })
            ->addColumn('attempted_date', function ($pickup_requests){
                $attempted_date = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id);
                if($attempts->exists()){
                    $attempted_date_rows = $attempts->pluck('attempt_date')->toArray();
                    if(count($attempted_date_rows) > 0){
                        foreach ($attempted_date_rows as $attempt_date) {
                            $attempted_date .= $attempt_date;
                            $attempted_date .= '<br>';
                        }
                    }
                }
                return $attempted_date;
            })
            ->addColumn('action', function($pickup_request) {
                if (session('role_id') == 1 || in_array(18, session('permissions'))) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>
                    </div>
                  </div>
          ';
                }
                else {
                    return '';
                }
            });

        return $datatables->make(true);
    }

    public function pending_assign(Request $request) {

        $pickup_request_ids = $request->input('pickup_request_ids');

        $rider_id = $request->input('rider_id');

        $rider_cut_off_time = NULL;
        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if($rider_settings->exists()){
            $rider_settings = $rider_settings->first();
            $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        }
        if(Carbon::now() > $rider_cut_off_time){
            return ['status' => 1, 'error' => 'Rider can not be assigned after cut off time!'];
        }

        if (empty($pickup_request_ids)) {
            return ['status' => 1, 'error' => 'No Pickup Request Selected'];
        }

        if (empty($rider_id)) {
            return ['status' => 1, 'error' => 'No Rider Selected'];
        }

        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_request = V2PickupRequest::find($pickup_request_id);

            if ($pickup_request->status_id != 1) {
                return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
            }
        }

        $pickups = 0;
        $bookings = 0;

        $vendor_flag = FALSE;

        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_request = V2PickupRequest::find($pickup_request_id);
            if($pickup_request->pickup_address->vendor != NULL){
                $vendor_flag = TRUE;
            }

            $pickup_request->rider_status = 2;
            $pickup_request->attempts = $pickup_request->attempts + 1;
            $pickup_request->current_rider_id = $rider_id;
            $pickup_request->last_updated_by = Auth::id();
            if($vendor_flag){
                $pickup_request->vendor = 1;
                $vendor_flag = FALSE;
            }
            $pickup_request->save();

            $pickup_request_attempt = new V2PickupRequestAttempt();
            $pickup_request_attempt->pickup_request_id = $pickup_request_id;
            $pickup_request_attempt->rider_id = $rider_id;
            $pickup_request_attempt->attempt_date = Carbon::now();
            $pickup_request_attempt->assigned_by = Auth::id();
            $pickup_request_attempt->save();
            $pickup_request->save();

            $pickups++;
            $bookings += $pickup_request->booked;
        }

        $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();

            $pickup_note->pickups += $pickups;

            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;
        }
        else {
            $pickup_note = new V2PickupNote();

            $pickup_note->rider_id = $rider_id;
            $pickup_note->pickups = $pickups;
            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;
        }

        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_note_request = new V2PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;

            $pickup_note_request->save();

            $assigned_shipments = $pickup_request->pickup_request_shipments;

//            if($pickup_request->pickup_address->vendor != NULL){
//                NotificationsController::send(43, $pickup_request->id, $pickup_request->pickup_address->id);
//            }

            if ($assigned_shipments) {
                foreach ($assigned_shipments as $assigned_shipment) {
                    $shipment = $assigned_shipment->shipment;

                    if ($shipment->booking_type_id == 3) {
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if($pickup_request->try_and_buy == NULL){
                            $pickup_request->try_and_buy = 1;
                            $pickup_request->save();
                        }
                    }
                }
            }
        }

        return ['status' => 0, 'success' => 'Pickup Request(s) has been Assigned to the Rider'];
    }

    public function pending_update(Request $request){
        $pickup_request_ids = $request->pickup_request_ids;
        $reason_id = $request->reason_id;
        $trax_remarks = $request->trax_remarks;
        if(count($pickup_request_ids) > 0){
            foreach ($pickup_request_ids as $pickup_request_id) {
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if($pickup_request){
                    $pickup_request_attempts = $pickup_request->pickup_attempt_latest;
                    $pickup_request->last_updated_by = Auth::id();
                    $pickup_request->save();
                    if($pickup_request_attempts){
                        $pickup_request_attempts->reason_id = $reason_id;
                        $pickup_request_attempts->trax_remarks = $trax_remarks;
                        $pickup_request_attempts->save();
                    }

                }
            }
            return ['status' => 0, 'success' => 'Pickup(s) updated successfully!'];
        }
        return ['status' => 1, 'error' => 'Pickup(s) not selected!'];

    }
    public function pending_all_bookings(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                if ($shipment_details->shipper_status_id == 1) {
                    $bookings[] = $shipment_details->tracking_number;
                }
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];
        }
        else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => FALSE];
        }
    }

    public function pending_bookings(Request $request){
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_shipments()->where('status',1)->get();

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                if ($shipment_details->shipper_status_id == 1) {
                    $bookings[] = $shipment_details->tracking_number;
                }
            }

            return ['status' => 0, 'success' => 'Pending Booked Shipments', 'booked' => $bookings];
        }
        else {
            return ['status' => 0, 'success' => 'No Pending Booked Shipments', 'booked' => FALSE];
        }
    }

    public function arrival_bulk_index(Request $request){
        return view('admin.v2_pickups.arrival_single_weight');
    }
    public function arrival_bulk_shipment_details(Request $request){
        $shipment_item = ShipmentItem::find($request->tracking_number);
        if($shipment_item){
            $shipment = Shipment::find($shipment_item->shipment_id);
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                $exists = FALSE;

                $pickup_note = V2PickupRequest::find($request->pickup_receive_pickup_note_id);

                foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
                    $pickup_request = $pickup_note_request->pickup_request;

                    if ($pickup_request->shipper_id == $shipment->user_id && $pickup_request->pickup_address_id == $shipment->pickup_address_id) {
                        $exists = TRUE;

                        break;
                    }
                }

                if ($exists) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['scanned_shipment_item'] = $shipment_item->id;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment does not belong to the Selected Pickup Note'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
            }
        }
        else{
            $shipment = Shipment::where('tracking_number', $request->tracking_number);

            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $pickup_request_id = NULL;
                $rider = NULL;
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                    $exists = FALSE;
                    if($shipment->shipper_status_id == 17){
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                     if($pickup_request_shipment->exists()){
                         $pickup_request_shipment = $pickup_request_shipment->latest()->first();
                         $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                         if($pickup_request_shipment->status == 0){
                             $exists = TRUE;
                         }
                         $pickup_request = V2PickupRequest::find($pickup_request_id);
                         if($pickup_request->current_rider_id == NULL){
                             $this->generate_trax_pickup($pickup_request_id);
                         }else{
                             $rider = $pickup_request->rider->name;
                         }
                     }
                    if($shipment->booking_type_id == 3){
                        if ($exists) {
                            $details = array();
                            $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                            $shipment_items_count = count($shipment_items);

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['shipment_items'] = $shipment_items;
                            $details['shipment_items_count'] = $shipment_items_count;

                            ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                            return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                        } else {
                            return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment does not belong to the Selected Pickup Note'];
                        }
                    }
                    else{
                        if ($exists) {

                            $details = array();

                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['shipper'] = $shipment->user->name;
                            $details['pickup_request_id'] = str_pad($pickup_request_id, 6, '0', STR_PAD_LEFT);
                            $details['rider'] = $rider;

                            ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                        } else {
                            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to the Selected Pickup Note'];
                        }
                    }

                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }
        }
    }

    public function generate_trax_pickup($pickup_request_id){
        $vendor_flag = FALSE;
        $pickup_request = V2PickupRequest::find($pickup_request_id);
        $settings = GlobalSettings::where('type', 'global_rider_id');
        if($settings->exists()){
            $settings = $settings->first();
            $rider_id = $settings->setting_value;
            $pickup_request_attempt = new V2PickupRequestAttempt();
            $pickup_request_attempt->pickup_request_id = $pickup_request_id;
            $pickup_request_attempt->rider_id = $rider_id;
            $pickup_request_attempt->attempt_date = Carbon::now();
            $pickup_request_attempt->assigned_by = Auth::id();
            $pickup_request_attempt->save();

            if($pickup_request->pickup_address->vendor != NULL){
                $vendor_flag = TRUE;
            }
            $pickup_request->rider_status = 2;
            $pickup_request->attempts = $pickup_request->attempts + 1;
            $pickup_request->current_rider_id = $rider_id;
            $pickup_request->last_updated_by = Auth::id();
            if($vendor_flag){
                $pickup_request->vendor = 1;
            }
            $pickup_request->save();
        }
    }


    public function arrival_individual_index(Request $request){
        return view('admin.v2_pickups.arrival_individual_weight');
    }

    public function arrival_individual_shipment_details(Request $request)
    {
        $shipment_item = ShipmentItem::find($request->tracking_number);
        if($shipment_item){
            $shipment = Shipment::find($shipment_item->shipment_id);
            if ($shipment->shipper_status_id == 1) {
                $details = array();
                $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                $shipment_items_count = count($shipment_items);

                $details['id'] = $shipment->id;
                $details['tracking_number'] = $shipment->tracking_number;
                $details['shipment_items'] = $shipment_items;
                $details['shipment_items_count'] = $shipment_items_count;
                $details['scanned_shipment_item'] = $shipment_item->id;

                ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
            }
        }
        else{
            $shipment = Shipment::where('tracking_number', $request->tracking_number);

            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $pickup_request_id = NULL;
                $rider = NULL;

                if ($shipment->shipper_status_id == 1) {

                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if($pickup_request_shipment->exists()){
                        $pickup_request_shipment = $pickup_request_shipment->latest()->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if($pickup_request->current_rider_id == NULL){
                            $this->generate_trax_pickup($pickup_request_id);
                            $rider = 'Trax Rider';
                        }else{
                            $rider = $pickup_request->rider->name;
                        }
                    }
                    if($shipment->booking_type_id == 3){
                        $details = array();
                        $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                        $shipment_items_count = count($shipment_items);

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipment_items'] = $shipment_items;
                        $details['shipment_items_count'] = $shipment_items_count;

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                    }
                    else{
                        if (empty($request->weight)) {
                            $shipment->actual_weight = (($request->length * $request->breadth * $request->height) / 5000);
                            $shipment->length = $request->length;
                            $shipment->breadth = $request->breadth;
                            $shipment->height = $request->height;
                        } else {
                            $shipment->actual_weight = $request->weight;
                        }
                        $shipment->save();

                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipper'] = $shipment->user->name;
                        $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                        $details['rider'] = $rider;
                        $details['weight'] = floatval($shipment->actual_weight);

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];

                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }
        }
    }


    public function arrival_individual_try_and_buy_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            if ($shipment->shipper_status_id == 1) {

                $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                if($pickup_request_shipment->exists()){
                    $pickup_request_shipment = $pickup_request_shipment->latest()->first();
                    $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                    $pickup_request = V2PickupRequest::find($pickup_request_id);
                    if($pickup_request->current_rider_id == NULL){
                        $this->generate_trax_pickup($pickup_request_id);
                        $rider = 'Trax Rider';
                    }else{
                        $rider = $pickup_request->rider->name;
                    }
                }
                if($shipment->booking_type_id == 3){
                    $shipment->actual_weight = $request->weight;
                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['weight'] = floatval($shipment->actual_weight);

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                }
                else{
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function arrival_individual_shipment_remove(Request $request) {
        $shipment = Shipment::find($request->id);

        if ($shipment) {
            if ($shipment->shipper_status_id == 1) {
                $shipment->actual_weight = NULL;
                $shipment->length = NULL;
                $shipment->breadth = NULL;
                $shipment->height = NULL;

                $shipment->save();

                return ['status' => 0, 'success' => 'Shipment has been removed'];
            }
            else {
                return ['status' => 1, 'error' => 'Given Shipment ID has already been modified'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment with given ID is present'];
        }
    }
    public function individual_arrival_submit(Request $request) {
        $shipment_ids = explode(',', $request->shipment_ids);

        $pickup_request_ids = array();

        $print_shipment_ids = array();

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if ($shipment->shipper_status_id == 1) {
                $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0)->orderBy('id', 'DESC')->first();
                if($pickup_request_shipment){
                    $reference_1_id = $pickup_request_shipment->pickup_request_id;

                    if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                        $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;
                    }
                }
                else{
                    $reference_1_id = NULL;
                }

                $shipment->shipper_status_id = 2;
                $shipment->consignee_status_id = 2;

                $shipment->save();
                $reference_2_id = NULL;
                ShipmentsJourneyController::add($shipment_id, 2, 2, NULL, NULL, NULL, Auth::id(), $reference_1_id, $reference_2_id);


                //Consolidated Shipments
                $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                if($consolidated_shipment){
//                $user_shipping_info = UserShippingInfo::find($shipment->pickup_address_id);
                    if($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id){
                        $check_all_consolidation_shipments = true;

                        $shipment->shipper_status_id = 58;
                        $shipment->consignee_status_id = 58;
                        $shipment->save();

                        ShipmentsJourneyController::add($shipment_id, 58, 58, NULL, NULL, NULL, Auth::id());

                        $consolidation_id = $consolidated_shipment->consolidation_id;
                        $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                        foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment){
                            $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                            if($check_remaining_consolidated_shipment->shipper_status_id != 58){
                                $check_all_consolidation_shipments = false;
                            }
                        }

                        if($check_all_consolidation_shipments == true){
                            foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment){
                                $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                $update_all_consolidated_shipment->shipper_status_id = 59;
                                $update_all_consolidated_shipment->consignee_status_id = 59;

                                $update_all_consolidated_shipment->save();

                                ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, NULL, NULL, NULL, Auth::id());
                            }
                        }
                    }
                }
                //Consolidated Shipments

                NotificationsController::send(3, $shipment_id);

                ShipmentChargesController::weight($shipment_id);
                ShipmentChargesController::cash_handling($shipment_id);
                ShipmentChargesController::insurance($shipment_id);
                ShipmentChargesController::fuel_surcharge($shipment_id);

                if ($shipment->charges_mode_id == 2) {
                    $shipment = Shipment::find($shipment_id);

                    $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                    $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                    $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                    $shipment->amount = $shipment->amount + $charges + $gst;

                    $shipment->save();

                    $print_shipment_ids[] = $shipment_id;
                }
            }
            else {
                unset($shipment_ids[$key]);
            }
        }


        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->whereIn('pickup_request_id', $pickup_request_ids);

            if ($pickup_request_shipment->exists()) {
                $pickup_request_shipment = $pickup_request_shipment->first();
                $pickup_request_shipment->status = 1;
                $pickup_request_shipment->save();
                ShipmentsPickupJourneyController::add($shipment_id, 4, Auth::id(), $pickup_request_shipment->pickup_request->id);

                $pickup_request = $pickup_request_shipment->pickup_request;

                $pickup_request->received = $pickup_request->received + 1;

                $pickup_request->save();
            }
        }

        NotificationsController::send(4, $shipment_ids);

            return redirect()->route('admin.v2_pickups.pending.index')->with('success','Shipments arrived Successfully!');

    }
}
