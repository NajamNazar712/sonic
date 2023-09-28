<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use App\RvShipmentAgent;
use App\RvAgentCallHistory;
use Illuminate\Http\Request;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use Illuminate\Support\Facades\DB;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\Admin\AdminRole;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Admin\StatusRemark;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Models\Admin\OsaChargesLog;
use App\Http\Models\RvAssignAgentStatus;
use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\ReattemptPercentageForShipper;
use App\Http\Models\Admin\ReattemptShipmentStatusRemarks;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\AdminInterceptRebookRequestHistoryController;
use App\Http\Models\ShipmentStatusReason;
use App\RvAssignAgentSubStatus;

trait RvTrait
{

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function getShipmentConsigneeCities($shipment_id)
    {
        if ($shipment_id) {
            $shipment = Shipment::where('id', $shipment_id)->first();
            if ($shipment) {
                if ($shipment->shipping_mode_id == 2) {
                    $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                    $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                        ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                        ->select('c.id as id', 'c.name as name')
                        ->where('shipments.id', $shipment_id)
                        ->where('cd.shipping_mode_id', 2)
                        ->where('c.status', 1)
                        ->whereNotNull('c.zone_id')
                        ->whereNotIn('c.id', $restricted_cities);
                } else {
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
    protected function rv_shipment_assign($data)
    {
        //if the same shipment has been already completed, new row will be created
        $completed_shipments = RvShipmentAssignAgent::where('shipment_id', $data['shipment_id'])->where('rv_state_id', 4)->latest()->first();
        if ($completed_shipments) {
            $new_shipment = new RvShipmentAssignAgent();
            $new_shipment->agent_id = $data['agent_id'];
            $new_shipment->shipment_id = $data['shipment_id'];
            $new_shipment->rv_state_id =  1;
            $new_shipment->rv_assign_agent_status_id =  null;
            $new_shipment->rv_assign_agent_sub_status_id = null;
            $new_shipment->shipments_journey_id = $data['shipments_journey_id'];
            $new_shipment->last_shipments_journey_id = $data['shipments_journey_id'];
            $new_shipment->save();
        } else {

            //else if shipment_id is equal to $data['shipment_id'] update the row or insert new row
            RvShipmentAssignAgent::updateOrInsert(
                ['shipment_id' => $data['shipment_id']],
                [ 
                    'agent_id' => $data['agent_id'],
                    'rv_state_id' => $data['rv_state_id'],
                    'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'],
                    'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'],
                    'shipments_journey_id' => $data['shipments_journey_id'],
                    'last_shipments_journey_id' => $data['shipments_journey_id'],
                    'created_at' => Carbon::now(),
                ]
            );

        }


        $rv_shipment_assign_agent_id = RvShipmentAssignAgent::max('id');

        RvShipmentAssignAgentDetails::create([
            'rv_shipment_assign_agent_id' => $rv_shipment_assign_agent_id,
            'shipment_id' => $data['shipment_id'],
            'shipments_journey_id' => $data['shipments_journey_id'],
            'agent_id' => $data['agent_id'],
            'rv_state_id' => $data['rv_state_id'] ?? 1,
            'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'] ?? null,
            'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'] ?? null,
            'last_shipments_journey_id' => $data['shipments_journey_id'] ?? null,
        ]);

        return true;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: Unassigning shipment from agent 
    protected function rv_unassign_agents($request, $shipment_id)
    {
        //if admin is un assigning shipment from un assign button 
        if (isset($request) && $request->action == 'un-assign') {
            $shipment_ids = $request->shipment_ids;
            if (count($shipment_ids)) {
                foreach ($shipment_ids as $shipment) {
                    $rv_unassign_agent = RvShipmentAssignAgent::where('shipment_id', $shipment)->where('rv_assign_agent_status_id', null)->where('rv_state_id', 1);
                    if ($rv_unassign_agent->exists()) {
                        
                        $rv_unassign_agent = $rv_unassign_agent->latest()->first();
                        $rv_unassign_agent->rv_state_id = 3; // we are setting status to open because in future any agent can get the shipment automatically from Virtual RCP Agent Screen
                        $rv_unassign_agent->updated_by_id = Auth::id();
                        $rv_unassign_agent->save();

                        //new row in RvShipmentAssignAgentDetails table
                        $rv_unassign_agent = RvShipmentAssignAgent::find(RvShipmentAssignAgent::max('id'));
                        $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment)->latest()->first();
                        $this->rv_shipment_assign_agent_details($rv_unassign_agent, $rv_unassign_agent, $shipments_journey);
                    }
                }
                return true;
            } else {
                return false;
            }
        } 
        
        //if admin is un assigning shipment from excel sheet 
        else {
            $rv_unassign_agent = RvShipmentAssignAgent::where('shipment_id', $shipment_id)->where('rv_assign_agent_status_id', null)->where('rv_state_id', 1);
            if ($rv_unassign_agent->exists()) 
            {
                $rv_unassign_agent = $rv_unassign_agent->latest()->first();
                $rv_unassign_agent->rv_state_id = 2;
                $rv_unassign_agent->updated_by_id = Auth::id();
                $rv_unassign_agent->save();
                
                //new row in RvShipmentAssignAgentDetails table
                $rv_unassign_agent = RvShipmentAssignAgent::find(RvShipmentAssignAgent::max('id'));
                $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment_id)->latest()->first();
                $this->rv_shipment_assign_agent_details($rv_unassign_agent, $rv_unassign_agent, $shipments_journey);

                return true;
            } 
            else {
                return false;
            }
        }
    }


    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey)
    {
        $rv_shipment_assign_agent_details  = new RvShipmentAssignAgentDetails();
        $rv_shipment_assign_agent_details->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
        $rv_shipment_assign_agent_details->agent_id = Auth::id();
        $rv_shipment_assign_agent_details->shipments_journey_id = $shipments_journey->id;
        $rv_shipment_assign_agent_details->last_shipments_journey_id = $shipments_journey->id;
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
    private function shipment_assign_agent_table_columns($request, $agent)
    {
        return [
            'rv_assign_agent_status_id' => $request->rv_assign_agent_status_id,
            'rv_assign_agent_sub_status_id' => $request->rv_assign_agent_sub_status_id,
            'rv_state_id' => 4,
            'rv_fake_status_id' => $request->rv_fake_status_id,
            'remarks' => $request->remarks,
            'is_fake_status' => $request->is_fake_status,
            'call_to_id' => $request->call_to_id,
            'updated_by_id' => Auth::id(),
            'rv_shipment_agent_id' => $agent->id,
        ];
    }

    protected function update_unresponsive_shipments_status($request, $shipment_assign_agent, $assigned_agent)
    {
        $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->latest()->first();

        $shipment_assign_agent_table_columns = $this->shipment_assign_agent_table_columns($request, $assigned_agent);
        if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count < 2) {
            $shipment_assign_agent_table_columns['rv_state_id'] = 2; //unassign shipment
            $shipment_assign_agent_table_columns['unresponsive_attempt_time'] = Carbon::now();

        } 
        else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count == 2) {
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 7; //set status to Shipper Advise Requested 
            $shipment_assign_agent_table_columns['rv_state_id'] = 2; //unassign shipment
            $shipment_assign_agent_table_columns['unresponsive_attempt_time'] = Carbon::now();

        } 
        else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count == 3) {
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 3; //set status to return confirm
            $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = 4; //set status as shipment completed
            $shipment_assign_agent_table_columns['unresponsive_attempt_time'] = Carbon::now();
        }
        return $shipment_assign_agent_table_columns;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: this function is updating table rows of rv_shipment_assign_agents
    protected function update_shipment_assign_agent($request, $assigned_agent, $admin_agent, $shipment_assign_agent)
    {
        $shipment_assign_agent_table_columns = $this->update_unresponsive_shipments_status($request, $shipment_assign_agent, $assigned_agent);

        if ($admin_agent->employee->staff_category_id == 3) {
            $assigned_agent->increment('total_shipments');
            $assigned_agent->increment('actual_productivity');
            $shipment_assign_agent_table_columns['updated_type_id'] = 2; // agent type
        } else {
            $assigned_agent->increment('already_updated');
            $shipment_assign_agent_table_columns['updated_type_id'] = 1; // admin type
        }

        $shipment_assign_agent->update($shipment_assign_agent_table_columns);

        return true;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: this function is new row of rv_shipment_assign_agents
    protected function add_shipment_agent($request, $shipment_assign_agent)
    {
        //updating columns in shipmen assign agent table 

        $add_agent = new RvShipmentAgent();
        $add_agent->agent_id = $shipment_assign_agent->agent_id;
        $add_agent->total_shipments  = $add_agent->total_shipments + 1;
        $add_agent->actual_productivity  = $add_agent->actual_productivity + 1;
        $add_agent->save();

        $shipment_assign_agent_table_columns = $this->shipment_assign_agent_table_columns($request, $add_agent);

        $shipment_assign_agent_table_columns['updated_type_id'] = 2; // agent type
        $shipment_assign_agent->update($shipment_assign_agent_table_columns);

        return true;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function update_shipment_status($request)
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

        if (in_array($parcel->shipper_status_id, [7, 8, 9, 12, 15, 52, 65])) {
            $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->whereIn('shipper_status_id', [7, 8, 9, 12, 15, 52, 65])->latest('id')->first();

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

    // Siderbar: N/A
    // URL: 
    // Description:
    protected function return_confirm($request)
    {
        $remarks = (isset($request['remarks']) && $request['remarks'] !== null) ? $request['remarks'] : null;
        $parcel = Shipment::find($request->shipment_id);
        $rv_sub_status = RvAssignAgentSubStatus::where('id', $request->rv_assign_agent_sub_status_id)->value('name');
        $shipment_status_reason = ShipmentStatusReason::where('name', 'like', '%' . $rv_sub_status . '%')->first()->id;
        
        //these both could be null 
        $consignee_refused_reasons = $request->consignee_refused_reasons;
        //
        
        $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
        if (!$dispute_check) {
            return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
        }
        if ($parcel->booking_type_id == 5) {
            return ['status' => 0, 'error' => "Reverse Pickup Shipment can not be updated to Return Confirm!"];
        }

        // if current shipment statuses are following update the shipment status in shipments table
        // 7 = Shipment - Not Attempted
        // 8 = Shipment - Delivery Unsuccessful
        // 9 = Shipment - On Hold
        // 12 = Shipment - Reason Validation Required
        // 15 = Shipment - On Hold for Self Collection
        // 52 = Shipment - Re-Attempt Requested
        // 65 = Shipment - Shipper Advise Requested 

        if (in_array($parcel->shipper_status_id, [7, 8, 9, 12, 15, 52, 65])) {

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
            ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_status_reason, $remarks, NULL, Auth::id(), null, null, 1, null, null, null, null, $consignee_refused_reasons);

            return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Return Confirm"];
        }
        return ['status' => 0, 'error' => "Shipment is in different status, Cannot mark it as Return - Confirm!"];
    }


    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function on_hold_for_self_collection(Request $request)
    {
        $shipmentId = $request->shipment_id;
        $remark = $request->remarks;
        if ($shipmentId) {
            if (Shipment::where('id', $shipmentId)->where('shipper_status_id', '!=', 15)->exists()) {
                $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipmentId);
                if ($consolidated_shipments->exists()) {
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $all_consolidation_shipments)->update(['shipper_status_id' => 15, 'consignee_status_id' => 15]);
                    foreach ($all_consolidation_shipments as $shipment) {
                        ShipmentsJourneyController::add($shipment, 15, 15, NULL, $remark, NULL, Auth::id());
                    }
                } else {
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 15, 'consignee_status_id' => 15]);
                    ShipmentsJourneyController::add($request->shipment_id, 15, 15, NULL, $remark, NULL, Auth::id());
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
    protected function intercept(Request $request)
    {
        $rules = [
            'replacement_parcel_image' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {

            DB::beginTransaction();
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

            // if ($shipment['shipper_status_id'] == 12 || $shipment['shipper_status_id'] == 52 || $crm == true) {
            if (in_array($shipment->shipper_status_id, [7, 8, 9, 12, 15, 52, 65]) || $crm == true) {
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

                        DB::commit();
                        return redirect()->back()->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
                    }
                } else {
                    DB::rollBack();
                    $request->request->set('rv_assign_agent_status_id', null); //passing rv_assign_agent_status_id as 'null' instead of '3' when error occurs in intercept 
                    return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
                }
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
            }
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: 
    protected function unresponsive(Request $request)
    {
        $shipment = Shipment::find($request->shipment_id);
        $user_id = $shipment->user_id;
        $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->first();

        $status = new RvAgentCallHistory();
        $status->shipment_id= $request->shipment_id;
        $status->rv_shipment_assign_agent_id = $rv_shipment_assign_agent->id;
        $status->call_finding_id = $request->rv_assign_agent_sub_status_id; //call finding reasons
        $status->call_to_id = $request->call_to_id; //Shipper or Consignee
        $status->remarks = $rv_shipment_assign_agent->remarks;
        $status->save();

        $rv_shipment_assign_agent->increment('unresponsive_count');
        $rv_shipment_assign_agent->unresponsive_attempt_time = Carbon::now();

        //if unresponsive count is 3 unassigned the shipment & set the assign_agent_status_id to 7, the shipment will be shown to to the shipper 
        if ($rv_shipment_assign_agent->unresponsive_count == 2) {
            //updating the shipment status to unresponsive(65) in shipments table
            Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 65, 'consignee_status_id' => 65]);

            //updating the shipment status to unresponsive(65) in shipments journey table
            ShipmentsJourneyController::add($request->shipment_id, 65, 65, 12, NULL, $user_id, Auth::id());
            return response()->json(['status' => 1]); 

        }

        //if unresponsive count 4 & rv_state_id is 3 (Open) then shipment status will be auto return confirm
        else if ($rv_shipment_assign_agent->unresponsive_count == 3) {
            Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
            ShipmentsJourneyController::add($request->shipment_id, 20, 20, NULL, NULL, $user_id, Auth::id());
        }
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

                        // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_details->id)->latest()->first();
                        // if ($return_assign_shipment) {
                        //     $return_assign_shipment->status = 0;
                        //     $return_assign_shipment->save();

                        //     $return_assign_log = new ReturnAssignedShipmentLogs();
                        //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        //     $return_assign_log->status = 1;
                        //     $return_assign_log->assigned_by = Auth::id();
                        //     $return_assign_log->save();
                        // }
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
    // Description: this function is updating table rows of rv_shipment_assign_agents from shipper side
    protected function shipment_status_update_shipper($request, $rv_assign_agent_status_id, $get_rv_state_id, $updated_rv_state_id)
    {
        $shipper = User::where('id', Auth::id())->first();
        if ($shipper) {

            $rv_shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)
                ->where('rv_assign_agent_status_id', $rv_assign_agent_status_id)
                ->where('rv_state_id', $get_rv_state_id)
                ->latest()->first();

            if ($rv_shipment_assign_agents) {
                $rv_shipment_agent = RvShipmentAgent::find($rv_shipment_assign_agents->rv_shipment_agent_id);
                // increment $rv_shipment_agent already updated
                $rv_shipment_agent->increment('already_updated');

                $rv_shipment_assign_agents->rv_state_id = $updated_rv_state_id;
                $rv_shipment_assign_agents->updated_type_id = 3; //Shipper
                $rv_shipment_assign_agents->updated_by_id = Auth::id();
                $rv_shipment_assign_agents->update();

                $rv_shipment_assign_agent_details  = new RvShipmentAssignAgentDetails();
                $rv_shipment_assign_agent_details->rv_shipment_assign_agent_id = $rv_shipment_assign_agents->id;
                $rv_shipment_assign_agent_details->agent_id = $rv_shipment_assign_agents->agent_id;
                $rv_shipment_assign_agent_details->shipments_journey_id = $rv_shipment_assign_agents->shipments_journey_id;
                $rv_shipment_assign_agent_details->last_shipments_journey_id = $rv_shipment_assign_agents->last_shipments_journey_id;
                $rv_shipment_assign_agent_details->shipment_id = $rv_shipment_assign_agents->shipment_id;
                $rv_shipment_assign_agent_details->rv_assign_agent_status_id = $rv_shipment_assign_agents->rv_assign_agent_status_id;
                $rv_shipment_assign_agent_details->rv_assign_agent_sub_status_id = $rv_shipment_assign_agents->rv_assign_agent_sub_status_id;
                $rv_shipment_assign_agent_details->rv_state_id = $rv_shipment_assign_agents->rv_state_id;
                $rv_shipment_assign_agent_details->updated_type_id = 3; //Shipper
                $rv_shipment_assign_agent_details->updated_by_id = Auth::id();
                $rv_shipment_assign_agent_details->is_fake_status = $rv_shipment_assign_agents->is_fake_status;
                $rv_shipment_assign_agent_details->rv_fake_status_id = $rv_shipment_assign_agents->rv_fake_status_id;
                $rv_shipment_assign_agent_details->remarks = $rv_shipment_assign_agents->remarks;
                $rv_shipment_assign_agent_details->call_to_id  = $rv_shipment_assign_agents->call_to_id;
                $rv_shipment_assign_agent_details->save();
                return true;
            } else {
                return ['status' => 1, 'error' => 'No Shipment ID Found'];
            }
        }
    }

    protected function included_shippers($sorted_agents, $agent_id, $agent_shipment_id = null)
    {
        $shipment = null;

        $rv_priority_shipper =  GlobalSettings::where('type', 'rv_shipper_priority');

        if($rv_priority_shipper->exists()){
            $rv_priority_shipper = $rv_priority_shipper->first();
            $rv_priority_shippers = explode(',', $rv_priority_shipper['text']);
        }

        $rv_priority_shippers = array_filter($rv_priority_shippers, function($value){
            return $value != "";
        });


        $all_shipper_exists =  GlobalSettings::where('type', 'rv_disable_shippers_all_shippers')->where('setting_value', 1)->exists();
        // If excluded_shippers setting is not found, initialize as an empty array
        $included_shippers = [];

        if ($all_shipper_exists) {
            $included_shipper =  GlobalSettings::where('type', 'rv_disable_shippers_excluded_shippers')->where('setting_value', 1);
            // Check if excluded_shippers exists and process it
            if ($included_shipper->exists()) {
                $included_shipper = $included_shipper->first();
                $included_shippers = explode(',', $included_shipper['text']);
            }

        }
        
        $included_shippers = array_filter($included_shippers, function($value){
            return $value != "";
        });


        $only_shipper = GlobalSettings::where('type', 'rv_disable_shippers_only_shippers')->where('setting_value', 1);
        // If only_shippers setting is not found, initialize as an empty array
        $only_shippers = [];
        // Check if only_shippers exists and process it
        if ($only_shipper->exists()) {
            $only_shipper = $only_shipper->first();
            $only_shippers = explode(',', $only_shipper['text']);
        }

        $only_shippers = array_filter($only_shippers, function($value){
            return $value != "";
        });

        
        foreach ($sorted_agents as $key => $agent) {
            $shipments = [];
            
            if($agent_shipment_id)
                $agent_shipment_id;
            
            else if (!empty($included_shippers)) {              
                $flag = false;                
                if (!empty($rv_priority_shippers) && !($only_shipper->exists())){
                    $mergeArr = array_merge($rv_priority_shippers, $included_shippers );
                    $mergeArr = array_unique($mergeArr);
                    $result = array_filter($mergeArr, function($value){
                        return $value != '';
                    });
                    $exploded_result = implode(',', $result);                    
                    $flag = true;
                }              
                $shipments = Shipment::whereIn('user_id', $flag ? $result : $included_shippers)
                    ->whereIn('shipper_status_id', [7, 8, 9, 15, 12, 65])
                    ->where('consignee_city_id', $agent['city_id']);
                if ($flag == true){
                    $shipments->orderByRaw("FIELD(user_id, $exploded_result)");
                } else {
                    $shipments->orderBy('id', 'ASC');
                } 
                $shipments = $shipments->get();
                if($shipments->isEmpty()){
                    continue;
                }  
            }
            
            // Check if only_shippers exists (1 && 0)
            else if (!empty($only_shippers) && !($all_shipper_exists)) {
        
                $shipments = Shipment::where('consignee_city_id', $agent['city_id'])
                ->whereIn('shipper_status_id', [7, 8, 9, 15, 12, 65])
                ->whereNotIn('user_id', $only_shippers)
                ->orderBy('id', 'ASC')
                ->get();
            }
                

            else if ($all_shipper_exists && !($included_shipper)->exists()) {
                $shipments = [];
            }

            
            // check if shipments exist
            if (count($shipments) || $agent_shipment_id) {
                
                //this check will work only if admin will assign shipment manually to agent 
                if($agent_shipment_id){
                    // if agent shipment is assigned - not assigned to same agent only 
                    $shipment_assigned_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $agent_shipment_id)->where('agent_id', Auth::id())->where('rv_state_id', 1);
                    if ($shipment_assigned_assigned_agent->exists()) {
                        return response()->json(['status' => 1, 'error' => 'Shipment is already assigned']);
                    }
                    
                    // Shipment is found and already in working state or return is completed, will not assigned to agent
                    $find_shipment_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $agent_shipment_id)->where('rv_state_id', 1)->first();
                    if ($find_shipment_assigned_agent) {
                        return response()->json(['status' => 1, 'error' => 'Shipment is already assigned']);
                    }

                    $shipments_journey = ShipmentsJourney::where('shipment_id', $agent_shipment_id)->latest()->first();
                    $data = [
                        'agent_id' => $agent_id,
                        'shipment_id' => $agent_shipment_id,
                        'shipments_journey_id' => $shipments_journey->id,
                        'rv_state_id' => 1, //Assigned
                        'rv_assign_agent_status_id' => null,
                        'rv_assign_agent_sub_status_id' => null,
                    ];
                    
                    // creating a new record
                    $this->rv_shipment_assign($data);

                    // return true;
                    return response()->json(['status' => 0, 'success' => 'Shipments Assigned successfully']);
                }

                // this check will work if agent gets the ticket from Virtual RCP Agent Screen
                else if ($shipments)
                {
                    foreach ($shipments as $key => $shipment) {
                        
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
                            $shipment = null;
                            continue;
                        }

                        $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                        
                        $data = [
                            'agent_id' => $agent_id,
                            'shipment_id' => $shipment->id,
                            'shipments_journey_id' => $shipments_journey->id,
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
            else {
                //No Shipment Found in Assigned Hub
                // return false;
                return response()->json(['status' => 1, 'error' => 'No zone assigned or shipment not found']);
            }
        }
        return $shipment;
    }
}
