<?php

namespace App\Http\Traits;

use App\BoltUndeliveredReasonMapCount;
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
use App\Http\Models\RvFakeStatus;
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
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ShipmentStatusReason;
use App\RvCronLog;
use App\RvAssignAgentSubStatus;
use App\RvShipmentTicket;
use App\RvShipmentTicketDeleteTable;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessRvShipmentTicket;
use GuzzleHttp\Client;
use App\Http\Models\Admin\DeliveryNote;

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
    protected function rv_shipment_assign($data,$ticketId = null)
    {
        try {
            //if the same shipment has been already completed, new row will be created
            DB::beginTransaction();
            $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $data['shipment_id'])->latest()->first();
            if ($rv_shipment_assign_agent) {
                $rv_shipment_assign_agent->agent_id = $data['agent_id'];
                $rv_shipment_assign_agent->call_count = $data['call_count'] ?? 0;
                $rv_shipment_assign_agent->shipments_journey_id = $data['shipments_journey_id'];
                $rv_shipment_assign_agent->last_shipments_journey_id = $data['shipments_journey_id'];
                $rv_shipment_assign_agent->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
                $rv_shipment_assign_agent->rv_assign_agent_sub_status_id = $data['rv_assign_agent_sub_status_id'];
                $rv_shipment_assign_agent->rv_state_id = $data['rv_state_id'];
                $rv_shipment_assign_agent->updated_type_id = Auth::guard('agent')->check() ? 2 : 1;
                $rv_shipment_assign_agent->updated_by_id = Auth::id() ?? $data['agent_id'];
                $rv_shipment_assign_agent->assigned_to_type_id = $data['assigned_to_type_id'] ?? 0;
                $rv_shipment_assign_agent->assigned_by = $data['assigned_by'] ?? 0;
                $rv_shipment_assign_agent->save();
            } else {
                $rv_shipment_assign_agent = RvShipmentAssignAgent::create([
                    'shipment_id' => $data['shipment_id'],
                    'agent_id' => $data['agent_id'],
                    'call_count' => $data['call_count'] ?? 0,
                    'shipments_journey_id' => $data['shipments_journey_id'],
                    'last_shipments_journey_id' => $data['shipments_journey_id'],
                    'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'],
                    'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'],
                    'rv_state_id' => $data['rv_state_id'],
                    'updated_type_id' => Auth::guard('agent')->check() ? 2 : 1,
                    'updated_by_id' => Auth::id() ?? $data['agent_id'],
                    'assigned_to_type_id' => $data['assigned_to_type_id'] ?? 0,
                    'assigned_by' => $data['assigned_by'] ?? 0,
                    'created_at' => Carbon::now(),
                ]);
            }

            // $rv_shipment_assign_agent = RvShipmentAssignAgent::updateOrCreate(
            //     ['shipment_id' => $data['shipment_id']],
            //     [ 
            //         'agent_id' => $data['agent_id'],
            //         'shipments_journey_id' => $data['shipments_journey_id'],
            //         'last_shipments_journey_id' => $data['shipments_journey_id'],
            //         'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'],
            //         'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'],
            //         'rv_state_id' => $data['rv_state_id'],
            //         'updated_type_id' => Auth::guard('agent')->check() ? 2 : 1,
            //         'updated_by_id' => Auth::id(),
            //         'assigned_to_type_id' => $data['assigned_to_type_id'] ?? 0,
            //         'assigned_by' => $data['assigned_by'] ?? 0,
            //         'created_at' => Carbon::now(),
            //     ]
            // );

            // }

        RvShipmentAssignAgentDetails::create([
            'rv_shipment_assign_agent_id' => $rv_shipment_assign_agent->id,
            'shipment_id' => $data['shipment_id'],
            'shipments_journey_id' => $data['shipments_journey_id'],
            'agent_id' => $data['agent_id'],
            'call_count' => $data['call_count'] ?? null,
            'updated_type_id' => Auth::guard('agent')->check() ? '2' : '1',
            'updated_by_id' => Auth::id(),
            'rv_state_id' => $data['rv_state_id'] ?? '1',
            'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'] ?? null,
            'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'] ?? null,
            'last_shipments_journey_id' => $data['shipments_journey_id'] ?? null,
            'call_to_id' => $data['call_to_id'] ?? '0',
            'assigned_to_type_id' => $data['assigned_to_type_id'],
            'assigned_by' => $data['assigned_by']
        ]);

        DB::commit();
        return true;
        }
        catch(\Throwable $th)
        {
            // $this->createDeliveryNoteErrorLog('12121', $data['shipment_id'], $th->getMessage());
            Log::channel('cronJobLog')->info('s ' . 'rv_shipment_assign Saved' . $th->getMessage());

            DB::rollBack();
        }
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
                        // $rv_unassign_agent->rv_state_id = 2;
                        $rv_unassign_agent->updated_by_id = Auth::id();
                        $rv_unassign_agent->assigned_to_type_id = 0;
                        $rv_unassign_agent->assigned_by = 0;
                        $rv_unassign_agent->save();

                        $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment)->latest()->first();

                        $request->merge([
                            'shipment_id' => $shipment, 'is_fake_status' => $rv_unassign_agent->is_fake_status, 'remarks' => $rv_unassign_agent->remarks,
                            'call_to_id' => $rv_unassign_agent->call_to_id, 'assigned_by' => 0
                        ]);
                        //new row in RvShipmentAssignAgentDetails table
                        $this->rv_shipment_assign_agent_details($request, $rv_unassign_agent, $shipments_journey);
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
            if ($rv_unassign_agent->exists()) {
                $rv_unassign_agent = $rv_unassign_agent->latest()->first();
                $rv_unassign_agent->rv_state_id = 3;
                $rv_unassign_agent->updated_by_id = Auth::id();
                $rv_unassign_agent->assigned_by = Null;
                $rv_unassign_agent->save();

                $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment_id)->latest()->first();
                $request->merge([
                    'shipment_id' => $shipment_id, 'is_fake_status' => $rv_unassign_agent->is_fake_status, 'remarks' => $rv_unassign_agent->remarks,
                    'call_to_id' => $rv_unassign_agent->call_to_id, 'assigned_by' => Null
                ]);
                //new row in RvShipmentAssignAgentDetails table
                $this->rv_shipment_assign_agent_details($request, $rv_unassign_agent, $shipments_journey);

                return true;
            } else {
                return false;
            }
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $rv_agent_call_history_record_id = null)
    { 
        try {
            $rv_shipment_assign_agent_details  = new RvShipmentAssignAgentDetails();
            $rv_shipment_assign_agent_details->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
            $rv_shipment_assign_agent_details->agent_id = $shipment_assign_agent->agent_id;
            $rv_shipment_assign_agent_details->call_count = $shipment_assign_agent->call_count;
            $rv_shipment_assign_agent_details->shipment_id = $request->shipment_id;
            $rv_shipment_assign_agent_details->shipments_journey_id = $shipments_journey->id;
            $rv_shipment_assign_agent_details->last_shipments_journey_id = $shipments_journey->id;
            $rv_shipment_assign_agent_details->rv_assign_agent_status_id = $shipment_assign_agent->rv_assign_agent_status_id;
            $rv_shipment_assign_agent_details->rv_assign_agent_sub_status_id = $shipment_assign_agent->rv_assign_agent_sub_status_id;
            $rv_shipment_assign_agent_details->rv_state_id = $shipment_assign_agent->rv_state_id;
            $rv_shipment_assign_agent_details->updated_type_id = Auth::guard('agent')->check() ? 2 : 1;
            $rv_shipment_assign_agent_details->updated_by_id = Auth::id() ?? $request->admin_id;
            $rv_shipment_assign_agent_details->is_fake_status = $request->is_fake_status ?? '';
            $rv_shipment_assign_agent_details->rv_fake_status_id = $request->rv_fake_status_id ?? '';
            $rv_shipment_assign_agent_details->remarks = $request->remarks;
            $rv_shipment_assign_agent_details->call_to_id  = $request->call_to_id;
            $rv_shipment_assign_agent_details->assigned_to_type_id  = $shipment_assign_agent->assigned_to_type_id;
            $rv_shipment_assign_agent_details->assigned_by  = $shipment_assign_agent->assigned_by;
            $rv_shipment_assign_agent_details->rv_agent_call_history_id  = $rv_agent_call_history_record_id ?? '';
            $rv_shipment_assign_agent_details->save();

            return true;
        } catch (\Throwable $th) {
            Log::channel('cronJobLog')->info('s ' . ' bot-call- message-failed rv_shipment_assign_agent_details' . $th->getMessage());
            return false;
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    private function shipment_assign_agent_table_columns($request, $agent)
    {
        return [
            //if user has requested for intercept same conginee set rv_assign_agent_status_id to 4(Intercept Approved) else $request->rv_assign_agent_status_id
            'rv_assign_agent_status_id' => ($request->rv_assign_agent_status_id == 3 && $request->intercept_type == 2) ? 4 : $request->rv_assign_agent_status_id,
            'rv_assign_agent_sub_status_id' => $request->rv_assign_agent_sub_status_id,
            'agent_id' => Auth::id() ?? $request->admin_id,
            'rv_fake_status_id' => $request->rv_fake_status_id,
            'remarks' => $request->remarks,
            'is_fake_status' => $request->is_fake_status,
            'call_to_id' => $request->call_to_id,
            'updated_type_id' => 2, //Agent
            'updated_by_id' => Auth::id() ?? $request->admin_id,
            'rv_shipment_agent_id' => $agent->id,
        ];
    }

    protected function update_shipments_status($request, $assigned_agent)
    {
        $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->latest()->first();
        $reattempt_count = BoltUndeliveredReasonMapCount::where('shipment_id', $request->shipment_id)->where('count', 3)->latest()->first();
        $reattempt_requested_shipment = Shipment::where('id', $request->shipment_id)->where('shipper_status_id', 52)->latest()->first();

        $shipment_assign_agent_table_columns = $this->shipment_assign_agent_table_columns($request, $assigned_agent);
        //if bot call is unresponsive and again status is updated to the open
        if($request->input === 0 && $request->rv_assign_agent_status_id == 6){
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 6; //set status to unresponive confirm again asign
            $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = $request->rv_assign_agent_sub_status_id;
            $shipment_assign_agent_table_columns['rv_state_id'] = 3;
            return $shipment_assign_agent_table_columns; // return assign again
        }
        //if shipment delivery count is 3 and again status is updated to unresponsive set the shipment to return confirm
        if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count > 0 && $reattempt_count) {
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 1; //set status to return confirm
            $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = null;
            $shipment_assign_agent_table_columns['rv_state_id'] = 4; //set status as shipment completed
        }
        // if current status of shipment is 52 (shipment reattempt requested) and agent has updated the status to unresponsive set the status to return confirm
        else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count > 0 && $reattempt_requested_shipment) {
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 1; //set status to return confirm
            $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = null;
            $shipment_assign_agent_table_columns['rv_state_id'] = 4; //set status as shipment completed
            
        } else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count < 3) {
            $shipment_assign_agent_table_columns['rv_state_id'] = 2; //unassign shipment
        } else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count == 3) {
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 7; //set status to Shipper Advise Requested 
            $shipment_assign_agent_table_columns['rv_state_id'] = 2; //unassign shipment
        } else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count >= 4) {
            $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 1; //set status to return confirm
            $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = null;
            $shipment_assign_agent_table_columns['rv_state_id'] = 4; //set status as shipment completed
        } elseif($request->rv_assign_agent_status_id == 3 ) { //bot call for other assitance rv_state_id is marked 3 is to manual agent
            $shipment_assign_agent_table_columns['rv_state_id'] = 3;
        }else{
            $shipment_assign_agent_table_columns['rv_state_id'] = 2;
        }
        return $shipment_assign_agent_table_columns;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: this function is updating table rows of rv_shipment_assign_agents
    protected function update_shipment_assign_agent($request, $assigned_agent, $admin_agent, $shipment_assign_agent)
    {
        $shipment_assign_agent_table_columns = $this->update_shipments_status($request, $assigned_agent);
        if ($admin_agent->employee) {
           
            // if $admin_agent is agent
            if ($admin_agent->employee->staff_category_id == 3) {
                $assigned_agent->increment('total_shipments');
                $assigned_agent->increment('actual_productivity');
                $shipment_assign_agent_table_columns['updated_type_id'] = 2; // agent type
            } else {
                // if $admin_agent is admin
                $assigned_agent->increment('already_updated');
                $shipment_assign_agent_table_columns['updated_type_id'] = 1; // admin type
            }

            $shipment_assign_agent_table_columns['call_count'] = (($shipment_assign_agent->call_count ?? 0) + 1);
            
            if($shipment_assign_agent_table_columns['call_count'] == 1)
            {
                $shipmentJourney = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id',12)->latest()
                ->select('created_at')
                ->first();
                if($shipmentJourney)
                {
                    $timeDifferenceInMinutes = Carbon::now()->diffInMinutes($shipmentJourney->created_at);
                    $shipment_assign_agent_table_columns['first_call_time_mins'] = $timeDifferenceInMinutes;
                }
            }

            $shipment_assign_agent->update($shipment_assign_agent_table_columns);

            return true;
        } else {
            return false;
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: this function is new row of rv_shipment_assign_agents
    protected function add_shipment_agent($request, $shipment_assign_agent)
    {
        try {
            //updating columns in shipment assign agent table 

            $add_agent = new RvShipmentAgent();
            $add_agent->agent_id = $shipment_assign_agent->agent_id;
            $add_agent->total_shipments  = $add_agent->total_shipments + 1;
            $add_agent->actual_productivity  = $add_agent->actual_productivity + 1;
            $add_agent->save();

            //this is updating status of rvshipment assign agent row 
            $shipment_assign_agent_table_columns = $this->shipment_assign_agent_table_columns($request, $add_agent);
            $reattempt_count = BoltUndeliveredReasonMapCount::where('shipment_id', $request->shipment_id)->where('count', 3)->latest()->first();
            $reattempt_requested_shipment = Shipment::where('id', $request->shipment_id)->where('shipper_status_id', 52)->latest()->first();

            //if shipment delivery count is 3 and again status is updated to unresponsive set the shipment to return confirm
            if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count > 0 && $reattempt_count) {
                $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 1; //set status to return confirm
                $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = null;
                $shipment_assign_agent_table_columns['rv_state_id'] = 4; //set status as shipment completed
            }
            // if current status of shipment is 52 (shipment reattempt requested) and agent has updated the status to unresponsive set the status to return confirm
            else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count > 0 && $reattempt_requested_shipment) {
                $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 1; //set status to return confirm
                $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = null;
                $shipment_assign_agent_table_columns['rv_state_id'] = 4; //set status as shipment completed
            } else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count < 3) {
                $shipment_assign_agent_table_columns['rv_state_id'] = 2; //unassign shipment
            } else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count == 3) {
                $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 7; //set status to Shipper Advise Requested 
                $shipment_assign_agent_table_columns['rv_state_id'] = 2; //unassign shipment
            } else if ($request->rv_assign_agent_status_id == 6 && $shipment_assign_agent->unresponsive_count == 4) {
                $shipment_assign_agent_table_columns['rv_assign_agent_status_id'] = 1; //set status to return confirm
                $shipment_assign_agent_table_columns['rv_assign_agent_sub_status_id'] = null;
                $shipment_assign_agent_table_columns['rv_state_id'] = 4; //set status as shipment completed
            } else {
                $shipment_assign_agent_table_columns['rv_state_id'] = 2;
            }

            $shipment_assign_agent_table_columns['call_count'] = (($shipment_assign_agent->call_count ?? 0) + 1);
            $shipment_assign_agent->update($shipment_assign_agent_table_columns);

            return true;
        } catch (\Throwable $th) {
            return false;
            //throw $th;
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description:
    protected function update_shipment_status($request,$botCall = 0)
    {
        
        if (Shipment::whereIn('shipper_status_id', [12, 52, 66])->where('id', $request->shipment_id)->doesntExist()) {
            RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();

            // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
            return ['status' => 0, 'error' => "Shipment is in different status, Cannot mark it as Reattempted!"];
        }
        
        if ($request->rv_assign_agent_status_id) {
            $rv_assign_agent_status = RvAssignAgentStatus::find($request->rv_assign_agent_status_id);
            $shipment_status_id = $rv_assign_agent_status->shipment_status_id; //replicate values from shipment_status table
            $call_finding_id = $rv_assign_agent_status->call_finding_id; // this is for unresponsive
            if ($rv_assign_agent_status && ($shipment_status_id !== null || $call_finding_id !== null)) {
                if ($shipment_status_id == 13) {
                    return $this->reattempt($request);
                } elseif ($shipment_status_id == 15) {
                    return $this->on_hold_for_self_collection($request);
                } elseif ($shipment_status_id == 20) {
                    return $this->return_confirm($request);
                } elseif ($shipment_status_id == 54) {
                    return $this->intercept($request);
                } elseif ($shipment_status_id == null) {
                    return $this->unresponsive($request,$botCall);
                }
            } elseif ($shipment_status_id === null && $call_finding_id === null) {
                return $this->refusal_on_call($request);
            }
        } else {
            return ['status' => 0, 'error' => "Something went wrong"];
        }
    }

    // Heading: N/A
    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: userId variable used for the AlistShipper
    protected function reattempt($request,$userId = null)
    {
        $remarks = $request->remarks ?? $request->remark;
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

        //Remove Shipment from RV Shipment Ticket
        // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
        RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();
        if ($userId) { // Alist Shipper is lay this shipper add on shipper advise requested
            $shipperStatus = [12, 52,  65, 66];
        } else {
            $shipperStatus = [12, 52, 66];
        }
        if (in_array($parcel->shipper_status_id, $shipperStatus)) {
            

            $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->whereIn('shipper_status_id', $shipperStatus)->latest('id')->first();
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

                if($userId){
                    ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remarks, $userId, null);
                }else{
                    ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remarks, NULL, Auth::id() ?? $request->agent_id);
                }

                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);
                $this->reattemptNotification($request->shipment_id);
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
    protected function return_confirm($request, $globalAdminId = null,$userId = null)
    {
        // $remarks = (is_array($request) && isset($request['remarks']) && $request['remarks'] !== null)  ? $request['remarks'] : null;
        $remarks = $request->remarks;
        $parcel = Shipment::find($request->shipment_id);

        $rv_sub_status = null;
        $shipment_status_reason = null;

        if ($request->rv_assign_agent_sub_status_id) {
            $rv_sub_status = RvAssignAgentSubStatus::where('id', $request->rv_assign_agent_sub_status_id)->value('name');  
            if ($rv_sub_status) {
                $shipment_status_reason = ShipmentStatusReason::where('name', 'like', '%' . $rv_sub_status . '%')->value('id');
            }
        }

        if (!$request->rv_assign_agent_sub_status_id || !$shipment_status_reason) { // Return confirm RVR reason_id bind in journey inserted
            $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->latest()->select('status_reason_id', 'remarks')->first();
            $shipment_status_reason = $journey->status_reason_id;
            $remarks =  ((!$remarks) ? $journey->remarks : $remarks);
        }
        //these both could be null 
        $consignee_refused_reasons = $request->consignee_refused_reasons ?? null;
        //

        $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
        if (!$dispute_check) {
            return ['status' => 0, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
        }
        if ($parcel->booking_type_id == 5) {
            return ['status' => 0, 'error' => "Reverse Pickup Shipment can not be updated to Return Confirm!"];
        }

        //Remove Shipment from RV Shipment Ticket
        //  dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
        RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();

        // if current shipment statuses are following update the shipment status in shipments table
        // 7 = Shipment - Not Attempted
        // 8 = Shipment - Delivery Unsuccessful
        // 9 = Shipment - On Hold
        // 12 = Shipment - Reason Validation Required
        // 15 = Shipment - On Hold for Self Collection
        // 52 = Shipment - Re-Attempt Requested
        // 66 = Shipment - Re-Attempt Call Requested (from shipper)
        // if (in_array($parcel->shipper_status_id, [7, 8, 9, 12, 15, 52])) { old for rv

        if (in_array($parcel->shipper_status_id, [12, 52, 66]) || $globalAdminId) {
//            Log::channel('cronJobLog')->info('s ' . ' agent:sarnotification return_confirm:in');

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
            
            ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_status_reason, $remarks, null, $globalAdminId ?? Auth::id() ?? $request->agent_id, null, null, 1, null, null, null, null, $consignee_refused_reasons);


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

            //Remove Shipment from RV Shipment Ticket
            //  dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($shipmentId));
            RvShipmentTicket::where('shipment_id', $shipmentId)->delete();

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

                return ['status' => 1, 'success' => "Shipment status successfully updated to Shipment - On Hold for Self Collection"];
            } else {
                return ['status' => 0, 'error' => "Shipment already updated to Shipment - On Hold for Self Collection!"];
            }
        } else {
            return ['status' => 0, 'error' => "Shipment ID Not selected!"];
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
            $s_amount = str_replace(",", "", $request->amount);
            $amount = intval($s_amount);
            $shipment = Shipment::find($request->shipment_id);
            $user_id = $shipment->user_id;
            $intercept_type = $request->intercept_type;
            $shipment_status = $shipment->status_shipper->name;
            $crm = false;
            $crm_request = CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_type_id', 11);
            if ($crm_request->exists()) {
                $crm = true;
            }

            //Remove Shipment from RV Shipment Ticket
            // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
            RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();

            if (in_array($shipment->shipper_status_id, [12, 52, 66]) || $crm == true) {
                if (
                    $shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name
                    || $shipment['consignee_address'] != $request->consignee_address || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1
                    || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email
                    || $shipment['amount'] != $amount
                ) {
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

                            $shipper_status_id = 54; //intercept requested
                            $consignee_status_id = 54; //intercept requested
                            $status_reason_id = Null;
                            $remarks = $request->remarks;
                            ShipmentsJourneyController::add($request->shipment_id, $shipper_status_id, $consignee_status_id, $status_reason_id, $remarks, $user_id, Auth::id());
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

                            $shipper_status_id = 55; //intercept approved
                            $consignee_status_id = 55; //intercept approved
                            $status_reason_id = Null;
                            $remarks = $request->remarks;
                            ShipmentsJourneyController::add($request->shipment_id, $shipper_status_id, $consignee_status_id, $status_reason_id, $remarks, $user_id, Auth::id());

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

                        return ['status' => 1, 'success' => 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']];
                    }
                } else {
                    $request->request->set('rv_assign_agent_status_id', null); //passing rv_assign_agent_status_id as 'null' instead of '3' when error occurs in intercept 
                    return ['status' => 0, 'error' => 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number'], 'redirect' => true];
                }
            } else {
                $request->request->set('rv_assign_agent_status_id', null); //passings rv_assign_agent_status_id as 'null' instead of '3' when error occurs in intercept 
                return redirect()->back()->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
            }
        }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: 
    protected function unresponsive(Request $request, $botCall = null)
    {
        $shipment = Shipment::find($request->shipment_id);
        $user_id = $shipment->user_id;
        $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->whereIn('rv_state_id', [1, 3])->latest()->first();
        $botInvalidNo = ($request->bot_auto_return > 0 ? 1 : 0); // bot invalid call 

        if ($rv_shipment_assign_agent) {
            try {
                $status = new RvAgentCallHistory();
                $status->shipment_id = $request->shipment_id;
                $status->rv_shipment_assign_agent_id = $rv_shipment_assign_agent->id;
                $status->call_finding_id = $request->rv_assign_agent_sub_status_id; //call finding reasons --bot call calling finding hard code id add
                $status->call_to_id = $request->call_to_id ?? 1; //Shipper or Consignee
                $status->remarks = $request->remarks;
                $status->updated_type_id = Auth::guard('agent')->check() ? 2 : 1;
                $status->updated_by_id = Auth::id() ?? $request->admin_id;
                $status->call_status = $request->call_status ?? 'Not Connected';
                $status->updated_at = $request->end_date ?? Carbon::now();
                $status->save();
                if($botInvalidNo > 0){ //If the consignee is invalid phone no during a bot call, the unresponsive count is set to 4, and the return_confirm is marked 
                    $rv_shipment_assign_agent->unresponsive_count = 3;
                    // return ['status' => 1, 'success' => 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
                }else{
                    $rv_shipment_assign_agent->increment('unresponsive_count');
                }
                $rv_shipment_assign_agent->unresponsive_attempt_time = Carbon::now();
                $rv_shipment_assign_agent->save();

                $rv_shipment_ticket = RvShipmentTicket::where('shipment_id',$request->shipment_id)->first();
                if($rv_shipment_ticket){
                    $rv_shipment_ticket->increment('call_count');
                    $rv_shipment_ticket->save();
                }
                if($rv_shipment_assign_agent->unresponsive_count <= 3 && !$botCall && $rv_shipment_ticket){
                    // $rv_shipment_ticket->updated_at = carbon::parse($rv_shipment_ticket->created_at)->addhours(2);
                    $rv_shipment_ticket->in_progress = 0;
                    $rv_shipment_ticket->save();
                    // RvShipmentTicket::where('shipment_id', $request->shipment_id)->update(['in_progress'=>0]);
                }
                $reattempt_count = BoltUndeliveredReasonMapCount::where('shipment_id', $request->shipment_id)->where('count',3)->first();
                //if reattempt count is 3 then shipment status will be auto return confirm
                if ($rv_shipment_assign_agent->unresponsive_count > 0 && $reattempt_count) {
                    //If reattampt count is 3 then will be marked rv_shipment_asign_agent completed.
                    if($botCall){
                        $rv_shipment_assign_agent->rv_assign_agent_status_id = 1;
                        $rv_shipment_assign_agent->rv_state_id = 4;
                        $rv_shipment_assign_agent->save();
                    }

                    $request->merge([
                        'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                        'remarks' => $request->remarks,
                        'rv_assign_agent_sub_status_id' => null
                    ]);
                    $this->return_confirm($request);
                    return ['status' => 1, 'success'=> 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
                }
                //if unresponsive count 3 & status_reason_id is 27,35 then shipment status will be auto return confirm
                else if ($rv_shipment_assign_agent->unresponsive_count > 2 && in_array($rv_shipment_ticket->shipment_status_reason_id,[27,35])) {

                    $request->merge([
                        'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                        'remarks' => $request->remarks,
                        'rv_assign_agent_sub_status_id' => null
                    ]);
                    $this->return_confirm($request);
                }
                //Auto-return if shipment has 3+ unresponsive attempts and is halted (halt_shipper=1)
                else if ($rv_shipment_assign_agent->unresponsive_count >= 3 && RvShipmentTicket::where(['shipment_id'=>$request->shipment_id,'halt_shipper'=>1])->exists()) {

                    $request->merge([
                        'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                        'remarks' => $request->remarks,
                        'rv_assign_agent_sub_status_id' => null
                    ]);
                    $this->return_confirm($request);
                }
                //if unresponsive count is 3 unassigned the shipment & set the assign_agent_status_id to 7, the shipment will be shown to to the shipper 
                else if ($rv_shipment_assign_agent->unresponsive_count == 3) {
                    //if the bot unresponsive count is 2 unassigned the shipment & set the assign_agent_status_id to 7, the shipment will be shown to to the shipper 
                    if($botCall){
                        $rv_shipment_assign_agent->rv_assign_agent_status_id = 7;
                        $rv_shipment_assign_agent->rv_state_id = 2;
                        $rv_shipment_assign_agent->save();
                    }
                    //updating the shipment status to Shipper Advise Requested(65) in shipments table
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 65, 'consignee_status_id' => 65]);
                    //get the shipment journey table in reason validation id
                    
                    //Remove Shipment from RV Shipment Ticket
                    // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
                    RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();
                    
                    // //updating the shipment status to Shipper Advise Requested(65) in shipments journey table
                    ShipmentsJourneyController::add($request->shipment_id, 65, 65, self::getShipmentJourneyStatusReasonId($request->shipment_id), NULL, $user_id, Auth::id() ?? $request->agent_id);
                    return ['status' => 1, 'success'=> 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
                }

                //if unresponsive count 3 & rv_state_id is 4 then shipment status will be auto return confirm
                else if ($rv_shipment_assign_agent->unresponsive_count > 3) {

                    $request->merge([
                        'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                        'remarks' => $request->remarks,
                        'rv_assign_agent_sub_status_id' => null
                    ]);
                    $this->return_confirm($request);
                }

                //if unresponsive and current status of shipment is 52 (shipment reattempt requested) then shipment status will be auto return confirm
                else if ($shipment->shipper_status_id == 52) {
                    $request->merge([
                        'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                        'remarks' => $request->remarks,
                        'rv_assign_agent_sub_status_id' => null
                    ]);
                    $this->return_confirm($request);
                }
                return ['status' => 1, 'success'=> 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
            } 
            catch (\Throwable $th) {
                    
                    return ['status' => 0, 'error'=> 'Something Went Wrong'.$th->getMessage(), 'redirect'=> true];
            }
        } else {
            return ['status' => 0, 'error' => 'Shipment not found', 'redirect' => true];
        }
    }

    protected function unresponsiveForceFully($data)
    {
        
        $shipment = Shipment::find($data->shipment_id);
//        Log::channel('cronJobLog')->info('s ' .'forceFUllyShipmentstatus update'.$shipment->tracking_number.'with id'. $shipment->tracking_number);
        $user_id = $shipment->user_id;
        $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $data->shipment_id)->whereIn('rv_state_id', [1, 3])->latest()->first();

        if ($rv_shipment_assign_agent) {
            try {
                $status = new RvAgentCallHistory();
                $status->shipment_id = $data->shipment_id;
                $status->rv_shipment_assign_agent_id = $rv_shipment_assign_agent->id;
                $status->call_finding_id = 32; //call finding reasons
                $status->call_to_id = $data->call_to_id; //Shipper or Consignee
                $status->remarks = 'As per CX dept';
                $status->updated_type_id = $data->updated_type_id;
                $status->updated_by_id = $data->updated_by_id;
                $status->save();

                $rv_shipment_assign_agent->increment('unresponsive_count');
                $rv_shipment_assign_agent->unresponsive_attempt_time = Carbon::now();
                $rv_shipment_assign_agent->save();
                $rv_shipment_ticket = RvShipmentTicket::where('shipment_id', $data->shipment_id)->increment('call_count');

                $reattempt_count = BoltUndeliveredReasonMapCount::where('shipment_id', $shipment)->where('count', 3)->first();
                //if reattempt count is 3 then shipment status will be auto return confirm
                // if ($rv_shipment_assign_agent->unresponsive_count > 0 && $reattempt_count) {
                //     $request->merge([
                //         'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                //         'remarks' => $request->remarks,
                //         'rv_assign_agent_sub_status_id' => null
                //     ]);
                //     $this->return_confirm($request);
                //     return ['status' => 1, 'success' => 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
                // }
                //if unresponsive count is 2 unassigned the shipment & set the assign_agent_status_id to 7, the shipment will be shown to to the shipper 
                if ($rv_shipment_assign_agent->unresponsive_count > 1) {
                    //updating the shipment status to Shipper Advise Requested(65) in shipments table
                    Shipment::where('id', $data->shipment_id)->update(['shipper_status_id' => 65, 'consignee_status_id' => 65]);
                    //get the shipment journey table in reason validation id

                    //Remove Shipment from RV Shipment Ticket
                    // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($data->shipment_id));
                    RvShipmentTicket::where('shipment_id', $data->shipment_id)->delete();

                    // //updating the shipment status to Shipper Advise Requested(65) in shipments journey table
                    ShipmentsJourneyController::add($data->shipment_id, 65, 65, self::getShipmentJourneyStatusReasonId($data->shipment_id), NULL, $user_id, 346);
                    return ['status' => 1, 'success' => 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
                }

                //if unresponsive count 3 & rv_state_id is 4 then shipment status will be auto return confirm
                // else if ($rv_shipment_assign_agent->unresponsive_count > 2) {

                //     $request->merge([
                //         'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                //         'remarks' => $request->remarks,
                //         'rv_assign_agent_sub_status_id' => null
                //     ]);
                //     $this->return_confirm($request);
                // }

                //if unresponsive and current status of shipment is 52 (shipment reattempt requested) then shipment status will be auto return confirm
                // else if ($shipment->shipper_status_id == 52) {
                //     $request->merge([
                //         'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                //         'remarks' => $request->remarks,
                //         'rv_assign_agent_sub_status_id' => null
                //     ]);
                //     $this->return_confirm($request);
                // }
                return ['status' => 1, 'success' => 'Shipment Updated Successfully', 'rv_agent_call_history_record_id' => $status->id];
            } catch (\Throwable $th) {
                $th->getMessage();
                return ['status' => 0, 'error' => 'Something Went Wrong', 'redirect' => true];
            }
        } else {
            return ['status' => 0, 'error' => 'Shipment not found', 'redirect' => true];
        }
    }
    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: 
    protected function refusal_on_call(Request $request)
    {
        $shipment = Shipment::find($request->shipment_id);
        $user_id = $shipment->user_id;
        $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->latest()->first();

        if ($rv_shipment_assign_agent) {
            try {
                Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 65, 'consignee_status_id' => 65]);

                //Remove Shipment from RV Shipment Ticket
                // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
                RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();

                //updating the shipment status to Shipper Advise Requested(65) in shipments journey table
                ShipmentsJourneyController::add($request->shipment_id, 65, 65, self::getShipmentJourneyStatusReasonId($request->shipment_id), NULL, $user_id, Auth::id());

                return ['status' => 1, 'success' => 'Shipment Updated Successfully'];
            } catch (\Throwable $th) {
                $th->getMessage();
                return ['status' => 0, 'error' => 'Something Went Wrong', 'redirect' => true];
            }
        } else {
            return ['status' => 0, 'error' => 'Shipment not found', 'redirect' => true];
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

    //older include shippers from master
    protected function included_shippers($agent_id, $agent_shipment_id = null)
    {
        $connection = 'reports_2';
        $shipment = null;
        $dateToday = Carbon::today();

        //this wont be null if admin is assigning shipment to an agent
        if ($agent_shipment_id)
            $agent_shipment_id;


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

        $included_shippers = array_filter($included_shippers, function ($value) {
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

        $only_shippers = array_filter($only_shippers, function ($value) {
            return $value != "";
        });

        $shipments = [];

        //Rv Disable Shippers Setting when all shippers are enbale and there are exluded shipper(agents can get those shippers shipments)
        if (!empty($included_shippers)) {

            $shipments = DB::connection($connection)->table('shipments')
                ->leftJoin('rv_shipment_assign_agents as rvsaa', function ($join) use ($dateToday) {
                    $join->on('rvsaa.shipment_id', '=', 'shipments.id')
                        ->where('rvsaa.rv_assign_agent_status_id', 6)
                        ->where('rvsaa.rv_state_id', 2)
                        ->whereDate('unresponsive_attempt_time', $dateToday);
                })
                ->whereIn('shipments.user_id', $included_shippers)
                ->whereIn('shipments.shipper_status_id', [12, 66, 52])
                ->whereNull('rvsaa.shipment_id')
                ->whereRaw('NOT EXISTS (
                SELECT sj.id
                FROM shipments_journey AS sj
                WHERE sj.status_reason_id IN (12, 27, 35)
                AND sj.shipment_id = shipments.id
                AND sj.id = (
                    SELECT MAX(id)
                    FROM shipments_journey
                    WHERE shipment_id = shipments.id
                )
            )');

            $shipments->orderBy('shipments.updated_at', 'ASC');

            $shipments = $shipments->get(['shipments.id']);
        }


        //Rv Disable Shippers Setting Screen when all shippers are disabled and there are shippers in only shippers select box(agents will not get those shippers shipments)
        else if (!empty($only_shippers) && !($all_shipper_exists)) {
            $all_shippers = User::where('status', 3)->pluck('id')->toArray();

            $all_shippers = array_filter($all_shippers, function ($value)  use ($only_shippers) {
                return !in_array($value, $only_shippers);
            });

            $result = array_filter($all_shippers, function ($value) {
                return $value != '';
            });

            if (!empty($result)) {

                $shipments = DB::connection($connection)->table('shipments')
                    ->leftJoin('rv_shipment_assign_agents as rvsaa', function ($join) use ($dateToday) {
                        $join->on('rvsaa.shipment_id', '=', 'shipments.id')
                            ->where('rvsaa.rv_assign_agent_status_id', 6)
                            ->where('rvsaa.rv_state_id', 2)
                            ->whereDate('unresponsive_attempt_time', $dateToday);
                    })
                    ->whereIn('shipments.shipper_status_id', [12, 66, 52])
                    ->whereNull('rvsaa.shipment_id')
                    ->whereIn('shipments.user_id', $result)
                    ->whereRaw('NOT EXISTS (
                    SELECT sj.id
                    FROM shipments_journey AS sj
                    WHERE sj.status_reason_id IN (12, 27, 35)
                    AND sj.shipment_id = shipments.id
                    AND sj.id = (
                        SELECT MAX(id)
                        FROM shipments_journey
                        WHERE shipment_id = shipments.id
                    )
                )');

                $shipments->orderBy('shipments.updated_at', 'ASC');

                $shipments = $shipments->get(['shipments.id']);
            }
        } else if ($all_shipper_exists && !($included_shipper)->exists()) {
            $shipment = [];
        }


        // check if shipments exist or if admin is assign shipment to agent
        if (count($shipments) || $agent_shipment_id) {

            //---THIS CHECK WILL WORK IF AGENT GETS THE TICKET FROM VIRTUAL RCP AGENT SCREEN---//
            if ($shipments && $agent_shipment_id == null) {
                foreach ($shipments as $key => $shipment) {

                    // IF AGENT SHIPMENT IS OPEN - ASSIGNED TO ANY USER WHO COMES FIRST
                    $shipment_assigned_unassigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->where('rv_state_id', 3);
                    if ($shipment_assigned_unassigned_agent->exists()) {
                        $shipment_assigned_unassigned_agent->first();
                        RvShipmentAssignAgent::where('shipment_id', $shipment->id)
                            ->where('rv_state_id', 3)
                            ->update(['rv_state_id' => 1, 'agent_id' => $agent_id, 'assigned_by' => 0]);

                        $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->latest()->first();
                        $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();

                        $data = [
                            'rv_shipment_assign_agent_id' => $shipment_assign_agent->id, 'agent_id' => $agent_id,
                            'shipments_journey_id' => $shipments_journey->id,
                            'last_shipments_journey_id' => $shipments_journey->id,
                            'shipment_id' => $shipment->id,
                            'rv_assign_agent_status_id' => Null,
                            'rv_assign_agent_sub_status_id' => Null,
                            'rv_state_id' => 1,
                            'updated_type_id' => 2,
                            'updated_by_id' =>  Auth::id(),
                            'is_fake_status' => 0,
                            'rv_fake_status_id' => Null,
                            'remarks' => Null,
                            'call_to_id' => 0,
                            'assigned_to_type_id' => Null,
                            'assigned_by' => Null,
                        ];
                        $this->data_rv_shipment_assign_agent_details($data);
                        break;
                    }

                    // if agent shipment is assigned - assigned to same agent only - if close mistakenly or in case of lost page
                    $shipment_assigned_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->where('agent_id', Auth::id())->where('rv_state_id', 1);
                    if ($shipment_assigned_assigned_agent->exists()) {
                        $shipment_assigned_assigned_agent->first();
                        break;
                    }

                    // if agent shipment is assigned - assigned to same agent only - if close mistakenly or in case of lost page
                    //  $shipment_assigned_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipment->id)->where('rv_state_id', 1)->first();
                    //  if ($shipment_assigned_assigned_agent) {
                    //     if($shipment_assigned_assigned_agent->agent_id == Auth::id()) // if shipment is already assigned to this user then pass this shipment to get ticket
                    //     {
                    //         break;
                    //     }
                    //         //otherwise skip this shipment (because this shipment is in process of another agent)
                    //         continue;
                    //  }

                    //  if shipment is found and unassigned(2) or completed(4) then update the current records

                    if (RvShipmentAssignAgent::where('shipment_id', $shipment->id)->whereIn('rv_state_id', [2, 4])->exists()) {
                        if (RvShipmentAssignAgent::where('shipment_id', $shipment->id)->whereDate('updated_at', date('Y-m-d'))->exists()) //if updated_at is already updated today due to any reason () by any agent then skip this shipment for all agents
                        {
                            $shipment = null;
                            continue;
                        }
                        // if shipment is not found in RvShipmentAssignAgent then assign this shipment to agent
                        $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->whereIn('shipper_status_id', [12, 66, 52])->latest()->first();

                        $data = [
                            'agent_id' => $agent_id,
                            'shipment_id' => $shipment->id,
                            'shipments_journey_id' => $shipments_journey->id,
                            'rv_state_id' => 1, //Assigned
                            'rv_assign_agent_status_id' => null,
                            'rv_assign_agent_sub_status_id' => null,
                            'assigned_to_type_id' => null,
                            'assigned_by' => null,
                        ];

                        //updating a row and creating new one
                        $this->rv_shipment_assign($data);
                        break;
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
                        'assigned_to_type_id' => null,
                        'assigned_by' => null,
                    ];

                    // creating a new record
                    $this->rv_shipment_assign($data);
                    break;
                }
            }

            //this check will work only if admin will assign shipment manually to agent 
            else if ($agent_shipment_id) {
                // if agent shipment is assigned - not assigned to same agent only 

                $rv_shipment = RvShipmentAssignAgent::where('shipment_id', $agent_shipment_id)->where('rv_state_id', 1);
                // $shipment_assigned_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $agent_shipment_id)->where('agent_id', Auth::id())->where('rv_state_id', 1);
                $shipment_assigned_assigned_agent = $rv_shipment->where('agent_id', Auth::id());
                if ($shipment_assigned_assigned_agent->exists()) {
                    return response()->json(['status' => 1, 'error' => 'Shipment is already assigned']);
                }

                // Shipment is found and already in working state or return is completed, will not assigned to agent
                $find_shipment_assigned_agent = $rv_shipment->first();
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
                    'assigned_to_type_id' => 2, //include shipper function is only using for contractual agent so thats why we have initial it by 2
                    'assigned_by' => Auth::id(),
                ];

                // creating a new record
                $this->rv_shipment_assign($data);

                // return true;
                return response()->json(['status' => 0, 'success' => 'Shipments Assigned successfully']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'No shipment found']);
        }
        // }
        return $shipment;
    }

    protected function findShipmentforAgent($agent_id)
    {
        $shipment = null;

        $agent = Admin::find($agent_id);

        $shipments = $this->getShipmentsFromRvShipmentTicket($agent);
        // check if shipments exist or if admin is assign shipment to agent
        //---THIS CHECK WILL WORK IF AGENT GETS THE TICKET FROM VIRTUAL RCP AGENT SCREEN---//
        if ($shipments->count()) {
            // $shipment_data = [];
            foreach ($shipments as $shipment) {
                $shipmentId = $shipment->shipment_id;
                $ticketId = $shipment->id;
                //Shipment table check the current status.
                if (!Shipment::whereIn('shipper_status_id', [12, 52, 66])->where('id', $shipmentId)->exists()) {
                    // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($shipmentId));
                    RvShipmentTicket::where('shipment_id', $shipmentId)->delete();
                    // $shipment_data['shipment_id'][] = $shipmentId;
                    $shipment = null;
                    continue;
                }
                // IF AGENT SHIPMENT IS OPEN - ASSIGNED TO ANY USER WHO COMES FIRST
                $shipment_assigned_unassigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipmentId)->where('rv_state_id', 3);     
                if ($shipment_assigned_unassigned_agent->exists()) {
                    $shipment_assigned_unassigned_agent->first();

                    // skip this shipment if already is in progress
                    if(RvShipmentTicket::where('shipment_id', $shipmentId)->where('in_progress', 1)->exists())
                    {
                        $shipment = null;
                        continue;
                    }
                    //else update this shipment status in rv_shipment_tickets to in_progress = 1
                    else
                    {
                        RvShipmentTicket::find($ticketId)->update(['in_progress' => 1]);
                    }

                    RvShipmentAssignAgent::where('shipment_id', $shipmentId)
                        ->where('rv_state_id', 3)
                        ->update(['rv_state_id'=> 1, 'agent_id'=> $agent_id, 'assigned_by'=> 0]);

                    $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $shipmentId)->latest()->first();
                    $shipments_journey = ShipmentsJourney::where('shipment_id', $shipmentId)->latest()->first();

                    $data = [
                        'rv_shipment_assign_agent_id' => $shipment_assign_agent->id, 'agent_id' => $agent_id,
                        'shipments_journey_id' => $shipments_journey->id,
                        'last_shipments_journey_id' => $shipments_journey->id,
                        'shipment_id' => $shipmentId,
                        'rv_assign_agent_status_id' => Null,
                        'rv_assign_agent_sub_status_id' => Null,
                        'rv_state_id' => 1,
                        'updated_type_id' => 2,
                        'updated_by_id' =>  Auth::id(),
                        'is_fake_status' => 0,
                        'rv_fake_status_id' => Null,
                        'remarks' => Null,
                        'call_to_id' => 0,
                        'assigned_to_type_id' => Null,
                        'assigned_by' => Null,
                        'call_count' => null,
                    ];
                    $this->data_rv_shipment_assign_agent_details($data, $ticketId);
                    break;
                }

                // if agent shipment is assigned - assigned to same agent only - if close mistakenly or in case of lost page
                $shipment_assigned_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipmentId)->where('agent_id', Auth::id())->where('rv_state_id', 1);
                if ($shipment_assigned_assigned_agent->exists()) {
                    // $shipment_assigned_assigned_agent->first();
                    RvShipmentTicket::find($ticketId)->update(['in_progress'=>1]);
                    break;
                }

                //  if shipment is found and unassigned(2) or completed(4) then update the current records

                if (RvShipmentAssignAgent::where('shipment_id', $shipmentId)->whereIn('rv_state_id', [2, 4])->exists()) {
                    $time = Carbon::parse(now())->subHour(2)->format('Y-m-d H:i:s');
                    if (RvShipmentAssignAgent::where('shipment_id', $shipmentId)->where('updated_at', '>=', $time)->exists()) //if updated_at is already updated 2 hours ago due to any reason () by any agent then skip this shipment for all agents
                    {
                        $shipment = null;

                         // skip this shipment if already is in progress
                        // if(RvShipmentTicket::where('shipment_id', $shipmentId)->where('in_progress', 1)->exists())
                        // {
                        //     $shipment = null;
                        //     continue;
                        // }
                        //else update this shipment status in rv_shipment_tickets to in_progress = 1
                        // else
                        // {
                        //     RvShipmentTicket::find($ticketId)->update(['in_progress'=>1]);
                        // }

                        continue;
                    }
                    // if shipment is not found in RvShipmentAssignAgent then assign this shipment to agent
                    $shipments_journey = ShipmentsJourney::where('shipment_id', $shipmentId)->whereIn('shipper_status_id', [12, 66, 52])->latest()->first();

                    $data = [
                        'agent_id' => $agent_id,
                        'shipment_id' => $shipmentId,
                        'shipments_journey_id' => $shipments_journey->id,
                        'rv_state_id' => 1, //Assigned
                        'rv_assign_agent_status_id' => null,
                        'rv_assign_agent_sub_status_id' => null,
                        'assigned_to_type_id' => null,
                        'assigned_by' => null,
                        'call_count' => 0,
                    ];

                     // skip this shipment if already is in progress
                     if(RvShipmentTicket::where('shipment_id', $shipmentId)->where('in_progress', 1)->exists())
                     {
                        $shipment = null;
                        continue;
                     }
                     //else update this shipment status in rv_shipment_tickets to in_progress = 1
                     else
                     {
                        RvShipmentTicket::find($ticketId)->update(['in_progress'=>1]);
                     }

                    //updating a row and creating new one
                    $this->rv_shipment_assign($data);
                    break;
                }

                // Shipment is found and already in working state or return is completed, new shipment will get to agent
                $find_shipment_assigned_agent = RvShipmentAssignAgent::where('shipment_id', $shipmentId)->first();
                if ($find_shipment_assigned_agent) {
                    $shipment = null;

                     // skip this shipment if already is in progress
                     if(RvShipmentTicket::where('shipment_id', $shipmentId)->where('in_progress', 1)->exists())
                     {
                        $shipment = null;
                         continue;
                     }
                     //else update this shipment status in rv_shipment_tickets to in_progress = 1
                     else
                     {
                         RvShipmentTicket::find($ticketId)->update(['in_progress'=>1]);
                     }

                    continue;
                }

                $shipments_journey = ShipmentsJourney::where('shipment_id', $shipmentId)->latest()->first();

                $data = [
                    'agent_id' => $agent_id,
                    'shipment_id' => $shipmentId,
                    'shipments_journey_id' => $shipments_journey->id,
                    'rv_state_id' => 1, //Assigned
                    'rv_assign_agent_status_id' => null,
                    'rv_assign_agent_sub_status_id' => null,
                    'assigned_to_type_id' => null,
                    'assigned_by' => null,
                    'call_count' => 0,
                ];

                 // skip this shipment if already is in progress
                 if(RvShipmentTicket::where('shipment_id', $shipmentId)->where('in_progress', 1)->exists())
                 {
                    $shipment = null;
                    continue;
                 }
                 //else update this shipment status in rv_shipment_tickets to in_progress = 1
                 else
                 {
                    RvShipmentTicket::find($ticketId)->update(['in_progress'=>1]);
                 }


                // creating a new record
                $this->rv_shipment_assign($data);

                break;
            }
            // self::rvshipmentDelete($shipment_data);
        }

        return $shipment;
        
    }

    protected function getShipmentsFromRvShipmentTicket($agent = null)
    {
        $shipments = RvShipmentTicket::on('reports')
            ->whereBetween('updated_at', [
                now()->startOfYear(),
                now()->endOfYear()
            ])->where('disabled_shipper',0)
                ->when($agent, function ($query, $agent) {
                    if($agent->agent_caller_type == 1) //These Agents will get shipments pending with first call only
                    {
                        return $query->where('call_count' , 0);
                    }
                    else if($agent->agent_caller_type == 2)//These Agents will get shipments pending with second call only
                    {
                        return $query->where('call_count' , '>', 0)->where('call_count' , '<', 2);
                    }
                    else
                    {
                        return $query->orderBy('call_count','ASC');//These Agents will get shipments in order of call count to Agent of Both Call Type
                    }
                })
                ->where('permanent_disable',0)
                ->where('in_progress', 0)
                ->where('is_completed',0)
                ->where('is_bot',0)
                ->orderBy('updated_at','asc')
                ->limit(500)
                ->get(['id','shipment_id']);
        return $shipments;                
    }

    protected function getShipmentJourneyStatusReasonId($shipment)
    {
        $statusReasonId = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest()->first()->status_reason_id;
        return $statusReasonId;
    }
    //updated and optimized
   
    public function get_call_status_history(Request $request, $shipment = null)
    {
        $mergedArray = [];
        $data = RvAgentCallHistory::select('rv_agent_call_histories.*')->with(['rv_call_finding' => function ($query) {
            $query->select('id', 'name');
        }, 'shipment.status_shipper' => function ($query) {
            $query->select('id', 'name');
        },  'updated_by'])->where('shipment_id', (isset($request->shipment_id) ? $request->shipment_id : $shipment))->orderby('updated_at', 'desc')->get();

        if ($data) {
            foreach ($data as $item) {
                $mergedArray[] = [
                    'data' => $item,
                    'user_name' => $item->updated_by->name ?? '-',
                ];
            }
            return  $mergedArray;
        } else {
            return false;
        }
    }

    protected function data_rv_shipment_assign_agent_details($data, $ticketId = null){
        $rv_shipment_assign_agent_details = new RvShipmentAssignAgentDetails();
        $rv_shipment_assign_agent_details->rv_shipment_assign_agent_id = $data['rv_shipment_assign_agent_id'];
        $rv_shipment_assign_agent_details->agent_id = $data['agent_id'];
        $rv_shipment_assign_agent_details->call_count = $data['call_count'] ?? null;
        $rv_shipment_assign_agent_details->shipments_journey_id = $data['shipments_journey_id'];
        $rv_shipment_assign_agent_details->last_shipments_journey_id = $data['last_shipments_journey_id'];
        $rv_shipment_assign_agent_details->shipment_id = $data['shipment_id'];
        $rv_shipment_assign_agent_details->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
        $rv_shipment_assign_agent_details->rv_assign_agent_sub_status_id = $data['rv_assign_agent_sub_status_id'];
        $rv_shipment_assign_agent_details->rv_state_id = $data['rv_state_id'];
        $rv_shipment_assign_agent_details->updated_type_id = $data['updated_type_id'];
        $rv_shipment_assign_agent_details->updated_by_id = $data['updated_by_id'];
        $rv_shipment_assign_agent_details->is_fake_status = $data['is_fake_status'];
        $rv_shipment_assign_agent_details->rv_fake_status_id = $data['rv_fake_status_id'];
        $rv_shipment_assign_agent_details->remarks = $data['remarks'];
        $rv_shipment_assign_agent_details->call_to_id  = $data['call_to_id'];
        $rv_shipment_assign_agent_details->assigned_to_type_id  = $data['assigned_to_type_id'];
        $rv_shipment_assign_agent_details->assigned_by  = $data['assigned_by'];
        $rv_shipment_assign_agent_details->save();

        // if($ticketId)
        // {
        //     RvShipmentTicket::find($ticketId)->update(['in_progress'=>1]);
        // }
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: this function is updating table rows of rv_shipment_assign_agents from shipper side
    protected function shipment_status_update_shipper($request, $updated_by_id, $updated_type_id, $update_rv_assign_agent_status_id, $updated_rv_state_id, $updated_rv_assign_agent_sub_status_id = null)
    {
        $shipper = User::where('id', $updated_by_id)->first();
        if ($shipper) {

            $rv_shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)
                ->where('rv_assign_agent_status_id', 7)
                ->where('rv_state_id', 2)
                ->latest()->first();

            DB::beginTransaction();
            try {
                if ($rv_shipment_assign_agents) {
                    $rv_shipment_assign_agents->rv_assign_agent_status_id = $update_rv_assign_agent_status_id;

                    //$updated_rv_assign_agent_sub_status_id will be used as null always when shipper has update status as reattempt call request or return confirm because sub status is not required in those stattuses  
                    $rv_shipment_assign_agents->rv_assign_agent_sub_status_id = $updated_rv_assign_agent_sub_status_id ?? $rv_shipment_assign_agents->rv_assign_agent_sub_status_id;
                    $rv_shipment_assign_agents->rv_state_id = $updated_rv_state_id;
                    $rv_shipment_assign_agents->updated_type_id = $updated_type_id;
                    $rv_shipment_assign_agents->updated_by_id = $updated_by_id;
                    $rv_shipment_assign_agents->update();

                    $data = [
                        'rv_shipment_assign_agent_id' => $rv_shipment_assign_agents->id,
                        'agent_id' => $rv_shipment_assign_agents->agent_id,
                        'shipments_journey_id' => $rv_shipment_assign_agents->shipments_journey_id,
                        'last_shipments_journey_id' => $rv_shipment_assign_agents->last_shipments_journey_id,
                        'shipment_id' => $rv_shipment_assign_agents->shipment_id,
                        'rv_assign_agent_status_id' => $rv_shipment_assign_agents->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => $rv_shipment_assign_agents->rv_assign_agent_sub_status_id,
                        'rv_state_id' => $rv_shipment_assign_agents->rv_state_id,
                        'updated_type_id' => $rv_shipment_assign_agents->updated_type_id,
                        'updated_by_id' =>  $rv_shipment_assign_agents->updated_by_id,
                        'is_fake_status' => $rv_shipment_assign_agents->is_fake_status,
                        'rv_fake_status_id' => $rv_shipment_assign_agents->rv_fake_status_id,
                        'remarks' => $rv_shipment_assign_agents->remarks,
                        'call_to_id' => $rv_shipment_assign_agents->call_to_id,
                        'assigned_by' => $rv_shipment_assign_agents->assigned_by,
                        'assigned_to_type_id' => $rv_shipment_assign_agents->assigned_to_type_id,
                    ];
                    $this->data_rv_shipment_assign_agent_details($data);

                    if($updated_rv_state_id != 3)
                    {
                        //Remove Shipment from RV Shipment Ticket
                        // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($request->shipment_id));
                        RvShipmentTicket::where('shipment_id', $request->shipment_id)->delete();

                    }

                    DB::commit();
                    return true;
                } else {
                    DB::rollback();
                    return ['status' => 1, 'error' => 'No Shipment ID Found'];
                }
            } catch (\Throwable $th) {
                return ['status' => 1, 'error' => $th->getMessage()];
            }
        }
    }

    protected function rv_shipment_assign_agent_by_admin($data)
    {
        $shipments_journey = ShipmentsJourney::where('shipment_id', $data['shipment_id'])->whereIn('shipper_status_id', [12, 65, 66, 52])->select('id','created_at')->first();

        if ($shipments_journey) {

            $shipment_exist = RvShipmentAssignAgent::where('shipment_id',  $data['shipment_id'])->first();
            if ($shipment_exist) {
                $agent_unassign_shipment = RvShipmentAssignAgent::where('rv_state_id', 3)->where('shipment_id', $data['shipment_id'])->whereNotNull('rv_assign_agent_status_id')->first();
                $rv_customer_experience_agent = RvShipmentAssignAgent::where('rv_state_id', 1)->where('shipment_id', $data['shipment_id'])->where('assigned_to_type_id', 1)->where('agent_id', $data['agent_id'])->first();
                $existing_completed_shipment = RvShipmentAssignAgent::where('shipment_id', $data['shipment_id'])->where('rv_assign_agent_status_id', '!=', 6)->whereIn('rv_state_id', [2, 4])->latest()->first();

                if ($agent_unassign_shipment) {
                    $agent_unassign_shipment->agent_id = $data['agent_id'];
                    $agent_unassign_shipment->increment('call_count');
                    $agent_unassign_shipment->shipments_journey_id = $shipments_journey->id;
                    $agent_unassign_shipment->last_shipments_journey_id = $shipments_journey->id;
                    $agent_unassign_shipment->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
                    $agent_unassign_shipment->rv_assign_agent_sub_status_id = isset($data['rv_assign_agent_sub_status_id']) ? $data['rv_assign_agent_sub_status_id'] : null;
                    $agent_unassign_shipment->rv_state_id = $data['state_id'] ?? 4;
                    $agent_unassign_shipment->updated_type_id = $data['type_id'] ?? 1;
                    $agent_unassign_shipment->updated_by_id = $data['updated_by_id'];
                    $agent_unassign_shipment->remarks = isset($data['remarks']) ? $data['remarks'] : null;
                    $agent_unassign_shipment->call_to_id  = 1;

                    if($agent_unassign_shipment->call_count == 1)
                    {
                        $timeDifferenceInMinutes = Carbon::now()->diffInMinutes($shipments_journey->created_at);
                        $agent_unassign_shipment->first_call_time_mins = $timeDifferenceInMinutes;
                    }

                    $agent_unassign_shipment->save();

                    $updated_data = [
                        'rv_shipment_assign_agent_id' => $agent_unassign_shipment->id,
                        'agent_id' => $agent_unassign_shipment->agent_id,
                        'call_count' => $agent_unassign_shipment->call_count,
                        'shipments_journey_id' => $agent_unassign_shipment->shipments_journey_id,
                        'last_shipments_journey_id' => $agent_unassign_shipment->last_shipments_journey_id,
                        'shipment_id' => $agent_unassign_shipment->shipment_id,
                        'rv_assign_agent_status_id' => $agent_unassign_shipment->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => $agent_unassign_shipment->rv_assign_agent_sub_status_id,
                        'rv_state_id' => $agent_unassign_shipment->rv_state_id,
                        'updated_type_id' => $agent_unassign_shipment->updated_type_id,
                        'updated_by_id' =>  $agent_unassign_shipment->updated_by_id,
                        'is_fake_status' => $agent_unassign_shipment->is_fake_status,
                        'rv_fake_status_id' => $agent_unassign_shipment->rv_fake_status_id,
                        'remarks' => $agent_unassign_shipment->remarks,
                        'call_to_id' => $agent_unassign_shipment->call_to_id,
                        'assigned_by' => $agent_unassign_shipment->assigned_by,
                        'assigned_to_type_id' => $agent_unassign_shipment->assigned_to_type_id,
                    ];

                    if ($updated_data['rv_state_id'] != 3)
                    {
                        //Remove Shipment from RV Shipment Ticket
                        // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($data['shipment_id']));
                        RvShipmentTicket::where('shipment_id', $data['shipment_id'])->delete();

                    }

                    $this->data_rv_shipment_assign_agent_details($updated_data);


                }
                //if shipment row in rv_shipment_assign_agent is found it means that admin has already assign the shipment to customer experince & customer experice 
                //agent is udating the status
                else if ($rv_customer_experience_agent) {
                    $rv_customer_experience_agent->agent_id = $data['agent_id'];
                    $rv_customer_experience_agent->increment('call_count');
                    $rv_customer_experience_agent->shipments_journey_id = $shipments_journey->id;
                    $rv_customer_experience_agent->last_shipments_journey_id = $shipments_journey->id;
                    $rv_customer_experience_agent->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
                    $rv_customer_experience_agent->rv_assign_agent_sub_status_id = isset($data['rv_assign_agent_sub_status_id']) ? $data['rv_assign_agent_sub_status_id'] : null;
                    $rv_customer_experience_agent->rv_state_id = $data['state_id'] ?? 4;
                    $rv_customer_experience_agent->updated_type_id = $data['type_id'] ?? 1;
                    $rv_customer_experience_agent->updated_by_id = $data['updated_by_id'];
                    $rv_customer_experience_agent->remarks = isset($data['remarks']) ? $data['remarks'] : null;
                    $rv_customer_experience_agent->call_to_id  = 1;

                    if($rv_customer_experience_agent->call_count == 1)
                    {
                        $timeDifferenceInMinutes = Carbon::now()->diffInMinutes($shipments_journey->created_at);
                        $rv_customer_experience_agent->first_call_time_mins = $timeDifferenceInMinutes;
                    }

                    $rv_customer_experience_agent->save();

                    $updated_data = [
                        'rv_shipment_assign_agent_id' => $rv_customer_experience_agent->id,
                        'agent_id' => $rv_customer_experience_agent->agent_id,
                        'call_count' => $rv_customer_experience_agent->call_count,
                        'shipments_journey_id' => $rv_customer_experience_agent->shipments_journey_id,
                        'last_shipments_journey_id' => $rv_customer_experience_agent->last_shipments_journey_id,
                        'shipment_id' => $rv_customer_experience_agent->shipment_id,
                        'rv_assign_agent_status_id' => $rv_customer_experience_agent->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => $rv_customer_experience_agent->rv_assign_agent_sub_status_id,
                        'rv_state_id' => $rv_customer_experience_agent->rv_state_id,
                        'updated_type_id' => $rv_customer_experience_agent->updated_type_id,
                        'updated_by_id' =>  $rv_customer_experience_agent->updated_by_id,
                        'is_fake_status' => $rv_customer_experience_agent->is_fake_status,
                        'rv_fake_status_id' => $rv_customer_experience_agent->rv_fake_status_id,
                        'remarks' => $rv_customer_experience_agent->remarks,
                        'call_to_id' => $rv_customer_experience_agent->call_to_id,
                        'assigned_by' => $rv_customer_experience_agent->assigned_by,
                        'assigned_to_type_id' => $rv_customer_experience_agent->assigned_to_type_id,
                    ];

                    if ($updated_data['rv_state_id'] != 3)
                    {
                        //Remove Shipment from RV Shipment Ticket
                        // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($data['shipment_id']));
                        RvShipmentTicket::where('shipment_id', $data['shipment_id'])->delete();

                    }
                    
                    $this->data_rv_shipment_assign_agent_details($updated_data);
                } else if ($existing_completed_shipment) {
                    $existing_completed_shipment->agent_id = $data['agent_id'];
                    $existing_completed_shipment->increment('call_count');
                    $existing_completed_shipment->shipments_journey_id = $shipments_journey->id;
                    $existing_completed_shipment->last_shipments_journey_id = $shipments_journey->id;
                    $existing_completed_shipment->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
                    $existing_completed_shipment->rv_assign_agent_sub_status_id = isset($data['rv_assign_agent_sub_status_id']) ? $data['rv_assign_agent_sub_status_id'] : null;
                    $existing_completed_shipment->rv_state_id = $data['state_id'] ?? 4;
                    $existing_completed_shipment->updated_type_id = $data['type_id'] ?? 1;
                    $existing_completed_shipment->updated_by_id = $data['updated_by_id'];
                    $existing_completed_shipment->remarks = isset($data['remarks']) ? $data['remarks'] : null;
                    $existing_completed_shipment->call_to_id  = 1;

                    if($existing_completed_shipment->call_count == 1)
                    {
                        $timeDifferenceInMinutes = Carbon::now()->diffInMinutes($shipments_journey->created_at);
                        $existing_completed_shipment->first_call_time_mins = $timeDifferenceInMinutes;
                    }

                    $existing_completed_shipment->save();

                    $updated_data = [
                        'rv_shipment_assign_agent_id' => $existing_completed_shipment->id,
                        'agent_id' => $existing_completed_shipment->agent_id,
                        'call_count' => $existing_completed_shipment->call_count,
                        'shipments_journey_id' => $existing_completed_shipment->shipments_journey_id,
                        'last_shipments_journey_id' => $existing_completed_shipment->last_shipments_journey_id,
                        'shipment_id' => $existing_completed_shipment->shipment_id,
                        'rv_assign_agent_status_id' => $existing_completed_shipment->rv_assign_agent_status_id,
                        'rv_assign_agent_sub_status_id' => $existing_completed_shipment->rv_assign_agent_sub_status_id,
                        'rv_state_id' => $existing_completed_shipment->rv_state_id,
                        'updated_type_id' => $existing_completed_shipment->updated_type_id,
                        'updated_by_id' =>  $existing_completed_shipment->updated_by_id,
                        'is_fake_status' => $existing_completed_shipment->is_fake_status,
                        'rv_fake_status_id' => $existing_completed_shipment->rv_fake_status_id,
                        'remarks' => $existing_completed_shipment->remarks,
                        'call_to_id' => $existing_completed_shipment->call_to_id,
                        'assigned_by' => $existing_completed_shipment->assigned_by,
                        'assigned_to_type_id' => $existing_completed_shipment->assigned_to_type_id,
                    ];

                    if ($updated_data['rv_state_id'] != 3)
                    {
                        //Remove Shipment from RV Shipment Ticket
                        // dispatch(new ProcessRemoveShipmentFromRvSh            
                        RvShipmentTicket::where('shipment_id', $data['shipment_id'])->delete();                        
                    }

                    $this->data_rv_shipment_assign_agent_details($updated_data);
                }
            }
            //if shipment row in rv_shipment_assign_agent is not found it means that admin is updating the status itself
            else {

                $rv_shipment_assign_agent = new RvShipmentAssignAgent();
                $rv_shipment_assign_agent->agent_id = $data['agent_id'];
                $rv_shipment_assign_agent->call_count = $data['call_count'] ?? 1;
                $rv_shipment_assign_agent->shipments_journey_id = $shipments_journey->id;
                $rv_shipment_assign_agent->last_shipments_journey_id = $shipments_journey->id;
                $rv_shipment_assign_agent->shipment_id = $data['shipment_id'];
                $rv_shipment_assign_agent->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
                $rv_shipment_assign_agent->rv_assign_agent_sub_status_id = isset($data['rv_assign_agent_sub_status_id']) ? $data['rv_assign_agent_sub_status_id'] : null;
                $rv_shipment_assign_agent->rv_state_id = $data['state_id'] ?? 4;
                $rv_shipment_assign_agent->is_fake_status = 0;
                $rv_shipment_assign_agent->rv_fake_status_id = null;
                $rv_shipment_assign_agent->rv_shipment_agent_id = 0;
                $rv_shipment_assign_agent->updated_type_id = $data['type_id'] ?? 1;
                $rv_shipment_assign_agent->updated_by_id = $data['updated_by_id'];
                $rv_shipment_assign_agent->remarks = isset($data['remarks']) ? $data['remarks'] : null;
                $rv_shipment_assign_agent->call_to_id  = 1;
                $rv_shipment_assign_agent->assigned_by  = 0;
                $rv_shipment_assign_agent->unresponsive_count  = 0;
                $rv_shipment_assign_agent->unresponsive_email_count  = 0;
                $rv_shipment_assign_agent->unresponsive_attempt_time  = null;
                $rv_shipment_assign_agent->assigned_to_type_id  = 0;

                if($rv_shipment_assign_agent->call_count == 1)
                {
                    $timeDifferenceInMinutes = Carbon::now()->diffInMinutes($shipments_journey->created_at);
                    $rv_shipment_assign_agent->first_call_time_mins = $timeDifferenceInMinutes;
                }

                $rv_shipment_assign_agent->save();

                $updated_data = [
                    'rv_shipment_assign_agent_id' => $rv_shipment_assign_agent->id,
                    'agent_id' => $rv_shipment_assign_agent->agent_id,
                    'call_count' => $rv_shipment_assign_agent->call_count,
                    'shipments_journey_id' => $rv_shipment_assign_agent->shipments_journey_id,
                    'last_shipments_journey_id' => $rv_shipment_assign_agent->last_shipments_journey_id,
                    'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                    'rv_assign_agent_status_id' => $rv_shipment_assign_agent->rv_assign_agent_status_id,
                    'rv_assign_agent_sub_status_id' => $rv_shipment_assign_agent->rv_assign_agent_sub_status_id,
                    'rv_state_id' => $rv_shipment_assign_agent->rv_state_id,
                    'updated_type_id' => $rv_shipment_assign_agent->updated_type_id,
                    'updated_by_id' =>  $rv_shipment_assign_agent->updated_by_id,
                    'is_fake_status' => $rv_shipment_assign_agent->is_fake_status,
                    'rv_fake_status_id' => $rv_shipment_assign_agent->rv_fake_status_id,
                    'remarks' => $rv_shipment_assign_agent->remarks,
                    'call_to_id' => $rv_shipment_assign_agent->call_to_id,
                    'assigned_by' => $rv_shipment_assign_agent->assigned_by,
                    'assigned_to_type_id' => $rv_shipment_assign_agent->assigned_to_type_id,
                ];

                if ($updated_data['rv_state_id'] != 3)
                {
                    //Remove Shipment from RV Shipment Ticket
                    // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($data['shipment_id']));
                    RvShipmentTicket::where('shipment_id', $data['shipment_id'])->delete();

                }

                $this->data_rv_shipment_assign_agent_details($updated_data);
            }

        }
        else{
            return false;
        }
        return true;
    }

    // Heading: N/A
    // Siderbar: N/A
    // URL: 
    // Description: this function is updating table rows of rv_shipment_assign_agents requested to approve or reject intercept request
    protected function update_rv_shipment_assign_agent_by_admin($data)
    {
        $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $data['shipment_id'])->latest()->first();
        $shipments_journey = ShipmentsJourney::where('shipment_id', $data['shipment_id'])->whereIn('shipper_status_id', [12, 65, 66, 52])->latest()->first();

        if ($rv_shipment_assign_agent) {
            $rv_shipment_assign_agent->shipment_id = $data['shipment_id'];
            $rv_shipment_assign_agent->shipments_journey_id = $shipments_journey->id;
            $rv_shipment_assign_agent->last_shipments_journey_id = $shipments_journey->id;
            $rv_shipment_assign_agent->rv_assign_agent_status_id = $data['rv_assign_agent_status_id'];
            $rv_shipment_assign_agent->rv_assign_agent_sub_status_id = isset($data['rv_assign_agent_sub_status_id']) ? $data['rv_assign_agent_sub_status_id'] : null;
            $rv_shipment_assign_agent->rv_state_id = 4;
            $rv_shipment_assign_agent->updated_type_id = 1;
            $rv_shipment_assign_agent->updated_by_id = $data['updated_by_id'];
            $rv_shipment_assign_agent->remarks = isset($data['remarks']) ? $data['remarks'] : null;
            
            if($rv_shipment_assign_agent->call_count == 0)
            {
                $timeDifferenceInMinutes = Carbon::now()->diffInMinutes($shipments_journey->created_at);
                $rv_shipment_assign_agent->first_call_time_mins = $timeDifferenceInMinutes;
            }

            $rv_shipment_assign_agent->increment('call_count');
            $rv_shipment_assign_agent->save();

            $rv_shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $data['shipment_id'])->latest()->first();
            $updated_data = [
                'rv_shipment_assign_agent_id' => $rv_shipment_assign_agent->id,
                'agent_id' => $rv_shipment_assign_agent->agent_id,
                'call_count' => $rv_shipment_assign_agent->call_count,
                'shipments_journey_id' => $rv_shipment_assign_agent->shipments_journey_id,
                'last_shipments_journey_id' => $rv_shipment_assign_agent->last_shipments_journey_id,
                'shipment_id' => $rv_shipment_assign_agent->shipment_id,
                'rv_assign_agent_status_id' => $rv_shipment_assign_agent->rv_assign_agent_status_id,
                'rv_assign_agent_sub_status_id' => $rv_shipment_assign_agent->rv_assign_agent_sub_status_id,
                'rv_state_id' => $rv_shipment_assign_agent->rv_state_id,
                'updated_type_id' => $rv_shipment_assign_agent->updated_type_id,
                'updated_by_id' =>  $rv_shipment_assign_agent->updated_by_id,
                'is_fake_status' => $rv_shipment_assign_agent->is_fake_status,
                'rv_fake_status_id' => $rv_shipment_assign_agent->rv_fake_status_id,
                'remarks' => $rv_shipment_assign_agent->remarks,
                'call_to_id' => $rv_shipment_assign_agent->call_to_id,
                'assigned_by' => $rv_shipment_assign_agent->assigned_by,
                'assigned_to_type_id' => $rv_shipment_assign_agent->assigned_to_type_id,
            ];

            if ($updated_data['rv_state_id'] != 3)
            {
                //Remove Shipment from RV Shipment Ticket
                // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($data['shipment_id']));
                RvShipmentTicket::where('shipment_id', $data['shipment_id'])->delete();

            }

            $this->data_rv_shipment_assign_agent_details($updated_data);
        } else {
            return false;
        }
    }

    /**
     * Reattempt attempt the shipper admin agent send the notification rider.
     */
    protected function reattemptNotification($shipmentId)
    {
        // Retrieve the DeliveryNoteShipment with related DeliveryNote data
        $shipmentDeliveryNote = DeliveryNoteShipment::with('delivery_note:id,pending_status,rider_id')
            ->where('shipment_id', $shipmentId)
            ->latest()->first();

        // Check if the DeliveryNoteShipment record exists
        if ($shipmentDeliveryNote) {
            // Check if the pending_status is less than 1
            if ($shipmentDeliveryNote->delivery_note->pending_status < 1) {
                // Send notification
                NotificationsController::app_notification(
                    22, // Notification type ID
                    $shipmentDeliveryNote->delivery_note->rider_id, // Rider ID
                    2, // Notification category or type
                    $shipmentId // Shipment ID
                );
            }
        } else {
            // Handle the case where the shipment delivery note is not found
            // This might involve logging an error or returning an error response
            // For example:
            return response()->json(['status' => 0, 'success' => "Delivery note shipment not found for shipment ID: {$shipmentId}"]);

            // throw new Exception("Delivery note shipment not found for shipment ID: {$shipmentId}");
        }
    }

    function fakeStatusMarkedDeliveries($data)
    {
    
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $data->shipment_id)->latest('delivery_note_id')->first();
        if($delivery_note_shipment){
            $fakeStatusRemark = RvFakeStatus::find($data->rv_fake_status_id);
            $delivery_note_shipment->fake_status = 1;
            $delivery_note_shipment->admin_id = Auth::id();
            $delivery_note_shipment->remarks = $fakeStatusRemark->name;
            $delivery_note_shipment->fake_status_updated_at = Carbon::now();
            $delivery_note_shipment->save();
            // <ShipmentScanningJourneyController::add($data->shipment_id, 6, 1, Auth::id(), NULL, NULL, NULL, NULL, session('latitude'), session('longitude'), NULL);
            return true;
        }else{
            return false;
        }

    }

    static function rvshipmentticketInsert($shipmentId,$shipperStatusId,$shipperReasonStatusId,$shipperUserId,$callCount = 0){
        $rvData = [
            'shipment_id' => $shipmentId,
            'shipper_status_id' => $shipperStatusId,
            'status_reason_id' => $shipperReasonStatusId,
            'shipment_user_id' => $shipperUserId,
            'call_count' => $callCount
        ];
        dispatch(new ProcessRvShipmentTicket($rvData));
    }

    static function botCallingDataSet($shipmentId,$callCount = null){
        
        if (GlobalSettings::where(['type' => 'bot_call_enable_disable', 'setting_value' => 1])->exists()) {
            if (RvShipmentTicket::where('shipment_id', $shipmentId)->whereNull('deleted_at')->where('is_bot', 1)->exists()) {
                $shipment = Shipment::with(['user:id,name,brand_name'])->select('user_id', 'consignee_phone_number_1', 'consignee_name', 'tracking_number', 'amount')->find($shipmentId);
                if($callCount == 3){
                    Log::channel('botCallJobLog')->info('s ' . '31538 this is hit multiple time or not...!' );
                   return self::botCallingThirdDataSet($shipmentId);
                }
                $base_uri = 'https://cap.zong.com.pk:8444/vpbx-apis/roboCalls/outboundCall';
                RvShipmentTicket::where('shipment_id', $shipmentId)->update(['in_progress' => 1]);

            $final_phone = self::phoneNo($shipment->consignee_phone_number_1);
                $post = [
                    'vpbx_id' => '66bdfd18cb67f',
                    'caller_id' => $final_phone,
                    'tracking_number' => $shipment->tracking_number,
                    'cod_amount' => $shipment->amount,
                    'brand_name' => $shipment->user->name ?? $shipment->user->brand_name,
                    'customer_name' => $shipment->consignee_name,
                ];
                return ['post' => $post, 'base_uri' => $base_uri, 'user_id' => $shipment->user_id];
            } else {
                return null;
            }
        }
    }
    static function botCallingThirdDataSet($shipmentId){
    
        if (GlobalSettings::where(['type' => 'bot_call_enable_disable', 'setting_value' => 1])->exists()) {
            // status_reason
            $rvShipmentikcet = RvShipmentTicket::where('shipment_id', $shipmentId)->whereNull('deleted_at');
           
            if ($rvShipmentikcet->exists()) {
               
                $base_uri = 'https://trax-api.xnotify.ai/api/messages';
                $rvShipment = $rvShipmentikcet->first();
                $rvShipment->update(['in_progress' => 1,'is_bot'=>1]);
                $shipment = Shipment::with(['user:id,name,brand_name', 'destination_city:id,name'])->select('user_id', 'consignee_city_id', 'consignee_phone_number_1', 'consignee_name', 'tracking_number', 'amount')->find($shipmentId);
                $final_phone = self::phoneNo($shipment->consignee_phone_number_1);
                $post = [
                    'send_to' => $final_phone,
                    'tracking_id' => (string) $shipment->tracking_number,
                    'tns_no' => $shipment->tracking_number . '-' . uniqid(),
                    'amount' => $shipment->amount,
                    'reason_name' => $rvShipment?->status_reason?->name ?? '',
                    'shipper' => $shipment->user->name ?? $shipment->user->brand_name,
                    'city_name' => $shipment->destination_city->name,
                    'location' => $shipment->destination_city->name,
                    'item_type' => "Document"
                ];
                return ['post' => $post, 'base_uri' => $base_uri, 'user_id' => $shipment->user_id];
            } else {
                return null;
            }
        }
    }
    static function phoneNo($phoneNumber)
    {
        // Clean the phone number by removing non-alphanumeric characters    
        $cleaned_phone = preg_replace("/[^a-zA-Z0-9]+/", "", $phoneNumber);
        if (substr($cleaned_phone, 0, 2) == "00" && substr($cleaned_phone, 0, 4) != '0092') {
            if (substr($cleaned_phone, 0, 3) === "000") {
                // Remove one "0" by replacing "00" at the start with "0"
                $final_phone = '0' . substr($cleaned_phone, 3);
            } else {
                $final_phone = '0' . substr($cleaned_phone, 2);
            }
        }   // Remove one "0" by replacing "00" at the start with "0"
        elseif (substr($cleaned_phone, 0, 3) == '+92') {
            $final_phone = '0' . substr($cleaned_phone, 3);
        }
        //Replace 92 with 0
        else if (substr($cleaned_phone, 0, 2) == '92') {
            $final_phone = '0' . substr($cleaned_phone, 2);
        }
        //Replace 0092 with 0
        else if (substr($cleaned_phone, 0, 4) == '0092') {
            $final_phone = '0' . substr($cleaned_phone, 4);
        }
        //Addition of 0
        else if (substr($cleaned_phone, 0, 1) != '0') {
            $final_phone = '0' . $cleaned_phone;
        } else {
            // No leading "00", so leave the cleaned phone number as is
            $final_phone = $cleaned_phone;
        }
        return $final_phone;
    }
    static function inValidEntityEntertain($tracking_number){
        $client = new Client(['base_uri' =>  config('app.url') . '/api/admin/bot_submit_ticket', 'http_errors' => FALSE, 'connect_timeout' => 60, 'timeout' => 60, 'verify' => false]);
        $response = $client->post('', [
            'headers' => [
                'Authorization' => 'dXhTblBlMFZDYTJGbkR4MENTaWg5dWZFV250Z29leDZoaEU4MDJkT0xGZEx6d3IydGgwWHdRVjBIWDB666bb6c93c2dfe'
            ],
            'json' => [
                'tracking_number' => $tracking_number,
                'call_status' => 'InvalidNumber',
                'remarks'=> 'Due to invalid number',
                'bot_auto_return' => 1, // auto returm confirm in  case of invalid number
                'input' => 0,
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => date('Y-m-d H:i:s'),
                'call_finding' => 28, //invalid numbers
                'sender_name' => 'sonic'
            ]
        ]);
        $response = $response->getBody()->getContents();
        $response = json_decode($response);
        Log::channel('botCallJobLog')->info('s ' . 'Log after  respsoned' . json_encode($response));

    }

    protected function shipmentDifferentStatus($shipmentId,$request){
        $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $shipmentId)->latest()->first();
        $assigned_agent = RvShipmentAgent::where('agent_id', $request->admin_id)->first();
        $admin_agent = Admin::where('id', $request->admin_id)->first();
        $shipments_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
        $this->update_shipment_assign_agent($request, $assigned_agent, $admin_agent, $shipment_assign_agent);
        $status = $this->callHistoryRecord($shipment_assign_agent, $request);
        $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $status->id);
    }

    protected function callHistoryRecord($shipment_assign_agent, $request){
        $status = new RvAgentCallHistory();
        $status->shipment_id = $request->shipment_id;
        $status->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
        $status->call_finding_id = $request->rv_assign_agent_sub_status_id; //call finding reasons
        $status->call_to_id = 1; //Shipper or Consignee
        $status->remarks = $request->remarks;
        $status->updated_type_id = Auth::guard('agent')->check() ? 2 : 1;
        $status->updated_by_id = $request->admin_id;
        $status->call_status = $request->call_status;
        $status->updated_at = $request->end_date;
        $status->save();
        return $status;
    }
    public function createRvCronLog($message)
    {
        RvCronLog::create([
            'message' => $message,
        ]);
    }
    public function remarksNSAOSAJourneyRVR($deliveryNoteId,$userId){
        
        $globalSetting = GlobalSettings::where(['setting_value'=>1,'type'=>'spec_shipper_remarks_nsa_osa'])->first();
        $specShipperCheck = $globalSetting ? explode(',', $globalSetting->text) : null;
        
        if($specShipperCheck && in_array($userId, $specShipperCheck)){
            $delivery_note_data = DeliveryNote::find($deliveryNoteId);
            $remarks = $delivery_note_data->rider->area->reporting_location->address ?? null;
            return $remarks;
        }
    }

    public function conditionalRvSarUpdate($shipment,$statusReasonId,$type=2){
        if(Shipment::where('id', $shipment->id)->where('shipper_status_id', 12)->exists()){
            $rvshipments = RvShipmentAssignAgent::where('shipment_id', $shipment->id);
            Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 65, 'consignee_status_id' => 65]);   
            ShipmentsJourneyController::add($shipment->id, 65, 65, $statusReasonId,null, $shipment->user_id, Auth::id() ?? 346);
            $updateField = (($type == 1) ? ['agent_id' => 346, 'rv_state_id' => 2, 'rv_assign_agent_status_id' => 7, 'unresponsive_count' => 3, 'unresponsive_email_count' => 1, 'unresponsive_email_time' => date('Y-m-d h:i:s')] : ['agent_id' => 346, 'rv_state_id' => 3, 'rv_assign_agent_status_id' => 2]);
            RvShipmentAssignAgent::where('shipment_id', $shipment->id)
            // ->whereDate('created_at',$date)
            ->update($updateField);                                    
            NotificationsController::send(220, $rvshipments);
        }
    }

    
}