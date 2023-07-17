<?php

namespace App\Http\Traits;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\AdminInterceptRebookRequestHistoryController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\OsaChargesLog;
use App\Http\Models\Admin\ReattemptPercentageForShipper;
use App\Http\Models\Admin\ReattemptShipmentStatusRemarks;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\RvAssignAgentStatus;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait RvTrait
{

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function getShipmentConsigneeCities($shipment_id){
        if($shipment_id){
            $shipment = Shipment::where('id',$shipment_id)->first();
            if($shipment){
                if($shipment->shipping_mode_id == 2){
                    $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                    ->select('c.id as id', 'c.name as name')
                    ->where('shipments.id', $shipment_id)
                    ->where('cd.shipping_mode_id',2)
                    ->where('c.status', 1)
                    ->whereNotNull('c.zone_id')
                    ->whereNotIn('c.id', $restricted_cities);

                }
                else{
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('c.status', 1)
                        ->whereNotNull('c.zone_id');
                }
                $consignee_cities = $consignee_cities->orderBy('c.name')->groupBy('c.name')->get();
                return (['shipment' => $shipment, 'consignee_cities' => $consignee_cities]);
            }
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function newRvShipmentAssign($data)
    {
        try {
            RvShipmentAssignAgent::updateOrCreate(
                [
                    'shipment_id' => $data['shipment_id'],
                ],
                [
                    'agent_id' => Auth::id(),
                    'rv_state_id' => $data['rv_state_id'] ?? 1,
                    'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'] ?? null,
                    'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'] ?? null,
                ]

            );
            return true;
        } catch (\Throwable $th) {
            return ['status' => 0, 'error' => $th->getMessage()];
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function makeRvShipmentAssignAgentDetails($shipment_assign_agent, $request)
    {
        $rv_shipment_assign_agent_details  = new RvShipmentAssignAgentDetails();
        $rv_shipment_assign_agent_details->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
        $rv_shipment_assign_agent_details->agent_id = Auth::id();
        $rv_shipment_assign_agent_details->shipment_id = $request->shipment_id;
        $rv_shipment_assign_agent_details->rv_assign_agent_status_id = $shipment_assign_agent->rv_assign_agent_status_id;
        $rv_shipment_assign_agent_details->rv_assign_agent_sub_status_id = $shipment_assign_agent->rv_assign_agent_sub_status_id;
        $rv_shipment_assign_agent_details->rv_state_id = $shipment_assign_agent->rv_state_id;
        $rv_shipment_assign_agent_details->updated_type_id = $shipment_assign_agent->updated_type_id;
        $rv_shipment_assign_agent_details->updated_by_id = $shipment_assign_agent->updated_by_id;
        $rv_shipment_assign_agent_details->is_fake_status = $request->is_fake_status;
        $rv_shipment_assign_agent_details->rv_fake_status_id = $request->rv_fake_status_id;
        $rv_shipment_assign_agent_details->remarks = $request->remarks;
        $rv_shipment_assign_agent_details->call_to_id  = $request->call_to_id;
        $rv_shipment_assign_agent_details->save();

        return true;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    private function shipment_assign_agent_table_columns($request, $assign_agent)
    {
        return [
            'rv_assign_agent_status_id' => $request->rv_assign_agent_status_id,
            'rv_assign_agent_sub_status_id' => $request->rv_assign_agent_sub_status_id,
            'rv_fake_status_id' => $request->fake_status,
            'remarks' => $request->shipment_remarks,
            'rv_state_id' => $request->is_fake_status,
            'call_to_id' => $request->call_to_id,
            'updated_by_id' => Auth::id(),
            'is_fake_status' => $request->fake_status,
            'rv_shipment_agent_id' => $assign_agent->id,
        ];
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function updateShipmentAssignAgent($request, $assign_agent, $admin_agent, $shipment_assign_agent)
    {
        $shipment_assign_agent_table_columns = $this->shipment_assign_agent_table_columns($request, $assign_agent);

        if ($admin_agent->employee->staff_category_id == 3) {
            $assign_agent->increment('total_shipments');
            $assign_agent->increment('actual_productivity');
            $shipment_assign_agent_table_columns['updated_type_id'] = 2; // agent type
        } else {
            $assign_agent->increment('already_updated');
            $shipment_assign_agent_table_columns['updated_type_id'] = 1; // admin type
        }

        $shipment_assign_agent->update($shipment_assign_agent_table_columns);

        return true;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function changeShipmentStatus($request)
    {
        if ($request->rv_assign_agent_status_id) {
            $rv_assign_agent_status = RvAssignAgentStatus::find($request->rv_assign_agent_status_id);
            $shipment_status_id = $rv_assign_agent_status->shipment_status_id; //replicate values from shipment_status table
            $call_finding_id = $rv_assign_agent_status->call_finding_id; // this is for unresponsive
            if ($rv_assign_agent_status && ($shipment_status_id || $call_finding_id)) {
                switch ($shipment_status_id) {
                    case '13': //Shipment - Re-Attempt
                        $this->reattempt($request);
                        break;
                    case '15': // Shipment - On Hold for Self Collection
                        $this->on_hold_for_self_collection($request);
                        break;
                    case '20': // Return - Confirm
                        $this->return_confirm($request);
                        break;
                    case '54': // Intercept Requested
                        $this->intercept($request);
                        break;
                    case null: // Unresponsive
                        $this->unresponsive($request);
                        break;

                    default:
                        # code...
                        break;
                }
            }
        } else {
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function return_confirm($request)
    {
        $remarks = $request->remarks;
        $parcel = Shipment::find($request->shipment_id);

        $return_reason = $request->single_return_reason_select;
        $consignee_refused_reasons = $request->consignee_refused_reasons;
        $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
        if (!$dispute_check) {
            return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
        }
        if ($parcel->booking_type_id == 5) {
            return ['status' => 0, 'error' => "Reverse Pickup Shipment can not be updated to Return Confirm!"];
        }
        if (!in_array($parcel->shipper_status_id, [13, 15, 20, 54, 55]) && ($parcel->shipper_status_id == 12 || $parcel->shipper_status_id == 52)) {

            Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
            NotificationsController::send(15, 0, $request->shipment_id);
            NotificationsController::send(16, 0, $request->shipment_id);

            if ($parcel->shipment_type == 1) {
                if ($parcel->booking_type_id != 4) {
                    ShipmentChargesController::return($request->shipment_id);
                    
                    if ($parcel->packaging_material_request != 1) {
                        AdminFinanceController::add_payment($request->shipment_id, 1);
                    }
                } else {
                    ShipmentChargesController::walk_in_return($request->shipment_id);

                    $parcel->walk_in_status = 2;

                    $parcel->save();

                    AdminFinanceController::done_payment($request->shipment_id, 1);
                }
            }
            ShipmentsJourneyController::add($request->shipment_id, 20, 20, $return_reason, $remarks, NULL, Auth::id(), null, null, 1, null, null, null, null, $consignee_refused_reasons);

            return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Return Confirm"];
        }
        return ['status' => 0, 'error' => "Shipment is in different status, Cannot mark it as Return - Confirm!"];
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function reattempt($request)
    {
        $remarks = $request->remarks;
        $parcel = Shipment::find($request->shipment_id);
        if ($request->has('charges')) {
            if ($request->charges != null) {
                $check = $this->update_estimate_charges($request->shipment_id, $request->charges);
                if ($check != 0) {
                    return ['status' => 0, 'error' => "Shipment not found on Estimation Charges"];
                }
            } else {
            }
        }

        if (!in_array($parcel->shipper_status_id, [13, 20]) && ($parcel->shipper_status_id == 12 || $parcel->shipper_status_id == 52)) {
            $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->whereIn('shipper_status_id', [12, 52])->latest('id')->first();

            if ($journey) {
                if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                    $parcel->nsa_osa_status = 1;

                    $parcel->save();

                    ShipmentChargesController::nsa_osa_charges($request->shipment_id);

                    NotificationsController::send(33, $request->shipment_id);
                } else if ($parcel->shipper_status_id == 52) {
                    $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->latest('id')->first();

                    if ($journey && ($journey->status_reason_id == 12)) {
                        $parcel->nsa_osa_status = 1;

                        $parcel->save();

                        ShipmentChargesController::nsa_osa_charges($request->shipment_id);
                    }
                }

                $parcel->shipper_status_id = 13;
                $parcel->consignee_status_id = 13;
                $parcel->save();

                ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remarks, NULL, Auth::id());

                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);
                $reattempt_remarks_col = new ReattemptShipmentStatusRemarks();
                $reattempt_remarks_col->shipment_id = $request->shipment_id;
                $reattempt_remarks_col->remarks = 'Manual';
                $reattempt_remarks_col->save();
            }

            return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Re-Attempt"];
        }
        return ['status' => 0, 'error' => "Shipment is in different status, Cannot mark it as Reattempted!"];
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    private function update_estimate_charges($shipment, $charge)
    {
        $shipment_id = $shipment;
        $charges = $charge;
        if ($shipment_id) {
            if ($charges != null) {
                $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipment_id);
                if ($consolidated_shipments->exists()) {
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $all_consolidation_shipments)->update(['nsa_osa_estimated_charges' => $charges]);
                } else {
                    $shipment = Shipment::find($shipment_id);
                    $shipment->nsa_osa_estimated_charges = $charges;
                    $shipment->save();
                }
                $this->add_osa_charges($shipment_id, $charges);

                return 0;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function add_osa_charges($shipment, $charge) //function to add in logs table //call from reattempt function
    {
        $nsa_charges_log = new OsaChargesLog();
        $nsa_charges_log->shipment_id = $shipment;
        $nsa_charges_log->osa_charges = $charge;
        $nsa_charges_log->updated_by = Auth::id();
        $nsa_charges_log->save();
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function assign_agent(Request $request)
    {
        $shipment_ids = $request->shipment_ids;
        if ($shipment_ids) {
            foreach ($shipment_ids as $shipment_id) {
                $check_already_assigned = ReturnAssignedShipments::where('shipment_id', $shipment_id)->where('status', 1)->first();
                if ($check_already_assigned) {
                    $check_already_assigned->status = 0;
                    $check_already_assigned->save();

                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $check_already_assigned->id;
                    $return_assign_log->status = 4;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();
                }
                $assign_shipments = new ReturnAssignedShipments();
                $assign_shipments->admin_id = $request->admin_id;
                $assign_shipments->shipment_id = $shipment_id;
                $assign_shipments->status = 1;
                $assign_shipments->assigned_by = Auth::id();
                $assign_shipments->save();

                $return_assign_log = new ReturnAssignedShipmentLogs();
                $return_assign_log->return_assign_shipment_id = $assign_shipments->id;
                $return_assign_log->status = 0;
                $return_assign_log->assigned_by = Auth::id();
                $return_assign_log->save();

                //set record in login/logut table
                // $check_agent_return_confrimation = AgentReturnConfirmation::where('admin_id',$request->admin_id)->where('current_date',Carbon::now()->format("Y-m-d"));

                // if(!$check_agent_return_confrimation->exists()){


                //     $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                //      ->where('admin_roles.department_id',3)->where('a.id',$request->admin_id)->where('a.status',1);

                //      if($agent_role->exists()){
                //          $agent_return_confrimation = new AgentReturnConfirmation;
                //          $agent_return_confrimation->admin_id = $request->admin_id;
                //          $agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                //          $agent_return_confrimation->current_date = Carbon::now()->format("Y-m-d");
                //          $agent_return_confrimation->save();
                //      }
                //  }
                //  else{
                //     $check_agent_return_confrimation = $check_agent_return_confrimation->get()->first();
                //     $check_agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                //     $check_agent_return_confrimation->save();
                //  }
                //set record in login/logut table end

            }
            return response()->json(['status' => 0, 'success' => 'Shipments Assigned successfully']);
        } else {
            return response()->json(['status' => 1, 'error' => 'No Shipment found!']);
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function unassign_agent(Request $request)
    {
        $shipment_ids = $request->shipment_ids;

        if ($request->action == 'un-assign') {
            foreach ($shipment_ids as $shipment) {
                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->where('status', 1);
                if ($return_assign_shipment->exists()) {
                    $return_assign_shipment = $return_assign_shipment->latest()->first();
                    $return_assign_shipment->status = 0;
                    $return_assign_shipment->save();


                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    $return_assign_log->status = 4;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();
                }
            }
            return ['status' => 1, 'success' => "Agent Unassigned successfully"];
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Assigning Unassigning Agents from Excel Sheet
    public function assign_agent_excel(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'agent_id' => 'Agent ID'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules_with_agent = [
            'tracking_number' => ['required', 'integer'],
            'agent_id' => ['required', 'integer']
        ];

        $rules_without_agent = [
            'tracking_number' => ['required', 'integer']
        ];
        $fields = [0 => 'tracking_number', 1 => 'agent_id'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Agent ID'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 1) {
                } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }
        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }

            unset($spreadsheet);
            $errors = array();
            $tracking_ids = array();
            $tracking_id_row = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;
                if (!empty($row['agent_id'])) {
                    $validate = Validator::make($row, $rules_with_agent, $messages);
                } else {
                    $validate = Validator::make($row, $rules_without_agent, $messages);
                }

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
                if (empty($errors['Row #' . $row_id])) {
                    if (!empty(trim($row['tracking_number']))) {
                        if (empty($tracking_ids)) {
                            $tracking_ids[] = $row['tracking_number'];
                            $tracking_id_row[$row['tracking_number']] = $row_id;
                        } else {
                            if (in_array($row['tracking_number'], $tracking_ids)) {
                                $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                            } else {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                        }
                    }
                    if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('shipper_status_id', [12, 52])->exists()) {
                        $errors['Row #' . $row_id][] = 'Shipment is not valid #' . $row['tracking_number'];
                    }
                    if (!empty($row['agent_id'])) { //if agent = 1 or 2 or 3
                        if (!AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 3)->where('a.status', 1)->where('a.id', $row['agent_id'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Agent ID is not valid #' . $row['agent_id']; //remove for ticket no 4934
                        }
                    }
                }
            }
            if (empty($errors)) {
                $tracking_numbers = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $shipment_id = trim($row['tracking_number']); //111
                    $agent_id = trim($row['agent_id']); //22

                    $id_shipment = Shipment::where('tracking_number', $shipment_id)->first();
                    $check_already_assigned = ReturnAssignedShipments::where('shipment_id', $id_shipment->id)->where('status', 1)->first();
                    if ($check_already_assigned) {
                        $check_already_assigned->status = 0;
                        $check_already_assigned->save();
                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $check_already_assigned->id;
                        $return_assign_log->status = 4;
                        $return_assign_log->assigned_by = Auth::id();
                        $return_assign_log->save();
                    }
                    $assign_shipments = new ReturnAssignedShipments();
                    $assign_shipments->admin_id = $agent_id;
                    $assign_shipments->shipment_id =  $id_shipment->id;
                    $assign_shipments->status = !empty($agent_id) ? 1 : 0;
                    $assign_shipments->assigned_by = Auth::id();
                    $assign_shipments->save();

                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $assign_shipments->id;
                    $return_assign_log->status = !empty($agent_id) ? 0 : 4;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();


                    //if agent is !empty

                    // if(!empty($agent_id)){
                    //     $check_agent_return_confrimation = AgentReturnConfirmation::where('admin_id',$agent_id)->whereDate('current_date',Carbon::now()->format("Y-m-d"));

                    //     if(!$check_agent_return_confrimation->exists()){
                    //         $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                    //             ->where('admin_roles.department_id',3)->where('a.id',$agent_id)->where('a.status',1);

                    //         if($agent_role->exists()){
                    //             $agent_return_confrimation = new AgentReturnConfirmation;
                    //             $agent_return_confrimation->admin_id = $agent_id;
                    //             $agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                    //             $agent_return_confrimation->current_date = Carbon::now()->format("Y-m-d");
                    //             $agent_return_confrimation->save();
                    //         }
                    //     }
                    //     else{
                    //         $check_agent_return_confrimation = $check_agent_return_confrimation->get()->first();
                    //         $check_agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                    //         $check_agent_return_confrimation->save();
                    //     }

                    // }else {
                    //     //if agent data is empty
                    //     $check_agent_return_confrimation = AgentReturnConfirmation::
                    //             where('return_assigned_shipment_id', $assign_shipments->id)
                    //                 ->orderby('current_date','desc')->first();
                    //     if(!empty($check_agent_return_confrimation)) {
                    //         $check_agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                    //         $check_agent_return_confrimation->save();
                    //     }

                    // }
                    //set record in login/logut table end

                    $tracking_numbers['Row #' . $row_id] = $shipment_id;
                }
                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $row . ': ' . $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));

                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            } else {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);

                return redirect()->back()->withErrors($errors);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }


     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: for bulk shipments
    public function return_confirm_status(Request $request)
    { //update to status 20 for confirm and 13 for re-attempt
        $shipment_ids = $request->shipment_ids;
        $return_reason = $request->return_reason_select;
        $consignee_refused_reasons = $request->consignee_refused_reasons;
        $remarks = $request->remark;

        if ($request->action == 'confirm') {

            foreach ($shipment_ids as $shipment) {
                $parcel = Shipment::find($shipment);
                $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
                if (!$dispute_check) {
                    return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                }
                if ($parcel->booking_type_id == 5) {
                    continue;
                }
                $remark_inp = "remark.$shipment";
                if (!in_array($parcel->shipper_status_id, [5, 13, 15, 20, 54, 55])) {

                    //    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;
                    //    $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                    $parcel->shipper_status_id = 20;
                    $parcel->consignee_status_id = 20;
                    $parcel->save();


                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    if ($parcel->shipment_type == 1) {
                        if ($parcel->booking_type_id != 4) {
                            ShipmentChargesController::return($shipment);

                            if ($parcel->packaging_material_request != 1) {

                                AdminFinanceController::add_payment($shipment, 1);
                            }
                        } else {
                            ShipmentChargesController::walk_in_return($shipment);

                            $parcel->walk_in_status = 2;

                            $parcel->save();

                            AdminFinanceController::done_payment($shipment, 1);
                        }
                    }
                    ShipmentsJourneyController::add($shipment, 20, 20, $return_reason, $remarks, NULL, Auth::id(), null, null, 1, null, null, null, null, $consignee_refused_reasons);
                }
            }
            return ['status' => 1, 'success' => "Shipment successfully updated as ( Return Confirm )"];
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Updating shipment status (return confirm / Reattempt) from Excel Sheet in Rcp Screen
    public function excel_store(Request $request)
    {
        $names = [
            'tracking_number' => 'Tracking Number',
            'shipper_status_id' => 'Status (0 - Confirm / 1 - Re-Attempt)',
            'remarks' => 'Remarks',
            'estimation_charges' => 'Estimation Charges'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules = [
            'tracking_number' => ['required', 'integer'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:0,1'],
            'remarks' => ['nullable', 'between:0,190'],
            'estimation_charges' => ['nullable', 'integer']
        ];
        $fields = [0 => 'tracking_number', 1 => 'shipper_status_id', 2 => 'remarks', 3 => 'estimation_charges'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Status (0 - Confirm / 1 - Re-Attempt)', 'Remarks', 'Estimation Charges'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 2) {
                } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }
        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }

            unset($spreadsheet);
            $errors = array();
            $tracking_ids = array();
            $tracking_id_row = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
                if (empty($errors['Row #' . $row_id])) {
                    $shid = Shipment::where('tracking_number', $row['tracking_number'])->first();
                    $parcel = ShipmentsJourney::where('shipment_id', $shid->id)->latest('id')->first();
                    $contains = 0;
                    if ($parcel) {
                        if (($parcel->status_reason_id == 12 && $row['shipper_status_id'] == 1 && !is_null($row['estimation_charges'])) || (($row['shipper_status_id'] == 0) && is_null($row['estimation_charges']))) {
                            $contains = 1;
                        } else {
                            if (is_null($row['estimation_charges']))
                                $contains = 2;
                            if ((($row['shipper_status_id'] == 1) && is_null($row['estimation_charges']) && $parcel->status_reason_id != 12)) {
                                $contains = 1;
                            } else
                                $contains = 0;
                        }
                    }
                    if (!empty(trim($row['tracking_number']))) {
                        if (empty($tracking_ids)) {
                            $tracking_ids[] = $row['tracking_number'];
                            $tracking_id_row[$row['tracking_number']] = $row_id;
                        } else {
                            if (in_array($row['tracking_number'], $tracking_ids)) {
                                $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                            } else {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                        }
                    }
                    if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('shipper_status_id', [12, 52])->exists()) {
                        $errors['Row #' . $row_id][] = 'Shipment is not ready for confirmation pending #' . $row['tracking_number'];
                    }
                    if ($contains == 0 || $contains == 2)
                        if ($contains == 0) {
                            $errors['Row #' . $row_id][] = 'Shipment is not OSA #' . $row['tracking_number'];
                        } else if ($contains == 2) {
                            $errors['Row #' . $row_id][] = 'OSA Shipment required estimation charges #' . $row['tracking_number'];
                        }
                }
            }
            if (empty($errors)) {
                $tracking_numbers = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $tracking = trim($row['tracking_number']);
                    $status = trim($row['shipper_status_id']);
                    $remarks = trim($row['remarks']);
                    $estimation_charges = trim($row['estimation_charges']);

                    if (!empty($row['remarks'])) {
                        $remarks = trim($row['remarks']);
                    } else {
                        $remarks = NULL;
                    }
                    $shipment_details = Shipment::where('tracking_number', $tracking)->first();
                    $shipment_history = ShipmentsJourney::where('shipment_id', $shipment_details->id)->latest('id')->first();
                    if ($status == 0) {
                        if ($shipment_details->booking_type_id == 5) {
                            continue;
                        }
                        $shipment_details->shipper_status_id = 20; //Confirmation Pending
                        $shipment_details->consignee_status_id = 20; //Confirmation Pending
                        NotificationsController::send(15, 0, $shipment_details->id);
                        NotificationsController::send(16, 0, $shipment_details->id);

                        if ($shipment_details->shipment_type == 1) {
                            if ($shipment_details->booking_type_id != 4) {
                                ShipmentChargesController::return($shipment_details->id);

                                if ($shipment_details->packaging_material_request != 1) {

                                    AdminFinanceController::add_payment($shipment_details->id, 1);
                                }
                            } else {
                                ShipmentChargesController::walk_in_return($shipment_details->id);

                                $shipment_details->walk_in_status = 2;

                                AdminFinanceController::done_payment($shipment_details->id, 1);
                            }
                        }

                        ShipmentsJourneyController::add($shipment_details->id, 20, 20, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());
                    } else if ($status == 1) {
                        $journey = ShipmentsJourney::where('shipment_id', $shipment_details->id)->where('shipper_status_id', 12)->latest('id')->first();
                        if ($journey) {
                            if ($shipment_details->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                                $shipment_details->nsa_osa_status = 1;
                                $shipment_details->save();
                                ShipmentChargesController::nsa_osa_charges($shipment_details->id);

                                $check = $this->update_estimate_charges($shipment_details->id, $estimation_charges);

                                NotificationsController::send(33, $shipment_details->id);
                            } else if ($shipment_details->shipper_status_id == 52) {
                                $journey = ShipmentsJourney::where('shipment_id', $shipment_details->id)->where('shipper_status_id', 12)->latest('id')->first();

                                if ($journey && ($journey->status_reason_id == 12)) {
                                    $shipment_details->nsa_osa_status = 1;

                                    $shipment_details->save();

                                    ShipmentChargesController::nsa_osa_charges($shipment_details->id);
                                }
                            }
                        }
                        $shipment_details->shipper_status_id = 13; //Re-Attempt
                        $shipment_details->consignee_status_id = 13;
                        ShipmentsJourneyController::add($shipment_details->id, 13, 13, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());

                        $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_details->id)->latest()->first();
                        if ($return_assign_shipment) {
                            $return_assign_shipment->status = 0;
                            $return_assign_shipment->save();

                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 1;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                        }
                        NotificationsController::send(15, 0, $shipment_details->id);
                        NotificationsController::send(16, 0, $shipment_details->id);
                    }
                    $shipment_details->save();
                    $tracking_numbers['Row #' . $row_id] = $tracking;
                }
                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $row . ': ' . $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));

                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            } else {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);

                return redirect()->back()->withErrors($errors);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function return_marked_single_status(Request $request)
    {
        $remark = $request->remark;
        if ($request->action == 'confirm') {
            $return_reason = $request->single_return_reason_select;
            $single_consignee_refused_reasons = $request->single_consignee_refused_reasons;
            $consignee_refused_reasons = $request->consignee_refused_reasons;
            $parcel = Shipment::find($request->shipment_id);
            $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
            if (!$dispute_check) {
                return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
            }
            if ($parcel->booking_type_id == 5) {
                return ['status' => 0, 'error' => "Reverse Pickup Shipment can not be updated to Return Confirm!"];
            }
            if (!in_array($parcel->shipper_status_id, [13, 15, 20, 54, 55]) && ($parcel->shipper_status_id == 12 || $parcel->shipper_status_id == 52)) {

                Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);



                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);

                if ($parcel->shipment_type == 1) {
                    if ($parcel->booking_type_id != 4) {
                        ShipmentChargesController::return($request->shipment_id);

                        if ($parcel->packaging_material_request != 1) {
                            AdminFinanceController::add_payment($request->shipment_id, 1);
                        }
                    } else {
                        ShipmentChargesController::walk_in_return($request->shipment_id);

                        $parcel->walk_in_status = 2;

                        $parcel->save();

                        AdminFinanceController::done_payment($request->shipment_id, 1);
                    }
                }
                ShipmentsJourneyController::add($request->shipment_id, 20, 20, $return_reason, $remark, NULL, Auth::id(), null, null, 1, null, null, null, null, $consignee_refused_reasons);
                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id);
                if ($return_assign_shipment->exists()) {
                    $return_assign_shipment = $return_assign_shipment->latest()->first();
                    $return_assign_shipment->status = 0;
                    $return_assign_shipment->save();

                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    $return_assign_log->status = 2;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();
                }

                return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Return Confirm"];
            }
            return ['status' => 0, 'error' => "Shipment is in different status, Cannot mark it as Return - Confirm!"];
        } else if ($request->action == 'reattempt') {
            $parcel = Shipment::find($request->shipment_id);
            if ($request->has('charges')) {
                if ($request->charges != null) {
                    $check = $this->update_estimate_charges($request->shipment_id, $request->charges);
                    if ($check != 0) {
                        return ['status' => 0, 'error' => "Shipment not found on Estimation Charges"];
                    }
                } else {
                }
            }

            if (!in_array($parcel->shipper_status_id, [13, 20]) && ($parcel->shipper_status_id == 12 || $parcel->shipper_status_id == 52)) {
                $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->whereIn('shipper_status_id', [12, 52])->latest('id')->first();

                if ($journey) {
                    if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                        $parcel->nsa_osa_status = 1;

                        $parcel->save();

                        ShipmentChargesController::nsa_osa_charges($request->shipment_id);

                        NotificationsController::send(33, $request->shipment_id);
                    } else if ($parcel->shipper_status_id == 52) {
                        $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->latest('id')->first();

                        if ($journey && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($request->shipment_id);
                        }
                    }

                    $parcel->shipper_status_id = 13;
                    $parcel->consignee_status_id = 13;
                    $parcel->save();

                    ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remark, NULL, Auth::id());

                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                    if ($return_assign_shipment) {
                        $return_assign_shipment->status = 0;
                        $return_assign_shipment->save();

                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        $return_assign_log->status = 1;
                        $return_assign_log->assigned_by = Auth::id();
                        $return_assign_log->save();
                    }

                    NotificationsController::send(15, 0, $request->shipment_id);
                    NotificationsController::send(16, 0, $request->shipment_id);
                    $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                    $reattempt_remarks_col->shipment_id = $request->shipment_id;
                    $reattempt_remarks_col->remarks = 'Manual';
                    $reattempt_remarks_col->save();
                }

                return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Re-Attempt"];
            }
            return ['status' => 0, 'error' => "Shipment is in different status, Cannot mark it as Reattempted!"];
        } else {
            return ['status' => 0, 'error' => "Invalid action, Please refresh your page!"];
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Reattempt for bulk shipments
    public function return_reattempt_status(Request $request)
    {
        $shipment_ids = $request->shipment_ids;

        if ($request->action == 'reattempt') {
            foreach ($shipment_ids as $shipment) {
                $parcel = Shipment::find($shipment);
                if (!in_array($parcel->shipper_status_id, [13, 20])) {
                    $remark_inp = "remark.$shipment";
                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null) ? $request->remark[$parcel->id] : null;

                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();



                    if ($journey) {
                        if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($shipment);

                            NotificationsController::send(33, $shipment);
                        } else if ($parcel->shipper_status_id == 52) {
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
                    // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                    // if ($return_assign_shipment) {
                    //     $return_assign_shipment->status = 0;
                    //     $return_assign_shipment->save();

                    //     $return_assign_log = new ReturnAssignedShipmentLogs();
                    //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    //     $return_assign_log->status = 1;
                    //     $return_assign_log->assigned_by = Auth::id();
                    //     $return_assign_log->save();
                    // }
                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                    $reattempt_remarks_col->shipment_id = $shipment;
                    $reattempt_remarks_col->remarks = 'Manual';
                    $reattempt_remarks_col->save();
                }
            }
            return ['status' => 1, 'success' => "Shipment successfully updated as ( Re-Attempt )"];
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function on_hold_for_self_collection(Request $request)
    {
        $shipmentId = $request->shipment_id;
        $remark = $request->remark;
        if ($shipmentId) {
            if (Shipment::where('id', $shipmentId)->where('shipper_status_id', '!=', 15)->exists()) {
                $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipmentId);
                if ($consolidated_shipments->exists()) {
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $all_consolidation_shipments)->update(['shipper_status_id' => 15, 'consignee_status_id' => 15]);
                    foreach ($all_consolidation_shipments as $shipment) {
                        ShipmentsJourneyController::add($shipment, 15, 15, NULL, $remark, NULL, Auth::id());
                        $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                        if ($return_assign_shipment) {
                            $return_assign_shipment->status = 0;
                            $return_assign_shipment->save();

                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 7;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                        }
                    }
                } else {
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 15, 'consignee_status_id' => 15]);
                    ShipmentsJourneyController::add($request->shipment_id, 15, 15, NULL, $remark, NULL, Auth::id());
                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                    if ($return_assign_shipment) {
                        $return_assign_shipment->status = 0;
                        $return_assign_shipment->save();

                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        $return_assign_log->status = 7;
                        $return_assign_log->assigned_by = Auth::id();
                        $return_assign_log->save();
                    }
                }

                return ['status' => 0, 'success' => "Shipment status successfully updated to Shipment - On Hold for Self Collection"];
            } else {
                return response()->json(['status' => 1, 'error' => 'Shipment already updated to Shipment - On Hold for Self Collection!']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipment ID Not selected!']);
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: This function is in AdminInterceptRebookRequestHistoryController using to update intercept different Consignee/ Same Consignee
    public function intercept(Request $request)
    {
        $rules = [
            'replacement_parcel_image' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {
            $s_amount = str_replace(",", "", $request->amount);
            $amount = intval($s_amount);
            $shipment = Shipment::find($request->shipment_id);
            $user_id = $shipment->user_id;
            $intercept_type = $request->consignee;

            $shipment_status = $shipment->status_shipper->name;
            $crm = false;
            $crm_request = CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_type_id', 11);
            if ($crm_request->exists()) {
                $crm = true;
            }
            if ($shipment['shipper_status_id'] == 12 || $shipment['shipper_status_id'] == 52 || $crm == true) {
                if ($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $amount) {
                    if ($shipment['intercepted'] == 1) {

                        return redirect()->back()->with('error', 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']);
                    } else {
                        $shipment = Shipment::find($request->shipment_id);
                        $s_amount = str_replace(",", "", "$request->amount");
                        $amount = (int)$s_amount;

                        //Different Consignee
                        if ($request->intercept_type == 1) {
                            InterceptReBookRequest::create([
                                'shipment_id' => $request->shipment_id,
                                'consignee_city_id' => $request->consignee_city,
                                'consignee_name' => $request->consignee_name,
                                'consignee_address' => $request->consignee_address,
                                'consignee_phone_number_1' => $request->consignee_phone_number_1,
                                'consignee_phone_number_2' => $request->consignee_phone_number_2,
                                'consignee_email' => $request->consignee_email,
                                'amount' => $amount,
                                'shipper_id' => $user_id,
                                'status' => 0,
                                'intercept_type' => $intercept_type,
                                'admin_id' => Auth::id()
                            ]);
                            $shipment->consignee_status_id = 54;
                            $shipment->shipper_status_id = 54;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, Auth::id());
                        }

                        //Same Consignee
                        else {
                            InterceptReBookRequestHistory::create([
                                'shipment_id' => $request->shipment_id,
                                'old_consignee_city_id' => $shipment->consignee_city_id,
                                'new_consignee_city_id' => $request->consignee_city,
                                'old_consignee_name' => $shipment->consignee_name,
                                'new_consignee_name' => $request->consignee_name,
                                'old_consignee_address' => $shipment->consignee_address,
                                'new_consignee_address' => $request->consignee_address,
                                'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'new_consignee_phone_number_1' => $request->consignee_phone_number_1,
                                'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'new_consignee_phone_number_2' => $request->consignee_phone_number_2,
                                'old_consignee_email' => $shipment->consignee_email,
                                'new_consignee_email' => $request->consignee_email,
                                'old_amount' => $shipment->amount,
                                'new_amount' => $amount,
                                'shipper_id' => $user_id,
                                'intercept_type' => $intercept_type,
                            ]);
                            $shipment->consignee_status_id = 55;
                            $shipment->shipper_status_id = 55;
                            $shipment->consignee_address = $request->consignee_address;
                            $shipment->consignee_phone_number_1 = $request->consignee_phone_number_1;
                            $shipment->consignee_phone_number_2 = $request->consignee_phone_number_2;

                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 55, 55, NULL, NULL, $user_id, Auth::id());

                            if ($request->hasFile('replacement_parcel_image')) {
                                $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $request->shipment_id);
                                if ($shipment_parcel_image->exists()) {
                                    $shipment_parcel_image = $shipment_parcel_image->first();
                                    Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                                } else {
                                    $shipment_parcel_image = new ShipmentReplacementParcelImage();
                                    $shipment_parcel_image->shipment_id = $request->shipment_id;
                                }
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'replacement_parcel/' . $request->shipment_id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_image));
                                $shipment_parcel_image->picture_path = $picture_path;
                                $shipment_parcel_image->save();
                            }
                        }
                        return redirect()->back()->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
                    }
                } else {
                    return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
                }
            } else {
                return redirect()->back()->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
            }
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Used in Delivery Controller Approving Intercept Request
    public function approve(Request $request)
    {
        $shipment_ids = $request->ids;
        $print = array();
        if (!empty($shipment_ids)) {
            $valid = FALSE;

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && $shipment->shipper_status_id == 54) {
                    $valid = TRUE;

                    $intercept = InterceptReBookRequest::where('shipment_id', $shipment_id)->first();
                    $previous_consignee_city_id = $shipment->consignee_city_id;
                    $new_consignee_city_id = $intercept->consignee_city_id;


                    // if($shipment->self_collection == 1){

                    // }

                    InterceptReBookRequestHistory::create([
                        'shipment_id' => $shipment->id,
                        'old_consignee_city_id' => $shipment->consignee_city_id,
                        'new_consignee_city_id' => $intercept->consignee_city_id,
                        'old_consignee_name' => $shipment->consignee_name,
                        'new_consignee_name' => $intercept->consignee_name,
                        'old_consignee_address' => $shipment->consignee_address,
                        'new_consignee_address' => $intercept->consignee_address,
                        'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                        'new_consignee_phone_number_1' => $intercept->consignee_phone_number_1,
                        'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                        'new_consignee_phone_number_2' => $intercept->consignee_phone_number_2,
                        'old_consignee_email' => $shipment->consignee_email,
                        'new_consignee_email' => $intercept->consignee_email,
                        'old_amount' => $shipment->amount,
                        'new_amount' => $intercept->amount,
                        'shipper_id' => $intercept->shipper_id,
                        'intercept_type' => $intercept->intercept_type
                    ]);

                    $shipment->consignee_city_id = $intercept['consignee_city_id'];
                    $shipment->consignee_name = $intercept['consignee_name'];
                    $shipment->consignee_address = $intercept['consignee_address'];
                    $shipment->consignee_phone_number_1 = $intercept['consignee_phone_number_1'];
                    $shipment->consignee_phone_number_2 = $intercept['consignee_phone_number_2'];
                    $shipment->consignee_email = $intercept['consignee_email'];
                    $shipment->amount = $intercept['amount'];
                    $shipment->shipper_status_id = 55;
                    $shipment->consignee_status_id = 55;

                    $shipment->save();


                    InterceptReBookRequest::where('shipment_id', $shipment_id)->update([
                        'status' => 1,
                        'updated_by' => Auth::id(),
                        'updated_by_date' => Carbon::now()
                    ]);

                    ShipmentChargesController::cash_handling($shipment_id);
                    ShipmentChargesController::weight($shipment_id);
                    ShipmentChargesController::fuel_surcharge($shipment_id);
                    ShipmentChargesController::intercept($shipment_id, $previous_consignee_city_id, $new_consignee_city_id);

                    ShipmentsJourneyController::add($shipment_id, 55, 55, NULL, NULL, NULL, Auth::id());


                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_id)->latest()->first();
                    if ($return_assign_shipment) {
                        $return_assign_shipment->status = 0;
                        $return_assign_shipment->save();

                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        $return_assign_log->status = 3;
                        $return_assign_log->assigned_by = Auth::id();
                        $return_assign_log->save();
                    }
                    $print[] = $shipment_id;
                }
            }

            if ($valid) {
                $text = 'Shipment(s) has been marked as Intercept Approved';
                return ['status' => 0, 'success' => $text, 'print' => $print];
            } else {
                return ['status' => 1, 'error' => 'No Valid Shipment(s) were Selected'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Used in Delivery Controller Rejecting Intercept Request
    public function reject(Request $request)
    {
        $shipment_ids = $request->ids;
        $remarks = $request->remarks;

        if (!empty($shipment_ids) && !empty($remarks)) {
            $valid = FALSE;

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && $shipment->shipper_status_id == 54) {
                    $valid = TRUE;

                    NotificationsController::send(15, 0, $shipment_id);
                    NotificationsController::send(16, 0, $shipment_id);

                    if ($shipment->booking_type_id != 4) {
                        ShipmentChargesController::return($shipment_id);

                        AdminFinanceController::add_payment($shipment_id, 1);
                    } else {
                        ShipmentChargesController::walk_in_return($shipment_id);

                        $shipment->walk_in_status = 2;

                        $shipment->save();

                        AdminFinanceController::done_payment($shipment_id, 1);
                    }


                    $shipment->shipper_status_id = 20;
                    $shipment->consignee_status_id = 20;

                    $shipment->save();

                    $status_reason_id = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->orderBy('id', 'desc')->pluck('status_reason_id')->first();

                    $new_intercept_request = InterceptReBookRequest::where('shipment_id', $shipment_id)->update([
                        'status' => 2,
                        'updated_by' => Auth::id(),
                        'updated_by_date' => Carbon::now()
                    ]);

                    ShipmentsJourneyController::add($shipment_id, 20, 20, $status_reason_id, $remarks, NULL, Auth::id());
                }
            }

            if ($valid) {
                return ['status' => 0, 'success' => 'Shipment(s) has been marked as Return Confirm'];
            } else {
                return ['status' => 1, 'error' => 'No Valid Shipment(s) were Selected'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    static public function auto_reattempt_status_for_max_delivery_ratio($shipment_id)
    {
        $global_admin = 346;

        $shipment = Shipment::find($shipment_id);
        if ($shipment) {
            $reattempt_percentage = ReattemptPercentageForShipper::where('user_id', $shipment->user_id);
            if ($reattempt_percentage->exists()) {
                $reattempt_percentage = $reattempt_percentage->first();
                $percentage = $reattempt_percentage->percentage;

                $check_switch = GlobalSettings::where('type', 'reattempt_flag');
                if ($check_switch->exists()) {
                    $check_switch = $check_switch->first();
                    if ($check_switch->setting_value == 1) {
                        $check_count = GlobalSettings::where('type', 'reattempt_count');
                        $count_limit = 2;
                        if ($check_count->exists()) {
                            $check_count = $check_count->first();
                            $count_limit = $check_count->setting_value;
                        }
                        if ($count_limit > $reattempt_percentage->count) {
                            $settings = GlobalSettings::where('type', 'reattempt_percentage');
                            $percentage_limit = 60;

                            if ($settings->exists()) {
                                $settings = $settings->first();
                                $percentage_limit = $settings->setting_value;
                            }
                            if ($percentage < $percentage_limit) {
                                return true;
                            }
                            $journey = ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 12)->latest('id')->first();
                            if ($journey) {
                                if (in_array($journey->status_reason_id, [12, 34, 40, 42, 50, 67, 69, 75])) {
                                    return true;
                                }
                            }

                            $shipment->shipper_status_id = 13;
                            $shipment->consignee_status_id = 13;
                            $shipment->save();

                            ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, 'Auto re-attempt status due to better Delivery Ratio', NULL, $global_admin);

                            $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_id)->latest()->first();
                            if ($return_assign_shipment) {
                                $return_assign_shipment->status = 0;
                                $return_assign_shipment->save();

                                $return_assign_log = new ReturnAssignedShipmentLogs();
                                $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                $return_assign_log->status = 1;
                                $return_assign_log->assigned_by = $return_assign_shipment->assigned_by;
                                $return_assign_log->save();
                            }

                            NotificationsController::send(15, 0, $shipment_id);
                            NotificationsController::send(16, 0, $shipment_id);
                            $reattempt_percentage->count += 1;
                            $reattempt_percentage->save();

                            $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                            $reattempt_remarks_col->shipment_id = $shipment_id;
                            $reattempt_remarks_col->remarks = 'Automatic';
                            $reattempt_remarks_col->save();
                        }
                    }
                } else {
                    $check_count = GlobalSettings::where('type', 'reattempt_count');
                    $count_limit = 2;
                    if ($check_count->exists()) {
                        $check_count = $check_count->first();
                        $count_limit = $check_count->setting_value;
                    }
                    if ($count_limit > $reattempt_percentage->count) {
                        $settings = GlobalSettings::where('type', 'reattempt_percentage');
                        $percentage_limit = 60;
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            $percentage_limit = $settings->setting_value;
                        }
                        if ($percentage < $percentage_limit) {
                            return true;
                        }
                        $journey = ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 12)->latest('id')->first();
                        if ($journey) {
                            if (in_array($journey->status_reason_id, [12, 34, 40, 42, 50, 67, 69, 75])) {
                                return true;
                            }
                        }


                        $shipment->shipper_status_id = 13;
                        $shipment->consignee_status_id = 13;
                        $shipment->save();


                        ShipmentsJourneyController::add($shipment_id, 13, 13, NULL, 'Auto re-attempt status due to better Delivery Ratio', NULL, $global_admin);

                        // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_id)->latest()->first();
                        // if($return_assign_shipment){
                        //     $return_assign_shipment->status = 0;
                        //     $return_assign_shipment->save();

                        //     $return_assign_log = new ReturnAssignedShipmentLogs();
                        //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        //     $return_assign_log->status = 1;
                        //     $return_assign_log->assigned_by = $return_assign_shipment->assigned_by;
                        //     $return_assign_log->save();
                        // }

                        NotificationsController::send(15, 0, $shipment_id);
                        NotificationsController::send(16, 0, $shipment_id);

                        $reattempt_percentage->count += 1;
                        $reattempt_percentage->save();
                        $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                        $reattempt_remarks_col->shipment_id = $shipment_id;
                        $reattempt_remarks_col->remarks = 'Automatic';
                        $reattempt_remarks_col->save();
                    }
                }
            }
        }
    }


     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: RetailReturnController's Function for reattempt 
    public function mark_reattempt(Request $request)
    {
        $parcel = Shipment::find($request->shipment_id);
        if ($parcel) {
            if ($parcel->shipper_status_id != 52) {
                if ($parcel->shipper_status_id == 12) {
                    $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 52, 'consignee_status_id' => 52]);

                    if (session('user_type') != 1) {
                        $reference_1_id = Auth::id();
                    } else {
                        $reference_1_id = null;
                    }
                    $last_reason = ShipmentsJourney::where('shipment_id', $parcel->id)->orderBy('id', 'DESC');
                    if ($last_reason->exists()) {
                        $last_reason = $last_reason->first();
                        $last_reason_id = $last_reason->status_reason_id;
                    } else {
                        $last_reason_id = NULL;
                    }
                    ShipmentsJourneyController::add($request->shipment_id, 52, 52, $last_reason_id, $request->remark, session('user_id'), NULL, $reference_1_id);

                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                    if ($return_assign_shipment) {
                        $return_assign_shipment->status = 0;
                        $return_assign_shipment->save();

                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        $return_assign_log->status = 5;
                        $return_assign_log->assigned_by = Auth::id();
                        $return_assign_log->save();
                    }

                    if ($journey) {
                        NotificationsController::send(33, $request->shipment_id);
                    }

                    return response()->json(['status' => 1, 'success' => "Shipment has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
                } else {
                    return ['status' => 0, 'error' => "Shipment is already updated for Re-attempt!"];
                }
            }
            return ['status' => 0, 'error' => "Shipment is already updated, Please check tracking!"];
        }
        return ['status' => 0, 'error' => "Something went wrong, try again later!"];
    }


    //Shipper Controller Functions

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function shipper_return_marked_single_status(Request $request)
    {
        $parcel = Shipment::find($request->shipment_id);
        if ($parcel) {
            if ($parcel->shipper_status_id == 12) {

                Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                $shipment_history = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();

                ShipmentChargesController::return($request->shipment_id);
                AdminFinanceController::add_payment($request->shipment_id, 1);
                ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $request->remark, session('user_id'), NULL);

                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id);
                if ($return_assign_shipment->exists()) {
                    $return_assign_shipment = $return_assign_shipment->latest()->first();
                    $return_assign_shipment->status = 0;
                    $return_assign_shipment->save();

                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    $return_assign_log->status = 2;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();
                }
                return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Return Confirm"];
            }
            return ['status' => 0, 'error' => "Something went wrong, try again later!"];
        }
        return ['status' => 0, 'error' => "Something went wrong, try again later!"];
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    public function return_reattempt_single_status(Request $request)
    {
        $parcel = Shipment::find($request->shipment_id);
        if ($parcel) {
            if ($parcel->shipper_status_id != 52) {
                if ($parcel->shipper_status_id == 12) {
                    $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 52, 'consignee_status_id' => 52]);

                    if (session('user_type') != 1) {
                        $reference_1_id = Auth::id();
                    } else {
                        $reference_1_id = null;
                    }
                    $last_reason = ShipmentsJourney::where('shipment_id', $parcel->id)->orderBy('id', 'DESC');
                    if ($last_reason->exists()) {
                        $last_reason = $last_reason->first();
                        $last_reason_id = $last_reason->status_reason_id;
                    } else {
                        $last_reason_id = NULL;
                    }
                    ShipmentsJourneyController::add($request->shipment_id, 52, 52, $last_reason_id, $request->remark, session('user_id'), NULL, $reference_1_id);

                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                    if ($return_assign_shipment) {
                        $return_assign_shipment->status = 0;
                        $return_assign_shipment->save();

                        $return_assign_log = new ReturnAssignedShipmentLogs();
                        $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        $return_assign_log->status = 5;
                        $return_assign_log->assigned_by = Auth::id();
                        $return_assign_log->save();
                    }

                    if ($journey) {
                        NotificationsController::send(33, $request->shipment_id);
                    }

                    return response()->json(['status' => 1, 'success' => "Shipment has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
                } else {
                    return ['status' => 0, 'error' => "Shipment is already updated for Re-attempt!"];
                }
            }
            return ['status' => 0, 'error' => "Shipment is already updated, Please check tracking!"];
        }
        return ['status' => 0, 'error' => "Something went wrong, try again later!"];
    }

     // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: ShipperInterceptRebookcontroller
    public function shipper_intercept_re_book_update(Request $request)
    {
        $rules = [
            'replacement_parcel_image' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {
            $s_amount = str_replace(",", "", $request->amount);
            $amount = intval($s_amount);
            $shipment = Shipment::find($request->shipment_id);
            $user_id = session('user_id');
            $intercept_type = $request->consignee;

            $shipment_status = $shipment->status_shipper->name;

            if ($shipment['shipper_status_id'] == 12) {
                if ($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $amount) {
                    if ($shipment['intercepted'] == 1) {
                        return redirect()->back()->with('error', 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']);
                    } else {
                        $s_amount = str_replace(",", "", "$request->amount");
                        $amount = (int)$s_amount;
                        if ($intercept_type == 1) {
                            InterceptReBookRequest::create([
                                'shipment_id' => $request->shipment_id,
                                'consignee_city_id' => $request->consignee_city,
                                'consignee_name' => $request->consignee_name,
                                'consignee_address' => $request->consignee_address,
                                'consignee_phone_number_1' => $request->consignee_phone_number_1,
                                'consignee_phone_number_2' => $request->consignee_phone_number_2,
                                'consignee_email' => $request->consignee_email,
                                'amount' => $amount,
                                'shipper_id' => $user_id,
                                'status' => 0,
                                'intercept_type' => $intercept_type,
                                'admin_id' => Auth::id()
                            ]);
                            $shipment->consignee_status_id = 54;
                            $shipment->shipper_status_id = 54;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, NULL);
                        } else {

                            InterceptReBookRequestHistory::create([
                                'shipment_id' => $request->shipment_id,
                                'old_consignee_city_id' => $shipment->consignee_city_id,
                                'new_consignee_city_id' => $request->consignee_city,
                                'old_consignee_name' => $shipment->consignee_name,
                                'new_consignee_name' => $request->consignee_name,
                                'old_consignee_address' => $shipment->consignee_address,
                                'new_consignee_address' => $request->consignee_address,
                                'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                                'new_consignee_phone_number_1' => $request->consignee_phone_number_1,
                                'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                                'new_consignee_phone_number_2' => $request->consignee_phone_number_2,
                                'old_consignee_email' => $shipment->consignee_email,
                                'new_consignee_email' => $request->consignee_email,
                                'old_amount' => $shipment->amount,
                                'new_amount' => $amount,
                                'shipper_id' => $user_id,
                            ]);
                            $shipment->consignee_status_id = 55;
                            $shipment->shipper_status_id = 55;
                            $shipment->intercepted = 1;
                            $shipment->save();

                            ShipmentsJourneyController::add($request->shipment_id, 55, 55, NULL, NULL, $user_id, NULL);

                            if ($request->hasFile('replacement_parcel_image')) {
                                $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $request->shipment_id);
                                if ($shipment_parcel_image->exists()) {
                                    $shipment_parcel_image = $shipment_parcel_image->first();
                                    Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                                } else {
                                    $shipment_parcel_image = new ShipmentReplacementParcelImage();
                                    $shipment_parcel_image->shipment_id = $request->shipment_id;
                                }
                                $time = Carbon::now()->toDateString();
                                $picture_path = 'replacement_parcel/' . $request->shipment_id . '_' . $time . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_image));
                                $shipment_parcel_image->picture_path = $picture_path;
                                $shipment_parcel_image->save();
                            }
                        }
                        return redirect()->route('cod.return.pending.index')->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
                    }
                } else {
                    return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
                }
            } else {
                return redirect()->route('cod.return.pending.index')->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
            }
        }
    }



    //     public function update_rv_assigned_shipment_status($rv_assigned_shipment, $status, $column_name){

    //     //Assuring if agent is updating the status update rows in rcp_assigned_agent
    //     $rv_assigned_shipment = $rv_assigned_shipment ->latest()->first();
    //     if($rv_assigned_shipment->admin_id == Auth::id()){

    //         $rv_assigned_shipment->shipment_status = $status;
    //         $rv_assigned_shipment->admin_id = Auth::id();
    //         $rv_assigned_shipment->save();


    //         //updating return row of agent 
    //         $rcp_assigned_agent = RcpAssignedAgent::where('id',$rv_assigned_shipment->rcp_assigned_agent_id)->first();
    //         $rcp_assigned_agent->increment($column_name);
    //         $rcp_assigned_agent->decrement('pending_shipments');
    //         $rcp_assigned_agent->increment('actual_productivity');
    //         $rcp_assigned_agent->admin_id = Auth::id();
    //         $rcp_assigned_agent->save();

    //         //creating log 
    //         $this->makeRvShipmentAssignAgentDetails($rv_assigned_shipment, $request);
    //         }

    //     //If admin is updating the status update rcp_assigned_shipment & log
    //         else{
    //         $rv_assigned_shipment = $rv_assigned_shipment ->latest()->first();
    //         $rv_assigned_shipment->shipment_status = $status;
    //         $rv_assigned_shipment->admin_id = Auth::id();
    //         $rv_assigned_shipment->save();

    //         //updating already_updated & pending of agent if shipment is updated by admin 
    //         $rcp_assigned_agent = RcpAssignedAgent::where('id',$rv_assigned_shipment->rcp_assigned_agent_id)->first();
    //         $rcp_assigned_agent->increment('already_updated');
    //         $rcp_assigned_agent->decrement('pending_shipments');
    //         $rcp_assigned_agent->save();


    //         $return_assign_log = new RcpAssignedShipmentLog();
    //         $return_assign_log->rcp_assigned_shipment_id = $rv_assigned_shipment->id;
    //         $return_assign_log->shipment_id = $rv_assigned_shipment->shipment_id;
    //         $return_assign_log->status = $status;
    //         $return_assign_log->admin_id = Auth::id();
    //         $return_assign_log->save();
    //         }
    // }

}
