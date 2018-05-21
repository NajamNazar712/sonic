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

        if ($request->filled('book_and_print')) {
          $print = $shipment_id;
        }
        else {
          $print = FALSE;
        }

        return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $shipment->tracking_number, 'print' => $print]);
      }
      else {
        return redirect()->back()->with('error', 'Order ID must be Unique');
      }
    }

    public function printAirWaybill(Request $request) {
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

      $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Air Waybill</title>

                    <style>
                      @page {
                        size: A4 portrait;
                        margin: 0mm;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        width: 12.5% !important;
                        border: 1px solid #09262e !important;
                      }

                      .color {
                        color: #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }

                      .border.twice {
                        border-width: 2px !important;
                      }

                      .border.twice-top {
                        border-top-width: 2px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 2px !important;
                      }

                      .border.twice-left {
                        border-left-width: 2px !important;
                      }

                      .border.twice-right {
                        border-right-width: 2px !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div class="p-2">
      ';

      $shipment_details = '';

      foreach($request->ids as $id) {
        $shipment = Shipment::find($id);

        if (Auth::id() == $shipment->user_id) { //Allow Admin as Well
          $table_start = '
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" colspan="2" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo.png') . '" width="250" class="d-block mx-auto"></td>
                            <td rowspan="3" colspan="2" class="text-center align-middle border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Date</strong></td>
                            <td>' . $shipment->created_at->format('d/m/Y') . '</td>
                            <td class="color primary"><strong>Time</strong></td>
                            <td>' . $shipment->created_at->format('H:i A') . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-left"><strong>Service</strong></td>
                            <td><strong>' . $shipment->booking_type->booking_type . (($shipment->booking_type_id == 3) ? ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' : '') . '</strong></td>
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td>' . $shipment->order_id . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                            <td class="border twice-bottom">' . $shipment->pickup_address->city->city_name . '</td>
                            <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                            <td class="border twice-bottom">' . $shipment->consignee_city->city_name . '</td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                            <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Name</strong></td>
                            <td colspan="3" class="border twice-right">' . $shipment->user->name . '</td>
                            <td class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="3">' . $shipment->consignee_name . '</td>
                          </tr>

                          <tr>
                            ' . (($shipment->information_display == 1) ?
                                '<td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>'
                                :
                                '<td rowspan="2" colspan="4" class="border twice-bottom twice-right"></td>'
                            ) . '
                            <td class="color secondary border twice-left"><strong>Address</strong></td>
                            <td colspan="3">' . $shipment->consignee_address . '</td>
                          </tr>
                          <tr>
                            ' . (($shipment->information_display == 1) ?
                                '<td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>'
                                :
                                ''
                            ) . '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                          </tr>
          ';

          $table_end = '
                          <tr>
                            <td rowspan="2" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                            <td rowspan="2" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->payment_mode->mode . '</strong></td>
                          </tr>
                          <tr>
                            <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>COD</strong></td>
                            <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top"><em>Kindly do not give any addtional charges to the Rider/Courier</em></td>
                          </tr>
                        </tbody>
                      </table>

                      <hr>
          ';

          if ($shipment->booking_type_id == 1) {
            $shipment_details .= $table_start;

            $item = $shipment->items->first();

            $shipment_details .= '
                        <tr>
                          <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                          <td class="color secondary border twice-top"><strong>Type</strong></td>
                          <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                          <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                          <td>' . $item->quantity . '</td>
                          <td colspan="2" class="border twice-top"></td>
                        </tr>
                        <tr>
                          <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                          <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                        </tr>
            ';

            $shipment_details .= $table_end;
          }
          else if ($shipment->booking_type_id == 2) {
            $shipment_details .= $table_start;

            $items = $shipment->items;

            $item = $items[1];

            $shipment_details .= '
                        <tr>
                          <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Replacement Item</strong></td>
                          <td class="color secondary border twice-top"><strong>Type</strong></td>
                          <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                          <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                          <td>' . $item->quantity . '</td>
                          <td colspan="2" class="border twice-top"></td>
                        </tr>
                        <tr>
                          <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                          <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                        </tr>
            ';

            $item = $items[0];

            $shipment_details .= '
                        <tr>
                          <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                          <td class="color secondary border twice-top"><strong>Type</strong></td>
                          <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                          <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                          <td>' . $item->quantity . '</td>
                          <td colspan="2" class="border twice-top"></td>
                        </tr>
                        <tr>
                          <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                          <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                        </tr>
            ';

            $shipment_details .= $table_end;
          }
          else if ($shipment->booking_type_id == 3) {
            foreach ($shipment->items as $item) {
              $shipment_details .= $table_start;

              $shipment_details .= '
                          <tr>
                            <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                            <td class="color secondary border twice-top"><strong>Type</strong></td>
                            <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                            <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                            <td>' . $item->quantity . '</td>
                            <td class="color secondary border twice-top"><strong>Price</strong></td>
                            <td class="border twice-top">Rs ' . number_format($item->price) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                            <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                          </tr>
              ';

              $shipment_details .= $table_end;
            }
          }
        }
      }

      $html .= $shipment_details;

      $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

      return $html;
    }
}