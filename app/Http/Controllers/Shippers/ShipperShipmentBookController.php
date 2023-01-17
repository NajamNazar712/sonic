<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\FTLController;
use App\Http\Controllers\ConsigneeInformationController;
use App\Http\Controllers\Webhook\ShipmentStatusWebhookController;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\BookingDestinationMappingKeyword;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyBlacklisted;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\ChargesModes;
use App\Http\Models\ConsigneeInfo;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDeliveryTypeStatus;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\DeliveryType;
use App\Http\Models\DistributionProduct;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\Rates\Corporate\CorporateDefaultRateDestinationHub;
use App\Http\Models\Rates\Corporate\CorporateDefaultRateOriginHub;
use App\Http\Models\Rates\Corporate\CorporateRateDestinationHub;
use App\Http\Models\Rates\Corporate\CorporateRateOriginHub;
use App\Http\Models\Rates\RateDestinationHub;
use App\Http\Models\Rates\RateOriginHub;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\ShipmentDistributionProduct;
use App\Http\Models\ShipmentInvoice;
use App\Http\Models\ShipmentInvoiceItem;
use App\Http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentsAirWaybillJourney;
use App\Http\Models\ShipmentShipperReference;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\SubstituteUserShipment;
use App\Http\Models\ZoneClassCity;
use App\Jobs\ProcessShipmentBookingDistributionDB;
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
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\Admin\BookingDestinationMapping;

use App\Jobs\ProcessShipmentBookingDB;
use App\Jobs\ProcessShipmentBookingDBPriority;

use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Validator;
use Illuminate\Validation\Rule;

use SnappyPDF;
use DNS2D;

class ShipperShipmentBookController extends Controller
{

    private function unique_order_id($order_id)
    {
        if (is_numeric($order_id)) {
            $length = strlen(session('prefix'));
            $check_order_id = str_split($order_id, $length);
            if (session('prefix') == $check_order_id[0]) {
                if (array_key_exists(1, $check_order_id)) {
                    return !(Shipment::where('user_id', session('user_id'))->where('order_id', $order_id)->exists());
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function restrict_unique_order_id($order_id)
    {
        return !(Shipment::where('user_id', session('user_id'))->where('order_id', $order_id)->exists());
    }

    private function set_service_type($service_type_id)
    {
        $service_type = BookingType::find($service_type_id);

        session(['service_type_id' => $service_type->id]);
        session(['service_type_name' => $service_type->booking_type]);
    }

    static public function add_pickup_address($user_id, $address, $person_of_contact, $vendor, $phone_number, $email_address, $city_id, $default, $hidden = FALSE)
    {
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


    static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces, $self_collection, $business_category_id, $open_shipment, $return_address_id)
    {


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
//        $shipment->pickup_date = $pickup_date;
        $shipment->special_instructions = $special_instructions;
        $shipment->estimated_weight = $estimated_weight;
        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->amount = $amount;

        if ($payment_mode_id == 2) {
            $settings = GlobalSettings::where('type', 'ccd_booking')->first();
            $ccd_accounts = array();
            $ccd_accounts = array_map('intval', explode(',', $settings->text));

            if (in_array($user_id, $ccd_accounts)) {
                $payment_mode_id = 2;
            } else {
                $payment_mode_id = 1;
            }
        }

        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->charges_mode_id = $charges_mode_id;
        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;

        $shipment->try_and_buy_fees = $try_and_buy_charges;
        $shipment->booked_by = session('user_type');
        $shipment->pieces = $pieces;
        $shipment->business_category_id = $business_category_id;
        if ($return_address_id) {
            $shipment->return_address_id = $return_address_id;
        }
        $shipment->save();

        $shipment_id = $shipment->id;
        $shipment_detail = new ShipmentDetail();
        $shipment_detail->shipment_id = $shipment_id;
        $shipment_detail->is_open = $open_shipment;
        $shipment_detail->save();

        if ($self_collection == TRUE) {
            $shipment_self_collection = new SelfCollectionShipment();
            $shipment_self_collection->shipment_id = $shipment_id;
            $shipment_self_collection->save();
        }

        AdminPickupsController::generate($shipment_id);

        if (session('user_type') != 1) {
            $reference_1_id = Auth::id();
        } else {
            $reference_1_id = null;
        }
        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL, $reference_1_id);

        ConsigneeInformationController::add($consignee_phone_number_1, $consignee_name, $consignee_address, $consignee_phone_number_2, $consignee_city_id, $user_id);

        //Existing Coordinates
        $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
            $sub_query->where('phone_number', $consignee_phone_number_1)
                ->orwhere('phone_number', $consignee_phone_number_2);
        })->where('address', $consignee_address);
        if ($coordinates->exists()) {
            $coordinates = $coordinates->latest()->first();

            $shipment_coordinates = new ConsigneeShipmentLocation();
            $shipment_coordinates->shipment_id = $shipment_id;
            $shipment_coordinates->previous_location_id = $coordinates->id;
            $shipment_coordinates->current_location_id = NULL;
            $shipment_coordinates->save();
        }
        //Existing Coordinates
//        if($pieces > 1){
//            $user = User::where('id', $user_id)->where('multipiece_status', 0);
//            if($user->exists()){
//                $user = $user->first();
//                $user->multipiece_status = 1;
//                $user->save();
//            }
//        }

        return $shipment_id;
    }

    static public function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id)
    {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);
        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        ShipmentStatusWebhookController::webhook_subscription($shipment_id, 1);

