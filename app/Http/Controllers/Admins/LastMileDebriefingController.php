<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\AgentCallMonitoring;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\RiderDelivery;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class LastMileDebriefingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function supervisor_view()
    {
        return view('admin.debriefing.supervisor');
    }

    public function supervisor_list(Request $request)
    {
        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id',  'oc.name as hub', 'riders.name as rider', 'delivery_notes.total_cod_amount as amount', 'delivery_notes.received_cod_amount as pending_cash_collection', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count', 'delivery_notes.special_rider','delivery_notes.special_rider_name','delivery_notes.special_rider_phone','delivery_notes.delivered_shipments as delivered_shipments',DB::raw('(SELECT COUNT(d.id) FROM delivery_notes AS d INNER JOIN delivery_note_shipments AS dns ON d.id = dns.delivery_note_id WHERE dns.delivery_note_id = delivery_notes.id AND dns.status = 1) AS shipments_undelivered_count'), DB::raw('(SELECT COUNT(p.id) FROM delivery_notes AS p INNER JOIN delivery_note_shipments AS pdns ON p.id = pdns.delivery_note_id WHERE pdns.delivery_note_id = delivery_notes.id AND pdns.status = 0) AS shipments_pending_count')])
            ->where('delivery_notes.status', 0);


        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('pending_cash_collection', function($shipment){
                return number_format($shipment->pending_cash_collection);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->addColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_count . '</button></div><h4 class="warning">100%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('delivered_shipments_link', function($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->delivered_shipments . '</button></div><h4 class="success">'. round(($deliveries->delivered_shipments / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('pending_shipments_link', function($deliveries) {
                if ($deliveries->shipments_pending_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_pending_count . '</button></div><h4 class="success">'. round(($deliveries->shipments_pending_count / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->addColumn('undelivered_shipments_link', function($deliveries) {
                if ($deliveries->shipments_undelivered_count != 0) {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $deliveries->shipments_undelivered_count . '</button></div><h4 class="yellow">'. round(($deliveries->shipments_undelivered_count / $deliveries->shipments_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($rider) {
                if($rider->special_rider){
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                }else{
                    return $rider->rider;
                }
            });

        return $datatables->make(true);
    }

    public function agents_call_monitoring_view()
    {
        return view('admin.debriefing.agent_call_monitoring');
    }

    public function agents_call_monitoring_list ()
    {
        $data = AgentCallMonitoring::join('admins as agent','agent.id','=','agent_call_monitorings.agent_id')
            ->leftjoin('cities as hub','hub.id','=','agent.default_hub_id')
            ->select(['agent.id as agent_id','agent.name as agent_name','hub.name as hub'])
            ->groupBy('agent_id');

        $datatables = Datatables::of($data)
            ->addColumn('assigned_calls_excel', function($calls) {
               return AgentCallMonitoring::where('agent_id',$calls->agent_id)->count();
            })
            ->addColumn('completed_calls_excel', function($calls) {
                return AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',1]])->count();
            })
            ->addColumn('pending_calls_excel', function($calls) {
                return AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',0]])->count();
            })
            ->addColumn('assigned_calls', function($calls) {
                $count = AgentCallMonitoring::where('agent_id',$calls->agent_id)->count();
                if($count != 0)
                {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="warning">100%</h4>';

                return $count_cell;
                }
                else{
                    return 0;
                }
            })
            ->addColumn('completed_calls', function($calls) {
                $total_count =  AgentCallMonitoring::where('agent_id',$calls->agent_id)->count();
                $count =  AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',1]])->count();
                if($count != 0)
                {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="success">'. round(($count / $total_count) * 100, 2) .'%</h4>';
                return $count_cell;
                }
                else{
                    return 0;
                }
            })
            ->addColumn('pending_calls', function($calls) {
                $total_count =  AgentCallMonitoring::where('agent_id',$calls->agent_id)->count();
                $count =  AgentCallMonitoring::where([['agent_id',$calls->agent_id],['completed',0]])->count();
                if($count != 0)
                {
                    $count_cell = '<div><button class="btn btn-sm btn-outline-info align-middle mb-1">' . $count . '</button></div><h4 class="danger">'. round(($count / $total_count) * 100, 2) .'%</h4>';
                    return $count_cell;
                }
                else{
                    return 0;
                }
            });

         return $datatables->make(true);
    }

    public function caller_agent_view()
    {
        if(session('role_id') != 1 && session('role_id') != 18)
        {
            return back();
        }

        $calls = AgentCallMonitoring::where('agent_id',Auth::id())
            ->where('completed',0)->where('skip',0);
        if($calls->exists())
        {
            $data = $calls->first();
        }
        else{
            $calls = AgentCallMonitoring::where('agent_id',Auth::id())
                ->where('completed',0)->where('skip',1);

            if($calls->exists()) {
                $data = $calls->first();
            }
            else{
                return view('admin.debriefing.caller_agent')->with(['data'=>false]);
            }
        }
        $where = array(7, 8, 9, 15, 18, 56);
        $statuses = ShipmentStatus::whereIn('id', $where)->select('id','name')->where('status', 1)->get();
        $shipment = Shipment::find($data->shipment_id);
        $delivery_note = DeliveryNote::find($data->delivery_note_id);
        $total_calls = AgentCallMonitoring::where('agent_id',Auth::id())->count();
        $completed_calls = AgentCallMonitoring::where('agent_id',Auth::id())->where('completed',1)->count();
        $pending_calls = AgentCallMonitoring::where('agent_id',Auth::id())->where('completed',0)->count();
        return view('admin.debriefing.caller_agent')->with(['data'=>true,'statuses'=>$statuses,'shipment'=>$shipment,'delivery_note'=>$delivery_note,'total_calls'=>$total_calls,'completed_calls'=>$completed_calls,'pending_calls'=>$pending_calls,'call'=>$data]);
    }

    public function caller_agent_skip(Request $request)
    {
        $data = AgentCallMonitoring::find($request->id);
        if($data)
        {
            $data->skip = 1;
            $data->update();
            return response()->json(['status'=>1]);
        }
    }

    public function caller_agent_next(Request $request)
    {
        $data = AgentCallMonitoring::find($request->call_id);
        if($data){
            $delivery_note_id = $data->delivery_note_id;
            $delivery_note = DeliveryNote::find($delivery_note_id);
            $zero_cod_shipments = array();
            $shipment = $data->shipment_id;
            $invalid_reason_shipments = array();
            if ($delivery_note) {

                if ($delivery_note->status == 1) {
                    return redirect(route('admin.dashboard.index'))->with('error', 'Delivery note already verified!');
                }
                $verification = 1;
                $current_time = Carbon::now();

                $dispute_shipments = array();
                $delivered_status_array = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 45, 46);
                $return_status_array = array(21, 22, 23, 24, 25, 44, 47, 48);
                if ($delivery_note_id != '') {
                    $shipment_details = Shipment::find($shipment);
                    if (!$shipment_details) {
                        return redirect()->back()->with('error', 'Shipment not found!');
                    }
                    $in_new_delivery_note = DeliveryNoteShipment::where('delivery_note_id', '>', $delivery_note_id)->where('shipment_id', $shipment)->exists();

                    $shipper_status_id = NULL;
                    $status_reason_id = NULL;
                    $shipment_journey_remarks = NULL;
                    $shipment_journey_remarks = $request->remarks;

                    $shipper_status_id = $request->status;
                    $status_reason_id = $request->reason;

                    $open_box_shipment = "open_box.$shipment";
                    $confirm_location_shipment = "confirm_location.$shipment";
                    if ($shipment_details->booking_type_id == 5 &&  $shipper_status_id == 12) {
                        return redirect()->back()->with('error', 'Reverse Shipment can not updated as return confirmation pending!');
                    }

                    if (!$shipper_status_id) {
                        return redirect()->back()->with('error', 'Shipment Status not selected!');
                    }

                    if (!$status_reason_id) {
                        return redirect()->back()->with('error', 'Shipment Reason not selected!');
                    }

                    if ($shipper_status_id != 14) {
                        if (in_array($status_reason_id, [3, 4, 12, 34, 50])) {
                            $phone_number = $shipment_details->consignee_phone_number_1;
                            $previous_delivered_shipments = Shipment::where(function ($query) use ($phone_number) {
                                $query->where('consignee_phone_number_1', $phone_number)
                                    ->orWhere('consignee_phone_number_2', $phone_number);
                            })
                                ->where('shipper_status_id', DB::raw(14));
                            if ($previous_delivered_shipments->exists()) {
                                return redirect()->back()->with('error', 'Shipment Reason invalid!');
                            }
                        }
                    }

                    if ($shipper_status_id == 14) {
                        if ($request->has($confirm_location_shipment)) {
                            $rider_delivery = RiderDelivery::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment)->first();
                            $coordinates_shipment = Shipment::find($shipment);
                            $consignee_phone_number_1 = $coordinates_shipment->consignee_phone_number_1;
                            $consignee_phone_number_2 = $coordinates_shipment->consignee_phone_number_2;
                            $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
                                $sub_query->where('phone_number', $consignee_phone_number_1)
                                    ->orwhere('phone_number', $consignee_phone_number_2);
                            })->where('address', $coordinates_shipment->consignee_address);
                            if ($coordinates->exists()) {
                                $coordinates = $coordinates->latest()->first();
                                $coordinates->lat = $rider_delivery->actual_location_latitude;
                                $coordinates->long = $rider_delivery->actual_location_longitude;
                                $coordinates->save();
                            } else {
                                $coordinates = new ConsigneeLocation();
                                $coordinates->phone_number = $consignee_phone_number_1;
                                $coordinates->address = $coordinates_shipment->consignee_address;
                                $coordinates->lat = $rider_delivery->actual_location_latitude;
                                $coordinates->long = $rider_delivery->actual_location_longitude;
                                $coordinates->save();
                            }
                            $existing_shipment_coordinates = ConsigneeShipmentLocation::where('shipment_id', $shipment);
                            if ($existing_shipment_coordinates->exists()) {
                                $existing_shipment_coordinates = $existing_shipment_coordinates->first();
                                $existing_shipment_coordinates->current_location_id = $coordinates->id;
                                $existing_shipment_coordinates->save();
                            } else {
                                $existing_shipment_coordinates = new ConsigneeShipmentLocation();
                                $existing_shipment_coordinates->shipment_id = $shipment;
                                $existing_shipment_coordinates->previous_location_id = null;
                                $existing_shipment_coordinates->current_location_id = $coordinates->id;
                                $existing_shipment_coordinates->save();
                            }
                        } else {
                            $existing_shipment_coordinates = ConsigneeShipmentLocation::where('shipment_id', $shipment);
                            if ($existing_shipment_coordinates->exists()) {
                                $existing_shipment_coordinates = $existing_shipment_coordinates->first();
                                if ($existing_shipment_coordinates->previous_location_id != null) {
                                    $existing_shipment_coordinates->current_location_id = $existing_shipment_coordinates->previous_location_id;
                                    $existing_shipment_coordinates->save();
                                }
                            }
                        }
                    }

                    if (!$in_new_delivery_note) {
                        if (!in_array($shipment->shipper_status_id, $return_status_array)) {

                            if (!in_array($shipment->shipper_status_id, $delivered_status_array)) {

                                $verify = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment)->first();

                                $verify->call_verification = 1;
                                $verify->save();

                                if ($shipper_status_id != null) {

                                    //$shipment = Shipment::where('id', $shipment)->first();
                                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->latest()->first();
                                    if ($shipment->shipper_status_id != $shipper_status_id) {
                                        if ($shipper_status_id == 7 || $shipper_status_id == 18) {
                                            ShipmentsJourneyController::add($shipment, $shipper_status_id, NULL, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id]);
                                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                        } else if ($shipper_status_id == 20) {
                                            $parcel = Shipment::find($shipment);

                                            if (!$parcel->packaging_material_request) {
                                                if ($parcel->shipper_status_id != 12) {
                                                    ShipmentsJourneyController::add($shipment, 12, 12, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                }
                                                ShipmentsJourneyController::add($shipment, 20, 20, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                                                if ($verification == 1) {
                                                    NotificationsController::send(15, 0, $shipment);
                                                    NotificationsController::send(16, 0, $shipment);

                                                    if ($parcel->booking_type_id != 4) {
                                                        ShipmentChargesController::return($shipment);

                                                        AdminFinanceController::add_payment($shipment, 1);
                                                    } else {
                                                        ShipmentChargesController::walk_in_return($shipment);

                                                        $parcel->walk_in_status = 2;

                                                        $parcel->save();

                                                        AdminFinanceController::done_payment($shipment, 1);
                                                    }
                                                }

                                            } else {
                                                if ($parcel->packaging_material_charges != null) {
                                                    ShipmentsJourneyController::add($shipment, 17, 17, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                    Shipment::where('id', $shipment)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);

                                                    if ($verification == 1) {
                                                        NotificationsController::send(15, 0, $shipment);
                                                        NotificationsController::send(16, 0, $shipment);
                                                    }
                                                }

                                            }
                                        } else if ($shipper_status_id == 56) {
                                            $parcel = Shipment::find($shipment);
                                            if ($parcel->booking_type_id == 2) {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 56, 'consignee_status_id' => 56]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                            }

                                        } else if (in_array($shipper_status_id, $delivered_status_array)) {
                                            $parcel = Shipment::where('id', $shipment)->first();
                                            if ($parcel->booking_type_id == 2) {
//                                                ShipmentsJourneyController::add($shipment, 30, 30, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 30, 'consignee_status_id' => 30]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 2]);
                                            } elseif ($parcel->booking_type_id == 3) {
                                                if ($parcel->package_type == 0) {
//                                                    ShipmentsJourneyController::add($shipment, 36, 36, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 3]);
                                                } else {
//                                                    ShipmentsJourneyController::add($shipment, 36, 36, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                                                }
                                            } elseif ($parcel->booking_type_id == 4) {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                if ($parcel->charges_mode_id == 1) {
                                                    Shipment::where('id', $shipment)->update(['received_amount' => 0, 'shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 7]);
                                                } else {
                                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                                                }
                                            } else {
//                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                                            }
                                            if ($verification == 1) {
                                                if (in_array($shipper_status_id, [14, 16, 30, 36, 37])) {
                                                    $parcel = Shipment::find($shipment);

                                                    if ($parcel->booking_type_id == 2) {
                                                        ShipmentChargesController::replacement($shipment);
                                                    } else if ($parcel->booking_type_id == 3) {
                                                        ShipmentChargesController::try_and_buy($shipment);
                                                    }

                                                    if ($parcel->booking_type_id != 4) {
                                                        AdminFinanceController::add_payment($shipment, 0);
                                                    } else {
                                                        AdminFinanceController::done_payment($shipment, 0);
                                                    }


                                                }
                                            }
                                        } else {
                                            if ($shipment->packaging_material_request == 0) {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                            } else if ($shipment->packaging_material_charges != '' && $shipment->packaging_material_request == 1) {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                            } else if ($shipment->packaging_material_charges == null && $shipment->packaging_material_request == 1) {
                                                if ($shipper_status_id != 12) {
                                                    ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                    Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                                }

                                            }

                                        }
                                        $dispute_shipments[] = $shipment;
                                    } else if (($shipment->shipper_status_id == $shipper_status_id) && ($journey->status_reason_id != $status_reason_id)) {
                                        if ($verification == 0) {

                                            $journey->status_reason_id = $status_reason_id;
                                            $journey->remarks = $shipment_journey_remarks;
                                            $journey->save();
                                        } else {
                                            ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                        }

                                    } else if (($shipment->shipper_status_id == $shipper_status_id) && ($journey->status_reason_id == $status_reason_id) && ($shipment_journey_remarks != $journey->remarks)) {
                                        if ($verification == 0) {
                                            $journey->remarks = $shipment_journey_remarks;
                                            $journey->save();
                                        } else {

                                            ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                        }


                                    } else {
                                        if ($verification == 1) {

                                            if (in_array($shipment->shipper_status_id, [14, 16, 30, 36, 37])) {
                                                $parcel = Shipment::find($shipment);

                                                if ($parcel->booking_type_id == 2) {
                                                    ShipmentChargesController::replacement($shipment);
                                                } else if ($parcel->booking_type_id == 3) {
                                                    ShipmentChargesController::try_and_buy($shipment);
                                                }

                                                if ($parcel->booking_type_id != 4) {
                                                    if (($parcel->packaging_material_request == 1 && $parcel->packaging_material_charges != '') || $parcel->packaging_material_request == 0) {
                                                        AdminFinanceController::add_payment($shipment, 0);
                                                    }
                                                } else {
                                                    if (($parcel->packaging_material_request == 1 && $parcel->amount != 0) || $parcel->packaging_material_request == 0) {
                                                        AdminFinanceController::done_payment($shipment, 0);
                                                    }
                                                }
//                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            } else {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, $status_reason_id, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            }
                                        }
                                    }
                                }//main if condition

                            } else {

                                $parcel = Shipment::find($shipment);
                                if ($verification == 1) {
                                    if ($parcel->booking_type_id == 2) {
                                        ShipmentChargesController::replacement($shipment);
                                    } else if ($parcel->booking_type_id == 3) {
                                        ShipmentChargesController::try_and_buy($shipment);
                                    }

                                    if ($parcel->booking_type_id != 4) {
                                        if (($parcel->packaging_material_request == 1 && $parcel->packaging_material_charges != '') || $parcel->packaging_material_request == 0) {
                                            AdminFinanceController::add_payment($shipment, 0);
                                        }
                                    } else {
                                        AdminFinanceController::done_payment($shipment, 0);
                                    }

                                    if (!in_array($shipment->shipper_status_id, $delivered_status_array)) {
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $parcel->id)->whereNotIn('shipper_status_id', [21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 44, 45, 46, 47, 48])->where('reference_1_id', $delivery_note_id)->latest()->first();

                                        if ($shipment_journey) {
                                            $received_or_refused_by = $shipment_journey->received_or_refused_by;
                                            ShipmentsJourneyController::add($shipment, $shipment_journey->shipper_status_id, $shipment_journey->consignee_status_id, $shipment_journey->status_reason_id, $shipment_journey->remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification, $received_or_refused_by);
                                        } else {
                                            ShipmentsJourneyController::add($shipment, $shipment->shipper_status_id, $shipment->consignee_status_id, NULL, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                        }

                                    }

                                    if ($parcel->amount == 0) {
                                        $zero_cod_shipments[] = $parcel->id;
                                    }

                                }


                            }
                        }
//
                    } else {
                        if ($request->has('status') && $shipper_status_id != null) {
                            if ($shipment->shipper_status_id != $shipper_status_id) {
                                $dispute_shipments[] = $shipment;
                            }
                        }
                    }

                    if ($verification == 1) {
                        $packaging_shipment = Shipment::find($shipment);
                        if ($packaging_shipment->shipper_status_id == 14) {
                            if ($packaging_shipment->packaging_material_request == 1) {
                                $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $packaging_shipment->tracking_number)->where('status_id', 3)->first();
                                if ($packaging_material_shipment != null) {
                                    $packaging_material_shipment->status_id = 4;
                                    $packaging_material_shipment->save();

                                    $packaging_request_history = new PackagingMaterialRequestHistory();
                                    $packaging_request_history->packaging_material_request_id = $packaging_material_shipment->id;
                                    $packaging_request_history->status = 4;
                                    $packaging_request_history->updated_by = Auth::id();
                                    $packaging_request_history->save();
                                }
                                $warehouse_stock_request = WarehouseStockRequest::where('tracking_number', $packaging_shipment->tracking_number);
                                if ($warehouse_stock_request->exists()) {
                                    $warehouse_stock_request = $warehouse_stock_request->first();
                                    $warehouse_stock_request->status_id = 4;
                                    $warehouse_stock_request->save();

                                    $warehouse_stock_request_history = new WarehouseStockRequestHistory();
                                    $warehouse_stock_request_history->warehouse_stock_request_id = $warehouse_stock_request->id;
                                    $warehouse_stock_request_history->status = 4;
                                    $warehouse_stock_request_history->updated_by = Auth::id();
                                    $warehouse_stock_request_history->save();
                                    foreach ($warehouse_stock_request->stock_request_details as $detail) {
                                        if (WarehouseStock::where('warehouse_id', $warehouse_stock_request->requested_by)->where('type_id', $detail->type_id)->where('type_size_id', $detail->size_id)->exists()) {
                                            $receiver_stock = WarehouseStock::where('warehouse_id', $warehouse_stock_request->requested_by)->where('type_id', $detail->type_id)->where('type_size_id', $detail->size_id)->first();
                                            $receiver_stock->stock += $detail->quantity;
                                            $receiver_stock->save();
                                        } else {

                                            $receiver_stock = new WarehouseStock();
                                            $receiver_stock->warehouse_id = $warehouse_stock_request->requested_by;
                                            $receiver_stock->type_id = $detail->type_id;
                                            $receiver_stock->type_size_id = $detail->size_id;
                                            $receiver_stock->stock = $detail->quantity;
                                            $receiver_stock->save();

                                        }
                                    }
                                }
                            }
                        }
                    }


                    if ($verification == 1) {
                        if (!empty($dispute_shipments)) {
                            DisputeController::add_delivery_wrong_status_dispute($delivery_note_id, $dispute_shipments);
                        }
                        $dncc_status = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38);
                        $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('status', '>', 1)->whereNotIn('status', [8, 10, 11])->select('shipment_id')->get();
                        $dncc_amount = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->where(function ($query) {
                            $query->where(function ($sub_query) {
                                $sub_query->where('booking_type_id', '!=', 4);
                            })
                                ->orWhere(function ($sub_query) {
                                    $sub_query->where('booking_type_id', '=', 4)
                                        ->where('charges_mode_id', '=', 2);
                                });
                        })->sum('received_amount');
                        $delivered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->count();

                        DeliveryNote::where('id', $delivery_note_id)->update(['delivered_shipments' => $delivered_shipments, 'verified_by' => Auth::id(), 'received_cod_amount' => $dncc_amount, 'status' => 1, 'last_updated_at' => $current_time, 'status_verified_at' => $current_time]);


                        NotificationsController::send(13, $delivery_note_id);
                        NotificationsController::send(14, $delivery_note_id);

                        if (!empty($zero_cod_shipments)) {
                            foreach ($zero_cod_shipments as $shipment_id) {
                                NotificationsController::send(35, $shipment_id);
                            }
                        }

                        $delivery_note_shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('status', '>', 1)->whereNotIn('status', [8, 10, 11])->pluck('shipment_id')->toArray();
                        if (count($delivery_note_shipment_ids) > 0) {
                            foreach ($delivery_note_shipment_ids as $delivery_note_shipment_id) {
                                $shipment = Shipment::find($delivery_note_shipment_id);
                                if ($shipment->user_id == 3324) {
                                    NotificationsController::send(104, $delivery_note_shipment_id);
                                }
                            }
                        }
                        if ($delivery_note->updated_by == NULL) {
                            $delivery_note->updated_by = Auth::id();
                            $delivery_note->save();
                        }

                        $data->completed = 1;
                        $data->update();

                        return redirect()->back()->with('success', 'Delivery Note verified and updated successfully!');

                    }
                } else {
                    return redirect()->back()->with('error', 'Delivery note not found!');
                }
            } else {
                return redirect()->back()->with('error', 'Shipments not found!');
            }

        }
        else{
            return redirect()->back()->with('error', 'No Data found!');
        }

    }
}
