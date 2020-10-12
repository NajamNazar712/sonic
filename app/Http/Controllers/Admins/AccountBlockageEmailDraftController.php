<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\http\Models\UserDocumentAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountBlockageEmailDraftController extends Controller
{
    static public function shipper_booking_order(){

        $now = Carbon::now();
        $last_15_days = Carbon::today()->subDays(15)->toDateString();

        $booking=Shipment::whereBetween('created_at',[$last_15_days,$now])->groupBy('user_id')->pluck('user_id')->toArray();

        $users = User::where('status',3)->pluck('id')->toArray();

        $pending_users = array_diff($users, $booking);
        if(count($pending_users) > 0){
            foreach ($pending_users as $user_id){
                $user = User::find($user_id);
                return $user->id;
            }
        }
    }
    static public function non_compliance(){
        $now = Carbon::now();
        $last_7_days = Carbon::today()->subDays(7)->toDateString();

        $user_documents=UserDocumentAttachment::whereBetween('created_at',[$last_7_days,$now])->groupBy('user_id')->pluck('user_id')->toArray();
        ($user_documents);
        $users = User::where('status',3)->pluck('id')->toArray();
        ($users);
        $pending_documents = array_diff($users, $user_documents);
        if(count($pending_documents) > 0){
            foreach ($pending_documents as $user_id){

                $user = User::find($user_id);
                if( $user->status == 3){
                    $user->status = 4;
                    $user->save();
                }
                return $user->id;
            }
        }
    }
    static public function fake_product(){
     $fake_product_ratio=ShipmentsJourney::select()->where('shipments_journey.shipment_id','shipments_journey.shipper_status_id')->get()->toArray();
     dd($fake_product_ratio);
    }
}
