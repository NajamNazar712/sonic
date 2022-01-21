<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipmentOriginChangeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function shipment_origin_change_index(){
        return view('client.shipment.origin_change.index');
    }
}
