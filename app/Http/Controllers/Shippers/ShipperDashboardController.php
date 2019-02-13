<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\BookingType;
use App\Http\Models\Product;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\BanksList;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\DisputeType;
use App\Http\Models\PackagingCharge;
use App\Http\Models\Shipment;
use App\Http\Models\City;

use Auth;

use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

//use Illuminate\Support\Facades\Auth;

class ShipperDashboardController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function access_denied() {
        return view('client.access_denied');
    }

    public function orders_index() {
        $should_not_show_status = array(32,33,34,35,36,37,38,46);
        $cities = City::where('status',1)->select('id','name')->get();
        $dispute_types = DisputeType::whereIn('id',[5,9])->get();
        $shipment_status = ShipmentStatus::select('id','name')->whereNotIn('id',$should_not_show_status)->get();
        $service_type = BookingType::all();
        $products = Product::select('id','product_name')->get();
        $payment_status = ShipmentPaymentStatus::all();

      return view('client.dashboard')->with(['cities'=>$cities,'dispute_types'=>$dispute_types,'shipment_status'=>$shipment_status,'service_type'=>$service_type,'products'=>$products,'payment_status'=>$payment_status]);
    }
    public function orders_list(Request $request) {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
            })
            ->join('shipment_status as ss','ss.id','=','shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->select(['shipments_journey.remarks as cancellation_remarks', 'si.description as product_description','shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.order_id','bt.booking_type as service_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.amount','p.product_name as product_type','shipments.created_at as booking_date','shipments.special_instructions as instructions','shipments.shipper_status_id', 'sps.name as payment_status','ssr.name as reason', 'shipments_journey.shipper_status_id as status_id'])
            ->where('shipments.user_id', session('user_id'))
            ->groupBy('shipments.id');

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('phone',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->editColumn('cancellation_remarks',function ($shipments){
                if($shipments->cancellation_remarks != null && $shipments->status_id == 17){
                    return $shipments->cancellation_remarks;
                }
                else{
                    return '-';
                }
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
            ->addColumn('action',function ($shipments) {
                $view_charges_button = '<button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>';
                $cancel_button = '<button type="button" class="dropdown-item cancel_order"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">Cancel</div></button>';
                $dispute_button = '<button type="button" class="dropdown-item dispute_modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Dispute</div></button>';

                if ($shipments->shipper_status_id != 17) {
                    $options = FALSE;

                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                    ';


                    if ($shipments->shipper_status_id > 1) {
                        $dropdown .= $view_charges_button;

                        $options = TRUE;
                    }

                    if ($shipments->shipper_status_id == 1 && (session('user_type') == 1 || in_array(2, session('permissions')))) {
                        $dropdown .= $cancel_button;

                        $options = TRUE;
                    }

                    // if (session('user_type') == 1 || in_array(6, session('permissions'))) {
                    //     $dropdown .= $dispute_button;

                    //     $options = TRUE;
                    // }


                    $dropdown .= '
                            </div>
                        </div>
                    ';

                    if ($options) {
                        return $dropdown;
                    }
                    else {
                        return '';
                    }
                }
                else {
                    return '';
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
    public function order_cancel(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment = Shipment::where('id',$shipment_id)->where('user_id', session('user_id'));
            if($shipment->exists()){
                $shipment = $shipment->first();

                if ($shipment->shipper_status_id == 1) {
                    $shipment->shipper_status_id = 17;
                    $shipment->consignee_status_id = 17;
                    $shipment->consignee_status_id = 17;
                    $shipment->save();

                    AdminPickupsController::cancel($shipment_id);

                    ShipmentsJourneyController::add($shipment_id, 17, 17, NULL, 'Cancelled by Shipper', session('user_id'), NULL);

                    return response()->json(['status'=>1,'success'=>'Shipment has been cancelled successfully']);
                }
                else {
                    return response()->json(['status'=>0,'error'=>'Shipment\'s Status has already been changed']);
                }
            }else{
                return response()->json(['status'=>0,'error'=>'Shipment not found']);
            }
        }
    }
    public function order_cancel_all(Request $request)
    {

        foreach ($request->ids as $id) {
            $shipment_id = Shipment::find($id);
            if ($shipment_id) {
                $shipment = Shipment::where('id', $shipment_id->id)->where('user_id', session('user_id'));
                if ($shipment->exists()) {
                    $shipment = $shipment->first();

                    if ($shipment->shipper_status_id == 1) {
                        $shipment->shipper_status_id = 17;
                        $shipment->consignee_status_id = 17;
                        $shipment->save();

                        AdminPickupsController::cancel($shipment_id->id);

                        ShipmentsJourneyController::add($shipment_id->id, 17, 17, NULL, 'Cancelled by Shipper', session('user_id'), NULL);

                    } else {
                        return response()->json(['status' => 0, 'error' => 'Shipment\'s Status has already been changed']);
                    }
                } else {
                    return  response()->json(['status' => 0, 'error' => 'Shipment not found']);
                }
            }
        }
        return response()->json(['status' => 1, 'success' => 'Shipment has been cancelled successfully']);
    }
    public function get_shipment_charges(Request $request){
        $shipment_id = $request->shipment_id;
        $shipment = Shipment::find($shipment_id);
        $returnHTML = view('client/components/shipment_charges')->with(['shipment'=>$shipment])->render();
        return response()->json($returnHTML);
    }
    public function ecommerce() {
      return view('client.ecommerce');
    }
    public function orderList() {
      return view('client.order_management');
    }
    public function orderPending() {
      return view('client.pending_booked_orders');
    }

    //User Profile

    public function userProfile()
    {
        $user = User::find(session('user_id'));
        $product = Product::find($user->product_id);
        $banks = BanksList::all();
        $pickup_city_list = City::where('pickup',1)->where('status',1)->get();
        return view('client.profile.index')->with(['user'=>$user,'product_name'=>$product->product_name,'banks'=>$banks,'pickup_city_list'=>$pickup_city_list]);
    }

    public function getPickups(Request $request) {
        $pickups = UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
        ->select(['user_shipping_infos.id as id','user_shipping_infos.pickup_address as pickup_address','user_shipping_infos.poc as poc','user_shipping_infos.phone as phone','user_shipping_infos.email as email','user_shipping_infos.status as status','user_shipping_infos.default_address as default_address','user_shipping_infos.user_id as user_id','c.name as city_name'])
        ->where('user_id', session('user_id'))
        ->where('hidden', 0);

        return Datatables::of($pickups)
        ->addColumn('action', function ($pickup) {
            $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
            ';

            $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
            $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
            $default_button = '<button type="button" class="dropdown-item default"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Make Default Address</div></button>';

            if ($pickup->default_address == 1) {
                $dropdown = 'Default Address';
            }
            else {
                if ($pickup->status == 0) {
                    $dropdown .= $enable_button;
                }
                else {
                    $dropdown .= $default_button;

                    if (UserShippingInfo::where('user_id', $pickup->user_id)->where('hidden', 0)->count() > 1) {
                        $dropdown .= $disable_button;
                    }
                }
            }

            $dropdown .= '
                    </div>
                </div>
            ';

            return $dropdown;
        })
        ->editColumn('status', function ($pickup) {
            return ($pickup->status == 1) ? 'Enabled' : 'Disabled';
        })
        ->make(true);
    }


    public function pickupStatusChange(Request $request){
        $pickup_id = $request->id;
        $status = $request->status;
        $shipping_info = UserShippingInfo::where('id',$pickup_id)->first();
        if($shipping_info->exists()){
            if($status == 'enable'){
                if($shipping_info->status == 0){
                    $shipping_info->status = 1;
                    $shipping_info->save();
                    return response()->json(['status'=>1,'success'=>"Pickup Address is now enabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"Pickup Address is already enabled!"]);
                }
            }else if($status == 'disable'){
                if(UserShippingInfo::where('user_id',$shipping_info->user_id)->count()==1)
                {
                    return response()->json(['status'=>0,'error'=>"Single Pickup Address cannot be set to disabled"]);
                }
                if($shipping_info->status == 1){
                    $shipping_info->status = 0;
                    $shipping_info->save();
                    return response()->json(['status'=>1,'success'=>"Pickup Address is now disabled!"]);
                }else{
                    return response()->json(['status'=>0,'error'=>"Pickup Address is already disabled!"]);

                }
            }
            else if($status == 'default')
            {
                if($shipping_info->default_address == 0)
                {
                    $shipping_info->default_address = 1;
                    $shipping_info->save();
                    UserShippingInfo::where('user_id', $shipping_info->user_id)->where('id', '!=', $pickup_id)->update(['default_address' => 0]);
                    return response()->json(['status'=>1,'success'=>"This Pickup Address is now default Pickup Address"]);
                }
                else
                {
                    return response()->json(['status'=>0,'error'=>"Pickup Address is already default Pickup Address"]);
                }

            }
        }else{
            return response()->json(['status'=>0,'error'=>"Pickup Address doesn\'t exist!"]);
        }
    }




    public function addPickup(Request $request) {
        $pickup_address = $request->pickup_address;
        $phone = $request->phone;
        $poc = $request->poc;
        $email = $request->email;
        $city_id = $request->city_id;
        $user_id = session('user_id');

        if($pickup_address != null && $phone != null && $poc != null && $email != null && $city_id != null)
        {
            UserShippingInfo::create(['user_id'=>$user_id,'pickup_address'=>$pickup_address,'poc'=>$poc,
                'email'=>$email,'city_id'=>$city_id,'phone'=>$phone]);
            return redirect()->back()->with('success','Pickup Address added successfully!');

        }else{
            return redirect()->back()->with('error','Pickup Address not added!');
        }
    }





    public function updateProfile(Request $request)
    {

        //1 for Admin, 0 for User
        $request->validate([
            'poc'=>'required|string|max:255',
            'phone'=>'required|string|max:255',
        ]);


        User::where('id', session('user_id'))->update(['poc'=>$request->poc,'phone'=>$request->phone,'phone2'=>$request->phone2,
            'updated_by_type'=>0,'updated_by_id'=> session('user_id')]);


        return redirect()->back()->with(['success'=>"Profile Information Successfully Updated"]);
    }


//    public function statistics_search(Request $request){
//        $graph = array();
//        $destination = $request->destination;
//        $current_date = $request->current_date;
//        $old_date = $request->old_date;
//        $date = $old_date;
//        $dates = array();
//        $dates[] = $date;
//        while ($date != $current_date) {
//            $date = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));
//            $dates[] = $date;
//        }
//        foreach ($dates as $this_date) {
//            $comparison_date = $this_date;
//            if($destination == ''){
//                $graph['dates'][] = Carbon::parse($this_date)->format('d M');
//                $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->where('shipper_status_id',1)->count();
//                $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[2,3,4])->count();
//                $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
//                $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
//                $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where('user_id', session('user_id'))->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
//            }else{
//                $graph['dates'][] = Carbon::parse($this_date)->format('d M');
//                $graph['booked'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination,'shipper_status_id'=>1])->count();
//                $graph['received'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[2,3,4])->count();
//                $graph['delivered'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->count();
//                $graph['pending'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19])->count();
//                $graph['return'][] = Shipment::whereDate('created_at', $comparison_date)->where(['user_id'=> session('user_id'),'consignee_city_id'=>$destination])->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46])->count();
//            }
//
//        }
//        return response()->json(['status'=>1,'graph'=>$graph]);
//    }
}