        return $tracking_number;
    }

    static public function generate_prefix_tracking_number($shipment_id, $order_id)
    {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $order_id;

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        ShipmentStatusWebhookController::webhook_subscription($shipment_id, 1);

        return $tracking_number;
    }

    static public function create_shipment_pieces($shipment_id, $pieces)
    {
        $total_pieces = 0;
        if ($pieces > 1) {

            for ($i = 1; $i <= $pieces; $i++) {
                $shipment_piece = new ShipmentPiece();
                $shipment_piece->shipment_id = $shipment_id;
                $total_pieces++;
                $shipment_piece->numbering = $total_pieces;
                $shipment_piece->tracking_number = $shipment_id . $total_pieces;
                $shipment_piece->save();
            }

        }
    }

    static public function add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type)
    {
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

    static public function add_distribution_item($shipment_id, $product_type_id, $item_quantity, $units_per_item, $price, $insurance)
    {
        $shipment_item = new ShipmentDistributionProduct();

        $shipment_item->shipment_id = $shipment_id;
        $shipment_item->product_type_id = $product_type_id;
        $shipment_item->items = $item_quantity;
        $shipment_item->units_per_item = $units_per_item;
        $shipment_item->price = $price;
        $shipment_item->insurance = $insurance;

        $shipment_item->save();
    }

    static public function phone_number($phone_number)
    {
        //Removing anything after Comma (,)
        $phone_number = preg_replace('/^([^,]*).*$/', '$1', $phone_number);

        //Removing anything after Slash (/)
        $phone_number = preg_replace('/^([^\/]*).*$/', '$1', $phone_number);

        //Removing all Dashes (-)
        $phone_number = str_replace('-', '', $phone_number);

        //Removing all Spaces ( )
        $phone_number = str_replace(' ', '', $phone_number);

        //Replace +92 with 0
        if (substr($phone_number, 0, 3) == '+92') {
            $phone_number = '0' . substr($phone_number, 3);
        } //Replace 92 with 0
        else if (substr($phone_number, 0, 2) == '92') {
            $phone_number = '0' . substr($phone_number, 2);
        } //Replace 0092 with 0
        else if (substr($phone_number, 0, 4) == '0092') {
            $phone_number = '0' . substr($phone_number, 4);
        } //Addition of 0
        else if (substr($phone_number, 0, 1) != '0') {
            $phone_number = '0' . $phone_number;
        }

        return $phone_number;
    }

    public function __construct()
    {
        $this->middleware('auth:web,substitute_users')->except(['print_air_waybill', 'corporate_invoice']);

        $this->middleware('auth:admin,web,substitute_users')->only(['print_air_waybill', 'corporate_invoice']);

        $this->middleware('Permission');
    }

    public function index()
    {
        // $time = Carbon::today()->addHour(15);
        // $current_time = Carbon::now();
        $date = Carbon::today();
        // if($current_time > $time){
        //     $date = Carbon::tomorrow();
        // }

        $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
        $user = User::with('shipping.city')->find(session('user_id'));
        $multi_piece = $user->multipiece_status;
        $cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        if (in_array(session('user_id'), [5982, 3324, 10104, 14110, 16292])) {
            $consignee_cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        } else {
            $consignee_cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        }
        $products = Product::orderBy('product_name')->get();
        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (!in_array(session('user_id'), $ccd_account_tags)) {
                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
            } else {
                $payment_modes = PaymentMode::whereNotIn('id', [3])->get();
            }
        } else {
            $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        }
        $check = NonServiceArea::pluck('name')->toArray();
        $charges_modes = ChargesModes::whereIn('id', [4])->get();
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if ($air_waybill->exists()) {
            $air_waybill = $air_waybill->first();
        } else {
            $air_waybill = null;
        }

        $omni_user = 0;
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->text != NULL) {
                $omni_accounts = array_map('intval', explode(',', $settings->text));
                if (in_array(session('user_id'), $omni_accounts)) {
                    $omni_user = 1;
                }
            }
        }
        // dd(1);
        return view('client.shipment.book.index')->with(['booking_types' => $booking_types, 'user' => $user, 'multi_piece' => $multi_piece, 'cities' => $cities, 'products' => $products, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'consignee_cities' => $consignee_cities, 'check' => $check, 'charges_modes' => $charges_modes, 'date' => $date, 'air_waybill' => $air_waybill, 'omni_user' => $omni_user]);
    }

    public function shipping_modes(Request $request)
    {
        $shipper_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1);
        $user = User::where('id', session('user_id'))->first();

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
                } else {
                    return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type, Pickup City and Consignee City'];
                }
            } else {
                return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type and Consignee City'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipping Modes has been Enabled for you'];
        }
    }

    public function check_cod_cap_zone_classes(Request $request)
    {
        if ($request->consignee_city != null && $request->pickup_city != null) {
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
            } else {
                return response()->json(['status' => 2, 'error' => '']);
            }
        } else {
            if ($request->pickup_city == null) {
                return response()->json(['status' => 3, 'error' => 'Pickup city is required']);
            } else {
                return response()->json(['status' => 4, 'error' => 'Consignee city is required']);
            }
        }
    }

    public function store(Request $request) {
        $rules = [
            'replacement_parcel_img' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {
        if (BookingType::where('id', '!=', 4)->where('id', $request->input('selected_service_type'))->exists()) {

            if ($request->filled('open_shipment')) {
                $open_shipment = 1;
            } else {
                $open_shipment = 0;

            }

            if (!empty($request->input('shipping_mode'))) {
                $user_id = session('user_id');

                if ($request->input('consignee_email_address')) {
                    $user_email_id = $request->input('consignee_email_address');
                } else {
                    $user = User::find($user_id);
                    $user_email_id = $user->email;
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
                    if ($request->input('make_default_address') == 1) {
                        $default = 1;
                    } else {
                        $default = 0;
                    }
                    UserShippingInfo::where('user_id', $user_id)->update(['default_address' => 0]);

                    $pickup_address_id = $this->add_pickup_address($user_id, $request->input('new_pickup_address'), $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_vendor'), $request->input('new_pickup_phone_number'), $request->input('new_pickup_email_address'), $pickup_city_id, $default);
                } else {
                    if ($service_type_id != 5) {
                        $pickup_address_id = $request->input('pickup_address');

                        $user_shipping_info = UserShippingInfo::find($pickup_address_id);

                        $pickup_city_id = $user_shipping_info->city_id;
                    } else {
                        $pickup_address_id = $this->add_pickup_address($user_id, $request->input('consignee_address'), $request->input('consignee_name'), NULL, $request->input('consignee_phone_number_1'), $user_email_id, $request->input('consignee_city'), 0, TRUE);

                        $pickup_city_id = $request->input('consignee_city');

                        $pickup_address_id_for_delivery = $request->input('pickup_address');
                    }
                }
                $return_address_id = FALSE;
                if ($request->input('return_address') == 0 && $request->input('return_address') != null) {
                    if ($service_type_id == 5) {
                        return redirect()->back()->with('error', 'New Return Address cannot be selected for Reverse Pickup');
                    }

                    $return_city_id = $request->input('new_return_city');
                    $default = 0;

                    $return_address_id = $this->add_pickup_address($user_id, $request->input('new_return_address'), $request->input('new_return_person_of_contact'), $request->input('new_return_vendor'), $request->input('new_return_phone_number'), $request->input('new_return_email_address'), $return_city_id, $default);
                } else {
                    if ($service_type_id == 1) {
                        if ($request->filled('return_address')) {
                            $return_address_id = $request->return_address;
                        }
                    }

                }
                /*  if ($service_type_id == 1 || $service_type_id == 2) {
                       if ($request->filled('repickup_addressturn_address')) {
                           $return_address_id = $request->return_address;
                       } else {
                           $return_address_id = FALSE;
                       }
                   }
                   else{
                       $return_address_id = FALSE;
                   }*/

                if ($service_type_id != 5) {
                    if ($request->filled('information_display')) {
                        $information_display = TRUE;
                    } else {
                        $information_display = FALSE;
                    }
                } else {
                    $information_display = TRUE;
                }

                if ($service_type_id == 1) {
                    if ($request->filled('self_collection')) {
                        $self_collection = TRUE;

                    } else {

                        $self_collection = FALSE;
                    }
                } else {

                    $self_collection = FALSE;
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
                } else {
                    $user_shipping_info = UserShippingInfo::find($pickup_address_id_for_delivery);

                    $consignee_city_id = $user_shipping_info->city_id;
                    $consignee_name = $user_shipping_info->poc;
                    $consignee_address = $user_shipping_info->pickup_address;
                    $consignee_phone_number_1 = $user_shipping_info->phone;
                    $consignee_phone_number_2 = NULL;
                    $consignee_email_address = $user_shipping_info->email;
                }

                $shipping_mode_id = $request->input('shipping_mode');

                $origin_allow = self::check_origin($pickup_address_id, $shipping_mode_id, $user_id);
                if ($origin_allow == false) {
                    return redirect()->back()->with('error', 'Origin city not allowed, please contact your sales person!');
                }

                $destination_allow = self::check_destination($consignee_city_id, $shipping_mode_id, $user_id, 2);
                if ($destination_allow == false) {
                    return redirect()->back()->with('error', 'Destination city not allowed, please contact your sales person!');
                }

                if ($request->filled('order_id')) {
                    $order_id = $request->input('order_id');
                } else {
                    $order_id = NULL;
                }

                if ($request->filled('package_type')) {
                    $package_type = TRUE;
                } else {
                    $package_type = FALSE;
                }

//                    $pickup_date = $request->input('pickup_date_formatted');

                if ($request->filled('special_instructions')) {
                    $special_instructions = $request->input('special_instructions');
                } else {
                    $special_instructions = NULL;
                }

                $estimated_weight = $request->input('estimated_weight');

                if ($service_type_id != 5) {
                    $charges_mode_id = $request->charges_mode;
                } else {
                    $charges_mode_id = 4;
                }

                if ($request->input('shipping_mode') == 4) {
                    $same_day_timing_id = $request->input('same-day_timing');
                } else {
                    $same_day_timing_id = NULL;
                }

                $amount = str_replace(',', '', $request->input('amount'));
                if ($service_type_id != 5) {
                    $payment_mode_id = $request->input('payment_mode');
                } else {
                    $payment_mode_id = 1;
                }

                if ($service_type_id == 3 && $payment_mode_id == 4) {
                    $payment_mode_id = 1;
                }

                if ($payment_mode_id == 4) {
                    $amount = 0;
                }
                if ($service_type_id == 3) {
                    $try_and_buy_charges = $request->input('try_and_buy_charges');
                    $amount = 0;
                } else {
                    $try_and_buy_charges = NULL;
                }

                $pieces_quantity = 1;


                if ($service_type_id == 1) {
                    $pieces_quantity = $request->pieces_quantity;
                }
                $business_category_id = 1;

                $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $self_collection, $business_category_id, $open_shipment, $return_address_id);

                if (session('user_type') == 2) {
                    $substitute_user_shipment = new SubstituteUserShipment();
                    $substitute_user_shipment->substitute_user_id = Auth::id();
                    $substitute_user_shipment->shipment_id = $shipment_id;
                    $substitute_user_shipment->save();
                }
                $this->add_consignee_info($user_id, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address);
                if (Session::has('prefix')) {
                    $tracking_number = $this->generate_prefix_tracking_number($shipment_id, $request->order_id);
                } else {
                    $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
                }
                if ($request->has('order_date_formatted')) {
                    if ($request->order_date_formatted != null) {
                        $order_date = new ShipmentOrderDate();
                        $order_date->shipment_id = $shipment_id;
                        $order_date->order_date = $request->order_date_formatted;
                        $order_date->save();
                    }
                }


                if ($request->shipper_reference_1 != null || $request->shipper_reference_2 != null || $request->shipper_reference_3 != null || $request->shipper_reference_4 != null || $request->shipper_reference_5 != null) {
                    $shipper_reference = new ShipmentShipperReference();
                    $shipper_reference->shipment_id = $shipment_id;
                    if ($request->shipper_reference_1 != null) {
                        $shipper_reference->reference_1 = $request->shipper_reference_1;
                    }
                    if ($request->shipper_reference_2 != null) {
                        $shipper_reference->reference_2 = $request->shipper_reference_2;
                    }
                    if ($request->shipper_reference_3 != null) {
                        $shipper_reference->reference_3 = $request->shipper_reference_3;
                    }
                    if ($request->shipper_reference_4 != null) {
                        $shipper_reference->reference_4 = $request->shipper_reference_4;
                    }
                    if ($request->shipper_reference_5 != null) {
                        $shipper_reference->reference_5 = $request->shipper_reference_5;
                    }
                    $shipper_reference->save();
                }

                if ($service_type_id == 1 || $service_type_id == 5) {
                    $product_type_id = $request->input('product_type');

                    if ($request->filled('item_description')) {
                        $item_description = $request->input('item_description');
                    } else {
                        $item_description = NULL;
                    }

                    $item_quantity = $request->input('item_quantity');

                    if ($request->filled('insurance')) {
                        $price = str_replace(',', '', $request->input('item_price'));
                        $insurance = TRUE;
                    } else {
                        $price = NULL;
                        $insurance = FALSE;
                    }

                    $type = 0;

                    $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
                    if ($service_type_id == 1 && $pieces_quantity > 1) {
                        $this->create_shipment_pieces($shipment_id, $pieces_quantity);
                    }
                } else if ($service_type_id == 2) {
                    $product_type_id = $request->input('product_type');

                    if ($request->filled('item_description')) {
                        $item_description = $request->input('item_description');
                    } else {
                        $item_description = NULL;
                    }

                    $item_quantity = $request->input('item_quantity');

                    if ($request->filled('insurance')) {
                        $price = str_replace(',', '', $request->input('item_price'));
                        $insurance = TRUE;
                    } else {
                        $price = NULL;
                        $insurance = FALSE;
                    }

                    $type = 0;

                    $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

                    $product_type_id = $request->input('replacement_product_type');

                    if ($request->filled('replacement_item_description')) {
                        $item_description = $request->input('replacement_item_description');
                    } else {
                        $item_description = NULL;
                    }

                    $item_quantity = $request->input('replacement_item_quantity');
                    $price = NULL;
                    $insurance = NULL;
                    $type = 1;

                    $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
                } else if ($service_type_id == 3) {
                    $try_and_buy_cod_amount = intval($try_and_buy_charges);
                    foreach ($request->input('try_and_buy') as $try_and_buy) {
                        $product_type_id = $try_and_buy['product_type'];

                        if (isset($try_and_buy['item_description']) && !empty($try_and_buy['item_description'])) {
                            $item_description = $try_and_buy['item_description'];
                        } else {
                            $item_description = NULL;
                        }

                        $item_quantity = $try_and_buy['item_quantity'];
                        $price = str_replace(',', '', $try_and_buy['item_price']);
                        $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($price);
                        if (isset($try_and_buy['insurance']) && !empty($try_and_buy['insurance'])) {
                            $insurance = TRUE;
                        } else {
                            $insurance = FALSE;
                        }

                        $type = 2;

                        $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
                    }
                    $shipment_try_and_buy = Shipment::find($shipment_id);
                    $shipment_try_and_buy->amount = $try_and_buy_cod_amount;
                    $shipment_try_and_buy->save();
                }

                NotificationsController::send(2, $shipment_id);

                $pickup_city = City::where('id', $pickup_city_id)->whereNotNull('pickup_cut_off_time');
                if($pickup_city->exists()){
                    $pickup_city = $pickup_city->first();
                    $cutofftime = $pickup_city->pickup_cut_off_time . ":00:00";
                }
                else{
                    $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();

                    $cutofftime = $settingsfortime->setting_value . ":00:00";
                }

                $now = Carbon::now()->format('H:i:s');
                if ($now > $cutofftime) {
                    NotificationsController::send(152, $shipment_id);
                    NotificationsController::send(153, $shipment_id);
                }

                if ($request->filled('book_and_print')) {
                    $print = $shipment_id;
                } else {
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

                    $pickup_city = City::where('id', $pickup_city_id)->whereNotNull('pickup_cut_off_time');
                    if($pickup_city->exists()){
                        $pickup_city = $pickup_city->first();
                        $cutofftime = $pickup_city->pickup_cut_off_time . ":00:00";
                    }
                    else{
                        $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();

                        $cutofftime = $settingsfortime->setting_value . ":00:00";
                    }

                    $now = Carbon::now()->format('H:i:s');

                    if ($now > $cutofftime) {
                        NotificationsController::send(152, $shipment_id);
                        NotificationsController::send(153, $shipment_id);
                    }
                }
                $user = User::find($user_id);
                if ($user->logo_status) {
                    $shipment = Shipment::find($shipment_id);
                    $shipment->shipment_invoice_status = 1;
                    $shipment->save();
                    if ($request->has('cod_breakup')) {
                        $shipping_charges = $request->cod_breakup_shipping_charges;
                        $total_cod = $request->cod_breakup_total_cod;
                        $descriptions = $request->cod_breakup_description;
                        $amounts = $request->cod_breakup_amount;
                        $this->cod_breakup_create($shipment_id, $shipping_charges, $total_cod, $descriptions, $amounts);
                    }
                }

                    if ($msg_string != null) {
                        NotificationsController::send(32, $shipment_id, $msg_string);

                        $now = Carbon::now()->format('H:i:s');
                        $pickup_city = City::where('id', $pickup_city_id)->whereNotNull('pickup_cut_off_time');
                        if($pickup_city->exists()){
                            $pickup_city = $pickup_city->first();
                            $cutofftime = $pickup_city->pickup_cut_off_time . ":00:00";
                        }
                        else{
                            $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();

                            $cutofftime = $settingsfortime->setting_value . ":00:00";
                        }

                        if($now > $cutofftime)
                        {
                            NotificationsController::send(152, $shipment_id);
                            NotificationsController::send(153, $shipment_id);
                        }
                    }
                    $user = User::find($user_id);
                    if($user->logo_status){
                        $shipment = Shipment::find($shipment_id);
                        $shipment->shipment_invoice_status = 1;
                        $shipment->save();
                        if($request->has('cod_breakup')){
                            $shipping_charges = $request->cod_breakup_shipping_charges;
                            $total_cod = $request->cod_breakup_total_cod;
                            $descriptions = $request->cod_breakup_description;
                            $amounts = $request->cod_breakup_amount;
                            $this->cod_breakup_create($shipment_id, $shipping_charges, $total_cod, $descriptions, $amounts);
                        }
                    }
                if($request->hasFile('replacement_parcel_img')){
                    $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $shipment_id);
                    if($shipment_parcel_image->exists()){
                        $shipment_parcel_image = $shipment_parcel_image->first();
                        Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                    }else{
                        $shipment_parcel_image = new ShipmentReplacementParcelImage();
                        $shipment_parcel_image->shipment_id = $shipment_id;
                    }
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'replacement_parcel/' . $shipment_id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_img));
                    $shipment_parcel_image->picture_path = $picture_path;
                    $shipment_parcel_image->save();
                }

                    return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
            }
            else {
                return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Service Type Selected');
        }
        }
    }

    public function order_id(Request $request)
    {
        if ($request->filled('order_id')) {
            return json_encode($this->unique_order_id($request->input('order_id')));
        } else {
            return 'false';
        }
    }

    public function restrict_order_id(Request $request)
    {
        if ($request->filled('order_id')) {
            return json_encode($this->restrict_unique_order_id($request->input('order_id')));
        } else {
            return 'false';
        }
    }

    public function shipment_check(Request $request)
    {
        $shipment_ids = array();
        if ($request->ids) {
            $i = 0;
            $sticker = TRUE;

            foreach ($request->ids as $id) {
                $shipment = Shipment::find($id);
                if ($shipment) {
                    if ($shipment->shipper_status_id == 1) {
                        $shipment_ids[$i] = $id;
                        $i++;

                        if (!ShipperAirWaybillSettings::where('user_id', $shipment->user_id)->where('type', 2)->exists()) {
                            $sticker = FALSE;
                        }
                    }
                }
            }
            if ($i > 0) {
                return ['status' => 1, 'ids' => $shipment_ids, 'sticker' => $sticker];
            } else {
                return ['status' => 2];
            }
        } else {
            return ['status' => 2];
        }
    }

    public static function air_waybill($user_type, $user_id, $ids, $body_only = FALSE, $type = NULL, $shipper_name = NULL, $shipper_phone = NULL, $print_status = NULL)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $watermark_flag = false;
        if ($user_type == 3) {
            $user_name = Admin::find($user_id)->name . ' (Admin)';
            $admin_name = Admin::find($user_id)->name;
            $watermark = 'DUPLICATE PRINTED BY: ' . $admin_name;
        } else if ($user_type == 1) {
            $user_name = User::find($user_id)->name . ' (Shipper)';
        } else if ($user_type == 2) {
            $sub_shipper = SubstituteUser::where('user_id', $user_id)->where('id', Auth::id())->select('name')->first();
            $user_name = $sub_shipper->name . ' (Sub-Shipper)';
        } else if ($user_type == 4) {
            
            $user_name = User::find($user_id)->name . ' (API)';
        } else {
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
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">
            ';

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                ';
            } else {
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
                        font-size: 0.7rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                        margin: 1rem 0;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                        margin-bottom: 0px;
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
                       div.page
                        {
                            page-break-after: always;
                            page-break-inside: avoid;
                        }
                        .piece_number{
                            font-size: 2.5rem;
                        }
                        .page-breaker{
                            page-break-after: always;
                        }
                    </style>
                  </head>
                  <body>
                    <div>
            ';

            if ($user_id == 12412) {
                $html .= '
                    <style>
                        .end_of_air_waybill {
                            page-break-after: always;
                        }
                    </style>
                ';
            }

            if ($user_type != 4 && $type != 'pdf') {
                $html .= '
                    <style>
                      @font-face {
                        font-family: "Fajer Noori Nastalique";
                        src: url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.eot') . '");
                        src: url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.eot?#iefix') . '") format("embedded-opentype"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.woff') . '") format("woff"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.otf') . '") format("opentype"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.ttf') . '") format("truetype"),
                        url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.svg#FajerNooriNastalique') . '") format("svg");
                        font-weight: normal;
                        font-style: normal;
                        unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFF;
                      }

                      .urdu {
                        font-family: "Fajer Noori Nastalique";
                      }
                    </style>
                ';
            } else {
                $html .= '
                    <style>
                      @font-face {
                        font-family: "Fajer Noori Nastalique";
                        src: url("data:font/truetype;charset=utf-8;base64,' . base64_encode(file_get_contents(public_path('fonts/urdu/Fajer-Noori-Nastalique.ttf'))) . '") format("truetype");
                        font-weight: normal;
                        font-style: normal;
                        unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFF;
                      }

                      .urdu {
                        font-family: "Fajer Noori Nastalique";
                        padding-bottom: .75rem !important;
                      }
                    </style>
                ';
            }

            if ($type == 'pdf') {
                $html .= '
                    <style>
                      body {
                        font-size: 0.7rem !important;
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

        $overall_shipment_details = '';
        $shipment_details = '';
        $page_items = 1;

        $settings = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if ($settings->exists()) {
            $settings = $settings->first();
            $prints = $settings->print_count;
            $page_break = $settings->page_breaker;
        } else {
            $prints = 1;
            $page_break = 0;
        }
        $check = DeliveryLocationMappingKeyword::pluck('keyword')->toArray();

        foreach ($ids as $id) {
            $shipment = Shipment::find($id);

            if ($user_type != 2) {
                ShipmentsAirWaybillJourneyController::add($id, $user_type, $user_id);
            } else {
                ShipmentsAirWaybillJourneyController::add($id, $user_type, Auth::id());
            }

            if ($user_type == 3 || $user_id == $shipment->user_id) {

                if ($shipment->booking_type_id == 3 && $user_type != 3) {
                    foreach ($shipment->items as $shipment_item) {
                        if ($page_items == 0) {
                            $table_start = '
                    <div class="page position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>
            ';
                        } else {
                            $table_start = '
                    <div class="position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>
            ';
                        }

                        if ($user_type != 4 && $type != 'pdf') {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                        } else {
                            if ($type != 'pdf') {
                                $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                        ';
                            } else {
                                $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">' . $print_details . '</td>
                        ';
                            }
                        }
                        if ($type != 'pdf') {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment_item->id . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment_item->id, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                                <tr>
                                    <td class="color secondary border twice-top twice-left"><strong>Type</strong></td>
                                    <td colspan="2" class="border twice-top">' . $shipment_item->product->product_name . '</td>
                                    <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                    <td colspan="1" class="border twice-top">' . $shipment_item->quantity . '</td>
                                    <td colspan="2" class="color secondary border twice-top"><b>Tracking Number</b></td>
                                </tr>
                    ';
                        } else {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment_item->id . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment_item->id, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                                <tr>
                                    <td class="color primary border twice-left"><strong>Type</strong></td>
                                    <td colspan="2" class="border twice-top">' . $shipment_item->product->product_name . '</td>
                                    <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                    <td colspan="1" class="border twice-top">' . $shipment_item->quantity . '</td>
                                    <td colspan="2" class="border twice-top">Tracking Number</td>
                                </tr>
                    ';
                        }

                        $table_start .= '
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="2" class="border twice-bottom">' . $shipment_item->description . '</td>
                                <td class="color secondary border twice-bottom"><strong>Price</strong></td>
                                <td class="border twice-bottom">Rs ' . number_format($shipment_item->price) . '</td>';
                        if ($type != 'pdf') {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                              </tr>
                            </tbody>
                        </table></div>
                    ';
                        } else {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                              </tr>
                            </tbody>
                        </table></div>
                    ';
                        }
                        $shipment_details .= $table_start;
                        $page_items++;
                        if ($page_items >= 5) {
                            $page_items = 0;
                        }
                    }
                } else {
                    $page_items = $page_items + 3;
                    if ($page_items >= 5) {
                        $page_items = 0;
                    }
                    $return_address_id = NULL;
                    if ($shipment->return_address_id != NULL) {
                        $return_address_id = $shipment->return_address_id;
                        $return_address_city = $shipment->return_address->city->name;
                        $return_address = $shipment->return_address->pickup_address;
                        $return_address_phone = $shipment->return_address->phone;
                    }
                    $table_start = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';

                    if ($user_type != 4 && $type != 'pdf') {
                        $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';

                        $distribution_logo = '
                                <td rowspan="3" colspan="3"  class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                    } else {
                        if ($type != 'pdf') {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                        ';

                            $distribution_logo = '
                               <td rowspan="3" colspan="3"  class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                        } else {
                            $table_start .= '
                                <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">' . $print_details . '</td>
                        ';

                            $distribution_logo = '
                                <td rowspan="3" colspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . public_path('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                        }
                    }
                    if ($type != 'pdf') {
                        $table_start .= '
                                <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="7" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 12, 12) . '" class="d-block mx-auto">
                                </td>
                        ';

                        if ($shipment->business_category->id == 2) {
                            $table_start .= '<td class="color primary border twice-left"><strong>Service Type</strong></td>
                                    ';
                        } else {
                            $table_start .= '<td class="color primary border twice-left"><strong>Service</strong></td>
                                    ';
                        }
                    } else {
                        $table_start .= '
                                <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="7" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 12, 12) . '" class="d-block mx-auto">
                                </td>
                        ';

                        if ($shipment->business_category->id == 2) {
                            $table_start .= '<td class="color primary border twice-left"><strong>Service Type</strong></td>
                                    ';
                        } else {
                            $table_start .= '<td class="color primary border twice-left"><strong>Service</strong></td>
                                    ';
                        }


                    }

                    if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 6) {
                        if ($shipment->user_id == 10354 && $shipment->distribution_products->count() > 0) {
                            $service_type = "Distribution";
                        } else {
                            $service_type = $shipment->booking_type->booking_type;
                        }
                        $table_start .= '
                                <td><strong>' . $service_type . '</strong></td>
                    ';
                    } else if ($shipment->booking_type_id == 2) {
                        if ($type != 'pdf') {
                            $table_start .= '
                                <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                        ';
                        } else {
                            $table_start .= '
                                <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong></td>
                        ';
                        }
                    }
//                    else if ($shipment->booking_type_id == 3) {
//                        $table_start .= '
//                                <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
//                    ';
//                    }
                    else {
                        $table_start .= '
                                <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
                    }

                    if ($type != 'pdf') {
                        $table_start .= '
                                <td class="color primary"><strong>Datetime</strong></td>
                                <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                              </tr>';

                        if ($shipment->business_category->id == 1) {
                            $table_start .= '<tr>
                                    <td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                                    <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>
                                ';
                        } else {
                            $table_start .= '<tr>
                                    <td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                                    <td><strong>International</strong></td>
                                ';
                        }

                        $origin = $return_address_id == NULL ? 'Origin' : 'Return';
                        $originstyle = $return_address_id == NULL ? '<td class="color primary border twice-bottom twice-left"><strong> ' . $origin . '</strong></td>' : '<td style="background-color:  #6e6e6e !important; color: white;" class="color border twice-bottom twice-left" ><strong> ' . $origin . '</strong></td>';

                        $origin_data = $return_address_id == NULL ? $shipment->pickup_address->city->name : $return_address_city;
                        $table_start .= '
                                <td class="color primary"><strong>Order ID</strong></td>
                                <td>' . $shipment->order_id . '</td>
                              </tr>
                              <tr>
                                ' . $originstyle . '
                                <td class="border twice-bottom"><strong>' . $origin_data . '</strong></td>
                                <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                              </tr>';

                        $table_start .= '
                                              <tr>
                                                <td colspan="3" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                                                <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                                              </tr>
                                              <tr>
                                                <td class="color secondary"><strong>Name</strong></td>
                                    ';


                    } else {
                        $table_start .= '
                                <td class="color primary"><strong>Order ID</strong></td>
                                <td>' . $shipment->order_id . '</td>
                              </tr>
                              <tr>';
                        if ($shipment->business_category->id == 1) {
                            $table_start .= '<td class="color primary border twice-left"><strong>Shipping</strong></td>
                                <td><strong>' . $shipment->shipping_mode->mode . '</strong></td>';
                        } else {
                            $table_start .= '<td class="color primary border twice-left"><strong>Shipping</strong></td>
                                    <td><strong>International</strong></td>';
                        }


                        $table_start .= '
                                <td class="color primary"><strong>Date</strong></td>
                                <td>' . $shipment->created_at->format('Y-m-d') . '</td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                                <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                                <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                              </tr>';

                        $table_start .= '
                                              <tr>
                                                <td colspan="3" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                                                <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                                              </tr>
                                              <tr>
                                                <td class="color secondary"><strong>Name</strong></td>
                                    ';


                    }
                    if ($shipper_name == NULL) {
                        if ($shipment->pickup_address->pickup_brand_name != NULL) {
                            // $company_name = $shipment->user->brand_name;
                            $company_name = $shipment->pickup_address->pickup_brand_name;


                        } else {
                            if ($shipment->user->brand_name != NULL) {
                                $company_name = $shipment->user->brand_name;
                            } else {
                                $company_name = $shipment->user->name;
                            }
                        }
                    } else {
                        $company_name = $shipper_name;
                    }


                    if ($shipment->booking_type_id != 4) {
                        $table_start .= '
                                <td colspan="2" class="border twice-right">' . $company_name . '</td>
                    ';
                    } else {
                        $table_start .= '
                                <td colspan="2" class="border twice-right">' . $company_name . ' (' . $shipment->pickup_address->poc . ')</td>
                    ';
                    }
                    if($shipment->packaging_material_request == 1){
                        $packaging_material_shipment = PackagingMaterialRequest::where('shipment_id', $shipment->id);
                        if($packaging_material_shipment->exists()){
                            $packaging_material_shipment = $packaging_material_shipment->first();
                            $consignee_name = $packaging_material_shipment->poc;
                        }
                        else{
                            $consignee_name = $shipment->consignee_name;
                        }
                    }
                    else{
                        $consignee_name = $shipment->consignee_name;
                    }
                    $table_start .= '
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $consignee_name . '</td>
                              </tr>

                              <tr>
                ';


                    $address = $return_address_id == NULL ? $shipment->pickup_address->pickup_address : $return_address;
                    $addressstyle = $return_address_id == NULL ? '<td class="color secondary"><strong>Address</strong></td>' : '<td style="background-color: #6e6e6e !important; color: white;" class="color secondary"><strong>Address</strong></td>';
                    if ($shipment->information_display == 1) {

                        if ($shipment->booking_type_id != 4) {
                            $table_start .= '
                                ' . $addressstyle . '
                                <td colspan="2" class="border twice-right">' . $address . '</td>
                        ';
                        } else {
                            $table_start .= '
                                ' . $addressstyle . '
                                <td colspan="2" class="border twice-right">' . $address . '</td>
                        ';
                        }


                    } else {
                        $table_start .= '
                                <td colspan="3" class="border twice-bottom twice-right"></td>
                                ';


                    }

                    $table_start .= '
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                        ';


                    if ($shipper_phone == NULL) {
                        $phonenumber = $return_address_id == NULL ? $shipment->pickup_address->phone : $return_address_phone;
                    } else {
                        $phonenumber = $shipper_phone;
                    }
                    $phonenumberstyle = $return_address_id == NULL ? '<td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>' : '<td class="color secondary border twice-bottom" style="background-color: #6e6e6e !important; color: white;"><strong>Phone Number(s)</strong></td>';
                    if ($type != 'pdf') {
                        if ($shipment->booking_type_id != 4) {
                            $table_start .= '
                                    ' . $phonenumberstyle . '
                                    <td colspan="2" class="border twice-bottom twice-right">' . $phonenumber . '</td>
                                ';

                        } else {
                            $table_start .= '
                            ' . $phonenumberstyle . '
                            <td colspan="2" class="border twice-bottom twice-right">' . $phonenumber . '</td>
                        ';
                        }
                    } else {
                        if ($shipment->booking_type_id != 4) {
                            $table_start .= '
                                    <td class="color secondary border twice-bottom"><strong>Phone No(s).</strong></td>
                                    <td colspan="2" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                                ';


                        } else {
                            $table_start .= '
                            <td class="color secondary border twice-bottom"><strong>Phone No(s).</strong></td>
                            <td colspan="2" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                        ';
                        }
                    }

                    if ($type != 'pdf') {
                        $table_start .= '
                                <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              </tr>
                    ';
                    } else {
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
                                <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>';
                        if ($shipment->shipping_mode_id == 2 && $shipment->estimated_weight != null) {
                            $table_end .= ' <td class="color primary border twice-top twice-bottom twice-left"><strong>Weight</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . '</strong></td>
                              </tr>
                              <tr>
                    ';
                        } else {
                            $table_end .= ' <td colspan="2" class="border twice-top twice-bottom twice-left" style="height: 20px;"></td>
                              </tr>
                              <tr>';
                        }

                        if ($shipment->booking_type_id == 5) {
                            $table_end .= '
                                <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                              </tr>
                              <tr>
                        ';
                        } elseif ($shipment->booking_type_id == 3) {
                            $table_end .= '
                                <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                              </tr>
                              <tr>
                        ';
                        } elseif ($shipment->booking_type_id != 4) {
                            if ($shipment->payment_mode_id == 2) {
                                $credit_icon = '<i class="la la-credit-card"></i>';
                            } else {
                                $credit_icon = '';
                            }
                            $table_end .= '
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->payment_mode->mode . ' ' . $credit_icon . '</strong></td>
                        ';
                        } else {
                            $table_end .= '
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                        ';
                        }


                    } else {
                        $table_end = '
                              <tr>
                                <td rowspan="2" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="2" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td colspan="2" class="border twice-top twice-bottom twice-left" style="height: 32px;"></td>
                    ';
                    }

                    if ($shipment->booking_type_id != 5 && $shipment->booking_type_id != 3) {
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
                    if ($shipment->shipment_detail()->exists()) {
                        if ($shipment->shipment_detail->is_open == 1) {
                            $table_end .= '<tr>
                                <td colspan="2" class="color primary border twice-top twice-bottom twice-left"><strong>Open Box</strong></td>
                                <td colspan="4" class="border twice-top twice-bottom twice-left"><strong> Yes <span><img src="' . asset('img/open_box_icon.png') . '" ></span></strong></td>
                                </tr>';
                        }
                    }
                    if ($user_type != 4 && $type != 'pdf') {
                        $table_end .= '
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>برائے مہربانی رائڈر / کورئیر کو کوئی اضافی پیسہ نہ دیں۔ اگر پارسل / پیکٹ خراب یا خراب حالت میں ہے تو ، براہ کرم اسے وصول نہ کریں۔</em></td>
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>ٹریکس لاجسٹک کا اس پارسل / پیکٹ میں موجود کسی آئٹم یا مواد سے کوئی تعلق نہیں ہے۔ ہم سامان ایک جگہ سے دوسری جگہ بھیجتے ہیں۔ اگر آپ کو اس بارے میں کوئی شکایت ہے تو ، براہ کرم متعلقہ آن لائن اسٹور سے رابطہ کریں۔</em></td>
                              </tr>
                            </tbody>
                        </table>
                    ';
                    } else {
                        $table_end .= '
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top font-small"><em>Kindly do not give any addtional charges to the rider/courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top font-small"><em>Trax Logistics has nothing to do with any item or content contained in this parcel/packet. We ship goods from one place to another. If you have a complaint about this, please contact the relevant online store.</em></td>
                              </tr>
                            </tbody>
                        </table>
                    ';
                    }

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
                      <div class="col row align-items-center justify-content-center end_of_air_waybill"><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                    ';
                        if($shipment->pieces  <=1 && $page_break == 1) {
                            $table_end .= '<div class="page-breaker"></div>';
                        }
                    }

                    if ($shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                        $shipment_details .= $table_start;

                        $item = $shipment->items->first();

                        $shipment_details .= '
                              <tr>
                                <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                                <td class="color secondary border twice-top"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td>' . $item->quantity . '</td>
                                <td colspan="1" class="color secondary border twice-top"><strong>Piece(s)</strong></td>
                                <td>' . $shipment->pieces . '</td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                              </tr>
                    ';

                        $shipment_details .= $table_end;
                    } else if ($shipment->booking_type_id == 1) {
                        if ($shipment->user_id == 10354 && $shipment->distribution_products->count() > 0) {
                            $shipment_details .= $table_start;

                            $shipment_details .= $table_end;
                        } else {
                            $shipment_details .= $table_start;

                            $item = $shipment->items->first();

                            $shipment_details .= '
                              <tr>
                                <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                                <td class="color secondary border twice-top"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td>' . $item->quantity . '</td>
                                <td colspan="1" class="color secondary border twice-top"><strong>Piece(s)</strong></td>
                                <td>' . $shipment->pieces . '</td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                              </tr>
                    ';

                            $shipment_details .= $table_end;
                        }
                    } else if ($shipment->booking_type_id == 2) {
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
                          <td rowspan="2"  style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="align-middle  border twice-top twice-bottom"><strong>Replacement Item</strong></td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-top"><strong>Type</strong></td>
                          <td colspan="2" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-top">' . $item->product->product_name . '</td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-top"><strong>Quantity</strong></td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important">' . $item->quantity . '</td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" colspan="2" class="border twice-top"></td>
                        </tr>
                        <tr>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-bottom"><strong>Description</strong></td>
                          <td colspan="6" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-bottom">' . $item->description . '</td>
                        </tr>
                    ';

                        $shipment_details .= $table_end;
                    } else if ($shipment->booking_type_id == 3) {
                        $shipment_details .= $table_start;

                        $item_quantity = 0;
                        foreach ($shipment->items as $item) {
                            $item_quantity += $item->quantity;
                        }
                        $shipment_details .= '
                              <tr>
                                <td rowspan="1" class="align-middle color primary border twice-top twice-bottom"><strong>Try & Buy Products</strong></td>
                                <td class="color secondary border twice-top"><strong>Products</strong></td>
                                <td colspan="2" class="border twice-top">' . count($shipment->items) . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td>' . $item_quantity . '</td>
                                <td colspan="4" class=""></td>
                              </tr>
                        ';
                        $shipment_details .= ' <tr>
                                <td colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Try & Buy Fees</strong></td>
                                <td colspan="6" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->try_and_buy_fees . '</td>
                              </tr>';
                        $shipment_details .= $table_end;

                    } else if ($shipment->booking_type_id == 6) {
                        $shipment_details .= $table_start;
                        $shipment_details .= $table_end;
                    }

                    if ($shipment->booking_type_id == 1 && $shipment->pieces > 1) {
                        $shipment_pieces = '';
                        foreach ($shipment->shipment_pieces as $piece) {
                            $shipment_pieces .= '<table class="table table-sm table-bordered border twice">
                        <tbody><tr>';
                            $shipment_pieces .= '<td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>';
                            $shipment_pieces .= '<td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($piece->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $piece->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($piece->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                                <td rowspan="1" class="color primary border twice-left"><strong>Origin</strong></td>
                                <td rowspan="1" class="border">' . $shipment->pickup_address->city->name . '</td>
                                <td rowspan="1" class="color primary border "><strong>Destination</strong></td>
                                <td rowspan="1" class="border">' . $shipment->consignee_city->name . '</td>
                                
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                <span><strong>' . $shipment->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right"><span class="piece_number"><strong>' . $piece->numbering . '/' . $shipment->pieces . '</strong></span>
                            </td>
                                </tr>
                                <tr>
                                <td class="color primary border twice-left"><strong>Shipper</strong></td>
                                <td class="border">' . $shipment->user->name . '</td>
                                <td class="color primary border "><strong>Booking Date</strong></td>
                                <td class="border">' . $shipment->created_at . '</td>
</tr>
                              ';
                            $shipment_pieces .= '</tbody></table>
                      <div class="col row align-items-center justify-content-center end_of_air_waybill"><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>';

                        }

                        if($shipment->pieces  > 0 && $page_break == 1) {
                            $shipment_pieces .= '<div class="page-breaker"></div>';
                        }
                        $shipment_details .= $shipment_pieces;
                    }

                    if ($shipment->user->logo_status) {
                        if ($shipment->shipment_invoice_status) {
                            $logo = $shipment->user->logo;
                            $invoice_id = '(' . ($shipment->order_id != null) ? $shipment->order_id : '' . ')';
                            if ($type != 'pdf') {
                                $shipper_logo = '<img src="' . asset('storage/shippers_logo/' . $logo) . '" width="100" class="d-block mb-1">';
                            } else {
                                $shipper_logo = '<img src="' . public_path('storage/shippers_logo/' . $logo) . '" width="100" class="d-block mb-1">';
                            }
                            $logo_invoice = '<div class="invoice p-1" style="page-break-before: always;">
                        <div class="row"><div class="col-3"><h2>Invoice ' . $invoice_id . '</h2></div></div>
                        <div class="row"><div class="col-6 text-center">
                        ' . $shipper_logo . '
    </div><div class="col-6 text-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mb-1" style="margin: 0 auto;"></div></div>
                        
                        <div class="row align-items-start justify-content-between p-2">
                            <div class="col-12">
                                <div class=""><h5 class="d-inline">Booking Date: </h5> <span>' . $shipment->created_at . '</span></div>
                                <div class="mb-2"><h5 class="d-inline">Shipper Name: </h5> <span>' . $shipment->user->name . '</span></div>
                                
                                <div class=""><h5 class="d-inline">Consignee Name: </h5> <span>' . $shipment->consignee_name . '</span></div>
                                <div class=""><h5 class="d-inline">Consignee Address: </h5> <span>' . $shipment->consignee_address . '</span></div>
                                <div class=""><h5 class="d-inline">Consignee City: </h5> <span>' . $shipment->consignee_city->name . '</span></div>
                                <div class=""><h5 class="d-inline">Consignee Phone Number: </h5> <span>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</span></div>
                            </div>
                        </div>';
                            $invoice_items = '';
                            $shipment_invoice = ShipmentInvoice::where('shipment_id', $shipment->id)->first();
                            if ($shipment_invoice) {
                                $invoice_items .= '<div class="row align-items-start justify-content-between summary">
                            <div class="col-12">
                                <table class="table table-sm invoice">
                                      <thead><tr><td  colspan="1">S.NO.</td>
                                      <td class="text-center" colspan="6">ITEM DESCRIPTION</td>
                                      <td class="text-center" colspan="2">AMOUNT</td></tr></thead><tbody>';
                                $serial = 1;
                                foreach ($shipment_invoice->items as $item) {
                                    $invoice_items .= '<tr>
                                        <td colspan="1">' . $serial . '</td>
                                        <td colspan="6" class="">' . $item->description . '</td>
                                        <td colspan="2" class="text-center color secondary">' . $item->amount . '</td>
                                        </tr>
                                        ';
                                    $serial++;
                                }
                                $invoice_items .= '<tr colspan="1"><td></td><td colspan="6" class="text-right">Shipping Charges</td><td class="text-center" colspan="2">' . $shipment_invoice->shipping_charges . '</td></tr>
                                    <td colspan="1"></td><td colspan="6" class="text-right">Total COD Amount</td><td class="text-center" colspan="2">' . $shipment_invoice->total_cod . '</td></tbody>
                                </table>
                            </div>
                        </div>';
                            }
                            $logo_invoice .= $invoice_items;
                            $logo_invoice .= '</div>';
                            $shipment_details .= $logo_invoice;
                        }

                    }

                    if ($shipment->user_id == 10354 && $shipment->booking_type_id == 1 && $shipment->distribution_products->count() > 0) {
                        $distribution_performa_start = '<div class="position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>';

                        $distribution_performa_start .= '<tr>
                        ' . $distribution_logo . '
                        <td class="color primary font-weight-bold">Tracking Number</td>
                        <td>' . $shipment->tracking_number . '</td>
                        </tr>';

                        $distribution_performa_start .= '<tr>
                        <td class="color primary font-weight-bold">Service Type</td>
                        <td>Distribution</td>
                        </tr>';

                        $distribution_performa_start .= '<tr>
                        <td class="color primary font-weight-bold">Booking Date/Time</td>
                        <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                        </tr>';

                        $distribution_performa_start .= '
                        <tr>
                            <td colspan="3" class="color primary font-weight-bold text-center">Shipper</td>
                            <td colspan="2" class="color primary font-weight-bold text-center">Consignee</td>
                        </tr>
                        ';

                        $distribution_performa_start .= '
                            <tr>
                                <td class="color primary font-weight-bold">Pickup Address</td>
                                <td colspan="2">' . $shipment->pickup_address->pickup_address . '</td>
                                <td class="color primary font-weight-bold">Delivery Address</td>
                                <td>' . $shipment->consignee_address . '</td>
                            </tr>
                            <tr>
                                <td class="color primary font-weight-bold">City</td>
                                <td colspan="2">' . $shipment->pickup_address->city->name . '</td>
                                <td class="color primary font-weight-bold">City</td>
                                <td>' . $shipment->consignee_city->name . '</td>
                            </tr>
                            <tr>
                                <td class="color primary font-weight-bold">Phone Number</td>
                                <td colspan="2">' . $shipment->pickup_address->phone . '</td>
                                <td class="color primary font-weight-bold">Phone Number</td>
                                <td>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            </tr>
                            <tr>
                                <td class="color primary font-weight-bold">NTN Number</td>
                                <td colspan="2">' . $shipment->user->ntn_no . '</td>
                                <td colspan="2"></td>
                            </tr>
                            </tbody>
                            </table>
                        ';

                        $distribution_booking_performa = '<table class="table mt-2 table-sm table-bordered border twice">
                        <tbody>
                        <tr>
                            <td colspan="2" class="color primary font-weight-bold">Products</td>
                            <td class="color primary font-weight-bold">Items/SKU\'s</td>
                            <td class="color primary font-weight-bold">Units Per Item</td>
                            <td class="color primary font-weight-bold">Total Units</td>
                            <td class="color primary font-weight-bold">Total Amount</td>
                        </tr>';

                        $total_items = 0;
                        $total_units_per_item = 0;
                        $total_units = 0;
                        $total_price = 0;
                        foreach ($shipment->distribution_products as $product) {
                            $total_items += $product->items;
                            $total_units_per_item += $product->units_per_item;
                            $total_units += $product->items * $product->units_per_item;
                            $total_price += $product->price;

                            $distribution_booking_performa .= '
                                <tr>
                                    <td colspan="2">' . $product->item->name . '</td>
                                    <td>' . $product->items . '</td>
                                    <td>' . $product->units_per_item . '</td>
                                    <td>' . $product->items * $product->units_per_item . '</td>
                                    <td>' . $product->price . '</td>
                                </tr>
                            ';
                        }

                        $distribution_booking_performa .= '
                                <tr>
                                    <td colspan="2" class="color primary font-weight-bold">Total</td>
                                    <td>' . $total_items . '</td>
                                    <td>' . $total_units_per_item . '</td>
                                    <td>' . $total_units . '</td>
                                    <td>' . $total_price . '</td>
                                </tr>
                                </tbody>
                                </table>
                                </div>
                                <div class="col row align-items-center justify-content-center end_of_air_waybill"><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>';


                        $shipment_details .= $distribution_performa_start;
                        $shipment_details .= $distribution_booking_performa;

                        if ($shipment->shipment_journey->first()->shipper_status_id == 14) {
                            $shipment_details .= $distribution_performa_start;

                            $distribution_delivery_performa = '<table class="table mt-2 table-sm table-bordered border twice">
                        <tbody>
                        <tr>
                            <td colspan="2" class="color primary font-weight-bold">Products</td>
                            <td class="color primary font-weight-bold">Booked Items/SKU\'s</td>
                            <td class="color primary font-weight-bold">Delivered Items/SKU\'s</td>
                            <td class="color primary font-weight-bold">Return Items/SKU\'s</td>
                            <td class="color primary font-weight-bold">Booked Units Per Item</td>
                            <td class="color primary font-weight-bold">Booked Total Units</td>
                            <td class="color primary font-weight-bold">Delivered Total Units</td>
                            <td class="color primary font-weight-bold">Returned Total Units</td>
                            <td class="color primary font-weight-bold">Total Amount</td>
                        </tr>';


                            $total_items = 0;
                            $total_units_per_item = 0;
                            $total_units = 0;
                            $total_price = 0;
                            $total_delivered_items = 0;
                            $total_returned_items = 0;
                            $total_delivered_units = 0;
                            $total_returned_units = 0;
                            foreach ($shipment->distribution_products as $product) {
                                $total_items += $product->items;
                                $total_delivered_items = $product->total_delivered_skus;
                                $total_returned_items = $product->items - $product->total_delivered_skus;
                                $total_units_per_item += $product->units_per_item;
                                $total_units += $product->items * $product->units_per_item;
                                $total_delivered_units += $product->total_delivered_units;
                                $total_returned_units += ($product->items * $product->units_per_item) - $product->total_delivered_units;
                                $total_price += $product->received_amount;

                                $distribution_delivery_performa .= '
                                <tr>
                                    <td colspan="2">' . $product->item->name . '</td>
                                    <td>' . $product->items . '</td>
                                    <td>' . $product->total_delivered_skus . '</td>
                                    <td>' . ($product->items - $product->total_delivered_skus) . '</td>
                                    <td>' . $product->units_per_item . '</td>
                                    <td>' . ($product->items * $product->units_per_item) . '</td>
                                    <td>' . $product->total_delivered_units . '</td>
                                    <td>' . (($product->items * $product->units_per_item) - $product->total_delivered_units) . '</td>
                                    <td>' . $product->received_amount . '</td>
                                </tr>
                            ';
                            }

                            $distribution_delivery_performa .= '
                                <tr>
                                    <td colspan="2" class="color primary font-weight-bold">Total</td>
                                    <td>' . $total_items . '</td>
                                    <td>' . $total_delivered_items . '</td>
                                    <td>' . $total_returned_items . '</td>
                                    <td>' . $total_units_per_item . '</td>
                                    <td>' . $total_units . '</td>
                                    <td>' . $total_delivered_units . '</td>
                                    <td>' . $total_returned_units . '</td>
                                    <td>' . $total_price . '</td>
                                </tr>
                                </tbody>
                                </table>
                                </div>
                                <div class="col row align-items-center justify-content-center end_of_air_waybill"><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>';

                            $shipment_details .= $distribution_delivery_performa;
                        }
                    }
                }
                //delivery location watermark start
                            $msg_string = null;
                            $str_arr = null;
                            $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $shipment->consignee_address);
                            // $str_arr = preg_split("/[ ,]+/", $shipment->consignee_address);
                            foreach ($check as $nsa) {
                                foreach ($str_arr as $arr_value) {
                                    if (strtolower($nsa) == strtolower($arr_value)) {
                                            $msg_string = $arr_value;
                                    }
                                }
                            }

                            $delivery_area = null;
                            if($msg_string != null){
                                $found = DeliveryLocationMappingKeyword::join('delivery_location_mappings as dlm','delivery_location_mapping_keywords.mapping_id','=','dlm.id')
                                ->select('dlm.area_name as area_name','dlm.id')
                                ->where('delivery_location_mapping_keywords.keyword',$msg_string)
                                ->where('dlm.city_id',$shipment->consignee_city_id);
                                if($found->exists()){
                                    $found = $found->first();
                                    $delivery_area = $found->area_name;
                                }
                            }

                            if($delivery_area != null){
                                for($i=0; $i<10; $i++){
                                    $delivery_area.= ' '.$delivery_area;
                                    if(strlen($delivery_area)>25){
                                        break;
                                    }
                                }
                                if($page_break){
                                    $shipment_details .= '
                                    <div id="delivery_area_watermark" class="delivery_area_watermark">
                                    <h1 style="
                                      text-align: center;  
                                      text-transform: uppercase;                  
                                      overflow: hidden;
                                      position: absolute;
                                      margin-top: -1200px;
                                      opacity: 0.2;
                                      transform: rotate(350deg);
                                      font-size: 400%; 
                                      color: #000000; 
                                      font-stretch: extra-expanded;"     
                                      > ' . $delivery_area . '  </h1>
                                    
                                    <!--<p>Your trial membership will expire in 3 days!</p>-->
                                  </div>';
                                }else{
                                    $shipment_details .= '
                                    <div id="delivery_area_watermark" class="delivery_area_watermark">
                                    <h1 style="
                                      text-align: center;  
                                      text-transform: uppercase;                  
                                      overflow: hidden;
                                      position: absolute;
                                      margin-top: -290px;
                                      opacity: 0.2;
                                      transform: rotate(350deg);
                                      font-size: 400%; 
                                      color: #000000; 
                                      font-stretch: extra-expanded;"     
                                      > ' . $delivery_area . '  </h1>
                                    
                                    <!--<p>Your trial membership will expire in 3 days!</p>-->
                                  </div>';
                                }
                                
                            }
                            
            //delivery location watermark end

                            
                            //receiving_sheet_print
                            if($print_status == 1){
                                
                                if($page_break){
                                    $shipment_details .= '
                                    <div id="watermark_" class="watermark_">
                                    <h1 style="
                                      text-align: center;  
                                      text-transform: uppercase;                  
                                      overflow: hidden;
                                      position: absolute;
                                      margin-top: -1200px;
                                      opacity: 0.2;
                                      transform: rotate(350deg);
                                      font-size: 400%; 
                                      color: red; 
                                      font-stretch: extra-expanded;"     
                                      > Duplicate Print  </h1>
                                    
                                    <!--<p>Your trial membership will expire in 3 days!</p>-->
                                  </div>';
                                }else{
                                    $shipment_details .= '
                                    <div id="watermark_" class="watermark_">
                                    <h1 style="
                                      text-align: center;  
                                      text-transform: uppercase;                  
                                      overflow: hidden;
                                      position: absolute;
                                      margin-top: -290px;
                                      opacity: 0.2;
                                      transform: rotate(350deg);
                                      font-size: 400%; 
                                      color: red; 
                                      font-stretch: extra-expanded;"     
                                      > Duplicate Print  </h1>
                                    
                                    <!--<p>Your trial membership will expire in 3 days!</p>-->
                                  </div>';
                                }
                                
                            }
            }
            if ($user_type == 3) {
                $airwaybill_journey = ShipmentsAirWaybillJourney::where('shipment_id', $shipment->id)->where('user_type', 3);
                if ($airwaybill_journey->exists()) {
                    $watermark_flag = true;
                }
            }

            $overall_shipment_details .= $shipment_details;

            for ($i = 1; $i < $prints; $i++) {
                $overall_shipment_details .= $shipment_details;
            }
            $shipment_details = '';
            //a
            // $overall_shipment_details .=$delivery_area_watermark;
            
        }

        $html .= $overall_shipment_details;

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
            

            if ($watermark_flag) {
                $html .= '
                  </body>
                  <div id="watermark_" class="watermark_">
                    <h1 style="
                   text-align: center;  
                   text-transform: uppercase;                  
                   overflow: hidden;
                   position: fixed;
                   margin-top: -320px;
                   opacity: 0.4;
                   transform: rotate(350deg);
                   font-size: 400%; 
                   color: red; 
                   font-stretch: extra-expanded;"     
                    > ' . $watermark . '  </h1>
                    
                    <!--<p>Your trial membership will expire in 3 days!</p>-->
                  </div>
                  
                </html>
            ';
            }

        }

        return $html;
    }



    public function print_air_waybill(Request $request)
    {

        $ids = GlobalSettings::where('type','cn_print_rights')->first();
        if($ids->text != null)
        {
            $ids=$ids->text;
            $role_ids = explode(',', $ids);

            if (in_array(session('role_id'),$role_ids)) {
                return response()->json(['status' => '2', 'error' => 'You are restricted from printing duplicate airway bill(s). Please contact your line manager.']);
            }
        }


        $user_type = NULL;
        $user_id = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();
        } else if (Auth::guard('web')->check()) {
            $user_type = 1;

            $user_id = session('user_id');
        } else if (Auth::guard('substitute_users')->check()) {
            $user_type = 2;

            $user_id = session('user_id');
        }

        if ($user_type) {
            $air_waybill_type = Session::get('air_waybill_type', 1);

            if ($air_waybill_type != 3) {
                if ($request->sticker) {
                    $shipment_ids = Shipment::whereIn('id', $request->ids)->orderBy('order_id', 'ASC')->orderBy('id', 'ASC')->pluck('id')->toArray();

                    return $this->air_waybill_sticker_pdf($user_type, $user_id, $shipment_ids);
                } else {
                    $shipper_phone = NULL;
                    $shipper_name = NULL;
                    if ($request->has('shipper_name')) {
                        $shipper_name = $request->shipper_name;
                    }

                    if ($request->has('shipper_phone')) {
                        $shipper_phone = $request->shipper_phone;
                    }
                    return $this->air_waybill($user_type, $user_id, $request->ids, FALSE, NULL, $shipper_name, $shipper_phone);
                }
            } else {
                return $this->air_waybill_sticker_barcode($user_type, $user_id, $request->ids);
            }
        }

    }

    public function excel_index()
    {

        $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
        $user = User::find(session('user_id'));
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        if (in_array(session('user_id'), [5982, 3324, 10104, 14110, 16292])) {
            $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        } else {
            $cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        }
        $products = Product::all();

        $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

        $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();

        if (in_array(4, $user_shipping_modes)) {
            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        } else {
            $shipping_mode_same_day_timings = NULL;
        }

        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (!in_array(session('user_id'), $ccd_account_tags)) {
                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
            } else {
                $payment_modes = PaymentMode::whereNotIn('id', [3])->get();
            }
        } else {
            $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        }
        $charges_modes = ChargesModes::whereIn('id', [4])->get();

        $omni_user = 0;
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->text != NULL) {
                $omni_accounts = array_map('intval', explode(',', $settings->text));
                if (in_array(session('user_id'), $omni_accounts)) {
                    $omni_user = 1;
                }
            }
        }

        return view('client.shipment.book.excel')->with(['booking_types' => $booking_types, 'user' => $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes, 'omni_user' => $omni_user]);
    }

    public function excel_store(Request $request)
    {
        //dd($request);
        $user_id = session('user_id');
        if (!$request->has('omni')) {
            $omni = 0;
        } else {
            $omni = $request->omni;
        }

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            if ($value) {
                $value = $this->phone_number($value);

                if (preg_match('/^((\+92)|(92)|(0092))-{0,1}\d{3}-{0,1}\d{7}$|^\d{3}-{1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$|^\d{3}-\d{7}$|^\d{10}$/', $value)) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

        Validator::extend('origin_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if ($service_type_id == 5) {
                    return true;
                }
                $result = self::check_origin($value, $shipping_mode_id, $user_id);
                if ($result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

        Validator::extend('destination_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if ($service_type_id == 5) {
                    return true;
                }
                $result = self::check_destination($value, $shipping_mode_id, $user_id, 1);
                if ($result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

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
            'self_collection' => 'Self Collection',
            'order_id' => 'Order ID',
            'order_date' => 'Order Date',


            'item_product_type_id' => 'Item Product Type ID',
            'item_description' => 'Item Description',
            'item_quantity' => 'Item Quantity',
            'item_insurance' => 'Item Insurance',
            'item_price' => 'Product Value',

            'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
            'replacement_item_description' => 'Replacement Item Description',
            'replacement_item_quantity' => 'Replacement Item Quantity',

            'item_product_type_id_1' => 'Try and Buy Item Product Type ID 1',
            'item_description_1' => 'Try and Buy Item Description 1',
            'item_quantity_1' => 'Try and Buy Item Quantity 1',
            'item_insurance_1' => 'Try and Buy Item Insurance 1',
            'item_price_1' => 'Try and Buy Product Value 1',
            'item_product_type_id_2' => 'Try and Buy Item Product Type ID 2',
            'item_description_2' => 'Try and Buy Item Description 2',
            'item_quantity_2' => 'Try and Buy Item Quantity 2',
            'item_insurance_2' => 'Try and Buy Item Insurance 2',
            'item_price_2' => 'Try and Buy Product Value 2',
            'item_product_type_id_3' => 'Try and Buy Item Product Type ID 3',
            'item_description_3' => 'Try and Buy Item Description 3',
            'item_quantity_3' => 'Try and Buy Item Quantity 3',
            'item_insurance_3' => 'Try and Buy Item Insurance 3',
            'item_price_3' => 'Try and Buy Product Value 3',
            'item_product_type_id_4' => 'Try and Buy Item Product Type ID 4',
            'item_description_4' => 'Try and Buy Item Description 4',
            'item_quantity_4' => 'Try and Buy Item Quantity 4',
            'item_insurance_4' => 'Try and Buy Item Insurance 4',
            'item_price_4' => 'Try and Buy Product Value 4',
            'item_product_type_id_5' => 'Try and Buy Item Product Type ID 5',
            'item_description_5' => 'Try and Buy Item Description 5',
            'item_quantity_5' => 'Try and Buy Item Quantity 5',
            'item_insurance_5' => 'Try and Buy Item Insurance 5',
            'item_price_5' => 'Try and Buy Product Value 5',

            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
            'shipping_mode_id' => 'Shipping Mode ID',
            'same_day_timing_id' => 'Same Day Timing ID',
            'try_and_buy_charges' => 'Try and Buy Charges',
            'amount' => 'Collection Amount',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID',
            'pieces_quantity' => 'Pieces',

            'shipper_reference_number_1' => 'Shipper Reference Number 1',
            'shipper_reference_number_2' => 'Shipper Reference Number 2',
            'shipper_reference_number_3' => 'Shipper Reference Number 3',
            'shipper_reference_number_4' => 'Shipper Reference Number 4',
            'shipper_reference_number_5' => 'Shipper Reference Number 5',
            'open_shipment' => 'Open Shipment',
            'return_address_id' => 'Return Address Id',

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

            'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'phone_number' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'origin_check' => 'Origin city not allowed, please contact your sales person!',
            'destination_check' => 'Destination city not allowed, please contact your sales person!',
        ];

        $rules = [
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0), 'origin_check'],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 1)->where('status', 1), 'destination_check'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'phone_number'],
            'consignee_phone_number_2' => ['nullable', 'phone_number'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'self_collection' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],

            'open_shipment' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],

            'item_product_type_id' => ['required_if:service_type_id,1,2,5', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required_if:service_type_id,1,2,5', 'nullable', 'between:0,1000'],
            'item_quantity' => ['required_if:service_type_id,1,2,5', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance' => ['required_if:service_type_id,1,2,5', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price' => ['required_if:item_insurance,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_1' => ['required_if:service_type_id,3', 'nullable', 'between:0,1000'],
            'item_quantity_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_1' => ['required_if:service_type_id,3', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_2' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_2' => ['required_with:item_product_type_id_2,', 'nullable', 'between:0,1000'],
            'item_quantity_2' => ['required_with:item_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_2' => ['required_with:item_product_type_id_2,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_2' => ['required_with:item_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_3' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_3' => ['required_with:item_product_type_id_3,', 'nullable', 'between:0,1000'],
            'item_quantity_3' => ['required_with:item_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_3' => ['required_with:item_product_type_id_3,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_3' => ['required_with:item_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_4' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_4' => ['required_with:item_product_type_id_4,', 'nullable', 'between:0,1000'],
            'item_quantity_4' => ['required_with:item_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_4' => ['required_with:item_product_type_id_4,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_4' => ['required_with:item_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_5' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_5' => ['required_with:item_product_type_id_5,', 'nullable', 'between:0,1000'],
            'item_quantity_5' => ['required_with:item_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_5' => ['required_with:item_product_type_id_5,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_5' => ['required_with:item_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,1000'],
            'replacement_item_quantity' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })],
            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'amount' => ['required_if:service_type_id,1,2', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            'try_and_buy_charges' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            // 'payment_mode_id' => ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
            //     $query->whereNotIn('id', [2]);
            // })],
            'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function ($query) {
                $query->whereIn('id', [4]);
            })],
            'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],


            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190'],
            'return_address_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0)],

        ];
        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (in_array($user_id, $ccd_account_tags)) {
                $rules['payment_mode_id'] = ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [3]);
                })];
            } else {
                $rules['payment_mode_id'] = ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [2, 3]);
                })];
            }
        }
        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        }
        if (isset($spreadsheet)) {
            $excel_type = $request->excel_type;

            $column_count = null;

            if ($excel_type == 1) {
                $column_count = 60;

                $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'self_collection', 10 => 'order_id', 11 => 'order_date', 12 => 'item_product_type_id', 13 => 'item_description', 14 => 'item_quantity', 15 => 'item_insurance', 16 => 'item_price', 17 => 'replacement_item_product_type_id', 18 => 'replacement_item_description', 19 => 'replacement_item_quantity', 20 => 'item_product_type_id_1', 21 => 'item_description_1', 22 => 'item_quantity_1', 23 => 'item_insurance_1', 24 => 'item_price_1', 25 => 'item_product_type_id_2', 26 => 'item_description_2', 27 => 'item_quantity_2', 28 => 'item_insurance_2', 29 => 'item_price_2', 30 => 'item_product_type_id_3', 31 => 'item_description_3', 32 => 'item_quantity_3', 33 => 'item_insurance_3', 34 => 'item_price_3', 35 => 'item_product_type_id_4', 36 => 'item_description_4', 37 => 'item_quantity_4', 38 => 'item_insurance_4', 39 => 'item_price_4', 40 => 'item_product_type_id_5', 41 => 'item_description_5', 42 => 'item_quantity_5', 43 => 'item_insurance_5', 44 => 'item_price_5', 45 => 'special_instructions', 46 => 'estimated_weight', 47 => 'shipping_mode_id', 48 => 'same_day_timing_id', 49 => 'try_and_buy_charges', 50 => 'amount', 51 => 'payment_mode_id', 52 => 'charges_mode_id', 53 => 'pieces_quantity', 54 => 'shipper_reference_number_1', 55 => 'shipper_reference_number_2', 56 => 'shipper_reference_number_3', 57 => 'shipper_reference_number_4', 58 => 'shipper_reference_number_5', 59 => 'open_shipment'];

                $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [4]);
                })];
                $service_type_check_id = null;
            } elseif ($excel_type == 2) {
                $column_count = 30;

                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'self_collection', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'special_instructions', 17 => 'estimated_weight', 18 => 'shipping_mode_id', 19 => 'same_day_timing_id', 20 => 'amount', 21 => 'payment_mode_id', 22 => 'charges_mode_id', 23 => 'pieces_quantity', 24 => 'shipper_reference_number_1', 25 => 'shipper_reference_number_2', 26 => 'shipper_reference_number_3', 27 => 'shipper_reference_number_4', 28 => 'shipper_reference_number_5', 29 => 'open_shipment'];
                $service_type_check_id = 1;
            } elseif ($excel_type == 3) {
                $column_count = 31;

                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'replacement_item_product_type_id', 16 => 'replacement_item_description', 17 => 'replacement_item_quantity', 18 => 'special_instructions', 19 => 'estimated_weight', 20 => 'shipping_mode_id', 21 => 'same_day_timing_id', 22 => 'amount', 23 => 'payment_mode_id', 24 => 'charges_mode_id', 25 => 'shipper_reference_number_1', 26 => 'shipper_reference_number_2', 27 => 'shipper_reference_number_3', 28 => 'shipper_reference_number_4', 29 => 'shipper_reference_number_5', 30 => 'open_shipment'];
                $service_type_check_id = 2;
            } elseif ($excel_type == 4) {
                $column_count = 48;

                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id_1', 11 => 'item_description_1', 12 => 'item_quantity_1', 13 => 'item_insurance_1', 14 => 'item_price_1', 15 => 'item_product_type_id_2', 16 => 'item_description_2', 17 => 'item_quantity_2', 18 => 'item_insurance_2', 19 => 'item_price_2', 20 => 'item_product_type_id_3', 21 => 'item_description_3', 22 => 'item_quantity_3', 23 => 'item_insurance_3', 24 => 'item_price_3', 25 => 'item_product_type_id_4', 26 => 'item_description_4', 27 => 'item_quantity_4', 28 => 'item_insurance_4', 29 => 'item_price_4', 30 => 'item_product_type_id_5', 31 => 'item_description_5', 32 => 'item_quantity_5', 33 => 'item_insurance_5', 34 => 'item_price_5', 35 => 'special_instructions', 36 => 'estimated_weight', 37 => 'shipping_mode_id', 38 => 'same_day_timing_id', 39 => 'try_and_buy_charges', 40 => 'payment_mode_id', 41 => 'charges_mode_id', 42 => 'shipper_reference_number_1', 43 => 'shipper_reference_number_2', 44 => 'shipper_reference_number_3', 45 => 'shipper_reference_number_4', 46 => 'shipper_reference_number_5', 47 => 'open_shipment'];
                $service_type_check_id = 3;
            } elseif ($excel_type == 5) {
                $column_count = 25;

                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'special_instructions', 16 => 'estimated_weight', 17 => 'shipping_mode_id', 18 => 'same_day_timing_id', 19 => 'shipper_reference_number_1', 20 => 'shipper_reference_number_2', 21 => 'shipper_reference_number_3', 22 => 'shipper_reference_number_4', 23 => 'shipper_reference_number_5', 24 => 'open_shipment'];
                $service_type_check_id = 5;
            } elseif ($excel_type == 6) {      //for omni
                $column_count = 31;

                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'self_collection', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'special_instructions', 17 => 'estimated_weight', 18 => 'shipping_mode_id', 19 => 'same_day_timing_id', 20 => 'amount', 21 => 'payment_mode_id', 22 => 'charges_mode_id', 23 => 'pieces_quantity', 24 => 'shipper_reference_number_1', 25 => 'shipper_reference_number_2', 26 => 'shipper_reference_number_3', 27 => 'shipper_reference_number_4', 28 => 'shipper_reference_number_5', 29 => 'open_shipment', 30 => 'return_address_id'];
                $omni = 1;
                $service_type_check_id = 1;
            } elseif ($excel_type == 7) {      //for omni overall
                $column_count = 61;

                $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'self_collection', 10 => 'order_id', 11 => 'order_date', 12 => 'item_product_type_id', 13 => 'item_description', 14 => 'item_quantity', 15 => 'item_insurance', 16 => 'item_price', 17 => 'replacement_item_product_type_id', 18 => 'replacement_item_description', 19 => 'replacement_item_quantity', 20 => 'item_product_type_id_1', 21 => 'item_description_1', 22 => 'item_quantity_1', 23 => 'item_insurance_1', 24 => 'item_price_1', 25 => 'item_product_type_id_2', 26 => 'item_description_2', 27 => 'item_quantity_2', 28 => 'item_insurance_2', 29 => 'item_price_2', 30 => 'item_product_type_id_3', 31 => 'item_description_3', 32 => 'item_quantity_3', 33 => 'item_insurance_3', 34 => 'item_price_3', 35 => 'item_product_type_id_4', 36 => 'item_description_4', 37 => 'item_quantity_4', 38 => 'item_insurance_4', 39 => 'item_price_4', 40 => 'item_product_type_id_5', 41 => 'item_description_5', 42 => 'item_quantity_5', 43 => 'item_insurance_5', 44 => 'item_price_5', 45 => 'special_instructions', 46 => 'estimated_weight', 47 => 'shipping_mode_id', 48 => 'same_day_timing_id', 49 => 'try_and_buy_charges', 50 => 'amount', 51 => 'payment_mode_id', 52 => 'charges_mode_id', 53 => 'pieces_quantity', 54 => 'shipper_reference_number_1', 55 => 'shipper_reference_number_2', 56 => 'shipper_reference_number_3', 57 => 'shipper_reference_number_4', 58 => 'shipper_reference_number_5', 59 => 'open_shipment', 60 => 'return_address_id'];

                $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [4]);
                })];
                $service_type_check_id = null;
                $omni = 1;
            } else {
                return redirect()->back()->with('error', 'Invalid Template Selected');
            }

            if (count($spreadsheet[0]) != $column_count) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            unset($spreadsheet[0]);
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
            } else {
                $forms = $request->all();

                $forms = $forms['form'];
                foreach ($forms as $form) {
                    $row = array();
                    foreach ($form as $key => $value) {
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
                $service_type_check_id = $request->service_type_check_id;

            }

            $errors = array();
            $nsa_error = array();
            $order_ids = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();
            $blacklist_errors = array();
            $blacklist_found_categories = array();
            $check_bdmk = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                ->where('bdm.status',1)
                ->select(['booking_destination_mapping_keywords.keyword'])
                ->pluck('keyword')
                ->toArray();
            $bdmk_error = array();
            $bdmk_result = array();


            if (Session::has('prefix')) {
                $rules['order_id'] = ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })];
            } else {
                if (Session::has('restrict_order_id')) {
                    $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })];
                } else {
                    $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
                }
            }

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if (!isset($row['charges_mode_id'])) {
                    $rows[$key]['charges_mode_id'] = 4;
                }
                if ($service_type_check_id != null) {
                    $rows[$key]['service_type_id'] = $service_type_check_id;
                    $row['service_type_id'] = $service_type_check_id;
                } else {
                    $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                        $query->whereNotIn('id', [4]);
                    })];
                }


                if (!isset($row['pieces_quantity']) || $row['pieces_quantity'] == null) {
                    $row['pieces_quantity'] = 1;
                }

                $rows[$key]['pieces_quantity'] = $row['pieces_quantity'];

                if (!isset($row['self_collection']) || $row['self_collection'] == null) {
                    $row['self_collection'] = 'no';
                }
                if (!isset($row['open_shipment']) || $row['open_shipment'] == null) {
                    $row['open_shipment'] = 'no';
                }
                $rows[$key]['open_shipment'] = $row['open_shipment'];

                if (!isset($row['return_address_id']) || $row['return_address_id'] == null || $row['service_type_id'] != 1) {
                    $row['return_address_id'] = NULL;
                }

                $rows[$key]['return_address_id'] = $row['return_address_id'];


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
                    if (Session::has('prefix')) {
                        $length = strlen(session('prefix'));
                        $check_order_id = str_split($row['order_id'], $length);
                        if (session('prefix') != $check_order_id[0]) {
                            $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                        } else {
                            if (!array_key_exists(1, $check_order_id)) {
                                $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                            }
                        }
                    }

                    if (!empty(trim($row['order_id']))) {
                        if (empty($order_ids)) {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        } else {
                            if (in_array($row['order_id'], $order_ids, true)) {
                                $errors[$row_id]['order_id'] = 'Same Order ID as of Row #' . $order_id_row[$row['order_id']];
                            } else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                            }
                        }
                    }

                    if ($row['service_type_id'] != 5) {
                        $user_shipping_info = UserShippingInfo::find($row['pickup_address_id']);

                        if (!$user_shipping_info->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Pickup Address ID #' . $row['pickup_address_id'] . ' is disabled';
                        }

                        if ($row['service_type_id'] == 1 && $omni == 1) {
                            if ($row['return_address_id'] != NULL) {
                                $user_return_info = UserShippingInfo::find($row['return_address_id']);

                                if (!$user_return_info->status) {
                                    $errors[$row_id]['return_address_id'] = 'Return Address ID #' . $row['return_address_id'] . ' is disabled';
                                }
                            }
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
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                        }

                        if ($consignee_city->id == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292) {
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is not allowed for this shipper';
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

                        if (($user_shipping_info->city->id != $consignee_city->id) && ($service_type_check_id == 1 || $service_type_check_id == 2)) {
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
                        if (!$request->excel_nsa) {
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
                        };
                        if(!$request->excel_bdmk) {
                            $bdmk_result[$row_id] = $this->check_bdmk($consignee_city->id, $row['consignee_address'], $check_bdmk);
//                        dd($bdmk_result[$row_id]);
                            if (isset($bdmk_result[$row_id]['invalid_cities'])) {
                                $bdmk_error[$row_id]['msg'] = $bdmk_result[$row_id]['invalid_cities'];
                            }
                        }

                        if (!CityDelivery::where('city_id', $consignee_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['consignee_city_name'] = 'Delivery is not allowed for City: ' . $consignee_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                        if (!$request->excel_blacklist) {
                            $consignee_phone_number_1 = substr_replace($row['consignee_phone_number_1'], '-', 4, 0);
                            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
                            if ($consignee_information->exists()) {
                                $consignee_information = $consignee_information->first();
                                $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information->id);
                                if ($manual_blacklist->exists()) {
                                    $manual_blacklist = $manual_blacklist->first();
                                    $blacklist_setting_id = $manual_blacklist->blacklist_setting_id;
                                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                    if ($blacklist_setting) {
                                        if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                            $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                            $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                        }
                                    }
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    } else {
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: 0, Delivered: 0, Undelivered: 0, Return Confirmed: 0';
                                    }
                                } else {
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_setting_id = $blacklist->blacklist_setting_id;
                                        $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                        if ($blacklist_setting) {
                                            if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                                $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                                $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                            }
                                        }
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    }
                                }
                            }
                        }
                    } else {
                        $pickup_consignee_city = City::where('name', $row['consignee_city_name'])->first();

                        if (!$pickup_consignee_city->status) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if ($pickup_consignee_city->id == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292) {
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $pickup_consignee_city->name . ' is not allowed for this shipper';
                        }

                        if (!$pickup_consignee_city->zone_id) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if ($user_id != 7762) {
                            if (!$pickup_consignee_city->pickup) {
                                $errors[$row_id]['consignee_city_name'] = 'Pickup is not allowed for City: ' . $pickup_consignee_city->name;
                            }
                        }

                        $pickup_address_id_for_delivery = $row['pickup_address_id'];
                        $pickup_address_for_delivery = UserShippingInfo::find($pickup_address_id_for_delivery);
                        $delivery_city = City::find($pickup_address_for_delivery->city_id);

                        if (!$delivery_city->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }
                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        $pickup_city_id = $pickup_consignee_city->id;

                        if ($delivery_city->id != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                            $errors[$row_id]['consignee_city_name'] = 'Same Day Delivery is not available for Different City Shipment';
                        }
                        if (!CityDelivery::where('city_id', $delivery_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery is not allowed for City: ' . $delivery_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                    }
                }
            }
//            dd($service_type_check_id);
            if (empty($errors)) {
                if (empty($nsa_error)) {
                    if (empty($bdmk_error)) {
                        if (TRUE || empty($blacklist_errors)) {
                            foreach ($rows as $key => $row) {
                                $row['user_id'] = $user_id;
                                $row['account_type_id'] = 1;
                                $row['nsas'] = $check;
                                $row['nsa'] = $request->excel_nsa;
                                if (session('user_type') == 2) {
                                    $row['substitute_user_id'] = Auth::id();
                                } else {
                                    $row['substitute_user_id'] = null;
                                }

                                if (Session::has('prefix')) {
                                    $row['prefix'] = session('prefix');
                                } else {
                                    $row['prefix'] = NULL;
                                }
                                $row['business_category_id'] = 1;

                                if ($row['service_type_id'] == 3 && $row['payment_mode_id'] == 4) {
                                    $row['payment_mode_id'] == 1;
                                }
                                if ($row['service_type_id'] != 5) {
                                    if ($row['payment_mode_id'] == 4) {
                                        $row['amount'] = 0;
                                    }
                                }
                                if ($user_id != 3324) {
                                    dispatch(new ProcessShipmentBookingDB($row));
                                } else {
                                    dispatch(new ProcessShipmentBookingDBPriority($row));
                                }
                            }

                            return redirect()->back()->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                        } else {
                            return view('client.shipment.book.blacklist')->with(['data' => $rows, 'blacklist_errors' => $blacklist_errors, 'blacklist_found_categories' => $blacklist_found_categories, 'service_type_check_id' => $service_type_check_id]);
                        }
                    }else{
                        return view('client.shipment.book.bdmk')->with(['data' => $rows, 'bdmk_error' => $bdmk_error, 'service_type_check_id' => $service_type_check_id, 'omni' => $omni]);
                    }
                } else {
                    return view('client.shipment.book.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error, 'service_type_check_id' => $service_type_check_id, 'omni' => $omni]);
                }
            } else {
                if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
                    $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                } else {
                    $cities = City::where('status', 1)->where('id', '!=', 1244)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                }
                $booking_types = BookingType::whereNotIn('id', [4])->pluck('booking_type', 'id');
                $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                    $query->where('pickup', 1)->where('business_category_id', 1)->where('status', 1)->whereNotNull('zone_id');
                })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                $products = Product::pluck('product_name', 'id');

                $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

                $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->pluck('mode', 'id');

                if (in_array(4, $user_shipping_modes)) {
                    $shipping_mode_same_day_timings = ShippingModeSameDayTiming::pluck('timing', 'id');
                } else {
                    $shipping_mode_same_day_timings = NULL;
                }
                $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
                if ($ccd_booking->exists()) {
                    $ccd_booking = $ccd_booking->first();
                    $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
                    if (!in_array(session('user_id'), $ccd_account_tags)) {
                        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->pluck('mode', 'id');
                    } else {
                        $payment_modes = PaymentMode::whereNotIn('id', [3])->pluck('mode', 'id');
                    }
                } else {
                    $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
                }
                //$payment_modes = PaymentMode::whereNotIn('id', [3])->pluck('mode', 'id');
                $charges_modes = ChargesModes::whereIn('id', [4])->pluck('charges_mode', 'id');
                $city_name = array();
                foreach ($cities as $city) {
                    $city_name[$city->name] = $city->name;
                }

                return view('client.shipment.book.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'user_shipping_modes' => $user_shipping_modes, 'charges_modes' => $charges_modes, 'service_type_check_id' => $service_type_check_id, 'omni' => $omni]);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    static public function corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id)
    {


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
        $shipment->special_instructions = $special_instructions;

        $shipment->estimated_weight = $estimated_weight;
        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->amount = $amount;

        if ($payment_mode_id == 2) {
            $settings = GlobalSettings::where('type', 'ccd_booking')->first();
            $ccd_accounts = array();
            $ccd_accounts = array_map('intval', explode(',', $settings->text));

            if (in_array($user_id, $ccd_accounts)) {
                $payment_mode_id = 2;
            } else {
                $payment_mode_id = 1;
            }
        }

        $shipment->payment_mode_id = $payment_mode_id;

        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;
        $shipment->walk_in_delivery_type_id = $delivery_type_id;
        $shipment->charges_mode_id = $charges_mode_id;
        $shipment->business_category_id = $business_category_id;

        $shipment->try_and_buy_fees = $try_and_buy_charges;

        $shipment->booked_by = session('user_type');
        $shipment->pieces = $pieces;
        if ($return_address_id) {
            $shipment->return_address_id = $return_address_id;
        }
        $shipment->save();

        $shipment_id = $shipment->id;
        $shipment_detail = new ShipmentDetail();
        $shipment_detail->shipment_id = $shipment_id;
        $shipment_detail->is_open = $open_shipment;
        $shipment_detail->save();
        if ($self_collection == TRUE) {
            $shipment_self_collection = new SelfCollectionShipment();
            $shipment_self_collection->shipment_id = $shipment_id;
            $shipment_self_collection->save();
        }

        AdminPickupsController::generate($shipment_id);

        if (session('user_type') != 1) {
            $reference_1_id = Auth::id();
        } else {
            $reference_1_id = null;
        }
        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL, $reference_1_id);
        ConsigneeInformationController::add($consignee_phone_number_1, $consignee_name, $consignee_address, $consignee_phone_number_2, $consignee_city_id, $user_id);

        //Existing Coordinates
        $coordinates = ConsigneeLocation::where(function ($sub_query) use ($consignee_phone_number_1, $consignee_phone_number_2) {
            $sub_query->where('phone_number', $consignee_phone_number_1)
                ->orwhere('phone_number', $consignee_phone_number_2);
        })->where('address', $consignee_address);
        if ($coordinates->exists()) {
            $coordinates = $coordinates->latest()->first();

            $shipment_coordinates = new ConsigneeShipmentLocation();
            $shipment_coordinates->shipment_id = $shipment_id;
            $shipment_coordinates->previous_location_id = $coordinates->id;
            $shipment_coordinates->current_location_id = NULL;
            $shipment_coordinates->save();
        }
        //Existing Coordinates
        //        if($pieces > 1){
//            $user = User::where('id', $user_id)->where('multipiece_status', 0);
//            if($user->exists()){
//                $user = $user->first();
//                $user->multipiece_status = 1;
//                $user->save();
//            }
//        }

        return $shipment_id;
    }

    public function corporate_index()
    {
        // $time = Carbon::today()->addHour(15);
        // $current_time = Carbon::now();
        $date = Carbon::today();
        // if($current_time > $time){
        //     $date = Carbon::tomorrow();
        // }
        $booking_types = BookingType::whereNotIn('id', [4])->get();
        $user = User::with('shipping.city')->find(session('user_id'));
        $multi_piece = $user->multipiece_status;
        $cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        if (in_array(session('user_id'), [5982, 3324, 10104, 14110, 16292])) {
            $consignee_cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        } else {
            $consignee_cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        }
        $products = Product::orderBy('product_name')->get();
        $distribution_products = DistributionProduct::orderBy('name')->get();
        $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();

        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (!in_array(session('user_id'), $ccd_account_tags)) {
                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
            } else {
                $payment_modes = PaymentMode::whereNotIn('id', [3])->get();
            }
        } else {
            $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        }
        $user_delivery_types = CorporateDeliveryTypeStatus::where('user_id', session('user_id'))->pluck('shipping_mode_id')->toArray();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $charges_modes = ChargesModes::whereIn('id', [3])->get();
        $check = NonServiceArea::pluck('name')->toArray();
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if ($air_waybill->exists()) {
            $air_waybill = $air_waybill->first();
        } else {
            $air_waybill = null;
        }
        $approve_ftl_requests = FtlRequest::where('shipper_id', session('user_id'))->where('status_id', 3)->get();
        $omni_user = 0;
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->text != NULL) {
                $omni_accounts = array_map('intval', explode(',', $settings->text));
                if (in_array(session('user_id'), $omni_accounts)) {
                    $omni_user = 1;
                }
            }
        }

        return view('client.shipment.book.corporate.index')->with(['booking_types' => $booking_types, 'multi_piece' => $multi_piece, 'user' => $user, 'cities' => $cities, 'distribution_products' => $distribution_products, 'products' => $products, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'consignee_cities' => $consignee_cities, 'check' => $check, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes, 'date' => $date, 'air_waybill' => $air_waybill, 'user_delivery_types' => $user_delivery_types, 'approve_ftl_requests' => $approve_ftl_requests, 'omni_user' => $omni_user]);
    }

    public function corporate_store(Request $request) {
        $rules = [
            'replacement_parcel_img' => ['nullable', 'mimes:png,jpeg,jpg'],
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return back()->with(['error' => "Invalid File Format Of Replacement Parcel Image"]);
        } else {
        if ($request->open_shipment == 'on') {
            $open_shipment = 1;
        } else {
            $open_shipment = 0;
        }
        if (!empty($request->input('shipping_mode'))) {
            $user_id = session('user_id');
            if ($request->input('consignee_email_address')) {
                $user_email_id = $request->input('consignee_email_address');
            } else {
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
            } else {
                if ($service_type_id != 5) {
                    $pickup_address_id = $request->input('pickup_address');

                    $user_shipping_info = UserShippingInfo::find($pickup_address_id);

                    $pickup_city_id = $user_shipping_info->city_id;
                } else {
                    $pickup_address_id = $this->add_pickup_address($user_id, $request->input('consignee_address'), $request->input('consignee_name'), NULL, $request->input('consignee_phone_number_1'), $user_email_id, $request->input('consignee_city'), 0, TRUE);

                    $pickup_city_id = $request->input('consignee_city');
                    $pickup_address_id_for_delivery = $request->input('pickup_address');
                }
            }

            /*	if ($service_type_id == 1 || $service_type_id == 2) {
                    if ($request->filled('return_address')) {
                        $return_address_id = $request->return_address;
                    } else {
                        $return_address_id = FALSE;
                    }
                }
                else{
                    $return_address_id = FALSE;
                }*/
            $return_address_id = FALSE;
            if ($request->input('return_address') == 0 && $request->input('return_address') != null) {
                if ($service_type_id == 5) {
                    return redirect()->back()->with('error', 'New Return Address cannot be selected for Reverse Pickup');
                }

                $return_city_id = $request->input('new_return_city');
                $default = 0;

                $return_address_id = $this->add_pickup_address($user_id, $request->input('new_return_address'), $request->input('new_return_person_of_contact'), $request->input('new_return_vendor'), $request->input('new_return_phone_number'), $request->input('new_return_email_address'), $return_city_id, $default);
            } else {

                if ($service_type_id == 1) {
                    if ($request->filled('return_address')) {
                        $return_address_id = $request->return_address;
                    } else {
                        $return_address_id = FALSE;
                    }
                }

            }

            if ($service_type_id != 5) {
                if ($request->filled('information_display')) {
                    $information_display = TRUE;
                } else {
                    $information_display = FALSE;
                }
            } else {
                $information_display = TRUE;
            }

            if ($service_type_id == 1) {
                if ($request->filled('self_collection')) {
                    $self_collection = TRUE;

                } else {
                    $self_collection = FALSE;


                }
            } else {
                $self_collection = FALSE;


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
            } else {
                $user_shipping_info = UserShippingInfo::find($pickup_address_id_for_delivery);

                $consignee_city_id = $user_shipping_info->city_id;
                $consignee_name = $user_shipping_info->poc;
                $consignee_address = $user_shipping_info->pickup_address;
                $consignee_phone_number_1 = $user_shipping_info->phone;
                $consignee_phone_number_2 = NULL;
                $consignee_email_address = $user_shipping_info->email;
            }

            $shipping_mode_id = $request->input('shipping_mode');

            $origin_allow = self::check_origin($pickup_address_id, $shipping_mode_id, $user_id);
            if ($origin_allow == false) {
                return redirect()->back()->with('error', 'Origin city not allowed, please contact your sales person!');
            }

            $destination_allow = self::check_destination($consignee_city_id, $shipping_mode_id, $user_id, 2);
            if ($destination_allow == false) {
                return redirect()->back()->with('error', 'Destination city not allowed, please contact your sales person!');
            }

            if ($request->filled('order_id')) {
                $order_id = $request->input('order_id');
            } else {
                $order_id = NULL;
            }

            if ($request->filled('package_type')) {
                $package_type = TRUE;
            } else {
                $package_type = FALSE;
            }


            if ($request->filled('special_instructions')) {
                $special_instructions = $request->input('special_instructions');
            } else {
                $special_instructions = NULL;
            }

            if ($request->input('shipping_mode') == 4) {
                $same_day_timing_id = $request->input('same-day_timing');
            } else {
                $same_day_timing_id = NULL;
            }

            $estimated_weight = $request->input('estimated_weight');

            if ($service_type_id != 5) {
                $payment_mode_id = $request->input('payment_mode');
                $delivery_type_id = $request->delivery_type;
                $charges_mode_id = $request->charges_mode;
            } else {
                $delivery_type_id = 1;
                $charges_mode_id = 3;
                $payment_mode_id = 1;
            }

            $amount = str_replace(',', '', $request->input('amount'));

            if ($service_type_id == 3 && $payment_mode_id == 4) {
                $payment_mode_id == 1;
            }

            if ($payment_mode_id == 4) {
                $amount = 0;
            }

            if ($service_type_id == 3) {
                $try_and_buy_charges = $request->input('try_and_buy_charges');
                $amount = 0;
            } else {
                $try_and_buy_charges = NULL;
            }

            $pieces_quantity = 1;
            if ($service_type_id == 1) {
                $pieces_quantity = $request->pieces_quantity ?? 1;
            }
            $business_category_id = 1;
            $shipment_id = $this->corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id);

            if (session('user_type') == 2) {
                $substitute_user_shipment = new SubstituteUserShipment();
                $substitute_user_shipment->substitute_user_id = Auth::id();
                $substitute_user_shipment->shipment_id = $shipment_id;
                $substitute_user_shipment->save();
            }
            if (Session::has('prefix')) {

                $tracking_number = $this->generate_prefix_tracking_number($shipment_id, $request->order_id);
            } else {

                $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
            }
            $this->add_consignee_info($user_id, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address);

            if ($service_type_id == 6) {

                $ftl_request_id = $request->approve_frieght_request;
                FtlRequest::where('id', $ftl_request_id)->update(['shipment_id' => $shipment_id, 'status_id' => 5, 'collection_type' => $request->ftl_collection_type]);
                FTLController::FTLRequestStatusHistory($ftl_request_id, 5, Auth::id());
            }


            if ($request->has('order_date_formatted')) {
                if ($request->order_date_formatted != null) {
                    $order_date = new ShipmentOrderDate();
                    $order_date->shipment_id = $shipment_id;
                    $order_date->order_date = $request->order_date_formatted;
                    $order_date->save();
                }
            }
            if ($request->shipper_reference_1 != null || $request->shipper_reference_2 != null || $request->shipper_reference_3 != null || $request->shipper_reference_4 != null || $request->shipper_reference_5 != null) {
                $shipper_reference = new ShipmentShipperReference();
                $shipper_reference->shipment_id = $shipment_id;
                if ($request->shipper_reference_1 != null) {
                    $shipper_reference->reference_1 = $request->shipper_reference_1;
                }
                if ($request->shipper_reference_2 != null) {
                    $shipper_reference->reference_2 = $request->shipper_reference_2;
                }
                if ($request->shipper_reference_3 != null) {
                    $shipper_reference->reference_3 = $request->shipper_reference_3;
                }
                if ($request->shipper_reference_4 != null) {
                    $shipper_reference->reference_4 = $request->shipper_reference_4;
                }
                if ($request->shipper_reference_5 != null) {
                    $shipper_reference->reference_5 = $request->shipper_reference_5;
                }
                $shipper_reference->save();
            }
            if ($service_type_id == 5) {
                $product_type_id = $request->input('product_type');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $request->input('item_quantity');

                if ($request->filled('insurance')) {
                    $price = str_replace(',', '', $request->input('item_price'));
                    $insurance = TRUE;
                } else {
                    $price = NULL;
                    $insurance = FALSE;
                }

                $type = 0;

                $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
                if ($service_type_id == 1 && $pieces_quantity > 1) {
                    $this->create_shipment_pieces($shipment_id, $pieces_quantity);
                }
            } else if ($service_type_id == 1) {
                if ($user_id == 10354 && $request->distribution_product_flag == 1) {
                    foreach ($request->input('distribution') as $distribution) {
                        $product_type_id = $distribution['product_type'];

                        if ($product_type_id == 0) {
                            $distribution_product_new = new DistributionProduct();
                            $distribution_product_new->name = $distribution['product_type_new'];
                            $distribution_product_new->save();

                            $product_type_id = $distribution_product_new->id;
                        }

                        $item_quantity = $distribution['item_quantity'];
                        $units_per_item = $distribution['units'];
                        $price = str_replace(',', '', $distribution['total_price']);

                        if (isset($distribution['insurance']) && !empty($distribution['insurance'])) {
                            $insurance = TRUE;
                        } else {
                            $insurance = FALSE;
                        }

                        $this->add_distribution_item($shipment_id, $product_type_id, $item_quantity, $units_per_item, $price, $insurance);
                    }
                } else {
                    $product_type_id = $request->input('product_type');

                    if ($request->filled('item_description')) {
                        $item_description = $request->input('item_description');
                    } else {
                        $item_description = NULL;
                    }

                    $item_quantity = $request->input('item_quantity');

                    if ($request->filled('insurance')) {
                        $price = str_replace(',', '', $request->input('item_price'));
                        $insurance = TRUE;
                    } else {
                        $price = NULL;
                        $insurance = FALSE;
                    }

                    $type = 0;

                    $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
                    if ($service_type_id == 1 && $pieces_quantity > 1) {
                        $this->create_shipment_pieces($shipment_id, $pieces_quantity);
                    }
                }
            } else if ($service_type_id == 2) {
                $product_type_id = $request->input('product_type');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $request->input('item_quantity');

                if ($request->filled('insurance')) {
                    $price = str_replace(',', '', $request->input('item_price'));
                    $insurance = TRUE;
                } else {
                    $price = NULL;
                    $insurance = FALSE;
                }

                $type = 0;

                $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

                $product_type_id = $request->input('replacement_product_type');

                if ($request->filled('replacement_item_description')) {
                    $item_description = $request->input('replacement_item_description');
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $request->input('replacement_item_quantity');
                $price = NULL;
                $insurance = NULL;
                $type = 1;

                $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
            } else if ($service_type_id == 3) {
                foreach ($request->input('try_and_buy') as $try_and_buy) {
                    $product_type_id = $try_and_buy['product_type'];

                    if (isset($try_and_buy['item_description']) && !empty($try_and_buy['item_description'])) {
                        $item_description = $try_and_buy['item_description'];
                    } else {
                        $item_description = NULL;
                    }

                    $item_quantity = $try_and_buy['item_quantity'];
                    $price = str_replace(',', '', $try_and_buy['item_price']);

                    if (isset($try_and_buy['insurance']) && !empty($try_and_buy['insurance'])) {
                        $insurance = TRUE;
                    } else {
                        $insurance = FALSE;
                    }

                    $type = 2;

                    $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
                }
            } elseif ($service_type_id == 6) {
                $product_type_id = $request->input('product_type');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                } else {
                    $item_description = NULL;
                }

                $item_quantity = $request->input('item_quantity');

                $type = 0;
                $price = 0;
                $insurance = FALSE;

                $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
            }

            NotificationsController::send(2, $shipment_id);
            $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();
            $now = Carbon::now()->format('H:i:s');
            $cutofftime = $settingsfortime->setting_value . ":00:00";
            if ($now > $cutofftime) {
                NotificationsController::send(152, $shipment_id);
                NotificationsController::send(153, $shipment_id);
            }

            if ($request->filled('book_and_print')) {
                $print = $shipment_id;
            } else {
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
                $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();
                $now = Carbon::now()->format('H:i:s');
                $cutofftime = $settingsfortime->setting_value . ":00:00";
                if ($now > $cutofftime) {
                    NotificationsController::send(152, $shipment_id);
                    NotificationsController::send(153, $shipment_id);
                }
            }
            if($request->hasFile('replacement_parcel_img')){
                $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $shipment_id);
                if($shipment_parcel_image->exists()){
                    $shipment_parcel_image = $shipment_parcel_image->first();
                    Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                }else{
                    $shipment_parcel_image = new ShipmentReplacementParcelImage();
                    $shipment_parcel_image->shipment_id = $shipment_id;
                }
                $time = Carbon::now()->toDateString();
                $picture_path = 'replacement_parcel/' . $shipment_id . '_' . $time . '.png';
                Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_parcel_img));
                $shipment_parcel_image->picture_path = $picture_path;
                $shipment_parcel_image->save();
            }

                return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
            }
        else {
            return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
        }
        }
    }

    public function corporate_invoice(Request $request)
    {
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

        $shipment = Shipment::where('id', $request->ids)->first();


        if ($request->has('admin') || session('user_id') == $shipment->user_id) {
            $table_start = '
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
                ';

            if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
            } else if ($shipment->booking_type_id == 2) {
                $table_start .= '
                            <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                    ';
            } else if ($shipment->booking_type_id == 3) {
                $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
                    ';
            } else {
                $table_start .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
            }

            $table_start .= '
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

            if ($shipment->pickup_address->pickup_brand_name != NULL) {
                // $company_name = $shipment->user->brand_name;
                $company_name = $shipment->pickup_address->pickup_brand_name;

            } else {
                if ($shipment->user->brand_name != NULL) {
                    $company_name = $shipment->user->brand_name;
                } else {
                    $company_name = $shipment->user->name;
                }
            }


            if ($shipment->booking_type_id != 4) {
                $table_start .= '
                            <td colspan="3" class="border twice-right">' . $company_name . '</td>
                    ';
            } else {
                $table_start .= '
                            <td colspan="3" class="border twice-right">' . $company_name . ' (' . $shipment->pickup_address->poc . ')</td>
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
                } else {
                    $table_start .= '
                            <td class="color secondary"><strong>Address</strong></td>
                            <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>
                        ';
                }
            } else {
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
                } else {
                    $table_start .= '
                            <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>
                        ';
                }
            } else {
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
            } else {
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
            } elseif ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 2) {
                $table_end .= '
                            <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount + $shipment->packaging_charges) . '</strong></td>
                    ';
            } else {
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

            if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
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
            } else if ($shipment->booking_type_id == 2) {
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
            } else if ($shipment->booking_type_id == 3) {
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

    public function corporate_excel_index()
    {

//        if(session('user_id') == 10354)
//        {
//            return back();
//        }

        $booking_types = BookingType::whereNotIn('id', [4, 6])->get();
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('business_category_id', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        if (in_array(session('user_id'), [5982, 3324, 10104, 14110, 16292])) {
            $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        } else {
            $cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        }
        $products = Product::all();
        $distribution_products = DistributionProduct::all();
        $user = User::find(session('user_id'));
        $delivery_types = DeliveryType::all();
        $charges_modes = ChargesModes::whereIn('id', [3])->get();
        $min_chargeable_weights = CorporateMinChargeableWeight::where('user_id', session('user_id'))->get();

        if (session('rate_type_id') != 3) {
            $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
        } else {
            $user_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
        }
        $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();


        if (in_array(4, $user_shipping_modes)) {
            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        } else {
            $shipping_mode_same_day_timings = NULL;
        }

        if (session('user_id') == 10354) {
            $payment_modes = PaymentMode::whereIn('id', [1])->get();
        } else {
            $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
            if ($ccd_booking->exists()) {
                $ccd_booking = $ccd_booking->first();
                $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
                if (!in_array(session('user_id'), $ccd_account_tags)) {
                    $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
                } else {
                    $payment_modes = PaymentMode::whereNotIn('id', [3])->get();
                }
            } else {
                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
            }
        }
        $omni_user = 0;
        $settings = GlobalSettings::where('type', 'omni_users');
        if ($settings->exists()) {
            $settings = $settings->first();
            if ($settings->text != NULL) {
                $omni_accounts = array_map('intval', explode(',', $settings->text));
                if (in_array(session('user_id'), $omni_accounts)) {
                    $omni_user = 1;
                }
            }
        }

        return view('client.shipment.book.corporate.excel')->with(['booking_types' => $booking_types, 'user' => $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'min_chargeable_weights' => $min_chargeable_weights, 'distribution_products' => $distribution_products, 'omni_user' => $omni_user]);
    }

    public function corporate_excel_distribution_index()
    {
        if (session('user_id') != 10354) {
            return back();
        }

        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('business_category_id', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
//        if(in_array(session('user_id'), [5982, 3324, 10104, 14110, 16292])) {
//            $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
//        }
//        else{
        $cities = City::where('id', '!=', 1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
//        }
        $products = DistributionProduct::all();
        $user = User::find(session('user_id'));
        $delivery_types = DeliveryType::all();
        $charges_modes = ChargesModes::whereIn('id', [3])->get();
        $min_chargeable_weights = CorporateMinChargeableWeight::where('user_id', session('user_id'))->get();

        if (session('rate_type_id') != 3) {
            $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
        } else {
            $user_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
        }
        $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();


//        if (in_array(4, $user_shipping_modes)) {
//            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
//        }
//        else {
//            $shipping_mode_same_day_timings = NULL;
//        }

//        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
//            if($ccd_booking->exists()){
//                $ccd_booking = $ccd_booking->first();
//                $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
//                if(!in_array(session('user_id'),$ccd_account_tags))
//                {$payment_modes = PaymentMode::whereNotIn('id', [2])->get();}
//                else
//                {$payment_modes = PaymentMode::all();}
//            }
//            else{
//                $payment_modes = PaymentMode::whereNotIn('id', [2])->get();
//            }

        $payment_modes = PaymentMode::whereIn('id', [1])->get();
        return view('client.shipment.book.corporate.distribution.excel')->with(['user' => $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'min_chargeable_weights' => $min_chargeable_weights]);
    }

    public function corporate_min_chargeable_weight(Request $request)
    {
        $min_chargeable_weight = CorporateMinChargeableWeight::where(['user_id' => session('user_id'), 'shipping_mode_id' => $request->shipping_mode, 'delivery_type_id' => $request->delivery_type])->first();
        if ($request->estimated_weight < $min_chargeable_weight['min_chargeable_weight']) {
            return ['status' => 1, 'min' => $min_chargeable_weight['min_chargeable_weight']];
        } else {
            return ['status' => 0];
        }

    }

    public function corporate_shipping_modes(Request $request)
    {
        $corporate_rate_type_id = User::find(session('user_id'))->corporate_rate_type_id;
        if ($corporate_rate_type_id != 3) {
            $shipper_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1);
            $user = User::where('id', session('user_id'))->first();
        } else {
            $shipper_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1);
            $user = User::where('id', session('user_id'))->first();
        }


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
                } else {
                    return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type, Pickup City and Consignee City'];
                }
            } else {
                return ['status' => 1, 'error' => 'No Shipping Modes Enabled for Selected Service Type and Consignee City'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipping Modes has been Enabled for you'];
        }
    }

    public function corporate_excel_store(Request $request)
    {

        $user_id = session('user_id');
        if (!$request->has('omni')) {
            $omni = 0;
        } else {
            $omni = $request->omni;
        }
        $rate_type_id = session('rate_type_id');

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            if ($value) {
                $value = $this->phone_number($value);

                if (preg_match('/^((\+92)|(92)|(0092))-{0,1}\d{3}-{0,1}\d{7}$|^\d{3}-{1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$|^\d{3}-\d{7}$|^\d{10}$/', $value)) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

        Validator::extend('origin_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            if ($value) {
                $result = self::check_origin($value, $shipping_mode_id, $user_id);
                if ($result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

        Validator::extend('destination_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            if ($value) {
                $result = self::check_destination($value, $shipping_mode_id, $user_id, 1);
                if ($result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

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
            'self_collection' => 'Self Collection',
            'order_id' => 'Order ID',
            'order_date' => 'Order Date',

            'item_product_type_id' => 'Item Product Type ID',
            'item_description' => 'Item Description',
            'item_quantity' => 'Item Quantity',
            'item_insurance' => 'Item Insurance',
            'item_price' => 'Product Value',

            'pieces_quantity' => 'Pieces',

            'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
            'replacement_item_description' => 'Replacement Item Description',
            'replacement_item_quantity' => 'Replacement Item Quantity',

            'item_product_type_id_1' => 'Try and Buy Item Product Type ID 1',
            'item_description_1' => 'Try and Buy Item Description 1',
            'item_quantity_1' => 'Try and Buy Item Quantity 1',
            'item_insurance_1' => 'Try and Buy Item Insurance 1',
            'item_price_1' => 'Try and Buy Product Value 1',
            'item_product_type_id_2' => 'Try and Buy Item Product Type ID 2',
            'item_description_2' => 'Try and Buy Item Description 2',
            'item_quantity_2' => 'Try and Buy Item Quantity 2',
            'item_insurance_2' => 'Try and Buy Item Insurance 2',
            'item_price_2' => 'Try and Buy Product Value 2',
            'item_product_type_id_3' => 'Try and Buy Item Product Type ID 3',
            'item_description_3' => 'Try and Buy Item Description 3',
            'item_quantity_3' => 'Try and Buy Item Quantity 3',
            'item_insurance_3' => 'Try and Buy Item Insurance 3',
            'item_price_3' => 'Try and Buy Product Value 3',
            'item_product_type_id_4' => 'Try and Buy Item Product Type ID 4',
            'item_description_4' => 'Try and Buy Item Description 4',
            'item_quantity_4' => 'Try and Buy Item Quantity 4',
            'item_insurance_4' => 'Try and Buy Item Insurance 4',
            'item_price_4' => 'Try and Buy Product Value 4',
            'item_product_type_id_5' => 'Try and Buy Item Product Type ID 5',
            'item_description_5' => 'Try and Buy Item Description 5',
            'item_quantity_5' => 'Try and Buy Item Quantity 5',
            'item_insurance_5' => 'Try and Buy Item Insurance 5',
            'item_price_5' => 'Try and Buy Product Value 5',

            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
            'shipping_mode_id' => 'Shipping Mode ID',
            'same_day_timing_id' => 'Same Day Timing ID',
            'try_and_buy_charges' => 'Try and Buy Charges',
            'amount' => 'Collection Amount',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID',

            'shipper_reference_number_1' => 'Shipper Reference Number 1',
            'shipper_reference_number_2' => 'Shipper Reference Number 2',
            'shipper_reference_number_3' => 'Shipper Reference Number 3',
            'shipper_reference_number_4' => 'Shipper Reference Number 4',
            'shipper_reference_number_5' => 'Shipper Reference Number 5',
            'open_shipment' => 'Open Shipment',
            'return_address_id' => 'Return Address Id',

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

            'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'phone_number' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'origin_check' => 'Origin city not allowed, please contact your sales person!',
            'destination_check' => 'Destination city not allowed, please contact your sales person!',
        ];

        $rules = [
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0), 'origin_check'],
            'delivery_type_id' => ['required_if:service_type_id,1,2,3', 'integer', 'digits_between:1,10', Rule::exists('delivery_types', 'id')],
            'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function ($query) {
                $query->whereIn('id', [3]);
            })],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 1)->where('status', 1), 'destination_check'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'phone_number'],
            'consignee_phone_number_2' => ['nullable', 'phone_number'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'self_collection' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],

            'order_date' => ['nullable', 'date_format:Y-m-d'],
            'open_shipment' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],

            'item_product_type_id' => ['required_if:service_type_id,1,2,5', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required_if:service_type_id,1,2,5', 'between:0,1000'],
            'item_quantity' => ['required_if:service_type_id,1,2,5', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance' => ['required_if:service_type_id,1,2,5', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price' => ['required_if:item_insurance,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],

            'item_product_type_id_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_1' => ['required_if:service_type_id,3', 'nullable', 'between:0,1000'],
            'item_quantity_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_1' => ['required_if:service_type_id,3', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_2' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_2' => ['required_with:item_product_type_id_2,', 'nullable', 'between:0,1000'],
            'item_quantity_2' => ['required_with:item_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_2' => ['required_with:item_product_type_id_2,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_2' => ['required_with:item_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_3' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_3' => ['required_with:item_product_type_id_3,', 'nullable', 'between:0,1000'],
            'item_quantity_3' => ['required_with:item_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_3' => ['required_with:item_product_type_id_3,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_3' => ['required_with:item_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_4' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_4' => ['required_with:item_product_type_id_4,', 'nullable', 'between:0,1000'],
            'item_quantity_4' => ['required_with:item_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_4' => ['required_with:item_product_type_id_4,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_4' => ['required_with:item_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_5' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_5' => ['required_with:item_product_type_id_5,', 'nullable', 'between:0,1000'],
            'item_quantity_5' => ['required_with:item_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_5' => ['required_with:item_product_type_id_5,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_5' => ['required_with:item_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,1000'],
            'replacement_item_quantity' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],

            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'amount' => ['required_if:service_type_id,1,2', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            'try_and_buy_charges' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            // 'payment_mode_id' => ['required_if:service_type_id,1,2', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
            //     $query->whereNotIn('id', [3]);
            // })],

            'return_address_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0)],

            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190'],
        ];
        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if ($ccd_booking->exists()) {
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if (in_array($user_id, $ccd_account_tags)) {
                $rules['payment_mode_id'] = ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [3]);
                })];
            } else {
                $rules['payment_mode_id'] = ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [2, 3]);
                })];
            }
        }
        if ($rate_type_id == 3) {
            $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_default_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        } else {
            $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        }
        if ($file = $request->file('shipments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        }

        if (isset($spreadsheet)) {
            $excel_type = $request->excel_type;
            $column_count = null;

            if ($excel_type == 0) {
            } else if ($excel_type == 1) {
                $column_count = 35;

                $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'delivery_type_id', 3 => 'information_display', 4 => 'consignee_city_name', 5 => 'consignee_name', 6 => 'consignee_address', 7 => 'consignee_phone_number_1', 8 => 'consignee_phone_number_2', 9 => 'consignee_email_address', 10 => 'self_collection', 11 => 'order_id', 12 => 'order_date', 13 => 'item_product_type_id', 14 => 'item_description', 15 => 'item_quantity', 16 => 'item_insurance', 17 => 'item_price', 18 => 'replacement_item_product_type_id', 19 => 'replacement_item_description', 20 => 'replacement_item_quantity', 21 => 'special_instructions', 22 => 'estimated_weight', 23 => 'shipping_mode_id', 24 => 'same_day_timing_id', 25 => 'amount', 26 => 'payment_mode_id', 27 => 'charges_mode_id', 28 => 'pieces_quantity', 29 => 'shipper_reference_number_1', 30 => 'shipper_reference_number_2', 31 => 'shipper_reference_number_3', 32 => 'shipper_reference_number_4', 33 => 'shipper_reference_number_5', 34 => 'open_shipment'];

                $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [4]);
                })];
                $service_type_check_id = null;
            } elseif ($excel_type == 2) {
                $column_count = 31;
                $fields = [0 => 'pickup_address_id', 1 => 'delivery_type_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'self_collection', 10 => 'order_id', 11 => 'order_date', 12 => 'item_product_type_id', 13 => 'item_description', 14 => 'item_quantity', 15 => 'item_insurance', 16 => 'item_price', 17 => 'special_instructions', 18 => 'estimated_weight', 19 => 'shipping_mode_id', 20 => 'same_day_timing_id', 21 => 'amount', 22 => 'payment_mode_id', 23 => 'charges_mode_id', 24 => 'pieces_quantity', 25 => 'shipper_reference_number_1', 26 => 'shipper_reference_number_2', 27 => 'shipper_reference_number_3', 28 => 'shipper_reference_number_4', 29 => 'shipper_reference_number_5', 30 => 'open_shipment'];
                $service_type_check_id = 1;
            } elseif ($excel_type == 3) {
                $column_count = 32;
                $fields = [0 => 'pickup_address_id', 1 => 'delivery_type_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'replacement_item_product_type_id', 17 => 'replacement_item_description', 18 => 'replacement_item_quantity', 19 => 'special_instructions', 20 => 'estimated_weight', 21 => 'shipping_mode_id', 22 => 'same_day_timing_id', 23 => 'amount', 24 => 'payment_mode_id', 25 => 'charges_mode_id', 26 => 'shipper_reference_number_1', 27 => 'shipper_reference_number_2', 28 => 'shipper_reference_number_3', 29 => 'shipper_reference_number_4', 30 => 'shipper_reference_number_5', 31 => 'open_shipment'];
                $service_type_check_id = 2;
            } elseif ($excel_type == 4) {
                $column_count = 49;
                $fields = [0 => 'pickup_address_id', 1 => 'delivery_type_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id_1', 12 => 'item_description_1', 13 => 'item_quantity_1', 14 => 'item_insurance_1', 15 => 'item_price_1', 16 => 'item_product_type_id_2', 17 => 'item_description_2', 18 => 'item_quantity_2', 19 => 'item_insurance_2', 20 => 'item_price_2', 21 => 'item_product_type_id_3', 22 => 'item_description_3', 23 => 'item_quantity_3', 24 => 'item_insurance_3', 25 => 'item_price_3', 26 => 'item_product_type_id_4', 27 => 'item_description_4', 28 => 'item_quantity_4', 29 => 'item_insurance_4', 30 => 'item_price_4', 31 => 'item_product_type_id_5', 32 => 'item_description_5', 33 => 'item_quantity_5', 34 => 'item_insurance_5', 35 => 'item_price_5', 36 => 'special_instructions', 37 => 'estimated_weight', 38 => 'shipping_mode_id', 39 => 'same_day_timing_id', 40 => 'try_and_buy_charges', 41 => 'payment_mode_id', 42 => 'charges_mode_id', 43 => 'shipper_reference_number_1', 44 => 'shipper_reference_number_2', 45 => 'shipper_reference_number_3', 46 => 'shipper_reference_number_4', 47 => 'shipper_reference_number_5', 48 => 'open_shipment'];
                $service_type_check_id = 3;

            } elseif ($excel_type == 5) {
                $column_count = 25;
                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'special_instructions', 16 => 'estimated_weight', 17 => 'shipping_mode_id', 18 => 'same_day_timing_id', 19 => 'shipper_reference_number_1', 20 => 'shipper_reference_number_2', 21 => 'shipper_reference_number_3', 22 => 'shipper_reference_number_4', 23 => 'shipper_reference_number_5', 24 => 'open_shipment'];
                $service_type_check_id = 5;
            } elseif ($excel_type == 6) {  //for omni
                $column_count = 32;
                $fields = [0 => 'pickup_address_id', 1 => 'delivery_type_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'self_collection', 10 => 'order_id', 11 => 'order_date', 12 => 'item_product_type_id', 13 => 'item_description', 14 => 'item_quantity', 15 => 'item_insurance', 16 => 'item_price', 17 => 'special_instructions', 18 => 'estimated_weight', 19 => 'shipping_mode_id', 20 => 'same_day_timing_id', 21 => 'amount', 22 => 'payment_mode_id', 23 => 'charges_mode_id', 24 => 'pieces_quantity', 25 => 'shipper_reference_number_1', 26 => 'shipper_reference_number_2', 27 => 'shipper_reference_number_3', 28 => 'shipper_reference_number_4', 29 => 'shipper_reference_number_5', 30 => 'open_shipment', 31 => 'return_address_id'];
                $service_type_check_id = 1;
                $omni = 1;
            } elseif ($excel_type == 7) {      //for omni overall
                $column_count = 36;

                $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'delivery_type_id', 3 => 'information_display', 4 => 'consignee_city_name', 5 => 'consignee_name', 6 => 'consignee_address', 7 => 'consignee_phone_number_1', 8 => 'consignee_phone_number_2', 9 => 'consignee_email_address', 10 => 'self_collection', 11 => 'order_id', 12 => 'order_date', 13 => 'item_product_type_id', 14 => 'item_description', 15 => 'item_quantity', 16 => 'item_insurance', 17 => 'item_price', 18 => 'replacement_item_product_type_id', 19 => 'replacement_item_description', 20 => 'replacement_item_quantity', 21 => 'special_instructions', 22 => 'estimated_weight', 23 => 'shipping_mode_id', 24 => 'same_day_timing_id', 25 => 'amount', 26 => 'payment_mode_id', 27 => 'charges_mode_id', 28 => 'pieces_quantity', 29 => 'shipper_reference_number_1', 30 => 'shipper_reference_number_2', 31 => 'shipper_reference_number_3', 32 => 'shipper_reference_number_4', 33 => 'shipper_reference_number_5', 34 => 'open_shipment', 35 => 'return_address_id'];

                $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [4]);
                })];
                $service_type_check_id = null;
                $omni = 1;
            } else {
                return redirect()->back()->with('error', 'Invalid Template Selected');
            }

            if (count($spreadsheet[0]) != $column_count) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }

            unset($spreadsheet[0]);
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
                    // dd($rows);
                }

                unset($spreadsheet);
            } else {
                $forms = $request->all();

                $forms = $forms['form'];
                foreach ($forms as $form) {
                    $row = array();
                    foreach ($form as $key => $value) {
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
                $service_type_check_id = $request->service_type_check_id;
            }

            $errors = array();
            $order_ids = array();
            $nsa_error = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();
            $blacklist_errors = array();
            $blacklist_found_categories = array();

            $check_bdmk = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                ->where('bdm.status',1)
                ->select(['booking_destination_mapping_keywords.keyword'])
                ->pluck('keyword')
                ->toArray();
            $bdmk_error = array();
            $bdmk_result = array();

            if (Session::has('prefix')) {
                $rules['order_id'] = ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })];
            } else {
                if (Session::has('restrict_order_id')) {
                    $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })];
                } else {
                    $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
                }
            }

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;


                if (!isset($row['charges_mode_id'])) {
                    $rows[$key]['charges_mode_id'] = 3;
                }
                if ($service_type_check_id != null) {
                    $rows[$key]['service_type_id'] = $service_type_check_id;
                    $row['service_type_id'] = $service_type_check_id;
                } else {
                    $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                        $query->whereNotIn('id', [4]);
                    })];
                }
                $shipping_mode_id = $row['shipping_mode_id'];
                if ($service_type_check_id != 5) {
                    if ($row['delivery_type_id'] == 2) {
                        $rules['delivery_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('corporate_delivery_type_statuses', 'delivery_type_id')->where(function ($query) use ($shipping_mode_id) {
                            $query->where('shipping_mode_id', $shipping_mode_id);
                        })];
                    }
                }


                if (!isset($row['pieces_quantity']) || $row['pieces_quantity'] == null) {
                    $row['pieces_quantity'] = 1;
                }
                if ($rate_type_id == 3) {
                    if (!isset($row['delivery_type_id']) || $row['delivery_type_id'] == null) {
                        $row['delivery_type_id'] = 1;
                    }
                }

                $rows[$key]['pieces_quantity'] = $row['pieces_quantity'];

                if (!isset($row['self_collection']) || $row['self_collection'] == null) {
                    $row['self_collection'] = 'no';
                }
                if (!isset($row['open_shipment']) || $row['open_shipment'] == null) {
                    $row['open_shipment'] = 'no';
                }
                $rows[$key]['open_shipment'] = $row['open_shipment'];

                $rows[$key]['self_collection'] = $row['self_collection'];

                if (!isset($row['return_address_id']) || $row['return_address_id'] == null || $row['service_type_id'] != 1) {
                    $row['return_address_id'] = NULL;
                }

                $rows[$key]['return_address_id'] = $row['return_address_id'];

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
                    if (Session::has('prefix')) {
                        $length = strlen(session('prefix'));
                        $check_order_id = str_split($row['order_id'], $length);
                        if (session('prefix') != $check_order_id[0]) {
                            $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                        } else {
                            if (!array_key_exists(1, $check_order_id)) {
                                $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                            }
                        }
                    }
                    if (!empty(trim($row['order_id']))) {
                        if (empty($order_ids)) {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        } else {
                            if (in_array($row['order_id'], $order_ids, true)) {
                                $errors[$row_id]['order_id'] = 'Same Order ID as of Row #' . $order_id_row[$row['order_id']];
                            } else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                            }
                        }
                    }

