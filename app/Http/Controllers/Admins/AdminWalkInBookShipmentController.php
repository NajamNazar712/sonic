<?php

namespace App\Http\controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\DeliveryType;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\Zone;
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
    private function unique_order_id($user_id,$order_id) {
        return !(Shipment::where('user_id',$user_id )->where('order_id', $order_id)->exists());
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

    static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id) {
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

        $shipment->amount = $amount;
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;
        $shipment->save();

        $shipment_id = $shipment->id;

        AdminPickupsController::generate($shipment_id);

        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL);

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
        $booking_types = BookingType::select('id')->where('id', '=', 4)->first();
        $use = User::with('shipping.city')->find(session('user_id'));
        $cities = City::where('pickup', 1)->where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $consignee_cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode = ShippingMode::where('id','!=', 4)->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $payment_modes = PaymentMode::whereIn('id', [4,1])->get();


        return view('admin.shipment.book.walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode , 'u' => $use, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'payment_modes' => $payment_modes,'consignee_cities' => $consignee_cities]);
    }

    static public function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id) {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }

    public function walk_in_store(Request $request){
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
                    $check = WalkInStandardWeightCharge::where(['shipping_mode_id' => $request->shipping_mode, 'delivery_type_id' => $request->delivery_type])->first();
                    if ($valid) {
                        if($request->actual_weight >= $check['actual_weight'] && $request->charges_per_kg >= $check['chargeable_weight']){

                        $service_type_id = $request->input('selected_service_type');

                        $this->set_service_type($service_type_id);

                            $pickup_city_id = $request->input('new_pickup_city');

                            $pickup_address_id = $this->add_pickup_address($user_id, $request->input('new_pickup_address'), $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_phone_number_1'), $request->input('new_pickup_email_address'), $pickup_city_id);

                        if ($request->filled('information_display')) {
                            $information_display = TRUE;
                        }
                        else {
                            $information_display = FALSE;
                        }
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

                        $pickup_date = $request->input('pickup_date_formatted');

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

                        $amount = str_replace(',', '', $request->input('total_receivable'));
                        $payment_mode_id = $request->input('payment_mode');

                        $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id);

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
                        return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);             }
                        else{
                            return redirect()->back()->with('error', 'On selected Shipping Mode and Delivery Type Actual weight must be greater then '. $check['actual_weight'] .' , Charges per KG must be greater then '. $check['chargeable_weight'] .'.');
                        }
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

    public function add_fuel_surcharge_gst_total(Request $request){
        $fuel = StandardFuelSurcharge::where('shipping_mode_id',$request->shipping_mode_id)->first();
        $fuel_surcharge = ($fuel['fuel_surcharge']/100)*($request->total_c);
        $city = City::where('id',$request->city_id)->first();
        $zone = Zone::where('id',$city['zone_id'])->first();
        $gst = $zone['gst']*($request->total_c + $fuel_surcharge);
        $receivable = $fuel_surcharge + $request->total_c + $gst;
        return response()->json(['gst'=>$gst, 'fuel'=>$fuel_surcharge, 'receivable'=>$receivable]);
    }
}
