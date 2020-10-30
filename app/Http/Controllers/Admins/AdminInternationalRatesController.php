<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\City;
use App\Http\Models\InternationalRatesCashHandlingCharges;
use App\Http\Models\InternationalRatesDiscountCharges;
use App\Http\Models\InternationalRatesHub;
use App\Http\Models\InternationalRatesInsuranceCharges;
use App\Http\Models\InternationalRatesRemark;
use App\Http\Models\InternationalRatesReturnCharges;
use App\Http\Models\InternationalRatesStatus;
use App\Http\Models\InternationalRatesWeightCharges;
use App\Http\Models\InternationalUsersInformation;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
class AdminInternationalRatesController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function add_rates_index($id){
        if($id){
            $user = User::find($id);
            if($user){
                $cities = City::where('hub', 1)->where('business_category_id', 2)->select('id', 'name')->get();

                return view('admin.international.rates.add_rates')->with(['cities' => $cities, 'shipper' => $user]);
            }
            return redirect()->back()->with('error', 'No User Found!');
        }
        return redirect()->back()->with('error', 'No data found!');
    }

    public function add_rates_submit(Request $request){

        $shipper_id = $request->shipper_id;
        $box_ids = $request->box_ids;
        if(count($box_ids) == 0){
            return redirect()->back()->with('error', 'Rates not submitted properly!');
        }
        $international_user_info = InternationalUsersInformation::where('user_id', $shipper_id);
        if($international_user_info->exists()){
            return redirect()->back()->with('error', 'Rates already present');
        }

        $intl_user_information = new InternationalUsersInformation();
        $intl_user_information->user_id = $shipper_id;
        $intl_user_information->status = 1;
        $intl_user_information->save();

        foreach ($box_ids as $box_id){
            $cash_key = 'cash_handling_switch_'.$box_id;
            $insurance_key = 'insurance_charges_switch_'.$box_id;
            $return_key = 'return_charges_switch_'.$box_id;

            $cash_handling_present = FALSE;
            $insurance_present = FALSE;
            $return_present = FALSE;
            if($request->has($cash_key)){
                $cash_handling_present = TRUE;
            }
            if($request->has($insurance_key)){
                $insurance_present = TRUE;
            }
            if($request->has($return_key)){
                $return_present = TRUE;
            }


            $rate_status = new InternationalRatesStatus();
            $rate_status->user_id = $shipper_id;
            $rate_status->box_id = $box_id;
            $rate_status->cash_handling_charges = ($cash_handling_present)? 1:0;
            $rate_status->insurance_charges = ($insurance_present)? 1:0;
            $rate_status->return_charges = ($return_present)? 1:0;
            $rate_status->save();

            $wa_switch = array();
            $wa_spkg = array();
            foreach ($request->range_up[$box_id] as $index => $range_up){

                $wa_sw = "wa_switch.$box_id.$index";
                $wa_sp = "spkg.$box_id.$index";

                if($request->has($wa_sw)) {
                    if (array_key_exists($index, $request->wa_switch[$box_id])) {
                        $wa_switch[$index] = 1;
                    } else {
                        $wa_switch[$index] = 0;
                    };
                }else{
                    $wa_switch[$index] = 0;
                }
                if($request->has($wa_sp)) {
                    if (array_key_exists($index, $request->spkg[$box_id])) {
                        $wa_spkg[$index] = $request->spkg[$box_id][$index];
                    } else {
                        $wa_spkg[$index] = 0;
                    };
                }else{
                    $wa_spkg[$index] = 0;
                }
                $weight_charges = new InternationalRatesWeightCharges();
                $weight_charges->user_id = $shipper_id;
                $weight_charges->box_id = $box_id;
                $weight_charges->range_up = $range_up;
                $weight_charges->range_down = $request->range_down[$box_id][$index];
                $weight_charges->weight_addition = $wa_switch[$index];
                $weight_charges->spkg = $wa_spkg[$index];
                $weight_charges->local_charges = $request->local_charges[$box_id][$index];
                $weight_charges->save();
            }

            foreach ($request->hubs[$box_id] as $hub_id){
                $intl_hubs = new InternationalRatesHub();
                $intl_hubs->user_id = $shipper_id;
                $intl_hubs->box_id = $box_id;
                $intl_hubs->hub_id = $hub_id;
                $intl_hubs->save();
            }
            if($cash_handling_present){
                foreach ($request->cash_range_up[$box_id] as $cash_index => $cash_handling){
                    $intl_cash_handling = new InternationalRatesCashHandlingCharges();
                    $intl_cash_handling->user_id = $shipper_id;
                    $intl_cash_handling->box_id = $box_id;
                    $intl_cash_handling->range_up = $cash_handling;
                    $intl_cash_handling->range_down = $request->cash_range_down[$box_id][$index];
                    $intl_cash_handling->charges = $request->cash_charges[$box_id][$index];
                    $intl_cash_handling->save();
                }
            }

            if($insurance_present){
                foreach ($request->ins_range_up[$box_id] as $ins_index => $insurance){
                    $intl_ins = new InternationalRatesInsuranceCharges();
                    $intl_ins->user_id = $shipper_id;
                    $intl_ins->box_id = $box_id;
                    $intl_ins->range_up = $insurance;
                    $intl_ins->range_down = $request->ins_range_down[$box_id][$index];
                    $intl_ins->charges = $request->ins_charges[$box_id][$index];
                    $intl_ins->save();
                }
            }

            if($return_present){
                $return_charges = $request->input('return_local_charges_'.$box_id);
                $intl_return = new InternationalRatesReturnCharges();
                $intl_return->user_id = $shipper_id;
                $intl_return->box_id = $box_id;
                $intl_return->local = $return_charges;
                $intl_return->save();
            }

            $discount_weight = NULL;
            $discount_cash = null;
            $discount_insurance = null;
            $discount_return = null;
            $discount_weight_key = 'discount_weight_switch_'.$box_id;
            $discount_cash_key = 'discount_cash_switch_'.$box_id;
            $discount_insurance_key = 'discount_insurance_switch_'.$box_id;
            $discount_return_key = 'discount_return_switch_'.$box_id;
            if($request->has($discount_weight_key)){
                $discount_weight = $request->input('discount_weight_'.$box_id);
            }
            if($request->has($discount_cash_key)){
                $discount_cash = $request->input('discount_cash_'.$box_id);
            }
            if($request->has($discount_insurance_key)){
                $discount_insurence = $request->input('discount_insurance_'.$box_id);
            }
            if($request->has($discount_return_key)){
                $discount_return = $request->input('discount_return_'.$box_id);
            }
            if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {

                $title = $request->input('discount_title_'.$box_id);
                $date_str = $request->input('daterange_'.$box_id);
                $date_sep = explode(' - ', $date_str);
                $date_to = explode('/', $date_sep[0]);
                $date_from = explode('/', $date_sep[1]);
                $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();

                $intl_discount = new InternationalRatesDiscountCharges();
                $intl_discount->user_id = $shipper_id;
                $intl_discount->box_id = $box_id;
                $intl_discount->weight = $discount_weight;
                $intl_discount->cash = $discount_cash;
                $intl_discount->insurance = $discount_insurance;
                $intl_discount->return = $discount_return;
                $intl_discount->to = $to;
                $intl_discount->from = $from;
                $intl_discount->title = $title;
                $intl_discount->save();
            }
        }
        if($request->has('rate_remarks') && $request->rate_remarks != null){
            $rate_remark = new InternationalRatesRemark();
            $rate_remark->user_id = $shipper_id;
            $rate_remark->remarks = $request->rate_remarks;
            $rate_remark->admin_id = Auth::id();
            $rate_remark->save();
        }
        return redirect()->back()->with('success', 'Rates added successfully!');

    }

    public function view_rates_index($id){
        $shipper_id = $id;
        if($shipper_id){
            $intl_user = InternationalUsersInformation::where('user_id', $shipper_id);
            if($intl_user->exists()){
                $user = User::find($shipper_id);
                $intl_rate_status = InternationalRatesStatus::where('user_id', $shipper_id)->get();
                $intl_hubs = InternationalRatesHub::all()->where('user_id', $shipper_id)->groupBy('box_id');
                $weight_charges = InternationalRatesWeightCharges::all()->where('user_id', $shipper_id)->groupBy('box_id');
                $cash_charges = InternationalRatesCashHandlingCharges::all()->where('user_id', $shipper_id)->groupBy('box_id');
                $insurance_charges = InternationalRatesInsuranceCharges::all()->where('user_id', $shipper_id)->groupBy('box_id');
                $return_charges = InternationalRatesReturnCharges::all()->where('user_id', $shipper_id)->groupBy('box_id');
                $discount_charges = InternationalRatesDiscountCharges::all()->where('user_id', $shipper_id)->groupBy('box_id');
                $rate_remarks = InternationalRatesRemark::where('user_id', $shipper_id)->orderBy('created_at','desc')->get();
                $cities = City::where('business_category_id', 2)->select('id', 'name')->get();

                return view('admin.international.rates.view_rates')->with(['cities' => $cities, 'shipper' => $user, 'rate_statuses' => $intl_rate_status, 'hubs' => $intl_hubs, 'weight_charges' => $weight_charges, 'cash_handling_charges' => $cash_charges, 'insurance_charges' => $insurance_charges, 'return_charges' => $return_charges, 'discount_charges' => $discount_charges,'rate_remarks' => $rate_remarks]);
            }
            return redirect()->back()->with('error', 'No User Found!');
        }
        return redirect()->back()->with('error', 'No data found!');
    }
}
