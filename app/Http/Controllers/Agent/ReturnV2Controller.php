<?php

namespace App\Http\Controllers\Agent;

use App\Http\Traits\RvTrait;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Http\Models\Admin\Admin;
use App\Http\Models\RvFakeStatus;
use App\Http\Models\RiderDelivery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\RvAgentAssignHub;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Admin\SubStatusCallFinding;
use App\Http\Models\RvAssignAgentStatus;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\RvAssignAgentSubStatus;
use App\RvShipmentAgent;
use Exception;

class ReturnV2Controller extends Controller
{
    use RvTrait;

    public function __construct()
    {
        $this->middleware('auth:agent');
        $this->middleware('Permission');
    }


    // Heading: Virtual RCP Agent Screen
    // Siderbar: N/A
    // URL: agent/dashboard
    // Description: this method is used to retrive Logged User, ShipmentStatus(RV) And Fake Status On Index Page

    public function index()
    {
        $user = Auth::user();
        $shipment_statuses = RvAssignAgentStatus::whereIn('id', [1, 2, 3, 4, 5])->get();
        $fake_status_remarks = RvFakeStatus::get();
        return view('agent.return_v2.index')->with(['user' => $user, 'shipment_statuses' => $shipment_statuses, 'fake_status_remarks' => $fake_status_remarks]);
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: agent/dashboard/get_shipment_reason
    // Description: this method is used to retrive SubStatuses Of Status And Remark Of Call Finding On Index Page

    public function get_shipment_reason(Request $request)
    {
        $reasons = RvAssignAgentSubStatus::where('rv_assign_agent_status_id', $request->id)->where('is_active', 1)->get();
        $unresponsive_reasons = SubStatusCallFinding::get();
        return response()->json(['reasons' => $reasons, 'unresponsive_reasons' => $unresponsive_reasons, 'status' => 1]);
    }

    public function get_ticket(Request $request)
    {
        // Get all assigned agent to hubs priority wise
        $sorted_agents = RvAgentAssignHub::where('agent_id', $request->auth_id)->orderBy('priority', 'ASC')->get();

        foreach ($sorted_agents as $key => $agent) {
            // Assigned hub to priority city and shipper status is 12 which is activate for return shipments
            $shipments = Shipment::where('consignee_city_id', $agent['city_id'])->where('shipper_status_id', 12)->orderBy('id', 'ASC')->get();
            // check if shipments exist
            if (count($shipments)) {

                foreach ($shipments as $key => $shipment) {

                    # code...
                    // if agent shipment is open - assigned to different user
                    $shipment_assigned_unassigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->where('rv_state_id', 3);
                    if ($shipment_assigned_unassigned_agent->exists()) {
                        $shipment_assigned_unassigned_agent->first();
                        break 2;
                    }

                    // if agent shipment is unassigned - assigned to same agent only - if close mistakely or in case of lost page

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
                    $this->newRvShipmentAssign($data);

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
    }
    public function get_intercepted_shipment(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $getData = $this->getShipmentConsigneeCities($shipment_id);
        return response()->json(['status' => 1, 'shipment' => $getData['shipment'], 'consignee_cities' => $getData['cosignee_cities']]);
    }



    public function get_submit(Request $request)
    {
        // dd($request->all());
        $validations = [
            'rv_assign_agent_status_id' => 'required',
            'rv_assign_agent_sub_status_id' => 'required',
            'is_fake_status' => 'required',
            'rv_fake_status_id' => 'required_if:is_fake_status,1',
        ];

        $data = [
            'rv_assign_agent_status_id' => $request->input('rv_assign_agent_status_id'),
            'rv_assign_agent_sub_status_id' => $request->input('rv_assign_agent_sub_status_id'),
            'is_fake_status' => $request->input('is_fake_status'),
            'rv_fake_status_id' => $request->input('rv_fake_status_id') ?? null,

        ];

        $validate = Validator::make($data, $validations);

        if ($validate->fails()) {
            return response()->json(['status' => 0, 'errors' => $validate->errors()]);
        } 
        
        else {

            $assign_agent = RvShipmentAgent::where('agent_id', Auth::id())->latest()->first();
            $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->first();
            $admin_agent = Admin::where('id', Auth::id())->first();

            //if shipment already exists update row
            if ($shipment_assign_agent->exists()) {
                $shipment_assign_agent = $shipment_assign_agent->latest()->first();

                // //if agent already exists
                // if ($assign_agent) {
                //     $assign_agent = $assign_agent->latest()->first();
                //     $this->updateShipmentAssignAgent($request, $assign_agent, $admin_agent, $shipment_assign_agent);
                // } //end if agent already exist 

                // else {
                //     //Creating row of new agent if agent not found
                //     $rv_shipment_agent = new RvShipmentAgent();
                //     $rv_shipment_agent->agent_id = Auth::id();
                //     $rv_shipment_agent->save();
                //     $this->updateShipmentAssignAgent($request, $rv_shipment_agent, $admin_agent, $shipment_assign_agent);
                //     $rv_shipment_agent->save();
                // }

                // //Maintaining Log in RvTrait
                // $this->makeRvShipmentAssignAgentDetails($shipment_assign_agent, $request);

                $shipment_ids = $request->shipment_id;
                $remarks= $request->remarks;

                dd($shipment_ids, $remarks);
                $this->changeShipmentStatus($request->shipment_reason_id, $shipment_ids, $remarks);

                if($request->shipment_reason == 'confirm'){
                }

                switch ($request->shipment_reason_id) {
                    case 1: // is for Return confirm - 20 shipment_status_id
                        # code...
                        break;
                    case 2: // is for Reattempt - 13 shipment_status_id

                        # code...
                        break;
                    case 3: // is for Intercept - 54 shipment_status_id

                        # code...
                        break;
                    case 4: // is for On Hold Self collection - 15 shipment_status_id
                        # code...
                        break;
                    case 5: // is for Unresponsive - part of call findings i.e id: 1

                        # code...
                        break;
                    
                    default:
                        # code...
                        break;
                }

                return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
            } 

            else{
                return response()->json(['status' => 1, 'error' => 'Something went wrong!']);
            }
            
        }
    }

    public function changeShipmentStatus($shipment_reason_id, $shipment_ids, $remarks) {
        dd(is_array($shipment_ids));
        if(!is_array($shipment_ids)){
            
        } else {

            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                if(!in_array($parcel->shipper_status_id, [13, 20])){
                    $remark_inp = "remark.$shipment";
                    $remarks = $request->remark[$parcel->id] != null? $request->remark[$parcel->id] : null;
    
                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();
    
    
    
                    if ($journey) {
                        if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;
    
                            $parcel->save();
    
                            ShipmentChargesController::nsa_osa_charges($shipment);
    
                            NotificationsController::send(33, $shipment);
                        }
                        else if ($parcel->shipper_status_id == 52) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();
    
                            if ($journey && ($journey->status_reason_id == 12)) {
                                $parcel->nsa_osa_status = 1;
    
                                $parcel->save();
    
                                ShipmentChargesController::nsa_osa_charges($shipment);
                            }
                        }
                    }
    
                    $parcel->shipper_status_id = 13;
                    $parcel->consignee_status_id = 13;
                    $parcel->save();
    
                    ShipmentsJourneyController::add($shipment, 13, 13, NULL, $remarks, NULL, Auth::id());
                   $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                   if($return_assign_shipment){
                       $return_assign_shipment->status = 0;
                       $return_assign_shipment->save();
    
                       $return_assign_log = new ReturnAssignedShipmentLogs();
                       $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                       $return_assign_log->status = 1;
                       $return_assign_log->assigned_by = Auth::id();
                       $return_assign_log->save();
                   }
                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);
    
                    $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                    $reattempt_remarks_col->shipment_id = $shipment;
                    $reattempt_remarks_col->remarks = 'Manual';
                    $reattempt_remarks_col->save();
                }
    
            }
        }
        
    }

