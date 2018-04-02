<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShipperDashboardController extends Controller
{
   public function index(){
   		return view('client.index');
   }
}
