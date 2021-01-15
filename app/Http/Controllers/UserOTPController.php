<?php

namespace App\Http\Controllers;

use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserOtpVerification;
use Illuminate\Http\Request;
use DB;

class UserOTPController extends Controller
{
    static public function generate_users_otp(){
        DB::table('user_otp_verifications')->truncate();
        $users = User::where('status', 3)->where('phone_number_verified', 1)->pluck('id')->toArray();
        if(count($users) > 0){
            foreach ($users as $user_id){
                $password = rand(10001,99999);
                $otp = new UserOtpVerification();
                $otp->user_id = $user_id;
                $otp->otp = $password;
                $otp->save();

                NotificationsController::send(114, $password, $user_id);

            }
        }
    }

    static public function verify_users_otp(){
        $unverified_users = UserOtpVerification::pluck('user_id')->toArray();
        if(count($unverified_users) > 0){
            User::whereIn('id', $unverified_users)->where('phone_number_verified', 1)->update(['phone_number_verified' => 0]);
        }
    }
}
