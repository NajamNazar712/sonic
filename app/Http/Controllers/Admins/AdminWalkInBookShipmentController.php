<?php

namespace App\Http\controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\ChargesModes;
use App\Http\Models\DeliveryType;
use App\Http\Models\WalkInCities;
use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\BookingType;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\City;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Models\PaymentMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use Illuminate\Support\Facades\Input;

use Auth;

use Validator;
use Illuminate\Validation\Rule;



class AdminWalkInBookShipmentController extends Controller
{
    private function unique_order_id($user_id, $order_id) {
        return !(Shipment::where('user_id', $user_id)->where('order_id', $order_id)->exists());
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

    static public function book($user_id, $service_type_id, $pickup_address_id, $pickup_city_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $fuel_surcharge, $actual_weight, $gst, $weight_charges, $r_amount, $delivery_type, $charges_mode_id) {
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

        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->actual_weight = $actual_weight;
        $shipment->estimated_weight = $actual_weight;
        $shipment->chargeable_weight = $actual_weight;
        $shipment->weight_charges = $weight_charges;
        $shipment->fuel_surcharge = $fuel_surcharge;
        $shipment->gst = $gst;
        $shipment->amount = $amount;
        $shipment->received_amount = $r_amount;
        $shipment->payment_mode_id = 1;
        $shipment->walk_in_delivery_type_id = $delivery_type;
        $shipment->charges_mode_id = $charges_mode_id;

        $self_collection = FALSE;

        if ($delivery_type == 2 && City::find($pickup_city_id)->hub_id == City::find($consignee_city_id)->hub_id) {
            $shipment->shipper_status_id = 15;
            $shipment->consignee_status_id = 15;

            $self_collection = TRUE;
        }
        else {
            $shipment->shipper_status_id = 2;
            $shipment->consignee_status_id = 2;
        }

        $shipment->save();

        $shipment_id = $shipment->id;

        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, NULL, Auth::id());

        ShipmentsJourneyController::add($shipment_id, 2, 2, NULL, NULL, NULL, Auth::id());

        if ($self_collection) {
            ShipmentsJourneyController::add($shipment_id, 15, 15, NULL, NULL, NULL, Auth::id());
        }

        return $shipment_id;
    }

