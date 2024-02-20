<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin,agent');
        // $this->middleware('auth:agent');
        // $this->middleware('auth:admin');
        // $this->middleware('auth:agent',['only'=>['save_coordinates']]);
        // $this->middleware('auth:agent')->only(['save_coordinates']);
        // if(Auth::guard('admin')->check() === true){

        // }else if(Auth::guard('agent')->check() === true){
        //     $this->middleware('auth:agent');

        // }
    }

    public function access_denied() {
        return view('admin.access_denied');
    }

    public function save_coordinates(Request $request) {

        session(['latitude' => $request->latitude, 'longitude' => $request->longitude]);
    }
}
