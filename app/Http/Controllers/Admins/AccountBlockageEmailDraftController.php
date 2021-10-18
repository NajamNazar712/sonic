<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\UserDocumentAttachment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AccountBlockageEmailDraftController extends Controller
{
    static public function shipper_booking_order(){

        $now = Carbon::now()->format('Y-m-d');
        //dd($now);
        $last_15_days = Carbon::today()->subDays(15)->format('Y-m-d');

        //$users = User::where('status',3)->pluck('id')->toArray();
        $users = User::join('shipments','user_id','=','users.id')->
        where(DB::raw("(STR_TO_DATE(shipments.created_at,'%Y-%m-%d'))"),$last_15_days)->where('users.status',3)->pluck('users.id')->toArray();
        //dd($users);
        $defaulter_users = array();

        if(count($users) > 0){
            foreach ($users as $user_id){
                $user = User::find($user_id);
                $defaulter_users[] = $user->id;
                //return $user->id;
            }
            if(count($defaulter_users) > 0){
                return $defaulter_users;
            }
        }
    }
    static public function non_compliance(){
        $now = Carbon::now();
        $last_7_days = Carbon::today()->subDays(7)->format('Y-m-d');

        $users = User::join('user_document_attachments as usd','usd.user_id','=','users.id')->
          where(DB::raw("(STR_TO_DATE(usd.created_at,'%Y-%m-%d'))"),$last_7_days)
            ->where('users.status',0)->where('users.documents_status', '!=' , 2)->pluck('users.id')->toArray();
           //dd($users);
        $defaulter_users = array();
        if(count($users) >= 0){
            foreach ($users as $user_id){

                $user = User::find($user_id);
                if( $user->status == 0){
                    $user->blacklist = 1;
                    $user->save();
                }
                $defaulter_users[] = $user->id;
            }
            if(count($defaulter_users) >= 0){
                return $defaulter_users;
            }
        }
    }
    static public function fake_product(){

        $fake_product_ratio_arrival = Shipment::leftjoin('shipments_journey', 'shipments_journey.shipment_id', '=', 'shipments.id')
            ->join('users', 'shipments_journey.user_id', '=' , 'users.id')
            ->where('shipments.user_id', 'user_id')
            ->where('shipments_journey.shipper_status_id', 2)
            ->count();
        $fake_product_ratio_pending = Shipment::leftjoin('shipments_journey', 'shipments_journey.shipment_id', '=', 'shipments.id')
            ->join('users', 'shipments_journey.user_id', '=' , 'users.id')
            ->where('shipments.user_id', 'user_id')
            ->where('shipments_journey.shipper_status_id', 12)
            ->count();

     $total = 0;

        if ($total != 0) {
            $fake_product = $fake_product_ratio_arrival / $fake_product_ratio_pending * 100;
            if ($fake_product >= 20) {
                foreach ($fake_product as $user_id) {
                    $user = User::find($user_id);
                    return $user->id;
                }
            } else {
                $fake_product = 0;
            }
        }
    }
}
