<?php

namespace App\Http\Controllers\Admins\V2Pickup;

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
        return view('admin.v2_pickups.rider_tracking');
    }
}
