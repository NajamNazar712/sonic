<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\Webhook\FinalChargesWebhookController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\ShipmentJourneyConsigneeRefusedSubReason;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\DeliveryShipmentsNotReceivedOperations;
use App\Http\Models\Admin\DeliveryShipmentsReceivedOperation;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\HBLKonnect\HblKonnectDeliveryNote;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransaction;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransactionDeliveryNote;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\Admin\PickupNoteStationDepositNote;
use App\Http\Models\Admin\ReplacementToRegularLog;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\RetailPickupNoteShipment;
use App\Http\Models\Admin\RiderCategoryByPass;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\StationDepositNoteAdjustment;
use App\Http\Models\Admin\StationDepositNoteLog;
use App\Http\Models\Admin\StationDepositNoteSlip;
use App\Http\Models\Admin\ShipmentOnHold;
use App\Http\Models\DeliveryNoteRequests;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\BanksList;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\BookingType;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\City;
use App\Http\Models\ConsigneeRefusedReason;
use App\Http\Models\Admin\AgentCallMonitoring;

use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\DeliveryCallVerificationRatio;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\MisroutedHistory;
use App\Http\Models\Notification;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\RestrictParcelsAttempt;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Rider;
use App\Http\Models\RiderCategory;
use App\Http\Models\RiderDelivery;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDistributionProduct;
use App\Http\Models\ShipmentInformationLog;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentOtp;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\Http\Models\WarehouseStock;
use App\Http\Models\WarehouseStockRequest;
use App\Http\Models\WarehouseStockRequestHistory;
use App\Http\Models\Admin\PettyCashStatement;
use App\Jobs\ProcessAgentCallMonitoring;
use App\Jobs\RCPSmsToConsignee;
use App\ReturnConfirmationPendingSmsAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SebastianBergmann\Environment\Console;
use Yajra\Datatables\Datatables;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Admin\PODImage;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ShipmentDetail;

class DeliveryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function pending_delivery_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 19);
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $shipping_mode = ShippingMode::all();
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        $service_type = BookingType::all();
        return view('admin.delivery.pending.index')->with(['shipment_status' => $shipment_status, 'shipping_mode' => $shipping_mode, 'service_type' => $service_type, 'hubs' => $hubs]);
    }

    public function pending_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 79);
        }
        $status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59); //for pending deliveries
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipments_journey as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 'shipments.id')
                    ->where('ras.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 13)'));
            })
            ->leftjoin('admins as agent', 'agent.id', '=', 'ras.admin_id')
            ->join('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sjd', function ($join) {
                $join->on('sjd.shipment_id', '=', 'shipments.id')
                    ->where('sjd.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('intercept_re_book_request_histories as irrh', 'irrh.shipment_id', '=', 'shipments.id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [DB::raw(2), DB::raw(3), DB::raw(5)])
                    ->where('crm.case_nature_id', DB::raw(1));
            })
            ->select('agent.name as agent', 'shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sjd.created_at as destination_arrival', 'sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc', 'crm.id as complaint')->whereRaw('IF (shipments.shipper_status_id IN (2, 49), (oc.hub_id = dc.hub_id), TRUE)')
            ->whereRaw('IF (shipments.shipper_status_id = 55, (irrh.old_consignee_city_id = irrh.new_consignee_city_id), TRUE)')
            ->whereIn('shipments.shipper_status_id', $status);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        if ($hub = $request->get('search_hub')) {
            $shipments = $shipments->where('h.id', '=', $hub);
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    } else if ($shipments->booking_type_id == 3) {
                        return "tnb_row";
                    } else {
                        return '';
                    }
                },
            ])
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('status_date', function ($shipments) {
                if ($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
                } else {
                    return " - ";
                }
            })
            ->editColumn('arrival', function ($shipments) {
                if ($shipments->arrival) {
                    return $shipments->arrival;
                } else {
                    return " - ";
                }
            })
            ->editColumn('destination_arrival', function ($shipments) {
                if ($shipments->destination_arrival) {
                    return $shipments->destination_arrival;
                } else {
                    return " - ";
                }
            })
            ->filterColumn('status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('shipping_mode', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('sm.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('bt.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(34, session('permissions'))) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                            <a href="#" class="dropdown-item dispute_modal"><i class="ft-alert-circle primary"></i> Dispute</a>
                        </div>
                      </div>
                    ';

                    return $dropdown;

                } else {
                    return '';
                }
            });
        if ($mode = $request->get('search_shipping_mode')) {

            $datatables->where('sm.id', '=', $mode);
        }
        return $datatables->make(true);


    }

    public function delivery_note_index()
    {
//        $riders = Rider::where('status', 1);

//        if (session('role_id') != 1) {
//            $riders = $riders->whereHas('city', function ($query) {
//                $query->whereIn('hub_id', session('hubs'));
//            });
//        }
//
//        $riders = $riders->get();

        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();
        $operation_rider_category = OperationRidersCategory::all();

        return view('admin.delivery.note.index')->with(['routes' => $routes, 'operation_rider_category' => $operation_rider_category]);
    }

    public function get_adjustment_reference(Request $request)
    {
        $adjustments = StationDepositNoteAdjustment::where('sdn_id', $request->sdn_id)->get();
        $html = "";
        foreach ($adjustments as $adjustment) {
            $html .= '<u><a href="javascript:void(0);" onclick="printStatement(' . $adjustment->petty_cash_statement_id . ')">' . $adjustment->petty_cash_statement_id . '</a></u><br>';
        }

        return response()->json(['status' => 1, 'html' => $html]);
    }

    public function check_rider_dncc_status(Request $request)
    {
        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', '2021-05-18 23:59:00');
        $delivery_note = DeliveryNote::where(['rider_id' => $request->rider_id, 'dncc_status' => 0])->where('status', '!=', 4)
            ->whereDate('created_at', '>', $datetime)
            ->whereDate('created_at', '!=', Carbon::today());

        if ($delivery_note->exists()) {
            $delivery_note_request = DeliveryNoteRequests::where('rider_id', $request->rider_id)->where('status', 2)->where('completed', 0)->latest()->first();
            if ($delivery_note_request) {
                $delivery_note_request->completed = 1;
                $delivery_note_request->save();
                $rider = Rider::find($request->rider_id);
                $ccd_rider = $rider->ccd;
                return response()->json(['status' => 1, 'ccd_rider' => $ccd_rider]);
            } else {
                return response()->json(['status' => 0, 'error' => "Rider can not be selected because previous delivery note is not been completed"]);
            }
        } else {
            $rider = Rider::find($request->rider_id);
            $ccd_rider = $rider->ccd;
            return response()->json(['status' => 1, 'ccd_rider' => $ccd_rider]);
        }
    }

    public function note_consolidation_check(Request $request)
    {
        $missing_shipments = array();
        foreach ($request->consolidation_ids as $id) {
            $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $id);
            if ($consolidation_shipments->exists()) {
                $consolidation_shipments = $consolidation_shipments->pluck('shipment_id')->toArray();
                foreach ($consolidation_shipments as $shipment_id) {
                    if (!in_array($shipment_id, $request->shipment_ids)) {
                        $tracking = Shipment::find($shipment_id)->tracking_number;
                        $missing_shipments[] = $tracking;
                    }

                }
            }
        }
        if (count($missing_shipments) > 0) {
            return response()->json(['missing_flag' => TRUE, 'missing_shipments' => $missing_shipments]);
        } else {
            return response()->json(['missing_flag' => FALSE]);
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

    public function get_shipment_details(Request $request)
    {
//        todo: bypasses rider category
        if ($request->tracking != '' && $request->rider_id != '' )
        {
            $tracking_number = $request->tracking;
            $rider_id = $request->rider_id;

            $rider_default_type = Rider::where('id',$rider_id)->select('rider_category_id')->first();

            $shipment = Shipment::where('tracking_number',$tracking_number)->select('actual_weight')->first();

            $weight = GlobalSettings::where('type', 'light_heavy_weight_for_shipment')->select('text')->first();

            if($shipment->actual_weight > $weight->text)
            {
                $rider_bypass_type = RiderCategoryByPass::where('rider_id',$rider_id)->where('status',1)->where('rider_category_id',2)->select('rider_category_id','id')->latest()->first();

                if($rider_bypass_type)
                {
                    $rider_bypass_id = $rider_bypass_type->id;
                    if($rider_bypass_type->rider_category_id != 2)
                    {
                        if($rider_default_type->rider_category_id == 1)
                        {
                            return ['status' => 1, 'error' => 'Shipment is heavy weighted and the selected rider type is light weighted !'];
                        }
                    }
                }
                elseif($rider_default_type->rider_category_id == 1)
                {
                    return ['status' => 1, 'error' => 'Shipment is heavy weighted and the selected rider type is light weighted !'];
                }
            }
            elseif($shipment->actual_weight <= $weight->text)
            {
                $rider_bypass_type = RiderCategoryByPass::where('rider_id',$rider_id)->where('status',1)->where('rider_category_id',1)->select('rider_category_id','id')->latest()->first();

                if($rider_bypass_type)
                {
                    $rider_bypass_id = $rider_bypass_type->id;
                    if($rider_bypass_type->rider_category_id != 1)
                    {
                        if($rider_default_type->rider_category_id == 2)
                        {
                            return ['status' => 1, 'error' => 'Shipment is light weighted and the selected rider type is heavy weighted !'];
                        }
                    }

                }
                elseif($rider_default_type->rider_category_id == 2)
                {
                    return ['status' => 1, 'error' => 'Shipment is light weighted and the selected rider type is heavy weighted !'];
                }
            }
        }
//        todo: bypasses rider category end

        $pending_status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);
        if ($request->tracking != '') {
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id', $pending_status);
            $remarks = '';
            $status = '';
            $rider_name = '';
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                if(!$dispute_check){
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
                    $old_delivery_note_id = DeliveryNoteShipment::join('delivery_notes', 'delivery_notes.id', '=', 'delivery_note_shipments.delivery_note_id')->where('delivery_note_shipments.shipment_id', $shipment->id)->where('delivery_notes.status', '!=', 4)->orderBy('delivery_note_id', 'desc');
                    if ($old_delivery_note_id->exists()) {
                        $old_delivery_note_id = $old_delivery_note_id->first();
                        $delivery_note_rider = DeliveryNote::where('id', $old_delivery_note_id->delivery_note_id)->first();
                        $rider_name = $delivery_note_rider->rider->name;
                        $is_updateable = DeliveryNoteShipment::where('delivery_note_id', $old_delivery_note_id->delivery_note_id)->where('status', 0)->count();
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
                                ShipmentScanningJourneyController::add($shipment->id, 4, 1, Auth::id(), null, null);
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
                            ShipmentScanningJourneyController::add($shipment->id, 4, 1, Auth::id(), null, null);
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
                        return ['status' => 1, 'error' => 'This Shipment is already in an unverified delivery note!'];

                    }
                } else {
                    return ['status' => 1, 'error' => 'This Shipment doesn\'t belongs to your assigned hubs!'];
                }
            } else {
                return ['status' => 1, 'error' => 'This Shipment is not ready for delivery yet or already in delivery note, please check tracking!'];
            }
        }
    }


    public function get_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }

        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function delivery_note_rider_check(Request $request)
    {
        $rider_id = $request->rider_id;
        $flag = true;
        $delivery_note_details = array();
        $delivery_notes = DeliveryNote::where('rider_id', $rider_id)->where('pending_status', 0)->where('status', '!=', 4)->get();
        if ($delivery_notes) {
            foreach ($delivery_notes as $note) {

                $delivery_note_details[$note->id] = $note;
                $flag = false;

            }
        }
        return response()->json(['flag' => $flag, 'delivery_note' => $delivery_note_details]);
    }

    public function create_delivery_note(Request $request)
    {
        if ($request->hub_id == '') {
            return redirect()->back()->with('error', 'Hub not found!');
        }

        if ($request->selected_route_id == '') {
            return redirect()->back()->with('error', 'Route not selected!');
        }

        if ($request->selected_rider_id == '') {
            return redirect()->back()->with('error', 'Rider not selected!');
        }

        $shipments = explode(',', $request->shipment_ids);

        if (count($shipments) == 0) {
            return redirect()->back()->with('error', 'Shipments not entered!');
        }

        $pending_status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49, 55, 59);

        $valid_shipments = Shipment::whereIn('id', $shipments)->whereIn('shipper_status_id', $pending_status)->pluck('id');

        $shipments_count = count($valid_shipments);

        if ($shipments_count != 0) {
            $valid_shipments = $valid_shipments->toArray();

            Shipment::whereIn('id', $valid_shipments)->update(['shipper_status_id' => 5, 'consignee_status_id' => 5]);

            $total_cod_amount = Shipment::whereIn('id', $valid_shipments)->where(function ($query) {
                $query->where('booking_type_id', '!=', 4)
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('booking_type_id', '=', 4)
                            ->where('charges_mode_id', '=', 2);
                    });
            })->sum('amount');

            $open_box_ids = explode(',', $request->open_box_ids);
            $notifications = explode(',', $request->notification_ids);
            $rider_informations = explode(',', $request->rider_info_ids);
            if (Notification::where('id', 40)->where('status', 1)->exists()) {
                $password = rand(10001, 99999);
            } else {
                $password = NULL;
            }
            $order = false;
            if ($request->has('order_checkbox')) {
                $order = true;
            }


            $admin = Auth::id();

            $normal_rider = TRUE;

            $rider = Rider::find($request->selected_rider_id);
            if ($rider->special_rider) {
                $note = DeliveryNote::create([
                    'hub_id' => $request->hub_id,
                    'rider_id' => $request->selected_rider_id,
                    'route_id' => $request->selected_route_id,
                    'shipments_count' => $shipments_count,
                    'admin_id' => $admin,
                    'total_cod_amount' => $total_cod_amount,
                    'password' => $password,
                    'last_updated_at' => Carbon::now(),
                    'special_rider_name' => $request->special_rider_name,
                    'special_rider_phone' => $request->special_rider_phone,
                    'special_rider' => 1,
                    'order' => $order
                ]);
                $normal_rider = FALSE;
            } else {
                $note = DeliveryNote::create([
                    'hub_id' => $request->hub_id,
                    'rider_id' => $request->selected_rider_id,
                    'route_id' => $request->selected_route_id,
                    'shipments_count' => $shipments_count,
                    'admin_id' => $admin,
                    'total_cod_amount' => $total_cod_amount,
                    'password' => $password,
                    'last_updated_at' => Carbon::now(),
                    'ordering' => $order
                ]);

            }
            if ($note) {
                if (!$order) {  //Default
                    sort($valid_shipments); //sort_valid_shipments;
                }
                $serial = 1;
                foreach ($valid_shipments as $index => $shipment) {
                    DeliveryNoteShipment::create([
                        'delivery_note_id' => $note->id,
                        'shipment_id' => $shipment,
                        'notification' => $notifications[$index],
                        'rider_information' => $rider_informations[$index],
                        'ordering' => $serial
                    ]);
                    $serial++;
                }

                foreach ($valid_shipments as $index => $shipment) {
                    if (in_array($shipment, $open_box_ids)) {
                        $shipment_detail = ShipmentDetail::where('shipment_id', $shipment)->where('is_open', '=', 0)->first();
                        if ($shipment_detail) {
                            $shipment_detail->is_open = 1;
                            $shipment_detail->save();
                        }

                        $shipment_data = Shipment::find($shipment);
                        $shipment_data->open_box = 1;
                        $shipment_data->save();

                        ShipmentOpenBoxJourneyController::add($shipment, 3, Auth::id());
                    }

                    $old_delivery_note_id = DeliveryNoteShipment::where('shipment_id', $shipment)->where('status', '>', 0)->orderBy('delivery_note_id', 'desc');

                    if ($old_delivery_note_id->exists()) {
                        $old_delivery_note_id = $old_delivery_note_id->first();

                        if (DeliveryNote::where('id', $old_delivery_note_id->delivery_note_id)->where('status', 0)->exists()) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('verification', 0)->latest()->first();
                            if ($journey) {
                                ShipmentsJourneyController::add($journey->shipment_id, $journey->shipper_status_id, $journey->consignee_status_id, $journey->status_reason_id, $journey->remarks, $journey->user_id, Auth::id(), $journey->reference_1_id, NULL, 1, $journey->received_or_refused_by);
                            }
                        }
                    }

                    ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, Auth::id(), $note->id, $note->rider_id);

                    $handover_shipments = HandoverShipments::where('shipment_id', $shipment)->whereIn('status', [1, 3]);
                    if ($handover_shipments->exists()) {
                        $handover_shipments = $handover_shipments->first();
                        $handover_shipments->status = 2;
                        $handover_shipments->save();
                        $handover_count = HandoverShipments::where('status', 1)->where('handover_id', $handover_shipments->handover_id)->count();
                        if ($handover_count == 0) {
                            $handover = Handover::find($handover_shipments->handover_id);
                            $handover->received_by = Auth::id();
                            $handover->received_at = Carbon::now();
                            $handover->received = $handover->received + 1;
                            $handover->status_id = 4;
                            $handover->save();
                        }
                        HandoverShipmentJourneyController::add($shipment, $handover_shipments->handover_id, 2);
                    }

                }

                foreach ($valid_shipments as $index => $shipment) {
                    NotificationsController::send(10, $note->id, $shipment);
                    NotificationsController::send(11, $note->id, $shipment);

                    if ($notifications[$index]) {
                        $shipment_obj = Shipment::find($shipment);
                        $shipment_otp = ShipmentOtp::where('shipment_id', $shipment);
                        if ($shipment_otp->exists()) {
                            $shipment_otp = $shipment_otp->first();
                        } else {
                            $otp = mt_rand(100000, 999999);
                            $shipment_otp = new ShipmentOtp();
                            $shipment_otp->shipment_id = $shipment;
                            $shipment_otp->otp = $otp;
                            $shipment_otp->save();
                        }
                        if ($shipment_obj->amount == 0) {
                            //English
                            NotificationsController::send(132, $note->id, $shipment);
                            //Urdu
                            NotificationsController::send(135, $note->id, $shipment);
                        } else {
                            NotificationsController::send(12, $note->id, $shipment);
                        }
                    }
                }
                NotificationsController::send(40, $note->id);
                if ($normal_rider) {
                    NotificationsController::app_notification(5, $request->selected_rider_id, 2, $note->id);
                }
            }

            //rider attendance
            if ($request->operation_rider_type_for_attendance == 1) {
                EmployeeAttendanceController::riders_attendance_mark($rider->id);
            }
            //rider attendance end

            //todo : update status 1 to 2 (take wo next time jbtk na aae jbtk rider cat ki request dubara na daljae)
            $rider_bypass_type = RiderCategoryByPass::where('rider_id', $request->selected_rider_id)->where('status', 1)->select('rider_category_id', 'id')->latest()->first();
            if ($rider_bypass_type) {
                $rider_bypass_id = $rider_bypass_type->id;
                $rider_bypass_update = RiderCategoryByPass::where('rider_id', $request->selected_rider_id)->where('id', $rider_bypass_id)->update(["status" => 2]);
            }
            //todo end

            return redirect()->back()->with(['success' => 'Delivery note has been created successfully', 'print' => $note->id]);
        } else {
            return redirect()->back()->with(['error' => 'All the Shipment(s) are not ready for delivery yet or already in another delivery note, please check tracking!']);

        }

    }

    public function delivery_note_receive_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 20);
        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();
        $operation_rider_category = OperationRidersCategory::all();
        return view('admin.delivery.receive.index')->with(['routes' => $routes, 'operation_rider_category' => $operation_rider_category]);
    }

    public function receive_deliveries_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 80);
        }

        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('rider_types', 'rider_types.id', '=', 'riders.rider_type_id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->join('zones as z','oc.zone_id','=','z.id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'delivery_notes.updated_by')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id',  'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'delivery_notes.created_at', 'delivery_notes.total_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count as shipments_count_link', 'delivery_notes.pending_status', 'delivery_notes.created_at','delivery_notes.last_updated_at','ad.name as updated_by','delivery_notes.special_rider','delivery_notes.special_rider_name','delivery_notes.special_rider_phone','delivery_notes.delivered_shipments as delivered_shipments',DB::raw('(SELECT COUNT(d.id) FROM delivery_notes AS d INNER JOIN delivery_note_shipments AS dns ON d.id = dns.delivery_note_id WHERE dns.delivery_note_id = delivery_notes.id AND dns.status = 0) AS shipments_unverified_count'),'oc.business_category_id as business_category','z.name as zone_name', 'riders.operation_rider_id', 'riders.rider_type_id','rider_types.name as rt'])
            ->where('delivery_notes.status', 0);


        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipments_unverified_link', function ($deliveries) {
                if ($deliveries->shipments_unverified_count != 0) {
                    return $deliveries->shipments_unverified_count;
                } else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($rider) {
                if ($rider->special_rider) {
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                } else {
                    return $rider->rider;
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('pending_status', function ($result) {
                if ($result->pending_status == 0) {
                    return 'Pending for Update';
                } else {
                    return 'Pending for Verification';
                }
            })
            ->editColumn('business_category', function ($result) {
                if ($result->business_category == 1) {
                    return 'Domestic';
                } else {
                    return 'International';
                }
            })
            ->filterColumn('pending_status', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword == 0 || $keyword == 1) {
                    $query->where('delivery_notes.pending_status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('business_category', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword == 1) {
                    $query->where('oc.business_category_id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $statusUpdate = route('admin.delivery.receive.status', ['id' => $result->delivery_note]);
                $route = route('admin.delivery.receive.update', ['note' => $result->delivery_note]);
                $verifyStatus = route('admin.delivery.receive.status.verify', ['note' => $result->delivery_note]);

                $receive_button = '<a href="' . $statusUpdate . '" class="dropdown-item" data-target-id="' . $result->delivery_note . '" class=""><i class="ft-plus-circle primary"></i> Receive</a>';
                $shift_shipment_button = '<a href="' . $route . '" class="dropdown-item deliverynoteupdate" data-target-id="' . $result->delivery_note . '"><i class="ft-plus-circle primary"></i> Edit Shipment</a>';
                $verify_statuses_button = '<a href="' . $verifyStatus . '" class="dropdown-item" data-target-id="' . $result->id . '"><i class="ft-plus-circle primary"></i> Verify Statuses</a>';
                $print_temporary_dncc_button = '<a class="dropdown-item printTempDNCC"><i class="ft-printer primary"></i> Print Temporary DNCC</a>';
                $print_undelivered_performa_button = '<a class="dropdown-item printUndeliveredDNCC"><i class="ft-printer primary"></i> Print Undelivered Performa</a>';
                $reassign_rider_button = '<a class="dropdown-item reassign_rider"><i class="la la-edit primary"></i> Reassign Rider</a>';

                if (session('role_id') == 1 || count(array_intersect([37, 38, 39], session('permissions'))) !== 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';
                    $statusCheck = DeliveryNoteShipment::where(['delivery_note_id' => $result->delivery_note, 'status' => 0])->get();
                    $updatedstatusCheck = DeliveryNoteShipment::where('delivery_note_id', $result->delivery_note)->where('status', '>', 0)->exists();


                    if (($result->pending_status == 0) && (session('role_id') == 1 || in_array(37, session('permissions')))) {
                        if(session('role_id') == 1 || !($result->operation_rider_id == 1 && $result->rider_type_id == 1)){
                            $dropdown .= $receive_button;
                        }
                    }

                    if (($result->created_at->diffInMinutes(Carbon::now()) <= 60) && (session('role_id') == 1 || in_array(38, session('permissions')))) {
                        if (!$updatedstatusCheck) {

                            $dropdown .= $shift_shipment_button;
                        }
                    }


                    if (($result->pending_status == 1) && (session('role_id') == 1 || in_array(39, session('permissions')))) {
                        $dropdown .= $verify_statuses_button;
                    }

                    if (($result->pending_status == 1) && (session('role_id') == 1 || in_array(37, session('permissions')))) {
                        $dropdown .= $print_temporary_dncc_button;

                        $dropdown .= $print_undelivered_performa_button;
                    }
                    if (($result->created_at->diffInMinutes(Carbon::now()) <= 15) && (session('role_id') == 1 || in_array(304, session('permissions')))) {
                        if (!$updatedstatusCheck) {
                            $dropdown .= $reassign_rider_button;
                        }
                    }

                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            });

        if ($tracking_number = $request->get('search_tracking')) {
            $datatables->join('delivery_note_shipments as dns', 'delivery_notes.id', '=', 'dns.delivery_note_id')
                ->join('shipments as s', 'dns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }
        if ($delivery_note_number = $request->get('delivery_note_number')) {
            $datatables->where('delivery_notes.id', '=', $delivery_note_number);
        }
        return $datatables->make(true);

    }

    public function receive_delivery_search(Request $request)
    {
        $search = Shipment::where('tracking_number', $request->tracking);
        if ($search->exists()) {
            $search = $search->first();
            $note = DeliveryNoteShipment::where('shipment_id', $search->id);

            if ($note->exists()) {
                $note = $note->first();
                return response()->json(['status' => 0, 'delivery_note' => $note->delivery_note_id]);
            } else {
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number in delivery notes'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number Exist'];

        }
    }

    public function receive_delivery_update(Request $request, $id)
    {
        $delivery_note = DeliveryNote::find($id);
        if ($delivery_note) {
            if($delivery_note->status == 4){
                return redirect()->back()->with('error', 'Delivery note is cancelled!');
            }
            if (($delivery_note->created_at->diffInMinutes(Carbon::now()) <= 60) && (session('role_id') == 1 || in_array(304, session('permissions')))) {
                if (DeliveryNoteShipment::where('delivery_note_id', $id)->where('status', '>', 0)->count() == 0) {
                    $service_type = BookingType::all();
                    return view('admin.delivery.receive.update')->with(['delivery_note_id' => $id, 'service_type' => $service_type]);
                } else {
                    return redirect()->route('admin.access_denied');
                }

            }
            return redirect()->route('admin.access_denied');
        }
        return redirect()->route('admin.access_denied');
    }

    public function receive_delivery_notes_list(Request $request, $id)
    {
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->join('shipments', 'shipments.id', '=', 'dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->select(['delivery_notes.id as delivery_note', 'shipments.tracking_number', 'shipments.id as shId', 'oc.name as destination', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address as address', 'shipments.amount as amount', 'bt.booking_type as service_type', 'shipments.payment_mode_id as payment_mode_id'])
            ->where('delivery_notes.id', $id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
                if ($deliveries->payment_mode_id != 2) {
                    return "<a href='javascript:void(0);' class='deliverynoterow'><button type='button' class='btn btn-sm btn-danger'>Remove</button></a>";
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

    public function receive_delivery_remove(Request $request)
    {
        $shipment = DeliveryNoteShipment::where('shipment_id', $request->shipment_id)->where('delivery_note_id', $request->delivery_note_id);
        if ($shipment->exists()) {
            $delivery_note = $request->delivery_note_id;
            $delivery = DeliveryNote::where('id', $delivery_note);
            if ($delivery->exists()) {
                $consolidation_shipments = ConsolidationShipments::where('shipment_id', $request->shipment_id);
                if ($consolidation_shipments->exists()) {
                    $consolidation_shipments = $consolidation_shipments->first();
                    $consolidation_id = $consolidation_shipments->consolidation_id;
                    $consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->pluck('shipment_id')->toArray();
                    $shipment_count = 0;
                    $shipment_cod = 0;
                    foreach ($consolidated_shipments as $consolidated_shipment) {
                        $parcel = Shipment::where('id', $consolidated_shipment)->first();
                        $shipment_cod += $parcel->amount;
                        $shipment_count += 1;
                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note, 'shipment_id' => $consolidated_shipment])->delete();
                        Shipment::where('id', $consolidated_shipment)->update(['shipper_status_id' => 6]);
                        ShipmentsJourneyController::add($consolidated_shipment, 6, NULL, NULL, NULL, NULL, Auth::id(), $request->delivery_note_id);
                    }
                    $delivery = $delivery->first();
                    $count = $delivery->shipments_count;
                    $cod = $delivery->total_cod_amount;
                    $count = $count - $shipment_count;
                    if ($parcel->booking_type_id != 4 || ($parcel->booking_type_id == 4 && $parcel->charges_mode_id == 2)) {
                        $cod = $cod - $shipment_cod;
                    }
                    if ($count == 0) {
                        DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => 0, 'total_cod_amount' => $cod, 'status' => 4]);
                    } else {
                        DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => $count, 'total_cod_amount' => $cod]);
                    }

                } else {
                    $parcel = Shipment::where('id', $request->shipment_id)->first();
                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note, 'shipment_id' => $request->shipment_id])->delete();
                    $delivery = $delivery->first();
                    $count = DeliveryNoteShipment::where('delivery_note_id', $delivery_note)->count();
                    $cod = $delivery->total_cod_amount;

                    if ($parcel->booking_type_id != 4 || ($parcel->booking_type_id == 4 && $parcel->charges_mode_id == 2)) {
                        $cod = $cod - $parcel->amount;
                    }

                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 6]);
                    ShipmentsJourneyController::add($request->shipment_id, 6, NULL, NULL, NULL, NULL, Auth::id(), $request->delivery_note_id);


                    if ($count == 0) {
                        DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => 0, 'total_cod_amount' => $cod, 'status' => 4]);
                    } else {
                        DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => $count, 'total_cod_amount' => $cod]);
                    }

                }

                return ['status' => 0, 'success' => 'Shipment is successfully removed'];
            } else {
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        } else {
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }

    public function received_print(Request $request)
    {
//        dd($request);
        $delivery_note_id = $request->id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                    <title>Delivery Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }

                      td.replacement span {
                        width: 22px;
                      }

                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }

                      td.try_and_buy span {
                        width: 22px;
                      }

                      td.try_and_buy span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }
                      
                      td.complaint {
                            background: #09262e !important;
                            color: #ffffff;
                       }
                      td.details_changed {
                            background: #000000 !important;
                            color: #ffffff;
                       }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $total_shipments = 0;
            $total_cod_amount = 0;
            $shipments = DeliveryNoteShipment::where('delivery_note_id', $request->id)->select('shipment_id')->orderBy('ordering', 'asc', 'shipment_id', 'asc')->get();

            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Item Qty</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                            <td class="color primary"><strong>Special Instructions</strong></td>
                            <td class="color primary"><strong>Open Shipment</strong></td>
                            <td class="color primary"><strong>Remarks</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Receiver\'s Name</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Sign</strong></td>
                          </tr>
        ';


            foreach ($shipments as $parcel) {
                $total_shipments++;
                $shipment = Shipment::find($parcel->shipment_id);
                $class = null;
                $details_change_class = null;
                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', 1)->whereIn('status_id', [2, 3, 5])->exists()) {
                    $class = 'complaint';
                } elseif (ShipmentInformationLog::where('shipment_id', $shipment->id)->exists()) {
                    $details_change_class = 'details_changed';
                }
                $check_walk_in = GlobalSettings::where('type', 'Walk-In')->first();
                if ($check_walk_in['setting_value'] == $shipment->user->id) {
                    $user_details = 'Walk-In (' . $shipment->pickup_address->poc . ') | ' . $shipment->pickup_address->phone;
                } else {
                    $user_details = $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '');
                }
                $ccd_icon = '';
                if ($shipment->payment_mode_id == 2) {
                    $tracking_number = '<b>' . $shipment->tracking_number . ' </b><br/><span><i class="la la-credit-card"></i>(Credit Card on Delivery-CCD)</span>';
                } else {
                    $tracking_number = $shipment->tracking_number;
                }
                $consignee_address = '';
                if ($shipment->consignee_address != null) {
                    $consignee_address = $shipment->consignee_address;
                }

                $shipment_details_row_start = '
                          <tr>
                            <td class="' . $class . '">' . $total_shipments . '</td>
                            <td class="' . $class . '">' . $tracking_number . '</td>
                            <td class="' . $class . '">' . $user_details . '</td>
                            <td class="' . $class . ' ' . $details_change_class . '">' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                             <td class="' . $class . '">' . $consignee_address . '</td>
                           
                ';

                if ($shipment->booking_type_id == 1) {
                    $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->booking_type->booking_type . '</td>
                ';
                } else if ($shipment->booking_type_id == 2) {
                    $shipment_details_row_start .= '
                    <td class="replacement ' . $class . '"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                ';
                } else if ($shipment->booking_type_id == 3) {
                    $shipment_details_row_start .= '
                    <td class="try_and_buy ' . $class . '"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/try_and_buy.png') . '"></span></td>
                ';
                } else {
                    $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->booking_type->booking_type . '</td>
                ';
                }

                $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment->items->sum('quantity') . '</td>';

                if ($shipment->booking_type_id != 4 || ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 2)) {
                    $shipment_details_row_start .= '
                            <td class="' . $class . '">Rs ' . number_format($shipment->amount) . '</td>
                    ';

                    $total_cod_amount += $shipment->amount;
                } else {
                    $shipment_details_row_start .= '
                            <td class="' . $class . '">Rs 0</td>
                    ';
                }
                if ($shipment->special_instructions != null) {
                    $shipment_details_row_start .= '<td class="' . $class . ' ' . $details_change_class . '">' . $shipment->special_instructions . '</td>';
                } else {
                    $shipment_details_row_start .= '<td class="' . $class . ' ' . $details_change_class . '">-</td>';
                }
                if ($shipment->shipment_detail()->exists()) {
                    if ($shipment->shipment_detail->is_open == 1) {
                        $shipment_details_row_start .= '
                    <td class="' . $class . '"><strong> Yes <span><img src="' . asset('img/open_box_icon.png') . '" ></span></strong></td>';
                    } else {
                        $shipment_details_row_start .= '
                    <td class="' . $class . '"><strong> No <span></span></strong></td>';

                    }
                } else {
                    $shipment_details_row_start .= '
                    <td class="' . $class . '"><strong> No <span></span></strong></td>';

                }
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', '!=', 5)->where('remarks', '!=', null)->select('remarks');

                if ($shipment_journey->exists()) {
                    $shipment_journey = $shipment_journey->latest()->first();

                    $shipment_details_row_start .= '
                            <td class="' . $class . '">' . $shipment_journey->remarks . '</td>
                    ';
                } else {
                    $shipment_details_row_start .= '
                            <td class="' . $class . '"></td>
                    ';
                }


                $shipment_details_row_start .= '
                            <td class="' . $class . '"></td>
                            <td class="' . $class . '"></td>
                          </tr>
                ';

                $shipment_details .= $shipment_details_row_start;

                if ($shipment->booking_type_id == 3) {
                    $total_Shipment_items = 0;
                    foreach ($shipment->items as $shipment_item) {
                        $total_Shipment_items++;
                        $shipment_details_row_start = '
                          <tr>
                            <td class="' . $class . '">' . $total_shipments . '.' . $total_Shipment_items . '</td>
                            <td class="' . $class . '">' . $shipment_item->id . ' (' . $shipment->tracking_number . ')</td>
                            <td class="' . $class . '"><b>Product Type:</b></td>
                            <td class="' . $class . '">' . $shipment_item->product->product_name . '</td>
                            <td class="' . $class . '">' . $shipment_item->description . '</td>
                ';
                        $shipment_details_row_start .= '
                    <td class="try_and_buy ' . $class . '"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/try_and_buy.png') . '"></span></td>
                ';

                        $shipment_details_row_start .= '
                    <td class="' . $class . '">' . $shipment_item->quantity . '</td>';

                        $shipment_details_row_start .= '
                            <td class="' . $class . '">Rs ' . number_format($shipment_item->price) . '</td>
                    ';
                        $shipment_details_row_start .= '<td class="' . $class . '">-</td>';

                        $shipment_details_row_start .= '
                        <td class="' . $class . '"></td>
                    ';


                        $shipment_details_row_start .= '
                            <td class="' . $class . '"></td>
                            <td class="' . $class . '"></td>
                          </tr>
                ';

                        $shipment_details .= $shipment_details_row_start;
                    }
                }
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $delivery_note = $delivery_note->first();
            $rider_id = NULL;
            if ($delivery_note->special_rider) {
                $rider_name = $rider->name . ' ( ' . $delivery_note->special_rider_name . ' )';
            } else {
                $rider_name = $rider->name;
                $rider_id = $rider->trax_id;
            }
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . '( ' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ' )';

            //HBL Konnect Integration
            $hbl_transactions_amount = 0;
            $hbl_transactions_delivery_note = HblKonnectTransactionDeliveryNote::where('delivery_note_id', $request->id);
            if($hbl_transactions_delivery_note->exists()){
                $hbl_transactions_delivery_note = $hbl_transactions_delivery_note->first();
                $hbl_transactions_amount = $hbl_transactions_delivery_note->transactions_amount;
                $cash_amount = $hbl_transactions_delivery_note->cash_amount;
            }
            else{
                $cash_amount = $delivery_note->recived_cod_amount;
            }
            //HBL Konnect Integration
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Delivery Note</strong></td>
                            <td class="text-center align-middle color secondary">Created at ' . $delivery_note_details->created_at . '</br> by ' . ucfirst($delivery_note_details->admin->name) . '</td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td colspan="2" rowspan="9" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Trax ID</strong></td>
                            <td>' . $rider_id . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Collection Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>HBL Transactions Amount</strong></td>
                            <td>Rs ' . number_format($hbl_transactions_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Cash Amount</strong></td>
                            <td>Rs ' . number_format($cash_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;

        }


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }


    public function receive_delivery_status_view(Request $request, $id)
    {
        $tomorrow = Carbon::tomorrow();
        $next3days = Carbon::today()->addDays(3)->addWeekday();
        $dayAfterTomorrow = Carbon::today()->addDays(2)->toDateString();
        $days15fromNow = Carbon::parse($dayAfterTomorrow)->addDays(15)->toDateString();

        $note_data = DeliveryNote::where('id', $id)->first();

        $rider = $note_data->rider;
        if(session('role_id') !== 1 && ($rider->operation_rider_id === 1 && $rider->rider_type_id === 1)){
            return redirect()->back()->with('error', 'You are not authorized to update this delivery note!');
        }
        $require_password = false;
        if ($note_data) {

            $rider = $note_data->rider;
            if(session('role_id') !== 1 && ($rider->operation_rider_id === 1 && $rider->rider_type_id === 1)){
                return redirect()->back()->with('error', 'You are not authorized to update this delivery note!');
            }

            if ($note_data->password != null) {
                $require_password = true;
            }
            $updates_count = DeliveryNoteShipment::where('delivery_note_id', $id)->where('status', '>', 0)->count();
            $shipment_update = 0;
            $undelivered_printed = 0;
            if ($updates_count > 0) {
                $shipment_update = 1;
                if ($note_data->undelivered_print == 1) {
                    $undelivered_printed = 1;
                }
            }

            if ($note_data->status == 0) {
                $note_data_shipments = DeliveryNoteShipment::where('delivery_note_id', $id)->pluck('shipment_id')->toArray();;
                $delivered_count = 0;
                $total_count = 0;
                foreach ($note_data_shipments as $shipment_id) {
                    $shipment = Shipment::where('id', $shipment_id)->first();
                    if ($shipment->shipper_status_id == 14 || $shipment->shipper_status_id == 30) {
                        $delivered_count = $delivered_count + 1;
                    }
                    $total_count = $total_count + 1;
                }
                $total = $delivered_count / $total_count;
                $total_percentage = $total * 100;
                $percentage = number_format((float)$total_percentage, 2, '.', '');
                $where = array(7, 8, 9, 10, 12, 14, 15, 18, 56);
                $statuses = ShipmentStatus::whereIn('id', $where)->select('id', 'name')->where('status', 1)->get();
                $consignee_refused_reasons = ConsigneeRefusedReason::where('status', 1)->select('id', 'reasons')->where('status', 1)->get();
                
                return view('admin.delivery.receive.add_status')->with(['delivery_note_id' => $id, 'shipments_count' => $note_data->shipments_count, 'delivery_note_status' => $note_data->pending_status, 'shipment_update' => $shipment_update, 'undelivered_printed' => $undelivered_printed, 'shipment_statuses' => $statuses, 'percentage' => $percentage, 'tomorrow' => $tomorrow, 'next3days' => $next3days, 'dayAfterTomorrow' => $dayAfterTomorrow, 'days15FromNow' => $days15fromNow, 'require_password' => $require_password, 'consignee_refused_reasons' => $consignee_refused_reasons]);
            } else {
                return redirect(route('admin.delivery.receive.index'));
            }
        } else {
            return redirect()->back()->with('error', 'Delivery note not found!');
        }
    }

    public function receive_delivery_status_list(Request $request, $id)
    {
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->join('shipments', 'shipments.id', '=', 'dns.shipment_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users', 'shipments.user_id', '=', 'users.id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 'shipments.id')
                    ->where('consolidations.consolidation_id', '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.reference_1_id = delivery_notes.id and shipments_journey.rider_id IS NOT NULL)'));
            })
            ->leftJoin('shipments_journey as sjl', function ($join) {
                $join->on('sjl.shipment_id', '=', 'shipments.id')
                    ->where('sjl.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.reference_1_id = delivery_notes.id)'));
            })
            ->leftjoin('shipment_status as rss', 'rss.id', '=', 'sj.shipper_status_id')
            ->leftjoin('shipment_status_reason as rssr', 'rssr.id', '=', 'sj.status_reason_id')
            ->leftJoin('rider_deliveries as rds', function ($join) {
                $join->on('rds.delivery_note_id', '=', 'delivery_notes.id')
                    ->where('rds.id', '=',
                        DB::raw('(select max(id) from rider_deliveries where rider_deliveries.delivery_note_id = delivery_notes.id and rider_deliveries.shipment_id = shipments.id)'));
            })
            ->select(['delivery_notes.id as delivery_note', 'shipments.tracking_number', 'shipments.id as shId', 'shipments.open_box as open_box', 'oc.name as destination', 'shipments.consignee_name', 'shipments.consignee_address as address', 'shipments.amount', 'users.id as shipper_id', 'users.name as shipper', 'bt.booking_type as service_type', 'ss.name as current_status', 'ss.id as current_status_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id', 'crm.id as complaint', 'shipments.packaging_material_charges', 'shipments.packaging_material_request', 'dns.ordering', 'consolidations.consolidation_id', 'sj.shipper_status_id as rider_status_id', 'sj.status_reason_id as rider_status_reason_id', 'rss.name as rider_status', 'rssr.name as rider_reason', 'shipments.nsa_osa_status as nsa_osa_status', 'sjl.shipper_status_id as latest_rider_status_id', 'sjl.received_or_refused_by', 'rds.ccd_image as ccd_image','sjl.relation','sjl.cnic'])
            ->where('delivery_notes.id', $id)
            ->orderBy('dns.ordering', 'asc', 'dns.shipment_id', 'asc');

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->setRowAttr([
                'class' => function ($deliveries) {
                    if ($deliveries->current_status_id !== 5) {
                        $delivered_statuses = array(14, 30, 36, 37);
                        if (in_array($deliveries->current_status_id, $delivered_statuses)) {
                            return 'statusDelivered';
                        } else if ($deliveries->current_status_id == 12) {
                            return 'statusReturn';
                        } else {
                            return 'statusUpdated';
                        }
                    } else if ($deliveries->complaint != null) {
                        return 'complaint_row';
                    } else {
                        return '';
                    }
                },
                'amount' => function ($deliveries) {
                    return $deliveries->amount;
                },
                'consolidation_id' => function ($deliveries) {
                    if ($deliveries->consolidation_id != null) {
                        return $deliveries->consolidation_id;
                    } else {
                        return '';
                    }
                }
            ])
            ->addColumn('collection_amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('users.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('users.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('users.name', 'users.name $1, usi.poc $1')
            ->addColumn('shipment_id_padded', function ($deliveries) {
                return str_pad($deliveries->shId, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('received_or_refused_by', function ($deliveries) {
                $receiver = $deliveries->received_or_refused_by;

                $received_refused_input = '<input class="form-control form-control-sm" name="received_refused_input[' . $deliveries->shId . ']" placeholder="Enter Name" value="' . $receiver . '">';
                return $received_refused_input;

            })
            ->addColumn('attempts', function ($deliveries) {
                $attempt_counts = ShipmentsJourney::where(['shipment_id' => $deliveries->shId, 'shipper_status_id' => 5, 'verification' => 1])->count();
                return $attempt_counts;
            })
            ->addColumn('open_box', function ($deliveries) {
                if ($deliveries->open_box == 1) {
                    $open_box_checkbox = '<input type="checkbox" class="open_box" name="open_box[' . $deliveries->shId . ']" checked>';
                } else {
                    $open_box_checkbox = '<input type="checkbox" class="open_box" name="open_box[' . $deliveries->shId . ']">';
                }
                return $open_box_checkbox;
            })
            ->addColumn('consolidation', function ($deliveries) {
                $consolidations = self::check_consolidation($deliveries->shId);
                $consol = '';
                if ($consolidations) {
                    $consol = $consolidations['order'] . '/' . $consolidations['count'];
                } else {
                    $consol = '-';
                }
                return $consol;
            })
            ->addColumn('consolidated_id', function ($deliveries) {
                if ($deliveries->consolidation_id) {
                    return $deliveries->consolidation_id;
                } else {
                    return '-';
                }
            })
            ->addColumn('status', function ($deliveries) {
                if ($deliveries->booking_type_id != 5) {
                    $flag = true;
                    $restrict_parcels_attempt = RestrictParcelsAttempt::where('shipper_id', $deliveries->shipper_id)->where('status', 1);
                    if ($restrict_parcels_attempt->exists()) {
                        $restrict_parcels_attempt = $restrict_parcels_attempt->first();
                        $attempt_counts = ShipmentsJourney::where(['shipment_id' => $deliveries->shId, 'shipper_status_id' => 5, 'verification' => 1])->count();
                        if ($attempt_counts >= $restrict_parcels_attempt->attempt_days) {
                            $flag = false;
                        }
                    }
                    if ($flag == true) {
                        if ($deliveries->packaging_material_request == 1 && $deliveries->packaging_material_charges == '') {
                            $where = array(7, 8, 9, 15, 18);
                        } else {
                            $where = array(7, 8, 9, 12, 15, 18);
                        }
                    } else {
                        $where = array(12);
                    }
                    if($deliveries->booking_type_id == 2){
                        array_push($where,56);
                    }
                }
                else {
                    $where = array(7, 8, 9, 15, 18);
                }

                $statuses = ShipmentStatus::whereIn('id', $where)->get();
                $drops = '';
                foreach ($statuses as $status) {
                    if ($deliveries->rider_status_reason_id != null && $deliveries->latest_rider_status_id != null) {
                        if ($status->id == $deliveries->rider_status_id) {
                            $drops .= '<option value="' . $status->id . '" selected="selected">' . $status->name . '</option>';
                        } else {
                            $drops .= '<option value="' . $status->id . '">' . $status->name . '</option>';
                        }
                    } else {
                        $drops .= '<option value="' . $status->id . '">' . $status->name . '</option>';
                    }
                }
                $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop[' . $deliveries->shId . ']" id="statusDrop_' . $deliveries->shId . '">' . $drops . '</select>';
                return $select;
            })
            ->addColumn('reason', function ($deliveries) {
                if ($deliveries->rider_status_reason_id != null && $deliveries->latest_rider_status_id != null) {
                    if ($deliveries->nsa_osa_status == 1) {
                        $reasons = ShipmentStatus::find($deliveries->rider_status_id)->reasons()->select('id', 'name')->whereNotIn('id', [12, 34])->orderBy('name')->get();
                    } else {
                        $reasons = ShipmentStatus::find($deliveries->rider_status_id)->reasons()->select('id', 'name')->orderBy('name')->get();
                    }
                    $drops = '';
                    foreach ($reasons as $reason) {
                        if ($reason->id == $deliveries->rider_status_reason_id) {
                            $drops .= '<option value="' . $reason->id . '" selected="selected">' . $reason->name . '</option>';
                        } else {
                            $drops .= '<option value="' . $reason->id . '">' . $reason->name . '</option>';
                        }
                    }
                    $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop[' . $deliveries->shId . ']" id="reasonDrop_' . $deliveries->shId . '">' . $drops . '</select>';
                } else {
                    $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop[' . $deliveries->shId . ']" id="reasonDrop_' . $deliveries->shId . '"></select>';
                }
                return $reason;
            })
            ->addColumn('remarks', function ($deliveries) {

                $journey_remarks = ShipmentsJourney::where('shipment_id', $deliveries->shId)->where('verification', 1)->where('shipper_status_id', '!=', 5)->latest()->first();
                $rem = ($journey_remarks->remarks != null) ? $journey_remarks->remarks : '';
                $reason = '<input type="hidden" id="remarks_id[' . $deliveries->shId . ']" name="remarks_id[' . $deliveries->shId . ']" value=""><input class="form-control form-control-sm" id="remarks[' . $deliveries->shId . ']" name="remarks[' . $deliveries->shId . ']" placeholder="Enter Remarks" value="' . $rem . '">';
                return $reason;
            })
            ->addColumn('ccd_image', function ($deliveries) {
                $image = '';
                if ($deliveries->ccd_image != null) {
                    $exists = Storage::disk('public')->exists($deliveries->ccd_image);
                    if ($exists) {
                        $image .= '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href ="' . asset(Storage::url($deliveries->ccd_image)) . '" target="_blank"><i class="la la-image"></i> View</a></div>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($deliveries->ccd_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }
                    return $image;
                } else {
                    return '-';
                }

            })
            ->addColumn('cnic', function ($deliveries) {
                if($deliveries->amount > 0)
                {
                    return '-';
                }
                else
                {   $mask = "$(this).inputmask({'mask': '99999-9999999-9', 'clearIncomplete': true})";

                    $cnic = '<input class="form-control form-control-sm cnic_input" onfocus="' . $mask. '"  name="cnic[' . $deliveries->shId . ']" placeholder="Enter CNIC" value="' . $deliveries->cnic . '"> </div>';
                    return $cnic;
                }
            })
            ->addColumn('relation', function ($deliveries) {
                if($deliveries->amount > 0)
                {
                    return '-';
                }
                else
                {
                    $relation = '<input class="form-control form-control-sm" name="relation[' . $deliveries->shId . ']" value="' . $deliveries->relation . '" placeholder="Enter Relation" value="' . $deliveries->relation . '" ></div>';
                    return $relation;
                }
            })
            ->addColumn('action', function ($deliveries) {
                return " <span class='dropdown'>
                    <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                            aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                    <div class='dropdown-menu open-left arrow'>

                      <a href='javascript:void(0);' class='dropdown-item clear'><i class='ft-rotate-cw primary'></i> Clear</a>                                         
                    </div></span>";

            })
            ->make(true);
    }

    public function receive_delivery_reason(Request $request)
    {
        $status_id = $request->status;
        $shipment_id = $request->shipment_id;
        if (Shipment::where('id', $shipment_id)->where('nsa_osa_status', 1)->exists()) {
            if (ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 5)->count() > 1) {
                $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->whereNotIn('id', [12, 34])->orderBy('name')->get();
            } else {
                $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->whereNotIn('id', [4, 6, 12, 34])->orderBy('name')->get();
            }
        } else {
            if (ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 5)->count() > 1) {
                $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->orderBy('name')->get();
            } else {
                if ($status_id == 12) {
                    $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->orderBy('name')->get();
                } else {
                    $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->whereNotIn('id', [4, 6])->orderBy('name')->get();
                }
            }
        }

        if (!$statuses->isEmpty()) {
            return response()->json(['status' => 0, 'reasons' => $statuses]);
        } else {
            return ['status' => 1, 'error' => 'No reasons are defined for this status!'];
        }

    }

    public function receive_delivery_reason_all(Request $request)
    {
        $status_id = $request->status;

        $statuses = ShipmentStatus::find($status_id)->reasons()->select('id', 'name')->orderBy('name')->get();

        if (!$statuses->isEmpty()) {
            return response()->json(['status' => 0, 'reasons' => $statuses]);
        } else {
            return ['status' => 1, 'error' => 'No reasons are defined for this status!'];
        }

    }

    public function receive_delivery_password_check(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        $password = $request->password;
        if (DeliveryNote::where('id', $delivery_note_id)->where('password', $password)->exists()) {
            return response()->json(['status' => 0]);
        } else {
            return response()->json(['status' => 1, 'error' => 'Wrong Password']);
        }

    }

    public function receive_delivery_status_submit_all(Request $request)
    {
        $open_box_ids = array();
        $received_shipments = array();
        $first_attempt_shipments = array();
        $regular_type_shipments = array();
        $restrict_status_shipments = array();
        $now = Carbon::now();
        $end_of_the_day = Carbon::today()->endOfDay()->addMinute(2);

        $rcp_sms_setting = GlobalSettings::where('type','return_confirmation_pending_sms')->first();
        $delivery_note_id = $request->delivery_note_id;
        $shipment_ids = $request->shipment_ids;
        if ($request->has('open_box_ids')) {
            $open_box_ids = $request->open_box_ids;
        }
        $selected_status = $request->selected_status;
        $password = $request->password;
        $invalid_reason_shipments = array();

        $delivery_password = DeliveryNote::where('id', $delivery_note_id)->where('password', $password);
        if (!$delivery_password->exists()) {
            return response()->json(['status' => 0, 'error' => 'Wrong Password']);
        }
        if ($selected_status == 0 || $selected_status == null || $selected_status == '') {
            return response()->json(['status' => 0, 'error' => 'Status not selected!']);
        }
        if (DeliveryNote::where('id', $delivery_note_id)->where('pending_status', 1)->exists()) {
            return response()->json(['status' => 0, 'error' => 'Delivery note already updated']);
        }
        $selected_reason = $request->selected_reason;
        $reason_for_first_attempt = array(7, 8, 35, 19, 34, 12, 27, 40);


        if ($delivery_note_id != '') {
            $restrict_statuses = array(5, 7, 8, 9, 12, 14, 15, 18, 30, 36, 37, 56);
            foreach ($shipment_ids as $index => $shipment) {
                $shipment_details = Shipment::find($shipment);
                if (!in_array($shipment_details->shipper_status_id, $restrict_statuses)) {
                    if (!in_array($shipment, $restrict_status_shipments)) {
                        array_push($restrict_status_shipments, $shipment);
                    }
                } elseif ($selected_status == 56 && $shipment_details->booking_type_id != 2){
                    if (!in_array($shipment_details->tracking_number, $regular_type_shipments)) {
                        array_push($regular_type_shipments, $shipment_details->tracking_number);
                    }
                }
                elseif (ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 5)->count() == 1) {

                    if ($selected_status == 12 && !in_array($selected_reason, $reason_for_first_attempt)) {
                        if (!in_array($shipment_details->tracking_number, $first_attempt_shipments)) {
                            array_push($first_attempt_shipments, $shipment_details->tracking_number);
                        }
                    } else {
                        if (!in_array($shipment, $received_shipments)) {
                            array_push($received_shipments, intval($shipment));
                        }
                    }
                } else {
                    if (!in_array($shipment, $received_shipments)) {
                        array_push($received_shipments, intval($shipment));
                    }
                }
            }

            foreach ($received_shipments as $shipment) {
                $shipment_details = Shipment::find($shipment);

                if ($shipment_details->booking_type_id == 5 && $selected_status == 12) {
                    continue;
                }

                /* if(ShipmentsJourney::where('shipment_id',$shipment)->where('shipper_status_id',5)->count() == 0){
                    continue;
                }*/

                if ($selected_status != 14) {
                    if (in_array($selected_reason, [3, 4, 12, 34, 50])) {

                        $phone_number = $shipment_details->consignee_phone_number_1;
                        $previous_delivered_shipments = Shipment::where(function ($query) use ($phone_number) {
                            $query->where('consignee_phone_number_1', $phone_number)
                                ->orWhere('consignee_phone_number_2', $phone_number);
                        })
                            ->where('shipper_status_id', DB::raw(14));
                        if ($previous_delivered_shipments->exists()) {
                            $invalid_reason_shipments[] = $shipment_details->tracking_number;
                            if (in_array($shipment_details->tracking_number, $first_attempt_shipments)) {
                                $index = array_search($shipment_details->tracking_number, $first_attempt_shipments);
                                if ($index !== false) {
                                    unset($first_attempt_shipments[$index]);
                                }
                            }
//                            continue;
                        }

                    }
                }

                $restrict_parcels_attempt = RestrictParcelsAttempt::where('shipper_id', $shipment_details->user_id)->where('status', 1);
                if ($restrict_parcels_attempt->exists() && $selected_status != 12 && $selected_status != 14) {
                    $restrict_parcels_attempt = $restrict_parcels_attempt->first();
                    $attempt_counts = ShipmentsJourney::where(['shipment_id' => $shipment_details->id, 'shipper_status_id' => 5, 'verification' => 1])->count();
                    if ($attempt_counts >= $restrict_parcels_attempt->attempt_days) {
                        continue;
                    }
                }
                if ($shipment_details) {
                    if ($shipment_details->nsa_osa_status == 1) {
                        if (in_array($selected_reason, [12, 34])) {
                            $selected_reason = null;
                        }
                    }
                    if (count($open_box_ids) > 0) {
                        if (in_array($shipment, $open_box_ids)) {
                            $shipment_details->open_box = 1;
                            $shipment_details->save();
                            ShipmentOpenBoxJourneyController::add($shipment, 4, Auth::id());
                        }
                    }
                    $received_refused_by_name = "received_or_refused_by.$shipment";  $cnic_ = "cnic.$shipment";  $relation_ = "relation.$shipment";
                    $journey_remarks = "remarks.$shipment";
                    if ($selected_status == 7 || $selected_status == 18) {
                        if ($shipment_details->shipper_status_id != $selected_status) {
                            ShipmentsJourneyController::add($shipment, $selected_status, NULL, $selected_reason, $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0);
                        }

                        if ($shipment_details->booking_type_id != 4) {
                            Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $selected_status]);
                        } else {
                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $selected_status]);
                        }

                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                    } else if ($selected_status == 14) {
                        if ($shipment_details->booking_type_id == 2) {
                            ShipmentsJourneyController::add($shipment, 30, 30, NULL, NULL, NULL, Auth::id(), $delivery_note_id, NULL, 1, ($request->has($received_refused_by_name) ? $request->received_or_refused_by[$shipment] : null),null,($request->has($cnic_) ? $request->cnic[$shipment] : null),($request->has($relation_) ? $request->relation[$shipment] : null));
                            Shipment::where('id', $shipment)->update(['received_amount' => $shipment_details->amount, 'shipper_status_id' => 30, 'consignee_status_id' => 30]);
                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 2]);
                        } elseif ($shipment_details->booking_type_id == 3) {
                            ShipmentsJourneyController::add($shipment, 36, 36, NULL, NULL, NULL, Auth::id(), $delivery_note_id, NULL, 1, ($request->has($received_refused_by_name) ? $request->received_or_refused_by[$shipment] : null),null,($request->has($cnic_) ? $request->cnic[$shipment] : null),($request->has($relation_) ? $request->relation[$shipment] : null));

                            Shipment::where('id', $shipment)->update(['received_amount' => $shipment_details->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 3]);

                        } elseif ($shipment_details->booking_type_id == 4) {
                            ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, Auth::id(), $delivery_note_id, NULL, 1, ($request->has($received_refused_by_name) ? $request->received_or_refused_by[$shipment] : null),null,($request->has($cnic_) ? $request->cnic[$shipment] : null),($request->has($relation_) ? $request->relation[$shipment] : null));

                            if ($shipment_details->charges_mode_id == 1) {
                                Shipment::where('id', $shipment)->update(['shipper_status_id' => 14, 'consignee_status_id' => 14]);
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 7]);
                            } else {
                                Shipment::where('id', $shipment)->update(['received_amount' => $shipment_details->amount, 'shipper_status_id' => 14, 'consignee_status_id' => 14]);
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                            }

                        } else {
                            ShipmentsJourneyController::add($shipment, 14, 14, NULL, NULL, NULL, Auth::id(), $delivery_note_id, NULL, 1, ($request->has($received_refused_by_name) ? $request->received_or_refused_by[$shipment] : null),null,($request->has($cnic_) ? $request->cnic[$shipment] : null),($request->has($relation_) ? $request->relation[$shipment] : null));
                            Shipment::where('id', $shipment)->update(['received_amount' => $shipment_details->amount, 'shipper_status_id' => 14, 'consignee_status_id' => 14]);

                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                        }
                    } else if ($selected_status == 56) {
                        if ($shipment_details->booking_type_id == 2) {
                            if ($shipment_details->shipper_status_id != $selected_status) {
                                ShipmentsJourneyController::add($shipment, $selected_status, $selected_status, $selected_reason, $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0);
                            }
                            Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $selected_status, 'consignee_status_id' => $selected_status]);

                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                        }
                    } else {
                        if ($selected_status == 12) {
                            $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                            if ($return_assign_shipment) {
                                $return_assign_shipment->status = 0;
                                $return_assign_shipment->save();

                                $return_assign_log = new ReturnAssignedShipmentLogs();
                                $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                $return_assign_log->status = 6;
                                $return_assign_log->assigned_by = Auth::id();
                                $return_assign_log->save();
                            }
                            if(in_array(session('role_id'),[18,19]) && $selected_status == 12 && in_array($selected_reason,[1,6,8,19]) && ($rcp_sms_setting->setting_value == 1) && !ReturnConfirmationPendingSmsAttempt::where('shipment_id',$shipment)->where('status',0)->exists()){
                                //dispatch(new RCPSmsToConsignee($shipment));
                                ReturnConfirmationPendingSmsAttempt::create(['shipment_id' => $shipment, 'status' => 0, 'count' => 0]);
                            }

                        }
                        if ($shipment_details->shipper_status_id != $selected_status) {
                            if ($shipment_details->packaging_material_request == 0) {
                                ShipmentsJourneyController::add($shipment, $selected_status, $selected_status, $selected_reason, $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0);
                            } else if ($shipment_details->packaging_material_charges != '' && $shipment_details->packaging_material_request == 1) {
                                ShipmentsJourneyController::add($shipment, $selected_status, $selected_status, $selected_reason, $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0);
                            } else if ($shipment_details->packaging_material_charges == null && $shipment_details->packaging_material_request == 1) {
                                if ($selected_status != 12) {
                                    ShipmentsJourneyController::add($shipment, $selected_status, $selected_status, $selected_reason, $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0);
                                }
                            }

                        }
                        if ($shipment_details->booking_type_id != 4) {
                            if ($shipment_details->packaging_material_request == 0) {
                                Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $selected_status, 'consignee_status_id' => $selected_status]);
                            } else if ($shipment_details->packaging_material_charges != '' && $shipment_details->packaging_material_request == 1) {
                                Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $selected_status, 'consignee_status_id' => $selected_status]);
                            } else if ($shipment_details->packaging_material_charges == null && $shipment_details->packaging_material_request == 1) {
                                if ($selected_status != 12) {
                                    Shipment::where('id', $shipment)->update(['shipper_status_id' => $selected_status, 'consignee_status_id' => $selected_status]);
                                }

                            }
                        } else {
                            Shipment::where('id', $shipment)->update(['shipper_status_id' => $selected_status, 'consignee_status_id' => $selected_status]);
                        }

                        if ($shipment_details->packaging_material_charges == null && $shipment_details->packaging_material_request == 1) {
                            if ($selected_status != 12) {
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                            }
                        } else {
                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                        }
                    }
                    if ($selected_status != 14) {
                        //auto agent assigning
                        $data = array();
                        $data['delivery_note_id'] = $delivery_note_id;
                        $data['shipment_id'] = $shipment;
                        dispatch(new ProcessAgentCallMonitoring($data));
                    }
                }
            }

            $delivery_note_data = DeliveryNote::find($delivery_note_id);
            $pending_status = 0;
            $updates_count = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('status', 0)->count();
            if ($updates_count == 0) {
                $delivery_note_data->pending_status = 1;
                $delivery_note_data->pending_for_verification_at = Carbon::now();
            }

            $delivered_status = array(14, 30, 36, 37);
            $delivered_shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
            $dncc_amount = Shipment::whereIn('id', $delivered_shipment_ids)->whereIn('shipper_status_id', $delivered_status)->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->where('booking_type_id', '!=', 4);
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('booking_type_id', '=', 4)
                            ->where('charges_mode_id', '=', 2);
                    });
            })->sum('received_amount');
            $count = Shipment::whereIn('id', $delivered_shipment_ids)->whereIn('shipper_status_id', $delivered_status)->count();

            $delivery_note_data->delivered_shipments = $count;
            $delivery_note_data->received_cod_amount = $dncc_amount;
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->status_updated_at = Carbon::now();
            $delivery_note_data->updated_by = Auth::id();
            $delivery_note_data->save();
            $response = array();
            if(count($invalid_reason_shipments) > 0){
                $response['invalid_shipments'] = $invalid_reason_shipments;
            }
            if(count($regular_type_shipments) > 0){
                $response['not_replacement_shipments'] = $regular_type_shipments;
            }
            if($response){
                $response['status'] = 2;
                $response['success'] = 'Statuses updated successfully!';
                $response['first_attempt_shipments'] = $first_attempt_shipments;
                return response()->json($response);
            } else {
                return response()->json(['status' => 1, 'success' => 'Statuses updated successfully!', 'first_attempt_shipments' => $first_attempt_shipments]);
            }

        } else {
            return response()->json(['status' => 0, 'error' => 'Delivery note not found!']);
        }
    }

    public function receive_delivery_status_submit(Request $request)
    {
        $consignee_cnic =  $request->cnic;
        $consignee_relation =  $request->relation;
        $now = Carbon::now();
        $end_of_the_day = Carbon::today()->endOfDay()->addMinute(2);

        $open_box_ids = array();
        $shipments = explode(',', $request->shipment_ids);
        $rcp_sms_setting = GlobalSettings::where('type','return_confirmation_pending_sms')->first();
        $open_box_ids = explode(',', $request->open_box_ids);
        $invalid_reason_shipments = array();
        $delivery_note_id = $request->delivery_note_id;
        $password = $request->password;
        if (!DeliveryNote::where('id', $delivery_note_id)->where('password', $password)->exists()) {
            return redirect()->back()->with('error', 'Wrong Password!');
        }
        if ($delivery_note_id != '') {

            $restrict_statuses = array(5, 7, 8, 9, 12, 14, 15, 18, 30, 36, 37, 56);
            foreach ($shipments as $index => $shipment) {
                $shipment_details = Shipment::find($shipment);
                if (!in_array($shipment_details->shipper_status_id, $restrict_statuses)) {
                    unset($shipments[$index]);
                }
            }

            foreach ($shipments as $key=>$shipment) {
                $consolidation_shipments = ConsolidationShipments::where('shipment_id', $shipment);
                if (!$consolidation_shipments->exists()) {
                    $statusId = "reason_drop.$shipment";
                    $status_drop = "status_drop.$shipment";
                    $shipment_status = Shipment::where('id', $shipment)->first();
                    if ($shipment_status->booking_type_id == 5 && ($request->has($status_drop) && $request->status_drop[$shipment] == 12)) {
                        continue;
                    }
                    if ($request->has($status_drop) && in_array($request->status_drop[$shipment], [14, 30, 36])) {
                        continue;
                    }
                    if (count($open_box_ids) > 0) {
                        if (in_array($shipment, $open_box_ids)) {
                            $shipment_status->open_box = 1;
                            $shipment_status->save();
                            ShipmentOpenBoxJourneyController::add($shipment, 4, Auth::id());
                        }
                    }
                    if ($request->has($status_drop) && $request->has($statusId)) {
                        if (in_array($request->reason_drop[$shipment], [3, 4, 12, 34, 50])) {
                            $current_shipment = Shipment::find($shipment);
                            $phone_number = $current_shipment->consignee_phone_number_1;
                            $previous_delivered_shipments = Shipment::where(function ($query) use ($phone_number) {
                                $query->where('consignee_phone_number_1', $phone_number)
                                    ->orWhere('consignee_phone_number_2', $phone_number);
                            })
                                ->where('shipper_status_id', DB::raw(14));
                            if ($previous_delivered_shipments->exists()) {
                                $invalid_reason_shipments[] = $current_shipment->tracking_number;
                                // continue;
                            }
                        }
                    }
                    if ($request->has($status_drop) && $request->status_drop[$shipment] != null) {
                        if ($request->status_drop[$shipment] == 7 || $request->status_drop[$shipment] == 18) {
                            if ($shipment_status->shipper_status_id != $request->status_drop[$shipment]) {
                                ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], NULL, ($request->has($statusId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0,NULL , NULL, NULL , NULL , $request->remarks_id[$shipment]);

                            }

                            if ($shipment_status->booking_type_id != 4) {
                                Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $request->status_drop[$shipment]]);
                            } else {
                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment]]);
                            }

                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);


                        } else if ($request->status_drop[$shipment] == 56) {
                            if ($shipment_status->booking_type_id == 2) {
                                if ($shipment_status->shipper_status_id != $request->status_drop[$shipment]) {
                                    ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($statusId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0,NULL , NULL , NULL , NULL , $request->remarks_id[$shipment]);
                                }
                                Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                            }

                        } else {
                            if ($request->status_drop[$shipment] == 12) {
                                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                                if ($return_assign_shipment) {
                                    $return_assign_shipment->status = 0;
                                    $return_assign_shipment->save();

                                    $return_assign_log = new ReturnAssignedShipmentLogs();
                                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                    $return_assign_log->status = 6;
                                    $return_assign_log->assigned_by = Auth::id();
                                    $return_assign_log->save();
                                }
                            }
                           /* if(in_array(session('role_id'),[18,19]) && in_array($request->reason_drop[$shipment],[1,6,8,19]) && ($rcp_sms_setting->setting_value == 1) && ($now > $end_of_the_day)){
                                dispatch(new RCPSmsToConsignee($shipment));
                            }*/
                        }
                        if ($shipment_status->shipper_status_id != $request->status_drop[$shipment]) {
                            if($shipment_status->packaging_material_request == 0){
                                
                                ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($statusId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0,NULL , NULL , NULL , NULL , $request->remarks_id[$shipment]);
                            }else if($shipment_status->packaging_material_charges != '' && $shipment_status->packaging_material_request == 1){
                                ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($statusId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0,NULL , NULL , NULL , NULL , $request->remarks_id[$shipment]);
                            }else if($shipment_status->packaging_material_charges == null && $shipment_status->packaging_material_request == 1){
                                if($request->status_drop[$shipment] != 12){
                                    ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($statusId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0,NULL , NULL , NULL , NULL , $request->remarks_id[$shipment]);
                                } else if ($shipment_status->packaging_material_charges == null && $shipment_status->packaging_material_request == 1) {
                                    if ($request->status_drop[$shipment] != 12) {
                                        ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($statusId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $delivery_note_id, NULL, 0,NULL , NULL , NULL , NULL , $request->remarks_id[$shipment]);
                                    }
                                }

                            }
                            if ($shipment_status->booking_type_id != 4) {
                                if ($shipment_status->packaging_material_request == 0) {
                                    Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                                } else if ($shipment_status->packaging_material_charges != '' && $shipment_status->packaging_material_request == 1) {
                                    Shipment::where('id', $shipment)->update(['received_amount' => null, 'shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                                } else if ($shipment_status->packaging_material_charges == null && $shipment_status->packaging_material_request == 1) {
                                    if ($request->status_drop[$shipment] != 12) {
                                        Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                                    }
                                }
                            } else {
                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                            }
                            if ($request->has($statusId)) {
                                if (($request->reason_drop[$shipment] == 12) && $shipment_status->booking_type_id != 4) {
                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 9]);
                                } else {
                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                }
                            } else {
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                            }
                        }
                        $data = array();
                        $data['delivery_note_id'] = $delivery_note_id;
                        $data['shipment_id'] = $shipment;
                        dispatch(new ProcessAgentCallMonitoring($data));

                    }

                }
