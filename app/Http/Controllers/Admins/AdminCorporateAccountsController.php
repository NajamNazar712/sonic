<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\CorporateBookingTypeCharge;
use App\Http\Models\CorporateCashHandlingCharge;
use App\Http\Models\CorporateDiscountCharge;
use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateInsuranceCharge;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateReturnCharge;
use App\Http\Models\CorporateStandardBookingTypeCharge;
use App\Http\Models\CorporateStandardCashHandlingCharge;
use App\Http\Models\CorporateStandardFuelSurcharge;
use App\Http\Models\CorporateStandardInsuranceCharge;
use App\Http\Models\CorporateStandardMinChargeableWeight;
use App\Http\Models\CorporateStandardReturnCharge;
use App\Http\Models\CorporateStandardWeightCharge;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\WMS\WmsUserInformation;
use App\Http\Models\WMS\WmsPerProductCharge;
use App\Http\Models\WMS\WmsPerSquareFootCharge;
use App\Http\Models\WMS\WmsLabellingCharge;
use App\Http\Models\WMS\WmsPackingCharge;
use App\Http\Models\WMS\WmsStorageTypeCharge;
use App\Http\Models\WMS\WmsPendingUserInformation;
use App\Http\Models\WMS\WmsPendingPerProductCharge;
use App\Http\Models\WMS\WmsPendingPerSquareFootCharge;
use App\Http\Models\WMS\WmsPendingLabellingCharge;
use App\Http\Models\WMS\WmsPendingPackingCharge;
use App\Http\Models\WMS\WmsPendingStorageTypeCharge;
use App\Http\Models\WMS\WmsHistoryUserInformation;
use App\Http\Models\WMS\WmsHistoryPerProductCharge;
use App\Http\Models\WMS\WmsHistoryPerSquareFootCharge;
use App\Http\Models\WMS\WmsHistoryLabellingCharge;
use App\Http\Models\WMS\WmsHistoryPackingCharge;
use App\Http\Models\WMS\WmsHistoryStorageTypeCharge;
use App\Http\Models\WMS\WmsStorageType;
use App\Http\Models\InvoicingCycle;
use App\Http\Models\Rates\CorporateRateHistory;
use App\Http\Models\Rates\HistoryCorporateBookingTypeCharges;
use App\Http\Models\Rates\HistoryCorporateMinChargeableWeight;
use App\Http\Models\Rates\PendingCorporateBookingTypeCharges;
use App\Http\Models\Rates\PendingCorporateCashHandlingCharge;
use App\Http\Models\Rates\PendingCorporateDiscountCharge;
use App\Http\Models\Rates\PendingCorporateFuelSurcharge;
use App\Http\Models\Rates\PendingCorporateInsuranceCharge;
use App\Http\Models\Rates\PendingCorporateMinChargeableWeight;
use App\Http\Models\Rates\PendingCorporateRateStatus;
use App\Http\Models\Rates\PendingCorporateReturnCharge;
use App\Http\Models\Rates\PendingCorporateWeightCharge;
use App\Http\Models\Rates\HistoryCorporateCashHandlingCharge;
use App\Http\Models\Rates\HistoryCorporateDiscountCharge;
use App\Http\Models\Rates\HistoryCorporateFuelSurcharge;
use App\Http\Models\Rates\HistoryCorporateInsuranceCharge;
use App\Http\Models\Rates\HistoryCorporateRateStatus;
use App\Http\Models\Rates\HistoryCorporateReturnCharge;
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
            $sale_person = SalePersonTag::where('user_id', $id)->first();
            $min_weight = CorporateStandardMinChargeableWeight::all()->groupBy('shipping_mode_id');
            $weight = CorporateStandardWeightCharge::all()->groupBy('shipping_mode_id');
            $bookingType = CorporateStandardBookingTypeCharge::all()->groupBy('shipping_mode_id');
            $cash = CorporateStandardCashHandlingCharge::all()->groupBy('shipping_mode_id');
            $insurance = CorporateStandardInsuranceCharge::all()->groupBy('shipping_mode_id');
            $return = CorporateStandardReturnCharge::all()->groupBy('shipping_mode_id');
            $fuel = CorporateStandardFuelSurcharge::all()->groupBy('shipping_mode_id');
            $invoicing_cycles = InvoicingCycle::all();
            $storage_types = WmsStorageType::all()->where('status', 1);
            $packaging_material_types = PackagingMaterialTypes::where('status', 1)->get();
            return view('admin.accounts.corporate.add_rates')->with(['shipper' => $user, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'min_weight' => $min_weight, 'sale_person' => $sale_person, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'packaging_material_types' => $packaging_material_types]);
        }
        return redirect()->back()->with('error', 'User rates not found!');
    }

    public function add_rates_submit(Request $request, $id)
    {
//        return $request;
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
            'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
            'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
            'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
            'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
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
            'on_return_class_0_charges.*.numeric' => 'The overnight class A return charges field must be numeric.',
            'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
            'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
            'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
            'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
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
            'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
            'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
            'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
            'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
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
            'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
            'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
            'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
            'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
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
            'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
            'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
            'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
            'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
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
            'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
            'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
            'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
            'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
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
            'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
            'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
            'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
            'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
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


            //warehouse

            'invoicing_cycle.*.required' => 'Invoicing Cycle field is required.',
            'invoicing_date.*.required' => 'Invoicing Cycle field is required.',
            'invoicing_cycle.*.numeric' => 'Invoicing Cycle field must be numeric.',
            'invoicing_date.*.numeric' => 'Invoicing Cycle field must be numeric.',
            'ppc_charges.required' => 'Per product charges field id required',
            'psf_charges.required' => 'Per square foot charges field id required',
            'storage_type.*.required' => 'Storage type field is required.',
            'storage_type_charges.*.required' => 'Storage type charges field is required',
            'storage_type.*.numeric' => 'Storage type field must be numeric.',
            'storage_type_charges.*.required' => 'Storage type charges field must be numeric',
            'packing_type.*.required' => 'Packing type field is required',
            'packing_charges.*.required' => 'Packing charges field is required',
            'packing_charges.*.numeric' => 'Packing charges field must be numeric',
            'labelling_charges.required' => 'Labelling charges field is required',
            'labelling_charges.numeric' => 'Labelling charges field must be numeric',
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
                'on_replacement_charges' => 'required|numeric',
                'on_tnb_charges' => 'required|numeric',
                'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_0_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_1_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_2_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                'on_return_class_3_charges.*' => 'required_if:on_return_switch,==,on|numeric',
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
                'ol_replacement_charges' => 'required|numeric',
                'ol_tnb_charges' => 'required|numeric',
                'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_0_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_1_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_2_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                'ol_return_class_3_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
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
                'detain_replacement_charges' => 'required|numeric',
                'detain_tnb_charges' => 'required|numeric',
                'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_0_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_1_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_2_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                'detain_return_class_3_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
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
                'sameday_replacement_charges' => 'required|numeric',
                'sameday_tnb_charges' => 'required|numeric',
                'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_0_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_1_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_2charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_return_class_3_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
            ];
        }

        if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
            $warehouse_validations = [
                'invoicing_cycle' => 'required|numeric',
                'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                'ppc_charges' => 'required_if:ppc_switch,==,on',
                'psf_charges' => 'required_if:psf_switch,==,on',
                'storage_type.*' => 'required',
                'storage_type_charges.*' => 'required|numeric',
                'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
            ];
        }

        $validations = array_merge($on_validations, $ol_validations, $detain_validations, $sameday_validations);

        $validate = Validator::make($request->all(), $validations, $messages);

        if ($validate->fails()) {
            return redirect()->back()
                ->withErrors($validate)
                ->withInput();
        }


        if ($request->has('on_main_switch') && $request->on_main_switch == 'on') {
            if ($request->has('on_default') && $request->on_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 1
                ]);
            }
            $ONRateAlready = CorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 1)->get();

            if ($ONRateAlready->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'delivery_type_id' => 1,
                    'min_chargeable_weight' => $request->on_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'delivery_type_id' => 2,
                    'min_chargeable_weight' => $request->on_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'status' => ($request->has('on_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('on_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('on_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('on_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
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

                //Replacement and Try and Buy charges
                CorporateBookingTypeCharge::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 1,
                    'replacement_charges' => $request->on_replacement_charges,
                    'try_and_buy_charges' => $request->on_tnb_charges
                ]);
                //Cash handling Charges
                if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                    foreach ($request->on_cash_range_up as $ind => $on_cash_range_up) {
                        CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::create([
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
                    CorporateReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'local' => $request->on_return_local_charges,
                        'national_charges_class_0' => $request->on_return_class_0_charges,
                        'national_charges_class_1' => $request->on_return_class_1_charges,
                        'national_charges_class_2' => $request->on_return_class_2_charges,
                        'national_charges_class_3' => $request->on_return_class_3_charges
                    ]);
                }
                //Fuel Charges
                if ($request->has('overnight_fuel_switch') && $request->overnight_fuel_switch == 'on') {
                    CorporateFuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'fuel_surcharge' => $request->overnight_fuel_surcharge
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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

                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->on_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


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
            //dd($weightAlready);
        }
        //Overland
        if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {

            if ($request->has('ol_default') && $request->ol_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 2
                ]);
            }

            $OLRatePresent = CorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 2)->get();

            if ($OLRatePresent->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'delivery_type_id' => 1,
                    'min_chargeable_weight' => $request->ol_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'delivery_type_id' => 2,
                    'min_chargeable_weight' => $request->ol_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'status' => ($request->has('ol_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('ol_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('ol_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('ol_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('overland_fuel_switch')) ? 1 : 0,
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
                CorporateBookingTypeCharge::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 2,
                    'replacement_charges' => $request->ol_replacement_charges,
                    'try_and_buy_charges' => $request->ol_tnb_charges
                ]);
                //Cash handling Charges
                if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                    foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up) {
                        CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::create([
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
                    CorporateReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'local' => $request->ol_return_local_charges,
                        'national_charges_class_0' => $request->ol_return_class_0_charges,
                        'national_charges_class_1' => $request->ol_return_class_1_charges,
                        'national_charges_class_2' => $request->ol_return_class_2_charges,
                        'national_charges_class_3' => $request->ol_return_class_3_charges
                    ]);
                }
                if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                    CorporateFuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'fuel_surcharge' => $request->overland_fuel_surcharge
                    ]);
                }

                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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
                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->ol_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


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
        //Detain
        if ($request->has('detain_main_switch') && $request->detain_main_switch == 'on') {

            if ($request->has('det_default') && $request->det_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 3
                ]);
            }

            $DetainRatePresent = CorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 3)->get();

            if ($DetainRatePresent->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'delivery_type_id' => 1,
                    'min_chargeable_weight' => $request->detain_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'delivery_type_id' => 2,
                    'min_chargeable_weight' => $request->detain_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'status' => ($request->has('detain_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('detain_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('detain_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('detain_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('detain_fuel_switch')) ? 1 : 0,
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
                //Replacement and Try and Buy charges
                CorporateBookingTypeCharge::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 3,
                    'replacement_charges' => $request->detain_replacement_charges,
                    'try_and_buy_charges' => $request->detain_tnb_charges
                ]);

                //Cash handling Charges
                if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                    foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                        CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::create([
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
                    CorporateReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'local' => $request->detain_return_local_charges,
                        'national_charges_class_0' => $request->detain_return_class_0_charges,
                        'national_charges_class_1' => $request->detain_return_class_1_charges,
                        'national_charges_class_2' => $request->detain_return_class_2_charges,
                        'national_charges_class_3' => $request->detain_return_class_3_charges
                    ]);
                }
                if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                    CorporateFuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'fuel_surcharge' => $request->detain_fuel_surcharge
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
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


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
        if ($request->has('sameday_main_switch') && $request->sameday_main_switch == 'on') {

            if ($request->has('sameday_default') && $request->sameday_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 4
                ]);
            }

            $SamedayRatePresent = CorporateRateStatus::where('user_id', $id)->where('shipping_mode_id', 4)->get();
            if ($SamedayRatePresent->isEmpty()) {
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'delivery_type_id' => 1,
                    'min_chargeable_weight' => $request->sameday_door_mcw_charges
                ]);
                CorporateMinChargeableWeight::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'delivery_type_id' => 2,
                    'min_chargeable_weight' => $request->sameday_hub_mcw_charges
                ]);
                CorporateRateStatus::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'status' => ($request->has('sameday_main_switch')) ? 1 : 0,
                    'cash_handling_charges' => ($request->has('sameday_cash_handling_switch')) ? 1 : 0,
                    'insurance_charges' => ($request->has('sameday_insurance_charges_switch')) ? 1 : 0,
                    'return_charges' => ($request->has('sameday_return_switch')) ? 1 : 0,
                    'fuel_charges' => ($request->has('sameday_fuel_switch')) ? 1 : 0,
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
                        'national_charges_class_1' => 0,
                        'national_charges_class_2' => 0,
                        'national_charges_class_3' => 0
                    ]);

                }
                foreach ($request->sameday_hub_range_up as $index => $sameday_hub_range_up) {
                    CorporateWeightCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
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
                //Replacement and Try and Buy charges
                CorporateBookingTypeCharge::create([
                    'user_id' => $id,
                    'shipping_mode_id' => 4,
                    'replacement_charges' => $request->sameday_replacement_charges,
                    'try_and_buy_charges' => $request->sameday_tnb_charges
                ]);
                //Cash handling Charges
                if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                    foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up) {
                        CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::create([
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
                    CorporateReturnCharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'local' => $request->sameday_return_local_charges,
                        'national_charges_class_0' => $request->sameday_return_class_0_charges,
                        'national_charges_class_1' => 0,
                        'national_charges_class_2' => 0,
                        'national_charges_class_3' => 0
                    ]);
                }
                if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                    CorporateFuelSurcharge::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'fuel_surcharge' => $request->sameday_fuel_surcharge
                    ]);
                }
                $discount_cash = null;
                $discount_weight = null;
                $discount_insurance = null;
                $discount_return = null;

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

                if ($discount_weight != null || $discount_cash != null || $discount_insurance != null || $discount_return != null) {


                    $date_str = $request->sameday_daterange;
                    $date_sep = explode(' - ', $date_str);
                    $date_to = explode('/', $date_sep[0]);
                    $date_from = explode('/', $date_sep[1]);
                    $to = Carbon::create($date_to[2], $date_to[0], $date_to[1], 0, 0, 0, 'UTC')->toDateTimeString();
                    $from = Carbon::create($date_from[2], $date_from[0], $date_from[1], 0, 0, 0, 'UTC')->toDateTimeString();


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

        if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {

            $wms_user_info = new WmsUserInformation();
            $wms_user_info->user_id = $id;
            $wms_user_info->warehousing = 1;
            $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
            $wms_user_info->invoicing_date = ($request->input('invoicing_date')) ? $request->invoicing_date : null;
            $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
            $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
            $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
            $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
            $wms_user_info->save();

            if ($request->has('ppc_switch')) {
                $ppc = new WmsPerProductCharge();
                $ppc->user_id = $id;
                $ppc->charges = $request->ppc_charges;
                $ppc->save();
            }
            if ($request->has('psf_switch')) {
                $psf = new WmsPerSquareFootCharge();
                $psf->user_id = $id;
                $psf->charges = $request->psf_charges;
                $psf->save();
            }

            foreach ($request->storage_type as $key => $storage_type) {
                $storage_charges = new WmsStorageTypeCharge();
                $storage_charges->user_id = $id;
                $storage_charges->storage_type_id = $storage_type;
                $storage_charges->charges = $request->storage_type_charges[$key];
                $storage_charges->save();
            }

            if ($request->has('packing_charges_switch')) {
                foreach ($request->packing_type as $key => $packing) {
                    $ptype = new WmsPackingCharge();
                    $ptype->user_id = $id;
                    $ptype->packing_type_id = $packing;
                    $ptype->charges = $request->packing_charges[$key];
                    $ptype->save();
                }
            }

            if ($request->has('labelling_charges_switch')) {
                $labelling = new WmsLabellingCharge();
                $labelling->user_id = $id;
                $labelling->charges = $request->labelling_charges;
                $labelling->save();
            }
        }

        User::where('id', $id)->update(['status' => 1, 'rates_added_by' => Auth::id()]);

        NotificationsController::send(38, $id);

        return redirect(route('admin.accounts.pending'))->with('success', 'All Rates are added');
    }

    public function edit_rates_index($id)
    {
        $user = User::find($id);
        $sale_person = SalePersonTag::where('user_id', $id)->where('status', 0)->first();
        if ((($user['rate_status'] >= 0) && $user['status'] == 1) || (($user['rate_status'] == 0) && $user['status'] == 3)) {
            $switches = CorporateRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $min_weight = CorporateMinChargeableWeight::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $weight = CorporateWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $bookingType = CorporateBookingTypeCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = CorporateCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = CorporateInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = CorporateReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = CorporateFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = CorporateDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $wms_user_info = WmsUserInformation::where('user_id', $id)->first();
            $wms_product_charges = WmsPerProductCharge::where('user_id', $id)->first();
            $wms_square_foot_charges = WmsPerSquareFootCharge::where('user_id', $id)->first();
            $wms_packing_charges = WmsPackingCharge::where('user_id', $id)->get();
            $wms_labelling_charges = WmsLabellingCharge::where('user_id', $id)->first();
            $wms_storage_charges = WmsStorageTypeCharge::where('user_id', $id)->get();
            $storage_types = WmsStorageType::all()->where('status', 1);
            $invoicing_cycles = InvoicingCycle::all();
            $packaging_material_types = PackagingMaterialTypes::where('status', 1)->get();
            $rate_status = $user['rate_status'];
        } elseif (($user['rate_status'] >= 1) && $user['status'] == 3) {
            $switches = PendingCorporateRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
//        return $switches;
//        var_dump(empty($switches));exit();
            $min_weight = PendingCorporateMinChargeableWeight::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $weight = PendingCorporateWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $bookingType = PendingCorporateBookingTypeCharges::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $cash = PendingCorporateCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $insurance = PendingCorporateInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $return = PendingCorporateReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $fuel = PendingCorporateFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $discount = PendingCorporateDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
            $wms_user_info = WmsPendingUserInformation::where('user_id', $id)->first();
            $wms_product_charges = WmsPendingPerProductCharge::where('user_id', $id)->first();
            $wms_square_foot_charges = WmsPendingPerSquareFootCharge::where('user_id', $id)->first();
            $wms_packing_charges = WmsPendingPackingCharge::where('user_id', $id)->get();
            $wms_labelling_charges = WmsPendingLabellingCharge::where('user_id', $id)->first();
            $wms_storage_charges = WmsPendingStorageTypeCharge::where('user_id', $id)->get();
            $storage_types = WmsStorageType::all()->where('status', 1);
            $invoicing_cycles = InvoicingCycle::all();
            $packaging_material_types = PackagingMaterialTypes::where('status', 1)->get();
            $rate_status = $user['rate_status'];
        }
        if (session('department_id') == 7) {
            if ($sale_person['admin_id'] == Auth::id() || session('role_id') == 4) {
                return view('admin.accounts.corporate.edit_rates')->with(['shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount, 'rate_status' => $rate_status, 'min_weight' => $min_weight, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types]);
            } else {
                return view('admin.access_denied');
            }
        } else {
            return view('admin.accounts.corporate.edit_rates')->with(['shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount, 'rate_status' => $rate_status, 'min_weight' => $min_weight, 'sale_person' => $sale_person, 'packaging_material_types' => $packaging_material_types, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types]);
        }
    }

    public function edit_rates_submit(Request $request, $id)
    {
//        return $request;
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
                'on_replacement_charges.numeric' => 'The overnight replacement charges field must be numeric.',
                'on_replacement_charges.required' => 'The overnight replacement charges field is required.',
                'on_tnb_charges.numeric' => 'The overnight try and buy charges field must be numeric.',
                'on_tnb_charges.required' => 'The overnight try and buy charges field is required.',
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
                'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
                'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
                'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
                'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
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
                'ol_replacement_charges.numeric' => 'The overland replacement charges field must be numeric.',
                'ol_replacement_charges.required' => 'The overland replacement charges field is required.',
                'ol_tnb_charges.numeric' => 'The overland try and buy charges field must be numeric.',
                'ol_tnb_charges.required' => 'The overland try and buy charges field is required.',
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
                'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
                'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
                'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
                'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
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
                'detain_replacement_charges.numeric' => 'The detain replacement charges field must be numeric.',
                'detain_replacement_charges.required' => 'The detain replacement charges field is required.',
                'detain_tnb_charges.numeric' => 'The detain try and buy charges field must be numeric.',
                'detain_tnb_charges.required' => 'The detain try and buy charges field is required.',
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
                'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
                'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
                'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
                'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
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
                'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
                'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
                'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
                'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
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


                //warehouse starts
                'invoicing_cycle.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_date.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_cycle.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'invoicing_date.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'ppc_charges.required' => 'Per product charges field id required',
                'psf_charges.required' => 'Per square foot charges field id required',
                'storage_type.*.required' => 'Storage type field is required.',
                'storage_type_charges.*.required' => 'Storage type charges field is required',
                'storage_type.*.numeric' => 'Storage type field must be numeric.',
                'storage_type_charges.*.required' => 'Storage type charges field must be numeric',
                'packing_type.*.required' => 'Packing type field is required',
                'packing_charges.*.required' => 'Packing charges field is required',
                'packing_charges.*.numeric' => 'Packing charges field must be numeric',
                'labelling_charges.required' => 'Labelling charges field is required',
                'labelling_charges.numeric' => 'Labelling charges field must be numeric',
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
                    'on_replacement_charges' => 'required|numeric',
                    'on_tnb_charges' => 'required|numeric',
                    'on_cash_range_up.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_range_down.*' => 'required_if:on_cash_handling_switch,==,on|numeric',
                    'on_cash_charges.*' => 'required_if:on_cash_handling_switch,==,on',
                    'on_ins_range_up.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_range_down.*' => 'required_if:on_insurance_charges_switch,==,on|numeric',
                    'on_ins_charges.*' => 'required_if:on_insurance_charges_switch,==,on',
                    'on_return_local_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_0_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*' => 'required_if:on_return_switch,==,on|numeric',
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
                    'ol_replacement_charges' => 'required|numeric',
                    'ol_tnb_charges' => 'required|numeric',
                    'ol_cash_range_up.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_range_down.*' => 'required_if:ol_cash_handling_switch,==,on|numeric',
                    'ol_cash_charges.*' => 'required_if:ol_cash_handling_switch,==,on',
                    'ol_ins_range_up.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_range_down.*' => 'required_if:ol_insurance_charges_switch,==,on|numeric',
                    'ol_ins_charges.*' => 'required_if:ol_insurance_charges_switch,==,on',
                    'ol_return_local_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_0_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
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
                    'detain_replacement_charges' => 'required|numeric',
                    'detain_tnb_charges' => 'required|numeric',
                    'detain_cash_range_up.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_range_down.*' => 'required_if:detain_cash_handling_switch,==,on|numeric',
                    'detain_cash_charges.*' => 'required_if:detain_cash_handling_switch,==,on',
                    'detain_ins_range_up.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_range_down.*' => 'required_if:detain_insurance_charges_switch,==,on|numeric',
                    'detain_ins_charges.*' => 'required_if:detain_insurance_charges_switch,==,on',
                    'detain_return_local_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_0_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
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
                    'sameday_replacement_charges' => 'required|numeric',
                    'sameday_tnb_charges' => 'required|numeric',
                    'sameday_cash_range_up.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_range_down.*' => 'required_if:sameday_cash_handling_switch,==,on|numeric',
                    'sameday_cash_charges.*' => 'required_if:sameday_cash_handling_switch,==,on',
                    'sameday_ins_range_up.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_range_down.*' => 'required_if:sameday_insurance_charges_switch,==,on|numeric',
                    'sameday_ins_charges.*' => 'required_if:sameday_insurance_charges_switch,==,on',
                    'sameday_return_local_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_0_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                ];
            }

            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
                $warehouse_validations = [
                    'invoicing_cycle' => 'required|numeric',
                    'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                    'ppc_charges' => 'required_if:ppc_switch,==,on',
                    'psf_charges' => 'required_if:psf_switch,==,on',
                    'storage_type.*' => 'required',
                    'storage_type_charges.*' => 'required|numeric',
                    'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                    'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                    'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
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
                if ($request->has('on_default') && $request->on_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 1
                    ]);
                }
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

                            CorporateWeightCharge::where('id', $request->on_door_weight_record[$index])
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

                            CorporateWeightCharge::where('id', $request->on_hub_weight_record[$index])
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
                    //Replacement and Try and Buy charges
                    if ($request->on_booking_record != null) {
                        CorporateBookingTypeCharge::where(['id' => $request->on_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges
                        ]);
                    } else {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $request->on_replacement_charges,
                            'try_and_buy_charges' => $request->on_tnb_charges
                        ]);
                    }
                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_cash_record)->delete();

                        foreach ($request->on_cash_record as $index => $on_cash_record) {
                            if ($request->on_cash_record[$index] != null) {
                                CorporateCashHandlingCharge::where(['id' => $request->on_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_cash_range_up[$index],
                                    'range_down' => $request->on_cash_range_down[$index],
                                    'charges' => $request->on_cash_charges[$index]
                                ]);
                            }
                            if ($request->on_cash_record[$index] == null) {
                                CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->whereNotIn('id', $request->on_insurance_record)->delete();
                        foreach ($request->on_insurance_record as $insurance => $on_insurance_record) {
                            if ($request->on_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharge::where(['id' => $request->on_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 1,
                                    'range_up' => $request->on_ins_range_up[$insurance],
                                    'range_down' => $request->on_ins_range_down[$insurance],
                                    'charges' => $request->on_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->on_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharge::create([
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
                            CorporateReturnCharge::where(['id' => $request->on_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0' => $request->on_return_class_0_charges,
                                'national_charges_class_1' => $request->on_return_class_1_charges,
                                'national_charges_class_2' => $request->on_return_class_2_charges,
                                'national_charges_class_3' => $request->on_return_class_3_charges
                            ]);
                        } elseif ($request->on_return_record == null) {
                            CorporateReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'local' => $request->on_return_local_charges,
                                'national_charges_class_0' => $request->on_return_class_0_charges,
                                'national_charges_class_1' => $request->on_return_class_1_charges,
                                'national_charges_class_2' => $request->on_return_class_2_charges,
                                'national_charges_class_3' => $request->on_return_class_3_charges
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
            }

            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
                if ($request->has('ol_default') && $request->ol_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 2
                    ]);
                }
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

                            CorporateWeightCharge::where('id', $request->ol_door_weight_record[$index])
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

                            CorporateWeightCharge::where('id', $request->ol_hub_weight_record[$index])
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
                    //Replacement and Try and Buy charges
                    if ($request->ol_booking_record != null) {
                        CorporateBookingTypeCharge::where(['id' => $request->ol_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges
                        ]);
                    } else {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $request->ol_replacement_charges,
                            'try_and_buy_charges' => $request->ol_tnb_charges
                        ]);
                    }

                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_cash_record)->delete();

                        foreach ($request->ol_cash_record as $index => $ol_cash_record) {
                            if ($request->ol_cash_record[$index] != null) {
                                CorporateCashHandlingCharge::where(['id' => $request->ol_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_cash_range_up[$index],
                                    'range_down' => $request->ol_cash_range_down[$index],
                                    'charges' => $request->ol_cash_charges[$index]
                                ]);
                            }
                            if ($request->ol_cash_record[$index] == null) {
                                CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->whereNotIn('id', $request->ol_insurance_record)->delete();
                        foreach ($request->ol_insurance_record as $insurance => $ol_insurance_record) {
                            if ($request->ol_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharge::where(['id' => $request->ol_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 2,
                                    'range_up' => $request->ol_ins_range_up[$insurance],
                                    'range_down' => $request->ol_ins_range_down[$insurance],
                                    'charges' => $request->ol_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->ol_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharge::create([
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
                            CorporateReturnCharge::where(['id' => $request->ol_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0' => $request->ol_return_class_0_charges,
                                'national_charges_class_1' => $request->ol_return_class_1_charges,
                                'national_charges_class_2' => $request->ol_return_class_2_charges,
                                'national_charges_class_3' => $request->ol_return_class_3_charges
                            ]);
                        } elseif ($request->ol_return_record == null) {
                            CorporateReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 2,
                                'local' => $request->ol_return_local_charges,
                                'national_charges_class_0' => $request->ol_return_class_0_charges,
                                'national_charges_class_1' => $request->ol_return_class_1_charges,
                                'national_charges_class_2' => $request->ol_return_class_2_charges,
                                'national_charges_class_3' => $request->ol_return_class_3_charges
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
                if ($request->has('det_default') && $request->det_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 3
                    ]);
                }
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

                            CorporateWeightCharge::where('id', $request->detain_door_weight_record[$index])
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

                            CorporateWeightCharge::where('id', $request->detain_hub_weight_record[$index])
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

                    //Replacement and Try and Buy charges
                    if ($request->detain_booking_record != null) {
                        CorporateBookingTypeCharge::where(['id' => $request->detain_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges
                        ]);
                    } else {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $request->detain_replacement_charges,
                            'try_and_buy_charges' => $request->detain_tnb_charges
                        ]);
                    }

                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_cash_record)->delete();

                        foreach ($request->detain_cash_record as $index => $detain_cash_record) {
                            if ($request->detain_cash_record[$index] != null) {
                                CorporateCashHandlingCharge::where(['id' => $request->detain_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_cash_range_up[$index],
                                    'range_down' => $request->detain_cash_range_down[$index],
                                    'charges' => $request->detain_cash_charges[$index]
                                ]);
                            }
                            if ($request->detain_cash_record[$index] == null) {
                                CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->whereNotIn('id', $request->detain_insurance_record)->delete();
                        foreach ($request->detain_insurance_record as $insurance => $detain_insurance_record) {
                            if ($request->detain_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharge::where(['id' => $request->detain_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 3,
                                    'range_up' => $request->detain_ins_range_up[$insurance],
                                    'range_down' => $request->detain_ins_range_down[$insurance],
                                    'charges' => $request->detain_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->detain_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharge::create([
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
                            CorporateReturnCharge::where(['id' => $request->detain_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0' => $request->detain_return_class_0_charges,
                                'national_charges_class_1' => $request->detain_return_class_1_charges,
                                'national_charges_class_2' => $request->detain_return_class_2_charges,
                                'national_charges_class_3' => $request->detain_return_class_3_charges
                            ]);
                        } elseif ($request->detain_return_record == null) {
                            CorporateReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 3,
                                'local' => $request->detain_return_local_charges,
                                'national_charges_class_0' => $request->detain_return_class_0_charges,
                                'national_charges_class_1' => $request->detain_return_class_1_charges,
                                'national_charges_class_2' => $request->detain_return_class_2_charges,
                                'national_charges_class_3' => $request->detain_return_class_3_charges
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
                if ($request->has('sameday_default') && $request->sameday_default == 'on') {
                    $default_shipping_mode = User::where('id', $id)->update([
                        'default_shipping_mode' => 4
                    ]);
                }
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

                            CorporateWeightCharge::where('id', $request->sameday_door_weight_record[$index])
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
                    CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 2])->whereNotIn('id', $request->sameday_hub_weight_record)->delete();
                    foreach ($request->sameday_hub_weight_record as $index => $sameday_hub_weight_record) {

                        if ($request->sameday_hub_weight_record[$index] != null) {

                            CorporateWeightCharge::where('id', $request->sameday_hub_weight_record[$index])
                                ->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
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
                                'shipping_mode_id' => 4,
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

                    //Replacement and Try and Buy charges
                    if ($request->sameday_booking_record != null) {
                        CorporateBookingTypeCharge::where(['id' => $request->sameday_booking_record])->update([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges
                        ]);
                    } else {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $request->sameday_replacement_charges,
                            'try_and_buy_charges' => $request->sameday_tnb_charges
                        ]);
                    }
                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_cash_record)->delete();

                        foreach ($request->sameday_cash_record as $index => $sameday_cash_record) {
                            if ($request->sameday_cash_record[$index] != null) {
                                CorporateCashHandlingCharge::where(['id' => $request->sameday_cash_record[$index]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_cash_range_up[$index],
                                    'range_down' => $request->sameday_cash_range_down[$index],
                                    'charges' => $request->sameday_cash_charges[$index]
                                ]);
                            }
                            if ($request->sameday_cash_record[$index] == null) {
                                CorporateCashHandlingCharge::create([
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
                        CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->whereNotIn('id', $request->sameday_insurance_record)->delete();
                        foreach ($request->sameday_insurance_record as $insurance => $sameday_insurance_record) {
                            if ($request->sameday_insurance_record[$insurance] != null) {
                                CorporateInsuranceCharge::where(['id' => $request->sameday_insurance_record[$insurance]])->update([
                                    'user_id' => $id,
                                    'shipping_mode_id' => 4,
                                    'range_up' => $request->sameday_ins_range_up[$insurance],
                                    'range_down' => $request->sameday_ins_range_down[$insurance],
                                    'charges' => $request->sameday_ins_charges[$insurance]
                                ]);
                            }
                            if ($request->sameday_insurance_record[$insurance] == null) {
                                CorporateInsuranceCharge::create([
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
                            CorporateReturnCharge::where(['id' => $request->sameday_return_record])->update([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0' => $request->sameday_return_class_0_charges,
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
                            ]);
                        } elseif ($request->sameday_return_record == null) {
                            CorporateReturnCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 4,
                                'local' => $request->sameday_return_local_charges,
                                'national_charges_class_0' => $request->sameday_return_class_0_charges,
                                'national_charges_class_1' => 0,
                                'national_charges_class_2' => 0,
                                'national_charges_class_3' => 0
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

            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
                $wms_user_info = WmsUserInformation::where('user_id', $id);
                if ($wms_user_info->exists()) {
                    $wms_user_info = $wms_user_info->first();
                    $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                    $wms_user_info->invoicing_date = ($request->input('invoicing_date')) ? $request->invoicing_date : null;
                    $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
                    $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
                    $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
                    $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
                } else {
                    $wms_user_info = new WmsUserInformation();
                    $wms_user_info->user_id = $id;
                    $wms_user_info->warehousing = 1;
                    $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                    $wms_user_info->invoicing_date = ($request->input('invoicing_date')) ? $request->invoicing_date : null;
                    $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
                    $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
                    $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
                    $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;

                }
                $wms_user_info->save();

                if ($request->has('ppc_switch')) {
                    $ppc = WmsPerProductCharge::where('user_id', $id);
                    if ($ppc->exists()) {
                        $ppc = $ppc->first();
                        $ppc->charges = $request->ppc_charges;
                    } else {
                        $ppc = new WmsPerProductCharge();
                        $ppc->user_id = $id;
                        $ppc->charges = $request->ppc_charges;
                    }
                    $ppc->save();
                } else {
                    WmsPerProductCharge::where('user_id', $id)->delete();
                }
                if ($request->has('psf_switch')) {
                    $psf = WmsPerSquareFootCharge::where('user_id', $id);
                    if ($psf->exists()) {
                        $psf = $psf->first();
                        $psf->charges = $request->psf_charges;
                    } else {
                        $psf = new WmsPerSquareFootCharge();
                        $psf->user_id = $id;
                        $psf->charges = $request->psf_charges;
                    }
                    $psf->save();

                }
                WmsStorageTypeCharge::where('user_id', $id)->delete();
                foreach ($request->storage_type as $key => $storage_type) {
                    $storage_charges = new WmsStorageTypeCharge();
                    $storage_charges->user_id = $id;
                    $storage_charges->storage_type_id = $storage_type;
                    $storage_charges->charges = $request->storage_type_charges[$key];
                    $storage_charges->save();
                }

                if ($request->has('packing_charges_switch')) {
                    WmsPackingCharge::where('user_id', $id)->delete();
                    foreach ($request->packing_type as $key => $packing) {
                        $ptype = new WmsPackingCharge();
                        $ptype->user_id = $id;
                        $ptype->packing_type_id = $packing;
                        $ptype->charges = $request->packing_charges[$key];
                        $ptype->save();
                    }
                } else {
                    WmsPackingCharge::where('user_id', $id)->delete();
                }

                if ($request->has('labelling_charges_switch')) {
                    $labelling = WmsLabellingCharge::where('user_id', $id);
                    if ($labelling->exists()) {
                        $labelling = $labelling->first();
                        $labelling->charges = $request->labelling_charges;
                    } else {
                        $labelling = new WmsLabellingCharge();
                        $labelling->user_id = $id;
                        $labelling->charges = $request->labelling_charges;
                    }
                    $labelling->save();

                } else {
                    WmsLabellingCharge::where('user_id', $id)->delete();
                }
            } else {
                $wms_user_info = WmsUserInformation::where('user_id', $id);
                if ($wms_user_info->exists()) {
                    $wms_user_info = $wms_user_info->first();
                    $wms_user_info->warehousing = 0;
                    $wms_user_info->save();
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
                'on_return_class_0_charges.*.required_if' => 'The overnight class A return charges field is required.',
                'on_return_class_1_charges.*.required_if' => 'The overnight class B return charges field is required.',
                'on_return_class_2_charges.*.required_if' => 'The overnight class C return charges field is required.',
                'on_return_class_3_charges.*.required_if' => 'The overnight class D return charges field is required.',
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
                'ol_return_class_0_charges.*.required_if' => 'The overland class A return charges field is required.',
                'ol_return_class_1_charges.*.required_if' => 'The overland class B return charges field is required.',
                'ol_return_class_2_charges.*.required_if' => 'The overland class C return charges field is required.',
                'ol_return_class_3_charges.*.required_if' => 'The overland class D return charges field is required.',
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
                'detain_return_class_0_charges.*.required_if' => 'The detain class A return charges field is required.',
                'detain_return_class_1_charges.*.required_if' => 'The detain class B return charges field is required.',
                'detain_return_class_2_charges.*.required_if' => 'The detain class C return charges field is required.',
                'detain_return_class_3_charges.*.required_if' => 'The detain class D return charges field is required.',
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
                'sameday_return_class_0_charges.*.required_if' => 'The sameday class A return charges field is required.',
                'sameday_return_class_1_charges.*.required_if' => 'The sameday class B return charges field is required.',
                'sameday_return_class_2_charges.*.required_if' => 'The sameday class C return charges field is required.',
                'sameday_return_class_3_charges.*.required_if' => 'The sameday class D return charges field is required.',
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

                //warehouse starts
                'invoicing_cycle.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_date.*.required' => 'Invoicing Cycle field is required.',
                'invoicing_cycle.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'invoicing_date.*.numeric' => 'Invoicing Cycle field must be numeric.',
                'ppc_charges.required' => 'Per product charges field id required',
                'psf_charges.required' => 'Per square foot charges field id required',
                'storage_type.*.required' => 'Storage type field is required.',
                'storage_type_charges.*.required' => 'Storage type charges field is required',
                'storage_type.*.numeric' => 'Storage type field must be numeric.',
                'storage_type_charges.*.required' => 'Storage type charges field must be numeric',
                'packing_type.*.required' => 'Packing type field is required',
                'packing_charges.*.required' => 'Packing charges field is required',
                'packing_charges.*.numeric' => 'Packing charges field must be numeric',
                'labelling_charges.required' => 'Labelling charges field is required',
                'labelling_charges.numeric' => 'Labelling charges field must be numeric',
            ];

            $validations = array();
            $on_validations = array();
            $ol_validations = array();
            $detain_validations = array();
            $sameday_validations = array();

            if ($request->has('on_default') && $request->on_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 1
                ]);
            }
            if ($request->has('ol_default') && $request->ol_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 2
                ]);
            }
            if ($request->has('det_default') && $request->det_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 3
                ]);
            }
            if ($request->has('sameday_default') && $request->sameday_default == 'on') {
                $default_shipping_mode = User::where('id', $id)->update([
                    'default_shipping_mode' => 4
                ]);
            }

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
                    'on_return_class_0_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_1_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_2_charges.*' => 'required_if:on_return_switch,==,on|numeric',
                    'on_return_class_3_charges.*' => 'required_if:on_return_switch,==,on|numeric',
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
                    'ol_return_class_0_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_1_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_2_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
                    'ol_return_class_3_charges.*' => 'required_if:ol_return_switch,==,on|numeric',
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
                    'detain_return_class_0_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_1_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_2_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
                    'detain_return_class_3_charges.*' => 'required_if:detain_return_switch,==,on|numeric',
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
                    'sameday_return_class_0_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_1_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_2_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_return_class_3_charges.*' => 'required_if:sameday_return_switch,==,on|numeric',
                    'sameday_fuel_surcharge' => 'required_if:sameday_fuel_switch,==,on|numeric',
                    'sameday_discount_title' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_daterange' => 'required_with:sameday_discount_weight_rate,sameday_discount_cash_rate,sameday_discount_insurance_rate,sameday_discount_return_rate,sameday_discount_packaging_rate',
                    'sameday_discount_weight_rate' => 'required_if:sameday_discount_weight_switch,==,on',
                    'sameday_discount_cash_rate' => 'required_if:sameday_discount_cash_switch,==,on',
                    'sameday_discount_insurance_rate' => 'required_if:sameday_discount_insurance_switch,==,on',
                    'sameday_discount_return_rate' => 'required_if:sameday_discount_return_switch,==,on',
                ];
            }

            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {
                $warehouse_validations = [
                    'invoicing_cycle' => 'required|numeric',
                    'invoicing_date.*' => 'required_if:invoicing_cycle,1,3',
                    'ppc_charges' => 'required_if:ppc_switch,==,on',
                    'psf_charges' => 'required_if:psf_switch,==,on',
                    'storage_type.*' => 'required',
                    'storage_type_charges.*' => 'required|numeric',
                    'packing_type.*' => 'required_if:packing_charges_switch,==,on',
                    'packing_charges.*' => 'required_if:packing_charges_switch,==,on|numeric',
                    'labelling_charges.*' => 'required_if:labelling_charges_switch,==,on|numeric',
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
            PendingCorporateCashHandlingCharge::where('user_id', $id)->delete();
            PendingCorporateInsuranceCharge::where('user_id', $id)->delete();
            PendingCorporateReturnCharge::where('user_id', $id)->delete();
            PendingCorporateFuelSurcharge::where('user_id', $id)->delete();
            PendingCorporateDiscountCharge::where('user_id', $id)->delete();
            PendingCorporateMinChargeableWeight::where('user_id', $id)->delete();

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
                        'fuel_charges' => ($request->has('overnight_fuel_switch')) ? 1 : 0,
                    ]);
                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $request->on_door_mcw_charges
                    ]);

                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $request->on_hub_mcw_charges
                    ]);
                    foreach ($request->on_door_weight_record as $index => $on_door_weight_record) {
                        PendingCorporateWeightCharge::create([
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
                    foreach ($request->on_hub_weight_record as $index => $on_hub_weight_record) {
                        PendingCorporateWeightCharge::create([
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
                    PendingCorporateBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'replacement_charges' => $request->on_replacement_charges,
                        'try_and_buy_charges' => $request->on_tnb_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('on_cash_handling_switch') && $request->on_cash_handling_switch == 'on') {
                        foreach ($request->on_cash_range_up as $ind => $on_cash_range_up) {
                            PendingCorporateCashHandlingCharge::create([
                                'user_id' => $id,
                                'shipping_mode_id' => 1,
                                'range_up' => $request->on_cash_range_up[$ind],
                                'range_down' => $request->on_cash_range_down[$ind],
                                'charges' => $request->on_cash_charges[$ind]
                            ]);
                        }
                    }
//                    dd($request);
                    //insurance charges
                    if ($request->has('on_insurance_charges_switch') && $request->on_insurance_charges_switch == 'on') {
                        foreach ($request->on_ins_range_up as $insurance => $on_ins_range_up) {
                            PendingCorporateInsuranceCharge::create([
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
                        PendingCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $request->on_return_local_charges,
                            'national_charges_class_0' => $request->on_return_class_0_charges,
                            'national_charges_class_1' => $request->on_return_class_1_charges,
                            'national_charges_class_2' => $request->on_return_class_2_charges,
                            'national_charges_class_3' => $request->on_return_class_3_charges
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


                        PendingCorporateDiscountCharge::create([
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
                //dd($weightAlready);
            }
            //Overland
            if ($request->has('ol_main_switch') && $request->ol_main_switch == 'on') {
//                dd($request->ol_door_mcw_charges);
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
                    ]);
                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $request->ol_door_mcw_charges
                    ]);

                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $request->ol_hub_mcw_charges
                    ]);
//                    dd($request->ol_replacement_charges);
                    foreach ($request->ol_door_weight_record as $index => $ol_door_weight_record) {
                        PendingCorporateWeightCharge::create([
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
                    foreach ($request->ol_hub_weight_record as $index => $ol_hub_weight_record) {
                        PendingCorporateWeightCharge::create([
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

                    PendingCorporateBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'replacement_charges' => $request->ol_replacement_charges,
                        'try_and_buy_charges' => $request->ol_tnb_charges
                    ]);

                    //Cash handling Charges
                    if ($request->has('ol_cash_handling_switch') && $request->ol_cash_handling_switch == 'on') {
                        foreach ($request->ol_cash_range_up as $ind => $ol_cash_range_up) {
                            PendingCorporateCashHandlingCharge::create([
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
                            PendingCorporateInsuranceCharge::create([
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
                        PendingCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $request->ol_return_local_charges,
                            'national_charges_class_0' => $request->ol_return_class_0_charges,
                            'national_charges_class_1' => $request->ol_return_class_1_charges,
                            'national_charges_class_2' => $request->ol_return_class_2_charges,
                            'national_charges_class_3' => $request->ol_return_class_3_charges
                        ]);
                    }
                    if ($request->has('overland_fuel_switch') && $request->overland_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $request->overland_fuel_surcharge
                        ]);
                    }
                    $discount_cash = 0;
                    $discount_weight = 0;
                    $discount_insurance = 0;
                    $discount_return = 0;

                    if ($request->has('ol_discount_weight_switch') && $request->ol_discount_weight_switch == 'on') {
                        $discount_weight = $request->ol_discount_weight_rate != 0 ? $request->ol_discount_weight_rate : 0;
//                    $discount_weight = $request->ol_discount_weight_rate;
                    }
                    if ($request->has('ol_discount_cash_switch') && $request->ol_discount_cash_switch == 'on') {
                        $discount_cash = $request->ol_discount_cash_rate != 0 ? $request->ol_discount_cash_rate : 0;
                    }
                    if ($request->has('ol_discount_insurance_switch') && $request->ol_discount_insurance_switch == 'on') {
                        $discount_insurance = $request->ol_discount_insurance_rate != 0 ? $request->ol_discount_insurance_rate : 0;
                    }
                    if ($request->has('ol_discount_return_switch') && $request->ol_discount_return_switch == 'on') {
                        $discount_return = $request->ol_discount_return_rate != 0 ? $request->ol_discount_insurance_rate : 0;
                    }

                    if ($discount_weight != 0 || $discount_cash != 0 || $discount_insurance != 0 || $discount_return != 0) {


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
                    ]);

                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $request->detain_door_mcw_charges
                    ]);

                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $request->detain_hub_mcw_charges
                    ]);

                    foreach ($request->detain_door_weight_record as $index => $detain_door_weight_record) {
                        PendingCorporateWeightCharge::create([
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
                    foreach ($request->detain_hub_weight_record as $index => $detain_hub_weight_record) {
                        PendingCorporateWeightCharge::create([
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

                    PendingCorporateBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'replacement_charges' => $request->detain_replacement_charges,
                        'try_and_buy_charges' => $request->detain_tnb_charges
                    ]);
                    //Cash handling Charges
                    if ($request->has('detain_cash_handling_switch') && $request->detain_cash_handling_switch == 'on') {
                        foreach ($request->detain_cash_range_up as $ind => $detain_cash_range_up) {
                            PendingCorporateCashHandlingCharge::create([
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
                            PendingCorporateInsuranceCharge::create([
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
                        PendingCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $request->detain_return_local_charges,
                            'national_charges_class_0' => $request->detain_return_class_0_charges,
                            'national_charges_class_1' => $request->detain_return_class_1_charges,
                            'national_charges_class_2' => $request->detain_return_class_2_charges,
                            'national_charges_class_3' => $request->detain_return_class_3_charges
                        ]);
                    }
                    if ($request->has('detain_fuel_switch') && $request->detain_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $request->detain_fuel_surcharge
                        ]);
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


                        PendingCorporateDiscountCharge::create([
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
                    ]);

                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $request->sameday_door_mcw_charges
                    ]);

                    PendingCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $request->sameday_hub_mcw_charges
                    ]);

                    foreach ($request->sameday_door_weight_record as $index => $sameday_door_weight_record) {
                        PendingCorporateWeightCharge::create([
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
                    foreach ($request->sameday_hub_weight_record as $index => $sameday_hub_weight_record) {
                        PendingCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
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

                    PendingCorporateBookingTypeCharges::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'replacement_charges' => $request->sameday_replacement_charges,
                        'try_and_buy_charges' => $request->sameday_tnb_charges
                    ]);

                    //Cash handling Charges
                    if ($request->has('sameday_cash_handling_switch') && $request->sameday_cash_handling_switch == 'on') {
                        foreach ($request->sameday_cash_range_up as $ind => $sameday_cash_range_up) {
                            PendingCorporateCashHandlingCharge::create([
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
                            PendingCorporateInsuranceCharge::create([
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
                        PendingCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $request->sameday_return_local_charges,
                            'national_charges_class_0' => $request->sameday_return_class_0_charges,
                            'national_charges_class_1' => 0,
                            'national_charges_class_2' => 0,
                            'national_charges_class_3' => 0
                        ]);
                    }
                    if ($request->has('sameday_fuel_switch') && $request->sameday_fuel_switch == 'on') {
                        PendingCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $request->sameday_fuel_surcharge
                        ]);
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


                        PendingCorporateDiscountCharge::create([
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
            WmsPendingUserInformation::where('user_id', $id)->delete();
            WmsPendingPerProductCharge::where('user_id', $id)->delete();
            WmsPendingPerSquareFootCharge::where('user_id', $id)->delete();
            WmsPendingStorageTypeCharge::where('user_id', $id)->delete();
            WmsPendingPackingCharge::where('user_id', $id)->delete();
            WmsPendingLabellingCharge::where('user_id', $id)->delete();
            if ($request->has('warehouse_main_switch') && $request->warehouse_main_switch == 'on') {

                $wms_user_info = new WmsPendingUserInformation();
                $wms_user_info->user_id = $id;
                $wms_user_info->warehousing = 1;
                $wms_user_info->invoicing_cycle = $request->invoicing_cycle;
                $wms_user_info->invoicing_date = ($request->input('invoicing_date')) ? $request->invoicing_date : null;
                $wms_user_info->per_product_charges = ($request->has('ppc_switch')) ? 1 : 0;
                $wms_user_info->per_square_foot_charges = ($request->has('psf_switch')) ? 1 : 0;
                $wms_user_info->packing_charges = ($request->has('packing_charges_switch')) ? 1 : 0;
                $wms_user_info->labelling_charges = ($request->has('labelling_charges_switch')) ? 1 : 0;
                $wms_user_info->save();

                if ($request->has('ppc_switch')) {
                    $ppc = new WmsPendingPerProductCharge();
                    $ppc->user_id = $id;
                    $ppc->charges = $request->ppc_charges;
                    $ppc->save();
                }
                if ($request->has('psf_switch')) {
                    $psf = new WmsPendingPerSquareFootCharge();
                    $psf->user_id = $id;
                    $psf->charges = $request->psf_charges;
                    $psf->save();
                }

                foreach ($request->storage_type as $key => $storage_type) {
                    $storage_charges = new WmsPendingStorageTypeCharge();
                    $storage_charges->user_id = $id;
                    $storage_charges->storage_type_id = $storage_type;
                    $storage_charges->charges = $request->storage_type_charges[$key];
                    $storage_charges->save();
                }

                if ($request->has('packing_charges_switch')) {
                    foreach ($request->packing_type as $key => $packing) {
                        $ptype = new WmsPendingPackingCharge();
                        $ptype->user_id = $id;
                        $ptype->packing_type_id = $packing;
                        $ptype->charges = $request->packing_charges[$key];
                        $ptype->save();
                    }
                }

                if ($request->has('labelling_charges_switch')) {
                    $labelling = new WmsPendingLabellingCharge();
                    $labelling->user_id = $id;
                    $labelling->charges = $request->labelling_charges;
                    $labelling->save();
                }
            }


            //dd($weightAlready);

            if ($request->approve == 1) {
                $user = User::find($id);

                if ($switches = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->first()) {

                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if ($switches = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->first()) {
                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if ($switches = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->first()) {
                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if ($switches = CorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->first()) {
                    HistoryCorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $switches['status'],
                        'cash_handling_charges' => $switches['cash_handling_charges'],
                        'insurance_charges' => $switches['insurance_charges'],
                        'return_charges' => $switches['return_charges'],
                        'fuel_charges' => $switches['fuel_charges']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 1])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 1])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 1])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 1])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 2])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 2])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 2])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($min_charge = CorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 2])->first()) {
                    HistoryCorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $min_charge['min_chargeable_weight']
                    ]);
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'delivery_type_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'delivery_type_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'delivery_type_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 1])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'delivery_type_id' => 1,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'delivery_type_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'delivery_type_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'delivery_type_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($weights = CorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 2])->get()) {
                    foreach ($weights as $weight) {
                        HistoryCorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'delivery_type_id' => 2,
                            'range_up' => $weight['range_up'],
                            'range_down' => $weight['range_down'],
                            'local_or_6hr' => $weight['local_or_6hr'],
                            'national_charges_class_0' => $weight['national_charges_class_0'],
                            'national_charges_class_1' => $weight['national_charges_class_1'],
                            'national_charges_class_2' => $weight['national_charges_class_2'],
                            'national_charges_class_3' => $weight['national_charges_class_3'],
                        ]);
                    }
                }

                if ($bookings = CorporateBookingTypeCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($bookings as $booking) {
                        HistoryCorporateBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $booking['replacement_charges'],
                            'try_and_buy_charges' => $booking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($bookings = CorporateBookingTypeCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($bookings as $booking) {
                        HistoryCorporateBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $booking['replacement_charges'],
                            'try_and_buy_charges' => $booking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($bookings = CorporateBookingTypeCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($bookings as $booking) {
                        HistoryCorporateBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $booking['replacement_charges'],
                            'try_and_buy_charges' => $booking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($bookings = CorporateBookingTypeCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($bookings as $booking) {
                        HistoryCorporateBookingTypeCharges::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $booking['replacement_charges'],
                            'try_and_buy_charges' => $booking['try_and_buy_charges']
                        ]);
                    }
                }

                if ($cashs = CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($cashs = CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($cashs = CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }
                if ($cashs = CorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($cashs as $cash) {
                        HistoryCorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $cash['range_up'],
                            'range_down' => $cash['range_down'],
                            'charges' => $cash['charges']
                        ]);
                    }
                }

                if ($insurances = CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($insurances = CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($insurances = CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($insurances = CorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($insurances as $insurance) {
                        HistoryCorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $insurance['range_up'],
                            'range_down' => $insurance['range_down'],
                            'charges' => $insurance['charges']
                        ]);
                    }
                }
                if ($returns = CorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($returns = CorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($returns = CorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($returns = CorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($returns as $return) {
                        HistoryCorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $return['local'],
                            'national_charges_class_0' => $return['national_charges_class_0'],
                            'national_charges_class_1' => $return['national_charges_class_1'],
                            'national_charges_class_2' => $return['national_charges_class_2'],
                            'national_charges_class_3' => $return['national_charges_class_3']
                        ]);
                    }
                }
                if ($fuels = CorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($fuels = CorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($fuels = CorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($fuels = CorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($fuels as $fuel) {
                        HistoryCorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $fuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($discounts = CorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if ($discounts = CorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if ($discounts = CorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                if ($discounts = CorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($discounts as $discount) {
                        HistoryCorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $discount['title'],
                            'weight' => $discount['weight'],
                            'cash' => $discount['cash'],
                            'insurance' => $discount['insurance'],
                            'return' => $discount['return'],
                            'to' => $discount['to'],
                            'from' => $discount['from'],
                            'added_by' => $discount['added_by']
                        ]);
                    }
                }
                $s = CorporateRateStatus::where(['user_id' => $id])->first();
                CorporateRateHistory::create([
                    'user_id' => $id,
                    'updated_by' => $user['rates_updated_by'],
                    'approved_by' => $user['rates_authorized_by'],
                    'from_date' => $s['created_at'],
                    'to_date' => Carbon::now()
                ]);

                //warehousung

                if ($wms_user_info = WmsUserInformation::where('user_id', $id)->first()) {
                    $wms_user_information = new WmsHistoryUserInformation();
                    $wms_user_information->user_id = $id;
                    $wms_user_information->warehousing = $wms_user_info['warehousing'];
                    $wms_user_information->invoicing_cycle = $wms_user_info['invoicing_cycle'];
                    $wms_user_information->invoicing_date = $wms_user_info['invoicing_date'];
                    $wms_user_information->per_product_charges = $wms_user_info['per_product_charges'];
                    $wms_user_information->per_square_foot_charges = $wms_user_info['per_square_foot_charges'];
                    $wms_user_information->packing_charges = $wms_user_info['packing_charges'];
                    $wms_user_information->labelling_charges = $wms_user_info['labelling_charges'];
                    $wms_user_information->save();
                }
                if ($ppc = WmsPerProductCharge::where('user_id', $id)->first()) {
                    $ppc_history = new WmsHistoryPerProductCharge();
                    $ppc_history->user_id = $ppc['user_id'];
                    $ppc_history->charges = $ppc['charges'];
                    $ppc_history->save();
                }
                if ($psf = WmsPerSquareFootCharge::where('user_id', $id)->first()) {
                    $psf_history = new WmsHistoryPerSquareFootCharge();
                    $psf_history->user_id = $psf['user_id'];
                    $psf_history->charges = $psf['charges'];
                    $psf_history->save();
                }

                if ($storage_type_charges = WmsStorageTypeCharge::where('user_id', $id)->get()) {
                    foreach ($storage_type_charges as $storage_charges) {
                        $history_storage_charge = new WmsHistoryStorageTypeCharge();
                        $history_storage_charge->user_id = $id;
                        $history_storage_charge->storage_type_id = $storage_charges['storage_type_id'];
                        $history_storage_charge->charges = $storage_charges['charges'];
                        $history_storage_charge->save();
                    }
                }

                if ($packing_charges = WmsPackingCharge::where('user_id', $id)->get()) {
                    foreach ($packing_charges as $packing_charges) {
                        $history_packing_charge = new WmsHistoryPackingCharge();
                        $history_packing_charge->user_id = $id;
                        $history_packing_charge->packing_type_id = $packing_charges['packing_type_id'];
                        $history_packing_charge->charges = $packing_charges['charges'];
                        $history_packing_charge->save();
                    }
                }

                if ($labelling = WmsLabellingCharge::where('user_id', $id)->first()) {
                    $labelling_history = new WmsHistoryLabellingCharge();
                    $labelling_history->user_id = $labelling['user_id'];
                    $labelling_history->charges = $labelling['charges'];
                    $labelling_history->save();
                }

                WmsUserInformation::where('user_id', $id)->delete();
                WmsPerProductCharge::where('user_id', $id)->delete();
                WmsPerSquareFootCharge::where('user_id', $id)->delete();
                WmsStorageTypeCharge::where('user_id', $id)->delete();
                WmsPackingCharge::where('user_id', $id)->delete();
                WmsLabellingCharge::where('user_id', $id)->delete();

                CorporateRateStatus::where('user_id', $id)->delete();
                CorporateWeightCharge::where('user_id', $id)->delete();
                CorporateBookingTypeCharge::where('user_id', $id)->delete();
                CorporateCashHandlingCharge::where('user_id', $id)->delete();
                CorporateInsuranceCharge::where('user_id', $id)->delete();
                CorporateReturnCharge::where('user_id', $id)->delete();
                CorporateFuelSurcharge::where('user_id', $id)->delete();
                CorporateDiscountCharge::where('user_id', $id)->delete();
                CorporateMinChargeableWeight::where('user_id', $id)->delete();

                if ($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 1])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if ($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 2])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if ($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 3])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }
                if ($pendingswitchs = PendingCorporateRateStatus::where(['user_id' => $id, 'shipping_mode_id' => 4])->first()) {
                    CorporateRateStatus::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'status' => $pendingswitchs['status'],
                        'cash_handling_charges' => $pendingswitchs['cash_handling_charges'],
                        'insurance_charges' => $pendingswitchs['insurance_charges'],
                        'return_charges' => $pendingswitchs['return_charges'],
                        'fuel_charges' => $pendingswitchs['fuel_charges']
                    ]);
                }

                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 1])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 1])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 1])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 1])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 1,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 2])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 1,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 2])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 2,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 2])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 3,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingmin_charge = PendingCorporateMinChargeableWeight::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 2])->first()) {
                    CorporateMinChargeableWeight::create([
                        'user_id' => $id,
                        'shipping_mode_id' => 4,
                        'delivery_type_id' => 2,
                        'min_chargeable_weight' => $pendingmin_charge['min_chargeable_weight']
                    ]);
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'delivery_type_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'delivery_type_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'delivery_type_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 1])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'delivery_type_id' => 1,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 1, 'delivery_type_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'delivery_type_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 2, 'delivery_type_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'delivery_type_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 3, 'delivery_type_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'delivery_type_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingweights = PendingCorporateWeightCharge::where(['user_id' => $id, 'shipping_mode_id' => 4, 'delivery_type_id' => 2])->get()) {
                    foreach ($pendingweights as $pendingweight) {
                        CorporateWeightCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'delivery_type_id' => 2,
                            'range_up' => $pendingweight['range_up'],
                            'range_down' => $pendingweight['range_down'],
                            'local_or_6hr' => $pendingweight['local_or_6hr'],
                            'national_charges_class_0' => $pendingweight['national_charges_class_0'],
                            'national_charges_class_1' => $pendingweight['national_charges_class_1'],
                            'national_charges_class_2' => $pendingweight['national_charges_class_2'],
                            'national_charges_class_3' => $pendingweight['national_charges_class_3'],
                        ]);
                    }
                }
                if ($pendingbookings = PendingCorporateBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingbookings as $pendingbooking) {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'replacement_charges' => $pendingbooking['replacement_charges'],
                            'try_and_buy_charges' => $pendingbooking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($pendingbookings = PendingCorporateBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingbookings as $pendingbooking) {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'replacement_charges' => $pendingbooking['replacement_charges'],
                            'try_and_buy_charges' => $pendingbooking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($pendingbookings = PendingCorporateBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingbookings as $pendingbooking) {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'replacement_charges' => $pendingbooking['replacement_charges'],
                            'try_and_buy_charges' => $pendingbooking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($pendingbookings = PendingCorporateBookingTypeCharges::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingbookings as $pendingbooking) {
                        CorporateBookingTypeCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'replacement_charges' => $pendingbooking['replacement_charges'],
                            'try_and_buy_charges' => $pendingbooking['try_and_buy_charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendingcashs = PendingCorporateCashHandlingCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingcashs as $pendingcash) {
                        CorporateCashHandlingCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendingcash['range_up'],
                            'range_down' => $pendingcash['range_down'],
                            'charges' => $pendingcash['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingCorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingCorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendinginsurance = PendingCorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendinginsurances = PendingCorporateInsuranceCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendinginsurances as $pendinginsurance) {
                        CorporateInsuranceCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'range_up' => $pendinginsurance['range_up'],
                            'range_down' => $pendinginsurance['range_down'],
                            'charges' => $pendinginsurance['charges']
                        ]);
                    }
                }
                if ($pendingreturns = PendingCorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingreturns = PendingCorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingreturns = PendingCorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingreturns = PendingCorporateReturnCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingreturns as $pendingreturn) {
                        CorporateReturnCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'local' => $pendingreturn['local'],
                            'national_charges_class_0' => $pendingreturn['national_charges_class_0'],
                            'national_charges_class_1' => $pendingreturn['national_charges_class_1'],
                            'national_charges_class_2' => $pendingreturn['national_charges_class_2'],
                            'national_charges_class_3' => $pendingreturn['national_charges_class_3']
                        ]);
                    }
                }
                if ($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingfuels = PendingCorporateFuelSurcharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingfuels as $pendingfuel) {
                        CorporateFuelSurcharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'fuel_surcharge' => $pendingfuel['fuel_surcharge']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 1])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 1,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 2])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 2,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 3])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 3,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }
                if ($pendingdiscounts = PendingCorporateDiscountCharge::where(['user_id' => $id, 'shipping_mode_id' => 4])->get()) {
                    foreach ($pendingdiscounts as $pendingdiscount) {
                        CorporateDiscountCharge::create([
                            'user_id' => $id,
                            'shipping_mode_id' => 4,
                            'title' => $pendingdiscount['title'],
                            'weight' => $pendingdiscount['weight'],
                            'cash' => $pendingdiscount['cash'],
                            'insurance' => $pendingdiscount['insurance'],
                            'return' => $pendingdiscount['return'],
                            'to' => $pendingdiscount['to'],
                            'from' => $pendingdiscount['from'],
                            'added_by' => $pendingdiscount['added_by']
                        ]);
                    }
                }


                if ($wms_user_info = WmsPendingUserInformation::where('user_id', $id)->first()) {
                    $wms_user_information = new WmsUserInformation();
                    $wms_user_information->user_id = $id;
                    $wms_user_information->warehousing = $wms_user_info['warehousing'];
                    $wms_user_information->invoicing_cycle = $wms_user_info['invoicing_cycle'];
                    $wms_user_information->invoicing_date = $wms_user_info['invoicing_date'];
                    $wms_user_information->per_product_charges = $wms_user_info['per_product_charges'];
                    $wms_user_information->per_square_foot_charges = $wms_user_info['per_square_foot_charges'];
                    $wms_user_information->packing_charges = $wms_user_info['packing_charges'];
                    $wms_user_information->labelling_charges = $wms_user_info['labelling_charges'];
                    $wms_user_information->save();
                }
                if ($ppc = WmsPendingPerProductCharge::where('user_id', $id)->first()) {
                    $ppc_history = new WmsPerProductCharge();
                    $ppc_history->user_id = $ppc['user_id'];
                    $ppc_history->charges = $ppc['charges'];
                    $ppc_history->save();
                }
                if ($psf = WmsPendingPerSquareFootCharge::where('user_id', $id)->first()) {
                    $psf_history = new WmsPerSquareFootCharge();
                    $psf_history->user_id = $psf['user_id'];
                    $psf_history->charges = $psf['charges'];
                    $psf_history->save();
                }

                if ($storage_type_charges = WmsPendingStorageTypeCharge::where('user_id', $id)->get()) {
                    foreach ($storage_type_charges as $storage_charges) {
                        $history_storage_charge = new WmsStorageTypeCharge();
                        $history_storage_charge->user_id = $id;
                        $history_storage_charge->storage_type_id = $storage_charges['storage_type_id'];
                        $history_storage_charge->charges = $storage_charges['charges'];
                        $history_storage_charge->save();
                    }
                }

                if ($packing_charges = WmsPendingPackingCharge::where('user_id', $id)->get()) {
                    foreach ($packing_charges as $packing_charges) {
                        $history_packing_charge = new WmsPackingCharge();
                        $history_packing_charge->user_id = $id;
                        $history_packing_charge->packing_type_id = $packing_charges['packing_type_id'];
                        $history_packing_charge->charges = $packing_charges['charges'];
                        $history_packing_charge->save();
                    }
                }

                if ($labelling = WmsPendingLabellingCharge::where('user_id', $id)->first()) {
                    $labelling_history = new WmsLabellingCharge();
                    $labelling_history->user_id = $labelling['user_id'];
                    $labelling_history->charges = $labelling['charges'];
                    $labelling_history->save();
                }

                WmsPendingUserInformation::where('user_id', $id)->delete();
                WmsPendingPerProductCharge::where('user_id', $id)->delete();
                WmsPendingPerSquareFootCharge::where('user_id', $id)->delete();
                WmsPendingStorageTypeCharge::where('user_id', $id)->delete();
                WmsPendingPackingCharge::where('user_id', $id)->delete();
                WmsPendingLabellingCharge::where('user_id', $id)->delete();

                PendingCorporateRateStatus::where('user_id', $id)->delete();
                PendingCorporateWeightCharge::where('user_id', $id)->delete();
                PendingCorporateBookingTypeCharges::where('user_id', $id)->delete();
                PendingCorporateCashHandlingCharge::where('user_id', $id)->delete();
                PendingCorporateInsuranceCharge::where('user_id', $id)->delete();
                PendingCorporateReturnCharge::where('user_id', $id)->delete();
                PendingCorporateFuelSurcharge::where('user_id', $id)->delete();
                PendingCorporateDiscountCharge::where('user_id', $id)->delete();
                PendingCorporateMinChargeableWeight::where('user_id', $id)->delete();
                User::where('id', $id)->update(['rate_status' => 0, 'rates_authorized_by' => Auth::id()]);
                return redirect(route('admin.accounts.active'))->with('success', 'User Rates is now approved.');
            }
            User::where('id', $id)->update(['rate_status' => 1, 'rates_updated_by' => Auth::id()]);
            return redirect()->back()->with('success', 'All Rates are updated');
        }
    }

    public function view_rates_index($id)
    {
        $sale_person = SalePersonTag::where('user_id', $id)->where('status', 0)->first();
        $user = User::find($id);
        $switches = CorporateRateStatus::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $min_weight = CorporateMinChargeableWeight::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $weight = CorporateWeightCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $bookingType = CorporateBookingTypeCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $cash = CorporateCashHandlingCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $insurance = CorporateInsuranceCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $return = CorporateReturnCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $fuel = CorporateFuelSurcharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $discount = CorporateDiscountCharge::all()->where('user_id', $id)->groupBy('shipping_mode_id');
        $packaging_material_types = PackagingMaterialTypes::with(['sizes'])->where('status', 1)->get();
        $wms_user_info = WmsUserInformation::where('user_id', $id)->first();
        $wms_product_charges = WmsPerProductCharge::where('user_id', $id)->first();
        $wms_square_foot_charges = WmsPerSquareFootCharge::where('user_id', $id)->first();
        $wms_packing_charges = WmsPackingCharge::where('user_id', $id)->get();
        $wms_labelling_charges = WmsLabellingCharge::where('user_id', $id)->first();
        $wms_storage_charges = WmsStorageTypeCharge::where('user_id', $id)->get();
        $storage_types = WmsStorageType::all()->where('status', 1);
        $invoicing_cycles = InvoicingCycle::all();
        if (session('department_id') == 7) {
            if ($sale_person['admin_id'] == Auth::id() || session('role_id') == 4) {
                return view('admin.accounts.corporate.view_rates')->with(['shipper' => $user, 'switches' => $switches, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'discountCharges' => $discount, 'min_weight' => $min_weight, 'sale_person' => $sale_person, 'wms_user_info' => $wms_user_info, 'wms_product_charges' => $wms_product_charges, 'wms_square_foot_charges' => $wms_square_foot_charges, 'wms_packing_charges' => $wms_packing_charges, 'wms_labelling_charges' => $wms_labelling_charges, 'wms_storage_charges' => $wms_storage_charges, 'invoicing_cycles' => $invoicing_cycles, 'storage_types' => $storage_types, 'packaging_material_types' => $packaging_material_types]);
            } else {
                return view('admin.access_denied');
            }
        }
    }
}
