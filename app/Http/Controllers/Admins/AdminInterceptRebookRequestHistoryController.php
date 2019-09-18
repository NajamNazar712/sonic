<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\City;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\InterceptReBookRequestHistory;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function intercept_re_book_index($shipment_id){
        $shipment = Shipment::where('id',$shipment_id)->first();
        $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
            ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
            ->select('c.id as id', 'c.name as name')
            ->where('shipments.id', $shipment_id)
            ->where('c.status', 1)
            ->whereNotNull('c.zone_id')
            ->orderBy('c.name')
            ->groupBy('c.name')
            ->get();
//        dd($consignee_cities);
//        $consignee_cities = City::leftjoin('city_deliveries as cd', 'cd.city_id', '=', 'cities.id')->leftjoin('')->where('status', 1)->where('pickup',1)->whereNotNull('zone_id')->orderBy('name')->get();
        return view('admin.intercept.index')->with(['shipment' => $shipment, 'consignee_cities' => $consignee_cities]);
    }

    public function intercept_re_book_update(Request $request)
    {
        $s_amount = str_replace(",", "", $request->amount);
        $amount = intval($s_amount);
        $shipment = Shipment::find($request->shipment_id);
        $user_id = $shipment->user_id;

        $shipment_status = $shipment->status_shipper->name;

        if ($shipment['shipper_status_id'] == 12) {
            if ($shipment['consignee_city_id'] != $request->consignee_city || $shipment['consignee_name'] != $request->consignee_name || $shipment['consignee_address'] != $request->consignee_address || $shipment['consignee_phone_number_1'] != $request->consignee_phone_number_1 || $shipment['consignee_phone_number_2'] != $request->consignee_phone_number_2 || $shipment['consignee_email'] != $request->consignee_email || $shipment['amount'] != $amount) {
                if ($shipment['intercepted'] == 1) {
                    return redirect()->back()->with('error', 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment['tracking_number']);
                } else {
                    $shipment = Shipment::find($request->shipment_id);
                    $s_amount = str_replace(",", "", "$request->amount");
                    $amount = (int)$s_amount;
                    InterceptReBookRequest::create([
                        'shipment_id' => $request->shipment_id,
                        'consignee_city_id' => $request->consignee_city,
                        'consignee_name' => $request->consignee_name,
                        'consignee_address' => $request->consignee_address,
                        'consignee_phone_number_1' => $request->consignee_phone_number_1,
                        'consignee_phone_number_2' => $request->consignee_phone_number_2,
                        'consignee_email' => $request->consignee_email,
                        'amount' => $amount,
                        'shipper_id' => $user_id,
                        'status' => 0,
                        'admin_id' => Auth::id()
                    ]);

                    $shipment->consignee_status_id = 54;
                    $shipment->shipper_status_id = 54;
                    $shipment->intercepted = 1;
                    $shipment->save();

                    ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, Auth::id());

                    return redirect()->route('admin.return.index')->with('success', 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment['tracking_number']);
                }
            } else {
                return redirect()->back()->with('error', 'Shipment is already book with same details against Tracking Number: ' . $shipment['tracking_number']);
            }
        } else {
            return redirect()->route('admin.return.index')->with('error', 'Shipment is already updated with Status : ' . $shipment_status . ' against Tracking Number: ' . $shipment['tracking_number']);
        }
    }
}
