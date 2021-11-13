<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Shippers\ShipperReceivingSheetController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CorporateDeliveryTypeStatus;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\DonePayment;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\GulAhmedCities;
use App\Http\Models\GulAhmedPickupAddress;
use App\Http\Models\HR\Employee;
use App\Http\Models\InterceptReBookRequest;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\ReportingLocation;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Rider;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentPrebook;
use App\Http\Models\ShipmentShipperReference;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shopify\ShopifyInvoiceSetting;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\TelenorShipmentStatusEstimatedTime;
use App\Http\Models\ZoneClassCity;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\Types\Null_;
use SnappyImage;
use SnappyPDF;
use Validator;


class APIController extends Controller
{
    private $names = [
        'person_of_contact' => 'Person of Contact',
        'vendor' => 'Vendor',
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
        'self_collection' => 'Self Collection',
       
        'order_id' => 'Order ID',
        'order_date' => 'Order Date',
        'package_type' => 'Package Type',
        'special_instructions' => 'Special Instructions',
        'estimated_weight' => 'Estimated Weight',
        'shipping_mode_id' => 'Shipping Mode ID',
        'same_day_timing_id' => 'Same Day Timing ID',
        'amount' => 'Collection Amount',
        'payment_mode_id' => 'Payment Mode ID',

        'item_product_type_id' => 'Item Product Type ID',
        'item_description' => 'Item Description',
        'item_quantity' => 'Item Quantity',
        'item_insurance' => 'Item Insurance',
        'product_value' => 'Product Value',

        'replacement_item_product_type_id' => 'Replacement Item Product Type ID',
        'replacement_item_description' => 'Replacement Item Description',
        'replacement_item_quantity' => 'Replacement Item Quantity',

        'items' => 'Item(s)',
        'items.*.item_product_type_id' => 'Item Product Type ID',
        'items.*.item_description' => 'Item Description',
        'items.*.item_quantity' => 'Item Quantity',
        'items.*.item_insurance' => 'Item Insurance',
        'items.*.product_value' => 'Product Value',

        'tracking_number' => 'Tracking Number',
        'type' => 'Type',

        'origin_city_id' => 'Origin City ID',
        'destination_city_id' => 'Destination City ID',

        'tracking_numbers' => 'Tracking Numbers',
        'tracking_numbers.*' => 'Tracking Number',

        'receiving_sheet_id' => 'Receiving Sheet ID',

        'charges_mode_id' => 'Charges Mode ID',

        'pieces_quantity' => 'Pieces',

        'shipper_reference_number_1' => 'Shipper Reference Number 1',
        'shipper_reference_number_2' => 'Shipper Reference Number 2',
        'shipper_reference_number_3' => 'Shipper Reference Number 3',
        'shipper_reference_number_4' => 'Shipper Reference Number 4',
        'shipper_reference_number_5' => 'Shipper Reference Number 5',
        'open_shipment' => 'Open Shipment',
        'return_address_id' => 'Return Address ID',
        'product_picture' => 'Product Picture',
        'invoice_picture' => 'Invoice Picture',
        'damage_product_picture' => 'Demage Product Picture',
        'product_packaging_picture' => 'Product Packaging Picture',
        'actual_product_picture' => 'Actual Product Picture',
        'missing_product_picture' => 'Missing Product Picture',
        'product_cost' => 'Product Cost',
        'consignee_type' => 'Consignee Type',

    ];

    private $messages = [
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

        'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',

        'consignee_phone_number_1.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
        'consignee_phone_number_2.regex' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',

        'distinct' => ':attribute must not be Repeated.',

        'phone_number' => ':attribute format is Invalid, required Format is: (03000000000, +92-300-0000000, 300-0000000, 0300-0000000).',
        'origin_check' => 'Origin city not allowed, please contact your sales person!',
        'destination_check' => 'Destination city not allowed, please contact your sales person!',
        'destination_return_check' => 'Return city not allowed, please contact your sales person!',
    ];

