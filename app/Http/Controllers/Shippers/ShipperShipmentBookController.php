<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\ChargesModes;
use App\Http\Models\ConsigneeInfo;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\DeliveryType;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\ZoneClassCity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsAirWaybillJourneyController;
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
use App\Http\Models\Admin\Admin;
use App\Http\Models\Shipper\SubstituteUser;

use App\Jobs\ProcessShipmentBooking;

use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Validator;
use Illuminate\Validation\Rule;

use SnappyPDF;

class ShipperShipmentBookController extends Controller
{
//    private function unique_order_id($order_id) {
//        return !(Shipment::where('user_id', session('user_id'))->where('order_id', $order_id)->exists());
//    }

    private function set_service_type($service_type_id) {
        $service_type = BookingType::find($service_type_id);

        session(['service_type_id' => $service_type->id]);
        session(['service_type_name' => $service_type->booking_type]);
    }

    static public function add_pickup_address($user_id, $address, $person_of_contact, $vendor, $phone_number, $email_address, $city_id, $default, $hidden = FALSE) {
        $user_shipping_info = new UserShippingInfo();

        $user_shipping_info->user_id = $user_id;
        $user_shipping_info->pickup_address = $address;
        $user_shipping_info->poc = $person_of_contact;
        $user_shipping_info->vendor = $vendor;
        $user_shipping_info->phone = $phone_number;
        $user_shipping_info->email = $email_address;
        $user_shipping_info->city_id = $city_id;
        $user_shipping_info->default_address = $default;

        if ($hidden) {
            $user_shipping_info->hidden = 2;
        }

        $user_shipping_info->save();

        return $user_shipping_info->id;
    }

