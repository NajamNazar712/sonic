<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\BookingType;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\Product;
use App\Http\Models\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class AdminConsolidatedController extends Controller
{

    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function consolidation_history_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        return view('admin.shipment.consolidated.history')->with(['shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products]);
    }

    public function consolidation_history_list(){
        $consolidation_shipments = ConsolidationShipments::leftjoin('shipments as s', 's.id', '=', 'consolidation_shipments.shipment_id')
            ->leftjoin('users as u', 'u.id', '=', 's.user_id')
            ->leftjoin('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('cities as oc', 'oc.id', '=','usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=','s.consignee_city_id')
            ->leftjoin('shipment_items as si', 'si.shipment_id', '=', 's.id')
            ->leftjoin('products as p', 'p.id', '=', 'si.product_type_id')
            ->leftjoin('booking_types as bt', 'bt.id', '=', 's.booking_type_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sjc', function ($join) {
                $join->on('sjc.shipment_id', '=', 's.id')
                    ->where('sjc.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'sjc.shipper_status_id')
            ->select('consolidation_shipments.consolidation_id as id', 's.tracking_number as tracking_number', 's.order_id as order_id', 'oc.name as origin', 'dc.name as destination', 's.consignee_name as consignee_name', 's.consignee_phone_number_1 as consignee_phone_number_1', 's.consignee_address as consignee_address', 's.amount as amount', 'p.product_name as product_type', 's.created_at', 'bt.booking_type as booking_type', 's.consignee_city_id as consignee_city_id', 'sj.created_at as arrival_date', 'u.name as shipper', 'ss.name as status', 'sjc.created_at as status_date', 'consolidation_shipments.order as order', DB::raw('(SELECT count(cs.id) FROM consolidation_shipments AS cs WHERE cs.consolidation_id = consolidation_shipments.consolidation_id) AS order_count'))
            ->groupBy('s.id');
        $datatables = Datatables::of($consolidation_shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('id_padded', function ($consolidation_shipment) {
                return str_pad($consolidation_shipment->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('consolidation', function ($consolidation_shipment) {
                return $consolidation_shipment->order . '/' . $consolidation_shipment->order_count;
            })
            ->editColumn('arrival_date', function ($consolidation_shipment) {
                if($consolidation_shipment->arrival_date){
                    return $consolidation_shipment->arrival_date;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('order_id',function ($consolidation_shipment){
                if($consolidation_shipment->order_id){
                    return $consolidation_shipment->order_id;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('arrival_aging',function ($consolidation_shipment){
                if($consolidation_shipment->arrival_date){
                    $days = Carbon::now()->diffInDays($consolidation_shipment->arrival_date);
                    if($days == 0){
                        return "-";
                    }else{
                        return $days;
                    }
                }
                else{
                    return '-';
                }
            })
            ->addColumn('status_aging',function ($consolidation_shipment){
                $days = Carbon::now()->diffInDays($consolidation_shipment->status_date);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            });
        return $datatables->make(true);
    }
}
