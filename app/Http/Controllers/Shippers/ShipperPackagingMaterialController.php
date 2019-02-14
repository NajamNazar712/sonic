<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\City;
use App\Http\Models\DiscountCharge;
use App\Http\Models\PackagingCharge;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingPaymentMode;
use App\Http\Models\PendingPayment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShipperPackagingMaterialController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }
    public function packaging_request(){
        $cities = City::where('status',1)->orderBy('name')->get();
        $address = UserShippingInfo::where(['user_id'=>session('user_id'),'hidden'=>0])->with('city')->get();
        $payment_mode = PackagingPaymentMode::all();
//        return $address;
        return view('client.packaging.flyers.index')->with(['address'=>$address,'cities'=>$cities,'payment_mode'=>$payment_mode]);
    }
    public function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id) {

        $user_shipping_info = new UserShippingInfo();

        $user_shipping_info->user_id = $user_id;
        $user_shipping_info->pickup_address = $address;
        $user_shipping_info->poc = $person_of_contact;
        $user_shipping_info->phone = $phone_number;
        $user_shipping_info->email = $email_address;
        $user_shipping_info->city_id = $city_id;

        $user_shipping_info->save();

        return $user_shipping_info->id;
    }
    public function packaging_request_submit(Request $request){
        $total_charges = 0;
        $boxFlyers = 0;
        $smallFlyers = ($request->sm_flyer != null)? $request->sm_flyer:0;
        $mediumFlyers =($request->md_flyer != null)? $request->md_flyer:0;
        $largeFlyers =($request->lg_flyer != null)? $request->lg_flyer:0;
//        $boxFlyers =($request->boxes != null)? $request->boxes:0;
        $charges = PackagingCharge::where('user_id',session('user_id'))->latest()->first();
        $total_charges += $smallFlyers * $charges->sm_flyer;
        $total_charges += $mediumFlyers * $charges->md_flyer;
        $total_charges += $largeFlyers * $charges->lg_flyer;
        $total_charges += $boxFlyers * $charges->box_flyer;
        $today = Carbon::today();

        if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',1)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }else if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',2)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }else if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',3)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }else if($discount = DiscountCharge::where('user_id', session('user_id'))->where('shipping_mode_id',4)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today)->exists()){
            $discount = $discount->first();
        }
        if(!empty($discount->packaging)){
            $discount_packaging = $discount->packaging;
            if (strpos($discount_packaging, '%') !== FALSE) {
                $discount_packaging = (floatval(str_replace('%', '', $discount_packaging)) / 100) * $total_charges;
            }
            else {
                $discount_packaging += floatval($discount_packaging);
            }
            $total_charges = $discount_packaging;
        }
        if($request->mode_of_payment == 1){
            $user_id = session('user_id');
            if ($request->input('address_select') == 0) {
                $result = PackagingMaterialRequest::create([
                    'user_id'=>$user_id,
                    'city_id'=>$request->new_pickup_city,
                    'small_flyers'=>$smallFlyers,
                    'medium_flyers'=>$mediumFlyers,
                    'large_flyers'=>$largeFlyers,
                    'boxes'=>$boxFlyers,
                    'address'=>$request->new_pickup_address,
                    'poc'=>$request->new_pickup_person_of_contact,
                    'phone'=>$request->new_pickup_phone_number,
                    'amount'=>$total_charges,
                    'packaging_payment_mode_id'=>$request->mode_of_payment
                ]);
                if($result){
                    return redirect()->back()->with('success','Request submitted!');
                }else{
                    return redirect()->back()->with('error','Request not submitted!');
                }
            }
            else {
                $address_id = $request->input('address_select');
                $user_address = UserShippingInfo::find($address_id);
                $result = PackagingMaterialRequest::create([
                    'user_id'=>$user_id,
                    'city_id'=>$user_address->city_id,
                    'small_flyers'=>$smallFlyers,
                    'medium_flyers'=>$mediumFlyers,
                    'large_flyers'=>$largeFlyers,
                    'boxes'=>$boxFlyers,
                    'address'=>$user_address->pickup_address,
                    'poc'=>$user_address->poc,
                    'phone'=>$user_address->phone,
                    'amount'=>$total_charges,
                    'packaging_payment_mode_id'=>$request->mode_of_payment

                ]);
                if($result){
                    return redirect()->back()->with('success','Request submitted!');
                }else{
                    return redirect()->back()->with('error','Request not submitted!');
                }
            }
        }else{
            if(PendingPayment::where('user_id', session('user_id'))->exists()){
                $balance = PendingPayment::where('user_id', session('user_id'))->first()->pending_payment_shipments->sum('payable');

            }else{
                return redirect()->back()->with('error','Can\'t  Request material!');
            }

            if($total_charges <= $balance){
                $user_id = session('user_id');
                if ($request->input('address_select') == 0) {
                    $result = PackagingMaterialRequest::create([
                        'user_id'=>$user_id,
                        'city_id'=>$request->new_pickup_city,
                        'small_flyers'=>($request->sm_flyer != null)? $request->sm_flyer:0,
                        'medium_flyers'=>($request->md_flyer != null)? $request->md_flyer:0,
                        'large_flyers'=>($request->lg_flyer != null)? $request->lg_flyer:0,
                        'boxes'=>($request->boxes != null)? $request->boxes:0,
                        'address'=>$request->new_pickup_address,
                        'poc'=>$request->new_pickup_person_of_contact,
                        'phone'=>$request->new_pickup_phone_number,
                        'amount'=>$total_charges,
                        'packaging_payment_mode_id'=>$request->mode_of_payment
                    ]);
                    if($result){
                        return redirect()->back()->with('success','Request submitted!');
                    }else{
                        return redirect()->back()->with('error','Request not submitted!');
                    }
                }
                else {
                    $address_id = $request->input('address_select');
                    $user_address = UserShippingInfo::find($address_id);
                    $result = PackagingMaterialRequest::create([
                        'user_id'=>$user_id,
                        'city_id'=>$user_address->city_id,
                        'small_flyers'=>($request->sm_flyer != null)? $request->sm_flyer:0,
                        'medium_flyers'=>($request->md_flyer != null)? $request->md_flyer:0,
                        'large_flyers'=>($request->lg_flyer != null)? $request->lg_flyer:0,
                        'boxes'=>($request->boxes != null)? $request->boxes:0,
                        'address'=>$user_address->pickup_address,
                        'poc'=>$user_address->poc,
                        'phone'=>$user_address->phone,
                        'amount'=>$total_charges,
                        'packaging_payment_mode_id'=>$request->mode_of_payment

                    ]);
                    if($result){
                        return redirect()->back()->with('success','Request submitted!');
                    }else{
                        return redirect()->back()->with('error','Request not submitted!');
                    }
                }

            }else{
                return redirect()->back()->with('error','Not enough balance!');

            }
        }

    }
}
