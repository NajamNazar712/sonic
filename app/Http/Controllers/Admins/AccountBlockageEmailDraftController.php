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
        $users = User::pluck('id')->toArray();
       // dd($users);

        //$date_file_name = Carbon::today()->format('Y_m_d');
        $now = Carbon::now();
        $booking=Shipment::whereRaw('DATEDIFF(DATE (NOW()),DATE (created_at))  > 15')->pluck('id')->toArray();
        
//        foreach ($booking as $bookings){
//                 $total =0;
//                //$date = Carbon::today()->format('Y_m_d');
//                $start = Carbon::parse($bookings->created_at);
//                $difference = $start->diffInDays($now);
//                if ($difference > 15) {
//                    $total++;
//            }
//        }
       // WHERE( DATEDIFF(DATE (NOW()),DATE ('created_at')) > 15)->select()->get();
        dd($booking);

    }
}