    static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id) {
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
        $shipment->charges_mode_id = $charges_mode_id;
        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;


        $shipment->booked_by = session('user_type');
        $shipment->save();

        $shipment_id = $shipment->id;

        AdminPickupsController::generate($shipment_id);

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
        $this->middleware('auth:web,substitute_users')->except(['print_air_waybill', 'print_air_waybill_custom_size', 'corporate_invoice']);

        $this->middleware('auth:admin,web,substitute_users')->only(['print_air_waybill', 'print_air_waybill_custom_size', 'corporate_invoice']);

        $this->middleware('Permission');
    }

    public function index() {
        // $time = Carbon::today()->addHour(15);
        // $current_time = Carbon::now();
        $date = Carbon::today();
        // if($current_time > $time){
        //     $date = Carbon::tomorrow();
        // }
        $booking_types = BookingType::whereNotIn('id', [4, 3])->get();
        $user = User::with('shipping.city')->find(session('user_id'));
        $cities = City::where('pickup', 1)->where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $consignee_cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        $check = NonServiceArea::pluck('name')->toArray();
        $charges_modes = ChargesModes::whereIn('id', [4])->get();
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if($air_waybill->exists()){
            $air_waybill = $air_waybill->first();
        }
        else{
            $air_waybill = null;
        }

        return view('client.shipment.book.index')->with(['booking_types' => $booking_types, 'user' => $user, 'cities' => $cities, 'products' => $products, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes,'consignee_cities' => $consignee_cities, 'check' => $check, 'charges_modes' => $charges_modes, 'date'=> $date, 'air_waybill' => $air_waybill]);
    }

    public function shipping_modes(Request $request) {
        $shipper_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1);
        $user = User::where('id',session('user_id'))->first();

        if ($shipper_shipping_modes->exists()) {
            $shipper_shipping_modes = $shipper_shipping_modes->pluck('shipping_mode_id')->toArray();

            $city_shipping_modes = CityDelivery::where('city_id', $request->consignee_city_id)->where('booking_type_id', $request->service_type_id)->whereIn('shipping_mode_id', $shipper_shipping_modes);

            if ($city_shipping_modes->exists()) {
                $city_shipping_modes = $city_shipping_modes->pluck('shipping_mode_id')->toArray();

                if ($request->pickup_city_id != $request->consignee_city_id) {
                    $city_shipping_modes = array_diff($city_shipping_modes, [4]);
                }

                if (!empty($city_shipping_modes)) {
                    $shipping_modes = ShippingMode::whereIn('id', $city_shipping_modes)->get();

                    return ['status' => 0, 'success' => 'Shipping Modes Updated', 'shipping_modes' => $shipping_modes, 'default_shipping_mode' => $user['default_shipping_mode']];
                }
                else {
                    return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type, Pickup City and Consignee City'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type and Consignee City'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipping Modes has been Enabled for you'];
        }
    }

    public function check_cod_cap_zone_classes(Request $request){
        if($request->consignee_city != null && $request->pickup_city != null) {
            if ($request->pickup_city != $request->consignee_city) {
                $city_zone = City::where('id', $request->consignee_city)->first();
                $zone = ZoneClassCity::where(['city_id' => $request->consignee_city, 'zone_id' => $city_zone['zone_id']]);
                $class_a = GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->first();
                $class_b = GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->first();
                $class_c = GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->first();
                $class_d = GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->first();
                if ($zone->exists()) {
                    $zone = $zone->first();

                    if ($zone['class'] == 0) {
                        $check_zone = $class_a['setting_value'];
                    } elseif ($zone['class'] == 1) {
                        $check_zone = $class_b['setting_value'];
                    } elseif ($zone['class'] == 2) {
                        $check_zone = $class_c['setting_value'];
                    } else {
                        $check_zone = $class_d['setting_value'];
                    }
                    if ($request->amount > $check_zone) {
                        return response()->json(['status' => 1, 'error' => 'Amount must be smaller then or equal to ' . $check_zone]);
                    } else {
                        return response()->json(['status' => 2, 'error' => '']);
                    }
                } else {
                    return response()->json(['status' => 0, 'error' => "Zone class does'nt exists"]);
                }
            }
            else{
                    return response()->json(['status' => 2, 'error' => '']);
            }
        }
        else{
            if($request->pickup_city == null){
                return response()->json(['status' => 3, 'error' => 'Pickup city is required']);
            }
            else{
                return response()->json(['status' => 4, 'error' => 'Consignee city is required']);
            }
        }
    }

    public function store(Request $request) {
        if (BookingType::whereNotIn('id', [3, 4])->where('id', $request->input('selected_service_type'))->exists()) {
            if (!empty($request->input('shipping_mode'))) {
                    $user_id = session('user_id');

                if($request->input('consignee_email_address')){
                    $user_email_id = $request->input('consignee_email_address');
                }
                else{
                    $user_email = User::find($user_id);
                    $user_email_id = $user_email->email;
                }

                    $service_type_id = $request->input('selected_service_type');

                    if ($service_type_id != 5) {
                        $this->set_service_type($service_type_id);
                    }

                    if ($request->input('pickup_address') == 0) {
                        if ($service_type_id == 5) {
                            return redirect()->back()->with('error', 'New Pickup Address cannot be selected for Reverse Pickup');
                        }

                        $pickup_city_id = $request->input('new_pickup_city');
                        if($request->input('make_default_address') == 1){
                            $default = 1;
                        }
                        else{
                            $default = 0;
                        }
                        UserShippingInfo::where('user_id', $user_id)->update(['default_address' => 0]);

                        $pickup_address_id = $this->add_pickup_address($user_id, $request->input('new_pickup_address'), $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_vendor'), $request->input('new_pickup_phone_number'), $request->input('new_pickup_email_address'), $pickup_city_id, $default);
                    }
                    else {
                        if ($service_type_id != 5) {
                            $pickup_address_id = $request->input('pickup_address');

                            $user_shipping_info = UserShippingInfo::find($pickup_address_id);

                            $pickup_city_id = $user_shipping_info->city_id;
                        }
                        else {
                            $pickup_address_id = $this->add_pickup_address($user_id, $request->input('consignee_address'), $request->input('consignee_name'), NULL, $request->input('consignee_phone_number_1'), $user_email_id, $request->input('consignee_city'), 0, TRUE);

                            $pickup_city_id = $request->input('consignee_city');

                            $pickup_address_id_for_delivery = $request->input('pickup_address');
                        }
                    }
                    if ($service_type_id != 5) {
                        if ($request->filled('information_display')) {
                            $information_display = TRUE;
                        } else {
                            $information_display = FALSE;
                        }
                    }
                    else{
                        $information_display = TRUE;
                    }

                    if ($service_type_id != 5) {
                        $consignee_city_id = $request->input('consignee_city');
                        $consignee_name = $request->input('consignee_name');
                        if (isset($request->consignee_address)) {
                            $consignee_address = $request->input('consignee_address');
                        } else {
                            $city_check = City::select('name')->where('id', $request->input('consignee_city'))->first();
                            $consignee_address = 'TRAX Office ' . $city_check['name'];
                        }
                        $consignee_phone_number_1 = $request->input('consignee_phone_number_1');

                        if ($request->filled('consignee_phone_number_2')) {
                            $consignee_phone_number_2 = $request->input('consignee_phone_number_2');
                        } else {
                            $consignee_phone_number_2 = NULL;
                        }

                        if ($request->filled('consignee_email_address')) {
                            $consignee_email_address = $request->input('consignee_email_address');
                        } else {
                            $consignee_email_address = NULL;
                        }
                    }
                    else {
                        $user_shipping_info = UserShippingInfo::find($pickup_address_id_for_delivery);

                        $consignee_city_id = $user_shipping_info->city_id;
                        $consignee_name = $user_shipping_info->poc;
                        $consignee_address = $user_shipping_info->pickup_address;
                        $consignee_phone_number_1 = $user_shipping_info->phone;
                        $consignee_phone_number_2 = NULL;
                        $consignee_email_address = $user_shipping_info->email;
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
                    if ($service_type_id != 5) {
                        $charges_mode_id = $request->charges_mode;
                    }
                    else{
                        $charges_mode_id = 4;
                    }

                    if ($request->input('shipping_mode') == 4) {
                        $same_day_timing_id = $request->input('same-day_timing');
                    }
                    else {
                        $same_day_timing_id = NULL;
                    }

                    $amount = str_replace(',', '', $request->input('amount'));
                    if ($service_type_id != 5) {
                        $payment_mode_id = $request->input('payment_mode');
                    }
                    else{
                        $payment_mode_id = 1;
                    }

                    $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id);
                    $this->add_consignee_info($user_id, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address);
                    $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

                    if ($service_type_id == 1 || $service_type_id == 5) {
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
                    $check = NonServiceArea::pluck('name')->toArray();
                    $msg_string = null;
                    $str_arr = null;
                    $str_arr = preg_split("/[ ,]+/", $consignee_address);
                    foreach ($check as $nsa) {
                        foreach ($str_arr as $arr_value) {
                            if (strtolower($nsa) == strtolower($arr_value)) {
                                $con_nsa = $arr_value;
                                if ($msg_string != null) {
                                    $msg_string = $msg_string . ', ' . $arr_value;
                                } else {
                                    $msg_string = $arr_value;
                                }
                            }
                        }
                    }

                    if ($msg_string != null) {
                        NotificationsController::send(32, $shipment_id, $msg_string);
                    }
                    return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
            }
            else {
                return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
            }
        }
        else {
            return redirect()->back()->with('error', 'Invalid Service Type Selected');
        }
    }

//    public function order_id(Request $request) {
//        if ($request->filled('order_id')) {
//            return json_encode($this->unique_order_id($request->input('order_id')));
//        }
//        else {
//            return 'false';
//        }
//    }
    public function shipment_check(Request $request){
        $shipment_ids = array();
        if($request->ids){
            $i = 0;
            $sticker = TRUE;

            foreach ($request->ids as $id){
                $shipment = Shipment::find($id);
                if($shipment){
                    if($shipment->shipper_status_id == 1){
                        $shipment_ids[$i] = $id;
                        $i++;

                        if ($shipment->user_id != 2842) {
                            $sticker = FALSE;
                        }
                    }
                }
            }
            if($i > 0) {
                return ['status' => 1, 'ids' => $shipment_ids, 'sticker' => $sticker];
            }
            else{
                return ['status' => 2];
            }
        }
        else{
            return ['status' => 2];
        }
    }

    public static function air_waybill($user_type, $user_id, $ids, $body_only = FALSE, $type = NULL) {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        if ($user_type == 3) {
            $user_name = Admin::find($user_id)->name . ' (Admin)';
        }
        else if ($user_type == 1) {
            $user_name = User::find($user_id)->name . ' (Shipper)';
        }
        else if ($user_type == 2) {
            $user_name = SubstituteUser::where('user_id',$user_id)->select('name')->first() . ' (Sub-Shipper)';
        }
        else if ($user_type == 4) {
            $user_name = User::find($user_id)->name . ' (API)';
        }
        else {
            $user_name = 'Unknown';
        }
        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        $html = '';

        if (!$body_only) {
            $html .= '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            ';

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                ';
            }
            else {
                $html .= '
                    <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
                ';
            }

            $html .= '
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

                      .void {
                        top: 0;
                        bottom: 0;
                        right: 0;
                        left: 0;
                        height: 80px;
                        font-size: 5rem;
                        line-height: 3.5rem;
                        opacity: 0.25;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
            ';

            if ($type == 'pdf') {
                $html .= '
                    <style>
                      body {
                        font-size: 0.75rem !important;
                        font-weight: bold !important;
                      }

                      td.replacement span {
                        width: auto !important;
                      }

                      .border.twice {
                        border-width: 1px !important;
                      }

                      .border.twice-top {
                        border-top-width: 1px !important;
                      }

                      .border.twice-bottom {
                        border-bottom-width: 1px !important;
                      }

                      .border.twice-left {
                        border-left-width: 1px !important;
                      }

                      .border.twice-right {
                        border-right-width: 1px !important;
                      }

                      .font-small {
                        font-size: 0.65rem !important;
                      }
                    </style>
                ';
            }
        }

        $shipment_details = '';

        foreach($ids as $id) {
            $shipment = Shipment::find($id);

            ShipmentsAirWaybillJourneyController::add($id, $user_type, $user_id);

            if ($user_type == 3 || $user_id == $shipment->user_id) {
                $table_start = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';

                if ($user_type != 4 && $type != 'pdf') {
                    $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                }
                else {
                    if ($type != 'pdf') {
                        $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo.png') . '" width="150" class="d-block mx-auto">' . $print_details . '</td>
                        ';
                    }
                    else {
                        $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                        ';
                    }
                }

                if ($type != 'pdf') {
                    $table_start .= '
                                <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>

                                <td class="color primary border twice-left"><strong>Service</strong></td>
                    ';
                }
                else {
                    $table_start .= '
                                <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>

                                <td class="color primary border twice-left"><strong>Service</strong></td>
                    ';
                }

                if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                    $table_start  .= '
                                <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
                }
                else if ($shipment->booking_type_id == 2) {
                    if ($type != 'pdf') {
                        $table_start  .= '
                                <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                        ';
                    }
                    else {
                        $table_start  .= '
                                <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong></td>
                        ';
                    }
                }
                else if ($shipment->booking_type_id == 3) {
                    $table_start  .= '
                                <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
                    ';
                }
                else {
                    $table_start  .= '
                                <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
                }

                if ($type != 'pdf') {
                    $table_start  .= '
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
                    ';
                }
                else {
                    $table_start  .= '
                                <td class="color primary"><strong>Order ID</strong></td>
                                <td>' . $shipment->order_id . '</td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-left"><strong>Shipping</strong></td>
                                <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                    ';

                    $table_start .= '
                                <td class="color primary"><strong>Date</strong></td>
                                <td>' . $shipment->created_at->format('Y-m-d') . '</td>
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
                    ';
                }

                if ($shipment->booking_type_id != 4) {
                    $table_start .= '
                                <td colspan="3" class="border twice-right">' . $shipment->user->name . '</td>
                    ';
                }
                else {
                    $table_start .= '
                                <td colspan="3" class="border twice-right">' . $shipment->user->name . ' (' . $shipment->pickup_address->poc . ')</td>
                    ';
                }

                $table_start .= '
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                ';

                if ($shipment->information_display == 1) {
                    if ($shipment->booking_type_id != 4) {
                        $table_start .= '
                                <td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                        ';
                    }
                    else {
                        $table_start .= '
                                <td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                        ';
                    }
                }
                else {
                    $table_start .= '
                                <td rowspan="2" colspan="4" class="border twice-bottom twice-right"></td>
                    ';
                }

                $table_start .= '
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                ';

                if ($shipment->information_display == 1) {
                    if ($type != 'pdf') {
                        if ($shipment->booking_type_id != 4) {
                            $table_start .= '
                                <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                            ';
                        }
                        else {
                            $table_start .= '
                                <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                            ';
                        }
                    }
                    else {
                        if ($shipment->booking_type_id != 4) {
                            $table_start .= '
                                <td class="color secondary border twice-bottom"><strong>Phone No(s).</strong></td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                            ';
                        }
                        else {
                            $table_start .= '
                                <td class="color secondary border twice-bottom"><strong>Phone No(s).</strong></td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                            ';
                        }
                    }
                }
                else {
                }

                if ($type != 'pdf') {
                    $table_start .= '
                                <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              </tr>
                    ';
                }
                else {
                    $table_start .= '
                                <td class="color secondary border twice-bottom twice-left"><strong>Phone No(s).</strong></td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              </tr>
                    ';
                }

                if ($type != 'pdf') {
                    $table_end = '
                              <tr>
                                <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Estimated Weight</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . ' kg</strong></td>
                              </tr>
                              <tr>
                    ';

                    if ($shipment->booking_type_id == 5) {
                        $table_end .= '
                                <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                              </tr>
                              <tr>
                        ';
                    }
                    elseif ($shipment->booking_type_id != 4) {
                        $table_end .= '
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->payment_mode->mode . '</strong></td>
                        ';
                    }
                    else {
                        $table_end .= '
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                        ';
                    }
                }
                else {
                    $table_end = '
                              <tr>
                                <td rowspan="2" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="2" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Weight</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . ' kg</strong></td>
                    ';
                }

                if ($shipment->booking_type_id != 5) {
                    $table_end .= '
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                    ';

                    if ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1) {
                        $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs 0</strong></td>
                        ';
                    } else {
                        $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                        ';
                    }
                }

                $table_end .= '
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top font-small"><em>Kindly do not give any addtional charges to the Rider/Courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                              </tr>
                            </tbody>
                        </table>
                ';

                if ($shipment->booking_type_id != 4 && $shipment->charges_mode_id == 2 && $shipment->shipper_status_id == 1) {
                    $table_end .= '
                        <div class="void position-absolute m-auto text-center font-weight-bold">Void Air Waybill after Arrival</div>
                    ';
                }

                $table_end .= '
                      </div>
                ';

                if ($type != 'pdf') {
                    $table_end .= '
                      <hr>
                    ';
                }

                if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
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

                    $item = $items[0];

                    $shipment_details .= '
                              <tr>
                                <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Delivery Item</strong></td>
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

                    $shipment_details .= $table_end;
                }
                else if ($shipment->booking_type_id == 3) {
                    $shipment_details .= $table_start;

                    foreach ($shipment->items as $item) {
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
                    }

                    $shipment_details .= $table_end;
                }
            }
        }

        $html .= $shipment_details;


        $settings = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if($settings->exists()){
            $settings = $settings->first();
            $prints = $settings->print_count;
        }
        else{
            $prints = 1;
        }

        for ($i=1 ; $i<$prints ; $i++) {
            $html .= $shipment_details;
        }

        if (!$body_only) {
            $html .= '
                    </div>
            ';

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                <script>
                  window.onload = function() {
                    window.print();
                  }
                </script>
                ';
            }

            $html .= '
                  </body>
                </html>
            ';
        }

        return $html;
    }

    public function print_air_waybill(Request $request) {

        $user_type = NULL;
        $user_id = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();
        }
        else if (Auth::guard('web')->check()) {
            $user_type = 1;

            $user_id = session('user_id');
        }
        else if (Auth::guard('substitute_users')->check()) {
            $user_type = 2;

            $user_id = session('user_id');
        }

        if ($user_type) {
            if ($request->sticker) {
                $shipment_ids = Shipment::whereIn('id', $request->ids)->orderBy('order_id', 'ASC')->orderBy('id', 'ASC')->pluck('id')->toArray();

                return $this->air_waybill_sticker_pdf($user_type, $user_id, $shipment_ids);
            }
            else {
                return $this->air_waybill($user_type, $user_id, $request->ids);
            }
        }
    }

    public function excel_index() {
        $booking_types = BookingType::whereNotIn('id',[3, 4, 5])->get();
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        $cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        $products = Product::all();

        $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

        $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();

        if (in_array(4, $user_shipping_modes)) {
            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        }
        else {
            $shipping_mode_same_day_timings = NULL;
        }

        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        $charges_modes = ChargesModes::whereIn('id', [4])->get();

        return view('client.shipment.book.excel')->with(['booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes]);
    }

    public function excel_store(Request $request) {
//        return $request;
        $user_id = session('user_id');
//        dd($request->all('form'));
        $names = [
            'service_type_id' => 'Service Type ID',
            'pickup_address_id' => 'Pickup Address ID',
            'information_display' => 'Information Display',
            'consignee_city_name' => 'Consignee City Name',
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
            'item_price' => 'Product Value',

            'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
            'replacement_item_description' => 'Replacement Item Description',
            'replacement_item_quantity' =>'Replacement Item Quantity',

            'pickup_date' => 'Pickup Date',
            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
            'shipping_mode_id' => 'Shipping Mode ID',
            'same_day_timing_id' => 'Same Day Timing ID',
            'amount' => 'Collection Amount',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID'
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

            'consignee_city_name.exists' => 'Given :attribute is of Invalid Name.',

            'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',

            'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: 03000000000.',
            'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: 03000000000.'
        ];

        $rules = [
            'service_type_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function($query) {
                $query->whereNotIn('id', [3, 4, 5]);
            })],
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id);
            })],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', 'exists:cities,name'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,190'],
            'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'consignee_phone_number_2' => ['nullable', 'regex:/^[0][0-9]{10}$/'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'order_id' => ['nullable', 'between:0,100'],

            'item_product_type_id' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required_if:service_type_id,1,2', 'between:0,500'],
            'item_quantity' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance' => ['required_if:service_type_id,1,2', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price' => ['required_if:item_insurance,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,500'],
            'replacement_item_quantity' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'pickup_date' => ['required', 'date_format:Y-m-d', 'after:yesterday'],
            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,10000'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })],
            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'amount' => ['required', 'integer', 'digits_between:1,20', 'min:0'],
            'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
                $query->whereNotIn('id', [2, 3]);
            })],
            'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function($query) {
                $query->whereIn('id', [4]);
            })]
        ];

        $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'order_id', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'replacement_item_product_type_id', 16 => 'replacement_item_description', 17 => 'replacement_item_quantity', 18 => 'pickup_date', 19 => 'special_instructions', 20 => 'estimated_weight', 21 => 'shipping_mode_id', 22 => 'same_day_timing_id', 23 => 'amount', 24 => 'payment_mode_id', 25 => 'charges_mode_id'];
