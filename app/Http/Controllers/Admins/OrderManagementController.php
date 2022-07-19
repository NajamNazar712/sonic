<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\BookingType;
use App\Http\Models\BusinessCategory;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\Product;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\Http\Models\WarehouseStock;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\PaymentMode;
use App\Http\Models\ShipmentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class OrderManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),44);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        $business_categories = BusinessCategory::all();
        $payment_modes = PaymentMode::all();
        return view('admin.order_management.index')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims, 'business_categories' => $business_categories, 'payment_modes' => $payment_modes]);
    }
    public function orders_list(Request $request)
    {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('payment_modes as pm', 'pm.id', '=', 'shipments.payment_mode_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftJoin('business_categories as bc', 'shipments.business_category_id', '=' , 'bc.id')
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.tracking_number as tracking','shipments.order_id','u.name as shipper','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2', 'shipments.created_at as booking_date','shipments.shipper_status_id', 'sps.name as payment_status', 'shipments.booking_type_id', 'usi.poc','usi.vendor as vendor','shipments_journey.shipper_status_id as status_id', 'bc.name as business_category', 'pm.mode as payment_mode']);

        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass')) ){
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }
        else if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))->orWhereIn('dc.hub_id', session('hubs'));
            });
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
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
            ->filterColumn('u.id', function ($query, $keyword) {
                return $query->where('shipments.user_id', '=', $keyword);
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('phone',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $keyword = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                            ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                    });
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('shipments_journey.shipper_status_id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('shipments.booking_type_id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('payment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('shipments.payment_status_id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action',function ($shipments) {
                if ($shipments->shipper_status_id != 17 && $shipments->shipper_status_id > 1) {
                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>
                            </div>
                        </div>
                    ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if ($shipment_status_select = $request->get('shipment_status_select')) {
            $datatable->whereIn('shipments_journey.shipper_status_id', $shipment_status_select);
        }
        if ($request->get('booking_from_date') && $request->get('booking_to_date')) {
            $from = $request->get('booking_from_date');
            $to = $request->get('booking_to_date');
            $datatable->whereBetween('shipments.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
    public function get_shipment_charges(Request $request){
        $retail_shipment = '';
        $shipment_id = $request->shipment_id;
        $shipment = Shipment::find($shipment_id);
        if($shipment->shipment_type == 2){
            $retail_shipment = RetailShipment::where('shipment_id',$shipment_id)->first();
        }
        $returnHTML = view('admin/components/shipment_charges')->with(['shipment'=>$shipment,'retail_shipment' => $retail_shipment])->render();
        return response()->json($returnHTML);
    }
    public function shipper_recall(Request $request) {
        $shipment_ids = $request->shipment_ids;

        if (!empty($shipment_ids)) {
            $valid = FALSE;

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && ($shipment->shipper_status_id == 2 || ($shipment->shipper_status_id == 58 && $shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id))) {
                    $valid = TRUE;

                    $check_walk_in = GlobalSettings::where('type', 'Walk-In')->first();
                    if($check_walk_in['setting_value'] == $shipment->user->id){
                        $shipment->shipper_status_id = 17;
                        $shipment->consignee_status_id = 17;

                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, 17, 17, NULL, NULL, NULL, Auth::id());
                    }
                    else {
                        if ($shipment->packaging_material_request != 1) {
                            //Consolidated Shipments
                            $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment_id)->first();
                            if($consolidated_shipment){
                                $consolidation_id = $consolidated_shipment->consolidation_id;
                                ConsolidationShipments::where('id', $consolidated_shipment->id)->delete();
                                $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();
                                if(count($remaining_consolidated_shipments) == 1){
                                    ConsolidationShipments::where('consolidation_id', $consolidation_id)->delete();
                                    Consolidation::where('id', $consolidation_id)->delete();
                                }
                                else{
                                    foreach ($remaining_consolidated_shipments as $index => $remaining_consolidated_shipment){
                                        $new_order_consolidated_shipment = ConsolidationShipments::find($remaining_consolidated_shipment->id);
                                        $new_order_consolidated_shipment->order = $index + 1;
                                        $new_order_consolidated_shipment->save();
                                    }
                                    $consolidation = Consolidation::find($consolidation_id);
                                    $consolidation->count = count($remaining_consolidated_shipments);
                                    if($consolidation->default_shipment_id == $shipment_id){
                                        $consolidation->default_shipment_id = $remaining_consolidated_shipments[0]->shipment_id;
                                    }
                                    $consolidation->save();
                                }
                            }
                            //Consolidated Shipments

                            if ($shipment->pickup_address->city->hub_id == $shipment->consignee_city->hub_id) {
                                $shipment->shipper_status_id = 20;
                                $shipment->consignee_status_id = 20;

                                $shipment->save();

                                ShipmentsJourneyController::add($shipment_id, 50, 50, NULL, NULL, NULL, Auth::id());

                                ShipmentsJourneyController::add($shipment_id, 20, 20, NULL, NULL, NULL, Auth::id());
                            } else {
                                $shipment->shipper_status_id = 22;
                                $shipment->consignee_status_id = 22;

                                $shipment->save();

                                ShipmentsJourneyController::add($shipment_id, 50, 50, NULL, NULL, NULL, Auth::id());

                                ShipmentsJourneyController::add($shipment_id, 20, 20, NULL, NULL, NULL, Auth::id());

                                ShipmentsJourneyController::add($shipment_id, 22, 22, NULL, NULL, NULL, Auth::id());
                            }
                        }
                        else {
                            $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                            if ($packaging_material_shipment != null) {
                                $request_id = $packaging_material_shipment->id;

                                $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->with('city')->first();

                                if ($packaging_material_shipment->status_id == 3) {
                                    $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id)->get();

                                    $hub_id = $packaging_material_request->city->hub_id;

                                    $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id)->first();

                                    $warehouse_id = $fulfilment_hub->warehouse_id;

                                    if($packaging_material_request_details){
                                        foreach ($packaging_material_request_details as $detail_add) {
                                            $type_id = $detail_add->type_id;
                                            $type_size_id = $detail_add->type_size_id;
                                            $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);

                                            $stock = $stock->first();
                                            $stock->stock = $stock['stock'] + $detail_add->quantity;
                                            $stock->save();
                                        }
                                    }

                                    $packaging_request_history_replenished = new PackagingMaterialRequestHistory();
                                    $packaging_request_history_replenished->packaging_material_request_id = $request_id;
                                    $packaging_request_history_replenished->status = 5;
                                    $packaging_request_history_replenished->updated_by = Auth::id();
                                    $packaging_request_history_replenished->save();
                                }

                                $shipment->shipper_status_id = 17;
                                $shipment->consignee_status_id = 17;

                                $shipment->save();

                                ShipmentsJourneyController::add($shipment_id, 50, 50, NULL, NULL, NULL, Auth::id());

                                ShipmentsJourneyController::add($shipment_id, 17, 17, NULL, NULL, NULL, Auth::id());


                                $packaging_material_request->status_id = 6;
                                $packaging_material_request->save();


                                $packaging_request_history = new PackagingMaterialRequestHistory();
                                $packaging_request_history->packaging_material_request_id = $request_id;
                                $packaging_request_history->status = 6;
                                $packaging_request_history->updated_by = Auth::id();
                                $packaging_request_history->save();
                            }
                        }

                        NotificationsController::send(15, 0, $shipment_id);
                        NotificationsController::send(16, 0, $shipment_id);

                        ShipmentChargesController::return($shipment_id);

                        AdminFinanceController::add_payment($shipment_id, 1);
                    }
                }
            }

            if ($valid) {
                return ['status' => 0, 'success' => 'Shipment(s) has been marked as Return Confirm due to Shipper Recall'];
            }
            else {
                return ['status' => 1, 'error' => 'No Valid Shipment(s) were Selected'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }

    public function shipment_print_status(Request $request) {
        $shipment_ids = $request->shipment_ids;

        if (!empty($shipment_ids)) {
            $valid_ids = array();
            $sticker = TRUE;

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 2) && !$shipment->packaging_material_request) {
                    $valid_ids[] = $shipment_id;

                    if (!ShipperAirWaybillSettings::where('user_id', $shipment->user_id)->where('type', 2)->exists()) {
                        $sticker = FALSE;
                    }
                }
            }

            if (!empty($valid_ids)) {
                return ['status' => 0, 'success' => 'Valid Shipment(s) Found', 'valid_ids' => $valid_ids, 'sticker' => $sticker];
            }
            else {
                return ['status' => 1, 'error' => 'No Valid Shipment(s) were Selected'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }

    public function telenor_shipments_arrival(Request $request){
        $shipments = Shipment::where('user_id', 4213)->where('shipper_status_id', 1);

        if ($shipments->exists()) {
            $shipments = $shipments->get();

            foreach ($shipments as $shipment) {
                V2AdminPickupsController::cancel($shipment->id);

                $status_id = 2;

                ShipmentsJourneyController::add($shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);

                if ($shipment->pickup_address->city_id != $shipment->consignee_city_id) {
                    $status_id = 4;

                    ShipmentsJourneyController::add($shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);
                }

                $shipment->shipper_status_id = $status_id;
                $shipment->consignee_status_id = $status_id;
                $shipment->actual_weight = 0.5;

                $shipment->save();

                ShipmentChargesController::weight($shipment->id);
                ShipmentChargesController::cash_handling($shipment->id);
                ShipmentChargesController::insurance($shipment->id);
                ShipmentChargesController::fuel_surcharge($shipment->id);
            }
            return ['status' => 0, 'success' => 'Shipment(s) arrived successfully'];
        }
        return ['status' => 1, 'error' => 'Shipment(s) not found!'];

    }

    public function foodpanda_shipments_arrival(Request $request){
        $shipments = Shipment::where('user_id', 4201)->where('shipper_status_id', 1);

        if ($shipments->exists()) {
            $shipments = $shipments->get();

            foreach ($shipments as $shipment) {
                V2AdminPickupsController::cancel($shipment->id);

                $status_id = 2;

                ShipmentsJourneyController::add($shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);

                if ($shipment->pickup_address->city_id != $shipment->consignee_city_id) {
                    $status_id = 4;

                    ShipmentsJourneyController::add($shipment->id, $status_id, $status_id, NULL, NULL, NULL, 57);
                }

                $shipment->shipper_status_id = $status_id;
                $shipment->consignee_status_id = $status_id;
                $shipment->actual_weight = 0.5;

                $shipment->save();

                ShipmentChargesController::weight($shipment->id);
                ShipmentChargesController::cash_handling($shipment->id);
                ShipmentChargesController::insurance($shipment->id);
                ShipmentChargesController::fuel_surcharge($shipment->id);
            }
            return ['status' => 0, 'success' => 'Shipment(s) arrived successfully'];
        }
        return ['status' => 1, 'error' => 'Shipment(s) not found!'];

    }

    public function self_collection_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),318);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $shipping_mode = ShippingMode::all();
        $products = Product::select('id','product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
        $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        return view('admin.self_collection.index')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels, 'case_nature_type_claims' => $case_nature_type_claims]);
    }
    public function self_Collection_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),319);
        }
        $shipments = Shipment::join('self_collection_shipments as scs', 'scs.shipment_id', '=', 'shipments.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('return_assigned_shipments as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 'shipments.id')
                    ->where('ras.status', '=' , 1);
            })
            ->leftjoin('admins as agent','agent.id','=','ras.admin_id')
            ->select(['agent.name as agent','shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.tracking_number as tracking','shipments.order_id','u.name as shipper','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','shipments.created_at as booking_date','shipments.shipper_status_id', 'sps.name as payment_status', 'shipments.booking_type_id', 'usi.poc','usi.vendor as vendor','shipments_journey.shipper_status_id as status_id', 'sj.created_at as arrival_date', 'u.phone as shipper_phone','u.phone2 as shipper_phone2', 'sm.mode as shipping_mode', 'h.name as hub'])
        ->where('shipments.shipper_status_id', '=', 15);

        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }
        else if (session('role_id') != 1) {
            $shipments = $shipments->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))->orWhereIn('dc.hub_id', session('hubs'));
            });
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('shipper_phone',function ($shipper){
                $phone = "$shipper->shipper_phone";
                if($shipper->shipper_phone2 != null){
                    $phone .= " | $shipper->shipper_phone2";
                }
                return $phone;
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
            ->filterColumn('u.id', function ($query, $keyword) {
                return $query->where('u.id', '=', $keyword);
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('phone',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $keyword = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                            ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                    });
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, s.consignee_phone_number_2 $1')
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('bt.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('payment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('sps.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('product_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('p.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });
            
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        return $datatable->make(true);
    }

    public function supply_chain_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),11);
        $shipment_status = ShipmentStatus::select('id','name')->whereIn('id',[1,2,3,4,20,21])->get();
        $service_type = BookingType::all();
        $shippers = User::select('id','name')->get();
        $products = Product::select('id','product_name')->get();
        return view('admin.supply_chain.index')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products ,'shippers' =>$shippers ]);;
    }
    public function supply_chain_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),71);
        }
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', function($join){
                $join->on('shipments.consignee_city_id', '=', 'dc.id')
                    ->where('dc.hub_id', '!=', DB::raw('oc.hub_id'));
            })
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->leftJoin('shipment_items as si','si.shipment_id','=','shipments.id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.tracking_number as tracking','shipments.pieces as pieces','si.quantity as quantity','shipments_journey.created_at as status_date','shipments.actual_weight as weight','u.name as shipper','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.created_at as booking_date','shipments.shipper_status_id','shipments.booking_type_id','shipments_journey.shipper_status_id as status_id'])
        ->whereIn('ss.id',[1,2,3,4,20,21]);

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('weight',function($shipment){
                if ($shipment->weight != null ) {
                    return $shipment->weight ;
                }
                else {
                    return '-';
                }
            })
            ->editColumn('status_date',function($shipment){
                if ($shipment->status_date != null ) {
                    return $shipment->status_date ;
                }
                else {
                    return '-';
                }
            })
            ->filterColumn('status',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('bt.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });

        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if ($shipment_status_select = $request->get('shipment_status_select')) {
            $datatable->whereIn('ss.id', $shipment_status_select);
        }
        if ($shipper = $request->get('shipper')) {
            $datatable->whereIn('shipments.user_id', $shipper);
        }
        if ($request->get('booking_from_date') && $request->get('booking_to_date')) {
            $from = $request->get('booking_from_date');
            $to = $request->get('booking_to_date');
            $datatable->whereBetween('shipments.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
}
