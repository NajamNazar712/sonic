<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use App\Http\Models\Admin\BackgroundImage;


class RetailLoginController extends Controller
{
    public function __construct()
{
    $this->middleware('guest:retail')->except('logout');
}

    public function showLoginForm(){
        $background_image = BackgroundImage::first();
        if($background_image != null)
        {
            $background_image['path'] = 'storage/'.$background_image->picture_path;
            $background_image['version'] = $background_image->version;
        }
        else
        {
            $background_image['path'] = "/img/promo-background-11-07-2023.png";
            $background_image['version'] = "2.6";

        }
        return view('retail.login')->with(['background_image' => $background_image]);
    }
    public function login(Request $request){
        //validate the form
//        $errors = new MessageBag;
        $this->validate($request, [
            'name' =>'required',
            'password' => 'required'
        ]);
        //Attempt to login
        if(Auth::guard('retail')->attempt(['name' => $request->name , 'password'=>$request->password], $request->remember)){
            //if Successfull then redirect to intended location

            $admin = Auth::guard('retail');
            if ($admin->user()->status == 0) {
                auth('retail')->logout();
                return back()->with('info', 'Your Account is Disabled, Contact Admin');
            }

            $setting = GlobalSettings::where('type', 'retail_store')->first();
            $shipper_user_id = $setting->setting_value;
            $pickup_address_id = $admin->user()->store->pickup_address_id;
            $category = $admin->user()->category;
            $category_id = $admin->user()->category_id;
            session(['user_id' => $shipper_user_id, 'pickup_address_id' => $pickup_address_id, 'category' => $category, 'category_id' => $category_id]);
            return redirect()->intended(route('retail.shipment.book.index'));
        }
        $errors = [$this->username() => trans('auth.failed')];
        return redirect()->back()->withInput($request->only('name','remember'))->withErrors($errors);

    }

    public function username()
    {
        return 'name';
    }

    public function not_found(){
        return view('errors.404');
    }
    public function logout(Request $request)
    {
        if(Auth::guard('retail')){
            Auth::guard('retail')->logout();

            $request->session()->invalidate();

            return redirect()->route('retail.login');
        }
        return redirect()->route('retail.login');

    }

    public function radius_check(Request $request){
        $retail_user = RetailUser::where('name', $request->name);
        if($retail_user->exists()){
            $retail_user = $retail_user->first();
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
        }
        if(Hash::check($request->input('password'), $retail_user->password)){
//            $retail_store = $retail_user->store;
//            $user_lat = $request->lat;
//            $user_lng = $request->lng;
//            $store_lat = $retail_store->location_latitude;
//            $store_lng = $retail_store->location_longitude;
//            $km = ( 6371 * acos( cos( deg2rad($user_lat) )
//                    * cos( deg2rad( $store_lat ) )
//                    * cos( deg2rad( $store_lng ) - deg2rad($user_lng) ) + sin( deg2rad($user_lat) )
//                    * sin( deg2rad( $store_lat ) ) ) );
//            $meters = $km * 1000;
//            if($meters <= 200){
//                $otp = mt_rand(100000,999999);
//                $retail_user->otp = $otp;
//                $retail_user->save();
//                NotificationsController::send(129, $retail_user, $otp);
//                return response()->json(['status' => 1, 'km' => $km, 'meters' => $meters]);
//            }
//            else{
//                return response()->json(['status' => 0, 'error' => 'Location not matched!', 'km' => $km, 'meters' => $meters, 'lat' => $user_lat, 'lng' => $user_lng]);
//            }

            return response()->json(['status' => 1]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
        }
    }

    public function verify_otp(Request $request){
//        $retail_user = RetailUser::where('name', $request->name);
//        if($retail_user->exists()){
//            $retail_user = $retail_user->first();
//            if($retail_user->otp == $request->otp){
//                return response()->json(['status' => 1]);
//            }
//            else{
//                return response()->json(['status' => 0, 'error' => 'Invalid OTP']);
//            }
//        }
//        else{
//            return response()->json(['status' => 0, 'error' => 'Invalid Credentials']);
//        }

        return response()->json(['status' => 1]);
    }
}
