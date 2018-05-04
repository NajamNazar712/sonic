<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\CashHandlingCharge;
use App\Http\Models\InsuranceCharge;
use App\Http\Models\PackagingCharge;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;
use App\Http\Models\CityInfo;
use App\Http\Models\PickupType;
use App\Http\Models\WeightCharge;
use App\Http\Models\BookingTypeCharges;
use App\Http\Models\ReturnCharge;
use App\Http\Models\DiscountCharge;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Collection;

class AdminDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(){
        return view('admin.dashboard');
    }
    public function ecommerce(){
        return view('admin.ecommerce');
    }
    public function orderList(){
        return view('admin.order_management');
    }
    public function orderPending(){
        return view('admin.pending_booked_orders');
    }
    public function pendingAccountsList(){
        return view('admin.accounts.pending_accounts_list');
    }
    public function activeAccountsList(){
        return view('admin.accounts.active_accounts_list');

    }
    public function blockAccountsList(){
        return view('admin.accounts.block_accounts_list');
    }
    public function UserStatus(Request $request){
//        dd($request);
        $id = $request->shid;
        $status = $request->status;
//        $active = User::where('id',$id)->s
        if($status == 'unblock'){
            $user = User::where('id',$id)->where('blacklist',1)->update(['blacklist'=>0]);
            $active = User::find($id)->first()->active;
            if($user == 1){
                if($active == 1){
                    return redirect()->route('admin.accounts.active');
                }elseif($active == 0){
                    return redirect()->route('admin.accounts.pending');
                }
            }
        }
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewBankInfo($id){
        $user = User::find($id);
        $bank = $user->bank;
        $returnHTML = view('admin/components/bank')->with(['bank'=>$bank,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShippingInfo($id){
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function viewShipperRates($id){
        $user = User::find($id);
        $shipping = $user->shipping;
        $returnHTML = view('admin.components.shipping')->with(['shipping'=>$shipping,'user'=>$user])->render();
        return response()->json($returnHTML);
    }
    public function pickup(){
//        $cit = CityInfo::all()->where('city_code','202');
        $cit = PickupType::find(1)->cities()->orderBy('city_name')->get();
//        $cite = $cit->cities()->get();
//        return $cite;


//    return $cit;
    }
    public function addRatesView($id){
        $user = User::find($id);
        return view('admin.accounts.add_rates')->with('shipper',$user);
    }

    /**
     * @param Request $request
     * @param $id
     * @return int
     */
    public function addRates(Request $request, $id){
//        return $request;
//            return $request->on_wa;die();
//                return $request->on_wa;die();
//       $validate = Validator::make($request, [
//            'on_wa_range_up.*' => 'required|between:0,99.99',
//            'on_wa_range_down.*' => 'required|between:0,99.99',
//            'on_wa_local_charges.*' => 'required|numeric',
//            'on_wa_national_charges.*' => 'required|numeric',
//            'on_wa_spkg'=>'numeric'
//
//        ]);
//        if($validate->fails()){
//            return redirect()->back()->with();
//        }
        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $weightAlready = WeightCharge::where('user_id',$id)->where('shipping_mode_id',1)->get();
            //dd($weightAlready);
            if($weightAlready->isEmpty()) {
                $wa_switch = array();
                $wa_spkg = array();
                foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                    $wa_switch = $request->on_wa_switch;
                    $wa_spkg = $request->on_wa_spkg;

                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'range_up' => $request->on_wa_range_up[$index],
                        'range_down' => $request->on_wa_range_down[$index],
                        'weight_addition' => ($wa_switch[$index] ? $wa_switch[$index] : 0),
                        'spkg' => ($request->on_wa_spkg[$index]? $request->on_wa_spkg[$index] : 0.00),
                        'local_or_6hr' => $request->on_wa_local_charges[$index],
                        'national_or_sameday' => $request->on_wa_national_charges[$index]
                    ]);
                }

                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                   'user_id'=>$id,
                   'shipping_mode_id'=>1,
                   'replacement_charges'=>$request->on_replacement_charges,
                   'try_and_buy_charges'=>$request->on_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on'){
                foreach ($request->on_cash_range_up as $ind => $on_cash_range_up){
                    CashHandlingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'range_up'=> $request->on_cash_range_up[$ind],
                        'range_down'=> $request->on_cash_range_down[$ind],
                        'charges'=> $request->on_cash_charges[$ind]
                    ]);
                }
                }
                //insurance charges
                if($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on'){
                    foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'range_up'=> $request->on_ins_range_up[$insurance],
                            'range_down'=> $request->on_ins_range_down[$insurance],
                            'charges'=> $request->on_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('on_return_switch') && $request->on_return_switch == 'on'){
                        ReturnCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'local'=> $request->on_return_local_charges,
                            'national'=> $request->on_return_national_charges
                        ]);
                }
                //Packaging Charges
                if($request->has('on_packaging_switch') && $request->on_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'sm_flyer'=> $request->on_flyer_sm,
                        'md_flyer'=> $request->on_flyer_md,
                        'lg_flyer'=> $request->on_flyer_lg,
                        'box_flyer'=> $request->on_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on'){
                    $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                }
                if($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on'){
                    $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                }
                if($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on'){
                    $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                }
                if($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $to = str_replace('/', '-', $date_sep[0]);
                    $from = str_replace('/', '-', $date_sep[1]);
                    $nto = date_create($to);
                    $to =date_format($nto,"Y-m-d H:i:s");
                    $nfrom = date_create($from);
                    $from =date_format($nfrom,"Y-m-d H:i:s");
//                    var_dump($from);die();
//                    $to = $to . ' 00:00:00';
//                    $from = $from . ' 23:59:59';
                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from
                    ]);
                }

            }
            return "Donnne";
        }
        //Overland