    public static function phone_number($phone_number)
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
        }
        //Replace 92 with 0
        else if (substr($phone_number, 0, 2) == '92') {
            $phone_number = '0' . substr($phone_number, 2);
        }
        //Replace 0092 with 0
        else if (substr($phone_number, 0, 4) == '0092') {
            $phone_number = '0' . substr($phone_number, 4);
        }
        //Addition of 0
        else if (substr($phone_number, 0, 1) != '0') {
            $phone_number = '0' . $phone_number;
        }

        return $phone_number;
    }

    public function login(Request $request)
    {
        $rules = [
            'email_address' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $user = User::where('email', $request->input('email_address'));

            if ($user->exists()) {
                $user = $user->first();

                if ($user->blacklist == 1) {
                    return response()->json(['status' => 1, 'message' => 'Your Account is Blacklisted.']);
                } else if ($user->status != 3) {
                    return response()->json(['status' => 1, 'message' => 'Your Account is not Activated yet.']);
                } else if ($user->phone_number_verified == 0) {
                    return response()->json(['status' => 1, 'message' => 'Your Account phone number is not verified.']);
                } else if (Hash::check($request->input('password'), $user->password)) {
                    $information = array();

                    $information['id'] = $user->id;
                    $information['name'] = $user->name;
                    $information['account_type_id'] = $user->account_type_id;
                    $information['api_key'] = $user->api_token;

                    return response()->json(['status' => 0, 'message' => 'Logged In Succesfully', 'information' => $information]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'Invalid Password']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'No User with given Email Address']);
            }
        }
    }

    public function verify(Request $request)
    {
        return response()->json(['status' => 0, 'message' => 'API Key is Valid']);
    }

    public function pickup_addresses(Request $request)
    {
        $user_id = $request->user_id;

        $pickup_addresses = User::find($user_id)->shipping;

        if (count($pickup_addresses)) {
            $details = array();

            foreach ($pickup_addresses as $pickup_address) {
                if ($pickup_address->hidden == 0) {
                    $detail = array();

                    $detail['id'] = $pickup_address->id;
                    $detail['person_of_contact'] = $pickup_address->poc;
                    $detail['phone_number'] = $pickup_address->phone;
                    $detail['email_address'] = $pickup_address->email;
                    $detail['address'] = $pickup_address->pickup_address;
                    $detail['status'] = $pickup_address->status;
                    $detail['default'] = ($pickup_address->default_address) ? true : false;

                    $detail['city'] = array();

                    $city = $pickup_address->city;

                    if ($city->status && $city->zone_id) {
                        $detail['city']['id'] = $city->id;
                        $detail['city']['name'] = $city->name;

                        $details[] = $detail;
                    }
                }
            }

            if (!empty($details)) {
                return response()->json(['status' => 0, 'message' => 'Pickup Addresses', 'pickup_addresses' => $details]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No Pickup Address']);
            }
        } else {
            return response()->json(['status' => 1, 'message' => 'No Pickup Address']);
        }
    }

    public function pickup_address_add(Request $request)
    {
        $user_id = $request->user_id;

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            if ($value) {
                $value = $this->phone_number($value);

                if (preg_match('/^((\+92)|(92)|(0092))-{0,1}\d{3}-{0,1}\d{7}$|^\d{3}-{1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$|^\d{3}-\d{7}$|^\d{10}$/', $value)) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        $rules = [
            'person_of_contact' => ['required', 'between:1,190'],
            'vendor' => ['nullable', 'filled', 'between:0,190'],
            'phone_number' => ['required', 'phone_number'],
            'email_address' => ['required', 'email'],
            'address' => ['required', 'between:1,190'],
            'city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $city = City::find($request->input('city_id'));

            if (!$city->status) {
                return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('city_id') . ' is deactivated']);
            }

            if (!$city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('city_id') . ' is deactivated']);
            }

            if (!$city->pickup) {
                return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $request->input('city_id')]);
            }

            $person_of_contact = $request->input('person_of_contact');
            $vendor = $request->input('vendor');
            $phone_number = $this->phone_number($request->phone_number);
            $email_address = $request->input('email_address');
            $address = $request->input('address');
            $city_id = $request->input('city_id');

            $pickup_address = new UserShippingInfo();

            $pickup_address->user_id = $user_id;
            $pickup_address->poc = $person_of_contact;
            $pickup_address->vendor = $vendor;
            $pickup_address->phone = $phone_number;
            $pickup_address->email = $email_address;
            $pickup_address->pickup_address = $address;
            $pickup_address->city_id = $city_id;

            $pickup_address->save();

            $id = $pickup_address->id;

            return response()->json(['status' => 0, 'message' => 'Pickup Address has been added', 'id' => $id]);
        }
    }

    public function shipment_book(Request $request)
    {
        $user_id = $request->user_id;

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            if ($value) {
                $value = $this->phone_number($value);

                if (preg_match('/^((\+92)|(92)|(0092))-{0,1}\d{3}-{0,1}\d{7}$|^\d{3}-{1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$|^\d{3}-\d{7}$|^\d{10}$/', $value)) {
                    return true;
                } else {
                    return false;
                }
            }
        });
        Validator::extend('origin_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if($service_type_id == 5){
                    return true;
                }
                $result = ShipperShipmentBookController::check_origin($value, $shipping_mode_id, $user_id);
                if ($result) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        Validator::extend('destination_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if($service_type_id == 5){
                    return true;
                }
                $result = ShipperShipmentBookController::check_destination($value, $shipping_mode_id, $user_id, 2);
                if ($result) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        Validator::extend('destination_return_check', function ($attribute, $value, $parameters, $validator) use ($user_id) {
            $data = $validator->getData();
            $shipping_mode_id = $data['shipping_mode_id'];
            $service_type_id = $data['service_type_id'];
            if ($value) {
                if($service_type_id == 5){
                    return true;
                }
                $result = ShipperShipmentBookController::check_return_destination($value, $shipping_mode_id, $user_id);
                if ($result) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        $user_type = User::where('id', $user_id)->first();
        if ($user_type['account_type_id'] == 1) {
            $rules = [
                'service_type_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [4]);
                })],
                'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('hidden', 0);
                }), 'origin_check'],
                'return_address_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('hidden', 0);
                }), 'destination_return_check'],
                'information_display' => ['required_if:service_type_id,1,2,3', 'nullable', 'boolean'],
                'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1), 'destination_check'],
                'consignee_name' => ['required', 'between:1,100'],
                'consignee_address' => ['required', 'between:1,255'],
                'consignee_phone_number_1' => ['required', 'phone_number'],
                'consignee_phone_number_2' => ['nullable', 'filled', 'phone_number'],
                'consignee_email_address' => ['nullable', 'filled', 'email'],
                'self_collection' => ['nullable', 'boolean'],
                'order_date' => ['nullable', 'date_format:Y-m-d'],
                'package_type' => ['required_if:service_type_id,3', 'boolean'],
                'special_instructions' => ['nullable', 'filled', 'between:0,190'],
                'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],
                'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
                })],
                'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
                'amount' => ['required_if:service_type_id,1,2', 'nullable', 'numeric', 'min:0'],
                // 'payment_mode_id' => ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function ($query) {
                //     $query->whereNotIn('id', [3]);
                // })],
                'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function ($query) {
                    $query->whereIn('id', [4]);
                })],

                'item_product_type_id' => ['required_if:service_type_id,1,2,5', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'item_description' => ['required_if:service_type_id,1,2,5', 'between:0,1000'],
                'item_quantity' => ['required_if:service_type_id,1,2,5', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'item_insurance' => ['required_if:service_type_id,1,2,5', 'boolean'],
                'product_value' => ['required_if:item_insurance,1', 'integer', 'digits_between:1,20', 'between:1,100000'],

                'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],

                'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,1000'],
                'replacement_item_quantity' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'between:1,10000'],

                'try_and_buy_fees' => ['required_if:service_type_id,3', 'nullable', 'numeric', 'min:0'],
                'items' => ['required_if:service_type_id,3', 'array', 'min:1', 'max:5'],
                'items.*.item_product_type_id' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'items.*.item_description' => ['required_if:service_type_id,3', 'between:0,1000'],
                'items.*.item_quantity' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'items.*.item_insurance' => ['required_if:service_type_id,3', 'boolean'],
                'items.*.product_value' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,20', 'between:1,100000'],

                'shipper_reference_number_1' => ['nullable', 'between:0,190'],
                'shipper_reference_number_2' => ['nullable', 'between:0,190'],
                'shipper_reference_number_3' => ['nullable', 'between:0,190'],
                'shipper_reference_number_4' => ['nullable', 'between:0,190'],
                'shipper_reference_number_5' => ['nullable', 'between:0,190'],
                'open_shipment' => ['nullable', 'boolean'],

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
        } else {
            $rules = [
                'service_type_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('booking_types', 'id')->where(function ($query) {
                    $query->whereNotIn('id', [4]);
                })],
                'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('hidden', 0);
                }), 'origin_check'],
                'return_address_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('hidden', 0);
                }), 'destination_return_check'],
                'information_display' => ['required_if:service_type_id,1,2,3', 'nullable', 'boolean'],
                'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1), 'destination_check'],
                'consignee_name' => ['required', 'between:1,100'],
                'consignee_address' => ['required', 'between:1,255'],
                'consignee_phone_number_1' => ['required', 'phone_number'],
                'consignee_phone_number_2' => ['nullable', 'filled', 'phone_number'],
                'consignee_email_address' => ['nullable', 'filled', 'email'],
                'order_date' => ['nullable', 'date_format:Y-m-d'],
                'package_type' => ['nullable', 'boolean'],
                'special_instructions' => ['nullable', 'filled', 'between:0,190'],
                'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],

                'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
                'amount' => ['required_if:service_type_id,1,2,3', 'nullable', 'numeric', 'between:0,1000000'],
                // 'payment_mode_id' => ['required_if:service_type_id,1,2,3', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
                //     $query->whereNotIn('id', [3]);
                // })],
                'charges_mode_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function ($query) {
                    $query->whereIn('id', [3]);
                })],

                'item_product_type_id' => ['required_if:service_type_id,1,2,5', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'item_description' => ['required_if:service_type_id,1,2,5', 'between:0,500'],
                'item_quantity' => ['required_if:service_type_id,1,2,5', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'item_insurance' => ['required_if:service_type_id,1,2,5', 'boolean'],
                'product_value' => ['required_if:item_insurance,1', 'integer', 'digits_between:1,20', 'between:1,100000'],

                'replacement_item_product_type_id' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'replacement_item_description' => ['required_if:service_type_id,2', 'between:0,1000'],
                'replacement_item_quantity' => ['required_if:service_type_id,2', 'integer', 'digits_between:1,10', 'between:1,1000'],

                'items' => ['required_if:service_type_id,3', 'array'],
                'items.*.item_product_type_id' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'exists:products,id'],
                'items.*.item_description' => ['required_if:service_type_id,3', 'between:0,1000'],
                'items.*.item_quantity' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,10', 'between:1,10000'],
                'items.*.item_insurance' => ['required_if:service_type_id,3', 'boolean'],
                'items.*.product_value' => ['required_if:service_type_id,3', 'integer', 'digits_between:1,20', 'between:1,100000'],
                'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],

                'shipper_reference_number_1' => ['nullable', 'between:0,190'],
                'shipper_reference_number_2' => ['nullable', 'between:0,190'],
                'shipper_reference_number_3' => ['nullable', 'between:0,190'],
                'shipper_reference_number_4' => ['nullable', 'between:0,190'],
                'shipper_reference_number_5' => ['nullable', 'between:0,190'],
                'open_shipment' => ['nullable', 'boolean'],

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

            if ($user_type['corporate_rate_type_id'] == 3) {
                $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_default_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
                })];
            } else {
                $rules['delivery_type_id'] = ['required_if:service_type_id,1,2', 'integer', 'digits_between:1,10', 'exists:delivery_types,id'];
                $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
                })];
            }
        }

        $shipment_pre_book = ShipmentPrebook::where('user_id', $user_id);
        if ($shipment_pre_book->exists()) {
            $rules['order_id'] = ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })];
        } else {
            if($user_type['restrict_order_id'] == 1){
                $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })];
            }
            else{
                $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
            }
        }

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consignee_phone_number_1 = $this->phone_number($request->consignee_phone_number_1);

            if ($request->filled('consignee_phone_number_2')) {
                $consignee_phone_number_2 = $this->phone_number($request->consignee_phone_number_2);
            }

            if ($request->filled('open_shipment')) {
                $open_shipment = $request->input('open_shipment');
            } else {
                $open_shipment = 0;
            }

            $service_type_id = $request->input('service_type_id');
            if ($shipment_pre_book->exists()) {
                $shipment_pre_book = $shipment_pre_book->first();
                $length = strlen($shipment_pre_book->prefix);
                $check_order_id = str_split($request->input('order_id'), $length);
                if ($shipment_pre_book->prefix != $check_order_id[0]) {
                    return response()->json(['status' => 1, 'message' => 'In-Valid Order ID']);
                } else {
                    if (!array_key_exists(1, $check_order_id)) {
                        return response()->json(['status' => 1, 'message' => 'In-Valid Order ID']);
                    }
                }
            } else {
                $shipment_pre_book = null;
            }
            if ($service_type_id != 5) {
                $user_shipping_info = UserShippingInfo::find($request->input('pickup_address_id'));

                if (!$user_shipping_info->status) {
                    return response()->json(['status' => 1, 'message' => 'Pickup Address ID #' . $request->input('pickup_address_id') . ' is disabled']);
                }

                if (!$user_shipping_info->city->status) {
                    return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id . ' is deactivated']);
                }
                if (!$user_shipping_info->city->zone_id) {
                    return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id . ' is deactivated']);
                }

                if (!$user_shipping_info->city->pickup) {
                    return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $user_shipping_info->city_id]);
                }

                if ($service_type_id == 1 || $service_type_id == 2) {
                    if ($request->has('return_address_id') && $request->input('return_address_id') != null) {
                        $user_shipping_info_return = UserShippingInfo::find($request->input('return_address_id'));

                        if (!$user_shipping_info_return->status) {
                            return response()->json(['status' => 1, 'message' => 'Return Address ID #' . $request->input('return_address_id') . ' is disabled']);
                        }

                        if (!$user_shipping_info_return->city->status) {
                            return response()->json(['status' => 1, 'message' => 'Return Address\'s City ID #' . $user_shipping_info_return->city_id . ' is deactivated']);
                        }
                        if (!$user_shipping_info_return->city->zone_id) {
                            return response()->json(['status' => 1, 'message' => 'Return Address\'s City ID #' . $user_shipping_info_return->city_id . ' is deactivated']);
                        }
                    }
                }

                $consignee_city = City::find($request->input('consignee_city_id'));

                if (($request->input('consignee_city_id') == 1244 && $user_id != 5982)) {
                    return response()->json(['status' => 1, 'message' => 'User is not allowed to book from ' . $request->input('consignee_city_id')]);
                }

                if (!$consignee_city->status) {
                    return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id') . ' is deactivated']);
                }

                if (!$consignee_city->zone_id) {
                    return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id') . ' is deactivated']);
                }

                $pickup_city_id = $user_shipping_info->city_id;

                if ($request->input('consignee_city_id') != $pickup_city_id && $request->input('shipping_mode_id') == 4) {
                    return response()->json(['status' => 1, 'message' => 'Same Day Delivery is not available for Different City Shipment']);
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
                        if ((int) $request->input('amount') > $check_zone) {
                            return response()->json(['status' => 1, 'message' => 'Amount must be smaller then or equal to ' . $check_zone]);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => "Zone class does'nt exists"]);
                    }
                }

                if (!CityDelivery::where('city_id', $request->input('consignee_city_id'))->where('booking_type_id', $request->input('service_type_id'))->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
                    return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $request->input('consignee_city_id') . ' with Service Type ID #' . $request->input('service_type_id') . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
                }
            } else {
                $pickup_consignee_city = City::find($request->input('consignee_city_id'));
                if (!$pickup_consignee_city->status) {
                    return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $pickup_consignee_city->city_id . ' is deactivated']);
                }

                if (!$pickup_consignee_city->zone_id) {
                    return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $pickup_consignee_city->city_id . ' is deactivated']);
                }

                if (!in_array($user_id, [7762, 4758])) {
                    if (!$pickup_consignee_city->pickup) {
                        return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $pickup_consignee_city->city_id]);
                    }
                }

                $pickup_address_id_for_delivery = $request->input('pickup_address_id');
                $pickup_address_for_delivery = UserShippingInfo::find($pickup_address_id_for_delivery);
                $delivery_city = City::find($pickup_address_for_delivery->city_id);

                if (!$delivery_city->status) {
                    return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id') . ' is deactivated']);
                }

                if (!$delivery_city->zone_id) {
                    return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $request->input('consignee_city_id') . ' is deactivated']);
                }

                $pickup_city_id = $pickup_consignee_city->id;

                if ($request->input('consignee_city_id') != $pickup_city_id && $request->input('shipping_mode_id') == 4) {
                    return response()->json(['status' => 1, 'message' => 'Same Day Delivery is not available for Different City Shipment']);
                }

                if (!in_array($user_id, [7762, 4758])) {
                    if (!CityDelivery::where('city_id', $delivery_city->id)->where('booking_type_id', $request->input('service_type_id'))->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
                        return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $delivery_city->id . ' with Service Type ID #' . $request->input('service_type_id') . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
                    }
                }
            }

            if ($user_type['account_type_id'] == 2) {
                if ($request->input('delivery_type_id') == 2) {
                    $allowed_delivery_type = CorporateDeliveryTypeStatus::where('user_id', $user_id);
                    if ($allowed_delivery_type->exists()) {
                        $allowed_delivery_type = $allowed_delivery_type->where('shipping_mode_id', $request->input('shipping_mode_id'))->where('delivery_type_id', $request->input('delivery_type_id'));
                        if (!$allowed_delivery_type->exists()) {
                            return response()->json(['status' => 1, 'message' => 'Selected Delivery Type is disabled']);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => 'Selected Delivery Type is disabled']);
                    }
                }
            }
            if ($service_type_id == 5) {

                if ($request->filled('consignee_email_address')) {
                    $user_email_id = $request->input('consignee_email_address');
                } else {
                    $user = User::find($user_id);
                    $user_email_id = $user->email;
                }
                $pickup_city_id = $request->input('consignee_city_id');
                $pickup_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $request->input('consignee_address'), $request->input('consignee_name'), null, substr_replace($consignee_phone_number_1, '-', 4, 0), $user_email_id, $pickup_city_id, 0, true);

                $pickup_delivery_address_id = $request->input('pickup_address_id');
                $pickup_delivery_address = UserShippingInfo::find($pickup_delivery_address_id);

                $consignee_city_id = $pickup_delivery_address->city_id;
                $consignee_name = $pickup_delivery_address->poc;
                $consignee_address = $pickup_delivery_address->pickup_address;
                $consignee_phone_number_1 = $pickup_delivery_address->phone;
                $consignee_phone_number_2 = null;
                $consignee_email_address = $pickup_delivery_address->email;
                $information_display = true;
                if ($user_type['account_type_id'] == 1) {
                    $charges_mode_id = 4;
                } else {
                    $charges_mode_id = 3;
                }
                $payment_mode_id = 1;
                $self_collection = false;
                $delivery_type_id = 1;
            } else {
                $pickup_address_id = $request->input('pickup_address_id');
                $consignee_city_id = $request->input('consignee_city_id');

                if ($user_type['account_type_id'] == 2) {
                    $delivery_type_id = $request->input('delivery_type_id');
                    $consignee_city_name = City::where('id', $consignee_city_id)->first();
                    if ($delivery_type_id == 2) {
                        $consignee_address = 'TRAX Office ' . $consignee_city_name['name'];
                    } else {
                        $consignee_address = $request->input('consignee_address');
                    }
                } else {
                    $consignee_address = $request->input('consignee_address');
                }

                $consignee_name = $request->input('consignee_name');
                $consignee_phone_number_1 = substr_replace($consignee_phone_number_1, '-', 4, 0);

                if ($request->filled('consignee_phone_number_2')) {
                    $consignee_phone_number_2 = substr_replace($consignee_phone_number_2, '-', 4, 0);
                } else {
                    $consignee_phone_number_2 = null;
                }

                if ($request->filled('consignee_email_address')) {
                    $consignee_email_address = $request->input('consignee_email_address');
                } else {
                    $consignee_email_address = null;
                }
                $information_display = $request->input('information_display');

                if ($request->filled('charges_mode_id')) {
                    $charges_mode_id = $request->input('charges_mode_id');
                } else {
                    if ($user_type['account_type_id'] == 1) {
                        $charges_mode_id = 4;
                    } else {
                        $charges_mode_id = 3;
                    }
                }

                $payment_mode_id = $request->input('payment_mode_id');
                $self_collection = false;
                if ($service_type_id == 1 && $request->has('self_collection')) {
                    if ($request->input('self_collection') != null) {
                        if ($request->input('self_collection') == 1) {
                            $self_collection = true;
                        }
                    }
                }

            }

            $return_address_id = null;
            if ($service_type_id == 1 || $service_type_id == 2) {
                if ($request->filled('return_address_id')) {
                    $return_address_id = $request->input('return_address_id');
                }
            }

            if ($request->filled('order_id')) {
                $order_id = $request->input('order_id');
            } else {
                $order_id = null;
            }

            if ($request->input('package_type') == 1) {
                $package_type = true;
            } else {
                $package_type = false;
            }

            if ($request->filled('special_instructions')) {
                $special_instructions = $request->input('special_instructions');
            } else {
                $special_instructions = null;
            }

            $estimated_weight = $request->input('estimated_weight');
            $shipping_mode_id = $request->input('shipping_mode_id');

            if ($shipping_mode_id == 4) {
                $same_day_timing_id = $request->input('same_day_timing_id');
            } else {
                $same_day_timing_id = null;
            }

            if ($service_type_id == 3) {
                $try_and_buy_charges = $request->input('try_and_buy_fees');
                $amount = 0;
            } elseif ($service_type_id == 5) {
                $try_and_buy_charges = null;
                $amount = 0;
            } else {
                $try_and_buy_charges = null;
                $amount = $request->input('amount');
            }
            $pieces_quantity = 1;
            if ($service_type_id == 1 && $request->has('pieces_quantity')) {
                if ($request->input('pieces_quantity') != null) {
                    $pieces_quantity = $request->input('pieces_quantity');
                } else {
                    $pieces_quantity = 1;
                }
            }

            if ($service_type_id == 3 && $payment_mode_id == 4) {
                $payment_mode_id = 1;
            }

            if ($payment_mode_id == 4) {
                $amount = 0;
            }

            $business_category_id = 1;
            if ($user_type['account_type_id'] == 1) {
                $shipment_id = ShipperShipmentBookController::book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $self_collection, $business_category_id, $open_shipment, $return_address_id);
            } else {

                if ($user_type['corporate_rate_type_id'] == 3) {
                    $delivery_type_id = 1;
                }
                $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id);
            }
           

            if ($shipment_pre_book) {
                $tracking_number = ShipperShipmentBookController::generate_prefix_tracking_number($shipment_id, $order_id);
            } else {
                $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
            }
            if ($request->has('order_date')) {
                if ($request->order_date != null) {
                    $order_date = new ShipmentOrderDate();
                    $order_date->shipment_id = $shipment_id;
                    $order_date->order_date = $request->order_date;
                    $order_date->save();
                }
            }

            if ($request->has('shipper_reference_number_1') || $request->has('shipper_reference_number_2') || $request->has('shipper_reference_number_3') || $request->has('shipper_reference_number_4') || $request->has('shipper_reference_number_5')) {
                $shipper_reference = new ShipmentShipperReference();
                $shipper_reference->shipment_id = $shipment_id;
                if ($request->has('shipper_reference_number_1')) {
                    $shipper_reference->reference_1 = $request->shipper_reference_number_1;
                }
                if ($request->has('shipper_reference_number_2')) {
                    $shipper_reference->reference_2 = $request->shipper_reference_number_2;
                }
                if ($request->has('shipper_reference_number_3')) {
                    $shipper_reference->reference_3 = $request->shipper_reference_number_3;
                }
                if ($request->has('shipper_reference_number_4')) {
                    $shipper_reference->reference_4 = $request->shipper_reference_number_4;
                }
                if ($request->has('shipper_reference_number_5')) {
                    $shipper_reference->reference_5 = $request->shipper_reference_number_5;
                }
                $shipper_reference->save();
            }

            if ($service_type_id == 1 || $service_type_id == 5) {
                $item_product_type_id = $request->input('item_product_type_id');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                } else {
                    $item_description = null;
                }

                $item_quantity = $request->input('item_quantity');

                if ($request->input('item_insurance') == 1) {
                    $item_price = str_replace(',', '', $request->input('product_value'));
                    $item_insurance = true;
                } else {
                    $item_price = null;
                    $item_insurance = false;
                }

                $item_type = 0;

                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

                if ($pieces_quantity > 1) {
                    ShipperShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
                }
            } else if ($service_type_id == 2) {
                $item_product_type_id = $request->input('item_product_type_id');

                if ($request->filled('item_description')) {
                    $item_description = $request->input('item_description');
                } else {
                    $item_description = null;
                }

                $item_quantity = $request->input('item_quantity');

                if ($request->input('item_insurance') == 1) {
                    $item_price = str_replace(',', '', $request->input('product_value'));
                    $item_insurance = true;
                } else {
                    $item_price = null;
                    $item_insurance = false;
                }

                $item_type = 0;

                ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

                $replacement_item_product_type_id = $request->input('replacement_item_product_type_id');

                if ($request->filled('replacement_item_description')) {
                    $replacement_item_description = $request->input('replacement_item_description');
                } else {
                    $replacement_item_description = null;
                }

                $replacement_item_quantity = $request->input('replacement_item_quantity');

                $replacement_item_price = null;
                $replacement_item_insurance = null;
                $replacement_item_type = 1;

                ShipperShipmentBookController::add_item($shipment_id, $replacement_item_product_type_id, $replacement_item_description, $replacement_item_quantity, $replacement_item_price, $replacement_item_insurance, $replacement_item_type);
            } else if ($service_type_id == 3) {
                $try_and_buy_cod_amount = intval($try_and_buy_charges);
                foreach ($request->input('items') as $item) {
                    $item_product_type_id = $item['item_product_type_id'];

                    if (isset($item['item_description']) && !empty($item['item_description'])) {
                        $item_description = $item['item_description'];
                    } else {
                        $item_description = null;
                    }

                    $item_quantity = $item['item_quantity'];
                    $item_price = $item['product_value'];

                    if (isset($item['item_insurance']) && !empty($item['item_insurance'])) {
                        $item_insurance = true;
                    } else {
                        $item_insurance = false;
                    }

                    $item_type = 2;

                    $try_and_buy_cod_amount = $try_and_buy_cod_amount + intval($item_price);
                    ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);
                }
                $shipment_try_and_buy = Shipment::find($shipment_id);
                $shipment_try_and_buy->amount = $try_and_buy_cod_amount;
                $shipment_try_and_buy->save();
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
            if ($user_type['logo_status'] == 1) {
                $shipment = Shipment::find($shipment_id);
                $shipment->shipment_invoice_status = 1;
                $shipment->save();
            }
            $blacklist_message = null;
            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
            if ($consignee_information->exists()) {
                $consignee_information = $consignee_information->first();
                $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                if ($blacklist->exists()) {
                    $blacklist = $blacklist->first();
                    $blacklist_setting_id = $blacklist->blacklist_setting_id;
                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                    if ($blacklist_setting) {
                        $blacklist_message = $blacklist_setting->message;
                    }
                }
            }

            if ($msg_string != null && $blacklist_message == null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
                $msg_string = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'non_service_area' => $msg_string . ' In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222.']);
            }
            if ($msg_string == null && $blacklist_message != null) {
                return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'blacklisted_consignee' => $blacklist_message]);
            }
            if ($msg_string != null && $blacklist_message != null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
                $msg_string = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'non_service_area' => $msg_string . ' In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222.', 'blacklisted_consignee' => $blacklist_message]);
            }

            NotificationsController::send(2, $shipment_id);
            $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();
            $now = Carbon::now()->format('H:i:s');
            $cutofftime = $settingsfortime->setting_value.":00:00";
            if($now>$cutofftime)
            {
                NotificationsController::send(152, $shipment_id);
                NotificationsController::send(153, $shipment_id);
            }
            if ($request->has('pieces_quantity')) {
                if ($request->input('pieces_quantity') > 1) {
                    $video = array("https://www.youtube.com/watch?v=Uy0KAIx3xHQ", "Please view this video so that you can follow required process. In case process is not followed completely we will not be able to process this shipment ملٹیپل پیسز شپمینٹ بک یا پیک کرنے کا طریقہ اس وڈیو میں ضرور دیکھیں اگر شپمینٹ بتاۓ ہؤۓ طریقہ  کے تہت  ہینڈاؤرنہیں ہوئ تو ہم اس شپمینٹ کو پروسیس نہیں کریں گے  ");

                    return response()->json(['status' => 0, 'message' => 'Please view this video so that you can follow required process. In case process is not followed completely we will not be able to process this shipment!', 'tracking_number' => $tracking_number, 'video' => $video]);
                }
            }
            return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number]);

        }
    }

    public function shipment_air_waybill(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required_without:tracking_numbers', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'tracking_numbers' => ['required_without:tracking_number', 'array', 'min:1'],
            'tracking_numbers.*' => ['required_without:tracking_number', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;
            $tracking_numbers = $request->tracking_numbers;

            if ($tracking_number) {
                $shipments = Shipment::where('tracking_number', $tracking_number)->get();
            } else {
                $shipments = Shipment::whereIn('tracking_number', $tracking_numbers)->get();
            }

            $air_waybill = '';
            $valid = false;

            foreach ($shipments as $shipment) {
                if ($shipment->shipper_status_id == 1) {
                    $air_waybill .= ShipperShipmentBookController::air_waybill(4, $user_id, [$shipment->id]);

                    $valid = true;
                }
            }

            if ($valid) {
                $filename = 'air_waybill.jpg';

                if (!isset($request->type) || $request->type == 0) {
                    $image = SnappyImage::loadHTML($air_waybill);

                    $filename = 'air_waybill' . '.jpg';

                    return $image->setOption('disable-smart-width', true)->setOption('width', 1280)->download($filename);
                } else {
                    $pdf = SnappyPDF::loadHTML($air_waybill);

                    $filename = 'air_waybill' . '.pdf';

                    return $pdf->download($filename);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Already Received']);
            }
        }
    }

    public function shipment_status(Request $request)
    {
        $user_id = $request->user_id;

        $user_ids = MergedSisterAccountMapping::where('head_user_id', $user_id)->pluck('sister_user_id')->toArray();

        $user_ids[] = $user_id;

        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_ids) {
                $query->whereIn('user_id', $user_ids);
            })],
            'type' => ['required', 'boolean'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;
            $type = $request->type;

            $shipment = Shipment::whereIn('user_id', $user_ids)->where('tracking_number', $tracking_number)->first();

            if ($type == 0) {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();
                if($shipment_journey->status_reason_id)
                {
                    $reasonID = $shipment_journey->status_reason_id;
                    $reason = ShipmentStatusReason::find($reasonID)->name;
                }
                else
                {
                    $reason=null;
                }
                if ($shipment_journey) {
                    $current_status = $shipment_journey->shipment_status_shipper->name;
                } else {
                    $current_status = $shipment->status_shipper->name;
                }
            } else {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->whereNotNull('consignee_status_id')->latest()->first();
                if($shipment_journey->status_reason_id)
                {
                    $reasonID = $shipment_journey->status_reason_id;
                    $reason = ShipmentStatusReason::find($reasonID)->name;
                }
                else
                {
                    $reason=null;
                }           
                if ($shipment_journey) {
                    $current_status = $shipment_journey->shipment_status_consignee->name;
                } else {
                    $current_status = $shipment->status_consignee->name;
                }
            }

            return response()->json(['status' => 0, 'message' => 'Status of Shipment #' . $tracking_number, 'current_status' => $current_status, 'reason' => $reason]);
        }
    }

    public function shipment_track(Request $request)
    {
        $user_id = $request->user_id;

        $user_ids = MergedSisterAccountMapping::where('head_user_id', $user_id)->pluck('sister_user_id')->toArray();

        $user_ids[] = $user_id;

        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_ids) {
                $query->whereIn('user_id', $user_ids);
            })],
            'type' => ['required', 'boolean'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;
            $type = $request->type;

            $shipment = Shipment::whereIn('user_id', $user_ids)->where('tracking_number', $tracking_number)->first();

            $details = array();

            $details['tracking_number'] = $tracking_number;

            $details['order_id'] = $shipment->order_id;

            $shipper = $shipment->user;

            $details['shipper']['name'] = $shipper->name;

            $pickup = $shipment->pickup_address;

            $details['pickup']['origin'] = $pickup->city->name;

            if ($type == 0) {
                $details['shipper']['account_number'] = $shipper->id;
                $details['shipper']['phone_number_1'] = $shipper->phone;
                $details['shipper']['phone_number_2'] = $shipper->phone2;
                $details['shipper']['email'] = $shipper->email;
                $details['shipper']['city'] = $shipper->city->name;

                $details['pickup']['person_of_contact'] = $pickup->poc;
                $details['pickup']['phone_number'] = $pickup->phone;
                $details['pickup']['email'] = $pickup->email;
                $details['pickup']['address'] = $pickup->pickup_address;
            }

            $details['consignee']['name'] = $shipment->consignee_name;
            $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
            $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
            $details['consignee']['destination'] = $shipment->consignee_city->name;
            $details['consignee']['address'] = $shipment->consignee_address;

            foreach ($shipment->items as $item) {
                $item_details = array();

                $item_details['order_id'] = $shipment->order_id;
                $item_details['product_type'] = $item->product->product_name;
                $item_details['description'] = $item->description;
                $item_details['quantity'] = $item->quantity;

                $details['order_information']['items'][] = $item_details;
            }

            if ($type == 0) {
                $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                $details['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;
                $details['order_information']['amount'] = $shipment->amount;
                $details['order_information']['instructions'] = $shipment->special_instructions;
            }

            if ($type == 0) {
                foreach ($shipment->shipment_journey as $journey) {
                    if ($journey->verification) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
                        $journey_details['timestamp'] = Carbon::parse($journey->created_at)->timestamp;
                        $journey_details['status'] = $journey->shipment_status_shipper->name;

                        $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : null;

                        $details['tracking_history'][] = $journey_details;
                    }
                }
            } else {
                foreach ($shipment->shipment_journey as $journey) {
                    if ($journey->consignee_status_id != null) {
                        if ($journey->verification) {
                            $journey_details = array();

                            $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
                            $journey_details['timestamp'] = Carbon::parse($journey->created_at)->timestamp;
                            $journey_details['status'] = $journey->shipment_status_consignee->name;

                            $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : null;

                            $details['tracking_history'][] = $journey_details;
                        }
                    }
                }
            }

            return response()->json(['status' => 0, 'message' => 'Tracking of Shipment #' . $tracking_number, 'details' => $details]);
        }
    }

    public function shipment_charges(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;

            $shipment = Shipment::where('tracking_number', $tracking_number)->first();

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

            if ($shipment_journey) {
                $current_status_id = $shipment_journey->shipper_status_id;
            } else {
                $current_status_id = $shipment->shipper_status_id;
            }

            $charges = array();

            if ($shipment->packaging_material_request) {
                $charges['packaging_material_charges'] = $shipment->packaging_material_charges;
            } else if (in_array($current_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
                if ($shipment->weight_charges) {
                    $charges['weight_charges'] = $shipment->weight_charges;
                }

                if ($shipment->cash_handling_charges) {
                    $charges['cash_handling_charges'] = $shipment->cash_handling_charges;
                }

                if ($shipment->insurance_charges) {
                    $charges['insurance_charges'] = $shipment->insurance_charges;
                }

                if ($shipment->fuel_surcharge) {
                    $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
                }

                if ($shipment->replacement_charges) {
                    $charges['replacement_charges'] = $shipment->replacement_charges;
                }

                if ($shipment->try_and_buy_charges) {
                    $charges['try_and_buy_charges'] = $shipment->try_and_buy_charges;
                }

                if ($shipment->intercept_charges) {
                    $charges['intercept_charges'] = $shipment->intercept_charges;
                }

                if ($shipment->nsa_osa_charges) {
                    $charges['nsa_osa_charges'] = $shipment->nsa_osa_charges;
                }
            } else if (in_array($current_status_id, [20, 21, 22, 23, 24, 25, 44])) {
                if ($shipment->weight_charges) {
                    $charges['weight_charges'] = $shipment->weight_charges;
                }

                if ($shipment->insurance_charges) {
                    $charges['insurance_charges'] = $shipment->insurance_charges;
                }

                if ($shipment->fuel_surcharge) {
                    $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
                }

                if ($shipment->return_charges) {
                    $charges['return_charges'] = $shipment->return_charges;
                }

                if ($shipment->intercept_charges) {
                    $charges['intercept_charges'] = $shipment->intercept_charges;
                }
            } else {
                if ($shipment->weight_charges) {
                    $charges['weight_charges'] = $shipment->weight_charges;
                }

                if ($shipment->insurance_charges) {
                    $charges['insurance_charges'] = $shipment->insurance_charges;
                }

                if ($shipment->fuel_surcharge) {
                    $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
                }
            }

            if (!empty($charges)) {
                return response()->json(['status' => 0, 'message' => 'Charges of Shipment #' . $tracking_number, 'charges' => $charges]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No Charges']);
            }
        }
    }

    public function shipment_payment_status(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;

            $shipment = Shipment::where('tracking_number', $tracking_number)->first();

            $shipment_payment_journey = $shipment->shipment_payment_journey;

            if (!$shipment_payment_journey->isEmpty()) {
                $current_payment_status = $shipment_payment_journey->first()->status->name;

                return response()->json(['status' => 0, 'message' => 'Payment Status of Shipment #' . $tracking_number, 'current_payment_status' => $current_payment_status]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No Payment Status']);
            }
        }
    }

    public function shipment_payments(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;

            $shipment = Shipment::where('tracking_number', $tracking_number)->first();

            $done_payment_shipments = $shipment->done_payment_shipments;

            if (!$done_payment_shipments->isEmpty()) {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

                if ($shipment_journey) {
                    $current_status_id = $shipment_journey->shipper_status_id;
                } else {
                    $current_status_id = $shipment->shipper_status_id;
                }

                $charges = array();

                if ($shipment->packaging_material_request) {
                    $charges['packaging_material_charges'] = $shipment->packaging_material_charges;
                } else if (in_array($current_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
                    if ($shipment->weight_charges) {
                        $charges['weight_charges'] = $shipment->weight_charges;
                    }

                    if ($shipment->cash_handling_charges) {
                        $charges['cash_handling_charges'] = $shipment->cash_handling_charges;
                    }

                    if ($shipment->insurance_charges) {
                        $charges['insurance_charges'] = $shipment->insurance_charges;
                    }

                    if ($shipment->fuel_surcharge) {
                        $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
                    }

                    if ($shipment->replacement_charges) {
                        $charges['replacement_charges'] = $shipment->replacement_charges;
                    }

                    if ($shipment->try_and_buy_charges) {
                        $charges['try_and_buy_charges'] = $shipment->try_and_buy_charges;
                    }

                    if ($shipment->intercept_charges) {
                        $charges['intercept_charges'] = $shipment->intercept_charges;
                    }

                    if ($shipment->nsa_osa_charges) {
                        $charges['nsa_osa_charges'] = $shipment->nsa_osa_charges;
                    }
                } else if (in_array($current_status_id, [20, 21, 22, 23, 24, 25, 44])) {
                    if ($shipment->weight_charges) {
                        $charges['weight_charges'] = $shipment->weight_charges;
                    }

                    if ($shipment->insurance_charges) {
                        $charges['insurance_charges'] = $shipment->insurance_charges;
                    }

                    if ($shipment->fuel_surcharge) {
                        $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
                    }

                    if ($shipment->return_charges) {
                        $charges['return_charges'] = $shipment->return_charges;
                    }

                    if ($shipment->intercept_charges) {
                        $charges['intercept_charges'] = $shipment->intercept_charges;
                    }
                } else {
                    if ($shipment->weight_charges) {
                        $charges['weight_charges'] = $shipment->weight_charges;
                    }

                    if ($shipment->insurance_charges) {
                        $charges['insurance_charges'] = $shipment->insurance_charges;
                    }

                    if ($shipment->fuel_surcharge) {
                        $charges['fuel_surcharge'] = $shipment->fuel_surcharge;
                    }
                }

                $current_payment_status = null;

                $shipment_payment_journey = $shipment->shipment_payment_journey;

                if (!$shipment_payment_journey->isEmpty()) {
                    $current_payment_status = $shipment_payment_journey->first()->status->name;
                }

                $payments = array();

                foreach ($done_payment_shipments as $done_payment_shipment) {
                    $payment = array();

                    $payment['id'] = $done_payment_shipment->done_payment_id;
                    $payment['datetime'] = $done_payment_shipment->updated_at->toDateTimeString();
                    $payment['type'] = $done_payment_shipment->type;
                    $payment['amount'] = $done_payment_shipment->amount;
                    $payment['charges'] = $done_payment_shipment->charges;
                    $payment['gst'] = $done_payment_shipment->gst;
                    $payment['payable'] = $done_payment_shipment->payable;

                    $payments[] = $payment;
                }

                return response()->json(['status' => 0, 'message' => 'Payment(s) of Shipment #' . $tracking_number, 'charges' => $charges, 'current_payment_status' => $current_payment_status, 'payments' => $payments]);
            } else {
                return response()->json(['status' => 1, 'message' => 'No Payments']);
            }
        }
    }

    public function shipment_cancel(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;
            $type = $request->type;

            $shipment = Shipment::where('tracking_number', $tracking_number)->first();

            if ($shipment->shipper_status_id == 1) {
                $shipment->shipper_status_id = 17;
                $shipment->consignee_status_id = 17;

                $shipment->save();

                V2AdminPickupsController::cancel($shipment->id);

                ShipmentsJourneyController::add($shipment->id, 17, 17, null, 'Cancelled by Shipper', $user_id, null);

                return response()->json(['status' => 0, 'message' => 'Shipment #' . $tracking_number . ' is Cancelled']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Shipment\'s Status has already been changed']);
            }
        }
    }

    public function receiving_sheet_create(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_numbers' => ['required', 'array', 'min:1'],
            'tracking_numbers.*' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_numbers = $request->tracking_numbers;

            $shipment_ids = Shipment::whereIn('tracking_number', $tracking_numbers)->pluck('id')->toArray();

            $receiving_sheet = ShipperReceivingSheetController::create($shipment_ids, $user_id);

            if ($receiving_sheet['status'] == 0) {
                return response()->json(['status' => 0, 'message' => $receiving_sheet['success'], 'receiving_sheet_id' => $receiving_sheet['receiving_sheet_id']]);
            } else {
                return response()->json(['status' => 1, 'message' => $receiving_sheet['error']]);
            }
        }
    }

    public function receiving_sheet_view(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'receiving_sheet_id' => ['required', 'integer', Rule::exists('receiving_sheets', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'type' => ['nullable', 'boolean'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $receiving_sheet_id = $request->receiving_sheet_id;

            $receiving_sheet = ShipperReceivingSheetController::view($receiving_sheet_id, 4);

            if (!isset($request->type) || $request->type == 0) {
                $image = SnappyImage::loadHTML($receiving_sheet);

                $filename = 'receiving_sheet_' . $receiving_sheet_id . '.jpg';

                return $image->setOption('disable-smart-width', true)->download($filename);
            } else {
                $pdf = SnappyPDF::loadHTML($receiving_sheet);

                $filename = 'receiving_sheet_' . $receiving_sheet_id . '.pdf';

                return $pdf->download($filename);
            }
        }
    }

    public function cities(Request $request)
    {
        $user_id = $request->user_id;

        $cities = City::where('status', 1);

        if ($cities->exists()) {
            $cities = $cities->get();

            $details = array();

            foreach ($cities as $city) {
                $detail = array();

                $detail['id'] = $city->id;
                $detail['name'] = $city->name;

                $hub = $city->hub_city;
                $detail['hub'] = array();

                $detail['hub']['id'] = $hub->id;
                $detail['hub']['name'] = $hub->name;

                $zone = $city->zone;
                $detail['zone'] = array();

                $detail['zone']['id'] = $zone->id;
                $detail['zone']['name'] = $zone->name;

                $detail['pickup'] = ($city->pickup) ? true : false;
                $detail['delivery'] = array();

                foreach ($city->deliveries as $delivery) {
                    $detail['delivery'][$delivery->booking_type->booking_type][] = $delivery->shipping_mode->mode;
                }

                $details[] = $detail;
            }

            return response()->json(['status' => 0, 'message' => 'Pickup and Delivery Information of Cities', 'cities' => $details]);
        } else {
            return response()->json(['status' => 1, 'message' => ' No City Present']);
        }
    }

    public function charges_calculate(Request $request)
    {
        $user_id = $request->user_id;

        $user = User::find($user_id);

        $rules = [
            'service_type_id' => ['required', 'integer', 'digits_between:1,10', 'exists:booking_types,id'],
            'origin_city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
            'destination_city_id' => ['required', 'integer', 'digits_between:1,10', 'exists:cities,id'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })],
            'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
            'delivery_type_id' => ['nullable', 'integer', 'digits_between:1,10', 'exists:delivery_types,id'],
            'amount' => ['required', 'integer', 'digits_between:1,20', 'between:0,1000000'],
        ];

        if ($user['account_type_id'] == 1) {
            $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        } else {
            $rules['shipping_mode_id'] = ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        }

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $origin_city = City::find($request->input('origin_city_id'));

            if (!$origin_city->status) {
                return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('origin_city_id') . ' is deactivated']);
            }

            if (!$origin_city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('origin_city_id') . ' is deactivated']);
            }

            if (!$origin_city->pickup) {
                return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $request->input('origin_city_id')]);
            }

            $destination_city = City::find($request->input('destination_city_id'));

            if (!$destination_city->status) {
                return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('destination_city_id') . ' is deactivated']);
            }

            if (!$destination_city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'City ID #' . $request->input('destination_city_id') . ' is deactivated']);
            }

            if ($request->input('origin_city_id') != $request->input('destination_city_id') && $request->input('shipping_mode_id') == 4) {
                return response()->json(['status' => 1, 'message' => 'Same Day Delivery is not available for Different City Shipment']);
            }

            if (!CityDelivery::where('city_id', $request->input('destination_city_id'))->where('booking_type_id', $request->input('service_type_id'))->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
                return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $request->input('destination_city_id') . ' with Service Type ID #' . $request->input('service_type_id') . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
            }

            $information = array();

            $information['origin'] = array();

            $information['origin']['city'] = $origin_city->name;
            $information['origin']['zone'] = $origin_city->zone->name;

            $information['destination'] = array();

            $information['destination']['city'] = $destination_city->name;

            if ($origin_city->id == $destination_city->id) {
                $information['destination']['class'] = 'Local';
            } else {
                $zone_class_city = ZoneClassCity::where('zone_id', $origin_city->zone_id)->where('city_id', $destination_city->id);

                if (!$zone_class_city->exists()) {
                    return response()->json(['status' => 1, 'message' => 'Class is not yet defined for City ID #' . $request->input('destination_city_id') . ' with respect to Zone ID #' . $origin_city->zone_id]);
                } else {
                    $zone_class_city = $zone_class_city->first();

                    if ($zone_class_city->class == 0) {
                        $class = 'A';
                    } else if ($zone_class_city->class == 1) {
                        $class = 'B';
                    } else if ($zone_class_city->class == 2) {
                        $class = 'C';
                    } else if ($zone_class_city->class == 3) {
                        $class = 'D';
                    } else {
                        $class = '-';
                    }

                    $information['destination']['class'] = $class;
                }
            }

            $delivery_type_id = null;

            if ($user->account_type_id == 2) {
                if ($request->has('delivery_type_id')) {
                    $delivery_type_id = $request->input('delivery_type_id');
                } else {
                    $delivery_type_id = 1;
                }
            }

            $information['charges'] = array();

            $calculation = ShipmentChargesController::calculate_weight($user->account_type_id, $user->id, $request->input('shipping_mode_id'), $request->input('same_day_timing_id'), $delivery_type_id, $request->input('estimated_weight'), $origin_city->id, $origin_city->zone_id, $destination_city->id, 1, 0);

            if ($calculation) {
                $information['charges']['weight'] = $calculation['weight_charges'];
                $information['chargeable_weight'] = $calculation['chargeable_weight'];
            } else {
                $information['charges']['weight'] = 0;
                $information['chargeable_weight'] = 0;
            }

            $calculation = ShipmentChargesController::calculate_cash_handling($user->account_type_id, $user->id, $request->input('shipping_mode_id'), $request->input('amount'));

            if ($calculation) {
                $information['charges']['cash_handling'] = $calculation['cash_handling_charges'];
            } else {
                $information['charges']['cash_handling'] = 0;
            }

            $calculation = ShipmentChargesController::calculate_fuel_surcharge($user->account_type_id, $user->id, $request->input('shipping_mode_id'), $information['charges']['weight']);

            if ($calculation) {
                $information['charges']['fuel_surcharge'] = $calculation['fuel_surcharge'];
            } else {
                $information['charges']['fuel_surcharge'] = 0;
            }

            return response()->json(['status' => 0, 'message' => 'Charges Calculated', 'information' => $information]);
        }
    }

    public function shipment_consolidate(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_numbers' => ['required', 'array', 'min:2'],
            'tracking_numbers.*' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'default_tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_numbers = $request->tracking_numbers;
            $default_tracking_number = $request->default_tracking_number;

            if (!in_array($default_tracking_number, $tracking_numbers)) {
                $tracking_numbers[] = $default_tracking_number;
            }

            $shipments = Shipment::whereIn('tracking_number', $tracking_numbers)->get();

            $first_shipment = $shipments[0];

            $valid = true;

            foreach ($shipments as $shipment) {
                if (!($shipment->shipper_status_id == 1 && $shipment->booking_type_id == 1 && $first_shipment->booking_type_id == $shipment->booking_type_id && $first_shipment->consignee_name == $shipment->consignee_name && $first_shipment->consignee_address == $shipment->consignee_address && $first_shipment->consignee_phone_number_1 == $shipment->consignee_phone_number_1 && $first_shipment->consignee_city_id == $shipment->consignee_city_id)) {
                    $valid = false;

                    break;
                }
            }

            if ($valid) {
                $shipment_ids = Shipment::whereIn('tracking_number', $tracking_numbers)->pluck('id')->toArray();

                $default_shipment_id = Shipment::where('tracking_number', '=', $default_tracking_number)->first()->id;

                $consolidated_shipments = ConsolidationShipments::whereIn('shipment_id', $shipment_ids);

                if (!$consolidated_shipments->exists()) {
                    $consolidation = new Consolidation();

                    $consolidation->count = count($shipment_ids);
                    $consolidation->default_shipment_id = $default_shipment_id;

                    $consolidation->save();

                    foreach ($shipment_ids as $index => $shipment_id) {
                        $consolidation_shipment = new ConsolidationShipments();

                        $consolidation_shipment->consolidation_id = $consolidation->id;
                        $consolidation_shipment->shipment_id = $shipment_id;
                        $consolidation_shipment->order = $index + 1;

                        $consolidation_shipment->save();
                    }

                    return response()->json(['status' => 0, 'message' => 'Shipments Consolidated Successfully!']);
                } else {
                    $consolidated_shipment_ids = $consolidated_shipments->pluck('id')->toArray();

                    $tracking_numbers = Shipment::whereIn('id', $consolidated_shipment_ids)->pluck('tracking_number')->toArray();

                    return response()->json(['status' => 1, 'message' => 'These Shipment(s) are already in another Consolidation!', 'tracking_numbers' => $tracking_numbers]);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'These Shipment(s) cannot be Consolidated Together!']);
            }
        }
    }

    public function shipment_track_public(Request $request)
    {
        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', 'exists:shipments,tracking_number'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;

            $shipment = Shipment::where('tracking_number', $tracking_number)->first();

            if ($shipment->user->blacklist == 0) {
                $details = array();

                $details['tracking_number'] = $tracking_number;

                $shipper = $shipment->user;

                $details['shipper']['name'] = $shipper->name;

                $pickup = $shipment->pickup_address;

                $details['pickup']['origin'] = $pickup->city->name;

                $details['consignee']['name'] = $shipment->consignee_name;
                $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                $details['consignee']['destination'] = $shipment->consignee_city->name;
                $details['consignee']['address'] = $shipment->consignee_address;

                foreach ($shipment->items as $item) {
                    $item_details = array();

                    $item_details['order_id'] = $shipment->order_id;
                    $item_details['product_type'] = $item->product->product_name;
                    $item_details['description'] = $item->description;
                    $item_details['quantity'] = $item->quantity;

                    $details['order_information']['items'][] = $item_details;
                }

                foreach ($shipment->shipment_journey as $journey) {
                    if ($journey->verification) {
                        $journey_details = array();

                        $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
                        $journey_details['timestamp'] = Carbon::parse($journey->created_at)->timestamp;
                        $journey_details['status'] = $journey->shipment_status_shipper->name;

                        $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : null;

                        $details['tracking_history'][] = $journey_details;
                    }
                }

                return response()->json(['status' => 0, 'message' => 'Tracking of Shipment #' . $tracking_number, 'details' => $details]);
            } else {
                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => ['tracking_number' => 'Invalid Tracking Number']]);
            }
        }
    }

    public function return_confirmation_pending(Request $request)
    {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->join('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at', '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 'shipments.id')
                    ->where('consolidations.consolidation_id', '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = shipments.id)'));
            })
            ->select('shipments.tracking_number', 'oc.name as origin', 'dc.name as destination', 'shipments.order_id', 'h.name as hub', 'shipments.consignee_name', 'shipments.consignee_phone_number_1', 'shipments.consignee_phone_number_2', 'shipments.consignee_address', 'shipments.amount', 'sm.mode as shipping_mode', 'bt.booking_type as service_type', 'ss.name as status', 'shipments_journey.remarks as remarks', 'ssr.name as reason', 'shipments_journey.created_at as status_date', 'sj.created_at as arrival_date', 'shipments.nsa_osa_estimated_charges')
            ->whereIn('shipments.shipper_status_id', [12, 52])
            ->where('shipments.user_id', $request->user_id)
            ->groupBy('shipments.id')
            ->get();

        return response()->json(['status' => 0, 'data' => $shipments]);
    }

    public function return_confirmation_pending_update(Request $request)
    {
        $user_id = $request->user_id;
        $rules = [
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'status' => ['required', 'numeric', Rule::in(1, 2)],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            //status = 1 -> Return Confirm, Starus = 2 -> Re-attempt requested
            $tracking_number = $request->tracking_number;

            $shipment = Shipment::where('tracking_number', $tracking_number)->first();
            if ($shipment) {
                $status = $request->status;
                if ($status == 1) {
                    if ($shipment->booking_type_id == 5) {
                        return response()->json(['status' => 1, 'message' => 'Reverse pickup Shipment can\'t be marked as Return Confirm!']);
                    }
                    if ($shipment->shipper_status_id == 20) {
                        return response()->json(['status' => 1, 'message' => 'Shipment is already marked as Return Confirm!']);
                    }
                    if ($shipment->shipper_status_id == 52) {
                        return response()->json(['status' => 1, 'message' => 'Shipment is already marked as Re-attempt requested!']);
                    }
                    if (!$shipment->packaging_material_request) {
                        if ($shipment->shipper_status_id == 12) {
                            $shipment->shipper_status_id = 20;
                            $shipment->consignee_status_id = 20;
                            $shipment->save();
                            $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                            ShipmentsJourneyController::add($shipment->id, 20, 20, $shipment_history->status_reason_id, 'Marked by shipper - API', $user_id, null);
                            ShipmentChargesController::return ($shipment->id);

                            AdminFinanceController::add_payment($shipment->id, 1);
                            return response()->json(['status' => 0, 'message' => "Shipment successfully marked as Shipment - Return Confirm"]);
                        } else {
                            return response()->json(['status' => 1, 'message' => "Shipment is not ready for Return Confirm"]);
                        }
                    } else {
                        $shipment->shipper_status_id = 17;
                        $shipment->consignee_status_id = 17;
                        $shipment->save();
                        $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                        ShipmentsJourneyController::add($shipment->id, 17, 17, $shipment_history->status_reason_id, 'Marked by shipper - API', $user_id, null);
                        return response()->json(['status' => 0, 'message' => "Shipment successfully marked as Shipment - Cancelled"]);
                    }

                } else {
                    if ($shipment->shipper_status_id == 52) {
                        return response()->json(['status' => 1, 'message' => 'Shipment is already marked as Re-attempt requested!']);
                    }
                    if ($shipment->shipper_status_id != 12) {
                        return response()->json(['status' => 1, 'message' => 'Shipment is not ready for Re-attempt!']);
                    }
                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                    $shipment->shipper_status_id = 52;
                    $shipment->consignee_status_id = 52;
                    $shipment->save();
                    ShipmentsJourneyController::add($shipment->id, 52, 52, null, 'Marked by shipper - API', $user_id, null);
                    if ($journey) {
                        NotificationsController::send(33, $shipment->id);
                    }
                    return response()->json(['status' => 0, 'message' => "Shipment has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);

                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Shipment with this tracking number not found!']);
            }
        }

    }

    public function shipment_book_gul_ahmed(Request $request)
    {
        $user_id = $request->user_id;
        $user_type = User::where('id', $user_id)->first();
        $rules = [
            'warehouse_id' => ['required', Rule::exists('gul_ahmed_pickup_addresses', 'warehouse_id')],
            'consignee_city_name' => ['required', 'between:1,100', Rule::exists('gul_ahmed_cities', 'city_name')],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'consignee_phone_number_2' => ['nullable', 'filled', 'regex:/^[0][0-9]{10}$/'],
            'consignee_email_address' => ['nullable', 'filled', 'email'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],
            'special_instructions' => ['nullable', 'filled', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],
            'amount' => ['required', 'nullable', 'numeric', 'between:0,1000000'],
            'item_description' => ['required', 'between:0,500'],
            'item_quantity' => ['required', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],
            'order_id' => ['required', 'integer', 'between:0,1000000000000', Rule::unique('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'reference_number' => ['nullable', 'filled', 'between:0,100'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $service_type_id = 1;
            $shipment_pre_book = ShipmentPrebook::where('user_id', $user_id);
            if ($shipment_pre_book->exists()) {
                $shipment_pre_book = $shipment_pre_book->first();
                $length = strlen($shipment_pre_book->prefix);
                $check_order_id = str_split($request->input('order_id'), $length);
                if ($shipment_pre_book->prefix != $check_order_id[0]) {
                    return response()->json(['status' => 1, 'message' => 'In-Valid Order ID']);
                } else {
                    if (!array_key_exists(1, $check_order_id)) {
                        return response()->json(['status' => 1, 'message' => 'In-Valid Order ID']);
                    }
                }
            } else {
                $shipment_pre_book = null;
            }
            $warehouse = GulAhmedPickupAddress::where('warehouse_id', ($request->input('warehouse_id')))->first();
            $pickup_address_id = $warehouse->pickup_address_id;
            $user_shipping_info = UserShippingInfo::find($pickup_address_id);

            if (!$user_shipping_info->status) {
                return response()->json(['status' => 1, 'message' => 'Pickup Address ID #' . $pickup_address_id . ' is disabled']);
            }

            if (!$user_shipping_info->city->status) {
                return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id . ' is deactivated']);
            }

            if (!$user_shipping_info->city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'Pickup Address\'s City ID #' . $user_shipping_info->city_id . ' is deactivated']);
            }

            if (!$user_shipping_info->city->pickup) {
                return response()->json(['status' => 1, 'message' => 'Pickup is not allowed for City ID #' . $user_shipping_info->city_id]);
            }
            $gul_ahmed_city = GulAhmedCities::where('city_name', $request->input('consignee_city_name'))->first();
            $consignee_city = City::find($gul_ahmed_city->city_id);

            if (!$consignee_city->status) {
                return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $consignee_city->id . ' is deactivated']);
            }

            if (!$consignee_city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'Consignee City ID #' . $consignee_city->id . ' is deactivated']);
            }

            $pickup_city_id = $user_shipping_info->city_id;

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
                    if ((int) $request->input('amount') > $check_zone) {
                        return response()->json(['status' => 1, 'message' => 'Amount must be smaller then or equal to ' . $check_zone]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => "Zone class does'nt exists"]);
                }
            }

            if (!CityDelivery::where('city_id', $consignee_city->id)->where('booking_type_id', 1)->where('shipping_mode_id', 1)->exists()) {
                return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $consignee_city->id . ' with Service Type Regular and Shipping Mode Overnight']);
            }
            $consignee_city_id = $consignee_city->id;
            $delivery_type_id = 1;
            $consignee_city_name = City::where('id', $consignee_city_id)->first();
            if ($delivery_type_id == 2) {
                $consignee_address = 'TRAX Office ' . $consignee_city_name['name'];
            } else {
                $consignee_address = $request->input('consignee_address');
            }

            $consignee_name = $request->input('consignee_name');
            $consignee_phone_number_1 = substr_replace($request->input('consignee_phone_number_1'), '-', 4, 0);

            if ($request->filled('consignee_phone_number_2')) {
                $consignee_phone_number_2 = substr_replace($request->input('consignee_phone_number_2'), '-', 4, 0);
            } else {
                $consignee_phone_number_2 = null;
            }

            if ($request->filled('consignee_email_address')) {
                $consignee_email_address = $request->input('consignee_email_address');
            } else {
                $consignee_email_address = null;
            }
            $information_display = 1;

            $charges_mode_id = 3;

            $payment_mode_id = 1;
            $self_collection = false;
            if ($service_type_id == 1 && $request->has('self_collection')) {
                if ($request->input('self_collection') != null) {
                    if ($request->input('self_collection') == 1) {
                        $self_collection = true;
                    }
                }
            }
