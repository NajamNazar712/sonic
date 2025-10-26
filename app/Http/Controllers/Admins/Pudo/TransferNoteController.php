<?php

namespace App\Http\Controllers\Admins\Pudo;

use App\Helpers\PayfastApiCall;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\CheckDisputeShipmentsController;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\Rider;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\WarehouseStockRequest;
use App\Models\PudoDeliverShipment;
use App\Models\RiderTransferNoteRequest;
use App\Models\RiderTransferNoteRequestShipment;
use App\Models\TransferNote;
use App\Models\TransferNoteShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class TransferNoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }


    public function get_shipment_details(Request $request)
    {
        $rules = [
            'tracking' => ['required', 'exists:shipments,tracking_number']
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return ['status' => 1, 'error' => 'Invalid Tracking Number'];
        } else {

            $flag = true;
            $pending_status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
            if ($request->tracking != '') {
                $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id', $pending_status);
                $remarks = '';
                $status = '';
                $rider_name = '';
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    $pudo_delivery_shipment = PudoDeliverShipment::where('shipment_id',$shipment->id)->first();
                    if(!$pudo_delivery_shipment){
                        return ['status' => 1, 'error' => 'Following shipment not belongs to a PUDO shipment'];
                    }

                    if ($request->operation_rider_type_id == 2) {
                        $latestAgentAssignment = RvShipmentAssignAgent::where('shipment_id', $shipment->id)
                            ->latest()
                            ->first();

                        if (optional($latestAgentAssignment)->rv_assign_agent_status_id == 2 && $latestAgentAssignment->agent_id == 4620) {
                            return ['status' => 1, 'error' => 'Shipment status is re-attempt for Hold-in-operation category'];
                        }
                    }

                    /******** COMMENT FOR PRODUCTION AS PER REVERT TICKET(6263)-  CAN BE REOPEN AGAIN (FROM ZOHAIB TARIQ) ********/
                    // $shipment_status_id = $shipment->shipper_status_id ?? NULL;
                    // $rider = Rider::where('id', $request->rider_id);
                    // if($rider->exists()){
                    //     $operation_rider_id = $rider->first()->operation_rider_id;
                    // }
                    // else{
                    //     return ['status' => 1, 'error' => 'Rider not found!'];
                    // }
                    // if ($operation_rider_id == 2 && ($shipment_status_id == NULL || $shipment_status_id == 13)) {
                    //     return ['status' => 1, 'error' => 'Shipment cannot be added because it is on Re-Attempt Status'];
                    // } else {
                    $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                    if (!$dispute_check) {
                        return ['status' => 1, 'error' => 'Shipment is in Dispute! For further assistance, please contact QA (CX)'];
                    }
                    if ($shipment->shipment_detail()->exists()) {
                        if ($shipment->shipment_detail->is_open == 1) {
                            $is_open_box = 1;
                        } else {
                            $is_open_box = 0;
                        }
                    } else {
                        $is_open_box = 0;
                    }
                    $on_hold_shipment = ShipmentOnHold::where('shipment_id', $shipment->id)->where('status', 1);
                    if ($on_hold_shipment->exists()) {
                        if (!in_array(Auth::id(), [10, 288, 423])) {
                            $on_hold_shipment = $on_hold_shipment->first();
                            $delivery_date = Carbon::parse($on_hold_shipment->delivery_date);
                            $today = Carbon::today();
                            if ($delivery_date > $today) {
                                $delivery_date = $delivery_date->toFormattedDateString();
                                return ['status' => 1, 'error' => 'Shipment is marked as On-Hold until ' . $delivery_date];
                            }
                        }
                    }
                    if ($shipment->packaging_material_request == 1) {
                        $packaging_material_request = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                        if ($packaging_material_request != null) {
                            if ($packaging_material_request->status_id != 3) {
                                return ['status' => 1, 'error' => 'Packaging Material Request is not dispatched yet!'];
                            }
                        }
                        $packaging_material_request_stock = WarehouseStockRequest::where('tracking_number', $shipment->tracking_number)->first();
                        if ($packaging_material_request_stock != null) {
                            if ($packaging_material_request_stock->status_id != 3) {
                                return ['status' => 1, 'error' => 'Warehouse Stock Request is not dispatched yet!'];
                            }
                        }
                    }

                    $admin_hub = City::find($shipment->consignee_city->hub_id)->id;
                    if (session('role_id') == 1 || in_array($admin_hub, session('hubs'))) {
                        $old_delivery_note_id = TransferNote::join('transfer_note_shipments', 'transfer_note.id', '=', 'transfer_note_shipments.transfer_note_id')
                            ->where('transfer_note_shipments.shipment_id', $shipment->id)
                            ->where('transfer_note.status_id', '!=', 3)
                            ->orderByDesc('transfer_note_shipments.transfer_note_id')
                            ->value('transfer_note_shipments.transfer_note_id');
                        if ($old_delivery_note_id) {
                            $delivery_note_rider = TransferNote::with('rider')->find($old_delivery_note_id);
                            $rider_name = $delivery_note_rider?->rider?->name;
                            $is_updateable = TransferNoteShipment::where('transfer_note_id', $old_delivery_note_id)
                                ->where('status_id', 1)
                                ->count();
                        } else {
                            $is_updateable = 0;
                        }

                        if ($is_updateable == 0) {
                            if (($shipment->consignee_city->hub_id != $shipment->pickup_address->city->hub_id) && $shipment->shipper_status_id == 2) {
                                return ['status' => 1, 'error' => 'Cargo not arrived at destination center!'];
                            } else if ($shipment->shipper_status_id == 49) {
                                $misroute_history = MisroutedHistory::where('shipment_id', $shipment->id);
                                if ($misroute_history->exists()) {
                                    $misroute_history = $misroute_history->latest()->first();
                                    if ($misroute_history->old_consignee_city->hub_id != $misroute_history->new_consignee_city->hub_id) {
                                        return ['status' => 1, 'error' => 'Shipment needs to be moved through cargo!'];
                                    }
                                } else {
                                    return ['status' => 1, 'error' => 'Shipment Not found!'];
                                }
                            } else if ($shipment->shipper_status_id == 55) {
                                $request_history = InterceptReBookRequestHistory::where('shipment_id', $shipment->id);
                                if ($request_history->exists()) {
                                    $request_history = $request_history->first();
                                    if ($request_history->old_consignee_city->hub_id != $request_history->new_consignee_city->hub_id) {
                                        return ['status' => 1, 'error' => 'Shipment needs to be moved through cargo!'];
                                    }
                                } else {
                                    return ['status' => 1, 'error' => 'Shipment Not found!'];
                                }
                            }


                            if ($request->has('hub_id')) {

                                $hub_id = $shipment->consignee_city->hub_id;
                                // rider assigned hub setting
//                                $rider_assigned_hub = RiderAssignedHubForDeliveryNote::where('rider_id', $request->rider_id);
//                                if ($rider_assigned_hub->exists()) {
//                                    $rider_assigned_hub = $rider_assigned_hub->first();
//                                    $rider_assigned_hubs = $rider_assigned_hub->hubs;
//                                    $rider_assigned_hubs = explode(',', $rider_assigned_hubs);
//
//                                    if (in_array($hub_id, $rider_assigned_hubs)) {
//                                        $flag = true;
//                                    } elseif ($request->hub_id == $hub_id) {
//                                        $flag = true;
//                                    } else {
//                                        $flag = false;
//                                    }
//                                }
//                                elseif ($request->hub_id == $hub_id) {
//                                    $flag = true;
//                                }
//                                else
//                                {
//                                    $flag = false;
//                                }
                                // rider assigned hub setting end

                                if ($request->hub_id == $hub_id) {
                                    if (!$request->has('pieces_confirm')) {
                                        if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                            $details = array();
                                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                            $details['id'] = $shipment->id;
                                            $details['tracking_number'] = $shipment->tracking_number;
                                            $details['pieces_count'] = $shipment->pieces;
                                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                        }
                                    }
                                    $destination = $shipment->consignee_city->name;
                                    $hub = City::find($shipment->consignee_city->hub_id)->name;
                                    $service = $shipment->booking_type->booking_type;
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                    if ($shipment_journey->exists()) {
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->orderBy('id', 'DESC')->first();

                                        $remarks = ($shipment_journey->remarks != '') ? $shipment_journey->remarks : ' - ';
                                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                        if ($status_id != '') {
                                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                            $status = $status_name->name;
                                        } else {
                                            $status = ' - ';
                                        }
                                    }
                                    $class = null;
                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                        $class = 'complaint_row';
                                    }
                                    ShipmentScanningJourneyController::add($shipment->id ,34,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL, $request->action);
                                    $consolidation_details = self::check_consolidation($shipment->id);
                                    $consolidation_flag = FALSE;

                                    if ($consolidation_details) {
                                        $consolidation_flag = TRUE;
                                    }

                                    $intercept = false;
                                    if (InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->exists()) {
                                        $intercept = true;
                                    }
                                    $amount_check = false;
                                    $amount_log = ChangeShipmentAmountLog::where('shipment_id', $shipment->id);
                                    if ($amount_log->exists()) {
                                        $amount_log = $amount_log->first();
                                        $amount_check = true;
                                    }
                                    $crm_request = array();
                                    if (($intercept == true && ($shipment->intercept_history->old_amount != $shipment->intercept_history->new_amount)) || ($amount_check == true && ($amount_log->old_amount != $amount_log->new_amount))) {
                                        if ($amount_check) {
                                            $crm_request['cod_change'] = $amount_log->new_amount;
                                        } else {
                                            $crm_request['cod_change'] = $shipment->intercept_history->new_amount;
                                        }
                                    } else {
                                        $crm_request['cod_change'] = null;
                                    }
                                    if (($intercept == true && ($shipment->intercept_history->old_consignee_address != $shipment->intercept_history->new_consignee_address))) {
                                        $crm_request['address_change'] = $shipment->intercept_history->new_consignee_address;
                                    } else {
                                        $crm_request['address_change'] = null;
                                    }
                                    if (($intercept == true && ($shipment->intercept_history->old_consignee_phone_number_1 != $shipment->intercept_history->new_consignee_phone_number_1))) {
                                        $crm_request['phone_one_change'] = $shipment->intercept_history->new_consignee_phone_number_1;
                                    } else {
                                        $crm_request['phone_one_change'] = null;
                                    }
                                    if ($shipment->payment_mode_id == 2) {
                                        $ccd_shipment = 1;
                                    } else {
                                        $ccd_shipment = 0;
                                    }
                                    return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'rider_name' => $rider_name, 'remarks' => $remarks, 'class' => $class, 'consolidation_flag' => $consolidation_flag, 'consolidation_details' => $consolidation_details, 'crm_request' => $crm_request, 'is_open_box' => $is_open_box, 'ccd_shipment' => $ccd_shipment]);
                                } else {
                                    return ['status' => 1, 'error' => 'Different hub, Select shipments from same hub!', 'hub_old' => $request->hub_id, 'newHub' => $hub_id];
                                }
                            } else {
                                if (!$request->has('pieces_confirm')) {
                                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                    }
                                }
                                $destination = $shipment->consignee_city->name;
                                $hub = City::find($shipment->consignee_city->hub_id)->id;
                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = $shipment_journey->select('shipper_status_id', 'remarks')->orderBy('id', 'DESC')->first();

                                    $remarks = ($shipment_journey->remarks != '') ? $shipment_journey->remarks : ' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                $class = null;
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                                    $class = 'complaint_row';
                                }
                                ShipmentScanningJourneyController::add($shipment->id ,34,1,Auth::id(),NULL,NULL,NULL,NULL, session('latitude'), session('longitude'), NULL, $request->action);
                                $consolidation_details = self::check_consolidation($shipment->id);

                                $consolidation_flag = FALSE;

                                if ($consolidation_details) {
                                    $consolidation_flag = TRUE;
                                }

                                $intercept = false;
                                if (InterceptReBookRequestHistory::where('shipment_id', $shipment->id)->exists()) {
                                    $intercept = true;
                                }
                                $amount_check = false;
                                $amount_log = ChangeShipmentAmountLog::where('shipment_id', $shipment->id);
                                if ($amount_log->exists()) {
                                    $amount_log = $amount_log->first();
                                    $amount_check = true;
                                }
                                $crm_request = array();
                                if (($intercept == true && ($shipment->intercept_history->old_amount != $shipment->intercept_history->new_amount)) || ($amount_check == true && ($amount_log->old_amount != $amount_log->new_amount))) {
                                    if ($intercept == true) {
                                        $crm_request['cod_change'] = $shipment->intercept_history->new_amount;
                                    } else {
                                        $crm_request['cod_change'] = $amount_log->new_amount;
                                    }
                                } else {
                                    $crm_request['cod_change'] = null;
                                }
                                if (($intercept == true && ($shipment->intercept_history->old_consignee_address != $shipment->intercept_history->new_consignee_address))) {
                                    $crm_request['address_change'] = $shipment->intercept_history->new_consignee_address;
                                } else {
                                    $crm_request['address_change'] = null;
                                }
                                if (($intercept == true && ($shipment->intercept_history->old_consignee_phone_number_1 != $shipment->intercept_history->new_consignee_phone_number_1))) {
                                    $crm_request['phone_one_change'] = $shipment->intercept_history->new_consignee_phone_number_1;
                                } else {
                                    $crm_request['phone_one_change'] = null;
                                }
                                if ($shipment->payment_mode_id == 2) {
                                    $ccd_shipment = 1;
                                } else {
                                    $ccd_shipment = 0;
                                }
                                return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'rider_name' => $rider_name, 'remarks' => $remarks, 'class' => $class, 'consolidation_flag' => $consolidation_flag, 'consolidation_details' => $consolidation_details, 'crm_request' => $crm_request, 'is_open_box' => $is_open_box, 'ccd_shipment' => $ccd_shipment]);
                            }
                        } else {
                            return ['status' => 1, 'error' => 'This Shipment is already in an unverified transfer note!'];
                        }
                    }
                    else {
                        return ['status' => 1, 'error' => 'This Shipment doesn\'t belongs to your assigned hubs!'];
                    }
                    // }//Commented else
                }
                else {
                    return ['status' => 1, 'error' => 'This Shipment is not ready for transfer yet or already in transfer note, please check tracking!'];
                }
            }
        }
    }

    public function rider_request_note_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 610);
        $riders = Rider::select('id', 'name', 'trax_id')->where('status', 1)->where('blacklist', 0)->get();

        return view('admin.pudo.transfer_note.rider_request.index')->with(['riders' => $riders]);
    }

    public function rider_request_note_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 611);
        }

        $delivery_note_requests = RiderTransferNoteRequest::join('riders as r', 'r.id', '=', 'rider_transfer_note_requests.rider_id')
            ->join('cities as c', 'c.id', '=', 'rider_transfer_note_requests.hub_id')
