<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\PickupAddressIbanMapping;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Webhook\FinalChargesSubscription;
use App\Http\Models\Webhook\InitialChargesSubscription;
use App\Http\Models\Webhook\PaymentStatusSubscription;
use App\Http\Models\Webhook\ShipmentStatusSubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

class ShipperGlobalSettingsController extends Controller
{

    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function air_waybill_printing_count_index(){
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if($air_waybill->exists()){
            $air_waybill = $air_waybill->first();
        }
        else{
            $air_waybill = null;
        }

        return view('client.settings.air_waybill_printing')->with(['air_waybill'=>$air_waybill]);
    }
    public function air_waybill_printing_count_store(Request $request){
        $settings = ShipperAirWaybillSettings::where('user_id', session('user_id'));

        if($settings->exists()){
            $settings = $settings->first();
            $settings->print_count = $request->air_waybill_printing_count;
            if($request->information_display){
                $settings->information = 1;
            }
            else{
                $settings->information = 0;
            }

            $settings->save();
        }
        else{
            $new_settings = new ShipperAirWaybillSettings();
            $new_settings->user_id = session('user_id');
            $new_settings->print_count = $request->air_waybill_printing_count;
            if($request->information_display == "on"){
                $new_settings->information = 1;
            }
            else{
                $new_settings->information = 0;
            }
            $new_settings->save();
        }

        return redirect()->back()->with('success', 'Settings Updated!');
}

    public function upload_logo_index(){
        $logo = Storage::url('shippers_logo/logo_' . session('user_id') . '.png');
        $shipper = User::find(session('user_id'));
        return view('client.settings.logo')->with(['logo'=>$logo, 'logo_status'=>$shipper->logo_status]);
    }

    public function upload_logo_submit(Request $request){
        if ($request->hasFile('upload_logo')) {
            $shipper = User::find(session('user_id'));
            $filename = 'logo_' . session('user_id') . '.png';

            $file = $request->file('upload_logo');

            Storage::disk('public')->putFileAs('shippers_logo', $file, $filename);
            $shipper->logo = $filename;
            $shipper->logo_status = 1;
            $shipper->save();
            return redirect()->back()->with('success', 'Logo Successfully Updated!');
        }
    }

    public function remove_logo(Request $request){
        $user_id = session('user_id');
        $shipper = User::find($user_id);
        $shipper->logo_status = 0;
        $shipper->logo = '';
        $shipper->save();

        $filename = 'logo_' . session('user_id') . '.png';
        Storage::disk('public')->delete('shippers_logo/'.$filename);

        return redirect()->back()->with('success', 'Logo successfully removed!');
    }
    public function shipping_information_bank_info(Request $request){
        $bank_info_id = $request->bank_info_id;
        if($bank_info_id){
            $user_bank_info = UserBankInfo::find($bank_info_id);
            if($user_bank_info){
                $details = array();
                $details['bank_name'] = $user_bank_info->bank->name;
                $details['bank_branch'] = $user_bank_info->bank_branch;
                $details['account_no'] = $user_bank_info->account_no;
                $details['title'] = $user_bank_info->account_title;
                $details['iban'] = $user_bank_info->iban;
                $details['city'] = $user_bank_info->city->name;
                return response()->json(['status' => 0, 'details' => $details]);
            }
            return response()->json(['status' => 1, 'error' => 'User Bank Information not found!']);
        }
        return response()->json(['status' => 1, 'error' => 'User Bank Information not selected!']);
    }
    public function shipping_information_index(){
        $user_id = session('user_id');
        $user_bank_infos = UserBankInfo::where('user_id', $user_id)->get();
        return view('client.settings.shipping_information')->with(['user_bank_infos' => $user_bank_infos]);
    }
    public function shipping_information_list(Request $request){
        $pickups = UserShippingInfo::join('cities as c', 'user_shipping_infos.city_id', '=', 'c.id')
            ->leftjoin('pickup_address_iban_mappings as paim', 'paim.pickup_address_id', '=', 'user_shipping_infos.id')
            ->leftjoin('user_bank_infos as ubi', 'ubi.id', '=','paim.bank_info_id')
            ->select(['user_shipping_infos.id as id','user_shipping_infos.pickup_address as pickup_address','user_shipping_infos.poc as poc','user_shipping_infos.phone as phone','user_shipping_infos.email as email','user_shipping_infos.status as status','user_shipping_infos.default_address as default_address','user_shipping_infos.user_id as user_id','c.name as city_name', 'user_shipping_infos.vendor','ubi.iban'])
            ->where('user_shipping_infos.user_id', session('user_id'))
            ->where('hidden', 0);

        return Datatables::of($pickups)
            ->editColumn('status', function ($pickup) {
                return ($pickup->status == 1) ? 'Enabled' : 'Disabled';
            })
            ->make(true);
    }
    public function shipping_information_add_iban(Request $request){
        $user_id = session('user_id');
        $pickup_address_ids = explode(',', $request->pickup_address_ids);
        $bank_info_id = $request->bank_info_select;
        if(count($pickup_address_ids) > 0){
            foreach ($pickup_address_ids as $pickup_address_id){
                $iban_map = PickupAddressIbanMapping::where('pickup_address_id', $pickup_address_id);
                if($iban_map->exists()){
                    $iban_map = $iban_map->first();
                    $iban_map->bank_info_id = $bank_info_id;
                }else{
                    $iban_map = new PickupAddressIbanMapping();
                    $iban_map->pickup_address_id = $pickup_address_id;
                    $iban_map->bank_info_id = $bank_info_id;
                }
                $iban_map->save();
            }
            return redirect()->back()->with(['success' => 'IBAN updated successfully!']);
        }
        return redirect()->back()->with(['error' => 'No pickup address selected!']);
    }