//                ShipmentsJourney::where('shipment_id', $shipment)->update(['cnic' => isset($consignee_cnic[$key]) ? $consignee_cnic[$key] : '','relation' => isset($consignee_relation[$key]) ? $consignee_relation[$key] : '']);
            }
            $delivery_note_data = DeliveryNote::find($delivery_note_id);
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->status_updated_at = Carbon::now();
            $delivery_note_data->updated_by = Auth::id();
            $updates_count = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('status', 0)->count();
            if ($updates_count == 0) {
                $delivery_note_data->pending_status = 1;
                $delivery_note_data->pending_for_verification_at = Carbon::now();
            }
            $delivery_note_data->save();
            $delivered_status = array(14, 30, 36, 37);
            $delivered_shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
            $dncc_amount = Shipment::whereIn('id', $delivered_shipment_ids)->whereIn('shipper_status_id', $delivered_status)->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->where('booking_type_id', '!=', 4);
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->where('booking_type_id', '=', 4)
                            ->where('charges_mode_id', '=', 2);
                    });
            })->sum('received_amount');
            $count = Shipment::whereIn('id', $delivered_shipment_ids)->whereIn('shipper_status_id', $delivered_status)->count();

            $delivery_note_data->delivered_shipments = $count;
            $delivery_note_data->received_cod_amount = $dncc_amount;
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->status_updated_at = Carbon::now();
            $delivery_note_data->updated_by = Auth::id();
            $delivery_note_data->save();

            if (count($invalid_reason_shipments) > 0) {
                $invalid_shipments = implode(", ", $invalid_reason_shipments);
                return redirect()->back()->with(['success' => 'Statuses updated successfully!', 'info' => 'Same consignee details found which are already marked as delivered of following Shipment(s): ' . $invalid_shipments]);
            } else {
                return redirect()->back()->with('success', 'Statuses updated successfully!');
            }
        } else {
            return redirect()->back()->with('error', 'Delivery note not found!');
        }
    }

    public function rider_category_bypass_request()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 552);
        if (session('role_id') != 1) {
            $riders = Rider::where('status', 1)->whereIn('city_id', session('hubs'))->where('blacklist', 0)->select('id', 'name','rider_category_id')->get();
        } else {
            $riders = Rider::where('status', 1)->where('blacklist', 0)->select('id', 'name','rider_category_id')->get();
        }
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        $rider_cat = RiderCategory::whereIn('id',[1,2])->get();
        return view('admin.delivery.note.rider_category_request')->with(['riders' => $riders, 'hubs' => $hubs, 'riders_cat' => $rider_cat]);
    }

    public function rider_cat_request_list(Request $requests)
    {
        if ($requests->get('excel') && $requests->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 553);
        }
        $request = RiderCategoryByPass::join('riders as r', 'r.id', '=', 'rider_category_by_passes.rider_id')
            ->leftjoin('admins as a','a.id','=','rider_category_by_passes.requested_by')
            ->leftjoin('admins as ad','ad.id','=','rider_category_by_passes.approved_by')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->select(['r.id as rider_id','r.name as rider', 'rider_category_by_passes.reason as reason', 'rider_category_by_passes.requested_at as requested_at','a.name as requested_by', 'rider_category_by_passes.approved_at as approved_at', 'ad.name as approved_by', 'rider_category_by_passes.status as status','rider_category_by_passes.rider_category_id as rider_type','rider_category_by_passes.id as id']);
        if ($requests->search_hub) {
            $request = $request->where('c.hub_id', $requests->search_hub);
        }

        $datatables = Datatables::of($request)
            ->editColumn('status', function ($result) {
                if ($result->status == 0) {
                    return 'Requested';
                } else {
                    return 'Approved';
                }
            })
            ->editColumn('rider_type', function ($result) {
                if ($result->rider_type == 1) {
                    return 'light';
                } else {
                    return 'heavy';
                }
            })
            ->filterColumn('rider_type', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('light', $keyword) !== FALSE) {
                    $query->where('rider_category_by_passes.rider_category_id', '=', 1);
                }
                else if (strpos('heavy', $keyword) !== FALSE) {
                    $query->where('rider_category_by_passes.rider_category_id', '=', 2);
                }
                else {
                    $query->whereRaw('FALSE');
                }
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('requested', $keyword) !== FALSE) {
                    $query->where('rider_category_by_passes.status', '=', 0);
                }
                else if (strpos('approved', $keyword) !== FALSE) {
                    $query->where('rider_category_by_passes.status', '=', 1)->orwhere('rider_category_by_passes.status', '=', 2);
                }
                else {
                    $query->whereRaw('FALSE');
                }
            })
            ->addColumn("action", function ($result) {
                if ((session('role_id') == 1 || count(array_intersect([760], session('permissions'))) !== 0) && $result->status == 0) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (($result->status == 0) && (session('role_id') == 1 || in_array(760, session('permissions')))) {
                        $dropdown .= '<button type="button" class="dropdown-item approve_request" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve </div></button>';
                    }
                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);

    }

    public function check_rider_cat(Request $request)
    {
        if($request->rider_cat == 1)
        {
            $rider = Rider::where('rider_category_id',2)->get();
            return response()->json(['status' => 1, 'rider' => $rider]);
        }
        elseif($request->rider_cat == 2)
        {
            $rider = Rider::where('rider_category_id',1)->get();
            return response()->json(['status' => 1, 'rider' => $rider]);
        }
    }

