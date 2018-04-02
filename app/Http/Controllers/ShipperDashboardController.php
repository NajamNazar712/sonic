<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShipperDashboardController extends Controller
{
   public function index(){
   		return view('client.index');
   }
   public function ecommerce(){
   		return view('client.ecommerce');
   }
}
