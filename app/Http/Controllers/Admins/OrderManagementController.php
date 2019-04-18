<?php

namespace App\Http\Controllers\Admins;


use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\BookingType;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
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
        return view('admin.order_management.index')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status]);
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

                if ($shipment && $shipment->shipper_status_id == 2 && !$shipment->packaging_material_request) {
                    $valid = TRUE;

                    $shipment->shipper_status_id = 20;
                    $shipment->consignee_status_id = 20;

                    $shipment->save();

                    ShipmentsJourneyController::add($shipment_id, 50, 50, NULL, NULL, NULL, Auth::id());

                    ShipmentsJourneyController::add($shipment_id, 20, 20, NULL, NULL, NULL, Auth::id());

                    NotificationsController::send(15, 0, $shipment_id);
                    NotificationsController::send(16, 0, $shipment_id);

                    ShipmentChargesController::return($shipment_id);

                    AdminFinanceController::add_payment($shipment_id, 1);
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
}
