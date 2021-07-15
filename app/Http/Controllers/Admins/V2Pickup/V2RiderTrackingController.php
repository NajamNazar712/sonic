<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupNote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class V2RiderTrackingController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function rider_tracking_index ()
    {
        if(Auth::user()->role_id == 1)
        {
            $cities = City::all();
        }
        else{
            $cities = City::whereIn('hub_id',session('hubs'))->get();
        }
        $riders = Rider::whereIn('city_id',$cities->pluck('id')->ToArray())->where([['status',1],['blacklist',0]])->get();
        return view('admin.v2_pickups.rider_tracking',compact(['riders','cities']));
    }

    public function rider_tracking_by_rider(Request $request)
    {
        $rider_id = $request->rider_id;

        // Getting Rider Data
        $data = V2PickupNote::join('v2_pickup_note_requests as pivot_requests','v2_pickup_notes.id','=','pivot_requests.pickup_note_id')
            ->join('v2_pickup_requests as requests',function ($join){
                $join->on('requests.id','=','pivot_requests.pickup_request_id');
            })
            ->join('user_shipping_infos as info','info.id','=','requests.pickup_address_id')
            ->leftjoin('users','users.id','=','info.user_id')
            ->leftjoin('v2_rider_pickups as pickups', 'pickups.pickup_request_id','=','pivot_requests.pickup_request_id')
            ->select('v2_pickup_notes.id as node_id','pickups.id as checking_id','pickups.pickup_not_pick_reason_id as checking_reason','requests.pickup_address_id as pickup_id','users.name as name','info.pickup_address as address','info.location_latitude as latitude','info.location_longitude as longitude')
            ->whereDate('v2_pickup_notes.created_at', Carbon::today())
            ->where('v2_pickup_notes.rider_id',$rider_id)
            ->where('v2_pickup_notes.id', DB::raw('(select MAX(id) from v2_pickup_notes as vpn where vpn.rider_id = v2_pickup_notes.rider_id and vpn.pickups > 0)'))
            ->get()->groupBy('pickup_id');

        $rider = Rider::where('riders.id',$rider_id)
            ->leftjoin('rider_location_logs as logs', 'riders.id','=','logs.rider_id')
            ->select('riders.name as name','riders.city_id','logs.updated_at as created_at','logs.latitude as rider_latitude','logs.longitude as rider_longitude')
            ->first();

        $city = City::where('id',$rider->city_id)->first();


        //   Setting Status based on data fetched from v2_rider_pickups table
        foreach ($data as $group)
        {
            if($group->where('checking_id','!=',null)->where('checking_reason','==',null)->count() > 0)
            {
                foreach ($group as $d)
                {
                    $d->status = 'picked';
                }
            }elseif ($group->where('checking_id','!=',null)->where('checking_reason','!=',null)->count() > 0)
            {
                foreach ($group as $d)
                {
                    $d->status = 'not-picked';
                }
            }
            else{
                foreach ($group as $d)
                {
                    $d->status = 'not-reached';
                }
            }
        }

        $array = ['city_latitude'=>$city->location_latitude ?? null, 'city_longitude'=>$city->location_longitude ?? null,'rider_latitude' => $rider->rider_latitude,'rider_longitude'=>$rider->rider_longitude,'rider_name'=>$rider->name,'data'=>$data->toArray(),'data_length'=>$data->count()];
        return $array;
    }

    public function rider_tracking_by_city(Request $request)
    {
        $city_id = $request->city_id;

        if(Auth::user()->role_id == 1)
        {
            $city = City::where('id',$city_id)->first();
        }
        else{
            $city = City::whereIn('hub_id',session('hubs'))->where('id',$city_id)->first();
        }

        if($city)
        {
            $data = Rider::where('riders.city_id',$city_id)
                ->where([['riders.status',1],['riders.blacklist',0]])
                ->join('rider_location_logs as logs', 'riders.id','=','logs.rider_id')
                ->select('riders.id','riders.name as name','logs.latitude as latitude','logs.longitude as longitude','logs.updated_at as created_at')
                ->get();
            $date = new \DateTime();
            $date->modify('-1 day');
            $formatted_date = $date->format('Y-m-d H:i:s');
            $total_riders = $data->count();
            $total_active_riders = $data->where('created_at','>=',$formatted_date)->count();
            $total_inactive_riders = $data->where('created_at','<=',$formatted_date)->count();
            $data = $data->where('created_at','>=',$formatted_date);
            $array = ['city_latitude'=>$city->location_latitude,'city_longitude'=>$city->location_longitude,'total_riders'=>$total_riders,'total_active_riders'=>$total_active_riders,'total_inactive_riders'=>$total_inactive_riders,'data'=>$data->toArray(),'data_count'=>$data->count()];
            return $array;
        }

        return 0;
    }
}
