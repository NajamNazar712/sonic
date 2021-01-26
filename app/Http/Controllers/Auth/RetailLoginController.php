<?php

namespace App\Http\Controllers\Auth;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class RetailLoginController extends Controller
{
    public function __construct()
{
    $this->middleware('guest:retail')->except('logout');
}

    public function showLoginForm(){
        return view('retail.login');
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
}
