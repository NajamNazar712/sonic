<?php

namespace App\Http\Controllers\Agent;

use App\RvAgentCallHistory;
use Exception;
use Carbon\Carbon;
use App\RvShipmentAgent;
use App\Http\Traits\RvTrait;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\RvAssignAgentSubStatus;
use Illuminate\Validation\Rule;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use App\Http\Models\RvFakeStatus;
use App\Http\Models\EmployeeShift;
use App\Http\Models\RiderDelivery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\RvAgentAssignHub;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\RvAssignAgentStatus;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\ShipmentStatusReason;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Admin\SubStatusCallFinding;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\ReattemptShipmentStatusRemarks;
use App\Models\AddressMissingShipment;
use App\RvShipmentTicket;
use Illuminate\Support\Facades\DB;

class ReturnV2Controller extends Controller
{
    use RvTrait;

    public function __construct()
    {
        $this->middleware('auth:agent');
    //    $this->middleware('Permissison');
    }


    // Heading: Virtual RCP Agent Screen
    // Sidebar: N/A
    // URL: agent/dashboard
    // Description: this method is used to retrieve Logged User, ShipmentStatus(RV) And Fake Status On Index Page

    public function index()
    {
        $startOfDay = Carbon::today()->startOfDay();
        $endOfDay = Carbon::today()->endOfDay();        
        $user = Auth::user();
        // $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)->where('is_visible', 1)->get();
        $fake_status_remarks = RvFakeStatus::get();
        $sub_status_return = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', 1)->where('is_active', 1)->pluck('id')->toArray();
        $agent_total_tickets = RvShipmentAssignAgentDetails::where('agent_id', '=', Auth::id())->where('updated_by_id', '=', Auth::id())->where('rv_assign_agent_status_id', '!=', '')->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $unresponsive_count = RvAgentCallHistory::where('updated_by_id', '=', Auth::id())->whereIn('call_finding_id', [1,28,29,30,31,32])->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $reattempt_count = RvShipmentAssignAgentDetails::where('agent_id', '=', Auth::id())->where('updated_by_id', '=', Auth::id())->where('rv_assign_agent_status_id', 2)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $intercept_count = RvShipmentAssignAgentDetails::where('agent_id', '=', Auth::id())->where('updated_by_id', '=', Auth::id())->whereIn('rv_assign_agent_status_id', [3,4])->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $hold_count = RvShipmentAssignAgentDetails::where('agent_id', '=', Auth::id())->where('updated_by_id', '=', Auth::id())->where('rv_assign_agent_status_id', 5)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        // $refused_on_call = RvShipmentAssignAgentDetails::where('agent_id', '=', Auth::id())->where('updated_by_id', '=', Auth::id())->whereIn('rv_assign_agent_status_id', [1,8])->whereNotNull('rv_assign_agent_sub_status_id')->whereIn('rv_assign_agent_sub_status_id', $sub_status_return)->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $refused_on_call = RvShipmentAssignAgentDetails::where('agent_id', '=', Auth::id())->where('updated_by_id', '=', Auth::id())
        ->where(function ($query) use ($sub_status_return) {
            $query->where(function ($query) use ($sub_status_return) {
                $query->where('rv_assign_agent_status_id', 1)
                    ->whereIn('rv_assign_agent_sub_status_id', $sub_status_return);
            })->orWhere(function ($query) {
                $query->where('rv_assign_agent_status_id', 8);
            });
        })
        ->whereNotNull('rv_assign_agent_sub_status_id')
        ->whereBetween('created_at', [$startOfDay, $endOfDay])->count();

        return view('agent.return_v2.index')->with(['hold_count'=>$hold_count,'intercept_count'=>$intercept_count,'user' => $user, 'shipment_statuses' => null, 'fake_status_remarks' => $fake_status_remarks, 'agent_total_tickets' => $agent_total_tickets, 'unresponsive_count' => $unresponsive_count, 'reattempt_count' => $reattempt_count, 'refused_on_call' => $refused_on_call]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/dashboard/get_shipment_reason
    // Description: this method is used to retrieve SubStatuses Of Status And Remark Of Call Finding On Index Page

    public function get_shipment_reason(Request $request)
    {
        $reasons = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', $request->id)->where('id', '<=', 32)->where('is_active', 1)->get();
        // $unresponsive_reasons = SubStatusCallFinding::get();
        $unresponsive_reasons = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', 6)->where('id','<=',32)->where('is_active', 1)->get();
        $addressMissingType = AddressMissingShipment::where(['shipment_id'=>$request->shipment_id,'status'=>0])->latest()->first();
        return response()->json(['reasons' => $reasons, 'unresponsive_reasons' => $unresponsive_reasons, 'addressMissingType'  => $addressMissingType?->type?->type_name ?? null, 'status' => 1]);
    }



    // Heading: N/A
    // Sidebar: N/A
    // URL: 
    // Description: Mark Attendance For Employee
    public function mark_attendance($admin)
    {
        $employee_attendance = EmployeeAttendance::where('employee_id', $admin->employee_id)->where('attendance_date', date('Y-m-d'));

        if ($employee_attendance->exists()) {
            $employee_attendance->update(['clock_in' => date('H:i:s'),'clock_out'=>null]);
        } else {
            $employee_attendance = new EmployeeAttendance();
            $employee_attendance->employee_id = $admin->employee_id;
            $employee_attendance->attendance_date = date('Y-m-d');
            $employee_attendance->clock_in = date('H:i:s');
            $employee_attendance->employee_type = $admin->employee_type ? $admin->employee_type : '1';
            $employee_attendance->clock_in_latitude = session('latitude');
            $employee_attendance->clock_in_longitude = session('longitude');
            $employee_attendance->save();
        }
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: 
    // Description:
    public function get_ticket(Request $request)
    {
        // $agent_sorted_hubs = RvAgentAssignHub::where('agent_id', $request->auth_id)->orderBy('priority', 'ASC')->select('city_id')->get();
        $admin = Admin::where('id', Auth::id());

        if ($admin->exists()) {
            $admin = $admin->first();

            if($admin->agent_caller_type == null)
            {
                return response()->json(['status' => 7, 'error' => 'Contact Your Admin For Assigning Agent Type!']);
            }

            $employee = Employee::where('phone_number', $admin->phone_number)->where('staff_category_id', 3)->where('status_id', '!=', 2);

            $shipment = [];
            if ($employee->exists()) {
                $current_time = Carbon::now();
                $employee = $employee->first();
                $shift_exist = EmployeeShift::where('id', $employee->shift_id)->where('shift_type_id', 2)->first();
                if ($shift_exist) {
                    $start_time = Carbon::parse($shift_exist->start_time);
                    $end_time = Carbon::parse($shift_exist->end_time);
                    if ($current_time->between($start_time, $end_time)) {

                        $agent_id = Auth::id();

                        if(session('latitude') != null){
                            $this->mark_attendance($admin);
                        }
                        //$assigned_shipments by admin
                        $assigned_shipment = RvShipmentAssignAgent::join('shipments as s', 'rv_shipment_assign_agents.shipment_id', '=', 's.id')
                        // ->whereRaw('NOT EXISTS (
                        //     SELECT sj.id
                        //     FROM shipments_journey AS sj
                        //     WHERE sj.status_reason_id IN (12, 27, 35)
                        //     AND sj.shipment_id = s.id
                        //     AND sj.id = (
                        //         SELECT MAX(id)
                        //         FROM shipments_journey
                        //         WHERE shipment_id = s.id
                        //     )
                        // )')
                        ->whereIn('s.shipper_status_id', [12,66,52])
                        ->where('agent_id', $agent_id)
                        ->where('rv_state_id', 1)
                        ->where('rv_assign_agent_status_id', null)
                        ->where('rv_assign_agent_sub_status_id', null)
                        ->where('assigned_to_type_id', '!=', 0)
                        ->where('assigned_by', '!=', 0)
                        ->first();
                        
                        // If no shipment is assigned and shipment status is (12,66,52) and no reasons id 12, 27, 35 on shipment journey, find an unassigned one
                        if (!$assigned_shipment) {
                            $already_assigned_shipment = RvShipmentAssignAgent::join('shipments as s', 'rv_shipment_assign_agents.shipment_id', '=', 's.id')
                            // ->whereRaw('NOT EXISTS (
                            //     SELECT sj.id
                            //     FROM shipments_journey AS sj
                            //     WHERE sj.status_reason_id IN (12, 27, 35)
                            //     AND sj.shipment_id = s.id
                            //     AND sj.id = (
                            //         SELECT MAX(id)
                            //         FROM shipments_journey
                            //         WHERE shipment_id = s.id
                            //     )
                            // )')
                            ->where('agent_id', $agent_id)
                            ->where('rv_state_id', 1)
                            ->whereIn('s.shipper_status_id', [12,66,52])
                            ->whereNull('rv_assign_agent_status_id')
                            ->whereNull('rv_assign_agent_sub_status_id')
                            ->first();

                            // If no shipment is already assigned, assign a new one
                            if (!$already_assigned_shipment) {
                                $shipment = $this->findShipmentforAgent($agent_id);
                            } else {
                                if (RvShipmentTicket::where(['shipment_id' => $already_assigned_shipment->shipment_id, 'permanent_disable' => '1'])->exists()) {
                                    return response()->json(['status' => 5, 'errors' => 'No shipment found or Shipper is disabled']);
                                } 
                                //update in_progress to 1 for ticket
                                RvShipmentTicket::where('shipment_id',$already_assigned_shipment->shipment_id)->update(['in_progress'=>1]);
                                $shipment = $already_assigned_shipment->shipment_id;
                            }
                        } else {
                            if (RvShipmentTicket::where(['shipment_id' => $assigned_shipment->shipment_id, 'permanent_disable' => '1'])->exists()) {
                                return response()->json(['status' => 5, 'errors' => 'No shipment found or Shipper is disabled']);
                            }
                            //update in_progress to 1 for ticket
                            RvShipmentTicket::where('shipment_id',$assigned_shipment->shipment_id)->update(['in_progress'=>1]);
                            $shipment = $assigned_shipment->shipment_id;
                        }
                        if ($shipment) {
                            $shipment = Shipment::find($shipment->shipment_id ?? $shipment);
                            try {
                                $shipper_city = $shipment->pickup_address->city;
                                $shipper_info = $shipment->user;
                                $service_type = $shipment->booking_type;
                                $consignee_city = $shipment->consignee_city;
                                $product_infos = $shipment->items;
                                $shipping_mode = $shipment->shipping_mode;
                                $business_category = $shipment->business_category;
                                $detail_product_infos = [];
                                $rider_details = [];
                                $hub =  Shipment::join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                                        ->join('cities as h', 'dc.hub_id', '=', 'h.id')
                                        ->where('shipments.id',$shipment->id)->first();

                                foreach ($product_infos as $product_info) {
                                    $detail_product = [
                                        'product_name' => $product_info->product->product_name,
                                        'description' => $product_info->description,
                                        'quantity' => $product_info->quantity,
                                        'order_id' => $product_info->shipment->order_id
                                    ];
                                    $detail_product_infos[] = $detail_product;
                                }

                                // $rider_info = RiderDelivery::where('shipment_id', $shipment->id)->latest()->first();
                                $latest_shipments_journey = ShipmentsJourney::where('shipper_status_id', 12)
                                ->where('shipment_id', $shipment->id)
                                ->latest()
                                ->first();
                                if (isset($latest_shipments_journey)) {
                                    // $rider_details['reason'] = ShipmentStatusReason::where('id', $rider_info->rider_status_reason_id)->first();
                                    // $rider_details['reason'] = $rider_details['reason']['name'] ? $rider_details['reason']['name'] : '-';
                                    // $rider_details['reason'] =ShipmentStatusReason::where('id', ShipmentsJourney::where('shipper_status_id',12)->where('shipment_id', $shipment->id)->latest()->first()->status_reason_id)->first();
                                    $rider_details['reason'] =ShipmentStatusReason::where('id', $latest_shipments_journey->status_reason_id)->first();
                                    $rider_details['reason'] = [
                                        'id'   => isset($rider_details['reason']['id']) ? $rider_details['reason']['id'] : '-',
                                        'name' => isset($rider_details['reason']['name']) ? $rider_details['reason']['name'] : '-',
                                    ];
                                    $rider_details['attempted_time'] = (isset($latest_shipments_journey->created_at)) ? ($latest_shipments_journey->created_at)->format('Y/m/d H:i:s') : '-';
                                    $rider_details['remarks'] = $latest_shipments_journey;
                                    $rider_details['remarks'] = $rider_details['remarks']->remarks ?? '-';
                                } else {
                                    // $rider_details['reason'] = '-';
                                    // $rider_details['reason'] =ShipmentStatusReason::where('id', $latest_shipments_journey->status_reason_id)->first();
                                    $rider_details['reason'] = '-';
                                    $rider_details['attempted_time'] = '-';
                                    // $rider_details['remarks'] = '-';
                                    // $rider_details['remarks'] = $latest_shipments_journey;
                                    $rider_details['remarks'] = '-';
                                }

                                $call_history = $this->get_call_status_history($request , $shipment->id);
                                $image_location = [];

                                $rider_delivery = RiderDelivery::where('shipment_id', $shipment->id)->first();
                                if ($rider_delivery != null) {
                                    if ($rider_delivery->picture_path != null) {
                                        $exists = Storage::disk('public')->exists($rider_delivery->picture_path);
                                        if ($exists) {
                                            $image_location['image'] = asset(Storage::url($rider_delivery->picture_path));
                                        } else {
                                            $image_location['image'] = '-----';
                                        }
                                    }
                                    if ($rider_delivery->audio_path != null) {
                                        $exists = Storage::disk('public')->exists($rider_delivery->audio_path);
                                        if ($exists) {
                                            $image_location['audio'] = asset(Storage::url($rider_delivery->audio_path));
                                        } else {
                                            $image_location['audio'] = '-----';
                                        }
                                    }
                                    if ($rider_delivery->actual_location_latitude != null && $rider_delivery->actual_location_longitude != null) {
                                        $image_location['location'] = $rider_delivery->actual_location_latitude . ',' . $rider_delivery->actual_location_longitude;
                                    }
                                }

                                $shipment_status = Shipment::leftJoin('shipments_journey as sja', function ($join) use ($shipment) {
                                        $join->on('sja.shipment_id', '=', DB::raw($shipment->id))
                                            ->where('sja.id', '=', DB::raw("(select max(id) from shipments_journey where shipments_journey.shipment_id = $shipment->id and shipments_journey.shipper_status_id = 5)"));
                                })
                                ->leftJoin('delivery_notes as dn', 'dn.id', '=', 'sja.reference_1_id')
                                ->where('dn.pending_status', 1)
                                ->first();
                                
                                //If pending status is 1, show 'Return Confirm' option in dropdown on the Virtual Rv Agent Screen
                                if($shipment_status){
                                    $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)
                                    ->where(function ($query) {
                                        $query->whereNull('shipment_status_id')
                                            ->whereNotNull('call_finding_id');
                                    })
                                    ->orWhere(function ($query) {
                                        $query->whereNotNull('shipment_status_id')
                                        ->where('is_visible', 1);
                                    })
                                    ->get();
                                }
                                //else pending status is 0, show 'Refusal on Call' option in dropdown on the Virtual Rv Agent Screen
                                else{
                                    $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)->where('is_visible', 1)->where('shipment_status_id','!=', 20)->Orwhere('shipment_status_id',null)->get();
                                }
                                
                                return response()->json(['status' => 0, 'call_history' => $call_history ,'shipment_statuses' => $shipment_statuses,'rider_details' => $rider_details, 'image_location' => $image_location, 
                                'business_category' => $business_category, 'service_type' => $service_type, 'detail_product_infos' => $detail_product_infos, 'shipping_mode' => $shipping_mode, 'shipment' => $shipment, 
                                'shipper_info' => $shipper_info, 'shipper_city' => $shipper_city, 'consignee_city' => $consignee_city, 'message' => 'Already Assigned', 'hub' => $hub]);
                            } catch (Exception $ex) {
                                return response()->json(['status' => 2, 'error' => $ex->getMessage()]);
                            }
                        }

                        else{
                            return response()->json(['status' => 5, 'errors' => 'No shipment found or Shipper is disabled']);
                        }

                    } else {
                        Auth::logout();
                        return response()->json(['status' => 3]);
                    }
                } else {
                    Auth::logout();
                    return response()->json(['status' => 4]);
                }
            }
        }
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: 
    // Description:
    public function get_intercepted_shipment(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $getData = $this->getShipmentConsigneeCities($shipment_id);
        return response()->json(['status' => 1, 'shipment' => $getData['shipment'], 'consignee_cities' => $getData['consignee_cities']]);
    }


    // Heading: N/A
    // Sidebar: N/A
    // URL: 
    // Description: this function is used in agent dashboard for submitting the ticket
    //2  reattempt, 3 intercept, 5 on hold
    public function submit_ticket(Request $request)
    {
        $validations = [
            'rv_assign_agent_status_id' => 'required',
            'rv_assign_agent_sub_status_id' => 'required_unless:rv_assign_agent_status_id, 2, 3, 8',
            'is_fake_status' => 'required',
            'rv_fake_status_id' => 'required_if:is_fake_status, 1',
            'remarks' => Rule::requiredIf(function () use ($request) {
                return $request->rv_assign_agent_status_id == 6 && $request->rv_assign_agent_sub_status_id == 1 || $request->rv_assign_agent_status_id == 5;
            }), //if unresponsive and other is selected remark is required
        ];
        
        // Conditional validation
        if ($request->input('rv_assign_agent_status_id') == 2 && in_array($request->input('reasonId'), [3, 5])) {
            $validations['consignee_address_1'] = 'required|string|max:255';
        }

        $data = [
            'rv_assign_agent_status_id' => $request->input('rv_assign_agent_status_id'),
            'rv_assign_agent_sub_status_id' => $request->input('rv_assign_agent_sub_status_id'),
            'is_fake_status' => $request->input('is_fake_status'),
            'rv_fake_status_id' => $request->input('rv_fake_status_id') ?? null,
            'remarks' => $request->input('remarks') ?? null,
            'consignee_address_1' => $request->input('consignee_address_1') ?? null,
        ];
        $validate = Validator::make($data, $validations);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'errors' => $validate->errors()]);
        } 
        else 
        {
           
            $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 1)->latest()->first();
            if($shipment_assign_agent){
                $assign_agent = RvShipmentAgent::where('agent_id', $shipment_assign_agent->agent_id)->whereDate('created_at', date('Y-m-d'))->first();
                $admin_agent = Admin::where('id', Auth::id())->first();       
                $shipments_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
                // $employee = Employee::where('phone_number', $request->phone_number)->first();
                // $employee_shift = EmployeeShift::where('id', $employee->shift_id)->first();
    
                // Check Employee Shift Time
                    try{
                        DB::beginTransaction();
                    if ($request->input('rv_assign_agent_status_id') == 2 && in_array($request->input('reasonId'), [3,4])) {
                        Shipment::where('id', $request->shipment_id)->update(['consignee_address'=>$request->consigneeAddress]);
                        AddressMissingShipment::where(['shipment_id'=>$request->shipment_id,'status'=>0])->update(['status'=>1,'updated_by'=>Auth::id()]);
                    }
                    //if agent already exists on same date update row
                        if ($request->is_fake_status > 0) {
                            $this->fakeStatusMarkedDeliveries($request);
                        }
                        if ($assign_agent) 
                        {
                            $update_shipment_status = $this->update_shipment_status($request); //updating status of shipment
                            if ($update_shipment_status['status'] == 0) {
                                DB::rollBack();
                                return response()->json(['status' => 4, 'error' => $update_shipment_status['error']]);
                            } 
                            else{
                                $rv_agent_call_history_record_id = $update_shipment_status['rv_agent_call_history_record_id'] ?? null;
                                $shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 2)->where('rv_assign_agent_status_id', 8)->latest()->first();
                                if($shipment_assign_agents){
                                    $shipment_assign_agent = $shipment_assign_agents;
                                }

                                else{
                                    // this function is updating rv_shipment_assign_agents table columns like increment total_shipments, actual_productivity, already_updated, updated_type_id, rv_state_id
                                    $add_shipment_agent = $this->update_shipment_assign_agent($request, $assign_agent, $admin_agent, $shipment_assign_agent);
                                    if($add_shipment_agent != true){
                                        DB::rollBack();
                                        return response()->json(['status' => 3, 'errors' => 'Agent Not Updated']);
                                    } 
                                    else {
                                        // $shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 2)->latest()->first();
                                        $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->latest()->first();
                                        // //adding logs in rv_shipment_assign_agent_details table
                                        $rv_shipment_assign_agent_details = $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $rv_agent_call_history_record_id);
                                        if($rv_shipment_assign_agent_details != true){
                                            DB::rollBack();
                                            return response()->json(['status' => 3, 'errors' => 'Shipment Details not updated']);
                                        } 
                                        DB::commit();
                                        return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
                                    }
                                }
                            }
                        }

