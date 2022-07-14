<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\InternationalShipmentExtraServiceCharges;
use App\Http\Models\City;
use App\Http\Models\HistoryInternationalUserRate;
use App\Http\Models\International\HistoryInternationalRatesCashHandlingCharges;
use App\Http\Models\International\HistoryInternationalRatesDiscountCharges;
use App\Http\Models\International\HistoryInternationalRatesHub;
use App\Http\Models\International\HistoryInternationalRatesInsuranceCharges;
use App\Http\Models\International\HistoryInternationalRatesReturnCharges;
use App\Http\Models\International\HistoryInternationalRatesStatus;
use App\Http\Models\International\HistoryInternationalRatesWeightCharges;
use App\Http\Models\International\PendingInternationalRatesCashHandlingCharges;
use App\Http\Models\International\PendingInternationalRatesDiscountCharges;
use App\Http\Models\International\PendingInternationalRatesHub;
use App\Http\Models\International\PendingInternationalRatesInsuranceCharges;
use App\Http\Models\International\PendingInternationalRatesReturnCharges;
use App\Http\Models\International\PendingInternationalRatesStatus;
use App\Http\Models\International\PendingInternationalRatesWeightCharges;
use App\Http\Models\InternationalRatesCashHandlingCharges;
use App\Http\Models\InternationalRatesDiscountCharges;
use App\Http\Models\InternationalRatesHub;
use App\Http\Models\InternationalRatesInsuranceCharges;
use App\Http\Models\InternationalRatesRemark;
use App\Http\Models\InternationalRatesReturnCharges;
use App\Http\Models\InternationalRatesStatus;
use App\Http\Models\InternationalRatesWeightCharges;
use App\Http\Models\InternationalShipment;
use App\Http\Models\InternationalStandardDhlRate;
use App\Http\Models\InternationalUserRate;
use App\Http\Models\InternationalUsersCreditLimit;
use App\Http\Models\InternationalUsersInformation;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\PendingInternationalUserRate;
use App\Http\Models\Rates\InternationalEconomyRate;
use App\Http\Models\Rates\InternationalEconomyRateHistory;
use App\Http\Models\Rates\InternationalEconomyRateStatus;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;

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
            if($user->status != 3){
                $user->status = 2;
                $user->save();
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates approved successfully!');
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
                $shipper = User::find($shipper_id);
                if(!$shipper){
                    return redirect()->back()->with('error', 'No User Found!');
                }
                $user_information = InternationalUsersInformation::where('user_id', $shipper_id);
                if($user_information->exists()){
                    $user_information = $user_information->first();
                }
                else{
                    $user_information = NULL;
                }
                $fuel_charges = 0;
                $fuel_surcharge = GlobalSettings::where('type', 'international_fuel_surcharge');
                if($fuel_surcharge->exists()){
                    $fuel_surcharge = $fuel_surcharge->first();
                    $fuel_charges = (float)$fuel_surcharge->text;
                }
                else{
                    return redirect()->back()->with(['error' => 'Rate settings not set!']);
                }
                $exchange_rate_charges = 0;
                $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                if($exchange_rate->exists()){
                    $exchange_rate = $exchange_rate->first();
                    $exchange_rate_charges = (float)$exchange_rate->text;;
                }
                else{
                    return redirect()->back()->with(['error' => 'Rate settings not set!']);
                }
                $gst = 0;
                $gst_rate = GlobalSettings::where('type', 'international_gst_rate');
                if($gst_rate->exists()){
                    $gst_rate = $gst_rate->first();
                    $gst = $gst = (float)$gst_rate->text;
                }
                else{
                    return redirect()->back()->with(['error' => 'Rate settings not set!']);
                }
                $margin = 0;
                if($user_information != NULL){
                    if($user_information->status == 1 || $user_information->status == 4 || $user_information->status == 5){
                        $international_user_rate = InternationalUserRate::where('user_id', $id);
                        if($international_user_rate->exists()){
                            $international_user_rate = $international_user_rate->first();
                            $margin = $international_user_rate->margin;
                        }
                    }
                    else{
                        $international_user_rate = PendingInternationalUserRate::where('user_id', $id);
                        if($international_user_rate->exists()){
                            $international_user_rate = $international_user_rate->first();
                            $margin = $international_user_rate->margin;
                        }
                    }
                }

                return view('admin.international.rates_view')->with(['shipper' => $shipper, 'exchange_charges' => $exchange_rate_charges, 'fuel_surcharge' => $fuel_charges, 'margin' => $margin, 'gst' => $gst, 'user_information' => $user_information]);

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
    }

    public function update_rates_index($id){
        if($id){
            $user = User::find($id);
            if($user){
                $user_information = NULL;
                $intl_user_information = InternationalUsersInformation::where('user_id', $user->id);
                if($intl_user_information->exists()){
                    $user_information = $intl_user_information->first();
                }

                $fuel_charges = 0;
                $fuel_surcharge = GlobalSettings::where('type', 'international_fuel_surcharge');
                if($fuel_surcharge->exists()){
                    $fuel_surcharge = $fuel_surcharge->first();
                    $fuel_charges = (float)$fuel_surcharge->text;
                }
                else{
                    return redirect()->back()->with(['error' => 'Rate settings not set!']);
                }
                $exchange_rate_charges = 0;
                $exchange_rate = GlobalSettings::where('type', 'international_exchange_rate');
                if($exchange_rate->exists()){
                    $exchange_rate = $exchange_rate->first();
                    $exchange_rate_charges = (float)$exchange_rate->text;;
                }
                else{
                    return redirect()->back()->with(['error' => 'Rate settings not set!']);
                }
                $gst = 0;
                $gst_rate = GlobalSettings::where('type', 'international_gst_rate');
                if($gst_rate->exists()){
                    $gst_rate = $gst_rate->first();
                    $gst = $gst = (float)$gst_rate->text;
                }
                else{
                    return redirect()->back()->with(['error' => 'Rate settings not set!']);
                }
                $margin = array('margin_1' => 0,'margin_2' => 0,'margin_3' => 0,'margin_4' => 0,'margin_5' => 0,'margin_6' => 0,'margin_7' => 0,'margin_8' => 0,'margin_9' => 0,'margin_10' => 0,'margin_11' => 0);
                
                if($user_information){
                    if($user_information->status == 1 || $user_information->status == 4 || $user_information->status == 5){
                        $international_user_rate = InternationalUserRate::where('user_id', $id);
                        if($international_user_rate->exists()){
                            $international_user_rate = $international_user_rate->first();
                            $margin['margin_1'] = $international_user_rate->margin_1;
                            $margin['margin_2'] = $international_user_rate->margin_2;
                            $margin['margin_3'] = $international_user_rate->margin_3;
                            $margin['margin_4'] = $international_user_rate->margin_4;
                            $margin['margin_5'] = $international_user_rate->margin_5;
                            $margin['margin_6'] = $international_user_rate->margin_6;
                            $margin['margin_7'] = $international_user_rate->margin_7;
                            $margin['margin_8'] = $international_user_rate->margin_8;
                            $margin['margin_9'] = $international_user_rate->margin_9;
                            $margin['margin_10'] = $international_user_rate->margin_10;
                            $margin['margin_11'] = $international_user_rate->margin_11;
                        }
                    }
                    else{
                        $international_user_rate = PendingInternationalUserRate::where('user_id', $id);
                        if($international_user_rate->exists()){
                            $international_user_rate = $international_user_rate->first();
                            $margin['margin_1'] = $international_user_rate->margin_1;
                            $margin['margin_2'] = $international_user_rate->margin_2;
                            $margin['margin_3'] = $international_user_rate->margin_3;
                            $margin['margin_4'] = $international_user_rate->margin_4;
                            $margin['margin_5'] = $international_user_rate->margin_5;
                            $margin['margin_6'] = $international_user_rate->margin_6;
                            $margin['margin_7'] = $international_user_rate->margin_7;
                            $margin['margin_8'] = $international_user_rate->margin_8;
                            $margin['margin_9'] = $international_user_rate->margin_9;
                            $margin['margin_10'] = $international_user_rate->margin_10;
                            $margin['margin_11'] = $international_user_rate->margin_11;
                        }
                    }
                }

                return view('admin.international.rates_update')->with(['shipper' => $user, 'exchange_charges' => $exchange_rate_charges, 'fuel_surcharge' => $fuel_charges, 'margin' => $margin, 'gst' => $gst, 'user_information' => $user_information]);
            }
            return redirect()->back()->with('error', 'No User Found!');
        }
        return redirect()->back()->with('error', 'No data found!');

    }

    public function standard_rates_list(Request $request, $id){

        $margin_1 = 0;
        $margin_2 = 0;
        $margin_3 = 0;
        $margin_4 = 0;
        $margin_5 = 0;
        $margin_6 = 0;
        $margin_7 = 0;
        $margin_8 = 0;
        $margin_9 = 0;
        $margin_10 = 0;
        $margin_11 = 0;

        $intl_user_information = InternationalUsersInformation::where('user_id', $id);
        if($intl_user_information->exists()){
            $user_information = $intl_user_information->first();
            if($user_information->status == 1 || $user_information->status == 4 || $user_information->status == 5){
                $international_user_rate = InternationalUserRate::where('user_id', $id);
                if($international_user_rate->exists()){
                    $international_user_rate = $international_user_rate->first();
                    $margin_1 = $international_user_rate->margin_1;
                    $margin_2 = $international_user_rate->margin_2;
                    $margin_3 = $international_user_rate->margin_3;
                    $margin_4 = $international_user_rate->margin_4;
                    $margin_5 = $international_user_rate->margin_5;
                    $margin_6 = $international_user_rate->margin_6;
                    $margin_7 = $international_user_rate->margin_7;
                    $margin_8 = $international_user_rate->margin_8;
                    $margin_9 = $international_user_rate->margin_9;
                    $margin_10 = $international_user_rate->margin_10;
                    $margin_11 = $international_user_rate->margin_11;
                }
            }
            else{
                $international_user_rate = PendingInternationalUserRate::where('user_id', $id);
                if($international_user_rate->exists()){
                    $international_user_rate = $international_user_rate->first();
                    $margin_1 = $international_user_rate->margin_1;
                    $margin_2 = $international_user_rate->margin_2;
                    $margin_3 = $international_user_rate->margin_3;
                    $margin_4 = $international_user_rate->margin_4;
                    $margin_5 = $international_user_rate->margin_5;
                    $margin_6 = $international_user_rate->margin_6;
                    $margin_7 = $international_user_rate->margin_7;
                    $margin_8 = $international_user_rate->margin_8;
                    $margin_9 = $international_user_rate->margin_9;
                    $margin_10 = $international_user_rate->margin_10;
                    $margin_11 = $international_user_rate->margin_11;
                }
            }
        }

        $rates_list = InternationalStandardDhlRate::select('id','range_up', 'range_down', 'zone_1', 'zone_2', 'zone_3', 'zone_4', 'zone_5', 'zone_6', 'zone_7', 'zone_8', 'zone_9', 'zone_10', 'zone_11');

        return Datatables::of($rates_list)
            ->editColumn('zone_1', function ($rate) use ($margin_1){
                if($margin_1 > 0){
                    return round($zone = ((100 + $margin_1) / 100) * $rate->zone_1, 2);
                }
                else{
                    return $rate->zone_1;
                }
            })
            ->editColumn('zone_2', function ($rate) use ($margin_2){
                if($margin_2 > 0){
                    return round($zone = ((100 + $margin_2) / 100) * $rate->zone_2, 2);
                }
                else{
                    return $rate->zone_2;
                }
            })
            ->editColumn('zone_3', function ($rate) use ($margin_3){
                if($margin_3 > 0){
                    return round($zone = ((100 + $margin_3) / 100) * $rate->zone_3, 2);
                }
                else{
                    return $rate->zone_3;
                }
            })
            ->editColumn('zone_4', function ($rate) use ($margin_4){
                if($margin_4 > 0){
                    return round($zone = ((100 + $margin_4) / 100) * $rate->zone_4, 2);
                }
                else{
                    return $rate->zone_4;
                }
            })
            ->editColumn('zone_5', function ($rate) use ($margin_5){
                if($margin_5 > 0){
                    return round($zone = ((100 + $margin_5 ) / 100) * $rate->zone_5, 2);
                }
                else{
                    return $rate->zone_5;
                }
            })
            ->editColumn('zone_6', function ($rate) use ($margin_6){
                if($margin_6 > 0){
                    return round($zone = ((100 + $margin_6) / 100) * $rate->zone_6, 2);
                }
                else{
                    return $rate->zone_6;
                }
            })
            ->editColumn('zone_7', function ($rate) use ($margin_7){
                if($margin_7 > 0){
                    return round($zone = ((100 + $margin_7) / 100) * $rate->zone_7, 2);
                }
                else{
                    return $rate->zone_7;
                }
            })
            ->editColumn('zone_8', function ($rate) use ($margin_8){
                if($margin_8 > 0){
                    return round($zone = ((100 + $margin_8) / 100) * $rate->zone_8, 2);
                }
                else{
                    return $rate->zone_8;
                }
            })
            ->editColumn('zone_9', function ($rate) use ($margin_9){
                if($margin_9 > 0){
                    return round($zone = ((100 + $margin_9) / 100) * $rate->zone_9, 2);
                }
                else{
                    return $rate->zone_9;
                }
            })
            ->editColumn('zone_10', function ($rate) use ($margin_10){
                if($margin_10 > 0){
                    return round($zone = ((100 + $margin_10) / 100) * $rate->zone_10, 2);
                }
                else{
                    return $rate->zone_10;
                }
            })
            ->editColumn('zone_11', function ($rate) use ($margin_11){
                if($margin_11 > 0){
                    return round($zone = ((100 + $margin_11) / 100) * $rate->zone_11, 2);
                }
                else{
                    return $rate->zone_11;
                }
            })

            ->make(true);
    }

    public function update_rates_submit(Request $request){

        $shipper_id = $request->shipper_id;
        if(!$shipper_id){
            return redirect()->back()->with('error', 'Shipper not found!');
        }

        $shipper = User::find($shipper_id);
        if(!$shipper){
            return redirect()->back()->with('error', 'Shipper not found!');
        }

        if($request->authorize == 1){
            $intl_user_information = InternationalUsersInformation::where('user_id', $shipper_id)->first();
            $intl_user_information->status = 1;
            $intl_user_information->save();

            if($shipper->status != 3){
                $shipper->status = 2;
                $shipper->save();
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates approved successfully!');
            }
            return redirect()->route('admin.accounts.active')->with('success', 'Rates approved successfully!');

        }
        else if ($request->approve == 1) {
            $intl_user_information = InternationalUsersInformation::where('user_id', $shipper_id)->first();
            $intl_user_information->status = 1;
            $intl_user_information->save();

            if($previous_rate_status = InternationalUserRate::where('user_id', $shipper_id)->first()){
                $history_international_user_rate = new HistoryInternationalUserRate();
                $history_international_user_rate->user_id = $previous_rate_status->user_id;
                $history_international_user_rate->margin_1 = $previous_rate_status->margin_1;
                $history_international_user_rate->margin_2 = $previous_rate_status->margin_2;
                $history_international_user_rate->margin_3 = $previous_rate_status->margin_3;
                $history_international_user_rate->margin_4 = $previous_rate_status->margin_4;
                $history_international_user_rate->margin_5 = $previous_rate_status->margin_5;
                $history_international_user_rate->margin_6 = $previous_rate_status->margin_6;
                $history_international_user_rate->margin_7 = $previous_rate_status->margin_7;
                $history_international_user_rate->margin_8 = $previous_rate_status->margin_8;
                $history_international_user_rate->margin_9 = $previous_rate_status->margin_9;
                $history_international_user_rate->margin_10 = $previous_rate_status->margin_10;
                $history_international_user_rate->margin_11 = $previous_rate_status->margin_11;

                $history_international_user_rate->updated_by = $previous_rate_status->updated_by;
                $history_international_user_rate->rates_updated_at = $previous_rate_status->rates_updated_at;
                $history_international_user_rate->save();
            }


            InternationalUserRate::where('user_id', $shipper_id)->delete();


            if($pending_rate_statuses = PendingInternationalUserRate::where('user_id', $shipper_id)->first()){
                    $international_user_rates = new InternationalUserRate();
                    $international_user_rates->user_id = $pending_rate_statuses->user_id;
                    $international_user_rates->margin_1 = $request->margin_1;
                    $international_user_rates->margin_2 = $request->margin_2;
                    $international_user_rates->margin_3 = $request->margin_3;
                    $international_user_rates->margin_4 = $request->margin_4;
                    $international_user_rates->margin_5 = $request->margin_5;
                    $international_user_rates->margin_6 = $request->margin_6;
                    $international_user_rates->margin_7 = $request->margin_7;
                    $international_user_rates->margin_8 = $request->margin_8;
                    $international_user_rates->margin_9 = $request->margin_9;
                    $international_user_rates->margin_10 = $request->margin_10;
                    $international_user_rates->margin_11 = $request->margin_11;
                    $international_user_rates->updated_by = $pending_rate_statuses->updated_by;
                    $international_user_rates->rates_updated_at = $pending_rate_statuses->rates_updated_at;
                    $international_user_rates->save();
            }

            PendingInternationalUserRate::where('user_id', $shipper_id)->delete();

            if($shipper->status != 3){
                $shipper->status = 2;
                $shipper->save();
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates approved successfully!');
            }
            if($shipper->status == 3){
                return redirect()->route('admin.accounts.active')->with('success', 'Rates approved successfully!');
            }
            else {
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates approved successfully!');
            }
        }
        else{
            $new_rate_flag = false;
            $international_user_info = InternationalUsersInformation::where('user_id', $shipper_id);
            if(!$international_user_info->exists()){
                $international_user_info = new InternationalUsersInformation();
                $international_user_info->user_id = $shipper_id;
                $international_user_info->status = 4;
                $new_rate_flag = true;
            }
            else{
                $international_user_info = $international_user_info->first();
                $international_user_info->status = 2;
            }
            $international_user_info->save();
            if($new_rate_flag == false){
                PendingInternationalUserRate::where('user_id', $shipper_id)->delete();

                $international_user_rates = new PendingInternationalUserRate();
                $international_user_rates->user_id = $shipper_id;
                $international_user_rates->margin_1 = $request->margin_1;
                $international_user_rates->margin_2 = $request->margin_2;
                $international_user_rates->margin_3 = $request->margin_3;
                $international_user_rates->margin_4 = $request->margin_4;
                $international_user_rates->margin_5 = $request->margin_5;
                $international_user_rates->margin_6 = $request->margin_6;
                $international_user_rates->margin_7 = $request->margin_7;
                $international_user_rates->margin_8 = $request->margin_8;
                $international_user_rates->margin_9 = $request->margin_9;
                $international_user_rates->margin_10 = $request->margin_10;
                $international_user_rates->margin_11 = $request->margin_11;
                $international_user_rates->updated_by = Auth::id();
                $international_user_rates->rates_updated_at = Carbon::now();
                $international_user_rates->save();
            }
            else{
                $international_user_rates = InternationalUserRate::where('user_id', $shipper_id);
                if($international_user_rates->exists()){
                    $international_user_rates = $international_user_rates->first();
                    $international_user_rates->margin_1 = $request->margin_1;
                    $international_user_rates->margin_2 = $request->margin_2;
                    $international_user_rates->margin_3 = $request->margin_3;
                    $international_user_rates->margin_4 = $request->margin_4;
                    $international_user_rates->margin_5 = $request->margin_5;
                    $international_user_rates->margin_6 = $request->margin_6;
                    $international_user_rates->margin_7 = $request->margin_7;
                    $international_user_rates->margin_8 = $request->margin_8;
                    $international_user_rates->margin_9 = $request->margin_9;
                    $international_user_rates->margin_10 = $request->margin_10;
                    $international_user_rates->margin_11 = $request->margin_11;
                    $international_user_rates->updated_by = Auth::id();
                    $international_user_rates->rates_updated_at = Carbon::now();

                }
                else{
                    $international_user_rates = new InternationalUserRate();
                    $international_user_rates->user_id = $shipper_id;
                    $international_user_rates->margin_1 = $request->margin_1;
                    $international_user_rates->margin_2 = $request->margin_2;
                    $international_user_rates->margin_3 = $request->margin_3;
                    $international_user_rates->margin_4 = $request->margin_4;
                    $international_user_rates->margin_5 = $request->margin_5;
                    $international_user_rates->margin_6 = $request->margin_6;
                    $international_user_rates->margin_7 = $request->margin_7;
                    $international_user_rates->margin_8 = $request->margin_8;
                    $international_user_rates->margin_9 = $request->margin_9;
                    $international_user_rates->margin_10 = $request->margin_10;
                    $international_user_rates->margin_11 = $request->margin_11;
                    $international_user_rates->updated_by = Auth::id();
                    $international_user_rates->rates_updated_at = Carbon::now();
                }
                $international_user_rates->save();
            }

            return redirect()->back()->with('success', 'Rates updated successfully!');
        }

    }

    public function addEconomyRatesView($id,$view = null)
    {
        // Checking For Permission
        if($view != null && $view == "view" && session('role_id') != 1 && !in_array(530, session('permissions')))
        {
            return redirect()->route('admin.access_denied');
        }
        else if($view == null && session('role_id') != 1 && count(array_intersect([529, 528], session('permissions'))) === 0)
        {
            return redirect()->route('admin.access_denied');
        }
        else if($view != null && $view != "view")
        {
            abort(404);
        }

        // Getting User And Checking If It Exists
        $user = User::find($id);
        if(!$user) {
            return redirect()->back()->with('error','User not found!');
        }

        // Checking If Screen is View Then Do Rate Exists
        if($view != null && $view == "view" && InternationalEconomyRate::where('user_id',$user->id)->doesntExist())
        {
            return redirect()->back()->with('error','Rates not found!');
        }


        $zones = Zone::where('business_category_id',2)->get();
        $data = array();

        // Getting Data For View Screen
        if($view != null && $view == "view")
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),265);
            foreach ($zones as $zone)
            {
                $zone_rates = InternationalEconomyRate::where([['user_id',$user->id],['zone_id',$zone->id]]);
                if($zone_rates->exists())
                {
                    $data[$zone->id] = $zone_rates->get();
                }
            }
        }
        else if($view == null){
            ActivityTrailController::createActivityTrailLog(Auth::id(),264);
            // Getting Data New History Is Present
            if(InternationalEconomyRateHistory::where([['user_id',$user->id],['status',0]])->exists())
            {
                foreach ($zones as $zone)
                {
                    $zone_rates = InternationalEconomyRateHistory::where([['user_id',$user->id],['zone_id',$zone->id],['status',0]]);
                    if($zone_rates->exists())
                    {
                        $data[$zone->id] = $zone_rates->get();
                    }
                }
            } // Getting Data If New History Is Not Present
            else{
                foreach ($zones as $zone)
                {
                    $zone_rates = InternationalEconomyRate::where([['user_id',$user->id],['zone_id',$zone->id]]);
                    if($zone_rates->exists())
                    {
                        $data[$zone->id] = $zone_rates->get();
                    }
                }
            }

        }

        // Getting Status of Rate
        $rate_status = InternationalEconomyRateStatus::where('user_id',$user->id);
        if($rate_status->exists())
        {
            $rate_status = $rate_status->first();
        }
        else{
            $rate_status = null;
        }

        return view('admin.accounts.economy_rates')->with(['shipper' => $user,'zones'=>$zones,'data'=>$data,'rate_status'=>$rate_status,'view'=>$view]);
    }

    public function addEconomyRatesStore($id,Request $request)
    {
        if($request->get('Add') == 1 && (session('role_id') == 1 || in_array(528, session('permissions')))) {
            $rateStatus = InternationalEconomyRateStatus::where('user_id', $id);
            if ($rateStatus->exists()) {
                $rateStatus = $rateStatus->first();
            } else {
                $rateStatus = new InternationalEconomyRateStatus();
                $rateStatus->user_id = $id;
            }
            $rateStatus->status = 1;
            $rateStatus->updated_by = Auth::id();
            $rateStatus->updated_on = now()->format("Y-m-d H:i:s");
            $rateStatus->save();
            self::EditEconomyRate($id, $request, 0);

            $user = User::find($id);
            if (in_array($user->status, [3, 4]) && $user->blacklist == 0) {
                return redirect()->route('admin.accounts.active')->with('success', 'Rates added successfully!');
            } else {
                return redirect()->route('admin.accounts.pending')->with('success', 'Rates added successfully!');
            }
        }
        else if($request->get('Approve') == 1 && (session('role_id') == 1 || in_array(529, session('permissions')))){
            $rate = InternationalEconomyRateStatus::where('user_id',$id);
            if($rate->exists())
            {
                $rate = $rate->first();
                $rate->status = 2;
                $rate->reject_reason = null;
                $rate->updated_by = Auth::id();
                $rate->updated_on =  now()->format("Y-m-d H:i:s");
                $rate->update();
                self::EditEconomyRate($id,$request,1);
                self::ApproveEconomyRate($id,$request);

                $user = User::find($id);
                if(in_array($user->status,[3,4]) && $user->blacklist == 0){
                    return redirect()->route('admin.accounts.active')->with('success', 'Rates Approved successfully!');
                }
                else{
                    return redirect()->route('admin.accounts.pending')->with('success', 'Rates Approved successfully!');
                }
            }
            return back()->with(['error'=>"Rates Not Found"]);
        }
        else{
            return back()->with(['error'=>"Invalid Request"]);
        }
    }

    // Reject Rates Function
    public function addEconomyRatesApprove($id,Request $request)
    {
       $rate = InternationalEconomyRateStatus::where('user_id',$id);
            if($rate->exists())
            {
                $rate = $rate->first();
                $rate->status = 3;
                $rate->reject_reason = $request->reject_reason;
                $rate->updated_by = Auth::id();
                $rate->updated_on =  now()->format("Y-m-d H:i:s");
                $rate->update();
                $user = User::find($id);
                if(in_array($user->status,[3,4]) && $user->blacklist == 0){
                    return redirect()->route('admin.accounts.active')->with('success', 'Rates Rejected successfully!');
                }
                else{
                    return redirect()->route('admin.accounts.pending')->with('success', 'Rates Rejected successfully!');
                }
            }
            return back()->with(['error'=>"Rates Not Found"]);

    }

    public static function EditEconomyRate($id,$request,$status)
    {
        $rates = InternationalEconomyRateHistory::where([['user_id',$id],['status',0]]);
        if($rates->exists())
        {
            $rates->delete();
        }
        $zones = Zone::where('business_category_id',2)->get();
        foreach ($zones as $zone)
        {
            if($request->has('z'.$zone->id.'_main_switch'))
            {
                $range_up = $request->input('z'.$zone->id.'_range_up');
                $range_down = $request->input('z'.$zone->id.'_range_down');
                $wa = $request->input('z'.$zone->id.'_wa_switch');
                $spkg = $request->input('z'.$zone->id.'_wa_spkg');
                $flat_charges = $request->input('z'.$zone->id.'_flat_charges');

                foreach ($range_up as $key => $value)
                {
                    $rate = new InternationalEconomyRateHistory();
                    $rate->user_id = $id;
                    $rate->zone_id = $zone->id;
                    $rate->range_up = $range_up[$key];
                    $rate->range_down = $range_down[$key];
                    $rate->weight_addition = isset($wa[$key]) ? 1 : 0;
                    $rate->kg_range = $spkg[$key] ?? 0.50;
                    $rate->flat_charges = $flat_charges[$key];
                    $rate->status = $status;
                    $rate->save();
                }
            }
        }
    }

    public static function ApproveEconomyRate($id,$request)
    {
        $rates = InternationalEconomyRate::where('user_id',$id);
        if($rates->exists())
        {
            $rates->delete();
        }

        $zones = Zone::where('business_category_id',2)->get();
        foreach ($zones as $zone)
        {
            if($request->has('z'.$zone->id.'_main_switch'))
            {
                $range_up = $request->input('z'.$zone->id.'_range_up');
                $range_down = $request->input('z'.$zone->id.'_range_down');
                $wa = $request->input('z'.$zone->id.'_wa_switch');
                $spkg = $request->input('z'.$zone->id.'_wa_spkg');
                $flat_charges = $request->input('z'.$zone->id.'_flat_charges');

                foreach ($range_up as $key => $value)
                {
                    $rate = new InternationalEconomyRate();
                    $rate->user_id = $id;
                    $rate->zone_id = $zone->id;
                    $rate->range_up = $range_up[$key];
                    $rate->range_down = $range_down[$key];
                    $rate->weight_addition = isset($wa[$key]) ? 1 : 0;
                    $rate->kg_range = $spkg[$key] ?? 0.50;
                    $rate->flat_charges = $flat_charges[$key];
                    $rate->save();
                }
            }
        }
    }

    public function get_credit(Request $request){
        $user_id = $request->user_id;
        if($user_id){
            $user = User::find($user_id);
            if($user){
                $credit_limit = NULL;
                $limit_usage = NULL;
                $limit_set = FALSE;
                $user_credit = InternationalUsersCreditLimit::where('user_id', $user->id);
                if($user_credit->exists()){
                    $user_credit = $user_credit->first();
                    $credit_limit = $user_credit->limit;
                    $limit_usage = $user_credit->limit_usage;
                    $limit_set = TRUE;
                }
                return response()->json(['status' => 0, 'limit_set' => $limit_set, 'credit_limit' => $credit_limit, 'limit_usage' => $limit_usage, 'user' => $user->name]);
            }
            else{
                return response()->json(['status' => 1, 'User not found!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'Something went wrong!']);
        }
    }

    public function credit_update(Request $request){
        $user_id = $request->user_id;
        if($user_id){
            $user = User::find($user_id);
            if($user){
                $user_credit = InternationalUsersCreditLimit::where('user_id', $user->id);
                if($user_credit->exists()){
                    $user_credit = $user_credit->first();
                    $user_credit->limit = $request->limit;
                    $user_credit->last_updated_by = Auth::id();
                    $user_credit->save();
                }
                else{
                    $user_credit = new InternationalUsersCreditLimit();
                    $user_credit->user_id = $user_id;
                    $user_credit->limit = $request->limit;
                    $user_credit->last_updated_by = Auth::id();
                    $user_credit->save();

                }
                return response()->json(['status' => 0, 'success' => 'Credit limit updated successfully!']);
            }
            else{
                return response()->json(['status' => 1, 'User not found!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'Something went wrong!']);
        }
    }

    public function extra_service_charges_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),555);
        return view('admin.international.service_charges');
    }

    public function international_shipment_details(Request $request){

        $tracking_number = $request->tracking_number;
        if($tracking_number != null) {

            $shipment = Shipment::where('tracking_number', $request->tracking_number);
            //$shipment_status = array(1,6,26,27,28,29,30,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                if ($shipment->shipment_type == 2) {
                    return response()->json(['status' => 0, 'error' => 'Retail Shipment Not Allowed']);
                } else {
                    if ($shipment->business_category_id == 2) {
                       /* if (Shipment::where('id', $shipment->id)->whereNotNull('esc_charges')->exists()) {
                            return response()->json(['status' => 0, 'error' => 'Charges already added against this tracking number']);
                        } else {
                            if (!in_array($shipment->shipper_status_id,$shipment_status )) {
                                return response()->json(['status' => 1, 'success' => 'Success']);
                            } else {
                                return response()->json(['status' => 0, 'error' => 'Charges Cannot be added against ' . $shipment->status_shipper->name]);
                            }
                        }*/

                       $shipment_invoice = InvoiceShipment::where('shipment_id',$shipment->id);
                       if($shipment_invoice->exists()){
                           $shipment_invoice = $shipment_invoice->latest()->first();
                           $invoice = Invoice::find($shipment_invoice->invoice_id);
                           if($invoice->status_id == 3){
                               return response()->json(['status' => 0, 'error' => 'Invoice already paid for this tracking number']);
                           }
                           else{
                               return response()->json(['status' => 1, 'success' => 'Success']);
                           }
                       }
                       else{
                           return response()->json(['status' => 1, 'success' => 'Success']);
                       }
                    } else {
                        return response()->json(['status' => 0, 'error' => 'Charges can be added only against international shipments']);
                    }
                }
            } else {
                return response()->json(['status' => 0, 'error' => 'Invalid Tracking Number']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Tracking Number is required']);
        }

    }

    public function extra_service_charges_submit(Request $request){

        $tracking_number = $request->tracking_number;
        $amount = $request->amount;
        if($tracking_number != null && $amount != null){
            $shipment = Shipment::where('tracking_number',$tracking_number)->first();
            if($shipment){
                $shipment = Shipment::where('id',$shipment->id)->first();
                $shipment->esc_charges = $amount;
                $shipment->save();

                if($shipment->shipper_status_id == 14){
                    AdminFinanceController::update_esc_charges($shipment->id,0);
                }

                return redirect()->back()->with('success', 'Charges Added!');
            }
            else{
                return redirect()->back()->with('error', 'Invalid Tracking Number!');
            }
        }
        else{
            return redirect()->back()->with('error', 'Tracking Number and Amount are required!');
        }
    }
}
