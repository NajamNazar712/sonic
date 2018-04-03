<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ShipperDashboardController extends Controller
{
   public function index(){
   		return view('client.dashboard');
   }
   public function ecommerce(){
   		return view('client.ecommerce');
   }
   public function orderList(){
   		return view('client.order_management');
   }
}
