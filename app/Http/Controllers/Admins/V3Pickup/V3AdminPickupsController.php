<?php

namespace App\Http\Controllers\Admins\V3Pickup;

use App\Http\Controllers\AddV3PickupController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3PickupRequestAttempt;
use App\Http\Models\V3Pickup\V3PickupRequestLegend;
use App\Http\Models\V3Pickup\V3PickupRequestNotPickReason;
use App\Http\Models\V3Pickup\V3PickupRequestRiderStatus;
use App\Http\Models\V3Pickup\V3PickupRequestStatus;
use App\Http\Models\V3Pickup\V3PickupTimeRange;
use App\Http\Models\V3Pickup\V3PickupType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Yajra\Datatables\Datatables;
use DB;
class V3AdminPickupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }
    public function add_pickup_request()
    {
        if(session('role_id') !== 1){

            $shippers = User::where('status',3)->whereIn('id',session('tagged_shippers'));
        }
        else{
            $shippers = User::where('status',3)->get();
        }

        $pickup_types = V3PickupType::all();
        $time_ranges = V3PickupTimeRange::all();
        return view('admin.v3_pickups.add_pickup')->with(['shippers' => $shippers, 'pickup_types' => $pickup_types, 'time_ranges' => $time_ranges]);
    }

    public function get_pickup_address(Request $request)
    {
        $shipper_id = $request->input('shipper_id');
        $pickup_addresses = UserShippingInfo::with('city')->where('user_id', $shipper_id)->where('status', 1)->where('hidden', 0)->get();
        return response()->json(['status' => 0, 'pickup_addresses' => $pickup_addresses]);
    }

    public function pickup_request_add(Request $request){

        $admin_id = Auth::id();
        $pickup_date = $request->pickup_date_formatted;
        $shipper_id = $request->shipper_id;
        $pickup_address_id = $request->pickup_address_id;
        $preferred_time_range = $request->preferred_time_range;
        $pickup_type_id = $request->pickup_type_id;
        $estimated_weight = $request->estimated_weight;
        $shipments_count = $request->shipments_count;
        $remarks = $request->remarks;

        $pickup_address = UserShippingInfo::find($request->pickup_address_id);

        $vendor = NULL;
        $city_id = $pickup_address->city->id;
        if($pickup_address->vendor !== null){
            $vendor = $pickup_address->vendor;
        }

        AddV3PickupController::add($shipper_id, $pickup_address_id, $pickup_date, $city_id, $preferred_time_range, $pickup_type_id, $estimated_weight, $shipments_count, $remarks, 1, $admin_id, $vendor);

        if($request->has('pickup')){
            AddV3PickupController::add_regular_pickup($shipper_id, $pickup_address_id);
        }
        return redirect()->back()->with('success', 'Pickup request added successfully!');

    }
    public function history_pickup_requests(Request $request)
    {
        $riders = Rider::where('status', 1)->select(['id', 'name','trax_id']);
        $pickup_statuses = V3PickupRequestStatus::all();
        $rider_statuses = V3PickupRequestRiderStatus::all();
        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
        $not_pick_reasons = V3PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $legends = V3PickupRequestLegend::all();
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

        return view('admin.v3_pickups.history_pickup_request')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);
    }

    public function history_list(Request $request)
    {

        $today = Carbon::now()->startOfDay();
        $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
            ->leftjoin('territories as t', 't.id', '=', 'u.territory_id')
            ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
            ->join('v3_pickup_request_rider_statuses as rs', 'rs.id', '=', 'v3_pickup_requests.rider_status')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
            ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
            //Assigned Date
            ->leftJoin('v3_pickup_request_attempts as vpa', function ($join) {
                $join->on('vpa.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where('vpa.id', '=',
                        DB::raw('(select max(id) from v3_pickup_request_attempts where v3_pickup_request_attempts.pickup_request_id = v3_pickup_requests.id)'));
            })
            //End
            ->leftJoin('v3_pickup_note_requests as vpn', function ($join) {
                $join->on('vpn.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where('vpn.id', '=',
                        DB::raw('(select max(id) from v3_pickup_note_requests where v3_pickup_note_requests.pickup_request_id = v3_pickup_requests.id)'));
            })
            ->leftJoin('v3_rider_pickups as vpr', function ($join) {
                $join->on('vpr.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where('vpr.id', '=',
                        DB::raw('(select max(id) from v3_rider_pickups where v3_rider_pickups.pickup_request_id = v3_pickup_requests.id)'));
            })
//            ->leftJoin('v2_rider_pickups as vpr', 'vpr.pickup_request_id', '=', 'v3_pickup_requests.id')
            ->leftjoin('star_shippers as ss','ss.user_id','=','u.id')
            ->select('v3_pickup_requests.id','v3_pickup_requests.reminder_status as reminder', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v3_pickup_requests.booked', 'v3_pickup_requests.booked as bookings_link', 'v3_pickup_requests.received', 'v3_pickup_requests.received as received_link', 'usi.vendor as vendor_name', 'prs.name as pickup_status', 'rs.name as rider_status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'lr.name as last_rider', 'v3_pickup_requests.try_and_buy', 'v3_pickup_requests.vendor', 'v3_pickup_requests.status_id', 'v3_pickup_requests.after_cut_off_time', 'vpn.pickup_note_id', 'vpn.pickup_note_id as pickup_note_no', 'vpr.shipments as shipments_rider_picked', 'vpa.created_at as assigned_date', 'v3_pickup_requests.reverse_pickup', 'vpr.rider_remarks as rider_remarks','usi.pickup_brand_name as brand_name', 'v3_pickup_requests.remarks as rev_remarks', 't.name as territory','ss.status as star_status')
            ->whereNotIn('v3_pickup_requests.status_id', [2, 4]);

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
                $attempts = V3PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('reason_id');
                if ($attempts->exists()) {
                    $reason_ids = $attempts->pluck('reason_id')->toArray();
                    if (count($reason_ids) > 0) {
                        foreach ($reason_ids as $reason_id) {
                            $reasons .= V3PickupRequestNotPickReason::find($reason_id)->name . ',' . PHP_EOL;
                        }
                    }
                }
                return $reasons;
            })
            ->addColumn('trax_remarks', function ($pickup_requests) {
                $trax_remarks = '';
                $attempts = V3PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('trax_remarks');
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
                $attempts = V3PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id)->whereNotNull('shipper_remarks');
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
                $attempts = V3PickupRequestAttempt::where('pickup_request_id', $pickup_requests->id);
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
                $datatables->where('v3_pickup_requests.reverse_pickup',1);
            }
            elseif($legend_filter==2){
                $datatables->where('v3_pickup_requests.vendor','<>',null);
            }
            elseif($legend_filter==3){
                $datatables->where('v3_pickup_requests.try_and_buy',1)
                    ->where('v3_pickup_requests.vendor',null);
            }
            elseif($legend_filter==4){
                $datatables->where('v3_pickup_requests.status_id',3)->where('v3_pickup_requests.attempts',1)
                    ->where('v3_pickup_requests.try_and_buy',null)
                    ->where('v3_pickup_requests.vendor',null);
            }
            elseif($legend_filter==5){
                $datatables->where('v3_pickup_requests.status_id',3)->where('v3_pickup_requests.attempts',2)
                    ->where('v3_pickup_requests.try_and_buy',null)
                    ->where('v3_pickup_requests.vendor',null);
            }
            elseif($legend_filter==6){
                $datatables->where('v3_pickup_requests.status_id',3)->where('v3_pickup_requests.attempts','>',2)
                    ->where('v3_pickup_requests.try_and_buy',null)
                    ->where('v3_pickup_requests.vendor',null);
            }
            elseif($legend_filter==7){
                $datatables->where('v3_pickup_requests.after_cut_off_time','<>',null)
                    ->where('v3_pickup_requests.status_id','<>',3)
                    ->where('v3_pickup_requests.try_and_buy',null)
                    ->where('v3_pickup_requests.vendor',null);
            }
            elseif($legend_filter==1){
                $datatables->where('v3_pickup_requests.created_at','<=',Carbon::now()->startOfDay()->addDays(6))
                    ->where('v3_pickup_requests.after_cut_off_time',null)
                    ->where('v3_pickup_requests.status_id','<>',3)
                    ->where('v3_pickup_requests.try_and_buy',null)
                    ->where('v3_pickup_requests.vendor',null)
                    ->where('v3_pickup_requests.reverse_pickup',null);
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
                $datatables->whereTime('v3_pickup_requests.created_at','<=',$cut_off_time);
            }
        }

        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $stop_date = date('Y-m-d H:i:s', strtotime($to . ' +1 day'));
            $datatables->whereBetween('v3_pickup_requests.created_at', [$from, $stop_date]);
        }

        if($request->get('star_shipper_filter') == 1)
        {
            $datatables->where('ss.status',1);
        }
        return $datatables->make(true);
    }
}