//        $form= $request->shipments;
//        dd($form);
        if($file = $request->file('shipments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Service Type ID', 'Pickup Address ID', 'Show Information on Air Waybill', 'Consignee City Name', 'Consignee Name', 'Consignee Address', 'Consignee Phone Number 1 (03000000000)', 'Consignee Phone Number 2 (03000000000)', 'Consignee Email Address', 'Order ID', 'Item Product Type ID', 'Item Description', 'Item Quantity', 'Item Insurance', 'Product Value', 'Replacement Item Product Type ID', 'Replacement Item Description', 'Replacement Item Quantity', 'Pickup Date (YYYY-MM-DD)', 'Special Instructions', 'Estimated Weight (kg)', 'Mode of Shipment ID', 'Same Day Timing ID', 'Collection Amount', 'Mode of Payment ID', 'Charges Mode ID'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 25) {}
                elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            else {
                unset($spreadsheet[0]);
            }
        }

        if (!isset($spreadsheet) || !empty($spreadsheet)) {
            $rows = array();

            if (isset($spreadsheet)) {
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
            }
            else
            {
                $forms=$request->all();

                $forms = $forms['form'];
                foreach($forms as $form) {
                    $row = array();
                    foreach ($form as $key=>$value){
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
            }

            $errors = array();
            $nsa_error = array();
            $order_ids = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();


            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if (!isset($row['charges_mode_id'])) {
                  $rows[$key]['charges_mode_id'] = 4;
                }

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    foreach ($validate->errors()->toArray() as $key => $error_array) {
                        foreach ($error_array as $error) {
                            if (!isset($errors[$row_id][$key])) {
                                $errors[$row_id][$key] = $error;
                            }
                        }
                    }
                }

                if (empty($errors[$row_id])) {
                    if (!empty(trim($row['order_id']))) {
                        if (empty($order_ids)) {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        } else {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        }
                    }
                    $user_shipping_info = UserShippingInfo::find($row['pickup_address_id']);

                    if (!$user_shipping_info->status) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup Address ID #' . $row['pickup_address_id'] . ' is disabled';
                    }

                    if (!$user_shipping_info->city->status) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup Address\'s City: ' . $user_shipping_info->city->name . ' is deactivated';
                    }

                    if (!$user_shipping_info->city->zone_id) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup Address\'s City: ' . $user_shipping_info->city->name . ' is deactivated';
                    }

                    if (!$user_shipping_info->city->pickup) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup is not allowed for City: ' . $user_shipping_info->city->name;
                    }

                    $consignee_city = City::where('name', $row['consignee_city_name'])->first();

                    if (!$consignee_city->status) {
                        $errors[$row_id]['consignee_city'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }

                    if (!$consignee_city->zone_id) {
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }
                    if (!$consignee_city->zone_id) {
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }

                    $pickup_city_id = $user_shipping_info->city_id;

                    if ($consignee_city->id != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                        $errors[$row_id]['consignee_city_name'] = 'Same Day Delivery is not available for Different City Shipment';
                    }

                    if ($user_shipping_info->city->id != $consignee_city->id) {
                        $city_zone = City::where('id', $consignee_city->id)->first();
                        $zone = ZoneClassCity::where(['city_id' => $consignee_city->id, 'zone_id' => $city_zone['zone_id']]);
                        $class_a = GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->first();
                        $class_b = GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->first();
                        $class_c = GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->first();
                        $class_d = GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->first();
                        if ($zone->exists()) {
                            $zone = $zone->first();

                            if ($zone['class'] == 0) {
                                $check_zone = $class_a['setting_value'];
                            } elseif ($zone['class'] == 1) {
                                $check_zone = $class_b['setting_value'];
                            } elseif ($zone['class'] == 2) {
                                $check_zone = $class_c['setting_value'];
                            } else {
                                $check_zone = $class_d['setting_value'];
                            }
                            if ((int)$row['amount'] > $check_zone) {
                                $errors[$row_id]['amount'] = 'Amount must be smaller then or equal to ' . $check_zone;
                            }
                        } else {
                            $errors[$row_id]['amount'] = "Zone class does'nt exists";
                        }
                    }
//                            dd($row['consignee_address']);
                    if(!$request->excel_nsa) {
                        $con_nsa = array();
                        $msg_string = '';
                        $str_arr = null;
                        $str_arr = preg_split("/[ ,]+/", $row['consignee_address']);
                        foreach ($check as $nsa) {
                            foreach ($str_arr as $arr_value) {
                                if (strtolower($nsa) == strtolower($arr_value)) {
                                    $con_nsa[$row_id] = $arr_value;
                                    if ($msg_string != null) {
                                        $msg_string = $msg_string . ', ' . $arr_value;
                                    } else {
                                        $msg_string = $arr_value;
                                    }
                                }
                            }
                        }
                        if (isset($con_nsa[$row_id])) {
                            $nsa_error[$row_id]['msg'] = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                        }
                    }

                    if (!CityDelivery::where('city_id', $consignee_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                        $errors[$row_id]['consignee_city_name'] = 'Delivery is not allowed for City: ' . $consignee_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                    }
                }
            }
