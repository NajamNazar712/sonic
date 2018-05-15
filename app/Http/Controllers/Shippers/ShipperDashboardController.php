<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\BookingType;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\CityInfo;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Models\ShippingModeSameDayTiming;
use App\Http\Models\PaymentMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;

use Auth;

class ShipperDashboardController extends Controller
{
    private function unique_order_id($order_id) {
      return !(Shipment::where('user_id', Auth::id())->where('order_id', $order_id)->exists());
    }

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

    public function shipmentBookView() {
      $booking_types = BookingType::all();
      $user = User::with('shipping.city')->find(Auth::id());
      $cities = CityInfo::orderBy('city_name')->get();
      $products = Product::orderBy('product_name')->get();
      $shipping_modes = ShippingMode::all();
      $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
      $payment_modes = PaymentMode::all();

      return view('client.shipment.book.index')->with(['booking_types' => $booking_types, 'user' => $user, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes]);
    }

    public function shipmentBookOrderID(Request $request) {
      if ($request->filled('order_id')) {
        return json_encode($this->unique_order_id($request->input('order_id')));
      }
      else {
        return 'false';
      }
    }

    public function shipmentBookStore(Request $request) {
      if ($request->filled('order_id')) {
        $valid = $this->unique_order_id($request->input('order_id'));
      }
      else {
        $valid = TRUE;
      }

      if ($valid) {
        $service = BookingType::find($request->input('selected_service_type'));

        session(['service_type_id' => $service->id]);
        session(['service_type_name' => $service->booking_type]);

        if ($request->input('pickup_address') == 0) {
          $pickup_city_code = CityInfo::find($request->input('new_pickup_city'))->value('city_code');

          $user_shipping_info = new UserShippingInfo();

          $user_shipping_info->user_id = Auth::id();
          $user_shipping_info->pickup_address = $request->input('new_pickup_address');
          $user_shipping_info->poc = $request->input('new_pickup_person_of_contact');
          $user_shipping_info->phone = $request->input('new_pickup_phone_number');
          $user_shipping_info->email = $request->input('new_pickup_email_address');

          $user_shipping_info->city_code = $pickup_city_code;

          $user_shipping_info->save();

          $pickup_address_id = $user_shipping_info->id;
        }
        else {
          $pickup_address_id = $request->input('pickup_address');

          $user_shipping_info = UserShippingInfo::find($pickup_address_id);

          $pickup_city_code = $user_shipping_info->city_code;
        }

        $shipment = new Shipment();

        $shipment->user_id = Auth::id();
        $shipment->booking_type_id = $request->input('selected_service_type');
        $shipment->pickup_address_id = $pickup_address_id;

        if ($request->filled('information_display')) {
          $shipment->information_display = TRUE;
        }
        else {
          $shipment->information_display = FALSE;
        }

        $shipment->consignee_city_id = $request->input('consignee_city');
        $shipment->consignee_name = $request->input('consignee_name');
        $shipment->consignee_address = $request->input('consignee_address');
        $shipment->consignee_phone_number_1 = $request->input('consignee_phone_number_1');

        if ($request->filled('consignee_phone_number_2')) {
            $shipment->consignee_phone_number_2 = $request->input('consignee_phone_number_2');
        }

        if ($request->filled('consignee_email_address')) {
          $shipment->consignee_email = $request->input('consignee_email_address');
        }

        if ($request->filled('order_id')) {
          $shipment->order_id = $request->input('order_id');
        }

        if ($request->filled('package_type')) {
          $shipment->package_type = TRUE;
        }
        else {
          $shipment->package_type = FALSE;
        }

        $shipment->pickup_date = $request->input('pickup_date_formatted');

        if ($request->filled('special_instructions')) {
          $shipment->special_instructions = $request->input('special_instructions');
        }

        $shipment->estimated_weight = $request->input('estimated_weight');
        $shipment->shipping_mode_id = $request->input('shipping_mode');

        if ($request->input('shipping_mode') == 4) {
          $shipment->same_day_timing_id = $request->input('same-day_timing');
        }

        $shipment->amount = str_replace(',', '', $request->input('amount'));
        $shipment->payment_mode_id = $request->input('payment_mode');
        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;

        $shipment->save();

        $shipment_id = $shipment->id;

        $consignee_city_code = CityInfo::find($request->input('consignee_city'))->value('city_code');

        $shipment->tracking_number = $pickup_city_code . $consignee_city_code . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->save();

        if ($request->input('selected_service_type') == 1) {
          $shipment_item = new ShipmentItem();

          $shipment_item->shipment_id = $shipment_id;
          $shipment_item->product_type_id = $request->input('product_type');

          if ($request->filled('item_description')) {
            $shipment->description = $request->input('item_description');
          }

          $shipment_item->quantity = $request->input('item_quantity');

          if ($request->filled('insurance')) {
            $shipment_item->price = str_replace(',', '', $request->input('item_price'));

            $shipment_item->insurance = TRUE;
          }
          else {
            $shipment_item->insurance = FALSE;
          }

          $shipment_item->type = 0;

          $shipment_item->save();
        }
        else if ($request->input('selected_service_type') == 2) {
          $shipment_item = new ShipmentItem();

          $shipment_item->shipment_id = $shipment_id;
          $shipment_item->product_type_id = $request->input('product_type');

          if ($request->filled('item_description')) {
            $shipment_item->description = $request->input('item_description');
          }

          $shipment_item->quantity = $request->input('item_quantity');

          if ($request->filled('insurance')) {
            $shipment_item->price = str_replace(',', '', $request->input('item_price'));

            $shipment_item->insurance = TRUE;
          }
          else {
            $shipment_item->insurance = FALSE;
          }

          $shipment_item->type = 0;

          $shipment_item->save();

          $shipment_item = new ShipmentItem();

          $shipment_item->shipment_id = $shipment_id;
          $shipment_item->product_type_id = $request->input('replacement_product_type');

          if ($request->filled('replacement_item_description')) {
            $shipment_item->description = $request->input('replacement_item_description');
          }

          $shipment_item->quantity = $request->input('replacement_item_quantity');
          $shipment_item->type = 1;

          $shipment_item->save();
        }
        else if ($request->input('selected_service_type') == 3) {
          foreach ($request->input('try_and_buy') as $try_and_buy) {
            $shipment_item = new ShipmentItem();

            $shipment_item->shipment_id = $shipment_id;
            $shipment_item->product_type_id = $try_and_buy['product_type'];

            if (isset($try_and_buy['item_description']) && !empty($try_and_buy['item_description'])) {
              $shipment_item->description = $try_and_buy['item_description'];
            }

            $shipment_item->quantity = $try_and_buy['item_quantity'];
            $shipment_item->price = str_replace(',', '', $try_and_buy['item_price']);

            if (isset($try_and_buy['insurance']) && !empty($try_and_buy['insurance'])) {
              $shipment_item->insurance = TRUE;
            }
            else {
              $shipment_item->insurance = FALSE;
            }

            $shipment_item->type = 2;

            $shipment_item->save();
          }
        }

        $shipment_journey = new ShipmentsJourney();

        $shipment_journey->shipment_id = $shipment_id;
        $shipment_journey->shipper_status_id = 1;
        $shipment_journey->consignee_status_id = 1;
        $shipment_journey->remarks = 'Shipment has been Booked!';
        $shipment_journey->user_id = Auth::id();

        $shipment_journey->save();

        return redirect()->back()->with('success', 'Shipment Booked with Tracking Number: ' . $shipment->tracking_number);
      }
      else {
        return redirect()->back()->with('error', 'Order ID must be Unique');
      }
    }
}
