<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\CorporateCashHandlingCharges;
use App\Http\Models\CorporateDiscountCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateInsuranceCharges;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateReturnCharges;
use App\Http\Models\CorporateStandardMinChargeableWeight;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\Rates\CorporateRateHistory;
use App\Http\Models\Rates\PendingCorporateCashHandlingCharges;
use App\Http\Models\Rates\PendingCorporateDiscountCharge;
use App\Http\Models\Rates\PendingCorporateFuelSurcharge;
use App\Http\Models\Rates\PendingCorporateInsuranceCharges;
use App\Http\Models\Rates\PendingCorporateRateStatus;
use App\Http\Models\Rates\PendingCorporateReturnCharges;
use App\Http\Models\Rates\PendingCorporateWeightCharge;
use App\Http\Models\Rates\HistoryCorporateCashHandlingCharges;
use App\Http\Models\Rates\HistoryCorporateDiscountCharge;
use App\Http\Models\Rates\HistoryCorporateFuelSurcharge;
use App\Http\Models\Rates\HistoryCorporateInsuranceCharges;
use App\Http\Models\Rates\HistoryCorporateRateStatus;
use App\Http\Models\Rates\HistoryCorporateReturnCharges;
use App\Http\Models\Rates\HistoryCorporateWeightCharge;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AdminCorporateAccountsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function add_rates_index($id)
    {
        $user = User::find($id);
        if (!CorporateRateStatus::where('user_id', $user->id)->exists()) {
            $min_weight = CorporateStandardMinChargeableWeight::all()->groupBy('shipping_mode_id');
            $weight = StandardWeightCharge::all()->groupBy('shipping_mode_id');
            $cash = StandardCashHandlingCharge::all()->groupBy('shipping_mode_id');
            $insurance = StandardInsuranceCharge::all()->groupBy('shipping_mode_id');
            $return = StandardReturnCharge::all()->groupBy('shipping_mode_id');
            $fuel = StandardFuelSurcharge::all()->groupBy('shipping_mode_id');
            return view('admin.accounts.corporate.add_rates')->with(['shipper' => $user, 'weight' => $weight, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'min_weight' => $min_weight]);
        }
        return redirect()->back()->with('error','User rates not found!');
    }

    public function add_rates_submit(Request $request, $id){

        $messages = [
            'on_door_mcw_charges.required' => 'The overnight doorstep minimum chargeable weight field is required.',
            'on_door_mcw_charges.numeric' => 'The overnight doorstep minimum chargeable weight field must be numeric or decimal.',
            'on_hub_mcw_charges.required' => 'The overnight hub minimum chargeable weight field is required.',
            'on_hub_mcw_charges.numeric' => 'The overnight hub minimum chargeable weight field must be numeric or decimal.',
            'on_door_range_up.*.required' => 'The overnight range up field is required.',
            'on_door_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
            'on_door_range_down.*.required' => 'The overnight range down field is required.',
            'on_door_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
            'on_door_local_charges.*.required' => 'The overnight local charges field is required.',
            'on_door_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
            'on_door_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
            'on_door_class_0_charges.*.required' => 'The overnight class A charges field is required.',
            'on_door_class_1_charges.*.required' => 'The overnight class B charges field is required.',
            'on_door_class_2_charges.*.required' => 'The overnight class C charges field is required.',
            'on_door_class_3_charges.*.required' => 'The overnight class D charges field is required.',
            'on_hub_range_up.*.required' => 'The overnight range up field is required.',
            'on_hub_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
            'on_hub_range_down.*.required' => 'The overnight range down field is required.',
            'on_hub_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
            'on_hub_local_charges.*.required' => 'The overnight local charges field is required.',
            'on_hub_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
            'on_hub_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
            'on_hub_class_0_charges.*.required' => 'The overnight class A charges field is required.',
            'on_hub_class_1_charges.*.required' => 'The overnight class B charges field is required.',
            'on_hub_class_2_charges.*.required' => 'The overnight class C charges field is required.',
            'on_hub_class_3_charges.*.required' => 'The overnight class D charges field is required.',
            'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
            'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
            'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
            'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
            'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
            'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
            'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
            'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
            'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
            'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
            'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
            'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
            'on_return_national_charges.required_if' => 'The overnight return national charges field is required.',
            'on_return_national_charges.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
            'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
            'on_discount_title.required_if' => 'The overnight discount title field must be required',
            'on_daterange.required_if' => 'The overnight discount date range field must be required',
            'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
            'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
            'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
            'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
            'on_discount_title.required_with'=>'The overnight discount title field is required',
            'on_daterange.required_with'=>'The overnight discount date field is required',
            //overland starts
            'ol_door_mcw_charges.required' => 'The overland doorstep minimum chargeable weight field is required.',
            'ol_door_mcw_charges.numeric' => 'The overland doorstep minimum chargeable weight field must be numeric or decimal.',
            'ol_hub_mcw_charges.required' => 'The overland hub minimum chargeable weight field is required.',
            'ol_hub_mcw_charges.numeric' => 'The overland hub minimum chargeable weight field must be numeric or decimal.',
            'ol_door_range_up.*.required' => 'The overland range up field is required.',
            'ol_door_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
            'ol_door_range_down.*.required' => 'The overland range down field is required.',
            'ol_door_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
            'ol_door_local_charges.*.required' => 'The overland local charges field is required.',
            'ol_door_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
            'ol_door_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
            'ol_door_class_0_charges.*.required' => 'The overland class A charges field is required.',
            'ol_door_class_1_charges.*.required' => 'The overland class B charges field is required.',
            'ol_door_class_2_charges.*.required' => 'The overland class C charges field is required.',
            'ol_door_class_3_charges.*.required' => 'The overland class D charges field is required.',
            'ol_hub_range_up.*.required' => 'The overland range up field is required.',
            'ol_hub_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
            'ol_hub_range_down.*.required' => 'The overland range down field is required.',
            'ol_hub_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
            'ol_hub_local_charges.*.required' => 'The overland local charges field is required.',
            'ol_hub_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
            'ol_hub_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
            'ol_hub_class_0_charges.*.required' => 'The overland class A charges field is required.',
            'ol_hub_class_1_charges.*.required' => 'The overland class B charges field is required.',
            'ol_hub_class_2_charges.*.required' => 'The overland class C charges field is required.',
            'ol_hub_class_3_charges.*.required' => 'The overland class D charges field is required.',
            'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
            'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
            'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
            'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
            'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
            'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
            'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
            'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
            'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
            'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
            'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
            'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
            'ol_return_national_charges.required_if' => 'The overland return national charges field is required.',
            'ol_return_national_charges.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
            'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
            'ol_discount_title.required_if' => 'The overland discount title field must be required',
            'ol_daterange.required_if' => 'The overland discount date range field must be required',
            'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
            'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
            'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
            'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
            'ol_discount_title.required_with'=>'The overland discount title field is required',
            'ol_daterange.required_with'=>'The overland discount date field is required',
            //overland end and detain starts
            'detain_door_mcw_charges.required' => 'The detain doorstep minimum chargeable weight field is required.',
            'detain_door_mcw_charges.numeric' => 'The detain doorstep minimum chargeable weight field must be numeric or decimal.',
            'detain_hub_mcw_charges.required' => 'The detain hub minimum chargeable weight field is required.',
            'detain_hub_mcw_charges.numeric' => 'The detain hub minimum chargeable weight field must be numeric or decimal.',
            'detain_door_range_up.*.required' => 'The detain range up field is required.',
            'detain_door_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
            'detain_door_range_down.*.required' => 'The detain range down field is required.',
            'detain_door_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
            'detain_door_local_charges.*.required' => 'The detain local charges field is required.',
            'detain_door_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
            'detain_door_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
            'detain_door_class_0_charges.*.required' => 'The detain class A charges field is required.',
            'detain_door_class_1_charges.*.required' => 'The detain class B charges field is required.',
            'detain_door_class_2_charges.*.required' => 'The detain class C charges field is required.',
            'detain_door_class_3_charges.*.required' => 'The detain class D charges field is required.',
            'detain_hub_range_up.*.required' => 'The detain range up field is required.',
            'detain_hub_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
            'detain_hub_range_down.*.required' => 'The detain range down field is required.',
            'detain_hub_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
            'detain_hub_local_charges.*.required' => 'The detain local charges field is required.',
            'detain_hub_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
            'detain_hub_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
            'detain_hub_class_0_charges.*.required' => 'The detain class A charges field is required.',
            'detain_hub_class_1_charges.*.required' => 'The detain class B charges field is required.',
            'detain_hub_class_2_charges.*.required' => 'The detain class C charges field is required.',
            'detain_hub_class_3_charges.*.required' => 'The detain class D charges field is required.',
            'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
            'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
            'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
            'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
            'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
            'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
            'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
            'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
            'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
            'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
            'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
            'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
            'detain_return_national_charges.required_if' => 'The detain return national charges field is required.',
            'detain_return_national_charges.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
            'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
            'detain_discount_title.required_if' => 'The detain discount title field must be required',
            'detain_daterange.required_if' => 'The detain discount date range field must be required',
            'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
            'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
            'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
            'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
            'detain_discount_title.required_with'=>'The detain discount title field is required',
            'detain_daterange.required_with'=>'The detain discount date field is required',
            //detain ends and sameday starts
            'sameday_door_mcw_charges.required' => 'The sameday doorstep minimum chargeable weight field is required.',
            'sameday_door_mcw_charges.numeric' => 'The sameday doorstep minimum chargeable weight field must be numeric or decimal.',
            'sameday_hub_mcw_charges.required' => 'The sameday hub minimum chargeable weight field is required.',
            'sameday_hub_mcw_charges.numeric' => 'The sameday hub minimum chargeable weight field must be numeric or decimal.',
            'sameday_door_range_up.*.required' => 'The sameday doorstep range up field is required.',
            'sameday_door_range_up.*.numeric' => 'The sameday doorstep range up field must be numeric or decimal.',
            'sameday_door_range_down.*.required' => 'The sameday doorstep range down field is required.',
            'sameday_door_range_down.*.numeric' => 'The sameday doorstep range down field must be numeric or decimal.',
            'sameday_door_local_charges.*.required' => 'The sameday doorstep local charges field is required.',
            'sameday_door_local_charges.*.numeric' => 'The sameday doorstep local charges field must be numeric.',
            'sameday_door_class_0_charges.*.required' => 'The sameday doorstep class A charges field is required.',
            'sameday_door_class_0_charges.*.numeric' => 'The sameday doorstep class A charges field must be numeric.',
            'sameday_hub_range_up.*.required' => 'The sameday hub range up field is required.',
            'sameday_hub_range_up.*.numeric' => 'The sameday hub range up field must be numeric or decimal.',
            'sameday_hub_range_down.*.required' => 'The sameday hub range down field is required.',
            'sameday_hub_range_down.*.numeric' => 'The sameday hub range down field must be numeric or decimal.',
            'sameday_hub_local_charges.*.required' => 'The sameday hub local charges field is required.',
            'sameday_hub_local_charges.*.numeric' => 'The sameday hub local charges field must be numeric.',
            'sameday_hub_class_0_charges.*.required' => 'The sameday hub class A charges field is required.',
            'sameday_hub_class_0_charges.*.numeric' => 'The sameday hub class A charges field must be numeric.',
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
            'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
            'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
            'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
            'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
            'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
            'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
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

                'on_door_mcw_charges' => 'required|numeric|between:0,10000',
                'on_hub_mcw_charges' => 'required|numeric|between:0,10000',
                'on_door_range_up.*' => 'required|numeric|between:0,10000',
                'on_door_range_down.*' => 'required|numeric|between:0,10000',
                'on_door_local_charges.*' => 'required|numeric',
                'on_door_class_0_charges.*' => 'required|numeric',
                'on_door_class_1_charges.*' => 'required',
                'on_door_class_2_charges.*' => 'required',
                'on_door_class_3_charges.*' => 'required',
                'on_hub_range_up.*' => 'required|numeric|between:0,10000',
                'on_hub_range_down.*' => 'required|numeric|between:0,10000',
                'on_hub_local_charges.*' => 'required|numeric',
                'on_hub_class_0_charges.*' => 'required|numeric',
                'on_hub_class_1_charges.*' => 'required',
                'on_hub_class_2_charges.*' => 'required',
                'on_hub_class_3_charges.*' => 'required',
                'on_cash_range_up.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*'=>'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*'=>'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*'=>'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*'=>'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'on_return_national_charges.*'=>'required_if:on_return_switch,==,on|numeric',
                'overnight_fuel_surcharge'=>'required_if:overnight_fuel_switch,==,on|numeric',
                'on_discount_title'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_daterange'=>'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                'on_discount_weight_rate'=>'required_if:on_discount_weight_switch,==,on',
                'on_discount_cash_rate'=>'required_if:on_discount_cash_switch,==,on',
                'on_discount_insurance_rate'=>'required_if:on_discount_insurance_switch,==,on',
                'on_discount_return_rate'=>'required_if:on_discount_return_switch,==,on',
            ];
        }
        //overland
        if($request->has('ol_main_switch') && $request->ol_main_switch == 'on'){
            $ol_validations = [
                'ol_door_mcw_charges' => 'required|numeric|between:0,10000',
                'ol_hub_mcw_charges' => 'required|numeric|between:0,10000',
                'ol_door_range_up.*' => 'required|numeric|between:0,10000',
                'ol_door_range_down.*' => 'required|numeric|between:0,10000',
                'ol_door_local_charges.*' => 'required|numeric',
                'ol_door_class_0_charges.*' => 'required|numeric',
                'ol_door_class_1_charges.*' => 'required',
                'ol_door_class_2_charges.*' => 'required',
                'ol_door_class_3_charges.*' => 'required',
                'ol_hub_range_up.*' => 'required|numeric|between:0,10000',
                'ol_hub_range_down.*' => 'required|numeric|between:0,10000',
                'ol_hub_local_charges.*' => 'required|numeric',
                'ol_hub_class_0_charges.*' => 'required|numeric',
                'ol_hub_class_1_charges.*' => 'required',
                'ol_hub_class_2_charges.*' => 'required',
                'ol_hub_class_3_charges.*' => 'required',
                'ol_cash_range_up.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*'=>'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*'=>'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*'=>'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*'=>'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'ol_return_national_charges.*'=>'required_if:ol_return_switch,==,on|numeric',
                'overland_fuel_surcharge'=>'required_if:overland_fuel_switch,==,on|numeric',
                'ol_discount_title'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_daterange'=>'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                'ol_discount_weight_rate'=>'required_if:ol_discount_weight_switch,==,on',
                'ol_discount_cash_rate'=>'required_if:ol_discount_cash_switch,==,on',
                'ol_discount_insurance_rate'=>'required_if:ol_discount_insurance_switch,==,on',
                'ol_discount_return_rate'=>'required_if:ol_discount_return_switch,==,on',
            ];
        }
        //overland
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on'){
            $detain_validations = [
                'detain_door_mcw_charges' => 'required|numeric|between:0,10000',
                'detain_hub_mcw_charges' => 'required|numeric|between:0,10000',
                'detain_door_range_up.*' => 'required|numeric|between:0,10000',
                'detain_door_range_down.*' => 'required|numeric|between:0,10000',
                'detain_door_local_charges.*' => 'required|numeric',
                'detain_door_class_0_charges.*' => 'required|numeric',
                'detain_door_class_1_charges.*' => 'required',
                'detain_door_class_2_charges.*' => 'required',
                'detain_door_class_3_charges.*' => 'required',
                'detain_hub_range_up.*' => 'required|numeric|between:0,10000',
                'detain_hub_range_down.*' => 'required|numeric|between:0,10000',
                'detain_hub_local_charges.*' => 'required|numeric',
                'detain_hub_class_0_charges.*' => 'required|numeric',
                'detain_hub_class_1_charges.*' => 'required',
                'detain_hub_class_2_charges.*' => 'required',
                'detain_hub_class_3_charges.*' => 'required',
                'detain_cash_range_up.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*'=>'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*'=>'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*'=>'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*'=>'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_return_national_charges.*'=>'required_if:detain_return_switch,==,on|numeric',
                'detain_fuel_surcharge'=>'required_if:detain_fuel_switch,==,on|numeric',
                'detain_discount_title'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_daterange'=>'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                'detain_discount_weight_rate'=>'required_if:detain_discount_weight_switch,==,on',
                'detain_discount_cash_rate'=>'required_if:detain_discount_cash_switch,==,on',
                'detain_discount_insurance_rate'=>'required_if:detain_discount_insurance_switch,==,on',
                'detain_discount_return_rate'=>'required_if:detain_discount_return_switch,==,on',
            ];
        }
        //sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){
            $sameday_validations = [
                'sameday_door_mcw_charges' => 'required|numeric|between:0,10000',
                'sameday_hub_mcw_charges' => 'required|numeric|between:0,10000',
                'sameday_door_range_up.*' => 'required|numeric|between:0,10000',
                'sameday_door_range_down.*' => 'required|numeric|between:0,10000',
                'sameday_door_local_charges.*' => 'required|numeric',
                'sameday_door_class_0_charges.*' => 'required|numeric',
                'sameday_door_class_1_charges.*' => 'required',
                'sameday_door_class_2_charges.*' => 'required',
                'sameday_door_class_3_charges.*' => 'required',
                'sameday_hub_range_up.*' => 'required|numeric|between:0,10000',
                'sameday_hub_range_down.*' => 'required|numeric|between:0,10000',
                'sameday_hub_local_charges.*' => 'required|numeric',
                'sameday_hub_class_0_charges.*' => 'required|numeric',
                'sameday_hub_class_1_charges.*' => 'required',
                'sameday_hub_class_2_charges.*' => 'required',
                'sameday_hub_class_3_charges.*' => 'required',
                'sameday_cash_range_up.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*'=>'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*'=>'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*'=>'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*'=>'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_national_charges.*'=>'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge'=>'required_if:sameday_fuel_switch,==,on|numeric',
                'sameday_discount_title'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange'=>'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate'=>'required_if:sameday_discount_weight_switch,==,on',
                'sameday_discount_cash_rate'=>'required_if:sameday_discount_cash_switch,==,on',
                'sameday_discount_insurance_rate'=>'required_if:sameday_discount_insurance_switch,==,on',
                'sameday_discount_return_rate'=>'required_if:sameday_discount_return_switch,==,on',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }
