<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\City;
use App\http\Models\International\HistoryInternationalRatesCashHandlingCharges;
use App\http\Models\International\HistoryInternationalRatesDiscountCharges;
use App\http\Models\International\HistoryInternationalRatesHub;
use App\http\Models\International\HistoryInternationalRatesInsuranceCharges;
use App\http\Models\International\HistoryInternationalRatesReturnCharges;
use App\http\Models\International\HistoryInternationalRatesStatus;
use App\http\Models\International\HistoryInternationalRatesWeightCharges;
use App\http\Models\International\PendingInternationalRatesCashHandlingCharges;
use App\http\Models\International\PendingInternationalRatesDiscountCharges;
use App\http\Models\International\PendingInternationalRatesHub;
use App\http\Models\International\PendingInternationalRatesInsuranceCharges;
use App\http\Models\International\PendingInternationalRatesReturnCharges;
use App\http\Models\International\PendingInternationalRatesStatus;
use App\http\Models\International\PendingInternationalRatesWeightCharges;
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
                if(InternationalUsersInformation::where('user_id', $user->id)->exists()){
                    return redirect()->back()->with('error', 'Rates already added!');
                }
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
        $intl_user_information->status = 4;
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
            $rate_status->admin_id = Auth::id();
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
                    $intl_cash_handling->range_down = $request->cash_range_down[$box_id][$cash_index];
                    $intl_cash_handling->charges = $request->cash_charges[$box_id][$cash_index];
                    $intl_cash_handling->save();
                }
            }

            if($insurance_present){
                foreach ($request->ins_range_up[$box_id] as $ins_index => $insurance){
                    $intl_ins = new InternationalRatesInsuranceCharges();
                    $intl_ins->user_id = $shipper_id;
                    $intl_ins->box_id = $box_id;
                    $intl_ins->range_up = $insurance;
                    $intl_ins->range_down = $request->ins_range_down[$box_id][$ins_index];
                    $intl_ins->charges = $request->ins_charges[$box_id][$ins_index];
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
                $discount_insurance = $request->input('discount_insurance_'.$box_id);
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
        $user = User::find($shipper_id);
        if($user->status == 3){
            return redirect()->route('admin.accounts.active')->with('success', 'Rates added successfully!');
        }
        else{
            User::where('id',$shipper_id)->update(['status' => 1,'rates_added_by'=>Auth::id()]);
            return redirect()->route('admin.accounts.pending')->with('success', 'Rates added successfully!');
        }

    }
	 public function edit_rates_index($id){
        if($id){
            $user = User::find($id);
            if($user){
                $cities = City::where('business_category_id', 2)->where('hub', 1)->select('id', 'name')->get();
                $user_information = InternationalUsersInformation::where('user_id', $user->id)->first();
                if($user_information->status == 1 || $user_information->status == 4 || $user_information->status == 5){
                    $rate_statuses = InternationalRatesStatus::where('user_id', $user->id)->get();
                    $weight_charges = InternationalRatesWeightCharges::where('user_id', $user->id)->get();
                    $rates_hubs = InternationalRatesHub::where('user_id', $user->id)->get();
                    $cash_handling_charges = InternationalRatesCashHandlingCharges::where('user_id', $user->id)->get();
                    $insurance_charges = InternationalRatesInsuranceCharges::where('user_id', $user->id)->get();
                    $return_charges = InternationalRatesReturnCharges::where('user_id', $user->id)->get();
                    $all_discount_charges = InternationalRatesDiscountCharges::where('user_id', $user->id)->get();
                    $discount_charges = array();
                    foreach ($all_discount_charges as $dis_charges){
                        $discount_charges[$dis_charges->box_id]['title'] = $dis_charges->title;
                        $discount_charges[$dis_charges->box_id]['date'] = date('m/d/Y', strtotime($dis_charges->to)).' - '.date('m/d/Y', strtotime($dis_charges->from));
                        $discount_charges[$dis_charges->box_id]['weight'] = $dis_charges->weight;
                        $discount_charges[$dis_charges->box_id]['cash'] = $dis_charges->cash;
                        $discount_charges[$dis_charges->box_id]['insurance'] = $dis_charges->insurance;
                        $discount_charges[$dis_charges->box_id]['return'] = $dis_charges->return;
                    }
                }
                else{
                    $rate_statuses = PendingInternationalRatesStatus::where('user_id', $user->id)->get();
                    $weight_charges = PendingInternationalRatesWeightCharges::where('user_id', $user->id)->get();
                    $rates_hubs = PendingInternationalRatesHub::where('user_id', $user->id)->get();
                    $cash_handling_charges = PendingInternationalRatesCashHandlingCharges::where('user_id', $user->id)->get();
                    $insurance_charges = PendingInternationalRatesInsuranceCharges::where('user_id', $user->id)->get();
                    $return_charges = PendingInternationalRatesReturnCharges::where('user_id', $user->id)->get();
                    $all_discount_charges = PendingInternationalRatesDiscountCharges::where('user_id', $user->id)->get();
                    $discount_charges = array();
                    foreach ($all_discount_charges as $dis_charges){
                        $discount_charges[$dis_charges->box_id]['title'] = $dis_charges->title;
                        $discount_charges[$dis_charges->box_id]['date'] = date('m/d/Y', strtotime($dis_charges->to)).' - '.date('m/d/Y', strtotime($dis_charges->from));
                        $discount_charges[$dis_charges->box_id]['weight'] = $dis_charges->weight;
                        $discount_charges[$dis_charges->box_id]['cash'] = $dis_charges->cash;
                        $discount_charges[$dis_charges->box_id]['insurance'] = $dis_charges->insurance;
                        $discount_charges[$dis_charges->box_id]['return'] = $dis_charges->return;
                    }
                }
                dd($rate_statuses);
                return view('admin.international.rates.edit_rates')->with(['cities' => $cities, 'shipper' => $user, 'user_information' => $user_information, 'rate_statuses' => $rate_statuses, 'weight_charges' => $weight_charges, 'rates_hubs' => $rates_hubs, 'cash_handling_charges' => $cash_handling_charges, 'insurance_charges' => $insurance_charges, 'return_charges' => $return_charges, 'discount_charges' => $discount_charges]);
            }
            return redirect()->back()->with('error', 'No User Found!');
        }
        return redirect()->back()->with('error', 'No data found!');
    }

    public function edit_rates_submit(Request $request){
        $shipper_id = $request->shipper_id;
        $user = User::find($shipper_id);
        $box_ids = $request->box_ids;
        if(count($box_ids) == 0){
            return redirect()->back()->with('error', 'Rates not submitted properly!');
        }
        if($request->authorize == 1){
            $intl_user_information = InternationalUsersInformation::where('user_id', $shipper_id)->first();
            $intl_user_information->status = 1;
            $intl_user_information->save();

            if($user->status != 3){
                $user->status = 2;
                $user->save();
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates approved successfully!');
            }
            return redirect()->route('admin.accounts.active')->with('success', 'Rates approved successfully!');


        }
        elseif ($request->approve == 1) {
            $intl_user_information = InternationalUsersInformation::where('user_id', $shipper_id)->first();
            $intl_user_information->status = 1;
            $intl_user_information->save();

            if($previous_rate_statuses = InternationalRatesStatus::where('user_id', $shipper_id)->get()){
                foreach ($previous_rate_statuses as $p_rate_status){
                    $history_rate_status = new HistoryInternationalRatesStatus();
                    $history_rate_status->user_id = $p_rate_status->user_id;
                    $history_rate_status->box_id = $p_rate_status->box_id;
                    $history_rate_status->cash_handling_charges = $p_rate_status->cash_handling_charges;
                    $history_rate_status->insurance_charges = $p_rate_status->insurance_charges;
                    $history_rate_status->return_charges = $p_rate_status->return_charges;
                    $history_rate_status->admin_id = $p_rate_status->admin_id;
                    $history_rate_status->save();

                }
            }

            if($previous_weight_charges = InternationalRatesWeightCharges::where('user_id', $shipper_id)->get()){
                foreach ($previous_weight_charges as $p_weight_charge){
                    $history_weight_charges = new HistoryInternationalRatesWeightCharges();
                    $history_weight_charges->user_id = $p_weight_charge->user_id;
                    $history_weight_charges->box_id = $p_weight_charge->box_id;
                    $history_weight_charges->range_up = $p_weight_charge->range_up;
                    $history_weight_charges->range_down = $p_weight_charge->range_down;
                    $history_weight_charges->weight_addition = $p_weight_charge->weight_addition;
                    $history_weight_charges->spkg = $p_weight_charge->spkg;
                    $history_weight_charges->local_charges = $p_weight_charge->local_charges;
                    $history_weight_charges->save();
                }
            }
            if($previous_rate_hubs = InternationalRatesHub::where('user_id', $shipper_id)->get()){
                foreach ($previous_rate_hubs as $p_rate_hub){
                    $history_intl_hubs = new HistoryInternationalRatesHub();
                    $history_intl_hubs->user_id = $p_rate_hub->user_id;
                    $history_intl_hubs->box_id = $p_rate_hub->box_id;
                    $history_intl_hubs->hub_id = $p_rate_hub->hub_id;
                    $history_intl_hubs->save();
                }
            }
            if($previous_cash_handling_charges = InternationalRatesCashHandlingCharges::where('user_id', $shipper_id)->get()){
                foreach ($previous_cash_handling_charges as $p_cash_handling_charge){
                    $history_intl_cash_handling = new HistoryInternationalRatesCashHandlingCharges();
                    $history_intl_cash_handling->user_id = $p_cash_handling_charge->user_id;
                    $history_intl_cash_handling->box_id = $p_cash_handling_charge->box_id;
                    $history_intl_cash_handling->range_up = $p_cash_handling_charge->range_up;
                    $history_intl_cash_handling->range_down = $p_cash_handling_charge->range_down;
                    $history_intl_cash_handling->charges = $p_cash_handling_charge->charges;
                    $history_intl_cash_handling->save();
                }
            }
            if($previous_insurance_charges = InternationalRatesInsuranceCharges::where('user_id', $shipper_id)->get()){
                foreach ($previous_insurance_charges as $p_insurance_charges){
                    $history_intl_ins = new HistoryInternationalRatesInsuranceCharges();
                    $history_intl_ins->user_id = $p_insurance_charges->user_id;
                    $history_intl_ins->box_id = $p_insurance_charges->box_id;
                    $history_intl_ins->range_up = $p_insurance_charges->range_up;
                    $history_intl_ins->range_down = $p_insurance_charges->range_down;
                    $history_intl_ins->charges = $p_insurance_charges->charges;
                    $history_intl_ins->save();
                }
            }
            if($previous_return_charges = InternationalRatesReturnCharges::where('user_id', $shipper_id)->get()){
                foreach ($previous_return_charges as $p_return_charges){
                    $history_intl_return = new HistoryInternationalRatesReturnCharges();
                    $history_intl_return->user_id = $p_return_charges->user_id;
                    $history_intl_return->box_id = $p_return_charges->box_id;
                    $history_intl_return->local = $p_return_charges->local;
                    $history_intl_return->save();
                }
            }
            if($previous_discount_charges = InternationalRatesDiscountCharges::where('user_id', $shipper_id)->get()){
                foreach ($previous_discount_charges as $p_discount_charges){
                    $history_intl_discount = new HistoryInternationalRatesDiscountCharges();
                    $history_intl_discount->user_id = $p_discount_charges->user_id;
                    $history_intl_discount->box_id = $p_discount_charges->box_id;
                    $history_intl_discount->weight = $p_discount_charges->weight;
                    $history_intl_discount->cash = $p_discount_charges->cash;
                    $history_intl_discount->insurance = $p_discount_charges->insurance;
                    $history_intl_discount->return = $p_discount_charges->return;
                    $history_intl_discount->to = $p_discount_charges->to;
                    $history_intl_discount->from = $p_discount_charges->from;
                    $history_intl_discount->title = $p_discount_charges->title;
                    $history_intl_discount->save();
                }
            }
            InternationalRatesStatus::where('user_id', $shipper_id)->delete();
            InternationalRatesWeightCharges::where('user_id', $shipper_id)->delete();
            InternationalRatesHub::where('user_id', $shipper_id)->delete();
            InternationalRatesCashHandlingCharges::where('user_id', $shipper_id)->delete();
            InternationalRatesInsuranceCharges::where('user_id', $shipper_id)->delete();
            InternationalRatesReturnCharges::where('user_id', $shipper_id)->delete();
            InternationalRatesDiscountCharges::where('user_id', $shipper_id)->delete();

            if($pending_rate_statuses = PendingInternationalRatesStatus::where('user_id', $shipper_id)->get()){
                foreach ($pending_rate_statuses as $rate_status){
                    $intl_rate_status = new InternationalRatesStatus();
                    $intl_rate_status->user_id = $rate_status->user_id;
                    $intl_rate_status->box_id = $rate_status->box_id;
                    $intl_rate_status->cash_handling_charges = $rate_status->cash_handling_charges;
                    $intl_rate_status->insurance_charges = $rate_status->insurance_charges;
                    $intl_rate_status->return_charges = $rate_status->return_charges;
                    $intl_rate_status->admin_id = $rate_status->admin_id;
                    $intl_rate_status->save();
                }
            }

            if($pending_weight_charges = PendingInternationalRatesWeightCharges::where('user_id', $shipper_id)->get()){
                foreach ($pending_weight_charges as $weight_charge){
                    $intl_weight_charge = new InternationalRatesWeightCharges();
                    $intl_weight_charge->user_id = $weight_charge->user_id;
                    $intl_weight_charge->box_id = $weight_charge->box_id;
                    $intl_weight_charge->range_up = $weight_charge->range_up;
                    $intl_weight_charge->range_down = $weight_charge->range_down;
                    $intl_weight_charge->weight_addition = $weight_charge->weight_addition;
                    $intl_weight_charge->spkg = $weight_charge->spkg;
                    $intl_weight_charge->local_charges = $weight_charge->local_charges;
                    $intl_weight_charge->save();
                }
            }
            if($pending_rate_hubs = PendingInternationalRatesHub::where('user_id', $shipper_id)->get()){
                foreach ($pending_rate_hubs as $rate_hub){
                    $intl_hubs = new InternationalRatesHub();
                    $intl_hubs->user_id = $rate_hub->user_id;
                    $intl_hubs->box_id = $rate_hub->box_id;
                    $intl_hubs->hub_id = $rate_hub->hub_id;
                    $intl_hubs->save();
                }
            }
            if($pending_cash_handling_charges = PendingInternationalRatesCashHandlingCharges::where('user_id', $shipper_id)->get()){
                foreach ($pending_cash_handling_charges as $cash_handling_charge){
                    $intl_cash_handling = new InternationalRatesCashHandlingCharges();
                    $intl_cash_handling->user_id = $cash_handling_charge->user_id;
                    $intl_cash_handling->box_id = $cash_handling_charge->box_id;
                    $intl_cash_handling->range_up = $cash_handling_charge->range_up;
                    $intl_cash_handling->range_down = $cash_handling_charge->range_down;
                    $intl_cash_handling->charges = $cash_handling_charge->charges;
                    $intl_cash_handling->save();
                }
            }
            if($pending_insurance_charges = PendingInternationalRatesInsuranceCharges::where('user_id', $shipper_id)->get()){
                foreach ($pending_insurance_charges as $insurance_charges){
                    $intl_ins = new InternationalRatesInsuranceCharges();
                    $intl_ins->user_id = $insurance_charges->user_id;
                    $intl_ins->box_id = $insurance_charges->box_id;
                    $intl_ins->range_up = $insurance_charges->range_up;
                    $intl_ins->range_down = $insurance_charges->range_down;
                    $intl_ins->charges = $insurance_charges->charges;
                    $intl_ins->save();
                }
            }
            if($pending_return_charges = PendingInternationalRatesReturnCharges::where('user_id', $shipper_id)->get()){
                foreach ($pending_return_charges as $return_charges){
                    $intl_return = new InternationalRatesReturnCharges();
                    $intl_return->user_id = $return_charges->user_id;
                    $intl_return->box_id = $return_charges->box_id;
                    $intl_return->local = $return_charges->local;
                    $intl_return->save();
                }
            }
            if($pending_discount_charges = PendingInternationalRatesDiscountCharges::where('user_id', $shipper_id)->get()){
                foreach ($pending_discount_charges as $discount_charges){
                    $intl_discount = new InternationalRatesDiscountCharges();
                    $intl_discount->user_id = $discount_charges->user_id;
                    $intl_discount->box_id = $discount_charges->box_id;
                    $intl_discount->weight = $discount_charges->weight;
                    $intl_discount->cash = $discount_charges->cash;
                    $intl_discount->insurance = $discount_charges->insurance;
                    $intl_discount->return = $discount_charges->return;
                    $intl_discount->to = $discount_charges->to;
                    $intl_discount->from = $discount_charges->from;
                    $intl_discount->title = $discount_charges->title;
                    $intl_discount->save();
                }
            }
            PendingInternationalRatesStatus::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesWeightCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesHub::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesCashHandlingCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesInsuranceCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesReturnCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesDiscountCharges::where('user_id', $shipper_id)->delete();

            if($request->has('rate_remarks') && $request->rate_remarks != null){
                $rate_remark = new InternationalRatesRemark();
                $rate_remark->user_id = $shipper_id;
                $rate_remark->remarks = $request->rate_remarks;
                $rate_remark->admin_id = Auth::id();
                $rate_remark->save();
            }
            if($user->status == 3){
                return redirect()->route('admin.accounts.active')->with('success', 'Rates approved successfully!');
            }
            else {
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates approved successfully!');
            }
        }
        else{
            $intl_user_information = InternationalUsersInformation::where('user_id', $shipper_id)->first();
            $intl_user_information->status = 2;
            $intl_user_information->save();

            PendingInternationalRatesStatus::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesWeightCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesHub::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesCashHandlingCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesInsuranceCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesReturnCharges::where('user_id', $shipper_id)->delete();
            PendingInternationalRatesDiscountCharges::where('user_id', $shipper_id)->delete();

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


                $rate_status = new PendingInternationalRatesStatus();
                $rate_status->user_id = $shipper_id;
                $rate_status->box_id = $box_id;
                $rate_status->cash_handling_charges = ($cash_handling_present)? 1:0;
                $rate_status->insurance_charges = ($insurance_present)? 1:0;
                $rate_status->return_charges = ($return_present)? 1:0;
                $rate_status->admin_id = Auth::id();
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
                    $weight_charges = new PendingInternationalRatesWeightCharges();
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
                    $intl_hubs = new PendingInternationalRatesHub();
                    $intl_hubs->user_id = $shipper_id;
                    $intl_hubs->box_id = $box_id;
                    $intl_hubs->hub_id = $hub_id;
                    $intl_hubs->save();
                }
                if($cash_handling_present){
                    foreach ($request->cash_range_up[$box_id] as $cash_index => $cash_handling){
                        $intl_cash_handling = new PendingInternationalRatesCashHandlingCharges();
                        $intl_cash_handling->user_id = $shipper_id;
                        $intl_cash_handling->box_id = $box_id;
                        $intl_cash_handling->range_up = $cash_handling;
                        $intl_cash_handling->range_down = $request->cash_range_down[$box_id][$cash_index];
                        $intl_cash_handling->charges = $request->cash_charges[$box_id][$cash_index];
                        $intl_cash_handling->save();
                    }
                }

                if($insurance_present){
                    foreach ($request->ins_range_up[$box_id] as $ins_index => $insurance){
                        $intl_ins = new PendingInternationalRatesInsuranceCharges();
                        $intl_ins->user_id = $shipper_id;
                        $intl_ins->box_id = $box_id;
                        $intl_ins->range_up = $insurance;
                        $intl_ins->range_down = $request->ins_range_down[$box_id][$ins_index];
                        $intl_ins->charges = $request->ins_charges[$box_id][$ins_index];
                        $intl_ins->save();
                    }
                }

                if($return_present){
                    $return_charges = $request->input('return_local_charges_'.$box_id);
                    $intl_return = new PendingInternationalRatesReturnCharges();
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
                $discount_return_key = 'discount_return_switch_'.$box_id;
                $discount_insurance_key = 'discount_insurance_switch_'.$box_id;
                if($request->has($discount_weight_key)){
                    $discount_weight = $request->input('discount_weight_'.$box_id);
                }
                if($request->has($discount_cash_key)){
                    $discount_cash = $request->input('discount_cash_'.$box_id);
                }
                if($request->has($discount_return_key)){
                    $discount_return = $request->input('discount_return_'.$box_id);
                }
                if($request->has($discount_insurance_key)){
                    $discount_insurance = $request->input('discount_insurance_'.$box_id);
                }
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {

                    $title = $request->input('discount_title_'.$box_id);
                    $date_str = $request->input('daterange_'.$box_id);
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();

                    $intl_discount = new PendingInternationalRatesDiscountCharges();
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

            return redirect()->back()->with('success', 'Rates updated successfully!');
        }

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
    public function rejectReasonSubmit(Request $request)
    {
        $shipper_id = $request->shipper_id;
        $reject_reason = $request->rejected_reason;
        $user_information = InternationalUsersInformation::where('user_id',$shipper_id)->first();
        if($request->authorization == 1){
            $user_information->status = 5;
        }
        else{
            $user_information->status = 3;
        }
        $user_information->rejected_reason = $reject_reason;
        $user_information->save();
        return ['success' => 'Rates has been rejected!'];
    }}
