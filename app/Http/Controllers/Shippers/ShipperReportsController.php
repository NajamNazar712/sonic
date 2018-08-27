<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperReportsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function qsr_index(){
        return view('client.reports.qsr_report');
    }
    public function qsr_list(Request $request){

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
//            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.id as shId','shipments.tracking_number','u.name as shipper','ss.name as history_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','shipments.amount'])
            ->whereNotIn('shipments.shipper_status_id',[1,14,16,17,36,39,40,41,43,47])
        ->where('shipments.user_id',Auth::id());
        $datatable = Datatables::of($shipments)
            ->addColumn('aging',function ($shipments){
                $now = Carbon::now();
                $days = Carbon::now()->diffInDays($shipments->arrival);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->editColumn('arrival', function ($shipments) {
                return $shipments->arrival ? with(new Carbon($shipments->arrival))->format('d/m/Y h:i:s A') : '';
            });
//        if ($shipper = $request->get('search_shipper')) {
//            $datatable->where('u.id', '=', $shipper);
//        }
//        if ($origin = $request->get('search_origin')) {
//            $datatable->where('oc.id', '=', $origin);
//        }
//        if ($destination = $request->get('search_destination')) {
//            $datatable->where('dc.id', '=', $destination);
//        }
//        if ($hub = $request->get('search_hub')) {
//            $datatable->where('h.id', '=', $hub);
//        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('sj.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }
}
