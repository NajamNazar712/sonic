<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\MisroutedHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class MisroutedHistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function misrouted_history_index(){
        return view('admin.delivery.misroute.history');
    }
    public function misrouted_history_list(Request $request){
        $misrouted = MisroutedHistory::join('shipments as s','s.id','=','misrouted_history.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as odc','odc.id','=','misrouted_history.old_consignee_city_id')
            ->join('cities as nc','nc.id','=','misrouted_history.new_consignee_city_id')
            ->join('admins as ad','ad.id','=','misrouted_history.admin_id')
            ->select('s.tracking_number','s.tracking_number as tracking_number_link','oc.name as origin','odc.name as old_consignee_city','nc.name as new_consignee_city','misrouted_history.old_consignee_name','misrouted_history.old_consignee_address','misrouted_history.old_consignee_phone_number_1','misrouted_history.old_consignee_phone_number_2','misrouted_history.old_consignee_email','misrouted_history.new_consignee_name','misrouted_history.new_consignee_address','misrouted_history.new_consignee_phone_number_1','misrouted_history.new_consignee_phone_number_2','misrouted_history.new_consignee_email','ad.name as updated_by','misrouted_history.created_at');
        if (session('role_id') != 1) {
            $misrouted = $misrouted->whereIn('odc.hub_id', session('hubs'))->orWhereIn('nc.hub_id', session('hubs'));
        }
        return Datatables::of($misrouted)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->make(true);
    }
}