//            ->join('routes as ro', 'ro.id', '=', 'rider_transfer_note_requests.route_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'rider_transfer_note_requests.updated_by')
            ->leftjoin('city_areas as ca', 'ca.id', '=', 'r.id')
            ->join('rider_types', 'rider_types.id', '=', 'r.rider_type_id')
            ->join('zones as z', 'c.zone_id', '=', 'z.id')
            ->whereDate('rider_transfer_note_requests.created_at', Carbon::today())
            ->where('rider_transfer_note_requests.status_id', 1)
            ->select('rider_transfer_note_requests.id as id', 'rider_transfer_note_requests.id as request_note_id', 'rider_transfer_note_requests.created_at as date', 'r.name as rider_name', 'c.name as hub', 'rider_transfer_note_requests.total_cod_amount as amount', 'rider_transfer_note_requests.shipments_count as shipments_count', 'rider_transfer_note_requests.shipments_count as shipments_count_link', 'z.name as zone_name', 'r.operation_rider_id', 'r.rider_type_id', 'rider_types.name as rt', 'ad.name as admin_name', 'rider_transfer_note_requests.updated_at as updated_at', 'ca.name as area', 'r.trax_id as rider_trax_id')
            ->orderBy('rider_transfer_note_requests.id', 'DESC');



        if (session('role_id') != 1) {
            $delivery_note_requests->whereIn('rider_transfer_note_requests.hub_id', session('hubs'));
        }

        if ($rider_trax_id = $request->get('rider_trax_id')) {
            $delivery_note_requests->where('rider_transfer_note_requests.rider_id', $rider_trax_id);
        }

        if ($consignee_phone = $request->get('consignee_phone') || $tracking_numbers = $request->get('tracking_numbers')) {
            $delivery_note_requests->leftjoin('rider_transfer_note_request_shipments as rdnrs', 'rdnrs.request_note_id', '=', 'rider_transfer_note_requests.id')
                ->leftjoin('shipments as s', 's.id', '=', 'rdnrs.shipment_id');

            if ($consignee_phone = $request->get('consignee_phone')) {
                $delivery_note_requests->where(function ($query) use ($consignee_phone) {
                    $query->where('s.consignee_phone_number_1', $consignee_phone)
                        ->orWhere('s.consignee_phone_number_2', $consignee_phone);
                });

            }

            if ($tracking_numbers = $request->get('tracking_numbers')) {
                $delivery_note_requests->whereIn('s.tracking_number', explode(',', $tracking_numbers))->groupBy('rider_transfer_note_requests.id');
            }

            $delivery_note_requests->groupBy('rider_transfer_note_requests.id');
        }



        $datatables = Datatables::of($delivery_note_requests)
            ->editColumn('amount', function ($delivery_note_requests) {
                return number_format($delivery_note_requests->amount);
            })
            ->addColumn('request_note_id_padded', function ($delivery_note_requests) {
                return str_pad($delivery_note_requests->request_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('transfer_note.id', function ($query, $keyword) {
                return $query->where('transfer_note.id', '=', $keyword);
            })
            ->editColumn('shipments_count_link', function ($delivery_note_requests) {
                if ($delivery_note_requests->shipments_count != 0) {
                    $shipment_count = RiderTransferNoteRequestShipment::where('request_note_id', $delivery_note_requests->id)
                        ->whereIn('status_id', [1, 4])->count();
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $shipment_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipments_count', function ($delivery_note_requests) {
                if ($delivery_note_requests->shipments_count != 0) {
                    $shipment_count = RiderTransferNoteRequestShipment::where('request_note_id', $delivery_note_requests->id)
                        ->whereIn('status_id', [1, 4])->count();
                    return $shipment_count;
                } else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($delivery_note_requests) {
                return $delivery_note_requests->rider_name;
            })
            ->editColumn('route', function ($delivery_note_requests) {
                return $delivery_note_requests->code . ' (' . $delivery_note_requests->start . ' to ' . $delivery_note_requests->end . ')';
            })
//            ->filterColumn('route', function ($query, $keyword) {
//                $keyword = strtolower($keyword);
//                if ($keyword != '') {
//                    $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
//                } else {
//                    $query->whereRaw('false');
//                }
//            })
            ->addColumn("action", function ($result) {
                $approveRoute = route('admin.transfer_note.rider_request.approve', ['id' => $result->request_note_id]);
                $rejectRoute = route('admin.transfer_note.rider_request.reject', ['id' => $result->request_note_id]);
                $editShipmentRoute = route('admin.transfer_note.rider_request.update', ['id' => $result->request_note_id]);
                $approveButton = '<a href="' . $approveRoute . '" class="dropdown-item" data-target-id="' . $result->request_note_id . '" class=""><i class="ft-check-circle primary"></i> Approve</a>';
                $rejectButton = '<a href="' . $rejectRoute . '" class="dropdown-item" data-target-id="' . $result->request_note_id . '" class=""><i class="ft-minus-circle primary"></i> Reject</a>';
                $editShipmentButton = '<a href="' . $editShipmentRoute . '" class="dropdown-item deliverynoteupdate" data-target-id="' . $result->delivery_note . '"><i class="ft-edit primary"></i> Edit</a>';
                if (session('role_id') == 1 || count(array_intersect([816, 817, 818], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    if (session('role_id') == 1 || in_array(816, session('permissions'))) {
                        $dropdown .= $approveButton;
                    }
                    if (session('role_id') == 1 || in_array(817, session('permissions'))) {
                        $dropdown .= $rejectButton;
                    }
                    if (session('role_id') == 1 || in_array(818, session('permissions'))) {
                        $dropdown .= $editShipmentButton;
                    }
                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            })->rawColumns(['shipments_count_link','action']);
        return $datatables->make(true);
    }

    public function request_note_shipments(Request $request)
    {
        $request_note_id = $request->input('request_note_id');
        $request_note = RiderTransferNoteRequest::find($request_note_id);
        $shipments = array();
        if ($request_note) {
            $shipments = RiderTransferNoteRequestShipment::join('shipments as s', 's.id', '=', 'rider_transfer_note_request_shipments.shipment_id')
                ->where('request_note_id', $request_note->id)->whereIn('rider_transfer_note_request_shipments.status_id', [1, 4])->pluck('s.tracking_number')->toArray();
        }
        if (count($shipments) > 0) {
            return ['status' => 0, 'success' => 'Transfer Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Transfer Note Shipments', 'shipments' => FALSE];
        }
    }


    public function request_note_approve(Request $request, $id)
    {
//        $insertData = [
//            'request_note_id' => $id,
//            'request_date' => date('Y-m-d'),
//            'created_at' => now(),
//            'updated_at' => now(),
//        ];
//
//        try {
//            TempRequestNoteApproval::insert($insertData);
//            $request_id = $id;
//        } catch (\Throwable $th) {
//            $request_id = 0;
//        }
        $request_id = $id;
        if ($request_id > 0) {
            $admin = Auth::id();
            $approved_at =Carbon::today();

            $delivery_request = RiderTransferNoteRequest::find($request_id);
            if ($delivery_request) {

                $shipments = RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->whereIn('status_id', [1, 4])->pluck('shipment_id')->toArray();
                $open_box_ids = RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->whereIn('status_id', [1, 4])->where('open_box', 1)->pluck('shipment_id')->toArray();
                $notifications = RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->whereIn('status_id', [1, 4])->where('notification', 1)->pluck('shipment_id')->toArray();
                $rider_informations = RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->whereIn('status_id', [1, 4])->where('rider_information', 1)->pluck('shipment_id')->toArray();

                if (count($shipments) == 0) {
                    return redirect()->back()->with('error', 'Shipments not entered!');
                }
                $pending_status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
                $valid_shipments = Shipment::whereIn('id', $shipments)->whereIn('shipper_status_id', $pending_status)->pluck('id');
                $shipments_count = count($valid_shipments);
                if ($shipments_count != 0) {
                    $valid_shipments = $valid_shipments->toArray();

                    $pudo_shipments = PudoDeliverShipment::whereIn('shipment_id', $valid_shipments)
                        ->pluck('shipment_id');
                    if ($pudo_shipments->isEmpty()) {
                        return redirect()->back()->with('error', 'Following shipments do not belong to PUDO shipments!');
                    }

                    $valid_shipments = $pudo_shipments->toArray();
                    $invalid_shipments = array_diff($shipments, $valid_shipments);

                    Shipment::whereIn('id', $valid_shipments)->update(['shipper_status_id' => 153, 'consignee_status_id' => 153]);
                    $total_cod_amount = Shipment::whereIn('id', $valid_shipments)->where(function ($query) {
                        $query->where('booking_type_id', '!=', 4)
                            ->orWhere(function ($sub_query) {
                                $sub_query->where('booking_type_id', '=', 4)
                                    ->where('charges_mode_id', '=', 2);
                            });
                    })->sum('amount');
                    $order = false;
                    if ($delivery_request->ordering) {
                        $order = true;
                    }
                    $normal_rider = TRUE;
                    $rider_id = $delivery_request->rider_id;
                    $rider = Rider::find($rider_id);
                    $hub_id = $delivery_request->hub_id;
                    $route_id = $delivery_request->route_id;

                    $note = new TransferNote();
                    $note->hub_id           = $hub_id;
                    $note->rider_id         = $rider_id;
                    $note->retail_store_id  = $delivery_request->retail_store_id;
                    $note->retail_store_type  = $delivery_request->retail_store_type;
                    $note->shipments_count  = $shipments_count;
                    $note->last_status_updated_by = $admin;
                    $note->last_status_updated_at = $approved_at;

//                    $note->approved_by      = $admin;
//                    $note->approved_at      = $approved_at;
                    $note->total_cod_amount = $total_cod_amount;
//                    $note->last_updated_at  = Carbon::now();
                    $note->ordering         = $order;
                    $note->created_via_app  = 1;
                    $note->request_note_id  = $delivery_request->id;
                    $note->save();

                    if ($note) {
                        if (!$order) { //Default
                            sort($valid_shipments); //sort_valid_shipments;
                        }
                        $serial = 1;
                        foreach ($valid_shipments as $shipment) {

                            $deliveryNoteShipment = new TransferNoteShipment();
                            $deliveryNoteShipment->transfer_note_id   = $note->id;
                            $deliveryNoteShipment->shipment_id        = $shipment;
                            $deliveryNoteShipment->notification       = in_array($shipment, $notifications) ? 1 : 0;
                            $deliveryNoteShipment->rider_information  = in_array($shipment, $rider_informations) ? 1 : 0;
                            $deliveryNoteShipment->ordering           = $serial;
                            $deliveryNoteShipment->last_status_updated_by = $admin;
                            $deliveryNoteShipment->last_status_updated_at = $approved_at;
                            $deliveryNoteShipment->save();

                            $serial++;
                        }

                        foreach ($valid_shipments as $shipment) {
                            if (in_array($shipment, $open_box_ids)) {
                                $shipment_detail = ShipmentDetail::where('shipment_id', $shipment)->where('is_open', '=', 0)->first();
                                if ($shipment_detail) {
                                    $shipment_detail->is_open = 1;
                                    $shipment_detail->save();
                                }

                                $shipment_data = Shipment::find($shipment);
                                $shipment_data->open_box = 1;
                                $shipment_data->save();

                                ShipmentOpenBoxJourneyController::add($shipment, 3, $admin);
                            }

                            $old_delivery_note_id = TransferNoteShipment::where('shipment_id', $shipment)->where('status_id', '>', 1)->orderBy('transfer_note_id', 'desc');

                            if ($old_delivery_note_id->exists()) {
                                $old_delivery_note_id = $old_delivery_note_id->first();

                                if (TransferNote::where('id', $old_delivery_note_id->transfer_note_id)->where('status_id', 1)->exists()) {
                                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('verification', 0)->latest()->first();
                                    if ($journey) {
                                        ShipmentsJourneyController::add($journey->shipment_id, $journey->shipper_status_id, $journey->consignee_status_id, $journey->status_reason_id, $journey->remarks, $journey->user_id, $admin, $journey->reference_1_id, NULL, 1, $journey->received_or_refused_by);
                                    }
                                }
                            }

                            ShipmentsJourneyController::add($shipment, 153, 153, NULL, NULL, NULL, $admin, $note->id, $note->rider_id);

                            $handover_shipments = HandoverShipments::where('shipment_id', $shipment)->whereIn('status', [1, 3]);
                            if ($handover_shipments->exists()) {
                                $handover_shipments = $handover_shipments->first();
                                $handover_shipments->status = 2;
                                $handover_shipments->save();
                                $handover_count = HandoverShipments::where('status', 1)->where('handover_id', $handover_shipments->handover_id)->count();
                                if ($handover_count == 0) {
                                    $handover = Handover::find($handover_shipments->handover_id);
                                    $handover->received_by = $admin;
                                    $handover->received_at = Carbon::now();
                                    $handover->received = $handover->received + 1;
                                    $handover->status_id = 4;
                                    $handover->save();
                                }
                                HandoverShipmentJourneyController::add($shipment, $handover_shipments->handover_id, 2);
                            }
                        }

//                        foreach ($valid_shipments as $shipment) {
//                            $payment_details = PayfastApiCall::ApiCall($note->id, $shipment);
//                            $rand = $payment_details['unique_key'];
//                            $payment_link = $payment_details['payment_link'];
//                            $url = $payment_details['url'];
//                            $trans_id = $payment_details['id'];
//                            $shipments_id = is_array($shipment) ? $shipment : [$shipment];
//
//                            CountFintechCharges::dispatch($shipments_id, $payment_link, $rand, $url, $trans_id);
//                            NotificationsController::send(10, $note->id, $shipment);
//                            NotificationsController::send(11, $note->id, $shipment);
//                            if (in_array($shipment, $notifications)) {
//                                $shipment_obj = Shipment::find($shipment);
//                                $shipment_otp = ShipmentOtp::where('shipment_id', $shipment);
//                                $otp = mt_rand(100000, 999999);
//                                $dbf_otp = mt_rand(100000, 999999);
//                                if ($shipment_otp->exists()) {
//                                    $shipment_otp = $shipment_otp->first();
//                                } else {
//                                    $shipment_otp = new ShipmentOtp();
//                                    $shipment_otp->shipment_id = $shipment;
//                                }
//                                $shipment_otp->otp = $otp;
//                                $shipment_otp->dbf_otp = $dbf_otp;
//                                $shipment_otp->rider_id = $note->rider_id;
//                                $shipment_otp->latitude = null;
//                                $shipment_otp->longitude = null;
//                                $shipment_otp->save();
//                                if ($shipment_obj->amount == 0) {
//                                    //English
//                                    NotificationsController::send(132, $note->id, $shipment);
//                                    //Urdu
//                                    NotificationsController::send(135, $note->id, $shipment);
//                                } else {
//                                    NotificationsController::send(12, $note->id, $shipment, $payment_link);
//                                }
//                            }
//                        }
                    }
//                    NotificationsController::send(40, $note->id);
//                    if ($normal_rider) {
//                        NotificationsController::app_notification(5, $rider_id, 2, $note->id);
//                    }

                    //rider attendance
//                    if ($rider->operation_rider_id == 1 && $rider->employee_id != null) {
//                        EmployeeAttendanceController::riders_attendance_mark($rider_id);
//                    }
                    //rider attendance end

                    //todo : update status 1 to 2 (take wo next time jbtk na aae jbtk rider cat ki request dubara na daljae)
//                    $rider_bypass_type = RiderCategoryByPass::where('rider_id', $rider_id)->where('status', 1)->select('rider_category_id', 'id')->latest()->first();
//                    if ($rider_bypass_type) {
//                        $rider_bypass_id = $rider_bypass_type->id;
//                        RiderCategoryByPass::where('rider_id', $rider_id)->where('id', $rider_bypass_id)->update(["status" => 2]);
//                    }
                    //todo end
                    $delivery_request->status_id = 2;
                    $delivery_request->approved_by = $admin;
                    $delivery_request->approved_at = Carbon::today();
                    $delivery_request->save();
//                    RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->whereIn('shipment_id', $valid_shipments)->update(['status_id' => 1]);
                    RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->whereIn('shipment_id', $invalid_shipments)->update(['status_id' => 3]);
                    return redirect()->back()->with('success', 'Transfer note has been Approved successfully' . PHP_EOL . 'Transfer Note ID: ' . $note->id);
                } else {
                    $delivery_request->status_id = 4;
                    $delivery_request->updated_by = $admin;
                    $delivery_request->save();
                    RiderTransferNoteRequestShipment::where('request_note_id', $delivery_request->id)->update(['status_id' => 3]);
                    return redirect()->back()->with('error', 'All the Shipment(s) are not ready for delivery yet or already in another transfer note, please check tracking!');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid Request ID');
            }
        } else {
            return redirect()->back()->with('error', 'Already Approve Request ID');
        }
    }

    public function request_note_reject(Request $request, $id)
    {
        $tranfer_request = RiderTransferNoteRequest::find($id);
        if ($tranfer_request) {
            $tranfer_request->status_id = 3;
            $tranfer_request->updated_by = Auth::id();
            $tranfer_request->save();
            RiderTransferNoteRequestShipment::where('request_note_id', $tranfer_request->id)
                ->update(['status_id' => 2]);
            return redirect()->back()->with('success', 'Transfer Note Creation Request is Rejected Successfully');
        }
        return redirect()->back()->with('error', 'Invalid Request ID');
    }

    public function request_note_update(Request $request, $id)
    {
        $service_type = BookingType::all();
        return view('admin.pudo.rider_request.update')->with(['request_note_id' => $id, 'service_type' => $service_type]);
    }

    public function request_note_update_list(Request $request, $id)
    {
        $deliveries = RiderTransferNoteRequest::join('rider_transfer_note_request_shipments as dns', 'dns.request_note_id', '=', 'rider_transfer_note_requests.id')
            ->join('shipments', 'shipments.id', '=', 'dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->select(['rider_transfer_note_requests.id as delivery_note', 'shipments.tracking_number', 'shipments.id as shId', 'oc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address as address', 'shipments.amount as amount', 'bt.booking_type as service_type', 'shipments.payment_mode_id as payment_mode_id'])
            ->where('rider_transfer_note_requests.id', $id)
            ->whereIn('dns.status_id', [1, 4]);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('rider_transfer_note_requests.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
                if ($deliveries->payment_mode_id != 2) {
                    return "<a href='javascript:void(0);' class='requestnoterow'><button type='button' class='btn btn-sm btn-danger'>Remove</button></a>";
                }
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->filterColumn('service_type', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('bt.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }

    public function request_note_remove(Request $request)
    {
        $shipment = RiderTransferNoteRequestShipment::join('shipments as s', 's.id', '=', 'rider_transfer_note_request_shipments.shipment_id')->where('rider_transfer_note_request_shipments.shipment_id', $request->shipment_id)->where('rider_transfer_note_request_shipments.request_note_id', $request->request_note_id)
            ->select('rider_transfer_note_request_shipments.*', 's.amount as cod');
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $request_note = $request->request_note_id;
            $delivery = RiderTransferNoteRequest::find($request_note);
            if ($delivery) {
                if ($shipment->status_id == 1) {
                    $shipment->status_id = 5;
                } else {
                    RiderTransferNoteRequestShipment::where('shipment_id', $request->shipment_id)->where('request_note_id', $request->request_note_id)->delete();
                }
                $delivery->total_cod_amount = $delivery->total_cod_amount - $shipment->cod;
                $delivery->shipments_count = $delivery->shipments_count - 1;
                $delivery->updated_by = Auth::id();
                $shipment->save();
                $delivery->save();
                return ['status' => 0, 'success' => 'Shipment is successfully removed'];
            } else {
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        } else {
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }

    public function request_note_remove_bulk(Request $request)
    {
        $shipments = $request->shipment_ids;
        $request_note_id = $request->request_note_id;
        if ($request_note_id) {
            foreach ($shipments as $shipment_id) {
                $shipment = RiderTransferNoteRequestShipment::join('shipments as s', 's.id', '=', 'rider_transfer_note_request_shipments.shipment_id')->where('rider_transfer_note_request_shipments.shipment_id', $shipment_id)->where('rider_transfer_note_request_shipments.request_note_id', $request->request_note_id)
                    ->select('rider_transfer_note_request_shipments.*', 's.amount as cod');
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    $request_note = $request->request_note_id;
                    $delivery = RiderTransferNoteRequest::find($request_note);
                    if ($delivery) {
                        if ($shipment->status_id == 1) {
                            $shipment->status = 5;
                            $shipment->save();
                        } else {
                            RiderTransferNoteRequestShipment::where('shipment_id', $request->shipment_id)->where('request_note_id', $request->request_note_id)->delete();
                        }
                        $delivery->total_cod_amount = $delivery->total_cod_amount - $shipment->cod;
                        $delivery->shipments_count = $delivery->shipments_count - 1;
                        $delivery->updated_by = Auth::id();
                        $delivery->save();
                    }
                }
            }
            return ['status' => 0, 'success' => 'Shipments are successfully removed'];
        } else {
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }
//
    public function add_shipments_in_request_note(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $request_note_id = $request->request_note_id;
        $shipment = Shipment::find($shipment_id);
        if ($shipment) {
            $request_note = RiderTransferNoteRequest::find($request_note_id);
            if ($request_note) {
                if ($request_note->status != 0) {
                    return response()->json(['status' => 1, 'error' => 'Request note is already approved']);
                }
                $rider = $request_note->rider;
                if ($shipment->payment_mode_id == 2 && $rider->ccd == 0) {
                    return response()->json(['status' => 1, 'error' => 'The selected Shipment is Credit Card on Delivery shipment and rider is not allowed/trained to use POS for CCD shipments']);
                }

                $request_shipment = RiderTransferNoteRequestShipment::where('request_note_id', $request_note_id)->where('shipment_id', $shipment->id);
                if ($request_shipment->exists()) {
                    $request_shipment = $request_shipment->first();
                    if (in_array($request_shipment->status_id, [1, 4])) {
                        return response()->json(['status' => 1, 'error' => 'Shipment is already in this delivery note!']);
                    } else if ($request_shipment->status_id == 5) {
                        $request_shipment->status_id = 4;
                        $request_note->total_cod_amount = $request_note->total_cod_amount + $shipment->amount;
                        $request_note->shipments_count = $request_note->shipments_count + 1;
                        $request_note->updated_by = Auth::id();
                        $request_shipment->save();
                        $request_note->save();
                        return response()->json(['status' => 0, 'success' => 'Shipment Added']);
                    }
                } else {
                    $serial = RiderTransferNoteRequestShipment::select('ordering')->where('request_note_id', $request_note->id)->orderBy('ordering', 'desc')->first();

                    $riderTransferShipment = new RiderTransferNoteRequestShipment();
                    $riderTransferShipment->request_note_id   = $request_note->id;
                    $riderTransferShipment->shipment_id       = $shipment->id;
                    $riderTransferShipment->notification      = 1;
                    $riderTransferShipment->rider_information = 1;
                    $riderTransferShipment->open_box          = 0;
                    $riderTransferShipment->status_id         = 4;
                    $riderTransferShipment->ordering          = $serial->ordering + 1;
                    $riderTransferShipment->save();

                    $request_note->total_cod_amount = $request_note->total_cod_amount + $shipment->amount;
                    $request_note->shipments_count = $request_note->shipments_count + 1;
                    $request_note->updated_type = 1;
                    $request_note->updated_by = Auth::id();
                    $request_note->save();
                    return response()->json(['status' => 0, 'success' => 'Shipment Added']);
                }
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipments Not Found']);
        }
    }


    static public function check_consolidation($shipment_id)
    {

        $consolidation_details = array();

        $consolidation_shipment = ConsolidationShipments::where('shipment_id', $shipment_id);

        if ($consolidation_shipment->exists()) {
            $consolidation_shipment = $consolidation_shipment->first();
            $consolidation = Consolidation::find($consolidation_shipment->consolidation_id);
            $consolidation_details['order'] = $consolidation_shipment->order;
            $consolidation_details['consolidation_id'] = $consolidation_shipment->consolidation_id;
            $consolidation_details['count'] = $consolidation->count;
            return $consolidation_details;
        } else {
            return false;
        }
    }


}
