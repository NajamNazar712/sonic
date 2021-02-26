<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\City;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class V2RiderTrackingController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function rider_tracking_index ()
    {
        $cities = City::whereIn('hub_id',session('hubs'))->get();
        $riders = Rider::whereIn('city_id',$cities->pluck('id')->ToArray())->get();
        return view('admin.v2_pickups.rider_tracking',compact(['riders','cities']));
    }

    public function rider_tracking_by_rider(Request $request)
    {
        $rider_id = $request->rider_id;

        // Getting Rider Data
        $data = Rider::where('riders.id',$rider_id)
            ->join('v2_pickup_notes as notes',function ($join){
                $join->on('riders.id','=','notes.rider_id')->whereDate('notes.created_at', Carbon::today());
            })
            ->join('v2_pickup_note_requests as pivot_requests','notes.id','=','pivot_requests.pickup_note_id')
            ->join('v2_pickup_requests as requests',function ($join){
                $join->on('requests.id','=','pivot_requests.pickup_request_id');
            })
            ->join('user_shipping_infos as info','info.id','=','requests.pickup_address_id')
            ->leftjoin('users','users.id','=','info.user_id')
            ->leftjoin('v2_rider_pickups as pickups', 'pickups.pickup_request_id','=','pivot_requests.pickup_request_id')
            ->select('pickups.id as checking_id','pickups.pickup_not_pick_reason_id as checking_reason','requests.pickup_address_id as pickup_id','users.name as name','info.pickup_address as address','info.pickup_address_lat as latitude','info.pickup_address_long as longitude')
            ->get()->groupBy('pickup_id');

        $rider = Rider::where('riders.id',$rider_id)
            ->leftjoin('rider_location_logs as logs', function($join){
                $join->on('riders.id','=','logs.rider_id')
                    ->whereRaw('logs.created_at IN (select MAX(a2.created_at) from rider_location_logs as a2 join riders as u2 on u2.id = a2.rider_id group by u2.id)');
            })
            ->select('riders.name as name','logs.created_at as created_at','logs.latitude as rider_latitude','logs.longitude as rider_longitude')
            ->first();


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

        $array = ['rider_latitude' => $rider->rider_latitude,'rider_longitude'=>$rider->rider_longitude,'rider_name'=>$rider->name,'rider_location_label'=>$rider->name." (".date("d-m-Y",strtotime($rider->created_at)).")",'data'=>$data->toArray(),'data_length'=>$data->count()];
        return $array;
    }

    public function rider_tracking_by_city(Request $request)
    {
        $city_id = $request->city_id;
        $city = City::whereIn('hub_id',session('hubs'))->where('id',$city_id)->first();

        if($city)
        {
            $data = Rider::where('riders.city_id',$city_id)
                ->join('rider_location_logs as logs', function($join){
                    $join->on('riders.id','=','logs.rider_id')
                        ->whereRaw('logs.created_at IN (select MAX(a2.created_at) from rider_location_logs as a2 join riders as u2 on u2.id = a2.rider_id group by u2.id)');
                })
                ->select('riders.id','riders.name as name','logs.latitude as latitude','logs.longitude as longitude','logs.created_at as created_at')
                ->get();
            $date = new \DateTime();
            $date->modify('-5 minutes');
            $formatted_date = $date->format('Y-m-d H:i:s');
            $total_riders = $data->count();
            $total_active_riders = $data->where('created_at','>=',$formatted_date)->count();
            $total_inactive_riders = $data->where('created_at','<=',$formatted_date)->count();
            $array = ['city_latitude'=>$city->location_latitude,'city_longitude'=>$city->location_longitude,'total_riders'=>$total_riders,'total_active_riders'=>$total_active_riders,'total_inactive_riders'=>$total_inactive_riders,'data'=>$data->toArray()];
            return $array;
        }

        return 0;
    }
}
