<?php

namespace App\Http\Controllers\Admins;


use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\BookingType;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\http\Models\WarehouseStock;
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

        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();
        $case_nature = CrmRequestCaseNature::get();
        $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->get();
        $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->get();
        $case_nature_channels = CrmRequestChannel::where('id', '!=', 1)->get();
        return view('admin.order_management.index')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'case_nature_channels' => $case_nature_channels]);
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
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.tracking_number as tracking','shipments.order_id','u.name as shipper','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','shipments.created_at as booking_date','shipments.shipper_status_id', 'sps.name as payment_status', 'shipments.booking_type_id', 'usi.poc','shipments_journey.shipper_status_id as status_id'])
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
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
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
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
            $datatable->whereIn('ss.id', $shipment_status_select);
        }
        if ($request->get('booking_from_date') && $request->get('booking_to_date')) {
            $from = $request->get('booking_from_date');
            $to = $request->get('booking_to_date');
            $datatable->whereBetween('shipments.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
    public function get_shipment_charges(Request $request){
        $shipment_id = $request->shipment_id;
        $shipment = Shipment::find($shipment_id);
        $returnHTML = view('admin/components/shipment_charges')->with(['shipment'=>$shipment])->render();
        return response()->json($returnHTML);
    }
    public function shipper_recall(Request $request) {
        $shipment_ids = $request->shipment_ids;

        if (!empty($shipment_ids)) {
            $valid = FALSE;

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && $shipment->shipper_status_id == 2) {
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

                                    foreach ($packaging_material_request_details as $detail_add) {
                                        $type_id = $detail_add->type_id;
                                        $type_size_id = $detail_add->type_size_id;
                                        $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);

                                        $stock = $stock->first();
                                        $stock->stock = $stock['stock'] + $detail_add->quantity;
                                        $stock->save();
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

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                if ($shipment && ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 2) && !$shipment->packaging_material_request) {
                    $valid_ids[] = $shipment_id;
                }
            }

            if (!empty($valid_ids)) {
                return ['status' => 0, 'success' => 'Valid Shipment(s) Found', 'valid_ids' => $valid_ids];
            }
            else {
                return ['status' => 1, 'error' => 'No Valid Shipment(s) were Selected'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }
}
