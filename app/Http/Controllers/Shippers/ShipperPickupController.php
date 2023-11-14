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
use App\Http\Models\Admin\Segment;
use App\Http\Models\Product;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\V3Pickup\V3PickupRequestService;
use App\Http\Models\V3Pickup\V3PickupService;
use App\Http\Models\V3Pickup\V3PickupShipmentType;
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

        $user_id=session('user_id');
        $user=User::find($user_id);
        $segment_id=$user->segment_id;

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
        $pickup_addresses = UserShippingInfo::with('city')->where('user_id',  $user->id)->where('status', 1)->where('hidden', 0)->get();
        $product = Segment::with('subCategorySegment')->find($segment_id);
        $pickup_shipment_types = V3PickupShipmentType::all();
        $additional_services = V3PickupService::all();
        // ->where('id',$segment_id)->get();
      
        return view('client.pickups.index')->with(['additional_services'=>$additional_services,'pickup_shipment_types'=>$pickup_shipment_types,'product'=>$product,'pickup_addresses'=>$pickup_addresses,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_type_claims' => $case_nature_type_claims, 'case_permission'=>$permission]);
    }

    public function pickup_list(Request $request)
    {
        $user_id=session('user_id');
        $pickup_requests = V3PickupRequest::join('users as u', 'v3_pickup_requests.shipper_id', '=', 'u.id')
                ->join('user_shipping_infos as usi', 'v3_pickup_requests.pickup_address_id', '=', 'usi.id')
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
                ->select('v3_pickup_requests.id','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by')
                ->where('v3_pickup_requests.shipper_id',  $user_id);

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

                    $edit_button='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> 
                    Edit </a>';
    
                    
                    $dropdown = "
                        <div class='btn-group'>
                        <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    // if ((session('role_id') == 1 || (in_array(583, session('permissions'))))) {
                    //     $dropdown .= $reminder_button;
                    // }

                    // if($reminder_request->status_id==1){
                       
                    //     $dropdown.=$edit_button;
                    // }
                   
                    if ($reminder_request->reverse_pickup == 1 && $reminder_request->rev_remarks == null) {

                        $dropdown .= $remarks_button;
                    }

                    $dropdown .= "
                            </div>
                        </div>
                    ";

                    return $dropdown;
                    
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
    
        // ActivityTrailController::createActivityTrailLog(Auth::id(), 6);

        // $statuses = array();
      

        // if(session('department_id') == 7){

        //     if (in_array(session('id'), session('sale_users_bypass'))) {
        //         $shippers = User::where('status',3)->get();
        //     }
        //     else{
        //         $shippers = User::where('status',3)->whereIn('id',session('tagged_shippers'));
        //     }

        // }
        // else{
        //     $shippers = User::where('status',3)->get();
        // }
        
        // $riders = Rider::where('status', 1)->select(['id', 'name', 'trax_id']);
        // $pickup_statuses = V3PickupRequestStatus::all();

        // if (session('role_id') != 1) {
        //     $riders = $riders->whereHas('city', function ($query) {
        //         $query->whereIn('hub_id', session('hubs'));
        //     });
        // }
     
        // // $not_pick_reasons = V3PickupRequestNotPickReason::all();
        // $riders = $riders->get();

        // $pickup_shipment_types = V3PickupShipmentType::all();
        // $time_ranges = V3PickupTimeRange::all();

        // $products = Segment::all();
        // $services = SubCategorySegment::all();

        // foreach($pickup_statuses as $pickup_status){
        //     $statuses[$pickup_status->id]['name'] = $pickup_status->name;
        //     $statuses[$pickup_status->id]['count'] = V3PickupRequest::whereDate('pickup_date', Carbon::today())->where('status_id', $pickup_status->id)->count();
        // }
        // 'pickup_statuses' => $pickup_statuses, 'not_pick_reasons' => $not_pick_reasons
    ///    $additional_services = V3PickupService::all();
        return view('client.pickups.schedule_pickup');
        // ->with(['riders' => $riders, 'shippers' => $shippers, 'pickup_shipment_types' => $pickup_shipment_types, 'time_ranges' => $time_ranges, 'products' => $products, 'services' => $services, 'additional_services' => $additional_services]);
    }
    

    public function schedule_requests_list(Request $request){
       
        $user_id=session('user_id');

        // if ($request->get('excel') && $request->get('excel') == true) {
        //     ActivityTrailController::createActivityTrailLog(Auth::id(), 66);
        // }
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
            ->select('v3_pickup_requests.shipper_id as shipper_id','rp.id','rp.days','v3_pickup_requests.services_count as services_count', 'v3_pickup_requests.id as pickup_request_id', 'u.id as user_id', 'v3_pickup_requests.pickup_date', 'v3_pickup_requests.created_at as pickup_created_at', 'ptr.name as time_range', 'v3_pickup_requests.booked as shipments', 'v3_pickup_requests.pieces', 'v3_pickup_requests.weight','u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'prs.name as status', 'v3_pickup_requests.attempts', 'cr.name as current_rider', 'cr.phone as current_rider_contact', 'v3_pickup_requests.status_id', 'v3_pickup_requests.received as shipments_picked', 'v3_pickup_requests.special_request', 'h.name as hub', 'vpt.name as pickup_type','vpt.id as pickup_type_id', 'pst.name as shipment_type', 'seg.name as product', 'v3_pickup_requests.generated_type', 'v3_pickup_requests.generated_by','rp.pickup','rp.approval','rt.short_code as route_code','rd.id as rider_id')
            ->where('v3_pickup_requests.shipper_id',$user_id)->get();
         
            // ->whereDate('v3_pickup_requests.pickup_date', Carbon::today());
    

        
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
            });
            // ->addColumn('action', function ($reminder_request) {
                
            //     // $reminder_button = '<a href="javascript:void(0);" class="dropdown-item reminderMarkStatus" data-action="reminder"><i class="ft-plus-circle primary"></i> Reminder </a>';
            //     // $edit_button='<a href="javascript:void(0);" class="dropdown-item edit_pickup_request" data-action="edit"><i class="ft-plus-square primary"></i> 
            //     // Edit </a>';
            //     // $approve_button='<a href="javascript:void(0);" class="dropdown-item approve_schedule" data-action="approve"><i class="ft-plus-square success"></i> Approve</a>';
            //     // $reject_button='<a href="javascript:void(0);" class="dropdown-item reject_schedule" data-action="reject"><i class="ft-plus-square danger"></i> Reject</a>';

            //     // $rescheduledays='<a href="javascript:void(0);" class="dropdown-item reschedule_days" data-action="reschedule_days"><i class="ft-plus-square info"></i> Reschedule Days</a>';

            //     // $remarks_button = '<a href="javascript:void(0);" class="dropdown-item addRemarks" data-action="reminder"><i class="ft-plus-circle primary"></i> Add Remarks </a>';

                
            //     // if (session('role_id') == 1 || count(array_intersect([583], session('permissions'))) !== 0) {
            //     //     $dropdown = "
            //     //         <div class='btn-group'>
            //     //            <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
            //     //             <div class='dropdown-menu dropdown-menu-sm'>";
                    
            //     //     $dropdown.=$rescheduledays;

            //     //     if($reminder_request->approval==0 && $reminder_request->pickup==0){
            //     //         $dropdown.=$approve_button;
            //     //         $dropdown.=$reject_button;
            //     //     }else if( $reminder_request->approval==2 && $reminder_request->pickup==0){
            //     //         $dropdown.=$approve_button;
            //     //     }else if($reminder_request->pickup==1 && $reminder_request->approval==1){
            //     //         $dropdown.=$reject_button;
            //     //     }
                
                    

            //     //     $dropdown .= "
            //     //             </div>
            //     //         </div>
            //     //     ";

            //     //     return $dropdown;
            //     // } else {
            //     //     return '';
            //     // }
            // });


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

    public function get_pickup_request_services(Request $request){
      
        $pickup_request_id=$request->pickup_request_id;
      

        $pickup_request_services=V3PickupService::join('v3_pickup_request_services','v3_pickup_services.id','=','v3_pickup_request_services.pickup_request_service_id')
        ->select('v3_pickup_services.id as id','v3_pickup_services.name as service_name','v3_pickup_request_services.count as count')->where('v3_pickup_request_services.pickup_request_id','=', $pickup_request_id)->get();
       
        return response()->json(['status' => 0, 'pickup_request_services' => $pickup_request_services]);

    }

    public function get_time_ranges(Request $request){
        $pickup_address_id=$request->pickup_address_id;
        $address=UserShippingInfo::find($pickup_address_id);
        $time_ranges = V3PickupTimeRange::where('city_id',$address->city_id)->get();
         return response()->json(['status'=>0,'time_ranges'=>$time_ranges]);
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
        $pickup_date = Carbon::parse($pickup_date)->toDateString();

        $shipper_id =  $user_id;
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
       
        $walkin_name='';
        $walkin_address = $request->address;
        $walkin_contact = $request->phone;
    

      
        $pickup_address = UserShippingInfo::find($pickup_address_id);

        $city_id = $pickup_address->city->id;
       
        $pickup_request_id = AddV3PickupController::add($shipper_id, $pickup_type_id, $pickup_address_id, $pickup_date, $city_id, $time_range_id, $shipment_type_id, $estimated_weight, $shipments_count, $pieces, $special_request, 0, $user_id, $walkin_name, $walkin_address, $walkin_contact, $product_id, $service_id);

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
            AddV3PickupController::add_regular_pickup($shipper_id, $pickup_address_id, $pickup_request_id,$days, $user_id, 0);
        }
        return redirect()->back()->with('success', 'Pickup request added successfully!');
    }
}