                        //when there is no row of agent in rv_shipment_agents table i.e update status on different day 
                        else {
                            $update_shipment_status = $this->update_shipment_status($request); //updating status of shipment
                            if ($update_shipment_status['status'] == 0) {
                                DB::rollBack();
                                return response()->json(['status' => 4, 'error' => $update_shipment_status['error']]);
                            } 
                            else {
                                $shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 2)->where('rv_assign_agent_status_id', 8)->latest()->first();
                                if($shipment_assign_agents){
                                    $shipment_assign_agent = $shipment_assign_agents;
                                }
                                else {
                                    $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 1)->latest()->first();
                                    // this function is updating rv_shipment_assign_agents table columns like increment total_shipments, actual_productivity, already_updated, updated_type_id, rv_state_id
                                    $add_shipment_agent = $this->add_shipment_agent($request, $shipment_assign_agent);
                                    if($add_shipment_agent != true){
                                        DB::rollBack();
                                        return response()->json(['status' => 3, 'errors' => 'Agent Not Updated']);
                                    } else {
                                        $rv_shipment_assign_agent_details = $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey);
                                        if($rv_shipment_assign_agent_details != true){
                                            DB::rollBack();
                                            return response()->json(['status' => 3, 'errors' => 'Shipment Details not updated']);
                                        } 
                                        else{
                                            DB::commit();
                                            return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    catch(\Throwable $th)
                    {
                        DB::rollBack();
                        return response()->json(['status' => 3, 'errors' => 'Something Went Wrong', 'info'=> $th->getMessage()]);
                    }
            }
            else{
                return response()->json(['status' => 3, 'errors' => 'This Shipment is Unassigned']);
            }
        }

    }
}
