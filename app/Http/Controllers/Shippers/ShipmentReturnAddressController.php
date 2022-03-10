<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyBlacklisted;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\BookingType;
use App\Http\Models\ChargesModes;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDeliveryTypeStatus;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\DeliveryType;
use App\Http\Models\PaymentMode;
use App\Http\Models\Product;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShippingMode;
use App\Http\Models\ShippingModeSameDayTiming;
use App\Http\Models\ZoneClassCity;
use App\Jobs\ProcessShipmentBookingDB;
use App\Jobs\ProcessShipmentBookingDBPriority;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class ShipmentReturnAddressController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function excel_index(){

        $settings = GlobalSettings::where('type', 'shipper_return_address');
        if ($settings->exists()) {
            $settings = $settings->first();
            if($settings->text != NULL){
                $return_accounts = array_map('intval', explode(',', $settings->text));
                if(!in_array(session('user_id'),$return_accounts)){
                    return redirect()->route('cod.access_denied');
                }
            }
        }


        $booking_types = BookingType::where('id', 1)->get();
        $user = User::find(session('user_id'));
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        if(in_array(session('user_id'), [5982, 3324, 10104, 14110, 16292])) {
            $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        }
        else{
            $cities = City::where('id','!=',1244)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->pluck('name');
        }
        $products = Product::all();


        if($user->account_type_id == 1){
            $charges_modes = ChargesModes::whereIn('id', [4])->get();
            $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

            $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();

        }
        else{
            $delivery_types = DeliveryType::all();
            $charges_modes = ChargesModes::whereIn('id', [3])->get();
            $min_chargeable_weights = CorporateMinChargeableWeight::where('user_id', $user->id)->get();
            if(session('rate_type_id') != 3){
                $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
            }
            else{
                $user_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
            }
            $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->get();

        }
        if (in_array(4, $user_shipping_modes)) {
            $shipping_mode_same_day_timings = ShippingModeSameDayTiming::all();
        }
        else {
            $shipping_mode_same_day_timings = NULL;
        }

        if(session('user_id') == 10354) {
            $payment_modes = PaymentMode::whereIn('id', [1])->get();
        }
        else {
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

        if($user->account_type_id == 1){
            return view('client.shipment.book.return.excel_index')->with(['booking_types' => $booking_types, 'user' => $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes]);
        }
        else{
            return view('client.shipment.book.return.excel_index')->with(['booking_types' => $booking_types,'user'=> $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'delivery_types' => $delivery_types, 'charges_modes' => $charges_modes, 'min_chargeable_weights' => $min_chargeable_weights]);
        }

    }

    public function excel_store(Request $request)
    {

        $user_id = session('user_id');
        $account_type_id = session('account_type');
        if($account_type_id == 2){
            $rate_type_id = session('rate_type_id');
        }
        Validator::extend('phone_number', function($attribute, $value, $parameters) {
            if ($value) {
                $value = ShipperShipmentBookController::phone_number($value);

                if (preg_match('/^((\+92)|(92)|(0092))-{0,1}\d{3}-{0,1}\d{7}$|^\d{3}-{1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$|^\d{3}-\d{7}$|^\d{10}$/', $value)) {
                    return TRUE;
                }
                else {
                    return FALSE;
                }
            }
        });

        Validator::extend('origin_check', function($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if($service_type_id == 5){
                    return true;
                }
                $result = ShipperShipmentBookController::check_origin($value, $shipping_mode_id, $user_id);
                if($result){
                    return TRUE;
                }
                else{
                    return FALSE;
                }
            }
        });

        Validator::extend('destination_check', function($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if($service_type_id == 5){
                    return true;
                }
                $result = ShipperShipmentBookController::check_destination($value, $shipping_mode_id, $user_id, 1);
                if($result){
                    return TRUE;
                }
                else{
                    return FALSE;
                }
            }
        });

        /*Validator::extend('return_address_check', function($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $return_city_name = $data['return_city'];
            $vendor = $data['return_vendor'];
            $result = FALSE;
            if ($value) {

                $return_city = City::where('name', $return_city_name)->where('status', 1);
                if($return_city->exists()){
                    $return_city = $return_city->first();
                    $return_address =
                }


                if($result){
                    return TRUE;
                }

            }
        });*/

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

            'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
            'replacement_item_description' => 'Replacement Item Description',
            'replacement_item_quantity' =>'Replacement Item Quantity',

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
            'return_address' => 'Return Address',
            'return_contact_person' => 'Return Contact Person',
            'return_vendor' => 'Return Vendor',
            'return_phone_number' => 'Return Phone Number',
            'return_city' => 'Return City',
            'return_email_address' => 'Return Email Address',

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

        if($account_type_id == 1){
            $rules = [
                'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
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
                'item_quantity_1' => ['required_if:service_type_id,3','nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'item_insurance_1' => ['required_if:service_type_id,3','nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
                'item_price_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

                'item_product_type_id_2' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'item_description_2' => ['required_with:item_product_type_id_2,', 'nullable', 'between:0,1000'],
                'item_quantity_2' => ['required_with:item_product_type_id_2,','nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'item_insurance_2' => ['required_with:item_product_type_id_2,','nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
                'item_price_2' => ['required_with:item_product_type_id_2,','nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

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
                'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function($query) use($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
                })],
                'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'nullable', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
                'amount' => ['required_if:service_type_id,1,2', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
                'try_and_buy_charges' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
                // 'payment_mode_id' => ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
                //     $query->whereNotIn('id', [2]);
                // })],
                'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function($query) {
                    $query->whereIn('id', [4]);
                })],
                'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],



                'shipper_reference_number_1' => ['nullable', 'between:0,190'],
                'shipper_reference_number_2' => ['nullable', 'between:0,190'],
                'shipper_reference_number_3' => ['nullable', 'between:0,190'],
                'shipper_reference_number_4' => ['nullable', 'between:0,190'],
                'shipper_reference_number_5' => ['nullable', 'between:0,190'],
                'return_address' => ['required', 'between:1,255'],
                'return_contact_person' => ['required', 'between:1,100'],
                'return_vendor' => ['required', 'between:1,100'],
                'return_phone_number' => ['required', 'phone_number'],
                'return_email_address' => ['required', 'email', 'between:0,100'],
                'return_city' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 1)->where('status', 1)],

            ];
        }
        else{
            $rules = [
                'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
                    $query->where('user_id', $user_id);
                })->where('hidden', 0), 'origin_check'],
                'delivery_type_id' => ['required_if:service_type_id,1,2,3', 'integer', 'digits_between:1,10', Rule::exists('delivery_types', 'id')],
                'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function($query) {
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
                'item_quantity_1' => ['required_if:service_type_id,3','nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'item_insurance_1' => ['required_if:service_type_id,3','nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
                'item_price_1' => ['required_if:service_type_id,3', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

                'item_product_type_id_2' => ['nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'item_description_2' => ['required_with:item_product_type_id_2,', 'nullable', 'between:0,1000'],
                'item_quantity_2' => ['required_with:item_product_type_id_2,','nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'item_insurance_2' => ['required_with:item_product_type_id_2,','nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
                'item_price_2' => ['required_with:item_product_type_id_2,','nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],

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

                'return_address_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
                    $query->where('user_id', $user_id);
                })->where('hidden', 0)],

                'shipper_reference_number_1' => ['nullable', 'between:0,190'],
                'shipper_reference_number_2' => ['nullable', 'between:0,190'],
                'shipper_reference_number_3' => ['nullable', 'between:0,190'],
                'shipper_reference_number_4' => ['nullable', 'between:0,190'],
                'shipper_reference_number_5' => ['nullable', 'between:0,190'],
                'return_address' => ['required', 'between:1,255'],
                'return_contact_person' => ['required', 'between:1,100'],
                'return_vendor' => ['required', 'between:1,100'],
                'return_phone_number' => ['required', 'phone_number'],
                'return_email_address' => ['required', 'email', 'between:0,100'],
                'return_city' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 1)->where('status', 1)],
            ];
        }
        $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
        if($ccd_booking->exists()){
            $ccd_booking = $ccd_booking->first();
            $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
            if(in_array($user_id,$ccd_account_tags))
            {
                $rules['payment_mode_id']  = ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [3]);
                })];
            }
            else
            {
                $rules['payment_mode_id']  = ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [2, 3]);
                })];
            }
        }

        if($account_type_id == 2){
            if($rate_type_id == 3){
                $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_default_rate_statuses', 'shipping_mode_id')->where(function($query) use($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
                })];
            }
            else{
                $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function($query) use($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
                })];
            }
        }
        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        }
        if (isset($spreadsheet)) {


            $column_count = null;


            if ($account_type_id == 1){
                $column_count = 36;

                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'consignee_name', 4 => 'consignee_address', 5 => 'consignee_phone_number_1', 6 => 'consignee_phone_number_2', 7 => 'consignee_email_address', 8 => 'self_collection', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'special_instructions', 17 => 'estimated_weight', 18 => 'shipping_mode_id', 19 => 'same_day_timing_id', 20 => 'amount', 21 => 'payment_mode_id', 22 => 'charges_mode_id', 23 => 'pieces_quantity', 24 => 'shipper_reference_number_1', 25 => 'shipper_reference_number_2', 26 => 'shipper_reference_number_3', 27 => 'shipper_reference_number_4', 28 => 'shipper_reference_number_5', 29 => 'open_shipment', 30 => 'return_address', 31 => 'return_contact_person', 32 => 'return_vendor', 33 => 'return_phone_number', 34 => 'return_city', 35 => 'return_email_address'];
                $service_type_check_id = 1;
            }
            else if($account_type_id == 2){
                $column_count = 37;

                $fields = [0 => 'pickup_address_id', 1 => 'delivery_type_id', 2 => 'information_display', 3 => 'consignee_city_name', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'self_collection', 10 => 'order_id', 11 => 'order_date', 12 => 'item_product_type_id', 13 => 'item_description', 14 => 'item_quantity', 15 => 'item_insurance', 16 => 'item_price', 17 => 'special_instructions', 18 => 'estimated_weight', 19 => 'shipping_mode_id', 20 => 'same_day_timing_id', 21 => 'amount', 22 => 'payment_mode_id', 23 => 'charges_mode_id', 24 => 'pieces_quantity', 25 => 'shipper_reference_number_1', 26 => 'shipper_reference_number_2', 27 => 'shipper_reference_number_3', 28 => 'shipper_reference_number_4', 29 => 'shipper_reference_number_5', 30 => 'open_shipment', 31 => 'return_address', 32 => 'return_contact_person', 33 => 'return_vendor', 34 => 'return_phone_number', 35 => 'return_city', 36 => 'return_email_address'];

                $service_type_check_id = 1;
            }
            else{
                return redirect()->back()->with('error', 'Invalid Template Selected');
            }

            if (count($spreadsheet[0]) != $column_count){
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
                $service_type_check_id = $request->service_type_check_id;

            }

            $errors = array();
            $nsa_error = array();
            $order_ids = array();
            $order_id_row = array();
            $check = NonServiceArea::pluck('name')->toArray();
            $blacklist_errors = array();
            $blacklist_found_categories = array();

            if(Session::has('prefix')){
                $rules['order_id'] = ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'order_id')->where(function($query) use($user_id) {
                    $query->where('user_id', $user_id);
                })];
            }
            else{
                if(Session::has('restrict_order_id')){
                    $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })];
                }
                else{
                    $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
                }
            }

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if($account_type_id == 1){
                    if (!isset($row['charges_mode_id'])) {
                        $rows[$key]['charges_mode_id'] = 4;
                    }
                }
                else{
                    if (!isset($row['charges_mode_id'])) {
                        $rows[$key]['charges_mode_id'] = 3;
                    }
                    $shipping_mode_id = $row['shipping_mode_id'];
                    if($service_type_check_id != 5) {
                        if ($row['delivery_type_id'] == 2) {
                            $rules['delivery_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('corporate_delivery_type_statuses', 'delivery_type_id')->where(function ($query) use ($shipping_mode_id) {
                                $query->where('shipping_mode_id', $shipping_mode_id);
                            })];
                        }
                    }

                    if($rate_type_id == 3){
                        if(!isset($row['delivery_type_id']) || $row['delivery_type_id'] == null){
                            $row['delivery_type_id'] = 1;
                        }
                    }
                }
                if($service_type_check_id != null){
                    $rows[$key]['service_type_id'] = $service_type_check_id;
                    $row['service_type_id'] = $service_type_check_id;
                }
                else{
                    $rules['service_type_id'] = ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function($query) {
                        $query->whereNotIn('id', [4]);
                    })];
                }


                if(!isset($row['pieces_quantity']) || $row['pieces_quantity'] == null){
                    $row['pieces_quantity'] = 1;
                }

                $rows[$key]['pieces_quantity'] = $row['pieces_quantity'];

                if(!isset($row['self_collection']) || $row['self_collection'] == null){
                    $row['self_collection'] = 'no';
                }
                if(!isset($row['open_shipment']) || $row['open_shipment'] == null){
                    $row['open_shipment'] = 'no';
                }
                $rows[$key]['open_shipment'] = $row['open_shipment'];


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
                    if(Session::has('prefix')){
                        $length = strlen(session('prefix'));
                        $check_order_id = str_split($row['order_id'], $length);
                        if(session('prefix') != $check_order_id[0]){
                            $errors[$row_id]['order_id'] = 'In-Valid Order ID';
                        }
                        else{
                            if(!array_key_exists(1, $check_order_id)){
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
                            }
                            else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                            }
                        }
                    }

                    if($account_type_id == 2){
                        if($service_type_check_id != 5){
                            if($row['delivery_type_id'] == 2){
                                $allowed_delivery_type = CorporateDeliveryTypeStatus::where('user_id', $user_id);
                                if($allowed_delivery_type->exists()){
                                    $allowed_delivery_type = $allowed_delivery_type->where('shipping_mode_id', $row['shipping_mode_id'])->where('delivery_type_id', $row['delivery_type_id']);
                                    if(!$allowed_delivery_type->exists()){
                                        $errors[$row_id]['delivery_type_id'] = 'Selected Delivery Type is disabled';
                                    }
                                }
                                else{
                                    $errors[$row_id]['delivery_type_id'] = 'Selected Delivery Type is disabled';
                                }
                            }
                        }
                    }

                    if($row['service_type_id'] != 5){
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
//                                $nsa_error[$row_id]['msg'] = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                            }
                        }

                        if (!CityDelivery::where('city_id', $consignee_city->id)->where('booking_type_id', $row['service_type_id'])->where('shipping_mode_id', $row['shipping_mode_id'])->exists()) {
                            $errors[$row_id]['consignee_city_name'] = 'Delivery is not allowed for City: ' . $consignee_city->name . ' with Service Type ID #' . $row['service_type_id'] . ' and Shipping Mode ID #' . $row['shipping_mode_id'];
                        }
                        if(!$request->excel_blacklist){
                            $consignee_phone_number_1 = substr_replace($row['consignee_phone_number_1'], '-', 4, 0);
                            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
                            if($consignee_information->exists()){
                                $consignee_information = $consignee_information->first();
                                $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information->id);
                                if($manual_blacklist->exists()){
                                    $manual_blacklist = $manual_blacklist->first();
                                    $blacklist_setting_id = $manual_blacklist->blacklist_setting_id;
                                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                    if($blacklist_setting){
                                        if(!array_key_exists($blacklist_setting_id, $blacklist_found_categories)){
                                            $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                            $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                        }
                                    }
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if($blacklist->exists()){
                                        $blacklist = $blacklist->first();
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: '.$blacklist->shipments. ', Delivered: '.$blacklist->delivered . '('.$blacklist->delivered_ratio.'), Undelivered: '.$blacklist->undelivered.'('. $blacklist->undelivered_ratio .'), Return Confirmed: '.$blacklist->return . '('. $blacklist->return_ratio .')';
                                    }else{
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: 0, Delivered: 0, Undelivered: 0, Return Confirmed: 0';
                                    }
                                }
                                else{
                                    $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                                    if($blacklist->exists()){
                                        $blacklist = $blacklist->first();
                                        $blacklist_setting_id = $blacklist->blacklist_setting_id;
                                        $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                                        if($blacklist_setting){
                                            if(!array_key_exists($blacklist_setting_id, $blacklist_found_categories)){
                                                $blacklist_found_categories[$blacklist_setting_id]['message'] = $blacklist_setting->message;
                                                $blacklist_found_categories[$blacklist_setting_id]['color'] = $blacklist_setting->color;
                                            }
                                        }
                                        $blacklist_errors[$row_id]['msg'] = 'Total Shipments: '.$blacklist->shipments. ', Delivered: '.$blacklist->delivered . '('.$blacklist->delivered_ratio.'), Undelivered: '.$blacklist->undelivered.'('. $blacklist->undelivered_ratio .'), Return Confirmed: '.$blacklist->return . '('. $blacklist->return_ratio .')';
                                    }
                                }
                            }
                        }
                    }
                    else{
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
                    if(TRUE || empty($blacklist_errors)){
                        foreach ($rows as $key => $row) {
                            $row['user_id'] = $user_id;
                            $row['account_type_id'] = $account_type_id;
                            $row['nsas'] = $check;
                            $row['nsa'] = $request->excel_nsa;
                            if(session('user_type') == 2){
                                $row['substitute_user_id'] = Auth::id();
                            }
                            else{
                                $row['substitute_user_id'] = null;
                            }

                            if(Session::has('prefix')){
                                $row['prefix'] = session('prefix');
                            }
                            else{
                                $row['prefix'] = NULL;
                            }
                            $row['business_category_id'] = 1;

                            if ($row['service_type_id'] == 3 && $row['payment_mode_id'] == 4) {
                                $row['payment_mode_id'] == 1;
                            }
                            if($row['service_type_id'] != 5){
                                if ($row['payment_mode_id'] == 4) {
                                    $row['amount'] = 0;
                                }
                            }
                            $return_city = City::where('name', $row['return_city'])->first();
                            $return_address_id = NULL;
                            $return_address = UserShippingInfo::where('user_id', $user_id)->where('vendor', $row['return_vendor'])->where('city_id', $return_city->id);
                            if($return_address->exists()){
                                $return_address = $return_address->first();
                                $return_address_id = $return_address->id;
                            }
                            else{
                                $return_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $row['return_address'], $row['return_contact_person'], $row['return_vendor'], $row['return_phone_number'], $row['return_email_address'], $return_city->id,0);
                            }

                            $row['return_address_id'] = $return_address_id;
                            if ($user_id != 3324) {
                                dispatch(new ProcessShipmentBookingDB($row));
                            }
                            else {
                                dispatch(new ProcessShipmentBookingDBPriority($row));
                            }
                        }

                        return redirect()->back()->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                    }
                    else{
                        return view('client.shipment.book.blacklist')->with(['data' => $rows, 'blacklist_errors' => $blacklist_errors, 'blacklist_found_categories' => $blacklist_found_categories, 'service_type_check_id' => $service_type_check_id]);
                    }
                }
                else {
                    return view('client.shipment.book.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error, 'service_type_check_id' => $service_type_check_id]);
                }
            }
            else {
                if(in_array($user_id, [5982, 3324, 10104, 14110, 16292])) {
                    $cities = City::where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                }
                else{
                    $cities = City::where('status', 1)->where('id','!=',1244)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
                }
                $booking_types = BookingType::where('id', 1)->pluck('booking_type','id');
                $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                    $query->where('pickup', 1)->where('business_category_id', 1)->where('status', 1)->whereNotNull('zone_id');
                })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                $products = Product::pluck('product_name', 'id');



                if($account_type_id == 1){
                    $user_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();

                    $charges_modes = ChargesModes::whereIn('id' , [4])->pluck('charges_mode','id');
                }
                else{
                    if($rate_type_id != 3){
                        $delivery_types = DeliveryType::pluck('delivery_type','id');
                    }
                    else{
                        $delivery_types = DeliveryType::where('id',1)->pluck('delivery_type','id');
                    }
                    $charges_modes = ChargesModes::whereIn('id' , [3])->pluck('charges_mode','id');

                    if(session('rate_type_id') != 3){

                        $user_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
                    }
                    else{
                        $user_shipping_modes = CorporateDefaultRateStatus::where('user_id', session('user_id'))->where('status', 1)->pluck('shipping_mode_id')->toArray();
                    }
                    $charges_modes = ChargesModes::whereIn('id' , [3])->pluck('charges_mode','id');
                }

                $shipping_modes = ShippingMode::whereIn('id', $user_shipping_modes)->pluck('mode', 'id');

                if (in_array(4, $user_shipping_modes)) {
                    $shipping_mode_same_day_timings = ShippingModeSameDayTiming::pluck('timing', 'id');
                } else {
                    $shipping_mode_same_day_timings = NULL;
                }
                $ccd_booking = GlobalSettings::where('type', 'ccd_booking');
                if($ccd_booking->exists()){
                    $ccd_booking = $ccd_booking->first();
                    $ccd_account_tags = array_map('intval', explode(',', $ccd_booking->text));
                    if(!in_array(session('user_id'),$ccd_account_tags))
                    {
                        $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->pluck('mode', 'id');
                    }
                    else
                    {
                        $payment_modes = PaymentMode::whereNotIn('id', [3])->pluck('mode', 'id');
                    }
                }
                else{
                    $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->get();
                }
                //$payment_modes = PaymentMode::whereNotIn('id', [3])->pluck('mode', 'id');


                $city_name = array();
                foreach ($cities as $city) {
                    $city_name[$city->name] = $city->name;
                }

                if($account_type_id == 1){
                    return view('client.shipment.book.return.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'user_shipping_modes' => $user_shipping_modes, 'charges_modes' => $charges_modes, 'service_type_check_id' => $service_type_check_id]);
                }
                else{
                    return view('client.shipment.book.return.corporate_errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'booking_types' => $booking_types, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'shipping_modes' => $shipping_modes, 'shipping_mode_same_day_timings' => $shipping_mode_same_day_timings, 'payment_modes' => $payment_modes, 'user_shipping_modes' => $user_shipping_modes, 'charges_modes' => $charges_modes, 'service_type_check_id' => $service_type_check_id, 'delivery_types' => $delivery_types]);
                }

            }
        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function return_address_change_excel_index(){
        if(Session::has('shipment_return_address_change') && session('shipment_return_address_change') == 1){
            return view('client.shipment.return_address_change.index');
        }
        else{
            return redirect()->route('cod.access_denied');
        }
    }

    public function return_address_change_excel_store(Request $request){

        $user_id = session('user_id');
        $allowed_statuses = array(20,22,24,27,29,30,33,35,37,44,45,46,47,48, 60);

        Validator::extend('return_status_check', function ($attribute, $value, $parameters, $validator) use ($user_id, $allowed_statuses) {
            $data = $validator->getData();
            if(isset($data['tracking_number'])){
                $return_address_id = $data['return_address_id'];
                $tracking_number = $data['tracking_number'];
            }
            else{
                return false;
            }
            if ($value) {

                $shipment = Shipment::where('tracking_number', $tracking_number)->where('user_id', $user_id)->whereIn('shipper_status_id', $allowed_statuses)->whereNotNull('return_address_id');
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    if($shipment->shipper_status_id == 20){
                        $return_city = NULL;
                        $current_return_city = $shipment->return_address->city_id;
                        $return_address = UserShippingInfo::find($return_address_id);
                        if($return_address){
                            $return_city = $return_address->city_id;
                        }
                        else{
                            return false;
                        }
                        if($current_return_city == $return_city){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                        return true;
                    }
                } else {
                    return false;
                }
            }
        });

        Validator::extend('return_destination_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {

            $data = $validator->getData();
            if(isset($data['return_address_id'])){
                $return_address_id = $data['return_address_id'];
                $tracking_number = $data['tracking_number'];
            }
            else{
                return false;
            }
            if ($value) {

                $shipment = Shipment::where('tracking_number', $tracking_number)->where('user_id', $user_id)->whereNotNull('return_address_id');
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    if($shipment->return_address_id == null){
                        return false;
                    }
                    $current_return_city = $shipment->return_address->city_id;

                    $return_city = UserShippingInfo::find($return_address_id);
                    if($return_city){
                        $return_city = $return_city->city_id;
                    }
                    if($current_return_city == $return_city){
                        return true;
                    }
                    return false;
                } else {
                    return false;
                }
            }
        });

        $names = [
            'tracking_number' => 'Tracking Number',
            'return_address_id' => 'Return Address ID'
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
            'return_destination_check' => 'Return city not same!',
            'return_status_check' => 'Shipment not arrived at return destination!',
        ];

        $rules = [
            'tracking_number' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id, $allowed_statuses) {
                $query->where('user_id', $user_id)->whereIn('shipper_status_id', $allowed_statuses)->whereNotNull('return_address_id');
            }), 'return_status_check'],
            'return_address_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('hidden', 0);
            }), 'return_destination_check'],
        ];

        $fields = [0 => 'tracking_number', 1 => 'return_address_id'];

        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Return Address ID'];
        }

        if (isset($spreadsheet)) {

            $column_count = 2;

            if (count($spreadsheet[0]) != $column_count) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            else {
                unset($spreadsheet[0]);
            }
        }

        if (!empty($spreadsheet) || !isset($spreadsheet)) {
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
            $tracking_numbers = array();

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }

            }
            if(!empty($errors)){
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            }
            else{

                foreach($rows as $key => $row){
                    $shipment = Shipment::where('tracking_number', $row['tracking_number'])->whereIn('shipper_status_id', $allowed_statuses)->first();


                    $shipment->return_address_id = $row['return_address_id'];

                    $shipment->save();

                    $tracking_numbers[] = $shipment->tracking_number;
                }


                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));


                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            }

        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }


    }


}
