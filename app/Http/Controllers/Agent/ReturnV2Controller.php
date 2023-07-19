<?php

namespace App\Http\Controllers\Agent;

use Exception;
use Carbon\Carbon;
use App\RvShipmentAgent;
use App\Http\Traits\RvTrait;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\RvAssignAgentSubStatus;
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
use Illuminate\Support\Facades\Validator;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Admin\SubStatusCallFinding;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\ReattemptShipmentStatusRemarks;
use Illuminate\Validation\Rule;

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
        $shipment_statuses = RvAssignAgentStatus::where('is_active', 1)->where('is_visible', 1)->get();
        $fake_status_remarks = RvFakeStatus::get();
        return view('agent.return_v2.index')->with(['user' => $user, 'shipment_statuses' => $shipment_statuses, 'fake_status_remarks' => $fake_status_remarks]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: agent/dashboard/get_shipment_reason
    // Description: this method is used to retrieve SubStatuses Of Status And Remark Of Call Finding On Index Page

    public function get_shipment_reason(Request $request)
    {
        $reasons = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', $request->id)->where('is_active', 1)->get();
        $unresponsive_reasons = SubStatusCallFinding::get();
        return response()->json(['reasons' => $reasons, 'unresponsive_reasons' => $unresponsive_reasons, 'status' => 1]);
    }

    // Heading: N/A
    // Sidebar: N/A
    // URL: 
    // Description:
    public function get_ticket(Request $request)
    {
        // Get all assigned agent to hubs priority wise
        $sorted_agents = RvAgentAssignHub::where('agent_id', $request->auth_id)->orderBy('priority', 'ASC')->get();

        $admin = Admin::where('id', Auth::id());

        if ($admin->exists()) {
            $admin = $admin->first();
            $employee = Employee::where('trax_id', $admin->trax_id)->where('staff_category_id', 3)->where('status_id', '!=', 2);

            if ($employee->exists()) {
                $current_time = Carbon::now();
                $employee = $employee->first();

                $shift_exist = EmployeeShift::where('id', $employee->shift_id)->where('shift_type_id', 2)->first();
                if ($shift_exist) {
                    // $shift_exist =  EmployeeShift::where('id', $employee->shift_id)->first();
                    $start_time = Carbon::parse($shift_exist->start_time);
                    $end_time = Carbon::parse($shift_exist->end_time);
                    if ($current_time->between($start_time, $end_time)) {
                        foreach ($sorted_agents as $key => $agent) {
                            // Assigned hub to priority city and shipper status is 12 which is activate for return shipments
                            $shipments = Shipment::where('consignee_city_id', $agent['city_id'])->where('shipper_status_id', 12)->orderBy('id', 'ASC')->get();
                            // check if shipments exist
                            if (count($shipments)) {

                                foreach ($shipments as $key => $shipment) {

                    # code...
                    // if agent shipment is open - assigned to any user who comes first
                    $shipment_assigned_unassigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->where('rv_state_id', 3);
                    if ($shipment_assigned_unassigned_agent->exists()) {
                        $shipment_assigned_unassigned_agent->first();
                        break 2;
                    }

                    // if agent shipment is assigned - assigned to same agent only - if close mistakenly or in case of lost page

                                    $shipment_assigned_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->where('agent_id', Auth::id())->where('rv_state_id', 1);
                                    if ($shipment_assigned_assigned_agent->exists()) {
                                        $shipment_assigned_assigned_agent->first();
                                        break 2;
                                    }

                                    // Shipment is found and already in working state or return is completed, new shipment will get to agent
                                    $find_shipment_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->first();
                                    if ($find_shipment_assigned_agent) {
                                        continue;
                                    }

                                    $data = [
                                        'shipment_id' => $shipment->id,
                                        'rv_state_id' => 1, //Assigned
                                        'rv_assign_agent_status_id' => null,
                                        'rv_assign_agent_sub_status_id' => null,
                                    ];

                                    // creating a new record
                                    $this->rv_shipment_assign($data);

                                    break 2;
                                }
                            }
                        }

                        try {
                            $shipper_city = $shipment->pickup_address->city;
                            $shipper_info = $shipment->user;
                            $service_type = $shipment->booking_type;
                            $consignee_city = $shipment->consignee_city;
                            $product_infos = $shipment->items;
                            $shipping_mode = $shipment->shipping_mode;
                            $business_category = $shipment->business_category;
                            $detail_product_infos = [];

                            foreach ($product_infos as $product_info) {

                                $detail_product = [
                                    'product_name' => $product_info->product->product_name,
                                    'description' => $product_info->description,
                                    'quantity' => $product_info->quantity,
                                    'order_id' => $product_info->shipment->order_id
                                ];

                                $detail_product_infos[] = $detail_product;
                            }


                            $image_location = [];

                            $rider_delivery = RiderDelivery::where('shipment_id', $shipment->id)->first();
                            if ($rider_delivery != null) {
                                if ($rider_delivery->picture_path != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->picture_path);
                                    if ($exists) {
                                        $image_location['image'] =  asset(Storage::url($rider_delivery->picture_path));
                                    } else {
                                        $image_location['image'] = Storage::disk('s3')->temporaryUrl($rider_delivery->picture_path, now()->addMinutes(5));
                                    }
                                }
                                if ($rider_delivery->audio_path != null) {
                                    $exists = Storage::disk('public')->exists($rider_delivery->audio_path);
                                    if ($exists) {
                                        $image_location['audio'] =  asset(Storage::url($rider_delivery->audio_path));
                                    } else {
                                        $image_location['audio'] = Storage::disk('s3')->temporaryUrl($rider_delivery->audio_path, now()->addMinutes(5));
                                    }
                                }

                                if ($rider_delivery->actual_location_latitude != null && $rider_delivery->actual_location_longitude != null) {
                                    $image_location['location'] = $rider_delivery->actual_location_latitude . ',' . $rider_delivery->actual_location_longitude;
                                }
                            }


                            if (isset($shipment_assigned_agents)) {
                                return response()->json(['status' => 1, 'image_location' => $image_location, 'business_category' => $business_category, 'service_type' => $service_type, 'detail_product_infos' => $detail_product_infos, 'shipping_mode' => $shipping_mode, 'shipment' => $shipment, 'shipper_info' => $shipper_info, 'shipper_city' => $shipper_city, 'consignee_city' => $consignee_city, 'message' => 'Assign Successfully']);
                            } else {
                                return response()->json(['status' => 0, 'image_location' => $image_location, 'business_category' => $business_category, 'service_type' => $service_type, 'detail_product_infos' => $detail_product_infos, 'shipping_mode' => $shipping_mode, 'shipment' => $shipment, 'shipper_info' => $shipper_info, 'shipper_city' => $shipper_city, 'consignee_city' => $consignee_city, 'message' => 'Already Assigned']);
                            }
                        } catch (Exception $ex) {
                            return response()->json(['status' => 2, 'error' => $ex->getMessage()]);
                        }
                    } else {
                        Auth::logout();
                        return response()->json(['status' => 3]);
                    }
                }else{
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
    public function submit_ticket(Request $request)
    {
        $validations = [
            'rv_assign_agent_status_id' => 'required',
            'rv_assign_agent_sub_status_id' => 'required_unless:rv_assign_agent_status_id, 2, 3, 5', //2  reattempt, 3 intercept, 5 on hold
            'is_fake_status' => 'required',
            'rv_fake_status_id' => 'required_if:is_fake_status, 1',
            'remarks' => Rule::requiredIf(function () use ($request) {
                return $request->rv_assign_agent_status_id == 6 && $request->rv_assign_agent_sub_status_id == 7;
            }),//if unresponsive and other is selected remark is required
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
        
        else {
            $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->first();
            $assign_agent = RvShipmentAgent::where('agent_id', $shipment_assign_agent->agent_id)->whereDate('created_at',date('Y-m-d'))->first();
            $admin_agent = Admin::where('id', Auth::id())->first();

            //if agent already exists on same date update row
            if($assign_agent){

            //if shipment already exists update row
                if ($shipment_assign_agent) {
                    $this->update_shipment_status($request); //updating status of shipment
                    $this->update_shipment_assign_agent($request, $assign_agent, $admin_agent, $shipment_assign_agent); 
                    $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent);
        
                    return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
                } 
                else {
                    return response()->json(['status' => 1, 'errors' => 'No Shipment Exist']);
                }
            }
            else{
                $this->update_shipment_status($request); //updating status of shipment
                $this->add_shipment_agent($request, $shipment_assign_agent);
                $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent);
                return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
            }
        }
    }
}





         // $admin = Admin::where('id', $request->auth_id);
        // if ($admin->exists()) {
        //     $admin = $admin->first();
        //     $employee_attendance = EmployeeAttendance::where('employee_id', $admin->employee_id)->where('attendance_date', date('Y-m-d'));

        //     if($employee_attendance->exists()){
        //         $employee_attendance->update(['clock_in'=>date('H:i:s')]);
        //     } else {

        //         $employee_attendance = new EmployeeAttendance();
        //         $employee_attendance->employee_id = $admin->employee_id;
        //         $employee_attendance->attendance_date = date('Y-m-d');
        //         $employee_attendance->clock_in = date('H:i:s');
        //         $employee_attendance->employee_type = $admin->employee_type ? $admin->employee_type : '1';
        //         // $employee_attendance->clock_out_latitude = session('latitude');
        //         // $employee_attendance->clock_out_longitude = session('longitude');
        //         $employee_attendance->save();
        //     }


        //     return response()->json(['employee_attendance'=>$employee_attendance]);

        // }
