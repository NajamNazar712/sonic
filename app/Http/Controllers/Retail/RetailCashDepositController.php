<?php

namespace App\Http\Controllers\Retail;

use App\http\Models\Admin\Retail\RetailShippingMode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RetailCashDepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function index(){
        $shipping_modes = RetailShippingMode::all();
        return view('retail.cash_deposit.index')->with(['shipping_modes' => $shipping_modes]);
    }

    public function list(Request $request){
//        $cash_deposit = RetailShippingMode::
    }
}
