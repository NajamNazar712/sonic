<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Controllers\Webhook\InitialChargesWebhookController;
use App\Http\Models\Admin\BookingSmsForShippers;
use App\Http\Models\Admin\ByPassWeightShippers;
use App\Http\Models\Admin\CancelledShipmentArrival;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\ShipmentsEstimatedWeight;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\City;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\InternationalShipment;
use App\Http\Models\PickupAction;
use App\Http\Models\ProjectArrivalShipper;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\SelfCollectionCities;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestRiderStatus;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\V2Pickup\V2PickupRequestStatus;
use App\Http\Models\V2Pickup\V2RiderPickup;
use App\Http\Models\V2Pickup\V2RiderPickupActionLog;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class V2AdminPickupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }
    public function pending_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 6);

        $riders = Rider::where('status', 1)->select(['id', 'name','trax_id']);
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
        if ($setting->exists()) {
            $setting = $setting->first();
            $cut_off_time = $setting->setting_value;
        }

        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if ($rider_settings->exists()) {
            $rider_settings = $rider_settings->first();
            $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        }

        return view('admin.v2_pickups.pending')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);
    }

    public function pending_list(Request $request)
    {
//        dd($request->all());
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        }
        $today = Carbon::now()->startOfDay();
        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->leftjoin('territories as t', 't.id', '=', 'u.territory_id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
            ->join('v2_pickup_request_rider_statuses as rs', 'rs.id', '=', 'v2_pickup_requests.rider_status')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v2_pickup_requests.current_rider_id')
            ->leftjoin('riders as lr', 'lr.id', '=', 'v2_pickup_requests.last_rider_id')
        //Assigned Date
            ->leftJoin('v2_pickup_request_attempts as vpa', function ($join) {
                $join->on('vpa.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('vpa.id', '=',
                        DB::raw('(select max(id) from v2_pickup_request_attempts where v2_pickup_request_attempts.pickup_request_id = v2_pickup_requests.id)'));
            })
        //End
            ->leftJoin('v2_pickup_note_requests as vpn', function ($join) {
                $join->on('vpn.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('vpn.id', '=',
                        DB::raw('(select max(id) from v2_pickup_note_requests where v2_pickup_note_requests.pickup_request_id = v2_pickup_requests.id)'));
            })
            ->leftJoin('v2_rider_pickups as vpr', function ($join) {
                $join->on('vpr.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('vpr.id', '=',
                        DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v2_pickup_requests.id)'));
            })
//            ->leftJoin('v2_rider_pickups as vpr', 'vpr.pickup_request_id', '=', 'v2_pickup_requests.id')
            ->leftjoin('star_shippers as ss','ss.user_id','=','u.id')
            ->select('v2_pickup_requests.id','v2_pickup_requests.reminder_status as reminder', 'v2_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link', 'v2_pickup_requests.received', 'v2_pickup_requests.received as received_link', 'usi.vendor as vendor_name', 'prs.name as pickup_status', 'rs.name as rider_status', 'v2_pickup_requests.attempts', 'cr.name as current_rider', 'lr.name as last_rider', 'v2_pickup_requests.try_and_buy', 'v2_pickup_requests.vendor', 'v2_pickup_requests.status_id', 'v2_pickup_requests.after_cut_off_time', 'vpn.pickup_note_id', 'vpn.pickup_note_id as pickup_note_no', 'vpr.shipments as shipments_rider_picked', 'vpa.created_at as assigned_date', 'v2_pickup_requests.reverse_pickup', 'vpr.rider_remarks as rider_remarks','usi.pickup_brand_name as brand_name', 'v2_pickup_requests.remarks as rev_remarks', 't.name as territory','ss.status as star_status')
            ->whereNotIn('v2_pickup_requests.status_id', [2, 4]);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }

        if (session('department_id') == 8) {
            $id = GlobalSettings::where('type','=','retail_store')->select('setting_value');
            if($id->exists())
            {
                $id = $id->first();
                $pickup_requests = $pickup_requests->where('u.id',$id->setting_value);
            }
        }

        $datatables = Datatables::of($pickup_requests)
            ->setRowAttr([
                'class' => function ($pickup_request) use ($today) {
                    if ($pickup_request->star_status == 1)
                    {
                        return 'star_sippers';
                    }
                    if ($pickup_request->reverse_pickup == 1) {
                        return 'reverse_pickup_row';
                    }
                    if ($pickup_request->vendor != null) {
                        return 'vendor_row';
                    } else if ($pickup_request->try_and_buy == 1) {
                        return 'try_and_buy';
                    } else if (($pickup_request->status_id == 3) && ($pickup_request->attempts == 1)) {
                        return 'first_attempt';
                    } else if (($pickup_request->status_id == 3) && ($pickup_request->attempts == 2)) {
                        return 'second_attempt';
                    } else if (($pickup_request->status_id == 3) && ($pickup_request->attempts > 2)) {
                        return 'multiple_attempt';
                    } else if ($pickup_request->after_cut_off_time) {
                        return 'after_cut_off_time';
                    } else if (Carbon::parse($pickup_request->pickup_address_created_at)->startOfDay()->diffInDays($today) <= 6) {
                        return 'new_pickup';
                    }
                }
            ])
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                if($pickup_requests->reminder == 1)
                {
                    if ($pickup_requests->star_status == 1)
                    {
                        $test = str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
                        $test1 ='<td class="align-middle pickup_request_id sorting_1" ><b style="background-color: 	#00FF00; font-size: 17px;"><i class="star_shippers_icon"></i>'.$test.'</b></td>';
                        return $test1;
                    }
                    else
                    {
                        $test = str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
                        $test1 ='<td class="align-middle pickup_request_id sorting_1" ><b style="background-color: 	#00FF00; font-size: 17px;">'.$test.'</b></td>';
                        return $test1;
                    }
                }
                if ($pickup_requests->star_status == 1)
                {
                    $test = str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
                    $test1 ='<td class="align-middle" ><i class="star_shippers_icon"></i>'.$test.'</b></td>';
                    return $test1;
                }
                else
                {
                    return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
                }

            })
            ->editColumn('bookings_link', function ($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('received_link', function ($pickup_request) {
                if ($pickup_request->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('trax_reason', function ($pickup_requests) {
                $reasons = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('reason_id');
                if ($attempts->exists()) {
                    $reason_ids = $attempts->pluck('reason_id')->toArray();
                    if (count($reason_ids) > 0) {
                        foreach ($reason_ids as $reason_id) {
                            $reasons .= V2PickupRequestNotPickReason::find($reason_id)->name . ',' . PHP_EOL;
                        }
                    }
                }
                return $reasons;
            })
            ->addColumn('trax_remarks', function ($pickup_requests) {
                $trax_remarks = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('trax_remarks');
                if ($attempts->exists()) {
                    $trax_remarks_rows = $attempts->pluck('trax_remarks')->toArray();
                    if (count($trax_remarks_rows) > 0) {
                        foreach ($trax_remarks_rows as $remark) {
                            $trax_remarks .= $remark . ',' . PHP_EOL;
                        }
                    }
                }
                return $trax_remarks;
            })
            ->addColumn('shipper_remarks', function ($pickup_requests) {
                $shipper_remarks = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('shipper_remarks');
                if ($attempts->exists()) {
                    $shipper_remarks_rows = $attempts->pluck('shipper_remarks')->toArray();
                    if (count($shipper_remarks_rows) > 0) {
                        foreach ($shipper_remarks_rows as $remark) {
                            $shipper_remarks .= $remark . ',' . PHP_EOL;
                        }
                    }
                }
                return $shipper_remarks;
            })
            ->addColumn('attempted_date', function ($pickup_requests) {
                $attempted_date = '';
                $attempts = V2PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id);
                if ($attempts->exists()) {
                    $attempted_date_rows = $attempts->pluck('attempt_date')->toArray();
                    if (count($attempted_date_rows) > 0) {
                        foreach ($attempted_date_rows as $index => $attempt_date) {
                            $attempted_date .= $attempt_date . ',' . PHP_EOL;
                        }
                    }
                }
                return $attempted_date;
            })
            ->editColumn('pickup_note_no', function ($pickup_requests) {
                if ($pickup_requests->pickup_note_id != null) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print" rel="' . $pickup_requests->pickup_note_id . '"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($pickup_requests->pickup_note_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }
                return '';
            })
            ->addColumn('action', function ($reminder_request) {
                $reminder_button = '<a href="javascript:void(0);" class="dropdown-item reminderMarkStatus" data-action="reminder"><i class="ft-plus-circle primary"></i> Reminder </a>';
                
                $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                    if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                        $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                        if ((session('role_id') == 1 || (in_array(583, session('permissions'))))) {
                            $dropdown .= $reminder_button;
                        }

                        if ($reminder_request->reverse_pickup == 1 && $reminder_request->rev_remarks == null) {

                            $dropdown .= $remarks_button;

                        }

                        $dropdown .= "
                            </div>
                        </div>
                    ";

                        return $dropdown;
                    } else {
                        return '';
                    }
            })
            ->addColumn('aging',function ($pickup_requests){
                $requested_date=$pickup_requests->requested_date;
                $settings = GlobalSettings::where('type', 'pickup_request_cut_off_time');
                if ($settings->exists()) {
                    $settings = $settings->first();
                    $days =Carbon::createFromTime($settings->setting_value, '0', '0', 'Asia/Karachi');
                   
                    $startTime = Carbon::parse($requested_date);
                    $endTime = Carbon::parse($days);

                    $totalDuration =  $startTime->diffInHours($endTime).' Hrs';
                   
                    //$difference =  $requested_date->diff($days)->format('%H:%I:%S')." Minutes";
                    //$difference=$requested_date-$days;
                    return $totalDuration;
                }
                //$days = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
            })
            ->editColumn('brand_name', function ($pickup_requests) {
                if($pickup_requests->brand_name==null){
                    $shipper = User::find($pickup_requests->user_id);
                    return $shipper->brand_name;
                }else{
                    return $pickup_requests->brand_name;
                }

            })
            ->addColumn('all_remarks',function ($pickup_requests){
                    return '<button class="btn btn-sm btn-outline-info align-middle all_remarks_btn" rel="' . $pickup_requests->id . '"><span class="align-middle">View Remarks</span></button>';
            });
            if($legend_filter = $request->get('legend_filter')){
                if($legend_filter==8){
                    $datatables->where('v2_pickup_requests.reverse_pickup',1);
                }
                elseif($legend_filter==2){
                    $datatables->where('v2_pickup_requests.vendor','<>',null);
                }
                elseif($legend_filter==3){
                    $datatables->where('v2_pickup_requests.try_and_buy',1)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==4){
                    $datatables->where('v2_pickup_requests.status_id',3)->where('v2_pickup_requests.attempts',1)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==5){
                    $datatables->where('v2_pickup_requests.status_id',3)->where('v2_pickup_requests.attempts',2)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==6){
                    $datatables->where('v2_pickup_requests.status_id',3)->where('v2_pickup_requests.attempts','>',2)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==7){
                    $datatables->where('v2_pickup_requests.after_cut_off_time','<>',null)
                    ->where('v2_pickup_requests.status_id','<>',3)
                    ->where('v2_pickup_requests.try_and_buy',null)
                    ->where('v2_pickup_requests.vendor',null);
                }
                elseif($legend_filter==1){
                    $datatables->where('v2_pickup_requests.created_at','<=',Carbon::now()->startOfDay()->addDays(6))
                        ->where('v2_pickup_requests.after_cut_off_time',null)
                        ->where('v2_pickup_requests.status_id','<>',3)
                        ->where('v2_pickup_requests.try_and_buy',null)
                        ->where('v2_pickup_requests.vendor',null)
                        ->where('v2_pickup_requests.reverse_pickup',null);
                }

            }
            
            if($legend_filter = $request->get('before_cut_off_time')){
                //to be made as before cut off time
                
                $cut_off_time = '17:30:00';
                $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
                if ($setting->exists()) {
                    $setting = $setting->first();
                    $cut_off_time = $setting->setting_value . ':00:00';
                    $cut_off_time = Carbon::parse($cut_off_time)->format('H:i:s');
                    $datatables->whereTime('v2_pickup_requests.created_at','<=',$cut_off_time);
                }
            }

        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $stop_date = date('Y-m-d H:i:s', strtotime($to . ' +1 day'));
            $datatables->whereBetween('v2_pickup_requests.created_at', [$from, $stop_date]);
        }

        if($request->get('star_shipper_filter') == 1)
        {
            $datatables->where('ss.status',1);
        }
                    return $datatables->make(true);
    }

    public function pending_assign(Request $request)
    {
        $pickup_request_ids = $request->input('pickup_request_ids');
        $pickup_request_ids = explode(',', $pickup_request_ids);
        $rider_id = $request->input('rider');
        $rider_ids = $request->input('rider');
        $previous_rider_id = null;
        $riders = array();
        $riders['new'] = $rider_id;
        $riders['new_phone'] = $rider_ids;
        $notification_data = array();
        if (count($pickup_request_ids) == 0) {
            return redirect()->back()->with('error', 'No Pickups selected!');
        }

        array_unique($pickup_request_ids);
        $rider_cut_off_time = null;
        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if ($rider_settings->exists()) {
            $rider_settings = $rider_settings->first();
            if ($rider_settings->setting_value != 0 && $rider_settings->setting_value != null) {
                $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
            }
        }
        if ($rider_cut_off_time != null) {
            if (Carbon::now() > $rider_cut_off_time) {
                return redirect()->back()->with('error', 'Rider can not be assigned after cut off time!');
            }
        }

        if (empty($pickup_request_ids)) {
            return redirect()->back()->with('error', 'No Pickup Request Selected!');
        }

        if (empty($rider_id)) {
            return redirect()->back()->with('error', 'No Rider Selected!');
        }

//        foreach ($pickup_request_ids as $pickup_request_id) {
        //            $pickup_request = V2PickupRequest::find($pickup_request_id);
        //
        //            if ($pickup_request->status_id != 1) {
        //                return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        //            }
        //        }

        $pickups = 0;
        $shipments = 0;

        $settings = GlobalSettings::where('type', 'pickup_arrival_cut_off_time');
        $arrival_cut_off_time = '8';
        if ($settings->exists()) {
            $settings = $settings->first();
            $arrival_cut_off_time = $settings->setting_value;
        }

        $start_date = Carbon::now()->startOfDay();
        $end_date = Carbon::now()->endOfDay();
        $today = Carbon::today();
        $today->hour($arrival_cut_off_time)->minute(0)->second(0);

        $allowed_pickup_requests = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
//            $existing_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('rider_id', $rider_id)->whereBetween('attempt_date', [$start_date, $end_date]);
            $existing_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('attempt_date', '>', $today);

            if (!$existing_pickup_request_attempt->exists()) {
                $pickup_request = V2PickupRequest::find($pickup_request_id);

                $previous_rider_id = $pickup_request->current_rider_id;

                $pickup_request->rider_status = 2;
                $pickup_request->attempts = $pickup_request->attempts + 1;
                $pickup_request->current_rider_id = $rider_id;
                $pickup_request->last_updated_by = Auth::id();
                $pickup_request->save();

                $pickup_request_attempt = new V2PickupRequestAttempt();
                $pickup_request_attempt->pickup_request_id = $pickup_request_id;
                $pickup_request_attempt->rider_id = $rider_id;
                $pickup_request_attempt->attempt_date = Carbon::now();
                $pickup_request_attempt->assigned_by = Auth::id();
                $pickup_request_attempt->save();

                if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                    $allowed_pickup_requests[] = $pickup_request_id;
                }

                $pickups++;
                $shipments = $shipments + $pickup_request->booked;
                self::retail_pickup_assign($pickup_request_id, $rider_id);
            }
            else {
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if ($pickup_request->current_rider_id == $rider_id) {

                    if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                        $allowed_pickup_requests[] = $pickup_request_id;
                    }

                } else {
                    $previous_rider_id = $pickup_request->current_rider_id;
                    $riders['old_rider_id'] = $pickup_request->current_rider_id;
                    $riders['new_rider_id'] = $rider_id;

                    $pickup_request->rider_status = 2;
                    $pickup_request->current_rider_id = $rider_id;
                    $pickup_request->last_updated_by = Auth::id();
                    $pickup_request->save();
                    $existing_pickup_request_attempt = $existing_pickup_request_attempt->latest('id')->first();

                    $existing_pickup_rider = $existing_pickup_request_attempt->rider_id;

                    $existing_pickup_request_attempt->rider_id = $rider_id;
                    $existing_pickup_request_attempt->assigned_by = Auth::id();
                    $existing_pickup_request_attempt->save();

                    $pickup_note_request = $pickup_request->pickup_note_request;
                    if ($pickup_note_request) {
                        $pickup_note = $pickup_note_request->pickup_note;
                        $pickup_note_rider = $pickup_note->rider_id;
                        if ($existing_pickup_rider == $pickup_note_rider) {
                            $pickup_request->pickup_note_request->delete();
                            $pickup_note->pickups = $pickup_note->pickups - 1;
                            $pickup_note->shipments = $pickup_note->shipments - $pickup_request->booked;
                            $pickup_note->save();
                        }
                    }
                    $pickups++;
                    $shipments = $shipments + $pickup_request->booked;
                    if (!in_array($pickup_request_id, $allowed_pickup_requests)) {
                        $allowed_pickup_requests[] = $pickup_request_id;
                    }
                    if ($riders['old_rider_id'] != null && $riders['new_rider_id'] != null) {
                        NotificationsController::send(106, $riders, $pickup_request_id);
                        NotificationsController::send(107, $riders, $pickup_request_id);
                    }
                    self::retail_pickup_assign($pickup_request_id, $rider_id);
                }
            }
            $notification_data[] = ["rider_id" => $rider_id, "previous_rider" => $previous_rider_id, "pickup_request" => $pickup_request->id];
        }
        if (count($allowed_pickup_requests) > 0) {
            $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->first();
                if (!V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->whereIn('pickup_request_id', $allowed_pickup_requests)->exists()) {
                    $pickup_note->pickups += $pickups;
                    $pickup_note->shipments += $shipments;

                    $pickup_note->save();

                }
                $pickup_note_id = $pickup_note->id;
            } else {
                $pickup_note = new V2PickupNote();

                $pickup_note->rider_id = $rider_id;
                $pickup_note->pickups = $pickups;
                $pickup_note->shipments = $shipments;
                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            }

            foreach ($allowed_pickup_requests as $pickup_request_id) {
                if (!V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->exists()) {
                    V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->where('status', 0)->delete();
                    $pickup_note_request = new V2PickupNoteRequest();

                    $pickup_note_request->pickup_note_id = $pickup_note_id;
                    $pickup_note_request->pickup_request_id = $pickup_request_id;

                    $pickup_note_request->save();
                    $pickup_request = V2PickupRequest::find($pickup_request_id);
                    $assigned_shipments = $pickup_request->pickup_request_shipments;
                    NotificationsController::send(42, $rider_id, $pickup_request->shipper_id);
                    if ($pickup_request->vendor == 1) {
                        NotificationsController::send(43, $pickup_request->id, $pickup_request->pickup_address->id);
                    }

                    if ($assigned_shipments) {
                        foreach ($assigned_shipments as $assigned_shipment) {
                            $shipment = $assigned_shipment->shipment;
//                    if ($shipment->booking_type_id == 3) {
                            //                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                            //                        if($pickup_request->try_and_buy == NULL){
                            //                            $pickup_request->try_and_buy = 1;
                            //                            $pickup_request->save();
                            //                        }
                            //                    }
                            if ($shipment->booking_type_id == 5) {
                                NotificationsController::send(77, $rider_id, $shipment->id);
                            }

                        }

                    }

                }
            }

            EmployeeAttendanceController::riders_attendance_mark($rider_id);

            foreach ($notification_data as $notification_datum){
                $previous_rider_id = $notification_datum["previous_rider"];
                $rider_id = $notification_datum["rider_id"];
                $pickup_request_id = $notification_datum["pickup_request"];
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if($pickup_request){
                    if ($previous_rider_id != NULL) {
                        NotificationsController::app_notification(2, $previous_rider_id, 2, $pickup_request->current_rider_id, $pickup_request->shipper_id);
                    }
                    if ($rider_id != NULL && $previous_rider_id == NULL) {
                        NotificationsController::app_notification(3, $rider_id, 2, $pickup_request->shipper_id);
                    } elseif ($rider_id != NULL && $previous_rider_id != NULL) {
                        NotificationsController::app_notification(1, $rider_id, 2, $previous_rider_id, $pickup_request->shipper_id);
                    }
                }
            }

            return redirect()->back()->with('success', 'Pickup Request(s) has been Assigned to the Rider!');
        } else {
            foreach ($notification_data as $notification_datum){
                $previous_rider_id = $notification_datum["previous_rider"];
                $rider_id = $notification_datum["rider_id"];
                $pickup_request_id = $notification_datum["pickup_request"];
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if($pickup_request){
                    if ($previous_rider_id != NULL) {
                        NotificationsController::app_notification(2, $previous_rider_id, 2, $pickup_request->current_rider_id, $pickup_request->shipper_id);
                    }
                    if ($rider_id != NULL && $previous_rider_id == NULL) {
                        NotificationsController::app_notification(3, $rider_id, 2, $pickup_request->shipper_id);
                    } elseif ($rider_id != NULL && $previous_rider_id != NULL) {
                        NotificationsController::app_notification(1, $rider_id, 2, $previous_rider_id, $pickup_request->shipper_id);
                    }
                }
            }
            return redirect()->back()->with('success', 'Pickup Request(s) rider updated / assigned!');
        }
        return redirect()->back()->with('error', 'Pickup Request(s) already assigned!');

    }

    public function pending_update(Request $request)
    {
        $pickup_request_ids = $request->pickup_request_ids;
        $pickup_request_ids = explode(',', $pickup_request_ids);

        $reason_id = $request->reason;
        $trax_remarks = $request->trax_remarks;
        if (count($pickup_request_ids) > 0) {
            foreach ($pickup_request_ids as $pickup_request_id) {
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                if ($pickup_request) {
                    $rider_id = $pickup_request->current_rider_id;
                    if ($rider_id == null) {
                        $this->generate_trax_pickup($pickup_request->id);
                    }
                    $pickup_request_attempts = $pickup_request->pickup_attempt_latest;
//                    $pickup_request->status_id = 3;
                    $pickup_request->last_updated_by = Auth::id();
                    $pickup_request->save();
                    if ($pickup_request_attempts) {
                        $pickup_request_attempts->reason_id = $reason_id;
                        $pickup_request_attempts->trax_remarks = $trax_remarks;
                        $pickup_request_attempts->save();
                    }
                    $pickup_note_request = V2PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->orderBy('id', 'desc')->first();
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    $pickup_note_request->status = 1;
                    $pickup_note_request->save();
                    $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                    if ($pickup_note_requests_count == 0) {
                        V2PickupNote::where('id', $pickup_note_id)->update(['status' => 1]);
                    }
                    NotificationsController::send(105, $pickup_request_id, $reason_id);
                }
            }
            return redirect()->back()->with('success', 'Pickup(s) updated successfully!');
        }
        return redirect()->back()->with('error', 'Pickup(s) not selected!');

    }
    public function pending_all_bookings(Request $request)
    {
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                if (in_array($shipment_details->shipper_status_id,[1, 53])) {
                    $bookings[] = $shipment_details->tracking_number;
                }
            }

            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];
        } else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => false];
        }
    }

    public function pending_received_bookings(Request $request)
    {
        $pickup_request_id = $request->input('pickup_request_id');

        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_all_booked_shipments = $pickup_request->pickup_request_received_shipments;

        if ($pickup_request_all_booked_shipments->count() != 0) {
            $bookings = array();
            foreach ($pickup_request_all_booked_shipments as $all_shipments) {
                $shipment = $all_shipments->shipment_id;
                $shipment_details = Shipment::find($shipment);
                $bookings[] = $shipment_details->tracking_number;

            }

            return ['status' => 0, 'success' => 'Pending Booked Shipments', 'booked' => $bookings];
        } else {
            return ['status' => 0, 'success' => 'No Pending Booked Shipments', 'booked' => false];
        }
    }
    public function pending_reminder(Request $request){
        $id = $request->shipment_id;
        $data = V2PickupRequest::find($id);
        $data->reminder_status = 1;
        $data->reminder_status = 1;
        $data->save();
        return ['status'=>1,'success'=>"Reminder successfully Set"];

    }

    public function arrival_bulk_index(Request $request)
    {

        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name', 'trax_id')->get();
        return view('admin.v2_pickups.arrival_single_weight')->with(['riders' => $riders, 'global_rider_id' => $global_rider_id]);
    }
    public function arrival_bulk_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            //todo: now checking canceled shipment arrival
            $user = ShipmentsJourney::where('shipment_id',$shipment->id)->select('user_id','shipper_status_id')->orderby('id','desc')->first();
            if($user->shipper_status_id == 17)
            {
                $canceled_shipment = CancelledShipmentArrival::where('shipper_id',$user->user_id)->first();
                if($canceled_shipment)
                {
                    return ['status' => 1, 'error' => 'Shipment is not allowed for arrival because shipper cancelled this shipment !'];
                }
            }
            //todo: now checking canceled shipment arrival end

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if(!$dispute_check){
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }
            $user = $shipment->user;
            if($user->sub_segment_id == 2){
                $settings = GlobalSettings::where('type', 'global_rider_id')->first();

                if ($settings) {
                    $global_rider_id = $settings->setting_value;
                } else {
                    $global_rider_id = 0;
                }

                $pickup_request_id = null;
                $rider = null;
                $rider_assigned_flag = false;
                $shipment_origin = $shipment->pickup_address->city->hub_id;
                if (session('role_id') != 1) {
                    if (!in_array($shipment_origin, session('hubs'))) {
                        return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                    }

                }

                if ($shipment->warehouse == 1) {
                    if ($shipment->warehouse_order_status != 5) {
                        return ['status' => 1, 'error' => 'Shipment is not dispatched yet!'];
                    }
                }

                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                    if ($shipment->booking_type_id == 3) {
                        $details = array();
                        $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                        $shipment_items_count = count($shipment_items);

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipment_items'] = $shipment_items;
                        $details['shipment_items_count'] = $shipment_items_count;
                        $details['city'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                    } else if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                        $details = array();
                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces_count'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                        $details['city'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;
                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        if ($shipment->shipper_status_id == 17) {
                            AdminPickupsController::generate($shipment->id);
                        }
                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                            $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        } else {
                            AdminPickupsController::generate($shipment->id);

                            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                            if ($pickup_request_shipment->exists()) {
                                $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                                $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                                $pickup_request = V2PickupRequest::find($pickup_request_id);
                                if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                    //                                $rider = Rider::find($rider_id)->name;
                                    $rider = '';
                                    $rider_assigned_flag = true;

                                } else {
                                    $rider = $pickup_request->rider->name;
                                }
                            }
                        }
                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['city'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;
                        $details['shipper'] = $shipment->user->name;
                        $details['pickup_request_id'] = str_pad($pickup_request_id, 6, '0', STR_PAD_LEFT);
                        $details['rider'] = $rider;
                        $details['city'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;

                        $details['pickup_request_id_unpadded'] = $pickup_request_id;
                        $details['rider_assigned'] = $rider_assigned_flag;


                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                    }

                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Bulk Arrival is only allowed for shipments of General Logistics - Express Shipper(s)'];
            }
        }
        $shipment_item = ShipmentItem::find($request->tracking_number);
        if ($shipment_item) {
            $shipment = Shipment::find($shipment_item->shipment_id);
            $user = $shipment->user;
            if($user->sub_segment_id == 2) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['scanned_shipment_item'] = $shipment_item->id;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            }
            else{
                return ['status' => 1, 'error' => 'Bulk Arrival is only allowed for shipments of General Logistics - Express Shipper(s)'];
            }

        }
        $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number);
        if ($shipment_pieces->exists()) {
            $shipment_pieces = $shipment_pieces->first();
            $shipment = Shipment::find($shipment_pieces->shipment_id);

            $user = $shipment->user;
            if($user->sub_segment_id == 2) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                    $details = array();
                    $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                    $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null, $shipment_pieces->id);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Bulk Arrival is only allowed for shipments of General Logistics - Express Shipper(s)'];
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function bulk_arrival_submit(Request $request)
    {

        $shipment_ids = explode(',', $request->shipment_ids);

        $pickup_request_ids = array();

        $print_shipment_ids = array();

        $unassigned_pickup_requests = array();

        $settings = GlobalSettings::where('type', 'global_rider_id');
        $pickup_rider_id = null;
        if ($settings->exists()) {
            $settings = $settings->first();
            $pickup_rider_id = $settings->setting_value;
        }

        if ($pickup_rider_id) {
            $unassigned_pickup_requests = explode(',', $request->pickup_request_ids);
        }

        $walkin_shipment_ids = array();
        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                $piece_request_remarks = null;
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0)->orderBy('id', 'DESC')->first();
                    //region Taha
                    $pickup_request=V2PickupRequest::where('id', $pickup_request_shipment->pickup_request_id)->first();
                    //endregion

                    if ($shipment->shipper_status_id == 62) {
                        $shipment_pieces_request = ShipmentPiecesRequest::where('shipment_id', $shipment->id)->where('status', 1);
                        if ($shipment_pieces_request->exists()) {
                            $shipment_pieces_request = $shipment_pieces_request->first();
                            $shipment_pieces_request->status = 2;
                            $shipment_pieces_request->request_status_id = 4;
                            $shipment_pieces_request->last_updated_by_admin = Auth::id();
                            $shipment_pieces_request->last_updated_at = Carbon::now();
                            $shipment_pieces_request->department_id = session('department_id');
                            $shipment_pieces_request->save();
                            $piece_request_remarks = 'Resolved through Arrival';
                        }
                    }

                    if ($pickup_request_shipment) {
                        $reference_1_id = $pickup_request_shipment->pickup_request_id;
                        $rider_id=$pickup_request->current_rider_id;

                        if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                            $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;
                        }
                    } else {
                        $reference_1_id = null;
                    }
                    if($pickup_request->current_rider_id==null)
                    { 
                        $rider_id=$pickup_rider_id;
                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $actual_weight = $shipment->estimated_weight;
                    } else {
                        if ($request->volumetric_weight == "on") {
                            $actual_weight = (($request->length * $request->breadth * $request->height) / 5000);
                            $shipment->length = $request->length;
                            $shipment->breadth = $request->breadth;
                            $shipment->height = $request->height;
                        } else {
                            $actual_weight = $request->weight;
                        }

                        $not_include_shippers1 = ByPassWeightShippers::all()->pluck('shipper_id')->toArray();
                        $not_include_shippers = [6693, 12412];
                        $not_include_shippers = array_merge($not_include_shippers,$not_include_shippers1);

                        if (!in_array($shipment->user_id, $not_include_shippers)) {
                            $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                            if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {
                                $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                if($shipment_estimated_weight->exists()){
                                    $shipment_estimated_weight = $shipment_estimated_weight->first();
                                }
                                else{
                                    $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                }
                                $shipment_estimated_weight->shipment_id = $shipment->id;
                                $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                $shipment_estimated_weight->actual_weight= $actual_weight;
                                if (empty($request->weight)) {
                                    $shipment_estimated_weight->length = $request->length;
                                    $shipment_estimated_weight->breadth = $request->breadth;
                                    $shipment_estimated_weight->height = $request->height;
                                }
                                else{
                                    $shipment_estimated_weight->length = null;
                                    $shipment_estimated_weight->breadth = null;
                                    $shipment_estimated_weight->height = null;
                                }
                                $shipment_estimated_weight->save();
                                $actual_weight = $shipment->estimated_weight;

                                $shipment->length = NULL;
                                $shipment->breadth = NULL;
                                $shipment->height = NULL;
                            }
                        }
                    }
                    if ($shipment->booking_type_id == 4) {
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment->id);
                        if ($international_shipment->exists()) {
                            $city = City::find($shipment->consignee_city_id);
                            $hub_id = $city->hub_id;
                            $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
                            $check = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);

                            if ($shipment->walk_in_delivery_type_id == 1) {
                                $check_actual_weight = $check->door_actual_weight;
                            } else {
                                $check_actual_weight = $check->hub_actual_weight;
                            }
                            if ($actual_weight < $check_actual_weight) {
                                $actual_weight = $check_actual_weight;
                            }
                        } else {
                            $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $shipment->shipping_mode_id, 'delivery_type_id' => $shipment->walk_in_delivery_type_id])->first();
                            if ($actual_weight < $check['actual_weight']) {
                                $actual_weight = $check['actual_weight'];
                            }
                        }
                    }

                    $shipment->actual_weight = $actual_weight;
                    

                    if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
                        $receiving_sheet_shipment->status = 1;
                        $receiving_sheet_shipment->save();

                        $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

                        $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

                        $receiving_sheet->received = $receiving_sheet->received + 1;

                        $receiving_sheet->save();

                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    } else {
                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    }

                    $shipment->shipper_status_id = 2;
                    $shipment->consignee_status_id = 2;

                    $shipment->save();
                    $reference_2_id = null;
                    
                    ShipmentsJourneyController::add($shipment_id, 2, 2, null, $piece_request_remarks, null, Auth::id(), $reference_1_id, $reference_2_id,1,null,$rider_id);

                    $self_collection_shipment = SelfCollectionShipment::where('shipment_id', $shipment_id);
                    if ($self_collection_shipment->exists()) {
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $shipment->shipper_status_id = 15;
                            $shipment->consignee_status_id = 15;

                            $shipment->save();
                            ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                            NotificationsController::send(126, $shipment_id);
                        }
                    }
                    $shipment->refresh();
                    if ($shipment->walk_in_delivery_type_id == 2 && $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                        $shipment->shipper_status_id = 15;
                        $shipment->consignee_status_id = 15;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                        NotificationsController::send(126, $shipment_id);
                    }
                    if ($shipment->booking_type_id == 4) {
                        $print_shipment_ids[] = $shipment_id;
                    }
                    //Consolidated Shipments
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id);
                    if ($consolidated_shipment->exists()) {
                        $consolidated_shipment = $consolidated_shipment->first();
//                $user_shipping_info = UserShippingInfo::find($shipment->pickup_address_id);
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $check_all_consolidation_shipments = true;

                            $shipment->shipper_status_id = 58;
                            $shipment->consignee_status_id = 58;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 58, 58, null, $piece_request_remarks, null, Auth::id());

                            $consolidation_id = $consolidated_shipment->consolidation_id;
                            $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                            foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment) {
                                $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                                if ($check_remaining_consolidated_shipment->shipper_status_id != 58) {
                                    $check_all_consolidation_shipments = false;
                                }
                            }

                            if ($check_all_consolidation_shipments == true) {
                                foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment) {
                                    $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                    $update_all_consolidated_shipment->shipper_status_id = 59;
                                    $update_all_consolidated_shipment->consignee_status_id = 59;

                                    $update_all_consolidated_shipment->save();

                                    ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, null, $piece_request_remarks, null, Auth::id());
                                }
                            }
                        }
                    }
                    //Consolidated Shipments

                    $booking_sms = BookingSmsForShippers::where('user_id', $shipment->user_id)->where('status', 1);
                    if ($booking_sms->exists()) {
                        NotificationsController::send(3, $shipment_id);
                    }
                    if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                        if ($shipment->booking_type_id == 4) {
                            ShipmentChargesController::walkin_weight($shipment_id);
                        } else {
                            ShipmentChargesController::weight($shipment_id);
                            if ($shipment->business_category_id == 1) {
                                ShipmentChargesController::cash_handling($shipment_id);
                                ShipmentChargesController::insurance($shipment_id);
                                ShipmentChargesController::fuel_surcharge($shipment_id);
                            } else {
                                ShipmentChargesController::international_fuel_surcharge($shipment_id);
                            }
                        }

                        if($shipment->walk_in_status == 0) {
                            InitialChargesWebhookController::webhook_subscription($shipment_id);
                        }
                    }

                    if ($shipment->shipment_type != 2 && $shipment->charges_mode_id == 2 && $shipment->booking_type_id != 4) {
                        $shipment = Shipment::find($shipment_id);

                        $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                        $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                        $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                        $shipment->amount = $shipment->amount + $charges + $gst;

                        $shipment->save();

                        $print_shipment_ids[] = $shipment_id;
                    }
                    if (($shipment->charges_mode_id == 2 || $shipment->charges_mode_id == 1) && $shipment->booking_type_id == 4) {
                        $walkin_shipment_ids[] = $shipment->id;
                    }

                }
            } else {
                unset($shipment_ids[$key]);
            }
        }

        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->whereIn('pickup_request_id', $pickup_request_ids);

            if ($pickup_request_shipment->exists()) {
                $pickup_note_id = NULL;
                $pickup_request_shipment = $pickup_request_shipment->first();
                $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                $pickup_request_shipment->status = 1;
                $pickup_request_shipment->save();
                $pickup_request_received_shipment = new V2PickupReceivedShipment();
                $pickup_request_received_shipment->pickup_request_id = $pickup_request_id;
                $pickup_request_received_shipment->shipment_id = $shipment->id;

                $pickup_request = V2PickupRequest::find($pickup_request_id);
                $current_rider_id = $pickup_request->current_rider_id;
                $pickup_note_request = $pickup_request->pickup_note_request;
                if ($pickup_note_request) {
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    if ($current_rider_id == null) {
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        $current_rider_id = $pickup_note->rider_id;
                    }
                }

                $pickup_request_received_shipment->pickup_note_id = $pickup_note_id;
                $pickup_request_received_shipment->rider_id = $current_rider_id;

                $pickup_request_received_shipment->save();
                $pickup_request = $pickup_request_shipment->pickup_request;
                ShipmentsPickupJourneyController::add($shipment_id, 2, Auth::id(), $pickup_request->id);

                $pickup_request->received = $pickup_request->received + 1;
                $pickup_request->status_id = 2;
                $pickup_request->save();


                $pickup_note_request = $pickup_request->pickup_note_request;
                if ($pickup_note_request) {
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    $pickup_note = V2PickupNote::find($pickup_note_id);
                    if ($pickup_note) {
                        $pickup_note->arrived_shipments = $pickup_note->arrived_shipments + 1;
                        $pickup_note->save();
                    }
                }
            }
        }
        $pickup_note_ids = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_request = V2PickupRequest::find($pickup_request_id);

            if ($pickup_request->received >= 1) {
                if ($pickup_rider_id && in_array($pickup_request_id, $unassigned_pickup_requests)) {
                    $pickup_note_id = $this->generate_assigned_pickup($pickup_request_id, $pickup_rider_id);

                    if (!in_array($pickup_note_id, $pickup_note_ids)) {
                        $pickup_note_ids[] = $pickup_note_id;
                    }

                } else {
                    $pickup_note_request = $pickup_request->pickup_note_request;
                    if ($pickup_note_request) {
                        $pickup_note_id = $pickup_note_request->pickup_note_id;
                        $pickup_note_request->status = 1;
                        $pickup_note_request->save();
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        if ($pickup_note) {
                            if ($pickup_note->status == 0) {
                                if (!in_array($pickup_note_id, $pickup_note_ids)) {
                                    $pickup_note_ids[] = $pickup_note_id;
                                }
                            }
                        }
                    }
                    $this->retail_pickup_arrival($pickup_request_id);
                }

            }
        }
        if (!empty($pickup_note_ids)) {
            foreach ($pickup_note_ids as $pickup_note_id) {
                $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                if ($pickup_note_requests_count == 0) {
                    $v2_pickup_note = V2PickupNote::where('id', $pickup_note_id)->first();
                    $v2_pickup_note->status = 1;
                    $v2_pickup_note->save();
                }
            }
        }

        NotificationsController::send(4, $shipment_ids);
        if (count($walkin_shipment_ids) > 0) {
            NotificationsController::send(85, $walkin_shipment_ids, Auth::id());
        }
        if (empty($print_shipment_ids)) {
            return redirect()->back()->with(['success' => 'Arrival Done']);
        } else {
            return redirect()->back()->with(['success' => 'Arrival Done', 'print_shipment_ids' => $print_shipment_ids]);
        }
