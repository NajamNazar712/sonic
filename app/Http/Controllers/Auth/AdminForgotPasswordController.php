<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Password;
class AdminForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }
    public function showLinkRequestForm()
    {
        return view('admin.auth.passwords.email');
    }

    public function GenerateOTP(Request $request)
    {
        $admin = Admin::where('phone_number', $request->phone_number)->orWhere('official_phone_number',$request->phone_number);
        if ($admin->exists()) {
            $admin = $admin->first();
            $environment = config('app.env');

            if ($environment == 'production' || $environment == 'staging') {
                $otp = mt_rand(100000, 999999);
                $admin->reset_pin_otp = $otp;
                $admin->save();
                NotificationsController::send(162, $admin->id,$request->phone_number);
            }

            return response()->json(['status' => 1]);
        }
        else {
            return response()->json(['status' => 0, 'error' => 'Invalid Phone Number']);
        }
    }
    public function broker(){
        return Password::broker('admins');
    }
}
