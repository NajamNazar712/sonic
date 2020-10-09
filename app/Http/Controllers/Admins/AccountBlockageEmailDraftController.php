<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountBlockageEmailDraftController extends Controller
{
    static public function shipper_booking_order(){

        //$date_file_name = Carbon::today()->format('Y_m_d');
        $now = Carbon::now();
        $last_15_days = Carbon::today()->subDays(15)->toDateString();
//        $booking=Shipment::where('created_at','>=',$last_15_days)->pluck('id')->toArray();
//        foreach ($booking as $bookings){
//           //dd($bookings->pluck('id')->toArray());
//        }
        $booking=Shipment::whereBetween('created_at',[$last_15_days,$now])->groupBy('user_id')->pluck('user_id')->toArray();

        $users =User::where('status',3)->pluck('id')->toArray();
            if(!$booking==$users){
                 $emails =[$booking,$users];
                return $emails;
            }
        //dd($booking);
      //  return $emails;
    }
    static public function non_compliance(){

    }
    static public function fake_product(){

    }
}
