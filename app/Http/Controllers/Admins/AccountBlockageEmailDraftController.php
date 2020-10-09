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

        $booking=Shipment::where('created_at','<=',Carbon::now()->subDays(15)->toDateTimeString());
        foreach ($booking as $bookings){
           dd($bookings->pluck('id')->toArray());
        }
       // dd($booking);
        return $booking;
    }
    static public function non_compliance(){

    }
    static public function fake_product(){

    }
}