    public function subscription_index(){
        $user_type = session('user_type');
        if($user_type == 1){
            $user_id = session('user_id');

            $user_subscription = ShipmentStatusSubscription::where('user_id', $user_id)->first();

            return view('client.settings.shipment_status_subscription')->with(['user_subscription' => $user_subscription]);
        }
        else{
            return redirect()->back()->with('error', 'Access Denied!');
        }

    }
    public function subscription_submit(Request $request){
        $user_id = session('user_id');
        $subscription_status = FALSE;

        if($request->has('subscription_status')){
            $subscription_status = TRUE;
        }

        if($subscription_status){
            $subscription_url = $request->subscription_url;

            if($subscription_url){

                $url = filter_var($subscription_url, FILTER_SANITIZE_URL);


                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    return redirect()->back()->with('error', 'Not a valid url!');
                }

                $sub_user = ShipmentStatusSubscription::where('user_id', $user_id);
                if($sub_user->exists()){
                    $sub_user = $sub_user->first();
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                else{
                    $sub_user = new ShipmentStatusSubscription();
                    $sub_user->user_id = $user_id;
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                $sub_user->save();
                return redirect()->back()->with('success', 'Setting Updated Successfully!');
            }
            return redirect()->back()->with('error', 'Not a valid url!');
        }
        else {
            $sub_user = ShipmentStatusSubscription::where('user_id', $user_id)->first();
            if($sub_user){
                $sub_user->status = 0;
                $sub_user->save();
            }
            return redirect()->back()->with('success', 'Setting Updated Successfully!');

        }
    }

    public function initial_charges_subscription_index(){
        $user_type = session('user_type');
        if($user_type == 1){
            $user_id = session('user_id');

            $user_subscription = InitialChargesSubscription::where('user_id', $user_id)->first();

            return view('client.settings.initial_charges_subscription')->with(['user_subscription' => $user_subscription]);
        }
        else{
            return redirect()->back()->with('error', 'Access Denied!');
        }

    }
    public function initial_charges_subscription_submit(Request $request){
        $user_id = session('user_id');
        $subscription_status = FALSE;

        if($request->has('subscription_status')){
            $subscription_status = TRUE;
        }

        if($subscription_status){
            $subscription_url = $request->subscription_url;

            if($subscription_url){

                $url = filter_var($subscription_url, FILTER_SANITIZE_URL);


                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    return redirect()->back()->with('error', 'Not a valid url!');
                }

                $sub_user = InitialChargesSubscription::where('user_id', $user_id);
                if($sub_user->exists()){
                    $sub_user = $sub_user->first();
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                else{
                    $sub_user = new InitialChargesSubscription();
                    $sub_user->user_id = $user_id;
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                $sub_user->save();
                return redirect()->back()->with('success', 'Setting Updated Successfully!');
            }
            return redirect()->back()->with('error', 'Not a valid url!');
        }
        else {
            $sub_user = InitialChargesSubscription::where('user_id', $user_id)->first();
            if($sub_user){
                $sub_user->status = 0;
                $sub_user->save();
            }
            return redirect()->back()->with('success', 'Setting Updated Successfully!');

        }
    }

    public function final_charges_subscription_index(){
        $user_type = session('user_type');
        if($user_type == 1){
            $user_id = session('user_id');

            $user_subscription = FinalChargesSubscription::where('user_id', $user_id)->first();

            return view('client.settings.final_charges_subscription')->with(['user_subscription' => $user_subscription]);
        }
        else{
            return redirect()->back()->with('error', 'Access Denied!');
        }

    }
    public function final_charges_subscription_submit(Request $request){
        $user_id = session('user_id');
        $subscription_status = FALSE;

        if($request->has('subscription_status')){
            $subscription_status = TRUE;
        }

        if($subscription_status){
            $subscription_url = $request->subscription_url;

            if($subscription_url){

                $url = filter_var($subscription_url, FILTER_SANITIZE_URL);


                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    return redirect()->back()->with('error', 'Not a valid url!');
                }

                $sub_user = FinalChargesSubscription::where('user_id', $user_id);
                if($sub_user->exists()){
                    $sub_user = $sub_user->first();
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                else{
                    $sub_user = new FinalChargesSubscription();
                    $sub_user->user_id = $user_id;
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                $sub_user->save();
                return redirect()->back()->with('success', 'Setting Updated Successfully!');
            }
            return redirect()->back()->with('error', 'Not a valid url!');
        }
        else {
            $sub_user = FinalChargesSubscription::where('user_id', $user_id)->first();
            if($sub_user){
                $sub_user->status = 0;
                $sub_user->save();
            }
            return redirect()->back()->with('success', 'Setting Updated Successfully!');

        }
    }

    public function payment_subscription_index(){
        $user_type = session('user_type');
        if($user_type == 1){
            $user_id = session('user_id');

            $user_subscription = PaymentStatusSubscription::where('user_id', $user_id)->first();

            return view('client.settings.payment_status_subscription')->with(['user_subscription' => $user_subscription]);
        }
        else{
            return redirect()->back()->with('error', 'Access Denied!');
        }

    }
    public function payment_subscription_submit(Request $request){
        $user_id = session('user_id');
        $subscription_status = FALSE;

        if($request->has('subscription_status')){
            $subscription_status = TRUE;
        }

        if($subscription_status){
            $subscription_url = $request->subscription_url;

            if($subscription_url){

                $url = filter_var($subscription_url, FILTER_SANITIZE_URL);


                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    return redirect()->back()->with('error', 'Not a valid url!');
                }

                $sub_user = PaymentStatusSubscription::where('user_id', $user_id);
                if($sub_user->exists()){
                    $sub_user = $sub_user->first();
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                else{
                    $sub_user = new PaymentStatusSubscription();
                    $sub_user->user_id = $user_id;
                    $sub_user->url = $url;
                    $sub_user->status = 1;
                }
                $sub_user->save();
                return redirect()->back()->with('success', 'Setting Updated Successfully!');
            }
            return redirect()->back()->with('error', 'Not a valid url!');
        }
        else {
            $sub_user = PaymentStatusSubscription::where('user_id', $user_id)->first();
            if($sub_user){
                $sub_user->status = 0;
                $sub_user->save();
            }
            return redirect()->back()->with('success', 'Setting Updated Successfully!');

        }
    }
}