//    public function check_dn_against_rider(Request $request)
//    {
//        $rider = $request->id;
//        $delivery_note = DeliveryNote::where('rider_id', $rider)->where('dncc_status', 0)->latest()->first();
//        if ($delivery_note) {
//            return response()->json(['status' => 1, 'note' => $delivery_note]);
//        } else {
//            return response()->json(['status' => 0, 'error' => 'No Delivery Note Found For the Rider']);
//        }
//    }

    public function rider_category_submit(Request $request)
    {

        $rider_bypass = RiderCategoryByPass::where('rider_id',$request->rider_id)->where('status',0)->latest()->first();
        if($rider_bypass)
        {
            return redirect()->route('admin.delivery.note.rider_category_request')->with(['error' => 'Request Already Present']);
        }
        else
        {
            $check_rider_category = Rider::where('id',$request->rider_id)->select('rider_category_id')->first();
            if($check_rider_category->rider_category_id == 1)
            {
                $rider_details = new RiderCategoryByPass();
                $rider_details->rider_category_id = 2;
                $rider_details->rider_id = $request->rider_id;
                $rider_details->reason = $request->reason;
                $rider_details->status = 0;
                $rider_details->requested_by = auth()->id();
                $rider_details->requested_at = Carbon::now();
                $rider_details->save();
                return redirect()->route('admin.delivery.note.rider_category_bypass_request')->with(['success' => 'Request Added']);
            }
            else
            {
                $rider_details = new RiderCategoryByPass();
                $rider_details->rider_category_id = 1;
                $rider_details->rider_id = $request->rider_id;
                $rider_details->reason = $request->reason;
                $rider_details->status = 0;
                $rider_details->requested_by = auth()->id();
                $rider_details->requested_at = Carbon::now();
                $rider_details->save();
                return redirect()->route('admin.delivery.note.rider_category_bypass_request')->with(['success' => 'Request Added']);
            }
        }
    }

    public function rider_category_approve(Request $request)
    {
        $rider_category_detail = RiderCategoryByPass::find($request->id);

        if ($rider_category_detail->status == 0) {
            $rider_category_detail->status = 1;
            $rider_category_detail->approved_at = Carbon::now();
            $rider_category_detail->approved_by = Auth::id();
            $rider_category_detail->save();

            return response()->json(['status' => 1, 'success' => 'Request Approved']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Status already approved']);
        }
    }

    public function rider_category_bypass_weight()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 554);
        $settings = GlobalSettings::where('type', 'light_heavy_weight_for_shipment')->select('text')->first();
        $weight = $settings->text;
        return view('admin.delivery.note.rider_category_bypass_weight')->with(['weight' => $weight]);
    }

    public function weight_store(Request $request)
    {
        if ($request->weight) {
            $existing_weight = GlobalSettings::where('type', 'light_heavy_weight_for_shipment')->select('text')->first();
            if ($request->weight == $existing_weight->text) {
                return redirect()->back()->with('error', 'Same Weight Entered!');
            } else {
                $User_Update = GlobalSettings::where('type', 'light_heavy_weight_for_shipment')->update(["text" => $request->weight]);
                return redirect()->back()->with('success', 'Weight Updated!');
            }
        } else {
            return redirect()->back()->with('error', 'Enter the Weight !');
        }
    }



    //ajax function
    //status 1 -> update , status 1 -> regular , status 2 -> replacement, status 3 -> try & buy  status 4 -> distribution
    public function receive_delivery_status_check(Request $request)
    {
        $note_id = $request->delivery_note_id;
        $shipments = DeliveryNoteShipment::where(['delivery_note_id' => $note_id])->count();
        if ($shipments > 0) {
            $replacements = DeliveryNoteShipment::where(['delivery_note_id' => $note_id, 'status' => 2])->get();
            if ($replacements) {
                foreach ($replacements as $shipment) {
                    $shipment_data = Shipment::where('id', $shipment->shipment_id);
                    if ($shipment_data->exists()) {
                        $data = $shipment_data->first();
                        if ($data->booking_type_id == 2) {
                            $replacement_ids[] = $data->id;
                        }
                    }
                }
            }

            $trybuy = DeliveryNoteShipment::where(['delivery_note_id' => $note_id, 'status' => 3])->get();
            if ($trybuy) {
                $trybuy_id = null;
                foreach ($trybuy as $try) {
                    $try_data = Shipment::where('id', $try->shipment_id);
                    if ($try_data->exists()) {
                        $trydata = $try_data->first();
                        if ($trydata->booking_type_id == 3) {
                            $trybuy_id = $trydata->id;
                        }
                    }
                }
            }

            $non_service_areas = DeliveryNoteShipment::where(['delivery_note_id' => $note_id, 'status' => 9])->get();
            if ($non_service_areas) {
                $non_service_area_shipments = array();
                foreach ($non_service_areas as $non_service_area) {
                    $nsa_data = Shipment::where('id', $non_service_area->shipment_id);
                    if ($nsa_data->exists()) {
                        $nsa_data = $nsa_data->first();
                        if ($nsa_data->booking_type_id != 4) {
                            $non_service_area_shipments[] = $nsa_data->id;
                        }
                    }
                }
            }

            $regular_shipment = DeliveryNoteShipment::where(['delivery_note_id' => $note_id, 'status' => 6])->get();
            if ($regular_shipment) {
                $distribution_id = null;
                foreach ($regular_shipment as $regular) {
                    $regular_data = Shipment::where('id', $regular->shipment_id)->where('user_id', 10354)->where('booking_type_id', 1);
                    if ($regular_data->exists()) {
                        $regular_data = $regular_data->first();
                        if (ShipmentDistributionProduct::where('shipment_id', $regular_data->id)->where('status', 0)->exists()) {
                            $distribution_id = $regular_data->id;
                        }
                    }
                }
            }

            if (!empty($replacement_ids)) {
                return ['status' => 2, 'success' => 'Shipment is replacement!', 'booking_type' => 2, 'replacement' => $replacement_ids];
            } else if (!empty($non_service_area_shipments)) {
                return ['status' => 9, 'success' => 'Shipment is Non Service Area!', 'non_service_area_shipments' => $non_service_area_shipments];
            } elseif ($trybuy_id != null) {
                return ['status' => 3, 'success' => 'Shipment is try and buy!', 'booking_type' => 3, 'try' => $trybuy_id];
            } elseif ($distribution_id != null) {
                return ['status' => 4, 'success' => 'Shipment is distribution!', 'booking_type' => 1, 'distribution' => $distribution_id];
            }
        } else {
            return ['status' => 0, 'error' => 'No shipments updated!'];
        }
    }

    public function receive_delivery_get_replacements(Request $request)
    {
        $shipments = $request->replacements;
        $parcel = array();
        foreach ($shipments as $shipment) {
            $parcel[] = Shipment::select('id', 'tracking_number', 'booking_type_id')->where('id', $shipment)->first();
            foreach ($parcel as $p) {
                $p['booking_type_id'] = $p->booking_type->booking_type;
            }
        }
        return ['status' => 0, 'data' => $parcel];
    }

    public function receive_delivery_replacements_submit(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        $shipments = explode(',', $request->shipment_id_list);
        foreach ($shipments as $shipment) {
            if ($request->weight[$shipment] != '') {
                Shipment::where('id', $shipment)->update(['replacement_weight' => $request->weight[$shipment]]);
                DeliveryNoteShipment::where(['shipment_id' => $shipment, 'delivery_note_id' => $delivery_note_id])->update(['status' => 4]);
            }
        }
        $delivery_note_data = DeliveryNote::find($request->delivery_note_id);
        $delivery_note_data->last_updated_at = Carbon::now();
        $delivery_note_data->status_updated_at = Carbon::now();
        $delivery_note_data->save();
        return redirect()->back()->with(['success' => 'Selected Replacement\'s weight updated!']);
    }

    //NSA Shipments DATA
    public function nsa_shipments_data(Request $request)
    {
        $shipments = $request->nsa_shipments;
        $delivery_note_id = $request->delivery_note_id;
        $shipments_data = array();
        foreach ($shipments as $shipment) {
            $remark = '';
            $shipments_data[$shipment] = Shipment::select('id', 'tracking_number', 'consignee_address', 'consignee_city_id', 'user_id')->where('id', $shipment)->first();
            $remark = ShipmentsJourney::where('reference_1_id', $delivery_note_id)->where('shipment_id', $shipment)->latest()->first();
            $shipments_data[$shipment]['remarks'] = ($remark->remarks != null) ? $remark->remarks : '';
            foreach ($shipments_data as $data) {
                $data['consignee_city_id'] = $data->consignee_city->name;
                $data['user_id'] = $data->user->name;

            }

        }
        return response()->json(['status' => 0, 'shipments' => $shipments_data]);
    }

    public function nsa_shipments_submit(Request $request)
    {

        $delivery_note_id = $request->delivery_note_id;
        $shipments = explode(',', $request->nsa_shipment_ids);
        foreach ($shipments as $shipment) {
            if ($request->charges[$shipment] != '' && $request->remarks[$shipment] != '') {
                Shipment::where('id', $shipment)->update(['nsa_osa_estimated_charges' => $request->charges[$shipment]]);
                DeliveryNoteShipment::where(['shipment_id' => $shipment, 'delivery_note_id' => $delivery_note_id])->update(['status' => 10]);
                ShipmentsJourney::where('reference_1_id', $delivery_note_id)->where('shipment_id', $shipment)->update(['remarks' => $request->remarks[$shipment]]);
            }
        }
        $delivery_note_data = DeliveryNote::find($request->delivery_note_id);
        $delivery_note_data->last_updated_at = Carbon::now();
        $delivery_note_data->status_updated_at = Carbon::now();
        $delivery_note_data->save();
        return redirect()->back()->with(['success' => 'Selected NSA Shipment(s) charges updated!']);
    }

    //try buy modal
    public function receive_delivery_get_trybuys(Request $request)
    {
        $shipment = $request->trybuy;
        if (isset($shipment)) {
            $product = array();
            $amount = Shipment::where('id', $shipment)->select('amount')->first();
            $parcel = ShipmentItem::where('shipment_id', $shipment)->get();
            foreach ($parcel as $item) {
                $product[] = ['pid' => $item->id, 'type' => $item->product->product_name, 'description' => ($item->description == '') ? ' - ' : $item->description, 'price' => $item->price];
//
            }
            return ['status' => 0, 'data' => $product, 'total_cod' => $amount->amount];
        } else {
            return ['status' => 1, 'error' => 'No Shipment found'];
        }
    }

    public function receive_delivery_trybuys_submit(Request $request)
    {
        $item_ids = explode(',', $request->trybuy_id_list);

        if (!empty($item_ids)) {
//            $cod = $request->trybuy_cod;
            $checked = $request->item_checked;
            $unchecked = $request->item_unchecked;
            $total_cod = 0;
            foreach ($item_ids as $item_id) {
                $shipment_item = ShipmentItem::find($item_id);
                $total_cod += $shipment_item->price;
                $shipment_item->bought = 1;
                $shipment_item->save();
            }
            $shipment = Shipment::find($request->trybuy_shipment_id);
            $total_cod += $shipment->try_and_buy_fees;
            if ($checked != $unchecked) {
                Shipment::where('id', $request->trybuy_shipment_id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 37, 'consignee_status_id' => 37]);
                ShipmentsJourneyController::add($request->trybuy_shipment_id, 37, 37, NULL, NULL, NULL, Auth::id(), $request->delivery_note_trybuy, NULL, 0);
            } elseif ($checked == $unchecked) {
                Shipment::where('id', $request->trybuy_shipment_id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
            }
            ShipmentChargesController::cash_handling($request->trybuy_shipment_id);
            DeliveryNoteShipment::where(['shipment_id' => $request->trybuy_shipment_id, 'delivery_note_id' => $request->delivery_note_trybuy])->update(['status' => 5]);

            $delivery_note_data = DeliveryNote::find($request->delivery_note_trybuy);
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->status_updated_at = Carbon::now();
            $delivery_note_data->save();
            return redirect()->back()->with('success', 'Try & Buy shipment updated');
        }

    }

    //verify delivery page
    public function receive_delivery_note_verify_view(Request $request, $id)
    {
        $note_data = DeliveryNote::where('id', $id)->first();
        if ($note_data && ($note_data->pending_status == 1)) {
            $note_data_shipments = DeliveryNoteShipment::where('delivery_note_id', $id)->pluck('shipment_id')->toArray();
            $delivered_count = 0;
            $total_count = 0;
            foreach ($note_data_shipments as $shipment_id) {
                $shipment = Shipment::where('id', $shipment_id)->first();
                if ($shipment->shipper_status_id == 14 || $shipment->shipper_status_id == 30) {
                    $delivered_count = $delivered_count + 1;
                }
                $total_count = $total_count + 1;
            }
            $total = $delivered_count / $total_count;
            $total_percentage = $total * 100;
            $percentage = number_format((float)$total_percentage, 2, '.', '');
            $setting_call_verification = DeliveryCallVerificationRatio::where('min', '<=', $total_percentage)->where('max', '>', $total_percentage)->first();
//            dd($setting_call_verification);
            if ($setting_call_verification == null) {
                $verification_percentage = 0;
                $verification_shipments_count = 0;
            } else {
                $verification_percentage = $setting_call_verification['verification'];
                $verification_shipments_count = round(($total_count - $delivered_count) * ($verification_percentage / 100));
            }

            return view('admin.delivery.receive.verify_status')->with(['delivery_note_id' => $id, 'shipments_count' => $note_data->shipments_count, 'delivery_note_status' => $note_data->status, 'percentage' => $percentage, 'verification_percentage' => $verification_percentage, 'verification_shipments_count' => $verification_shipments_count]);
        } else {
            return redirect(route('admin.delivery.receive.index'))->with('error', 'Delivery Note not ready for verification!');
        }
    }

    public function receive_delivery_verify_status_list(Request $request, $id)
    {
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->join('shipments', 'shipments.id', '=', 'dns.shipment_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users', 'shipments.user_id', '=', 'users.id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as rrb', function ($join) {
                $join->on('rrb.shipment_id', '=', 'shipments.id')
                    ->where('rrb.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sjr', function ($join) {
                $join->on('sjr.shipment_id', '=', 'shipments.id')
                    ->where('sjr.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.reference_1_id = delivery_notes.id and shipments_journey.rider_id IS NOT NULL)'));
            })
            ->leftjoin('shipment_status as rss', 'rss.id', '=', 'sjr.shipper_status_id')
            ->leftjoin('shipment_status_reason as rssr', 'rssr.id', '=', 'sjr.status_reason_id')
            ->leftJoin('rider_deliveries as rds', function ($join) {
                $join->on('rds.delivery_note_id', '=', 'delivery_notes.id')
                    ->where('rds.id', '=',
                        DB::raw('(select max(id) from rider_deliveries where rider_deliveries.delivery_note_id = delivery_notes.id and rider_deliveries.shipment_id = shipments.id)'));
            })
            ->leftjoin('consignee_shipment_locations as csl', 'csl.shipment_id', '=', 'shipments.id')
            ->leftjoin('consignee_locations as pcls', 'pcls.id', '=', 'csl.previous_location_id')
            ->leftjoin('consignee_locations as ccls', 'ccls.id', '=', 'csl.current_location_id')
            ->leftjoin('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->leftjoin('shipment_open_boxes as sob', 'shipments.id', '=', 'sob.shipment_id')
            ->select(['riders.name as rider_name', 'delivery_notes.id as delivery_note', 'shipments.tracking_number', 'shipments.tracking_number as tracking_number_link', 'shipments.consignee_phone_number_1', 'shipments.id as shId', 'shipments.open_box as open_box', 'oc.name as destination', 'shipments.consignee_name', 'shipments.consignee_address as address', 'shipments.amount as amount', 'users.name as shipper', 'shipments.booking_type_id', 'bt.booking_type as service_type', 'ss.name as current_status', 'ss.id as current_status_id', 'dns.call_verification', 'dns.fake_status as fake_status', 'sj.created_at as arrival', 'usi.poc', 'rrb.received_or_refused_by', 'rrb.status_reason_id as reason_id', 'dns.ordering', 'rss.name as rider_status', 'rssr.name as rider_reason', 'rds.actual_location_latitude as actual_location_latitude', 'rds.actual_location_longitude as actual_location_longitude', 'csl.previous_location_id as previous_location_id', 'csl.current_location_id as current_location_id', 'pcls.lat as plat', 'pcls.long as plong', 'ccls.lat as clat', 'ccls.long as clong', 'rds.ccd_image as ccd_image', 'rds.otp_entered as otp_entered', 'sob.open_box_type as open_box_type','rrb.cnic','rrb.relation'])
            ->where('delivery_notes.id', $id)
            ->orderBy('dns.ordering', 'asc', 'dns.shipment_id', 'asc');

        return Datatables::of($deliveries)
            ->setRowAttr([
                'tracking_number' => function ($shipments) {
                    return $shipments->tracking_number;
                },
            ])
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number_link' class='tracking' target='_blank'>$shipments->tracking_number_link</a></u>";
            })
            ->editColumn('consignee_phone', function ($shipments) {
                return '<button type="button" class="btn btn-sm btn-outline-info align-middle consignee_info_label" rel="' . $shipments->consignee_phone_number_1 . '"><i class="la la-lg la-phone align-middle"></i> <span class="align-middle">' . $shipments->consignee_phone_number_1 . '</span></button>';
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('shipment_id_padded', function ($deliveries) {
                return str_pad($deliveries->shId, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('status', function ($deliveries) {
                $flag = true;
                $not_rcp = false;
//                if(ShipmentsJourney::where(['shipment_id' => $deliveries->shId, 'shipper_status_id' => 5, 'verification' => 1])->count() < 2)
//                {
//                    $not_rcp = true;
//                }
                $restrict_parcels_attempt = RestrictParcelsAttempt::where('shipper_id', $deliveries->shipper_id)->where('status', 1);
                if ($restrict_parcels_attempt->exists()) {
                    $restrict_parcels_attempt = $restrict_parcels_attempt->first();
                    $attempt_counts = ShipmentsJourney::where(['shipment_id' => $deliveries->shId, 'shipper_status_id' => 5, 'verification' => 1])->count();
                    if ($attempt_counts >= $restrict_parcels_attempt->attempt_days) {
                        $flag = false;
                    }
                }
                if ($flag == true) {
                    if ($deliveries->packaging_material_request == 1 && $deliveries->packaging_material_charges == '') {
                        $where = array(7, 8, 9, 15, 18, 56);
                    } else {
                        if ($deliveries->booking_type_id == 5) {
                            $where = array(7, 8, 9, 15, 18);
                        } else {
                            if ($not_rcp === true) {
                                $where = array(7, 8, 9, 15, 18);
                                if($deliveries->booking_type_id == 2){
                                    array_push($where,56);
                                }
                            } else {
                                $where = array(7, 8, 9, 12, 15, 18);
                                if($deliveries->booking_type_id == 2){
                                    array_push($where,56);
                                }
                            }

                        }
                    }
                } else {
                    if ($not_rcp === true) {
                        $where = array(14);
                    } else {
                        $where = array(12, 14);
                    }
                }

                $delivered_statuses = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46);
                $statuses = ShipmentStatus::whereIn('id', $where)->get();
                $drops = '';
                $disable = '';
                $selected_status = '';
                if (in_array($deliveries->current_status_id, $delivered_statuses)) {
                    $select = $deliveries->current_status;
                } else {
                    foreach ($statuses as $status) {
                        if ($status->id == $deliveries->current_status_id) {
                            $selected_status = 'selected';
                        } else {
                            $selected_status = '';
                        }

                        $drops .= '<option value="' . $status->id . '" ' . $selected_status . '>' . $status->name . '</option>';
                        $select = '<select class="form-control form-control-sm select2 statusDrop" status="' . $deliveries->current_status_id . '" name="status_drop[' . $deliveries->shId . ']">' . $drops . '</select>';

                    }
                }
                return $select;
            })
            ->addColumn('reason', function ($deliveries) {
                $status_reason = '';
                $reason_name = '';
                $reason_id = '';
                $delivered_statuses = array(14, 16, 30, 36, 37);
                $shipment_data = Shipment::find($deliveries->shId);
                $status_id = $shipment_data->shipment_journey()->latest()->first();
                $status_data = ShipmentStatus::where('id', $status_id->shipper_status_id)->select('id', 'name')->first();
                if ($status_id->status_reason_id != '') {

                    $status_reason = ShipmentStatusReason::where('id', $status_id->status_reason_id)->first();
                    $reason_name = $status_reason->name;
                    $reason_id = $status_reason->id;
                }
                if (in_array($deliveries->current_status_id, $delivered_statuses)) {
                    $reason = $reason_name;
                } else {
                    $reason = '<select class="form-control form-control-sm select2 reasonDrop" reasonId="' . $status_id->status_reason_id . '" name="reason_drop[' . $deliveries->shId . ']" ><option value="' . $reason_id . '">' . $reason_name . '</option></select>';
                }

                return $reason;
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('users.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('users.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('users.name', 'users.name $1, usi.poc $1')
            ->addColumn('remarks', function ($deliveries) {
                $shipment_data = Shipment::find($deliveries->shId);
                $status_id = $shipment_data->shipment_journey()->latest()->first();
                $delivered_statuses = array(14, 16, 30, 36, 37);
                if (in_array($deliveries->current_status_id, $delivered_statuses)) {
                    $remarks = $status_id->remarks;
                } else {
                    $remarks = '<input class="form-control form-control-sm" name="remarks[' . $deliveries->shId . ']" placeholder="Enter Remarks" value="' . $status_id->remarks . '">';
                }
                return $remarks;
            })
            ->addColumn('call_verification', function ($deliveries) {
                $delivered_statuses = array(14, 16, 30, 36, 37);
                if (in_array($deliveries->current_status_id, $delivered_statuses)) {
                    return '';
                } else {
                    if ($deliveries->call_verification == 1) {
                        $check = 'checked';
                    } else {
                        $check = '';
                    }
                    return '<input type="checkbox" name="call_verification[' . $deliveries->shId . ']" ' . $check . '>';
                }
            })
            ->addColumn('fake_status', function ($deliveries) {
                if ($deliveries->fake_status == 1) {
                    $check = 'checked';
                } else {
                    $check = '';
                }
                return '<input type="checkbox" name="fake_status[' . $deliveries->shId . ']" ' . $check . '>';
            })
            ->addColumn('open_box', function ($deliveries) {
                if ($deliveries->open_box == 1) {
                    $open_box_checkbox = '<input type="checkbox" class="open_box" name="open_box[' . $deliveries->shId . ']" checked>';
                } else {
                    $open_box_checkbox = '<input type="checkbox" class="open_box" name="open_box[' . $deliveries->shId . ']">';
                }
                return $open_box_checkbox;
            })
            ->addColumn('confirm_location', function ($deliveries) {
                if ($deliveries->current_status_id != 14) {
                    return '';
                } else {
                    $lat = null;
                    $long = null;
                    if ($deliveries->actual_location_latitude != null && $deliveries->actual_location_longitude != null) {
                        $lat = $deliveries->actual_location_latitude;
                        $long = $deliveries->actual_location_longitude;
                    } else if ($deliveries->previous_location_id != null) {
                        $lat = $deliveries->plat;
                        $long = $deliveries->plong;
                    }
                    if ($lat != null && $long != null) {
                        $confirm_location_checkbox = '<input type="checkbox" class="confirm_location" name="confirm_location[' . $deliveries->shId . ']">';
                    } else {
                        return '';
                    }
                    return $confirm_location_checkbox;
                }
            })
            ->addColumn('rider_location', function ($deliveries) {
                $location = '<div class="text-center">';
                if ($deliveries->actual_location_latitude != null && $deliveries->actual_location_longitude != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $deliveries->actual_location_latitude . ',' . $deliveries->actual_location_longitude . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->addColumn('existing_location', function ($deliveries) {
                $lat = null;
                $long = null;
                if ($deliveries->previous_location_id != null) {
                    $lat = $deliveries->plat;
                    $long = $deliveries->plong;
                }
                $location = '<div class="text-center">';
                if ($lat != null && $long != null) {
                    $location .= '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $lat . ',' . $long . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
                    $location .= '</div>';
                    return $location;
                } else {
                    return '-';
                }
            })
            ->addColumn('ccd_image', function ($deliveries) {
                $image = '';
                if ($deliveries->ccd_image != null) {
                    $exists = Storage::disk('public')->exists($deliveries->ccd_image);
                    if ($exists) {
                        $image .= '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href ="' . asset(Storage::url($deliveries->ccd_image)) . '" target="_blank"><i class="la la-image"></i> View</a></div>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($deliveries->ccd_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }
                    return $image;
                } else {
                    return '-';
                }

            })
            ->addColumn('otp_entered', function ($deliveries) {
                if ($deliveries->otp_entered != null) {
                    return ($deliveries->otp_entered == 1) ? "Yes" : "No";
                } else {
                    return '-';
                }
            })
            ->addColumn('open_box_type', function ($deliveries) {
                if ($deliveries->open_box_type != null) {
                    if($deliveries->open_box_type == 1){
                        return 'On Request';
                    }else{
                        return 'Forcefully';
                    }
                } else {
                    return '-';
                }
            })
//            ->addColumn('cnic', function ($deliveries) {
//                if($deliveries->amount > 0)
//                {
//                    return '-';
//                }
//                else
//                {   $mask = "$(this).inputmask({'mask': '99999-9999999-9', 'clearIncomplete': true})";
//
//                    $cnic = '<input readonly class="form-control form-control-sm cnic_input" onfocus="' . $mask. '"  name="cnic[' . $deliveries->shId . ']" placeholder="Enter CNIC" value="' . $deliveries->cnic . '"> </div>';
//                    return $cnic;
//                }
//            })
//            ->addColumn('relation', function ($deliveries) {
//                if($deliveries->amount > 0)
//                {
//                    return '-';
//                }
//                else
//                {
//                    $relation = '<input readonly class="form-control form-control-sm" name="relation[' . $deliveries->shId . ']" value="' . $deliveries->relation . '" placeholder="Enter Relation" value="' . $deliveries->relation . '" ></div>';
//                    return $relation;
//                }
//            })
            ->make(true);
    }


    // Check
    public function receive_delivery_verify_status_submit(Request $request)
    {  
        $now = Carbon::now();
        $end_of_the_day = Carbon::today()->endOfDay()->addMinute(2);
        $rcp_sms_setting = GlobalSettings::where('type','return_confirmation_pending_sms')->first();
        $delivery_note_id = $request->delivery_note_id;
        $delivery_note = DeliveryNote::find($delivery_note_id);
        $zero_cod_shipments = array();
        $shipments = explode(',', $request->shipment_ids);
        $invalid_reason_shipments = array();
//        $shipments = $request->shipment_ids;
        $lost_shipments_array = array();
        if (count($shipments) == $delivery_note->shipments_count) {
            if ($delivery_note) {

                if ($delivery_note->status == 1) {
                    return redirect(route('admin.delivery.receive.index'))->with('error', 'Delivery note already verified!');
                }
                if ($request->submit_button_id == 'statusVerifySubmit') {
                    $verification = 1;
                } else {
                    $verification = 0;
                }
                $current_time = Carbon::now();

                $shipment_count = 0;
                $dispute_shipments = array();
                $delivered_status_array = array(14, 30, 36, 37, 26, 27, 28);

                $restrict_statuses = array(5, 7, 8, 9, 12, 14, 15, 18, 30, 36, 37, 56 , 26, 27, 28);
                foreach ($shipments as $index => $shipment) {
                    $shipment_details = Shipment::find($shipment);
                    if (!in_array($shipment_details->shipper_status_id, $restrict_statuses)) {
                        unset($shipments[$index]);
                    }
                }

                $return_status_array = array(21, 22, 23, 24, 25, 44, 47, 48);
                if ($delivery_note_id != '') {
                    foreach ($shipments as $shipment) {
                        $shipment_details = Shipment::find($shipment);

                        $in_new_delivery_note = DeliveryNoteShipment::where('delivery_note_id', '>', $delivery_note_id)->where('shipment_id', $shipment)->exists();
                        $shipper_status_details = Shipment::where('id', $shipment)->first();
                        if (!$shipper_status_details) {
                            continue;
                        }
                        $call = "call_verification.$shipment";
                        $fake = "fake_status.$shipment";
                        $status_drop = "status_drop.$shipment";
                        $remarks_input = "remarks.$shipment";
                        $shipper_status_id = NULL;
                        $shipment_journey_remarks = NULL;
                        if ($request->has($status_drop)) {
                            $shipper_status_id = $request->status_drop[$shipment];
                        }
                        $reasonId = "reason_drop.$shipment";
                        $status_reason_id = NULL;
                        if ($request->has($reasonId)) {
                            $status_reason_id = $request->reason_drop[$shipment];
                        }
                        if ($request->has($remarks_input)) {
                            $shipment_journey_remarks = $request->remarks[$shipment];
                        }
                        $open_box_shipment = "open_box.$shipment";
                        $confirm_location_shipment = "confirm_location.$shipment";
                        if ($shipment_details->booking_type_id == 5 && ($request->has($status_drop) && $shipper_status_id == 12)) {
                            continue;
                        }
                        if ($request->has($status_drop) && $request->has($reasonId)) {
                            if ($shipper_status_id != 14) {
                                if (in_array($status_reason_id, [3, 4, 12, 34, 50])) {
                                    $current_shipment = Shipment::find($shipment);
                                    $phone_number = $current_shipment->consignee_phone_number_1;
                                    $previous_delivered_shipments = Shipment::where(function ($query) use ($phone_number) {
                                        $query->where('consignee_phone_number_1', $phone_number)
                                            ->orWhere('consignee_phone_number_2', $phone_number);
                                    })
                                        ->where('shipper_status_id', DB::raw(14));
                                    if ($previous_delivered_shipments->exists()) {
                                        $invalid_reason_shipments[] = $current_shipment->tracking_number;
//                                    continue;
                                    }
                                }
                            }
                        }
                        $verify_fake = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment)->first();

                        if ($verify_fake) {
                            if ($request->has($fake)) {
                                $verify_fake->fake_status = 1;
                                $verify_fake->fake_status_updated_at = Carbon::now();
                                $verify_fake->save();
                            } else {
                                $verify_fake->fake_status = 0;
                                $verify_fake->fake_status_updated_at = Carbon::now();
                                $verify_fake->save();
                            }
                        }
                        if ($request->has($open_box_shipment)) {

                            $shipment_details->open_box = 1;
                            $shipment_details->save();
                            if ($verification) {
                                ShipmentOpenBoxJourneyController::add($shipment, 5, Auth::id());
                            } else {
                                ShipmentOpenBoxJourneyController::add($shipment, 4, Auth::id());
                            }


                        }

                        if ($request->has($status_drop) && $shipper_status_id == 14) {
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
                            if (!in_array($shipper_status_details->shipper_status_id, $return_status_array)) {

                                if (!in_array($shipper_status_details->shipper_status_id, $delivered_status_array)) {

                                    $verify = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment)->first();
                                    if ($request->has($call)) {
                                        $verify->call_verification = 1;
                                        $verify->save();
                                    } else {
                                        $verify->call_verification = 0;
                                        $verify->save();
                                    }
                                    if ($request->has($status_drop) && $shipper_status_id != null) {

                                        //$shipper_status_details = Shipment::where('id', $shipment)->first();
                                        $journey = ShipmentsJourney::where('shipment_id', $shipment)->latest()->first();
                                        if ($shipper_status_details->shipper_status_id != $shipper_status_id) {
                                            if ($shipper_status_id == 7 || $shipper_status_id == 18) {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, NULL, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id]);
                                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                            } else if ($shipper_status_id == 20) {
                                                $parcel = Shipment::find($shipment);

                                                if (!$parcel->packaging_material_request) {
                                                    if ($parcel->shipper_status_id != 12) {
                                                         if(in_array(session('role_id'),[18,19]) && $shipper_status_id == 12 && in_array($request->reason_drop[$shipment],[1,6,8,19]) && ($rcp_sms_setting->setting_value == 1) && !ReturnConfirmationPendingSmsAttempt::where('shipment_id',$shipment)->where('status',0)->exists()){
                                                              //dispatch(new RCPSmsToConsignee($shipment));
                                                             ReturnConfirmationPendingSmsAttempt::create(['shipment_id' => $shipment, 'status' => 0, 'count' => 0]);
                                                          }
                            ShipmentsJourneyController::add($shipment, 12, 12, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                    }
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
                                                    ShipmentsJourneyController::add($shipment, 20, 20, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);


                                                } else {
                                                    if ($parcel->packaging_material_charges != null) {
                                                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                                                        ShipmentsJourneyController::add($shipment, 20, 20, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
//                                                        ShipmentsJourneyController::add($shipment, 17, 17, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
//
//                                                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
//
//                                                        if ($verification == 1) {
//                                                            NotificationsController::send(15, 0, $shipment);
//                                                            NotificationsController::send(16, 0, $shipment);
//                                                        }
                                                    }

                                                }
                                            } else if ($shipper_status_id == 56) {
                                                $parcel = Shipment::find($shipment);
                                                if ($parcel->booking_type_id == 2) {
                                                    ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 56, 'consignee_status_id' => 56]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                                }

                                            } else if (in_array($shipper_status_id, $delivered_status_array)) {
                                                $parcel = Shipment::where('id', $shipment)->first();
                                                if ($parcel->booking_type_id == 2) {
//                                                ShipmentsJourneyController::add($shipment, 30, 30, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 30, 'consignee_status_id' => 30]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 2]);
                                                } elseif ($parcel->booking_type_id == 3) {
                                                    if ($parcel->package_type == 0) {
//                                                    ShipmentsJourneyController::add($shipment, 36, 36, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                        Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 3]);
                                                    } else {
//                                                    ShipmentsJourneyController::add($shipment, 36, 36, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                        Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                                                    }
                                                } elseif ($parcel->booking_type_id == 4) {
                                                    ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);

                                                    if ($parcel->charges_mode_id == 1) {
                                                        Shipment::where('id', $shipment)->update(['received_amount' => 0, 'shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 7]);
                                                    } else {
                                                        Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                                                    }
                                                } else {
//                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
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

                                                        FinalChargesWebhookController::webhook_subscription($shipment);
                                                    }
                                                }
                                            } else {
                                                if ($shipper_status_details->packaging_material_request == 0) {
                                                    ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                    Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                                } else if ($shipper_status_details->packaging_material_charges != '' && $shipper_status_details->packaging_material_request == 1) {
                                                    ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                    Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                                } else if ($shipper_status_details->packaging_material_charges == null && $shipper_status_details->packaging_material_request == 1) {
                                                    if ($shipper_status_id != 12) {
                                                        ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                        Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $shipper_status_id]);
                                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                                    }
                                                }
                                            }
                                            $dispute_shipments[] = $shipment;
                                        } else if (($shipper_status_details->shipper_status_id == $shipper_status_id) && ($journey->status_reason_id != ($request->has($reasonId) ? $status_reason_id : null))) {
                                            if ($verification == 0) {

                                                $journey->status_reason_id = $request->has($reasonId) ? $status_reason_id : null;
                                                $journey->remarks = $shipment_journey_remarks;
                                                $journey->save();
                                            } else {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            }

                                        } else if (($shipper_status_details->shipper_status_id == $shipper_status_id) && ($journey->status_reason_id == ($request->has($reasonId) ? $status_reason_id : null)) && ($shipment_journey_remarks != $journey->remarks)) {
                                            if ($verification == 0) {
                                                $journey->remarks = $shipment_journey_remarks;
                                                $journey->save();
                                            } else {

                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            }


                                        } else {
                                            if ($verification == 1) {

                                                if (in_array($shipper_status_details->shipper_status_id, [14, 16, 30, 36, 37])) {
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

                                                    FinalChargesWebhookController::webhook_subscription($shipment);
//                                                ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                } else {
                                                    ShipmentsJourneyController::add($shipment, $shipper_status_id, $shipper_status_id, ($request->has($reasonId) ? $status_reason_id : null), $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                                    if ($shipper_status_details->shipper_status_id == 18) {
                                                        $lost_shipments_array[] = $shipment;
                                                    }
                                                }
                                            }
                                        }
                                        if(in_array(session('role_id'),[18,19]) && $shipper_status_id == 12 && in_array($status_reason_id,[1,6,8,19]) && ($rcp_sms_setting->setting_value == 1) && !ReturnConfirmationPendingSmsAttempt::where('shipment_id',$shipment)->where('status',0)->exists()){
                                            //dispatch(new RCPSmsToConsignee($shipment));
                                            ReturnConfirmationPendingSmsAttempt::create(['shipment_id' => $shipment, 'status' => 0, 'count' => 0]);
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

                                        if ($shipper_status_details->shipper_status_id == 14 || $shipper_status_details->shipper_status_id == 30 || $shipper_status_details->shipper_status_id == 36 || $shipper_status_details->shipper_status_id == 37) {
                                            FinalChargesWebhookController::webhook_subscription($shipment);
                                        }

                                        if (!in_array($shipper_status_details->shipper_status_id, $delivered_status_array)) {
                                            $shipment_journey = ShipmentsJourney::where('shipment_id', $parcel->id)->whereNotIn('shipper_status_id', [21, 22, 23, 24, 25, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 44, 45, 46, 47, 48])->where('reference_1_id', $delivery_note_id)->latest()->first();

                                            if ($shipment_journey) {
                                                $received_or_refused_by = $shipment_journey->received_or_refused_by;
                                                ShipmentsJourneyController::add($shipment, $shipment_journey->shipper_status_id, $shipment_journey->consignee_status_id, $shipment_journey->status_reason_id, $shipment_journey->remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification, $received_or_refused_by);
                                            } else {
                                                ShipmentsJourneyController::add($shipment, $shipper_status_details->shipper_status_id, $shipper_status_details->consignee_status_id, NULL, $shipment_journey_remarks, NULL, Auth::id(), $delivery_note_id, NULL, $verification);
                                            }

                                        }

                                        if ($parcel->amount == 0) {
                                            $zero_cod_shipments[] = $parcel->id;
                                        }

                                    }


                                }
                            }
//                        if ($verification == 1) {
//                            $restrict_parcels_attempt = RestrictParcelsAttempt::where('shipper_id', $shipment_details->user_id)->where('status', 1);
//                            if ($restrict_parcels_attempt->exists() && $request->has($status_drop) && $shipper_status_id == 12)
//                            {
//                                $restrict_parcels_attempt = $restrict_parcels_attempt->first();
//                                $attempt_counts = ShipmentsJourney::where(['shipment_id' => $shipment_details->id, 'shipper_status_id' => 5, 'verification' => 1])->count();
//                                if ($attempt_counts >= $restrict_parcels_attempt->attempt_days)
//                                {
//                                    $parcel = Shipment::find($shipment);
//                                    if (!$parcel->packaging_material_request) {
//                                        ShipmentsJourneyController::add($shipment, 20, 20, ($request->has($reasonId) ? $status_reason_id : null), 'after ' . $attempt_counts . ' attempts return', NULL, Auth::id(), $delivery_note_id, NULL, $verification);
//                                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
//                                        NotificationsController::send(15, 0, $shipment);
//                                        NotificationsController::send(16, 0, $shipment);
//
//                                        if ($parcel->booking_type_id != 4) {
//                                            ShipmentChargesController::return($shipment);
//
//                                            AdminFinanceController::add_payment($shipment, 1);
//                                        } else {
//                                            ShipmentChargesController::walk_in_return($shipment);
//
//                                            $parcel->walk_in_status = 2;
//
//                                            $parcel->save();
//
//                                            AdminFinanceController::done_payment($shipment, 1);
//                                        }
//                                    }
//                                }
//                            }
//                        }
                        } else {
                            if ($request->has($status_drop) && $shipper_status_id != null) {
                                if ($shipper_status_details->shipper_status_id != $shipper_status_id) {
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
                        //For Debriefing
                        $this->agent_call_completed($delivery_note->id);

                        if ($delivery_note->updated_by == NULL) {
                            $delivery_note->updated_by = Auth::id();
                            $delivery_note->save();
                        }

                        if (count($lost_shipments_array) > 0) {
                            NotificationsController::send(150, $lost_shipments_array);
                        }
                        if (count($invalid_reason_shipments) > 0) {
                            $invalid_shipments = implode(", ", $invalid_reason_shipments);
                            return redirect()->back()->with(['success' => 'Delivery Note verified and updated successfully!', 'info' => 'Same consignee details found which are already marked as delivered of following Shipment(s): ' . $invalid_shipments]);
                        } else {
                            return redirect()->back()->with('success', 'Delivery Note verified and updated successfully!');
                        }
                    } else {
                        $delivery_note_data = DeliveryNote::find($delivery_note_id);
                        $delivery_note_data->last_updated_at = $current_time;
                        $delivery_note_data->updated_by = Auth::id();
                        $delivery_note_data->save();
                        if (count($invalid_reason_shipments) > 0) {
                            $invalid_shipments = implode(", ", $invalid_reason_shipments);
                            return redirect()->back()->with(['success' => 'Delivery Note updated successfully!', 'info' => 'Same consignee details found which are already marked as delivered of following Shipment(s): ' . $invalid_shipments]);
                        } else {
                            return redirect()->back()->with('success', 'Delivery Note updated successfully!');
                        }
                    }
                } else {
                    return redirect()->back()->with('error', 'Delivery note not found!');
                }
            } else {
                return redirect()->back()->with('error', 'Shipments not found!');
            }
        } else {
            return redirect()->back()->with('error', 'Shipments count does not match!');
        }


    }

    //For Debriefing
    public function agent_call_completed($deliverynote)
    {
        $agents_check = AgentCallMonitoring::where('delivery_note_id', '=', $deliverynote)->where('completed', '=', '0')->update(array('completed' => 1));
    }

    //print dncc
    public function dncc_print(Request $request)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note Cash Collection</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      
                      .w-150 {
                        width: 150px;
                      }
                      
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {

            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
//            $shipment_details = '
//                      <table class="table table-bordered border" style="margin-bottom: 10rem !important;">
//                        <tbody>
//                          <tr>
//                            <td class="color primary"><strong>S. No.</strong></td>
//                            <td class="color primary"><strong>Tracking No.</strong></td>
//                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
//                            <td class="color primary"><strong>Consignee Address</strong></td>
//                            <td class="color primary"><strong>Service Type</strong></td>
//                            <td class="color primary"><strong>Client Name & Phone</strong></td>
//                            <td class="color primary"><strong>Weight</strong></td>
//                            <td class="color primary"><strong>Collection Amount</strong></td>
//                          </tr>
//        ';
//
//
            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
////                    $shipment = Shipment::find($parcel->shipment_id);
//                $check_walk_in = GlobalSettings::where('type', 'Walk-In')->first();
//                if($check_walk_in['setting_value'] == $shipment->user->id){
//                    $user_details = 'Walk-In ('.$shipment->pickup_address->poc . ') | ' . $shipment->pickup_address->phone;
//                }
//                else{
//                    $user_details = $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '');
//                }
//                $shipment_details_row_start = '
//                          <tr>
//                            <td>' . $total_shipments . '</td>
//                            <td>' . $shipment->tracking_number . '</td>
//                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
//                            <td>' . $shipment->consignee_address . '</td>
//                            <td>' . $shipment->booking_type->booking_type . '</td>
//                            <td>' . $user_details . '</td>
//                            <td>' . (($shipment->booking_type_id == 2) ? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
//                ';
//
//                if ($shipment->booking_type_id != 4 || ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 2)) {
//                    $shipment_details_row_start .= '
//                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
//                    ';
//
                if (!($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1)) {
                    $total_cod_amount += $shipment->received_amount;
                }

//                }
//                else {
//                    $shipment_details_row_start .= '
//                            <td>Rs 0</td>
//                    ';
//                }
//
//                $shipment_details_row_start .= '
//                          </tr>
//                ';
//
//                $shipment_details .= $shipment_details_row_start;
            }
//            $shipment_details .= '
//                        </tbody>
//                      </table>
//        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            if ($request->has('temporary') && ($request->temporary != null)) {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Temporary Cash Collection</strong></td>';
            } else {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>';
            }


            $main_details .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $main_details .= '
                      <div class="mt-2 manual_form">
                      <div class="row  mt-1">
                         <div class="col">
                            <div class="text-right">
                                <span class="d-inline-block w-150 text-left"><strong>DNCC Amount</strong></span>
                                <strong>Rs. ' . number_format($total_cod_amount) . '</strong>
                            </div>
                          </div>
                        </div>
                        <hr>';
            $html .= $main_details;
            $html .= '</br></br></br></br></br></br>';
            $html .= $main_details;
//            $html .= '<div class="row justify-content-end mt-2">
//                                    <div class="col-3 ">
//                                    <table class="table table-sm table-bordered border">
//                                        <thead>
//                                          <tr>
//                                            <th class="color primary"><strong>Denomination</strong></th>
//                                            <th class="color primary"><strong>Qty</strong></th>
//                                            <th class="color primary"><strong>Amount</strong></th>
//
//                                          </tr>
//                                          </thead>
//                                          <tbody>
//                                          <tr><td>5,000</td><td></td><td></td></tr>
//                                          <tr><td>1,000</td><td></td><td></td></tr>
//                                          <tr><td>500</td><td></td><td></td></tr>
//                                          <tr><td>100</td><td></td><td></td></tr>
//                                          <tr><td>50</td><td></td><td></td></tr>
//                                          <tr><td>20</td><td></td><td></td></tr>
//                                          <tr><td>10</td><td></td><td></td></tr>
//                                          <tr><td><b>Coins</b></td><td></td><td></td></tr>
//                                          <tr><td><b>Total</b></td><td></td><td></td></tr>
//                                          </tbody>
//                                    </table>
//                                    </div>
//                                  </div>';
//
//            $html .= $shipment_details;

//                      $html .= '<div class="row justify-content-center align-items-end mt-5">
//                          <div class="col justify-content-center ">
//                            <div class="text-center">
//                              <span class="d-block w-200 mx-auto line"></span>
//                              <strong class="d-inline-block w-200">Rider Name</strong>
//                            </div>
//                          </div>
//                          <div class="col justify-content-center ">
//                            <div class="text-center">
//                              <span class="d-block w-200 mx-auto line"></span>
//                              <strong class="d-inline-block w-200">Rider Signature</strong>
//                            </div>
//                          </div>
//                        </div>
//                        <div class="row justify-content-between align-items-end mt-5">
//                          <div class="col justify-content-center ">
//                            <div class="text-center">
//                              <span class="d-block w-200 mx-auto line"></span>
//                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
//                            </div>
//                          </div>
//                          <div class="col justify-content-center ">
//                            <div class="text-center">
//                              <span class="d-block w-200 mx-auto line"></span>
//                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
//                            </div>
//                          </div>
//                        </div>
//                        <div class="row justify-content-between align-items-end mt-5">
//                          <div class="col justify-content-center ">
//                            <div class="text-center">
//                              <span class="d-block w-200 mx-auto line"></span>
//                              <strong class="d-inline-block w-200">Cashier Name</strong>
//                            </div>
//                          </div>
//                          <div class="col justify-content-center ">
//                            <div class="text-center">
//                              <span class="d-block w-200 mx-auto line"></span>
//                              <strong class="d-inline-block w-200">Cashier Signature</strong>
//                            </div>
//                          </div>
//                        </div>
//                      </div>
//        ';
        }

//        return $shipments;


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;


    }

    public function dncc_undelivered_print(Request $request)
    {
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $delivery_note_print = $delivery_note->first();
            if ($delivery_note_print->undelivered_print == 0) {
                $delivery_note_print->undelivered_print = 1;
                $delivery_note_print->last_updated_at = Carbon::now();
                $delivery_note_print->save();
            }
        }

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Undelivered Performa</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';

        if ($delivery_note->exists()) {

            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(5, 14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status', 1)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereNotIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Status</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';

            if (!empty($filtered_shipments)) {
                foreach ($filtered_shipments as $shipment) {
                    $total_shipments++;
                    $status = ShipmentStatus::find($shipment->shipper_status_id);
                    $check_walk_in = GlobalSettings::where('type', 'Walk-In')->first();
                    if ($check_walk_in['setting_value'] == $shipment->user->id) {
                        $user_details = 'Walk-In (' . $shipment->pickup_address->poc . ') | ' . $shipment->pickup_address->phone;
                    } else {
                        $user_details = $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '');
                    }
                    $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $user_details . '</td>
                            <td>' . $status->name . '</td>
                    ';

                    if ($shipment->booking_type_id != 4 || ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 2)) {
                        $shipment_details_row_start .= '
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                        ';

                        $total_cod_amount += $shipment->received_amount;
                    } else {
                        $shipment_details_row_start .= '
                            <td>Rs 0</td>
                        ';
                    }

                    $shipment_details_row_start .= '
                      </tr>
                    ';

                    $shipment_details .= $shipment_details_row_start;
                }
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Undelivered Performa</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="p-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Undelivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
            $html .= '
                      <div class="mt-2 manual_form">
                        <hr>
                        <div class="row justify-content-center align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Signature</strong>
                            </div>
                          </div>
                        </div>
                      </div>
        ';
        }

//        return $shipments;


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;


    }

    //completed deliveries
    public function pending_cash_collection_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 21);
        return view('admin.delivery.complete.pending_cash_collection');
    }

    public function pending_cash_collection_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 81);
        }

        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('hbl_konnect_transaction_delivery_notes as hktdn', 'hktdn.delivery_note_id', '=', 'delivery_notes.id')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'ub.name as updated_by', 'delivery_notes.updated_at as updated_at', 'delivery_notes.delivered_shipments', 'delivery_notes.delivered_shipments as delivered_shipments_link', 'delivery_notes.created_at', 'delivery_notes.received_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count as shipments_count_link', 'delivery_notes.special_rider', 'delivery_notes.special_rider_name', 'delivery_notes.special_rider_phone', 'hktdn.transactions_amount as transactions_amount', 'hktdn.cash_amount as cash_amount'])
            ->where('delivery_notes.cash_collection_status', 0)
            ->where('delivery_notes.status', '!=', 4)
            ->where('delivery_notes.pending_status', 1);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a><br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('transactions_amount', function ($shipment) {
                if($shipment->transactions_amount != null){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $shipment->transactions_amount . '</button>';
                }
                else{
                    return '-';
                }
            })
            ->editColumn('cash_amount', function ($shipment) {
                if($shipment->cash_amount != null){
                    return number_format($shipment->cash_amount);
                }
                else{
                    return number_format($shipment->amount);
                }
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($rider) {
                if ($rider->special_rider) {
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                } else {
                    return $rider->rider;
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($deliveries) {
                if (session('role_id') == 1 || in_array(106, session('permissions'))) {
                    $dropdown = '
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <a href="javascript:void(0);" class="dropdown-item cash_collect"><i class="la la-money primary"></i> Collect Cash</a></div></div>';

                    return $dropdown;
                }
                return '';
            })
            ->addColumn('ccd_image', function ($deliveries) {
                $image = '<div class="text-center"><button type="button" class="btn btn-primary btn-sm ccd_slip_list"><i class="la la-image"></i> CCD Receipts</button></div>';
                return $image;
            });
        if ($tracking_number = $request->get('tracking_numbers')) {
            $datatable->join('delivery_note_shipments as dns', 'delivery_notes.id', '=', 'dns.delivery_note_id')
                ->join('shipments as s', 'dns.shipment_id', '=', 's.id')
                ->whereIn('s.tracking_number', explode(',', $tracking_number))
                ->groupBy('delivery_notes.id');
        }
        if ($dncc = $request->get('dncc')) {
            $datatable->whereIn('delivery_notes.id', explode(',', $dncc));
        }
        return $datatable->make(true);
    }

    public function pending_cash_collect(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        if ($delivery_note_id != null) {
            $delivery_note_details = DeliveryNote::find($delivery_note_id);
            if ($delivery_note_details->cash_collection_status == 0) {
                $delivery_note_details = DeliveryNote::where('id', $delivery_note_id)->where('cash_collection_status', 0)->first();
                $delivery_note_details->cash_collection_status = 1;
                $delivery_note_details->cash_collected_by = Auth::id();
                $delivery_note_details->cash_collected_at = Carbon::now();
                $delivery_note_details->save();
                return response()->json(['status' => 1, 'success' => 'Cash collected successfully!']);
            } else {
                return response()->json(['status' => 0, 'error' => 'Cash is already collected!']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'Delivery note not found!']);
        }
    }

    public function pending_cash_collect_all(Request $request)
    {
        $note_ids = explode(',', $request->delivery_note_ids);
        $notes = array();
        foreach ($note_ids as $note_id) {
            $note_details = DeliveryNote::where('id', $note_id)->where('cash_collection_status', 0)->first();
            if ($note_details) {
                $note_details->cash_collection_status = 1;
                $note_details->cash_collected_by = Auth::id();
                $note_details->cash_collected_at = Carbon::now();
                $note_details->save();
            } else {
                $notes[] = $note_id;
            }
        }
        if (empty($notes)) {
            return response()->json(['status' => 1, 'success' => 'Cash collected successfully!']);
        } else {
            return response()->json(['status' => 0, 'error' => 'These delivery notes could not be updated!', 'notes' => $notes]);
        }


    }

    //HBL Konnect Information
    public function hbl_konnect_transactions_information(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        $hbl_konnect_transactions = HblKonnectTransaction::where('delivery_note_id', $delivery_note_id);
        if($hbl_konnect_transactions->exists()){
            $hbl_konnect_transactions = $hbl_konnect_transactions->get();
            $details = array();
            foreach ($hbl_konnect_transactions as $key => $hbl_konnect_transaction){
                $details[$key]['transaction_id'] = $hbl_konnect_transaction->transaction_id;
                $details[$key]['amount'] = $hbl_konnect_transaction->amount;
                $details[$key]['deposited_at'] = Carbon::parse($hbl_konnect_transaction->created_at)->toDateTimeString();
            }
            return response()->json(['status' => 1, 'details' => $details]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Transactions not found!']);
        }


    }
    //HBL Konnect Information

    public function completed_deliveries_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 23);
        return view('admin.delivery.complete.index');
    }

    public function completed_receive_deliveries_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 83);
        }

        $deliveries = DeliveryNote::join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ccb', 'ccb.id', '=', 'delivery_notes.cash_collected_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'ub.name as updated_by', 'delivery_notes.updated_at as updated_at', 'delivery_notes.delivered_shipments', 'delivery_notes.delivered_shipments as delivered_shipments_link', 'delivery_notes.created_at', 'delivery_notes.received_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count as shipments_count_link', 'delivery_notes.cash_collected_by', 'ccb.name as cash_collected', 'delivery_notes.cash_collected_at', 'delivery_notes.special_rider', 'delivery_notes.special_rider_name', 'delivery_notes.special_rider_phone', 'delivery_notes.status'])
            ->where('delivery_notes.cash_collection_status', 1)
            ->where('delivery_notes.dncc_status', 0);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a><br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('rider', function ($rider) {
                if ($rider->special_rider) {
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                } else {
                    return $rider->rider;
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            });
//        if ($tracking_number = $request->get('search_tracking')) {
//            $datatable->join('delivery_note_shipments as dns', 'delivery_notes.id', '=', 'dns.delivery_note_id')
//                ->join('shipments as s', 'dns.shipment_id', '=', 's.id')
//                ->where('s.tracking_number', '=', $tracking_number);
//        }
        if ($tracking_number = $request->get('tracking_numbers')) {
            $datatable->join('delivery_note_shipments as dns', 'delivery_notes.id', '=', 'dns.delivery_note_id')
                ->join('shipments as s', 'dns.shipment_id', '=', 's.id')
                ->whereIn('s.tracking_number', explode(',', $tracking_number))
                ->groupBy('delivery_notes.id');
        }
        if ($dncc = $request->get('dncc')) {
            $datatable->whereIn('delivery_notes.id', explode(',', $dncc));
        }

        return $datatable->make(true);

    }

    public function get_dncc_to_add(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $delivery_notes = DeliveryNote::where('dncc_status', 0)->where('cash_collection_status', 1)->where('hub_id', $sdn->hub_id)->where('status', 1)->orderBy('id', 'desc')->get(['id']);
                    return response()->json(['status' => 1, 'dn' => $delivery_notes]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Can not add DNCC to SDN']);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Invalid SDN Id']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'SDN Id Not Found']);
        }
    }

    public function add_dncc(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $delivery_note = DeliveryNote::where('dncc_status', 0)->where('cash_collection_status', 1)->where('hub_id', $sdn->hub_id)->where('id', $request->dncc_id)->first();

                    DeliveryNoteStationDepositNote::create([
                        'station_deposit_note_id' => $sdn->id,
                        'delivery_note_id' => $delivery_note->id
                    ]);

                    $sdn->dncc_count = $sdn->dncc_count + 1;
                    $sdn->sdn_delivered_shipments = $sdn->sdn_delivered_shipments + $delivery_note->delivered_shipments;
                    $sdn->sdn_amount = $sdn->sdn_amount + $delivery_note->received_cod_amount;
                    $sdn->sdn_net_amount = $sdn->sdn_net_amount + $delivery_note->received_cod_amount;
                    $sdn->update();

//                    $delivery_note->expense = "";
//                    $delivery_note->net_amount = "";
                    $delivery_note->remarks = $request->remarks;
                    $delivery_note->dncc_status = 1;
                    $delivery_note->update();

                    return back()->with(['success' => 'DNCC added to SDN']);
                } else {
                    return back()->with(['error' => 'Can not add DNCC to SDN']);
                }
            } else {
                return back()->with(['error' => 'Invalid SDN Id']);
            }
        } else {
            return back()->with(['error' => 'SDN Id Not Found']);
        }
    }

    public function get_dncc_to_remove(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $delivery_note_ids = [];
                    foreach ($sdn->delivery_notes_list as $dn_list) {
                        $delivery_note_ids[] = $dn_list->delivery_note_id;
                    }

                    return response()->json(['status' => 1, 'dncc' => DeliveryNote::whereIn('id', $delivery_note_ids)->get()]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Can not remove DNCC from SDN']);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Invalid SDN Id']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'SDN Id Not Found']);
        }
    }

    public function remove_dncc(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $dncc_ids = explode(',', $request->dncc_id);
                    $delivery_notes = DeliveryNote::whereIn('id', $dncc_ids)->where('dncc_status', 1);
                    if ($delivery_notes->exists()) {
                        $total_dncc = 0;
                        $total_delivered_shipments = 0;
                        $total_sdn_amount = 0;
                        $total_sdn_net_amount = 0;
                        $delivery_notes = $delivery_notes->get();
                        foreach ($delivery_notes as $delivery_note) {
                            $total_dncc++;
                            $total_delivered_shipments += $delivery_note->delivered_shipments;
                            $total_sdn_amount += $delivery_note->received_cod_amount;
                            $total_sdn_net_amount += $delivery_note->received_cod_amount;

                            $delivery_note->remarks = "";
                            $delivery_note->dncc_status = 0;
                            $delivery_note->update();

                            DeliveryNoteStationDepositNote::where('station_deposit_note_id', $sdn->id)->where('delivery_note_id', $delivery_note->id)->delete();
                        }

                        $sdn->dncc_count = $sdn->dncc_count - $total_dncc;
                        $sdn->sdn_delivered_shipments = $sdn->sdn_delivered_shipments - $total_delivered_shipments;
                        $sdn->sdn_amount = $sdn->sdn_amount - $total_sdn_amount;
                        $sdn->sdn_net_amount = $sdn->sdn_net_amount - $total_sdn_net_amount;
                        $sdn->update();

                        return back()->with(['success' => 'DNCC removed successfully']);
                    } else {
                        return back()->with('error', 'Invalid DNCC');
                    }
                } else {
                    return back()->with(['error' => 'Can not add DNCC to SDN']);
                }
            } else {
                return back()->with(['error' => 'Invalid SDN Id']);
            }
        } else {
            return back()->with(['error' => 'SDN Id Not Found']);
        }
    }

    public function get_pncc_to_add(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $pickup_notes = RetailPickupNote::where('status', 4)->where('pncc_status', 0)->where('hub_id', $sdn->hub_id)->orderBy('id', 'desc')->get(['id']);
                    return response()->json(['status' => 1, 'dn' => $pickup_notes]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Can not add RNCC to SDN']);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Invalid SDN Id']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'SDN Id Not Found']);
        }
    }

    public function add_pncc(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $pickup_note = RetailPickupNote::where('pncc_status', 0)->where('status', 4)->where('hub_id', $sdn->hub_id)->where('id', $request->pncc_id)->first();

                    PickupNoteStationDepositNote::create([
                        'station_deposit_note_id' => $sdn->id,
                        'retail_pickup_note_id' => $pickup_note->id
                    ]);

                    $sdn->dncc_count = $sdn->dncc_count + 1;
                    $sdn->sdn_delivered_shipments = $sdn->sdn_delivered_shipments + $pickup_note->shipments;
                    $sdn->sdn_amount = $sdn->sdn_amount + $pickup_note->amount;
                    $sdn->sdn_net_amount = $sdn->sdn_net_amount + $pickup_note->amount;
                    $sdn->update();

//                    $delivery_note->expense = "";
//                    $delivery_note->net_amount = "";
                    $pickup_note->remarks = $request->remarks;
                    $pickup_note->pncc_status = 1;
                    $pickup_note->update();

                    return back()->with(['success' => 'RNCC added to SDN']);
                } else {
                    return back()->with(['error' => 'Can not add RNCC to SDN']);
                }
            } else {
                return back()->with(['error' => 'Invalid SDN Id']);
            }
        } else {
            return back()->with(['error' => 'SDN Id Not Found']);
        }
    }

    public function get_pncc_to_remove(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $pickup_note_ids = [];
                    foreach ($sdn->pickup_notes_list as $dn_list) {
                        $pickup_note_ids[] = $dn_list->retail_pickup_note_id;
                    }

                    return response()->json(['status' => 1, 'pncc' => RetailPickupNote::whereIn('id', $pickup_note_ids)->get()]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Can not remove RNCC from SDN']);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Invalid SDN Id']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'SDN Id Not Found']);
        }
    }

    public function remove_pncc(Request $request)
    {
        if ($request->has('sdn_id')) {
            $sdn = StationDepositNote::find($request->sdn_id);
            if ($sdn) {
                if ($sdn->adjusted == 0 && $sdn->status == 0) {
                    $pncc_ids = explode(',', $request->pncc_id);
                    $pickup_notes = RetailPickupNote::whereIn('id', $pncc_ids)->where('pncc_status', 1);
                    if ($pickup_notes->exists()) {
                        $total_pncc = 0;
                        $total_shipments = 0;
                        $total_sdn_amount = 0;
                        $total_sdn_net_amount = 0;
                        $pickup_notes = $pickup_notes->get();
                        foreach ($pickup_notes as $pickup_note) {
                            $total_pncc++;
                            $total_shipments += $pickup_note->shipments;
                            $total_sdn_amount += $pickup_note->amount;
                            $total_sdn_net_amount += $pickup_note->amount;

                            $pickup_note->remarks = "";
                            $pickup_note->pncc_status = 0;
                            $pickup_note->update();

                            PickupNoteStationDepositNote::where('station_deposit_note_id', $sdn->id)->where('retail_pickup_note_id', $pickup_note->id)->delete();
                        }

                        $sdn->dncc_count = $sdn->dncc_count - $total_pncc;
                        $sdn->sdn_delivered_shipments = $sdn->sdn_delivered_shipments - $total_shipments;
                        $sdn->sdn_amount = $sdn->sdn_amount - $total_sdn_amount;
                        $sdn->sdn_net_amount = $sdn->sdn_net_amount - $total_sdn_net_amount;
                        $sdn->update();

                        return back()->with(['success' => 'RNCC removed successfully']);
                    } else {
                        return back()->with('error', 'Invalid RNCC');
                    }
                } else {
                    return back()->with(['error' => 'Can not add RNCC to SDN']);
                }
            } else {
                return back()->with(['error' => 'Invalid SDN Id']);
            }
        } else {
            return back()->with(['error' => 'SDN Id Not Found']);
        }
    }

    //for ajax select dncc
    public function completed_deliveries_selected_dncc(Request $request)
    {
        $note_ids = explode(',', $request->delivery_note_ids);
        $updated = DeliveryNote::where('dncc_status', 1)->whereIn('id', $note_ids)->exists();
        if (!$updated) {
            session(['dncc_ids' => $note_ids]);
            $delivery_note = DeliveryNote::find($note_ids[0]);
            $hub_name = $delivery_note->hub->name;
            $banks_list = BanksList::where(['affiliate' => 1, 'status' => 1])->select('id', 'name')->get();
            return view('admin.delivery.complete.sdn_create')->with(['hub_name' => $hub_name, 'banks_list' => $banks_list, 'dncc_ids' => session('dncc_ids')]);
        } else {
            return redirect(route('admin.delivery.sdn.index'))->with('error', 'SDN already created!');
        }
    }

    public function create_sdn_view(Request $request)
    {
        return redirect(route('admin.delivery.completed.index'))->with('error', 'Kindly reselect the Delivery Notes for Deposit!');
    }

    public function get_sdn_list(Request $request)
    {
        $dncc_ids = session('dncc_ids');
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->select(['delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'delivery_notes.received_cod_amount', 'delivery_notes.shipments_count', 'delivery_notes.delivered_shipments'])
            ->whereIn('delivery_notes.id', $dncc_ids);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('received_cod_amount', function ($shipment) {
                return number_format($shipment->received_cod_amount);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->setRowAttr([
                'data-hub' => function ($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
//            ->editColumn('expense',function($deliveries){
//                return "<input id='expense' class='form-control expense numeric' placeholder='Expense' name='expense[{$deliveries->delivery_note_id}]'>";
//            })
//            ->editColumn('net_amount',function($deliveries){
//                return "<input class='form-control net_amount' readonly placeholder='Net Amount' name='net_amount[{$deliveries->delivery_note_id}]'>";
//            })
            ->addColumn('remarks', function ($deliveries) {
                $reason = '<input class="form-control" name="remarks[' . $deliveries->delivery_note_id . ']" placeholder="Enter Remarks">';
                return $reason;
            })
            ->make(true);
    }

    public function create_sdn_submit(Request $request)
    {
        if ($request->sdn_hub_id) {
            $dncc_ids = explode(',', $request->sdn_dncc_ids);
            $check_status = DeliveryNote::whereIn('id', $dncc_ids)->where('dncc_status', 1)->exists();
            if (!$check_status) {
                $expense = $request->has('total_expenses') ? $request->total_expenses : 0;
                $total_amount = $request->total_dncc_amount;
//                $total_amount = $request->has('total_amount') ? $request->total_amount : $request->total_dncc_amount;
                $created_by =  Auth::id();
                $sdn = StationDepositNote::create([
                    'hub_id' => $request->sdn_hub_id,
                    'dncc_count' => $request->sdn_count,
                    'sdn_delivered_shipments' => $request->sdn_delivered_shipments,
                    'sdn_amount' => $request->total_dncc_amount,
                    'sdn_net_amount' => $total_amount,
                    'deposited_by' => $created_by
                ]);
                foreach ($dncc_ids as $dncc) {
                    DeliveryNoteStationDepositNote::create([
                        'station_deposit_note_id' => $sdn->id,
                        'delivery_note_id' => $dncc
                    ]);
                    DeliveryNote::where('id', $dncc)->update(['expense' => $request->expense[$dncc], 'net_amount' => $request->net_amount[$dncc], 'remarks' => $request->remarks[$dncc], 'dncc_status' => 1]);
                }

                self::add_sdn_logs($sdn->id, 0, $created_by);
                return redirect(route('admin.delivery.sdn.index'));
            } else {
                return redirect(route('admin.delivery.sdn.index'))->with('error', 'SDN already created!');
            }
        }
    }

    public function sdn_petty_cash_detail(Request $request)
    {
        $petty_cash_id = $request->petty_cash_id;
        if ($petty_cash_id) {
            $petty_cash_details = PettyCashStatement::find($petty_cash_id);
            $details = array();
            $details['date'] = $petty_cash_details->created_at;
            $details['amount'] = $petty_cash_details->total_amount;
            $details['reference'] = $petty_cash_details->reference_no;

            return response()->json(['status' => 0, 'details' => $details]);
        }
        return response()->json(['status' => 1, 'error' => 'Petty Cash not found!']);
    }

    public function sdn_view(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 25);
        $petty_cash_ids = array();
        $petty_cash_ids = StationDepositNote::where('petty_cash_statement_id', '!=', null)->pluck('petty_cash_statement_id')->toArray();
        $petty_cash_list = PettyCashStatement::whereIn('status', [0, 1, 2, 7])->whereNotIn('id', $petty_cash_ids)->select('id')->get();
        $banks = BanksList::where('affiliate', 1)->get();
        return view('admin.delivery.sdn.index')->with(['banks' => $banks, 'petty_cash_list' => $petty_cash_list]);
    }

    public function sdn_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 85);
        }

        $sdn = StationDepositNote::
        join('cities AS oc', 'station_deposit_notes.hub_id', '=', 'oc.id')
            ->leftjoin('station_deposit_note_adjustments as sdna', function ($join) {
                $join->on('sdna.sdn_id', '=', 'station_deposit_notes.id')
                    ->where('sdna.id', '=',
                        DB::raw('(select max(id) from station_deposit_note_adjustments where station_deposit_note_adjustments.sdn_id = station_deposit_notes.id)'));
            })
            ->join('admins', 'admins.id', '=', 'station_deposit_notes.deposited_by')
            ->leftjoin('banks_lists', 'banks_lists.id', '=', 'station_deposit_notes.banks_list_id')
            ->select(['admins.name as resolved_by', 'station_deposit_notes.id as sdn', 'station_deposit_notes.id as sdn_id', 'oc.name as hub', 'station_deposit_notes.dncc_count', 'station_deposit_notes.dncc_count as dncc_link', 'station_deposit_notes.sdn_delivered_shipments', 'station_deposit_notes.sdn_delivered_shipments as delivered_shipments_link', 'station_deposit_notes.sdn_amount', 'station_deposit_notes.sdn_net_amount', 'admins.name as deposited_by', 'station_deposit_notes.created_at', 'station_deposit_notes.deposit_slip', 'station_deposit_notes.status', 'banks_lists.name as bank', 'station_deposit_notes.deposit_slip_status', 'station_deposit_notes.sdn_deposit_amount', 'station_deposit_notes.adjustment_amount', 'station_deposit_notes.adjustment_date', 'station_deposit_notes.adjustment_ref', 'station_deposit_notes.adjusted as adjusted', 'station_deposit_notes.sdn_type', 'sdna.date as adjustment_date_latest', 'station_deposit_notes.closed_at']);
        //admins.name as resolved_by to be changed before merging on sprint_78
        if (session('role_id') != 1) {
            $sdn = $sdn->whereIn('oc.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($sdn)
            ->setRowAttr([
                'data-type' => function ($sdn) {
                    return $sdn->sdn_type;
                },
            ])
            ->editColumn('sdn', function ($sdn) {
                return "<a href='javascript:void(0);' class='printSDN'><u>" . str_pad($sdn->sdn_id, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->editColumn('sdn_amount', function ($shipment) {
                return number_format($shipment->sdn_amount);
            })
            ->editColumn('sdn_net_amount', function ($shipment) {
                return number_format($shipment->sdn_net_amount);
            })
            ->editColumn('sdn_deposit_amount', function ($shipment) {
                if ($shipment->sdn_deposit_amount) {
                    return number_format($shipment->sdn_deposit_amount);
                } else {
                    return '-';
                }
            })
            ->addColumn('sdn_adjustment_amount', function ($shipment) {
                if ($shipment->adjustment_amount) {
                    return number_format($shipment->adjustment_amount);
                } else {
                    return '-';
                }
            })
            ->addColumn('sdn_id_padded', function ($sdn) {
                return str_pad($sdn->sdn_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('adjusted_reference_link', function ($sdn) {
                if ($sdn->adjustment_ref != null) {
                    return $sdn->adjustment_ref;
                } else {
                    $adjustment_count = StationDepositNoteAdjustment::where('sdn_id', $sdn->sdn)->count();
                    if ($adjustment_count > 0) {
                        return '<button class="btn btn-sm btn-outline-info align-middle">' . $adjustment_count . '</button>';
                    } else {
                        return 0;
                    }
                }
            })

            ->addColumn('adjusted_reference_count', function ($sdn) {
                if ($sdn->adjustment_ref != null) {
                    return $sdn->adjustment_ref;
                } else {
                    $adjustment_count = StationDepositNoteAdjustment::where('sdn_id', $sdn->sdn)->count();
                    return $adjustment_count;
                }
            })

            ->editColumn('adjustment_date', function ($deliveries) {
                $date = str_replace('00:00:00', '', $deliveries->adjustment_date_latest);
                return $date;
            })
            ->addColumn('difference_amount', function ($sdn) {
                $deposit_adjustment_amount = $sdn->sdn_deposit_amount + $sdn->adjustment_amount;
                $difference_amount = 0;

                $difference_amount = $sdn->sdn_amount - $deposit_adjustment_amount;
                return number_format($difference_amount);
            })
            ->filterColumn('station_deposit_notes.id', function ($query, $keyword) {
                return $query->where('station_deposit_notes.id', '=', $keyword);
            })
            ->addColumn('deposit_slip', function ($sdn) {
                $now = Carbon::now();
                if ($sdn->deposit_slip == null && $sdn->deposit_slip_status == 1) {
                    return '<a class="btn btn-sm btn-outline-info align-middle deposit_slip_view" href="#"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';

                } else if ($sdn->deposit_slip != null) {
                    $img_url = 'uploads/sdn/' . $sdn->deposit_slip;
                    if (file_exists($img_url)) {
                        return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/sdn/' . $sdn->deposit_slip) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl('station_deposit_notes/' . $sdn->deposit_slip, now()->addMinutes(5));
                        return '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                } else {
                    return '-';
                }
            })
            ->editColumn('dncc_link', function ($pickup_notes) {
                if ($pickup_notes->dncc_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->dncc_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($pickup_notes) {
                if ($pickup_notes->sdn_delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->sdn_delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('aging', function ($sdn) {
                $start_date = Carbon::parse($sdn->created_at);
                if ($sdn->status == 3) {
                    $end_date = Carbon::parse($sdn->closed_at);
                    return $end_date->diffInHours($start_date);;
                } else {
                    $end_date = Carbon::now();
                    return $end_date->diffInHours($start_date);;
                }
            })
            ->addColumn("action", function ($result) {
                $route = route('admin.delivery.sdn.details', ['id' => $result->sdn_id]);
                $retail_route = route('admin.delivery.sdn.retail.details', ['id' => $result->sdn_id]);

                $details_button = '<button onclick="window.open(\'' . $route . '\')" type="button" class="dropdown-item" data-target-id="' . $result->sdn_id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1"> Details</div></div></button>';
                $retail_details_button = '<button onclick="window.open(\'' . $retail_route . '\')" type="button" class="dropdown-item" data-target-id="' . $result->sdn_id . '"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1"> Details</div></div></button>';
                $adjustment_add_button = '<button type="button" class="dropdown-item adjustment_add" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Add SDN Adjustment</div></button>';
                $upload_deposit_slip_button = '<button type="button" class="dropdown-item" data-target-id="' . $result->sdn_id . '" data-target="#uploadDepositSlip" data-toggle="modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Upload Deposit Slip</div></button>';

                $reconcile_to_deposit = '<button type="button" class="dropdown-item update_status_deposit"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Update Status To Deposit</div></button>';

                $add_dncc = '<button type="button" class="dropdown-item add_dncc"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Add DNCC</div></button>';

                $add_pncc = '<button type="button" class="dropdown-item add_pncc"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Add RNCC</div></button>';

                $remove_dncc = '<button type="button" class="dropdown-item remove_dncc"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Remove DNCC</div></button>';

                $remove_pncc = '<button type="button" class="dropdown-item remove_pncc"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Remove RNCC</div></button>';

                $view_logs = '<button type="button" class="dropdown-item view_logs"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">View Status History</div></button>';

                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';
                if(($result->sdn_amount - ($result->sdn_deposit_amount + $result->adjustment_amount)) == 0 && $result->status == 1){

                    $closed_status = '<button type="button" class="dropdown-item update_status_closed"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Update Status To Closed</div></button>';
                    $dropdown .= $closed_status;
                }

                if(($result->sdn_amount - ($result->sdn_deposit_amount + $result->adjustment_amount)) == 0 && $result->status != 2){
                    $reconcile_to_resolved = '<button type="button" class="dropdown-item update_status_resolved"  data-target-id="' . $result->sdn_id . '" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Update Status To Resolved</div></button>';
                    $dropdown .= $reconcile_to_resolved;
                }

                if ($result->sdn_type == 1) {
                    $dropdown .= $details_button;
                } else {
                    $dropdown .= $retail_details_button;
                }
                if (session('role_id') == 1 || in_array(251, session('permissions'))) {
                    if (session('department_id') == 6) {
                        if ($result->adjusted == 0) {
                            $dropdown .= $adjustment_add_button;
                        }
                    } else {
                        $dropdown .= $adjustment_add_button;
                    }
                }

                if (($result->status == 0) && (session('role_id') == 1 || in_array(43, session('permissions')))) {
                    $dropdown .= $upload_deposit_slip_button;
                }

                if ($result->sdn_type == 1 && $result->status == 2 && (session('role_id') == 1 || in_array(604, session('permissions')))) {
                    $dropdown .= $reconcile_to_deposit;
                }


                if ($result->status == 0 && $result->adjusted == 0 && (session('role_id') == 1 || in_array(605, session('permissions')))) {
                    if ($result->sdn_type == 1) {
                        $dropdown .= $add_dncc;

                        if ($result->dncc_count > 1) {
                            $dropdown .= $remove_dncc;
                        }
                    } else {
                        $dropdown .= $add_pncc;

                        if ($result->dncc_count > 1) {
                            $dropdown .= $remove_pncc;
                        }
                    }
                }
                $dropdown .= $view_logs;
                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;
            })
            ->editColumn('status', function ($sdn) {
                if ($sdn->status == 0) {
                    return 'Created';
                } else if ($sdn->status == 1) {
                    return 'Deposited';
                } else if ($sdn->status == 2) {
                    return 'Resolved';
                } else {
                    return 'Closed';
                }
            })
            ->filterColumn('status', function ($query, $keyword) {
                if ($keyword == 0) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else if ($keyword == 1) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else if ($keyword == 2) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else if ($keyword == 3) {
                    $query->where('station_deposit_notes.status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            });
        if ($tracking_number = $request->get('search_tracking')) {
            $datatable->join('delivery_note_station_deposit_notes as dnsdn', 'station_deposit_notes.id', '=', 'dnsdn.station_deposit_note_id')
                ->join('delivery_notes as dn', 'dnsdn.delivery_note_id', '=', 'dn.id')
                ->join('delivery_note_shipments as dnss', 'dnss.delivery_note_id', '=', 'dn.id')
                ->join('shipments as s', 'dnss.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number)
                ->groupBy('station_deposit_notes.id');
        }
        if ($dncc = $request->get('scan_dncc')) {
            $datatable->join('delivery_note_station_deposit_notes as dnsdns', 'station_deposit_notes.id', '=', 'dnsdns.station_deposit_note_id')
                ->where('dnsdns.delivery_note_id', '=', $dncc)
                ->groupBy('station_deposit_notes.id');
        }
        if ($sdn = $request->get('scan_sdn')) {
            $datatable->whereIn('station_deposit_notes.id', explode(',', $sdn));
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('station_deposit_notes.status_updated_at', [$from, $to]);
        }
        if ($request->get('search_date_from_deposited') && $request->get('search_date_to_deposited')) {
            $from = $request->get('search_date_from_deposited');
            $to = $request->get('search_date_to_deposited');
            $datatable->where('station_deposit_notes.status', 1);
            $datatable->whereBetween('station_deposit_notes.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function back_to_deposit(Request $request)
    {
        $station_deposit_note = StationDepositNote::find($request->sdn_id);

        if ($station_deposit_note) {
            if ($station_deposit_note->status == 2) {
                if ($station_deposit_note->sdn_type == 1) {
                    $station_deposit_note->status = 1;
                    $station_deposit_note->status_updated_at = Carbon::now();
                    $station_deposit_note->status_updated_by = Auth::id();

                    $station_deposit_note->save();

                    $delivery_note_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $station_deposit_note->id)->get();

                    foreach ($delivery_note_ids as $value) {
                        foreach (DeliveryNoteShipment::where('delivery_note_id', $value->delivery_note_id)->get() as $delivery_note_shipment) {
                            if ($delivery_note_shipment->status == 7) {

                                $shipment = Shipment::where('id', $delivery_note_shipment->shipment_id);
                                if ($shipment->exists()) {
                                    $shipment = $shipment->first();
                                    if ($shipment->booking_type_id == 2) {
                                        $delivery_note_shipment->status = 4;
                                    } else if ($shipment->booking_type_id == 3) {
                                        $delivery_note_shipment->status = 5;
                                    } else if ($shipment->booking_type_id == 4) {
                                        $delivery_note_shipment->status = 6;
                                        $shipment->walk_in_status = 0;
                                        $shipment->save();
                                    } else {
                                        $delivery_note_shipment->status = 6;
                                    }
                                }

                                $delivery_note_shipment->save();
                            }
                        }
                    }

                    self::add_sdn_logs($station_deposit_note->id, 1, Auth::id());
                    return response()->json(['status' => 1, 'message' => 'Station Deposit Note Status Updated To Deposited']);
                } else {

                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Station Deposit Note Not Resolved']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Station Deposit Note Not Found']);
        }
    }



    public function get_petty_cash_statements(Request $request)
    {
        $petty_cash_list = PettyCashStatement::whereIn('status', [0, 1, 2, 7])->where('sdn_id', $request->id)->select(['id', 'created_at as date', 'total_amount as amount']);

        if ($petty_cash_list->exists()) {
            $petty_cash_list = $petty_cash_list->get();
            return response()->json(['status' => 1, 'data' => $petty_cash_list]);
        } else {
            return response()->json(['status' => 0, 'message' => "No Petty Cash Statements Find for Current SDN"]);
        }
    }

    public function sdn_details(Request $request, $id)
    {
        return view('admin.delivery.sdn.details')->with('sdn_id', $id);
    }

    public function sdn_details_ajax(Request $request, $id)
    {
        $deliveries = StationDepositNote::
        join('delivery_note_station_deposit_notes as dnsdn', 'dnsdn.station_deposit_note_id', '=', 'station_deposit_notes.id')
            ->join('delivery_notes', 'delivery_notes.id', '=', 'dnsdn.delivery_note_id')
            ->join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->select(['delivery_notes.id as dncc', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'delivery_notes.received_cod_amount', 'delivery_notes.shipments_count', 'delivery_notes.delivered_shipments', 'delivery_notes.expense', 'delivery_notes.net_amount', 'delivery_notes.remarks'])
            ->where('station_deposit_notes.id', $id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('station_deposit_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->editColumn('dncc', function ($deliveries) {
                return str_pad($deliveries->dncc, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->editColumn('received_cod_amount', function ($shipment) {
                return number_format($shipment->received_cod_amount);
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }

    public function sdn_deposit_slip(Request $request)
    {
        $deposit_rows = explode(',', $request->deposit_rows);
        $sdn_id = $request->sdn_id;
        if ($sdn_id) {
            $sdn = StationDepositNote::find($sdn_id);

        } else {
            return redirect()->back()->with(['status' => 0, 'error' => 'Station Deposit Note ID not found!']);
        }
        $total_amount = 0;
        foreach ($deposit_rows as $row) {
            $total_amount += $request->amount[$row];
        }
        if ($sdn->sdn_amount >= $total_amount) {
            $sdn->sdn_deposit_amount = $total_amount;
        } else {
            return redirect()->back()->with(['status' => 0, 'error' => 'Deposit Amount cannot be greater than DNCC Amount!']);
        }
        foreach ($deposit_rows as $row) {

            $file_name = 'deposit_slip_' . $row;
            $deposit_details = new StationDepositNoteSlip();
            $deposit_details->station_deposit_note_id = $sdn_id;
            $deposit_details->deposit_date = $request->date[$row];
            $deposit_details->bank_id = $request->bank[$row];
            $deposit_details->amount = $request->amount[$row];
            $deposit_details->uploaded_by = Auth::id();
            $image = $request->file($file_name);
//            $extension = $image->getClientOriginalExtension();
            $extension = 'png';
            $random = rand(1000, 100000);
            $now = Carbon::now();
            $time = $now->year . '_' . $now->month;
            $slip = $time . $random . Auth::id() . '.' . $extension;
            $image->move(public_path('uploads/sdn'), $slip);

            $deposit_details->image = $slip;
            $deposit_details->save();
        }

        $sdn->sdn_deposit_amount = $total_amount;
        $sdn->deposit_slip_status = 1;
        $sdn->status = 1;
        $sdn->save();

        $pnsdn = PickupNoteStationDepositNote::where('station_deposit_note_id',$sdn->id)->get()->first();
            if ($pnsdn) {
                    $retail_shipment = RetailPickupNoteShipment::where('retail_pickup_note_id', $pnsdn->retail_pickup_note_id)->get()->first();
                    if($retail_shipment){
                        $retail_cash_depost = RetailCashDepositShipment::where('shipment_id',$retail_shipment->shipment_id)->get()->first();
                        if($retail_cash_depost){

                            RetailCashDeposit::where('id',$retail_cash_depost->cash_deposit_id)->update([
                                'status' => 2,
                            ]);
                        }
                    }
            }
        self::add_sdn_logs($sdn_id, 1, Auth::id());
        return redirect()->back()->with(['status' => 1, 'success' => 'Deposit Slip uploaded successfully!']);

    }

    public function sdn_deposit_slip_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Station Deposit Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';

        $sdn = StationDepositNote::where('id', $request->id);
        if ($sdn->exists()) {
            $total_dncc = 0;

//            $shipments = DeliveryNoteShipment::where('delivery_note_id',$request->id)->select('shipment_id')->get();
            $dncc_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->id)->select('delivery_note_id')->get();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>DN No.</strong></td>
                            <td class="color primary"><strong>Rider Name</strong></td>
                            <td class="color primary"><strong>Route</strong></td>
                            <td class="color primary"><strong>Total No. Of Shipments</strong></td>
                            <td class="color primary"><strong>No. Of Delivered Shipments</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($dncc_ids as $dncc) {
                $total_dncc++;
                $dncc_note = DeliveryNote::find($dncc->delivery_note_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_dncc . '</td>
                            <td>' . str_pad($dncc_note->id, 6, '0', STR_PAD_LEFT) . '</td>
                            <td>' . $dncc_note->rider->name . '</td>
                            <td>' . $dncc_note->route->code . '( ' . $dncc_note->route->start . ' to ' . $dncc_note->route->end . ' )' . '</td>
                            <td>' . $dncc_note->shipments_count . '</td>
                            <td>' . $dncc_note->delivered_shipments . '</td>
                            <td>Rs ' . number_format($dncc_note->received_cod_amount) . '</td>
                            
                          </tr>
            ';

                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $station_note_details = StationDepositNote::where('id', $request->id)->first();
            $city_name = $station_note_details->hub->name;
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Station Deposit Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Hub Name</strong></td>
                            <td>' . $city_name . '</td>
                            <td rowspan="7" class="text-center align-middle p-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>                         
                          <tr>
                            <td class="color secondary"><strong>Total DNCC Amount</strong></td>
                            <td>Rs ' . number_format($station_note_details->sdn_amount) . '</td>
                          </tr>

                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;

        }

//        return $shipments;


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;
    }

    public function misroute_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 322);
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.delivery.misroute.index')->with(['shipment_status' => $shipment_status, 'shipping_mode' => $shipping_mode, 'service_type' => $service_type]);
    }

    public function misroute_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 323);
        }

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
//            ->join('delivery_note_shipments as dns','dns.shipment_id','=','shipments.id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->join('delivery_note_shipments as dns', function ($join) {
                $join->on('dns.shipment_id', '=', 'shipments.id')
                    ->where('dns.delivery_note_id', '=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->join('delivery_notes as dn', 'dn.id', '=', 'dns.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc')
            ->where('shipments.shipper_status_id', 11)
            ->where('dn.status', 1);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        return Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper . ' (' . $shipment->poc . ')';
                } else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('status_date', function ($shipments) {
                if ($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
                } else {
                    return " - ";
                }
            })
            ->editColumn('arrival', function ($shipments) {
                if ($shipments->arrival) {
                    return $shipments->arrival;
                } else {
                    return " - ";
                }
            })
            ->filterColumn('shipping_mode', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('sm.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
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

    public function sdn_dncc_list(Request $request)
    {
        $sdn_id = $request->input('sdn_id');
        $sdn_details = StationDepositNote::find($sdn_id);
        if ($sdn_details && $sdn_details->sdn_type == 2) {
            $dn_list = $sdn_details->pickup_notes_list;

            if ($dn_list->count() != 0) {
                $pickup_notes = array();
                foreach ($dn_list as $notes) {
                    $pickup_notes[] = $notes->retail_pickup_note_id;
                }
                return ['status' => 1, 'success' => 'Booked Shipments', 'pickup_notes' => $pickup_notes];
            } else {
                return ['status' => 0, 'success' => 'No Booked Shipments', 'pickup_notes' => FALSE];
            }
        } else {
            $dn_list = $sdn_details->delivery_notes_list;

            if ($dn_list->count() != 0) {
                $delivery_notes = array();
                foreach ($dn_list as $notes) {
                    $delivery_notes[] = $notes->delivery_note_id;
                }
                return ['status' => 2, 'success' => 'Booked Shipments', 'delivery_notes' => $delivery_notes];
            } else {
                return ['status' => 0, 'success' => 'No Booked Shipments', 'delivery_notes' => FALSE];
            }
        }


    }

    public function sdn_dncc_print(Request $request)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note Cash Collection</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                      
                      .w-150 {
                        width: 150px;
                      }
                      
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $delivery_note_data = $delivery_note->first();
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->save();
            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(14, 16, 30, 36, 37);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status', '>', 1)->where('status', '!=', 8)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
//                    $shipment = Shipment::find($parcel->shipment_id);
                $check_walk_in = GlobalSettings::where('type', 'Walk-In')->first();
                if ($check_walk_in['setting_value'] == $shipment->user->id) {
                    $user_details = 'Walk-In (' . $shipment->pickup_address->poc . ') | ' . $shipment->pickup_address->phone;
                } else {
                    $user_details = $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '');
                }
                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $user_details . '</td>
                            <td>' . (($shipment->booking_type_id == 2) ? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                            
                          </tr>
            ';
                $total_cod_amount += $shipment->received_amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            if ($request->has('temporary') && ($request->temporary != null)) {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Temporary Cash Collection</strong></td>';
            } else {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>';
            }


            $main_details .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
            $html .= '
                      <div class="mt-2 manual_form">
                      <div class="row  mt-1">
                         <div class="col">
                            <div class="text-right">
                                <span class="d-inline-block w-150 text-left"><strong>DNCC Amount</strong></span>
                                <strong>Rs. ' . number_format($total_cod_amount) . '</strong>
                            </div>
                          </div>
                        </div>
                        <hr>
                        
                        <div class="row justify-content-center align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Signature</strong>
                            </div>
                          </div>
                        </div>
                      </div>
        ';
        }

//        return $shipments;


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;


    }

    public function sdn_delivered_shipments(Request $request)
    {
        $sdn_id = $request->input('sdn_id');
        $sdn_details = StationDepositNote::find($sdn_id);
        if ($sdn_details && $sdn_details->sdn_type == 2) {
            $dn_list = $sdn_details->pickup_notes_list;
            if ($dn_list->count() != 0) {
                $shipments = array();
                foreach ($dn_list as $note) {
                    $shipment_ids = RetailPickupNoteShipment::where('retail_pickup_note_id', $note->retail_pickup_note_id)->select('shipment_id')->get();
                    foreach ($shipment_ids as $id) {
                        $shipments[$note->retail_pickup_note_id][] = Shipment::find($id)->pluck('tracking_number');
                    }
                }
                return ['status' => 1, 'success' => 'Delivered Shipments', 'shipments' => $shipments];
            } else {
                return ['status' => 0, 'success' => 'No Delivered Shipments', 'shipments' => FALSE];

            }
        } else {
            $dn_list = $sdn_details->delivery_notes_list;
            if ($dn_list->count() != 0) {
                $shipments = array();
                foreach ($dn_list as $note) {
                    $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $note->delivery_note_id)->where('status', '>', 1)->where('status', '!=', 10)->select('shipment_id')->get();
                    foreach ($shipment_ids as $id) {
                        $shipments[$note->delivery_note_id][] = Shipment::find($id)->pluck('tracking_number');
                    }
                }
                return ['status' => 2, 'success' => 'Delivered Shipments', 'shipments' => $shipments];
            } else {
                return ['status' => 0, 'success' => 'No Delivered Shipments', 'shipments' => FALSE];

            }
        }

    }

    public function receive_delivery_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }

    }

    public function receive_shipments_delivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                if ($delivery_note_shipment->status > 1 && $delivery_note_shipment->status != 8) {
                    $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                    $shipments[] = $shipment->tracking_number;
                }
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }

    }

    public function receive_shipments_undelivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                if ($delivery_note_shipment->status == 1) {
                    $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                    $shipments[] = $shipment->tracking_number;
                }
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }

    }

    public function fake_status_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_fake_status_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() > 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {

                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;

            }
            return ['status' => 0, 'success' => 'Delivery Note Fake Status Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Fake Status Shipments', 'shipments' => FALSE];
        }

    }

    public function receive_shipments_pending(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                if ($delivery_note_shipment->status == 0) {
                    $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                    $shipments[] = $shipment->tracking_number;
                }
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function cash_collection_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function cash_collection_shipments_delivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->where('status', '>', 1)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function cash_collection_shipments_ccd_slip(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $rider_deliveries = RiderDelivery::join('shipments as s', 's.id', '=', 'rider_deliveries.shipment_id')
            ->where('delivery_note_id', $delivery_note_id)
            ->where('delivered_status', 1)
            ->select('rider_deliveries.id as id', 's.tracking_number as tracking_number', 's.payment_mode_id as payment_mode_id', 'rider_deliveries.ccd_image as ccd_image', 's.id as shipment_id')->get();
        if (count($rider_deliveries) > 0) {
            $sorted_array = array();
            $now = Carbon::now();
            foreach ($rider_deliveries as $rider_delivery) {
                $sorted_array[$rider_delivery->id]['tracking_number'] = $rider_delivery->tracking_number;
                $image = '';
                $upload_image = '';
                if ($rider_delivery->ccd_image != null) {
                    $exists = Storage::disk('public')->exists($rider_delivery->ccd_image);
                    if ($exists) {
                        $image .= '<div class="text-center"><a type="button" class="btn btn-primary btn-sm picture" href ="' . asset(Storage::url($rider_delivery->ccd_image)) . '" target="_blank"><i class="la la-image"></i> View</a></div>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($rider_delivery->ccd_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }
                    $sorted_array[$rider_delivery->id]['ccd_image'] = $image;
                } else {                                                    // <----------upload Image button
                    $sorted_array[$rider_delivery->id]['ccd_image'] = '-';
                }
                if ($rider_delivery->payment_mode_id == 2) {
                    $upload_image .= ' <div class="col">
                                <div class="form-group">
                                <input type="hidden" name="shipment_ids[]" value="' . $rider_delivery->shipment_id . '">
                                    <input type="file" name="images[' . $rider_delivery->shipment_id . ']" class="w-20p p-1 border-primary" title="Select File" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-maxsize="5242880">
                                </div> 
                            </div>';

                    $sorted_array[$rider_delivery->id]['ccd_upload'] = $upload_image;
                } else {
                    $sorted_array[$rider_delivery->id]['ccd_upload'] = '-';
                }
            }
            return ['status' => 0, 'ccd_slips' => $sorted_array];
        } else {
            return ['status' => 1, 'error' => 'No CCD slips found!'];
        }

    }

    public function completed_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function completed_shipments_delivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->where('status', '>', 1)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function history_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 303);
        $operation_rider_category = OperationRidersCategory::all();
        return view('admin.delivery.history.index')->with(['status' => '1', 'operation_rider_category' => $operation_rider_category]);
    }

    public function history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 304);
        }
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->leftjoin('admins as ccb', 'delivery_notes.cash_collected_by', '=', 'ccb.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('rider_delivery_note_statuses as rdns', 'rdns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('rider_deliveries as rd', 'rd.delivery_note_id', '=', 'delivery_notes.id')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'ub.name as updated_by', 'delivery_notes.updated_at as updated_at', 'delivery_notes.delivered_shipments', 'delivery_notes.delivered_shipments as delivered_shipments_link', 'delivery_notes.created_at', 'delivery_notes.received_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count as shipments_count_link', 'delivery_notes.status', 'delivery_notes.pending_status', 'delivery_notes.cash_collection_status', 'delivery_notes.dncc_status', 'delivery_notes.last_updated_at', 'delivery_notes.cash_collected_by', 'ccb.name as cash_collected', 'delivery_notes.cash_collected_at', 'delivery_notes.special_rider', 'delivery_notes.special_rider_name', 'delivery_notes.special_rider_phone', 'rdns.status as updated_via_app', 'rd.id as rider_delivery_id', 'rd.delivered_status as delivered_status', 'rd.picture_path as picture_path'])
            ->where('riders.operation_rider_id', $request->get('operation_rider_id'))
            ->groupBy('delivery_notes.id');
        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                $link = "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
                if ($deliveries->pending_status == 1) {
                    $link .= "<br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
                }
                return $link;
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('main_status', function ($deliveries) {
                if ($deliveries->status == 0) {
                    if ($deliveries->pending_status == 0) {
                        return 'Pending for Update';
                    } else if ($deliveries->pending_status == 1) {
                        return 'Pending for Verificatin';
                    }
                } else if ($deliveries->status == 1) {
                    if ($deliveries->dncc_status == 1) {
                        return 'Completed';
                    } else if ($deliveries->cash_collection_status == 1) {
                        return 'Cash Collected';
                    } else {
                        return 'Verified';
                    }
                } else if ($deliveries->status == 4) {
                    return 'Canceled';
                }
            })
            ->filterColumn('main_status', function ($query, $keyword) {
                if ($keyword == 0) {
                    $query->where('delivery_notes.pending_status', 0)->where('delivery_notes.status', 0);
                } else if ($keyword == 1) {
                    $query->where('delivery_notes.pending_status', 1)->where('delivery_notes.status', 0);
                } else if ($keyword == 2) {
                    $query->where('delivery_notes.cash_collection_status', 1)->where('delivery_notes.dncc_status', 0);
                } else if ($keyword == 3) {
                    $query->where('delivery_notes.dncc_status', 1)->where('delivery_notes.cash_collection_status', 1);
                } else if ($keyword == 4) {
                    $query->where('delivery_notes.status', 1)->where('delivery_notes.cash_collection_status', 0);
                } else if ($keyword == 5) {
                    $query->where('delivery_notes.status', 4);
                }
            })
            ->editColumn('rider', function ($rider) {
                if ($rider->special_rider) {
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                } else {
                    return $rider->rider;
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('updated_via_app', function($shipment) {
               if ($shipment->updated_via_app == 1) {
                        return 'Partial';
                    } elseif ($shipment->updated_via_app == 2) {
                        return 'Yes';
                    } elseif ($shipment->updated_via_app == 0) {
                        return 'No';
                    }
                    else{
                        return '-';
                    }
            })
            ->filterColumn('rdns.status', function ($query, $keyword) {
                if ($keyword != 0) {
                    $query->where('rdns.status', $keyword);
                } else {
                    $query->where('rdns.status' , null)->orWhere('rdns.status',0);
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('delivery_notes.created_at', [$from, $to]);
        }
        return $datatable->make(true);

    }

    public function signature_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 305);
        return view('admin.delivery.signature.index');
    }

    public function signature_list(Request $request)
    {
        $deliveries = RiderDelivery::join('shipments as s', 'rider_deliveries.shipment_id', '=', 's.id')
            ->join('delivery_notes as dn', 'dn.id', '=', 'rider_deliveries.delivery_note_id')
            ->select(['s.tracking_number', 'rider_deliveries.picture_path', 'rider_deliveries.delivered_status', 'rider_deliveries.delivery_note_id as delivery_note_id', 'dn.pending_status'])
            ->where('rider_deliveries.delivered_status', '1');


        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                $link = "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT) . "</u></a>";
                if ($deliveries->pending_status == 1) {
                    $link .= "<br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
                }
                return $link;
            })
            ->editColumn('tracking_number', function ($deliveries) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$deliveries->tracking_number' class='tracking' target='_blank'>$deliveries->tracking_number</a></u>";
            })
            ->editColumn('picture_path', function ($deliveries) {
                if ($deliveries->delivered_status == 1) {
                    $image = '';
                    if ($deliveries->picture_path != null) {
                        $exists = Storage::disk('public')->exists($deliveries->picture_path);
                        if ($exists) {
                            $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($deliveries->picture_path)) . '"><i class="la la-image"></i> View</button></div>';
                        } else {
                            $img = Storage::disk('s3')->temporaryUrl($deliveries->picture_path, now()->addMinutes(5));
                            $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                        }

                        return $image;
                    }
                } else {
                    return '-';
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('s.tracking_number', explode(',', $tracking_numbers));
        }
        if ($delivery_note_ids = $request->get('search_delivery_note_ids')) {
            $datatable->whereIn('rider_deliveries.delivery_note_id', explode(',', $delivery_note_ids));
        }
        return $datatable->make(true);

    }

    public function history_shipments(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function history_shipments_delivered(Request $request)
    {
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->whereNotIn('status', [8, 10, 11])->where('status', 6)->get();
        $shipments = array();
        if ($delivery_note_shipments->count() != 0) {
            foreach ($delivery_note_shipments as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function misrouted_update_index()
    {

        $cities = City::select(['id', 'name as text'])->where('status', 1)->where('business_category_id', 1)->get();

        return view('admin.delivery.misroute.update')->with('cities', $cities);
    }

    public function get_misroute_shipment_info(Request $request)
    {

        $passing_status_array = array(2, 3, 4, 6, 7, 8, 9, 10, 11, 13, 15);
        $passing_delivery_status_array = array(6, 7, 8, 9, 10, 11, 13, 15);
        $tracking_number = $request->tracking_number;
        if ($tracking_number != '') {
            $shipment = Shipment::where('tracking_number', $tracking_number)->whereIn('shipper_status_id', $passing_status_array);
            if ($shipment->exists()) {
                $data = array();
                $shipment = $shipment->first();
                if ($shipment->shipper_status_id != 3) {
                    if (in_array($shipment->shipper_status_id, $passing_delivery_status_array)) {
                        $delivery_note_shipments = DeliveryNoteShipment::where('shipment_id', $shipment->id)->max('delivery_note_id');
                        $delivery_note = DeliveryNote::find($delivery_note_shipments);
                        if ($delivery_note) {
                            if ($delivery_note->status == 0) {
                                return response()->json(['status' => 0, 'error' => 'Shipment is added in an unverified delivery note!']);
                            }
                        }

                    }
                    $consignee_cities = null;
                    if ($shipment->shipping_mode_id == 2) {
                        $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                        $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                            ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                            ->select(['c.id', 'c.name as text'])
                            ->groupBy('c.id')
                            ->where('shipments.id', $shipment->id)
                            ->where('c.status', 1)
                            ->whereNotNull('c.zone_id')
                            ->whereNotIn('c.id', $restricted_cities)->get();
                    }

                    $data['id'] = $shipment->id;
                    $data['tracking_number'] = $shipment->tracking_number;
                    $data['shipping_mode_id'] = $shipment->shipping_mode_id;
                    $data['consignee_city_id'] = $shipment->consignee_city->id;
//                $data['consignee_city_name'] = $shipment->consignee_city->name;
                    $data['consignee_name'] = $shipment->consignee_name;
                    $data['consignee_address'] = $shipment->consignee_address;
                    $data['consignee_phone1'] = $shipment->consignee_phone_number_1;
                    $data['consignee_phone2'] = ($shipment->consignee_phone_number_2 != '') ? $shipment->consignee_phone_number_2 : '';
                    $data['consignee_email'] = ($shipment->consignee_email != '') ? $shipment->consignee_email : '';
                    $data['amount'] = number_format($shipment->amount);


                    ShipmentScanningJourneyController::add($shipment->id, 10, 1, Auth::id(), null, null);

                    return response()->json(['status' => 1, 'details' => $data, 'overland_cities' => $consignee_cities]);
                } else {
                    return response()->json(['status' => 0, 'error' => 'This shipment is currently in transit, please receive its cargo first to update it as Misroute!']);
                }


            } else {
                return response()->json(['status' => 0, 'error' => 'Shipment is not ready for misrouted!']);
            }

        }
    }

    public function misroute_shipment_update(Request $request)
    {
        $passing_status_array = array(2, 3, 4, 6, 7, 8, 9, 10, 11, 13, 15);
        $shipments = explode(',', $request->shipment_ids);
        if ($shipments) {
            foreach ($shipments as $shipment_id) {
                $shipment = Shipment::where('id', $shipment_id)->whereIn('shipper_status_id', $passing_status_array);
                if ($shipment->exists()) {
                    $shipment = $shipment->first();

                    if ($shipment->consignee_city_id == $request->consignee_city[$shipment_id]) {
                        continue;
                    }
                    if ($shipment->shipper_status_id == 3) {
                        $cargo_consignment_shipment = CargoConsignmentShipment::where('shipment_id', $shipment->id);
                        if ($cargo_consignment_shipment->exists()) {
                            $cargo_consignment_shipment = $cargo_consignment_shipment->max('cargo_consignment_id');

                            $cargo = CargoConsignment::find($cargo_consignment_shipment);
                            $cargo->cargo_consignment_shipments()->where('shipment_id', $shipment->id)->delete();
                            if (in_array($cargo->status_id, [1, 2, 6, 7])) {
                                $shipments_count = $cargo->shipments;
                                $shipment_weight = $cargo->shipment_weight;
                                $shipments_count = $shipments_count - 1;
                                $cargo->shipments = $shipments_count;
                                $cargo->shipments_weight = $shipment_weight - $shipment->actual_weight;
                                if ($shipments_count == 0) {
                                    $cargo->status_id = 5;
                                }
                                $cargo->save();
                            } else if ($cargo->status_id == 4) {
                                $shipments_count = $cargo->shipments;
                                $shipments_received_count = $cargo->received_shipments;
                                $shipment_weight = $cargo->shipment_weight;
                                $shipments_count = $shipments_count - 1;
                                $cargo->shipments = $shipments_count;
                                $cargo->shipments_weight = $shipment_weight - $shipment->actual_weight;
                                if ($shipments_count == 0) {
                                    $cargo->status_id = 5;
                                } else if ($shipments_count == $shipments_received_count) {
                                    $cargo->status_id = 3;
                                }
                                $cargo->save();
                            }
                        }
                        AdminCargoController::check_draft_shipments($shipment_id);

                    }
                    MisroutedHistory::create([
                        'shipment_id' => $shipment_id,
                        'old_consignee_city_id' => $shipment->consignee_city_id,
                        'old_consignee_name' => $shipment->consignee_name,
                        'old_consignee_address' => $shipment->consignee_address,
                        'old_consignee_phone_number_1' => $shipment->consignee_phone_number_1,
                        'old_consignee_phone_number_2' => $shipment->consignee_phone_number_2,
                        'old_consignee_email' => $shipment->consignee_email,
                        'new_consignee_city_id' => $request->consignee_city[$shipment_id],
                        'new_consignee_name' => $request->consignee_name[$shipment_id],
                        'new_consignee_address' => $request->consignee_address[$shipment_id],
                        'new_consignee_phone_number_1' => $request->consignee_phone1[$shipment_id],
                        'new_consignee_phone_number_2' => ($request->consignee_phone2[$shipment_id] != '') ? $request->consignee_phone2[$shipment_id] : '',
                        'new_consignee_email' => ($request->consignee_email[$shipment_id] != '') ? $request->consignee_email[$shipment_id] : '',
                        'admin_id' => Auth::id()

                    ]);

                    $shipment->consignee_city_id = $request->consignee_city[$shipment_id];
                    $shipment->consignee_name = $request->consignee_name[$shipment_id];
                    $shipment->consignee_address = $request->consignee_address[$shipment_id];
                    $shipment->consignee_phone_number_1 = $request->consignee_phone1[$shipment_id];
                    $shipment->consignee_phone_number_2 = ($request->consignee_phone2[$shipment_id] != '') ? $request->consignee_phone2[$shipment_id] : '';
                    $shipment->consignee_email = ($request->consignee_email[$shipment_id] != '') ? $request->consignee_email[$shipment_id] : '';
                    $shipment->shipper_status_id = 49;
                    $shipment->consignee_status_id = 49;
                    $shipment->save();
                    ShipmentChargesController::weight($shipment->id);
                    ShipmentsJourneyController::add($shipment->id, 49, 49, NULL, NULL, NULL, Auth::id());


                }

            }

            return redirect()->back()->with(['success' => 'Misrouted Shipments has been updated successfully', 'shipments' => $shipments, 'excel' => True]);
        } else {
            return redirect()->back()->with(['error' => 'Shipment with Misroute Status not found!']);

        }
    }

    public function misroute_shipment_excel(Request $request)
    {
        $shipments = explode(',', $request->ids);
        if (count($shipments) > 0) {

            $details = array();

            $details[] = ['S. No.', 'Tracking No.', 'Destination', 'Consignee Name', 'Address', 'Amount'];

            $serial_number = 1;

            foreach ($shipments as $shipment) {
                $shipment_details = Shipment::find($shipment);

                $row = array();

                $row[] = $serial_number;
                $row[] = $shipment_details->tracking_number;
                $row[] = $shipment_details->consignee_city->name;
                $row[] = $shipment_details->consignee_name;
                $row[] = $shipment_details->consignee_address;
                $row[] = $shipment_details->amount;

                $details[] = $row;

                $serial_number++;
            }

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->fromArray($details);

            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="misrouted_updated_shipments_' . Auth::id() . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        }
    }

    public function intercept_request_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 324);
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();

//        $city =  City::where('status', 1)->whereNotNull('zone_id')->where('pickup', 1)->orderBy('name')->get();
        return view('admin.delivery.intercept.index')->with(['shipping_mode' => $shipping_mode, 'service_type' => $service_type]);
    }

    public function intercept_request_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 325);
        }
        $shipments = Shipment::leftjoin('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftjoin('intercept_re_book_requests as irbr', 'irbr.shipment_id', '=', 'shipments.id')
            ->leftjoin('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('cities AS odc', 'irbr.consignee_city_id', '=', 'odc.id')
            ->leftjoin('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftjoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftjoin('return_assigned_shipments as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 'shipments.id')
                    ->where('ras.status', '=', 1);
            })
            ->leftjoin('admins as agent', 'agent.id', '=', 'ras.admin_id')
//            ->join('delivery_note_shipments as dns','dns.shipment_id','=','shipments.id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select('agent.name as agent', 'shipments.id as shId', 'shipments.order_id as order_id', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as old_destination', 'odc.name as new_destination', 'h.name as hub', 'irbr.consignee_name', 'irbr.consignee_phone_number_1 as phone', 'irbr.consignee_address', 'irbr.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipper_status_id', 'irbr.intercept_type as type')
            ->where('shipments.shipper_status_id', 54)
            ->groupBy('shipments.id');

//            dd($shipments);
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        return Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->editColumn('order_id', function ($shipment) {
                if ($shipment->order_id == null) {
                    return '-';
                } else {
                    return $shipment->order_id;
                }
            })
            ->editColumn('arrival', function ($shipments) {
                if ($shipments->arrival) {
                    return $shipments->arrival;
                } else {
                    return " - ";
                }
            })
            ->filterColumn('shipping_mode', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('sm.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('type', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('irbr.intercept_type', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('type', function ($shipments) {
                if ($shipments->type == null) {
                    return '-';
                } else if ($shipments->type == 2) {
                    return 'Same Consignee';
                } else {
                    return 'Different Consignee';
                }
            })
            ->make(true);
    }

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

                    $status_reason_id = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',12)->orderBy('id','desc')->pluck('status_reason_id')->first();

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

    public function fake_status_remove_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 301);
        return view('admin.delivery.fake_status.index');
    }

    public function fake_status_remove_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 302);
        }
        $fake_status = DeliveryNoteShipment::leftjoin('delivery_notes as dn', 'dn.id', '=', 'delivery_note_shipments.delivery_note_id')
            ->leftjoin('shipments as s', 's.id', '=', 'delivery_note_shipments.shipment_id')
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'delivery_note_shipments.shipment_id')
                    ->where('sj.created_at', '=', DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = delivery_note_shipments.shipment_id and shipments_journey.reference_1_id = delivery_note_shipments.delivery_note_id)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'sj.shipper_status_id')
            ->leftjoin('cities as h', 'h.id', '=', 'dn.hub_id')
            ->leftjoin('riders as r', 'r.id', '=', 'dn.rider_id')
            ->select('delivery_note_shipments.delivery_note_id as delivery_note_id', 'delivery_note_shipments.shipment_id as shipment_id', 'dn.id as delivery_note', 's.amount as amount', 'h.name as hub', 'r.name as rider', 'ss.name as status', 'dn.created_at as created_at')
            ->where('delivery_note_shipments.fake_status', 1);

        $datatables = Datatables::of($fake_status)
            ->editColumn('tracking_number_link', function ($fake_status) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$fake_status->tracking_number' class='tracking' target='_blank'>$fake_status->tracking_number</a></u>";
            })
            ->editColumn('delivery_note', function ($fake_status) {
                return str_pad($fake_status->delivery_note, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            });
        if ($request->has('search_tracking_no')) {
            $tracking_number = $request->get('search_tracking_no');
        } else {
            $tracking_number = null;
        }
        $datatables->where('s.tracking_number', '=', $tracking_number);

        return $datatables->make(true);
    }

    public function fake_status_remove(Request $request)
    {
        if ($request->has('ids')) {
            $shipment = Shipment::where('tracking_number', $request->tracking_number)->first();
            foreach ($request->ids as $delivery_note_id) {
                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment['id']])->update([
                    'fake_status' => 0,
                    'fake_status_updated_at' => Carbon::now()
                ]);
            }
            ShipmentScanningJourneyController::add($shipment['id'], 5, 1, Auth::id(), null, null);
            return ['status' => 1, 'success' => 'Fake Status has been removed'];
        }
        return ['status' => 0, 'error' => 'Something went wrong'];
    }

    public function replacement_not_collected_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 326);
        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->whereNotIn('shipment_status_reason_id', [2, 55])->pluck('shipment_status_reason_id')->toArray();
        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
        return view('admin.delivery.replacement.not_collected')->with(['return_confirm_reasons' => $return_confirm_reasons]);
    }

    public function replacement_not_collected_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 327);
        }
        $shipment = Shipment::leftjoin('cities as dc', 'dc.id', '=', 'shipments.consignee_city_id')
            ->leftjoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at', '=', DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sj.status_reason_id')
            ->select('shipments.tracking_number as tracking_number', 'shipments.id as shipment_id', 'shipments.consignee_name as consignee_name', 'shipments.consignee_address as consignee_address', 'shipments.consignee_phone_number_1 as phone', 'shipments.amount as amount', 'shipments.amount as cod_amount', 'dc.name as destination', 'sj.status_reason_id as reason', 'sj.status_reason_id as reason_id', 'ssr.name as reason_name')
            ->where('shipments.shipper_status_id', 56)
            ->where('shipments.shipper_status_id', DB::raw('sj.shipper_status_id'))
            ->groupBy('shipments.id');

        $datatables = Datatables::of($shipment)
            ->editColumn('tracking_number_link', function ($shipment) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipment->tracking_number' class='tracking' target='_blank'>$shipment->tracking_number</a></u>";
            })
            ->addColumn('reason', function ($shipment) {
                $reasons = ShipmentStatus::find(56)->reasons()->select('id', 'name')->orderBy('name')->get();
                $drops = '';
                $selected = '';
                foreach ($reasons as $reason) {
                    if ($reason->id == $shipment->reason) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    $drops .= '<option value="' . $reason->id . '" ' . $selected . '>' . $reason->name . '</option>';
                }
                $select = '<select class="form-control form-control-sm select2 reason_select" name="reason[' . $shipment->shipment_id . ']">' . $drops . '</select>';
                return $select;

            })
            ->editColumn('amount', function ($shipment) {
                $amount = '<input class="form-control form-control-sm col_amount" value="' . $shipment->amount . '" name="amount[' . $shipment->shipment_id . ']" data-rule-required="true" data-msg-required="Amount is required">';
                return $amount;
            });

        return $datatables->make(true);
    }

    public function replacement_not_collected_re_attempt(Request $request)
    {
        foreach ($request->shipment_ids as $shipment_id) {
            $shipment = Shipment::where('id', $shipment_id)->first();
            Shipment::where('id', $shipment_id)->update([
                'shipper_status_id' => 13,
                'consignee_status_id' => 13,
                'amount' => $request->shipment_amount[$shipment_id],
            ]);
            if ($shipment->amount != $request->shipment_amount[$shipment_id]) {
                ChangeShipmentAmountLog::create([
                    'shipment_id' => $shipment_id,
                    'old_amount' => $shipment->amount,
                    'new_amount' => $request->shipment_amount[$shipment_id],
                    'admin_id' => Auth::id()
                ]);
            }
            ShipmentsJourneyController::add($shipment_id, 13, 13, $request->shipment_reason[$shipment_id], NULL, NULL, Auth::id());
        }
        return ['status' => 1, 'success' => 'Shipment has been marked as Re-Attempt'];
    }

    public function replacement_not_collected_regular_re_attempt(Request $request)
    {
        foreach ($request->shipment_ids as $shipment_id) {
            $shipment = Shipment::where('id', $shipment_id)->first();
            if($shipment->warehouse == 1){
                $product_type = ShipmentItem::where(['shipment_id' => $shipment_id, 'type' => 1])->get();
                foreach ($product_type as $product){
                    $insurance = $product['insurance'];
                    $type = $product['type'];
                    $product_type_id = $product['product_type_id'];
                    $item_description = $product['description'];
                    $item_quantity = $product['quantity'];
                    $item_price = $product['price'];
                    if($item_price == null){
                        $item_price = 0;
                    }
                    $replacement_charges = $shipment->replacement_charges;
                    ReplacementToRegularLog::create([
                        'shipment_id' => $shipment->id,
                        'updated_by' => Auth::id(),
                        'replacement_charges' => $replacement_charges,
                        'product_type_id' => $product_type_id,
                        'item_description' => $item_description,
                        'item_quantity' => $item_quantity,
                        'item_price' => $item_price,
                        'insurance' => $insurance,
                        'type' => $type,
                    ]);
                }
            }
            else{
                $product_type = ShipmentItem::where(['shipment_id' => $shipment_id, 'type' => 1])->first();
                $insurance = $product_type['insurance'];
                $type = $product_type['type'];
                $product_type_id = $product_type['product_type_id'];
                $item_description = $product_type['description'];
                $item_quantity = $product_type['quantity'];
                $item_price = $product_type['price'];
                $replacement_charges = $shipment['replacement_charges'];
                ReplacementToRegularLog::create([
                    'shipment_id' => $shipment->id,
                    'updated_by' => Auth::id(),
                    'replacement_charges' => $replacement_charges,
                    'product_type_id' => $product_type_id,
                    'item_description' => $item_description,
                    'item_quantity' => $item_quantity,
                    'item_price' => $item_price,
                    'insurance' => $insurance,
                    'type' => $type,
                ]);
            }

            Shipment::where('id', $shipment_id)->update([
                'booking_type_id' => 1,
                'shipper_status_id' => 13,
                'consignee_status_id' => 13,
                'amount' => $request->shipment_amount[$shipment_id],
            ]);
            if ($shipment->amount != $request->shipment_amount[$shipment_id]) {
                ChangeShipmentAmountLog::create([
                    'shipment_id' => $shipment_id,
                    'old_amount' => $shipment->amount,
                    'new_amount' => $request->shipment_amount[$shipment_id],
                    'admin_id' => Auth::id()
                ]);
            }

            ShipmentsJourneyController::add($shipment_id, 13, 13, $request->shipment_reason[$shipment_id], NULL, NULL, Auth::id());


            ShipmentItem::where(['shipment_id' => $shipment->id, 'type' => 1])->delete();
        }
        return ['status' => 1, 'success' => 'Shipment Service type is changed to Regular and has been marked as Re-Attempt'];
    }

    public function replacement_collected_index()
    {
        return view('admin.delivery.replacement.collected');
    }

    public function change_shipment_booking_type_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->orderBy('id', 'DESC')->first();
            if ($shipment->shipper_status_id == 30) {
                $delivery_note_id = $shipment_journey->reference_1_id;
                if ($delivery_note_id) {
                    $delivery_note = DeliveryNote::find($delivery_note_id);
                    if ($delivery_note->status == 1) {

                        $details = array();
                        $shipper = $shipment->user;
                        $details['id'] = $shipment->id;

                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['status'] = $shipment->status_shipper->name;

                        $details['service_type'] = $shipment->booking_type->booking_type;
                        $details['shipping_mode'] = $shipment->shipping_mode->mode;
                        $details['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);

                        $details['payment_mode'] = $shipment->payment_mode->mode;
                        $details['amount'] = number_format($shipment->amount);

                        $details['shipper']['name'] = $shipper->name;
                        $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                        $details['shipper']['phone_number_1'] = $shipper->phone;
                        $details['shipper']['phone_number_2'] = $shipper->phone2;
                        $details['shipper']['origin'] = $shipper->city->name;
                        $details['shipper']['address'] = $shipper->address;

                        $details['consignee']['name'] = $shipment->consignee_name;
                        $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                        $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                        $details['consignee']['destination'] = $shipment->consignee_city->name;
                        $details['consignee']['address'] = $shipment->consignee_address;

                        ShipmentScanningJourneyController::add($shipment->id, 12, 1, Auth::id(), null, null);

                        return ['status' => 0, 'success' => 'Shipment\'s service type can be changed', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Shipment Delivery note is Not Verified yet'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Delivery Note not found!'];
                }

            } else {
                return ['status' => 1, 'error' => 'Shipment Status is Not Replacement - Collected'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function change_shipment_booking_type(Request $request)
    {
        $shipment = Shipment::where('id', $request->shipment_id);
        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $replacement_charges = $shipment['replacement_charges'];
            $product_type = ShipmentItem::where(['shipment_id' => $shipment->id, 'type' => 1]);
            if ($shipment->booking_type_id == 2) {
                if ($product_type->exists()) {

                    if($shipment->warehouse == 1){
                        $product_type = $product_type->get();
                        foreach ($product_type as $product){
                            $insurance = $product['insurance'];
                            $type = $product['type'];
                            $product_type_id = $product['product_type_id'];
                            $item_description = $product['description'];
                            $item_quantity = $product['quantity'];
                            $item_price = $product['price'];

                            if($item_price == null){
                                $item_price = 0;
                            }


                            ReplacementToRegularLog::create([
                                'shipment_id' => $shipment->id,
                                'updated_by' => Auth::id(),
                                'replacement_charges' => $replacement_charges,
                                'product_type_id' => $product_type_id,
                                'item_description' => $item_description,
                                'item_quantity' => $item_quantity,
                                'item_price' => $item_price,
                                'insurance' => $insurance,
                                'type' => $type,
                            ]);
                        }

                    }
                    else{
                        $product_type = $product_type->first();
                        $insurance = $product_type['insurance'];
                        $type = $product_type['type'];
                        $product_type_id = $product_type['product_type_id'];
                        $item_description = $product_type['description'];
                        $item_quantity = $product_type['quantity'];
                        $item_price = $product_type['price'];

                        ReplacementToRegularLog::create([
                            'shipment_id' => $shipment->id,
                            'updated_by' => Auth::id(),
                            'replacement_charges' => $replacement_charges,
                            'product_type_id' => $product_type_id,
                            'item_description' => $item_description,
                            'item_quantity' => $item_quantity,
                            'item_price' => $item_price,
                            'insurance' => $insurance,
                            'type' => $type,
                        ]);

                    }


                    Shipment::where('id', $request->shipment_id)->update([
                        'booking_type_id' => 1,
                        'shipper_status_id' => 13,
                        'consignee_status_id' => 13
                    ]);
                    ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, NULL, NULL, Auth::id());

                    if ($replacement_charges != null) {
                        AdminFinanceController::add_adjustment($shipment->id, $replacement_charges, 5, 5);
                    }

                    $shipment->replacement_charges = NULL;

                    $shipment->save();

                    ShipmentItem::where(['shipment_id' => $shipment->id, 'type' => 1])->delete();

                    return redirect()->back()->with('success', 'Shipment Service type has been updated to Regular');
                } else {
                    return redirect()->back()->with('error', 'Shipment Service type cann\'t be update to Regular');
                }
            } else {
                return redirect()->back()->with('error', 'Shipment Service type cann\'t be update to Regular');
            }
        } else {
            return redirect()->back()->with('error', 'Shipment Service type cann\'t be update to Regular');
        }
    }

    public function replacement_to_regular_logs_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 328);
        return view('admin.delivery.replacement.replacement_to_regular_logs');
    }

    public function replacement_to_regular_logs_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 329);
        }
        $replacement_to_regular_logs = ReplacementToRegularLog::leftjoin('shipments as s', 's.id', '=', 'replacement_to_regular_logs.shipment_id')
            ->leftjoin('products as p', 'p.id', '=', 'replacement_to_regular_logs.product_type_id')
            ->leftjoin('admins as a', 'a.id', '=', 'replacement_to_regular_logs.updated_by')
            ->select('s.tracking_number as tracking_number', 'p.product_name as product_type', 'replacement_to_regular_logs.replacement_charges as replacement_charges', 'replacement_to_regular_logs.item_description as item_description', 'replacement_to_regular_logs.item_quantity as item_quantity', 'replacement_to_regular_logs.item_price as item_price', 'replacement_to_regular_logs.insurance as insurance', 'replacement_to_regular_logs.created_at as created_at');

        $datatables = Datatables::of($replacement_to_regular_logs)
            ->editColumn('tracking_number_link', function ($shipment) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipment->tracking_number' class='tracking' target='_blank'>$shipment->tracking_number</a></u>";
            })
            ->editColumn('replacement_charges', function ($shipment) {
                if ($shipment->replacement_charges != null) {
                    return number_format($shipment->replacement_charges);
                } else {
                    return "-";
                }
            })
            ->editColumn('insurance', function ($shipment) {
                if ($shipment->insurance == 1) {
                    return "Yes";
                } else {
                    return "No";
                }
            });

        return $datatables->make(true);
    }

    public function sdn_slip_view(Request $request)
    {
        $sdn_id = $request->sdn_id;
        if ($sdn_id) {
            $slips = StationDepositNoteSlip::where('station_deposit_note_id', $sdn_id)->get();
            if (count($slips) > 0) {
                $sorted_array = array();
                $now = Carbon::now();
                foreach ($slips as $slip) {
                    $sorted_array[$slip->id]['date'] = Carbon::parse($slip->deposit_date)->toDateString();
                    $sorted_array[$slip->id]['bank'] = BanksList::find($slip->bank_id)->name;
                    $sorted_array[$slip->id]['code'] = $slip->id;
                    $sorted_array[$slip->id]['amount'] = $slip->amount;
                    $sorted_array[$slip->id]['created_at'] = Carbon::parse($slip->created_at)->toDateTimeString();
                    $sorted_array[$slip->id]['uploaded_by'] = ($slip->uploaded_by != '') ? $slip->uploaded_by_admin->name : '';
                    $img_url = 'uploads/sdn/'. $slip->image;
                    if(file_exists($img_url)){
                        $sorted_array[$slip->id]['image'] = '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/sdn/' . $slip->image) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl('station_deposit_notes/' . $slip->image, now()->addMinutes(5));
                        $sorted_array[$slip->id]['image'] = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }
                }

                return ['status' => 0, 'slips' => $sorted_array];
            } else {
                return ['status' => 1, 'error' => 'No deposit note slips found!'];
            }
        } else {
            return ['status' => 1, 'error' => 'No deposit note ID selected!'];

        }
    }

    public function sdn_adjustment_add(Request $request)
    {
        $sdn_id = $request->sdn_id;
        if ($sdn_id) {
            $sdn = StationDepositNote::find($sdn_id);
            $dncc_amount = $sdn->sdn_amount;
            $deposit_amount = $sdn->sdn_deposit_amount;
            $adjustment_amount = $sdn->adjustment_amount;

            $rows = explode(',', $request->sdn_rows);
            foreach ($rows as $row) {
                $statement = PettyCashStatement::find($request->statement[$row]);
                if ($statement) {
                    $adjustment_amount += $statement->total_amount;
                }
            }
            $total = $deposit_amount + $adjustment_amount;
            if ($dncc_amount == $total) {
                $sdn->adjustment_amount = $adjustment_amount;
                $sdn->adjusted = 1;
                $sdn->sdn_net_amount = $dncc_amount - $deposit_amount - $adjustment_amount;
                $sdn->save();

                foreach ($rows as $row) {
                    $statement = PettyCashStatement::find($request->statement[$row]);
                    if ($statement) {
                        $statement->status = 5;
                        $statement->save();

                        foreach ($statement->petty_cash_statement_details as $detail) {
                            if ($detail->status == 0) {
                                $detail->status = 2;
                                $detail->update();
                            }
                        }

                        $sdn_adjustment = new StationDepositNoteAdjustment();
                        $sdn_adjustment->sdn_id = $sdn->id;
                        $sdn_adjustment->petty_cash_statement_id = $statement->id;
                        $sdn_adjustment->date = $statement->created_at;
                        $sdn_adjustment->amount = $statement->total_amount;
                        $sdn_adjustment->save();
                    }
                }


                return redirect()->back()->with(['success' => 'Adjustment added successfully!']);

            } else {
                return redirect()->back()->with(['error' => 'DNCC cannot be less/greater than sum of adjustment amount & deposit amount']);
            }

        } else {
            return redirect()->back()->with(['error' => 'Station Deposit Note ID not found!']);
        }
    }

    public function log_fake_statuses_index()
    {
        return view('admin.fake_statuses.index');
    }

    public function log_fake_statuses_store(Request $request)
    {
        $tracking_number = $request->tracking_number;
        $shipment = Shipment::where('tracking_number', $tracking_number)->first();
        if ($shipment) {
            $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment->id)->latest('delivery_note_id')->first();
            if ($delivery_note_shipment) {
                $delivery_note_shipment->fake_status = 1;
                $delivery_note_shipment->admin_id = Auth::id();
                $delivery_note_shipment->remarks = $request->remarks;
                $delivery_note_shipment->fake_status_updated_at = Carbon::now();
                $delivery_note_shipment->save();


                ShipmentScanningJourneyController::add($shipment->id, 6, 1, Auth::id(), null, null);
                return redirect()->back()->with(['success' => 'Shipment successfully marked as Fake Status!']);
            } else {
                return redirect()->back()->with(['error' => 'Shipment with given Tracking Number not found in Delivery Note!']);
            }
        } else {
            return redirect()->back()->with(['error' => 'Shipment with given Tracking Number not found!']);
        }
    }

    static public function sdn_archive_directory()
    {

        $files = File::glob(public_path() . '/uploads/sdn/*.*');
        $now = Carbon::now();
        foreach ($files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.", filemtime($file));
                $file_name = pathinfo($file);
                if ($now->diffInDays($created) > 1) {
                    Storage::disk('s3')->put('station_deposit_notes/' . $file_name['basename'], file_get_contents($file));
                    if (Storage::disk('s3')->exists('station_deposit_notes/' . $file_name['basename'])) {
                        File::delete($file);
                    }
                }
            }
        }
    }

    static public function reassign_rider(Request $request)
    {
        $rider_id = $request->rider;
        $delivery_note_id = $request->delivery_note_id;
        $rider = Rider::leftjoin('cities as c', 'c.id', '=', 'riders.city_id')->select('c.hub_id as hub_id', 'riders.ccd as ccd')->where('riders.id', $rider_id);
        $delivery_note = DeliveryNote::find($delivery_note_id);
        if ($delivery_note) {
            if ($rider->exists()) {
                $rider = $rider->first();
                if ($delivery_note->hub_id == $rider->hub_id) {
                    $ccd_flag = false;
                    foreach ($delivery_note->delivery_note_shipments as $delivery_note_shipment) {
                        $shipment = $delivery_note_shipment->shipment;
                        if ($shipment->payment_mode_id == 2) {
                            $ccd_flag = true;
                        }
                    }
                    if ($ccd_flag == false || ($ccd_flag == true && $rider->ccd == 1)) {
                        $delivery_note->rider_id = $rider_id;
                        $delivery_note->save();

                        //rider attendance
                        if ($request->oper_id == 1) {
                            $attendance_datetime = Carbon::now()->format('Y-m-d H:i:s');
                            $attendance_date = Carbon::now()->format('Y-m-d');
                            $attendance_time = Carbon::now()->format('H:i:s');
                            // $city_id_location = City::find($delivery_note->hub_id);

                            $rider_attendance = EmployeeAttendance::where('employee_id', $rider_id)
                                ->whereDate('attendance_date', $attendance_date)
                                ->where('employee_type', 2);

                            if (!$rider_attendance->exists()) {
                                $rider_attendance = new EmployeeAttendance();
                                $rider_attendance->employee_id = $rider_id;
                                $rider_attendance->employee_type = 2;
                                $rider_attendance->attendance_date = $attendance_date;
                                $rider_attendance->clock_in_datetime = $attendance_datetime;
                                $rider_attendance->clock_in_latitude = '0';
                                $rider_attendance->clock_in_longitude = '0';
                                $rider_attendance->save();

                                $rider_attendance_action = new EmployeeAttendanceActionLog();
                                $rider_attendance_action->employee_id = $rider_id;
                                $rider_attendance_action->employee_type = 2;
                                $rider_attendance_action->action_id = 1;
                                $rider_attendance_action->attendance_date = $attendance_date;
                                $rider_attendance_action->action_date = $attendance_datetime;
                                $rider_attendance_action->latitude = '0';
                                $rider_attendance_action->longitude = '0';
                                $rider_attendance_action->save();
                            } else {
                                $rider_attendance = $rider_attendance->get()->first();
                                if ($rider_attendance->clock_in_datetime == NULL) {
                                    $rider_attendance->clock_in_datetime = $attendance_datetime;
                                    $rider_attendance->save();


                                    $rider_attendance_action = new EmployeeAttendanceActionLog();
                                    $rider_attendance_action->employee_id = $rider->id;
                                    $rider_attendance_action->employee_type = 2;
                                    $rider_attendance_action->action_id = 1;
                                    $rider_attendance_action->attendance_date = $attendance_date;
                                    $rider_attendance_action->action_date = $attendance_datetime;
                                    $rider_attendance_action->latitude = '0';
                                    $rider_attendance_action->longitude = '0';
                                    $rider_attendance_action->save();
                                }

                            }
                        }
                        //rider attendance end
                        return response()->json(['status' => 0, 'success' => 'Rider updated successfully']);
                    } else {
                        return response()->json(['status' => 1, 'error' => 'Delivery Note has shipments with payment mode Credit Card on Delivery-CCD it can only be assigned to a rider with POS enabled']);
                    }
                } else {
                    return response()->json(['status' => 1, 'error' => 'Rider must be of same hub']);
                }
            } else {
                return response()->json(['status' => 1, 'error' => 'Rider must be of same hub']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Delivery note does not exists']);
        }
    }

    static public function rider_delivery_archive_directory()
    {

        $files = File::glob(public_path() . '/storage/rider_delivery/*.*');
        $now = Carbon::now();
        foreach ($files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.", filemtime($file));
                $file_name = pathinfo($file);
                if ($now->diffInDays($created) > 1) {
                    Storage::disk('s3')->put('rider_delivery/' . $file_name['basename'], file_get_contents($file));
                    if (Storage::disk('s3')->exists('rider_delivery/' . $file_name['basename'])) {
                        File::delete($file);
                    }
                }
            }
        }
    }

    public function receive_delivery_remove_bulk(Request $request)
    {

        $shipments = $request->shipment_ids;
        $delivery_note_id = $request->delivery_note_id;
        if ($delivery_note_id) {
            foreach ($shipments as $shipment_id) {
                $shipment = DeliveryNoteShipment::where('shipment_id', $shipment_id)->where('delivery_note_id', $delivery_note_id);
                if ($shipment->exists()) {
                    $delivery_note = $delivery_note_id;
                    $delivery = DeliveryNote::where('id', $delivery_note);
                    if ($delivery->exists()) {
                        $consolidation_shipments = ConsolidationShipments::where('shipment_id', $shipment_id);
                        if ($consolidation_shipments->exists()) {
                            $consolidation_shipments = $consolidation_shipments->first();
                            $consolidation_id = $consolidation_shipments->consolidation_id;
                            $consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->pluck('shipment_id')->toArray();
                            $shipment_count = 0;
                            $shipment_cod = 0;
                            foreach ($consolidated_shipments as $consolidated_shipment) {
                                $parcel = Shipment::where('id', $consolidated_shipment)->first();
                                $shipment_cod += $parcel->amount;
                                $shipment_count += 1;
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note, 'shipment_id' => $consolidated_shipment])->delete();
                                Shipment::where('id', $consolidated_shipment)->update(['shipper_status_id' => 6]);
                                ShipmentsJourneyController::add($consolidated_shipment, 6, NULL, NULL, NULL, NULL, Auth::id(), $delivery_note_id);
                            }
                            $delivery = $delivery->first();
                            $count = $delivery->shipments_count;
                            $cod = $delivery->total_cod_amount;
                            $count = $count - $shipment_count;
                            if ($parcel->booking_type_id != 4 || ($parcel->booking_type_id == 4 && $parcel->charges_mode_id == 2)) {
                                $cod = $cod - $shipment_cod;
                            }
                            if ($count == 0) {
                                DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => 0, 'total_cod_amount' => $cod, 'status' => 4]);
                            } else {
                                DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => $count, 'total_cod_amount' => $cod]);
                            }

                        } else {
                            $parcel = Shipment::where('id', $shipment_id)->first();
                            DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note, 'shipment_id' => $shipment_id])->delete();
                            $delivery = $delivery->first();
                            $count = DeliveryNoteShipment::where('delivery_note_id', $delivery_note)->count();
                            $cod = $delivery->total_cod_amount;

                            if ($parcel->booking_type_id != 4 || ($parcel->booking_type_id == 4 && $parcel->charges_mode_id == 2)) {
                                $cod = $cod - $parcel->amount;
                            }
                            if ($count == 0) {
                                DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => 0, 'total_cod_amount' => 0, 'status' => 4]);
                            } else {
                                DeliveryNote::where('id', $delivery_note)->update(['shipments_count' => $count, 'total_cod_amount' => $cod]);
                            }
                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => 6]);
                            ShipmentsJourneyController::add($shipment_id, 6, NULL, NULL, NULL, NULL, Auth::id(), $delivery_note_id);
                        }
                    }
                }
            }
            return ['status' => 0, 'success' => 'Shipments are successfully removed'];
        } else {
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }

    public function add_shipments_in_receive_deliveries(Request $request)
    {

        $shipment_id = $request->shipment_id;

        $delivery_note_id = $request->delivery_note_id;
        $shipment = Shipment::find($shipment_id);
        $serial = '';
        if ($shipment) {
            $delivery_note = DeliveryNote::find($delivery_note_id);
            if ($delivery_note) {
                if($delivery_note->status == 4){
                    return response()->json(['status' => 1, 'error' => 'Delivery note is cancelled, all shipments removed!']);
                }
                $rider = $delivery_note->rider;
                if ($shipment->payment_mode_id == 2 && $rider->ccd == 0) {
                    return response()->json(['status' => 1, 'error' => 'The selected Shipment is Credit Card on Delivery shipment and rider is not allowed/trained to use POS for CCD shipments']);
                }

                if (DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id)->exists()) {
                    return response()->json(['status' => 1, 'error' => 'Shipment is already in this delivery note!']);
                }
                $total_shipments = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->count();

                $cod_amount = $delivery_note->total_cod_amount;
                $total_cod_amount = $cod_amount + $shipment->amount;
                $delivery_note->shipments_count = $total_shipments + 1;
                $delivery_note->total_cod_amount = $total_cod_amount;
                $delivery_note->save();

                if ($delivery_note->ordering == 1) {
                    $serial = DeliveryNoteShipment::select('ordering')->where('delivery_note_id', $delivery_note_id)->orderBy('ordering', 'desc')->first();
                    $serial = $serial->ordering;
                }
                $serial++;

                $delivery_note_shipment = new DeliveryNoteShipment();
                $delivery_note_shipment->delivery_note_id = $delivery_note_id;
                $delivery_note_shipment->shipment_id = $shipment->id;
                $delivery_note_shipment->ordering = $serial;
                $delivery_note_shipment->save();

                $shipment->shipper_status_id = 5;
                $shipment->consignee_status_id = 5;
                $shipment->save();
                ShipmentsJourneyController::add($shipment->id, 5, 5, NULL, NULL, NULL, Auth::id(), $delivery_note_id, $delivery_note->rider_id);

                $shipment_otp = ShipmentOtp::where('shipment_id', $shipment->id);
                if (!$shipment_otp->exists()) {
                    $otp = mt_rand(100000, 999999);
                    $shipment_otp = new ShipmentOtp();
                    $shipment_otp->shipment_id = $shipment->id;
                    $shipment_otp->otp = $otp;
                    $shipment_otp->save();
                }
                if ($shipment->amount == 0) {
                    //English
                    NotificationsController::send(132, $delivery_note_id, $shipment->id);
                    //Urdu
                    NotificationsController::send(135, $delivery_note_id, $shipment->id);
                } else {
                    NotificationsController::send(12, $delivery_note_id, $shipment->id);
                }

                return response()->json(['status' => 0, 'success' => 'Shipments Added']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipments Not Found']);
        }

    }

    Public function operation_riders(Request $request)
    {


        $operation_id = $request->operation_rider_id;

        $riders = Rider::leftjoin('cities as c', 'riders.city_id', '=', 'c.id')
            ->leftjoin('cities as h', 'c.hub_id', '=', 'h.id')
//            ->select(['riders.*','h.name as hub_name'])
        ->where('riders.operation_rider_id', $operation_id)
        ->where('riders.status', 1);

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }
        if ($riders) {
            $riders = $riders->select('riders.id', 'riders.name', 'riders.trax_id','h.name as hub_name')->get();
//            dd($riders);
            return response()->json(['status' => 1, 'riders' => $riders, 'success' => 'Riders Found']);
        } else {
            return response()->json(['status' => 0, 'error' => 'No Riders Found']);
        }
    }

    public function quick_receiving_delivery_index()
    {
        return view('admin.delivery.quick_receiving.index');
    }

    public $undelivered_status = array(7, 8, 9, 12, 15, 18, 56, 29, 10, 11, 29, 35);

    public function quick_receiving_track_delivery_note(Request $request)
    {
        $tracking_numbers = array();
        $delivery_note_id = $request->delivery_note_id;
        $total_shipments = 0;
        $delivery_note = DeliveryNote::where('id', $delivery_note_id);
        if ($delivery_note->doesntExist()) {
            return response()->json(['status' => 1, 'error' => 'Invalid Delivery Note Number']);
        }
        $delivery_note = $delivery_note->first();
        $delivery_note_shipments = $delivery_note->delivery_note_shipments;
        foreach ($delivery_note_shipments as $delivery_note_shipment) {
            $shipment = $delivery_note_shipment->shipment;
            if (in_array($shipment->shipper_status_id, $this->undelivered_status)) {
                array_push($tracking_numbers, $shipment->tracking_number);
                $total_shipments++;
            }
        }
        if ($total_shipments == 0) {
            return response()->json(['status' => 1, 'error' => 'Delivery Note doesn\'t contain any returned shipments']);
        }
        return response()->json(['status' => 0, 'tracking_numbers' => $tracking_numbers, 'total_shipments' => $total_shipments, 'delivery_note_number' => str_pad($delivery_note->id, 6, 0, STR_PAD_LEFT)]);
    }

    public function quick_receiving_track_tracking_number(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        $tracking_number = $request->tracking_number;
        $shipment = Shipment::where('tracking_number', $tracking_number);
        if ($shipment->doesntExist()) {
            return response()->json(['status' => 1, 'error' => 'Invalid Tracking Number']);
        }
        $shipment = $shipment->first();
        if (!in_array($shipment->shipper_status_id, $this->undelivered_status)) {
            return response()->json(['status' => 1, 'error' => 'Invalid Tracking Number']);
        }

        $delivery_note = DeliveryNote::find($delivery_note_id);
        $delivery_shipments = $delivery_note->delivery_note_shipments->where('shipment_id', $shipment->id);
        if ($delivery_shipments->count() < 1) {
            return response()->json(['status' => 1, 'error' => 'Tracking Number doesn\'t belong to this delivery note']);
        }
        $delivery_shipments = $delivery_shipments->first();
        $journey = $shipment->shipment_journey->first();
        $class = null;
        if ($shipment->shipper_status_id !== 5) {
            $delivered_statuses = array(14, 30, 36, 37);
            if (in_array($shipment->shipper_status_id, $delivered_statuses)) {
                $class = 'statusDelivered';
            } else if ($shipment->shipper_status_id == 12) {
                $class = 'statusReturn';
            } else {
                $class = 'statusUpdated';
            }
        } else if (CrmRequest::where('shipment_id', $shipment->id)->exists()) {
            $class = 'complaint_row';
        } else {
            $class = '';
        }

        ShipmentScanningJourneyController::add($shipment->id, 21, 1, Auth::id(), null . null);
        return response()->json(['status' => 0, 'details' => ['row_id' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'status' => $journey->shipment_status_shipper->name, 'reason' => $journey->shipment_status_reason->name ?? null, 'remarks' => $journey->remarks, 'status_date' => date('Y-m-d H:i:s', strtotime($journey->created_at)), 'origin' => $shipment->pickup_address->city->name, 'destination' => $shipment->consignee_city->name, 'amount' => $shipment->amount, 'shipper_name' => $shipment->user->name, 'class' => $class]]);
    }

    public function quick_receiving_submit(Request $request)
    {

        $delivery_note_id = $request->delivery_note;
        $tracking_numbers = $request->tracking_number;
        $shipments = Shipment::whereIn('tracking_number', $tracking_numbers);
        if ($shipments->count() != count($tracking_numbers)) {
            return back()->with(['error' => 'Invalid Tracking Numbers']);
        }
        $delivery_note = DeliveryNote::find($delivery_note_id);
        $shipment_ids_from_delivery_note = $delivery_note->delivery_note_shipments->pluck('shipment_id');
        $shipment_ids = array();
        $shipment_trackings = array();
        foreach ($shipment_ids_from_delivery_note as $id) {
            $temp_shipment_var = Shipment::find($id);
            if (in_array($temp_shipment_var->shipper_status_id, $this->undelivered_status)) {
                array_push($shipment_ids, $id);
                array_push($shipment_trackings, $temp_shipment_var->tracking_number);
            }
        }

        for ($i = 0; $i < count($shipment_trackings); $i++) {
            if (in_array($shipment_trackings[$i], $tracking_numbers)) {
                if (DeliveryShipmentsReceivedOperation::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment_ids[$i])->exists() || DeliveryShipmentsNotReceivedOperations::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment_ids[$i])->exists()) {
                    return back()->with(['error' => 'This delivery note can not be received again']);
                }
            }
        }

        for ($i = 0; $i < count($shipment_trackings); $i++) {
            if (in_array($shipment_trackings[$i], $tracking_numbers)) {
                $received = new DeliveryShipmentsReceivedOperation();
                $received->delivery_note_id = $delivery_note_id;
                $received->shipment_id = $shipment_ids[$i];
                $received->admin_id = Auth::id();
                $received->save();
            } else {
                $not_received = new DeliveryShipmentsNotReceivedOperations();
                $not_received->delivery_note_id = $delivery_note_id;
                $not_received->shipment_id = $shipment_ids[$i];
                $not_received->save();
            }
        }

        return back()->with(['success' => 'Shipments Received Successfully']);
    }

    public function request_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 269);
        if (session('role_id') != 1) {

            $riders = Rider::where('status', 1)->whereIn('city_id', session('hubs'))->where('blacklist', 0)->select('id', 'name')->get();
        } else {
            $riders = Rider::where('status', 1)->where('blacklist', 0)->select('id', 'name')->get();
        }
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->where('status', 1)->where('business_category_id', 1)->select('id', 'name')->get();
        return view('admin.delivery.note.request')->with(['riders' => $riders, 'hubs' => $hubs]);
    }

    public function request_list(Request $requests)
    {
        if ($requests->get('excel') && $requests->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 270);
        }
        $request = DeliveryNoteRequests::join('riders as r', 'r.id', '=', 'delivery_note_requests.rider_id')
            ->join('admins as a', 'a.id', '=', 'delivery_note_requests.requested_by')
            ->leftjoin('admins as ad', 'ad.id', '=', 'delivery_note_requests.approved_by')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->select(['delivery_note_requests.id as id', 'r.name as rider', 'delivery_note_requests.dn_received_amount as dn_received_amount', 'delivery_note_requests.amount as amount', 'delivery_note_requests.reason as reason', 'delivery_note_requests.requested_at as requested_at', 'delivery_note_requests.approved_at as approved_at', 'a.name as requested_by', 'ad.name as approved_by', 'delivery_note_requests.status as status', 'r.id as rider_id', 'delivery_note_requests.delivery_note as delivery_note', 'c.name as hub']);
        if ($requests->search_hub) {
            $request = $request->where('c.hub_id', $requests->search_hub);
        }

        $datatables = Datatables::of($request)
            ->editColumn('status', function ($result) {
                if ($result->status == 1) {
                    return 'Requested';
                } else {
                    return 'Approved';
                }
            })
            ->editColumn('delivery_note', function ($result) {
                if ($result->delivery_note) {
                    return $result->delivery_note;
                } else {
                    return '-';
                }
            })
            ->addColumn("action", function ($result) {
                if ((session('role_id') == 1 || count(array_intersect([533], session('permissions'))) !== 0) && $result->status == 1) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (($result->status == 1) && (session('role_id') == 1 || in_array(533, session('permissions')))) {
                        $dropdown .= '<button type="button" class="dropdown-item approve_request" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve </div></button>';

                    }
                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            });
        return $datatables->make(true);

    }

    public function delivery_note_info(Request $request)
    {
        $rider = $request->id;
        $delivery_note = DeliveryNote::where('rider_id', $rider)->where('dncc_status', 0)->latest()->first();
        if ($delivery_note) {
            return response()->json(['status' => 1, 'note' => $delivery_note]);
        } else {
            return response()->json(['status' => 0, 'error' => 'No Delivery Note Found For the Rider']);
        }
    }

    public function request_submit(Request $request)
    {
        if (DeliveryNoteRequests::where('rider_id', $request->rider_id)->where('status', 1)->exists()) {
            return redirect()->route('admin.delivery.note.request_index')->with(['error' => 'Request Already Present']);
        } else {
            $note = new DeliveryNoteRequests();
            $note->rider_id = $request->rider_id;
            $note->dn_received_amount = $request->dncc;
            $note->amount = $request->amount;
            $note->reason = $request->reason;
            $note->delivery_note = $request->dnid;
            $note->requested_at = Carbon::now();
            $note->requested_by = Auth::id();
            $note->status = 1;
            $note->save();

            return redirect()->route('admin.delivery.note.request_index')->with(['success' => 'Request Added']);
        }


    }

    public function request_approve(Request $request)
    {

        $note = DeliveryNoteRequests::find($request->id);

        if ($note->status == 1) {
            $note->status = 2;
            $note->approved_at = Carbon::now();
            $note->approved_by = Auth::id();
            $note->save();

            return response()->json(['status' => 1, 'success' => 'Request Approved']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Status already approved']);
        }
    }

    public function receive_delivery_get_distribution(Request $request)
    {
        $shipment = $request->distribution;
        if (isset($shipment)) {
            $product = array();
            $parcels = ShipmentDistributionProduct::where('shipment_id', $shipment)->where('status', 0)->get();
            foreach ($parcels as $parcel) {
                $product[] = ['pid' => $parcel->id, 'type' => $parcel->item->name, 'items' => $parcel->items, 'units_per_item' => $parcel->units_per_item, 'total_delivered_units' => $parcel->total_delivered_units, 'price' => $parcel->price];
//
            }

            return ['status' => 0, 'data' => $product];
        } else {
            return ['status' => 1, 'error' => 'No Shipment found'];
        }
    }

    public function receive_delivery_distribution_submit(Request $request)
    {
        $shipment_id = $request->distribution_shipment_id;
        $note_id = $request->delivery_note_distribution;
        if (!empty($request->total_delivered_units)) {
            $total_cod = 0;
            foreach ($request->total_delivered_units as $id => $value) {

                $shipment_item = ShipmentDistributionProduct::find($id);
                $shipment_item->total_delivered_units = $value;
                $shipment_item->total_delivered_skus = $request->get('total_delivered_skus')[$id];
                $shipment_item->received_amount = $request->get('amount')[$id];
                $shipment_item->status = 1;
                $shipment_item->save();
                $total_cod += $shipment_item->received_amount;
            }

            Shipment::where('id', $shipment_id)->update(['amount' => $total_cod, 'received_amount' => $total_cod, 'shipper_status_id' => 14, 'consignee_status_id' => 14]);
            /*  ShipmentsJourneyController::add($shipment_id, 37, 37, NULL, NULL, NULL, Auth::id(), $request->delivery_note_trybuy, NULL, 0);*/

            //ShipmentChargesController::cash_handling($request->trybuy_shipment_id);
            //DeliveryNoteShipment::where(['shipment_id' => $shipment_id, 'delivery_note_id' =>$note_id])->update(['status' => 5]);

            $delivery_note_data = DeliveryNote::find($note_id);
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->status_updated_at = Carbon::now();
            $delivery_note_data->save();
            return redirect()->back()->with('success', 'Distribution shipment updated');
        }

    }

    public function upload_pod(Request $request)
    {

        $image = $request->file('pod_file');
        $extension = $image->getClientOriginalExtension();
        $random = rand(1000, 100000);
        $now = Carbon::now();
        $time = $now->year . '_' . $now->month;
        $generated_image_name = $time . $random . Auth::id() . '.' . $extension;
        $image->move(public_path('uploads/pod_images'), $generated_image_name);
        $pod_image = new PODImage();
        $pod_image->pod_file = $generated_image_name;
        $pod_image->added_by = Auth::id();
        $pod_image->shipment_id = $request->shipment_id;
        $pod_image->save();
        return redirect()->back()->with('success', 'POD File Uploaded');
    }

    public function cash_collection_upload_receipt(Request $request)
    {
        $delivery_note_id = $request->input('ccd_delivery_note_id');
        if ($request->has(('shipment_ids')) && is_array($request->images)) {
            if (count($request->shipment_ids) > 0) {
                if (count($request->images) > 0) {
                    foreach ($request->shipment_ids as $shipment_id) {
                        if (array_key_exists($shipment_id, $request->images)) {
                            $rider_delivery = RiderDelivery::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment_id);
                            if ($rider_delivery->exists()) {
                                $rider_delivery = $rider_delivery->first();
                                if ($rider_delivery->ccd_image != null) {
                                    Storage::disk('public')->delete('rider_delivery/' . $rider_delivery->ccd_image);
                                }
                                $picture_path = 'rider_delivery/ccd_image_' . $rider_delivery->id . '.png';
                                Storage::disk('public')->put($picture_path, file_get_contents($request->images[$shipment_id]));
                                $rider_delivery->ccd_image = $picture_path;
                                $rider_delivery->save();
                            }
                        }
                    }
                }
            }
        }
        return redirect('/admin/delivery/cash_collection/pending');
    }

    public function delivery_note_otp_generation(Request $request)
    {
        $environment = config('app.env');

        if ($environment == 'production' || $environment == 'staging') {
            $rider_id = $request->get('rider');
            $rider = Rider::find($rider_id);
            if ($rider) {
                $otp = mt_rand(100000, 999999);
                $rider->delivery_note_otp = $otp;
                $rider->otp_date = Carbon::now();
                $rider->save();
                NotificationsController::app_notification(9, $rider->id, 2, $otp);
                NotificationsController::send(144, $rider, $otp);
                return response()->json(['status' => 1]);
            } else {
                return response()->json(['status' => 0, 'error' => 'Rider not found!']);
            }
        }
        return response()->json(['status' => 1]);
    }

    public function delivery_note_otp_verification(Request $request)
    {
        $environment = config('app.env');

        if ($environment == 'production' || $environment == 'staging') {
            $rider = Rider::find($request->rider);
            if ($rider) {
                if ($rider->delivery_note_otp == $request->otp) {
                    return response()->json(['status' => 1]);
                } else {
                    return response()->json(['status' => 0, 'error' => 'Invalid OTP']);
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Rider not selected!']);

            }
        } else {
            return response()->json(['status' => 1]);
        }
    }

    static public function add_sdn_logs($sdn_id, $status_id, $admin_id){
        $sdn_log = new StationDepositNoteLog();
        $sdn_log->sdn_id = $sdn_id;
        $sdn_log->status_id = $status_id;
        $sdn_log->admin_id = $admin_id;
        $sdn_log->save();
    }

    public function sdn_status_logs(Request $request){
        $sdn_id = $request->sdn_id;
        if($sdn_id){
            $status_logs = array();
            $logs = StationDepositNoteLog::where('sdn_id', $sdn_id);
            if($logs->exists()){
                $logs = $logs->get();
                foreach ($logs as $log){
                    if($log->status_id == 0){
                        $status_logs[$log->id]['status'] = 'Created';
                    }
                    else if($log->status_id == 1){
                        $status_logs[$log->id]['status'] = 'Deposited';
                    }
                    else if($log->status_id == 2){
                        $status_logs[$log->id]['status'] = 'Resolved';
                    }
                    else{
                        $status_logs[$log->id]['status'] = 'Closed';
                    }
                    $status_logs[$log->id]['updated_by'] = $log->updated_by->name;
                    $status_logs[$log->id]['date'] = Carbon::parse($log->created_at)->toDateTimeString();
                }

                return response()->json(['status' => 1, 'sdn_id' => str_pad($sdn_id, 6, '0', STR_PAD_LEFT), 'logs' => $status_logs]);
            }
            return response()->json(['status' => 0, 'message' => 'No logs found!']);
        }
    }

    public function closed(Request $request){
        $station_deposit_note = StationDepositNote::find($request->sdn_id);

        if ($station_deposit_note) {
            if ($station_deposit_note->status == 1) {
                $station_deposit_note->status = 3;
                $station_deposit_note->closed_at = Carbon::now();
                $station_deposit_note->save();
                return response()->json(['status' => 1, 'message' => 'Station Deposit Note Status Updated To Closed']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Station Deposit Note Not Resolved']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Station Deposit Note Not Found']);
        }
    }

    public function resolved(Request $request){
        $station_deposit_note = StationDepositNote::find($request->sdn_id);

        if ($station_deposit_note) {
            if (($station_deposit_note->sdn_amount - ($station_deposit_note->sdn_deposit_amount + $station_deposit_note->adjustment_amount)) == 0) {
                $station_deposit_note->status = 2;
                $station_deposit_note->closed_at = Carbon::now();
                $station_deposit_note->save();
                return response()->json(['status' => 1, 'message' => 'Station Deposit Note Status Updated To Resolved']);
            } else {
                return response()->json(['status' => 0, 'message' => 'Pending Difference Amount']);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Station Deposit Note Not Found']);
        }
    }

    public function bulk_closed(Request $request){
        $station_deposit_notes = StationDepositNote::whereIn('id',$request->sdn_ids);

        if ($station_deposit_notes->exists()) {
            $station_deposit_notes = $station_deposit_notes->get();
            foreach($station_deposit_notes as $station_deposit_note){
                if (($station_deposit_note->sdn_amount - ($station_deposit_note->sdn_deposit_amount + $station_deposit_note->adjustment_amount)) == 0)
                {
                    if($station_deposit_note->status == 1) {
                        $station_deposit_note->status = 3;
                        $station_deposit_note->closed_at = Carbon::now();
                        $station_deposit_note->save();
                        return response()->json(['status'=> 1,'success'=>"Station Deposit Notes Status Updated To Closed"]);
                    }else{
                        return response()->json(['status'=> 0,'error'=>"Status Should Be Deposited First"]);
                    }
                }
                else{
                    return response()->json(['status'=> 0,'error'=>"Difference Amount is pending"]);
                }
            }
        }
    }

    public function bulk_resolved(Request $request){
        $station_deposit_notes = StationDepositNote::whereIn('id',$request->sdn_ids);
        $return_id = "";

        if ($station_deposit_notes->exists()) {
            $station_deposit_notes = $station_deposit_notes->get();
            foreach($station_deposit_notes as $station_deposit_note){
                if (($station_deposit_note->sdn_amount - ($station_deposit_note->sdn_deposit_amount + $station_deposit_note->adjustment_amount)) == 0) {
                    $station_deposit_note->status = 2;
                    $station_deposit_note->closed_at = Carbon::now();
                    $station_deposit_note->save();
                }else{
                    $return_id.=  "ID = ".$station_deposit_note->id.", ";
                }
            }
            if(empty($return_id)) {
                return response()->json(['status' => 1, 'success' => "Station Deposit Notes Status Updated To Resolved"]);
            }else{
                return response()->json(['status' => 0, 'error' => "Difference Amount is pending"]);
            }

        } else {
            return response()->json(['status'=> 0,'error'=>"Station Deposit Notes Not Found"]);

        }
    }
}