//        return $request;

        if($request->has('on_main_switch') && $request->on_main_switch == 'on'){
            $ONRateAlready = CorporateRateStatus::where('user_id',$id)->where('shipping_mode_id',1)->get();

            if($ONRateAlready->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'delivery_type_id'=>1,
                    'min_chargeable_weight' => $request->on_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'delivery_type_id'=>2,
                    'min_chargeable_weight' => $request->on_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>1,
                    'status'=> ($request->has('on_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('on_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('on_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('on_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                ]);
                foreach ($request->on_door_range_up as $index => $on_door_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 1,
                        'range_up' => $request->on_door_range_up[$index],
                        'range_down' => $request->on_door_range_down[$index],
                        'local_or_6hr' => $request->on_door_local_charges[$index],
                        'national_charges_class_0' => $request->on_door_class_0_charges[$index],
                        'national_charges_class_1' => $request->on_door_class_1_charges[$index],
                        'national_charges_class_2' => $request->on_door_class_2_charges[$index],
                        'national_charges_class_3' => $request->on_door_class_3_charges[$index]
                    ]);

                }
                foreach ($request->on_hub_range_up as $index => $on_hub_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 2,
                        'range_up' => $request->on_hub_range_up[$index],
                        'range_down' => $request->on_hub_range_down[$index],
                        'local_or_6hr' => $request->on_hub_local_charges[$index],
                        'national_charges_class_0' => $request->on_hub_class_0_charges[$index],
                        'national_charges_class_1' => $request->on_hub_class_1_charges[$index],
                        'national_charges_class_2' => $request->on_hub_class_2_charges[$index],
                        'national_charges_class_3' => $request->on_hub_class_3_charges[$index]
                    ]);

                }

                //Cash handling Charges
                if($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on'){
                    foreach ($request->on_cash_range_up as $ind => $on_cash_range_up){
                        CorporateCashHandlingCharges::create([
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
                        CorporateInsuranceCharges::create([
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
                    CorporateReturnCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'local'=> $request->on_return_local_charges,
                        'national'=> $request->on_return_national_charges
                    ]);
                }
                //Fuel Charges
                if($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on'){
                    CorporateFuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>1,
                        'fuel_surcharge'=> $request->overnight_fuel_surcharge
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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

                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    CorporateDiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'title'=> $request->on_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
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

            $OLRatePresent = CorporateRateStatus::where('user_id',$id)->where('shipping_mode_id',2)->get();

            if($OLRatePresent->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'delivery_type_id'=>1,
                    'min_chargeable_weight' => $request->ol_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'delivery_type_id'=>2,
                    'min_chargeable_weight' => $request->ol_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>2,
                    'status'=> ($request->has('ol_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('ol_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('ol_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('ol_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('overland_fuel_switch'))? 1:0,
                ]);

                foreach ($request->ol_door_range_up as $index => $ol_door_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 1,
                        'range_up' => $request->ol_door_range_up[$index],
                        'range_down' => $request->ol_door_range_down[$index],
                        'local_or_6hr' => $request->ol_door_local_charges[$index],
                        'national_charges_class_0' => $request->ol_door_class_0_charges[$index],
                        'national_charges_class_1' => $request->ol_door_class_1_charges[$index],
                        'national_charges_class_2' => $request->ol_door_class_2_charges[$index],
                        'national_charges_class_3' => $request->ol_door_class_3_charges[$index]
                    ]);

                }
                foreach ($request->ol_hub_range_up as $index => $ol_hub_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 2,
                        'range_up' => $request->ol_hub_range_up[$index],
                        'range_down' => $request->ol_hub_range_down[$index],
                        'local_or_6hr' => $request->ol_hub_local_charges[$index],
                        'national_charges_class_0' => $request->ol_hub_class_0_charges[$index],
                        'national_charges_class_1' => $request->ol_hub_class_1_charges[$index],
                        'national_charges_class_2' => $request->ol_hub_class_2_charges[$index],
                        'national_charges_class_3' => $request->ol_hub_class_3_charges[$index]
                    ]);

                }

                //Cash handling Charges
                if($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on'){
                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up){
                        CorporateCashHandlingCharges::create([
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
                        CorporateInsuranceCharges::create([
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
                    CorporateReturnCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'local'=> $request->ol_return_local_charges,
                        'national'=> $request->ol_return_national_charges
                    ]);
                }
                if($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on'){
                    CorporateFuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>2,
                        'fuel_surcharge'=> $request->overland_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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
                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    CorporateDiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'title'=> $request->ol_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }

        }
        //Detain
        if($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

            $DetainRatePresent = CorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

            if ($DetainRatePresent->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>3,
                    'delivery_type_id'=>1,
                    'min_chargeable_weight' => $request->detain_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>3,
                    'delivery_type_id'=>2,
                    'min_chargeable_weight' => $request->detain_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>3,
                    'status'=> ($request->has('detain_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('detain_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('detain_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('detain_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('detain_fuel_switch'))? 1:0,
                ]);
                foreach ($request->detain_door_range_up as $index => $detain_door_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 1,
                        'range_up' => $request->detain_door_range_up[$index],
                        'range_down' => $request->detain_door_range_down[$index],
                        'local_or_6hr' => $request->detain_door_local_charges[$index],
                        'national_charges_class_0' => $request->detain_door_class_0_charges[$index],
                        'national_charges_class_1' => $request->detain_door_class_1_charges[$index],
                        'national_charges_class_2' => $request->detain_door_class_2_charges[$index],
                        'national_charges_class_3' => $request->detain_door_class_3_charges[$index]
                    ]);

                }
                foreach ($request->detain_hub_range_up as $index => $detain_hub_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 2,
                        'range_up' => $request->detain_hub_range_up[$index],
                        'range_down' => $request->detain_hub_range_down[$index],
                        'local_or_6hr' => $request->detain_hub_local_charges[$index],
                        'national_charges_class_0' => $request->detain_hub_class_0_charges[$index],
                        'national_charges_class_1' => $request->detain_hub_class_1_charges[$index],
                        'national_charges_class_2' => $request->detain_hub_class_2_charges[$index],
                        'national_charges_class_3' => $request->detain_hub_class_3_charges[$index]
                    ]);

                }

                //Cash handling Charges
                if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                    foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                        CorporateCashHandlingCharges::create([
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
                        CorporateInsuranceCharges::create([
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
                    CorporateReturnCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'local' => $request->detain_return_local_charges,
                        'national' => $request->detain_return_national_charges
                    ]);
                }
                if($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on'){
                    CorporateFuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>3,
                        'fuel_surcharge'=> $request->detain_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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

                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->detain_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    CorporateDiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'title' => $request->detain_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'to' => $to,
                        'from' => $from,
                        'added_by' => Auth::id()
                    ]);
                }

            }

        }
        //Sameday
        if($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on'){

            $SamedayRatePresent = CorporateRateStatus::where('user_id',$id)->where('shipping_mode_id',4)->get();
            if($SamedayRatePresent->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'delivery_type_id'=>1,
                    'min_chargeable_weight' => $request->sameday_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'delivery_type_id'=>2,
                    'min_chargeable_weight' => $request->sameday_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id'=>$id,
                    'shipping_mode_id'=>4,
                    'status'=> ($request->has('sameday_main_switch'))? 1:0,
                    'cash_handling_charges'=> ($request->has('sameday_cash_handling_switch'))? 1:0,
                    'insurance_charges'=> ($request->has('sameday_insurance_charges_switch'))? 1:0,
                    'return_charges'=> ($request->has('sameday_return_switch'))? 1:0,
                    'fuel_charges'=> ($request->has('sameday_fuel_switch'))? 1:0,
                ]);

                foreach ($request->sameday_door_range_up as $index => $sameday_door_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 1,
                        'range_up' => $request->sameday_door_range_up[$index],
                        'range_down' => $request->sameday_door_range_down[$index],
                        'local_or_6hr' => $request->sameday_door_local_charges[$index],
                        'national_charges_class_0' => $request->sameday_door_class_0_charges[$index],
                        'national_charges_class_1' => $request->sameday_door_class_1_charges[$index],
                        'national_charges_class_2' => $request->sameday_door_class_2_charges[$index],
                        'national_charges_class_3' => $request->sameday_door_class_3_charges[$index]
                    ]);

                }
                foreach ($request->sameday_hub_range_up as $index => $sameday_hub_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 2,
                        'range_up' => $request->sameday_hub_range_up[$index],
                        'range_down' => $request->sameday_hub_range_down[$index],
                        'local_or_6hr' => $request->sameday_hub_local_charges[$index],
                        'national_charges_class_0' => $request->sameday_hub_class_0_charges[$index],
                        'national_charges_class_1' => 0,
                        'national_charges_class_2' => 0,
                        'national_charges_class_3' => 0
                    ]);

                }

                //Cash handling Charges
                if($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on'){
                    foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up){
                        CorporateCashHandlingCharges::create([
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
                        CorporateInsuranceCharges::create([
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
                    CorporateReturnCharges::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'local'=> $request->sameday_return_local_charges,
                        'national'=> $request->sameday_return_national_charges
                    ]);
                }
                if($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on'){
                    CorporateFuelSurcharge::create([
                        'user_id'=>$id,
                        'shipping_mode_id'=>4,
                        'fuel_surcharge'=> $request->sameday_fuel_surcharge
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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

                if($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2],$date_to[0],$date_to[1],0,0,0,'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2],$date_from[0],$date_from[1],0,0,0,'UTC')->toDateTimeString();


                    CorporateDiscountCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'title'=> $request->sameday_discount_title,
                        'weight' => $discount_weight,
                        'cash' => $discount_cash,
                        'insurance' => $discount_insurance,
                        'return' => $discount_return,
                        'to' => $to,
                        'from' => $from,
                        'added_by'=>Auth::id()
                    ]);
                }

            }
        }
        User::where('id',$id)->update(['status'=>1,'rates_added_by'=>Auth::id()]);


        return redirect(route('admin.accounts.pending'))->with('success','All Rates are added');
    }

    public function edit_rates_index($id){
        $user = User::find($id);
        if ((($user['rate_status'] >= 0) && $user['status']==1) || (($user['rate_status']==0) && $user['status']==3)) {
            $switches = CorporateRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $min_weight = CorporateMinChargeableWeight::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $weight = CorporateWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');

            $cash = CorporateCashHandlingCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = CorporateInsuranceCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = CorporateReturnCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = CorporateFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = CorporateDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_status = $user['rate_status'];
        }
        elseif(($user['rate_status']>=1) && $user['status']==3){
            $switches = PendingCorporateRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
            $weight = PendingCorporateWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        $cash = '';
            $cash = PendingCorporateCashHandlingCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = PendingCorporateInsuranceCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = PendingCorporateReturnCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = PendingCorporateFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = PendingCorporateDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $rate_status = $user['rate_status'];
        }
//        return $discount;
        return view('admin.accounts.corporate.edit_rates')->with(['shipper'=>$user,'switches'=>$switches,'weight'=>$weight,'cashHandling'=>$cash,'insuranceCharges'=>$insurance,'returnCharges'=>$return,'fuelCharges'=>$fuel, 'discountCharges'=>$discount, 'rate_status'=>$rate_status, 'min_weight' => $min_weight]);
    }

    public function edit_rates_submit(Request $request, $id)
    {

        $user = User::find($id);
        if ($user['status'] != 3) {

            $messages = [
                'on_door_mcw_charges.required' => 'The overnight doorstep minimum chargeable weight field is required.',
                'on_door_mcw_charges.numeric' => 'The overnight doorstep minimum chargeable weight field must be numeric or decimal.',
                'on_hub_mcw_charges.required' => 'The overnight hub minimum chargeable weight field is required.',
                'on_hub_mcw_charges.numeric' => 'The overnight hub minimum chargeable weight field must be numeric or decimal.',
                'on_door_range_up.*.required' => 'The overnight range up field is required.',
                'on_door_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_door_range_down.*.required' => 'The overnight range down field is required.',
                'on_door_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_door_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_door_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_door_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_door_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_door_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_door_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_door_class_3_charges.*.required' => 'The overnight class D charges field is required.',
                'on_hub_range_up.*.required' => 'The overnight range up field is required.',
                'on_hub_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_hub_range_down.*.required' => 'The overnight range down field is required.',
                'on_hub_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_hub_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_hub_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_hub_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_hub_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_hub_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_hub_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_hub_class_3_charges.*.required' => 'The overnight class D charges field is required.',
                'on_cash_range_up.*.required_if' => 'The overnight cash range up field is required.',
                'on_cash_range_up.*.numeric' => 'The overnight cash range up field must be numeric.',
                'on_cash_range_down.*.required_if' => 'The overnight cash range down field is required.',
                'on_cash_range_down.*.numeric' => 'The overnight cash range down field must be numeric.',
                'on_cash_charges.*.required_if' => 'The overnight cash charges field is required.',
                'on_ins_range_up.*.required_if' => 'The overnight insurance range up field is required.',
                'on_ins_range_up.*.numeric' => 'The overnight insurance range up field must be numeric or percentage.',
                'on_ins_range_down.*.required_if' => 'The overnight insurance range down field is required.',
                'on_ins_range_down.*.numeric' => 'The overnight insurance range down field must be numeric or percentage.',
                'on_ins_charges.*.required_if' => 'The overnight insurance charges field is required.',
                'on_return_local_charges.required_if' => 'The overnight return local charges field is required.',
                'on_return_local_charges.numeric' => 'The overnight return local charges field must be numeric or percentage.',
                'on_return_national_charges.required_if' => 'The overnight return national charges field is required.',
                'on_return_national_charges.numeric' => 'The overnight return national charges field must be numeric or percentage.',
                'overnight_fuel_surcharge.required_if' => 'The overnight return national charges field is required.',
                'overnight_fuel_surcharge.numeric' => 'The overnight return national charges field must be numeric or percentage.',
                'on_discount_title.required_if' => 'The overnight discount title field must be required',
                'on_daterange.required_if' => 'The overnight discount date range field must be required',
                'on_discount_weight_rate.required_if' => 'The overnight discount weight field must be required',
                'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
                'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
                'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
                'on_discount_title.required_with' => 'The overnight discount title field is required',
                'on_daterange.required_with' => 'The overnight discount date field is required',
                //overland starts
                'ol_door_mcw_charges.required' => 'The overland doorstep minimum chargeable weight field is required.',
                'ol_door_mcw_charges.numeric' => 'The overland doorstep minimum chargeable weight field must be numeric or decimal.',
                'ol_hub_mcw_charges.required' => 'The overland hub minimum chargeable weight field is required.',
                'ol_hub_mcw_charges.numeric' => 'The overland hub minimum chargeable weight field must be numeric or decimal.',
                'ol_door_range_up.*.required' => 'The overland range up field is required.',
                'ol_door_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_door_range_down.*.required' => 'The overland range down field is required.',
                'ol_door_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_door_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_door_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_door_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_door_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_door_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_door_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_door_class_3_charges.*.required' => 'The overland class D charges field is required.',
                'ol_hub_range_up.*.required' => 'The overland range up field is required.',
                'ol_hub_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_hub_range_down.*.required' => 'The overland range down field is required.',
                'ol_hub_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_hub_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_hub_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_hub_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_hub_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_hub_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_hub_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_hub_class_3_charges.*.required' => 'The overland class D charges field is required.',
                'ol_cash_range_up.*.required_if' => 'The overland cash range up field is required.',
                'ol_cash_range_up.*.numeric' => 'The overland cash range up field must be numeric.',
                'ol_cash_range_down.*.required_if' => 'The overland cash range down field is required.',
                'ol_cash_range_down.*.numeric' => 'The overland cash range down field must be numeric.',
                'ol_cash_charges.*.required_if' => 'The overland cash charges field is required.',
                'ol_ins_range_up.*.required_if' => 'The overland insurance range up field is required.',
                'ol_ins_range_up.*.numeric' => 'The overland insurance range up field must be numeric or percentage.',
                'ol_ins_range_down.*.required_if' => 'The overland insurance range down field is required.',
                'ol_ins_range_down.*.numeric' => 'The overland insurance range down field must be numeric or percentage.',
                'ol_ins_charges.*.required_if' => 'The overland insurance charges field is required.',
                'ol_return_local_charges.required_if' => 'The overland return local charges field is required.',
                'ol_return_local_charges.numeric' => 'The overland return local charges field must be numeric or percentage.',
                'ol_return_national_charges.required_if' => 'The overland return national charges field is required.',
                'ol_return_national_charges.numeric' => 'The overland return national charges field must be numeric or percentage.',
                'overland_fuel_surcharge.required_if' => 'The overland return national charges field is required.',
                'overland_fuel_surcharge.numeric' => 'The overland return national charges field must be numeric or percentage.',
                'ol_discount_title.required_if' => 'The overland discount title field must be required',
                'ol_daterange.required_if' => 'The overland discount date range field must be required',
                'ol_discount_weight_rate.required_if' => 'The overland discount weight field must be required',
                'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
                'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
                'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
                'ol_discount_title.required_with' => 'The overland discount title field is required',
                'ol_daterange.required_with' => 'The overland discount date field is required',
                //overland end and detain starts
                'detain_door_mcw_charges.required' => 'The detain doorstep minimum chargeable weight field is required.',
                'detain_door_mcw_charges.numeric' => 'The detain doorstep minimum chargeable weight field must be numeric or decimal.',
                'detain_hub_mcw_charges.required' => 'The detain hub minimum chargeable weight field is required.',
                'detain_hub_mcw_charges.numeric' => 'The detain hub minimum chargeable weight field must be numeric or decimal.',
                'detain_door_range_up.*.required' => 'The detain range up field is required.',
                'detain_door_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_door_range_down.*.required' => 'The detain range down field is required.',
                'detain_door_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_door_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_door_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_door_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_door_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_door_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_door_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_door_class_3_charges.*.required' => 'The detain class D charges field is required.',
                'detain_hub_range_up.*.required' => 'The detain range up field is required.',
                'detain_hub_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_hub_range_down.*.required' => 'The detain range down field is required.',
                'detain_hub_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_hub_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_hub_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_hub_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_hub_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_hub_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_hub_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_hub_class_3_charges.*.required' => 'The detain class D charges field is required.',
                'detain_cash_range_up.*.required_if' => 'The detain cash range up field is required.',
                'detain_cash_range_up.*.numeric' => 'The detain cash range up field must be numeric.',
                'detain_cash_range_down.*.required_if' => 'The detain cash range down field is required.',
                'detain_cash_range_down.*.numeric' => 'The detain cash range down field must be numeric.',
                'detain_cash_charges.*.required_if' => 'The detain cash charges field is required.',
                'detain_ins_range_up.*.required_if' => 'The detain insurance range up field is required.',
                'detain_ins_range_up.*.numeric' => 'The detain insurance range up field must be numeric or percentage.',
                'detain_ins_range_down.*.required_if' => 'The detain insurance range down field is required.',
                'detain_ins_range_down.*.numeric' => 'The detain insurance range down field must be numeric or percentage.',
                'detain_ins_charges.*.required_if' => 'The detain insurance charges field is required.',
                'detain_return_local_charges.required_if' => 'The detain return local charges field is required.',
                'detain_return_local_charges.numeric' => 'The detain return local charges field must be numeric or percentage.',
                'detain_return_national_charges.required_if' => 'The detain return national charges field is required.',
                'detain_return_national_charges.numeric' => 'The detain return national charges field must be numeric or percentage.',
                'detain_fuel_surcharge.required_if' => 'The detain return national charges field is required.',
                'detain_fuel_surcharge.numeric' => 'The detain return national charges field must be numeric or percentage.',
                'detain_discount_title.required_if' => 'The detain discount title field must be required',
                'detain_daterange.required_if' => 'The detain discount date range field must be required',
                'detain_discount_weight_rate.required_if' => 'The detain discount weight field must be required',
                'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
                'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
                'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
                'detain_discount_title.required_with' => 'The detain discount title field is required',
                'detain_daterange.required_with' => 'The detain discount date field is required',
                //detain ends and sameday starts
                'sameday_door_mcw_charges.required' => 'The sameday doorstep minimum chargeable weight field is required.',
                'sameday_door_mcw_charges.numeric' => 'The sameday doorstep minimum chargeable weight field must be numeric or decimal.',
                'sameday_hub_mcw_charges.required' => 'The sameday hub minimum chargeable weight field is required.',
                'sameday_hub_mcw_charges.numeric' => 'The sameday hub minimum chargeable weight field must be numeric or decimal.',
                'sameday_door_range_up.*.required' => 'The sameday doorstep range up field is required.',
                'sameday_door_range_up.*.numeric' => 'The sameday doorstep range up field must be numeric or decimal.',
                'sameday_door_range_down.*.required' => 'The sameday doorstep range down field is required.',
                'sameday_door_range_down.*.numeric' => 'The sameday doorstep range down field must be numeric or decimal.',
                'sameday_door_local_charges.*.required' => 'The sameday doorstep local charges field is required.',
                'sameday_door_local_charges.*.numeric' => 'The sameday doorstep local charges field must be numeric.',
                'sameday_door_class_0_charges.*.required' => 'The sameday doorstep class A charges field is required.',
                'sameday_door_class_0_charges.*.numeric' => 'The sameday doorstep class A charges field must be numeric.',
                'sameday_hub_range_up.*.required' => 'The sameday hub range up field is required.',
                'sameday_hub_range_up.*.numeric' => 'The sameday hub range up field must be numeric or decimal.',
                'sameday_hub_range_down.*.required' => 'The sameday hub range down field is required.',
                'sameday_hub_range_down.*.numeric' => 'The sameday hub range down field must be numeric or decimal.',
                'sameday_hub_local_charges.*.required' => 'The sameday hub local charges field is required.',
                'sameday_hub_local_charges.*.numeric' => 'The sameday hub local charges field must be numeric.',
                'sameday_hub_class_0_charges.*.required' => 'The sameday hub class A charges field is required.',
                'sameday_hub_class_0_charges.*.numeric' => 'The sameday hub class A charges field must be numeric.',
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
                'sameday_discount_title.required_if' => 'The sameday discount title field must be required',
                'sameday_daterange.required_if' => 'The sameday discount date range field must be required',
                'sameday_discount_weight_rate.required_if' => 'The sameday discount weight field must be required',
                'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
                'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
                'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
                'sameday_discount_title.required_with' => 'The sameday discount title field is required',
                'sameday_daterange.required_with' => 'The sameday discount date field is required',
                //sameday ends
            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $on_validations = [

                    'on_door_mcw_charges' => 'required|numeric|between:0,10000',
                    'on_hub_mcw_charges' => 'required|numeric|between:0,10000',
                    'on_door_range_up.*' => 'required|numeric|between:0,10000',
                    'on_door_range_down.*' => 'required|numeric|between:0,10000',
                    'on_door_local_charges.*' => 'required|numeric',
                    'on_door_class_0_charges.*' => 'required|numeric',
                    'on_door_class_1_charges.*' => 'required',
                    'on_door_class_2_charges.*' => 'required',
                    'on_door_class_3_charges.*' => 'required',
                    'on_hub_range_up.*' => 'required|numeric|between:0,10000',
                    'on_hub_range_down.*' => 'required|numeric|between:0,10000',
                    'on_hub_local_charges.*' => 'required|numeric',
                    'on_hub_class_0_charges.*' => 'required|numeric',
                    'on_hub_class_1_charges.*' => 'required',
                    'on_hub_class_2_charges.*' => 'required',
                    'on_hub_class_3_charges.*' => 'required',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_national_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',
                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                ];
            }
            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ol_validations = [
                    'ol_door_mcw_charges' => 'required|numeric|between:0,10000',
                    'ol_hub_mcw_charges' => 'required|numeric|between:0,10000',
                    'ol_door_range_up.*' => 'required|numeric|between:0,10000',
                    'ol_door_range_down.*' => 'required|numeric|between:0,10000',
                    'ol_door_local_charges.*' => 'required|numeric',
                    'ol_door_class_0_charges.*' => 'required|numeric',
                    'ol_door_class_1_charges.*' => 'required',
                    'ol_door_class_2_charges.*' => 'required',
                    'ol_door_class_3_charges.*' => 'required',
                    'ol_hub_range_up.*' => 'required|numeric|between:0,10000',
                    'ol_hub_range_down.*' => 'required|numeric|between:0,10000',
                    'ol_hub_local_charges.*' => 'required|numeric',
                    'ol_hub_class_0_charges.*' => 'required|numeric',
                    'ol_hub_class_1_charges.*' => 'required',
                    'ol_hub_class_2_charges.*' => 'required',
                    'ol_hub_class_3_charges.*' => 'required',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_national_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',
                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                ];
            }
            //overland
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $detain_validations = [
                    'detain_door_mcw_charges' => 'required|numeric|between:0,10000',
                    'detain_hub_mcw_charges' => 'required|numeric|between:0,10000',
                    'detain_door_range_up.*' => 'required|numeric|between:0,10000',
                    'detain_door_range_down.*' => 'required|numeric|between:0,10000',
                    'detain_door_local_charges.*' => 'required|numeric',
                    'detain_door_class_0_charges.*' => 'required|numeric',
                    'detain_door_class_1_charges.*' => 'required',
                    'detain_door_class_2_charges.*' => 'required',
                    'detain_door_class_3_charges.*' => 'required',
                    'detain_hub_range_up.*' => 'required|numeric|between:0,10000',
                    'detain_hub_range_down.*' => 'required|numeric|between:0,10000',
                    'detain_hub_local_charges.*' => 'required|numeric',
                    'detain_hub_class_0_charges.*' => 'required|numeric',
                    'detain_hub_class_1_charges.*' => 'required',
                    'detain_hub_class_2_charges.*' => 'required',
                    'detain_hub_class_3_charges.*' => 'required',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_national_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',
                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                ];
            }
            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $sameday_validations = [
                    'sameday_door_mcw_charges' => 'required|numeric|between:0,10000',
                    'sameday_hub_mcw_charges' => 'required|numeric|between:0,10000',
                    'sameday_door_range_up.*' => 'required|numeric|between:0,10000',
                    'sameday_door_range_down.*' => 'required|numeric|between:0,10000',
                    'sameday_door_local_charges.*' => 'required|numeric',
                    'sameday_door_class_0_charges.*' => 'required|numeric',
                    'sameday_door_class_1_charges.*' => 'required',
                    'sameday_door_class_2_charges.*' => 'required',
                    'sameday_door_class_3_charges.*' => 'required',
                    'sameday_hub_range_up.*' => 'required|numeric|between:0,10000',
                    'sameday_hub_range_down.*' => 'required|numeric|between:0,10000',
                    'sameday_hub_local_charges.*' => 'required|numeric',
                    'sameday_hub_class_0_charges.*' => 'required|numeric',
                    'sameday_hub_class_1_charges.*' => 'required',
                    'sameday_hub_class_2_charges.*' => 'required',
                    'sameday_hub_class_3_charges.*' => 'required',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_national_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                ];
            }
            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }

            if ($request->on_rate_record != null) {
                CorporateRateStatus::where('id', $request->on_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => ($request->has('on_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                    ]);
            } else {
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'status' => ($request->has('on_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                ]);
            }
            if ($request->ol_rate_record != null) {
                CorporateRateStatus::where('id', $request->ol_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                    ]);
            } else {
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                ]);
            }
            if ($request->det_rate_record != null) {
                CorporateRateStatus::where('id', $request->det_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                    ]);
            } else {
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
                ]);
            }
            if ($request->same_rate_record != null) {
                CorporateRateStatus::where('id', $request->same_rate_record)
                    ->update([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                    ]);
            } else {
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                ]);
            }


            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $ONRateAlready = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->get();

                if (!$ONRateAlready->isEmpty()) {
                    if ($request->overnight_door_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->overnight_door_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'delivery_type_id' => 1,
                                'min_chargeable_weight' => $request->on_door_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'delivery_type_id' => 1,
                            'min_chargeable_weight' => $request->on_door_mcw_charges
                        ]);
                    }
                    if ($request->overnight_hub_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->overnight_hub_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'delivery_type_id' => 2,
                                'min_chargeable_weight' => $request->on_hub_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'delivery_type_id' => 2,
                            'min_chargeable_weight' => $request->on_hub_mcw_charges
                        ]);
                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 1])->whereNotIn('id', $request->on_door_weight_record)->delete();
                    foreach ($request->on_door_weight_record as $index => $on_door_weight_record) {

                        if ($request->on_door_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->on_door_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'delivery_type_id' => 1,
                                    'range_up' => $request->on_door_range_up[$index],
                                    'range_down' => $request->on_door_range_down[$index],
                                    'local_or_6hr' => $request->on_door_local_charges[$index],
                                    'national_charges_class_0' => $request->on_door_class_0_charges[$index],
                                    'national_charges_class_1' => $request->on_door_class_1_charges[$index],
                                    'national_charges_class_2' => $request->on_door_class_2_charges[$index],
                                    'national_charges_class_3' => $request->on_door_class_3_charges[$index]
                                ]);
                        }
                        if ($request->on_door_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'delivery_type_id' => 1,
                                'range_up' => $request->on_door_range_up[$index],
                                'range_down' => $request->on_door_range_down[$index],
                                'local_or_6hr' => $request->on_door_local_charges[$index],
                                'national_charges_class_0' => $request->on_door_class_0_charges[$index],
                                'national_charges_class_1' => $request->on_door_class_1_charges[$index],
                                'national_charges_class_2' => $request->on_door_class_2_charges[$index],
                                'national_charges_class_3' => $request->on_door_class_3_charges[$index]
                            ]);
                        }


                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 2])->whereNotIn('id', $request->on_hub_weight_record)->delete();
                    foreach ($request->on_hub_weight_record as $index => $on_hub_weight_record) {

                        if ($request->on_hub_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->on_hub_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'delivery_type_id' => 2,
                                    'range_up' => $request->on_hub_range_up[$index],
                                    'range_down' => $request->on_hub_range_down[$index],
                                    'local_or_6hr' => $request->on_hub_local_charges[$index],
                                    'national_charges_class_0' => $request->on_hub_class_0_charges[$index],
                                    'national_charges_class_1' => $request->on_hub_class_1_charges[$index],
                                    'national_charges_class_2' => $request->on_hub_class_2_charges[$index],
                                    'national_charges_class_3' => $request->on_hub_class_3_charges[$index]
                                ]);
                        }
                        if ($request->on_hub_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'delivery_type_id' => 2,
                                'range_up' => $request->on_hub_range_up[$index],
                                'range_down' => $request->on_hub_range_down[$index],
                                'local_or_6hr' => $request->on_hub_local_charges[$index],
                                'national_charges_class_0' => $request->on_hub_class_0_charges[$index],
                                'national_charges_class_1' => $request->on_hub_class_1_charges[$index],
                                'national_charges_class_2' => $request->on_hub_class_2_charges[$index],
                                'national_charges_class_3' => $request->on_hub_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharges::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_cash_record)->delete();

                        foreach ($request->on_cash_record as $index => $on_cash_record) {
                            if ($request->on_cash_record[$index] != null) {
                                CorporateCashHandlingCharges::where(['id' => $request->on_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                            if ($request->on_cash_record[$index] == null) {
                                CorporateCashHandlingCharges::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        CorporateInsuranceCharges::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_insurance_record)->delete();
                        foreach ($request->on_insurance_record as $insurance => $on_insurance_record) {
                            if ($request->on_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharges::where(['id' => $request->on_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_ins_range_up[$insurance],
                                    'range_down' => $request->on_ins_range_down[$insurance],
                                    'charges' => $request->on_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->on_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharges::create([
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
                    if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                        if ($request->on_return_record != null) {
                            CorporateReturnCharges::where(['id' => $request->on_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national' => $request->on_return_national_charges
                            ]);
                        } elseif ($request->on_return_record == null) {
                            CorporateReturnCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national' => $request->on_return_national_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                        if ($request->on_fuel_record != null) {
                            CorporateFuelSurcharge::where(['id' => $request->on_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'fuel_surcharge' => $request->overnight_fuel_surcharge
                            ]);
                        } elseif ($request->on_fuel_record == null) {
                            CorporateFuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'fuel_surcharge' => $request->overnight_fuel_surcharge
                            ]);
                        }

                    }
                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;

                    if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                        $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : 0;
//                    $discount_weight = $request->on_discount_weight_rate;
                    }
                    if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                        $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : 0;
                    }
                    if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : 0;
                    }
                    if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                        $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : 0;
                    }
                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0) {


                        $date_str = $request->on_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->on_discount_record != null) {
                            CorporateDiscountCharge::where(['id' => $request->on_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'title' => $request->on_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->on_discount_record == null) {
                            CorporateDiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'title' => $request->on_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }
                //dd($weightAlready);
            }

            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $OLRateAlready = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->get();

                if (!$OLRateAlready->isEmpty()) {
                    if ($request->overland_door_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->overland_door_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'delivery_type_id' => 1,
                                'min_chargeable_weight' => $request->ol_door_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'delivery_type_id' => 1,
                            'min_chargeable_weight' => $request->ol_door_mcw_charges
                        ]);
                    }
                    if ($request->overland_hub_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->overland_hub_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'delivery_type_id' => 2,
                                'min_chargeable_weight' => $request->ol_hub_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'delivery_type_id' => 2,
                            'min_chargeable_weight' => $request->ol_hub_mcw_charges
                        ]);
                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 1])->whereNotIn('id', $request->ol_door_weight_record)->delete();
                    foreach ($request->ol_door_weight_record as $index => $ol_door_weight_record) {

                        if ($request->ol_door_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->ol_door_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'delivery_type_id' => 1,
                                    'range_up' => $request->ol_door_range_up[$index],
                                    'range_down' => $request->ol_door_range_down[$index],
                                    'local_or_6hr' => $request->ol_door_local_charges[$index],
                                    'national_charges_class_0' => $request->ol_door_class_0_charges[$index],
                                    'national_charges_class_1' => $request->ol_door_class_1_charges[$index],
                                    'national_charges_class_2' => $request->ol_door_class_2_charges[$index],
                                    'national_charges_class_3' => $request->ol_door_class_3_charges[$index]
                                ]);
                        }
                        if ($request->ol_door_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'delivery_type_id' => 1,
                                'range_up' => $request->ol_door_range_up[$index],
                                'range_down' => $request->ol_door_range_down[$index],
                                'local_or_6hr' => $request->ol_door_local_charges[$index],
                                'national_charges_class_0' => $request->ol_door_class_0_charges[$index],
                                'national_charges_class_1' => $request->ol_door_class_1_charges[$index],
                                'national_charges_class_2' => $request->ol_door_class_2_charges[$index],
                                'national_charges_class_3' => $request->ol_door_class_3_charges[$index]
                            ]);
                        }


                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 2])->whereNotIn('id', $request->ol_hub_weight_record)->delete();
                    foreach ($request->ol_hub_weight_record as $index => $ol_hub_weight_record) {

                        if ($request->ol_hub_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->ol_hub_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'delivery_type_id' => 2,
                                    'range_up' => $request->ol_hub_range_up[$index],
                                    'range_down' => $request->ol_hub_range_down[$index],
                                    'local_or_6hr' => $request->ol_hub_local_charges[$index],
                                    'national_charges_class_0' => $request->ol_hub_class_0_charges[$index],
                                    'national_charges_class_1' => $request->ol_hub_class_1_charges[$index],
                                    'national_charges_class_2' => $request->ol_hub_class_2_charges[$index],
                                    'national_charges_class_3' => $request->ol_hub_class_3_charges[$index]
                                ]);
                        }
                        if ($request->ol_hub_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'delivery_type_id' => 2,
                                'range_up' => $request->ol_hub_range_up[$index],
                                'range_down' => $request->ol_hub_range_down[$index],
                                'local_or_6hr' => $request->ol_hub_local_charges[$index],
                                'national_charges_class_0' => $request->ol_hub_class_0_charges[$index],
                                'national_charges_class_1' => $request->ol_hub_class_1_charges[$index],
                                'national_charges_class_2' => $request->ol_hub_class_2_charges[$index],
                                'national_charges_class_3' => $request->ol_hub_class_3_charges[$index]
                            ]);
                        }


                    }


                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharges::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_cash_record)->delete();

                        foreach ($request->ol_cash_record as $index => $ol_cash_record) {
                            if ($request->ol_cash_record[$index] != null) {
                                CorporateCashHandlingCharges::where(['id' => $request->ol_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                            if ($request->ol_cash_record[$index] == null) {
                                CorporateCashHandlingCharges::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                        CorporateInsuranceCharges::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_insurance_record)->delete();
                        foreach ($request->ol_insurance_record as $insurance => $ol_insurance_record) {
                            if ($request->ol_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharges::where(['id' => $request->ol_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_ins_range_up[$insurance],
                                    'range_down' => $request->ol_ins_range_down[$insurance],
                                    'charges' => $request->ol_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->ol_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharges::create([
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
                    if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                        if ($request->ol_return_record != null) {
                            CorporateReturnCharges::where(['id' => $request->ol_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national' => $request->ol_return_national_charges
                            ]);
                        } elseif ($request->ol_return_record == null) {
                            CorporateReturnCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national' => $request->ol_return_national_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        if ($request->ol_fuel_record != null) {
                            CorporateFuelSurcharge::where(['id' => $request->ol_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'fuel_surcharge' => $request->overland_fuel_surcharge
                            ]);
                        } elseif ($request->ol_fuel_record == null) {
                            CorporateFuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'fuel_surcharge' => $request->overland_fuel_surcharge
                            ]);
                        }

                    }

                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : 0;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : 0;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : 0;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : 0;
                    }

                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0) {


                        $date_str = $request->ol_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->ol_discount_record != null) {
                            CorporateDiscountCharge::where(['id' => $request->ol_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'title' => $request->ol_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->ol_discount_record == null) {
                            CorporateDiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'title' => $request->ol_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }
            }

            //detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $DTRateAlready = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->get();

                if (!$DTRateAlready->isEmpty()) {
                    if ($request->detain_door_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->detain_door_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'delivery_type_id' => 1,
                                'min_chargeable_weight' => $request->detain_door_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'delivery_type_id' => 1,
                            'min_chargeable_weight' => $request->detain_door_mcw_charges
                        ]);
                    }
                    if ($request->detain_hub_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->detain_hub_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'delivery_type_id' => 2,
                                'min_chargeable_weight' => $request->detain_hub_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'delivery_type_id' => 2,
                            'min_chargeable_weight' => $request->detain_hub_mcw_charges
                        ]);
                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 1])->whereNotIn('id', $request->detain_door_weight_record)->delete();
                    foreach ($request->detain_door_weight_record as $index => $detain_door_weight_record) {

                        if ($request->detain_door_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->detain_door_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'delivery_type_id' => 1,
                                    'range_up' => $request->detain_door_range_up[$index],
                                    'range_down' => $request->detain_door_range_down[$index],
                                    'local_or_6hr' => $request->detain_door_local_charges[$index],
                                    'national_charges_class_0' => $request->detain_door_class_0_charges[$index],
                                    'national_charges_class_1' => $request->detain_door_class_1_charges[$index],
                                    'national_charges_class_2' => $request->detain_door_class_2_charges[$index],
                                    'national_charges_class_3' => $request->detain_door_class_3_charges[$index]
                                ]);
                        }
                        if ($request->detain_door_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'delivery_type_id' => 1,
                                'range_up' => $request->detain_door_range_up[$index],
                                'range_down' => $request->detain_door_range_down[$index],
                                'local_or_6hr' => $request->detain_door_local_charges[$index],
                                'national_charges_class_0' => $request->detain_door_class_0_charges[$index],
                                'national_charges_class_1' => $request->detain_door_class_1_charges[$index],
                                'national_charges_class_2' => $request->detain_door_class_2_charges[$index],
                                'national_charges_class_3' => $request->detain_door_class_3_charges[$index]
                            ]);
                        }


                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 2])->whereNotIn('id', $request->detain_hub_weight_record)->delete();
                    foreach ($request->detain_hub_weight_record as $index => $detain_hub_weight_record) {

                        if ($request->detain_hub_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->detain_hub_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'delivery_type_id' => 2,
                                    'range_up' => $request->detain_hub_range_up[$index],
                                    'range_down' => $request->detain_hub_range_down[$index],
                                    'local_or_6hr' => $request->detain_hub_local_charges[$index],
                                    'national_charges_class_0' => $request->detain_hub_class_0_charges[$index],
                                    'national_charges_class_1' => $request->detain_hub_class_1_charges[$index],
                                    'national_charges_class_2' => $request->detain_hub_class_2_charges[$index],
                                    'national_charges_class_3' => $request->detain_hub_class_3_charges[$index]
                                ]);
                        }
                        if ($request->detain_hub_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'delivery_type_id' => 2,
                                'range_up' => $request->detain_hub_range_up[$index],
                                'range_down' => $request->detain_hub_range_down[$index],
                                'local_or_6hr' => $request->detain_hub_local_charges[$index],
                                'national_charges_class_0' => $request->detain_hub_class_0_charges[$index],
                                'national_charges_class_1' => $request->detain_hub_class_1_charges[$index],
                                'national_charges_class_2' => $request->detain_hub_class_2_charges[$index],
                                'national_charges_class_3' => $request->detain_hub_class_3_charges[$index]
                            ]);
                        }


                    }

                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharges::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_cash_record)->delete();

                        foreach ($request->detain_cash_record as $index => $detain_cash_record) {
                            if ($request->detain_cash_record[$index] != null) {
                                CorporateCashHandlingCharges::where(['id' => $request->detain_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                            if ($request->detain_cash_record[$index] == null) {
                                CorporateCashHandlingCharges::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('detain_insurance_charges_switch') && $request->detain_insurance_charges_switch == 'on') {
                        CorporateInsuranceCharges::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_insurance_record)->delete();
                        foreach ($request->detain_insurance_record as $insurance => $detain_insurance_record) {
                            if ($request->detain_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharges::where(['id' => $request->detain_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_ins_range_up[$insurance],
                                    'range_down' => $request->detain_ins_range_down[$insurance],
                                    'charges' => $request->detain_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->detain_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharges::create([
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
                    if ($request->has('detain_return_switch') && $request->detain_return_switch == 'on') {
                        if ($request->detain_return_record != null) {
                            CorporateReturnCharges::where(['id' => $request->detain_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national' => $request->detain_return_national_charges
                            ]);
                        } elseif ($request->detain_return_record == null) {
                            CorporateReturnCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national' => $request->detain_return_national_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        if ($request->detain_fuel_record != null) {
                            CorporateFuelSurcharge::where(['id' => $request->detain_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'fuel_surcharge' => $request->detain_fuel_surcharge
                            ]);
                        } elseif ($request->detain_fuel_record == null) {
                            CorporateFuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'fuel_surcharge' => $request->detain_fuel_surcharge
                            ]);
                        }

                    }

                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;

                    if ($request->has('detain_discount_weight_switch') && $request->detain_discount_weight_switch == 'on') {
                        $discount_weight = $request->detain_discount_weight_rate != null ? $request->detain_discount_weight_rate : 0;
//                    $discount_weight = $request->detain_discount_weight_rate;
                    }
                    if ($request->has('detain_discount_cash_switch') && $request->detain_discount_cash_switch == 'on') {
                        $discount_cash = $request->detain_discount_cash_rate != null ? $request->detain_discount_cash_rate : 0;
                    }
                    if ($request->has('detain_discount_insurance_switch') && $request->detain_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->detain_discount_insurance_rate != null ? $request->detain_discount_insurance_rate : 0;
                    }
                    if ($request->has('detain_discount_return_switch') && $request->detain_discount_return_switch == 'on') {
                        $discount_return = $request->detain_discount_return_rate != null ? $request->detain_discount_insurance_rate : 0;
                    }

                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0) {


                        $date_str = $request->detain_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->detain_discount_record != null) {
                            CorporateDiscountCharge::where(['id' => $request->detain_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'title' => $request->detain_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->detain_discount_record == null) {
                            CorporateDiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'title' => $request->detain_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }

            }

            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $SDRateAlready = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->get();

                if (!$SDRateAlready->isEmpty()) {
                    if ($request->sameday_door_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->sameday_door_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'delivery_type_id' => 1,
                                'min_chargeable_weight' => $request->sameday_door_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'delivery_type_id' => 1,
                            'min_chargeable_weight' => $request->sameday_door_mcw_charges
                        ]);
                    }
                    if ($request->sameday_hub_min_chargeable_weight != null) {
                        CorporateMinChargeableWeight::where('id', $request->sameday_hub_min_chargeable_weight)
                            ->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'delivery_type_id' => 2,
                                'min_chargeable_weight' => $request->sameday_hub_mcw_charges
                            ]);
                    } else {
                        CorporateMinChargeableWeight::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'delivery_type_id' => 2,
                            'min_chargeable_weight' => $request->sameday_hub_mcw_charges
                        ]);
                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 1])->whereNotIn('id', $request->sameday_door_weight_record)->delete();
                    foreach ($request->sameday_door_weight_record as $index => $sameday_door_weight_record) {

                        if ($request->sameday_door_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->sameday_door_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'delivery_type_id' => 1,
                                    'range_up' => $request->sameday_door_range_up[$index],
                                    'range_down' => $request->sameday_door_range_down[$index],
                                    'local_or_6hr' => $request->sameday_door_local_charges[$index],
                                    'national_charges_class_0' => $request->sameday_door_class_0_charges[$index],
                                    'national_charges_class_1' => 0,
                                    'national_charges_class_2' => 0,
                                    'national_charges_class_3' => 0
                                ]);
                        }
                        if ($request->sameday_door_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'delivery_type_id' => 1,
                                'range_up' => $request->sameday_door_range_up[$index],
                                'range_down' => $request->sameday_door_range_down[$index],
                                'local_or_6hr' => $request->sameday_door_local_charges[$index],
                                'national_charges_class_0' => $request->sameday_door_class_0_charges[$index],
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        }


                    }
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 2])->whereNotIn('id', $request->sameday_hub_weight_record)->delete();
                    foreach ($request->sameday_hub_weight_record as $index => $sameday_hub_weight_record) {

                        if ($request->sameday_hub_weight_record[$index] != null) {

                            $weight_row = CorporateWeightCharge::where('id', $request->sameday_hub_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'delivery_type_id' => 2,
                                    'range_up' => $request->sameday_hub_range_up[$index],
                                    'range_down' => $request->sameday_hub_range_down[$index],
                                    'local_or_6hr' => $request->sameday_hub_local_charges[$index],
                                    'national_charges_class_0' => $request->sameday_hub_class_0_charges[$index],
                                    'national_charges_class_1' => 0,
                                    'national_charges_class_2' => 0,
                                    'national_charges_class_3' => 0
                                ]);
                        }
                        if ($request->sameday_hub_weight_record[$index] == null) {
                            CorporateWeightCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'delivery_type_id' => 2,
                                'range_up' => $request->sameday_hub_range_up[$index],
                                'range_down' => $request->sameday_hub_range_down[$index],
                                'local_or_6hr' => $request->sameday_hub_local_charges[$index],
                                'national_charges_class_0' => $request->sameday_hub_class_0_charges[$index],
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        }


                    }

                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharges::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_cash_record)->delete();

                        foreach ($request->sameday_cash_record as $index => $sameday_cash_record) {
                            if ($request->sameday_cash_record[$index] != null) {
                                CorporateCashHandlingCharges::where(['id' => $request->sameday_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                            if ($request->sameday_cash_record[$index] == null) {
                                CorporateCashHandlingCharges::create([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                        }
                    }
                    //insurance charges
                    if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                        CorporateInsuranceCharges::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_insurance_record)->delete();
                        foreach ($request->sameday_insurance_record as $insurance => $sameday_insurance_record) {
                            if ($request->sameday_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharges::where(['id' => $request->sameday_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_ins_range_up[$insurance],
                                    'range_down' => $request->sameday_ins_range_down[$insurance],
                                    'charges' => $request->sameday_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->sameday_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharges::create([
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
                    if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                        if ($request->sameday_return_record != null) {
                            CorporateReturnCharges::where(['id' => $request->sameday_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national' => $request->sameday_return_national_charges
                            ]);
                        } elseif ($request->sameday_return_record == null) {
                            CorporateReturnCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national' => $request->sameday_return_national_charges
                            ]);
                        }

                    }
                    //Return Charges
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        if ($request->sameday_fuel_record != null) {
                            CorporateFuelSurcharge::where(['id' => $request->sameday_fuel_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'fuel_surcharge' => $request->sameday_fuel_surcharge
                            ]);
                        } elseif ($request->sameday_fuel_record == null) {
                            CorporateFuelSurcharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'fuel_surcharge' => $request->sameday_fuel_surcharge
                            ]);
                        }

                    }

                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;

                    if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                        $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : 0;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                    }
                    if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                        $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : 0;
                    }
                    if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : 0;
                    }
                    if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                        $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : 0;
                    }

                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0) {


                        $date_str = $request->sameday_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();

                        if ($request->sameday_discount_record != null) {
                            CorporateDiscountCharge::where(['id' => $request->sameday_discount_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'title' => $request->sameday_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        } elseif ($request->sameday_discount_record == null) {
                            CorporateDiscountCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'title' => $request->sameday_discount_title,
                                'weight' => $discount_weight,
                                'cash' => $discount_cash,
                                'insurance' => $discount_insurance,
                                'return' => $discount_return,
                                'to' => $to,
                                'from' => $from,
                                'added_by' => Auth::id()
                            ]);
                        }

                    }

                }

            }
            User::where('id', $id)->update(['rate_status' => 1]);
            if ($request->authorize == 1) {
                User::where('id', $id)->update(['rate_status' => 0, 'status' => 2, 'rates_authorized_by' => Auth::id()]);
                return redirect(route('admin.accounts.pending'))->with('success', 'User is now authorized.');
            }

            return redirect()->back()->with('success', 'All Rates are updated');
        }

        if ($user['status'] == 3) {
            $messages = [
                'on_wa_range_up.*.required' => 'The overnight range up field is required.',
                'on_wa_range_up.*.numeric' => 'The overnight range up field must be numeric or decimal.',
                'on_wa_range_up.*.between' => 'The overnight range up field must be between 0 to 999.99',
                'on_wa_range_down.*.required' => 'The overnight range down field is required.',
                'on_wa_range_down.*.numeric' => 'The overnight range down field must be numeric or decimal.',
                'on_wa_range_down.*.between' => 'The overnight range down field must be between 0 to 999.99',
                'on_wa_spkg.*.numeric' => 'The overnight KG Range field must be numeric.',
                'on_wa_local_charges.*.required' => 'The overnight local charges field is required.',
                'on_wa_local_charges.*.numeric' => 'The overnight local charges field must be numeric.',
                'on_class_0_charges.*.numeric' => 'The overnight class A charges field must be numeric.',
                'on_class_0_charges.*.required' => 'The overnight class A charges field is required.',
                'on_class_1_charges.*.required' => 'The overnight class B charges field is required.',
                'on_class_2_charges.*.required' => 'The overnight class C charges field is required.',
                'on_class_3_charges.*.required' => 'The overnight class D charges field is required.',
//            'on_class_0_charges.*.numeric' => 'The overnight national charges field must be numeric.',
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
//            'on_discount_weight_rate.numeric' => 'The overnight discount weight field must be numeric',
                'on_discount_cash_rate.required_if' => 'The overnight discount cash field must be required',
//            'on_discount_cash_rate.numeric' => 'The overnight discount cash field must be numeric',
                'on_discount_insurance_rate.required_if' => 'The overnight discount insurance field must be required',
//            'on_discount_insurance_rate.numeric' => 'The overnight discount insurance field must be numeric',
                'on_discount_return_rate.required_if' => 'The overnight discount return field must be required',
//            'on_discount_return_rate.numeric' => 'The overnight discount return field must be numeric',
                'on_discount_packaging_rate.required_if' => 'The overnight discount packaging field must be required',
//            'on_discount_packaging_rate.numeric' => 'The overnight discount packaging field must be numeric',
                'on_discount_title.required_with' => 'The overnight discount title field is required',
                'on_daterange.required_with' => 'The overnight discount date field is required',
                //overland starts
                'ol_wa_range_up.*.required' => 'The overland range up field is required.',
                'ol_wa_range_up.*.numeric' => 'The overland range up field must be numeric or decimal.',
                'ol_wa_range_up.*.between' => 'The overland range up field must be between 0 to 999.99',
                'ol_wa_range_down.*.required' => 'The overland range down field is required.',
                'ol_wa_range_down.*.numeric' => 'The overland range down field must be numeric or decimal.',
                'ol_wa_range_down.*.between' => 'The overland range down field must be between 0 to 999.99',
                'ol_wa_spkg.*.numeric' => 'The overland KG Range field must be numeric.',
                'ol_wa_local_charges.*.required' => 'The overland local charges field is required.',
                'ol_wa_local_charges.*.numeric' => 'The overland local charges field must be numeric.',
                'ol_class_0_charges.*.required' => 'The overland class A charges field is required.',
                'ol_class_0_charges.*.numeric' => 'The overland class A charges field must be numeric.',
                'ol_class_1_charges.*.required' => 'The overland class B charges field is required.',
                'ol_class_2_charges.*.required' => 'The overland class C charges field is required.',
                'ol_class_3_charges.*.required' => 'The overland class D charges field is required.',
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
//            'ol_discount_weight_rate.numeric' => 'The overland discount weight field must be numeric',
                'ol_discount_cash_rate.required_if' => 'The overland discount cash field must be required',
//            'ol_discount_cash_rate.numeric' => 'The overland discount cash field must be numeric',
                'ol_discount_insurance_rate.required_if' => 'The overland discount insurance field must be required',
//            'ol_discount_insurance_rate.numeric' => 'The overland discount insurance field must be numeric',
                'ol_discount_return_rate.required_if' => 'The overland discount return field must be required',
//            'ol_discount_return_rate.numeric' => 'The overland discount return field must be numeric',
                'ol_discount_packaging_rate.required_if' => 'The overland discount packaging field must be required',
//            'ol_discount_packaging_rate.numeric' => 'The overland discount packaging field must be numeric',
                'ol_discount_title.required_with' => 'The overland discount title field is required',
                'ol_daterange.required_with' => 'The overland discount date field is required',
                //overland end and detain starts
                'detain_wa_range_up.*.required' => 'The detain range up field is required.',
                'detain_wa_range_up.*.numeric' => 'The detain range up field must be numeric or decimal.',
                'detain_wa_range_up.*.between' => 'The detain range up field must be between 0 to 999.99',
                'detain_wa_range_down.*.required' => 'The detain range down field is required.',
                'detain_wa_range_down.*.numeric' => 'The detain range down field must be numeric or decimal.',
                'detain_wa_range_down.*.between' => 'The detain range down field must be between 0 to 999.99',
                'detain_wa_spkg.*.numeric' => 'The detain KG Range field must be numeric.',
                'detain_wa_local_charges.*.required' => 'The detain local charges field is required.',
                'detain_wa_local_charges.*.numeric' => 'The detain local charges field must be numeric.',
                'detain_class_0_charges.*.required' => 'The detain class A charges field is required.',
                'detain_class_0_charges.*.numeric' => 'The detain class A charges field must be numeric.',
                'detain_class_1_charges.*.required' => 'The detain class B charges field is required.',
                'detain_class_2_charges.*.required' => 'The detain class C charges field is required.',
                'detain_class_3_charges.*.required' => 'The detain class D charges field is required.',
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
//            'detain_discount_weight_rate.numeric' => 'The detain discount weight field must be numeric',
                'detain_discount_cash_rate.required_if' => 'The detain discount cash field must be required',
//            'detain_discount_cash_rate.numeric' => 'The detain discount cash field must be numeric',
                'detain_discount_insurance_rate.required_if' => 'The detain discount insurance field must be required',
//            'detain_discount_insurance_rate.numeric' => 'The detain discount insurance field must be numeric',
                'detain_discount_return_rate.required_if' => 'The detain discount return field must be required',
//            'detain_discount_return_rate.numeric' => 'The detain discount return field must be numeric',
                'detain_discount_packaging_rate.required_if' => 'The detain discount packaging field must be required',
//            'detain_discount_packaging_rate.numeric' => 'The detain discount packaging field must be numeric',
                'detain_discount_title.required_with' => 'The detain discount title field is required',
                'detain_daterange.required_with' => 'The detain discount date field is required',
                //detain ends and sameday starts
                'sameday_wa_range_up.*.required' => 'The sameday range up field is required.',
                'sameday_wa_range_up.*.numeric' => 'The sameday range up field must be numeric or decimal.',
                'sameday_wa_range_up.*.between' => 'The sameday range up field must be between 0 to 999.99',
                'sameday_wa_range_down.*.required' => 'The sameday range down field is required.',
                'sameday_wa_range_down.*.numeric' => 'The sameday range down field must be numeric or decimal.',
                'sameday_wa_range_down.*.between' => 'The sameday range down field must be between 0 to 999.99',
                'sameday_wa_spkg.*.numeric' => 'The sameday KG Range field must be numeric.',
                'sameday_wa_local_charges.*.required' => 'The sameday local charges field is required.',
                'sameday_wa_local_charges.*.numeric' => 'The sameday local charges field must be numeric.',
                'sameday_wa_class_0_charges.*.required' => 'The sameday class A charges field is required.',
                'sameday_wa_class_0_charges.*.numeric' => 'The sameday class A charges field must be numeric.',
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
//            'sameday_discount_weight_rate.numeric' => 'The sameday discount weight field must be numeric',
                'sameday_discount_cash_rate.required_if' => 'The sameday discount cash field must be required',
//            'sameday_discount_cash_rate.numeric' => 'The sameday discount cash field must be numeric',
                'sameday_discount_insurance_rate.required_if' => 'The sameday discount insurance field must be required',
//            'sameday_discount_insurance_rate.numeric' => 'The sameday discount insurance field must be numeric',
                'sameday_discount_return_rate.required_if' => 'The sameday discount return field must be required',
//            'sameday_discount_return_rate.numeric' => 'The sameday discount return field must be numeric',
                'sameday_discount_packaging_rate.required_if' => 'The sameday discount packaging field must be required',
//            'sameday_discount_packaging_rate.numeric' => 'The sameday discount packaging field must be numeric',
                'sameday_discount_title.required_with' => 'The sameday discount title field is required',
                'sameday_daterange.required_with' => 'The sameday discount date field is required',
                //sameday ends
            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $on_validations = [
                    'on_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'on_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'on_wa_local_charges.*' => 'required|numeric',
                    'on_class_0_charges.*' => 'required|numeric',
                    'on_class_1_charges.*' => 'required',
                    'on_class_2_charges.*' => 'required',
                    'on_class_3_charges.*' => 'required',
                    'on_wa_spkg.*' => 'numeric',
                    'on_replacement_charges' => 'required|numeric',
                    'on_tnb_charges' => 'required|numeric',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_national_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'overnight_fuel_surcharge' => 'required_if:overnight_fuel_switch,==,on|numeric',
                    'on_flyer_sm' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_md' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_lg' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_flyer_box' => 'required_if:on_packaging_switch,==,on|numeric',
                    'on_discount_title' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_daterange' => 'required_with:on_discount_weight_rate,on_discount_cash_rate,on_discount_insurance_rate,on_discount_return_rate,on_discount_packaging_rate',
                    'on_discount_weight_rate' => 'required_if:on_discount_weight_switch,==,on',
                    'on_discount_cash_rate' => 'required_if:on_discount_cash_switch,==,on',
                    'on_discount_insurance_rate' => 'required_if:on_discount_insurance_switch,==,on',
                    'on_discount_return_rate' => 'required_if:on_discount_return_switch,==,on',
                    'on_discount_packaging_rate' => 'required_if:on_discount_packaging_switch,==,on'
                ];
            }
            //overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                $ol_validations = [
                    'ol_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'ol_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'ol_wa_local_charges.*' => 'required|numeric',
                    'ol_class_0_charges.*' => 'required|numeric',
                    'ol_class_1_charges.*' => 'required',
                    'ol_class_2_charges.*' => 'required',
                    'ol_class_3_charges.*' => 'required',
                    'ol_wa_spkg.*' => 'numeric',
                    'ol_replacement_charges' => 'required|numeric',
                    'ol_tnb_charges' => 'required|numeric',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_national_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'overland_fuel_surcharge' => 'required_if:overland_fuel_switch,==,on|numeric',
                    'ol_flyer_sm' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_md' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_lg' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_flyer_box' => 'required_if:ol_packaging_switch,==,on|numeric',
                    'ol_discount_title' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_daterange' => 'required_with:ol_discount_weight_rate,ol_discount_cash_rate,ol_discount_insurance_rate,ol_discount_return_rate,ol_discount_packaging_rate',
                    'ol_discount_weight_rate' => 'required_if:ol_discount_weight_switch,==,on',
                    'ol_discount_cash_rate' => 'required_if:ol_discount_cash_switch,==,on',
                    'ol_discount_insurance_rate' => 'required_if:ol_discount_insurance_switch,==,on',
                    'ol_discount_return_rate' => 'required_if:ol_discount_return_switch,==,on',
                    'ol_discount_packaging_rate' => 'required_if:ol_discount_packaging_switch,==,on',
                ];
            }
            //overland
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {
                $detain_validations = [
                    'detain_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'detain_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'detain_wa_local_charges.*' => 'required|numeric',
                    'detain_class_0_charges.*' => 'required|numeric',
                    'detain_class_1_charges.*' => 'required',
                    'detain_class_2_charges.*' => 'required',
                    'detain_class_3_charges.*' => 'required',
                    'detain_wa_spkg.*' => 'numeric',
                    'detain_replacement_charges' => 'required|numeric',
                    'detain_tnb_charges' => 'required|numeric',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_national_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_fuel_surcharge' => 'required_if:detain_fuel_switch,==,on|numeric',
                    'detain_flyer_sm' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_md' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_lg' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_flyer_box' => 'required_if:detain_packaging_switch,==,on|numeric',
                    'detain_discount_title' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_daterange' => 'required_with:detain_discount_weight_rate,detain_discount_cash_rate,detain_discount_insurance_rate,detain_discount_return_rate,detain_discount_packaging_rate',
                    'detain_discount_weight_rate' => 'required_if:detain_discount_weight_switch,==,on',
                    'detain_discount_cash_rate' => 'required_if:detain_discount_cash_switch,==,on',
                    'detain_discount_insurance_rate' => 'required_if:detain_discount_insurance_switch,==,on',
                    'detain_discount_return_rate' => 'required_if:detain_discount_return_switch,==,on',
                    'detain_discount_packaging_rate' => 'required_if:detain_discount_packaging_switch,==,on',
                ];
            }
            //sameday
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {
                $sameday_validations = [
                    'sameday_wa_range_up.*' => 'required|numeric|between:0,10000',
                    'sameday_wa_range_down.*' => 'required|numeric|between:0,10000',
                    'sameday_wa_local_charges.*' => 'required|numeric',
                    'sameday_class_0_charges.*' => 'required|numeric',
                    'sameday_wa_spkg.*' => 'numeric',
                    'sameday_replacement_charges' => 'required|numeric',
                    'sameday_tnb_charges' => 'required|numeric',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_national_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                    'sameday_flyer_sm' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_md' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_lg' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_flyer_box' => 'required_if:sameday_packaging_switch,==,on|numeric',
                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                    'sameday_discount_packaging_rate' => 'required_if:sameday_discount_packaging_switch,==,on',
                ];
            }

            $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

            $validate = Validator::make($request->all(), $validations, $messages);

            if ($validate->fails()) {
                return redirect()->back()
                    ->withErrors($validate)
                    ->withInput();
            }
            PendingCorporateRateStatus::where('user_id', $id)->delete();
            PendingCorporateWeightCharge::where('user_id', $id)->delete();
            PendingCorporateCashHandlingCharges::where('user_id', $id)->delete();
            PendingCorporateInsuranceCharges::where('user_id', $id)->delete();
            PendingCorporateReturnCharges::where('user_id', $id)->delete();
            PendingCorporateFuelSurcharge::where('user_id', $id)->delete();
            PendingCorporateDiscountCharge::where('user_id', $id)->delete();

            if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
                $ONRateAlready = PendingCorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 1)->get();
                if ($ONRateAlready->isEmpty()) {
                    PendingCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => ($request->has('on_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
//                   'fuel_charges'=> ($request->has('overnight_fuel_switch'))? 1:0,
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('on_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch = array();
                    $wa_spkg = array();
                    foreach ($request->on_wa_range_up as $index => $on_wa_range_up) {
                        if ($request->has('on_wa_switch')) {
                            if (array_key_exists($index, $request->on_wa_switch)) {
                                $wa_switch[$index] = 1;
                            } else {
                                $wa_switch[$index] = 0;
                            };
                        } else {
                            $wa_switch[$index] = 0;
                        }
                        if ($request->has('on_wa_spkg')) {
                            if (array_key_exists($index, $request->on_wa_spkg)) {
                                $wa_spkg[$index] = $request->on_wa_spkg[$index];
                            } else {
                                $wa_spkg[$index] = 0;
                            };
                        } else {
                            $wa_spkg[$index] = 0;
                        }
                        PendingCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $request->on_wa_range_up[$index],
                            'range_down' => $request->on_wa_range_down[$index],
                            'weight_addition' => $wa_switch[$index],
                            'spkg' => $wa_spkg[$index],
                            'local_or_6hr' => $request->on_wa_local_charges[$index],
                            'national_charges_class_0' => $request->on_class_0_charges[$index],
                            'national_charges_class_1' => $request->on_class_1_charges[$index],
                            'national_charges_class_2' => $request->on_class_2_charges[$index],
                            'national_charges_class_3' => $request->on_class_3_charges[$index]
                        ]);

                    }

                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        foreach ($request->on_cash_range_up as $ind => $on_cash_range_up) {
                            PendingCorporateCashHandlingCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_cash_range_up[$ind],
                                'range_down' => $request->on_cash_range_down[$ind],
                                'charges' => $request->on_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up) {
                            PendingCorporateInsuranceCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_ins_range_up[$insurance],
                                'range_down' => $request->on_ins_range_down[$insurance],
                                'charges' => $request->on_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('on_return_switch') && $request->on_return_switch == 'on') {
                        PendingCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $request->on_return_local_charges,
                            'national' => $request->on_return_national_charges
                        ]);
                    }
                    //Return Charges
                    if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $request->overnight_fuel_surcharge
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('on_discount_weight_switch') && $request->on_discount_weight_switch == 'on') {
                        $discount_weight = $request->on_discount_weight_rate != null ? $request->on_discount_weight_rate : null;
//                    $discount_weight = $request->on_discount_weight_rate;
                    }
                    if ($request->has('on_discount_cash_switch') && $request->on_discount_cash_switch == 'on') {
                        $discount_cash = $request->on_discount_cash_rate != null ? $request->on_discount_cash_rate : null;
                    }
                    if ($request->has('on_discount_insurance_switch') && $request->on_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->on_discount_insurance_rate != null ? $request->on_discount_insurance_rate : null;
                    }
                    if ($request->has('on_discount_return_switch') && $request->on_discount_return_switch == 'on') {
                        $discount_return = $request->on_discount_return_rate != null ? $request->on_discount_insurance_rate : null;
                    }
                    if ($request->has('on_discount_packaging_switch') && $request->on_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->on_discount_packaging_rate != null ? $request->on_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->on_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $request->on_discount_title,
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
                //dd($weightAlready);
            }
            //Overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {

                $OLRatePresent = PendingCorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 2)->get();

                if ($OLRatePresent->isEmpty()) {
                    PendingCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('ol_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch_overland = array();
                    $wa_spkg_overland = array();
                    foreach ($request->ol_wa_range_up as $index => $ol_wa_range_up) {
                        if ($request->has('ol_wa_switch')) {
                            if (array_key_exists($index, $request->ol_wa_switch)) {
                                $wa_switch_overland[$index] = 1;
                            } else {
                                $wa_switch_overland[$index] = 0;
                            };
                        } else {
                            $wa_switch_overland[$index] = 0;
                        }
                        if ($request->has('ol_wa_spkg')) {
                            if (array_key_exists($index, $request->ol_wa_spkg)) {
                                $wa_spkg_overland[$index] = $request->ol_wa_spkg[$index];
                            } else {
                                $wa_spkg_overland[$index] = 0;
                            };
                        } else {
                            $wa_spkg_overland[$index] = 0;
                        }
                        PendingCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $request->ol_wa_range_up[$index],
                            'range_down' => $request->ol_wa_range_down[$index],
                            'weight_addition' => $wa_switch_overland[$index],
                            'spkg' => $wa_spkg_overland[$index],
                            'local_or_6hr' => $request->ol_wa_local_charges[$index],
                            'national_charges_class_0' => $request->ol_class_0_charges[$index],
                            'national_charges_class_1' => $request->ol_class_1_charges[$index],
                            'national_charges_class_2' => $request->ol_class_2_charges[$index],
                            'national_charges_class_3' => $request->ol_class_3_charges[$index]
                        ]);
                    }
                    
                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up) {
                            PendingCorporateCashHandlingCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_cash_range_up[$ind],
                                'range_down' => $request->ol_cash_range_down[$ind],
                                'charges' => $request->ol_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('ol_insurance_charges_switch') && $request->ol_insurance_charges_switch == 'on') {
                        foreach ($request->ol_ins_range_up as $insurance => $ol_ins_range_up) {
                            PendingCorporateInsuranceCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'range_up' => $request->ol_ins_range_up[$insurance],
                                'range_down' => $request->ol_ins_range_down[$insurance],
                                'charges' => $request->ol_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('ol_return_switch') && $request->ol_return_switch == 'on') {
                        PendingCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $request->ol_return_local_charges,
                            'national' => $request->ol_return_national_charges
                        ]);
                    }
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $request->overland_fuel_surcharge
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != null ? $request->ol_discount_weight_rate : null;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != null ? $request->ol_discount_cash_rate : null;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != null ? $request->ol_discount_insurance_rate : null;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != null ? $request->ol_discount_insurance_rate : null;
                    }
                    if ($request->has('ol_discount_packaging_switch') && $request->ol_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->ol_discount_packaging_rate != null ? $request->ol_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->ol_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $request->ol_discount_title,
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
            //Detain
            if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

                $DetainRatePresent = PendingCorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

                if ($DetainRatePresent->isEmpty()) {
                    PendingCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
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
                        PendingCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $request->detain_wa_range_up[$index],
                            'range_down' => $request->detain_wa_range_down[$index],
                            'weight_addition' => $wa_switch_detain[$index],
                            'spkg' => $wa_spkg_detain[$index],
                            'local_or_6hr' => $request->detain_wa_local_charges[$index],
                            'national_charges_class_0' => $request->detain_class_0_charges[$index],
                            'national_charges_class_1' => $request->detain_class_1_charges[$index],
                            'national_charges_class_2' => $request->detain_class_2_charges[$index],
                            'national_charges_class_3' => $request->detain_class_3_charges[$index]
                        ]);
                    }

                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                            PendingCorporateCashHandlingCharges::create([
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
                            PendingCorporateInsuranceCharges::create([
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
                        PendingCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $request->detain_return_local_charges,
                            'national' => $request->detain_return_national_charges
                        ]);
                    }
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $request->detain_fuel_surcharge
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
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingCorporateDiscountCharge::create([
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
            if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {

                $SamedayRatePresent = PendingCorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 4)->get();
                if ($SamedayRatePresent->isEmpty()) {
                    PendingCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                        'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                        'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                        'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                        'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
                        'packaging_charges' => ($request->has('sameday_packaging_switch')) ? 1 : 0
                    ]);
                    $wa_switch_sameday = array();
                    $wa_spkg_sameday = array();
                    foreach ($request->sameday_wa_range_up as $index => $sameday_wa_range_up) {
                        if ($request->has('sameday_wa_switch')) {
                            if (array_key_exists($index, $request->sameday_wa_switch)) {
                                $wa_switch_sameday[$index] = 1;
                            } else {
                                $wa_switch_sameday[$index] = 0;
                            };
                        } else {
                            $wa_switch_sameday[$index] = 0;
                        }
                        if ($request->has('sameday_wa_spkg')) {
                            if (array_key_exists($index, $request->sameday_wa_spkg)) {
                                $wa_spkg_sameday[$index] = $request->sameday_wa_spkg[$index];
                            } else {
                                $wa_spkg_sameday[$index] = 0;
                            };
                        } else {
                            $wa_spkg_sameday[$index] = 0;
                        }
                        PendingCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $request->sameday_wa_range_up[$index],
                            'range_down' => $request->sameday_wa_range_down[$index],
                            'weight_addition' => $wa_switch_sameday[$index],
                            'spkg' => $wa_spkg_sameday[$index],
                            'local_or_6hr' => $request->sameday_wa_local_charges[$index],
                            'national_charges_class_0' => $request->sameday_class_0_charges[$index],
                            'national_charges_class_1' => 0,
                            'national_charges_class_2' => 0,
                            'national_charges_class_3' => 0
                        ]);
                    }
                    
                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up) {
                            PendingCorporateCashHandlingCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_cash_range_up[$ind],
                                'range_down' => $request->sameday_cash_range_down[$ind],
                                'charges' => $request->sameday_cash_charges[$ind]
                            ]);
                        }
                    }
                    //insurance charges
                    if ($request->has('sameday_insurance_charges_switch') && $request->sameday_insurance_charges_switch == 'on') {
                        foreach ($request->sameday_ins_range_up as $insurance => $sameday_ins_range_up) {
                            PendingCorporateInsuranceCharges::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'range_up' => $request->sameday_ins_range_up[$insurance],
                                'range_down' => $request->sameday_ins_range_down[$insurance],
                                'charges' => $request->sameday_ins_charges[$insurance]
                            ]);
                        }
                    }
                    //Return Charges
                    if ($request->has('sameday_return_switch') && $request->sameday_return_switch == 'on') {
                        PendingCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $request->sameday_return_local_charges,
                            'national' => $request->sameday_return_national_charges
                        ]);
                    }
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $request->sameday_fuel_surcharge
                        ]);
                    }
                    $discount_cash = null;
                    $discount_weight = null;
                    $discount_insurance = null;
                    $discount_return = null;
                    $discount_packaging = null;

                    if ($request->has('sameday_discount_weight_switch') && $request->sameday_discount_weight_switch == 'on') {
                        $discount_weight = $request->sameday_discount_weight_rate != null ? $request->sameday_discount_weight_rate : null;
//                    $discount_weight = $request->sameday_discount_weight_rate;
                    }
                    if ($request->has('sameday_discount_cash_switch') && $request->sameday_discount_cash_switch == 'on') {
                        $discount_cash = $request->sameday_discount_cash_rate != null ? $request->sameday_discount_cash_rate : null;
                    }
                    if ($request->has('sameday_discount_insurance_switch') && $request->sameday_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->sameday_discount_insurance_rate != null ? $request->sameday_discount_insurance_rate : null;
                    }
                    if ($request->has('sameday_discount_return_switch') && $request->sameday_discount_return_switch == 'on') {
                        $discount_return = $request->sameday_discount_return_rate != null ? $request->sameday_discount_insurance_rate : null;
                    }
                    if ($request->has('sameday_discount_packaging_switch') && $request->sameday_discount_packaging_switch == 'on') {
                        $discount_packaging = $request->sameday_discount_packaging_rate != null ? $request->sameday_discount_packaging_rate : null;
                    }
                    if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null || $discount_packaging != null) {


                        $date_str = $request->sameday_daterange;
                        $date_sep = explode(' - ', $date_str);
                        $date_to = explode('/', $date_sep[0]);
                        $date_from = explode('/', $date_sep[1]);
                        $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                        $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


                        PendingCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $request->sameday_discount_title,
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
            //dd($weightAlready);

            if ($request->approve == 1) {
                $user = User::find($id);

                if($switches = CorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 1])->first()) {

                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = CorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 2])->first()) {
                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = CorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 3])->first()) {
                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($switches = CorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 4])->first()) {
                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'packaging_charges' => $switches['packaging_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if($weights = CorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($weights = CorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($weights = CorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if($weights = CorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'weight_addition' => $weight['weight_addition'],
                            'spkg' => $weight['spkg'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }

                if($cashs = CorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($cashs = CorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($cashs = CorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($cashs = CorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if($insurances = CorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($insurances = CorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($insurances = CorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($insurances = CorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if($returns = CorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $return['local'],
                            'national' => $return['national']
                        ]);
                    }
                }
                if($returns = CorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $return['local'],
                            'national' => $return['national']
                        ]);
                    }
                }
                if($returns = CorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $return['local'],
                            'national' => $return['national']
                        ]);
                    }
                }
                if($returns = CorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $return['local'],
                            'national' => $return['national']
                        ]);
                    }
                }
                if($fuels = CorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = CorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = CorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($fuels = CorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if($discounts = CorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if($discounts = CorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if($discounts = CorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if($discounts = CorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'packaging' => $discount['packaging'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                $s = CorporateRateStatus::where(['user_id' => $id ])->first();
                CorporateRateHistory::create([
                    'user_id' => $id,
                    'updated_by' => $user['rates_updated_by'],
                    'approved_by' => $user['rates_authorized_by'],
                    'from_date' => $s['created_at'],
                    'to_date' => Carbon::now()
                ]);

                CorporateRateStatus::where('user_id', $id)->delete();
                CorporateWeightCharge::where('user_id', $id)->delete();
                CorporateCashHandlingCharges::where('user_id', $id)->delete();
                CorporateInsuranceCharges::where('user_id', $id)->delete();
                CorporateReturnCharges::where('user_id', $id)->delete();
                CorporateFuelSurcharge::where('user_id', $id)->delete();
                CorporateDiscountCharge::where('user_id', $id)->delete();

                if($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 1])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 2])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 3])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id , 'shipping_mode_id' => 4])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'packaging_charges' => $pendingswitchs['packaging_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'weight_addition' => $pendingweight['weight_addition'],
                            'spkg' => $pendingweight['spkg'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if($pendingcashs = PendingCorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendingcashs = PendingCorporateCashHandlingCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if($pendinginsurances = PendingCorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendinginsurances = PendingCorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendinginsurance = PendingCorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendinginsurances = PendingCorporateInsuranceCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if($pendingreturns = PendingCorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $pendingreturn['local'],
                            'national' => $pendingreturn['national']
                        ]);
                    }
                }
                if($pendingreturns = PendingCorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $pendingreturn['local'],
                            'national' => $pendingreturn['national']
                        ]);
                    }
                }
                if($pendingreturns = PendingCorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $pendingreturn['local'],
                            'national' => $pendingreturn['national']
                        ]);
                    }
                }
                if($pendingreturns = PendingCorporateReturnCharges::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $pendingreturn['local'],
                            'national' => $pendingreturn['national']
                        ]);
                    }
                }
                if($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id , 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'packaging' => $pendingdiscount['packaging'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                PendingCorporateRateStatus::where('user_id', $id)->delete();
                PendingCorporateWeightCharge::where('user_id', $id)->delete();
                PendingCorporateCashHandlingCharges::where('user_id', $id)->delete();
                PendingCorporateInsuranceCharges::where('user_id', $id)->delete();
                PendingCorporateReturnCharges::where('user_id', $id)->delete();
                PendingCorporateFuelSurcharge::where('user_id', $id)->delete();
                PendingCorporateDiscountCharge::where('user_id', $id)->delete();
                User::where('id', $id)->update(['rate_status' => 0, 'rates_authorized_by' => Auth::id()]);
                return redirect(route('admin.accounts.active'))->with('success', 'User Rates is now approved.');
            }
            User::where('id', $id)->update(['rate_status' => 1, 'rates_updated_by' => Auth::id()]);
            return redirect()->back()->with('success', 'All Rates are updated');
        }
    }

    public function view_rates_index(){
        
    }
}