//                        dd($errors[$row_id]['amount']);
                    if ($service_type_check_id != 5) {
                        if ($row['delivery_type_id'] == 2) {
                            $allowed_delivery_type = CorporateDeliveryTypeStatus::where('user_id', $user_id);
                            if ($allowed_delivery_type->exists()) {
                                $allowed_delivery_type = $allowed_delivery_type->where('shipping_mode_id', $row['shipping_mode_id'])->where('delivery_type_id', $row['delivery_type_id']);
                                if (!$allowed_delivery_type->exists()) {
                                    $errors[$row_id]['delivery_type_id'] = 'Selected Delivery Type is disabled';
                                }
                            } else {
                                $errors[$row_id]['delivery_type_id'] = 'Selected Delivery Type is disabled';
                            }
                        }
                    }
                    if ($row['service_type_id'] != 5) {
                        $user_shipping_info = UserShippingInfo::find($row['pickup_address_id']);

                        if (!$user_shipping_info->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Pickup Address ID #' . $row['pickup_address_id'] . ' is disabled';
                        }

                        if ($row['service_type_id'] == 1 && $omni == 1) {
                            if ($row['return_address_id'] != NULL) {
                                $user_return_info = UserShippingInfo::find($row['return_address_id']);

                                if (!$user_return_info->status) {
                                    $errors[$row_id]['return_address_id'] = 'Return Address ID #' . $row['return_address_id'] . ' is disabled';
                                }
                            }

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
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                        }

                        if ($consignee_city->id == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292) {
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is not allowed for this shipper';
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

                        if ($user_shipping_info->city->id != $consignee_city->id && ($service_type_check_id == 1 || $service_type_check_id == 2)) {
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

                        if (!$request->excel_nsa) {
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
                                // $nsa_error[$row_id]['msg'] = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                            }
                        }

                        if(!$request->excel_bdmk) {
                            $bdmk_result[$row_id] = $this->check_bdmk($consignee_city->id, $row['consignee_address'], $check_bdmk);
                            if (isset($bdmk_result[$row_id]['invalid_cities'])) {
                                $bdmk_error[$row_id]['msg'] = $bdmk_result[$row_id]['invalid_cities'];
                            }
                        }

                        if (!CityDelivery::where('city_id', $consignee_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['consignee_city_name'] = 'Delivery is not allowed for City: ' . $consignee_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                        if (!$request->excel_blacklist) {
                            $consignee_phone_number_1 = substr_replace($row['consignee_phone_number_1'], '-', 4, 0);
                            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
                            if ($consignee_information->exists()) {
                                $consignee_information = $consignee_information->first();
                                $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information->id);
                                if ($manual_blacklist->exists()) {
                                    $manual_blacklist = $manual_blacklist->first();
                                    $blacklist_setting_id = $manual_blacklist->blacklist_setting_id;
                                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                    if ($blacklist_setting) {
                                        if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                            $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                            $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                        }
                                    }
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    } else {
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: 0, Delivered: 0, Undelivered: 0, Return Confirmed: 0';
                                    }
                                } else {
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_setting_id = $blacklist->blacklist_setting_id;
                                        $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                        if ($blacklist_setting) {
                                            if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                                $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                                $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                            }
                                        }
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    }
                                }
                            }
                        }
                    } else {
                        $pickup_consignee_city = City::where('name', $row['consignee_city_name'])->first();

                        if (!$pickup_consignee_city->status) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if ($pickup_consignee_city->id == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292) {
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $pickup_consignee_city->name . ' is not allowed for this shipper';
                        }

                        if (!$pickup_consignee_city->zone_id) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if ($user_id != 7762) {
                            if (!$pickup_consignee_city->pickup) {
                                $errors[$row_id]['consignee_city_name'] = 'Pickup is not allowed for City: ' . $pickup_consignee_city->name;
                            }
                        }

                        $pickup_address_id_for_delivery = $row['pickup_address_id'];
                        $pickup_address_for_delivery = UserShippingInfo::find($pickup_address_id_for_delivery);
                        $delivery_city = City::find($pickup_address_for_delivery->city_id);

                        if (!$delivery_city->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }
                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        $pickup_city_id = $pickup_consignee_city->id;

                        if ($delivery_city->id != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                            $errors[$row_id]['consignee_city_name'] = 'Same Day Delivery is not available for Different City Shipment';
                        }
                        if (!CityDelivery::where('city_id', $delivery_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery is not allowed for City: ' . $delivery_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                    }
                }
            }

            if (empty($errors)) {
                if (empty($nsa_error)) {
                    if (empty($bdmk_error)) {
                        if (TRUE || empty($blacklist_errors)) {
                            $tracking_numbers = array();

                            foreach ($rows as $key => $row) {
                                $row['user_id'] = $user_id;
                                $row['account_type_id'] = 2;
                                $row['nsas'] = $check;
                                $row['nsa'] = $request->excel_nsa;
                                if (Session::has('prefix')) {
                                    $row['prefix'] = session('prefix');
                                } else {
                                    $row['prefix'] = NULL;
                                }
                                if (session('user_type') == 2) {
                                    $row['substitute_user_id'] = Auth::id();
                                } else {
                                    $row['substitute_user_id'] = null;
                                }
                                $row['business_category_id'] = 1;
                                if ($row['service_type_id'] == 3 && $row['payment_mode_id'] == 4) {
                                    $row['payment_mode_id'] = 1;
                                }
                                if (!isset($row['payment_mode_id'])) {
                                    $rows[$key]['payment_mode_id'] = 1;
                                    $row['payment_mode_id'] = 1;
                                }
                                if ($row['payment_mode_id'] == 4) {
                                    $row['amount'] = 0;
                                }
                                if ($user_id != 3324) {
                                    dispatch(new ProcessShipmentBookingDB($row));
                                } else {
                                    dispatch(new ProcessShipmentBookingDBPriority($row));
                                }
                            }

                            return redirect()->back()->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                        } else {
                            return view('client.shipment.book.corporate.blacklist')->with(['data' => $rows, 'blacklist_errors' => $blacklist_errors, 'blacklist_found_categories' => $blacklist_found_categories, 'service_type_check_id' => $service_type_check_id]);
                        }
                    }else{
                        return view('client.shipment.book.corporate.bdmk')->with(['data' => $rows, 'bdmk_error' => $bdmk_error, 'service_type_check_id' => $service_type_check_id, 'omni' => $omni]);
                    }
                } else {
                    return view('client.shipment.book.corporate.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error, 'service_type_check_id' => $service_type_check_id, 'omni' => $omni]);
                }
            } else {
                if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
                    $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                } else {
                    $cities = City::where('status', 1)->where('id', '!=', 1244)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                }
                $booking_types = BookingType::whereNotIn('id', [3, 4])->pluck('booking_type', 'id');
                $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                    $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
                })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                $products = Product::pluck('product_name', 'id');
                if ($rate_type_id != 3) {
                    $delivery_types = DeliveryType::pluck('delivery_type', 'id');
                } else {
                    $delivery_types = DeliveryType::where('id', 1)->pluck('delivery_type', 'id');
                }
                $charges_modes = ChargesModes::whereIn('id', [3])->pluck('charges_mode', 'id');

                if (session('rate_type_id') != 3) {

                    $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
                } else {
                    $user_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
                }

                $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->pluck('mode', 'id');

                if (in_array(4, $user_shipping_modes)) {
                    $shipping_mode_same_day_timings = ShippingModeSameDayTiming::pluck('timing', 'id');
                } else {
                    $shipping_mode_same_day_timings = NULL;
                }

                $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
                if ($ccd_booking->exists()) {
                    $ccd_booking = $ccd_booking->first();
                    $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
                    if (!in_array(session('user_id'), $ccd_account_tags)) {
                        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->pluck('mode', 'id');
                    } else {
                        $payment_modes = PaymentMode::whereNotIn('id', [3])->pluck('mode', 'id');;
                    }
                } else {
                    $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
                }
                $city_name = array();
                foreach ($cities as $city) {
                    $city_name[$city->name] = $city->name;
                }

                return view('client.shipment.book.corporate.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'user_shipping_modes' => $user_shipping_modes, 'service_type_check_id' => $service_type_check_id, 'omni' => $omni]);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function corporate_excel_distribution_store(Request $request)
    {

        $user_id = session('user_id');
        $rate_type_id = session('rate_type_id');

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            if ($value) {
                $value = $this->phone_number($value);

                if (preg_match('/^((\+92)?(0092)?(92)?(0)?)(3)([0-9]{9})$/', $value)) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

        Validator::extend('origin_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            if ($value) {
                $result = self::check_origin($value, $shipping_mode_id, $user_id);
                if ($result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });

        Validator::extend('destination_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            if ($value) {
                $result = self::check_destination($value, $shipping_mode_id, $user_id, 1);
                if ($result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        });
        $names = [
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
            'order_date' => 'Order Date',

            'distribution_product_type_id_1' => 'Distribution Item Product Type ID 1',
            'distribution_item_per_sku_1' => 'Distribution Item Per SKU 1',
            'distribution_unit_per_item_1' => 'Distribution Unit Per Item 1',
            'distribution_item_insurance_1' => 'Distribution Item Insurance 1',
            'distribution_item_price_1' => 'Distribution Product Value 1',

            'distribution_product_type_id_2' => 'Distribution Item Product Type ID 2',
            'distribution_item_per_sku_2' => 'Distribution Item Per SKU 2',
            'distribution_unit_per_item_2' => 'Distribution Unit Per Item 2',
            'distribution_item_insurance_2' => 'Distribution Item Insurance 2',
            'distribution_item_price_2' => 'Distribution Product Value 2',

            'distribution_product_type_id_3' => 'Distribution Item Product Type ID 3',
            'distribution_item_per_sku_3' => 'Distribution Item Per SKU 3',
            'distribution_unit_per_item_3' => 'Distribution Unit Per Item 3',
            'distribution_item_insurance_3' => 'Distribution Item Insurance 3',
            'distribution_item_price_3' => 'Distribution Product Value 3',

            'distribution_product_type_id_4' => 'Distribution Item Product Type ID 4',
            'distribution_item_per_sku_4' => 'Distribution Item Per SKU 4',
            'distribution_unit_per_item_4' => 'Distribution Unit Per Item 4',
            'distribution_item_insurance_4' => 'Distribution Item Insurance 4',
            'distribution_item_price_4' => 'Distribution Product Value 4',

            'distribution_product_type_id_5' => 'Distribution Item Product Type ID 5',
            'distribution_item_per_sku_5' => 'Distribution Item Per SKU 5',
            'distribution_unit_per_item_5' => 'Distribution Unit Per Item 5',
            'distribution_item_insurance_5' => 'Distribution Item Insurance 5',
            'distribution_item_price_5' => 'Distribution Product Value 5',

            'distribution_product_type_id_6' => 'Distribution Item Product Type ID 6',
            'distribution_item_per_sku_6' => 'Distribution Item Per SKU 6',
            'distribution_unit_per_item_6' => 'Distribution Unit Per Item 6',
            'distribution_item_insurance_6' => 'Distribution Item Insurance 6',
            'distribution_item_price_6' => 'Distribution Product Value 6',

            'distribution_product_type_id_7' => 'Distribution Item Product Type ID 7',
            'distribution_item_per_sku_7' => 'Distribution Item Per SKU 7',
            'distribution_unit_per_item_7' => 'Distribution Unit Per Item 7',
            'distribution_item_insurance_7' => 'Distribution Item Insurance 7',
            'distribution_item_price_7' => 'Distribution Product Value 7',

            'distribution_product_type_id_8' => 'Distribution Item Product Type ID 8',
            'distribution_item_per_sku_8' => 'Distribution Item Per SKU 8',
            'distribution_unit_per_item_8' => 'Distribution Unit Per Item 8',
            'distribution_item_insurance_8' => 'Distribution Item Insurance 8',
            'distribution_item_price_8' => 'Distribution Product Value 8',

            'distribution_product_type_id_9' => 'Distribution Item Product Type ID 9',
            'distribution_item_per_sku_9' => 'Distribution Item Per SKU 9',
            'distribution_unit_per_item_9' => 'Distribution Unit Per Item 9',
            'distribution_item_insurance_9' => 'Distribution Item Insurance 9',
            'distribution_item_price_9' => 'Distribution Product Value 9',

            'distribution_product_type_id_10' => 'Distribution Item Product Type ID 10',
            'distribution_item_per_sku_10' => 'Distribution Item Per SKU 10',
            'distribution_unit_per_item_10' => 'Distribution Unit Per Item 10',
            'distribution_item_insurance_10' => 'Distribution Item Insurance 10',
            'distribution_item_price_10' => 'Distribution Product Value 10',


//            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
            'shipping_mode_id' => 'Shipping Mode ID',
//            'same_day_timing_id' => 'Same Day Timing ID',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID',

//            'shipper_reference_number_1' => 'Shipper Reference Number 1',
//            'shipper_reference_number_2' => 'Shipper Reference Number 2',
//            'shipper_reference_number_3' => 'Shipper Reference Number 3',
//            'shipper_reference_number_4' => 'Shipper Reference Number 4',
//            'shipper_reference_number_5' => 'Shipper Reference Number 5',
//            'open_shipment' => 'Open Shipment'

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

            'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'phone_number' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
            'origin_check' => 'Origin city not allowed, please contact your sales person!',
            'destination_check' => 'Destination city not allowed, please contact your sales person!',
        ];

        $rules = [
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0), 'origin_check'],
            'delivery_type_id' => [Rule::requiredIf($rate_type_id != 3), 'integer', 'digits_between:1,10', Rule::exists('delivery_types', 'id')],
            'charges_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function ($query) {
                $query->whereIn('id', [3]);
            })],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 1), 'destination_check'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'phone_number'],
            'consignee_phone_number_2' => ['nullable', 'phone_number'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],
