<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\City;
use App\Http\Models\PickupAction;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DWSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public function rider_receiving_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 491);
        $riders = Rider::select('id', 'name')->where('status', 1)->get();
        $cities = City::select('id', 'name')->get();
        $default_date = Carbon::now();
        return view('admin.v2_pickups.receiving_sheet')->with(['riders' => $riders, 'cities' => $cities, 'default_date' => $default_date]);
    }

    public function rider_receiving_list(Request $request){

    }
}
