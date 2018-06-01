<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;

class ShipperDashboardController extends Controller
{
    public function __construct() {
      $this->middleware('auth');
    }

    public function index() {
      return view('client.dashboard');
    }
    public function ecommerce() {
      return view('client.ecommerce');
    }
    public function orderList() {
      return view('client.order_management');
    }
    public function orderPending() {
      return view('client.pending_booked_orders');
    }
}