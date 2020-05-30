<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\PickupRequest;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestRiderStatus;
use App\Http\Models\V2Pickup\V2PickupRequestStatus;use Carbon\Carbon;
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
        return view('admin.v2_pickups.pending')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons]);
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
            ->select('v2_pickup_requests.id','v2_pickup_requests.id as pickup_request_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link' , 'v2_pickup_requests.received', 'usi.vendor', 'prs.name as pickup_status' , 'rs.name as rider_status', 'v2_pickup_requests.attempts', 'cr.name as current_rider', 'lr.name as last_rider', 'v2_pickup_requests.try_and_buy', 'v2_pickup_requests.vendor','v2_pickup_requests.status_id', 'v2_pickup_requests.after_cut_off_time', DB::raw('(SELECT SUM(`rs`.`shipments`) FROM `v2_rider_pickups` AS `rs` INNER JOIN `v2_pickup_requests` AS `vpr` ON `rs`.`pickup_request_id` = `vpr`.`id` WHERE `vpr`.`id` = `rs`.`pickup_request_id`) AS `shipments_rider_picked`'))
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
}
