<?php

namespace App\Http\Controllers\Admins;

use App\City;
use App\CityDelivery;
use App\CityHub;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\BookingType;
use App\Http\Models\CashHandlingCharge;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\HubInfo;
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
use App\Http\Models\RateStatus;
use App\Http\Models\ShippingMode;
//standard rates
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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
        $id = $request->shid; //shipper id
        $status = $request->status;
        if($status == 'active'){
            $user = User::find($id);
            if($user->status == 2){
               $action = User::where('id',$id)->update(['status'=>3]);
               if($action == 1){
                   return redirect()->route('admin.accounts.active')->with('success', 'User is activated.');
               }else{
                   return back()->with('danger', 'There is some problem please try again.');
               }
            }else{
                return back()->with('danger', 'This user\'s rates are not set.');
            }
        }

//        if($status == 'unblock'){
//            $user = User::where('id',$id)->where('blacklist',1)->update(['blacklist'=>0]);
//            $active = User::find($id)->first()->active;
//            if($user == 1){
//                if($active == 1){
//                    return redirect()->route('admin.accounts.active');
//                }elseif($active == 0){
//                    return redirect()->route('admin.accounts.pending');
//                }
//            }
//        }
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

    public function addRatesView($id){
        $user = User::find($id);
        $weight = StandardWeightCharge::all()->groupBy('shipping_mode_id');
//        return $weight;

        $bookingType = StandardBookingTypeCharge::all()->groupBy('shipping_mode_id');
        $cash = StandardCashHandlingCharge::all()->groupBy('shipping_mode_id');
        $insurance = StandardInsuranceCharge::all()->groupBy('shipping_mode_id');
        $return = StandardReturnCharge::all()->groupBy('shipping_mode_id');
        $fuel = StandardFuelSurcharge::all()->groupBy('shipping_mode_id');
        $packaging = StandardPackagingCharge::all()->groupBy('shipping_mode_id');
        return view('admin.accounts.add_rates')->with(['shipper'=>$user,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging]);
    }

    public function editRatesView($id){
        $user = User::find($id);
        $switches = RateStatus::all()->where('user_id',$id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
        $weight = WeightCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
//        $cash = '';
        $bookingType = BookingTypeCharges::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $cash = CashHandlingCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $insurance = InsuranceCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $return = ReturnCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $fuel = FuelSurcharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $packaging = PackagingCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
        $discount = DiscountCharge::all()->where('user_id',$id)->groupBy('shipping_mode_id');
//        return $discount;
        return view('admin.accounts.edit_rates')->with(['shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'shippingType'=>$bookingType,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel,'packagingCharges'=>$packaging,'discountCharges'=>$discount]);

    }
    public function editRates(Request $request, $id){
//        return $request;

        $messages = [
            'on_wa_range_up.*.required' => 'The overnight range up field is required.',
            'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
            'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 99.99.',
            'on_wa_range_down.*.required' => 'The overnight range down field is required.',
            'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
            'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 99.99.',
            'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
            'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
            'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
            'on_wa_national_charges.*.required' => 'The overnight national charges field is required.',
            'on_wa_national_charges.*.numeric' => 'The overnight national charges field must be numeric.',
            'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
            'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
            'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
            'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
            'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
            'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
            'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
            'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
            'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
            //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
            'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
            'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
            'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
            'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
            'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
            //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
            'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
            'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
            'on_return_national_charges.required_if' => 'The overnight return national charges field is required.',
            'on_return_national_charges.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
            'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'on_flyer_sm.required_if' => 'The overnight small flyer field is required',
            'on_flyer_sm.numeric' => 'The overnight small flyer field must be numeric',
            'on_flyer_md.required_if' => 'The overnight meduim flyer field is required',
            'on_flyer_md.numeric' => 'The overnight medium flyer field must be numeric',
            'on_flyer_lg.required_if' => 'The overnight large flyer field is required',
            'on_flyer_lg.numeric' => 'The overnight large flyer field must be numeric',
            'on_flyer_box.required_if' => 'The overnight box flyer field is required',
            'on_flyer_box.numeric' => 'The overnight box flyer field must be numeric',
            'on_discount_title.required_if' => 'The overnight discount title field must be required',
            'on_daterange.required_if' => 'The overnight discount date range field must be required',
            'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
            'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
            'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
            'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
            'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
            'on_discount_title.required_with'=>'The overnight discount title field is required',
            'on_daterange.required_with'=>'The overnight discount date field is required',
            //overland starts
            'ol_wa_range_up.*.required' => 'The overland range up field is required.',
            'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
            'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 99.99.',
            'ol_wa_range_down.*.required' => 'The overland range down field is required.',
            'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
            'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 99.99.',
            'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
            'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
            'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
            'ol_wa_national_charges.*.required' => 'The overland national charges field is required.',
            'ol_wa_national_charges.*.numeric' => 'The overland national charges field must be numeric.',
            'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
            'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
            'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
            'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
            'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
            'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
            'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
            'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
            'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
            //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
            'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
            'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
            'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
            'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
            'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
            //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
            'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
            'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
            'ol_return_national_charges.required_if' => 'The overland return national charges field is required.',
            'ol_return_national_charges.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
            'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'ol_flyer_sm.required_if' => 'The overland small flyer field is required',
            'ol_flyer_sm.numeric' => 'The overland small flyer field must be numeric',
            'ol_flyer_md.required_if' => 'The overland meduim flyer field is required',
            'ol_flyer_md.numeric' => 'The overland medium flyer field must be numeric',
            'ol_flyer_lg.required_if' => 'The overland large flyer field is required',
            'ol_flyer_lg.numeric' => 'The overland large flyer field must be numeric',
            'ol_flyer_box.required_if' => 'The overland box flyer field is required',
            'ol_flyer_box.numeric' => 'The overland box flyer field must be numeric',
            'ol_discount_title.required_if' => 'The overland discount title field must be required',
            'ol_daterange.required_if' => 'The overland discount date range field must be required',
            'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
            'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
            'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
            'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
            'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
            'ol_discount_title.required_with'=>'The overland discount title field is required',
            'ol_daterange.required_with'=>'The overland discount date field is required',
            //overland end and detain starts
            'detain_wa_range_up.*.required' => 'The detain range up field is required.',
            'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
            'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 99.99.',
            'detain_wa_range_down.*.required' => 'The detain range down field is required.',
            'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
            'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 99.99.',
            'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
            'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
            'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
            'detain_wa_national_charges.*.required' => 'The detain national charges field is required.',
            'detain_wa_national_charges.*.numeric' => 'The detain national charges field must be numeric.',
            'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
            'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
            'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
            'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
            'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
            'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
            'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
            'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
            'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
            //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
            'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
            'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
            'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
            'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
            'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
            //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
            'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
            'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
            'detain_return_national_charges.required_if' => 'The detain return national charges field is required.',
            'detain_return_national_charges.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
            'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_flyer_sm.required_if' => 'The detain small flyer field is required',
            'detain_flyer_sm.numeric' => 'The detain small flyer field must be numeric',
            'detain_flyer_md.required_if' => 'The detain meduim flyer field is required',
            'detain_flyer_md.numeric' => 'The detain medium flyer field must be numeric',
            'detain_flyer_lg.required_if' => 'The detain large flyer field is required',
            'detain_flyer_lg.numeric' => 'The detain large flyer field must be numeric',
            'detain_flyer_box.required_if' => 'The detain box flyer field is required',
            'detain_flyer_box.numeric' => 'The detain box flyer field must be numeric',
            'detain_discount_title.required_if' => 'The detain discount title field must be required',
            'detain_daterange.required_if' => 'The detain discount date range field must be required',
            'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
            'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
            'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
            'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
            'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
            'detain_discount_title.required_with'=>'The detain discount title field is required',
            'detain_daterange.required_with'=>'The detain discount date field is required',
            //detain ends and sameday starts
            'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
            'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
            'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 99.99.',
            'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
            'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
            'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 99.99.',
            'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
            'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
            'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
            'sameday_wa_national_charges.*.required' => 'The sameday national charges field is required.',
            'sameday_wa_national_charges.*.numeric' => 'The sameday national charges field must be numeric.',
            'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
            'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
            'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
            'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
            'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
            'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
            'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
            'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
            'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
            // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
            'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
            'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
            'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
            'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
            'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
            //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
            'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
            'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
            'sameday_return_national_charges.required_if' => 'The sameday return national charges field is required.',
            'sameday_return_national_charges.numeric' => 'The sameday return national charges field must be numeric or percentage.',
            'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
            'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',
            'sameday_flyer_sm.required_if' => 'The sameday small flyer field is required',
            'sameday_flyer_sm.numeric' => 'The sameday small flyer field must be numeric',
            'sameday_flyer_md.required_if' => 'The sameday meduim flyer field is required',
            'sameday_flyer_md.numeric' => 'The sameday medium flyer field must be numeric',
            'sameday_flyer_lg.required_if' => 'The sameday large flyer field is required',
            'sameday_flyer_lg.numeric' => 'The sameday large flyer field must be numeric',
            'sameday_flyer_box.required_if' => 'The sameday box flyer field is required',
            'sameday_flyer_box.numeric' => 'The sameday box flyer field must be numeric',
            'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
            'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
            'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
            'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
            'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
            'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
            'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
            'sameday_discount_title.required_with'=>'The sameday discount title field is required',
            'sameday_daterange.required_with'=>'The sameday discount date field is required',
            //sameday ends
        ];

        $validations = array();
        $on_validations = array();
        $ol_validations = array();
        $detain_validations = array();
        $sameday_validations = array();

        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $on_validations = [
                'on_wa_range_up.*' => 'required|numeric|between:0,1000',
                'on_wa_range_down.*' => 'required|numeric|between:0,1000',
                'on_wa_local_charges.*' => 'required|numeric',
                'on_wa_national_charges.*' => 'required|numeric',
                'on_wa_spkg.*'=>'numeric',
                'on_replacement_charges'=>'required|numeric',
                'on_tnb_charges'=>'required|numeric',
                'on_cash_range_up.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*'=>'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*'=>'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_national_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'overnight_fuel_surcharge'=>'required_if:overnight_fuel_switch,==,on|numeric',
                'on_flyer_sm'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_md'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_lg'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_box'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_discount_title'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_daterange'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_discount_weight_rate'=>'required_if:on_discount_weight_switch,==,on|numeric',
                'on_discount_cash_rate'=>'required_if:on_discount_cash_switch,==,on|numeric',
                'on_discount_insurance_rate'=>'required_if:on_discount_insurance_switch,==,on|numeric',
                'on_discount_return_rate'=>'required_if:on_discount_return_switch,==,on|numeric',
                'on_discount_packaging_rate'=>'required_if:on_discount_packaging_switch,==,on|numeric'
            ];
        }
        //overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $ol_validations = [
                'ol_wa_range_up.*' => 'required|numeric|between:0,1000',
                'ol_wa_range_down.*' => 'required|numeric|between:0,1000',
                'ol_wa_local_charges.*' => 'required|numeric',
                'ol_wa_national_charges.*' => 'required|numeric',
                'ol_wa_spkg.*'=>'numeric',
                'ol_replacement_charges'=>'required|numeric',
                'ol_tnb_charges'=>'required|numeric',
                'ol_cash_range_up.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*'=>'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*'=>'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_national_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'overland_fuel_surcharge'=>'required_if:overland_fuel_switch,==,on|numeric',
                'ol_flyer_sm'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_md'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_lg'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_box'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_discount_title'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_daterange'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_discount_weight_rate'=>'required_if:ol_discount_weight_switch,==,on|numeric',
                'ol_discount_cash_rate'=>'required_if:ol_discount_cash_switch,==,on|numeric',
                'ol_discount_insurance_rate'=>'required_if:ol_discount_insurance_switch,==,on|numeric',
                'ol_discount_return_rate'=>'required_if:ol_discount_return_switch,==,on|numeric',
                'ol_discount_packaging_rate'=>'required_if:ol_discount_packaging_switch,==,on|numeric',
            ];
        }
        //overland
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $detain_validations = [
                'detain_wa_range_up.*' => 'required|numeric|between:0,1000',
                'detain_wa_range_down.*' => 'required|numeric|between:0,1000',
                'detain_wa_local_charges.*' => 'required|numeric',
                'detain_wa_national_charges.*' => 'required|numeric',
                'detain_wa_spkg.*'=>'numeric',
                'detain_replacement_charges'=>'required|numeric',
                'detain_tnb_charges'=>'required|numeric',
                'detain_cash_range_up.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*'=>'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*'=>'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_national_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_fuel_surcharge'=>'required_if:detain_fuel_switch,==,on|numeric',
                'detain_flyer_sm'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_md'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_lg'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_box'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_discount_title'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_daterange'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_discount_weight_rate'=>'required_if:detain_discount_weight_switch,==,on|numeric',
                'detain_discount_cash_rate'=>'required_if:detain_discount_cash_switch,==,on|numeric',
                'detain_discount_insurance_rate'=>'required_if:detain_discount_insurance_switch,==,on|numeric',
                'detain_discount_return_rate'=>'required_if:detain_discount_return_switch,==,on|numeric',
                'detain_discount_packaging_rate'=>'required_if:detain_discount_packaging_switch,==,on|numeric',
            ];
        }
        //sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $sameday_validations = [
                'sameday_wa_range_up.*' => 'required|numeric|between:0,1000',
                'sameday_wa_range_down.*' => 'required|numeric|between:0,1000',
                'sameday_wa_local_charges.*' => 'required|numeric',
                'sameday_wa_national_charges.*' => 'required|numeric',
                'sameday_wa_spkg.*'=>'numeric',
                'sameday_replacement_charges'=>'required|numeric',
                'sameday_tnb_charges'=>'required|numeric',
                'sameday_cash_range_up.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*'=>'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*'=>'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_national_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge'=>'required_if:sameday_fuel_switch,==,on|numeric',
                'sameday_flyer_sm'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_md'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_lg'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_box'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_discount_title'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate'=>'required_if:sameday_discount_weight_switch,==,on|numeric',
                'sameday_discount_cash_rate'=>'required_if:sameday_discount_cash_switch,==,on|numeric',
                'sameday_discount_insurance_rate'=>'required_if:sameday_discount_insurance_switch,==,on|numeric',
                'sameday_discount_return_rate'=>'required_if:sameday_discount_return_switch,==,on|numeric',
                'sameday_discount_packaging_rate'=>'required_if:sameday_discount_packaging_switch,==,on|numeric',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }


        if($request->on_rate_record != null){
            RateStatus::where('id',$request->on_rate_record)
                ->update([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'status'=> ($request->has('on_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('on_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('on_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('on_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('on_packaging_switch'))? 1:0
                ]);
        }else{
            RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'status'=> ($request->has('on_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('on_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('on_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('on_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('on_packaging_switch'))? 1:0
                ]);
        }
        if($request->ol_rate_record != null){
            RateStatus::where('id',$request->ol_rate_record)
                ->update([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'status'=> ($request->has('ol_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('ol_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('ol_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('ol_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overland_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('ol_packaging_switch'))? 1:0
                ]);
        }else{
            RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'status'=> ($request->has('ol_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('ol_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('ol_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('ol_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overland_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('ol_packaging_switch'))? 1:0
                ]);
        }
        if($request->det_rate_record != null){
            RateStatus::where('id',$request->det_rate_record)
                ->update([
                    'user_id'=>$id,
                    'shipping_mode_id'=>3,
                    'status'=> ($request->has('detain_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('detain_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('detain_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('detain_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('detain_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('detain_packaging_switch'))? 1:0
                ]);
        }else{
            RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>3,
                    'status'=> ($request->has('detain_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('detain_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('detain_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('detain_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('detain_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('detain_packaging_switch'))? 1:0
                ]);
        }
        if($request->same_rate_record != null){
            RateStatus::where('id',$request->same_rate_record)
                ->update([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'status'=> ($request->has('sameday_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('sameday_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('sameday_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('sameday_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('sameday_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('sameday_packaging_switch'))? 1:0
                ]);
        }else{
            RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'status'=> ($request->has('sameday_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('sameday_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('sameday_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('sameday_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('sameday_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('sameday_packaging_switch'))? 1:0
                ]);
        }



        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $ONRateAlready = RateStatus::where(['user_id'=>$id,'shipping_mode_id'=>1])->get();

            if(!$ONRateAlready->isEmpty()) {

                $wa_switch = array();
                $wa_spkg = array();
                WeightCharge::where(['user_id'=>$id,'shipping_mode_id'=>1])->whereNotIn('id', $request->on_weight_record)->delete();
                foreach ($request->on_weight_record as $index => $on_weight_record) {
                    if($request->has('on_wa_switch')) {
                        if (array_key_exists($index, $request->on_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('on_wa_spkg')) {
                        if (array_key_exists($index, $request->on_wa_spkg)) {
                            $wa_spkg[$index] = $request->on_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0;
                        };
                    }else{
                        $wa_spkg[$index] = 0;
                    }
                    if($request->on_weight_record[$index] != null){

                        $weight_row = WeightCharge::where('id',$request->on_weight_record[$index])
                            ->update([
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
                    if($request->on_weight_record[$index] == null){
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


                }

                //Replacement and Try and Buy charges
                if($request->on_booking_record != null){
                    BookingTypeCharges::where(['id'=>$request->on_booking_record])->update([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'replacement_charges'=>$request->on_replacement_charges,
                        'try_and_buy_charges'=>$request->on_tnb_charges
                    ]);
                }else{
                    BookingTypeCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'replacement_charges'=>$request->on_replacement_charges,
                        'try_and_buy_charges'=>$request->on_tnb_charges
                    ]);
                }


                //Cash handling Charges
                if($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on'){
                    CashHandlingCharge::where(['user_id'=>$id,'shipping_mode_id'=>1])->whereNotIn('id', $request->on_cash_record)->delete();

                    foreach ($request->on_cash_record as $index => $on_cash_record){
                        if($request->on_cash_record[$index] != null){
                        CashHandlingCharge::where(['id'=>$request->on_cash_record[$index]])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'range_up'=> $request->on_cash_range_up[$index],
                            'range_down'=> $request->on_cash_range_down[$index],
                            'charges'=> $request->on_cash_charges[$index]
                        ]);
                        }
                        if($request->on_cash_record[$index] == null){
                            CashHandlingCharge::create([
                                'user_id'=>$id,
                                'shipping_mode_id'=>1,
                                'range_up'=> $request->on_cash_range_up[$index],
                                'range_down'=> $request->on_cash_range_down[$index],
                                'charges'=> $request->on_cash_charges[$index]
                            ]);
                        }
                    }
                }
                //insurance charges
                if($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on'){
                    InsuranceCharge::where(['user_id'=>$id,'shipping_mode_id'=>1])->whereNotIn('id', $request->on_insurance_record)->delete();
                    foreach ($request->on_insurance_record as $insurance => $on_insurance_record){
                        if($request->on_insurance_record[$insurance] != null) {
                            InsuranceCharge::where(['id'=>$request->on_insurance_record[$insurance]])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_ins_range_up[$insurance],
                                'range_down' => $request->on_ins_range_down[$insurance],
                                'charges' => $request->on_ins_charges[$insurance]
                            ]);
                        }
                        if($request->on_insurance_record[$insurance] == null){
                            InsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_ins_range_up[$insurance],
                                'range_down' => $request->on_ins_range_down[$insurance],
                                'charges' => $request->on_ins_charges[$insurance]
                            ]);
                        }
                    }
                }
                //Return Charges
                if($request->has('on_return_switch') && $request->on_return_switch == 'on'){
                    if($request->on_return_record != null){
                        ReturnCharge::where(['id'=>$request->on_return_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'local'=> $request->on_return_local_charges,
                            'national'=> $request->on_return_national_charges
                        ]);
                    }elseif ($request->on_return_record == null){
                        ReturnCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'local'=> $request->on_return_local_charges,
                            'national'=> $request->on_return_national_charges
                        ]);
                    }

                }
                //Return Charges
                if($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on'){
                    if($request->on_fuel_record != null){
                        FuelSurcharge::where(['id'=>$request->on_fuel_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'fuel_surcharge'=> $request->overnight_fuel_surcharge
                        ]);
                    }elseif($request->on_fuel_record == null){
                        FuelSurcharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'fuel_surcharge'=> $request->overnight_fuel_surcharge
                        ]);
                    }

                }
                //Packaging Charges
                if($request->has('on_packaging_switch') && $request->on_packaging_switch == 'on'){
                    if($request->on_packaging_record != null){
                        PackagingCharge::where(['id'=>$request->on_packaging_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'sm_flyer'=> $request->on_flyer_sm,
                            'md_flyer'=> $request->on_flyer_md,
                            'lg_flyer'=> $request->on_flyer_lg,
                            'box_flyer'=> $request->on_flyer_box
                        ]);
                    }else{
                        PackagingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>1,
                            'sm_flyer'=> $request->on_flyer_sm,
                            'md_flyer'=> $request->on_flyer_md,
                            'lg_flyer'=> $request->on_flyer_lg,
                            'box_flyer'=> $request->on_flyer_box
                        ]);
                    }

                }
                $discount_cash = 0;
                $discount_weight = 0;
                $discount_insurance = 0;
                $discount_return = 0;
                $discount_packaging = 0;

                if($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on'){
                    $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : 0;
//                    $discount_weight = $request->on_discount_weight_rate;
                }
                if($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on'){
                    $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : 0;
                }
                if($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : 0;
                }
                if($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on'){
                    $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : 0;
                }
                if($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : 0;
                }
                if($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();

                    if($request->on_discount_record != null){
                        DiscountCharge::where(['id'=>$request->on_discount_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title'=> $request->on_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }elseif ($request->on_discount_record == null){
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title'=> $request->on_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }

                }

            }
            //dd($weightAlready);
        }

        //overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $ONRateAlready = RateStatus::where(['user_id'=>$id,'shipping_mode_id'=>2])->get();

            if(!$ONRateAlready->isEmpty()) {

                $wa_switch = array();
                $wa_spkg = array();
                WeightCharge::where(['user_id'=>$id,'shipping_mode_id'=>2])->whereNotIn('id', $request->ol_weight_record)->delete();
                foreach ($request->ol_weight_record as $index => $ol_weight_record) {
                    if($request->has('ol_wa_switch')) {
                        if (array_key_exists($index, $request->ol_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('ol_wa_spkg')) {
                        if (array_key_exists($index, $request->ol_wa_spkg)) {
                            $wa_spkg[$index] = $request->ol_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0;
                        };
                    }else{
                        $wa_spkg[$index] = 0;
                    }
                    if($request->ol_weight_record[$index] != null){

                        $weight_row = WeightCharge::where('id',$request->ol_weight_record[$index])
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_wa_range_up[$index],
                                'range_down' => $request->ol_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->ol_wa_local_charges[$index],
                                'national_or_sameday' => $request->ol_wa_national_charges[$index]
                            ]);
                    }
                    if($request->ol_weight_record[$index] == null){
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $request->ol_wa_range_up[$index],
                            'range_down' => $request->ol_wa_range_down[$index],
                            'weight_addition' => $wa_switch[$index],
                            'spkg' => $wa_spkg[$index],
                            'local_or_6hr' => $request->ol_wa_local_charges[$index],
                            'national_or_sameday' => $request->ol_wa_national_charges[$index]
                        ]);
                    }


                }

                //Replacement and Try and Buy charges
                if($request->ol_booking_record != null){
                    BookingTypeCharges::where(['id'=>$request->ol_booking_record])->update([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'replacement_charges'=>$request->ol_replacement_charges,
                        'try_and_buy_charges'=>$request->ol_tnb_charges
                    ]);
                }else{
                    BookingTypeCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'replacement_charges'=>$request->ol_replacement_charges,
                        'try_and_buy_charges'=>$request->ol_tnb_charges
                    ]);
                }


                //Cash handling Charges
                if($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on'){
                    CashHandlingCharge::where(['user_id'=>$id,'shipping_mode_id'=>2])->whereNotIn('id', $request->ol_cash_record)->delete();

                    foreach ($request->ol_cash_record as $index => $ol_cash_record){
                        if($request->ol_cash_record[$index] != null){
                            CashHandlingCharge::where(['id'=>$request->ol_cash_record[$index]])->update([
                                'user_id'=>$id,
                                'shipping_mode_id'=>2,
                                'range_up'=> $request->ol_cash_range_up[$index],
                                'range_down'=> $request->ol_cash_range_down[$index],
                                'charges'=> $request->ol_cash_charges[$index]
                            ]);
                        }
                        if($request->ol_cash_record[$index] == null){
                            CashHandlingCharge::create([
                                'user_id'=>$id,
                                'shipping_mode_id'=>2,
                                'range_up'=> $request->ol_cash_range_up[$index],
                                'range_down'=> $request->ol_cash_range_down[$index],
                                'charges'=> $request->ol_cash_charges[$index]
                            ]);
                        }
                    }
                }
                //insurance charges
                if($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on'){
                    InsuranceCharge::where(['user_id'=>$id,'shipping_mode_id'=>2])->whereNotIn('id', $request->ol_insurance_record)->delete();
                    foreach ($request->ol_insurance_record as $insurance => $ol_insurance_record){
                        if($request->ol_insurance_record[$insurance] != null) {
                            InsuranceCharge::where(['id'=>$request->ol_insurance_record[$insurance]])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_ins_range_up[$insurance],
                                'range_down' => $request->ol_ins_range_down[$insurance],
                                'charges' => $request->ol_ins_charges[$insurance]
                            ]);
                        }
                        if($request->ol_insurance_record[$insurance] == null){
                            InsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_ins_range_up[$insurance],
                                'range_down' => $request->ol_ins_range_down[$insurance],
                                'charges' => $request->ol_ins_charges[$insurance]
                            ]);
                        }
                    }
                }
                //Return Charges
                if($request->has('ol_return_switch') && $request->ol_return_switch == 'on'){
                    if($request->ol_return_record != null){
                        ReturnCharge::where(['id'=>$request->ol_return_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'local'=> $request->ol_return_local_charges,
                            'national'=> $request->ol_return_national_charges
                        ]);
                    }elseif ($request->ol_return_record == null){
                        ReturnCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'local'=> $request->ol_return_local_charges,
                            'national'=> $request->ol_return_national_charges
                        ]);
                    }

                }
                //Return Charges
                if($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on'){
                    if($request->ol_fuel_record != null){
                        FuelSurcharge::where(['id'=>$request->ol_fuel_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'fuel_surcharge'=> $request->overland_fuel_surcharge
                        ]);
                    }elseif($request->ol_fuel_record == null){
                        FuelSurcharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'fuel_surcharge'=> $request->overland_fuel_surcharge
                        ]);
                    }

                }
                //Packaging Charges
                if($request->has('ol_packaging_switch') && $request->ol_packaging_switch == 'on'){
                    if($request->ol_packaging_record != null){
                        PackagingCharge::where(['id'=>$request->ol_packaging_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'sm_flyer'=> $request->ol_flyer_sm,
                            'md_flyer'=> $request->ol_flyer_md,
                            'lg_flyer'=> $request->ol_flyer_lg,
                            'box_flyer'=> $request->ol_flyer_box
                        ]);
                    }else{
                        PackagingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>2,
                            'sm_flyer'=> $request->ol_flyer_sm,
                            'md_flyer'=> $request->ol_flyer_md,
                            'lg_flyer'=> $request->ol_flyer_lg,
                            'box_flyer'=> $request->ol_flyer_box
                        ]);
                    }

                }
                $discount_cash = 0;
                $discount_weight = 0;
                $discount_insurance = 0;
                $discount_return = 0;
                $discount_packaging = 0;

                if($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on'){
                    $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : 0;
//                    $discount_weight = $request->ol_discount_weight_rate;
                }
                if($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on'){
                    $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : 0;
                }
                if($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : 0;
                }
                if($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on'){
                    $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : 0;
                }
                if($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : 0;
                }
                if($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();

                    if($request->ol_discount_record != null){
                        DiscountCharge::where(['id'=>$request->ol_discount_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title'=> $request->ol_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }elseif ($request->ol_discount_record == null){
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title'=> $request->ol_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }

                }

            }
            //dd($weightAlready);
        }

        //detain
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $ONRateAlready = RateStatus::where(['user_id'=>$id,'shipping_mode_id'=>3])->get();

            if(!$ONRateAlready->isEmpty()) {

                $wa_switch = array();
                $wa_spkg = array();
                WeightCharge::where(['user_id'=>$id,'shipping_mode_id'=>3])->whereNotIn('id', $request->detain_weight_record)->delete();
                foreach ($request->detain_weight_record as $index => $detain_weight_record) {
                    if($request->has('detain_wa_switch')) {
                        if (array_key_exists($index, $request->detain_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('detain_wa_spkg')) {
                        if (array_key_exists($index, $request->detain_wa_spkg)) {
                            $wa_spkg[$index] = $request->detain_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0;
                        };
                    }else{
                        $wa_spkg[$index] = 0;
                    }
                    if($request->detain_weight_record[$index] != null){

                        $weight_row = WeightCharge::where('id',$request->detain_weight_record[$index])
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_wa_range_up[$index],
                                'range_down' => $request->detain_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->detain_wa_local_charges[$index],
                                'national_or_sameday' => $request->detain_wa_national_charges[$index]
                            ]);
                    }
                    if($request->detain_weight_record[$index] == null){
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_wa_range_up[$index],
                            'range_down' => $request->detain_wa_range_down[$index],
                            'weight_addition' => $wa_switch[$index],
                            'spkg' => $wa_spkg[$index],
                            'local_or_6hr' => $request->detain_wa_local_charges[$index],
                            'national_or_sameday' => $request->detain_wa_national_charges[$index]
                        ]);
                    }


                }

                //Replacement and Try and Buy charges
                if($request->detain_booking_record != null){
                    BookingTypeCharges::where(['id'=>$request->detain_booking_record])->update([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'replacement_charges'=>$request->detain_replacement_charges,
                        'try_and_buy_charges'=>$request->detain_tnb_charges
                    ]);
                }else{
                    BookingTypeCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'replacement_charges'=>$request->detain_replacement_charges,
                        'try_and_buy_charges'=>$request->detain_tnb_charges
                    ]);
                }


                //Cash handling Charges
                if($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on'){
                    CashHandlingCharge::where(['user_id'=>$id,'shipping_mode_id'=>3])->whereNotIn('id', $request->detain_cash_record)->delete();

                    foreach ($request->detain_cash_record as $index => $detain_cash_record){
                        if($request->detain_cash_record[$index] != null){
                            CashHandlingCharge::where(['id'=>$request->detain_cash_record[$index]])->update([
                                'user_id'=>$id,
                                'shipping_mode_id'=>3,
                                'range_up'=> $request->detain_cash_range_up[$index],
                                'range_down'=> $request->detain_cash_range_down[$index],
                                'charges'=> $request->detain_cash_charges[$index]
                            ]);
                        }
                        if($request->detain_cash_record[$index] == null){
                            CashHandlingCharge::create([
                                'user_id'=>$id,
                                'shipping_mode_id'=>3,
                                'range_up'=> $request->detain_cash_range_up[$index],
                                'range_down'=> $request->detain_cash_range_down[$index],
                                'charges'=> $request->detain_cash_charges[$index]
                            ]);
                        }
                    }
                }
                //insurance charges
                if($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on'){
                    InsuranceCharge::where(['user_id'=>$id,'shipping_mode_id'=>3])->whereNotIn('id', $request->detain_insurance_record)->delete();
                    foreach ($request->detain_insurance_record as $insurance => $detain_insurance_record){
                        if($request->detain_insurance_record[$insurance] != null) {
                            InsuranceCharge::where(['id'=>$request->detain_insurance_record[$insurance]])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_ins_range_up[$insurance],
                                'range_down' => $request->detain_ins_range_down[$insurance],
                                'charges' => $request->detain_ins_charges[$insurance]
                            ]);
                        }
                        if($request->detain_insurance_record[$insurance] == null){
                            InsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'range_up' => $request->detain_ins_range_up[$insurance],
                                'range_down' => $request->detain_ins_range_down[$insurance],
                                'charges' => $request->detain_ins_charges[$insurance]
                            ]);
                        }
                    }
                }
                //Return Charges
                if($request->has('detain_return_switch') && $request->detain_return_switch == 'on'){
                    if($request->detain_return_record != null){
                        ReturnCharge::where(['id'=>$request->detain_return_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'local'=> $request->detain_return_local_charges,
                            'national'=> $request->detain_return_national_charges
                        ]);
                    }elseif ($request->detain_return_record == null){
                        ReturnCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'local'=> $request->detain_return_local_charges,
                            'national'=> $request->detain_return_national_charges
                        ]);
                    }

                }
                //Return Charges
                if($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on'){
                    if($request->detain_fuel_record != null){
                        FuelSurcharge::where(['id'=>$request->detain_fuel_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'fuel_surcharge'=> $request->detain_fuel_surcharge
                        ]);
                    }elseif($request->detain_fuel_record == null){
                        FuelSurcharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'fuel_surcharge'=> $request->detain_fuel_surcharge
                        ]);
                    }

                }
                //Packaging Charges
                if($request->has('detain_packaging_switch') && $request->detain_packaging_switch == 'on'){
                    if($request->detain_packaging_record != null){
                        PackagingCharge::where(['id'=>$request->detain_packaging_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'sm_flyer'=> $request->detain_flyer_sm,
                            'md_flyer'=> $request->detain_flyer_md,
                            'lg_flyer'=> $request->detain_flyer_lg,
                            'box_flyer'=> $request->detain_flyer_box
                        ]);
                    }else{
                        PackagingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>3,
                            'sm_flyer'=> $request->detain_flyer_sm,
                            'md_flyer'=> $request->detain_flyer_md,
                            'lg_flyer'=> $request->detain_flyer_lg,
                            'box_flyer'=> $request->detain_flyer_box
                        ]);
                    }

                }
                $discount_cash = 0;
                $discount_weight = 0;
                $discount_insurance = 0;
                $discount_return = 0;
                $discount_packaging = 0;

                if($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on'){
                    $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : 0;
//                    $discount_weight = $request->detain_discount_weight_rate;
                }
                if($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on'){
                    $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : 0;
                }
                if($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : 0;
                }
                if($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on'){
                    $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : 0;
                }
                if($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : 0;
                }
                if($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                    $date_str = $request->detain_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();

                    if($request->detain_discount_record != null){
                        DiscountCharge::where(['id'=>$request->detain_discount_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title'=> $request->detain_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }elseif ($request->detain_discount_record == null){
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title'=> $request->detain_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }

                }

            }
            //dd($weightAlready);
        }

        //sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $ONRateAlready = RateStatus::where(['user_id'=>$id,'shipping_mode_id'=>4])->get();

            if(!$ONRateAlready->isEmpty()) {

                $wa_switch = array();
                $wa_spkg = array();
                WeightCharge::where(['user_id'=>$id,'shipping_mode_id'=>4])->whereNotIn('id', $request->sameday_weight_record)->delete();
                foreach ($request->sameday_weight_record as $index => $sameday_weight_record) {
                    if($request->has('sameday_wa_switch')) {
                        if (array_key_exists($index, $request->sameday_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('sameday_wa_spkg')) {
                        if (array_key_exists($index, $request->sameday_wa_spkg)) {
                            $wa_spkg[$index] = $request->sameday_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0;
                        };
                    }else{
                        $wa_spkg[$index] = 0;
                    }
                    if($request->sameday_weight_record[$index] != null){

                        $weight_row = WeightCharge::where('id',$request->sameday_weight_record[$index])
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_wa_range_up[$index],
                                'range_down' => $request->sameday_wa_range_down[$index],
                                'weight_addition' => $wa_switch[$index],
                                'spkg' => $wa_spkg[$index],
                                'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                                'national_or_sameday' => $request->sameday_wa_national_charges[$index]
                            ]);
                    }
                    if($request->sameday_weight_record[$index] == null){
                        WeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $request->sameday_wa_range_up[$index],
                            'range_down' => $request->sameday_wa_range_down[$index],
                            'weight_addition' => $wa_switch[$index],
                            'spkg' => $wa_spkg[$index],
                            'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                            'national_or_sameday' => $request->sameday_wa_national_charges[$index]
                        ]);
                    }


                }

                //Replacement and Try and Buy charges
                if($request->sameday_booking_record != null){
                    BookingTypeCharges::where(['id'=>$request->sameday_booking_record])->update([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'replacement_charges'=>$request->sameday_replacement_charges,
                        'try_and_buy_charges'=>$request->sameday_tnb_charges
                    ]);
                }else{
                    BookingTypeCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'replacement_charges'=>$request->sameday_replacement_charges,
                        'try_and_buy_charges'=>$request->sameday_tnb_charges
                    ]);
                }


                //Cash handling Charges
                if($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on'){
                    CashHandlingCharge::where(['user_id'=>$id,'shipping_mode_id'=>4])->whereNotIn('id', $request->sameday_cash_record)->delete();

                    foreach ($request->sameday_cash_record as $index => $sameday_cash_record){
                        if($request->sameday_cash_record[$index] != null){
                            CashHandlingCharge::where(['id'=>$request->sameday_cash_record[$index]])->update([
                                'user_id'=>$id,
                                'shipping_mode_id'=>4,
                                'range_up'=> $request->sameday_cash_range_up[$index],
                                'range_down'=> $request->sameday_cash_range_down[$index],
                                'charges'=> $request->sameday_cash_charges[$index]
                            ]);
                        }
                        if($request->sameday_cash_record[$index] == null){
                            CashHandlingCharge::create([
                                'user_id'=>$id,
                                'shipping_mode_id'=>4,
                                'range_up'=> $request->sameday_cash_range_up[$index],
                                'range_down'=> $request->sameday_cash_range_down[$index],
                                'charges'=> $request->sameday_cash_charges[$index]
                            ]);
                        }
                    }
                }
                //insurance charges
                if($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on'){
                    InsuranceCharge::where(['user_id'=>$id,'shipping_mode_id'=>4])->whereNotIn('id', $request->sameday_insurance_record)->delete();
                    foreach ($request->sameday_insurance_record as $insurance => $sameday_insurance_record){
                        if($request->sameday_insurance_record[$insurance] != null) {
                            InsuranceCharge::where(['id'=>$request->sameday_insurance_record[$insurance]])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_ins_range_up[$insurance],
                                'range_down' => $request->sameday_ins_range_down[$insurance],
                                'charges' => $request->sameday_ins_charges[$insurance]
                            ]);
                        }
                        if($request->sameday_insurance_record[$insurance] == null){
                            InsuranceCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_ins_range_up[$insurance],
                                'range_down' => $request->sameday_ins_range_down[$insurance],
                                'charges' => $request->sameday_ins_charges[$insurance]
                            ]);
                        }
                    }
                }
                //Return Charges
                if($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on'){
                    if($request->sameday_return_record != null){
                        ReturnCharge::where(['id'=>$request->sameday_return_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'local'=> $request->sameday_return_local_charges,
                            'national'=> $request->sameday_return_national_charges
                        ]);
                    }elseif ($request->sameday_return_record == null){
                        ReturnCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'local'=> $request->sameday_return_local_charges,
                            'national'=> $request->sameday_return_national_charges
                        ]);
                    }

                }
                //Return Charges
                if($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on'){
                    if($request->sameday_fuel_record != null){
                        FuelSurcharge::where(['id'=>$request->sameday_fuel_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'fuel_surcharge'=> $request->sameday_fuel_surcharge
                        ]);
                    }elseif($request->sameday_fuel_record == null){
                        FuelSurcharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'fuel_surcharge'=> $request->sameday_fuel_surcharge
                        ]);
                    }

                }
                //Packaging Charges
                if($request->has('sameday_packaging_switch') && $request->sameday_packaging_switch == 'on'){
                    if($request->sameday_packaging_record != null){
                        PackagingCharge::where(['id'=>$request->sameday_packaging_record])->update([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'sm_flyer'=> $request->sameday_flyer_sm,
                            'md_flyer'=> $request->sameday_flyer_md,
                            'lg_flyer'=> $request->sameday_flyer_lg,
                            'box_flyer'=> $request->sameday_flyer_box
                        ]);
                    }else{
                        PackagingCharge::create([
                            'user_id'=>$id,
                            'shipping_mode_id'=>4,
                            'sm_flyer'=> $request->sameday_flyer_sm,
                            'md_flyer'=> $request->sameday_flyer_md,
                            'lg_flyer'=> $request->sameday_flyer_lg,
                            'box_flyer'=> $request->sameday_flyer_box
                        ]);
                    }

                }
                $discount_cash = 0;
                $discount_weight = 0;
                $discount_insurance = 0;
                $discount_return = 0;
                $discount_packaging = 0;

                if($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on'){
                    $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : 0;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                }
                if($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on'){
                    $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : 0;
                }
                if($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on'){
                    $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : 0;
                }
                if($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on'){
                    $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : 0;
                }
                if($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on'){
                    $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : 0;
                }
                if($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0 || $discount_packaging != 0) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();

                    if($request->sameday_discount_record != null){
                        DiscountCharge::where(['id'=>$request->sameday_discount_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title'=> $request->sameday_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }elseif ($request->sameday_discount_record == null){
                        DiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title'=> $request->sameday_discount_title,
                            'weight' => $discount_weight,
                            'cash' => $discount_cash,
                            'insurance' => $discount_insurance,
                            'return' => $discount_return,
                            'packaging' => $discount_packaging,
                            'to' => $to,
                            'from' => $from,
                            'added_by'=>Auth::id()
                        ]);
                    }

                }

            }
            //dd($weightAlready);
        }
        if($request->authorize == 1){
            User::where('id',$id)->update(['status'=>2]);
            return redirect(route('admin.accounts.pending'))->with('success','User is now authorized.');
        }

                return redirect()->back()->with('success','All Rates are updated');
    }
    /**
     * @param Request $request
     * @param $id
     * @return int
     */
    public function addRates(Request $request, $id){
//    return $request;
        $messages = [
            'on_wa_range_up.*.required' => 'The overnight range up field is required.',
            'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
            'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 99.99.',
            'on_wa_range_down.*.required' => 'The overnight range down field is required.',
            'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
            'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 99.99.',
            'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
            'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
            'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
            'on_wa_national_charges.*.required' => 'The overnight national charges field is required.',
            'on_wa_national_charges.*.numeric' => 'The overnight national charges field must be numeric.',
            'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
            'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
            'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
            'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
            'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
            'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
            'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
            'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
            'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
            //'on_cash_charges.*.string' => 'The overnight cash charges field must be string.',
            'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
            'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
            'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
            'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
            'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
            //'on_ins_charges.*.string' => 'The overnight insurance charges field must be string.',
            'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
            'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
            'on_return_national_charges.required_if' => 'The overnight return national charges field is required.',
            'on_return_national_charges.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
            'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'on_flyer_sm.required_if' => 'The overnight small flyer field is required',
            'on_flyer_sm.numeric' => 'The overnight small flyer field must be numeric',
            'on_flyer_md.required_if' => 'The overnight meduim flyer field is required',
            'on_flyer_md.numeric' => 'The overnight medium flyer field must be numeric',
            'on_flyer_lg.required_if' => 'The overnight large flyer field is required',
            'on_flyer_lg.numeric' => 'The overnight large flyer field must be numeric',
            'on_flyer_box.required_if' => 'The overnight box flyer field is required',
            'on_flyer_box.numeric' => 'The overnight box flyer field must be numeric',
            'on_discount_title.required_if' => 'The overnight discount title field must be required',
            'on_daterange.required_if' => 'The overnight discount date range field must be required',
            'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
            'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
            'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
            'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
            'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
            'on_discount_title.required_with'=>'The overnight discount title field is required',
            'on_daterange.required_with'=>'The overnight discount date field is required',
            //overland starts
            'ol_wa_range_up.*.required' => 'The overland range up field is required.',
            'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
            'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 99.99.',
            'ol_wa_range_down.*.required' => 'The overland range down field is required.',
            'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
            'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 99.99.',
            'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
            'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
            'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
            'ol_wa_national_charges.*.required' => 'The overland national charges field is required.',
            'ol_wa_national_charges.*.numeric' => 'The overland national charges field must be numeric.',
            'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
            'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
            'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
            'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
            'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
            'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
            'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
            'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
            'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
            //'ol_cash_charges.*.numeric' => 'The overland cash charges field must be numeric or percentage.',
            'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
            'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
            'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
            'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
            'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
            //'ol_ins_charges.*.numeric' => 'The overland insurance charges field must be numeric or percentage.',
            'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
            'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
            'ol_return_national_charges.required_if' => 'The overland return national charges field is required.',
            'ol_return_national_charges.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
            'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'ol_flyer_sm.required_if' => 'The overland small flyer field is required',
            'ol_flyer_sm.numeric' => 'The overland small flyer field must be numeric',
            'ol_flyer_md.required_if' => 'The overland meduim flyer field is required',
            'ol_flyer_md.numeric' => 'The overland medium flyer field must be numeric',
            'ol_flyer_lg.required_if' => 'The overland large flyer field is required',
            'ol_flyer_lg.numeric' => 'The overland large flyer field must be numeric',
            'ol_flyer_box.required_if' => 'The overland box flyer field is required',
            'ol_flyer_box.numeric' => 'The overland box flyer field must be numeric',
            'ol_discount_title.required_if' => 'The overland discount title field must be required',
            'ol_daterange.required_if' => 'The overland discount date range field must be required',
            'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
            'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
            'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
            'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
            'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
            'ol_discount_title.required_with'=>'The overland discount title field is required',
            'ol_daterange.required_with'=>'The overland discount date field is required',
            //overland end and detain starts
            'detain_wa_range_up.*.required' => 'The detain range up field is required.',
            'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
            'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 99.99.',
            'detain_wa_range_down.*.required' => 'The detain range down field is required.',
            'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
            'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 99.99.',
            'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
            'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
            'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
            'detain_wa_national_charges.*.required' => 'The detain national charges field is required.',
            'detain_wa_national_charges.*.numeric' => 'The detain national charges field must be numeric.',
            'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
            'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
            'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
            'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
            'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
            'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
            'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
            'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
            'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
            //'detain_cash_charges.*.numeric' => 'The detain cash charges field must be numeric or percentage.',
            'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
            'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
            'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
            'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
            'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
            //'detain_ins_charges.*.numeric' => 'The detain insurance charges field must be numeric or percentage.',
            'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
            'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
            'detain_return_national_charges.required_if' => 'The detain return national charges field is required.',
            'detain_return_national_charges.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
            'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_flyer_sm.required_if' => 'The detain small flyer field is required',
            'detain_flyer_sm.numeric' => 'The detain small flyer field must be numeric',
            'detain_flyer_md.required_if' => 'The detain meduim flyer field is required',
            'detain_flyer_md.numeric' => 'The detain medium flyer field must be numeric',
            'detain_flyer_lg.required_if' => 'The detain large flyer field is required',
            'detain_flyer_lg.numeric' => 'The detain large flyer field must be numeric',
            'detain_flyer_box.required_if' => 'The detain box flyer field is required',
            'detain_flyer_box.numeric' => 'The detain box flyer field must be numeric',
            'detain_discount_title.required_if' => 'The detain discount title field must be required',
            'detain_daterange.required_if' => 'The detain discount date range field must be required',
            'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
            'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
            'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
            'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
            'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
            'detain_discount_title.required_with'=>'The detain discount title field is required',
            'detain_daterange.required_with'=>'The detain discount date field is required',
            //detain ends and sameday starts
            'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
            'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
            'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 99.99.',
            'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
            'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
            'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 99.99.',
            'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
            'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
            'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
            'sameday_wa_national_charges.*.required' => 'The sameday national charges field is required.',
            'sameday_wa_national_charges.*.numeric' => 'The sameday national charges field must be numeric.',
            'sameday_replacement_charges.numeric' => 'The sameday replacement charges field must be numeric.',
            'sameday_replacement_charges.required' => 'The sameday replacement charges field is required.',
            'sameday_tnb_charges.numeric' => 'The sameday try and buy charges field must be numeric.',
            'sameday_tnb_charges.required' => 'The sameday try and buy charges field is required.',
            'sameday_cash_range_up.*.required_if' => 'The sameday cash range up field is required.',
            'sameday_cash_range_up.*.numeric' => 'The sameday cash range up field must be numeric.',
            'sameday_cash_range_down.*.required_if' => 'The sameday cash range down field is required.',
            'sameday_cash_range_down.*.numeric' => 'The sameday cash range down field must be numeric.',
            'sameday_cash_charges.*.required_if' => 'The sameday cash charges field is required.',
           // 'sameday_cash_charges.*.numeric' => 'The sameday cash charges field must be numeric or percentage.',
            'sameday_ins_range_up.*.required_if' => 'The sameday insurance range up field is required.',
            'sameday_ins_range_up.*.numeric' => 'The sameday insurance range up field must be numeric or percentage.',
            'sameday_ins_range_down.*.required_if' => 'The sameday insurance range down field is required.',
            'sameday_ins_range_down.*.numeric' => 'The sameday insurance range down field must be numeric or percentage.',
            'sameday_ins_charges.*.required_if' => 'The sameday insurance charges field is required.',
            //'sameday_ins_charges.*.numeric' => 'The sameday insurance charges field must be numeric or percentage.',
            'sameday_return_local_charges.required_if' => 'The sameday return local charges field is required.',
            'sameday_return_local_charges.numeric' => 'The sameday return local charges field must be numeric or percentage.',
            'sameday_return_national_charges.required_if' => 'The sameday return national charges field is required.',
            'sameday_return_national_charges.numeric' => 'The sameday return national charges field must be numeric or percentage.',
            'sameday_fuel_surcharge.required_if' => 'The sameday return national charges field is required.',
            'sameday_fuel_surcharge.numeric' => 'The sameday return national charges field must be numeric or percentage.',
            'sameday_flyer_sm.required_if' => 'The sameday small flyer field is required',
            'sameday_flyer_sm.numeric' => 'The sameday small flyer field must be numeric',
            'sameday_flyer_md.required_if' => 'The sameday meduim flyer field is required',
            'sameday_flyer_md.numeric' => 'The sameday medium flyer field must be numeric',
            'sameday_flyer_lg.required_if' => 'The sameday large flyer field is required',
            'sameday_flyer_lg.numeric' => 'The sameday large flyer field must be numeric',
            'sameday_flyer_box.required_if' => 'The sameday box flyer field is required',
            'sameday_flyer_box.numeric' => 'The sameday box flyer field must be numeric',
            'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
            'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
            'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
            'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
            'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
            'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
            'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
            'sameday_discount_title.required_with'=>'The sameday discount title field is required',
            'sameday_daterange.required_with'=>'The sameday discount date field is required',
            //sameday ends
        ];

        $validations = array();
        $on_validations = array();
        $ol_validations = array();
        $detain_validations = array();
        $sameday_validations = array();

        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $on_validations = [
                'on_wa_range_up.*' => 'required|numeric|between:0,1000',
                'on_wa_range_down.*' => 'required|numeric|between:0,1000',
                'on_wa_local_charges.*' => 'required|numeric',
                'on_wa_national_charges.*' => 'required|numeric',
                'on_wa_spkg.*'=>'numeric',
                'on_replacement_charges'=>'required|numeric',
                'on_tnb_charges'=>'required|numeric',
                'on_cash_range_up.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*'=>'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*'=>'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_national_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'overnight_fuel_surcharge'=>'required_if:overnight_fuel_switch,==,on|numeric',
                'on_flyer_sm'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_md'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_lg'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_flyer_box'=>'required_if:on_packaging_switch,==,on|numeric',
                'on_discount_title'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_daterange'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_discount_weight_rate'=>'required_if:on_discount_weight_switch,==,on|numeric',
                'on_discount_cash_rate'=>'required_if:on_discount_cash_switch,==,on|numeric',
                'on_discount_insurance_rate'=>'required_if:on_discount_insurance_switch,==,on|numeric',
                'on_discount_return_rate'=>'required_if:on_discount_return_switch,==,on|numeric',
                'on_discount_packaging_rate'=>'required_if:on_discount_packaging_switch,==,on|numeric'
            ];
        }
        //overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $ol_validations = [
                'ol_wa_range_up.*' => 'required|numeric|between:0,1000',
                'ol_wa_range_down.*' => 'required|numeric|between:0,1000',
                'ol_wa_local_charges.*' => 'required|numeric',
                'ol_wa_national_charges.*' => 'required|numeric',
                'ol_wa_spkg.*'=>'numeric',
                'ol_replacement_charges'=>'required|numeric',
                'ol_tnb_charges'=>'required|numeric',
                'ol_cash_range_up.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*'=>'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*'=>'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_national_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'overland_fuel_surcharge'=>'required_if:overland_fuel_switch,==,on|numeric',
                'ol_flyer_sm'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_md'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_lg'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_flyer_box'=>'required_if:ol_packaging_switch,==,on|numeric',
                'ol_discount_title'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_daterange'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_discount_weight_rate'=>'required_if:ol_discount_weight_switch,==,on|numeric',
                'ol_discount_cash_rate'=>'required_if:ol_discount_cash_switch,==,on|numeric',
                'ol_discount_insurance_rate'=>'required_if:ol_discount_insurance_switch,==,on|numeric',
                'ol_discount_return_rate'=>'required_if:ol_discount_return_switch,==,on|numeric',
                'ol_discount_packaging_rate'=>'required_if:ol_discount_packaging_switch,==,on|numeric',
            ];
        }
             //overland
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $detain_validations = [
                'detain_wa_range_up.*' => 'required|numeric|between:0,1000',
                'detain_wa_range_down.*' => 'required|numeric|between:0,1000',
                'detain_wa_local_charges.*' => 'required|numeric',
                'detain_wa_national_charges.*' => 'required|numeric',
                'detain_wa_spkg.*'=>'numeric',
                'detain_replacement_charges'=>'required|numeric',
                'detain_tnb_charges'=>'required|numeric',
                'detain_cash_range_up.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*'=>'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*'=>'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_national_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_fuel_surcharge'=>'required_if:detain_fuel_switch,==,on|numeric',
                'detain_flyer_sm'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_md'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_lg'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_flyer_box'=>'required_if:detain_packaging_switch,==,on|numeric',
                'detain_discount_title'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_daterange'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_discount_weight_rate'=>'required_if:detain_discount_weight_switch,==,on|numeric',
                'detain_discount_cash_rate'=>'required_if:detain_discount_cash_switch,==,on|numeric',
                'detain_discount_insurance_rate'=>'required_if:detain_discount_insurance_switch,==,on|numeric',
                'detain_discount_return_rate'=>'required_if:detain_discount_return_switch,==,on|numeric',
                'detain_discount_packaging_rate'=>'required_if:detain_discount_packaging_switch,==,on|numeric',
            ];
        }
            //sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $sameday_validations = [
                'sameday_wa_range_up.*' => 'required|numeric|between:0,1000',
                'sameday_wa_range_down.*' => 'required|numeric|between:0,1000',
                'sameday_wa_local_charges.*' => 'required|numeric',
                'sameday_wa_national_charges.*' => 'required|numeric',
                'sameday_wa_spkg.*'=>'numeric',
                'sameday_replacement_charges'=>'required|numeric',
                'sameday_tnb_charges'=>'required|numeric',
                'sameday_cash_range_up.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*'=>'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*'=>'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_national_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge'=>'required_if:sameday_fuel_switch,==,on|numeric',
                'sameday_flyer_sm'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_md'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_lg'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_flyer_box'=>'required_if:sameday_packaging_switch,==,on|numeric',
                'sameday_discount_title'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate'=>'required_if:sameday_discount_weight_switch,==,on|numeric',
                'sameday_discount_cash_rate'=>'required_if:sameday_discount_cash_switch,==,on|numeric',
                'sameday_discount_insurance_rate'=>'required_if:sameday_discount_insurance_switch,==,on|numeric',
                'sameday_discount_return_rate'=>'required_if:sameday_discount_return_switch,==,on|numeric',
                'sameday_discount_packaging_rate'=>'required_if:sameday_discount_packaging_switch,==,on|numeric',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }

        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
        $ONRateAlready = RateStatus::where('user_id',$id)->where('shipping_mode_id',1)->get();

            if($ONRateAlready->isEmpty()) {
                RateStatus::create([
                   'user_id'=>$id,
                   'shipping_mode_id'=>1,
                   'status'=> ($request->has('on_main_switch'))? 1:0,
                   'cash_handling_charges'=> ($request->has('on_cash_handling_switch'))? 1:0,
                   'insurance_charges'=> ($request->has('on_insurance_charges_switch'))? 1:0,
                   'return_charges'=> ($request->has('on_return_switch'))? 1:0,
//                   'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                   'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                   'packaging_charges'=> ($request->has('on_packaging_switch'))? 1:0
                ]);
                $wa_switch = array();
                $wa_spkg = array();
                foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                    if($request->has('on_wa_switch')) {
                        if (array_key_exists($index, $request->on_wa_switch)) {
                            $wa_switch[$index] = 1;
                        } else {
                            $wa_switch[$index] = 0;
                        };
                    }else{
                        $wa_switch[$index] = 0;
                    }
                    if($request->has('on_wa_spkg')) {
                        if (array_key_exists($index, $request->on_wa_spkg)) {
                            $wa_spkg[$index] = $request->on_wa_spkg[$index];
                        } else {
                            $wa_spkg[$index] = 0;
                        };
                    }else{
                        $wa_spkg[$index] = 0;
                    }
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
                //Return Charges
                if($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'fuel_surcharge'=> $request->overnight_fuel_surcharge
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
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();
//                    $to = str_replace('/', '-', $date_sep[0]);
//                    $from = str_replace('/', '-', $date_sep[1]);
//                    $nto = date_create($to);
//                    $to =date_format($nto,"Y-m-d H:i:s");
//                    $nfrom = date_create($from);
//                    $from =date_format($nfrom,"Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'title'=> $request->on_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            //dd($weightAlready);
        }
        //Overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){

            $OLRatePresent = RateStatus::where('user_id',$id)->where('shipping_mode_id',2)->get();

            if($OLRatePresent->isEmpty()) {
                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'status'=> ($request->has('ol_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('ol_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('ol_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('ol_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overland_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('ol_packaging_switch'))? 1:0
                ]);
                $wa_switch_overland = array();
                $wa_spkg_overland = array();
                foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                    if($request->has('ol_wa_switch')) {
                        if (array_key_exists($index, $request->ol_wa_switch)) {
                            $wa_switch_overland[$index] = 1;
                        } else {
                            $wa_switch_overland[$index] = 0;
                        };
                    }else{
                        $wa_switch_overland[$index] = 0;
                    }
                    if($request->has('ol_wa_spkg')) {
                        if (array_key_exists($index, $request->ol_wa_spkg)) {
                            $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                        } else {
                            $wa_spkg_overland[$index] = 0;
                        };
                    }else{
                        $wa_spkg_overland[$index] = 0;
                    }
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
                if($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'fuel_surcharge'=> $request->overland_fuel_surcharge
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
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();
//                    $to = str_replace('/', '-', $date_sep[0]);
//                    $from = str_replace('/', '-', $date_sep[1]);
//                    $nto = date_create($to);
//                    $to =date_format($nto,"Y-m-d H:i:s");
//                    $nfrom = date_create($from);
//                    $from =date_format($nfrom,"Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'title'=> $request->ol_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            
        }
        //Detain
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

            $DetainRatePresent = RateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

            if ($DetainRatePresent->isEmpty()) {
                RateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges'=> ($request->has('detain_fuel_switch'))? 1:0,
                    'packaging_charges' => ($request->has('detain_packaging_switch')) ? 1 : 0
                ]);
                $wa_switch_detain = array();
                $wa_spkg_detain = array();
                foreach ($request->detain_wa_range_up as $index => $detain_wa_range_up) {
                    if ($request->has('detain_wa_switch')) {
                        if (array_key_exists($index, $request->detain_wa_switch)) {
                            $wa_switch_detain[$index] = 1;
                        } else {
                            $wa_switch_detain[$index] = 0;
                        };
                    } else {
                        $wa_switch_detain[$index] = 0;
                    }
                    if ($request->has('detain_wa_spkg')) {
                        if (array_key_exists($index, $request->detain_wa_spkg)) {
                            $wa_spkg_detain[$index] = $request->detain_wa_spkg[$index];
                        } else {
                            $wa_spkg_detain[$index] = 0;
                        };
                    } else {
                        $wa_spkg_detain[$index] = 0;
                    }
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
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'replacement_charges' => $request->detain_replacement_charges,
                    'try_and_buy_charges' => $request->detain_tnb_charges
                ]);
                //Cash handling Charges
                if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                    foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                        CashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_cash_range_up[$ind],
                            'range_down' => $request->detain_cash_range_down[$ind],
                            'charges' => $request->detain_cash_charges[$ind]
                        ]);
                    }
                }
                //insurance charges
                if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                    foreach ($request->detain_ins_range_up as $insurance => $detain_ins_range_up) {
                        InsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_ins_range_up[$insurance],
                            'range_down' => $request->detain_ins_range_down[$insurance],
                            'charges' => $request->detain_ins_charges[$insurance]
                        ]);
                    }
                }
                //Return Charges
                if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                    ReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'local' => $request->detain_return_local_charges,
                        'national' => $request->detain_return_national_charges
                    ]);
                }
                if($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'fuel_surcharge'=> $request->detain_fuel_surcharge
                    ]);
                }
                //Packaging Charges
                if ($request->has('detain_packaging_switch') && $request->detain_packaging_switch == 'on') {
                    PackagingCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'sm_flyer' => $request->detain_flyer_sm,
                        'md_flyer' => $request->detain_flyer_md,
                        'lg_flyer' => $request->detain_flyer_lg,
                        'box_flyer' => $request->detain_flyer_box
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;
                $discount_packaging = null;

                if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                    $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : null;
//                    $discount_weight = $request->detain_discount_weight_rate;
                }
                if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                    $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : null;
                }
                if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                    $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                    $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : null;
                }
                if ($request->has('detain_discount_packaging_switch') && $request->detain_discount_packaging_switch == 'on') {
                    $discount_packaging = $request->detain_discount_packaging_rate != null ? $request->detain_discount_packaging_rate : null;
                }
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                    $date_str = $request->detain_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();
//                    $to = str_replace('/', '-', $date_sep[0]);
//                    $from = str_replace('/', '-', $date_sep[1]);
//                    $nto = date_create($to);
//                    $to = date_format($nto, "Y-m-d H:i:s");
//                    $nfrom = date_create($from);
//                    $from = date_format($nfrom, "Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'title' => $request->detain_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

            }

        }
        //Sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){

            $SamedayRatePresent = RateStatus::where('user_id',$id)->where('shipping_mode_id',4)->get();
            if($SamedayRatePresent->isEmpty()) {
                RateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'status'=> ($request->has('sameday_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('sameday_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('sameday_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('sameday_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('sameday_fuel_switch'))? 1:0,
                    'packaging_charges'=> ($request->has('sameday_packaging_switch'))? 1:0
                ]);
                $wa_switch_sameday = array();
                $wa_spkg_sameday = array();
                foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                    if($request->has('sameday_wa_switch')) {
                        if (array_key_exists($index, $request->sameday_wa_switch)) {
                            $wa_switch_sameday[$index] = 1;
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        };
                    }else{
                        $wa_switch_sameday[$index] = 0;
                    }
                    if($request->has('sameday_wa_spkg')) {
                        if (array_key_exists($index, $request->sameday_wa_spkg)) {
                            $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                        } else {
                            $wa_spkg_sameday[$index] = 0;
                        };
                    }else{
                        $wa_spkg_sameday[$index] = 0;
                    }
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
                if($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on'){
                    FuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'fuel_surcharge'=> $request->sameday_fuel_surcharge
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
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'Asia/Karachi')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'Asia/Karachi')->toDateTimeString();
//                    $to = str_replace('/', '-', $date_sep[0]);
//                    $from = str_replace('/', '-', $date_sep[1]);
//                    $nto = date_create($to);
//                    $to =date_format($nto,"Y-m-d H:i:s");
//                    $nfrom = date_create($from);
//                    $from =date_format($nfrom,"Y-m-d H:i:s");

                    DiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'title'=> $request->sameday_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'packaging' => $discount_packaging,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
            }
            User::where('id',$id)->update(['status'=>1]);

        return redirect(route('admin.accounts.pending'))->with('success','All Rates are added');
    }
    public function activeAccountListAjax(){
       $users = User::join('city_infos', 'users.city_code', '=', 'city_infos.city_code')
            ->select(['users.id', 'users.name', 'city_infos.city_name' ,'users.poc','users.phone','users.address', 'users.email'])->where('status',3)->where('blacklist',0);

        return Datatables::of($users)->addColumn("action", function ($result) {
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
    //->join('rate_statuses','users.id','=','rate_statuses.user_id')

    public function pendingAccountListAjax(){
        $users = User::join('city_infos', 'users.city_code', '=', 'city_infos.city_code')
            ->select(['users.id', 'users.name', 'city_infos.city_name' ,'users.poc','users.phone','users.address','users.status', 'users.email','users.created_at'])->whereIn('status',[0,1,2])->where('blacklist',0);
         //$isRate = RateStatus::where('user_id',$users->id);

        return Datatables::of($users)
            ->editColumn('created_at', function ($users) {
                return $users->created_at ? with(new Carbon($users->created_at))->format('d/m/Y H:i:s A') : '';
            })
            ->editColumn('status', function ($users) {
                return $users->status == 0? 'Request Received': ($users->status == 1? 'Rates Added' : ($users->status == 2? 'Pending for Activation':''));
            })
            ->addColumn("action", function ($result) {
                                            $dropdown = "
                                                <span class='dropdown'>
                                                    <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                            aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                                    <div class='dropdown-menu open-left arrow'>
                                                      <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> View Bank Info</a>
                                                      <a href='#' class='dropdown-item' data-target-id='{$result->id}' data-toggle='modal' data-target='#ShippingInfoModal'><i class='ft-plus-circle primary'></i> View Shipping Info</a>";
                                                    if($result->status == 2){
                                                        $dropdown .= "<a href='#' class='dropdown-item' data-target-id='{$result->id}' rel='active' data-toggle='modal' data-target='#ConfirmModal'><i class='ft-plus-circle primary'></i> Activate Account</a>";

                                                    }
                                            if (RateStatus::where('user_id', $result->id)->exists()) {
                                                $dropdown .= "
                                                        <a href='".route('admin.edit.rates',['id'=> $result->id])."' class='dropdown-item'><i class='ft-plus-circle primary'></i> Edit Rates</a>
                                                ";
                                            }
                                            else {
                                                $dropdown .= "
                                                        <a href='".route('admin.add.rates',['id'=> $result->id])."' class='dropdown-item'><i class='ft-plus-circle primary'></i> Add Rates</a>
                                                ";
                                            }

                                            $dropdown .= "
                                                    </div>
                                                </span>";

                                            return $dropdown;
                                        })
                                      ->make(true);

    }
    public function blockAccountListAjax(){
        $users = User::join('city_infos', 'users.city_code', '=', 'city_infos.city_code')
            ->select(['users.id', 'users.name', 'city_infos.city_name' ,'users.poc','users.phone','users.address', 'users.email'])->where('blacklist',1);

        return Datatables::of($users)->addColumn("action", function ($result) {
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

    public function cityView(){
//        $hubs = City::where('hub',1)->get();
//        return $hubs[0]->id;
        return view('admin.management.city_management');
    }
    public function cityListAjax(){
        $cities = City::join('cities as h' ,'cities.hub_id', '=' , 'h.id')
        ->select(['cities.id','cities.name' ,'h.name as hub','cities.hub_id','cities.status']);
        return Datatables::of($cities)
        ->editColumn('status', function ($cities) {
            return $cities->status == 0? 'Inactive': 'Active';
        })
        ->addColumn("action", function ($result) {
            $dropdown = "<span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}' rel='editcity' data-toggle='modal' data-target='#editCity'><i class='ft-plus-circle primary'></i> Update City Status</a>";
                                              if($result->status == 1) {
                                                  $dropdown .= "<a href='#' class='dropdown-item' data-target-id='{$result->id}' rel='cityInactive' data-toggle='modal' data-target='#ConfirmModalCity'><i class='ft-plus-circle primary'></i> Deactivate City</a>";
                                              }else {
                                                  $dropdown .= " <a href='#' class='dropdown-item' data-target-id='{$result->id}' rel='cityactive' data-toggle='modal' data-target='#ConfirmModalCity'><i class='ft-plus-circle primary'></i> Activate City</a>";
                                              }
                                            $dropdown .="</div></span>";
                                              return $dropdown;
        })
            ->make(true);
    }
    public function getCityForm(){
        $hubs = City::where('hub',1)->get();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::all();
        return view('admin.management.add_city_form')->with(['hubs'=>$hubs,'shippingMode'=>$shippingMode,'booking'=>$booking]);
    }
    public function getEditCityForm(Request $request,$id){
//        return $id;
        $city = City::find($id);
        if($city->hub == 1){
            $cityhub = City::find($city->hub_id);
            $isHub = 1;
        }else{
            $isHub = 0;
            $cityhub = '';
        }
        $delivery = CityDelivery::where('city_id',$city->id)->groupBy('booking_type_id')->get();
        $hubs = City::where('hub',1)->get();
        $shippingMode = ShippingMode::all();
        $booking = BookingType::all();
        return view('admin.management.edit_city_form')->with(['hubs'=>$hubs,'shippingMode'=>$shippingMode,'booking'=>$booking,'isHub'=>$isHub,'city'=>$city,'delivery'=>$delivery,'cityhub'=>$cityhub]);

    }
    public function addCityHub(Request $request){

        if($request->postType == 'city'){

            $city = City::create([
                'name'=>$request->cityName,
                'hub'=>0,
                'hub_id'=>$request->hubs,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1
            ]);

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$city->id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }

            return redirect()->back()->with('success','city added successfully');
        }elseif($request->postType == 'hub'){
            $city = City::create([
                'name'=>$request->cityName,
                'hub'=>1,
                'pickup'=>($request->has('pickup'))? 1:0,
                'status'=>1
            ]);
            City::where('id',$city->id)->update(['hub_id'=>$city->id]);

            foreach ($request->delivery as $booking_type_id => $shipping_modes) {
                foreach ($shipping_modes as $shipping_mode_id => $shipping_mode_value) {
                    CityDelivery::create([
                        'city_id'=>$city->id,
                        'booking_type_id'=>$booking_type_id,
                        'shipping_mode_id'=>$shipping_mode_id,
                    ]);
                }
            }
            return redirect()->back()->with('success','Hub city added successfully');
        }
    }

    public function CityStatus(Request $request){
        $id = $request->cid; //city id
        $status = $request->status;
        if($status == 'cityInactive'){
            $city = City::find($id);
            if($city->status == 1 && $city->hub == 1){
                $citylist = City::where(['hub_id'=>$city->id,''])->get();
                if(count($citylist) > 1){

                    return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
                }elseif (count($citylist) == 1){
                    $action = City::where('id',$city->id)->update(['status'=>0]);
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }
            }elseif ($city->status == 1){
                $action = City::where('id',$city->id)->update(['status'=>0]);
                if($action == 1){
                    return redirect()->route('admin.management.city')->with('success', 'City is inactive now.');
                }else{
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }

            }
        }elseif($status == 'cityactive'){
            $city = City::find($id);
            if($city->status == 0){
                $action = City::where('id',$city->id)->update(['status'=>1]);
                if($action == 1){
                    return redirect()->route('admin.management.city')->with('success', 'City is active now.');
                }else{
                    return redirect()->route('admin.management.city')->with('danger', 'There is some problem please try again.');
                }
            }else{
                return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');

            }
        }
        return redirect()->route('admin.management.city')->with('danger', 'This city is already inactive.');
    }
}
