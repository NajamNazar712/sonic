<?php

namespace App\Http\Controllers\Retail;

use App\http\Models\Admin\Retail\RetailProduct;
use App\Http\Models\BusinessCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RetailShipmentBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function index(){
        $products = RetailProduct::all();
        $business_categories = BusinessCategory::where('id', '!=', 2)->get();
        $shipping_modes = BusinessCategory::where('id', '!=', 2)->get();
        return view('retail.shipment.booking.index')->with(['products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes]);
    }
}
