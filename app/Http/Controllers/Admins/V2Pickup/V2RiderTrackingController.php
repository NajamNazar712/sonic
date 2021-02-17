<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\City;
use App\Http\Models\Rider;
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
        $riders = Rider::all();
        $cities = City::all();
        return view('admin.v2_pickups.rider_tracking',compact(['riders','cities']));
    }

    public function rider_tracking_by_rider(Request $request)
    {
        $rider_id = $request->rider_id;

        $data = Rider::where('riders.id',$rider_id)
            ->leftjoin('route_locations as routes', 'routes.route_id','=','riders.route_id')
            ->leftjoin('user_shipping_infos as info','info.id','=','routes.pickup_address_id')
            ->select('riders.name as name','routes.route_id as route_id','routes.pickup_address_id as pickup_id','info.pickup_address as address','info.pickup_address_lat as latitude','info.pickup_address_long as longitude')
            ->get();
        return $data->toArray();
    }
}
