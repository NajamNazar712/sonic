<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\InterceptReBookRequestHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class AdminInterceptRebookRequestHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function intercept_request_history_index(){
        return view('admin.delivery.intercept.history');
    }

    public function  intercept_request_history_list(Request $request){
        $intercept = InterceptReBookRequestHistory::join('shipments as s','s.id','=','intercept_re_book_request_histories.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities as odc','odc.id','=','intercept_re_book_request_histories.old_consignee_city_id')
            ->join('cities as nc','nc.id','=','intercept_re_book_request_histories.new_consignee_city_id')
            ->select('s.tracking_number','s.tracking_number as tracking_number_link','oc.name as origin','odc.name as old_consignee_city','nc.name as new_consignee_city','intercept_re_book_request_histories.old_consignee_name','intercept_re_book_request_histories.old_consignee_address','intercept_re_book_request_histories.old_consignee_phone_number_1','intercept_re_book_request_histories.old_consignee_phone_number_2','intercept_re_book_request_histories.old_consignee_email','intercept_re_book_request_histories.new_consignee_name','intercept_re_book_request_histories.new_consignee_address','intercept_re_book_request_histories.new_consignee_phone_number_1','intercept_re_book_request_histories.new_consignee_phone_number_2','intercept_re_book_request_histories.new_consignee_email','intercept_re_book_request_histories.created_at','intercept_re_book_request_histories.old_amount','intercept_re_book_request_histories.new_amount');
        if (session('role_id') != 1) {
            $intercept = $intercept->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereIn('odc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query) {
                        $sub_query->whereIn('nc.hub_id', session('hubs'));
                    });
            });
        }
        return Datatables::of($intercept)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('old_amount', function($shipment){
                return number_format($shipment->old_amount);
            })
            ->editColumn('new_amount', function($shipment){
                return number_format($shipment->new_amount);
            })
            ->make(true);
    }
}
