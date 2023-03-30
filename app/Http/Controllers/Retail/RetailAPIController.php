<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailPaymentMode;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Admin\Retail\RetailTraxBox;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\RetailAppSlider;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\HR\Employee;
use App\Http\Models\Product;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RetailAPIController extends Controller
{
    private $names = [
        'email_address' => 'Email Address',
        'password' => 'Password',
        'attendance_date' => 'Attendance Date',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'action' => 'Action',
        'from_date' => 'From Date',
        'user_name' => 'Username',

    ];

    private $messages = [
        'integer' => ':attribute must be an Integer.',
        'digits' => ':attribute must be of :digits Digits.',
        'exists' => 'Given :attribute is of Invalid ID.',
        'image' => ':attribute must be an Image.'
    ];

    public function login(Request $request)
    {
        $rules = [
            'user_name' => ['required'],
            'password' => ['required', 'min:6']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user = RetailUser::where('name', $request->input('user_name'));
            if ($user->exists()) {
                $user = $user->first();

                if($user->category == 2){
                    $trax_center = RetailTraxCenter::find($user->category_id);
                }elseif ($user->category == 1){
                    $trax_center = RetailFranchise::find($user->category_id);
                }

                if($user->status == 0){
                    return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                }
                if (Hash::check($request->input('password'), $user->password)) {
                    $information = array();
                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['phone'] = $user->phone_no;
                    $information['cnic'] = $user->cnic;
                    $information['address'] = $user->address;
                    $information['pickup_address_id'] = $trax_center->pickup_address_id;
                    $information['role'] = 'retail_user';

                    if ($user->api_token) {
                        $information['api_token'] = $user->api_token;
                    } else {
                        $api_token = uniqid(base64_encode(str_random(60)));

                        $user->api_token = $api_token;

                        $user->save();

                        $information['api_token'] = $api_token;
                    }
                    $information['welcome_bit'] = 0;
                    if(!$user->first_login){
                        $information['welcome_bit'] = 1;
                        $information['welcome_message'] = "Welcome to TRAX ".$user->name;
                    }
                    $user->first_login = 1;
                    $user->save();
                    return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Password']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Wrong Username/Password!']);
            }
        }
    }

    public function retail_index(Request $request){
        $products = Product::all();
        $business_categories = BusinessCategory::where('id', '!=', 2)->get();
        $shipping_modes = RetailShippingMode::all();
        $domestic_cities = City::where('business_category_id', 1)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', '=', 1)->get();
        $trax_boxes = RetailTraxBox::all();
        $banks = BanksList::all();
        return response()->json(["status" => 0, 'products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'domestic_overland_cities' => $domestic_overland_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes, 'banks' => $banks]);
    }

    public function retail_bank_info(Request $request){
        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no)
            ->select('iban', 'account_number', 'cheque_image', 'bank_id');
        $shipment_count = RetailShipment::where('shipper_phone_no', $request->shipper_phone_no)->where('shipping_mode', 3)->count();
        if($shipper_info->exists()){
            $shipper_info = $shipper_info->get();
            return response()->json(["status" => 0, "shipper_bank_info" => $shipper_info, "shipment_count" => $shipment_count]);
        }
        return response()->json(["status" => 0, "shipper_bank_info" => "", "shipment_count" => ""]);
    }

    public function retail_shipment_store(Request $request)
    {
        $retail_user_id = $request->retail_user_id;
        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $user_id = $setting->setting_value;
        $pickup_address_id = $request->pickup_address_id;
        $category = $request->category;
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;

        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_cell_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $request->input('order_id');
        $package_type = FALSE;
        $special_instructions = NULL;


        $shipping_mode_check = $request->input('shipping_mode_id');
        $consignee_city_id = $request->input('city_id');
        if ($shipping_mode_check == 1) {
            $shipping_mode_id = 2;
        } elseif ($shipping_mode_check == 4) {
            $shipping_mode_id = 3;
        } else {
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        $request->weight_charges = str_replace(',', '', $request->input('weight_charges'));
        $request->fuel_surcharge = str_replace(',', '', $request->input('fuel_surcharge'));

        $city = City::find($pickup_city_id);
        $gst = $city->zone->gst;
        $total_charges_without_gst = $request->weight_charges + $request->fuel_surcharge;
        $gst = $gst * $total_charges_without_gst;
        $total_charges = $total_charges_without_gst + $gst;
        if ($shipping_mode_check == 3) {
            $amount = str_replace(',', '', $request->input('cod_amount'));
            $r_amount = 0;
        } else {
            $amount = 0;
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        $charges_mode_id = 1;

        if ($request->volumetric_weight == 1) {
            $estimated_weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
            $length = $request->length;
            $breadth = $request->breadth;
            $height = $request->height;
        } else {
            $estimated_weight = $request->input('weight');
            $length = null;
            $breadth = null;
            $height = null;
        }

        $shipment_id = RetailShipmentBookController::book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

        $tracking_number = RetailShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = $request->product_id;

        $item_description = NULL;

        $item_quantity = 1;

        if ($request->input('insurance') == 1) {
            $price = str_replace(',', '', $request->input('insurance_amount'));
            $insurance = TRUE;
        } else {
            $price = NULL;
            $insurance = FALSE;
        }

        $type = 0;

        RetailShipmentBookController::add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

        if ($pieces_quantity > 1) {
            RetailShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
        }
        $destination = $request->city_id;

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_cell_no);
        if ($shipper_info->exists()) {
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;


            if ($request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        } else {
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;
            $shipper_info->save();
            if ($request->hasFile('cheque') && $request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        }
        $retail_shipment = new RetailShipment();
        $retail_shipment->shipment_id = $shipment_id;
        $retail_shipment->product_type_id = $request->product_id;
        $retail_shipment->shipping_mode = $request->shipping_mode_id;
        $retail_shipment->destination = $destination;
        $retail_shipment->payment_mode_id = $request->payment_mode_id;
        $retail_shipment->shipper_phone_no = $request->shipper_cell_no;
        $retail_shipment->shipper_name = $request->shipper_name;
        $retail_shipment->shipper_cnic = $request->shipper_cnic;
        $retail_shipment->shipper_address = $request->shipper_address;
        $retail_shipment->trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;
        $retail_shipment->total_charges_without_gst = $total_charges_without_gst;
        $retail_shipment->gst = $gst;
        $retail_shipment->total_charges = $total_charges;
        $retail_shipment->weight_charges = $request->weight_charges;
//        $retail_shipment->cash_handling_charges = $request->cash_handling_charges;
        $retail_shipment->fuel_surcharge = $request->fuel_surcharge;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->retail_user_id = $retail_user_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', $category)->where('retail_user_id', $retail_user_id);;
        if($cash_deposit->exists()){
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $total_charges;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        }
        else{
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = $category;
            $cash_deposit->retail_user_id = $retail_user_id;
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $total_charges;
            $cash_deposit->save();

        }

        $cash_deposit_shipment = new RetailCashDepositShipment();
        $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
        $cash_deposit_shipment->shipment_id = $shipment_id;
        $cash_deposit_shipment->shipping_mode_id = $request->shipping_mode_id;
        $cash_deposit_shipment->save();


        AdminPickupsController::generate($shipment_id);
        NotificationsController::send(115, $tracking_number, $shipper_info->id);

        return response()->json(['status' => 0, 'message' => 'Shipment Booked with Tracking Number: ' . $tracking_number]);

    }

    public function retail_shipment_store_v2(Request $request)
    {
        $retail_user_id = $request->retail_user_id;
        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $user_id = $setting->setting_value;
        $pickup_address_id = $request->pickup_address_id;
        $category = $request->category;
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;

        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_cell_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $request->input('order_id');
        $package_type = FALSE;
        $special_instructions = NULL;


        $shipping_mode_check = $request->input('shipping_mode_id');
        $consignee_city_id = $request->input('city_id');
        if ($shipping_mode_check == 1) {
            $shipping_mode_id = 2;
        } elseif ($shipping_mode_check == 4) {
            $shipping_mode_id = 3;
        } else {
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        if ($shipping_mode_check == 3) {
            $amount = str_replace(',', '', $request->input('cod_amount'));
            $r_amount = 0;
        } else {
            $amount = 0;
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        $charges_mode_id = 1;

        if ($request->volumetric_weight == 1) {
            $estimated_weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
            $length = $request->length;
            $breadth = $request->breadth;
            $height = $request->height;
        } else {
            $estimated_weight = $request->input('weight');
            $length = null;
            $breadth = null;
            $height = null;
        }

        $shipment_id = RetailShipmentBookController::book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

        $tracking_number = RetailShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = $request->product_id;

        $item_description = NULL;

        $item_quantity = 1;

        if ($request->input('insurance') == 1) {
            $price = str_replace(',', '', $request->input('insurance_amount'));
            $insurance = TRUE;
        } else {
            $price = NULL;
            $insurance = FALSE;
        }

        $type = 0;

        RetailShipmentBookController::add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

        if ($pieces_quantity > 1) {
            RetailShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
        }
        $destination = $request->city_id;

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_cell_no);
        if ($shipper_info->exists()) {
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;


            if ($request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        } else {
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;
            $shipper_info->save();
            if ($request->hasFile('cheque') && $request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        }
        $retail_shipment = new RetailShipment();
        $retail_shipment->shipment_id = $shipment_id;
        $retail_shipment->product_type_id = $request->product_id;
        $retail_shipment->shipping_mode = $request->shipping_mode_id;
        $retail_shipment->destination = $destination;
        $retail_shipment->payment_mode_id = $request->payment_mode_id;
        $retail_shipment->shipper_phone_no = $request->shipper_cell_no;
        $retail_shipment->shipper_name = $request->shipper_name;
        $retail_shipment->shipper_cnic = $request->shipper_cnic;
        $retail_shipment->shipper_address = $request->shipper_address;
        $retail_shipment->trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;
        $retail_shipment->total_charges = $amount;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->retail_user_id = $retail_user_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', $category)->where('retail_user_id', $retail_user_id);;
        if($cash_deposit->exists()){
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $amount;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        }
        else{
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = $category;
            $cash_deposit->retail_user_id = $retail_user_id;
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $amount;
            $cash_deposit->save();

        }

        $cash_deposit_shipment = new RetailCashDepositShipment();
        $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
        $cash_deposit_shipment->shipment_id = $shipment_id;
        $cash_deposit_shipment->shipping_mode_id = $request->shipping_mode_id;
        $cash_deposit_shipment->save();


        AdminPickupsController::generate($shipment_id);
        NotificationsController::send(115, $tracking_number, $shipper_info->id);

        return response()->json(['status' => 0, 'message' => 'Shipment Booked with Tracking Number: ' . $tracking_number]);

    }

    public function retail_shipment_calculate_rates(Request $request){
        $retail_user_id = $request->retail_user_id;
        $retail_user = RetailUser::find($retail_user_id);
        $trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;
        if($retail_user) {
            $pickup_city_id = $retail_user->store->pickup_address->city_id;
            $discount =  $retail_user->store->discount;
            if ($request->volumetric_weight == 1) {
                $weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
            } else {
                $weight = $request->input('weight');
            }
            $insurance =  intval($retail_user->store->insurance);
        
            $packaging = ($request->packaging_amount != null) ? str_replace(',', '', $request->packaging_amount) : 0;

            $insurance_amount = ($request->insurance_amount != null) ? intval($request->insurance_amount) : 0;
    
            if ($insurance_amount > 0) {
                $insurance_amount = round($insurance_amount * $insurance / 100, 2);
            }

            $rates = RetailRatesCalculationController::rates($request->shipping_mode_id, $request->business_category_id, $pickup_city_id, $request->city_id, $trax_box_id, $discount, $weight,$insurance_amount, intval($packaging));
            return response()->json(['status' => 0, 'rates' => $rates]);
        }
        return response()->json(['status' => 1, 'message' => "Invalid User"]);
    }

    public function retail_ticker_images(Request $request)
    {
        $retail_ticker_images = RetailAppSlider::orderBy('id', 'ASC');
        if ($retail_ticker_images->exists()) {
            $retail_ticker_images = $retail_ticker_images->get();
            return response()->json(['status' => 0, 'images' => $retail_ticker_images]);
        } else {
            return response()->json(['status' => 1, 'message' => 'No Images Found']);
        }
    }

    public function retail_shipment_store_v3(Request $request)
    {
        $retail_user_id = $request->retail_user_id;
        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $user_id = $setting->setting_value;
        $pickup_address_id = $request->pickup_address_id;
        $category = $request->category;
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;
        $trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;

        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_cell_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $request->input('order_id');
        $package_type = FALSE;
        $special_instructions = NULL;

        $retail_type = '';
        $retail_user = RetailUser::find($retail_user_id);
        if($retail_user->category == 1){
            $retail_type = RetailFranchise::find($retail_user->category_id);
        }
        else{
            $retail_type = RetailTraxCenter::find($retail_user->category_id);
        } 

        $discount = $retail_type->discount;
        $insurance = $retail_type->insurance;

        $shipping_mode_check = $request->input('shipping_mode_id');
        $consignee_city_id = $request->input('city_id');
        if ($shipping_mode_check == 1) {
            $shipping_mode_id = 2;
        } elseif ($shipping_mode_check == 4) {
            $shipping_mode_id = 3;
        } else {
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        // if ($shipping_mode_check == 3) {
        //     $amount = str_replace(',', '', $request->input('cod_amount'));
        //     $r_amount = 0;
        // } else {
        //     $amount = 0;
        //     $r_amount = 0;
        // }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category_id');

        $charges_mode_id = 1;

        if ($request->volumetric_weight == 1) {
            $estimated_weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
            $length = $request->length;
            $breadth = $request->breadth;
            $height = $request->height;
        } else {
            $estimated_weight = $request->input('weight');
            $length = null;
            $breadth = null;
            $height = null;
        }


        $packaging = ($request->packaging_amount != null) ? intval($request->packaging_amount) : 0;

        $insurance_amount = ($request->insurance_amount != null) ? str_replace(',', '', $request->insurance_amount) : 0;
        if ($insurance_amount > 0) {
            $insurance_amount = round(intval($insurance_amount) * $insurance / 100, 2);
        }

        $rates = RetailRatesCalculationController::rates($request->shipping_mode_id, $request->business_category_id, $pickup_city_id, $request->city_id, $trax_box_id, $discount, $estimated_weight, $insurance_amount, $packaging);
    
     
        $charges = $rates["total_charges"];
        if ($shipping_mode_check == 3) {
            $amount = str_replace(',', '', $request->input('cod_amount'));
            $r_amount = 0;
            if ($charges_mode_id == 2) {
                $amount = $amount + $charges;
            }
        } else {
            $amount = 0;
            if ($charges_mode_id == 2) {
                $amount = $charges;
            }
            $r_amount = 0;
        }

        $shipment_id = RetailShipmentBookController::book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

        $tracking_number = RetailShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = $request->product_id;

        $item_description = NULL;

        $item_quantity = 1;

        if ($request->input('insurance') == 1) {
            $price = str_replace(',', '', $request->input('insurance_amount'));
            $insurance = TRUE;
        } else {
            $price = NULL;
            $insurance = FALSE;
        }

        $type = 0;

        RetailShipmentBookController::add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

        if ($pieces_quantity > 1) {
            RetailShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
        }
        $destination = $request->city_id;

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_cell_no);
        if ($shipper_info->exists()) {
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;


            if ($request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        } else {
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $request->shipper_cell_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;
            $shipper_info->save();
            if ($request->hasFile('cheque') && $request->iban_no != null && $request->account_no != null && $request->bank_id != null) {
                $shipper_info->bank_id = $request->bank_id;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';
                    $file = $request->file('cheque');
                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        }
        $retail_shipment = new RetailShipment();
        $retail_shipment->shipment_id = $shipment_id;
        $retail_shipment->product_type_id = $request->product_id;
        $retail_shipment->shipping_mode = $request->shipping_mode_id;
        $retail_shipment->destination = $destination;
        $retail_shipment->payment_mode_id = $request->payment_mode_id;
        $retail_shipment->shipper_phone_no = $request->shipper_cell_no;
        $retail_shipment->shipper_name = $request->shipper_name;
        $retail_shipment->shipper_cnic = $request->shipper_cnic;
        $retail_shipment->shipper_address = $request->shipper_address;
        $retail_shipment->trax_box_id = ($request->trax_box_id != -1) ? $request->trax_box_id : null;
        $retail_shipment->total_charges = $charges;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->charges_with_discount = $rates['charges_with_discount'];
        $retail_shipment->discount = $rates['discount_amount'];
        $retail_shipment->packaging_charges = $rates['packaging_and_insurance_charges'];
        $retail_shipment->insurance_charges = $rates['insurance_amount'];
        $retail_shipment->total_charges_without_gst = $rates['charges_without_gst'];
        $retail_shipment->weight_charges = $rates['charges_without_gst'];
        $retail_shipment->gst = $rates['gst_charges'];
        $retail_shipment->retail_user_id = $retail_user_id;
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', $category)->where('retail_user_id', $retail_user_id);
        ;
        if ($cash_deposit->exists()) {
            $cash_deposit = $cash_deposit->first();
            $total_shipments = $cash_deposit->total_cn + 1;
            $total_cash = $cash_deposit->total_cash + $amount;
            $cash_deposit->total_cn = $total_shipments;
            $cash_deposit->total_cash = $total_cash;
            $cash_deposit->save();
        } else {
            $cash_deposit = new RetailCashDeposit();
            $cash_deposit->category = $category;
            $cash_deposit->retail_user_id = $retail_user_id;
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $amount;
            $cash_deposit->save();

        }

        $cash_deposit_shipment = new RetailCashDepositShipment();
        $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
        $cash_deposit_shipment->shipment_id = $shipment_id;
        $cash_deposit_shipment->shipping_mode_id = $request->shipping_mode_id;
        $cash_deposit_shipment->save();


        AdminPickupsController::generate($shipment_id);
        NotificationsController::send(115, $tracking_number, $shipper_info->id);

        return response()->json(['status' => 0, 'message' => 'Shipment Booked with Tracking Number: ' . $tracking_number]);

    }
}
