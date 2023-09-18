<?php

namespace App\Http\Controllers\Admins\V3Pickup;

use App\Http\Controllers\AddV3PickupController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\Segment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\SubCategorySegment;
use App\Http\Models\V3Pickup\V3PickupNote;
use App\Http\Models\V3Pickup\V3PickupNoteRequest;
use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3PickupRequestAttempt;
use App\Http\Models\V3Pickup\V3PickupRequestLegend;
use App\Http\Models\V3Pickup\V3PickupRequestNotPickReason;
use App\Http\Models\V3Pickup\V3PickupRequestRiderStatus;
use App\Http\Models\V3Pickup\V3PickupRequestService;
use App\Http\Models\V3Pickup\V3PickupRequestStatus;
use App\Http\Models\V3Pickup\V3PickupService;
use App\Http\Models\V3Pickup\V3PickupShipmentType;
use App\Http\Models\V3Pickup\V3PickupTimeRange;
use App\Http\Models\V3Pickup\V3PickupType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\V3Pickup\V3RegularPickup;
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
        if(session('department_id') == 7){

            if (in_array(session('id'), session('sale_users_bypass'))) {
                $shippers = User::where('status',3)->get();
            }
            else{
                $shippers = User::where('status',3)->whereIn('id',session('tagged_shippers'));
            }

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
        $pickup_date = Carbon::parse($pickup_date)->toDateString();

        $shipper_id = $request->shipper_id;
        $pickup_address_id = $request->pickup_address_id;
        $time_range_id = $request->preferred_time_range;
        if(V3PickupRequest::where('shipper_id', $shipper_id)->where('pickup_address_id', $pickup_address_id)->where('time_range_id', $time_range_id)->where('status_id', 1)->exists()){
            return redirect()->back()->with('error', 'Pickup request already in-process!');
        }

        $pickup_type_id = $request->pickup_type_id;
        $estimated_weight = $request->estimated_weight;
        $shipments_count = $request->shipments_count;
        $pieces = $request->pieces;
        $special_request = $request->remarks;

        $shipment_type_id = $request->shipment_type_id;
        $product_id = $request->product_id;
        $service_id = $request->service_id;
        $walkin_name = '';
        $walkin_address = '';
        $walkin_contact = '';
        if($pickup_type_id == 2){
            $walkin_name = $request->walkin_name;
            $walkin_address = $request->walkin_address;
            $walkin_contact = $request->walkin_phone;

            $pickup_address_id = ShipperShipmentBookController::add_pickup_address(117, $walkin_address,$walkin_name,null, substr_replace($walkin_contact, '-', 4, 0),
            'test@gmail.com',101,0,true);
           
        }
        else{
            $walkin_address = $request->address;
            $walkin_contact = $request->phone;
        }

      
        $pickup_address = UserShippingInfo::find($pickup_address_id);

        $city_id = $pickup_address->city->id;

        $shipper_id=(is_null($shipper_id)?$pickup_address->user_id:$shipper_id);

       
        $pickup_request_id = AddV3PickupController::add($shipper_id, $pickup_type_id, $pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request, 1, $admin_id, $walkin_name, $walkin_address, $walkin_contact, $product_id, $service_id);

        $additonalservice_count=0;
        foreach ($request->additional_services as $service_id => $service_count){
            if($service_count > 0){
                $additonalservice_count+=1;
                $additional_service = new V3PickupRequestService();
                $additional_service->pickup_request_id = $pickup_request_id;
                $additional_service->pickup_request_service_id = $service_id;
                $additional_service->count = $service_count;
                $additional_service->save();
            }
        }     
        if($additonalservice_count>0){
            V3PickupRequest::where('id', $pickup_request_id)
            ->update(['services_count' => $additonalservice_count]);
        }
       

        if($request->regular_pickup == 2 && $request->pickup_type_id==1){
            $days = [];
            foreach ($request->days as $index => $day){
                $days[] = $index;
            }
            $days = implode(',', $days);
            AddV3PickupController::add_regular_pickup($shipper_id, $pickup_address_id, $pickup_request_id,$days, $admin_id, 1);
        }
        return redirect()->back()->with('success', 'Pickup request added successfully!');

    }

    public function pickup_request_update(Request $request){

       
        $admin_id = Auth::id();
        $pickup_request_id=$request->pickup_request_id;
        $pickup_date =Carbon::parse($request->pickup_date)->toDateString();

        $pickup_address_id = $request->edit_pickup_address_id;
        $time_range_id = $request->preferred_time_range;
        // if(V3PickupRequest::where('shipper_id', $shipper_id)->where('pickup_address_id', $pickup_address_id)->where('time_range_id', $time_range_id)->where('status_id', 1)->exists()){
        //     return redirect()->back()->with('error', 'Pickup request already in-process!');
        // }

        $pickup_type_id = $request->pickup_type_id;
        $estimated_weight = $request->estimated_weight;
        $shipments_count = $request->shipments_count;
        $pieces = $request->pieces;
        $special_request = $request->remarks;

        $shipment_type_id = $request->shipment_type_id;
        $product_id = $request->product_id;
        $service_id = $request->service_id;
        $walkin_name = $request->walkin_name;
        $walkin_address = $request->walkin_address;
        $walkin_contact = $request->walkin_phone;

        $pickup_address_id=ShipperShipmentBookController::update_pickup_addres($pickup_address_id,117, $walkin_address,$walkin_name,null, substr_replace($walkin_contact, '-', 4, 0),'test@gmail.com',101,0,true);
       
        // if($pickup_type_id == 2){
        //     $walkin_name = $request->walkin_name;
        //     $walkin_address = $request->walkin_address;
        //     $walkin_contact = $request->walkin_phone;

        //     $pickup_address_id = ShipperShipmentBookController::add_pickup_address(117, $walkin_address,$walkin_name,null, substr_replace($walkin_contact, '-', 4, 0),
        //     'test@gmail.com',101,0,true);
           
        // }
        // else{
        //     $walkin_address = $request->address;
        //     $walkin_contact = $request->phone;
        // }

      
        // $pickup_address = UserShippingInfo::find($pickup_address_id);

        // $city_id = $pickup_address->city->id;

        // $shipper_id=(is_null($shipper_id)?$pickup_address->user_id:$shipper_id);

       
        $pickup_request_id = AddV3PickupController::update($pickup_request_id,117, $pickup_type_id, $pickup_address_id, $pickup_date, 101, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request,1, $admin_id, $walkin_name, $walkin_address, $walkin_contact, $product_id, $service_id);

        V3PickupRequestService::where('pickup_request_id',$pickup_request_id)->delete();

        $additonalservice_count=0;
        foreach ($request->additional_services as $service_id => $service_count){
            if($service_count > 0){
                $additonalservice_count+=1;
                $additional_service = new V3PickupRequestService();
                $additional_service->pickup_request_id = $pickup_request_id;
                $additional_service->pickup_request_service_id = $service_id;
                $additional_service->count = $service_count;
                $additional_service->save();
            }
        }     
        if($additonalservice_count>0){
            V3PickupRequest::where('id', $pickup_request_id)
            ->update(['services_count' => $additonalservice_count]);
        }
       

        // if($request->regular_pickup == 2 && $request->pickup_type_id==1){
        //     $days = [];
        //     foreach ($request->days as $index => $day){
        //         $days[] = $index;
        //     }
        //     $days = implode(',', $days);
        //     AddV3PickupController::add_regular_pickup($shipper_id, $pickup_address_id, $pickup_request_id,$days, $admin_id, 1);
        // }
        return redirect()->back()->with('success', 'Pickup request updated successfully!');
    //    dd(Carbon::parse($request->pickup_date)->toDateString());
    }

    public function pending_requests_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 6);

        $statuses = array();

        if(session('department_id') == 7){

            if (in_array(session('id'), session('sale_users_bypass'))) {
                $shippers = User::where('status',3)->get();
            }
            else{
                $shippers = User::where('status',3)->whereIn('id',session('tagged_shippers'));
            }

        }
        else{
            $shippers = User::where('status',3)->get();
        }

        $riders = Rider::where('status', 1)->select(['id', 'name', 'trax_id']);
        $pickup_statuses = V3PickupRequestStatus::all();

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $not_pick_reasons = V3PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $pickup_shipment_types = V3PickupShipmentType::all();
        $time_ranges = V3PickupTimeRange::all();

        $products = Segment::all();
        $services = SubCategorySegment::all();

        foreach($pickup_statuses as $pickup_status){
            $statuses[$pickup_status->id]['name'] = $pickup_status->name;
            $statuses[$pickup_status->id]['count'] = V3PickupRequest::whereDate('pickup_date', Carbon::today())->where('status_id', $pickup_status->id)->count();
        }

        $additional_services = V3PickupService::all();
        return view('admin.v3_pickups.pending')->with(['riders' => $riders, 'pickup_statuses' => $pickup_statuses, 'not_pick_reasons' => $not_pick_reasons, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services, 'statuses' => $statuses]);
    }

    public function pending_requests_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        }
        $today = Carbon::now()->startOfDay();
        $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
