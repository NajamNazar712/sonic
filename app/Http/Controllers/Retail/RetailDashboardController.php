<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class RetailDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }
    public function dashboard(){
        return view('retail.dashboard');
    }
}
