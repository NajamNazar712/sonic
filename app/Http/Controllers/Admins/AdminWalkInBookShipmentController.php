<?php

namespace App\Http\controllers\Admins;

use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\DeliveryType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\BookingType;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\RateStatus;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Models\ShippingModeSameDayTiming;
use App\Http\Models\PaymentMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;

use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Validator;
use Illuminate\Validation\Rule;


class AdminWalkInBookShipmentController extends Controller
{
    //
    public function index() {
        $booking_types = BookingType::select('id')->where('id', '=', 4)->first();
        $user = User::with('shipping.city')->find(session('user_id'));
        $cities = City::where('pickup', 1)->where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $consignee_cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode = ShippingMode::where('id','!=', 4)->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $payment_modes = PaymentMode::whereIn('id', [4,1])->get();
        $check = NonServiceArea::pluck('name')->toArray();


        return view('admin.shipment.book.walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode , 'user' => $user, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'payment_modes' => $payment_modes,'consignee_cities' => $consignee_cities, 'check' => $check]);
    }
}
