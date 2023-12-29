<?php

namespace App\Http\Controllers\Agent;

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
use Illuminate\Support\Facades\DB;

class ReturnV2Controller extends Controller
{
    use RvTrait;

    public function __construct()
    {
        $this->middleware('auth:agent');
        $this->middleware('Permission');
    }


    // Heading: Virtual RCP Agent Screen
    // Sidebar: N/A
    // URL: agent/dashboard
    // Description: this method is used to retrieve Logged User, ShipmentStatus(RV) And Fake Status On Index Page

    public function index()
    {
        $user = Auth::user();
        // $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)->where('is_visible', 1)->get();
        $fake_status_remarks = RvFakeStatus::get();
        $sub_status_return = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', 1)->where('is_active', 1)->pluck('id')->toArray();
        $agent = RvShipmentAssignAgent::where('agent_id', '=', Auth::id())->where('rv_assign_agent_status_id', '!=', '')->get();
        $unresponsive_count = RvShipmentAssignAgent::where('agent_id', '=', Auth::id())->where('rv_assign_agent_status_id', 6)->count();
        $reattempt_count = RvShipmentAssignAgent::where('agent_id', '=', Auth::id())->where('rv_assign_agent_status_id', 2)->count();
        $intercept_count = RvShipmentAssignAgent::where('agent_id', '=', Auth::id())->where('rv_assign_agent_status_id', 3)->count();
        $hold_count = RvShipmentAssignAgent::where('agent_id', '=', Auth::id())->where('rv_assign_agent_status_id', 5)->count();
        $refused_on_call = RvShipmentAssignAgent::where('agent_id', '=', Auth::id())->where('rv_assign_agent_status_id', 1)->whereIn('rv_assign_agent_sub_status_id', $sub_status_return)->count();

        return view('agent.return_v2.index')->with(['hold_count'=>$hold_count,'intercept_count'=>$intercept_count,'user' => $user, 'shipment_statuses' => null, 'fake_status_remarks' => $fake_status_remarks, 'agent_total_tickets' => $agent, 'unresponsive_count' => $unresponsive_count, 'reattempt_count' => $reattempt_count, 'refused_on_call' => $refused_on_call]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/dashboard/get_shipment_reason
    // Description: this method is used to retrieve SubStatuses Of Status And Remark Of Call Finding On Index Page

    public function get_shipment_reason(Request $request)
    {
        $reasons = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', $request->id)->where('is_active', 1)->get();
        // $unresponsive_reasons = SubStatusCallFinding::get();
        $unresponsive_reasons = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', 6)->where('is_active', 1)->get();
        return response()->json(['reasons' => $reasons, 'unresponsive_reasons' => $unresponsive_reasons, 'status' => 1]);
    }



    // Heading: N/A
    // Sidebar: N/A
    // URL: 
    // Description: Mark Attendance For Employee
    public function mark_attendance($admin)
    {
        $employee_attendance = EmployeeAttendance::where('employee_id', $admin->employee_id)->where('attendance_date', date('Y-m-d'));

        if ($employee_attendance->exists()) {
            $employee_attendance->update(['clock_in' => date('H:i:s')]);
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

        $sorted_agents = RvAgentAssignHub::where('agent_id', $request->auth_id)->orderBy('priority', 'ASC')->get();

        $admin = Admin::where('id', Auth::id());

        if ($admin->exists()) {
            $admin = $admin->first();

           
            $employee = Employee::where('phone_number', $admin->phone_number)->where('staff_category_id', 3)->where('status_id', '!=', 2);

            $shipment = [];
            if ($employee->exists()) {
                $current_time = Carbon::now();
                $employee = $employee->first();
                $shift_exist = EmployeeShift::where('id', $employee->shift_id)->where('shift_type_id', 2)->first();
                if ($shift_exist) {
                    // $shift_exist =  EmployeeShift::where('id', $employee->shift_id)->first();
                    $start_time = Carbon::parse($shift_exist->start_time);
                    $end_time = Carbon::parse($shift_exist->end_time);
                    if ($current_time->between($start_time, $end_time)) {
                        // Assuming $sorted_agents is an array containing agents with their city_id

                        $agent_id = Auth::id();

                        $shipment = $this->included_shippers($sorted_agents, $agent_id);

                        
                        if ($shipment) {
                            try {
                                if(session('latitude') != null){
                                    $this->mark_attendance($admin);
                                }else {
                                    return response()->json(['status' => 6]);
                                }
                                dd($shipment);
                                // $shipment = DeliveryNoteShipment::join('delivery_notes as dn','delivery_note_shipments.delivery_note_id','dn.id')
                                // ->where('delivery_note_shipments.shipment_id', $shipment->id)
                                // ->where('dn.pending_status', 1)
                                // ->first();
                                
                                // if($shipment){
                                //     $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)->where('is_visible', 1)->get();
                                // }
                                // else{
                                //     //if pending status is not 1 dont show return confirm status in the dropdown
                                //     $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)->where('is_visible', 1)->whereNotIn('shipment_status_id', [20])->get();
                                // }

                                $shipper_city = $shipment->pickup_address->city;
                                $shipper_info = $shipment->user;
                                $service_type = $shipment->booking_type;
                                $consignee_city = $shipment->consignee_city;
                                $product_infos = $shipment->items;
                                $shipping_mode = $shipment->shipping_mode;
                                $business_category = $shipment->business_category;
                                $detail_product_infos = [];
                                $rider_details = [];

                                foreach ($product_infos as $product_info) {

                                    $detail_product = [
                                        'product_name' => $product_info->product->product_name,
                                        'description' => $product_info->description,
                                        'quantity' => $product_info->quantity,
                                        'order_id' => $product_info->shipment->order_id
                                    ];

                                    $detail_product_infos[] = $detail_product;
                                }

                                $rider_info = RiderDelivery::where('shipment_id', $shipment->id)->latest()->first();

                                if (isset($rider_info)) {
                                    $rider_details['reason'] = ShipmentStatusReason::where('id', $rider_info->rider_status_reason_id)->first();
                                    $rider_details['reason'] = $rider_details['reason']['name'] ? $rider_details['reason']['name'] : '-';
                                    $rider_details['attempted_time'] = (isset($rider_info->created_at)) ? ($rider_info->created_at)->format('Y/m/d H:i:s') : '-';
                                    $rider_details['remarks'] = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                                    $rider_details['remarks'] = $rider_details['remarks']->remarks ?? '-';

                                } else {
                                    $rider_details['reason'] = '-';
                                    $rider_details['attempted_time'] = '-';
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
                                            // $image_location['image'] = Storage::disk('s3')->temporaryUrl($rider_delivery->picture_path, now()->addMinutes(5));
                                            $image_location['image'] = '-----';
                                        }
                                    }
                                    if ($rider_delivery->audio_path != null) {
                                        $exists = Storage::disk('public')->exists($rider_delivery->audio_path);
                                        if ($exists) {
                                            $image_location['audio'] = asset(Storage::url($rider_delivery->audio_path));
                                        } else {
                                            // $image_location['audio'] = Storage::disk('s3')->temporaryUrl($rider_delivery->audio_path, now()->addMinutes(5));
                                            $image_location['audio'] = '-----';
                                        }
                                    }

                                    if ($rider_delivery->actual_location_latitude != null && $rider_delivery->actual_location_longitude != null) {
                                        $image_location['location'] = $rider_delivery->actual_location_latitude . ',' . $rider_delivery->actual_location_longitude;
                                    }
                                }


                                // if (isset($shipment_assigned_agents)) {
                                //     return response()->json(['status' => 1, 'call_history' => $call_history ,'shipment_statuses' => $shipment_statuses,'rider_details' => $rider_details, 'image_location' => $image_location, 'business_category' => $business_category, 'service_type' => $service_type, 'detail_product_infos' => $detail_product_infos, 'shipping_mode' => $shipping_mode, 'shipment' => $shipment, 'shipper_info' => $shipper_info, 'shipper_city' => $shipper_city, 'consignee_city' => $consignee_city, 'message' => 'Assign Successfully']);
                                // } 
                                // else {
                                //     return response()->json(['status' => 0, 'call_history' => $call_history ,'shipment_statuses' => $shipment_statuses,'rider_details' => $rider_details, 'image_location' => $image_location, 'business_category' => $business_category, 'service_type' => $service_type, 'detail_product_infos' => $detail_product_infos, 'shipping_mode' => $shipping_mode, 'shipment' => $shipment, 'shipper_info' => $shipper_info, 'shipper_city' => $shipper_city, 'consignee_city' => $consignee_city, 'message' => 'Already Assigned']);
                                // }
                                
                                return response()->json(['status' => 0, 'call_history' => $call_history ,'shipment_statuses' => $shipment_statuses,'rider_details' => $rider_details, 'image_location' => $image_location, 'business_category' => $business_category, 'service_type' => $service_type, 'detail_product_infos' => $detail_product_infos, 'shipping_mode' => $shipping_mode, 'shipment' => $shipment, 'shipper_info' => $shipper_info, 'shipper_city' => $shipper_city, 'consignee_city' => $consignee_city, 'message' => 'Already Assigned']);
                            } catch (Exception $ex) {
                                return response()->json(['status' => 2, 'error' => $ex->getMessage()]);
                            }
                        }

                        else{
                            return response()->json(['status' => 5, 'errors' => 'No zone assigned or shipment not found']);
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
            'rv_assign_agent_sub_status_id' => 'required_unless:rv_assign_agent_status_id, 2, 3, 5',
            'is_fake_status' => 'required',
            'rv_fake_status_id' => 'required_if:is_fake_status, 1',
            'remarks' => Rule::requiredIf(function () use ($request) {
                return $request->rv_assign_agent_status_id == 6 && $request->rv_assign_agent_sub_status_id == 19;
            }), //if unresponsive and other is selected remark is required
        ];

        $data = [
            'rv_assign_agent_status_id' => $request->input('rv_assign_agent_status_id'),
            'rv_assign_agent_sub_status_id' => $request->input('rv_assign_agent_sub_status_id'),
            'is_fake_status' => $request->input('is_fake_status'),
            'rv_fake_status_id' => $request->input('rv_fake_status_id') ?? null,
            'remarks' => $request->input('remarks') ?? null,

        ];

        $validate = Validator::make($data, $validations);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'errors' => $validate->errors()]);
        } 
        else 
        {
            $current_time = Carbon::now();
            $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 1)->latest()->first();
            if($shipment_assign_agent){
                $assign_agent = RvShipmentAgent::where('agent_id', $shipment_assign_agent->agent_id)->whereDate('created_at', date('Y-m-d'))->first();
                $admin_agent = Admin::where('id', Auth::id())->first();
                $shipments_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
                $employee = Employee::where('phone_number', $request->phone_number)->first();
                $employee_shift = EmployeeShift::where('id', $employee->shift_id)->first();
    
                // Check Employee Shift Time
                if ($current_time->between(Carbon::parse($employee_shift['start_time']), Carbon::parse($employee_shift['end_time']))) {
                    try{
                        DB::beginTransaction();
                            //if agent already exists on same date update row
                        if ($assign_agent) 
                        {
                            //if shipment already exists update row
                            if ($shipment_assign_agent) {
                                $update_shipment_status = $this->update_shipment_status($request); //updating status of shipment
                                if ($update_shipment_status['status'] == 0) {
                                    DB::rollBack();
                                    return response()->json(['status' => 4, 'error' => $update_shipment_status['error']]);
                                } else{
                                    $rv_shipment_assign_agent_details = $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey);
                                    if($rv_shipment_assign_agent_details != true){
                                        DB::rollBack();
                                        return response()->json(['status' => 3, 'errors' => 'Shipment Details not updated']);
                                    } else{
                                        $add_shipment_agent = $this->update_shipment_assign_agent($request, $assign_agent, $admin_agent, $shipment_assign_agent);
                                        if($add_shipment_agent != true){
                                            DB::rollBack();
                                            return response()->json(['status' => 3, 'errors' => 'Agent Not Updated']);
                                        } else {
                                            DB::commit();
                                            return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
                                        }
                                    }
                                }
                            } else {
                                DB::rollBack();
                                return response()->json(['status' => 1, 'errors' => 'No Shipment Exist']);
                            }
                        } 
                        else {
                            $update_shipment_status = $this->update_shipment_status($request); //updating status of shipment
                            if ($update_shipment_status['status'] == 0) {
                                DB::rollBack();
                                return response()->json(['status' => 4, 'error' => $update_shipment_status['error']]);
                            } else {
                                $rv_shipment_assign_agent_details = $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey);
                                if($rv_shipment_assign_agent_details != true){
                                    DB::rollBack();
                                    return response()->json(['status' => 3, 'errors' => 'Shipment Details not updated']);
                                } else {
                                    $add_shipment_agent = $this->add_shipment_agent($request, $shipment_assign_agent);
                                    if($add_shipment_agent != true){
                                        DB::rollBack();
                                        return response()->json(['status' => 3, 'errors' => 'Agent Not Updated']);
                                    } else {
                                        DB::commit();
                                        return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
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
                else 
                {
                    Auth::logout();
                    return response()->json(['status' => 2, 'success' => 'Successfully logout']);
                }
            }
            else{
                return response()->json(['status' => 3, 'errors' => 'This Shipment is Unassigned']);
            }
        }

    }
}
