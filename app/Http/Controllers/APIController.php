<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\HBLKonnect\HblKonnectTransactionRetail;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransactionRetailNote;
use App\Http\Models\Admin\HBLKonnect\RetailNoteHblKonnectTransaction;
use App\Http\Models\Admin\HBLKonnect\RetailNoteHblKonnectTransactionRetail;
use App\Http\Models\Admin\RcpAssignedAgent;
use App\Http\Models\Admin\RcpAssignedShipment;
use App\Http\Models\Admin\RcpAssignedShipmentLog;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\ReceivingSheetPrintStatus;
use App\GuestApiToken;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\Admin\OneLink\OneLinkPaymentChargesRange;
use App\Http\Controllers\Admins\FTLController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Shippers\ShipperReceivingSheetController;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use App\Http\Models\Admin\BookingDestinationMappingKeyword;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\HBLKonnect\HblKonnectDeliveryNote;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransaction;
use App\Http\Models\Admin\HBLKonnect\HblKonnectTransactionDeliveryNote;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\OneLink\OneLink;
use App\Http\Models\Admin\OneLink\OneLinkOutForDeliveryShipmentPayment;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\TelenorOtherCouriers;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CorporateDeliveryTypeStatus;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentCalculation;
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
use App\Http\Models\Shipper\ReturnSheet;
use App\Http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentPrebook;
use App\Http\Models\ShipmentReplacementParcelImage;
use App\Http\Models\ShipmentShipperReference;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\ReturnSheetShipments;
use App\Http\Models\Shopify\ShopifyInvoiceSetting;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\SubstituteUserShipment;
use App\Http\Models\TelenorShipmentStatusEstimatedTime;
use App\Http\Models\ZoneClassCity;
use App\ReturnConfirmationPendingSmsAttempt;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\PseudoTypes\False_;
use phpDocumentor\Reflection\Types\Null_;
use SnappyImage;
use SnappyPDF;
use Validator;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\ReceivingSheetShipment;
use App\Jobs\ProcessGulAhmedShipmentConfirmation;
use Vectorface\Whip\Whip;

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
        //        dd($request);
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
            'brand_name' => ['string'],
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
            $brand_name = $request->input('brand_name');
            //dd($brand_name);
            $pickup_address = new UserShippingInfo();

            $pickup_address->user_id = $user_id;
            $pickup_address->poc = $person_of_contact;
            $pickup_address->vendor = $vendor;
            $pickup_address->phone = $phone_number;
            $pickup_address->email = $email_address;
            $pickup_address->pickup_address = $address;
            $pickup_address->city_id = $city_id;
            $pickup_address->pickup_brand_name = $brand_name;

            $pickup_address->save();

            $id = $pickup_address->id;

            return response()->json(['status' => 0, 'message' => 'Pickup Address has been added', 'id' => $id]);
        }
    }

    public function shipment_book(Request $request)
    {
        /********************************NOTE********************************/
        /*This API is also using from Trax App Booking Form and Shopify, Please Concern with Mobile Team also Before Adding any required Parameter*/
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
            if (isset($data['shipping_mode_id']) && isset($data['service_type_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = $data['service_type_id'];
            } else {
                return false;
            }

            if ($value) {
                if ($service_type_id == 5) {
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
            if (isset($data['shipping_mode_id']) && isset($data['service_type_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = $data['service_type_id'];
            } else {
                return false;
            }
            if ($value) {
                if ($service_type_id == 5) {
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
            if (isset($data['shipping_mode_id']) && isset($data['service_type_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = $data['service_type_id'];
            } else {
                return false;
            }
            if ($value) {
                if ($service_type_id == 5) {
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
                'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', 'exists:shipping_modes,id', Rule::exists('rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)->where('status', 1);
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
                'package_type' => ['nullable', 'boolean'],
                'special_instructions' => ['nullable', 'filled', 'between:0,190'],
                'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],

                'same_day_timing_id' => ['required_if:shipping_mode_id,4', 'integer', 'digits_between:1,10', 'exists:shipping_mode_same_day_timings,id'],
                'amount' => ['required_if:service_type_id,1,2', 'nullable', 'numeric', 'min:0'],
                'parcel_value' => ['nullable', 'numeric', 'min:1'],
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
                'substitute_user_email' => ['nullable', 'filled', 'email']
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
//            dd('type not 1');
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
                'parcel_value' => ['nullable', 'numeric', 'between:1,1000000'],
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
                'replacement_item_image' => ['nullable', 'mimes:png,jpeg,jpg'],

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
                'substitute_user_email' => ['nullable', 'filled', 'email'],

                'ftl_collection_type' => ['required_if:service_type_id,6', 'integer', 'digits_between:1,10'],
                'approve_freight_request' => ['required_if:service_type_id,6', 'integer', 'digits_between:1,10'],

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
            if ($user_type['restrict_order_id'] == 1) {
                $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })];
            } else {
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
//                dd('s',$request->all());
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

                if ($service_type_id == 1) {
                    if ($request->has('return_address_id') && $request->input('return_address_id') != null) {

                        $settings = GlobalSettings::where('type', 'omni_users');
                        if ($settings->exists()) {
                            $settings = $settings->first();
                            if ($settings->text != NULL) {
                                $omni_accounts = array_map('intval', explode(',', $settings->text));
                                if (!in_array($user_id, $omni_accounts)) {
                                    return response()->json(['status' => 1, 'message' => $user_type['name'] . 'is not an omni account']);
                                }
                            }
                        }

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

                if (($request->input('consignee_city_id') == 1244 && $user_id != 5982 && $user_id != 3324 && $user_id != 10104 && $user_id != 14110 && $user_id != 16292)) {
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
//                dd('s1');
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
                // $information_display = $request->input('information_display');
                $information_display = 1;

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
            if ($service_type_id == 1) {
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
                $parcel_value = 0;
            } elseif ($service_type_id == 5) {
                $try_and_buy_charges = null;
                $amount = 0;
                $parcel_value = 0;
            } else {
                $try_and_buy_charges = null;
                $amount = $request->input('amount');
                $parcel_value = $request->input('parcel_value');
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
                $parcel_value = 0;
            }


//            dd('response',$request->all(),$amount,$parcel_value);

            $business_category_id = 1;
            if ($user_type['account_type_id'] == 1) {
                $shipment_id = ShipperShipmentBookController::book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces_quantity, $self_collection, $business_category_id, $open_shipment, $return_address_id,$parcel_value);
            } else {

                if ($user_type['corporate_rate_type_id'] == 3) {
                    $delivery_type_id = 1;
                }
                $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id,$parcel_value);
            }


            if ($shipment_pre_book) {
                $tracking_number = ShipperShipmentBookController::generate_prefix_tracking_number($shipment_id, $order_id);
            } else {
                $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
            }

            //substitute_user_email
            if ($request->has('substitute_user_email')) {
                if ($request->substitute_user_email != null) {
                    $sub_user = SubstituteUser::where(['user_id' => $user_id, 'email' => $request->substitute_user_email, 'status' => 1]);
                    if ($sub_user->exists()) {
                        $sub_user = $sub_user->first();
                        $sub_user_id = $sub_user->id;
                        $substitute_user_shipment = new SubstituteUserShipment();
                        $substitute_user_shipment->substitute_user_id = $sub_user_id;
                        $substitute_user_shipment->shipment_id = $shipment_id;
                        $substitute_user_shipment->save();

                        $shipment = Shipment::find($shipment_id);
                        $shipment->booked_by = 2;
                        $shipment->save();
                    }
                }
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

                if ($request->has('replacement_item_image')) {
                    $shipment_parcel_image = ShipmentReplacementParcelImage::where('shipment_id', $shipment_id);
                    if ($shipment_parcel_image->exists()) {
                        $shipment_parcel_image = $shipment_parcel_image->first();
                        Storage::disk('public')->delete($shipment_parcel_image->picture_path);
                    } else {
                        $shipment_parcel_image = new ShipmentReplacementParcelImage();
                        $shipment_parcel_image->shipment_id = $shipment_id;
                    }
                    $time = Carbon::now()->toDateString();
                    $picture_path = 'replacement_parcel/' . $shipment_id . '_' . $time . '.png';
                    Storage::disk('public')->put($picture_path, file_get_contents($request->replacement_item_image));
                    $shipment_parcel_image->picture_path = $picture_path;
                    $shipment_parcel_image->save();
                }
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

            $check_bdmk = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                ->where('bdm.status', 1)
                ->select(['booking_destination_mapping_keywords.keyword'])
                ->pluck('keyword')
                ->toArray();
            $bdmk_error = "";

            $consignee_city = City::find($consignee_city_id);

            $bdmk_result = $this->check_bdmk($consignee_city->id, $consignee_address, $check_bdmk, $consignee_city->name);
            if (isset($bdmk_result['invalid_cities'])) {
                $bdmk_error = $bdmk_result['invalid_cities'];
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

            if ($service_type_id == 6) {
                $ftl_request_id = $request->approve_freight_request;
                FtlRequest::where('id', $ftl_request_id)->update(['shipment_id' => $shipment_id, 'status_id' => 5, 'collection_type' => $request->ftl_collection_type]);
                FTLController::FTLRequestStatusHistory($ftl_request_id, 5, $user_id);
            }

            $return_array = array(
                'status' => 0,
                'message' => 'Shipment has been Booked!',
                'tracking_number' => $tracking_number
            );

            if ($msg_string != null && $blacklist_message == null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
                $msg_string = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                $return_array['non_service_area'] = $msg_string . ' In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222.';
            }
            if ($msg_string == null && $blacklist_message != null) {
                $return_array['blacklisted_consignee'] = $blacklist_message;
            }
            if ($msg_string != null && $blacklist_message != null) {
                NotificationsController::send(32, $shipment_id, $msg_string);
                $msg_string = "A Possible Address Anomaly: " . $msg_string . " Detected!";
                $return_array['non_service_area'] = $msg_string . ' In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222.';
                $return_array['blacklisted_consignee'] = $blacklist_message;
            }

            // bdmk work

            if ($bdmk_error != null && $blacklist_message == null) {
                $bdmk_error = $bdmk_error;
                $return_array['bdmk_message'] = $bdmk_error;
            }
            if ($bdmk_error == null && $blacklist_message != null) {
                $return_array['blacklisted_consignee'] = $blacklist_message;
            }
            if ($bdmk_error != null && $blacklist_message != null) {
                $bdmk_error = $bdmk_error;
                $return_array['bdmk_message'] = $bdmk_error;
            }

            if (isset($return_array['non_service_area']) || isset($return_array['blacklisted_consignee']) || isset($return_array['bdmk_message'])) {
                return response()->json($return_array);
            }

            // bdmk work end

            NotificationsController::send(2, $shipment_id);
            $now = Carbon::now()->format('H:i:s');
            $pickup_city = City::where('id', $pickup_city_id)->whereNotNull('pickup_cut_off_time');
            if ($pickup_city->exists()) {
                $pickup_city = $pickup_city->first();
                $cutofftime = $pickup_city->pickup_cut_off_time . ":00:00";
            } else {
                $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();

                $cutofftime = $settingsfortime->setting_value . ":00:00";
            }

            if ($now > $cutofftime) {
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

            $origin = $shipment->pickup_address->city->name;
            $destination = $shipment->consignee_city->name;



            $order_date = $shipment->pickup_date;
            $booking_date = $shipment->created_at;

            if ($type == 0) {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

                if ($shipment_journey) {
                    $current_status = $shipment_journey->shipment_status_shipper->name;
                    $current_status_datetime = Carbon::parse($shipment_journey->created_at)->format('d/m/Y h:i A');

                    if ($shipment_journey->status_reason_id) {
                        $reason = ShipmentStatusReason::find($shipment_journey->status_reason_id)->name;
                    } else {
                        $reason = null;
                    }
                } else {
                    $current_status = $shipment->status_shipper->name;
                    $current_status_datetime = Carbon::parse($shipment->updated_at)->format('d/m/Y h:i A');
                    $reason = null;
                }
            } else {
                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->whereNotNull('consignee_status_id')->latest()->first();

                if ($shipment_journey) {
                    $current_status = $shipment_journey->shipment_status_shipper->name;
                    $current_status_datetime = Carbon::parse($shipment_journey->created_at)->format('d/m/Y h:i A');

                    if ($shipment_journey->status_reason_id) {
                        $reason = ShipmentStatusReason::find($shipment_journey->status_reason_id)->name;
                    } else {
                        $reason = null;
                    }
                } else {
                    $current_status = $shipment->status_shipper->name;
                    $current_status_datetime = Carbon::parse($shipment->updated_at)->format('d/m/Y h:i A');
                    $reason = null;
                }
            }

            return response()->json(['status' => 0, 'message' => 'Status of Shipment #' . $tracking_number, 'current_status' => $current_status, 'reason' => $reason, 'current_status_datetime' => $current_status_datetime, 'origin' => $origin, 'destination' => $destination, 'order_date' => $order_date, 'booking_date' => $booking_date]);
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

            $details['order_date'] = $shipment->pickup_date;
            $details['booking_date'] = $shipment->created_at;

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
                if ($shipment->packaging_charges) {
                    $charges['packing_charges'] = $shipment->packaging_charges;
                }
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

                if ($shipment->cash_handling_charges) {
                    $charges['cash_handling_charges'] = $shipment->cash_handling_charges;
                }

                if ($shipment->packaging_charges) {
                    $charges['packing_charges'] = $shipment->packaging_charges;
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

                if ($shipment->packaging_charges) {
                    $charges['packing_charges'] = $shipment->packaging_charges;
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

                if ($shipment->cash_handling_charges) {
                    $charges['cash_handling_charges'] = $shipment->cash_handling_charges;
                }

                if ($shipment->packaging_charges) {
                    $charges['packing_charges'] = $shipment->packaging_charges;
                }
            }
            $total_charges_without_gst = array_sum($charges);
            $gst = $shipment->pickup_address->city->zone->gst;
            $gst = $gst * $total_charges_without_gst;
            $total_charges = $shipment->amount - ($gst + $total_charges_without_gst);
            if ($current_status_id == 1) {
                $charges['net_payable'] = 0.0;
            } else {
                $charges['net_payable'] = number_format($total_charges, 2);
            }
            $charges['total_charges'] = number_format($total_charges_without_gst, 2);
            $charges['gst'] = number_format($gst, 2);
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

            if ($shipment->warehouse == 1) {
                return response()->json(['status' => 1, 'message' => 'Warehouse Shipment\'s can\'t be cancelled through Sonic.']);
            } else {
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

    public function receiving_sheet_print(Request $request)
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
            $receiving_sheet = ReceivingSheet::find($receiving_sheet_id);

            // if ($receiving_sheet) {
            //     return response()->json(['status' => 1, 'message' => 'Receiving Sheet Not Found.']);
            // }
            if (count($receiving_sheet->receiving_sheet_shipments) > 200) {
                return response()->json(['status' => 1, 'message' => 'Too many shipments.']);
            }
            if (!isset($request->type) || $request->type == 0) {
                if (count($receiving_sheet->receiving_sheet_shipments) > 50) {
                    return response()->json(['status' => 1, 'message' => 'Too many shipments, please use type=1 to extract pdf.']);
                }
            }
            $print_status = 0;
            $receiving_sheet_print = ReceivingSheetPrintStatus::where('receiving_sheet_id', $receiving_sheet->id);
            if ($receiving_sheet_print->exists()) {
                $receiving_sheet_print = $receiving_sheet_print->get()->first();
                $print_status = $receiving_sheet_print->status;
            }
            $receiving_sheet = ShipperReceivingSheetController::print_receiving_sheet_and_air_waybill_api($receiving_sheet_id, 4, $print_status);

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
            $cities = $cities->with(['hub_city', 'zone', 'deliveries.booking_type', 'deliveries.shipping_mode'])->get();

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

    public function shopify_cities(Request $request)
    {
        $user_id = $request->user_id;

        $cities = City::where('status', 1)->where('business_category_id', 1);

        if ($cities->exists()) {
            $cities = $cities->select('id', 'name')->get();

            return response()->json(['status' => 0, 'message' => 'List of Cities', 'cities' => $cities]);
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

            $gst = $origin_city->zone->gst;
            $total_charges_without_gst = array_sum($information['charges']);
            $gst = ROUND(($gst * $total_charges_without_gst), 2, PHP_ROUND_HALF_DOWN);
            $net_payable = ROUND(($request->amount - ($total_charges_without_gst + $gst)), 2, PHP_ROUND_HALF_DOWN);
            $information['charges']['total_charges'] = $total_charges_without_gst;
            $information['charges']['gst'] = $gst;
            $information['charges']['net_payable'] = $net_payable;

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

                $details['order_information']['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);
                $details['order_information']['amount'] = $shipment->amount;

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
            ->leftJoin('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt', 'bt.id', '=', 'shipments.booking_type_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where(
                        'shipments_journey.created_at',
                        '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)')
                    );
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where(
                        'sj.created_at',
                        '=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)')
                    );
            })
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 'shipments.id')
                    ->where(
                        'consolidations.consolidation_id',
                        '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = shipments.id)')
                    );
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
            'remarks' => ['nullable'],
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);
        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            //status = 1 -> Return Confirm, Starus = 2 -> Re-attempt requested
            $tracking_number = $request->tracking_number;

            $remarks = 'Marked by shipper - API';
            if ($request->has('remarks')) {
                if ($request->remarks != null) {
                    $remarks = $request->remarks;
                }
            }

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

                    if ($shipment->shipper_status_id == 12) {
                        $shipment->shipper_status_id = 20;
                        $shipment->consignee_status_id = 20;
                        $shipment->save();
                        $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                        ShipmentChargesController::return($shipment->id);

                        AdminFinanceController::add_payment($shipment->id, 1);
                        ShipmentsJourneyController::add($shipment->id, 20, 20, $shipment_history->status_reason_id, $remarks, $user_id, null);
                        return response()->json(['status' => 0, 'message' => "Shipment successfully marked as Shipment - Return Confirm"]);
                    } else {
                        return response()->json(['status' => 1, 'message' => "Shipment is not ready for Return Confirm"]);
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
                    ShipmentsJourneyController::add($shipment->id, 52, 52, null, $remarks, $user_id, null);
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
                        $air_waybill .= ShopifyController::invoice_generate($user_id, $request->orders[$shipment->tracking_number], $shop_invoice_setting, $shipment);
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
            'type' => ['required', 'boolean']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $order_id = $request->order_id;
            $type = $request->type;

            $details = array();

            foreach ($user_ids as $user_id) {

                $shipments = Shipment::where('user_id', $user_id)->where('order_id', $order_id)->get();
                foreach ($shipments as $shipment) {
                    $detail = array();

                    $detail['tracking_number'] = $shipment->tracking_number;

                    $detail['order_id'] = $shipment->order_id;

                    $details['order_date'] = $shipment->pickup_date;
                    $details['booking_date'] = $shipment->created_at;

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

                //                $telenor_other_shipments = TelenorOtherCouriers::where('consignee_number', $order_id);
                //                if($telenor_other_shipments->exists() && $user_id == 3324){
                //
                //                    $telenor_other_shipments = $telenor_other_shipments->get();
                //                    foreach ($telenor_other_shipments as $shipment){
                //
                //                        $detail = array();
                //
                //                        $detail['tracking_number'] = $shipment->tracking_number;
                //                        $detail['order_id'] = $shipment->consignee_number;
                //                        $detail['status'] = $shipment->status;
                //                        $detail['created_at'] = $shipment->consignee_number;
                //                        $detail['updated_at'] = $shipment->consignee_number;
                //
                //                        $details[] = $detail;
                //                    }
                //                }
            }

            if (!empty($details)) {
                return response()->json(['status' => 0, 'message' => 'Tracking of Shipment(s) - Order ID #' . $order_id, 'details' => $details]);
            } else {
                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => ['order_id' => "Given Order ID is of Invalid ID."]]);
            }
        }
    }

    public function shipment_status_order_id(Request $request)
    {
        $user_id = $request->user_id;

        $user_ids = MergedSisterAccountMapping::where('head_user_id', $user_id)->pluck('sister_user_id')->toArray();

        $user_ids[] = $user_id;

        $rules = [
            'type' => ['required', 'boolean']
        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $order_id = $request->order_id;
            $type = $request->type;

            //            $telenor_other_shipments = TelenorOtherCouriers::where('consignee_number', $order_id);
            $details = array();

            foreach ($user_ids as $user_id) {

                $shipments = Shipment::where('user_id', $user_id)->where('order_id', $order_id)->get();

                foreach ($shipments as $shipment) {
                    $detail = array();

                    $detail['origin'] = $shipment->pickup_address->city->name;
                    $detail['destination'] = $shipment->consignee_city->name;

                    if ($type == 0) {
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->latest()->first();

                        if ($shipment_journey) {
                            $current_status = $shipment_journey->shipment_status_shipper->name;
                            $current_status_datetime = Carbon::parse($shipment_journey->created_at)->format('d/m/Y h:i A');

                            if ($shipment_journey->status_reason_id) {
                                $reason = ShipmentStatusReason::find($shipment_journey->status_reason_id)->name;
                            } else {
                                $reason = null;
                            }
                        } else {
                            $current_status = $shipment->status_shipper->name;
                            $current_status_datetime = Carbon::parse($shipment->updated_at)->format('d/m/Y h:i A');
                            $reason = null;
                        }
                    } else {
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->whereNotNull('consignee_status_id')->latest()->first();

                        if ($shipment_journey) {
                            $current_status = $shipment_journey->shipment_status_consignee->name;
                            $current_status_datetime = Carbon::parse($shipment_journey->created_at)->format('d/m/Y h:i A');
                            if ($shipment_journey->status_reason_id) {
                                $reason = ShipmentStatusReason::find($shipment_journey->status_reason_id)->name;
                            } else {
                                $reason = null;
                            }
                        } else {
                            $current_status = $shipment->status_consignee->name;
                            $current_status_datetime = Carbon::parse($shipment->updated_at)->format('d/m/Y h:i A');
                            $reason = null;
                        }
                    }

                    $detail['tracking_number'] = $shipment->tracking_number;
                    $detail['status'] = $current_status;
                    $detail['reason'] = $reason;
                    $detail['current_status_datetime'] = $current_status_datetime;
                    $detail['order_date'] = $shipment->pickup_date;
                    $detail['booking_date'] = $shipment->created_at;

                    $details[] = $detail;
                }

                //                $telenor_other_shipments = TelenorOtherCouriers::where('consignee_number', $order_id);
                //                if($telenor_other_shipments->exists() && $user_id == 3324){
                //
                //                    $telenor_other_shipments = $telenor_other_shipments->get();
                //                    foreach ($telenor_other_shipments as $shipment){
                //
                //                        $detail = array();
                //
                //                        $detail['tracking_number'] = $shipment->tracking_number;
                //                        $detail['order_id'] = $shipment->consignee_number;
                //                        $detail['status'] = $shipment->status;
                //                        $detail['created_at'] = $shipment->consignee_number;
                //                        $detail['updated_at'] = $shipment->consignee_number;
                //
                //                        $details[] = $detail;
                //                    }
                //                }
            }


            if (!empty($details)) {
                return response()->json(['status' => 0, 'message' => 'Status of Shipment(s) - Order ID #' . $order_id, 'details' => $details]);
            } else {
                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => ['order_id' => "Given Order ID is of Invalid ID."]]);
            }
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
                    $data['total_charges'] = $invoice->total_charges;
                    $data['total_gst'] = $invoice->total_gst;
                    $data['total_invoice_amount'] = $invoice->total_invoice_amount;
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
                            $details[$shipment->tracking_number]['amount'] = $shipment->amount;
                            $details[$shipment->tracking_number]['actual_weight'] = $shipment->actual_weight;
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
                    $done_payment_calculation = DonePaymentCalculation::where('done_payment_id', $done_payment->id)->first();
                    $account_type_id = $done_payment->shipper->account_type_id;
                    $data['billing_method'] = $done_payment->shipper->account_type->name;
                    $data['invoice_date'] = Carbon::parse($done_payment->created_at)->toDateTimeString();
                    $data['total_charges'] = $done_payment_calculation->charges;
                    $data['total_gst'] = $done_payment_calculation->gst;
                    $data['total_invoice_amount'] = $done_payment_calculation->payable;
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
                            $details[$shipment->tracking_number]['amount'] = $shipment->amount;
                            $details[$shipment->tracking_number]['actual_weight'] = $shipment->actual_weight;
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
                                $current_status['Location'] = $this->shipment_google_location_name($shipment->shipper_status_id, $cities);

                                $transit_event = array();

                                $transit_event['Status'] = $this->shipment_google_status_name($shipment->shipper_status_id);
                                $transit_event['Date'] = Carbon::parse($shipment->updated_at)->toIso8601String();
                                $transit_event['Location'] = $this->shipment_google_location_name($shipment->shipper_status_id, $cities);

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

        //type_id
        /*
        1- Admin
        2- Rider
        3- Shipper
        4- Consignee*/
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            EmployeeDeviceToken::where('device_token', $request->get('device_token'))->delete();
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
            if ($request->tracking_number) {
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
            } else if ($request->order_id) {
                $detail = array();
                $order_id = $request->order_id;
                $shipments = Shipment::where('order_id', $order_id)->where('user_id', $user_id)->get();

                foreach ($shipments as $shipment) {
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
                    ]
                ]);
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
            'case_nature_id' => ['required'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            if ($nature_id == 1 || $nature_id == 2 || $nature_id == 4) {
                $rules = [
                    'tracking_number' => ['required_without:tracking_numbers', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })],
                ];
                $validate = Validator::make($request->all(), $rules, $this->messages);

                $validate->setAttributeNames($this->names);
            }

            if ($nature_id == 1 || $nature_id == 2) {
                //complaints


                $crm_request_type = CrmRequestCaseNatureType::where('nature_id', $nature_id)->where('status_id', 1)->pluck('id')->toArray();
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
                $crm_request_type = CrmRequestCaseNatureType::where('nature_id', $nature_id)->where('status_id', 1)->pluck('id')->toArray();
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

                                $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_product_price);
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

                                $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->file('product_picture'), $request->file('invoice_picture'), Null, Null, Null, Null, $request->file('missing_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->missing_product_price);
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
                    } else {
                        return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                    }
                } else {
                    return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                }
            } else if($nature_id == 3){
                $rules = [
                    'description' => ['required'],
                ];
                $validate = Validator::make($request->all(), $rules, $this->messages);

                $validate->setAttributeNames($this->names);

                if ($validate->fails()) {
                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                } else {

                    $crm_request = CRMController::add($nature_id, null, 1, 1, $user_id, $launched_by, null, $user_id, null, $description);

                        // $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description);
                    return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
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
        } else {
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
                    if ($shipment->shipper_status_id == 12) {
                        //                        if (!$shipment->packaging_material_request) {
                        Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                        $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                        ShipmentChargesController::return($shipment->id);
                        //                NotificationsController::send(15, 0, $request->shipment_id);
                        //                NotificationsController::send(16, 0, $request->shipment_id);

                        AdminFinanceController::add_payment($shipment->id, 1);
                        ShipmentsJourneyController::add($shipment->id, 20, 20, $shipment_history->status_reason_id, $remark, $user_id, null);
                        //                        } else {
                        //                            Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 17, 'consignee_status_id' => 17]);
                        //                            $shipment_history = ShipmentsJourney::where('shipment_id', $shipment->id)->latest()->first();
                        //                            ShipmentsJourneyController::add($shipment->id, 17, 17, $shipment_history->status_reason_id, $remark, $user_id, null);
                        //                            //                NotificationsController::send(15, 0, $request->shipment_id);
                        //                            //                NotificationsController::send(16, 0, $request->shipment_id);
                        //                        }
                        // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment->id);
                        // if ($return_assign_shipment->exists()) {
                        //     $return_assign_shipment = $return_assign_shipment->latest()->first();
                        //     $return_assign_shipment->status = 0;
                        //     $return_assign_shipment->save();

                        //     $return_assign_log = new ReturnAssignedShipmentLogs();
                        //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                        //     $return_assign_log->status = 2; //Return Confirm
                        //     $return_assign_log->assigned_by = $user_id;
                        //     $return_assign_log->save();
                        // }
                            
                        
                        //Rcp Request Create
                        $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $shipment->id);
                        if ($rcp_assigned_shipment->exists()) {
                            $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                            $rcp_assigned_shipment->shipment_status = 4; //return confirm status
                            $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                            $rcp_assigned_shipment->user_id = $user_id;
                            $rcp_assigned_shipment->save();

                            //updating already_updated & pending of agent if shipment is updated by shipper 
                            $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                            $already_updated = $rcp_assigned_agent->increment('already_updated');
                            $rcp_assigned_agent->decrement('pending_shipments');
                            $rcp_assigned_agent->save();


                            $return_assign_log = new RcpAssignedShipmentLog ();
                            $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                            $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                            $return_assign_log->status = 4; //return confirm status
                            $return_assign_log->user_id = $user_id;
                            $return_assign_log->save();
                        }

                        return response()->json(['status' => 0, 'message' => 'Shipment successfully marked as Shipment - Return Confirm']);
                    }
                    return response()->json(['status' => 1, 'message' => 'Shipment is already updated']);
                } elseif ($request->type == 2) {

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

                            //Reateempt Request
                            // $rcp_assigned_shipment = ReturnAssignedShipments::where('shipment_id', $shipment->id)->latest()->first();
                            $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $shipment->id)->latest()->first();
                            if ($rcp_assigned_shipment) {
                                // $return_assign_shipment->status = 0;
                                // $return_assign_shipment->save();

                                // $return_assign_log = new ReturnAssignedShipmentLogs();
                                // $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                // $return_assign_log->status = 5; //reattempt request
                                // $return_assign_log->assigned_by = $user_id;
                                // $return_assign_log->save();
                                
                                $rcp_assigned_shipment->shipment_status = 10; //reattempt request 
                                $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                                $rcp_assigned_shipment->user_id = $user_id;
                                $rcp_assigned_shipment->save();

                                //updating already_updated & pending of agent if shipment is updated by shipper 
                                $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                $already_updated = $rcp_assigned_agent->increment('already_updated');
                                $rcp_assigned_agent->decrement('pending_shipments');
                                $rcp_assigned_agent->save();


                                $return_assign_log = new RcpAssignedShipmentLog ();
                                $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                $return_assign_log->status = 10; //reattempt
                                $return_assign_log->user_id = $user_id;
                                $return_assign_log->save();
                            }
                            if ($journey) {
                                NotificationsController::send(33, $shipment->id);
                            }
                            return response()->json(['status' => 0, 'message' => 'Shipment successfully updated as ( Re-Attempt - Requested )']);
                        }
                    }
                    return response()->json(['status' => 1, 'message' => 'Shipment not found!']);
                } elseif ($request->type == 3) {
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

                                            //Updating New RcpAssigned Tables for Same Consignee Intercept
                                            $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $shipment->id);
                                            if ($rcp_assigned_shipment->exists()) {
                                                
                                                $rcp_assigned_shipment = $rcp_assigned_shipment ->latest()->first();
                                                $rcp_assigned_shipment->shipment_status = 8; //intercept approved
                                                $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                                                $rcp_assigned_shipment->user_id = $user_id;
                                                $rcp_assigned_shipment->save();

                                                //updating already_updated & pending of agent if shipment is updated by shipper 
                                                $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                                $already_updated = $rcp_assigned_agent->increment('already_updated');
                                                $rcp_assigned_agent->decrement('pending_shipments');
                                                $rcp_assigned_agent->save();

                                                //creating log for request intercept then approved
                                                $return_assign_log = new RcpAssignedShipmentLog();
                                                $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                                $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                                $return_assign_log->status = 7; //intercept request
                                                $return_assign_log->user_id = $user_id;
                                                $return_assign_log->save();

                                                $return_assign_log = new RcpAssignedShipmentLog();
                                                $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                                $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                                $return_assign_log->status = 8; //intercept approved
                                                $return_assign_log->user_id = $user_id;
                                                $return_assign_log->save(); 
                                            }  

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

                                            //Updating New RcpAssigned Tables for different Consignee Intercept
                                            $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $shipment->id);
                                            if ($rcp_assigned_shipment->exists()) {
                                                    $rcp_assigned_shipment = $rcp_assigned_shipment ->latest()->first();
                                                    $rcp_assigned_shipment->shipment_status = 7; //intercept request
                                                    $rcp_assigned_shipment->assigned_status = 2; //unassign agent 
                                                    $rcp_assigned_shipment->user_id = $user_id;
                                                    $rcp_assigned_shipment->save();

                                                    //updating already_updated & pending of agent if shipment is updated by shipper 
                                                    $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                                    $already_updated = $rcp_assigned_agent->increment('already_updated');
                                                    $rcp_assigned_agent->decrement('pending_shipments');
                                                    $rcp_assigned_agent->save();

                                                    //creating log 
                                                    $return_assign_log = new RcpAssignedShipmentLog();
                                                    $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                                    $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                                    $return_assign_log->status = 7; //intercept request
                                                    $return_assign_log->user_id = $user_id;
                                                    $return_assign_log->save();
                                                        
                                                }  


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
    public function receiving_sheet_add(Request $request)
    {
        $user_id = $request->user_id;
        $rules = [
            'receiving_sheet_id' => ['required', 'integer', Rule::exists('receiving_sheets', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment = Shipment::where('user_id', $user_id)->where('tracking_number', $request->tracking_number)->first();
            if ($shipment->shipper_status_id != 1) {
                return ['status' => 1, 'message' => $shipment->tracking_number . ' can no longer be added to a Receiving Sheet'];
            }
            $receiving_sheet_id = $request->receiving_sheet_id;
            $tracking_number = $request->tracking_number;
            $shipmentid = $shipment->id;

            $receiving_sheet = ReceivingSheet::find($receiving_sheet_id);
            $receiving_sheet_shipment = ReceivingSheetShipment::find($shipmentid);
            if ($receiving_sheet_shipment) {
                return ['status' => 1, 'message' => 'Shipment was added in Receiving Sheet: ' . $receiving_sheet_shipment->receiving_sheet_id];
            }
            if ($receiving_sheet->status == 0) {
                $first_receiving_sheet_shipment = ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->first();

                if ($first_receiving_sheet_shipment) {
                    $first_shipment = Shipment::find($first_receiving_sheet_shipment->shipment_id);

                    if ($shipment->pickup_address_id == $first_shipment->pickup_address_id) {
                        $receiving_sheet_shipment = new ReceivingSheetShipment();

                        $receiving_sheet_shipment->shipment_id = $shipmentid;
                        $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;

                        $receiving_sheet_shipment->save();

                        $receiving_sheet->booked = $receiving_sheet->booked + 1;

                        $receiving_sheet->save();

                        if ($shipment->user_id == 7828) {
                            $confirmation_datetime = Carbon::now()->toDateTimeString();

                            $confirmation_shipments = array();

                            $confirmation_shipment = array();

                            $confirmation_shipment['CNN'] = $shipment->tracking_number;
                            $confirmation_shipment['reference_number'] = $shipment->order_id;
                            $confirmation_shipment['ConfirmationDateTime'] = $confirmation_datetime;

                            $confirmation_shipments[] = $confirmation_shipment;

                            dispatch(new ProcessGulAhmedShipmentConfirmation($confirmation_shipments));
                        }

                        return ['status' => 0, 'message' => 'Shipment has been Added to the Receiving Sheet'];
                    } else {
                        return ['status' => 1, 'message' => 'Given Shipment\'s Pickup Address is different from the other Shipments of the selected Receiving Sheet'];
                    }
                } else {
                    $receiving_sheet_shipment = new ReceivingSheetShipment();

                    $receiving_sheet_shipment->shipment_id = $shipmentid;
                    $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;

                    $receiving_sheet_shipment->save();

                    $receiving_sheet->booked = $receiving_sheet->booked + 1;

                    $receiving_sheet->save();

                    if ($shipment->user_id == 7828) {
                        $confirmation_datetime = Carbon::now()->toDateTimeString();

                        $confirmation_shipments = array();

                        $confirmation_shipment = array();

                        $confirmation_shipment['CNN'] = $shipment->tracking_number;
                        $confirmation_shipment['reference_number'] = $shipment->order_id;
                        $confirmation_shipment['ConfirmationDateTime'] = $confirmation_datetime;

                        $confirmation_shipments[] = $confirmation_shipment;

                        dispatch(new ProcessGulAhmedShipmentConfirmation($confirmation_shipments));
                    }

                    return ['status' => 0, 'message' => 'Shipment has been Added to the Receiving Sheet'];
                }
            } else {
                return ['status' => 1, 'message' => 'Shipment w.r.t. Receiving Sheet ID not found'];
            }
        }
    }
    public function receiving_sheet_void(Request $request)
    {
        $user_id = $request->user_id;
        $rules = [
            'receiving_sheet_id' => ['required', 'integer', Rule::exists('receiving_sheets', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
            'tracking_number' => ['required', 'integer', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $shipment = Shipment::where('user_id', $user_id)->where('tracking_number', $request->tracking_number)->first();

            $receiving_sheet_id = $request->receiving_sheet_id;
            $tracking_number = $request->tracking_number;
            $shipmentid = $shipment->id;

            $receiving_sheet_shipment = ReceivingSheetShipment::where('shipment_id', $shipmentid)->where('receiving_sheet_id', $receiving_sheet_id);
            if (!$receiving_sheet_shipment->exists()) {
                return ['status' => 1, 'message' => 'Shipment not found or Voided previously'];
            }
            $receiving_sheet = ReceivingSheet::find($receiving_sheet_id);

            if ($receiving_sheet->status == 0) {
                $receiving_sheet_shipment->delete();

                if (!ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->exists()) {
                    $receiving_sheet->booked = $receiving_sheet->booked - 1;
                    $receiving_sheet->status = 2;

                    $receiving_sheet->save();
                }
                if (ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->exists()) {
                    $receiving_sheet->booked = $receiving_sheet->booked - 1;

                    $receiving_sheet->save();
                }

                return ['status' => 0, 'message' => 'Shipment has been Voided'];
            } else {
                return ['status' => 1, 'message' => 'Shipment w.r.t. Receiving Sheet ID not found'];
            }
        }
    }
    public function receiving_sheet_cancel(Request $request)
    {
        $user_id = $request->user_id;
        $rules = [
            'receiving_sheet_id' => ['required', 'integer', Rule::exists('receiving_sheets', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $receiving_sheet_id = $request->receiving_sheet_id;
            $receiving_sheet = ReceivingSheet::find($receiving_sheet_id);
            $receiving_sheet_shipment = ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet->id);
            if ($receiving_sheet->status == 0) {
                $receiving_sheet_shipment->delete();

                if (!ReceivingSheetShipment::where('receiving_sheet_id', $request->input('receiving_sheet_id'))->exists()) {
                    $receiving_sheet->booked = 0;
                    $receiving_sheet->status = 2;

                    $receiving_sheet->save();
                }
                if (ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->exists()) {
                    $receiving_sheet->booked = 0;

                    $receiving_sheet->save();
                }

                return ['status' => 0, 'message' => 'Receiving Sheet has been Cancelled'];
            } else {
                return ['status' => 1, 'message' => 'Receiving Sheet has been Cancelled or Removed'];
            }
        }
    }

    public function rcp_sms_from_consignee(Request $request)
    {
        if ($request->ip() == "202.141.247.130" || $request->ip() == "202.141.247.133") {
            if ($request->has('message') && !empty($request->message)) {
                $message = $request->message;

                $data = explode(" ", urldecode($message));

                if (count($data) == 3) {
                    $response_yes = array("YES", 'YE', 'Y');
                    $response_no = array("NO", 'N');
                    $res = strtoupper($data[1]);
                    $tracking_number = $data[2];

                    $shipment = Shipment::where('tracking_number', $tracking_number)->first();

                    if ($shipment) {
                        $rcp = ReturnConfirmationPendingSmsAttempt::where('shipment_id', $shipment->id)->where('status', 0);

                        if ($rcp->exists()) {
                            $rcp = $rcp->first();

                            if (in_array($res, $response_yes)) {
                                if (in_array($shipment->shipper_status_id, [12, 52])) {
                                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest('id')->first();

                                    if ($journey) {
                                        if ($shipment->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                                            $shipment->nsa_osa_status = 1;

                                            $shipment->save();

                                            ShipmentChargesController::nsa_osa_charges($shipment->id);

                                            NotificationsController::send(33, $shipment->id);
                                        } else if ($shipment->shipper_status_id == 52) {
                                            $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 12)->latest('id')->first();

                                            if ($journey && ($journey->status_reason_id == 12)) {
                                                $shipment->nsa_osa_status = 1;

                                                $shipment->save();

                                                ShipmentChargesController::nsa_osa_charges($shipment->id);
                                            }
                                        }
                                    }

                                    $shipment->shipper_status_id = 13;
                                    $shipment->consignee_status_id = 13;
                                    $shipment->save();

                                    ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, NULL, NULL, 1728);
                                    // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment->id)->latest()->first();

                                    // if ($return_assign_shipment) {
                                    //     $return_assign_shipment->status = 0;
                                    //     $return_assign_shipment->save();

                                    //     $return_assign_log = new ReturnAssignedShipmentLogs();
                                    //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                    //     $return_assign_log->status = 1; //reattempt status
                                    //     $return_assign_log->assigned_by = 1728;
                                    //     $return_assign_log->save();
                                    // }

                                    $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $shipment->id);
                                    if ($rcp_assigned_shipment->exists()) {
                                        $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                                        $rcp_assigned_shipment->shipment_status = 3; //reattempt status
                                        $rcp_assigned_shipment->admin_id = 1728;
                                        $rcp_assigned_shipment->save();
            
                                        //updating already_updated & pending of agent if shipment is updated by shipper 
                                        // $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                        // $already_updated = $rcp_assigned_agent->increment('already_updated');
                                        // $rcp_assigned_agent->decrement('pending_shipments');
                                        // $rcp_assigned_agent->save();
            
            
                                        $return_assign_log = new RcpAssignedShipmentLog ();
                                        $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                        $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                        $return_assign_log->status = 3; //reattempt status
                                        $return_assign_log->admin_id = 1728;
                                        $return_assign_log->save();
                                    }

                                    NotificationsController::send(15, 0, $shipment->id);
                                    NotificationsController::send(16, 0, $shipment->id);
                                }

                                $res_from_consignee = $data[0] . " " . $res;

                                $rcp->response = $res_from_consignee;
                                $rcp->status = 2;
                                $rcp->save();

                                return ['status' => 1, 'message' => 'Message Received'];
                            } elseif (in_array($res, $response_no)) {
                                if (in_array($shipment->shipper_status_id, [12, 52])) {
                                    $shipment->shipper_status_id = 20;
                                    $shipment->consignee_status_id = 20;
                                    $shipment->save();

                                    NotificationsController::send(15, 0, $shipment->id);
                                    NotificationsController::send(16, 0, $shipment->id);

                                    if ($shipment->shipment_type == 1) {
                                        if ($shipment->booking_type_id != 4) {
                                            ShipmentChargesController::return($shipment->id);

                                            if ($shipment->packaging_material_request != 1) {
                                                AdminFinanceController::add_payment($shipment->id, 1);
                                            }
                                        } else {
                                            ShipmentChargesController::walk_in_return($shipment->id);

                                            $shipment->walk_in_status = 2;

                                            $shipment->save();

                                            AdminFinanceController::done_payment($shipment->id, 1);
                                        }
                                    }
                                    ShipmentsJourneyController::add($shipment->id, 20, 20, 38, NULL, NULL, 1728);
                                    // $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment->id);
                                    // if ($return_assign_shipment->exists()) {
                                    //     $return_assign_shipment = $return_assign_shipment->latest()->first();
                                    //     $return_assign_shipment->status = 0;
                                    //     $return_assign_shipment->save();

                                    //     $return_assign_log = new ReturnAssignedShipmentLogs();
                                    //     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                                    //     $return_assign_log->status = 2; //return confirm status
                                    //     $return_assign_log->assigned_by = 1728;
                                    //     $return_assign_log->save();
                                    // }

                                    $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $shipment->id);
                                    if ($rcp_assigned_shipment->exists()) {
                                        $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                                        $rcp_assigned_shipment->shipment_status = 4; //return confirm status
                                        $rcp_assigned_shipment->admin_id = 1728;
                                        $rcp_assigned_shipment->save();
            
                                        //updating already_updated & pending of agent if shipment is updated by shipper 
                                        $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                                        $already_updated = $rcp_assigned_agent->increment('already_updated');
                                        $rcp_assigned_agent->decrement('pending_shipments');
                                        $rcp_assigned_agent->save();
            
            
                                        $return_assign_log = new RcpAssignedShipmentLog ();
                                        $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                                        $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                                        $return_assign_log->status = 4; //return confirm status
                                        $return_assign_log->admin_id = 1728;
                                        $return_assign_log->save();
                                    }
                                }

                                $res_from_consignee = $data[0] . " " . $res;

                                $rcp->response = $res_from_consignee;
                                $rcp->status = 1;
                                $rcp->save();

                                return ['status' => 1, 'message' => 'Message Received'];
                            } else {
                                return ['status' => 0, 'message' => 'Invalid message response from consignee'];
                            }
                        } else {
                            return ['status' => 0, 'message' => 'Invalid message response from consignee - attempt'];
                        }
                    } else {
                        return ['status' => 0, 'message' => 'Invalid message response from consignee - tracking number'];
                    }
                } else {
                    return ['status' => 0, 'message' => 'Invalid message response from consignee - format'];
                }
            } else {
                return ['status' => 0, 'message' => 'message field is required'];
            }
        } else {
            return ['status' => 0, 'message' => 'Unauthorized IP'];
        }
    }

    public function receiving_sheet_list(Request $request)
    {
        $user_id = $request->user_id;

        $rules = [

            'from_date' => ['required', 'date_format:Y-m-d'],
            'to_date' => ['required', 'date_format:Y-m-d'],

        ];

        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $date_from = explode('-',  $request->from_date);
            $date_to = explode('-',  $request->to_date);

            $from = Carbon::create($date_from[0], $date_from[1], $date_from[2], '0', '0', '0', 'UTC')->toDateTimeString();
            $to = Carbon::create($date_to[0], $date_to[1], $date_to[2], '23', '59', '59', 'UTC')->toDateTimeString();

            $receiving_sheets = ReceivingSheet::join('user_shipping_infos as usi', 'receiving_sheets.pickup_address_id', '=', 'usi.id')
                ->leftjoin('cities as c', 'usi.city_id', '=', 'c.id')
                ->join('users as u', 'u.id', '=', 'receiving_sheets.user_id')
                ->select('receiving_sheets.id as receiving_sheet_id', 'receiving_sheets.created_at as created_at', 'receiving_sheets.booked as bookings', 'receiving_sheets.received as receiving', 'receiving_sheets.pickup_address_id as pickup_address', 'c.name as origin', 'usi.pickup_address as address')
                ->whereBetween('receiving_sheets.created_at', [$from, $to]);

            if ($receiving_sheets->exists()) {
                $receiving_sheets = $receiving_sheets->get();

                $details = array();
                $details['status'] = 0;
                $details['from_date'] = $request->from_date;
                $details['to_date'] = $request->to_date;
                $details['receiving_sheets'] = [];
                foreach ($receiving_sheets as $receiving_sheet) {
                    $detail = array();
                    $detail['receiving_sheet_id'] = str_pad($receiving_sheet->receiving_sheet_id, 6, '0', STR_PAD_LEFT);
                    $detail['created_at'] = Carbon::parse($receiving_sheet->created_at)->format('d/m/Y h:i A');
                    $detail['shipments_booked'] = $receiving_sheet->bookings;
                    $detail['shipments_received'] = $receiving_sheet->receiving;
                    if ($receiving_sheet->bookings != 0) {
                        $detail['shipments_short_received'] = $receiving_sheet->bookings - $receiving_sheet->receiving;
                    } else {
                        $detail['shipments_short_received'] = 0;
                    }
                    $detail['pickup_address_id'] = $receiving_sheet->pickup_address;
                    $detail['origin_city'] = $receiving_sheet->origin;
                    $detail['address'] = $receiving_sheet->address;
                    array_push($details['receiving_sheets'], $detail);
                }

                return response()->json($details);
            } else {
                return response()->json(['status' => 1, 'message' => 'Receiving Sheet Not Found ']);
            }
        }
    }

    public function hbl_konnect_transactions(Request $request)
    {
        $valid_ip_addresses = array();
        $valid_ip_addresses[] = '103.111.84.67';
        $valid_ip_addresses[] = '103.111.85.67';
        $environment = config('app.env');

        if ($environment == 'production') {
            $whip = new Whip();
            $ip_address = $whip->getValidIpAddress();
            if (in_array($ip_address, $valid_ip_addresses)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = true;
        }
        if ($flag) {
            $rules = [
                'delivery_note_id' => ['required', 'integer', Rule::exists('delivery_notes', 'id')],
                'amount' => ['required', 'numeric', 'min:0'],
                'transaction_id' => ['required', 'integer', 'min:0'],
            ];
            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $errors = array();
                foreach ($validate->errors()->all() as $index => $error) {
                    $errors[$index]['error_code'] = 10;
                    $errors[$index]['error_text'] = 'Invalid Input.';
                    if ($error == 'delivery note id is Required.') {
                        $errors[$index]['error_code'] = 1;
                        $errors[$index]['ERROR_TEXT'] = 'Delivery note id is Required.';
                    }
                    if ($error == 'delivery note id must be an Integer.') {
                        $errors[$index]['error_code'] = 2;
                        $errors[$index]['error_text'] = 'Delivery note id must be an Integer.';
                    }
                    if ($error == 'Given delivery note id is of Invalid ID.') {
                        $errors[$index]['error_code'] = 3;
                        $errors[$index]['error_text'] = 'Given delivery note id is of Invalid ID.';
                    }
                    if ($error == 'Collection Amount is Required.') {
                        $errors[$index]['error_code'] = 4;
                        $errors[$index]['ERROR_TEXT'] = 'Collection Amount is Required.';
                    }
                    if ($error == 'Collection Amount must be a Number.') {
                        $errors[$index]['error_code'] = 5;
                        $errors[$index]['error_text'] = 'Collection Amount must be a Number.';
                    }
                    if ($error == 'The Collection Amount must be at least 0.') {
                        $errors[$index]['error_code'] = 6;
                        $errors[$index]['error_text'] = 'The Collection Amount must be at least 0.';
                    }
                    if ($error == 'transaction id is Required.') {
                        $errors[$index]['error_code'] = 7;
                        $errors[$index]['ERROR_TEXT'] = 'Transaction id is Required.';
                    }
                    if ($error == 'transaction id must be an Integer.') {
                        $errors[$index]['error_code'] = 8;
                        $errors[$index]['error_text'] = 'Transaction id must be an Integer.';
                    }
                    if ($error == 'The transaction id must be at least 0.') {
                        $errors[$index]['error_code'] = 9;
                        $errors[$index]['error_text'] = 'The transaction id must be at least 0.';
                    }
                }
                return response()->json(['status' => 0, 'message' => 'Error(s) in Input', 'errors' => $errors]);
            } else {
                $transaction_id = $request->transaction_id;
                $delivery_note_id = $request->delivery_note_id;
                $amount = $request->amount;
                $existing_hbl_konnect_transaction = HblKonnectTransaction::where('transaction_id', $transaction_id);
                if ($existing_hbl_konnect_transaction->exists()) {
                    return ['status' => 1, 'message' => 'Request completed successfully!'];
                } else {
                    $hbl_konnect_transaction = new HblKonnectTransaction();
                    $hbl_konnect_transaction->transaction_id = $transaction_id;
                    $hbl_konnect_transaction->delivery_note_id = $delivery_note_id;
                    $hbl_konnect_transaction->amount = $amount;
                    $hbl_konnect_transaction->save();


                    $delivery_note = DeliveryNote::where('id', $delivery_note_id);
                    if ($delivery_note->exists()) {
                        $delivery_note = $delivery_note->first();
                    }
                    $transaction_amount = $amount;

                    $hbl_konnect_transaction_delivery_note = HblKonnectTransactionDeliveryNote::where('delivery_note_id', $delivery_note_id);
                    if ($hbl_konnect_transaction_delivery_note->exists()) {
                        $hbl_konnect_transaction_delivery_note = $hbl_konnect_transaction_delivery_note->first();
                        $transaction_amount = $hbl_konnect_transaction_delivery_note->transactions_amount + $amount;
                    } else {
                        $hbl_konnect_transaction_delivery_note = new HblKonnectTransactionDeliveryNote();
                        $hbl_konnect_transaction_delivery_note->delivery_note_id = $delivery_note_id;
                    }

                    $cash_amount = $delivery_note->received_cod_amount - $transaction_amount;
                    $hbl_konnect_transaction_delivery_note->transactions_amount = $transaction_amount;
                    $hbl_konnect_transaction_delivery_note->cash_amount = $cash_amount;
                    $hbl_konnect_transaction_delivery_note->save();

                    return ['status' => 1, 'message' => 'Request completed successfully!'];
                }
            }
        } else {
            return ['status' => 2, 'message' => 'Access Denied!'];
        }
    }
    public function hbl_konnect_delivery_note_information(Request $request)
    {
        $valid_ip_addresses = array();
        $valid_ip_addresses[] = '103.111.84.67';
        $valid_ip_addresses[] = '103.111.85.67';
        $environment = config('app.env');

        if ($environment == 'production') {
            $whip = new Whip();
            $ip_address = $whip->getValidIpAddress();
            if (in_array($ip_address, $valid_ip_addresses)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = true;
        }
        if ($flag) {
            $rules = [
                'delivery_note_id' => ['required', 'integer', Rule::exists('delivery_notes', 'id')]
            ];
            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $errors = array();
                foreach ($validate->errors()->all() as $index => $error) {
                    $errors[$index]['error_code'] = 10;
                    $errors[$index]['error_text'] = 'Invalid Input.';
                    if ($error == 'delivery note id is Required.') {
                        $errors[$index]['error_code'] = 1;
                        $errors[$index]['ERROR_TEXT'] = 'Delivery note id is Required.';
                    }
                    if ($error == 'delivery note id must be an Integer.') {
                        $errors[$index]['error_code'] = 2;
                        $errors[$index]['error_text'] = 'Delivery note id must be an Integer.';
                    }
                    if ($error == 'Given delivery note id is of Invalid ID.') {
                        $errors[$index]['error_code'] = 3;
                        $errors[$index]['error_text'] = 'Given delivery note id is of Invalid ID.';
                    }
                }
                return response()->json(['status' => 0, 'message' => 'Error(s) in Input', 'errors' => $errors]);
            } else {
                $delivery_note_id = $request->delivery_note_id;
                $delivery_note = DeliveryNote::where('id', $delivery_note_id);
                if ($delivery_note->exists()) {
                    $delivery_note = $delivery_note->first();
                    $min_date = Carbon::parse('01-07-2022 00:00:00')->toDateTimeString();
                    if ($delivery_note->created_at >= $min_date) {
                        $hbl_konnect_transaction_delivery_note = HblKonnectTransactionDeliveryNote::where('delivery_note_id', $delivery_note->id);
                        $transactions_amount = 0;
                        if ($hbl_konnect_transaction_delivery_note->exists()) {
                            $hbl_konnect_transaction_delivery_note = $hbl_konnect_transaction_delivery_note->first();
                            $transactions_amount = $hbl_konnect_transaction_delivery_note->transactions_amount;
                        }
                        $net_amount = $delivery_note->received_cod_amount - $transactions_amount;
                        $rider_id = $delivery_note->rider_id;
                        $rider = Rider::where('id', $rider_id);
                        if ($rider->exists()) {
                            $rider = $rider->first();
                            $rider_name = $rider->name;
                            $rider_trax_id = $rider->trax_id;
                        }
                        return response()->json(['status' => 1, 'delivery_note_id' =>  str_pad($delivery_note->id, 6, '0', STR_PAD_LEFT), 'amount' => $net_amount, 'rider_name' => $rider_name, 'rider_trax_id' => $rider_trax_id]);
                    } else {
                        return response()->json(['status' => 0, 'message' => 'Delivery Note restricted!']);
                    }
                } else {
                    return response()->json(['status' => 0, 'message' => 'Delivery Note Not Found!']);
                }
            }
        } else {
            return ['status' => 2, 'message' => 'Access Denied!'];
        }
    }

    public function out_for_delivery_shipment_payment(Request $request)
    {
        $rules = [
            'consumer_number' => ['required', 'integer'],
            'transaction_authentication_id' => ['required'],
            'transaction_amount' => ['required'],
            'transaction_date' => ['required'],
            'transaction_time' => ['required'],
            'bank_mnemonic' => ['required'],
            'reserved' => ['nullable'],
            'consumer_prefix' => ['required'],
            'tracking_number' => ['required'],
            'shipment_id' => ['required', Rule::exists('shipments', 'id')],
            'delivery_note_id' => ['required', Rule::exists('delivery_notes', 'id')]
        ];

        $validate = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $consumer_number = $request->consumer_number;
            $transaction_authentication_id = $request->transaction_authentication_id;
            $transaction_amount = $request->transaction_amount;
            $transaction_date = $request->transaction_date;
            $transaction_time = $request->transaction_time;
            $bank_mnemonic = $request->bank_mnemonic;
            $reserved = $request->reserved;
            $consumer_prefix = $request->consumer_prefix;
            $tracking_number = $request->tracking_number;
            $shipment_id = $request->shipment_id;
            $delivery_note_id = $request->delivery_note_id;

            $one_link_charges = OneLinkPaymentChargesRange::where('range_up', '<', $transaction_amount)->where('range_down', '>', $transaction_amount);

            if ($one_link_charges->exists()) {
                $one_link_charges = $one_link_charges->first();

                $charges = $one_link_charges->charges;
            } else {
                $charges = 0;
            }

            $one_link_payment_transaction = new OneLinkOutForDeliveryShipmentPayment();
            $one_link_payment_transaction->consumer_number = $consumer_number;
            $one_link_payment_transaction->transaction_authentication_id = $transaction_authentication_id;
            $one_link_payment_transaction->transaction_amount = $transaction_amount;
            $one_link_payment_transaction->one_link_charges = $charges;
            $one_link_payment_transaction->transaction_date = $transaction_date;
            $one_link_payment_transaction->transaction_time = $transaction_time;
            $one_link_payment_transaction->bank_mnemonic = $bank_mnemonic;
            $one_link_payment_transaction->reserved = $reserved;
            $one_link_payment_transaction->consumer_prefix = $consumer_prefix;
            $one_link_payment_transaction->tracking_number = $tracking_number;
            $one_link_payment_transaction->shipment_id = $shipment_id;
            $one_link_payment_transaction->delivery_note_id = $delivery_note_id;
            $one_link_payment_transaction->save();

            $delivery_note = DeliveryNote::find($delivery_note_id);
            $update_count = $delivery_note->one_link_payment_count + 1;
            $delivery_note->one_link_payment_count = $update_count;
            $delivery_note->save();
            $amount = OneLinkOutForDeliveryShipmentPayment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment_id)->sum('transaction_amount');
            $shipment = Shipment::find($shipment_id);
            $shipment->received_amount = $amount;
            $shipment->save();

            NotificationsController::app_notification(19, $delivery_note->rider_id, 2, $delivery_note->rider_id, $one_link_payment_transaction->id);
            NotificationsController::send(185, $delivery_note->rider_id, $one_link_payment_transaction->id);

            return response()->json(['status' => 0, 'message' => 'Successful Bill Payment']);
        }
    }


    public function onelink_payment_billinquiry(Request $request)
    {
        $valid_ip_addresses = array();
        $valid_ip_addresses[] = '103.111.84.67';
        $environment = config('app.env');

        if ($environment == 'production') {
            $whip = new Whip();
            $ip_address = $whip->getValidIpAddress();
            if (in_array($ip_address, $valid_ip_addresses)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = true;
        }
        if ($flag) {

            $username = $request->header('username');
            $password = $request->header('password');

            $return_data['response_Code'] = "";
            $return_data['consumer_Detail'] = "";
            $return_data['bill_status'] = "";
            $return_data['due_date'] = "";
            $return_data['amount_within_dueDate'] = "";
            $return_data['amount_after_dueDate'] = "";
            $return_data['billing_month'] = "";
            $return_data['date_paid'] = "";
            $return_data['amount_paid'] = "";
            $return_data['tran_auth_Id'] = "";
            $return_data['reserved'] = "";

            // getting body data
            $request_data['consumer_number'] = $request->input('consumer_number');
            $request_data['bank_mnemonic'] = $request->input('bank_mnemonic');
            $request_data['reserved'] = $request->input('reserved');

            if (isset($username) && isset($password) && isset($request_data['consumer_number']) && isset($request_data['bank_mnemonic'])) {
                $authenticate = OneLink::where("username", $username)->where("active", 0)->first();

                if ($authenticate) {
                    if (Hash::check($password, $authenticate->password) && $authenticate->bank_mnemonic == $request_data['bank_mnemonic']) {


                        // explode consumer_prefx from consumer_number
                        $consumer_prefx  = substr($request_data['consumer_number'], 0, 6);

                        // explode tracking number from consumer_number
                        $tracking_no  = substr($request_data['consumer_number'], 6);

                        // getting shipment data according to tracking no provided via body parameter $tracking_no
                        $shipment_data = Shipment::join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
                            ->where('shipments.tracking_number', $tracking_no)
                            ->select('shipments.*', 'ss.code as status_code', 'ss.name as status_name', 'ss.description as status_desc', 'ss.status as status_status', 'ss.id as status_id')
                            ->first();

                        // blocked shipments ids are mentioned in $blocked_shipments
                        $blocked_shipments = array(17, 20, 21, 22, 23, 24, 25, 44, 47, 48, 50, 57, 60);

                        if ($shipment_data) {
                            if (!in_array($shipment_data->status_id, $blocked_shipments)) {
                                if ($shipment_data->amount > 0) {
                                    // response_code 00 work
                                    $return_data['response_Code'] = "00";
                                    $return_data['consumer_Detail'] = $shipment_data->consignee_name;

                                    // check bill status
                                    if ($shipment_data->amount == $shipment_data->received_amount) {
                                        // Bill paid status
                                        $transaction_data = OneLinkOutForDeliveryShipmentPayment::with('shipment_data')->where('tracking_number', $tracking_no)->first();

                                        $return_data['response_Code'] = "06";
                                        $return_data['bill_status'] = "P";
                                        $return_data['date_paid'] = isset($transaction_data->tran_date) ? $transaction_data->tran_date : ""; // getting date from 1link transaction table after creating migration
                                        $return_data['amount_paid'] = isset($transaction_data->transaction_amount) ? $transaction_data->transaction_amount : ""; // getting paid amount from 1link transaction table after creating migration
                                        $return_data['tran_auth_Id'] = isset($transaction_data->tran_auth_id) ? $transaction_data->tran_auth_id : ""; // getting tran_auth_Id from 1link transaction table after creating migration
                                        $return_data['reserved'] = "bill already paid";
                                    } else {
                                        // Bill Unpaid status
                                        $return_data['bill_status'] = "U";
                                    }

                                    // creating due date yyyMMdd
                                    $return_data['due_date'] = Carbon::parse($shipment_data->created_at)->format('Ymd');

                                    // creating amount -- will always set + prefix if amount is not negative otherwise - if negative
                                    // total length is 14 
                                    // 1 for + or - prefix
                                    // 11 digit for amount
                                    // 2 last digit for decimal values
                                    // example amount is 120 filling length +0000000012000

                                    $amount_length = strlen($shipment_data->amount);

                                    // prefix + because we only have possitive value to be collected
                                    $return_data['amount_within_dueDate'] .= "+";

                                    // adding 0 before amount to fill required length
                                    for ($i = 1; $i <= 11 - $amount_length; $i++) {
                                        $return_data['amount_within_dueDate'] .= "0";
                                    }

                                    $return_data['amount_within_dueDate'] .= $shipment_data->amount;

                                    // adding 00 for decimal value Required for 1Link API
                                    $return_data['amount_within_dueDate'] .= "00";

                                    // $return_data['amount_after_dueDate'] is same as $return_data['amount_within_dueDate'] because Trax is not charging for late payment
                                    $return_data['amount_after_dueDate'] = $return_data['amount_within_dueDate'];

                                    // creating billing month
                                    $return_data['billing_month'] = Carbon::parse($shipment_data->created_at)->format('ym');

                                    // reserved field is optional
                                    $return_data['reserved'] = $shipment_data->status_desc;
                                } else {
                                    // if shipment amount is 0 or less
                                    $return_data['response_Code'] = "01";
                                    $return_data['reserved'] = "consumer number does not exist";
                                }
                            } else {
                                // if shipment is blocked response_Code return 02
                                $return_data['response_Code'] = "02";
                                $return_data['consumer_Detail'] = $shipment_data->consignee_name;
                                $return_data['bill_status'] = "B";
                                $return_data['reserved'] = "consumer number block";
                            }
                        } else {

                            // if shipment is not exist in DB 
                            $return_data['response_Code'] = "01";
                            $return_data['reserved'] = "consumer number does not exist";
                        }

                        // return status 200 due to sucessfully Inquiry
                        return json_encode(['status' => 200, 'message' => 'Inquiry Data Found', 'result' =>  $return_data]);
                    } else {
                        // Unauthorized when password is incorrect
                        $return_data['response_Code'] = "04";
                        $return_data['reserved'] = "invalid username or password or bank mnemonic";

                        return json_encode(['status' => 200, 'message' => 'Invalid Data', 'result' =>  $return_data]);
                    }
                } else {
                    // Unauthorized! Invalid username or password when username and password both are incorrect

                    $return_data['response_Code'] = "04";
                    $return_data['reserved'] = "invalid username or password";

                    return json_encode(['status' => 200, 'message' => 'Invalid Data', 'result' =>  $return_data]);
                }
            } else {
                $return_data['response_Code'] = "03";
                $return_data['reserved'] = "unknown error/bad transaction";

                return json_encode(['status' => 200, 'message' => 'Invalid Data', 'result' =>  $return_data]);
            }
        } else {
            // Access Forbidden! when ip is not not matched with given in above code in production environment
            return ['status' => 403, 'message' => 'Access Forbidden!'];
        }
    }

    public function onelink_payment_billpayment(Request $request)
    {
        $valid_ip_addresses = array();
        $valid_ip_addresses[] = '103.111.84.67';
        $environment = config('app.env');

        if ($environment == 'production') {
            $whip = new Whip();
            $ip_address = $whip->getValidIpAddress();
            if (in_array($ip_address, $valid_ip_addresses)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = true;
        }
        if ($flag) {

            $username = $request->header('username');
            $password = $request->header('password');

            $return_data['response_Code'] = "";
            $return_data['Identification_parameter'] = "";
            $return_data['reserved'] = "";

            // getting body data
            $request_data['consumer_number'] = $request->input('consumer_number');
            $request_data['tran_auth_id'] = $request->input('tran_auth_id');
            $request_data['transaction_amount'] = $request->input('transaction_amount');
            $request_data['tran_date'] = $request->input('tran_date');
            $request_data['tran_time'] = $request->input('tran_time');
            $request_data['bank_mnemonic'] = $request->input('bank_mnemonic');
            $request_data['reserved'] = $request->input('reserved');

            if (isset($username) && isset($password) && isset($request_data['consumer_number']) && isset($request_data['tran_auth_id']) && isset($request_data['transaction_amount']) && isset($request_data['tran_date']) && isset($request_data['tran_time']) && isset($request_data['bank_mnemonic'])) {
                $authenticate = OneLink::where("username", $username)->where("active", 0)->first();

                if ($authenticate) {
                    // return json_encode($request_data);
                    if (Hash::check($password, $authenticate->password) && $authenticate->bank_mnemonic == $request_data['bank_mnemonic']) {
                        // explode consumer_prefx from consumer_number
                        $consumer_prefx  = substr($request_data['consumer_number'], 0, 6);

                        // explode tracking number from consumer_number
                        $tracking_no  = substr($request_data['consumer_number'], 6);

                        // getting shipment data according to tracking no provided via body parameter $tracking_no
                        $shipment_data = Shipment::join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
                            ->where('shipments.tracking_number', $tracking_no)
                            ->select('shipments.*', 'ss.code as status_code', 'ss.name as status_name', 'ss.description as status_desc', 'ss.status as status_status', 'ss.id as status_id')
                            ->first();

                        // blocked shipments ids are mentioned in $blocked_shipments
                        // $blocked_shipments = array(17,20,21,22,23,24,25,44,47,48,50,57,60);

                        if ($shipment_data) {
                            $transaction_data = OneLinkOutForDeliveryShipmentPayment::where('tracking_number', $tracking_no)->first();

                            if ($transaction_data) {
                                if ($transaction_data->consumer_number == $request_data['consumer_number'] && $transaction_data->tran_auth_id == $request_data['tran_auth_id'] && $transaction_data->tran_date == $request_data['tran_date'] && $transaction_data->tran_time == $request_data['tran_time']) {
                                    $return_data['response_Code'] = "03";
                                    $return_data['reserved'] = "duplicate transaction";
                                } else {
                                    $return_data['response_Code'] = "06";
                                    $return_data['reserved'] = "bill already paid";
                                }
                            } else {

                                if ($shipment_data->amount == $shipment_data->received_amount) {
                                    // response_code 00 work
                                    $return_data['response_Code'] = "06";
                                    $return_data['reserved'] = "bill already paid";
                                } else {

                                    $transfer_amount = ((int)$request_data['transaction_amount']) / 100;
                                    $tran_date_formated = Carbon::parse($request_data['tran_date'])->format('Y-m-d');
                                    $tran_time_formated = Carbon::parse($request_data['tran_time'])->format('h:i:s');

                                    $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_data->id)->orderBy('delivery_note_id', 'desc')->first();
                                    $delivery_note = $delivery_note_shipment->delivery_note_id;

                                    $delivery_note_data = DeliveryNote::where('id', $delivery_note)->first();

                                    $update_count = $delivery_note_data->one_link_payment_count + 1;

                                    DeliveryNote::where('id', $delivery_note)->update(['one_link_payment_count' => $update_count]);


                                    $request_data['consumer_prefx'] = $consumer_prefx;
                                    $request_data['tracking_no'] = $tracking_no;
                                    $request_data['shipment_id'] = $shipment_data->id;
                                    $request_data['amount'] = $transfer_amount;
                                    $request_data['tran_date_formated'] = $tran_date_formated;
                                    $request_data['tran_time_formated'] = $tran_time_formated;
                                    $request_data['delivery_note_id'] = $delivery_note;

                                    $upload_transaction = OneLinkOutForDeliveryShipmentPayment::create($request_data);


                                    if ($upload_transaction) {
                                        $return_data['response_Code'] = "00";
                                        $return_data['Identification_parameter'] = $shipment_data->consignee_name;
                                        $return_data['reserved'] = "successful bill payment";
                                        Shipment::where('tracking_number', $tracking_no)->update(['received_amount' => $transfer_amount]);
                                        NotificationsController::app_notification(19, $delivery_note_data->rider_id, 2, $delivery_note_data->rider_id, $upload_transaction->id);
                                        NotificationsController::send(185, $delivery_note_data->rider_id, $upload_transaction->id);
                                        return json_encode(['status' => 200, 'message' => 'Successful Bill Payment', 'result' =>  $return_data]);
                                    } else {
                                        $return_data['response_Code'] = "02";
                                        $return_data['reserved'] = "unknown error / bad transaction";

                                        return json_encode(['status' => 200, 'message' => 'Unknown Error / Bad Transaction', 'result' =>  $return_data]);
                                    }
                                }
                            }
                        } else {
                            // if shipment is not exist in DB 
                            $return_data['response_Code'] = "01";
                            $return_data['reserved'] = "consumer number does not exist";
                        }
                        // return status 200 due to sucessfully Inquiry
                        return json_encode(['status' => 200, 'message' => 'Inquiry Data Found', 'result' =>  $return_data]);
                    } else {
                        // Unauthorized when password is incorrect
                        $return_data['response_Code'] = "04";
                        $return_data['reserved'] = "invalid username or password or bank mnemonic";

                        return json_encode(['status' => 200, 'message' => 'Invalid Data', 'result' =>  $return_data]);
                    }
                } else {
                    // Unauthorized! Invalid username or password when username and password both are incorrect
                    $return_data['response_Code'] = "04";
                    $return_data['reserved'] = "invalid username or password";

                    return json_encode(['status' => 200, 'message' => 'Invalid Data', 'result' =>  $return_data]);
                }
            } else {
                $return_data['response_Code'] = "02";
                $return_data['reserved'] = "unknown error / bad transaction";

                return json_encode(['status' => 200, 'message' => 'Unknown Error / Bad Transaction', 'result' =>  $return_data]);
            }
        } else {
            // Access Forbidden! when ip is not not matched with given in above code in production environment
            return ['status' => 403, 'message' => 'Access Forbidden!'];
        }
    }

    //BOTSIFY WhatsApp API
    public function whatsapp_shipper_phone_number(Request $request)
    {
        //        if (strstr(strtolower(gethostbyaddr($_SERVER['REMOTE_ADDR'])), 'app.botsify.com')) {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $phone_number = substr($request->phone_number, 0, 4) . '-' . substr($request->phone_number, 4, 7);
            $shipper = User::where('phone', $phone_number);
            if ($shipper->exists()) {
                $shipper = $shipper->first();
                $current_status['Status'] = 1;
                $current_status['StatusText'] = 'SUCCESS';
                $current_status['Date'] = Carbon::now()->toIso8601String();
                $current_status['Success'] = 'Shipper found against phone number: ' . $phone_number;

                $details['UserID'] = $shipper->id;
                $details['UserName'] = $shipper->name;
            } else {
                $sub_shipper = SubstituteUser::where('phone_number', $phone_number);
                if ($sub_shipper->exists()) {
                    $sub_shipper = $sub_shipper->first();
                    $current_status['Status'] = 1;
                    $current_status['StatusText'] = 'SUCCESS';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Success'] = 'Substitute Shipper found against phone number: ' . $phone_number;

                    $details['UserID'] = $sub_shipper->user_id;
                    $details['UserName'] = $sub_shipper->shipper->name;
                    $details['SubstituteUserName'] = $sub_shipper->name;
                } else {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'No shipper exists against phone number: ' . $phone_number;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            }

            return response()->json(['CurrentStatus' => $current_status, 'Details' => $details]);
        }
        //        } else {
        //            $current_status = array();
        //
        //            $current_status['Status'] = 'ERROR';
        //            $current_status['Date'] = Carbon::now()->toIso8601String();
        //            $current_status['Error'] = 'Unauthorized Host';
        //
        //            return response()->json(['CurrentStatus' => $current_status]);
        //        }
    }

    public function whatsapp_shipper_tracking(Request $request)
    {
        //        if (strstr(strtolower(gethostbyaddr($_SERVER['REMOTE_ADDR'])), 'app.botsify.com')) {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'tracking_number' => ['required', 'integer', 'digits_between:10,20'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $tracking_number = $request->tracking_number;
            $phone_number = substr($request->phone_number, 0, 4) . '-' . substr($request->phone_number, 4, 7);

            $shipper = User::where('phone', $phone_number);
            if ($shipper->exists()) {
                $shipper = $shipper->first();

                $details['UserID'] = $shipper->id;
                $details['UserName'] = $shipper->name;
            } else {
                $sub_shipper = SubstituteUser::where('phone_number', $phone_number);
                if ($sub_shipper->exists()) {
                    $sub_shipper = $sub_shipper->first();

                    $details['UserID'] = $sub_shipper->user_id;
                    $details['UserName'] = $sub_shipper->shipper->name;
                    $details['SubstituteUserName'] = $sub_shipper->name;

                    $shipper = $sub_shipper->shipper;
                } else {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'No Shipper/Substitute Shipper exists against phone number: ' . $phone_number;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            }
        }
        $shipment = Shipment::where('tracking_number', $tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->where('user_id', $shipper->id);
            if ($shipment->exists()) {
                $shipment = $shipment->first();
                $current_status['Status'] = 1;
                $current_status['StatusText'] = 'SUCCESS';
                $current_status['Date'] = Carbon::now()->toIso8601String();
                $current_status['Success'] = 'Shipment found against Tracking Number: ' . $tracking_number;

                $details['ServiceType'] = $shipment->booking_type->booking_type;
                $details['OriginCity'] = $shipment->pickup_address->city->name;
                $details['DestinationCity'] = $shipment->consignee_city->name;
                $details['ConsigneeName'] = $shipment->consignee_name;
                $details['ConsigneeAddress'] = $shipment->consignee_address;
                $details['ConsigneePhone'] = $shipment->consignee_phone_number_1;
                if ($shipment->consignee_phone_number_2 != null) {
                    $details['ConsigneePhone'] .= ' / ' . $shipment->consignee_phone_number_2;
                }
                $details['OrderID'] = $shipment->order_id;
                $details['Amount'] = $shipment->amount;
                $details['CurrentStatus'] = $shipment->status_shipper->name;

                if ($shipment->status == 5) {
                    $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment->id)->orderBy('id', 'desc')->first();
                    $delivery_note = $delivery_note_shipment->delivery_note;
                    $rider = $delivery_note->rider;

                    $details['RiderName'] = $rider->name;
                    $details['RiderPhoneNumber'] = $rider->phone;
                }

                //                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('verification', 1)->get();
                //                    foreach ($shipment_journey as $journey){
                //                        $details['Journey'][]['ShipmentStatus'] = $journey->shipment_status_shipper->name;
                //                        $details['Journey'][]['StatusDateTime'] = Carbon::parse($journey->created_at)->toDateTimeString();
                //                    }
            } else {
                $current_status['Status'] = 0;
                $current_status['StatusText'] = 'ERROR';
                $current_status['Date'] = Carbon::now()->toIso8601String();
                $current_status['Error'] = 'Shipment does\'nt belongs to ' . $shipper->name;

                return response()->json(['CurrentStatus' => $current_status]);
            }
        } else {
            $current_status['Status'] = 0;
            $current_status['StatusText'] = 'ERROR';
            $current_status['Date'] = Carbon::now()->toIso8601String();
            $current_status['Error'] = 'Shipment not found against Tracking Number: ' . $tracking_number;

            return response()->json(['CurrentStatus' => $current_status]);
        }

        return response()->json(['CurrentStatus' => $current_status, 'Details' => $details]);
        //        } else {
        //            $current_status = array();
        //
        //            $current_status['Status'] = 'ERROR';
        //            $current_status['Date'] = Carbon::now()->toIso8601String();
        //            $current_status['Error'] = 'Unauthorized Host';
        //
        //            return response()->json(['CurrentStatus' => $current_status]);
        //        }
    }

    public function whatsapp_crm_request_create(Request $request)
    {
        //        if (strstr(strtolower(gethostbyaddr($_SERVER['REMOTE_ADDR'])), 'app.botsify.com')) {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'tracking_number' => ['required', 'integer', 'digits_between:10,20'],
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $phone_number = substr($request->phone_number, 0, 4) . '-' . substr($request->phone_number, 4, 7);
            $shipper = User::where('phone', $phone_number);
            if ($shipper->exists()) {
                $shipper = $shipper->first();

                $details['UserID'] = $shipper->id;
                $details['UserName'] = $shipper->name;

                $launched_by = 1;
                $user_id = $shipper->id;
            } else {
                $sub_shipper = SubstituteUser::where('phone_number', $phone_number);
                if ($sub_shipper->exists()) {
                    $sub_shipper = $sub_shipper->first();

                    $details['UserID'] = $sub_shipper->user_id;
                    $details['UserName'] = $sub_shipper->shipper->name;
                    $details['SubstituteUserName'] = $sub_shipper->name;

                    $shipper = $sub_shipper->shipper;
                    $user_id = $shipper->id;

                    $launched_by = 2;
                } else {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'No Shipper/Substitute Shipper exists against phone number: ' . $phone_number;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            }

            $nature_id = $request->case_nature_id;
            $complaint_id = $request->case_nature_type_id;
            $description = $request->description;
            $tracking_number = $request->tracking_number;

            $shipment = Shipment::where('tracking_number', $tracking_number);
            if ($shipment->exists()) {
                $shipment = $shipment->where('user_id', $user_id);
                if ($shipment->exists()) {
                    $shipment = $shipment->first();

                    if ($nature_id == 1 || $nature_id == 2) {
                        //complaints
                        $crm_request_type = CrmRequestCaseNatureType::where('nature_id', $nature_id)->where('status_id', 1)->pluck('id')->toArray();
                        if (in_array($complaint_id, $crm_request_type)) {
                            $rules = [
                                'description' => ['required'],
                            ];
                            $validate = Validator::make($request->all(), $rules, $this->messages);

                            $validate->setAttributeNames($this->names);

                            if ($validate->fails()) {
                                return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                            } else {
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', $nature_id)->exists()) {
                                    return response()->json(['status' => 1, 'message' => 'Same Request already against given Tracking Number exists!']);
                                } else {
                                    $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description);
                                }
                                return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                        }
                    } elseif ($nature_id == 4) {
                        $crm_request_type = CrmRequestCaseNatureType::where('nature_id', $nature_id)->where('status_id', 1)->pluck('id')->toArray();
                        if (in_array($complaint_id, $crm_request_type)) {
                            if ($complaint_id == 26) {
                                if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', $nature_id)->exists()) {
                                    return response()->json(['status' => 1, 'message' => 'Same Request already against given Tracking Number exists!']);
                                } else {
                                    $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description);
                                }
                                return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                            } elseif ($complaint_id == 21) {
                                $rules = [
                                    'product_cost' => ['required', 'integer'],
                                    'product_picture' => ['required', 'url'],
                                    'invoice_picture' => ['required', 'url'],
                                    'damage_product_picture' => ['required', 'url'],
                                    'product_packaging_picture' => ['required', 'url'],
                                    'actual_product_picture' => ['required', 'url'],
                                    'damage_product_price' => ['required', 'integer'],
                                    'description' => ['required'],

                                ];
                                $validate = Validator::make($request->all(), $rules, $this->messages);

                                $validate->setAttributeNames($this->names);

                                if ($validate->fails()) {
                                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                                } else {

                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', $nature_id)->exists()) {
                                        return response()->json(['status' => 1, 'message' => 'Same Request already against given Tracking Number exists!']);
                                    } else {
                                        $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->product_picture, $request->invoice_picture, $request->damage_product_picture, $request->product_packaging_picture, $request->actual_product_picture, $request->damage_product_price);
                                    }

                                    return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                                }
                            } elseif ($complaint_id == 22) {
                                $rules = [
                                    'product_cost' => ['required', 'integer'],
                                    'product_picture' => ['required', 'url'],
                                    'invoice_picture' => ['required', 'url'],
                                    'missing_product_picture' => ['required', 'url'],
                                    'product_packaging_picture' => ['required', 'url'],
                                    'actual_product_picture' => ['required', 'url'],
                                    'missing_product_price' => ['required', 'integer'],
                                    'description' => ['required'],


                                ];
                                $validate = Validator::make($request->all(), $rules, $this->messages);

                                $validate->setAttributeNames($this->names);

                                if ($validate->fails()) {
                                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                                } else {
                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', $nature_id)->exists()) {
                                        return response()->json(['status' => 1, 'message' => 'Same Request already against given Tracking Number exists!']);
                                    } else {
                                        $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->product_picture, $request->invoice_picture, Null, Null, Null, Null, $request->missing_product_picture, $request->product_packaging_picture, $request->actual_product_picture, $request->missing_product_price);
                                    }
                                    return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                                }
                            } elseif ($complaint_id == 29 || $complaint_id == 25 || $complaint_id == 24 || $complaint_id == 23) {
                                $rules = [
                                    'product_picture' => ['required', 'url'],
                                    'invoice_picture' => ['required', 'url'],
                                    'product_cost' => ['required', 'integer'],
                                    'description' => ['required'],

                                ];
                                // $request->product_cost;
                                $validate = Validator::make($request->all(), $rules, $this->messages);

                                $validate->setAttributeNames($this->names);

                                if ($validate->fails()) {
                                    return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
                                } else {
                                    if (CrmRequest::where('shipment_id', $shipment->id)->where('case_nature_id', $nature_id)->exists()) {
                                        return response()->json(['status' => 1, 'message' => 'Same Request already against given Tracking Number exists!']);
                                    } else {
                                        $crm_request = CRMController::add($nature_id, $complaint_id, 1, 1, $user_id, $launched_by, $shipment->id, $user_id, null, $description, $request->product_cost, $request->product_picture, $request->invoice_picture);
                                    }
                                    return response()->json(['status' => 0, 'message' => 'CRM Request has been added', 'id' => $crm_request]);
                                }
                            } else {
                                return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                            }
                        } else {
                            return response()->json(['status' => 1, 'message' => 'case_nature_type_id not found!']);
                        }
                    } else {
                        return response()->json(['status' => 1, 'message' => 'case_nature_id should be 1 (Complaints), 2 (Service Request) and 4 (Claims)']);
                    }
                } else {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'Shipment does\'nt belongs to ' . $shipper->name;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            } else {
                $current_status['Status'] = 0;
                $current_status['StatusText'] = 'ERROR';
                $current_status['Date'] = Carbon::now()->toIso8601String();
                $current_status['Error'] = 'Shipment not found against Tracking Number: ' . $tracking_number;

                return response()->json(['CurrentStatus' => $current_status]);
            }
        }
        //            }
        //        else {
        //            $current_status = array();
        //
        //            $current_status['Status'] = 'ERROR';
        //            $current_status['Date'] = Carbon::now()->toIso8601String();
        //            $current_status['Error'] = 'Unauthorized Host';
        //
        //            return response()->json(['CurrentStatus' => $current_status]);
        //        }
    }
    public function whatsapp_shipper_crm_tracking(Request $request)
    {
        //        if (strstr(strtolower(gethostbyaddr($_SERVER['REMOTE_ADDR'])), 'app.botsify.com')) {
        $rules = [
            'phone_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'tracking_number' => ['required_without:crm_request_id', 'integer', 'digits_between:10,20'],
            'crm_request_id' => ['required_without:tracking_number', 'integer', 'digits_between:3,10']
        ];
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {
            $phone_number = substr($request->phone_number, 0, 4) . '-' . substr($request->phone_number, 4, 7);
            $shipper = User::where('phone', $phone_number);
            if ($shipper->exists()) {
                $shipper = $shipper->first();

                $details['UserID'] = $shipper->id;
                $details['UserName'] = $shipper->name;
                $user_id = $shipper->id;
            } else {
                $sub_shipper = SubstituteUser::where('phone_number', $phone_number);
                if ($sub_shipper->exists()) {
                    $sub_shipper = $sub_shipper->first();

                    $details['UserID'] = $sub_shipper->user_id;
                    $details['UserName'] = $sub_shipper->shipper->name;
                    $details['SubstituteUserName'] = $sub_shipper->name;

                    $shipper = $sub_shipper->shipper;
                    $user_id = $shipper->id;
                } else {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'No Shipper/Substitute Shipper exists against phone number: ' . $phone_number;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            }
        }
        $crm_request_id = $request->crm_request_id;
        $tracking_number = $request->tracking_number;
        if ($tracking_number) {
            $shipment = Shipment::where('tracking_number', $tracking_number);
            if ($shipment->exists()) {
                $shipment = $shipment->where('user_id', $user_id);
                if ($shipment->exists()) {
                    $shipment = $shipment->first();
                    $crm_request = CrmRequest::where('shipment_id', $shipment->id)->orderBy('id', 'desc');
                    if ($crm_request->exists()) {
                        $crm_request = $crm_request->first();
                    } else {
                        $current_status['Status'] = 0;
                        $current_status['StatusText'] = 'ERROR';
                        $current_status['Date'] = Carbon::now()->toIso8601String();
                        $current_status['Error'] = 'CRM Request not found against Tracking Number: ' . $tracking_number;

                        return response()->json(['CurrentStatus' => $current_status]);
                    }
                } else {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'Shipment does\'nt belongs to ' . $shipper->name;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            }
        } else {
            $crm_request = CrmRequest::find($crm_request_id);
            if ($crm_request) {
                if ($crm_request->shipper_id != $user_id) {
                    $current_status['Status'] = 0;
                    $current_status['StatusText'] = 'ERROR';
                    $current_status['Date'] = Carbon::now()->toIso8601String();
                    $current_status['Error'] = 'CRM Request does\'nt belongs to ' . $shipper->name;

                    return response()->json(['CurrentStatus' => $current_status]);
                }
            } else {
                $current_status['Status'] = 0;
                $current_status['StatusText'] = 'ERROR';
                $current_status['Date'] = Carbon::now()->toIso8601String();
                $current_status['Error'] = 'CRM Request not found against CRM ID: ' . $crm_request_id;

                return response()->json(['CurrentStatus' => $current_status]);
            }
        }

        $current_status['Status'] = 1;
        $current_status['StatusText'] = 'SUCCESS';
        $current_status['Date'] = Carbon::now()->toIso8601String();
        $current_status['Success'] = 'CRM Request found!';

        $details['CaseNature'] = $crm_request->nature->name;
        if ($crm_request->case_nature_type_id != null) {
            $details['CaseNatureType'] = $crm_request->nature_type->type;
        }
        $details['Description'] = $crm_request->description;
        $status = '';
        if ($crm_request->status_id == 1) {
            $status = 'Launched';
        } else if ($crm_request->status_id == 2) {
            $status = 'In-Process';
        } else if ($crm_request->status_id == 3) {
            $status = 'Resolved';
        } else if ($crm_request->status_id == 4) {
            $status = 'Closed';
        } else if ($crm_request->status_id == 5) {
            $status = 'Re-Open';
        }
        $details['Status'] = $status;
        if ($crm_request->shipment_id != null) {
            $details['ShipmentTrackingNumber'] = $crm_request->shipment->tracking_number;
            $details['ShipmentCurrentStatus'] = $crm_request->shipment->status_shipper->name;
        }

        return response()->json(['CurrentStatus' => $current_status, 'Details' => $details]);
        //        } else {
        //            $current_status = array();
        //
        //            $current_status['Status'] = 'ERROR';
        //            $current_status['Date'] = Carbon::now()->toIso8601String();
        //            $current_status['Error'] = 'Unauthorized Host';
        //
        //            return response()->json(['CurrentStatus' => $current_status]);
        //        }
    }
    //BOTSIFY WhatsApp API


    function check_bdmk($city_id, $consignee_address, $check_bdmk, $city_name)
    {

        if (isset($city_id)) {

            $str_arr = null;
            $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $consignee_address);
            $found_keyword = array();
            $result = array();
            foreach ($check_bdmk as $nsa) {
                foreach ($str_arr as $arr_value) {
                    $arr_value = trim($arr_value);
                    if (strtolower($nsa) == strtolower($arr_value)) {
                        array_push($found_keyword, $arr_value);
                    }
                }
            }
            $invalid_cities  = array();
            $invalid_cities_string = "";
            if ($found_keyword) {
                $data_found = BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', 'bdm.id', '=', 'booking_destination_mapping_keywords.mapping_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'bdm.city_id')
                    ->select('bdm.city_id', 'c.name as city_name', 'booking_destination_mapping_keywords.keyword')
                    ->whereIn('booking_destination_mapping_keywords.keyword', $found_keyword);
                if ($data_found->exists()) {
                    $data_found = $data_found->get();

                    foreach ($data_found as $value) {
                        if ($value->city_id != $city_id) {
                            $dd = isset($invalid_cities[$value->city_name]) ? $invalid_cities[$value->city_name] : '';
                            $invalid_cities[$value->city_name] = trim($dd) . " " . $value->keyword;
                        }
                    }
                    if ($invalid_cities) {
                        foreach ($invalid_cities as $key => $value) {
                            if (empty($invalid_cities_string)) {
                                // $invalid_cities_string=  $key.':' ." ".$value;
                                $invalid_cities_string =  "Dear User, The area " . trim($value) . " is actually present in $key instead of $city_name";
                            } else {
                                $invalid_cities_string = $invalid_cities_string . " and the area " . trim($value) . " is actually present in $key instead of $city_name";
                            }
                        }
                        $invalid_cities_string .= ". For assistance, Call: 021-38772222";
                        return $result = array('status' => 'false', 'invalid_cities' => trim($invalid_cities_string), 'error' => 'Invalid Address');
                    }
                }
            }
            return $result;
        }
    }

    public function shipment_book_daraz(Request $request)
    {
        /********************************NOTE********************************/
        /*This API is also using from Trax App Booking Form, Please Concern with Mobile Team also Before Adding any required Parameter*/
        $user_id = $request->user_id;
        $daraz_account_ids = [7306, 7308, 10389];
        if (!in_array($user_id, $daraz_account_ids)) {
            return response()->json(['status' => 1, 'message' => 'Shipper is not allowed.']);
        }

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
            if (isset($data['shipping_mode_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = 1;
            } else {
                return false;
            }

            if ($value) {
                if ($service_type_id == 5) {
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
            if (isset($data['shipping_mode_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = 1;
            } else {
                return false;
            }
            if ($value) {
                if ($service_type_id == 5) {
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

        $user_type = User::where('id', $user_id)->first();
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::unique('shipments', 'tracking_number')],
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('hidden', 0);
            }), 'origin_check'],

            'return_address' => ['required', 'between:1,255'],
            'return_contact_person' => ['required', 'between:1,100'],
            'return_vendor' => ['required', 'between:1,100'],
            'return_phone_number' => ['required', 'phone_number'],
            'return_email_address' => ['required', 'email', 'between:0,100'],
            'return_city_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1)->where('status', 1)],

            'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1), 'destination_check'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'phone_number'],
            'consignee_phone_number_2' => ['nullable', 'filled', 'phone_number'],
            'consignee_email_address' => ['nullable', 'filled', 'email'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],
            'special_instructions' => ['nullable', 'filled', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],

            'amount' => ['required_if:service_type_id,1,2,3', 'nullable', 'numeric', 'between:0,1000000'],

            'item_description' => ['required', 'between:0,500'],
            'item_quantity' => ['required', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190'],

            'order_id' => ['nullable', 'filled', 'between:0,100'],

        ];

        if ($user_type['corporate_rate_type_id'] == 3) {
            $rules['shipping_mode_id'] = ['required', 'integer', 'between:1,2', 'exists:shipping_modes,id', Rule::exists('corporate_default_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        } else {
            $rules['shipping_mode_id'] = ['required', 'integer', 'between:1,2', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        }
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $shipment_pre_book = ShipmentPrebook::where('user_id', $user_id)->first();
            $length = strlen($shipment_pre_book->prefix);
            $tracking_number_prefix = str_split($request->tracking_number, $length);
            if ($shipment_pre_book->prefix != $tracking_number_prefix[0]) {
                return response()->json(['status' => 1, 'message' => 'Prefix not matched with given tracking number']);
            }

            //Return Address
            $return_city_id = $request->return_city_id;
            $return_address = UserShippingInfo::where('user_id', $user_id)->where('vendor', $request->return_vendor)->where('city_id', $return_city_id);
            if ($return_address->exists()) {
                $return_address = $return_address->first();
                $return_address_id = $return_address->id;
            } else {
                $return_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $request->return_address, $request->return_contact_person, $request->return_vendor, $request->return_phone_number, $request->return_email_address, $return_city_id, 0);
            }

            $shipping_mode_id = $request->input('shipping_mode_id');
            //Return Address

            $result = ShipperShipmentBookController::check_return_destination($return_address_id, $shipping_mode_id, $user_id);
            if (!$result) {
                return response()->json(['status' => 1, 'message' => 'Return city not allowed, please contact your sales person!']);
            }

            $consignee_phone_number_1 = $this->phone_number($request->consignee_phone_number_1);

            if ($request->filled('consignee_phone_number_2')) {
                $consignee_phone_number_2 = $this->phone_number($request->consignee_phone_number_2);
            }

            $open_shipment = 0;

            $service_type_id = 1;
            $shipment_pre_book = null;

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


            $user_shipping_info_return = UserShippingInfo::find($return_address_id);

            if (!$user_shipping_info_return->status) {
                return response()->json(['status' => 1, 'message' => 'Return Address ID #' . $return_address_id . ' is disabled']);
            }

            if (!$user_shipping_info_return->city->status) {
                return response()->json(['status' => 1, 'message' => 'Return Address\'s City ID #' . $user_shipping_info_return->city_id . ' is deactivated']);
            }
            if (!$user_shipping_info_return->city->zone_id) {
                return response()->json(['status' => 1, 'message' => 'Return Address\'s City ID #' . $user_shipping_info_return->city_id . ' is deactivated']);
            }

            $consignee_city = City::find($request->input('consignee_city_id'));


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

            if (!CityDelivery::where('city_id', $request->input('consignee_city_id'))->where('booking_type_id', $service_type_id)->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
                return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $request->input('consignee_city_id') . ' with Service Type ID #' . $service_type_id . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
            }

            $pickup_address_id = $request->input('pickup_address_id');
            $consignee_city_id = $request->input('consignee_city_id');

            $consignee_address = $request->input('consignee_address');

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
            $information_display = 1;

            $charges_mode_id = 3;

            $self_collection = false;
            if ($service_type_id == 1 && $request->has('self_collection')) {
                if ($request->input('self_collection') != null) {
                    if ($request->input('self_collection') == 1) {
                        $self_collection = true;
                    }
                }
            }

            if ($request->filled('order_id')) {
                $order_id = $request->input('order_id');
            } else {
                $order_id = null;
            }

            $package_type = false;

            if ($request->filled('special_instructions')) {
                $special_instructions = $request->input('special_instructions');
            } else {
                $special_instructions = null;
            }

            $estimated_weight = $request->input('estimated_weight');
            $amount = $request->input('amount');

            $same_day_timing_id = null;
            $try_and_buy_charges = null;
            $pieces_quantity = 1;

            $payment_mode_id = 1;

            $business_category_id = 1;
            $delivery_type_id = 1;

            $tracking_number = $request->input('tracking_number');
            $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id);

            $shipment = Shipment::find($shipment_id);
            $shipment->tracking_number = $tracking_number;
            $shipment->save();

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

            $item_product_type_id = 24;

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


            if ($user_type['logo_status'] == 1) {
                $shipment = Shipment::find($shipment_id);
                $shipment->shipment_invoice_status = 1;
                $shipment->save();
            }


            return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number]);
        }
    }

    public function shipment_book_jazzcash(Request $request)
    {
        /********************************NOTE********************************/
        /*This API is also using from Trax App Booking Form, Please Concern with Mobile Team also Before Adding any required Parameter*/
        $user_id = $request->user_id;
        $jazzcash_account_ids = [10104, 14781 , 14110, 10381, 10358];
        if (!in_array($user_id, $jazzcash_account_ids)) {
            return response()->json(['status' => 1, 'message' => 'Shipper is not allowed.']);
        }

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
            if (isset($data['shipping_mode_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = 1;
            } else {
                return false;
            }

            if ($value) {
                if ($service_type_id == 5) {
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
            if (isset($data['shipping_mode_id'])) {
                $shipping_mode_id = $data['shipping_mode_id'];
                $service_type_id = 1;
            } else {
                return false;
            }
            if ($value) {
                if ($service_type_id == 5) {
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

        $user_type = User::where('id', $user_id)->first();
        $rules = [
            'tracking_number' => ['required', 'integer', Rule::unique('shipments', 'tracking_number')],
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('hidden', 0);
            }), 'origin_check'],

            'return_address' => ['nullable', 'between:1,255'],
            'return_contact_person' => ['nullable', 'between:1,100'],
            'return_vendor' => ['nullable', 'between:1,100'],
            'return_phone_number' => ['nullable', 'phone_number'],
            'return_email_address' => ['nullable', 'email', 'between:0,100'],
            'return_city_id' => ['nullable', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1)->where('status', 1)],

            'consignee_city_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('cities', 'id')->where('business_category_id', 1), 'destination_check'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'phone_number'],
            'consignee_phone_number_2' => ['nullable', 'filled', 'phone_number'],
            'consignee_email_address' => ['nullable', 'filled', 'email'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],
            'special_instructions' => ['nullable', 'filled', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,100000'],

            'amount' => ['required_if:service_type_id,1,2,3', 'nullable', 'numeric', 'between:0,1000000'],

            'item_description' => ['required', 'between:0,500'],
            'item_quantity' => ['required', 'integer', 'digits_between:1,10', 'between:1,10000'],

            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190'],

            'order_id' => ['nullable', 'filled', 'between:0,100'],

        ];

        if ($user_type['corporate_rate_type_id'] == 3) {
            $rules['shipping_mode_id'] = ['required', 'integer', 'between:1,2', 'exists:shipping_modes,id', Rule::exists('corporate_default_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        } else {
            $rules['shipping_mode_id'] = ['required', 'integer', 'between:1,2', 'exists:shipping_modes,id', Rule::exists('corporate_rate_statuses', 'shipping_mode_id')->where(function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('status', 1);
            })];
        }
        $validate = Validator::make($request->all(), $rules, $this->messages);

        $validate->setAttributeNames($this->names);

        if ($validate->fails()) {
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validate->errors()]);
        } else {

            $shipment_pre_book = ShipmentPrebook::where('user_id', $user_id)->first();
            if($shipment_pre_book){
                $length = strlen($shipment_pre_book->prefix);
                $tracking_number_prefix = str_split($request->tracking_number, $length);
                if ($shipment_pre_book->prefix != $tracking_number_prefix[0]) {
                    return response()->json(['status' => 1, 'message' => 'Prefix not matched with given tracking number']);
                }
            }
            else{
                return response()->json(['status' => 1, 'message' => 'Registered Prefix not found!']);
            }

            //Return Address

            $return_address_id = null;

            $shipping_mode_id = $request->input('shipping_mode_id');

            if($request->has('return_city_id') && $request->has('return_vendor') && $request->has('return_address') && $request->has('return_contact_person') && $request->has('return_phone_number') && $request->has('return_email_address')){
                $return_city_id = $request->return_city_id;
                $return_address = UserShippingInfo::where('user_id', $user_id)->where('vendor', $request->return_vendor)->where('city_id', $return_city_id);
                if ($return_address->exists()) {
                    $return_address = $return_address->first();
                    $return_address_id = $return_address->id;
                } else {
                    $return_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $request->return_address, $request->return_contact_person, $request->return_vendor, $request->return_phone_number, $request->return_email_address, $return_city_id, 0);
                }


                //Return Address

                $result = ShipperShipmentBookController::check_return_destination($return_address_id, $shipping_mode_id, $user_id);
                if (!$result) {
                    return response()->json(['status' => 1, 'message' => 'Return city not allowed, please contact your sales person!']);
                }
            }

            $consignee_phone_number_1 = $this->phone_number($request->consignee_phone_number_1);

            if ($request->filled('consignee_phone_number_2')) {
                $consignee_phone_number_2 = $this->phone_number($request->consignee_phone_number_2);
            }

            $open_shipment = 0;

            $service_type_id = 1;
            $shipment_pre_book = null;

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

            if($return_address_id != null){
                $user_shipping_info_return = UserShippingInfo::find($return_address_id);

                if (!$user_shipping_info_return->status) {
                    return response()->json(['status' => 1, 'message' => 'Return Address ID #' . $return_address_id . ' is disabled']);
                }

                if (!$user_shipping_info_return->city->status) {
                    return response()->json(['status' => 1, 'message' => 'Return Address\'s City ID #' . $user_shipping_info_return->city_id . ' is deactivated']);
                }
                if (!$user_shipping_info_return->city->zone_id) {
                    return response()->json(['status' => 1, 'message' => 'Return Address\'s City ID #' . $user_shipping_info_return->city_id . ' is deactivated']);
                }
            }

            $consignee_city = City::find($request->input('consignee_city_id'));


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

            if (!CityDelivery::where('city_id', $request->input('consignee_city_id'))->where('booking_type_id', $service_type_id)->where('shipping_mode_id', $request->input('shipping_mode_id'))->exists()) {
                return response()->json(['status' => 1, 'message' => 'Delivery is not allowed for City ID #' . $request->input('consignee_city_id') . ' with Service Type ID #' . $service_type_id . ' and Shipping Mode ID #' . $request->input('shipping_mode_id')]);
            }

            $pickup_address_id = $request->input('pickup_address_id');
            $consignee_city_id = $request->input('consignee_city_id');

            $consignee_address = $request->input('consignee_address');

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
            $information_display = 1;

            $charges_mode_id = 3;

            $self_collection = false;
            if ($service_type_id == 1 && $request->has('self_collection')) {
                if ($request->input('self_collection') != null) {
                    if ($request->input('self_collection') == 1) {
                        $self_collection = true;
                    }
                }
            }

            if ($request->filled('order_id')) {
                $order_id = $request->input('order_id');
            } else {
                $order_id = null;
            }

            $package_type = false;

            if ($request->filled('special_instructions')) {
                $special_instructions = $request->input('special_instructions');
            } else {
                $special_instructions = null;
            }

            $estimated_weight = $request->input('estimated_weight');

            $amount = 0;
            if($request->has('amount')){
                $amount = $request->input('amount');
            }

            $same_day_timing_id = null;
            $try_and_buy_charges = null;
            $pieces_quantity = 1;

            $payment_mode_id = 1;

            $business_category_id = 1;
            $delivery_type_id = 1;

            $tracking_number = $request->input('tracking_number');
            $shipment_id = ShipperShipmentBookController::corporate_book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $delivery_type_id, $same_day_timing_id, $charges_mode_id, $amount, $payment_mode_id, $pieces_quantity, $self_collection, $business_category_id, $try_and_buy_charges, $open_shipment, $return_address_id);

            $shipment = Shipment::find($shipment_id);
            $shipment->tracking_number = $tracking_number;
            $shipment->save();

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

            $item_product_type_id = 24;

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


            if ($user_type['logo_status'] == 1) {
                $shipment = Shipment::find($shipment_id);
                $shipment->shipment_invoice_status = 1;
                $shipment->save();
            }


            return response()->json(['status' => 0, 'message' => 'Shipment has been Booked!', 'tracking_number' => $tracking_number]);
        }
    }

    public function return_shipment_info(Request $request){
        $validateshipment = Validator::make($request->all(), [
            'tracking'     => 'required',
        ]);

        if( $validateshipment->fails()){
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validateshipment->errors()]);
        }
        else{
            $return_statuses = array(25, 31, 38, 23, 28, 34);
            $tracking_number = $request->tracking;
            $shipment = Shipment::where('tracking_number', $tracking_number)->where('user_id', $request->user_id); 
                if($shipment->exists()){
                    $shipment = $shipment->first();
                    if(in_array($shipment->shipper_status_id, $return_statuses)){
                        $return_sheet = ReturnSheet::where('shipment_id', $shipment->id);
                        if($return_sheet->exists()){
                            $return_sheet = $return_sheet->first();
                            if($return_sheet->status_id == 0){
                                return response()->json(['status' => 1, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $shipment->consignee_city->name, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => ($shipment->amount), 'shipment_status' => $shipment->status_shipper->name]);
                            }
                            else{
                                return response()->json(['status' => 0, 'error' => 'Shipment is already received with remarks ' . $return_sheet->remarks]);
                            }
                        }
                        else{
                            return response()->json(['status' => 0, 'error' => 'Shipment is not ready to be received!']);
                        }
                    }
                    else{
                        return response()->json(['status' => 0, 'error' => 'Shipment is not ready to be received!']);
                    }
                }
            else{
                return response()->json(['status' => 0, 'error' => 'Shipment with given Tracking Number not Found!']);
            }
        }
    }

    public function shipper_received_shipments(Request $request){
        $validateshipment = Validator::make($request->all(), [
            'shipment_ids' => 'required',
            'user_type'    => 'required',
        ]);

        if( $validateshipment->fails()){
            return response()->json(['status' => 1, 'message' => 'Error(s) in Input', 'errors' => $validateshipment->errors()]);
        }
        else{
            $shipments_list = $request->shipment_ids;
            $user           = $request->user_id;
            $user_type      = $request->user_type;
            $received_by    = '';
            $userDetials    = User::where('id', $user)->first();

            if($user_type == 2){
                $received_by = ' (Substitute User)';
            }
            $ships = [];
            foreach($shipments_list as $shipment_id){
                $ships[] = $shipment_id;
                if(!empty($shipment_id)){
                    $shipments = Shipment::where('tracking_number', $shipment_id); 
                    if($shipments->exists()){
                        $shipments = $shipments->first();
                        $return_sheet = ReturnSheet::where('shipment_id', $shipments->id);
                        $ReturnSheetShipments = new ReturnSheetShipments();
                        if($return_sheet->exists()){        
                            $return_sheet = $return_sheet->first();
                            $return_sheet->status_id = 1;
                            $return_sheet->received_at = Carbon::now();
                            $return_sheet->remarks = 'Received By ' . $userDetials->name . $received_by;
                            $return_sheet->save();

                            $ReturnSheetShipments->shipment_id = $shipments->id;
                            $ReturnSheetShipments->scan_via = 2;
                            $ReturnSheetShipments->return_sheet_id = $return_sheet->id;
                            $ReturnSheetShipments->save();
                        }
                       
                    }
                    else{
                        return response()->json([
                            'status'  => 0, 
                            'error' => 'Enter a Valid Shipment No'
                        ]);
                    }
                }
            }   
            return response()->json([
                'status'  => 1, 
                'success' => 'Shipement Received Successfully'
            ]);
        }
    }

    public function return_shipments_list(){
        dd('this function return shipments list');
    }


    public function hbl_konnect_retail_note_cash_collection_information(Request $request)
    {
        $valid_ip_addresses = array();
        $valid_ip_addresses[] = '103.111.84.67';
        $valid_ip_addresses[] = '103.111.85.67';
        $environment = config('app.env');

        if ($environment == 'production') {
            $whip = new Whip();
            $ip_address = $whip->getValidIpAddress();
            if (in_array($ip_address, $valid_ip_addresses)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = true;
        }
        if ($flag) {
            $rules = [
                'retail_note_cash_collection_id' => ['required', 'integer', Rule::exists('retail_cash_deposits', 'id')]
            ];
            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $errors = array();
                foreach ($validate->errors()->all() as $index => $error) {
                    $errors[$index]['error_code'] = 10;
                    $errors[$index]['error_text'] = 'Invalid Input.';
                    if ($error == 'delivery note id is Required.') {
                        $errors[$index]['error_code'] = 1;
                        $errors[$index]['ERROR_TEXT'] = 'Delivery note id is Required.';
                    }
                    if ($error == 'delivery note id must be an Integer.') {
                        $errors[$index]['error_code'] = 2;
                        $errors[$index]['error_text'] = 'Delivery note id must be an Integer.';
                    }
                    if ($error == 'Given delivery note id is of Invalid ID.') {
                        $errors[$index]['error_code'] = 3;
                        $errors[$index]['error_text'] = 'Given delivery note id is of Invalid ID.';
                    }
                }
                return response()->json(['status' => 0, 'message' => 'Error(s) in Input', 'errors' => $errors]);
            } else {

                $retail_note_cash_collection_id = $request->retail_note_cash_collection_id;
                $retail_note = RetailCashDeposit::where('id', $retail_note_cash_collection_id);
                if ($retail_note->exists()) {
                    $retail_note = $retail_note->first();
                    $min_date = Carbon::parse('01-07-2022 00:00:00')->toDateTimeString();
                    if ($retail_note->created_at >= $min_date) {
                        $hbl_konnect_transaction_delivery_note = HblKonnectTransactionRetailNote::where('retail_note_id', $retail_note->id);
                        $transactions_amount = 0;
                        if ($hbl_konnect_transaction_delivery_note->exists()) {
                            $hbl_konnect_transaction_delivery_note = $hbl_konnect_transaction_delivery_note->first();
                            $transactions_amount = $hbl_konnect_transaction_delivery_note->transactions_amount;
                        }
                        $net_amount = $retail_note->total_cash - $transactions_amount;
                        $retail_user_id = $retail_note->retail_user_id;
                        $admin = Admin::where('id', $retail_user_id);
                        if ($admin->exists()) {
                            $admin = $admin->first();
                            $admin_name = $admin->name;
                            $admin_trax_id = $admin->trax_id;
                            $admin_cnic = $admin->cnic;
                        }
                        return response()->json(['status' => 1, 'retail_note_id' =>  str_pad($retail_note->id, 6, '0', STR_PAD_LEFT), 'amount' => $net_amount, 'admin_name' => $admin_name, 'admin_trax_id' => $admin_trax_id,'admin_cnic'=>$admin_cnic]);
                    } else {
                        return response()->json(['status' => 0, 'message' => 'Retail Note restricted!']);
                    }
                } else {
                    return response()->json(['status' => 0, 'message' => 'Retail Note Not Found!']);
                }
            }
        } else {
            return ['status' => 2, 'message' => 'Access Denied!'];
        }
    }

    public function hbl_konnect_retail_note_cash_collection_transactions(Request $request)
    {
        $valid_ip_addresses = array();
        $valid_ip_addresses[] = '103.111.84.67';
        $valid_ip_addresses[] = '103.111.85.67';
        $environment = config('app.env');

        if ($environment == 'production') {
            $whip = new Whip();
            $ip_address = $whip->getValidIpAddress();
            if (in_array($ip_address, $valid_ip_addresses)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = true;
        }
        if ($flag) {
            $rules = [
                'retail_note_id' => ['required', 'integer', Rule::exists('retail_cash_deposits', 'id')],
                'amount' => ['required', 'numeric', 'min:0'],
                'transaction_id' => ['required', 'integer', 'min:0'],
            ];
            $validate = Validator::make($request->all(), $rules, $this->messages);

            $validate->setAttributeNames($this->names);

            if ($validate->fails()) {
                $errors = array();
                foreach ($validate->errors()->all() as $index => $error) {
                    $errors[$index]['error_code'] = 10;
                    $errors[$index]['error_text'] = 'Invalid Input.';
                    if ($error == 'retail note id is Required.') {
                        $errors[$index]['error_code'] = 1;
                        $errors[$index]['ERROR_TEXT'] = 'retail note id is Required.';
                    }
                    if ($error == 'retail note id must be an Integer.') {
                        $errors[$index]['error_code'] = 2;
                        $errors[$index]['error_text'] = 'retail note id must be an Integer.';
                    }
                    if ($error == 'Given retail note id is of Invalid ID.') {
                        $errors[$index]['error_code'] = 3;
                        $errors[$index]['error_text'] = 'Given retail note id is of Invalid ID.';
                    }
                    if ($error == 'Collection Amount is Required.') {
                        $errors[$index]['error_code'] = 4;
                        $errors[$index]['ERROR_TEXT'] = 'Collection Amount is Required.';
                    }
                    if ($error == 'Collection Amount must be a Number.') {
                        $errors[$index]['error_code'] = 5;
                        $errors[$index]['error_text'] = 'Collection Amount must be a Number.';
                    }
                    if ($error == 'The Collection Amount must be at least 0.') {
                        $errors[$index]['error_code'] = 6;
                        $errors[$index]['error_text'] = 'The Collection Amount must be at least 0.';
                    }
                    if ($error == 'transaction id is Required.') {
                        $errors[$index]['error_code'] = 7;
                        $errors[$index]['ERROR_TEXT'] = 'Transaction id is Required.';
                    }
                    if ($error == 'transaction id must be an Integer.') {
                        $errors[$index]['error_code'] = 8;
                        $errors[$index]['error_text'] = 'Transaction id must be an Integer.';
                    }
                    if ($error == 'The transaction id must be at least 0.') {
                        $errors[$index]['error_code'] = 9;
                        $errors[$index]['error_text'] = 'The transaction id must be at least 0.';
                    }
                }
                return response()->json(['status' => 0, 'message' => 'Error(s) in Input', 'errors' => $errors]);
            } else {
                $transaction_id = $request->transaction_id;
                $retail_note_id = $request->retail_note_id;
                $amount = $request->amount;
                $existing_hbl_konnect_transaction = HblKonnectTransactionRetail::where('transaction_id', $transaction_id);
                if ($existing_hbl_konnect_transaction->exists()) {
                    return ['status' => 1, 'message' => 'Transaction Already Exists !'];
                } else {
                    $retail_note = RetailCashDeposit::where('id', $retail_note_id);
                    if ($retail_note->exists()) {
                        $retail_note = $retail_note->first();

                        if($retail_note->status != 0)
                        {
                            return ['status' => 0, 'message' => 'Retail note already updated !'];
                        }
                    }

                    $transaction_amount = $amount;

                    $hbl_konnect_transaction_delivery_note = HblKonnectTransactionRetailNote::where('retail_note_id', $retail_note_id);
                    if ($hbl_konnect_transaction_delivery_note->exists()) {
                        $hbl_konnect_transaction_delivery_note = $hbl_konnect_transaction_delivery_note->first();
                        $transaction_amount = $hbl_konnect_transaction_delivery_note->transactions_amount + $amount;
                    } else {
                        $hbl_konnect_transaction_delivery_note = new HblKonnectTransactionRetailNote();
                        $hbl_konnect_transaction_delivery_note->retail_note_id = $retail_note_id;
                    }

                    $cash_amount = $retail_note->total_cash - $transaction_amount;
                    if ($cash_amount < 0)
                    {
                        $hbl_konnect_transaction_delivery_note = HblKonnectTransactionRetailNote::where('retail_note_id', $retail_note_id);
                        if ($hbl_konnect_transaction_delivery_note->exists())
                        {
                            $hbl_konnect_transaction_delivery_note = $hbl_konnect_transaction_delivery_note->first();

                            $remaining_amount = $hbl_konnect_transaction_delivery_note->cash_amount;

                            return ['status' => 0, 'message' => 'Net amount should be less then or equal to ' .$remaining_amount];
                        }
                    }
                    $hbl_konnect_transaction_delivery_note->transactions_amount = $transaction_amount;
                    $hbl_konnect_transaction_delivery_note->cash_amount = $cash_amount;
                    $hbl_konnect_transaction_delivery_note->save();

                    $hbl_konnect_transaction = new HblKonnectTransactionRetail();
                    $hbl_konnect_transaction->transaction_id = $transaction_id;
                    $hbl_konnect_transaction->retail_note_id = $retail_note_id;
                    $hbl_konnect_transaction->amount = $amount;
                    $hbl_konnect_transaction->save();

                    return ['status' => 1, 'message' => 'Request completed successfully!'];
                }
            }
        } else {
            return ['status' => 2, 'message' => 'Access Denied!'];
        }
    }
}
