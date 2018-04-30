<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\BookingType;
use App\Http\Models\Shipper\User;
use App\Http\Models\CityInfo;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Models\PaymentMode;

use Auth;

class ShipperDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
   		return view('client.dashboard');
   }
   public function ecommerce(){
   		return view('client.ecommerce');
   }
   public function orderList(){
   		return view('client.order_management');
   }
    public function orderPending(){
        return view('client.pending_booked_orders');
    }

    public function shipmentBookView() {
      $booking_types = BookingType::all();
      $user = User::find(Auth::id())->with('shipping.city')->first();
      $cities = CityInfo::orderBy('city_name')->get();
      $products = Product::orderBy('product_name')->get();
      $shipping_modes = ShippingMode::all();
      $payment_modes = PaymentMode::all();

      // session(['service_type_id' => 1]);
      // session(['service_type_name' => 'Regular']);

      // session()->forget('service_type_id');
      // session()->forget('service_type_name');

      return view('client.shipment.book.index')->with(['booking_types' => $booking_types, 'user' => $user, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'payment_modes' => $payment_modes]);
    }

    public function shipmentBookStore(Request $request) {
      $service = BookingType::find($request->selected_service_type);

      session(['service_type_id' => $service->id]);
      session(['service_type_name' => $service->booking_type]);

      return redirect()->back()->with('success', 'Under Construction');
    }
}
