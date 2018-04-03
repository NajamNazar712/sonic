<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class AdminDashboardController extends Controller
{
    public function index(){
        return view('admin.dashboard');
    }
    public function ecommerce(){
        return view('admin.ecommerce');
    }
    public function orderList(){
        return view('admin.order_management');
    }
    public function orderPending(){
        return view('admin.pending_booked_orders');
    }
}
