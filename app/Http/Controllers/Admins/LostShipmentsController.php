<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\BookingType;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class LostShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function lost_shipments_index(){
        $shipment_status = ShipmentStatus::select('id', 'name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.lost.index')->with(['shipment_status' => $shipment_status, 'shipping_mode' => $shipping_mode, 'service_type' => $service_type]);
    }
    public function lost_shipments_list(Request $request){
            $status = array(2, 4, 6, 7, 8, 9, 10, 13, 15, 49); //for pending deliveries
            $normal = 2;
            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->join('cities as h', 'dc.hub_id', '=', 'h.id')
                ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
                ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
                ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
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
                ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
                ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1 as phone', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'ssr.name as reason', 'shipments_journey.remarks as remarks', 'shipments_journey.created_at as status_date', 'shipments_journey.created_at as current_status_date', 'sj.created_at as arrival')
                ->whereRaw('IF (shipments.shipper_status_id = 2, (oc.hub_id = dc.hub_id), TRUE)')
//            ->whereRaw('IF (shipments.shipper_status_id = 49, (oc.hub_id = dc.hub_id), TRUE)')
                ->whereIn('shipments.shipper_status_id', $status);

            if (session('role_id') != 1) {
                $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
            }

            return Datatables::of($shipments)
                ->editColumn('tracking_number_link', function ($shipments) {
                    $route = route('admin.tracking.index');
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                })
                
                ->editColumn('arrival', function ($shipments) {
                    if ($shipments->arrival) {
                        return $shipments->arrival;
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
                ->make(true);
    }

}