//        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
//            $weightAlreadyOverland = WeightCharge::where('user_id',$id)->where('shipping_mode_id',2)->get();
//            //dd($weightAlready);
//            if($weightAlreadyOverland->isEmpty()) {
//                foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
//                    WeightCharge::create([
//                        'user_id' => $id,
//                        'shipping_mode_id' => 1,
//                        'range_up' => $request->ol_wa_range_up[$index],
//                        'range_down' => $request->ol_wa_range_down[$index],
//                        'weight_addition' => ($request->has("ol_wa_switch[$index]"))? 1 : 0,
//                        'spkg' => ($request->has("ol_wa_spkg[$index]")? $request->ol_wa_spkg[$index] : 0.00),
//                        'local_or_6hr' => $request->ol_wa_local_charges[$index],
//                        'national_or_sameday' => $request->ol_wa_national_charges[$index]
//                    ]);
//                }
//                //Replacement and Try and Buy charges
//                BookingTypeCharges::create([
//                    'user_id'=>$id,
//                    'shipping_mode_id'=>1,
//                    'replacement_charges'=>$request->ol_replacement_charges,
//                    'try_and_buy_charges'=>$request->ol_tnb_charges
//                ]);
//                //Cash handling Charges
//                if($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on'){
//                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up){
//                        CashHandlingCharge::create([
//                            'user_id'=>$id,
//                            'shipping_mode_id'=>1,
//                            'range_up'=> $request->ol_cash_range_up[$ind],
//                            'range_down'=> $request->ol_cash_range_down[$ind],
//                            'charges'=> $request->ol_cash_charges[$ind]
//                        ]);
//                    }
//                }
//                //insurance charges
//                if($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on'){
//                    foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up){
//                        InsuranceCharge::create([
//                            'user_id'=>$id,
//                            'shipping_mode_id'=>1,
//                            'range_up'=> $request->ol_ins_range_up[$insurance],
//                            'range_down'=> $request->ol_ins_range_down[$insurance],
//                            'charges'=> $request->ol_ins_charges[$insurance]
//                        ]);
//                    }
//                }
//                //Return Charges
//                if($request->has('ol_return_switch') && $request->ol_return_switch == 'on'){
//                    ReturnCharge::create([
//                        'user_id'=>$id,
//                        'shipping_mode_id'=>1,
//                        'local'=> $request->ol_return_local_charges,
//                        'national'=> $request->ol_return_national_charges
//                    ]);
//                }
//                //Packaging Charges
//                if($request->has('ol_packaging_switch') && $request->ol_packaging_switch == 'on'){
//                    PackagingCharge::create([
//                        'user_id'=>$id,
//                        'shipping_mode_id'=>1,
//                        'sm_flyer'=> $request->ol_flyer_sm,
//                        'md_flyer'=> $request->ol_flyer_md,
//                        'lg_flyer'=> $request->ol_flyer_lg,
//                        'box_flyer'=> $request->ol_flyer_box
//                    ]);
//                }
//                $discount_cash = null;
//                $discount_weight = null;
//                $discount_insurance = null;
//                $discount_return = null;
//                $discount_packaging = null;
//
//                if($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on'){
//                    $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
////                    $discount_weight = $request->ol_discount_weight_rate;
//                }
//                if($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on'){
//                    $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
//                }
//                if($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on'){
//                    $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
//                }
//                if($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on'){
//                    $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
//                }
//                if($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on'){
//                    $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
//                }
//                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {
//
//
//                    $date_str = $request->on_daterange;
//                    $date_sep = explode(' - ', $date_str);
//                    $to = str_replace('/', '-', $date_sep[0]);
//                    $from = str_replace('/', '-', $date_sep[1]);
//                    $to = $to . ' 00:00:00';
//                    $from = $from . ' 24:59:59';
////                $from = date_create($from);
////                $to =date_format($to,"Y-m-d H:i:s");
////                $from =date_format($from,"Y-m-d H:i:s");
//                    DiscountCharge::create([
//                        'user_id' => $id,
//                        'shipping_mode_id' => 1,
//                        'weight' => $discount_weight,
//                        'cash' => $discount_cash,
//                        'insurance' => $discount_insurance,
//                        'return' => $discount_return,
//                        'packaging' => $discount_packaging,
//                        'to' => $to,
//                        'from' => $from
//                    ]);
//                }
//            }
//        }
        //
    }
    public function activeAccountListAjax(){

        $data  = [];
        $users = User::where('active',1)->where('blacklist',0)->get();
        foreach ($users as $user) {
            $obj = new \stdClass;
            $obj->id = $user->id;
            $obj->name = $user->name;
            $obj->city = $user->city->city_name;
            $obj->poc = $user->poc;
            $obj->phone = $user->phone;
            $obj->address = $user->address;
            $obj->email = $user->email;
            $data[] = $obj;
        }
        $result = new Collection($data);
        return Datatables::of($result)->addColumn("action", function ($result) {
                                            return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>

                                            </div>
                                            </span>";
                                        })
                                      ->make(true);

    }
    public function pendingAccountListAjax(){

        $data  = [];
        $users = User::where('active',0)->where('blacklist',0)->get();
        foreach ($users as $user) {
            $obj = new \stdClass;
            $obj->id = $user->id;
            $obj->name = $user->name;
            $obj->city = $user->city->city_name;
            $obj->poc = $user->poc;
            $obj->phone = $user->phone;
            $obj->address = $user->address;
            $obj->email = $user->email;
            $data[] = $obj;
        }
        $result = new Collection($data);
        return Datatables::of($result)->addColumn("action", function ($result) {
                                            return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>
                                              <a href='".route('admin.add.rates',['id'=> $result->id])."' class='dropdown-item'><i class='ft-plus-circle primary'></i> Add Rates</a>
                                            </div>
                                            </span>";
                                        })
                                      ->make(true);

    }
    public function blockAccountListAjax(){

        $data  = [];
        $users = User::where('blacklist',1)->get();
        foreach ($users as $user) {
            $obj = new \stdClass;
            $obj->id = $user->id;
            $obj->name = $user->name;
            $obj->city = $user->city->city_name;
            $obj->poc = $user->poc;
            $obj->phone = $user->phone;
            $obj->address = $user->address;
            $obj->email = $user->email;
            $data[] = $obj;
        }
        $result = new Collection($data);
        return Datatables::of($result)->addColumn("action", function ($result) {
                                            return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>

                                            </div>
                                            </span>";
                                        })
                                      ->make(true);

    }

}
