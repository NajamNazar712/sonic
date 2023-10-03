<?php

namespace App\Http\Controllers\Admins\V3Pickup;

use App\Http\Controllers\AddV3PickupController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\Admin\ByPassWeightShippers;
use App\Http\Models\Admin\CancelledShipmentArrival;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\ShipmentsEstimatedWeight;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\City;
use App\Http\Models\InternationalShipment;
use App\Http\Models\ProjectArrivalShipper;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Segment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\SubCategorySegment;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\V3Pickup\V3PickupNote;
use App\Http\Models\V3Pickup\V3PickupNoteRequest;
use App\Http\Models\V3Pickup\V3PickupRequest;
use App\Http\Models\V3Pickup\V3PickupRequestAttempt;
use App\Http\Models\V3Pickup\V3PickupRequestLegend;
use App\Http\Models\V3Pickup\V3PickupRequestNotPickReason;
use App\Http\Models\V3Pickup\V3PickupRequestReason;
use App\Http\Models\V3Pickup\V3PickupRequestRiderStatus;
use App\Http\Models\V3Pickup\V3PickupRequestService;
use App\Http\Models\V3Pickup\V3PickupRequestShipment;
use App\Http\Models\V3Pickup\V3PickupRequestStatus;
use App\Http\Models\V3Pickup\V3PickupService;
use App\Http\Models\V3Pickup\V3PickupShipmentType;
use App\Http\Models\V3Pickup\V3PickupTimeRange;
use App\Http\Models\V3Pickup\V3PickupType;
use App\RouteLocations;
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

        if($pickup_address_id){
            $pickup_address = UserShippingInfo::find($pickup_address_id);

            $city_id = $pickup_address->city->id;
        }
        else{
            $city_id = $request->city_id;
        }

        $walkin_account_id = 117;

        if($pickup_type_id == 2){
            $walk_in_user = GlobalSettings::where('type', 'Walk-In')->first();
            if ($walk_in_user){
                $walkin_account_id = $walk_in_user->setting_value;
            }

            $shipper_id = $walkin_account_id;
        }


        $walkin_name = '';
        $walkin_address = '';
        $walkin_contact = '';
        if($pickup_type_id == 2){
            $walkin_name = $request->walkin_name;
            $walkin_address = $request->walkin_address;
            $walkin_contact = $request->walkin_phone;

            //walking process
            $pickup_address_id = ShipperShipmentBookController::add_pickup_address($shipper_id, $walkin_address,$walkin_name,null, substr_replace($walkin_contact, '-', 4, 0),
            'info@trax.pk',$city_id,0,true);
           
        }
        else{
            $walkin_address = $request->address;
            $walkin_contact = $request->phone;
        }

       
        $pickup_request_id = AddV3PickupController::add($shipper_id, $pickup_type_id, $pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request, 1, $admin_id, $walkin_name, $walkin_address, $walkin_contact, $product_id, $service_id);

        $additonal_service_count = 0;
        foreach ($request->additional_services as $service_id => $service_count){
            if($service_count > 0){
                $additonal_service_count += 1;
                $additional_service = new V3PickupRequestService();
                $additional_service->pickup_request_id = $pickup_request_id;
                $additional_service->pickup_request_service_id = $service_id;
                $additional_service->count = $service_count;
                $additional_service->save();
            }
        }     
        if($additonal_service_count > 0){
            V3PickupRequest::where('id', $pickup_request_id)->update(['services_count' => $additonal_service_count]);
        }
        V3PickupRequestJourneysController::add_pickup_request_journey($pickup_request_id, 1,1, $admin_id);

        if($request->regular_pickup == 2 && $request->pickup_type_id == 1){
            $days = [];
            foreach ($request->days as $index => $day){
                $days[] = $index;
            }
            $days = implode(',', $days);
            AddV3PickupController::add_regular_pickup($shipper_id, $pickup_address_id, $pickup_request_id,$days, $admin_id, 1);
        }

        // if($pickup_type_id == 1){
        //     $this->auto_pickup_assign($pickup_request_id);
        // }

        return redirect()->back()->with('success', 'Pickup request added successfully!');

    }

    public function pending_request_edit($id)
    {
        $pickup_request_id = $id;

        if (!$pickup_request_id) {
            return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
        }
        $pickup_request = V3PickupRequest::with(['pickup_request_services','pickup_address.user'])
        ->find($pickup_request_id);
        // dd($pickup_request);
            // if ($pickup_request->pickup_type == 1) {
                // $pickup_request->load('');
            // }
        return response()->json(['status'=>0,'pickup_request'=>$pickup_request]);
    }

    public function pickup_request_update(Request $request){

       
        $admin_id =session('id') ;
        $pickup_request_id=$request->pickup_request_id;
     
        $pickup_date =Carbon::parse($request->pickup_date)->toDateString();
       
        $pickup_address_id = $request->edit_pickup_address_id;
        $time_range_id = $request->preferred_time_range;

        // if(V3PickupRequest::where('shipper_id', $shipper_id)->where('pickup_address_id', $pickup_address_id)->where('time_range_id', $time_range_id)->where('status_id', 1)->exists()){
        //     return redirect()->back()->with('error', 'Pickup request already in-process!');
        // }

        $shipper_id=$request->edit_shipper_id;
        $city_id=$request->edit_city_id;
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

        
        if( $pickup_type_id ==  2){
             $pickup_address_id=ShipperShipmentBookController::update_pickup_addres($pickup_address_id,$shipper_id, $walkin_address,$walkin_name,null, substr_replace($walkin_contact, '-', 4, 0),'info@trax.pk', $city_id,0,true);
        }


        if($pickup_date >Carbon::today()->toDateString() && $pickup_type_id == 1){
           
            $new_pickup_request_id = AddV3PickupController::add($shipper_id, $pickup_type_id, $pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request,1, $admin_id, $walkin_name, $walkin_address, $walkin_contact, $product_id, $service_id);
           
            $additonalservice_count = 0;
            foreach ($request->additional_services as $service_id => $service_count){
                if($service_count > 0){
                    $additonalservice_count += 1;
                    $additional_service = new V3PickupRequestService();
                    $additional_service->pickup_request_id = $new_pickup_request_id;
                    $additional_service->pickup_request_service_id = $service_id;
                    $additional_service->count = $service_count;
                    $additional_service->save();
                }
            }    

          
            V3PickupRequest::where('id',  $new_pickup_request_id )
            ->update(['services_count' => $additonalservice_count]); 
           
            // update existing pickup request and set reschedule_request_id
            V3PickupRequest::where('id',$pickup_request_id)->update(['status_id'=>8,'reschedule_request_id' => $new_pickup_request_id]);

            // update regular pickup and update new pickup_request_id in existing regular pickup
            $regular_pickup=V3RegularPickup::where('pickup_request_id',$pickup_request_id)->first();
            if($regular_pickup){
                $regular_pickup->pickup_request_id=$new_pickup_request_id;
                $regular_pickup->save();
                // V3RegularPickup::where('pickup_request_id',$pickup_request_id)->update(['pickup_request_id' => $new_pickup_request_id]);
            }

            //rider auto assign
            // $this->auto_pickup_assign($new_pickup_request_id);

            // update pickup request service  and update new pickup_request_id in existing pickup request service 
            // V3PickupRequestService::where('pickup_request_id',$pickup_request_id)->update(['pickup_request_id' => $new_pickup_request_id]);

            // $pickup_service=V3PickupRequestService::where('pickup_request_id',$pickup_request_id)->get();
            // if($pickup_service){
            //     $pickup_service->update(['pickup_request_id' => $new_pickup_request_id]);
            // }
          
        }else{
          
                $additonalservice_count=0;
             
                foreach ($request->additional_services as $service_id => $service_count){
                  
                    if($service_count > 0){
                        $additonalservice_count+=1;
                        V3PickupRequestService::updateOrCreate(
                            [
                                'pickup_request_id' => $pickup_request_id,
                                'pickup_request_service_id' => $service_id,
                            ],
                            [
                                'count' => $service_count,
                            ]
                        );
                    }else{
                       
                        V3PickupRequestService::where('pickup_request_id',$pickup_request_id)->where('pickup_request_service_id',$service_id)->delete();
                    }
                }
             
               AddV3PickupController::update($pickup_request_id,$shipper_id, $pickup_type_id, $pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request,1, $admin_id, $walkin_name, $walkin_address, $walkin_contact, $product_id, $service_id,$additonalservice_count,$admin_id);
                // V3PickupRequest::where('id', $pickup_request_id)
                // ->update(['services_count' => $additonalservice_count]);
        }
         
      
        return redirect()->back()->with('success', 'Pickup request updated successfully!');
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

        if (session('role_id') == 1){
            $cities = City::where('pickup', 1)->where('status', 1)->get();
        }
        else{
            $cities = City::whereIn('id', session('hubs'))->get();
        }


        $pickup_reasons = V3PickupRequestReason::all();
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

        return view('admin.v3_pickups.pending')->with(['riders' => $riders, 'pickup_statuses' => $pickup_statuses, 'pickup_reasons' => $pickup_reasons, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services, 'statuses' => $statuses, 'cities' => $cities]);
    }

    public function pending_requests_list(Request $request){

   
       
    
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        }
        $today = Carbon::now()->startOfDay();
        $statuses=V3PickupRequestStatus::where('id','!=',7)->get();
        $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->leftjoin('route_locations as rl', 'rl.pickup_address_id', '=', 'v3_pickup_requests.pickup_address_id')
            ->leftjoin('routes as rt', 'rt.id', '=', 'rl.route_id')
            // ->leftjoin('rider_routes as rr', 'rr.route_id', '=', 'rt.id')
            ->leftjoin('riders as rd', 'rd.route_id', '=', 'rt.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('cities as h', 'ci.hub_id', '=', 'h.id')
            ->join('v3_pickup_types as vpt', 'vpt.id', '=', 'v3_pickup_requests.pickup_type')
            ->join('v3_pickup_time_ranges as ptr', 'ptr.id', '=', 'v3_pickup_requests.time_range_id')
            ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
            ->join('v3_pickup_shipment_types as pst', 'pst.id', '=', 'v3_pickup_requests.pickup_shipment_type_id')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
            // ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
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
        
            ->select('v3_pickup_requests.id','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status','prs.id as status_id', 'v3_pickup_requests.attempts','cr.id as current_rider_id', 'cr.name as current_rider', 'cr.phone as current_rider_phone', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type','vpt.id as pickup_type_id', 'pst.name as shipment_type', 'seg.name as product','rt.short_code as route_code','rd.id as rider_id','rd.name as rider_name','rd.phone as rider_phone','v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by')
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
        // $lasttime=;
        $datatables = Datatables::of($pickup_requests)
        ->setRowAttr([
                'class'=>function($pickup_request){
                    if(in_array($pickup_request->status_id, [2, 3, 4])){
                        $time_range = explode('-',$pickup_request->time_range);
                        $time_range = Carbon::createFromFormat('h A',trim($time_range[1]));
                        if(Carbon::now()->greaterThan( $time_range)){
                           return 'delay_time';
                        }
                    }
                }
            ])
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
                $edit_button='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> 
                Edit </a>';
                $edit_button_reschedule='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> Reschedule</a>';

                $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                $update_request_status='<a href="javascript:void(0);" class="dropdown-item update_request_status" data-action="Update Status" data-current_status_id='.$reminder_request->status_id.'><i class="ft-plus-square primary"></i> Change Status</a>';

                if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                    $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if($reminder_request->pickup_type_id==1 && $reminder_request->status_id!=8){
                        $dropdown.=$edit_button_reschedule;
                    }else if ($reminder_request->pickup_type_id==2){
                        $dropdown.=$edit_button;
                    }

                   if($reminder_request->status_id!=1  && $reminder_request->status_id!=8){
                        $dropdown.=$update_request_status;
                    } 
                    // if ((session('role_id') == 1 || (in_array(583, session('permissions'))))) {
                    //     $dropdown .= $reminder_button;
                    // }

                //    if ($reminder_request->reverse_pickup == 1 && $reminder_request->rev_remarks == null) {
                //         $dropdown .= $remarks_button;
                //    }
                    if ($reminder_request->attempts == 1) {
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
            if($pickup_status_id == 0){
                $datatables->where('v3_pickup_requests.status_id', [1,2,3,4,5,6,7]);
            }
            else{
                $datatables->where('v3_pickup_requests.status_id', $pickup_status_id);
            }
        }
        return $datatables->make(true);

    }

    public function add_pickup_remark(Request $request){
       
        $pickup_request_id=$request->remark_pickup_request_id;
        $add_remark=$request->add_remark;
        $pickuprequestattempt=V3PickupRequestAttempt::where('pickup_request_id',$pickup_request_id)->latest()->first();
        $pickuprequestattempt->trax_remarks=$add_remark;
        if($pickuprequestattempt->save()){
                return redirect()->back()->with('success', 'Remarks Add Successfully');
        }
    }
    public function get_pickup_remarks($id){
        $pickup_request_id = $id;
        if (!$pickup_request_id) {
            return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
        }
        $pickup_request_attempt = V3PickupRequestAttempt::select('trax_remarks')->where('pickup_request_id',$pickup_request_id)->latest()->first();
        return response()->json(['status'=>0,'pickup_request_attempt'=>$pickup_request_attempt]);
    }

    public function pending_request_status_update(Request $request){
       
        $user_id=session('id');
        $pickup_request_id = $request->pickup_request_id;
        $status_id = $request->status_id;
        if(V3PickupRequestStatus::where('id',$status_id)->exists()){
            $pickup_request = V3PickupRequest::find($pickup_request_id);
            if($pickup_request->current_rider_id){
                $pickup_request->status_id = $status_id;
                $pickup_request->last_updated_by = $user_id;
                if($pickup_request->save()){
                    return redirect()->back()->with('success','Status update successfully');
                }
                    return redirect()->back()->with('error','Something went wrong, please refresh and try again!');
            }
            return redirect()->back()->with('error','The rider has not been assigned!');
           
        }else{
                return redirect()->back()->with('error','Pickup Request Status not found!');
        }
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

        // foreach($pickup_statuses as $pickup_status){
        //     $statuses[$pickup_status->id]['name'] = $pickup_status->name;
        //     $statuses[$pickup_status->id]['count'] = V3PickupRequest::whereDate('pickup_date', Carbon::today())->where('status_id', $pickup_status->id)->count();
        // }
        // 'pickup_statuses' => $pickup_statuses, 'not_pick_reasons' => $not_pick_reasons
        $additional_services = V3PickupService::all();
        return view('admin.v3_pickups.schedule_pickup')->with(['riders' => $riders, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services]);
    }

    public function schedule_requests_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        }
        // $today = Carbon::now()->startOfDay();
        $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('v3_regular_pickups as rp','v3_pickup_requests.id','=','rp.pickup_request_id')
            ->leftjoin('route_locations as rl', 'rl.pickup_address_id', '=', 'v3_pickup_requests.pickup_address_id')
            ->leftjoin('routes as rt', 'rt.id', '=', 'rl.route_id')
            // ->leftjoin('rider_routes as rr', 'rr.route_id', '=', 'rt.id')
            ->leftjoin('riders as rd', 'rd.route_id', '=', 'rt.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->join('cities as h', 'ci.hub_id', '=', 'h.id')
            ->join('v3_pickup_types as vpt', 'vpt.id', '=', 'v3_pickup_requests.pickup_type')
            ->join('v3_pickup_time_ranges as ptr', 'ptr.id', '=', 'v3_pickup_requests.time_range_id')
            ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
            ->join('v3_pickup_shipment_types as pst', 'pst.id', '=', 'v3_pickup_requests.pickup_shipment_type_id')
            ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
            // ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
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
            ->select('rp.id','rp.days','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type','vpt.id as pickup_type_id', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by','rp.pickup','rp.approval','rt.short_code as route_code','rd.id as rider_id');
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
                    return '<button class="btn btn-md btn-primary align-middle" onclick="showadditionalservices(event,'.$pickup_requests->pickup_request_id.')">'. $pickup_requests->services_count .'</button>';
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
                // $edit_button='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> 
                // Edit </a>';
                $approve_button='<a href="javascript:void(0);" class="dropdown-item approve_schedule" data-action="approve"><i class="ft-plus-square success"></i> Approve</a>';
                $reject_button='<a href="javascript:void(0);" class="dropdown-item reject_schedule" data-action="reject"><i class="ft-plus-square danger"></i> Reject</a>';

                $rescheduledays='<a href="javascript:void(0);" class="dropdown-item reschedule_days" data-action="reschedule_days"><i class="ft-plus-square info"></i> Reschedule Days</a>';

                $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                
                if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
                    $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";
                    
                    $dropdown.=$rescheduledays;

                    if($reminder_request->approval==0 && $reminder_request->pickup==0){
                        $dropdown.=$approve_button;
                        $dropdown.=$reject_button;
                    }else if( $reminder_request->approval==2 && $reminder_request->pickup==0){
                        $dropdown.=$approve_button;
                    }else if($reminder_request->pickup==1 && $reminder_request->approval==1){
                        $dropdown.=$reject_button;
                    }
                
                   
                    // if ((session('role_id') == 1 || (in_array(583, session('permissions'))))) {
                    //     $dropdown .= $reminder_button;
                    // }

                    // if ($reminder_request->reverse_pickup == 1 && $reminder_request->rev_remarks == null) {

                    //     $dropdown .= $remarks_button;
                    // }
                    // if ($reminder_request->attempts == 1) {
                    //     $dropdown .= $remarks_button;
                    // }
                    

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
       $regular_pickup_id=$request->regular_pickup_id;
       if (!$regular_pickup_id) {
         return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
       }
       $regular_pickup=V3RegularPickup::find($regular_pickup_id);
       $regular_pickup->pickup=1;
       $regular_pickup->approval=1;
       $regular_pickup->approved_by=$user_id;
       if($regular_pickup->save()){
             return response()->json(['status' => 0, 'success' => 'Schedule Approved']);
       }
       
    }
    public function schedule_rejected(Request $request){
        // $user_id=session('id');
        $regular_pickup_id=$request->regular_pickup_id;
        if (!$regular_pickup_id) {
          return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
        }
        // 'approved_by'=>$user_id
        $regular_pickup=V3RegularPickup::find($regular_pickup_id);
        $regular_pickup->approval=2;
        $regular_pickup->pickup=0;
        if($regular_pickup->save()){
              return response()->json(['status' => 0, 'success' => 'Schedule Rejected']);
        }
    }

    public function get_regular_pickup_days($id){
        $regular_pickup_id=$id;
        if (!$regular_pickup_id) {
            return response()->json(['status' => 1, 'error' => 'Something went wrong, please refresh and try again!']);
        }
        $regular_pickup=V3RegularPickup::find($regular_pickup_id);
        return response()->json(['status' => 0, 'regular_pickup' => $regular_pickup]);
    }
    public function pickup_days_update(Request $request){
      
        $regular_pickup_id = $request->regular_pickup_id;
        $days = implode(',',$request->days);
        if (!$regular_pickup_id) {
            return redirect()->back()->with('error','Something went wrong, please refresh and try again!');
        }
        $regular_pickup = V3RegularPickup::find($regular_pickup_id);
        $regular_pickup->days=$days;
        $regular_pickup->save();
        return redirect()->back()->with('success','Pickup Days Update Successfully');

    }

    public function history_pickup_index(Request $request)
    {
       
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

        $pickup_reasons = V3PickupRequestReason::all();
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
        return view('admin.v3_pickups.pickup_history')->with(['riders' => $riders, 'pickup_statuses' => $pickup_statuses, 'pickup_reasons' => $pickup_reasons, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services, 'statuses' => $statuses]);
    }

    public function history_list(Request $request)
    {
       

            if ($request->get('excel') && $request->get('excel') == true) {
                ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
            }
            // $today = Carbon::now()->startOfDay();
            $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
                ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
                ->leftjoin('route_locations as rl', 'rl.pickup_address_id', '=', 'v3_pickup_requests.pickup_address_id')
                ->leftjoin('routes as rt', 'rt.id', '=', 'rl.route_id')
                // ->leftjoin('rider_routes as rr', 'rr.route_id', '=', 'rt.id')
                ->leftjoin('riders as rd', 'rd.route_id', '=', 'rt.id')
                ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
                ->join('cities as h', 'ci.hub_id', '=', 'h.id')
                ->join('v3_pickup_types as vpt', 'vpt.id', '=', 'v3_pickup_requests.pickup_type')
                ->join('v3_pickup_time_ranges as ptr', 'ptr.id', '=', 'v3_pickup_requests.time_range_id')
                ->join('v3_pickup_request_statuses as prs', 'prs.id', '=', 'v3_pickup_requests.status_id')
                ->join('v3_pickup_shipment_types as pst', 'pst.id', '=', 'v3_pickup_requests.pickup_shipment_type_id')
                ->leftjoin('riders as cr', 'cr.id', '=', 'v3_pickup_requests.current_rider_id')
                // ->leftjoin('riders as lr', 'lr.id', '=', 'v3_pickup_requests.last_rider_id')
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
                ->select('v3_pickup_requests.id','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by','rt.short_code as route_code','rd.id as rider_id')
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

                        // if ($reminder_request->reverse_pickup == 1 && $reminder_request->rev_remarks == null) {

                        //     $dropdown .= $remarks_button;
                        // }
                        if ($reminder_request->attempts == 1) {
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
       
      
        return response()->json(['status' => 0, 'pickup_request_services' => $pickup_request_services]);

    }

 
    public function rider_assign(Request $request)
    {
       
        $admin_id=session('id');
        $pickup_request_ids = $request->input('pickup_request_ids');
        $pickup_request_ids = explode(',', $pickup_request_ids);
        $rider_id = $request->input('rider');
        $previous_rider_id = null;
        $riders = array();
        $notification_data = array();
        if (count($pickup_request_ids) == 0) {
            return redirect()->back()->with('error', 'No Pickups selected!');
        }

        array_unique($pickup_request_ids);

        if (empty($pickup_request_ids)) {
            return redirect()->back()->with('error', 'No Pickup Request Selected!');
        }

        if (empty($rider_id)) {
            return redirect()->back()->with('error', 'No Rider Selected!');
        }

        $pickups = 0;
        $shipments = 0;
        $today = Carbon::today()->toDateTimeString();
        $allowed_pickup_requests = array();
        foreach ($pickup_request_ids as $pickup_request_id) {
            //            $existing_pickup_request_attempt = V2PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->where('rider_id', $rider_id)->whereBetween('attempt_date', [$start_date, $end_date]);
            $existing_pickup_request_attempt = V3PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->whereDate('attempt_date', $today);

            if (!$existing_pickup_request_attempt->exists()) {
                $pickup_request = V3PickupRequest::find($pickup_request_id);

                $previous_rider_id = $pickup_request->current_rider_id;

                $pickup_request->status_id = 2;
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
                    V3PickupRequestJourneysController::add_pickup_request_journey($pickup_request->id,$pickup_request->status_id,1,$admin_id);

                    $previous_rider_id = $pickup_request->current_rider_id;
                    $riders['old_rider_id'] = $pickup_request->current_rider_id;
                    $riders['new_rider_id'] = $rider_id;

                    $pickup_request->status_id = 2;
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
            $pickup_note = V3PickupNote::where('rider_id', $rider_id)->where('status', 0);

            if ($pickup_note->exists()) {
                $pickup_note = $pickup_note->first();
                if (!V3PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->whereIn('pickup_request_id', $allowed_pickup_requests)->exists()) {
                    $pickup_note->pickups += $pickups;
                    $pickup_note->shipments += $shipments;

                    $pickup_note->save();
                }
                $pickup_note_id = $pickup_note->id;
            } else {
                $pickup_note = new V3PickupNote();

                $pickup_note->rider_id = $rider_id;
                $pickup_note->pickups = $pickups;
                $pickup_note->shipments = $shipments;
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
        return redirect()->back()->with('error', 'Pickup Request(s) already assigned!');
    }

    static public function auto_pickup_assign($pickup_request_id){

        $pickup_request = V3PickupRequest::find($pickup_request_id);
        $pickup_address_id = $pickup_request->pickup_address_id;
        if(RouteLocations::where('pickup_address_id',$pickup_address_id)->exists()){
            $route = RouteLocations::where('pickup_address_id',$pickup_address_id)->first();
            $route_status = Route::find($route->route_id);
            if($route_status->status != 1 ){
                return false;
            }
        }
        else{
            return false;
        }
       
        $rider = Rider::where('route_id',$route->route_id)->select('id');
     
        if(!$rider->exists()){
            return false;
        }
        else{
            $rider = $rider->first();
          
        }
        $rider_id = $rider->id;
        $pickup_request = V3PickupRequest::find($pickup_request_id);
        $pickup_address_id = $pickup_request->pickup_address_id;


        $admin_settings = GlobalSettings::where('type','auto_assign_admin');
        if($admin_settings->exists()){
            $admin_settings = $admin_settings->first();
            if($admin_settings->setting_value != 0 && $admin_settings->setting_value != null){
                $admin_id = $admin_settings->setting_value;
            }
            else{
                $admin_id = NULL;
            }
        }

        $pickups = 0;
        $shipments = 0;

        $today = Carbon::today();

        $global_admin_id = $admin_id;

        $existing_pickup_request_attempt = V3PickupRequestAttempt::where('pickup_request_id', $pickup_request_id)->whereDate('attempt_date', $today);
        if(!$existing_pickup_request_attempt->exists()){
            $pickup_request = V3PickupRequest::find($pickup_request_id);

            $pickup_request->status_id = 2;
            $pickup_request->attempts = $pickup_request->attempts + 1;
            $pickup_request->current_rider_id = $rider_id;
            $pickup_request->last_updated_by = $global_admin_id;
            $pickup_request->save();

            $pickup_request_attempt = new V3PickupRequestAttempt();
            $pickup_request_attempt->pickup_request_id = $pickup_request_id;
            $pickup_request_attempt->rider_id = $rider_id;
            $pickup_request_attempt->attempt_date = Carbon::now();
            $pickup_request_attempt->assigned_by = $global_admin_id;
            $pickup_request_attempt->save();

            $pickups++;
            $shipments = $shipments + $pickup_request->booked;
            V3AdminPickupsController::retail_pickup_assign($pickup_request_id, $rider_id);
        }else{
            $pickup_request = V3PickupRequest::find($pickup_request_id);
            if($pickup_request->current_rider_id != $rider_id){
                $pickup_request->status_id = 2;
                $pickup_request->current_rider_id = $rider_id;
                $pickup_request->last_updated_by = $global_admin_id;
                $pickup_request->save();
                $existing_pickup_request_attempt = $existing_pickup_request_attempt->latest('id')->first();

                $existing_pickup_rider = $existing_pickup_request_attempt->rider_id;

                $existing_pickup_request_attempt->rider_id = $rider_id;
                $existing_pickup_request_attempt->assigned_by = $global_admin_id;
                $existing_pickup_request_attempt->save();

                $pickup_note_request = $pickup_request->pickup_note_request;
                if($pickup_note_request){
                    $pickup_note = $pickup_note_request->pickup_note;
                    $pickup_note_rider = $pickup_note->rider_id;
                    if($existing_pickup_rider == $pickup_note_rider){
                        $pickup_request->pickup_note_request->delete();
                        $pickup_note->pickups = $pickup_note->pickups - 1;
                        $pickup_note->save();
                    }
                }
                $pickups++;
                $shipments = $shipments + $pickup_request->booked;
                V3AdminPickupsController::retail_pickup_assign($pickup_request_id, $rider_id);
            }

        }

        $pickup_note = V3PickupNote::where('rider_id', $rider_id)->where('status', 0);

        if ($pickup_note->exists()) {
            $pickup_note = $pickup_note->first();
            if(!V3PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request_id)->exists()){
                $pickup_note->pickups += $pickups;
                $pickup_note->shipments += $shipments;

                $pickup_note->save();

                $pickup_note_id = $pickup_note->id;
            }

        }
        else {
            $pickup_note = new V3PickupNote();

            $pickup_note->rider_id = $rider_id;
            $pickup_note->pickups = $pickups;
            $pickup_note->shipments = $shipments;
            $pickup_note->save();

            $pickup_note_id = $pickup_note->id;
        }


        if(!V3PickupNoteRequest::where('pickup_note_id', $pickup_note_id)->where('pickup_request_id', $pickup_request_id)->exists()){
            $pickup_note_request = new V3PickupNoteRequest();

            $pickup_note_request->pickup_note_id = $pickup_note_id;
            $pickup_note_request->pickup_request_id = $pickup_request_id;

            $pickup_note_request->save();
            NotificationsController::app_notification(4, $rider_id, 2, $pickup_request->shipper_id);
        }
    }

    public function arrival_individual_index(Request $request){
        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name', 'trax_id')->get();
        return view('admin.v2_pickups.arrival_individual_weight')->with(['riders' => $riders, 'global_rider_id' => $global_rider_id]);
    }

    public function arrival_individual_shipment_details(Request $request)
    {
        $rider_id = $request->rider_id;
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('user_id', 'shipper_status_id')->orderby('id', 'desc')->first();
            if ($shipment_journey->shipper_status_id == 17) {
                $canceled_shipment = CancelledShipmentArrival::where('shipper_id', $shipment_journey->user_id)->first();
                if ($canceled_shipment) {
                    return ['status' => 1, 'error' => 'Shipment is not allowed for arrival because shipper cancelled this shipment !'];
                }
            }


            $pickup_request_id = null;
            $rider = null;
            $rider_assigned_flag = false;

            $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
            if (!$dispute_check) {
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
                $global_rider_id = 346;
            }

            if (in_array($shipment->shipper_status_id, [1, 17, 53, 61, 62, 64])) {
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
                    $pickup_request_shipment = V3PickupRequestShipment::where('shipment_id', $shipment->id);
                    if ($pickup_request_shipment->exists()) {
                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
                        $pickup_request = V3PickupRequest::find($pickup_request_id);
                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
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
                        if ($project_arrival_include_shippers->exists()) {
                            if ($shipment->actual_weight == null) {
                                $weight_flag = true;
                            } else {
                                $weight_flag = false;
                            }
                        }
                        if ($weight_flag == true) {
                            if (empty($request->weight)) {

                                $actual_weight = (($request->length * $request->breadth * $request->height) / 5000);

                                if ($actual_weight < 0.1) {
                                    return ['status' => 1, 'error' => 'Volumetric weight cannot be less than 0.1'];
                                }
                                $shipment->length = $request->length;
                                $shipment->breadth = $request->breadth;
                                $shipment->height = $request->height;
                            } else {
                                $actual_weight = $request->weight;
                            }
                        } else {
                            $actual_weight = $shipment->actual_weight;
                        }

                        $not_include_shippers1 = ByPassWeightShippers::all()->pluck('shipper_id')->toArray();

                        $not_include_shippers = [6693, 12412];

                        $not_include_shippers = array_merge($not_include_shippers, $not_include_shippers1);

                        if (!in_array($shipment->user_id, $not_include_shippers)) {
                            $estimate_actual_difference = $shipment->estimated_weight - $actual_weight;

                            if ($shipment->estimated_weight != 1 && $estimate_actual_difference > 0 && $estimate_actual_difference < 5) {

                                $shipment_estimated_weight = ShipmentsEstimatedWeight::where('shipment_id', $shipment->id);
                                if ($shipment_estimated_weight->exists()) {
                                    $shipment_estimated_weight = $shipment_estimated_weight->first();
                                } else {
                                    $shipment_estimated_weight = new ShipmentsEstimatedWeight();
                                }
                                $shipment_estimated_weight->shipment_id = $shipment->id;
                                $shipment_estimated_weight->estimated_weight = $shipment->estimated_weight;
                                $shipment_estimated_weight->actual_weight = $actual_weight;
                                if (empty($request->weight)) {
                                    $shipment_estimated_weight->length = $request->length;
                                    $shipment_estimated_weight->breadth = $request->breadth;
                                    $shipment_estimated_weight->height = $request->height;
                                } else {
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
                    $shipment->save();

                    $rider_picked = false;
                    if(!$rider_assigned_flag){
                        $v2_pickup_note_request = V3PickupNoteRequest::where('pickup_request_id',$pickup_request_id)->latest()->first();
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
}