//            'open_shipment' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],


            'distribution_product_type_id_1' => ['required', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_1' => ['required', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_1' => ['required', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_1' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_1' => ['required', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_2' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_2' => ['required_with:distribution_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_2' => ['required_with:distribution_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_2' => ['required_with:distribution_product_type_id_2,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_2' => ['required_with:distribution_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_3' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_3' => ['required_with:distribution_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_3' => ['required_with:distribution_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_3' => ['required_with:distribution_product_type_id_3,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_3' => ['required_with:distribution_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_4' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_4' => ['required_with:distribution_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_4' => ['required_with:distribution_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_4' => ['required_with:distribution_product_type_id_4,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_4' => ['required_with:distribution_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_5' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_5' => ['required_with:distribution_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_5' => ['required_with:distribution_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_5' => ['required_with:distribution_product_type_id_5,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_5' => ['required_with:distribution_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_6' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_6' => ['required_with:distribution_product_type_id_6,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_6' => ['required_with:distribution_product_type_id_6,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_6' => ['required_with:distribution_product_type_id_6,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_6' => ['required_with:distribution_product_type_id_6,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_7' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_7' => ['required_with:distribution_product_type_id_7,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_7' => ['required_with:distribution_product_type_id_7,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_7' => ['required_with:distribution_product_type_id_7,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_7' => ['required_with:distribution_product_type_id_7,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_8' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_8' => ['required_with:distribution_product_type_id_8,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_8' => ['required_with:distribution_product_type_id_8,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_8' => ['required_with:distribution_product_type_id_8,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_8' => ['required_with:distribution_product_type_id_8,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_9' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_9' => ['required_with:distribution_product_type_id_9,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_9' => ['required_with:distribution_product_type_id_9,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_9' => ['required_with:distribution_product_type_id_9,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_9' => ['required_with:distribution_product_type_id_9,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'distribution_product_type_id_10' => ['nullable', 'integer', 'digits_between:1,10', 'exists:distribution_products,id'],
            'distribution_item_per_sku_10' => ['required_with:distribution_product_type_id_10,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_unit_per_item_10' => ['required_with:distribution_product_type_id_10,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'distribution_item_insurance_10' => ['required_with:distribution_product_type_id_10,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'distribution_item_price_10' => ['required_with:distribution_product_type_id_10,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],


//            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],

//            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                $query->whereIn('id', [1]);
            })],

            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190'],
        ];
        if ($rate_type_id == 3) {
            $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_default_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        } else {
            $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        }
        if ($file = $request->file('shipments')) {

            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet();
            $highestColumn = $spreadsheet->getHighestDataColumn();
            $highestRow = $spreadsheet->getHighestDataRow();
            $spreadsheet = $spreadsheet->rangeToArray('A1:' . $highestColumn . $highestRow);
        }

        if (isset($spreadsheet)) {
            $excel_type = $request->excel_type;
            $column_count = null;

            if ($excel_type == 0) {
                $column_count = 65;

                $fields = [0 => 'pickup_address_id', 1 => 'delivery_type_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'order_id', 10 => 'order_date', 11 => 'distribution_product_type_id_1', 12 => 'distribution_item_per_sku_1', 13 => 'distribution_unit_per_item_1', 14 => 'distribution_item_insurance_1', 15 => 'distribution_item_price_1', 16 => 'distribution_product_type_id_2', 17 => 'distribution_item_per_sku_2', 18 => 'distribution_unit_per_item_2', 19 => 'distribution_item_insurance_2', 20 => 'distribution_item_price_2', 21 => 'distribution_product_type_id_3', 22 => 'distribution_item_per_sku_3', 23 => 'distribution_unit_per_item_3', 24 => 'distribution_item_insurance_3', 25 => 'distribution_item_price_3', 26 => 'distribution_product_type_id_4', 27 => 'distribution_item_per_sku_4', 28 => 'distribution_unit_per_item_4', 29 => 'distribution_item_insurance_4', 30 => 'distribution_item_price_4', 31 => 'distribution_product_type_id_5', 32 => 'distribution_item_per_sku_5', 33 => 'distribution_unit_per_item_5', 34 => 'distribution_item_insurance_5', 35 => 'distribution_item_price_5', 36 => 'distribution_product_type_id_6', 37 => 'distribution_item_per_sku_6', 38 => 'distribution_unit_per_item_6', 39 => 'distribution_item_insurance_6', 40 => 'distribution_item_price_6', 41 => 'distribution_product_type_id_7', 42 => 'distribution_item_per_sku_7', 43 => 'distribution_unit_per_item_7', 44 => 'distribution_item_insurance_7', 45 => 'distribution_item_price_7', 46 => 'distribution_product_type_id_8', 47 => 'distribution_item_per_sku_8', 48 => 'distribution_unit_per_item_8', 49 => 'distribution_item_insurance_8', 50 => 'distribution_item_price_8', 51 => 'distribution_product_type_id_9', 52 => 'distribution_item_per_sku_9', 53 => 'distribution_unit_per_item_9', 54 => 'distribution_item_insurance_9', 55 => 'distribution_item_price_9', 56 => 'distribution_product_type_id_10', 57 => 'distribution_item_per_sku_10', 58 => 'distribution_unit_per_item_10', 59 => 'distribution_item_insurance_10', 60 => 'distribution_item_price_10', 61 => 'estimated_weight', 62 => 'shipping_mode_id', 63 => 'payment_mode_id', 64 => 'charges_mode_id'];
                $service_type_check_id = 1;
            } else {
                return redirect()->back()->with('error', 'Invalid Template Selected');
            }

            if (count($spreadsheet[0]) != $column_count) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }

            unset($spreadsheet[0]);
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
            } else {
                $forms = $request->all();

                $forms = $forms['form'];
                foreach ($forms as $form) {
                    $row = array();
                    foreach ($form as $key => $value) {
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
                $service_type_check_id = 1;
            }

            $errors = array();
            $order_ids = array();
            $nsa_error = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();
            $blacklist_errors = array();
            $blacklist_found_categories = array();


            if (Session::has('prefix')) {
                $rules['order_id'] = ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })];
            } else {
                if (Session::has('restrict_order_id')) {
                    $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })];
                } else {
                    $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
                }
            }

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if (!isset($row['charges_mode_id'])) {
                    $rows[$key]['charges_mode_id'] = 3;
                }
                if ($service_type_check_id != null) {
                    $rows[$key]['service_type_id'] = $service_type_check_id;
                    $row['service_type_id'] = $service_type_check_id;
                } else {
                    $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                        $query->whereIn('id', [1]);
                    })];
                }

                $shipping_mode_id = $row['shipping_mode_id'];

                if ($service_type_check_id != 5) {
                    if ($row['delivery_type_id'] == 2) {
                        $rules['delivery_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('corporate_delivery_type_statuses', 'delivery_type_id')->where(function ($query) use ($shipping_mode_id) {
                            $query->where('shipping_mode_id', $shipping_mode_id);
                        })];
                    }
                }


                if (!isset($row['pieces_quantity']) || $row['pieces_quantity'] == null) {
                    $row['pieces_quantity'] = 1;
                }
                if ($rate_type_id == 3) {
                    if (!isset($row['delivery_type_id']) || $row['delivery_type_id'] == null) {
                        $row['delivery_type_id'] = 1;
                    }
                }

                $rows[$key]['pieces_quantity'] = $row['pieces_quantity'];

                if (!isset($row['self_collection']) || $row['self_collection'] == null) {
                    $row['self_collection'] = 'no';
                }
                if (!isset($row['open_shipment']) || $row['open_shipment'] == null) {
                    $row['open_shipment'] = 'no';
                }
                $rows[$key]['open_shipment'] = $row['open_shipment'];

                $rows[$key]['self_collection'] = $row['self_collection'];

                if (!isset($row['return_address_id']) || $row['return_address_id'] == null) {
                    $row['return_address_id'] = NULL;
                }

                $rows[$key]['return_address_id'] = $row['return_address_id'];

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
                    if (Session::has('prefix')) {
                        $length = strlen(session('prefix'));
                        $check_order_id = str_split($row['order_id'], $length);
                        if (session('prefix') != $check_order_id[0]) {
                            $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                        } else {
                            if (!array_key_exists(1, $check_order_id)) {
                                $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                            }
                        }
                    }
                    if (!empty(trim($row['order_id']))) {
                        if (empty($order_ids)) {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        } else {
                            if (in_array($row['order_id'], $order_ids, true)) {
                                $errors[$row_id]['order_id'] = 'Same Order ID as of Row #' . $order_id_row[$row['order_id']];
                            } else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                            }
                        }
                    }

                    if ($service_type_check_id != 5) {
                        if ($row['delivery_type_id'] == 2) {
                            $allowed_delivery_type = CorporateDeliveryTypeStatus::where('user_id', $user_id);
                            if ($allowed_delivery_type->exists()) {
                                $allowed_delivery_type = $allowed_delivery_type->where('shipping_mode_id', $row['shipping_mode_id'])->where('delivery_type_id', $row['delivery_type_id']);
                                if (!$allowed_delivery_type->exists()) {
                                    $errors[$row_id]['delivery_type_id'] = 'Selected Delivery Type is disabled';
                                }
                            } else {
                                $errors[$row_id]['delivery_type_id'] = 'Selected Delivery Type is disabled';
                            }
                        }
                    }
                    if ($row['service_type_id'] != 5) {
                        $user_shipping_info = UserShippingInfo::find($row['pickup_address_id']);

                        if (!$user_shipping_info->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Pickup Address ID #' . $row['pickup_address_id'] . ' is disabled';
                        }

                        if ($row['service_type_id'] == 1 || $row['service_type_id'] == 2) {
                            if ($row['return_address_id'] != NULL) {
                                $user_return_info = UserShippingInfo::find($row['return_address_id']);

                                if (!$user_return_info->status) {
                                    $errors[$row_id]['return_address_id'] = 'Return Address ID #' . $row['return_address_id'] . ' is disabled';
                                }
                            }
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
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                        }

                        if ($consignee_city->id == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292) {
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is not allowed for this shipper';
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

                        if ($user_shipping_info->city->id != $consignee_city->id && ($service_type_check_id == 1 || $service_type_check_id == 2)) {
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

                        if (!$request->excel_nsa) {
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
                        if (!$request->excel_blacklist) {
                            $consignee_phone_number_1 = substr_replace($row['consignee_phone_number_1'], '-', 4, 0);
                            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
                            if ($consignee_information->exists()) {
                                $consignee_information = $consignee_information->first();
                                $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information->id);
                                if ($manual_blacklist->exists()) {
                                    $manual_blacklist = $manual_blacklist->first();
                                    $blacklist_setting_id = $manual_blacklist->blacklist_setting_id;
                                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                    if ($blacklist_setting) {
                                        if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                            $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                            $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                        }
                                    }
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    } else {
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: 0, Delivered: 0, Undelivered: 0, Return Confirmed: 0';
                                    }
                                } else {
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_setting_id = $blacklist->blacklist_setting_id;
                                        $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                        if ($blacklist_setting) {
                                            if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                                $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                                $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                            }
                                        }
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    }
                                }
                            }
                        }
                    } else {
                        $pickup_consignee_city = City::where('name', $row['consignee_city_name'])->first();

                        if (!$pickup_consignee_city->status) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if ($pickup_consignee_city->id == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292) {
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $pickup_consignee_city->name . ' is not allowed for this shipper';
                        }

                        if (!$pickup_consignee_city->zone_id) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if ($user_id != 7762) {
                            if (!$pickup_consignee_city->pickup) {
                                $errors[$row_id]['consignee_city_name'] = 'Pickup is not allowed for City: ' . $pickup_consignee_city->name;
                            }
                        }

                        $pickup_address_id_for_delivery = $row['pickup_address_id'];
                        $pickup_address_for_delivery = UserShippingInfo::find($pickup_address_id_for_delivery);
                        $delivery_city = City::find($pickup_address_for_delivery->city_id);

                        if (!$delivery_city->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }
                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        $pickup_city_id = $pickup_consignee_city->id;

                        if ($delivery_city->id != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                            $errors[$row_id]['consignee_city_name'] = 'Same Day Delivery is not available for Different City Shipment';
                        }
                        if (!CityDelivery::where('city_id', $delivery_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery is not allowed for City: ' . $delivery_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                    }
                }
            }

            if (empty($errors)) {
                if (empty($nsa_error)) {
                    if (TRUE || empty($blacklist_errors)) {
                        $tracking_numbers = array();

                        foreach ($rows as $key => $row) {
                            $row['user_id'] = $user_id;
                            $row['account_type_id'] = 2;
                            $row['nsas'] = $check;
                            $row['nsa'] = $request->excel_nsa;
                            if (Session::has('prefix')) {
                                $row['prefix'] = session('prefix');
                            } else {
                                $row['prefix'] = NULL;
                            }
                            if (session('user_type') == 2) {
                                $row['substitute_user_id'] = Auth::id();
                            } else {
                                $row['substitute_user_id'] = null;
                            }
                            $row['business_category_id'] = 1;
                            dispatch(new ProcessShipmentBookingDistributionDB($row));
                        }

                        return redirect()->route('cod.shipment.book.corporate_excel_distribution')->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                    } else {
                        return view('client.shipment.book.corporate.blacklist')->with(['data' => $rows, 'blacklist_errors' => $blacklist_errors, 'blacklist_found_categories' => $blacklist_found_categories, 'service_type_check_id' => $service_type_check_id]);
                    }

                } else {
                    return view('client.shipment.book.corporate.distribution.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error, 'service_type_check_id' => $service_type_check_id]);
                }
            } else {
                if (in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
                    $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                } else {
                    $cities = City::where('status', 1)->where('id', '!=', 1244)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                }
                $booking_types = BookingType::whereNotIn('id', [3, 4])->pluck('booking_type', 'id');
                $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                    $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
                })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                $products = DistributionProduct::pluck('name', 'id');
                if ($rate_type_id != 3) {
                    $delivery_types = DeliveryType::pluck('delivery_type', 'id');
                } else {
                    $delivery_types = DeliveryType::where('id', 1)->pluck('delivery_type', 'id');
                }
                $charges_modes = ChargesModes::whereIn('id', [3])->pluck('charges_mode', 'id');

                if (session('rate_type_id') != 3) {

                    $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
                } else {
                    $user_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
                }

                $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->pluck('mode', 'id');

                if (in_array(4, $user_shipping_modes)) {
                    $shipping_mode_same_day_timings = ShippingModeSameDayTiming::pluck('timing', 'id');
                } else {
                    $shipping_mode_same_day_timings = NULL;
                }

//                $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
//                    if($ccd_booking->exists()){
//                        $ccd_booking = $ccd_booking->first();
//                        $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
//                        if(!in_array(session('user_id'),$ccd_account_tags))
//                        {$payment_modes = PaymentMode::whereNotIn('id', [2])->pluck('mode', 'id');}
//                        else
//                        {$payment_modes = PaymentMode::first()->pluck('mode', 'id');;}
//                    }
//                    else{
//                        $payment_modes = PaymentMode::whereNotIn('id', [2])->get();
//                    }
                $payment_modes = PaymentMode::whereIn('id', [1])->pluck('mode', 'id');
                $city_name = array();
                foreach ($cities as $city) {
                    $city_name[$city->name] = $city->name;
                }

                return view('client.shipment.book.corporate.distribution.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'user_shipping_modes' => $user_shipping_modes, 'service_type_check_id' => $service_type_check_id]);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public static function add_consignee_info($shipper_id, $city_id, $name, $address, $phone1, $phone2 = NULL, $email = NULL)
    {

        $consignee_info = ConsigneeInfo::where('phone_number_1', $phone1)->where('shipper_id', $shipper_id);
        if ($consignee_info->exists()) {

            $consignee_info = $consignee_info->first();

            $consignee_info->city_id = $city_id;
            $consignee_info->name = $name;
            $consignee_info->address = $address;
            $consignee_info->phone_number_1 = $phone1;
            $consignee_info->phone_number_2 = $phone2;
            $consignee_info->email = $email;
            $consignee_info->save();

        } else {

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

    public function get_consignee_infos(Request $request)
    {
        $data = array();
        $consignee_info = ConsigneeInfo::where('phone_number_1', 'LIKE', "%" . $request->q . "%")->orWhere('phone_number_2', 'LIKE', "%" . $request->q . "%");
        if ($consignee_info->exists()) {
            $consignee_info = $consignee_info->limit(10)->get();
            foreach ($consignee_info as $item) {
                $data[] = ['id' => $item->id, 'full_name' => $item->phone_number_1 . ' / ' . $item->name, 'text' => $item->name];
            }
            return response()->json(['status' => 1, 'data' => $data, 'total_count' => count($data)]);
        }

    }

    public function get_consignee_info(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $consignee_info = ConsigneeInfo::find($id);
            if ($consignee_info) {
                return response()->json(['status' => 1, 'details' => $consignee_info]);
            } else {
                return response()->json(['status' => 0, 'error' => 'Consignee Information not found!']);
            }
        }
    }

    public static function air_waybill_sticker_pdf($user_type, $user_id, $shipment_ids)
    {
        $air_waybills = array();

        foreach ($shipment_ids as $shipment_id) {
            $air_waybills[] = self::air_waybill($user_type, $user_id, [$shipment_id], FALSE, 'pdf');
        }

        $options = ['margin-top' => '0.125in', 'margin-bottom' => '0.125in', 'margin-left' => '0.125in', 'margin-right' => '0.125in', 'page-width' => '6in', 'page-height' => '4in'];

        $pdf = SnappyPDF::snappy()->getOutputFromHtml($air_waybills, $options);

        $filename = 'air_waybills.pdf';

        return new Response($pdf, 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ));
    }

    public static function air_waybill_sticker_barcode($user_type, $user_id, $shipment_ids)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '<!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                <title>Air Waybill Sticker Barcode</title>
                <style type="text/css">
                  * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                  }
                  body {
                    background: none !important;
                    color: #000 !important;
                  }
                  .pwrapper {margin: auto; page-break-inside: avoid;}
                  .logo {margin-bottom:5px;}
                  .logo img {margin-bottom:2.5px; filter: brightness(0);}
                  .logo span {font-size: 8px;}
                  .barcode span {font-size: 12px;}
                  @media print {
                   html, body {min-width:auto!important; min-height:auto!important;}
                   @page {margin:0 !important; size: landscape;}
                   .pwrapper {margin: auto; page-break-inside: avoid;}
                   .logo span {font-size: 8px;}
                   .barcode span {font-size: 12px;}
                  }
                </style>
              </head>
              <body>
        ';

        $barcodes = '';

        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            $barcodes .= '
                <div class="text-center pwrapper p-1">
                    <div class="logo">
                        <img src="' . asset('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">
                        <div class="row no-gutters locations">
                            <span class="col-6 text-left">' . $shipment->pickup_address->city->name . '</span>
                            <span class="col-6 text-right">' . $shipment->consignee_city->hub_city->name . ' (' . $shipment->consignee_city->name . ')' . '</span>
                        </div>
                        <span class="d-block">' . implode(' ', str_split('92' . str_replace('-', '', ltrim($shipment->consignee_phone_number_1, '0')))) . '</span>
                    </div>
                    <div class="barcode">
                        <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 70)) . '" class="img-fluid mx-auto d-block h-auto">
                        <span class="d-block"><strong>* ' . $shipment->tracking_number . ' *</strong></span>
                    </div>
                </div>
            ';
        }

        $html .= $barcodes;

        $html .= '
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

    public function shipments_list_index(Request $request)
    {
        return view('client.shipment.list');
    }

    public function shipments_list_store(Request $request)
    {
        $print_by = $request->print_by;
        $check = false;
        $error = '';
        if($print_by == 2){
            if(isset($request->consignee_phone_number) && !empty($request->consignee_phone_number)){
                $check = true;
            }
            else{
                $error = 'No Consignee Phone Number entered';
            }
        }
        elseif ($print_by == 1){
            if(isset($request->order_id) && !empty($request->order_id)){
                $check = true;
            }
            else{
                $error = 'No Order ID entered';
            }
        }
        if ($check) {
            if($print_by == 1){
                $order_id = $request->order_id;
                $shipment = Shipment::where('user_id', session('user_id'))->where('shipper_status_id', '!=', 17)->where('order_id', $order_id);
            }
            else{
                $consignee_phone_number = $request->consignee_phone_number;

                if (substr($consignee_phone_number, 0, 2) == '92') {
                    $consignee_phone_number = substr($consignee_phone_number, 2);
                }

                $consignee_phone_number = '0' . substr_replace($consignee_phone_number, '-', 3, 0);

                $shipment = Shipment::where('user_id', session('user_id'))->where('shipper_status_id', '!=', 17)->where('consignee_phone_number_1', $consignee_phone_number);
            }

            if ($shipment->exists()) {
                $shipment = $shipment->latest('id')->first();

                $shipment_array = array();

                $shipment_array['id'] = $shipment->id;
                $shipment_array['tracking_number'] = $shipment->tracking_number;
                $shipment_array['consignee_name'] = $shipment->consignee_name;
                $shipment_array['consignee_phone_number'] = $request->consignee_phone_number;
                $shipment_array['order_id'] = $request->order_id;

                return ['status' => 0, 'success' => 'Shipment(s) found', 'shipment' => $shipment_array];
            } else {
                return ['status' => 2, 'error' => 'No Shipment found for ' . $request->consignee_phone_number];
            }
        } else {
            return ['status' => 1, 'error' => $error];
        }
    }

    public function shipments_verify_index(Request $request)
    {
        return view('client.shipment.verify');
    }

    public function shipments_verify_store(Request $request)
    {
        if (isset($request->tracking_number) && !empty($request->tracking_number) && isset($request->consignee_phone_number) && !empty($request->consignee_phone_number)) {
            $shipment = Shipment::where('user_id', session('user_id'))->where('tracking_number', $request->tracking_number);

            if ($shipment->exists()) {
                $shipment = $shipment->latest('id')->first();

                $consignee_phone_number = '92' . str_replace('-', '', ltrim($shipment->consignee_phone_number_1, '0'));

                if ($consignee_phone_number == $request->consignee_phone_number) {
                    $shipment_array = array();

                    $shipment_array['id'] = $shipment->id;
                    $shipment_array['tracking_number'] = $shipment->tracking_number;
                    $shipment_array['consignee_name'] = $shipment->consignee_name;
                    $shipment_array['consignee_phone_number'] = $consignee_phone_number;

                    return ['status' => 0, 'success' => 'Tracking Number and Consignee Phone Number match', 'shipment' => $shipment_array];
                } else {
                    return ['status' => 1, 'error' => 'Tracking Number and Consignee Phone Number does not match'];
                }
            } else {
                return ['status' => 1, 'error' => 'No Shipment with entered Tracking Number found'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Tracking Number and/or Consignee Phone Number entered'];
        }
    }

    public function cod_breakup_create($shipment_id, $shipping_charges, $total_cod, $invoice_item_descriptions, $amounts)
    {
        $invoice = new ShipmentInvoice();
        $invoice->shipment_id = $shipment_id;
        $invoice->shipping_charges = $shipping_charges;
        $invoice->total_cod = $total_cod;
        $invoice->save();

        foreach ($invoice_item_descriptions as $key => $description) {
            $invoice_item = new ShipmentInvoiceItem();
            $invoice_item->shipment_invoice_id = $invoice->id;
            $invoice_item->description = $description;
            $invoice_item->amount = $amounts[$key];
            $invoice_item->save();
        }

    }

    public function check_consignee_return_ratio(Request $request)
    {
        $phone = $request->phone;
        $message = '';
        $consignee_information = ConsigneeInformation::where('phone', $phone);
        if ($consignee_information->exists()) {
            $consignee_information = $consignee_information->first();
            $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information->id);
            $color = NULL;
            if ($manual_blacklist->exists()) {
                $manual_blacklist = $manual_blacklist->first();
                $blacklist_setting = BlacklistSetting::find($manual_blacklist->blacklist_setting_id);
                if ($blacklist_setting) {
                    $message = $blacklist_setting->message;
                    $color = $blacklist_setting->color;
                    return response()->json(['status' => 0, 'message' => $message, 'color' => $color]);
                }
            } else {
                $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                if ($blacklist->exists()) {
                    $blacklist = $blacklist->first();
                    $blacklist_setting_id = $blacklist->blacklist_setting_id;
                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                    if ($blacklist_setting) {
                        $message = $blacklist_setting->message;
                        $color = $blacklist_setting->color;
                        return response()->json(['status' => 0, 'message' => $message, 'color' => $color]);
                    }
                }
                return response()->json(['status' => 1]);
            }


        }
        return response()->json(['status' => 1]);
    }

    public function international_excel_index()
    {
        $booking_types = BookingType::whereNotIn('id', [4])->get();
        $user = User::find(session('user_id'));
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('business_category_id', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        $cities = City::where('status', 1)->where('business_category_id', 2)->where('permanent_disabled',0)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        $products = Product::all();

        $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

        $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();

        if (in_array(4, $user_shipping_modes)) {
            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        } else {
            $shipping_mode_same_day_timings = NULL;
        }

        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
        $charges_modes = ChargesModes::whereIn('id', [4])->get();

        return view('client.shipment.book.excel')->with(['booking_types' => $booking_types, 'user' => $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes]);
    }

    public function international_excel_store(Request $request)
    {
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
            'self_collection' => 'Self Collection',
            'order_id' => 'Order ID',
            'order_date' => 'Order Date',


            'item_product_type_id' => 'Item Product Type ID',
            'item_description' => 'Item Description',
            'item_quantity' => 'Item Quantity',
            'item_insurance' => 'Item Insurance',
            'item_price' => 'Product Value',

            'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
            'replacement_item_description' => 'Replacement Item Description',
            'replacement_item_quantity' => 'Replacement Item Quantity',

            'item_product_type_id_1' => 'Try and Buy Item Product Type ID 1',
            'item_description_1' => 'Try and Buy Item Description 1',
            'item_quantity_1' => 'Try and Buy Item Quantity 1',
            'item_insurance_1' => 'Try and Buy Item Insurance 1',
            'item_price_1' => 'Try and Buy Product Value 1',
            'item_product_type_id_2' => 'Try and Buy Item Product Type ID 2',
            'item_description_2' => 'Try and Buy Item Description 2',
            'item_quantity_2' => 'Try and Buy Item Quantity 2',
            'item_insurance_2' => 'Try and Buy Item Insurance 2',
            'item_price_2' => 'Try and Buy Product Value 2',
            'item_product_type_id_3' => 'Try and Buy Item Product Type ID 3',
            'item_description_3' => 'Try and Buy Item Description 3',
            'item_quantity_3' => 'Try and Buy Item Quantity 3',
            'item_insurance_3' => 'Try and Buy Item Insurance 3',
            'item_price_3' => 'Try and Buy Product Value 3',
            'item_product_type_id_4' => 'Try and Buy Item Product Type ID 4',
            'item_description_4' => 'Try and Buy Item Description 4',
            'item_quantity_4' => 'Try and Buy Item Quantity 4',
            'item_insurance_4' => 'Try and Buy Item Insurance 4',
            'item_price_4' => 'Try and Buy Product Value 4',
            'item_product_type_id_5' => 'Try and Buy Item Product Type ID 5',
            'item_description_5' => 'Try and Buy Item Description 5',
            'item_quantity_5' => 'Try and Buy Item Quantity 5',
            'item_insurance_5' => 'Try and Buy Item Insurance 5',
            'item_price_5' => 'Try and Buy Product Value 5',

            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
            'shipping_mode_id' => 'Shipping Mode ID',
            'same_day_timing_id' => 'Same Day Timing ID',
            'try_and_buy_charges' => 'Try and Buy Charges',
            'amount' => 'Collection Amount',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID',
            'pieces_quantity' => 'Pieces',

            'shipper_reference_number_1' => 'Shipper Reference Number 1',
            'shipper_reference_number_2' => 'Shipper Reference Number 2',
            'shipper_reference_number_3' => 'Shipper Reference Number 3',
            'shipper_reference_number_4' => 'Shipper Reference Number 4',
            'shipper_reference_number_5' => 'Shipper Reference Number 5',
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
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0)],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 1)->where('status', 1)],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'consignee_phone_number_2' => ['nullable', 'regex:/^[0][0-9]{10}$/'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'self_collection' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],

            'item_product_type_id' => ['required_if:service_type_id,1,2,5', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required_if:service_type_id,1,2,5', 'nullable', 'between:0,1000'],
            'item_quantity' => ['required_if:service_type_id,1,2,5', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance' => ['required_if:service_type_id,1,2,5', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price' => ['required_if:item_insurance,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_1' => ['required_if:service_type_id,3', 'nullable', 'between:0,1000'],
            'item_quantity_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_1' => ['required_if:service_type_id,3', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_2' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_2' => ['required_with:item_product_type_id_2,', 'nullable', 'between:0,1000'],
            'item_quantity_2' => ['required_with:item_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_2' => ['required_with:item_product_type_id_2,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_2' => ['required_with:item_product_type_id_2,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_3' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_3' => ['required_with:item_product_type_id_3,', 'nullable', 'between:0,1000'],
            'item_quantity_3' => ['required_with:item_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_3' => ['required_with:item_product_type_id_3,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_3' => ['required_with:item_product_type_id_3,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_4' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_4' => ['required_with:item_product_type_id_4,', 'nullable', 'between:0,1000'],
            'item_quantity_4' => ['required_with:item_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_4' => ['required_with:item_product_type_id_4,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_4' => ['required_with:item_product_type_id_4,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'item_product_type_id_5' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description_5' => ['required_with:item_product_type_id_5,', 'nullable', 'between:0,1000'],
            'item_quantity_5' => ['required_with:item_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance_5' => ['required_with:item_product_type_id_5,', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price_5' => ['required_with:item_product_type_id_5,', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

            'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,1000'],
            'replacement_item_quantity' => ['required_if:service_type_id,2', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })],
            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'amount' => ['required_if:service_type_id,1,2', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            'try_and_buy_charges' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            'payment_mode_id' => ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                $query->whereNotIn('id', [3]);
            })],
            'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function ($query) {
                $query->whereIn('id', [4]);
            })],
            'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],

            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190']

        ];
        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        }
        if (isset($spreadsheet)) {
            if (count($spreadsheet[0]) == 59) {
                $fields = [0 => 'service_type_id', 1 => 'pickup_address_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'self_collection', 10 => 'order_id', 11 => 'order_date', 12 => 'item_product_type_id', 13 => 'item_description', 14 => 'item_quantity', 15 => 'item_insurance', 16 => 'item_price', 17 => 'replacement_item_product_type_id', 18 => 'replacement_item_description', 19 => 'replacement_item_quantity', 20 => 'item_product_type_id_1', 21 => 'item_description_1', 22 => 'item_quantity_1', 23 => 'item_insurance_1', 24 => 'item_price_1', 25 => 'item_product_type_id_2', 26 => 'item_description_2', 27 => 'item_quantity_2', 28 => 'item_insurance_2', 29 => 'item_price_2', 30 => 'item_product_type_id_3', 31 => 'item_description_3', 32 => 'item_quantity_3', 33 => 'item_insurance_3', 34 => 'item_price_3', 35 => 'item_product_type_id_4', 36 => 'item_description_4', 37 => 'item_quantity_4', 38 => 'item_insurance_4', 39 => 'item_price_4', 40 => 'item_product_type_id_5', 41 => 'item_description_5', 42 => 'item_quantity_5', 43 => 'item_insurance_5', 44 => 'item_price_5', 45 => 'special_instructions', 46 => 'estimated_weight', 47 => 'shipping_mode_id', 48 => 'same_day_timing_id', 49 => 'try_and_buy_charges', 50 => 'amount', 51 => 'payment_mode_id', 52 => 'charges_mode_id', 53 => 'pieces_quantity', 54 => 'shipper_reference_number_1', 55 => 'shipper_reference_number_2', 56 => 'shipper_reference_number_3', 57 => 'shipper_reference_number_4', 58 => 'shipper_reference_number_5'];

                $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    ;
                    $query->whereNotIn('id', [4, 6]);
                })];
                $service_type_check_id = null;
            } elseif (count($spreadsheet[0]) == 29) {
                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'self_collection', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'special_instructions', 17 => 'estimated_weight', 18 => 'shipping_mode_id', 19 => 'same_day_timing_id', 20 => 'amount', 21 => 'payment_mode_id', 22 => 'charges_mode_id', 23 => 'pieces_quantity', 24 => 'shipper_reference_number_1', 25 => 'shipper_reference_number_2', 26 => 'shipper_reference_number_3', 27 => 'shipper_reference_number_4', 28 => 'shipper_reference_number_5'];
                $service_type_check_id = 1;
            } elseif (count($spreadsheet[0]) == 30) {
                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'replacement_item_product_type_id', 16 => 'replacement_item_description', 17 => 'replacement_item_quantity', 18 => 'special_instructions', 19 => 'estimated_weight', 20 => 'shipping_mode_id', 21 => 'same_day_timing_id', 22 => 'amount', 23 => 'payment_mode_id', 24 => 'charges_mode_id', 25 => 'shipper_reference_number_1', 26 => 'shipper_reference_number_2', 27 => 'shipper_reference_number_3', 28 => 'shipper_reference_number_4', 29 => 'shipper_reference_number_5'];
                $service_type_check_id = 2;
            } elseif (count($spreadsheet[0]) == 47) {
                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id_1', 11 => 'item_description_1', 12 => 'item_quantity_1', 13 => 'item_insurance_1', 14 => 'item_price_1', 15 => 'item_product_type_id_2', 16 => 'item_description_2', 17 => 'item_quantity_2', 18 => 'item_insurance_2', 19 => 'item_price_2', 20 => 'item_product_type_id_3', 21 => 'item_description_3', 22 => 'item_quantity_3', 23 => 'item_insurance_3', 24 => 'item_price_3', 25 => 'item_product_type_id_4', 26 => 'item_description_4', 27 => 'item_quantity_4', 28 => 'item_insurance_4', 29 => 'item_price_4', 30 => 'item_product_type_id_5', 31 => 'item_description_5', 32 => 'item_quantity_5', 33 => 'item_insurance_5', 34 => 'item_price_5', 35 => 'special_instructions', 36 => 'estimated_weight', 37 => 'shipping_mode_id', 38 => 'same_day_timing_id', 39 => 'try_and_buy_charges', 40 => 'payment_mode_id', 41 => 'charges_mode_id', 42 => 'shipper_reference_number_1', 43 => 'shipper_reference_number_2', 44 => 'shipper_reference_number_3', 45 => 'shipper_reference_number_4', 46 => 'shipper_reference_number_5'];
                $service_type_check_id = 3;
            } elseif (count($spreadsheet[0]) == 24) {
                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'order_id', 9 => 'order_date', 10 => 'item_product_type_id', 11 => 'item_description', 12 => 'item_quantity', 13 => 'item_insurance', 14 => 'item_price', 15 => 'special_instructions', 16 => 'estimated_weight', 17 => 'shipping_mode_id', 18 => 'same_day_timing_id', 19 => 'shipper_reference_number_1', 20 => 'shipper_reference_number_2', 21 => 'shipper_reference_number_3', 22 => 'shipper_reference_number_4', 23 => 'shipper_reference_number_5'];
                $service_type_check_id = 5;
            } else {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            unset($spreadsheet[0]);
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
            } else {
                $forms = $request->all();

                $forms = $forms['form'];
                foreach ($forms as $form) {
                    $row = array();
                    foreach ($form as $key => $value) {
                        $row[$key] = $value;
                    }
                    $rows[] = $row;
                }
                $service_type_check_id = $request->service_type_check_id;
            }

            $errors = array();
            $nsa_error = array();
            $order_ids = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();
            $blacklist_errors = array();
            $blacklist_found_categories = array();

            if (Session::has('prefix')) {
                $rules['order_id'] = ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })];
            } else {
                if (Session::has('restrict_order_id')) {
                    $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })];
                } else {
                    $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
                }
            }

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if (!isset($row['charges_mode_id'])) {
                    $rows[$key]['charges_mode_id'] = 4;
                }
                if ($service_type_check_id != null) {
                    $rows[$key]['service_type_id'] = $service_type_check_id;
                    $row['service_type_id'] = $service_type_check_id;
                } else {
                    $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                        $query->whereNotIn('id', [4]);
                    })];
                }


                if (!isset($row['pieces_quantity']) || $row['pieces_quantity'] == null) {
                    $row['pieces_quantity'] = 1;
                }

                $rows[$key]['pieces_quantity'] = $row['pieces_quantity'];

                if (!isset($row['self_collection']) || $row['self_collection'] == null) {
                    $row['self_collection'] = 'no';
                }

                $rows[$key]['self_collection'] = $row['self_collection'];

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
                    if (Session::has('prefix')) {
                        $length = strlen(session('prefix'));
                        $check_order_id = str_split($row['order_id'], $length);
                        if (session('prefix') != $check_order_id[0]) {
                            $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                        } else {
                            if (!array_key_exists(1, $check_order_id)) {
                                $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                            }
                        }
                    }

                    if (!empty(trim($row['order_id']))) {
                        if (empty($order_ids)) {
                            $order_ids[] = $row['order_id'];
                            $order_id_row[$row['order_id']] = $row_id;
                        } else {
                            if (in_array($row['order_id'], $order_ids, true)) {
                                $errors[$row_id]['order_id'] = 'Same Order ID as of Row #' . $order_id_row[$row['order_id']];
                            } else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                            }
                        }
                    }

                    if ($row['service_type_id'] != 5) {
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
                            $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
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

                        if (($user_shipping_info->city->id != $consignee_city->id) && ($service_type_check_id == 1 || $service_type_check_id == 2)) {
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
                        if (!$request->excel_nsa) {
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
                        if (!$request->excel_blacklist) {
                            $consignee_phone_number_1 = substr_replace($row['consignee_phone_number_1'], '-', 4, 0);
                            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
                            if ($consignee_information->exists()) {
                                $consignee_information = $consignee_information->first();
                                $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information->id);
                                if ($manual_blacklist->exists()) {
                                    $manual_blacklist = $manual_blacklist->first();
                                    $blacklist_setting_id = $manual_blacklist->blacklist_setting_id;
                                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                    if ($blacklist_setting) {
                                        if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                            $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                            $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                        }
                                    }
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    } else {
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: 0, Delivered: 0, Undelivered: 0, Return Confirmed: 0';
                                    }
                                } else {
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if ($blacklist->exists()) {
                                        $blacklist = $blacklist->first();
                                        $blacklist_setting_id = $blacklist->blacklist_setting_id;
                                        $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                        if ($blacklist_setting) {
                                            if (!array_key_exists($blacklist_setting_id, $blacklist_found_categories)) {
                                                $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                                $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                            }
                                        }
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: ' . $blacklist->shipments . ', Delivered: ' . $blacklist->delivered . '(' . $blacklist->delivered_ratio . '), Undelivered: ' . $blacklist->undelivered . '(' . $blacklist->undelivered_ratio . '), Return Confirmed: ' . $blacklist->return . '(' . $blacklist->return_ratio . ')';
                                    }
                                }
                            }
                        }
                    } else {
                        $pickup_consignee_city = City::where('name', $row['consignee_city_name'])->first();

                        if (!$pickup_consignee_city->status) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if (!$pickup_consignee_city->zone_id) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup Address\'s City: ' . $pickup_consignee_city->name . ' is deactivated';
                        }

                        if (!$pickup_consignee_city->pickup) {
                            $errors[$row_id]['consignee_city_name'] = 'Pickup is not allowed for City: ' . $pickup_consignee_city->name;
                        }

                        $pickup_address_id_for_delivery = $row['pickup_address_id'];
                        $pickup_address_for_delivery = UserShippingInfo::find($pickup_address_id_for_delivery);
                        $delivery_city = City::find($pickup_address_for_delivery->city_id);

                        if (!$delivery_city->status) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }
                        if (!$delivery_city->zone_id) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery City: ' . $delivery_city->name . ' is deactivated';
                        }

                        $pickup_city_id = $pickup_consignee_city->id;

                        if ($delivery_city->id != $pickup_city_id && $row['shipping_mode_id'] == 4) {
                            $errors[$row_id]['consignee_city_name'] = 'Same Day Delivery is not available for Different City Shipment';
                        }
                        if (!CityDelivery::where('city_id', $delivery_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['pickup_address_id'] = 'Delivery is not allowed for City: ' . $delivery_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                    }
                }
            }

            if (empty($errors)) {
                if (empty($nsa_error)) {
                    if (TRUE || empty($blacklist_errors)) {
                        foreach ($rows as $key => $row) {
                            $row['user_id'] = $user_id;
                            $row['account_type_id'] = 1;
                            $row['nsas'] = $check;
                            $row['nsa'] = $request->excel_nsa;
                            if (session('user_type') == 2) {
                                $row['substitute_user_id'] = Auth::id();
                            } else {
                                $row['substitute_user_id'] = null;
                            }

                            if (Session::has('prefix')) {
                                $row['prefix'] = session('prefix');
                            } else {
                                $row['prefix'] = NULL;
                            }

                            if ($row['service_type_id'] == 3 && $row['payment_mode_id'] == 4) {
                                $row['payment_mode_id'] == 1;
                            }

                            if ($row['payment_mode_id'] == 4) {
                                $row['amount'] = 0;
                            }

                            if ($user_id != 3324) {
                                dispatch(new ProcessShipmentBookingDB($row));
                            } else {
                                dispatch(new ProcessShipmentBookingDBPriority($row));
                            }
                        }

                        return redirect()->back()->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                    } else {
                        return view('client.shipment.book.blacklist')->with(['data' => $rows, 'blacklist_errors' => $blacklist_errors, 'blacklist_found_categories' => $blacklist_found_categories, 'service_type_check_id' => $service_type_check_id]);
                    }
                } else {
                    return view('client.shipment.book.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error, 'service_type_check_id' => $service_type_check_id]);
                }
            } else {
                $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                $booking_types = BookingType::whereNotIn('id', [4])->pluck('booking_type', 'id');
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

                $payment_modes = PaymentMode::whereNotIn('id', [3])->pluck('mode', 'id');
                $charges_modes = ChargesModes::whereIn('id', [4])->pluck('charges_mode', 'id');
                $city_name = array();
                foreach ($cities as $city) {
                    $city_name[$city->name] = $city->name;
                }
                return view('client.shipment.book.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'user_shipping_modes' => $user_shipping_modes, 'charges_modes' => $charges_modes, 'service_type_check_id' => $service_type_check_id]);
            }
        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function get_ftl_info(Request $request)
    {
        if ($request->id) {
            $ftl_request = FtlRequest::find($request->id);
            $data = array('origin_id' => $ftl_request->origin_id, 'destination_id' => $ftl_request->destination_id, 'weight' => $ftl_request->weight, 'quantity' => $ftl_request->quantity, 'total_charges' => $ftl_request->total_charges);

            return response()->json(['status' => 1, 'data' => $data]);
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function check_shipment_allowed_city(Request $request)
    {
        $shipping_mode_id = $request->shipping_mode_id;
        $pickup_city_id = $request->pickup_city_id;
        $consignee_city_id = $request->consignee_city_id;
        $service_type_id = $request->service_type_id;

        $user_id = session('user_id');

        $user = User::find($user_id);

        $pickup_city = City::find($pickup_city_id);
        if (!$pickup_city) {
            return FALSE;
        }
        $destination_city = City::find($consignee_city_id);
        if (!$destination_city) {
            return FALSE;
        }

        $pickup_city = $pickup_city->id;
        $destination_city = $destination_city->id;
        $origin_city_allowed = TRUE;
        $destination_city_allowed = TRUE;
        if ($service_type_id == 5) {
            return ['origin_city_allowed' => $origin_city_allowed, 'destination_city_allowed' => $destination_city_allowed];
        }

        if ($user->account_type_id == 1) {
            $rate_origin = RateOriginHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
            if ($rate_origin->exists()) {
                $origin = RateOriginHub::where(['user_id' => $user_id, 'city_id' => $pickup_city, 'shipping_mode_id' => $shipping_mode_id]);
                if (!$origin->exists()) {
                    $origin_city_allowed = FALSE;
                }
            }
            $rate_destination = RateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
            if ($rate_destination->exists()) {
                $destination = RateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city, 'shipping_mode_id' => $shipping_mode_id]);
                if (!$destination->exists()) {
                    $destination_city_allowed = FALSE;
                }
            }

        } else {
            if ($user->corporate_rate_type_id == 1 || $user->corporate_rate_type_id == 2 || $user->corporate_rate_type_id == NULL) {
                $rate_origin = CorporateRateOriginHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                if ($rate_origin->exists()) {
                    $origin = CorporateRateOriginHub::where(['user_id' => $user_id, 'city_id' => $pickup_city, 'shipping_mode_id' => $shipping_mode_id]);
                    if (!$origin->exists()) {
                        $origin_city_allowed = FALSE;
                    }
                }
                $rate_destination = CorporateRateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                if ($rate_destination->exists()) {
                    $destination = CorporateRateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city, 'shipping_mode_id' => $shipping_mode_id]);
                    if (!$destination->exists()) {
                        $destination_city_allowed = FALSE;
                    }
                }
            } else if ($user->corporate_rate_type_id == 3) {
                $rate_origin = CorporateDefaultRateOriginHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                if ($rate_origin->exists()) {
                    $origin = CorporateDefaultRateOriginHub::where(['user_id' => $user_id, 'city_id' => $pickup_city, 'shipping_mode_id' => $shipping_mode_id]);
                    if (!$origin->exists()) {
                        $origin_city_allowed = FALSE;
                    }
                }
                $rate_destination = CorporateDefaultRateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                if ($rate_destination->exists()) {
                    $destination = CorporateDefaultRateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city, 'shipping_mode_id' => $shipping_mode_id]);
                    if (!$destination->exists()) {
                        $destination_city_allowed = FALSE;
                    }
                }
            }


        }
        return ['origin_city_allowed' => $origin_city_allowed, 'destination_city_allowed' => $destination_city_allowed];
    }

    static public function check_origin($pickup_address_id, $shipping_mode_id, $user_id)
    {

        $pickup_city = UserShippingInfo::find($pickup_address_id);

        if ($pickup_city) {
            $pickup_city_id = $pickup_city->city_id;

            $user = User::find($user_id);

            $origin_city_allowed = TRUE;

            if ($user->account_type_id == 1) {
                $rate_origin = RateOriginHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                if ($rate_origin->exists()) {
                    $origin = RateOriginHub::where(['user_id' => $user_id, 'city_id' => $pickup_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                    if (!$origin->exists()) {
                        $origin_city_allowed = FALSE;
                    }
                }

            } else {
                if ($user->corporate_rate_type_id == 1 || $user->corporate_rate_type_id == 2 || $user->corporate_rate_type_id == NULL) {
                    $rate_origin = CorporateRateOriginHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                    if ($rate_origin->exists()) {
                        $origin = CorporateRateOriginHub::where(['user_id' => $user_id, 'city_id' => $pickup_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                        if (!$origin->exists()) {
                            $origin_city_allowed = FALSE;
                        }
                    }
                } else if ($user->corporate_rate_type_id == 3) {
                    $rate_origin = CorporateDefaultRateOriginHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                    if ($rate_origin->exists()) {
                        $origin = CorporateDefaultRateOriginHub::where(['user_id' => $user_id, 'city_id' => $pickup_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                        if (!$origin->exists()) {
                            $origin_city_allowed = FALSE;
                        }
                    }
                }


            }

            return $origin_city_allowed;
        } else {
            return FALSE;
        }


    }

    static public function check_destination($consignee_city, $shipping_mode_id, $user_id, $type)
    {

        if ($type == 1) {
            $destination_city = City::where('name', $consignee_city)->first();
        } else {
            $destination_city = City::where('id', $consignee_city)->first();
        }
        if ($destination_city) {
            $destination_city_id = $destination_city->id;
            $user = User::find($user_id);

            $destination_city_allowed = TRUE;

            if ($user->account_type_id == 1) {
                $rate_destination = RateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                if ($rate_destination->exists()) {
                    $destination = RateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                    if (!$destination->exists()) {
                        $destination_city_allowed = FALSE;
                    }
                }

            } else {
                if ($user->corporate_rate_type_id == 1 || $user->corporate_rate_type_id == 2 || $user->corporate_rate_type_id == NULL) {
                    $rate_destination = CorporateRateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                    if ($rate_destination->exists()) {
                        $destination = CorporateRateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                        if (!$destination->exists()) {
                            $destination_city_allowed = FALSE;
                        }
                    }
                } else if ($user->corporate_rate_type_id == 3) {
                    $rate_destination = CorporateDefaultRateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                    if ($rate_destination->exists()) {
                        $destination = CorporateDefaultRateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                        if (!$destination->exists()) {
                            $destination_city_allowed = FALSE;
                        }
                    }
                }


            }
            return $destination_city_allowed;
        } else {
            return FALSE;
        }


    }

    static public function check_return_destination($pickup_address_id, $shipping_mode_id, $user_id)
    {

        $pickup_city = UserShippingInfo::find($pickup_address_id);

        if ($pickup_city) {

            $pickup_city_id = $pickup_city->city_id;

            if ($pickup_city_id) {
                $destination_city_id = $pickup_city_id;
                $user = User::find($user_id);

                $destination_city_allowed = TRUE;

                if ($user->account_type_id == 1) {
                    $rate_destination = RateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                    if ($rate_destination->exists()) {
                        $destination = RateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                        if (!$destination->exists()) {
                            $destination_city_allowed = FALSE;
                        }
                    }

                } else {
                    if ($user->corporate_rate_type_id == 1 || $user->corporate_rate_type_id == 2 || $user->corporate_rate_type_id == NULL) {
                        $rate_destination = CorporateRateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                        if ($rate_destination->exists()) {
                            $destination = CorporateRateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                            if (!$destination->exists()) {
                                $destination_city_allowed = FALSE;
                            }
                        }
                    } else if ($user->corporate_rate_type_id == 3) {
                        $rate_destination = CorporateDefaultRateDestinationHub::where('user_id', $user_id)->where('shipping_mode_id', $shipping_mode_id);
                        if ($rate_destination->exists()) {
                            $destination = CorporateDefaultRateDestinationHub::where(['user_id' => $user_id, 'city_id' => $destination_city_id, 'shipping_mode_id' => $shipping_mode_id]);
                            if (!$destination->exists()) {
                                $destination_city_allowed = FALSE;
                            }
                        }
                    }


                }
                return $destination_city_allowed;
            } else {
                return FALSE;
            }

        }

    }
    function check_bdmk($city_id,$consignee_address,$check_bdmk){

        if(isset($city_id)){

            $str_arr = null;
            $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $consignee_address);
            $found_keyword = array();
            $result = array();
            foreach ($check_bdmk as $nsa) {
                foreach ($str_arr as $arr_value) {
                    $arr_value = trim($arr_value);
                    if (strtolower($nsa) == strtolower($arr_value)) {
                        array_push($found_keyword,$arr_value);
                    }
                }
            }
            $invalid_cities  = array();
            $invalid_cities_string = "";
            if($found_keyword) {
                $data_found = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'bdm.city_id')
                    ->select('bdm.city_id','c.name as city_name','booking_destination_mapping_keywords.keyword')
                    ->whereIn('booking_destination_mapping_keywords.keyword', $found_keyword);
                if ($data_found->exists()) {
                    $data_found =$data_found->get();

                    foreach ($data_found as $value){
                        if($value->city_id != $city_id){
                            $dd = isset($invalid_cities[$value->city_name]) ? $invalid_cities[$value->city_name] : '';
                            $invalid_cities[$value->city_name] = trim($dd)." ".$value->keyword;
                        }
                    }
                    if($invalid_cities){
                        foreach ($invalid_cities as $key=>$value){
                            if(empty($invalid_cities_string)){
                                $invalid_cities_string=  'Area <strong>'.$value.'</strong> is in <strong>'.$key.'</strong>';
                            }else{
                                $invalid_cities_string= $invalid_cities_string . "<br />". 'Area<strong>'.$value.'</strong> is in <strong>'.$key.'</strong>';
                            }

                        }
                        return $result = array('status'=>'false','invalid_cities'=>trim($invalid_cities_string),'error'=>'Invalid Address');
                    }
                }
            }
            return $result;
        }
    }

    public function address_verify(Request $request){
        if(isset($request->city_id)){


            $city_id = $request->city_id;
            $consignee_address = $request->consignee_address;
            $check = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                ->where('bdm.status',1)
                ->select(['booking_destination_mapping_keywords.keyword'])
                ->pluck('keyword')
                ->toArray();

            $str_arr = null;
            $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $consignee_address);
            $found_keyword = array();
            foreach ($check as $nsa) {
                foreach ($str_arr as $arr_value) {
                    $arr_value = trim($arr_value);
                    if (strtolower($nsa) == strtolower($arr_value)) {
                        array_push($found_keyword,$arr_value);
                    }
                }
            }
            $invalid_cities  = array();
            if($found_keyword) {
                $data_found = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'bdm.city_id')
                    ->select('bdm.city_id','c.name as city_name','booking_destination_mapping_keywords.keyword')
                    ->whereIn('booking_destination_mapping_keywords.keyword', $found_keyword);
                if ($data_found->exists()) {
                    $data_found =$data_found->get();

                    foreach ($data_found as $value){
                        if($value->city_id != $city_id){
                            $dd = isset($invalid_cities[$value->city_name]) ? $invalid_cities[$value->city_name] : '';
                            $invalid_cities[$value->city_name] = trim($dd)." ".$value->keyword;
                        }
                    }
                    if($invalid_cities){
                        return response()->json(['status'=>'false','invalid_cities'=>$invalid_cities,'error'=>'Invalid Address']);
                    }
                }
            }
            return response()->json(['status'=>'true']);

        }
        return response()->json(['status'=>'true']);

    }


}