    public function return_reattempt_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt
        
        $shipment_ids = $request->shipment_ids;

        if($request->action == 'reattempt'){
            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                if(!in_array($parcel->shipper_status_id, [13, 20])){
                    $remark_inp = "remark.$shipment";
                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;

                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();



                    if ($journey) {
                        if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($shipment);

                            NotificationsController::send(33, $shipment);
                        }
                        else if ($parcel->shipper_status_id == 52) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();

                            if ($journey && ($journey->status_reason_id == 12)) {
                                $parcel->nsa_osa_status = 1;

                                $parcel->save();

                                ShipmentChargesController::nsa_osa_charges($shipment);
                            }
                        }
                    }

                    $parcel->shipper_status_id = 13;
                    $parcel->consignee_status_id = 13;
                    $parcel->save();

                    ShipmentsJourneyController::add($shipment, 13, 13, NULL, $remarks, NULL, Auth::id());
                   $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                   if($return_assign_shipment){
                       $return_assign_shipment->status = 0;
                       $return_assign_shipment->save();

                       $return_assign_log = new ReturnAssignedShipmentLogs();
                       $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                       $return_assign_log->status = 1;
                       $return_assign_log->assigned_by = Auth::id();
                       $return_assign_log->save();
                   }
                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                    $reattempt_remarks_col->shipment_id = $shipment;
                    $reattempt_remarks_col->remarks = 'Manual';
                    $reattempt_remarks_col->save();
                }

            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )"];

        }
    }

    public function change_status_to_self_collection(Request $request){

        $shipmentId = $request->shipment_id;
        $remark = $request->remark;
        if($shipmentId){
            if(Shipment::where('id', $shipmentId)->where('shipper_status_id','!=', 15)->exists()){
              
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>15,'consignee_status_id'=>15]);
                ShipmentsJourneyController::add($request->shipment_id, 15, 15, NULL, $remark, NULL, Auth::id());

                //
                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                if($return_assign_shipment){
                    $return_assign_shipment->status = 0;
                    $return_assign_shipment->save();
                
                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    $return_assign_log->status = 7;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();
                }
                
                return ['status'=>0, 'success'=>"Shipment status successfully updated to Shipment - On Hold for Self Collection"];
            }else{
                return response()->json(['status' => 1, 'error' => 'Shipment already updated to Shipment - On Hold for Self Collection!']);
            }

        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment ID Not selected!']);
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
