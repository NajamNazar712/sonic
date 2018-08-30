<?php

namespace App\Http\Controllers\Shippers;

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

class ShipperShipmentBookController extends Controller
{
    private function unique_order_id($order_id) {
      return !(Shipment::where('user_id', session('user_id'))->where('order_id', $order_id)->exists());
    }

    private function set_service_type($service_type_id) {
      $service_type = BookingType::find($service_type_id);

      session(['service_type_id' => $service_type->id]);
      session(['service_type_name' => $service_type->booking_type]);
    }

    static public function add_pickup_address($user_id, $address, $person_of_contact, $phone_number, $email_address, $city_id) {
      $user_shipping_info = new UserShippingInfo();

      $user_shipping_info->user_id = $user_id;
      $user_shipping_info->pickup_address = $address;
      $user_shipping_info->poc = $person_of_contact;
      $user_shipping_info->phone = $phone_number;
      $user_shipping_info->email = $email_address;
      $user_shipping_info->city_id = $city_id;

      $user_shipping_info->save();

      return $user_shipping_info->id;
    }

    static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id) {
      $shipment = new Shipment();

      $shipment->user_id = $user_id;
      $shipment->booking_type_id = $service_type_id;
      $shipment->pickup_address_id = $pickup_address_id;
      $shipment->information_display = $information_display;

      $shipment->consignee_city_id = $consignee_city_id;
      $shipment->consignee_name = $consignee_name;
      $shipment->consignee_address = $consignee_address;
      $shipment->consignee_phone_number_1 = $consignee_phone_number_1;
      $shipment->consignee_phone_number_2 = $consignee_phone_number_2;
      $shipment->consignee_email = $consignee_email_address;

      $shipment->order_id = $order_id;
      $shipment->package_type = $package_type;
      $shipment->pickup_date = $pickup_date;
      $shipment->special_instructions = $special_instructions;


      $shipment->estimated_weight = $estimated_weight;
      $shipment->shipping_mode_id = $shipping_mode_id;
      $shipment->same_day_timing_id = $same_day_timing_id;

      $shipment->amount = $amount;
      $shipment->payment_mode_id = $payment_mode_id;
      $shipment->shipper_status_id = 1;
      $shipment->consignee_status_id = 1;

      $shipment->save();

      $shipment_id = $shipment->id;

      AdminPickupsController::generate($user_id, $shipment_id);

      ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL);

      return $shipment_id;
    }

    static public function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id) {
      $shipment = Shipment::find($shipment_id);

      $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

      $shipment->tracking_number = $tracking_number;

      $shipment->save();

      return $tracking_number;
    }

    static public function add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type) {
      $shipment_item = new ShipmentItem();

      $shipment_item->shipment_id = $shipment_id;
      $shipment_item->product_type_id = $product_type_id;
      $shipment_item->description = $item_description;
      $shipment_item->quantity = $item_quantity;
      $shipment_item->price = $price;
      $shipment_item->insurance = $insurance;
      $shipment_item->type = $type;

      $shipment_item->save();
    }

    public function __construct() {
      $this->middleware('auth:web,substitute_users')->except('print_air_waybill');

      $this->middleware('Permission');
    }

    public function index() {
      $booking_types = BookingType::all();
      $user = User::with('shipping.city')->find(session('user_id'));
      $cities = City::where('status',1)->where('pickup',1)->orderBy('name')->get();
      $consignee_cities = City::where('status',1)->orderBy('name')->get();
      $products = Product::orderBy('product_name')->get();
      $shipping_modes = ShippingMode::all();
      $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
      $payment_modes = PaymentMode::all();

      return view('client.shipment.book.index')->with(['booking_types' => $booking_types, 'user' => $user, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes,'consignee_cities'=>$consignee_cities]);
    }

    public function store(Request $request) {
      if ($request->filled('order_id')) {
        $valid = $this->unique_order_id($request->input('order_id'));
      }
      else {
        $valid = TRUE;
      }

      if ($valid) {
        $user_id = session('user_id');

        $service_type_id = $request->input('selected_service_type');

        $this->set_service_type($service_type_id);

        if ($request->input('pickup_address') == 0) {
          $pickup_city_id = $request->input('new_pickup_city');

          $pickup_address_id = $this->add_pickup_address($user_id, $request->input('new_pickup_address'), $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_phone_number'), $request->input('new_pickup_email_address'), $pickup_city_id);
        }
        else {
          $pickup_address_id = $request->input('pickup_address');

          $user_shipping_info = UserShippingInfo::find($pickup_address_id);

          $pickup_city_id = $user_shipping_info->city_id;
        }

        if ($request->filled('information_display')) {
          $information_display = TRUE;
        }
        else {
          $information_display = FALSE;
        }

        $consignee_city_id = $request->input('consignee_city');
        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_phone_number_1');

        if ($request->filled('consignee_phone_number_2')) {
            $consignee_phone_number_2 = $request->input('consignee_phone_number_2');
        }
        else {
          $consignee_phone_number_2 = NULL;
        }

        if ($request->filled('consignee_email_address')) {
          $consignee_email_address = $request->input('consignee_email_address');
        }
        else {
          $consignee_email_address = NULL;
        }

        if ($request->filled('order_id')) {
          $order_id = $request->input('order_id');
        }
        else {
          $order_id = NULL;
        }

        if ($request->filled('package_type')) {
          $package_type = TRUE;
        }
        else {
          $package_type = FALSE;
        }

        $pickup_date = $request->input('pickup_date_formatted');

        if ($request->filled('special_instructions')) {
          $special_instructions = $request->input('special_instructions');
        }
        else {
          $special_instructions = NULL;
        }

        $estimated_weight = $request->input('estimated_weight');
        $shipping_mode_id = $request->input('shipping_mode');

        if ($request->input('shipping_mode') == 4) {
          $same_day_timing_id = $request->input('same-day_timing');
        }
        else {
          $same_day_timing_id = NULL;
        }

        $amount = str_replace(',', '', $request->input('amount'));
        $payment_mode_id = $request->input('payment_mode');

        $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id);

        $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        if ($service_type_id == 1) {
          $product_type_id = $request->input('product_type');

          if ($request->filled('item_description')) {
            $item_description = $request->input('item_description');
          }
          else {
            $item_description = NULL;
          }

          $item_quantity = $request->input('item_quantity');

          if ($request->filled('insurance')) {
            $price = str_replace(',', '', $request->input('item_price'));
            $insurance = TRUE;
          }
          else {
            $price = NULL;
            $insurance = FALSE;
          }

          $type = 0;

          $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
        }
        else if ($service_type_id == 2) {
          $product_type_id = $request->input('product_type');

          if ($request->filled('item_description')) {
            $item_description = $request->input('item_description');
          }
          else {
            $item_description = NULL;
          }

          $item_quantity = $request->input('item_quantity');

          if ($request->filled('insurance')) {
            $price = str_replace(',', '', $request->input('item_price'));
            $insurance = TRUE;
          }
          else {
            $price = NULL;
            $insurance = FALSE;
          }

          $type = 0;

          $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

          $product_type_id = $request->input('replacement_product_type');

          if ($request->filled('replacement_item_description')) {
            $item_description = $request->input('replacement_item_description');
          }
          else {
            $item_description = NULL;
          }

          $item_quantity = $request->input('replacement_item_quantity');
          $price = NULL;
          $insurance = NULL;
          $type = 1;

          $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
        }
        else if ($service_type_id == 3) {
          foreach ($request->input('try_and_buy') as $try_and_buy) {
            $product_type_id = $try_and_buy['product_type'];

            if (isset($try_and_buy['item_description']) && !empty($try_and_buy['item_description'])) {
              $item_description = $try_and_buy['item_description'];
            }
            else {
              $item_description = NULL;
            }

            $item_quantity = $try_and_buy['item_quantity'];
            $price = str_replace(',', '', $try_and_buy['item_price']);

            if (isset($try_and_buy['insurance']) && !empty($try_and_buy['insurance'])) {
              $insurance = TRUE;
            }
            else {
              $insurance = FALSE;
            }

            $type = 2;

            $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
          }
        }

        NotificationsController::send(2, $shipment_id);

        if ($request->filled('book_and_print')) {
          $print = $shipment_id;
        }
        else {
          $print = FALSE;
        }

        return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
      }
      else {
        return redirect()->back()->with('error', 'Order ID must be Unique');
      }
    }

    public function order_id(Request $request) {
      if ($request->filled('order_id')) {
        return json_encode($this->unique_order_id($request->input('order_id')));
      }
      else {
        return 'false';
      }
    }

    public function print_air_waybill(Request $request) {
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

        if ($request->has('admin') || session('user_id') == $shipment->user_id) {
          $table_start = '
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td rowspan="3" colspan="3" class="text-center align-middle border twice-bottom twice-left twice-right">
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
                            <td class="border twice-bottom">' . $shipment->pickup_address->city->name . '</td>
                            <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                            <td class="border twice-bottom">' . $shipment->consignee_city->name . '</td>
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

    public function excel_index() {
      $booking_types = BookingType::where('id', '!=', 3)->get();
      $pickup_addresses = UserShippingInfo::with('city')->where('user_id', session('user_id'))->where('hidden', 0)->get();
      $cities = City::all();
      $products = Product::all();
      $shipping_modes = ShippingMode::all();
      $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
      $payment_modes = PaymentMode::all();

      return view('client.shipment.book.excel')->with(['booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes]);
    }

    public function excel_store(Request $request) {
      $user_id = session('user_id');

      $names = [
        'person_of_contact' => 'Person of Contact',
        'phone_number' => 'Phone Number',
        'email_address' => 'Email Address',
        'address' => 'Address',
        'city_id' => 'City ID',

        'service_type_id' => 'Service Type ID',
        'pickup_address_id' => 'Pickup Address ID',
        'information_display' => 'Information Display',
        'consignee_city_id' => 'Consignee City ID',
        'consignee_name' => 'Consignee Name',
        'consignee_address' => 'Consignee Address',
        'consignee_phone_number_1' => 'Consignee Phone Number 1',
        'consignee_phone_number_2' => 'Consignee Phone Number 2',
        'consignee_email_address' => 'Consignee Email Address',
        'order_id' => 'Order ID',

        'item_product_type_id' => 'Item Product Type ID',
        'item_description' => 'Item Description',
        'item_quantity' => 'Item Quantity',
        'item_insurance' => 'Item Insurance',
        'item_price' => 'Item Price',

        'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
        'replacement_item_description' => 'Replacement Item Description',
        'replacement_item_quantity' =>'Replacement Item Quantity',

        'pickup_date' => 'Pickup Date',
        'special_instructions' => 'Special Instructions',
        'estimated_weight' => 'Estimated Weight',
        'shipping_mode_id' => 'Shipping Mode ID',
        'same_day_timing_id' => 'Same Day Timing ID',
        'amount' => 'Amount',
        'payment_mode_id' => 'Payment Mode ID'
      ];

      $messages = [
        'required' => ':attribute is Required.',
        'required_if' => ':attribute is Required when :other is :value.',
        'filled' => ':attribute is Optional but cannot be Empty if Present.',
        'integer' => ':attribute must be an Integer.',
        'numeric' => ':attribute must be a Number.',
        'boolean' => ':attribute must be 0 or 1.',
        'digits_between' => ':attribute must be between :min and :max Digits.',
        'email' => ':attribute must be a Valid Email Address.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'unique' => ':attribute is already Present.',
        'date_format' => ':attribute must be of valid Format, required Format is: YYYY-MM-DD.',
        'in' => ':attribute must be No or Yes.',

        'phone_number.regex' => ':attribute format is Invalid, required Format is: 0300-0000000.',

        'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: 0300-0000000.',
        'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: 0300-0000000.'
      ];

      $rules = [
        'service_type_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function($query) {
          $query->where('id', '!=', 3);
        })],
        'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })],
        'information_display' => ['required', 'string', 'in:No,Yes,no,yes'],
        'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
        'consignee_name' => ['required', 'between:1,100'],
        'consignee_address' => ['required', 'between:1,190'],
        'consignee_phone_number_1' => ['required', 'regex:/[0-9]{4}-[0-9]{7}$/'],
        'consignee_phone_number_2' => ['nullable', 'regex:/[0-9]{4}-[0-9]{7}$/'],
        'consignee_email_address' => ['nullable', 'email'],
        'order_id' => ['nullable', Rule::unique('shipments')->where(function($query) use($user_id) {
          $query->where('user_id', $user_id);
        })],

        'item_product_type_id' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
        'item_description' => ['nullable', 'between:0,190'],
        'item_quantity' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'between:1,1000'],
        'item_insurance' => ['required_if:service_type_id,1,2', 'string', 'in:No,Yes,no,yes'],
        'item_price' => ['required_if:item_insurance,Yes,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

        'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
        'replacement_item_description' => ['nullable', 'between:0,190'],
        'replacement_item_quantity' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'between:1,1000'],

        'pickup_date' => ['required', 'date_format:Y-m-d', 'after:yesterday'],
        'special_instructions' => ['nullable', 'between:0,190'],
        'estimated_weight' => ['required', 'numeric', 'between:0.1,1000'],
        'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id'],
        'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
        'amount' => ['required', 'integer', 'digits_between:1,20', 'between:1,1000000'],
        'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:payment_modes,id']
      ];

      $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'information_display', 3 => 'consignee_city_id', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'order_id', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'replacement_item_product_type_id', 16 => 'replacement_item_description', 17 => 'replacement_item_quantity', 18 => 'pickup_date', 19 => 'special_instructions', 20 => 'estimated_weight', 21 => 'shipping_mode_id', 22 => 'same_day_timing_id', 23 => 'amount', 24 => 'payment_mode_id'];

      $file = $request->file('shipments');

      $spreadsheet = IOFactory::createReaderForFile($file);
      $spreadsheet->setReadDataOnly(true);
      $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

      $header = ['Service Type ID', 'Pickup Address ID', 'Show Information on Air Waybill', 'Consignee City ID', 'Consignee Name', 'Consignee Address', 'Consignee Phone Number 1 (0300-0000000)', 'Consignee Phone Number 2 (0300-0000000)', 'Consignee Email Address', 'Order ID', 'Item Product Type ID', 'Item Description', 'Item Quantity', 'Item Insurance', 'Item Price', 'Replacement Item Product Type ID', 'Replacement Item Description', 'Replacement Item Quantity', 'Pickup Date (YYYY-MM-DD)', 'Special Instructions', 'Estimated Weight (kg)', 'Mode of Shipment ID', 'Same Day Timing ID', 'Amount', 'Mode of Payment ID'];

      if ($spreadsheet[0] == $header) {
        unset($spreadsheet[0]);

        if (!empty($spreadsheet)) {
          $rows = array();

          foreach ($spreadsheet as $spreadsheet_row) {
            $row = array();

            foreach ($spreadsheet_row as $key => $value) {
              $row[$fields[$key]] = $value;
            }

            $rows[] = $row;
          }

          unset($spreadsheet);

          $errors = array();

          foreach ($rows as $key => $row) {
            $row_id = $key + 1;

            $validate = Validator::make($row, $rules, $messages);

            $validate->setAttributeNames($names);

            if ($validate->fails()) {
              $errors['Row #' . $row_id] = $validate->errors()->all();
            }

            if (empty($errors['Row #' . $row_id])) {
              if (!RateStatus::where('user_id', $user_id)->where('shipping_mode_id', $row['shipping_mode_id'])->where('status', 1)->exists()) {
                $errors['Row #' . $row_id][] = 'Booking is not enabled for Shipping Mode ID #' . $row['shipping_mode_id'] . ' on your Account';
              }

              $user_shipping_info = UserShippingInfo::find($row['pickup_address_id']);

              if (!$user_shipping_info->city->status) {
                $errors['Row #' . $row_id][] = 'Pickup Address\'s City ID #' . $user_shipping_info->city_id . ' is deactivated';
              }

              if (!$user_shipping_info->city->pickup) {
                 $errors['Row #' . $row_id][] = 'Pickup is not allowed for City ID #' . $user_shipping_info->city_id;
              }

              $consignee_city = City::find($row['consignee_city_id']);

              if (!$consignee_city->status) {
                $errors['Row #' . $row_id][] = 'Consignee City ID #' . $row['consignee_city_id'] . ' is deactivated';
              }

              $pickup_city_id = $user_shipping_info->city_id;

              if ($row['consignee_city_id'] != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                $errors['Row #' . $row_id][] = 'Same Day Delivery is not available for Different City Shipment';
              }

              if (!CityDelivery::where('city_id', $row['consignee_city_id'])->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                $errors['Row #' . $row_id][] = 'Delivery is not allowed for City ID #' . $row['consignee_city_id'] . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
              }
            }
          }

          if (empty($errors)) {
            $tracking_numbers = array();

            foreach ($rows as $key => $row) {
              $row_id = $key + 1;

              $service_type_id = $row['service_type_id'];
              $pickup_address_id = $row['pickup_address_id'];

              if ($row['information_display'] == 'Yes' || $row['information_display'] == 'yes') {
                $information_display = TRUE;
              }
              else {
                $information_display = FALSE;
              }

              $consignee_city_id = $row['consignee_city_id'];
              $consignee_name = $row['consignee_name'];
              $consignee_address = $row['consignee_address'];
              $consignee_phone_number_1 = $row['consignee_phone_number_1'];

              if (!empty(trim($row['consignee_phone_number_2']))) {
                  $consignee_phone_number_2 = $row['consignee_phone_number_2'];
              }
              else {
                $consignee_phone_number_2 = NULL;
              }

              if (!empty(trim($row['consignee_email_address']))) {
                $consignee_email_address = $row['consignee_email_address'];
              }
              else {
                $consignee_email_address = NULL;
              }

              if (!empty(trim($row['order_id']))) {
                $order_id = $row['order_id'];
              }
              else {
                $order_id = NULL;
              }

              $pickup_date = $row['pickup_date'];

              if (!empty(trim($row['special_instructions']))) {
                $special_instructions = $row['special_instructions'];
              }
              else {
                $special_instructions = NULL;
              }

              $estimated_weight = $row['estimated_weight'];
              $shipping_mode_id = $row['shipping_mode_id'];

              if ($shipping_mode_id == 4) {
                $same_day_timing_id = $row['same_day_timing_id'];
              }
              else {
                $same_day_timing_id = NULL;
              }

              $amount = $row['amount'];
              $payment_mode_id = $row['payment_mode_id'];

              $package_type = TRUE;

              $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id);

              $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

              if ($service_type_id == 1) {
                $item_product_type_id = $row['item_product_type_id'];

                if (!empty(trim($row['item_description']))) {
                  $item_description = $row['item_description'];
                }
                else {
                  $item_description = NULL;
                }

                $item_quantity = $row['item_quantity'];

                if ($row['item_insurance'] == 'Yes' || $row['item_insurance'] == 'yes') {
                  $item_price = str_replace(',', '', $row['item_price']);
                  $item_insurance = TRUE;
                }
                else {
                  $item_price = NULL;
                  $item_insurance = FALSE;
                }

                $item_type = 0;

                $this->add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
              }
              else if ($service_type_id == 2) {
                $item_product_type_id = $row['item_product_type_id'];

                if (!empty(trim($row['item_description']))) {
                  $item_description = $row['item_description'];
                }
                else {
                  $item_description = NULL;
                }

                $item_quantity = $row['item_quantity'];

                if ($row['item_insurance'] == 'Yes' || $row['item_insurance'] == 'yes') {
                  $item_price = str_replace(',', '', $row['item_price']);
                  $item_insurance = TRUE;
                }
                else {
                  $item_price = NULL;
                  $item_insurance = FALSE;
                }

                $item_type = 0;

                $this->add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

                $replacement_item_product_type_id = $row['replacement_item_product_type_id'];

                if (!empty(trim($row['replacement_item_description']))) {
                  $replacement_item_description = $row['replacement_item_description'];
                }
                else {
                  $replacement_item_description = NULL;
                }

                $replacement_item_quantity = $row['replacement_item_quantity'];

                $replacement_item_price = NULL;
                $replacement_item_insurance = NULL;
                $replacement_item_type = 1;

                $this->add_item($shipment_id, $replacement_item_product_type_id, $replacement_item_description, $replacement_item_quantity, $replacement_item_price, $replacement_item_insurance, $replacement_item_type);
              }

              $tracking_numbers['Row #' . $row_id] = $tracking_number;

              NotificationsController::send(2, $shipment_id);
            }

            $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
              return $row . ': ' . $tracking_number;
            }, array_keys($tracking_numbers), $tracking_numbers));

            return redirect()->back()->with(['success' => 'Shipment(s) Booked with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
          }
          else {
            $errors = array_map(function ($row, $errors) {
              return $row . ':' . PHP_EOL . implode(' | ', $errors);
            }, array_keys($errors), $errors);

            return redirect()->back()->withErrors($errors);
          }
        }
        else {
          return redirect()->back()->with('error', 'No Shipments in File');
        }
      }
      else {
        return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
      }
    }
}