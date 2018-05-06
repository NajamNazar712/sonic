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

                    if(array_key_exists($index,$request->on_wa_switch)){ $wa_switch[] = 1;}else{$wa_switch[] =  0;};
                    if(array_key_exists($index,$request->on_wa_spkg)){ $wa_spkg[] = $request->on_wa_spkg[$index];}else{$wa_spkg[] =  0;};

                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'range_up' => $request->on_wa_range_up[$index],
                        'range_down' => $request->on_wa_range_down[$index],
                        'weight_addition' => $wa_switch[$index],
                        'spkg' => $wa_spkg[$index],
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
            
        }
        //Overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $weightAlready = WeightCharge::where('user_id',$id)->where('shipping_mode_id',2)->get();
            //dd($weightAlready);
            if($weightAlready->isEmpty()) {
                $wa_switch_overland = array();
                $wa_spkg_overland = array();
                foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {

                    if(array_key_exists($index,$request->ol_wa_switch)){ $wa_switch_overland[] = 1;}else{$wa_switch_overland[] =  0;};
                    if(array_key_exists($index,$request->ol_wa_spkg)){ $wa_spkg_overland[] = $request->ol_wa_spkg[$index];}else{$wa_spkg_overland[] =  0;};

                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'range_up' => $request->ol_wa_range_up[$index],
                        'range_down' => $request->ol_wa_range_down[$index],
                        'weight_addition' => $wa_switch_overland[$index],
                        'spkg' => $wa_spkg_overland[$index],
                        'local_or_6hr' => $request->ol_wa_local_charges[$index],
                        'national_or_sameday' => $request->ol_wa_national_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'replacement_charges'=>$request->ol_replacement_charges,
                    'try_and_buy_charges'=>$request->ol_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on'){
                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'range_up'=> $request->ol_cash_range_up[$ind],
                            'range_down'=> $request->ol_cash_range_down[$ind],
                            'charges'=> $request->ol_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on'){
                    foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'range_up'=> $request->ol_ins_range_up[$insurance],
                            'range_down'=> $request->ol_ins_range_down[$insurance],
                            'charges'=> $request->ol_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('ol_return_switch') && $request->ol_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'local'=> $request->ol_return_local_charges,
                        'national'=> $request->ol_return_national_charges
                    ]);
                }
                //Packaging Charges
                if($request->has('ol_packaging_switch') && $request->ol_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'sm_flyer'=> $request->ol_flyer_sm,
                        'md_flyer'=> $request->ol_flyer_md,
                        'lg_flyer'=> $request->ol_flyer_lg,
                        'box_flyer'=> $request->ol_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on'){
                    $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                }
                if($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on'){
                    $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                }
                if($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on'){
                    $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                }
                if($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $to = str_replace('/', '-', $date_sep[0]);
                    $from = str_replace('/', '-', $date_sep[1]);
                    $nto = date_create($to);
                    $to =date_format($nto,"Y-m-d H:i:s");
                    $nfrom = date_create($from);
                    $from =date_format($nfrom,"Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
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
            
        }
        //Detain
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $weightAlready = WeightCharge::where('user_id',$id)->where('shipping_mode_id',3)->get();
            //dd($weightAlready);
            if($weightAlready->isEmpty()) {
                $wa_switch_detain = array();
                $wa_spkg_detain = array();
                foreach ($request->detain_wa_range_up as $index => $detain_wa_range_up) {

                    if(array_key_exists($index,$request->detain_wa_switch)){ $wa_switch_detain[] = 1;}else{$wa_switch_detain[] =  0;};
                    if(array_key_exists($index,$request->detain_wa_spkg)){ $wa_spkg_detain[] = $request->detain_wa_spkg[$index];}else{$wa_spkg_detain[] =  0;};

                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'range_up' => $request->detain_wa_range_up[$index],
                        'range_down' => $request->detain_wa_range_down[$index],
                        'weight_addition' => $wa_switch_detain[$index],
                        'spkg' => $wa_spkg_detain[$index],
                        'local_or_6hr' => $request->detain_wa_local_charges[$index],
                        'national_or_sameday' => $request->detain_wa_national_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>3,
                    'replacement_charges'=>$request->detain_replacement_charges,
                    'try_and_buy_charges'=>$request->detain_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on'){
                    foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'range_up'=> $request->detain_cash_range_up[$ind],
                            'range_down'=> $request->detain_cash_range_down[$ind],
                            'charges'=> $request->detain_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on'){
                    foreach ($request->detain_ins_range_up as $insurance => $detain_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'range_up'=> $request->detain_ins_range_up[$insurance],
                            'range_down'=> $request->detain_ins_range_down[$insurance],
                            'charges'=> $request->detain_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('detain_return_switch') && $request->detain_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'local'=> $request->detain_return_local_charges,
                        'national'=> $request->detain_return_national_charges
                    ]);
                }
                //Packaging Charges
                if($request->has('detain_packaging_switch') && $request->detain_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'sm_flyer'=> $request->detain_flyer_sm,
                        'md_flyer'=> $request->detain_flyer_md,
                        'lg_flyer'=> $request->detain_flyer_lg,
                        'box_flyer'=> $request->detain_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on'){
                    $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : null;
//                    $discount_weight = $request->detain_discount_weight_rate;
                }
                if($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on'){
                    $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : null;
                }
                if($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on'){
                    $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->detain_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $to = str_replace('/', '-', $date_sep[0]);
                    $from = str_replace('/', '-', $date_sep[1]);
                    $nto = date_create($to);
                    $to =date_format($nto,"Y-m-d H:i:s");
                    $nfrom = date_create($from);
                    $from =date_format($nfrom,"Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
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
            
        }
        //Sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $weightAlready = WeightCharge::where('user_id',$id)->where('shipping_mode_id',4)->get();
            //dd($weightAlready);
            if($weightAlready->isEmpty()) {
                $wa_switch_sameday = array();
                $wa_spkg_sameday = array();
                foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {

                    if(array_key_exists($index,$request->sameday_wa_switch)){ $wa_switch_sameday[] = 1;}else{$wa_switch_sameday[] =  0;};
                    if(array_key_exists($index,$request->sameday_wa_spkg)){ $wa_spkg_sameday[] = $request->sameday_wa_spkg[$index];}else{$wa_spkg_sameday[] =  0;};

                    WeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'range_up' => $request->sameday_wa_range_up[$index],
                        'range_down' => $request->sameday_wa_range_down[$index],
                        'weight_addition' => $wa_switch_sameday[$index],
                        'spkg' => $wa_spkg_sameday[$index],
                        'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                        'national_or_sameday' => $request->sameday_wa_national_charges[$index]
                    ]);
                }


                //Replacement and Try and Buy charges
                BookingTypeCharges::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'replacement_charges'=>$request->sameday_replacement_charges,
                    'try_and_buy_charges'=>$request->sameday_tnb_charges
                ]);
                //Cash handling Charges
                if($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on'){
                    foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up){
                        CashHandlingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'range_up'=> $request->sameday_cash_range_up[$ind],
                            'range_down'=> $request->sameday_cash_range_down[$ind],
                            'charges'=> $request->sameday_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on'){
                    foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up){
                        InsuranceCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'range_up'=> $request->sameday_ins_range_up[$insurance],
                            'range_down'=> $request->sameday_ins_range_down[$insurance],
                            'charges'=> $request->sameday_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on'){
                    ReturnCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'local'=> $request->sameday_return_local_charges,
                        'national'=> $request->sameday_return_national_charges
                    ]);
                }
                //Packaging Charges
                if($request->has('sameday_packaging_switch') && $request->sameday_packaging_switch == 'on'){
                    PackagingCharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'sm_flyer'=> $request->sameday_flyer_sm,
                        'md_flyer'=> $request->sameday_flyer_md,
                        'lg_flyer'=> $request->sameday_flyer_lg,
                        'box_flyer'=> $request->sameday_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on'){
                    $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                }
                if($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on'){
                    $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                }
                if($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on'){
                    $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                }
                if($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $to = str_replace('/', '-', $date_sep[0]);
                    $from = str_replace('/', '-', $date_sep[1]);
                    $nto = date_create($to);
                    $to =date_format($nto,"Y-m-d H:i:s");
                    $nfrom = date_create($from);
                    $from =date_format($nfrom,"Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
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
            
        }
        return redirect()->back()->with('info','All Rates Done');
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