//            }

            if ($request->filled('order_id')) {
                $order_id = $request->input('order_id');
            } else {
                $order_id = null;
            }

            if ($request->filled('reference_number')) {
                $reference_number = $request->input('reference_number');
            } else {
                $reference_number = null;
            }

            $package_type = true;

            if ($request->filled('special_instructions')) {
                $special_instructions = $request->input('special_instructions');
            } else {
                $special_instructions = null;
            }

            $return_address_id = null;
            if ($service_type_id == 1 || $service_type_id == 2) {
                if ($request->filled('return_address_id')) {
                    $return_address_id = $request->input('return_address_id');
                }
            }

            $estimated_weight = $request->input('estimated_weight');
            $shipping_mode_id = 1;
            $same_day_timing_id = null;

            $try_and_buy_charges = null;
            $amount = $request->input('amount');
            $pieces_quantity = 1;
            if ($service_type_id == 1 && $request->has('pieces_quantity')) {
                if ($request->input('pieces_quantity') != null) {
                    $pieces_quantity = $request->input('pieces_quantity');
                } else {
                    $pieces_quantity = 1;
                }
            }

            if ($service_type_id == 3 && $payment_mode_id == 4) {
                $payment_mode_id = 1;
            }

            if ($payment_mode_id == 4) {
                $amount = 0;
            }

            $business_category_id = 1;
            $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $reference_number, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, 0, 0, $return_address_id);

            if ($shipment_pre_book) {
                $tracking_number = ShipperShipmentBookController::generate_prefix_tracking_number($shipment_id, $order_id);
            } else {
                $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
            }
            if ($request->has('order_date')) {
                if ($request->order_date != null) {
                    $order_date = new ShipmentOrderDate();
                    $order_date->shipment_id = $shipment_id;
                    $order_date->order_date = $request->order_date;
                    $order_date->save();
                }
            }
            $item_product_type_id = 1;

            if ($request->filled('item_description')) {
                $item_description = $request->input('item_description');
            } else {
                $item_description = null;
            }

            $item_quantity = $request->input('item_quantity');
            $item_price = null;
            $item_insurance = false;

            $item_type = 0;

            ShipperShipmentBookController::add_item($shipment_id, $item_product_type_id, $item_description, $item_quantity, $item_price, $item_insurance, $item_type);

            if ($pieces_quantity > 1) {
                ShipperShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
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
            if ($user_type['logo_status'] == 1) {
                $shipment = Shipment::find($shipment_id);
                $shipment->shipment_invoice_status = 1;
                $shipment->save();
            }
            $blacklist_message = null;
            $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
            if ($consignee_information->exists()) {
                $consignee_information = $consignee_information->first();
                $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
                if ($blacklist->exists()) {
                    $blacklist = $blacklist->first();
                    $blacklist_setting_id = $blacklist->blacklist_setting_id;
                    $blacklist_setting = BlacklistSetting::find($blacklist_setting_id);
                    if ($blacklist_setting) {
                        $blacklist_message = $blacklist_setting->message;
                    }
                }
            }

            if ($msg_string != null && $blacklist_message == null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
                $msg_string = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'non_service_area' => $msg_string . ' In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222.']);
            }
            if ($msg_string == null && $blacklist_message != null) {
                return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'blacklisted_consignee' => $blacklist_message]);
            }
            if ($msg_string != null && $blacklist_message != null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
                $msg_string = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'non_service_area' => $msg_string . ' In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222.', 'blacklisted_consignee' => $blacklist_message]);
            }

            NotificationsController::send(2, $shipment_id);
            if ($request->has('pieces_quantity')) {
                if ($request->input('pieces_quantity') > 1) {
                    $video = array("https://www.youtube.com/watch?v=Uy0KAIx3xHQ", "Please view this video so that you can follow required process. In case process is not followed completely we will not be able to process this shipment ملٹیپل پیسز شپمینٹ بک یا پیک کرنے کا طریقہ اس وڈیو میں ضرور دیکھیں اگر شپمینٹ بتاۓ ہؤۓ طریقہ  کے تہت  ہینڈاؤرنہیں ہوئ تو ہم اس شپمینٹ کو پروسیس نہیں کریں گے  ");

                    return response()->json(['status' => 0, 'message' => 'Please view this video so that you can follow required process. In case process is not followed completely we will not be able to process this shipment!', 'tracking_number' => $tracking_number, 'video' => $video]);
                }
            }
            return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number, 'reference_number' => $reference_number]);
        }
    }

    public function shipment_air_waybill_shopify_invoice(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_numbers' => ['required', 'array', 'min:1'],
            'tracking_numbers.*' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'orders' => ['required', 'array', 'min:1'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_numbers = $request->tracking_numbers;

            if ($tracking_numbers) {
                $shipments = Shipment::whereIn('tracking_number', $tracking_numbers)->get();
            }

            $air_waybill = '';
            $valid = false;
            $invoice = false;
            $shop_invoice_setting = ShopifyInvoiceSetting::where('user_id', $user_id);
            if ($shop_invoice_setting->exists()) {
                $shop_invoice_setting = $shop_invoice_setting->first();
                $invoice = true;
            }

            foreach ($shipments as $shipment) {
                if ($shipment->shipper_status_id == 1) {
                    $air_waybill .= ShipperShipmentBookController::air_waybill(4, $user_id, [$shipment->id]);

                    if (!empty($request->orders[$shipment->tracking_number]) && $invoice) {
                        $air_waybill .= ShopifyController::invoice_generate($user_id, $request->orders[$shipment->tracking_number], $shop_invoice_setting);
                    }
                    $valid = true;
                }
            }

            if ($valid) {
                $filename = 'air_waybill.jpg';

                if (!isset($request->type) || $request->type == 0) {
                    $image = SnappyImage::loadHTML($air_waybill);

                    $filename = 'air_waybill' . '.jpg';

                    return $image->setOption('disable-smart-width', true)->setOption('width', 1280)->download($filename);
                } else {
                    $pdf = SnappyPDF::loadHTML($air_waybill);

                    $filename = 'air_waybill' . '.pdf';

                    return $pdf->download($filename);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Already Received']);
            }
        }
    }

    public function shipment_track_order_id(Request $request)
    {
        $user_id = $request->user_id;

        $user_ids = MergedSisterAccountMapping::where('head_user_id', $user_id)->pluck('sister_user_id')->toArray();

        $user_ids[] = $user_id;

        $rules = [
            'order_id' => ['required', Rule::exists('shipments', 'order_id')->where(function ($query) use ($user_ids) {
                $query->whereIn('user_id', $user_ids);
            })],
            'type' => ['required', 'boolean'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $order_id = $request->order_id;
            $type = $request->type;

            $shipments = Shipment::whereIn('user_id', $user_ids)->where('order_id', $order_id)->get();

            $details = array();

            foreach ($shipments as $shipment) {
                $detail = array();

                $detail['tracking_number'] = $shipment->tracking_number;

                $detail['order_id'] = $shipment->order_id;

                $shipper = $shipment->user;

                $detail['shipper']['name'] = $shipper->name;

                $pickup = $shipment->pickup_address;

                $detail['pickup']['origin'] = $pickup->city->name;

                if ($type == 0) {
                    $detail['shipper']['account_number'] = $shipper->id;
                    $detail['shipper']['phone_number_1'] = $shipper->phone;
                    $detail['shipper']['phone_number_2'] = $shipper->phone2;
                    $detail['shipper']['email'] = $shipper->email;
                    $detail['shipper']['city'] = $shipper->city->name;

                    $detail['pickup']['person_of_contact'] = $pickup->poc;
                    $detail['pickup']['phone_number'] = $pickup->phone;
                    $detail['pickup']['email'] = $pickup->email;
                    $detail['pickup']['address'] = $pickup->pickup_address;
                }

                $detail['consignee']['name'] = $shipment->consignee_name;
                $detail['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                $detail['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                $detail['consignee']['destination'] = $shipment->consignee_city->name;
                $detail['consignee']['address'] = $shipment->consignee_address;

                foreach ($shipment->items as $item) {
                    $item_details = array();

                    $item_details['order_id'] = $shipment->order_id;
                    $item_details['product_type'] = $item->product->product_name;
                    $item_details['description'] = $item->description;
                    $item_details['quantity'] = $item->quantity;

                    $detail['order_information']['items'][] = $item_details;
                }

                if ($type == 0) {
                    $detail['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                    $detail['order_information']['shipping_mode'] = $shipment->shipping_mode->mode;
                    $detail['order_information']['amount'] = $shipment->amount;
                    $detail['order_information']['instructions'] = $shipment->special_instructions;
                }

                if ($type == 0) {
                    foreach ($shipment->shipment_journey as $journey) {
                        if ($journey->verification) {
                            $journey_details = array();

                            $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
                            $journey_details['timestamp'] = Carbon::parse($journey->created_at)->timestamp;
                            $journey_details['status'] = $journey->shipment_status_shipper->name;

                            $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : null;

                            $detail['tracking_history'][] = $journey_details;
                        }
                    }
                } else {
                    foreach ($shipment->shipment_journey as $journey) {
                        if ($journey->consignee_status_id != null) {
                            if ($journey->verification) {
                                $journey_details = array();

                                $journey_details['date_time'] = Carbon::parse($journey->created_at)->format('d/m/Y h:i A');
                                $journey_details['timestamp'] = Carbon::parse($journey->created_at)->timestamp;
                                $journey_details['status'] = $journey->shipment_status_consignee->name;

                                $journey_details['status_reason'] = ($journey->status_reason_id) ? $journey->shipment_status_reason->name : null;

                                $detail['tracking_history'][] = $journey_details;
                            }
                        }
                    }
                }

                $details[] = $detail;
            }

            return response()->json(['status' => 0, 'message' => 'Tracking of Shipment - Order ID #' . $order_id, 'details' => $details]);
        }
    }

    public function shipment_status_order_id(Request $request)
    {
        $user_id = $request->user_id;

        $user_ids = MergedSisterAccountMapping::where('head_user_id', $user_id)->pluck('sister_user_id')->toArray();

        $user_ids[] = $user_id;

        $rules = [
            'order_id' => ['required', Rule::exists('shipments', 'order_id')->where(function ($query) use ($user_ids) {
                $query->whereIn('user_id', $user_ids);
            })],
            'type' => ['required', 'boolean'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $order_id = $request->order_id;
            $type = $request->type;

            $shipments = Shipment::whereIn('user_id', $user_ids)->where('order_id', $order_id)->get();

            $details = array();

            foreach ($shipments as $shipment) {
                $detail = array();

                if ($type == 0) {
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

                    if ($shipment_journey) {
                        $current_status = $shipment_journey->shipment_status_shipper->name;
                    } else {
                        $current_status = $shipment->status_shipper->name;
                    }
                } else {
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->whereNotNull('consignee_status_id')->latest()->first();

                    if ($shipment_journey) {
                        $current_status = $shipment_journey->shipment_status_consignee->name;
                    } else {
                        $current_status = $shipment->status_consignee->name;
                    }
                }

                $detail['tracking_number'] = $shipment->tracking_number;
                $detail['status'] = $current_status;

                $details[] = $detail;
            }

            return response()->json(['status' => 0, 'message' => 'Status of Shipment(s) - Order ID #' . $order_id, 'details' => $details]);
        }
    }

    public function shipment_status_consingee_phone_number(Request $request)
    {
        $user_id = $request->user_id;

        $user_ids = MergedSisterAccountMapping::where('head_user_id', $user_id)->pluck('sister_user_id')->toArray();

        $user_ids[] = $user_id;

        $rules = [
            'phone_number' => ['required', Rule::exists('shipments', 'consignee_phone_number_1')->where(function ($query) use ($user_ids) {
                $query->whereIn('user_id', $user_ids);
            })],
            'type' => ['required', 'boolean'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $phone_number = $request->phone_number;
            $type = $request->type;

            $shipments = Shipment::whereIn('user_id', $user_ids)->where('consignee_phone_number_1', $phone_number)->get();

            $details = array();

            foreach ($shipments as $shipment) {
                $detail = array();

                if ($type == 0) {
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

                    if ($shipment_journey) {
                        $current_status = $shipment_journey->shipment_status_shipper->name;
                    } else {
                        $current_status = $shipment->status_shipper->name;
                    }
                } else {
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->whereNotNull('consignee_status_id')->latest()->first();

                    if ($shipment_journey) {
                        $current_status = $shipment_journey->shipment_status_consignee->name;
                    } else {
                        $current_status = $shipment->status_consignee->name;
                    }
                }

                $detail['tracking_number'] = $shipment->tracking_number;
                $detail['status'] = $current_status;

                $details[] = $detail;
            }

            return response()->json(['status' => 0, 'message' => 'Status of Shipment(s) - Consignee Phone Number #' . $phone_number, 'details' => $details]);
        }
    }

    public function payments(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required', 'array', 'min:1'],
            'tracking_number.*' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;

            $shipments = Shipment::whereIn('tracking_number', $tracking_number)->get();

            $data = array();

            foreach ($shipments as $shipment) {

                $account_type_id = $shipment->user->account_type_id;

                $done_payment_shipments = $shipment->done_payment_shipments;

                if (!$done_payment_shipments->isEmpty()) {
                    $data[$shipment->tracking_number] = array();

                    foreach ($done_payment_shipments as $done_payment_shipment) {
                        $details = array();

                        if ($done_payment_shipment->done_payment->status == 0) {
                            $details['payment_status'] = 'Processed';
                        } else if ($done_payment_shipment->done_payment->status == 1) {
                            $details['payment_status'] = 'Paid';
                        } else if ($done_payment_shipment->done_payment->status == 2) {
                            $details['payment_status'] = 'Reverted';
                        } else {
                            $details['payment_status'] = 'Unknown';
                        }
                        $details['billing_method'] = $shipment->user->account_type->name;
                        $details['payment_date'] = Carbon::parse($done_payment_shipment->done_payment->created_at)->toDateTimeString();
                        $details['payment_method'] = 'IBFT';
                        $details['payment_type'] = $done_payment_shipment->type;
                        if ($done_payment_shipment->type == 0) {
                            $details['payment_type'] = 'Delivered';
                        } else if ($done_payment_shipment->type == 1) {
                            $details['payment_type'] = 'Returned';
                        } else {
                            $details['payment_type'] = 'Adjusted';
                        }
                        $details['payment_id'] = $done_payment_shipment->done_payment->id;
                        if ($account_type_id == 2) {
                            $details['invoice_ids'] = array();
                            $details['invoice_ids'] = InvoiceShipment::where('shipment_id', $shipment->id)->groupBy('invoice_id')->pluck('invoice_id')->toArray();
                        }
                        $data[$shipment->tracking_number][] = $details;
                    }
                }
            }
            return response()->json(['status' => 0, 'payments' => $data]);
        }
    }

    public function invoice(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'id' => ['required', 'integer', 'digits_between:1,20'],
            'type' => ['required', 'integer', 'between:1,2'],
        ];

        //type = 1 => Invoice, 2 => Payment

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $data = array();

            $type = $request->type;

            if ($type == 1) {
                $invoice = Invoice::where('id', $request->id)->where('user_id', $user_id);
                if ($invoice->exists()) {
                    $invoice = $invoice->first();
                    $account_type_id = $invoice->shipper->account_type_id;
                    $data['billing_method'] = 'Corporate Invoicing Account';
                    $data['invoice_date'] = Carbon::parse($invoice->invoicing_date)->toDateTimeString();
                    $data['shipments'] = array();
                    $invoice_shipments = $invoice->invoice_shipments;

                    if (!$invoice_shipments->isEmpty()) {
                        foreach ($invoice_shipments as $invoice_shipment) {
                            $shipment = $invoice_shipment->shipment;
                            $details = array();
                            if ($invoice_shipment->type == 0) {
                                $details[$shipment->tracking_number]['payment_type'] = 'Delivered';
                            } else if ($invoice_shipment->type == 1) {
                                $details[$shipment->tracking_number]['payment_type'] = 'Returned';
                            } else {
                                $details[$shipment->tracking_number]['payment_type'] = 'Adjusted';
                            }
                            $details[$shipment->tracking_number]['weight_charges'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->weight_charges : 0);
                            $details[$shipment->tracking_number]['cash_handling_charges'] = (($account_type_id == 2 && $invoice_shipment->type == 0 && $invoice_shipment->charges != 0) ? $shipment->cash_handling_charges : 0);
                            $details[$shipment->tracking_number]['insurance_charges'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->insurance_charges : 0);
                            $details[$shipment->tracking_number]['return_charges'] = (($account_type_id == 2 && $invoice_shipment->type == 1 && $invoice_shipment->charges != 0) ? $shipment->return_charges : 0);
                            $details[$shipment->tracking_number]['fuel_surcharge'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->fuel_surcharge : 0);
                            $details[$shipment->tracking_number]['replacement_charges'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->replacement_charges : 0);
                            $details[$shipment->tracking_number]['try_and_buy_charges'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->try_and_buy_charges : 0);
                            $details[$shipment->tracking_number]['intercept_charges'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->intercept_charges : 0);
                            $details[$shipment->tracking_number]['osa_charges'] = (($account_type_id == 2 && $invoice_shipment->type != 2 && $invoice_shipment->charges != 0) ? $shipment->nsa_osa_charges : 0);
                            $details[$shipment->tracking_number]['adjustment_charges'] = (($account_type_id == 2 && $invoice_shipment->type == 2) ? $invoice_shipment->invoice_amount : 0);
                            $details[$shipment->tracking_number]['total_charges'] = $invoice_shipment->charges;
                            $details[$shipment->tracking_number]['gst'] = $invoice_shipment->gst;
                            $details[$shipment->tracking_number]['invoice_amount'] = $invoice_shipment->invoice_amount;
                            $data['shipments'][] = $details;

                        }
                        return response()->json(['status' => 0, 'payments' => $data]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => ['Invoice not found!']]);
                }

            } else {

                $done_payment = DonePayment::where('id', $request->id)->where('user_id', $user_id);
                if ($done_payment->exists()) {
                    $done_payment = $done_payment->first();
                    $account_type_id = $done_payment->shipper->account_type_id;
                    $data['billing_method'] = $done_payment->shipper->account_type->name;
                    $data['invoice_date'] = Carbon::parse($done_payment->created_at)->toDateTimeString();
                    $data['shipments'] = array();
                    $done_payment_shipments = $done_payment->done_payment_shipments;

                    if (!$done_payment_shipments->isEmpty()) {
                        foreach ($done_payment_shipments as $done_payment_shipment) {
                            $shipment = $done_payment_shipment->shipment;
                            $details = array();
                            if ($done_payment_shipment->type == 0) {
                                $details[$shipment->tracking_number]['payment_type'] = 'Delivered';
                            } else if ($done_payment_shipment->type == 1) {
                                $details[$shipment->tracking_number]['payment_type'] = 'Returned';
                            } else {
                                $details[$shipment->tracking_number]['payment_type'] = 'Adjusted';
                            }
                            $details[$shipment->tracking_number]['weight_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->weight_charges : 0);
                            $details[$shipment->tracking_number]['cash_handling_charges'] = (($account_type_id == 1 && $done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? $shipment->cash_handling_charges : 0);
                            $details[$shipment->tracking_number]['insurance_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->insurance_charges : 0);
                            $details[$shipment->tracking_number]['return_charges'] = (($account_type_id == 1 && $done_payment_shipment->type == 1 && $done_payment_shipment->charges != 0) ? $shipment->return_charges : 0);
                            $details[$shipment->tracking_number]['fuel_surcharge'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->fuel_surcharge : 0);
                            $details[$shipment->tracking_number]['replacement_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->replacement_charges : 0);
                            $details[$shipment->tracking_number]['try_and_buy_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->try_and_buy_charges : 0);
                            $details[$shipment->tracking_number]['intercept_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->intercept_charges : 0);
                            $details[$shipment->tracking_number]['osa_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->nsa_osa_charges : 0);
                            $details[$shipment->tracking_number]['adjustment_charges'] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $done_payment_shipment->charges : 0);
                            $details[$shipment->tracking_number]['total_charges'] = $done_payment_shipment->charges;
                            $details[$shipment->tracking_number]['gst'] = $done_payment_shipment->gst;
                            $details[$shipment->tracking_number]['invoice_amount'] = (($done_payment_shipment->type == 2) ? $done_payment_shipment->payable : 0);
                            $data['shipments'][] = $details;

                        }
                        return response()->json(['status' => 0, 'payments' => $data]);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => ['Invoice not found!']]);
                }
            }

            return response()->json(['status' => 0, 'payments' => $data]);

        }
    }

    public function bolt_login(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
            'device_token' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($employee->exists()) {
                $employee = $employee->first();
                if ($employee->request_status_id == 3) {
                    if ($employee->employee_type_id == 1) {
                        $admin = Admin::where('trax_id', $employee->trax_id);
                        if ($admin->exists()) {
                            $admin = $admin->first();
                            if ($admin->status == 0) {
                                return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                            }
                            if ($request->input('pin') == $employee->pin) {
                                $information['name'] = $employee->name;
                                $information['phone'] = $employee->phone_number;
                                $information['cnic'] = $employee->cnic;
                                $information['address'] = $employee->address;
                                $information['role'] = 'staff';

                                if ($employee->forget_pin_status == 1) {
                                    $information['forget_pin_status'] = $employee->forget_pin_status;
                                }
                                //Reporting Location
                                $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id');
                                if ($reporting_location->exists()) {
                                    $reporting_location = $reporting_location->first();
                                    $information['distance'] = $reporting_location->radius;
                                    $information['lat'] = $reporting_location->lat;
                                    $information['long'] = $reporting_location->long;
                                } else {
                                    $information['distance'] = 0;
                                    $information['lat'] = 0;
                                    $information['long'] = 0;
                                }

                                //Device Token
                                if ($request->has('device_token')) {
                                    $employee_device_token = EmployeeDeviceToken::where('employee_id', $admin->id)
                                        ->where('employee_type_id', 1);
                                    if ($employee_device_token->exists()) {
                                        $employee_device_token = $employee_device_token->first();
                                    } else {
                                        $employee_device_token = new EmployeeDeviceToken();
                                        $employee_device_token->employee_id = $admin->id;
                                        $employee_device_token->employee_type_id = 1;
                                    }
                                    $employee_device_token->device_token = $request->get('device_token');
                                    $employee_device_token->save();
                                }

                                //API_TOKEN
                                if ($admin->api_token) {
                                    $information['api_token'] = $admin->api_token;
                                } else {
                                    $api_token = uniqid(base64_encode(str_random(60)));

                                    $admin->api_token = $api_token;

                                    $admin->save();

                                    $information['api_token'] = $api_token;
                                }
                                return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                            } else {
                                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Pending for approval']);
                        }
                    } elseif ($employee->employee_type_id == 2) {
                        $rider = Rider::where('trax_id', $employee->trax_id);
                        if ($rider->exists()) {
                            $rider = $rider->first();
                            if ($rider->status == 0) {
                                return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                            }
                            if ($request->input('pin') == $employee->pin) {
                                $information['name'] = $employee->name;
                                $information['phone'] = $employee->phone_number;
                                $information['cnic'] = $employee->cnic;
                                $information['address'] = $employee->address;
                                $information['role'] = 'rider';

                                if ($employee->forget_pin_status == 1) {
                                    $information['forget_pin_status'] = $employee->forget_pin_status;
                                }

                                //Reporting Location
                                $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id');
                                if ($reporting_location->exists()) {
                                    $reporting_location = $reporting_location->first();
                                    $information['distance'] = $reporting_location->radius;
                                    $information['lat'] = $reporting_location->lat;
                                    $information['long'] = $reporting_location->long;
                                } else {
                                    $information['distance'] = 0;
                                    $information['lat'] = 0;
                                    $information['long'] = 0;
                                }

                                //Device Token
                                if ($request->has('device_token')) {
                                    $employee_device_token = EmployeeDeviceToken::where('employee_id', $rider->id)
                                        ->where('employee_type_id', 2);
                                    if ($employee_device_token->exists()) {
                                        $employee_device_token = $employee_device_token->first();
                                    } else {
                                        $employee_device_token = new EmployeeDeviceToken();
                                        $employee_device_token->employee_id = $rider->id;
                                        $employee_device_token->employee_type_id = 2;
                                    }
                                    $employee_device_token->device_token = $request->get('device_token');
                                    $employee_device_token->save();
                                }

                                //API_TOKEN
                                if ($rider->api_token) {
                                    $information['api_token'] = $rider->api_token;
                                } else {
                                    $api_token = uniqid(base64_encode(str_random(60)));

                                    $rider->api_token = $api_token;

                                    $rider->save();

                                    $information['api_token'] = $api_token;
                                }
                                return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                            } else {
                                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Pending for approval']);
                        }
                    } elseif ($employee->employee_type_id == 3) {
                        $retail_user = RetailUser::where('trax_id', $employee->trax_id);
                        if ($retail_user->exists()) {
                            $retail_user = $retail_user->first();

                            if ($retail_user->category == 2) {
                                $trax_center = RetailTraxCenter::find($retail_user->category_id);
                            } elseif ($retail_user->category == 1) {
                                $trax_center = RetailFranchise::find($retail_user->category_id);
                            }
                            if ($retail_user->status == 0) {
                                return response()->json(['status' => 1, 'message' => 'Account disabled, Please contact admin!']);
                            }
                            if ($request->input('pin') == $employee->pin) {
                                $information['name'] = $employee->name;
                                $information['phone'] = $employee->phone_number;
                                $information['cnic'] = $employee->cnic;
                                $information['address'] = $employee->address;
                                $information['pickup_address_id'] = $trax_center->pickup_address_id;
                                $information['role'] = 'retail_user';

                                if ($employee->forget_pin_status == 1) {
                                    $information['forget_pin_status'] = $employee->forget_pin_status;
                                }

                                //Reporting Location
                                $reporting_location = ReportingLocation::join('employees as e', 'reporting_locations.id', 'e.reporting_location_id');
                                if ($reporting_location->exists()) {
                                    $reporting_location = $reporting_location->first();
                                    $information['distance'] = $reporting_location->radius;
                                    $information['lat'] = $reporting_location->lat;
                                    $information['long'] = $reporting_location->long;
                                } else {
                                    $information['distance'] = 0;
                                    $information['lat'] = 0;
                                    $information['long'] = 0;
                                }

                                //Device Token
                                if ($request->has('device_token')) {
                                    $employee_device_token = EmployeeDeviceToken::where('employee_id', $retail_user->id)
                                        ->where('employee_type_id', 3);
                                    if ($employee_device_token->exists()) {
                                        $employee_device_token = $employee_device_token->first();
                                    } else {
                                        $employee_device_token = new EmployeeDeviceToken();
                                        $employee_device_token->employee_id = $retail_user->id;
                                        $employee_device_token->employee_type_id = 3;
                                    }
                                    $employee_device_token->device_token = $request->get('device_token');
                                    $employee_device_token->save();
                                }

                                //API_TOKEN
                                if ($retail_user->api_token) {
                                    $information['api_token'] = $retail_user->api_token;
                                } else {
                                    $api_token = uniqid(base64_encode(str_random(60)));

                                    $retail_user->api_token = $api_token;

                                    $retail_user->save();

                                    $information['api_token'] = $api_token;
                                }
                                return response()->json(['status' => 0, 'message' => 'Logged In Successfully', 'information' => $information]);
                            } else {
                                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Pending for approval']);
                        }
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'Pending for approval']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Invalid Credentials']);
            }

        }
    }

    private function shipment_google_location_name($id, $cities)
    {
        if (in_array($id, [1, 2, 53, 61, 63, 17, 19, 25, 18, 51])) {
            return $cities['origin']['city'];
        } elseif (in_array($id, [3])) {
            return $cities['origin']['hub'];
        } elseif (in_array($id, [4, 15, 11, 49, 56, 6, 7, 9, 12, 13, 52, 54, 55, 58, 59, 62, 20, 21, 22, 23, 24, 44, 47, 48, 57, 60, 50])) {
            return $cities['destination']['hub'];
        } elseif (in_array($id, [5, 8, 10, 14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
            return $cities['destination']['city'];
        } else {
            return $cities['origin']['city'];
        }
    }

    private function shipment_google_status_name($id)
    {
        if (in_array($id, [1])) {
            return 'TICKET_CREATED';
        } elseif (in_array($id, [2, 53, 61, 63])) {
            return 'PACKAGE_RECEIVED';
        } elseif (in_array($id, [3])) {
            return 'IN_TRANSIT';
        } elseif (in_array($id, [4])) {
            return 'RECEIVED_DELIVERY_LOCATION';
        } elseif (in_array($id, [5])) {
            return 'OUT_FOR_DELIVERY';
        } elseif (in_array($id, [17, 19])) {
            return 'CANCELLED';
        } elseif (in_array($id, [8, 10])) {
            return 'DELIVERY_FAILED';
        } elseif (in_array($id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
            return 'DELIVERED';
        } elseif (in_array($id, [15])) {
            return 'AVAILABLE_FOR_PICKUP';
        } elseif (in_array($id, [11, 49, 56])) {
            return 'DELAYED';
        } elseif (in_array($id, [6, 7, 9, 12, 13, 52, 54, 55, 58, 59, 62])) {
            return 'ON_HOLD';
        } elseif (in_array($id, [20, 21, 22, 23, 24, 44, 47, 48, 57, 60, 50])) {
            return 'RETURNING_TO_SENDER';
        } elseif (in_array($id, [25, 18, 51])) {
            return 'DELIVERED_TO_SENDER';
        } else {
            return 'ERROR';
        }
    }

    public function shipment_google_track(Request $request)
    {
        if (strstr(strtolower(gethostbyaddr($_SERVER['REMOTE_ADDR'])), 'google')) {
            $rules = [
                'TrackingNumber' => ['required'],
            ];

            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $current_status = array();

                $current_status['Status'] = 'ERROR';
                $current_status['Date'] = Carbon::now()->toIso8601String();
                $current_status['Error'] = 'Missing Tracking Number';

                return response()->json(['CurrentStatus' => $current_status]);
            } else {
                $rules = [
                    'TrackingNumber' => ['integer', 'digits_between:10,20'],
                ];

                $validate = Validator::make($request->all(), $rules, $this->messages);

                $validate->setAttributeNames($this->names);

                if ($validate->fails()) {
                    $current_status = array();

                    $current_status['Status'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'Invalid Tracking Number';

                    return response()->json(['CurrentStatus' => $current_status, 'TrackingNumber' => $request->TrackingNumber]);
                } else {
                    $tracking_number = $request->TrackingNumber;

                    $shipment = Shipment::where('tracking_number', $tracking_number);

                    if ($shipment->exists()) {
                        $shipment = $shipment->first();

                        if ($shipment->user->blacklist == 0) {
                            $output = array();

                            $output['TrackingNumber'] = $tracking_number;

                            $tracking_url = 'https://sonic.pk/tracking?tracking_number=' . $tracking_number;

                            $output['TrackingURL'] = $tracking_url;

                            $support_phone_numbers = ['+9221111118729'];

                            $output['SupportPhoneNumbers'] = $support_phone_numbers;

                            $cities = array();

                            $origin = $shipment->pickup_address->city;
                            $destination = $shipment->pickup_address->city;

                            $cities['origin']['city'] = $origin->name;
                            $cities['origin']['hub'] = $origin->hub_city->name;
                            $cities['destination']['city'] = $destination->name;
                            $cities['destination']['hub'] = $destination->hub_city->name;

                            $created_date = Carbon::parse($shipment->created_at)->toIso8601String();

                            $output['CreateDate'] = $created_date;

                            $current_status = array();
                            $transit_events = array();

                            $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1);

                            if ($shipments_journey->exists()) {
                                $shipments_journey = $shipments_journey->get();

                                $pickup = false;
                                $delivered = false;

                                if (!in_array($shipment->shipper_status_id, [1, 17])) {
                                    $pickup = true;

                                    if (in_array($shipment->shipper_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46])) {
                                        $delivered = true;
                                    }
                                }

                                foreach ($shipments_journey as $shipment_journey) {
                                    $transit_event = array();

                                    $transit_event['Status'] = $this->shipment_google_status_name($shipment_journey->shipper_status_id);
                                    $transit_event['Date'] = Carbon::parse($shipment_journey->created_at)->toIso8601String();
                                    $transit_event['Location'] = $this->shipment_google_location_name($shipment_journey->shipper_status_id, $cities);

                                    $transit_events[] = $transit_event;

                                    if ($pickup) {
                                        if (!isset($output['PickupDate']) && in_array($shipment_journey->shipper_status_id, [2, 53, 61, 63])) {
                                            $pickup_date = Carbon::parse($shipment_journey->created_at)->toIso8601String();

                                            $output['PickupDate'] = $pickup_date;
                                        }

                                        if ($delivered && in_array($shipment_journey->shipper_status_id, [14, 30, 36, 37])) {
                                            $delivered_date = Carbon::parse($shipment_journey->created_at)->toIso8601String();

                                            $output['DeliveredDate'] = $delivered_date;
                                        }
                                    }
                                }

                                $shipment_journey = $shipments_journey->last();

                                $current_status['Status'] = $this->shipment_google_status_name($shipment_journey->shipper_status_id);
                                $current_status['Date'] = Carbon::parse($shipment_journey->created_at)->toIso8601String();
                                $current_status['Location'] = $this->shipment_google_location_name($shipment_journey->shipper_status_id, $cities);
                            } else {
                                $current_status['Status'] = $this->shipment_google_status_name($shipment->shipper_status_id);
                                $current_status['Date'] = Carbon::parse($shipment->updated_at)->toIso8601String();
                                $current_status['Location'] = $this->shipment_google_location_name($shipment_journey->shipper_status_id, $cities);

                                $transit_event = array();

                                $transit_event['Status'] = $this->shipment_google_status_name($shipment->shipper_status_id);
                                $transit_event['Date'] = Carbon::parse($shipment->updated_at)->toIso8601String();
                                $transit_event['Location'] = $this->shipment_google_location_name($shipment_journey->shipper_status_id, $cities);

                                $transit_events[] = $transit_event;
                            }

                            $output['CurrentStatus'] = $current_status;
                            $output['TransitEvents'] = $transit_events;

                            return response()->json($output);
                        } else {
                            $current_status = array();

                            $current_status['Status'] = 'ERROR';
                            $current_status['Date'] = Carbon::now()->toIso8601String();
                            $current_status['Error'] = 'Invalid Tracking Number';

                            return response()->json(['CurrentStatus' => $current_status, 'TrackingNumber' => $request->TrackingNumber]);
                        }
                    } else {
                        $current_status = array();

                        $current_status['Status'] = 'ERROR';
                        $current_status['Date'] = Carbon::now()->toIso8601String();
                        $current_status['Error'] = 'Invalid Tracking Number';

                        return response()->json(['CurrentStatus' => $current_status, 'TrackingNumber' => $tracking_number]);
                    }
                }
            }
        } else {
            $current_status = array();

            $current_status['Status'] = 'ERROR';
            $current_status['Date'] = Carbon::now()->toIso8601String();
            $current_status['Error'] = 'Unauthorized Host';

            return response()->json(['CurrentStatus' => $current_status]);
        }
    }

    public function bolt_forget_pin(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($employee->exists()) {
                $employee = $employee->first();
                $pin = rand(1000, 9999);
                $employee->pin = $pin;
                $employee->forget_pin_status = 1;
                $employee->save();
                NotificationsController::bolt_forget_pin($employee->phone_number, $pin, $employee->name);
                return response()->json(['status' => 0, 'forget_pin_message' => 'Pin has been sent to your registered number']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function bolt_reset_pin(Request $request)
    {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'pin' => ['required', 'integer', 'digits:4'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee = Employee::where('phone_number', substr_replace($request->input('phone_number'), '-', 4, 0));
            if ($employee->exists()) {
                $employee = $employee->first();
                $employee->pin = $request->pin;
                $employee->forget_pin_status = 0;
                $employee->save();
                return response()->json(['status' => 0, 'reset_pin_message' => 'Pin has been reset successfully']);
            } else {
                return response()->json(['status' => 1, 'message' => 'Phone number not registered']);
            }
        }
    }

    public function store_device_token(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer'],
            'type_id' => ['required', 'integer'],
            'device_token' => ['required'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee_device_token = EmployeeDeviceToken::where('employee_id', $request->employee_id)->where('employee_type_id', $request->type_id);

            if ($employee_device_token->exists()) {
                $employee_device_token = $employee_device_token->first();
            } else {
                $employee_device_token = new EmployeeDeviceToken();
                $employee_device_token->employee_id = $request->employee_id;
                $employee_device_token->employee_type_id = $request->type_id;
            }
            $employee_device_token->device_token = $request->device_token;
            $employee_device_token->save();
            return response()->json(['status' => 0, 'message' => 'Device Token Store']);
        }
    }

    public function delete_device_token(Request $request)
    {
        $rules = [
            'employee_id' => ['required', 'integer'],
            'type_id' => ['required', 'integer'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $employee_device_token = EmployeeDeviceToken::where('employee_id', $request->employee_id)->where('employee_type_id', $request->type_id);
            if ($employee_device_token->exists()) {
                $employee_device_token->delete();
            }
            return response()->json(['status' => 0, 'message' => 'Device Token Deleted']);
        }
    }

    public function shipment_status_eta(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [
            'tracking_number' => ['required_without:order_id', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'order_id' => ['required_without:tracking_number', 'regex:/^[A-Za-z0-9\-\_]+$/u', 'max:20', Rule::exists('shipments', 'order_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if($request->tracking_number)
            {
                $tracking_number = $request->tracking_number;

                $shipment = Shipment::where('tracking_number', $tracking_number)->first();

                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

                if (!$shipment_journey) {
                    return response()->json(['status' => 1, 'message' => 'No data found!']);
                }

                $current_status_id = $shipment_journey->shipper_status_id;
                $current_status_time = $shipment_journey->created_at;
                $current_reason_id = $shipment_journey->status_reason_id;

                $details = array();

                $duration = TelenorShipmentStatusEstimatedTime::where('shipper_status_id', $current_status_id);
                if ($duration->exists()) {
                    $duration = $duration->first();
                    $estimated_eta = $duration->eta;

                    $actual_eta = Carbon::now()->diffInDays($current_status_time);

                    $difference_eta = $estimated_eta - $actual_eta;
                    if ($difference_eta < 0) {
                        $difference_eta = 0;
                    }
                    $details['tracking_number'] = $tracking_number;
                    $details['status'] = ShipmentStatus::find($current_status_id)->name;
                    $details['ETA'] = $difference_eta . ' days';

                    if ($difference_eta == 0) {
                        if ($current_reason_id != null) {
                            $details['reason'] = ShipmentStatusReason::find($current_reason_id)->name . '. Please call us at 021-111-118-729 for queries and updates.';
                        } else {
                            $details['reason'] = '';
                        }
                    }

                }

                if (!empty($details)) {
                    return response()->json(['status' => 0, 'message' => 'ETA of Shipment #' . $tracking_number, 'details' => $details]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'No data found!']);
                }
            }
            else if($request->order_id) 
            {
                $detail = array();
                $order_id = $request->order_id;
                $shipments = Shipment::where('order_id', $order_id)->where('user_id', $user_id)->get();

                foreach($shipments as $shipment)
                {
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();
                    
                    if (!$shipment_journey) {
                        return response()->json(['status' => 1, 'message' => 'No data found!']);
                    }
                    
                    $current_status_id = $shipment_journey->shipper_status_id;
                    $current_status_time = $shipment_journey->created_at;
                    $current_reason_id = $shipment_journey->status_reason_id;
    

                    $duration = TelenorShipmentStatusEstimatedTime::where('shipper_status_id', $current_status_id);
                    if ($duration->exists()) {
                        
                        $duration = $duration->first();
                        $estimated_eta = $duration->eta;
    
                        $actual_eta = Carbon::now()->diffInDays($current_status_time);
    
                        $difference_eta = $estimated_eta - $actual_eta;
                        if ($difference_eta < 0) {
                            $difference_eta = 0;
                        }
                        $details = array();
                        $details['order_id'] = $order_id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['status'] = ShipmentStatus::find($current_status_id)->name;
                        $details['ETA'] = $difference_eta . ' days';
    
                        if ($difference_eta == 0) {
                            if ($current_reason_id != null) {
                                $details['reason'] = ShipmentStatusReason::find($current_reason_id)->name . '. Please call us at 021-111-118-729 for queries and updates.';
                            } else {
                                $details['reason'] = '';
                            }
                        }
                        $detail[] = $details;
                    }
                }
                if (!empty($detail)) {
                    return response()->json(['status' => 0, 'message' => 'ETA of Order #' . $order_id, 'details' => $detail]);
                } else {
                    return response()->json(['status' => 1, 'message' => 'No data found!']);
                }
            }
        }
    }

    public function live_tracking(Request $request)
    {
        $database = app('firebase.database');
        $reference = $database->getReference('OnRouteShipments/in-transit');

        // if ($reference->getSnapshot()->getChild($request->tracking_id)->exists()) {
        //       $details = $database->getReference('OnRouteShipments/in-transit/' . $request->tracking_id)->getValue();
        //       return response()->json(['status' => 0, 'data' => $details]);

        // } else {
        //   return response()->json(['status' => 1, 'message' => 'No data found!']);

        // }

        if ($reference->getSnapshot()->getChild($request->tracking_id)->exists()) {
            $database->getReference('OnRouteShipments/in-transit/' . $request->tracking_id)->set(
                [
                    'runner_location_latitude' => $request->latitude,
                    'runner_location_longitude' => $request->longitude,
                ]
            );
            $details = $database->getReference('OnRouteShipments/in-transit/' . $request->tracking_id)->getValue();
            return response()->json(['status' => 0, 'data' => $details]);
        } else {
            $reference = $database->getReference('OnRouteShipments/in-transit')
                ->update([
                    $request->tracking_id => [
                        'runner_location_latitude' => $request->latitude,
                        'runner_location_longitude' => $request->longitude,
                    ]]);
            $details = $database->getReference('OnRouteShipments/in-transit/' . $request->tracking_id)->getValue();
            return response()->json(['status' => 0, 'data' => $details]);
        }
    }

    public function crm_request_create(Request $request)
    {
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->case_nature_type_id;
        $description = $request->description;
        $launched_by = 1;
        $user_id = $request->user_id;
        $substitute_user = SubstituteUser::find($user_id);
        if ($substitute_user) {
            $launched_by = 2;
        }

        $rules = [
            'tracking_number' => ['required_without:tracking_numbers', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'case_nature_id' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            if ($nature_id == 1 || $nature_id == 2) {
                //complaints


                  $crm_request_type = CrmRequestCaseNatureType::where('nature_id', $nature_id)->where('status_id',1)->pluck('id')->toArray();
                  if (in_array($complaint_id, $crm_request_type)) {
                    $rules = [
                      'description' => ['required'],
                    ];
                    $validate = Validator::make($request->all(), $rules, $this->messages);
            
                    $validate->setAttributeNames($this->names);
            
                    if ($validate->fails()) {
                        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                    } else {
                      $shipment = Shipment::where('tracking_number', $request->tracking_number);
                      if ($shipment->exists()) {
                          $shipment = $shipment->first();
                          $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description);
                          return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                      } else {
                          return response()->json(['status' => 1, 'message' => 'Tracking Number not found!']);
                      }
                    }
                  } else {
                      return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                  }
            } elseif ($nature_id == 4) {
                $crm_request_type = CrmRequestCaseNatureType::where('nature_id', $nature_id)->where('status_id',1)->pluck('id')->toArray();
                if (in_array($complaint_id, $crm_request_type)) {
                    if ($complaint_id == 26) {
                        $shipment = Shipment::where('tracking_number', $request->tracking_number);
                        if ($shipment->exists()) {
                            $shipment = $shipment->first();

                            $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description);
                            return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                        } else {
                            return response()->json(['status' => 1, 'message' => 'Tracking Number not found!']);
                        }

                    } elseif ($complaint_id == 21) {
                        $rules = [
                            'product_cost' => ['required', 'integer'],
                            'product_picture' => ['required', 'image'],
                            'invoice_picture' => ['required', 'image'],
                            'damage_product_picture' => ['required', 'image'],
                            'product_packaging_picture' => ['required', 'image'],
                            'actual_product_picture' => ['required', 'image'],
                            'damage_product_price' => ['required', 'integer'],
                            'description' => ['required'],

                        ];
                        $validate = Validator::make($request->all(), $rules, $this->messages);

                        $validate->setAttributeNames($this->names);

                        if ($validate->fails()) {
                            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                        } else {
                            $shipment = Shipment::where('tracking_number', $request->tracking_number);
                            if ($shipment->exists()) {
                                $shipment = $shipment->first();

                                $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_product_price );
                                return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                            } else {
                                return response()->json(['status' => 1, 'message' => 'Tracking Number not found!']);
                            }
                        }

                    } elseif ($complaint_id == 22) {

                        $rules = [
                            'product_cost' => ['required', 'integer'],
                            'product_picture' => ['required', 'image'],
                            'invoice_picture' => ['required', 'image'],
                            'missing_product_picture' => ['required', 'image'],
                            'product_packaging_picture' => ['required', 'image'],
                            'actual_product_picture' => ['required', 'image'],
                            'missing_product_price' => ['required', 'integer'],
                            'description' => ['required'],


                        ];
                        $validate = Validator::make($request->all(), $rules, $this->messages);

                        $validate->setAttributeNames($this->names);

                        if ($validate->fails()) {
                            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                        } else {
                            $shipment = Shipment::where('tracking_number', $request->tracking_number);
                            if ($shipment->exists()) {
                                $shipment = $shipment->first();
                                // CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);

                                $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), Null, Null, Null, Null, $request->file('missing_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture') , $request->missing_product_price);
                                return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                            } else {
                                return response()->json(['status' => 1, 'message' => 'Tracking Number not found!']);
                            }
                        }

                    } elseif ($complaint_id == 29 || $complaint_id == 25 || $complaint_id == 24 || $complaint_id == 23) {
                        $rules = [
                            'product_picture' => ['required', 'image'],
                            'invoice_picture' => ['required', 'image'],
                            'product_cost' => ['required', 'integer'],
                            'description' => ['required'],

                        ];
                        // $request->product_cost;
                        $validate = Validator::make($request->all(), $rules, $this->messages);

                        $validate->setAttributeNames($this->names);

                        if ($validate->fails()) {
                            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                        } else {
                            $shipment = Shipment::where('tracking_number', $request->tracking_number);
                            if ($shipment->exists()) {
                                $shipment = $shipment->first();
                                $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'));

                                // $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description);
                                return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                            } else {
                                return response()->json(['status' => 1, 'message' => 'Tracking Number not found!']);
                            }
                        }
                    }else{
                        return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'case_nature_id should be 1 (Complaints), 2 (Service Request) and 4 (Claims)']);

            }

        }

    }

    public function rcp_request_create(Request $request)
    {
        $user_id = $request->user_id;


        $rules = [
            'type' => ['required', 'integer', 'digits_between:1,3'],
            'tracking_number' => ['required_without:tracking_numbers', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        }
        else {
            $shipment = Shipment::where('tracking_number', $request->tracking_number);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $shipping_mode_id = $shipment->shipping_mode_id;
                Validator::extend('destination_check', function ($attribute, $value, $parameters, $validator) use ($user_id, $shipping_mode_id) {

                    if ($value) {
                        $result = ShipperShipmentBookController::check_destination($value, $shipping_mode_id, $user_id, 2);
                        if ($result) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                });

                if ($request->type == 1) {
                    if ($request->filled('remark')) {
                        $remark = $request->remark;
                    } else {
                        $remark = null;
                    }
                    if (!in_array($shipment->shipper_status_id, [20, 52])) {
                        if (!$shipment->packaging_material_request) {
                            Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                            $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                            ShipmentsJourneyController::add($shipment->id, 20, 20, $shipment_history->status_reason_id, $remark, $user_id, null);
                            //                NotificationsController::send(15, 0, $request->shipment_id);
                            //                NotificationsController::send(16, 0, $request->shipment_id);

                            ShipmentChargesController::return ($shipment->id);
                            AdminFinanceController::add_payment($shipment->id, 1);
                        } else {
                            Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
                            $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                            ShipmentsJourneyController::add($shipment->id, 17, 17, $shipment_history->status_reason_id, $remark, $user_id, null);
                            //                NotificationsController::send(15, 0, $request->shipment_id);
                            //                NotificationsController::send(16, 0, $request->shipment_id);
                        }
                        $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment->id);
                        if ($return_assign_shipment->exists()) {
                            $return_assign_shipment = $return_assign_shipment->latest()->first();
                            $return_assign_shipment->status = 0;
                            $return_assign_shipment->save();
                            
                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 2;
                            $return_assign_log->assigned_by = $user_id;
                            $return_assign_log->save();

                        }
                        return response()->json(['status' => 0, 'message' => 'Shipment successfully marked as Shipment - Return Confirm']);
                    }
                    return response()->json(['status' => 1, 'message' => 'Shipment is already updated']);

                }
                elseif ($request->type == 2) {

                    if ($request->filled('remark')) {
                        $remark = $request->remark;
                    } else {
                        $remark = null;
                    }
                    if ($shipment->shipper_status_id != 52) {
                        if ($shipment->shipper_status_id == 12) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                            Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 52, 'consignee_status_id' => 52]);

                            $last_reason = ShipmentsJourney::where('shipment_id', $shipment->id)->orderBy('id', 'DESC');
                            if ($last_reason->exists()) {
                                $last_reason = $last_reason->first();
                                $last_reason_id = $last_reason->status_reason_id;
                            } else {
                                $last_reason_id = null;
                            }
                            ShipmentsJourneyController::add($shipment->id, 52, 52, $last_reason_id, $remark, $user_id, null, null);

                            $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment->id)->latest()->first();
                            if ($return_assign_shipment) {
                                $return_assign_shipment->status = 0;
                                $return_assign_shipment->save();

                                $return_assign_log = new ReturnAssignedShipmentLogs();
                                $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                $return_assign_log->status = 5;
                                $return_assign_log->assigned_by = $user_id;
                                $return_assign_log->save();
                            }
                            if ($journey) {
                                NotificationsController::send(33, $shipment->id);
                            }
                            return response()->json(['status' => 0, 'message' => 'Shipment successfully updated as ( Re-Attempt - Requested )']);

                        }
                    }
                    return response()->json(['status' => 1, 'message' => 'Shipment not found!']);

                }
                elseif ($request->type == 3) {
                    $rules = [
                        'consignee_type' => ['required', 'integer', 'between:1,2'],
                    ];
                    $validate = Validator::make($request->all(), $rules, $this->messages);

                    $validate->setAttributeNames($this->names);

                    if ($validate->fails()) {
                        return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                    } else {
                        if ($request->consignee_type == 1) {

                            $rules = [
                                'consignee_address' => ['required', 'between:1,255'],
                                'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
                                'consignee_phone_number_2' => ['nullable', 'filled', 'regex:/^[0][0-9]{10}$/'],
                            ];

                            $validate = Validator::make($request->all(), $rules, $this->messages);

                            $validate->setAttributeNames($this->names);

                            if ($validate->fails()) {
                                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                            } else {

                                $phone_number = $this->phone_number($request->consignee_phone_number_1);

                                if ($request->filled('consignee_phone_number_2')) {
                                    $phone_number2 = $this->phone_number($request->consignee_phone_number_2);
                                } else {
                                    $phone_number2 = $shipment->consignee_phone_number_2;
                                }

                                if ($shipment->shipper_status_id == 12) {
                                    if ($shipment->consignee_address != $request->consignee_address || $shipment->consignee_phone_number_1 != $phone_number || $shipment->consignee_phone_number_2 != $phone_number2) {
                                        if ($shipment->intercepted == 1) {
                                            return response()->json(['status' => 1, 'message' => 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment->tracking_number]);
                                        } else {
                                            
                                            InterceptReBookRequest::create([
                                                'shipment_id' => $shipment->id,
                                                'consignee_city_id' => $shipment->consignee_city_id,
                                                'consignee_name' => $shipment->consignee_name,
                                                'consignee_address' => $request->consignee_address,
                                                'consignee_phone_number_1' => $phone_number,
                                                'consignee_phone_number_2' => $phone_number2,
                                                'consignee_email' => $shipment->consignee_email,
                                                'amount' => $shipment->amount,
                                                'shipper_id' => $user_id,
                                                'status' => 0,
                                            ]);

                                            $shipment->consignee_status_id = 54;
                                            $shipment->shipper_status_id = 54;
                                            $shipment->intercepted = 1;
                                            $shipment->save();
                                            ShipmentsJourneyController::add($request->shipment_id, 54, 54, NULL, NULL, $user_id, NULL);

                                            // ShipmentsJourneyController::add($shipment->id, 55, 55, NULL, NULL, $user_id, NULL);
                                            
                                            

                                            
                                            return response()->json(['status' => 0, 'message' => 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment->tracking_number]);

                                        }
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Shipment is already updated with Status : ' . $shipment->status_shipper->name . ' against Tracking Number: ' . $shipment->tracking_number]);
                                    }

                                } else {
                                    return response()->json(['status' => 1, 'message' => 'Shipment is already updated with Status : ' . $shipment->status_shipper->name . ' against Tracking Number: ' . $shipment->tracking_number]);
                                }
                            }

                        } else {
                            //different consignee
                            $rules = [
                              
                                'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1), 'destination_check'],
                                'consignee_name' => ['required', 'between:1,100'],
                                'consignee_address' => ['required', 'between:1,255'],
                                'consignee_phone_number_1' => ['required', 'regex:/^[0][0-9]{10}$/'],
                                'consignee_phone_number_2' => ['nullable', 'filled', 'regex:/^[0][0-9]{10}$/'],
                                'consignee_email_address' => ['nullable', 'filled', 'email'],
                                'amount' => ['required', 'nullable', 'numeric', 'between:0,1000000'],
                            ];

                            $validate = Validator::make($request->all(), $rules, $this->messages);

                            $validate->setAttributeNames($this->names);

                            if ($validate->fails()) {
                                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                            } else {

                                $phone_number = $this->phone_number($request->consignee_phone_number_1);

                                if ($request->filled('consignee_phone_number_2')) {
                                    $phone_number2 = $this->phone_number($request->consignee_phone_number_2);
                                } else {
                                    $phone_number2 = $shipment->consignee_phone_number_2;
                                }

                                if ($shipment->shipper_status_id == 12) {
                                    if ($shipment->consignee_city_id != $request->consignee_city_id || $shipment->consignee_name != $request->consignee_name || $shipment->consignee_address != $request->consignee_address || $shipment->consignee_phone_number_1 != $phone_number || $shipment->consignee_phone_number_2 != $phone_number2 || $shipment->consignee_email != $request->consignee_email || $shipment->amount != $request->amount) {
                                        if ($shipment->intercepted == 1) {
                                            return response()->json(['status' => 1, 'message' => 'Intercept/Re-Book is already requested against Tracking Number: ' . $shipment->tracking_number]);
                                        } else {
                                            
                                            $s_amount = str_replace(",", "", "$request->amount");
                                            $amount = (int) $s_amount;
                                            InterceptReBookRequest::create([
                                                'shipment_id' => $shipment->id,
                                                'consignee_city_id' => $request->consignee_city_id,
                                                'consignee_name' => $request->consignee_name,
                                                'consignee_address' => $request->consignee_address,
                                                'consignee_phone_number_1' => $phone_number,
                                                'consignee_phone_number_2' => $phone_number2,
                                                'consignee_email' => $request->consignee_email,
                                                'amount' => $amount,
                                                'shipper_id' => $user_id,
                                                'status' => 0,
                                            ]);

                                            $shipment->consignee_status_id = 54;
                                            $shipment->shipper_status_id = 54;
                                            $shipment->intercepted = 1;
                                            $shipment->save();

                                            ShipmentsJourneyController::add($shipment->id, 54, 54, null, null, $user_id, NULL);
                                            
                                            
                                            return response()->json(['status' => 0, 'message' => 'Intercept/Re-Book request submitted against Tracking Number: ' . $shipment->tracking_number]);
                                        }
                                    } else {
                                        return response()->json(['status' => 1, 'message' => 'Shipment is already book with same details against Tracking Number: ' . $shipment->tracking_number]);
                                    }
                                } else {
                                    return response()->json(['status' => 1, 'message' => 'Shipment is already updated with Status : ' . $shipment->status_shipper->name . ' against Tracking Number: ' . $shipment->tracking_number]);
                                }

                            }
                        }
                    }
                }
            } else {
                return response()->json(['status' => 1, 'message' => 'Tracking Number not found!']);
            }
        }
    }
}