    static public function add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $type) {
        $shipment_item = new ShipmentItem();

        $shipment_item->shipment_id = $shipment_id;
        $shipment_item->product_type_id = $product_type_id;
        $shipment_item->description = $item_description;
        $shipment_item->quantity = $item_quantity;
        $shipment_item->type = $type;

        $shipment_item->save();
    }

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index() {
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();
        $user_id = $check_id['setting_value'];
        $booking_types = BookingType::select('id')->where('id', '=', 4)->first();
        $user_shipping_infos = UserShippingInfo::where('user_id', $user_id)->get();
        $cities = WalkInCities::join('cities as c', 'c.id', '=', 'walk_in_cities.city_id')
            ->select('c.name as city_name','c.id as city_id')->where('walk_in_cities.pickup', 1)->get();
        $consignee_cities = WalkInCities::join('cities as c', 'c.id', '=', 'walk_in_cities.city_id')
            ->select('c.name as city_name','c.id as city_id')->where('walk_in_cities.delivery', 1)->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode = ShippingMode::where('id','!=', 4)->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $charges_modes = ChargesModes::where('id','!=', 3)->get();
        return view('admin.shipment.book.walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode , 'user_shipping_infos' => $user_shipping_infos, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes,'consignee_cities' => $consignee_cities]);
    }

    static public function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id) {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }

    public function walk_in_store(Request $request) {
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();

        $user_id = $check_id['setting_value'];

        if (BookingType::where('id', '!=', 3)->where('id', $request->input('selected_service_type'))->exists()) {
            if ($request->filled('order_id')) {
                $valid = $this->unique_order_id($user_id,$request->input('order_id'));
            }
            else {
                $valid = TRUE;
            }

            if (!empty($request->input('shipping_mode'))) {
                if ($valid) {

                    $service_type_id = $request->input('selected_service_type');

                        if ($request->input('pickup_address') == 0) {
                            $pickup_city_id = $request->input('new_pickup_city');
                            $new_pickup_address = City::select('name')->where('id',$pickup_city_id)->first();
                            $pickup_address = 'TRAX Office ' . $new_pickup_address['name'];
                            $pickup_address_id = $this->add_pickup_address($user_id, $pickup_address, $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_phone_number'), $request->input('new_pickup_email_address'), $pickup_city_id);
                        }
                        else {
                            $pickup_address_id = $request->input('pickup_address');

                            $user_shipping_info = UserShippingInfo::find($pickup_address_id);

                            $pickup_city_id = $user_shipping_info->city_id;
                        }

                        $information_display = TRUE;

                        if(isset($request->consignee_address)) {
                            $address = $request->input('consignee_address');
                        }
                        else{
                            $city_check = City::select('name')->where('id',$request->input('consignee_city'))->first();
                            $address = 'TRAX Office ' . $city_check['name'];
                        }
                    $consignee_city_id = $request->input('consignee_city');
                    $consignee_name = $request->input('consignee_name');
                    $consignee_address = $address;
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

                    $pickup_date = Carbon::now();

                    if ($request->filled('special_instructions')) {
                        $special_instructions = $request->input('special_instructions');
                    }
                    else {
                        $special_instructions = NULL;
                    }

                    $shipping_mode_id = $request->input('shipping_mode');

                    if ($request->input('shipping_mode') == 4) {
                        $same_day_timing_id = $request->input('same-day_timing');
                    }
                    else {
                        $same_day_timing_id = NULL;
                    }

                    $actual_weight = $request->actual_weight;
                    $charges_per_kg = $request->charges_per_kg;

                    $charges_mode_id = $request->charges_mode;
                    $delivery_type = $request->delivery_type;

                    $weight_charges = ROUND(($actual_weight * $charges_per_kg), 0, PHP_ROUND_HALF_DOWN);

                    $fuel = StandardFuelSurcharge::where('shipping_mode_id',$shipping_mode_id)->first();
                    $fuel_surcharge = ROUND(($fuel['fuel_surcharge']/100)*($weight_charges), 0, PHP_ROUND_HALF_DOWN);

                    $city = City::where('id',$pickup_city_id)->first();
                    $zone = Zone::where('id',$city['zone_id'])->first();
                    $gst = ROUND(($zone['gst']*($weight_charges + $fuel_surcharge)), 0, PHP_ROUND_HALF_DOWN);

                    if($request->charges_mode == 1) {
                        $receivable = ROUND(($fuel_surcharge + $weight_charges + $gst), 0, PHP_ROUND_HALF_DOWN);

                        $amount = 0;

                        $r_amount = $receivable;
                    }
                    else{
                        $receivable = ROUND(($fuel_surcharge + $weight_charges + $gst), 0, PHP_ROUND_HALF_DOWN);

                        $amount = $receivable;

                        $r_amount = NULL;
                    }

                    $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $pickup_city_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $fuel_surcharge, $actual_weight, $gst, $weight_charges, $r_amount, $delivery_type, $charges_mode_id);

                    $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

                        $product_type_id = $request->input('product_type');

                        if ($request->filled('item_description')) {
                            $item_description = $request->input('item_description');
                        }
                        else {
                            $item_description = NULL;
                        }

                        $item_quantity = $request->input('item_quantity');

                        $type = 0;

                        $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $type);


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
            else {
                return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
            }
        }
        else {
            return redirect()->back()->with('error', 'Invalid Service Type Selected');
        }
    }

    public function check_standard_weight(Request $request){
        if($request->shipping_mode != null && $request->delivery_type != null && $request->consignee_city != null && $request->pickup_city != null && $request->delivery_type != null) {
            $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $request->shipping_mode, 'delivery_type_id' => $request->delivery_type])->first();
            if ($request->pickup_city != $request->consignee_city) {
                $city_zone = City::where('id', $request->consignee_city)->first();
                $zone = ZoneClassCity::where(['city_id' => $request->consignee_city, 'zone_id' => $city_zone['zone_id']]);
                if ($zone->exists()) {
                    $zone = $zone->first();

                    if ($zone['class'] == 0) {
                        $check_zone = $check['chargeable_weight_charges_class_0'];
                    } elseif ($zone['class'] == 1) {
                        $check_zone = $check['chargeable_weight_charges_class_1'];
                    } elseif ($zone['class'] == 2) {
                        $check_zone = $check['chargeable_weight_charges_class_2'];
                    } else {
                        $check_zone = $check['chargeable_weight_charges_class_3'];
                    }
                    if ($request->actual_weight < $check['actual_weight']) {
                        return response()->json(['status' => 1, 'error' => 'Actual Weight must be greater then or equal to ' . $check['actual_weight']]);
                    } elseif ($request->charges_per_kg < $check_zone) {
                        return response()->json(['status' => 0, 'error' => 'Charges per kg must be greater then or equal to ' . $check_zone]);
                    } else {
                        return response()->json(['status' => 2, 'error' => '']);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => "Zone class does'nt exists"]);
                }
            }
            else{
                if ($request->actual_weight < $check['actual_weight']) {
                    return response()->json(['status' => 1, 'error' => 'Actual Weight must be greater then or equal to ' . $check['actual_weight']]);
                } elseif ($request->charges_per_kg < $check['chargeable_weight_local']) {
                    return response()->json(['status' => 0, 'error' => 'Charges per kg must be greater then or equal to ' .  $check['chargeable_weight_local']]);
                } else {
                    return response()->json(['status' => 2, 'error' => '']);
                }
            }
        }
        else{
            if($request->pickup_city == null){
                return response()->json(['status' => 3, 'error' => 'Pickup city is required']);
            }
            elseif($request->delivery_type == null){
                return response()->json(['status' => 4, 'error' => 'Delivery type is required']);
            }
            elseif($request->consignee_city == null){
                return response()->json(['status' => 5, 'error' => 'Consignee city is required']);
            }
            elseif($request->shipping_mode == null){
                return response()->json(['status' => 6, 'error' => 'Shipping mode is required']);
            }
        }
    }

    public function add_fuel_surcharge_gst_total(Request $request){
        $weight_charges = ROUND($request->weight_charges, 0, PHP_ROUND_HALF_DOWN);

        $fuel = StandardFuelSurcharge::where('shipping_mode_id',$request->shipping_mode_id)->first();
        $fuel_surcharge = ROUND(($fuel['fuel_surcharge']/100)*($weight_charges), 0, PHP_ROUND_HALF_DOWN);

        $city = City::where('id',$request->city_id)->first();
        $zone = Zone::where('id',$city['zone_id'])->first();
        $gst = ROUND(($zone['gst']*($weight_charges + $fuel_surcharge)), 0, PHP_ROUND_HALF_DOWN);

        $total_charges = ROUND(($weight_charges + $fuel_surcharge), 0, PHP_ROUND_HALF_DOWN);

        $receivable = ROUND(($fuel_surcharge + $weight_charges + $gst), 0, PHP_ROUND_HALF_DOWN);

        return response()->json(['gst'=>$gst, 'fuel'=>$fuel_surcharge, 'total_charges' => $total_charges, 'receivable'=>$receivable]);
    }

    public function order_id(Request $request) {
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();

        $user_id = $check_id['setting_value'];

        if ($request->filled('order_id')) {
            return json_encode($this->unique_order_id($user_id, $request->input('order_id')));
        }
        else {
            return 'false';
        }
    }

    public function print_air_waybill(Request $request) {
        $user_type = NULL;
        $user_id = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;
        }
        else if (Auth::guard('web')->check()) {
            $user_type = 1;
        }
        else if (Auth::guard('substitute_users')->check()) {
            $user_type = 2;
        }

        if ($user_type) {
            $user_id = Auth::id();

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
                          }

                          * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                          }

                          body {
                            background: none !important;
                            color: #09262e !important;
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

                          td.replacement span {
                            width: 22px;
                          }

                          td.replacement span img {
                            display: block;
                            width: 100%;
                            margin: auto;
                            background: #c8c8c8;
                            border-radius: 25px;
                          }
                          
                          .invoice {
                                page-break-before: always;
                           }
                           
                           .invoice table.table-bordered tbody tr td {
                            width: auto !important;
                          }
                        </style>
                      </head>
                      <body>
                        <div>
            ';

            $shipment_details = '';

            $check_id = GlobalSettings::select('setting_value')->where('type', 'Walk-In')->first();
            $user_id = $check_id['setting_value'];

            $shipment = Shipment::where('id',$request->ids)->first();

            if ($user_id == $shipment->user_id) {
                $table_start = '
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Serivce</strong></td>
                            ';
                    $table_start  .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                            <td class="color primary"><strong>Datetime</strong></td>
                            <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                            <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                ';

                $table_start .= '
                                <td class="color primary"><strong>Order ID</strong></td>
                                <td>' . $shipment->order_id . '</td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                                <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                              </tr>
                              <tr>
                                <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                                <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                              </tr>
                              <tr>
                                <td class="color secondary"><strong>Name</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->user->name . ' (' . $shipment->pickup_address->poc . ')</td>
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                                <td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                    <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                                <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              </tr>
                ';

                $table_end = '
                              <tr>
                                <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Estimated Weight</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . ' kg</strong></td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                ';

                if ($shipment->charges_mode_id == 1) {
                    $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs 0</strong></td>
                    ';
                }
                else {
                    $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                    ';
                }

                $table_end .= '
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top"><em>Kindly do not give any addtional charges to the Rider/Courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                              </tr>
                            </tbody>
                          </table>

                          <hr>
                ';

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

            $html .= $shipment_details;

            if ($request->has('twice')) {
                $html .= $shipment_details;
            }

            $invoice = '<div class="invoice p-1">
                    <table class="table table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-left align-middle">
                            <img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mb-1">
                            <div><strong>TRAX ONLINE PRIVATE LIMITED</strong></div>
                            <div><strong>Address:</strong> Plot #4, DMCHS, Block #7/8, Adjacent to IBL Building Centre, Tipu Sultan Road, Karachi.</div>
                            <div><strong>NTN:</strong> 7930679-5</div>
                          </td>
                          <td class="text-center align-middle color primary"><strong>INVOICE</strong></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>'. $shipment->pickup_address->poc .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>'. $shipment->pickup_address->phone .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>Tracking No.</strong></td>
                                    <td>'. $shipment->tracking_number .'</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($shipment->created_at)->format('d/m/Y') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';
            $invoice_serial_number = 1;

            $total_weight_charges = 0;
            $total_weight_charges = $shipment->weight_charges;
            $total_fuel_surcharge = $shipment->fuel_surcharge;
            $total_charges = 0;
            $total_charges = $total_weight_charges + $total_fuel_surcharge;
            $total_gst = 0;
            $total_gst = $shipment->gst;
            $total_invoice_amount = 0;
            $total_invoice_amount = $total_charges + $total_gst;
            $invoice_details = '';
            $invoice_details .= '
            <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                            <td class="color primary text-left"><strong>Invoice Summary</strong></td>
                            <td class="color primary text-right" style="width: 20% !important;"><strong>Amount (PKR)</strong></td>
                        </tr>
                        <tr>
                          <td class="text-left">Weight Charges</td>
                          <td class="text-right">' . number_format($total_weight_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Fuel Surcharge</td>
                          <td class="text-right">' . number_format($total_fuel_surcharge) . '</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format($total_invoice_amount) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';
            $invoice .= $invoice_details;
            $html .= $invoice;

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
}