//            ->leftjoin('route_locations as rl', 'rl.pickup_address_id', '=', 'v3_pickup_requests.pickup_address_id')
//            ->leftjoin('routes as rt', 'rt.id', '=', 'rl.route_id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('cities as h', 'ci.hub_id', '=', 'h.id')
            ->join('v3_pickup_types as vpt', 'vpt.id', '=', 'v3_pickup_requests.pickup_type')
            ->join('v3_pickup_time_ranges as ptr', 'ptr.id', '=', 'v3_pickup_requests.time_range_id')
            ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
            ->join('v3_pickup_shipment_types as pst', 'pst.id', '=', 'v3_pickup_requests.pickup_shipment_type_id')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
            ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'v3_pickup_requests.segment_id')
//            ->leftjoin('sub_category_segments as sub_seg', 'sub_seg.id', '=', 'v3_pickup_requests.sub_segment_id')
            ->leftJoin('v3_pickup_request_attempts as vpa', function ($join) {
                $join->on('vpa.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where(
                        'vpa.id',
                        '=',
                        DB::raw('(select max(id) from v3_pickup_request_attempts where v3_pickup_request_attempts.pickup_request_id = v3_pickup_requests.id)')
                    );
            })
            ->leftJoin('v3_rider_pickups as vpr', function ($join) {
                $join->on('vpr.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where(
                        'vpr.id',
                        '=',
                        DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v3_pickup_requests.id)')
                    );
            })
            ->select('v3_pickup_requests.id','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'lr.name as last_rider', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type','vpt.id as pickup_type_id', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by')
            ->whereDate('v3_pickup_requests.pickup_date', Carbon::today());
            // ->where('v3_pickup_requests.status_id', '=',1);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }

        if (session('department_id') == 8) {
            $id = GlobalSettings::where('type', '=', 'retail_store')->select('setting_value');
            if ($id->exists()) {
                $id = $id->first();
                $pickup_requests = $pickup_requests->where('u.id', $id->setting_value);
            }
        }

        $datatables = Datatables::of($pickup_requests)

            ->addColumn('shipment_pieces', function ($pickup_requests) {
                return $pickup_requests->shipments . '/' . $pickup_requests->pieces;
            })
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                    return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('shipments_picked', function ($pickup_request) {
                if ($pickup_request->received > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('services_count_btn', function ($pickup_requests) {
                
                if ($pickup_requests->services_count > 0) {
                    return '<button class="btn btn-md btn-primary align-middle" onclick="showadditionalservices(event,'.$pickup_requests->id.')">'. $pickup_requests->services_count .'</button>';
                } else {
                    return '';
                }
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
            ->addColumn('action', function ($reminder_request) {
                
                $reminder_button = '<a href="javascript:void(0);" class="dropdown-item reminderMarkStatus" data-action="reminder"><i class="ft-plus-circle primary"></i> Reminder </a>';
                $edit_button='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> 
                Edit </a>';

                $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                    $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if($reminder_request->pickup_type_id==2){
                       
                        $dropdown.=$edit_button;
                    }
                   
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
            });


        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $stop_date = Carbon::parse($to)->addDay(1)->toDateTimeString();
            $datatables->whereBetween('v3_pickup_requests.pickup_date', [$from, $stop_date]);
        }
        if ($pickup_status_id = $request->get('pickup_status_id')) {
            $datatables->where('v3_pickup_requests.status_id', $pickup_status_id);
        }
        return $datatables->make(true);

    }

    public function schedule_requests_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 6);

        $statuses = array();
      

        if(session('department_id') == 7){

            if (in_array(session('id'), session('sale_users_bypass'))) {
                $shippers = User::where('status',3)->get();
            }
            else{
                $shippers = User::where('status',3)->whereIn('id',session('tagged_shippers'));
            }

        }
        else{
            $shippers = User::where('status',3)->get();
        }
        
        $riders = Rider::where('status', 1)->select(['id', 'name', 'trax_id']);
        $pickup_statuses = V3PickupRequestStatus::all();

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
     
        // $not_pick_reasons = V3PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $pickup_shipment_types = V3PickupShipmentType::all();
        $time_ranges = V3PickupTimeRange::all();

        $products = Segment::all();
        $services = SubCategorySegment::all();

        foreach($pickup_statuses as $pickup_status){
            $statuses[$pickup_status->id]['name'] = $pickup_status->name;
            $statuses[$pickup_status->id]['count'] = V3PickupRequest::whereDate('pickup_date', Carbon::today())->where('status_id', $pickup_status->id)->count();
        }
        // 'pickup_statuses' => $pickup_statuses, 'not_pick_reasons' => $not_pick_reasons
        $additional_services = V3PickupService::all();
        return view('admin.v3_pickups.schedule_pickup')->with(['riders' => $riders, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services, 'statuses' => $statuses]);
    }

    public function schedule_requests_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        }
        // $today = Carbon::now()->startOfDay();
        $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('v3_regular_pickups as rp','v3_pickup_requests.id','=','rp.pickup_request_id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('cities as h', 'ci.hub_id', '=', 'h.id')
            ->join('v3_pickup_types as vpt', 'vpt.id', '=', 'v3_pickup_requests.pickup_type')
            ->join('v3_pickup_time_ranges as ptr', 'ptr.id', '=', 'v3_pickup_requests.time_range_id')
            ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
            ->join('v3_pickup_shipment_types as pst', 'pst.id', '=', 'v3_pickup_requests.pickup_shipment_type_id')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
            ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'v3_pickup_requests.segment_id')
            ->leftJoin('v3_pickup_request_attempts as vpa', function ($join) {
                $join->on('vpa.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where(
                        'vpa.id',
                        '=',
                        DB::raw('(select max(id) from v3_pickup_request_attempts where v3_pickup_request_attempts.pickup_request_id = v3_pickup_requests.id)')
                    );
            })
            ->leftJoin('v3_rider_pickups as vpr', function ($join) {
                $join->on('vpr.pickup_request_id', '=', 'v3_pickup_requests.id')
                    ->where(
                        'vpr.id',
                        '=',
                        DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v3_pickup_requests.id)')
                    );
            })
            ->select('v3_pickup_requests.id','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'lr.name as last_rider', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type','vpt.id as pickup_type_id', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by','rp.pickup','rp.approval')
            ->whereDate('v3_pickup_requests.pickup_date', Carbon::today());
            // dd($pickup_requests);
            // ->where('v3_pickup_requests.status_id', '=',1);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if (session('department_id') == 7) {
            if (!in_array(session('id'), session('sale_users_bypass'))) {
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }

        if (session('department_id') == 8) {
            $id = GlobalSettings::where('type', '=', 'retail_store')->select('setting_value');
            if ($id->exists()) {
                $id = $id->first();
                $pickup_requests = $pickup_requests->where('u.id', $id->setting_value);
            }
        }

        $datatables = Datatables::of($pickup_requests)

            ->addColumn('shipment_pieces', function ($pickup_requests) {
                return $pickup_requests->shipments . '/' . $pickup_requests->pieces;
            })
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                    return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('shipments_picked', function ($pickup_request) {
                if ($pickup_request->received > 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('services_count_btn', function ($pickup_requests) {
                
                if ($pickup_requests->services_count > 0) {
                    return '<button class="btn btn-md btn-primary align-middle" onclick="showadditionalservices(event,'.$pickup_requests->id.')">'. $pickup_requests->services_count .'</button>';
                } else {
                    return '';
                }
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
            ->addColumn('action', function ($reminder_request) {
                
                $reminder_button = '<a href="javascript:void(0);" class="dropdown-item reminderMarkStatus" data-action="reminder"><i class="ft-plus-circle primary"></i> Reminder </a>';
                // $edit_button='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> 
                // Edit </a>';
                $approve_button='<a href="javascript:void(0);" class="dropdown-item approve_schedule" data-action="approve"><i class="ft-plus-square success"></i> Approve</a>';
                $reject_button='<a href="javascript:void(0);" class="dropdown-item reject_schedule" data-action="reject"><i class="ft-plus-square danger"></i> Reject</a>';


                $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                    $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if($reminder_request->approval==0 && $reminder_request->pickup==1){
                        $dropdown.=$approve_button;
                        $dropdown.=$reject_button;
                    }
                    // else if($reminder_request->approval==2 && $reminder_request->pickup==0){

                    // }
                
                   
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
            });


        if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
            $from = $request->get('requested_from_date');
            $to = $request->get('requested_to_date');
            $stop_date = Carbon::parse($to)->addDay(1)->toDateTimeString();
            $datatables->whereBetween('v3_pickup_requests.pickup_date', [$from, $stop_date]);
        }
        if ($pickup_status_id = $request->get('pickup_status_id')) {
            $datatables->where('v3_pickup_requests.status_id', $pickup_status_id);
        }
        return $datatables->make(true);
    }

    public function schedule_approved(Request $request){
       
       $user_id=session('id');
       $pickup_request_id=$request->pickup_request_id;
       if (!$pickup_request_id) {
         return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
       }
       if(V3RegularPickup::where('pickup_request_id',$pickup_request_id)->update(['approval'=>1,'approved_by'=>$user_id])){
             return response()->json(['status' => 0, 'success' => 'Schedule Approved']);
       }
       


       
    }
    public function schedule_rejected(Request $request){
        $user_id=session('id');
        $pickup_request_id=$request->pickup_request_id;
        if (!$pickup_request_id) {
          return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
        }
        // 'approved_by'=>$user_id
        if(V3RegularPickup::where('pickup_request_id',$pickup_request_id)->update(['approval'=>2,'pickup'=>0])){
              return response()->json(['status' => 0, 'success' => 'Schedule Rejected']);
        }
    }

    public function history_pickup_index(Request $request)
    {
       
        // $riders = Rider::where('status', 1)->select(['id', 'name','trax_id']);
        // $pickup_statuses = V3PickupRequestStatus::all();
        // $rider_statuses = V3PickupRequestRiderStatus::all();
        // if (session('role_id') != 1) {
        //     $riders = $riders->whereHas('city', function ($query) {
        //         $query->whereIn('hub_id', session('hubs'));
        //     });
        // }
        // $not_pick_reasons = V3PickupRequestNotPickReason::all();
        // $riders = $riders->get();

        // $legends = V3PickupRequestLegend::all();
        // $cut_off_time = '17:30:00';
        // $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        // if ($setting->exists()) {
        //     $setting = $setting->first();
        //     $cut_off_time = $setting->setting_value;
        // }

        // $rider_settings = GlobalSettings::where('type', 'rider_assignment_cut_off_time');
        // if ($rider_settings->exists()) {
        //     $rider_settings = $rider_settings->first();
        //     $rider_cut_off_time = Carbon::createFromTime($rider_settings->setting_value, '0', '0', 'Asia/Karachi');
        // }
        //  return view('admin.v3_pickups.history_pickup_request')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time, 'pickup_statuses' => $pickup_statuses, 'rider_statuses' => $rider_statuses, 'not_pick_reasons' => $not_pick_reasons, 'rider_cut_off_time' => $rider_cut_off_time]);

        ActivityTrailController::createActivityTrailLog(Auth::id(), 6);

        $statuses = array();

        if(session('department_id') == 7){

            if (in_array(session('id'), session('sale_users_bypass'))) {
                $shippers = User::where('status',3)->get();
            }
            else{
                $shippers = User::where('status',3)->whereIn('id',session('tagged_shippers'));
            }

        }
        else{
            $shippers = User::where('status',3)->get();
        }

        $riders = Rider::where('status', 1)->select(['id', 'name', 'trax_id']);
        $pickup_statuses = V3PickupRequestStatus::all();

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $not_pick_reasons = V3PickupRequestNotPickReason::all();
        $riders = $riders->get();

        $pickup_shipment_types = V3PickupShipmentType::all();
        $time_ranges = V3PickupTimeRange::all();

        $products = Segment::all();
        $services = SubCategorySegment::all();

        foreach($pickup_statuses as $pickup_status){
            $statuses[$pickup_status->id]['name'] = $pickup_status->name;
            $statuses[$pickup_status->id]['count'] = V3PickupRequest::where('pickup_date', Carbon::today())->where('status_id', $pickup_status->id)->count();
        }


        $additional_services = V3PickupService::all();
        return view('admin.v3_pickups.pickup_history')->with(['riders' => $riders, 'pickup_statuses' => $pickup_statuses, 'not_pick_reasons' => $not_pick_reasons, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services, 'statuses' => $statuses]);
    }

    public function history_list(Request $request)
    {
       

            if ($request->get('excel') && $request->get('excel') == true) {
                ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
            }
            // $today = Carbon::now()->startOfDay();
            $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
                ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
                ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
                ->join('cities as h', 'ci.hub_id', '=', 'h.id')
                ->join('v3_pickup_types as vpt', 'vpt.id', '=', 'v3_pickup_requests.pickup_type')
                ->join('v3_pickup_time_ranges as ptr', 'ptr.id', '=', 'v3_pickup_requests.time_range_id')
                ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
                ->join('v3_pickup_shipment_types as pst', 'pst.id', '=', 'v3_pickup_requests.pickup_shipment_type_id')
                ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
                ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
                ->leftjoin('segments as seg', 'seg.id', '=', 'v3_pickup_requests.segment_id')
                ->leftJoin('v3_pickup_request_attempts as vpa', function ($join) {
                    $join->on('vpa.pickup_request_id', '=', 'v3_pickup_requests.id')
                        ->where(
                            'vpa.id',
                            '=',
                            DB::raw('(select max(id) from v3_pickup_request_attempts where v3_pickup_request_attempts.pickup_request_id = v3_pickup_requests.id)')
                        );
                })
                ->leftJoin('v3_rider_pickups as vpr', function ($join) {
                    $join->on('vpr.pickup_request_id', '=', 'v3_pickup_requests.id')
                        ->where(
                            'vpr.id',
                            '=',
                            DB::raw('(select max(id) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v3_pickup_requests.id)')
                        );
                })
                ->select('v3_pickup_requests.id','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'lr.name as last_rider', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by')
                ->where('v3_pickup_requests.status_id', '>',1);
                // ->where('prs.name','Confirmed');
                // ->where('v3_pickup_requests.shipper_id',session('id'));
                // ->whereDate('v3_pickup_requests.pickup_date', Carbon::today());

            if (session('role_id') != 1) {
                $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
            }
            if (session('department_id') == 7) {
                if (!in_array(session('id'), session('sale_users_bypass'))) {
                    $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
                }
            }

            if (session('department_id') == 8) {
                $id = GlobalSettings::where('type', '=', 'retail_store')->select('setting_value');
                if ($id->exists()) {
                    $id = $id->first();
                    $pickup_requests = $pickup_requests->where('u.id', $id->setting_value);
                }
            }

            $datatables = Datatables::of($pickup_requests)

                ->addColumn('shipment_pieces', function ($pickup_requests) {
                    return $pickup_requests->shipments . '/' . $pickup_requests->pieces;
                })
                ->editColumn('pickup_request_id', function ($pickup_requests) {
                        return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
                })
                ->addColumn('shipments_picked', function ($pickup_request) {
                    if ($pickup_request->received > 0) {
                        return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->received . '</button>';
                    } else {
                        return 0;
                    }
                })
                ->addColumn('services_count_btn', function ($pickup_requests) {
                    
                    if ($pickup_requests->services_count > 0) {
                        return '<button class="btn btn-md btn-primary align-middle" onclick="showadditionalservices(event,'.$pickup_requests->id.')">'. $pickup_requests->services_count .'</button>';
                    } else {
                        return '';
                    }
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
                ->addColumn('action', function ($reminder_request) {
                    // $reminder_button = '<a href="javascript:void(0);" class="dropdown-item reminderMarkStatus" data-action="reminder"><i class="ft-plus-circle primary"></i> Reminder </a>';

                    $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                    if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                        $dropdown = "
                            <div class='btn-group'>
                            <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                                <div class='dropdown-menu dropdown-menu-sm'>";

                        // if ((session('role_id') == 1 || (in_array(583, session('permissions'))))) {
                        //     $dropdown .= $reminder_button;
                        // }

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
                });

            if ($request->get('requested_from_date') && $request->get('requested_to_date')) {
                $from = $request->get('requested_from_date');
                $to = $request->get('requested_to_date');
                $stop_date = Carbon::parse($to)->addDay(1)->toDateTimeString();
                $datatables->whereBetween('v3_pickup_requests.pickup_date', [$from, $stop_date]);
            }
            if ($pickup_status_id = $request->get('pickup_status_id')) {
                $datatables->where('v3_pickup_requests.status_id', $pickup_status_id);
            }
            return $datatables->make(true);

    }

    public function pending_requests_assign(Request $request)
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
            $existing_pickup_request_attempt = V3PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('attempt_date', '>', $today);

            if (!$existing_pickup_request_attempt->exists()) {
                $pickup_request = V3PickupRequest::find($pickup_request_id);

                $previous_rider_id = $pickup_request->current_rider_id;

                $pickup_request->rider_status = 2;
                $pickup_request->attempts = $pickup_request->attempts + 1;
                $pickup_request->current_rider_id = $rider_id;
                $pickup_request->last_updated_by = Auth::id();
                $pickup_request->save();

                $pickup_request_attempt = new V3PickupRequestAttempt();
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
            } else {
                $pickup_request = V3PickupRequest::find($pickup_request_id);
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
//                            $pickup_note->shipments = $pickup_note->shipments - $pickup_request->booked;
                            $pickup_note->save();
                        }
                    }
                    $pickups++;
//                    $shipments = $shipments + $pickup_request->booked;
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
            $pickup_note = V3PickupNote::where('rider_id', $rider_id)->where('status', 0);

            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->first();
                if (!V3PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->whereIn('pickup_request_id', $allowed_pickup_requests)->exists()) {
                    $pickup_note->pickups += $pickups;
//                    $pickup_note->shipments += $shipments;

                    $pickup_note->save();
                }
                $pickup_note_id = $pickup_note->id;
            } else {
                $pickup_note = new V3PickupNote();

                $pickup_note->rider_id = $rider_id;
                $pickup_note->pickups = $pickups;
//                $pickup_note->shipments = $shipments;
                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            }

            foreach ($allowed_pickup_requests as $pickup_request_id) {
                if (!V3PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->exists()) {
                    V3PickupNoteRequest::where('pickup_request_id', $pickup_request_id)->where('status', 0)->delete();
                    $pickup_note_request = new V3PickupNoteRequest();

                    $pickup_note_request->pickup_note_id = $pickup_note_id;
                    $pickup_note_request->pickup_request_id = $pickup_request_id;

                    $pickup_note_request->save();
                    $pickup_request = V3PickupRequest::find($pickup_request_id);
//                    $assigned_shipments = $pickup_request->pickup_request_shipments;
                    NotificationsController::send(42, $rider_id, $pickup_request->shipper_id);
                    if ($pickup_request->vendor == 1) {
                        NotificationsController::send(43, $pickup_request->id, $pickup_request->pickup_address->id);
                    }
                }
            }

            EmployeeAttendanceController::riders_attendance_mark($rider_id);

            foreach ($notification_data as $notification_datum) {
                $previous_rider_id = $notification_datum["previous_rider"];
                $rider_id = $notification_datum["rider_id"];
                $pickup_request_id = $notification_datum["pickup_request"];
                $pickup_request = V3PickupRequest::find($pickup_request_id);
                if ($pickup_request) {
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
            foreach ($notification_data as $notification_datum) {
                $previous_rider_id = $notification_datum["previous_rider"];
                $rider_id = $notification_datum["rider_id"];
                $pickup_request_id = $notification_datum["pickup_request"];
                $pickup_request = V3PickupRequest::find($pickup_request_id);
                if ($pickup_request) {
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

    public function get_shipper_info(Request $request){
        $shipper_id = $request->shipper_id;
        $product_id = User::find($shipper_id)->segment_id;
        $pickup_addresses = UserShippingInfo::with('city')->where('user_id', $shipper_id)->where('status', 1)->where('hidden', 0)->get();

        return response()->json(['status' => 0, 'pickup_addresses' => $pickup_addresses, 'product_id' => $product_id]);

    }
    public function get_pickup_request_services(Request $request){
        $pickup_request_id=$request->pickup_request_id;
        // $additional_services=V3PickupRequest::with('pickup_request_services')->where('id',$pickuprequest_id)->get();
        $pickup_request_services=V3PickupService::join('v3_pickup_request_services','v3_pickup_services.id','=','v3_pickup_request_services.pickup_request_service_id')
        ->select('v3_pickup_services.id as id','v3_pickup_services.name as service_name','v3_pickup_request_services.count as count')->where('v3_pickup_request_services.pickup_request_id','=', $pickup_request_id)->get();
       
      
        // dd($pickuprequest_services);
        return response()->json(['status' => 0, 'pickup_request_services' => $pickup_request_services]);

    }

    public function pending_request_edit($id)
    {
        $pickup_request_id = $id;

        if (!$pickup_request_id) {
            return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
        }
        $pickup_request = V3PickupRequest::with('pickup_request_services')->find($pickup_request_id);
        // dd($pickup_request);
        return response()->json(['status'=>0,'pickup_request'=>$pickup_request]);

        // if (!$pickup_request_id) {
        //     return response()->json(['status' => 1, 'error' => 'Pickup request not found!']);
        // }
    }
}