//                dd($nsa_error);

                if (empty($errors)) {
                    if (empty($nsa_error)) {
                        foreach ($rows as $key => $row) {
                            $row['user_id'] = $user_id;
                            $row['account_type_id'] = 1;
                            $row['nsas'] = $check;
                            $row['nsa'] = $request->excel_nsa;

                            dispatch(new ProcessShipmentBooking($row));
                        }

                        return redirect()->back()->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                    }
                    else {
                        return view('client.shipment.book.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error]);
                    }
                }
                    else {
                    $cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                    $booking_types = BookingType::whereNotIn('id', [3, 4, 5])->pluck('booking_type', 'id');
                    $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                        $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
                    })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                    $products = Product::pluck('product_name', 'id');

                    $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

                    $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->pluck('mode', 'id');

                    if (in_array(4, $user_shipping_modes)) {
                        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::pluck('timing', 'id');
                    } else {
                        $shipping_mode_same_day_timings = NULL;
                    }

                    $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->pluck('mode', 'id');
                    $charges_modes = ChargesModes::whereIn('id' , [4])->pluck('charges_mode','id');
                    $city_name = array();
                    foreach ($cities as $city) {
                        $city_name[$city->name] = $city->name;
                    }
                    return view('client.shipment.book.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'user_shipping_modes' => $user_shipping_modes, 'charges_modes' => $charges_modes]);
                }
        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    static public function corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id) {

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
        $shipment->walk_in_delivery_type_id = $delivery_type_id;
        $shipment->charges_mode_id = $charges_mode_id;

        $shipment->booked_by = session('user_type');
        $shipment->save();

        $shipment_id = $shipment->id;

        AdminPickupsController::generate($shipment_id);

        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL);

        return $shipment_id;
    }

    public function corporate_index() {
        // $time = Carbon::today()->addHour(15);
        // $current_time = Carbon::now();
        $date = Carbon::today();
        // if($current_time > $time){
        //     $date = Carbon::tomorrow();
        // }
        $booking_types = BookingType::whereNotIn('id', [4, 3])->get();
        $user = User::with('shipping.city')->find(session('user_id'));
        $cities = City::where('pickup', 1)->where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $consignee_cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $charges_modes = ChargesModes::whereIn('id', [2, 3])->get();
        $check = NonServiceArea::pluck('name')->toArray();
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if($air_waybill->exists()){
            $air_waybill = $air_waybill->first();
        }
        else{
            $air_waybill = null;
        }

        return view('client.shipment.book.corporate.index')->with(['booking_types' => $booking_types, 'user' => $user, 'cities' => $cities, 'products' => $products, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes,'consignee_cities' => $consignee_cities, 'check' => $check, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes,'date' => $date, 'air_waybill' => $air_waybill]);
    }

    public function corporate_store(Request $request) {

        if (!empty($request->input('shipping_mode'))) {
                $user_id = session('user_id');
                if($request->input('consignee_email_address')){
                    $user_email_id = $request->input('consignee_email_address');
                }
                else{
                    $user_email = User::find($user_id);
                    $user_email_id = $user_email->email;
                }

                $service_type_id = $request->input('selected_service_type');

                if ($service_type_id != 5) {
                    $this->set_service_type($service_type_id);
                }

                if ($request->input('pickup_address') == 0) {
                    if ($service_type_id == 5) {
                        return redirect()->back()->with('error', 'New Pickup Address cannot be selected for Reverse Pickup');
                    }

                    $pickup_city_id = $request->input('new_pickup_city');

                    $pickup_address_id = $this->add_pickup_address($user_id, $request->input('new_pickup_address'), $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_vendor'), $request->input('new_pickup_phone_number'), $request->input('new_pickup_email_address'), $pickup_city_id, 0);
                }
                else {
                    if ($service_type_id != 5) {
                        $pickup_address_id = $request->input('pickup_address');

                        $user_shipping_info = UserShippingInfo::find($pickup_address_id);

                        $pickup_city_id = $user_shipping_info->city_id;
                    }
                    else {
                        $pickup_address_id = $this->add_pickup_address($user_id, $request->input('consignee_address'), $request->input('consignee_name'), NULL, $request->input('consignee_phone_number_1'), $user_email_id, $request->input('consignee_city'), 0, TRUE);

                        $pickup_city_id = $request->input('consignee_city');
                    }
                }

                if ($service_type_id != 5) {
                    if ($request->filled('information_display')) {
                        $information_display = TRUE;
                    } else {
                        $information_display = FALSE;
                    }
                }
                else{
                    $information_display = TRUE;
                }

                if ($service_type_id != 5) {
                    $consignee_city_id = $request->input('consignee_city');
                    $consignee_name = $request->input('consignee_name');
                    if (isset($request->consignee_address)) {
                        $consignee_address = $request->input('consignee_address');
                    } else {
                        $city_check = City::select('name')->where('id', $request->input('consignee_city'))->first();
                        $consignee_address = 'TRAX Office ' . $city_check['name'];
                    }
                    $consignee_phone_number_1 = $request->input('consignee_phone_number_1');

                    if ($request->filled('consignee_phone_number_2')) {
                        $consignee_phone_number_2 = $request->input('consignee_phone_number_2');
                    } else {
                        $consignee_phone_number_2 = NULL;
                    }

                    if ($request->filled('consignee_email_address')) {
                        $consignee_email_address = $request->input('consignee_email_address');
                    } else {
                        $consignee_email_address = NULL;
                    }
                }
                else {
                    $user_shipping_info = UserShippingInfo::find($pickup_address_id);

                    $consignee_city_id = $user_shipping_info->city_id;
                    $consignee_name = $user_shipping_info->poc;
                    $consignee_address = $user_shipping_info->pickup_address;
                    $consignee_phone_number_1 = $user_shipping_info->phone;
                    $consignee_phone_number_2 = NULL;
                    $consignee_email_address = $user_shipping_info->email;
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

                if ($request->input('shipping_mode') == 4) {
                    $same_day_timing_id = $request->input('same-day_timing');
                }
                else {
                    $same_day_timing_id = NULL;
                }

                $estimated_weight = $request->input('estimated_weight');
                $shipping_mode_id = $request->input('shipping_mode');

                if ($service_type_id != 5) {
                    $payment_mode_id = $request->input('payment_mode');
                    $delivery_type_id = $request->delivery_type;
                    $charges_mode_id = $request->charges_mode;
                }
                else{
                    $delivery_type_id = 1;
                    $charges_mode_id = 3;
                    $payment_mode_id = 1;
                }

                $amount = str_replace(',', '', $request->input('amount'));

                $shipment_id = $this->corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id);

                $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
                $this->add_consignee_info($user_id, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address);
                if ($service_type_id == 1 || $service_type_id == 5) {
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
            $check = NonServiceArea::pluck('name')->toArray();
            $msg_string = null;
            $str_arr = null;
            $str_arr = preg_split("/[ ,]+/", $consignee_address);
            foreach ($check as $nsa) {
                foreach ($str_arr as $arr_value) {
                    if (strtolower($nsa) == strtolower($arr_value)) {
                        $con_nsa = $arr_value;
                        if ($msg_string != null) {
                            $msg_string = $msg_string . ', ' . $arr_value;
                        } else {
                            $msg_string = $arr_value;
                        }
                    }
                }
            }

            if ($msg_string != null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
            }
                return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
            }
        else {
            return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
        }
    }

    public function corporate_invoice(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Invoice</title>

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
                    </style>
                  </head>
                  <body>
                    <div>
      ';

        $shipment_details = '';

        $shipment = Shipment::where('id',$request->ids)->first();
        if ($request->has('admin') || session('user_id') == $shipment->user_id) {
            $table_start = '
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
                ';

            if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                $table_start  .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
            }
            else if ($shipment->booking_type_id == 2) {
                $table_start  .= '
                            <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                    ';
            }
            else if ($shipment->booking_type_id == 3) {
                $table_start  .= '
                            <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
                    ';
            }
            else {
                $table_start  .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
            }

            $table_start  .= '
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
                ';

            if ($shipment->booking_type_id != 4) {
                $table_start .= '
                            <td colspan="3" class="border twice-right">' . $shipment->user->name . '</td>
                    ';
            }
            else {
                $table_start .= '
                            <td colspan="3" class="border twice-right">' . $shipment->user->name . ' (' . $shipment->pickup_address->poc . ')</td>
                    ';
            }

            $table_start .= '
                            <td class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="3">' . $shipment->consignee_name . '</td>
                          </tr>

                          <tr>
                ';

            if ($shipment->information_display == 1) {
                if ($shipment->booking_type_id != 4) {
                    $table_start .= '
                            <td class="color secondary"><strong>Address</strong></td>
                            <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                        ';
                }
                else {
                    $table_start .= '
                            <td class="color secondary"><strong>Address</strong></td>
                            <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                        ';
                }
            }
            else {
                $table_start .= '
                            <td rowspan="2" colspan="4" class="border twice-bottom twice-right"></td>
                    ';
            }

            $table_start .= '
                            <td class="color secondary border twice-left"><strong>Address</strong></td>
                            <td colspan="3">' . $shipment->consignee_address . '</td>
                          </tr>
                          <tr>
                ';

            if ($shipment->information_display == 1) {
                if ($shipment->booking_type_id != 4) {
                    $table_start .= '
                            <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                        ';
                }
                else {
                    $table_start .= '
                            <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                        ';
                }
            }
            else {
            }

            $table_start .= '
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
                ';

            if ($shipment->booking_type_id != 4) {
                $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->payment_mode->mode . '</strong></td>
                    ';
            }
            else {
                $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                    ';
            }

            $table_end .= '
                          </tr>
                          <tr>
                            <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                ';

            if ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1) {
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

            if ($shipment->booking_type_id == 1  || $shipment->booking_type_id == 4) {
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

                $item = $items[0];

                $shipment_details .= '
                        <tr>
                          <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Delivery Item</strong></td>
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

                $shipment_details .= $table_end;
            }
            else if ($shipment->booking_type_id == 3) {
                $shipment_details .= $table_start;

                foreach ($shipment->items as $item) {
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
                }

                $shipment_details .= $table_end;
            }
        }

        $html .= $shipment_details;

        if ($request->has('twice')) {
            $html .= $shipment_details;
        }

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

    public function corporate_excel_index() {
        $booking_types = BookingType::whereNotIn('id',[3, 4, 5])->get();
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        $cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        $products = Product::all();
        $delivery_types = DeliveryType::all();
        $charges_modes = ChargesModes::whereIn('id', [2, 3])->get();
        $min_chargeable_weights = CorporateMinChargeableWeight::where('user_id', session('user_id'))->get();

        $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

        $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();

        if (in_array(4, $user_shipping_modes)) {
            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        }
        else {
            $shipping_mode_same_day_timings = NULL;
        }

        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();

        return view('client.shipment.book.corporate.excel')->with(['booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'min_chargeable_weights' => $min_chargeable_weights]);
    }

    public function corporate_min_chargeable_weight(Request $request){
        $min_chargeable_weight = CorporateMinChargeableWeight::where(['user_id' => session('user_id'), 'shipping_mode_id' => $request->shipping_mode, 'delivery_type_id' => $request->delivery_type])->first();
        if($request->estimated_weight < $min_chargeable_weight['min_chargeable_weight']){
            return ['status' => 1, 'min' => $min_chargeable_weight['min_chargeable_weight']];
        }
        else{
            return ['status' => 0];
        }

    }

    public function corporate_shipping_modes(Request $request) {
        $shipper_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1);
        $user = User::where('id',session('user_id'))->first();

        if ($shipper_shipping_modes->exists()) {
            $shipper_shipping_modes = $shipper_shipping_modes->pluck('shipping_mode_id')->toArray();

            $city_shipping_modes = CityDelivery::where('city_id', $request->consignee_city_id)->where('booking_type_id', $request->service_type_id)->whereIn('shipping_mode_id', $shipper_shipping_modes);

            if ($city_shipping_modes->exists()) {
                $city_shipping_modes = $city_shipping_modes->pluck('shipping_mode_id')->toArray();

                if ($request->pickup_city_id != $request->consignee_city_id) {
                    $city_shipping_modes = array_diff($city_shipping_modes, [4]);
                }

                if (!empty($city_shipping_modes)) {
                    $shipping_modes = ShippingMode::whereIn('id', $city_shipping_modes)->get();

                    return ['status' => 0, 'success' => 'Shipping Modes Updated', 'shipping_modes' => $shipping_modes, 'default_shipping_mode' => $user['default_shipping_mode']];
                }
                else {
                    return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type, Pickup City and Consignee City'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type and Consignee City'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipping Modes has been Enabled for you'];
        }
    }

    public function corporate_excel_store(Request $request) {
        $user_id = session('user_id');
//        dd($request->all('form'));
        $names = [
            'service_type_id' => 'Service Type ID',
            'pickup_address_id' => 'Pickup Address ID',
            'delivery_type_id' => 'Delivery Type ID',
            'information_display' => 'Information Display',
            'consignee_city_name' => 'Consignee City Name',
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
            'item_price' => 'Product Value',

            'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
            'replacement_item_description' => 'Replacement Item Description',
            'replacement_item_quantity' =>'Replacement Item Quantity',

            'pickup_date' => 'Pickup Date',
            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
            'shipping_mode_id' => 'Shipping Mode ID',
            'same_day_timing_id' => 'Same Day Timing ID',
            'amount' => 'Collection Amount',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID'
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

            'consignee_city_name.exists' => 'Given :attribute is of Invalid Name.',

            'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',

            'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: 03000000000.',
            'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: 03000000000.'
        ];

        $rules = [
            'service_type_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function($query) {
                $query->whereNotIn('id', [3, 4, 5]);
            })],
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id);
            })],
            'delivery_type_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('delivery_types', 'id')],
            'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function($query) {
                $query->whereIn('id', [2, 3]);
            })],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', 'exists:cities,name'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,190'],
            'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'consignee_phone_number_2' => ['nullable', 'regex:/^[0][0-9]{10}$/'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'order_id' => ['nullable', 'between:0,100'],

            'item_product_type_id' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required_if:service_type_id,1,2', 'between:0,500'],
            'item_quantity' => ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance' => ['required_if:service_type_id,1,2', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price' => ['required_if:item_insurance,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,500'],
            'replacement_item_quantity' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'pickup_date' => ['required', 'date_format:Y-m-d', 'after:yesterday'],
            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,10000'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })],
            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'amount' => ['required', 'integer', 'digits_between:1,20', 'min:0'],
            'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
                $query->whereNotIn('id', [2, 3]);
            })]
        ];

        $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'delivery_type_id', 3 => 'information_display', 4 => 'consignee_city_name', 5 => 'consignee_name', 6 => 'consignee_address', 7 => 'consignee_phone_number_1', 8 => 'consignee_phone_number_2', 9 => 'consignee_email_address', 10 => 'order_id', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'replacement_item_product_type_id', 17 => 'replacement_item_description', 18 => 'replacement_item_quantity', 19 => 'pickup_date', 20 => 'special_instructions', 21 => 'estimated_weight', 22 => 'shipping_mode_id', 23 => 'same_day_timing_id', 24 => 'amount', 25 => 'payment_mode_id', 26 => 'charges_mode_id'];
