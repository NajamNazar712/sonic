<?php

namespace App\Http\Controllers\Retail;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RetailDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }
    public function dashboard(){
//        return view('retail.dashboard');
        return redirect()->route('retail.booking.index');
    }
}
