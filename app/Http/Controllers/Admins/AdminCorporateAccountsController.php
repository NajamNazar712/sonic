<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\StandardBookingTypeCharge;
use App\Http\Models\Admin\StandardCashHandlingCharge;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\StandardInsuranceCharge;
use App\Http\Models\Admin\StandardPackagingCharge;
use App\Http\Models\Admin\StandardReturnCharge;
use App\Http\Models\Admin\StandardWeightCharge;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;



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
            $weight = StandardWeightCharge::all()->groupBy('shipping_mode_id');
            $bookingType = StandardBookingTypeCharge::all()->groupBy('shipping_mode_id');
            $cash = StandardCashHandlingCharge::all()->groupBy('shipping_mode_id');
            $insurance = StandardInsuranceCharge::all()->groupBy('shipping_mode_id');
            $return = StandardReturnCharge::all()->groupBy('shipping_mode_id');
            $fuel = StandardFuelSurcharge::all()->groupBy('shipping_mode_id');
            $packaging = StandardPackagingCharge::all()->groupBy('shipping_mode_id');
            return view('admin.accounts.corporate.add_rates')->with(['shipper' => $user, 'weight' => $weight, 'shippingType' => $bookingType, 'cashHandling' => $cash, 'insuranceCharges' => $insurance, 'returnCharges' => $return, 'fuelCharges' => $fuel, 'packagingCharges' => $packaging]);
        }
        return redirect()->back()->with('error','User rates not found!');
    }

    public function add_rates_submit(Request $request, $id){

    }
}