//        $form= $request->shipments;
//        dd($form);
        if($file = $request->file('shipments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Service Type ID', 'Pickup Address ID', 'Delivery Type ID', 'Show Information on Air Waybill', 'Consignee City Name', 'Consignee Name', 'Consignee Address', 'Consignee Phone Number 1 (03000000000)', 'Consignee Phone Number 2 (03000000000)', 'Consignee Email Address', 'Order ID', 'Item Product Type ID', 'Item Description', 'Item Quantity', 'Item Insurance', 'Product Value', 'Replacement Item Product Type ID', 'Replacement Item Description', 'Replacement Item Quantity', 'Pickup Date (YYYY-MM-DD)', 'Special Instructions', 'Estimated Weight (kg)', 'Mode of Shipment ID', 'Same Day Timing ID', 'Collection Amount', 'Mode of Payment ID', 'Charges Mode ID'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 25) {}
                elseif ($header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            else {
                unset($spreadsheet[0]);
            }
        }

        if (!isset($spreadsheet) || !empty($spreadsheet)) {
            $rows = array();

            if (isset($spreadsheet)) {
                foreach ($spreadsheet as $spreadsheet_row) {
                    $row = array();

                    foreach ($spreadsheet_row as $key => $value) {
                        $row[$fields[$key]] = $value;
                    }

                    $rows[] = $row;
                }

                unset($spreadsheet);
            }
            else
            {
                $forms=$request->all();

                $forms = $forms['form'];
                foreach($forms as $form) {
                    $row = array();
                    foreach ($form as $key=>$value){
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
            }

            $errors = array();
            $order_ids = array();
            $nsa_error = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if (!isset($row['charges_mode_id'])) {
                  $rows[$key]['charges_mode_id'] = 3;
                }

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    foreach ($validate->errors()->toArray() as $key => $error_array) {
                        foreach ($error_array as $error) {
                            if (!isset($errors[$row_id][$key])) {
                                $errors[$row_id][$key] = $error;
                            }
                        }
                    }
                }

                if (empty($errors[$row_id])) {
                    if (!empty(trim($row['order_id']))) {
                        if (empty($order_ids)) {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        }
                        else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                        }
                    }

//                        dd($errors[$row_id]['amount']);

                    $user_shipping_info = UserShippingInfo::find($row['pickup_address_id']);

                    if (!$user_shipping_info->status) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup Address ID #' . $row['pickup_address_id'] . ' is disabled';
                    }

                    if (!$user_shipping_info->city->status) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup Address\'s City: ' . $user_shipping_info->city->name . ' is deactivated';
                    }

                    if (!$user_shipping_info->city->zone_id) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup Address\'s City: ' . $user_shipping_info->city->name . ' is deactivated';
                    }

                    if (!$user_shipping_info->city->pickup) {
                        $errors[$row_id]['pickup_address_id'] = 'Pickup is not allowed for City: ' . $user_shipping_info->city->name;
                    }

                    $consignee_city = City::where('name', $row['consignee_city_name'])->first();

                    if (!$consignee_city->status) {
                        $errors[$row_id]['consignee_city'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }

                    if (!$consignee_city->zone_id) {
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }
                    if (!$consignee_city->zone_id) {
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }

                    $pickup_city_id = $user_shipping_info->city_id;

                    if ($consignee_city->id != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                        $errors[$row_id]['consignee_city_name'] = 'Same Day Delivery is not available for Different City Shipment';
                    }

                    if ($user_shipping_info->city->id != $consignee_city->id) {
                        $city_zone = City::where('id', $consignee_city->id)->first();
                        $zone = ZoneClassCity::where(['city_id' => $consignee_city->id, 'zone_id' => $city_zone['zone_id']]);
                        $class_a = GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->first();
                        $class_b = GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->first();
                        $class_c = GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->first();
                        $class_d = GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->first();
                        if ($zone->exists()) {
                            $zone = $zone->first();

                            if ($zone['class'] == 0) {
                                $check_zone = $class_a['setting_value'];
                            } elseif ($zone['class'] == 1) {
                                $check_zone = $class_b['setting_value'];
                            } elseif ($zone['class'] == 2) {
                                $check_zone = $class_c['setting_value'];
                            } else {
                                $check_zone = $class_d['setting_value'];
                            }
                            if ((int)$row['amount'] > $check_zone) {
                                $errors[$row_id]['amount'] = 'Amount must be smaller then or equal to ' . $check_zone;
                            }
                        } else {
                            $errors[$row_id]['amount'] = "Zone class does'nt exists";
                        }
                    }

                    if(!$request->excel_nsa) {
                        $con_nsa = array();
                        $msg_string = '';
                        $str_arr = null;
                        $str_arr = preg_split("/[ ,]+/", $row['consignee_address']);
                        foreach ($check as $nsa) {
                            foreach ($str_arr as $arr_value) {
                                if (strtolower($nsa) == strtolower($arr_value)) {
                                    $con_nsa[$row_id] = $arr_value;
                                    if ($msg_string != null) {
                                        $msg_string = $msg_string . ', ' . $arr_value;
                                    } else {
                                        $msg_string = $arr_value;
                                    }
                                }
                            }
                        }
                        if (isset($con_nsa[$row_id])) {
                            $nsa_error[$row_id]['msg'] = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                        }
                    }

                    if (!CityDelivery::where('city_id', $consignee_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                        $errors[$row_id]['consignee_city_name'] = 'Delivery is not allowed for City: ' . $consignee_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                    }
                }
            }

            if (empty($errors)) {
                if (empty($nsa_error)) {
                $tracking_numbers = array();

                foreach ($rows as $key => $row) {
                    $row['user_id'] = $user_id;
                    $row['account_type_id'] = 2;
                    $row['nsas'] = $check;
                    $row['nsa'] = $request->excel_nsa;

                    dispatch(new ProcessShipmentBooking($row));
                }

                return redirect()->back()->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
            }
                else{
                    return view('client.shipment.book.corporate.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error]);
                }
            }
            else {
                $cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                $booking_types = BookingType::whereNotIn('id', [3, 4, 5])->pluck('booking_type','id');
                $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                    $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
                })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                $products = Product::pluck('product_name','id');
                $delivery_types = DeliveryType::pluck('delivery_type','id');;
                $charges_modes = ChargesModes::whereIn('id' , [2, 3])->pluck('charges_mode','id');

                $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

                $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->pluck('mode','id');

                if (in_array(4, $user_shipping_modes)) {
                    $shipping_mode_same_day_timings = ShippingModeSameDayTiming::pluck('timing','id');
                }
                else {
                    $shipping_mode_same_day_timings = NULL;
                }

                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->pluck('mode','id');
                $city_name = array();
                foreach ($cities as $city){
                    $city_name[$city->name]=$city->name;
                }

                return view('client.shipment.book.corporate.errors')->with(['data' => $rows,'errors' => $errors, 'cities' => $city_name,'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'user_shipping_modes' => $user_shipping_modes]);
            }
        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public static function add_consignee_info($shipper_id, $city_id, $name, $address, $phone1, $phone2 = NULL, $email = NULL){

        $consignee_info = ConsigneeInfo::where('phone_number_1', $phone1)->where('shipper_id', $shipper_id);
        if($consignee_info->exists()){

            $consignee_info = $consignee_info->first();

            $consignee_info->city_id = $city_id;
            $consignee_info->name = $name;
            $consignee_info->address = $address;
            $consignee_info->phone_number_1 = $phone1;
            $consignee_info->phone_number_2 = $phone2;
            $consignee_info->email = $email;
            $consignee_info->save();

        }else{

            $consignee_info = new ConsigneeInfo();

            $consignee_info->shipper_id = $shipper_id;

            $consignee_info->city_id = $city_id;

            $consignee_info->name = $name;

            $consignee_info->address = $address;

            $consignee_info->phone_number_1 = $phone1;

            $consignee_info->phone_number_2 = $phone2;

            $consignee_info->email = $email;

            $consignee_info->save();

        }
    }

    public function get_consignee_infos(Request $request){
        $data = array();
        $consignee_info = ConsigneeInfo::where('shipper_id', $request->shipper)->where('phone_number_1','LIKE', "%".$request->q."%");
        if($consignee_info->exists()){
            $consignee_info = $consignee_info->limit(10)->get();
            foreach ($consignee_info as $item) {
                $data[] = ['id' => $item->id, 'full_name' => $item->phone_number_1. ' / '.$item->name, 'text' => $item->name];
            }
            return response()->json(['status' => 1,'data' => $data,'total_count' => count($data)]);
        }

    }

    public function get_consignee_info(Request $request){
        $id = $request->id;
        if($id){
            $consignee_info = ConsigneeInfo::find($id);
            if($consignee_info){
                return response()->json(['status' => 1, 'details' => $consignee_info]);
            }else{
                return response()->json(['status' => 0, 'error' => 'Consignee Information not found!']);
            }
        }
    }

    public static function air_waybill_sticker_pdf($user_type, $user_id, $shipment_ids) {
        $air_waybills = array();

        foreach ($shipment_ids as $shipment_id) {
            $air_waybills[] = self::air_waybill($user_type, $user_id, [$shipment_id], FALSE, 'pdf');
        }

        $options = ['margin-top' => '0.125in', 'margin-bottom' => '0.125in', 'margin-left' => '0.125in', 'margin-right' => '0.125in', 'page-width' => '6in', 'page-height' => '4in'];

        $pdf = SnappyPDF::snappy()->getOutputFromHtml($air_waybills, $options);

        $filename = 'air_waybills.pdf';

        return new Response($pdf, 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ));
    }
}