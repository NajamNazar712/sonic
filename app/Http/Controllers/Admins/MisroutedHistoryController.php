<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\MisroutedHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;

class MisroutedHistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function misrouted_history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),47);
        return view('admin.delivery.misroute.history');
    }
    public function misrouted_history_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),107);
        }
        $misrouted = MisroutedHistory::join('shipments as s','s.id','=','misrouted_history.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as odc','odc.id','=','misrouted_history.old_consignee_city_id')
            ->join('cities as nc','nc.id','=','misrouted_history.new_consignee_city_id')
            ->join('admins as ad','ad.id','=','misrouted_history.admin_id')
            ->select('s.tracking_number','s.tracking_number as tracking_number_link','oc.name as origin','odc.name as old_consignee_city','nc.name as new_consignee_city','misrouted_history.old_consignee_name','misrouted_history.old_consignee_address','misrouted_history.old_consignee_phone_number_1','misrouted_history.old_consignee_phone_number_2','misrouted_history.old_consignee_email','misrouted_history.new_consignee_name','misrouted_history.new_consignee_address','misrouted_history.new_consignee_phone_number_1','misrouted_history.new_consignee_phone_number_2','misrouted_history.new_consignee_email','ad.name as updated_by','misrouted_history.created_at as created');
        if (session('role_id') != 1) {
            $misrouted = $misrouted->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('odc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->whereIn('nc.hub_id', session('hubs'));
                    });
            });
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $misrouted->whereBetween('misrouted_history.created_at', [$from, $to]);
        }

        return Datatables::of($misrouted)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->rawColumns(['tracking_number_link'])
            ->make(true);
    }
}