//        return redirect()->route('admin.v2_pickups.pending.index')->with('success','Shipments arrived Successfully!');

    }

    public function generate_trax_pickup($pickup_request_id)
    {
        $pickup_request = V2PickupRequest::find($pickup_request_id);
        $settings = GlobalSettings::where('type', 'global_rider_id');
        if ($settings->exists()) {
            $settings = $settings->first();
            $rider_id = $settings->setting_value;
            $pickup_request_attempt = new V2PickupRequestAttempt();
            $pickup_request_attempt->pickup_request_id = $pickup_request_id;
            $pickup_request_attempt->rider_id = $rider_id;
            $pickup_request_attempt->attempt_date = Carbon::now();
            $pickup_request_attempt->assigned_by = Auth::id();
            $pickup_request_attempt->save();

            $pickup_request->rider_status = 2;
            $pickup_request->attempts = $pickup_request->attempts + 1;
            $pickup_request->current_rider_id = $rider_id;
            $pickup_request->last_updated_by = Auth::id();
            $pickup_request->save();

            $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->first();

                $pickup_note->pickups += 1;
                $pickup_note->shipments += $pickup_request->booked;

                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            } else {
                $pickup_note = new V2PickupNote();

                $pickup_note->rider_id = $rider_id;
                $pickup_note->pickups = 1;
                $pickup_note->shipments = $pickup_request->booked;
                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            }

            $pickup_note_request = new V2PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;

            $pickup_note_request->save();

            return $rider_id;
        }
    }

    public function generate_assigned_pickup($pickup_request_id, $rider_id)
    {
        $pickup_request = V2PickupRequest::find($pickup_request_id);

        $pickup_request_attempt = new V2PickupRequestAttempt();
        $pickup_request_attempt->pickup_request_id = $pickup_request_id;
        $pickup_request_attempt->rider_id = $rider_id;
        $pickup_request_attempt->attempt_date = Carbon::now();
        $pickup_request_attempt->assigned_by = Auth::id();
        $pickup_request_attempt->save();

        $pickup_request->rider_status = 2;
        $pickup_request->attempts = $pickup_request->attempts + 1;
        $pickup_request->current_rider_id = $rider_id;
        $pickup_request->last_updated_by = Auth::id();
        $pickup_request->save();

        $pickup_note = V2PickupNote::where('rider_id', $rider_id)->where('status', 0);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();

            $pickup_note->pickups += 1;
            $pickup_note->shipments += $pickup_request->booked;

            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;

            $pickup_note_requests = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id);
            if ($pickup_note_requests->exists()) {

                $pickup_note_request = $pickup_note_requests->first();

                $pickup_note_request->pickup_note_id = $pickup_note_id;
                $pickup_note_request->pickup_request_id = $pickup_request_id;
                $pickup_note_request->status = 1;

                $pickup_note_request->save();
            } else {
                $pickup_note_request = new V2PickupNoteRequest();

                $pickup_note_request->pickup_note_id = $pickup_note_id;
                $pickup_note_request->pickup_request_id = $pickup_request_id;
                $pickup_note_request->status = 1;

                $pickup_note_request->save();
            }
        } else {
            $pickup_note = new V2PickupNote();

            $pickup_note->rider_id = $rider_id;
            $pickup_note->pickups = 1;
            $pickup_note->shipments = $pickup_request->booked;
            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;

            $pickup_note_request = new V2PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;
            $pickup_note_request->status = 1;

            $pickup_note_request->save();
        }
        EmployeeAttendanceController::riders_attendance_mark($rider_id);

        return $pickup_note_id;
    }

    public function arrival_individual_index(Request $request)
    {
        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name','trax_id')->get();
        return view('admin.v2_pickups.arrival_individual_weight')->with(['riders' => $riders, 'global_rider_id' => $global_rider_id]);
    }

    public function arrival_individual_shipment_details(Request $request)
    {
         $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();


            //todo: now checking canceled shipment arrival
                $user = ShipmentsJourney::where('shipment_id',$shipment->id)->select('user_id','shipper_status_id')->orderby('id','desc')->first();
                if($user->shipper_status_id == 17)
                {
                    $canceled_shipment = CancelledShipmentArrival::where('shipper_id',$user->user_id)->first();
                    if($canceled_shipment)
                    {
                        return ['status' => 1, 'error' => 'Shipment is not allowed for arrival because shipper cancelled this shipment !'];
                    }
                }
            //todo: now checking canceled shipment arrival end


            $pickup_request_id = null;
            $rider = null;
            $rider_assigned_flag = false;

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if(!$dispute_check){
                return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }
            $shipment_origin = $shipment->pickup_address->city->hub_id;
            if (session('role_id') != 1) {
                if (!in_array($shipment_origin, session('hubs'))) {
                    return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                }
            }

            if ($shipment->warehouse == 1) {
                if ($shipment->warehouse_order_status != 5) {
                    return ['status' => 1, 'error' => 'Shipment is not dispatched yet!'];
                }
            }

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }

            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                if ($shipment->booking_type_id == 3) {

                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;




                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                    $details = array();
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces_count'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                } else {
                    if ($shipment->shipper_status_id == 17) {
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                            //                            $rider = Rider::find($rider_id)->name;
                            $rider = '';
                            $rider_assigned_flag = true;
                            


                        } else {
                            $rider = $pickup_request->rider->name;
                        }
                    } else {
                        AdminPickupsController::generate($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                                $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        }
                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if($project_arrival_include_shippers->exists()){
                            if($shipment->actual_weight == null){
                                $weight_flag = true;
                            }
                            else{
                                $weight_flag = false;
                            }
                        }
                        if($weight_flag == true){
                            if (empty($request->weight)) {

                                $actual_weight = (($request->length * $request->breadth * $request->height) / 5000);

                                if($actual_weight < 0.1){
                                    return ['status' => 1, 'error' => 'Volumetric weight cannot be less than 0.1'];
                                }
                                $shipment->length = $request->length;
                                $shipment->breadth = $request->breadth;
                                $shipment->height = $request->height;
                            } else {
                                $actual_weight = $request->weight;
                            }
                        }
                        else{
                            $actual_weight = $shipment->actual_weight;
                        }

                        $not_include_shippers1 = ByPassWeightShippers::all()->pluck('shipper_id')->toArray();

                        $not_include_shippers = [6693, 12412];

                        $not_include_shippers = array_merge($not_include_shippers,$not_include_shippers1);

                        if (!in_array($shipment->user_id, $not_include_shippers)) {
                            $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                            if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {

                                $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                if($shipment_estimated_weight->exists()){
                                    $shipment_estimated_weight = $shipment_estimated_weight->first();
                                }
                                else{
                                    $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                }
                                $shipment_estimated_weight->shipment_id = $shipment->id;
                                $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                $shipment_estimated_weight->actual_weight= $actual_weight;
                                if (empty($request->weight)) {
                                    $shipment_estimated_weight->length = $request->length;
                                    $shipment_estimated_weight->breadth = $request->breadth;
                                    $shipment_estimated_weight->height = $request->height;
                                }
                                else{
                                    $shipment_estimated_weight->length = null;
                                    $shipment_estimated_weight->breadth = null;
                                    $shipment_estimated_weight->height = null;
                                }
                                $shipment_estimated_weight->save();

                                $actual_weight = $shipment->estimated_weight;

                                $shipment->length = NULL;
                                $shipment->breadth = NULL;
                                $shipment->height = NULL;
                            }
                        }
                    }
                    if ($shipment->booking_type_id == 4) {
                        $international_shipment = InternationalShipment::where('shipment_id', $shipment->id);
                        if ($international_shipment->exists()) {
                            $city = City::find($shipment->consignee_city_id);
                            $hub_id = $city->hub_id;
                            $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
                            $check = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);

                            if ($shipment->walk_in_delivery_type_id == 1) {
                                $check_actual_weight = $check->door_actual_weight;
                            } else {
                                $check_actual_weight = $check->hub_actual_weight;
                            }
                            if ($actual_weight < $check_actual_weight) {
                                $actual_weight = $check_actual_weight;
                            }
                        } else {
                            $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $shipment->shipping_mode_id, 'delivery_type_id' => $shipment->walk_in_delivery_type_id])->first();
                            if ($actual_weight < $check['actual_weight']) {
                                $actual_weight = $check['actual_weight'];
                            }
                        }
                    }
                    //here update amount
                    // if($shipment->booking_type_id==6){

                        // $ftl_request = FtlRequest::where('shipment_id',$shipment->id)->first();
                        // $other_amount = FtlRequestAdditionalCost::where('ftl_request_id',$ftl_request->id)->sum('amount');
                        // //amount or received_amount need to confirm
                        // $shipment->amount= ((($ftl_request->freight_charges/$ftl_request->weight)*$actual_weight)-$other_amount);
                    // }
                    $shipment->actual_weight = $actual_weight;
                    $shipment->save();

                    $rider_picked = false;
                    if(!$rider_assigned_flag){
                        $v2_pickup_note_request = V2PickupNoteRequest::where('pickup_request_id',$pickup_request_id)->latest()->first();
                        if($v2_pickup_note_request){
                            $check_journey = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',53)->where('reference_1_id',$pickup_request_id)->where('reference_2_id',$v2_pickup_note_request->pickup_note_id)->latest()->first();
                            if($check_journey){
                                $rider_picked = true;
                            }
                        } 
                    
                    }


                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['weight'] = floatval($shipment->actual_weight);
                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['rider_picked'] = $rider_picked;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    $id = Auth::user();

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];

                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            $shipment_item = ShipmentItem::find($request->tracking_number);
            if ($shipment_item) {
                $shipment = Shipment::find($shipment_item->shipment_id);
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['scanned_shipment_item'] = $shipment_item->id;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;


                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number);
                if ($shipment_pieces->exists()) {
                    $shipment_pieces = $shipment_pieces->first();
                    $shipment = Shipment::find($shipment_pieces->shipment_id);
                    if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                        $details = array();
                        $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                        $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                        $details['city'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null, $shipment_pieces->id);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                    }
                }
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function arrival_try_and_buy_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                $rider_assigned_flag = false;
                if ($shipment->booking_type_id == 3) {
                    if ($shipment->shipper_status_id == 17) {
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                            //                            $rider = Rider::find($rider_id)->name;
                            $rider = '';
                            $rider_assigned_flag = true;
                        } else {
                            $rider = $pickup_request->rider->name;
                        }
                    } else {
                        AdminPickupsController::generate($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                                $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        }
                    }
                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if($project_arrival_include_shippers->exists()){
                            if($shipment->actual_weight == null){
                                $weight_flag = true;
                            }
                            else{
                                $weight_flag = false;
                            }
                        }
                        if($weight_flag == true){
                            if ($request->has('weight')) {
                                $shipment->actual_weight = $request->weight;
                            }
                        }
                    }
                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['weight'] = floatval($shipment->actual_weight);
                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function arrival_individual_shipment_remove(Request $request)
    {
        $shipment = Shipment::find($request->id);

        if ($shipment) {
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                $shipment->actual_weight = null;
                $shipment->length = null;
                $shipment->breadth = null;
                $shipment->height = null;

                $shipment->save();

                return ['status' => 0, 'success' => 'Shipment has been removed'];
            } else {
                return ['status' => 1, 'error' => 'Given Shipment ID has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given ID is present'];
        }
    }

    public function individual_arrival_submit(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);

        $pickup_request_ids = array();

        $print_shipment_ids = array();

        $unassigned_pickup_requests = array();

        $settings = GlobalSettings::where('type', 'global_rider_id');
        $pickup_rider_id = null;
        if ($settings->exists()) {
            $settings = $settings->first();
            $pickup_rider_id = $settings->setting_value;
        }

        if ($pickup_rider_id) {
            $unassigned_pickup_requests = explode(',', $request->pickup_request_ids);
        }

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                if ($shipment->actual_weight == null) {
                    unset($shipment_ids[$key]);
                    continue;
                }
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                    $piece_request_remarks = null;
                    if ($shipment->shipper_status_id == 62) {
                        $shipment_pieces_request = ShipmentPiecesRequest::where('shipment_id', $shipment->id)->where('status', 1);
                        if ($shipment_pieces_request->exists()) {
                            $shipment_pieces_request = $shipment_pieces_request->first();
                            $shipment_pieces_request->status = 2;
                            $shipment_pieces_request->request_status_id = 4;
                            $shipment_pieces_request->last_updated_by_admin = Auth::id();
                            $shipment_pieces_request->last_updated_at = Carbon::now();
                            $shipment_pieces_request->department_id = session('department_id');
                            $shipment_pieces_request->save();
                            $piece_request_remarks = 'Resolved through Arrival';
                        }
                    }

                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0)->orderBy('id', 'DESC')->first();
                     //region Taha
                     $pickup_request=V2PickupRequest::where('id', $pickup_request_shipment->pickup_request_id)->first();
                     //endregion
                     
                    if ($pickup_request_shipment) {
                        $reference_1_id = $pickup_request_shipment->pickup_request_id;
                        $rider_id=$pickup_request->current_rider_id;

                        if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                            $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;
                        }
                    } else {
                        $reference_1_id = null;
                    }
                    if($pickup_request->current_rider_id==null)
                    { 
                        $rider_id=$pickup_rider_id;
                    }
                    if ($receiving_sheet_shipment = $shipment->receiving_sheet_shipment) {
                        $receiving_sheet_shipment->status = 1;
                        $receiving_sheet_shipment->save();

                        $receiving_sheet_id = $receiving_sheet_shipment->receiving_sheet_id;

                        $receiving_sheet = $receiving_sheet_shipment->receiving_sheet;

                        $receiving_sheet->received = $receiving_sheet->received + 1;

                        $receiving_sheet->save();

                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->receiving_sheet_id = $receiving_sheet_id;
                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    } else {
                        if (!ReceivingSheetReceived::where('shipment_id', $shipment_id)->exists()) {
                            $receiving_sheet_received = new ReceivingSheetReceived();

                            $receiving_sheet_received->user_id = $shipment->user_id;
                            $receiving_sheet_received->pickup_address_id = $shipment->pickup_address_id;
                            $receiving_sheet_received->shipment_id = $shipment_id;

                            $receiving_sheet_received->save();
                        }
                    }

                    $shipment->shipper_status_id = 2;
                    $shipment->consignee_status_id = 2;

                    $shipment->save();
                    $reference_2_id = null;
                    ShipmentsJourneyController::add($shipment_id, 2, 2, null, $piece_request_remarks, null, Auth::id(), $reference_1_id, $reference_2_id,1,null,$rider_id);

                    $self_collection_shipment = SelfCollectionShipment::where('shipment_id', $shipment_id);
                    if ($self_collection_shipment->exists()) {
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $shipment->shipper_status_id = 15;
                            $shipment->consignee_status_id = 15;

                            $shipment->save();
                            ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                            NotificationsController::send(126, $shipment_id);
                        }
                    }
                    $shipment->refresh();
                    if ($shipment->walk_in_delivery_type_id == 2 && $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                        $shipment->shipper_status_id = 15;
                        $shipment->consignee_status_id = 15;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 15, 15, null, $piece_request_remarks, null, Auth::id());
                        NotificationsController::send(126, $shipment_id);
                    }
                    if ($shipment->booking_type_id == 4) {
                        $print_shipment_ids[] = $shipment_id;
                    }
                    //Consolidated Shipments
                    $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                    if ($consolidated_shipment) {
//                $user_shipping_info = UserShippingInfo::find($shipment->pickup_address_id);
                        if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                            $check_all_consolidation_shipments = true;

                            $shipment->shipper_status_id = 58;
                            $shipment->consignee_status_id = 58;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 58, 58, null, $piece_request_remarks, null, Auth::id());

                            $consolidation_id = $consolidated_shipment->consolidation_id;
                            $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                            foreach ($remaining_consolidated_shipments as $remaining_consolidated_shipment) {
                                $check_remaining_consolidated_shipment = Shipment::find($remaining_consolidated_shipment->shipment_id);
                                if ($check_remaining_consolidated_shipment->shipper_status_id != 58) {
                                    $check_all_consolidation_shipments = false;
                                }
                            }

                            if ($check_all_consolidation_shipments == true) {
                                foreach ($remaining_consolidated_shipments as $update_remaining_consolidated_shipment) {
                                    $update_all_consolidated_shipment = Shipment::find($update_remaining_consolidated_shipment->shipment_id);

                                    $update_all_consolidated_shipment->shipper_status_id = 59;
                                    $update_all_consolidated_shipment->consignee_status_id = 59;

                                    $update_all_consolidated_shipment->save();

                                    ShipmentsJourneyController::add($update_remaining_consolidated_shipment->shipment_id, 59, 59, null, $piece_request_remarks, null, Auth::id());
                                }
                            }
                        }
                    }
                    //Consolidated Shipments

                    $booking_sms = BookingSmsForShippers::where('user_id', $shipment->user_id)->where('status', 1);
                    if ($booking_sms->exists()) {
                        NotificationsController::send(3, $shipment_id);
                    }
                    if ($shipment->packaging_material_request == 0 && $shipment->shipment_type == 1) {
                        if ($shipment->booking_type_id == 4) {
                            ShipmentChargesController::walkin_weight($shipment_id);
                        } else {
                            ShipmentChargesController::weight($shipment_id);
                            if ($shipment->business_category_id == 1) {
                                ShipmentChargesController::cash_handling($shipment_id);
                                ShipmentChargesController::insurance($shipment_id);
                                ShipmentChargesController::fuel_surcharge($shipment_id);
                            } else {
                                ShipmentChargesController::international_fuel_surcharge($shipment_id);
                            }
                        }

                        if($shipment->walk_in_status == 0) {
                            InitialChargesWebhookController::webhook_subscription($shipment_id);
                        }
                    }



                    if ($shipment->shipment_type != 2 && $shipment->charges_mode_id == 2 && $shipment->booking_type_id != 4) {
                        $shipment = Shipment::find($shipment_id);

                        $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge;

                        $gst = Zone::find($shipment->pickup_address->city->zone_id)->gst;

                        $gst = ROUND(($charges * $gst), 0, PHP_ROUND_HALF_DOWN);

                        $shipment->amount = $shipment->amount + $charges + $gst;

                        $shipment->save();

                        $print_shipment_ids[] = $shipment_id;
                    }
                    if (($shipment->charges_mode_id == 2 || $shipment->charges_mode_id == 1) && $shipment->booking_type_id == 4) {
                        $shipment_ids = array($shipment->id);
                        NotificationsController::send(85, $shipment_ids, Auth::id());
                    }
                }
            } else {
                unset($shipment_ids[$key]);
            }
        }

        $pickup_note_ids = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
            $pickup_request = V2PickupRequest::find($pickup_request_id);
            if ($pickup_request->received >= 1) {
                if ($pickup_rider_id && in_array($pickup_request_id, $unassigned_pickup_requests)) {
                    $pickup_note_id = $this->generate_assigned_pickup($pickup_request_id, $pickup_rider_id);

                    if (!in_array($pickup_note_id, $pickup_note_ids)) {
                        $pickup_note_ids[] = $pickup_note_id;
                    }

                } else {
                    $pickup_note_request = $pickup_request->pickup_note_request;
                    if ($pickup_note_request) {
                        $pickup_note_id = $pickup_note_request->pickup_note_id;
                        $pickup_note_request->status = 1;
                        $pickup_note_request->save();
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        if ($pickup_note) {
                            if ($pickup_note->status == 0) {
                                if (!in_array($pickup_note_id, $pickup_note_ids)) {
                                    $pickup_note_ids[] = $pickup_note_id;
                                }
                            }
                        }
                    }
                    $this->retail_pickup_arrival($pickup_request_id);
                }

            }
        }

        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->whereIn('pickup_request_id', $pickup_request_ids);

            if ($pickup_request_shipment->exists()) {
                $pickup_note_id = NULL;
                $pickup_request_shipment = $pickup_request_shipment->first();
                $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                $pickup_request_shipment->status = 1;
                $pickup_request_shipment->save();
                $pickup_request_received_shipment = new V2PickupReceivedShipment();
                $pickup_request_received_shipment->pickup_request_id = $pickup_request_id;
                $pickup_request_received_shipment->shipment_id = $shipment->id;

                $pickup_request = V2PickupRequest::find($pickup_request_id);
                $current_rider_id = $pickup_request->current_rider_id;
                $pickup_note_request = $pickup_request->pickup_note_request;
                if ($pickup_note_request) {
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    if ($current_rider_id == null) {
                        $pickup_note = V2PickupNote::find($pickup_note_id);
                        $current_rider_id = $pickup_note->rider_id;
                    }
                }

                $pickup_request_received_shipment->pickup_note_id = $pickup_note_id;
                $pickup_request_received_shipment->rider_id = $current_rider_id;

                $pickup_request_received_shipment->save();
                $pickup_request = $pickup_request_shipment->pickup_request;
                ShipmentsPickupJourneyController::add($shipment_id, 2, Auth::id(), $pickup_request->id);

                $pickup_request->received = $pickup_request->received + 1;
                $pickup_request->status_id = 2;
                $pickup_request->save();


                $pickup_note_request = $pickup_request->pickup_note_request;
                if ($pickup_note_request) {
                    $pickup_note_id = $pickup_note_request->pickup_note_id;
                    $pickup_note = V2PickupNote::find($pickup_note_id);
                    if ($pickup_note) {
                        $pickup_note->arrived_shipments = $pickup_note->arrived_shipments + 1;
                        $pickup_note->save();
                    }
                }
            }
        }

        if (!empty($pickup_note_ids)) {
            foreach ($pickup_note_ids as $pickup_note_id) {
                $pickup_note_requests_count = V2PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('status', 0)->count();
                if($pickup_note_requests_count == 0){
                    $v2_pickup_note = V2PickupNote::where('id', $pickup_note_id)->first();
                    $v2_pickup_note->status = 1;
                    $v2_pickup_note->save();
                }
            }
        }
        NotificationsController::send(4, $shipment_ids);

        //todo: send sms for self collection!
        foreach ($shipment_ids as $shipment_id) {
            $self_collection_shipment = SelfCollectionShipment::where('shipment_id',$shipment_id)->first();
            if($self_collection_shipment)
            {

                $shipment = Shipment::where('id',$shipment_id)->first();
                $user_city = $shipment->user->city_id;

//                $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id
//                if($shipment->consignee_city_id == $user_city)
                if($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id)
                {
                    if($shipment->consignee_city->hub_id == '202' || $shipment->consignee_city->hub_id == '223')
                    {
                        NotificationsController::send(178, $shipment_id);
                    }
                    else
                    {
                        $city_id = SelfCollectionCities::where('city_id', $shipment->consignee_city->hub_id)->select('city_id','address')->first();
                        NotificationsController::send(75, $shipment_id, $city_id->address);
                    }
                }
            }
        }
        //todo: send sms for self collection end!

        if (empty($print_shipment_ids)) {
            return redirect()->back()->with(['success' => 'Arrival Done']);
        } else {
            return redirect()->back()->with(['success' => 'Arrival Done', 'print_shipment_ids' => $print_shipment_ids]);
        }
//        return redirect()->route('admin.v2_pickups.pending.index')->with('success','Shipments arrived Successfully!');

    }

    public function assigned_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      .vendor_pickup_row{
                        background-color: var(--light);
                      }
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';

        foreach ($request->ids as $id) {
            $pickup_note = V2PickupNote::find($id);
            $rider = Rider::find($pickup_note->rider_id);
            $route = $rider->route;
            $route_name = '';
            if ($route) {
                $route_name = $route->code . ' (' . $route->start . ' to ' . $route->end . ')';
            }
            $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Trax ID</strong></td>
                            <td>' . $rider->trax_id . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                          <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td> ' . $pickup_note->rider->city->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

            $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Vendor</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Sales Person</strong></td>
                            <td class="color primary"><strong>Person of Contact</strong></td>
                            <td class="color primary"><strong>Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

            $serial_number = 1;

            $pickup_note_requests = $pickup_note->pickup_note_requests;
            $reverse_pickup_shipment_ids = array();
            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = $pickup_note_request->pickup_request;

                $shipper = $pickup_request->shipper;
                $pickup_address = $pickup_request->pickup_address;
                $poc = SalePersonTag::join('admins as ad', 'ad.id', '=', 'sale_person_tags.admin_id')
                    ->leftjoin('users as us', 'us.id', '=', 'sale_person_tags.user_id')
                    ->leftjoin('shipper_contacts as sc', 'sc.shipper_id', '=', 'us.id')
                    ->where('sale_person_tags.status', 0)->where('us.id', $shipper->id)
                    ->select('ad.name as admin_name', 'ad.phone_number as admin_phone_number', 'sc.phone_number as phone_number', 'sc.poc')->get()->toArray();
//dd($poc);
                $pocName = "";
                $phoneNo = "";
                $names = "";
                $i = 0;
                foreach ($poc as $data) {
                    if ($i == null) {
                        if ($i == 0) {
                            $pocName .= '' . $data['poc'];
                            $phoneNo .= ' ' . $data['admin_phone_number'] . ',';
                            $phoneNo .= '' . $data['phone_number'];
                            $names = $data['admin_name'];
                            $i++;
                        } else {
                            $pocName .= ',' . $data['poc'];
                            $phoneNo .= ',' . $data['phone_number'];
                            $phoneNo .= ',' . $data['admin_phone_number'];

                        }
                    }
                }
                $color = '';
                if ($pickup_address->vendor != null) {
                    $color = 'vendor_pickup_row';
                }

                $html .= '
                          <tr class="' . $color . '">
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['vendor'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $names . '</td>
                            <td>' . $pocName . '</td>
                            <td>' . $phoneNo . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['booked'] . '</td>
                            <td>' . Carbon::parse($pickup_request['pickup_date'])->format('Y-m-d') . '</td>
                          </tr>
          ';

                $serial_number++;

                $pickup_request_shipments = $pickup_request->pickup_request_shipments;
                if ($pickup_request_shipments) {
                    foreach ($pickup_request_shipments as $pickup_request_shipment) {
                        if (Shipment::where('id', $pickup_request_shipment->shipment_id)->where('booking_type_id', 5)->exists()) {
                            $reverse_pickup_shipment_ids[] = $pickup_request_shipment->shipment_id;
                        }
                    }
                }
            }

            $html .= '
                        </tbody>
                      </table>

                      <hr>
        ';
            if (count($reverse_pickup_shipment_ids) > 0) {
                $airway_bill_html = '';
                $airway_bill_html = $this->print_air_waybill($reverse_pickup_shipment_ids, $rider->name);
                $html .= $airway_bill_html;
//                return response()->json(['status' => 0, 'shipment_ids' => $reverse_pickup_shipment_ids, 'rider_name' => $rider->name]);
            }
        }

        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }

    public function v2_pickups_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 7);
        $pickup_types = [['id' => 0, 'text' => 'Not Pick'], ['id' => 1, 'text' => 'Pick']];
        $pickup_not_pick_reasons = V2PickupRequestNotPickReason::all();

        return view('admin.v2_pickups.rider_pickups')->with(['pickup_types' => $pickup_types, 'pickup_not_pick_reasons' => $pickup_not_pick_reasons]);
    }

    public function pickups_list_v2(Request $request)
    {

        $excel = false;
        if ($request->get('excel') && $request->get('excel') == true) {
            $excel = true;
            ActivityTrailController::createActivityTrailLog(Auth::id(), 67);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $pickup_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp1` where `rp1`.`pickup_type` = 1 and `rp1`.`created_at` Between "' . $from . '" AND "' . $to . '") AS `pickup_picked`');
            $pickup_not_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp` where `rp`.`pickup_type` = 0 and `rp`.`created_at` Between "' . $from . '" AND "' . $to . '") AS `pickup_not_picked`');
        } else {
            $pickup_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp1` where `rp1`.`pickup_type` = 1) AS `pickup_picked`');
            $pickup_not_picked = DB::raw('(SELECT COUNT(*) FROM `v2_rider_pickups` AS `rp` where `rp`.`pickup_type` = 0) AS `pickup_not_picked`');
        }

        $rider_pickups = V2RiderPickup::leftjoin('v2_pickup_request_not_pick_reasons as pnpr', 'v2_rider_pickups.pickup_not_pick_reason_id', 'pnpr.id')
            ->join('v2_pickup_notes as pn', 'v2_rider_pickups.pickup_note_id', 'pn.id')
            ->join('v2_pickup_requests as pr', 'v2_rider_pickups.pickup_request_id', 'pr.id')
            ->join('riders as r', 'pn.rider_id', 'r.id')
            ->join('users as u', 'pr.shipper_id', 'u.id')
            ->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
            ->join('cities as c', 'usi.city_id', 'c.id')
            ->select('v2_rider_pickups.id', 'v2_rider_pickups.added_at', 'r.name as rider', 'u.name as shipper', 'usi.pickup_address', 'c.name as city', 'v2_rider_pickups.pickup_type', 'v2_rider_pickups.created_at', 'v2_rider_pickups.start_location_latitude', 'v2_rider_pickups.start_location_longitude', 'v2_rider_pickups.actual_location_latitude', 'v2_rider_pickups.actual_location_longitude', 'v2_rider_pickups.distance_from_start_to_actual', 'v2_rider_pickups.current_location_latitude', 'v2_rider_pickups.current_location_longitude', 'v2_rider_pickups.distance_from_current_to_actual', 'v2_rider_pickups.shipments', 'pnpr.name as reason', 'v2_rider_pickups.picture_path', 'v2_rider_pickups.pickup_note_id', 'v2_rider_pickups.pickup_request_id', $pickup_not_picked, $pickup_picked, 'v2_rider_pickups.rider_remarks as rider_remarks', 'v2_rider_pickups.audio_path');
        if (session('role_id') != 1) {
            $rider_pickups = $rider_pickups->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($rider_pickups)
            ->editColumn('pickup_note_id', function ($rider_pickup) {
                return str_pad($rider_pickup->pickup_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_request_id', function ($rider_pickup) {
                return str_pad($rider_pickup->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_type', function ($rider_pickup) {
                if ($rider_pickup->pickup_type == 0) {
                    return 'Not Pick';
                } else {
                    return 'Pick';
                }
            })
            ->editColumn('distance_from_start_to_actual', function ($rider_pickup) use ($excel) {
                $distance_from_start_to_actual = $rider_pickup->distance_from_start_to_actual;

                if ($distance_from_start_to_actual == 0) {
                    $distance_from_start_to_actual = 0;
                }

                if ($excel) {
                    return $distance_from_start_to_actual;
                }
                return '<a class="btn btn-sm btn-outline-info align-middle" href="http://maps.google.com/maps?saddr=' . $rider_pickup->start_location_latitude . ',' . $rider_pickup->start_location_longitude . '&daddr=' . $rider_pickup->actual_location_latitude . ',' . $rider_pickup->actual_location_longitude . '" target="_blank">' . $distance_from_start_to_actual . '</a>';
            })
            ->editColumn('distance_from_current_to_actual', function ($rider_pickup) use ($excel) {
                $distance_from_current_to_actual = $rider_pickup->distance_from_current_to_actual;

                if ($distance_from_current_to_actual == 0) {
                    $distance_from_current_to_actual = 0;
                }

                if ($rider_pickup->current_location_latitude && $rider_pickup->current_location_longitude && !$excel) {
                    return '<a class="btn btn-sm btn-outline-info align-middle" href="http://maps.google.com/maps?saddr=' . $rider_pickup->current_location_latitude . ',' . $rider_pickup->current_location_longitude . '&daddr=' . $rider_pickup->actual_location_latitude . ',' . $rider_pickup->actual_location_longitude . '" target="_blank">' . $distance_from_current_to_actual . '</a>';
                } else {
                    return $distance_from_current_to_actual;
                }
            })
            ->editColumn('picture_path', function ($rider_pickup) use ($excel) {
                if ($rider_pickup->pickup_type == 0) {
                    $image = '';
                    if ($rider_pickup->picture_path != null) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($rider_pickup->picture_path)) . '"><i class="la la-image"></i> View</button></div>';

                        if ($excel) {
                            return asset(Storage::url($rider_pickup->picture_path));
                        }
                        return $image;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('signature_via_app', function ($rider_pickup) use ($excel) {
                if ($rider_pickup->pickup_type == 1) {
                    $image = '';
                    if ($rider_pickup->picture_path != null) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm signature" data-link="' . asset(Storage::url($rider_pickup->picture_path)) . '"><i class="la la-image"></i> View</button></div>';
                        if ($excel) {
                            return asset(Storage::url($rider_pickup->picture_path));
                        }
                        return $image;
                    }
                } else {
                    return '-';
                }
            })
            ->editColumn('audio_path', function ($rider_pickup) use ($excel) {
                $audio = '';
                if ($rider_pickup->audio_path != null) {
                    $audio .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm audio" data-link="' . asset(Storage::url($rider_pickup->audio_path)) . '"><i class="la la-file-sound-o"></i> Listen</button></div>';
                    if ($excel) {
                        return asset(Storage::url($rider_pickup->audio_path));
                    }
                    return $audio;
                } else {
                    return '-';
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $rider_pickups->whereBetween('v2_rider_pickups.created_at', [$from, $to]);
        }
        return $datatables->make(true);
    }
    public function pickups_action_log_index_v2()
    {

        ActivityTrailController::createActivityTrailLog(Auth::id(), 8);
        $pickup_actions = PickupAction::all();
        $riders = DB::connection('reports')->table('riders')->get(['id', 'name']);
        $admins = DB::connection('reports')->table('admins')->get(['id', 'name']);
        $cities = DB::connection('reports')->table('cities')->get(['id', 'name']);
        return view('admin.v2_pickups.action_log.index')->with(['pickup_actions' => $pickup_actions, 'riders' => $riders, 'admins' => $admins, 'cities' => $cities]);
    }
    public function pickups_action_log_list_v2(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 68);
        }
        $rider_pickup_action_logs = V2RiderPickupActionLog::join('pickup_actions as pa', 'v2_rider_pickup_action_logs.type_id', 'pa.id')
            ->join('v2_pickup_notes as pn', 'v2_rider_pickup_action_logs.pickup_note_id', 'pn.id')
            ->join('v2_pickup_requests as pr', 'v2_rider_pickup_action_logs.pickup_request_id', 'pr.id')
            ->join('v2_pickup_request_attempts as pra', 'pra.pickup_request_id', 'v2_rider_pickup_action_logs.pickup_request_id')
            ->join('riders as r', 'pr.current_rider_id', 'r.id')
            ->join('users as u', 'pr.shipper_id', 'u.id')
            ->join('user_shipping_infos as usi', 'pr.pickup_address_id', 'usi.id')
            ->join('cities as c', 'usi.city_id', 'c.id')
            ->select('v2_rider_pickup_action_logs.id', 'v2_rider_pickup_action_logs.logged_at', 'r.name as rider', 'u.name as shipper', 'usi.pickup_address', 'c.name as city', 'pa.name as type', 'v2_rider_pickup_action_logs.pickup_note_id', 'v2_rider_pickup_action_logs.pickup_request_id', 'pr.created_at', 'pra.assigned_by', 'pr.city_id as city_id', 'pr.current_rider_id');

        if (session('role_id') != 1) {
            $rider_pickup_action_logs = $rider_pickup_action_logs->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($rider_pickup_action_logs)
        // ->editColumn('pickup_note_id', function ($rider_pickup_action_log) {
        //     return str_pad($rider_pickup_action_log->pickup_note_id, 6, '0', STR_PAD_LEFT);
        // })
            ->editColumn('pickup_request_id', function ($rider_pickup_action_log) {
                return str_pad($rider_pickup_action_log->pickup_request_id, 6, '0', STR_PAD_LEFT);
            });

        if ($pn_no = $request->get('search_pn_no')) {
            $rider_pickup_action_logs->where('v2_rider_pickup_action_logs.pickup_request_id', '=', $pn_no);
        }
        if ($assigned_by = $request->get('search_assigned_by')) {
            $rider_pickup_action_logs->where('pra.assigned_by', '=', $assigned_by);
        }
        if ($rider = $request->get('search_rider')) {
            $rider_pickup_action_logs->where('pr.current_rider_id', '=', $rider);
        }
        if ($city = $request->get('search_city')) {
            $rider_pickup_action_logs->where('pr.city_id', '=', $city);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $rider_pickup_action_logs->whereBetween('pr.created_at', [$from, $to]);
        }

        return $datatables->make(true);
    }

    public function arrival_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                ShipmentScanningJourneyController::add($shipment_id, 1, 1, Auth::id(), null, null, $shipment_piece->id);
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }

        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }
    public function arrival_piece_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                if ($shipment->pieces > 1) {
                    $rider_assigned_flag = false;
                    if ($shipment->shipper_status_id == 17) {
                        AdminPickupsController::generate($shipment->id);
                    }
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V2PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
                            //                            $rider = Rider::find($rider_id)->name;
                            $rider = '';
                            $rider_assigned_flag = true;
                        } else {
                            $rider = $pickup_request->rider->name;
                        }
                    } else {
                        AdminPickupsController::generate($shipment->id);

                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if ($pickup_request_shipment->exists()) {
                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                            $pickup_request = V2PickupRequest::find($pickup_request_id);
                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
                                //                                $rider = Rider::find($rider_id)->name;
                                $rider = '';
                                $rider_assigned_flag = true;
                            } else {
                                $rider = $pickup_request->rider->name;
                            }
                        }
                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if($project_arrival_include_shippers->exists()){
                            if($shipment->actual_weight == null){
                                $weight_flag = true;
                            }
                            else{
                                $weight_flag = false;
                            }
                        }
                        if($weight_flag == true){
                            if ($request->has('weight')) {
                                $shipment->actual_weight = $request->weight;
                            }
                        }
                    }
                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['amount'] = $shipment->amount;
                    $details['weight'] = floatval($shipment->actual_weight);
                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public static function cancel($shipment_id)
    {
        $pickup_request_assigned_shipment = V2PickupRequestShipment::where('shipment_id', $shipment_id)->where('status', 0)->orderBy('id', 'desc');

        if ($pickup_request_assigned_shipment->exists()) {
            $pickup_request_assigned_shipment = $pickup_request_assigned_shipment->first();

            $pickup_request = $pickup_request_assigned_shipment->pickup_request;

            $bookings = $pickup_request->booked - 1;

            $pickup_request->booked = $bookings;

            $pickup_request->save();

            ShipmentsPickupJourneyController::add($shipment_id, 4, null, $pickup_request->id);

            $pickup_request_assigned_shipment->delete();

            if ($bookings == 0) {
                $pickup_request->status_id = 4;

                $pickup_request->save();

                if ($pickup_request->pickup_note_request) {
                    $pickup_note = $pickup_request->pickup_note_request->pickup_note;

                    if ($bookings == 0) {
                        V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request->id)->delete();
                    }

                    $pickup_note_requests = V2PickupNoteRequest::where('pickup_note_id', $pickup_note->id);

                    if ($pickup_note_requests->exists()) {
                        if ($bookings == 0) {
                            $pickup_note->pickups = $pickup_note->pickups - 1;
                        }
                        $pickup_note->shipments = $pickup_note->shipments - 1;

                        $pickup_note->save();
                    } else {
                        $pickup_note->pickups = 0;
                        $pickup_note->shipments = 0;
                        $pickup_note->status = 1;

                        $pickup_note->save();
                    }
                }
            }
        }
    }

    public function print_air_waybill($shipment_ids, $rider_name)
    {
        $user_type = null;
        $user_id = null;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();

            $user_name = Auth::user()->name . ' (Admin) #' . $user_id;
        } else {
            $user_name = 'Unknown';
        }

        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        if ($user_type) {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

            $html = '<div>';

            $shipment_details = '';

            foreach ($shipment_ids as $shipment_id) {

                $shipment = Shipment::where('id', $shipment_id)->first();

                $table_start = '<table class="table table-sm table-bordered border twice mb-0" style="page-break-before: always; min-height: 80px;" >
                                <tbody><tr><td class="align-middle" style="width: 40%;">I hereby confirm that i have picked the shipment mentioned in the Description field</td><td class="align-middle" style="width: 30%;"><span class="font-weight-bold">Rider Name: </span><span class="line">' . $rider_name . '</span></td><td class="align-middle" style="width: 30%;"><span class="font-weight-bold">Rider Signature: </span><span class="w-200 ml-auto line"></span></td></tr></tbody>
                      </table>
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
                            ';
                $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                            <td class="color primary"><strong>Datetime</strong></td>
                            <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                            <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                ';

                $table_start .= '
                                <td class="color primary"><strong>Order ID</strong></td>
                                <td>' . $shipment->order_id . '</td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                                <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                              </tr>
                              <tr>
                                <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                                <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                              </tr>
                              <tr>
                                <td class="color secondary"><strong>Name</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->user->name . ' (' . $shipment->pickup_address->poc . ')</td>
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                                <td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                    <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                                <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              </tr>
                ';

                $table_end = '
                              <tr>
                                <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Estimated Weight</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . ' kg</strong></td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                ';

                $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                    ';

                $table_end .= '
                              </tr>';
                if($shipment->shipment_detail()->exists()){
                    if($shipment->shipment_detail->is_open==1){
                    $table_end .= '<tr>
                                <td colspan="2" class="color primary border twice-top twice-bottom twice-left"><strong>Open Box</strong></td>
                                <td colspan="4" class="border twice-top twice-bottom twice-left"><strong> Yes <span><img src="' . asset('img/open_box_icon.png') . '" ></span></strong></td>

                                </tr>';
                }
                }
                $table_end .= '<tr>
                                <td colspan="8" class="text-center border twice-top"><em>Kindly do not give any addtional charges to the Rider/Courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                              </tr>
                            </tbody>
                          </table>

                          <hr>
                ';

                $shipment_details .= $table_start;

                $item = $shipment->items->first();

                $shipment_details .= '
                            <tr>
                              <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                              <td class="color secondary border twice-top"><strong>Type</strong></td>
                              <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                              <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                              <td>' . $item->quantity . '</td>
                              <td colspan="2" class="border twice-top"></td>
                            </tr>
                            <tr>
                              <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                              <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                            </tr>
                ';

                $shipment_details .= $table_end;

            }
            $html .= $shipment_details;
            $html .= '</div>';

            return $html;
        }
    }

    public function rider_receiving_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 9);
        $pickup_actions = PickupAction::all();
        $default_date = Carbon::now();
        $riders = Rider::select('id', 'name')->where('status', 1)->get();
        $cities = City::select('id', 'name')->get();
        return view('admin.v2_pickups.receiving_sheet')->with(['riders' => $riders, 'cities' => $cities, 'pickup_actions' => $pickup_actions, 'default_date' => $default_date]);
    }

    public function rider_receiving_check_pickup(Request $request)
    {
        $pickup_date = $request->pickup_date;
        $rider_id = $request->rider_id;
        if ($pickup_date != null && $rider_id != null) {
            $pickup_note = V2PickupNote::whereDate('created_at', $pickup_date)->where('rider_id', $rider_id);
            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->latest()->first();
                if ($pickup_note) {
                    return response()->json(['status' => 0, 'pickup_note_id' => $pickup_note->id]);
                }
            }
            return response()->json(['status' => 1, 'error' => 'No Pickups found!']);
        }
        return response()->json(['status' => 1, 'error' => 'Please Select filters correctly!']);
    }

    public function rider_receiving_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      .vendor_pickup_row{
                        background-color: var(--light);
                      }
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $id = $request->id;
        $pickup_note = V2PickupNote::find($id);
        $rider = Rider::find($pickup_note->rider_id);
        $route = $rider->route;
        $route_name = '';
        if ($route) {
            $route_name = $route->code . ' (' . $route->start . ' to ' . $route->end . ')';
        }
        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td> ' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td> ' . $pickup_note->rider->city->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Vendor</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Rider Picked</strong></td>
                            <td class="color primary"><strong>Arrived</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

        $serial_number = 1;

        $pickup_note_requests = $pickup_note->pickup_note_requests;
        $reverse_pickup_shipment_ids = array();
        $total_booked = 0;
        $total_rider_picked = 0;
        $total_arrived = 0;
        foreach ($pickup_note_requests as $pickup_note_request) {
            $pickup_request = $pickup_note_request->pickup_request;

            $shipper = $pickup_request->shipper;
            $pickup_address = $pickup_request->pickup_address;
            $color = '';
            if ($pickup_address->vendor != null) {
                $color = 'vendor_pickup_row';
            }
            $rider_pickuped = 0;
            $pickup_request_received_shipments = 0;
            $pickup_date = '';
            $rider_pickups = V2RiderPickup::where('pickup_note_id', $pickup_note_request->pickup_note_id)->where('pickup_request_id', $pickup_request->id);
            if ($rider_pickups->exists()) {
                $rider_pickups = $rider_pickups->latest()->first();
                $rider_pickuped = $rider_pickups->shipments;
                if ($rider_pickups->added_at != '') {
                    $pickup_date = Carbon::parse($rider_pickups->added_at)->format('Y-m-d');
                }

            }
            $pickup_request_received_shipments = count($pickup_request->pickup_request_received_shipments);
            $html .= '
                          <tr class="' . $color . '">
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['vendor'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['booked'] . '</td>
                            <td>' . $rider_pickuped . '</td>
                            <td>' . $pickup_request_received_shipments . '</td>
                            <td>' . $pickup_date . '</td>
                          </tr>
          ';
            $total_booked += $pickup_request['booked'];
            $total_rider_picked += $rider_pickuped;
            $total_arrived += $pickup_request_received_shipments;
            $serial_number++;
        }

        $html .= '
                        <tr>
                          <td colspan="6" style="font-weight: bold; text-align: center;">Total</td>
                          <td  style="font-weight: bold">' . $total_booked . '</td>
                          <td  style="font-weight: bold">' . $total_rider_picked . '</td>
                          <td  style="font-weight: bold">' . $total_arrived . '</td>
                          <td  style="font-weight: bold">-</td>

                        </tr>
                        </tbody>
                      </table>
                      <br>
                      <div>Operation Staff Receiver</div>
                      <br>
                      <br>
                      <div>
                        Name : __________________________
                      </div>
                      <br>
                      <div>
                        Signature : ______________________
                      </div>

                      <hr>
        ';

        $html .= '


                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }
    public function rider_receiving_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 69);
        }

        $from = $request->get('search_date_from');
        $to = strval(Carbon::parse($request->get('search_date_to'))->addDay());

        $count = DB::table('v2_pickup_notes')
            ->join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id');

        if ($city = $request->get('search_city')) {
            $count = $count->join('cities as c', function ($join) use ($city) {
                $join->where('r.city_id', $city);
            });
        }

        if ($search_rider = $request->get('search_rider')) {
            $count = $count->where('r.id', '=', $search_rider);
        }

        $count = $count->whereBetween('v2_pickup_notes.created_at', [$from, $to]);

        $count = $count->count();

        $rider = V2PickupNote::join('riders as r', 'r.id', '=', 'v2_pickup_notes.rider_id')
            ->join('v2_pickup_note_requests as pnr', 'pnr.pickup_note_id', '=', 'v2_pickup_notes.id')
            ->join('v2_pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->leftjoin('v2_pickup_request_shipments as prs', 'prs.pickup_request_id', '=', 'pr.id')
            ->leftjoin('shipments_journey as total_s', function ($join) {
                $join->on('total_s.shipment_id', '=', 'prs.shipment_id')
                    ->where('total_s.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = prs.shipment_id and shipments_journey.reference_1_id = pr.id and shipments_journey.shipper_status_id = 53 and verification = 1)'));
            })
            ->select('v2_pickup_notes.id as note_id', 'v2_pickup_notes.id as id', 'v2_pickup_notes.created_at as date', 'r.name as rider', DB::raw('(SELECT SUM(vprs.booked) FROM v2_pickup_note_requests AS vpnr LEFT JOIN v2_pickup_requests AS vprs ON vprs.id = vpnr.pickup_request_id WHERE vpnr.pickup_note_id = v2_pickup_notes.id ) as total_shipment_count'), DB::raw('(SELECT COUNT(vpnr2.shipment_id) FROM v2_pickup_received_shipments AS vpnr2 WHERE vpnr2.pickup_note_id = v2_pickup_notes.id AND vpnr2.pickup_note_id is not null and vpnr2.created_at between "' . $from . '" and "' . $to . '") as total_arrived_count'), DB::raw('count(total_s.id) as rider_picked'))
            ->whereBetween('v2_pickup_notes.created_at', [$from, $to])
            ->groupBy('v2_pickup_notes.id');

        if ($city = $request->get('search_city')) {
            $rider = $rider->join('cities as c', function ($join) use ($city) {
                    $join->where('r.city_id', $city);
            });
        }

        if ($search_rider = $request->get('search_rider')) {
            $rider = $rider->where('r.id', '=', $search_rider);
        }

        $datatable = Datatables::of($rider)
            ->setTotalRecords($count)
            ->editColumn('note_id', function ($rider) {
                if ($rider->note_id != null) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($rider->note_id, 6, '0', STR_PAD_LEFT) . '</span></button>'
                    ;
                }
            })
            ->addColumn('total_shipment', function ($data) {
                if ($data->total_shipment_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->total_shipment_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('total_arrived', function ($data) {
                if ($data->total_arrived_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->total_arrived_count . '</button>';
                } else {
                    return 0;
                }
            })

        ;

        return $datatable->make(true);

    }

    public function pickup_route_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 10);
        // $users = User::join('user_shipping_infos as usi','usi.user_id','=','users.id')->select('users.id','pickup_address','users.name','usi.id as address_id')->where('usi.status',1)->get();
        if (session('role_id') != 1) {
            $users = User::join('cities as c','c.id','=','users.city_id')->where('users.status',3)->whereIn('c.hub_id',session('hubs'))->select(['users.id', 'users.name'])->get();
        }
        else{
            $users = User::select(['id', 'name'])->get();
        }
        $cities = City::where('business_category_id', 1)->where('status', 1)->select(['id', 'name'])->get();
        $riders = Rider::where('status', 1)->select(['id', 'name', 'trax_id'])->get();
        return view('admin.v2_pickups.pickup_route')->with(['cities' => $cities, 'riders' => $riders, 'users' => $users]);
    }

    public function rider_tracking_index()
    {
        // $users = User::join('user_shipping_infos as usi','usi.user_id','=','users.id')->select('users.id','pickup_address','users.name','usi.id as address_id')->where('usi.status',1)->get();
        //  $users = User::select(['id','name'])->get();
        //  $cities = City::where('business_category_id', 1)->select(['id','name'])->get();
        //  $riders = Rider::where('status', 1)->select(['id','name'])->get();
        //  return view('admin.v2_pickups.pickup_route')->with(['cities' => $cities,'riders' => $riders,'users' => $users]);
    }

    public function pickup_route_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 70);
        }
        $routes = Route::join('cities', 'routes.city_id', '=', 'cities.id')
            ->leftjoin('riders', 'riders.route_id', '=', 'routes.id')
            ->select(['cities.name as city', 'routes.id as id', 'routes.code as code', 'routes.start', 'routes.end', 'routes.junction', 'routes.status as status', 'routes.created_at', 'riders.name as rider', 'riders.trax_id as rider_trax_id'])->where('routes.route_type_id', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($routes)
            ->editColumn('status', function ($routes) {
                return ($routes->status == 0) ? 'Inactive' : 'Active';
            })
            ->filterColumn('status', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('active', $keyword) !== false) {
                    $query->where('routes.status', '=', 1);
                } else if (strpos('inactive', $keyword) !== false) {
                    $query->where('routes.status', '=', 0);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([94, 95], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (session('role_id') == 1 || in_array(94, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item update_route" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Route</div></button>';
                    }

                    if (session('role_id') == 1 || in_array(95, session('permissions'))) {
                        if ($result->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeInactive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Deactivate Route</div></button>';
                        } else {
                            $dropdown .= '<button type="button" class="dropdown-item deactivate" data-target-id=' . $result->id . ' rel="routeActive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Activate Route</div></button>';
                        }
                    }
                    $dropdown .= '<button type="button" class="dropdown-item assign_location" data-target-id=' . $result->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Assign Shipper</div></button>';

                    $dropdown .= '<button type="button" class="dropdown-item view_location" data-target-id=' . $result->id . ' rel="assignlocation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Pickup Addresses </div></button>';

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function edit_route_ajax(Request $request)
    {
        $id = $request->route_id;
        $data = array();
        if ($id) {
            $route = Route::find($id);
            $city_id = $route->city_id;
            $code = $route->code;
            $start = $route->start;
            $end = $route->end;
            $junctions = $route->junction;
            $rider = Rider::where('route_id', $id);
            if ($rider->exists()) {
                $rider_id = $rider->select('id')->first();
                $rider_id = $rider_id->id;
            } else {
                $rider_id = null;
            }
            $data = (['city_id' => $city_id, 'code' => $code, 'start' => $start, 'end' => $end, 'rider_id' => $rider_id, 'junctions' => $junctions]);
            return response()->json(['details' => $data]);
        }
    }

    public static function retail_pickup_assign($pickup_request_id, $rider_id)
    {
        $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 1);
        if ($retail_pickup_note->exists()) {
            $retail_pickup_note = $retail_pickup_note->first();
            $retail_pickup_note->rider_id = $rider_id;
            $retail_pickup_note->assigned_by = Auth::id();
            $retail_pickup_note->assigned_at = Carbon::now();
            $retail_pickup_note->status = 2;
            $retail_pickup_note->save();
        }
    }
    public function retail_pickup_arrival($pickup_request_id)
    {
        $retail_pickup_note = RetailPickupNote::where('pickup_request_id', $pickup_request_id)->where('status', 2);
        if ($retail_pickup_note->exists()) {
            $retail_pickup_note = $retail_pickup_note->first();
            $retail_pickup_note->status = 3;
            $retail_pickup_note->save();
        }
    }

    public function total_shipments(Request $request)
    {

        $note_id = $request->note_id;
        $note = V2PickupNote::find($note_id);
        $pickup_note_requests = $note->pickup_note_requests;
        $bookings = array();
        if ($pickup_note_requests) {
            foreach ($pickup_note_requests as $note) {
                $pickup_request_id = $note->pickup_request_id;
                $pickup_request = V2PickupRequest::find($pickup_request_id);
                $pickup_request_shipments = $pickup_request->pickup_request_shipments;
                foreach ($pickup_request_shipments as $all_shipments) {
                    $shipment = $all_shipments->shipment_id;
                    $shipment_details = Shipment::find($shipment);
                    $bookings[] = $shipment_details->tracking_number;
                }
            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];

        } else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'booked' => false];
        }
    }

    public function arrived_shipments(Request $request)
    {
        $note_id = $request->note_id;
        $note = V2PickupNote::find($note_id);
        $arrived = array();
        if($note){
            $pickup_note_received_shipments = V2PickupReceivedShipment::where('pickup_note_id', $note->id)->pluck('shipment_id')->toArray();
            if(count($pickup_note_received_shipments) > 0){
                foreach ($pickup_note_received_shipments as $shipment) {
                    $shipment_details = Shipment::find($shipment);
                    $arrived[] = $shipment_details->tracking_number;
                }
                return ['status' => 0, 'success' => 'Arrived Shipments', 'arrived' => $arrived];
            }
            return ['status' => 0, 'success' => 'No Arrived Shipments', 'arrived' => false];
        }
        else {
            return ['status' => 0, 'success' => 'No Arrived Shipments', 'arrived' => false];
        }
    }

    public function unassigned_index()
    {
        $riders = Rider::where('status', 1)->select(['id', 'name']);
        $pickup_statuses = V2PickupRequestStatus::all();
        $rider_statuses = V2PickupRequestRiderStatus::all();
        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
        $not_pick_reasons = V2PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $legends = V2PickupRequestLegend::whereNotIn('id', [4, 5, 6])->get();
        $cut_off_time = '17:30:00';
        $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        if ($setting->exists()) {
            $setting = $setting->first();
            $cut_off_time = $setting->setting_value;
        }

        $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        if ($rider_settings->exists()) {
            $rider_settings = $rider_settings->first();
            $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        }

        return view('admin.v2_pickups.un_assigned')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);
    }

    public function unassigned_list(Request $request)
    {

        $today = Carbon::now()->startOfDay();
        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v2_pickup_request_statuses as prs', 'prs.id', '=', 'v2_pickup_requests.status_id')
        //Assigned Date

            ->select('v2_pickup_requests.id', 'v2_pickup_requests.id as pickup_request_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link', 'v2_pickup_requests.received', 'v2_pickup_requests.received as received_link', 'usi.vendor as vendor_name', 'prs.name as pickup_status', 'v2_pickup_requests.attempts', 'v2_pickup_requests.try_and_buy', 'v2_pickup_requests.vendor', 'v2_pickup_requests.status_id', 'v2_pickup_requests.after_cut_off_time', 'v2_pickup_requests.reverse_pickup')
            ->whereNull('v2_pickup_requests.current_rider_id')
            ->whereNotIn('v2_pickup_requests.status_id', [2, 4]);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        $datatables = Datatables::of($pickup_requests)
            ->setRowAttr([
                'class' => function ($pickup_request) use ($today) {
                    if ($pickup_request->reverse_pickup == 1) {
                        return 'reverse_pickup_row';
                    }
                    if ($pickup_request->vendor != null) {
                        return 'vendor_row';
                    } else if ($pickup_request->try_and_buy == 1) {
                        return 'try_and_buy';
                    } else if ($pickup_request->after_cut_off_time) {
                        return 'after_cut_off_time';
                    } else if (Carbon::parse($pickup_request->pickup_address_created_at)->startOfDay()->diffInDays($today) <= 6) {
                        return 'new_pickup';
                    }
                },
            ])
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('bookings_link', function ($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('action', function ($pickup_request) {
                if (session('role_id') == 1 || in_array(18, session('permissions'))) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>
                    </div>
                  </div>
          ';
                } else {
                    return '';
                }
            });

        return $datatables->make(true);
    }

    public function add_remarks(Request $request){
        $pickup_req = V2PickupRequest::find($request->v2_pickup_req_id);
        if($pickup_req){
            $pickup_req->remarks = $request->add_remark;
            $pickup_req->save();

            NotificationsController::send(177, $request->v2_pickup_req_id);

            return redirect()->back()->with('success', 'Remarks Added');

        }else{
            return redirect()->back()->with('error', 'Pickup Request Not Found!');

        }
    }

    public function all_remarks(Request $request){

        $v2_pickup_request = V2PickupRequest::find($request->pickup_req_id);
        if($v2_pickup_request){

            $trax_reason = '';
            $attempts = V2PickupRequestAttempt::where('pickup_request_id', $v2_pickup_request->id)->whereNotNull('reason_id');
            if ($attempts->exists()) {
                $reason_ids = $attempts->pluck('reason_id')->toArray();
                if (count($reason_ids) > 0) {
                    foreach ($reason_ids as $reason_id) {
                        $trax_reason .= V2PickupRequestNotPickReason::find($reason_id)->name . ',' . PHP_EOL;
                    }
                }
            }
            $trax_remarks = '';
            $attempts = V2PickupRequestAttempt::where('pickup_request_id', $v2_pickup_request->id)->whereNotNull('trax_remarks');
            if ($attempts->exists()) {
                $trax_remarks_rows = $attempts->pluck('trax_remarks')->toArray();
                if (count($trax_remarks_rows) > 0) {
                    foreach ($trax_remarks_rows as $remark) {
                        $trax_remarks .= $remark . ',' . PHP_EOL;
                    }
                }
            }

            $shipper_remarks = '';
            $attempts = V2PickupRequestAttempt::where('pickup_request_id', $v2_pickup_request->id)->whereNotNull('shipper_remarks');
            if ($attempts->exists()) {
                $shipper_remarks_rows = $attempts->pluck('shipper_remarks')->toArray();
                if (count($shipper_remarks_rows) > 0) {
                    foreach ($shipper_remarks_rows as $remark) {
                        $shipper_remarks .= $remark . ',' . PHP_EOL;
                    }
                }
            }

            $pickup_req = V2PickupRequest::leftJoin('v2_rider_pickups as vpr', function ($join) {
                                $join->on('vpr.pickup_request_id', '=', 'v2_pickup_requests.id')
                                    ->where('vpr.id', '=',
                                        DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v2_pickup_requests.id)'));
                            })->select('vpr.rider_remarks as rider_remarks')
                            ->where('v2_pickup_requests.id',$request->pickup_req_id);
            $rider_remarks = '';
            if($pickup_req->exists()){
                $rider_remarks = $pickup_req->get()->first()->rider_remarks;

            }
            $data = [];
            $data['trax_reason'] = $trax_reason;
            $data['trax_remarks'] = $trax_remarks;
            $data['shipper_remarks'] = $shipper_remarks;
            $data['rider_remarks'] = $rider_remarks;
            $data['remarks'] = $v2_pickup_request->remarks;
            $data['reverse_pickup'] = $v2_pickup_request->reverse_pickup;

        }else{
            $data = [];
            $data['trax_reason'] = '';
            $data['trax_remarks'] = '';
            $data['shipper_remarks'] = '';
            $data['rider_remarks'] = '';
            $data['remarks'] = '';
            $data['reverse_pickup'] = '';

        }
        return response()->json(['status' => 0, 'remarks' => $data]);
    }


    public function project_shippers_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 10);

        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;

        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name','trax_id')->get();

        return view('admin.v2_pickups.project_shipper_arrival')->with(['global_rider_id' => $global_rider_id, 'riders' => $riders]);
    }

    public function project_shippers_shipment_details(Request $request)
    {
        $include_shippers = ProjectArrivalShipper::all()->pluck('user_id')->toArray();

        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            if(in_array($shipment->user_id, $include_shippers)){
                //todo: now checking canceled shipment arrival
                $user = ShipmentsJourney::where('shipment_id',$shipment->id)->select('user_id','shipper_status_id')->orderby('id','desc')->first();
                if($user->shipper_status_id == 17)
                {
                    $canceled_shipment = CancelledShipmentArrival::where('shipper_id',$user->user_id)->first();
                    if($canceled_shipment)
                    {
                        return ['status' => 1, 'error' => 'Shipment is not allowed for arrival because shipper cancelled this shipment !'];
                    }
                }
                //todo: now checking canceled shipment arrival end

                $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                if(!$dispute_check){
                    return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                }
                $shipment_origin = $shipment->pickup_address->city->hub_id;
                if (session('role_id') != 1) {
                    if (!in_array($shipment_origin, session('hubs'))) {
                        return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                    }
                }

                if ($shipment->warehouse == 1) {
                    if ($shipment->warehouse_order_status != 5) {
                        return ['status' => 1, 'error' => 'Shipment is not dispatched yet!'];
                    }
                }

                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                    if ($shipment->booking_type_id == 3) {

                        $details = array();
                        $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                        $shipment_items_count = count($shipment_items);

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipment_items'] = $shipment_items;
                        $details['shipment_items_count'] = $shipment_items_count;

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                    } else if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                        $details = array();
                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces_count'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null, $shipment_pieces->id);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        if ($shipment->shipper_status_id == 17) {
                            AdminPickupsController::generate($shipment->id);
                        }
                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if (!$pickup_request_shipment->exists()) {
                            AdminPickupsController::generate($shipment->id);
                        }

                        if(!empty($request->weight) || (!empty($request->length) && !empty($request->breadth) && !empty($request->height))){
                            if (empty($request->weight)) {
                                $actual_weight = (($request->length * $request->breadth * $request->height) / 5000);

                                if($actual_weight < 0.1){
                                    return ['status' => 1, 'error' => 'Volumetric weight cannot be less than 0.1'];
                                }
                                $shipment->length = $request->length;
                                $shipment->breadth = $request->breadth;
                                $shipment->height = $request->height;
                            } else {
                                $actual_weight = $request->weight;
                            }
                            $shipment->actual_weight = $actual_weight;
                        }
                        else{
                            $actual_weight = null;
                        }

                        $shipment->save();

                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['city'] = $shipment->consignee_city->name;
                        $details['hub'] = $shipment->consignee_city->hub_city->name;
                        $details['shipper'] = $shipment->user->name;
                        $details['weight'] = floatval($shipment->actual_weight);



                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];

                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            }
            else{
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not of Project Arrival Shipper'];
            }
        } else {
            $shipment_item = ShipmentItem::find($request->tracking_number);
            if ($shipment_item) {
                $shipment = Shipment::find($shipment_item->shipment_id);

                if(in_array($shipment->user_id, $include_shippers)) {
                    if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
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
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not of Project Arrival Shipper'];
                }
            } else {
                $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number);
                if ($shipment_pieces->exists()) {
                    $shipment_pieces = $shipment_pieces->first();
                    $shipment = Shipment::find($shipment_pieces->shipment_id);

                    if(in_array($shipment->user_id, $include_shippers)) {
                        if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                            $details = array();
                            $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                            $details['id'] = $shipment->id;
                            $details['tracking_number'] = $shipment->tracking_number;
                            $details['pieces'] = $shipment->pieces;
                            $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                            $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;

                            ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null, $shipment_pieces->id);
                            return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                        } else {
                            return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                        }
                    }
                    else{
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not of Project Arrival Shipper'];
                    }
                }
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function project_shippers_try_and_buy_shipment_details(Request $request)
    {
        $include_shippers = ProjectArrivalShipper::all()->pluck('user_id')->toArray();
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            if(in_array($shipment->user_id, $include_shippers)) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                    if ($shipment->booking_type_id == 3) {
                        if ($shipment->shipper_status_id == 17) {
                            AdminPickupsController::generate($shipment->id);
                        }
                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if (!$pickup_request_shipment->exists()) {
                            AdminPickupsController::generate($shipment->id);
                        }
                        if(!empty($request->weight) || (!empty($request->length) && !empty($request->breadth) && !empty($request->height))){
                            $shipment->actual_weight = $request->weight;
                        }
                        $shipment->save();

                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipper'] = $shipment->user->name;
                        $details['weight'] = floatval($shipment->actual_weight);

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not of Project Arrival Shipper'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function project_shippers_piece_shipment_details(Request $request)
    {
        $include_shippers = ProjectArrivalShipper::all()->pluck('user_id')->toArray();
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            if(in_array($shipment->user_id, $include_shippers)) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                    if ($shipment->pieces > 1) {
                        if ($shipment->shipper_status_id == 17) {
                            AdminPickupsController::generate($shipment->id);
                        }
                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
                        if (!$pickup_request_shipment->exists()) {
                            AdminPickupsController::generate($shipment->id);
                        }

                        if(!empty($request->weight) || (!empty($request->length) && !empty($request->breadth) && !empty($request->height))){
                            $shipment->actual_weight = $request->weight;
                        }

                        $shipment->save();

                        $details = array();

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipper'] = $shipment->user->name;
                        $details['amount'] = $shipment->amount;
                        $details['weight'] = floatval($shipment->actual_weight);

                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                        return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Tracking Number\'s does not have pieces'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not of Project Arrival Shipper'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function project_shippers_shipment_remove(Request $request)
    {
        $shipment = Shipment::find($request->id);

        if ($shipment) {
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {
                $shipment->actual_weight = null;
                $shipment->length = null;
                $shipment->breadth = null;
                $shipment->height = null;

                $shipment->save();

                return ['status' => 0, 'success' => 'Shipment has been removed'];
            } else {
                return ['status' => 1, 'error' => 'Given Shipment ID has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given ID is present'];
        }
    }
    public function project_shippers_store(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17) {

                    $shipment->shipper_status_id = 64;
                    $shipment->consignee_status_id = 64;

                    $shipment->save();

                    $reference_2_id = null;
                    ShipmentsJourneyController::add($shipment_id, 64, 64, null, null, null, Auth::id(), null, null,1,null,null);

                }
            }
        }

        return redirect()->back()->with(['success' => 'Project Shipper Receiving Done']);
    }
}
