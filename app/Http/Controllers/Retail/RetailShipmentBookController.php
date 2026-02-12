<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\BookingDestinationMappingKeyword;
use App\Http\Models\Admin\Retail\RetailPaymentMode;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Admin\Retail\RetailTraxBox;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\ChargesMode;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\InternationalShipment;
use App\Http\Models\Product;
use App\Http\Models\RetailInternationalShippingMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShippingMode;
use App\Jobs\ProcessRetailShipmentBookingDB;
use App\RetailShipperNameVerification;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Retail\RetailReference;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyBlacklisted;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\Blacklist\ConsigneeInformationLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Validator;
use Illuminate\Validation\Rule;
use DNS2D;
use App\Http\Models\RetailRequestCity;
use App\Http\Models\RetailUserCommission;
use App\Http\Models\RetailFranchiseCommission;
use App\Http\Models\RetailUserHistory;
use App\Http\Models\RetailUserProductPercentage;
use App\Http\Models\TotalSumFranchiseCommission;
use App\Http\Models\TotalSumRetailTraxCenter;
use App\Http\Models\RetailFranchiseCharge;
use App\RetailDiscountCode;
use Illuminate\Support\Facades\Log;
use App\Http\Models\ShipperSegmentLogs;
use App\Http\Models\Shipper\User;
use App\Models\ParentProduct;
use App\Models\RetailShipmentFlyerNumber;

class RetailShipmentBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

        //$this->middleware('auth:retail')->except(['slip']);

//        $this->middleware('Permission');
    }

    static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces, $business_category_id, $length, $breadth, $height) {

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
        $shipment->length = $length;
        $shipment->breadth = $breadth;
        $shipment->height = $height;

        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->received_amount = $r_amount;
        $shipment->amount = $amount;
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->charges_mode_id = $charges_mode_id;
        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;

        $shipment->try_and_buy_fees = $try_and_buy_charges;
        $shipment->pieces = $pieces;
        $shipment->business_category_id = $business_category_id;
        $shipment->shipment_type = 2;

        $shipment->save();

        $shipment_id = $shipment->id;


        $reference_1_id = null;
        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL, $reference_1_id);
        
        $consignee_information = ConsigneeInformation::where('phone', $consignee_phone_number_1);
        if(!$consignee_information->exists()){
            $consignee_information = new ConsigneeInformation();
            $consignee_information->phone = $consignee_phone_number_1;
            $consignee_information->phone2 = $consignee_phone_number_2;
            $consignee_information->name = $consignee_name;
            $consignee_information->address = $consignee_address;
            $consignee_information->city_id = $consignee_city_id;
            $consignee_information->retail_consignee = 1;
            $consignee_information->save();

            // $consignee_information = $consignee_information->first();
            $consignee_information_log = new ConsigneeInformationLog();
            $consignee_information_log->consignee_information_id = $consignee_information->id;
            $consignee_information_log->phone = $consignee_information->phone;
            $consignee_information_log->phone2 = $consignee_information->phone2;
            $consignee_information_log->name = $consignee_information->name;
            $consignee_information_log->address = $consignee_information->address;
            $consignee_information_log->city_id = $consignee_information->city_id;
            $consignee_information_log->user_id = $user_id;
            $consignee_information_log->save();

        }else{

          $consignee_information = $consignee_information->first();
            $consignee_information->phone = $consignee_phone_number_1;
            $consignee_information->phone2 = $consignee_phone_number_2;
            $consignee_information->name = $consignee_name;
            $consignee_information->address = $consignee_address;
            $consignee_information->city_id = $consignee_city_id;
            $consignee_information->save();

            $consignee_information_log = new ConsigneeInformationLog();
            $consignee_information_log->consignee_information_id = $consignee_information->id;
            $consignee_information_log->phone = $consignee_information->phone;
            $consignee_information_log->phone2 = $consignee_information->phone2;
            $consignee_information_log->name = $consignee_information->name;
            $consignee_information_log->address = $consignee_information->address;
            $consignee_information_log->city_id = $consignee_information->city_id;
            $consignee_information_log->user_id = $user_id;
            $consignee_information_log->save();
        }
        return $shipment_id;
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

    static public function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id) {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }

    static public function create_shipment_pieces($shipment_id, $pieces){
        $total_pieces = 0;
        if($pieces > 1){

            for($i=1; $i<=$pieces; $i++){
                $shipment_piece = new ShipmentPiece();
                $shipment_piece->shipment_id = $shipment_id;
                $total_pieces++;
                $shipment_piece->numbering = $total_pieces;
                $shipment_piece->tracking_number = $shipment_id . $total_pieces;
                $shipment_piece->save();
            }

        }
    }

    public function index(){
        $products = Product::all();
        $parent_products = ParentProduct::get();
        $business_categories = BusinessCategory::all();
        $shipping_modes = RetailShippingMode::where('business_category_id',1)->get();
        $retail_international_shipping_modes =  RetailShippingMode::where('business_category_id',2)->get();
        $domestic_cities = City::where('business_category_id', 1)->where('booking_enable_status', 1)->where('status', 1)->get();
        $international_cities = City::where('business_category_id', 2)->where('permanent_disabled',0)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', '=', 1)->get();
        $charges_modes = ChargesMode::whereIn('id', [1, 2])->get();
        $trax_boxes = RetailTraxBox::all();
        $banks = BanksList::all();
        $check_nsa = NonServiceArea::pluck('name')->toArray();

        $refs = ['Social Media','Website','Signages','Existing Customer','Others'];
        return view('retail.shipment.booking.index')->with(['products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'domestic_overland_cities' => $domestic_overland_cities,'international_cities'=>$international_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes, 'banks' => $banks, 'charges_modes' => $charges_modes, 'refs' => $refs,'retail_international_shipping_modes' => $retail_international_shipping_modes, 'parent_products' => $parent_products,'check_nsa'=>$check_nsa]);
    }

    public function store(Request $request){
        if($request->ref == 'Others'){
          $ref = $request->ref_name;
        }else{
          $ref = $request->ref;
        }
        $setting = GlobalSettings::where('type', 'retail_store')->first();
        $shipper_user_id = $setting->setting_value;
        $user_id = $shipper_user_id;
        $pickup_address_id = Auth::user()->store->pickup_address_id;
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;

        $discount =  Auth::user()->store->discount;

        if($request->has('discount_code'))
        {
            if ($request->filled('retail_discount_percentage') && $request->retail_discount_percentage > 0) {
                $discount = $request->retail_discount_percentage;
            }
        }

        $insurance = Auth::user()->store->insurance;
        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_phone_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = $request->input('order_id');
        $package_type = FALSE;
        $special_instructions = $request->special_instructions;
        $business_category_id = $request->input('business_category');

        $packaging = ($request->packaging_amount != null) ?  str_replace(',', '', $request->packaging_amount) : 0;
        $insurance_amount = ($request->insurance_amount != null) ?  str_replace(',', '',$request->insurance_amount) : 0;

        if($insurance_amount > 0 ){
            $insurance_amount = round($insurance_amount * $insurance / 100,2);
        }


        $shipping_mode_check = $request->input('shipping_mode');
        if ($shipping_mode_check == 1) {
            if($request->input('business_category') == 1)
            {
                $consignee_city_id = $request->input('domestic_overland_destination');
            }
            else{
                $consignee_city_id = $request->input('international_destination');
            }
            $shipping_mode_id = 2;
        }
        elseif ($shipping_mode_check == 4){
            if($request->input('business_category') == 1) {
                $consignee_city_id = $request->input('domestic_destination');
            }
            else{
                $consignee_city_id = $request->input('international_destination');
            }
            $shipping_mode_id = 3;
        }
        elseif ($shipping_mode_check == 11){
            if($request->input('business_category') == 1) {
                $consignee_city_id = $request->input('domestic_destination');
            }
            else{
                $consignee_city_id = $request->input('international_destination');
            }
            $shipping_mode_id = 2;
        }
        else{
            if($request->input('business_category') == 1) {
                $consignee_city_id = $request->input('domestic_destination');
            }
            else{
                $consignee_city_id = $request->input('international_destination');
            }
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        if ($request->volumetric_weight == "on") {
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

        $rates = RetailRatesCalculationController::rates($shipping_mode_check, $business_category_id, $pickup_city_id, $consignee_city_id, $request->trax_box, $discount, $estimated_weight,$insurance_amount,$packaging, $request->product, $request->cod, count($request->get('flyer', [])), $request->charged_sms1);


        if($request->has('admin_discount'))
        {
            if ($request->filled('admin_discount') && $request->admin_discount > 0) {
                if ($request->has('admin_discount_type1') && $request->admin_discount_type1 ==  1)
                {
                    $rates['total_charges'] = $rates['total_charges'] - ($rates['total_charges'] * $request->admin_discount)/100; //todo: for %
                }
                if ($request->has('admin_discount_type1') && $request->admin_discount_type1 ==  0)
                {
                    $rates['total_charges'] = $rates['total_charges'] - $request->admin_discount; // todo: for flat
                }
            }
        }

        if ($rates['total_charges'] <= 0)
        {
            return redirect()->back()->with(['error' => 'Total charges cannot be less than zero !']);
        }

       
        if( $rates['charges'] == 0 && $rates['charges_with_discount'] == 0) {
            return redirect()->back()->with(['error' => 'Charges should be greater than zero']);
        }


        $city = City::find($pickup_city_id);

        $charges_mode_id = $request->input('charges_mode');
        if($shipping_mode_check == 3){
            $amount = str_replace(',', '', $request->input('cod'));
            $r_amount = 0;
            if($charges_mode_id == 2){
                $amount = $amount + $rates['total_charges'];
            }
        }
        else{
            $amount = 0;
            if($charges_mode_id == 2){
                $amount = $rates['total_charges'];
            }
            $r_amount = 0;
        }
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category');

        $parcelAmount = $request->input('parcel_amount');
        $parcelAmount = trim($parcelAmount);
        $parcelAmount = str_replace(',', '', $parcelAmount);
        $parcelAmoutInShipment = (float)$parcelAmount;

        $quantity = $request->input('quantity');
        $quantity = trim($quantity);
        $quantity = str_replace(',', '', $quantity);
        $quantityForShipmentItem = (int)$quantity;

        $shipment_id = $this->book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id , $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

        if($request->input('business_category') == 2) {
            $international_shipment_booking = new InternationalShipment();
            $international_shipment_booking->shipment_id = $shipment_id;
            $international_shipment_booking->postal_code = 00000;
            $international_shipment_booking->save();
        }

        $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = $request->product;

        $item_description = NULL;

        // $item_quantity = 1;
        $item_quantity = $quantityForShipmentItem;

        if ($request->input('insurance_offered') == 1) {
            $price = str_replace(',', '', $request->input('insurance_amount'));
            $insurance = TRUE;
        }
        else {
            $price = NULL;
            $insurance = FALSE;
        }

        $type = 0;

        $this->add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);

        if($pieces_quantity > 1){
            $this->create_shipment_pieces($shipment_id, $pieces_quantity);
        }

        if($request->shipping_mode == 1){
            if($request->input('business_category') == 1) {
                $destination = $request->domestic_overland_destination;
            }
            else{
                $destination = $request->input('international_destination');
            }
        }
        else{
            if($request->input('business_category') == 1) {
                $destination = $request->domestic_destination;
            }
            else{
            $destination = $request->input('international_destination');
            }
        }

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no);
        if($shipper_info->exists()){
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $request->shipper_phone_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;

            if($shipping_mode_check == 3 ){
                if (!$shipper_info->iban_no && (!$request->iban_no || $request->iban_no == '')) {
                    return redirect()->back()->with(['error' => 'Please provide the IBAN number']);
                }
                if (!$shipper_info->account_no && (!$request->account_no || $request->account_no == '')) {
                    return redirect()->back()->with(['error' => 'Please provide the account number']);
                }
                if (!$shipper_info->bank && (!$request->bank || $request->bank == '')) {
                    return redirect()->back()->with(['error' => 'Please choose a bank']);
                }
                if (!$shipper_info->cheque_image && !$request->hasFile('cheque_image')) {
                    return redirect()->back()->with(['error' => 'Please provide the cheque image']);
                }
            }
           
            if ($request->iban_no != null && $request->account_no != null && $request->bank != null) {
                $shipper_info->bank_id = $request->bank;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque_image')){
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';

                    $file = $request->file('cheque_image');

                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        }
        else{
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $request->shipper_phone_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            $shipper_info->city_id = $pickup_city_id;
            $shipper_info->save();

            if($shipping_mode_check == 3 ){
                if ($request->iban_no == null || $request->iban_no == ''){
                    return redirect()->back()->with(['error' => 'Please provide the IBAN number']);
                } 
                if ($request->account_no == null || $request->account_no == '') {
                    return redirect()->back()->with(['error' => 'Please provide the account number']);
                } 
                if ($request->bank == null || $request->bank == ''){
                    return redirect()->back()->with(['error' => 'Please choose a bank']);
                } 
                if (!$request->hasFile('cheque_image')){
                    return redirect()->back()->with(['error' => 'Please provide the cheque image']);
                } 
            }

            if ($request->hasFile('cheque_image') && $request->iban_no != null && $request->account_no != null && $request->bank != null) {
                $shipper_info->bank_id = $request->bank;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                if ($request->hasFile('cheque_image')) {
                    $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';

                    $file = $request->file('cheque_image');

                    Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                    $shipper_info->cheque_image = $filename;
                    $shipper_info->completed_status = 1;
                }
            }
            $shipper_info->save();
        }

        $admin_id = Auth::id();
        $center_n_franchise = RetailUser::where('id',$admin_id)->select('category','category_id');
        if ($center_n_franchise->exists())
        {
            $center_n_franchise = $center_n_franchise->first();
            $cat = $center_n_franchise->category;
            $cat_id = $center_n_franchise->category_id;
        }
        else
        {
            $cat = $center_n_franchise = null;
            $cat_id = $center_n_franchise = null;
        }

        $retail_shipment = new RetailShipment();
        $retail_shipment->shipment_id = $shipment_id;
        $retail_shipment->product_type_id = $request->product;
        $retail_shipment->shipping_mode = $request->shipping_mode;
        $retail_shipment->destination = $destination;
        $retail_shipment->payment_mode_id = $request->payment_mode;
        $retail_shipment->shipper_phone_no = $request->shipper_phone_no;
        $retail_shipment->shipper_name = $request->shipper_name;
        $retail_shipment->shipper_cnic = $request->shipper_cnic;
        $retail_shipment->shipper_address = $request->shipper_address;
        $retail_shipment->trax_box_id = $request->trax_box;
        $retail_shipment->total_charges_without_gst = $rates['charges'];
        $retail_shipment->gst = $rates['gst_charges'];
        $retail_shipment->total_charges = $rates['total_charges'];
        $retail_shipment->weight_charges = $rates['charges'];
        $retail_shipment->discount = $rates['discount_amount'];
        $retail_shipment->charges_with_discount = $rates['charges_with_discount'];
        $retail_shipment->insurance_charges = $insurance_amount;
        $retail_shipment->packaging_charges = $packaging;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->retail_user_id = Auth::id();
        $retail_shipment->category = $cat;
        $retail_shipment->category_id = $cat_id;
        $retail_shipment->wht = $rates['wht'];
        $retail_shipment->cod_sst = $rates['cod_sst'];
        $retail_shipment->flyer_charges = $rates['flyer_without_gst'];
        $retail_shipment->charged_sms = $request->charged_sms1;
        $retail_shipment->sms_charges = $rates['sms_charges'];
        if($request->has('admin_discount'))
        {
            $retail_shipment->admin_discount = $request->admin_discount;
        }
        if($request->has('admin_discount_type'))
        {
            if($request->admin_discount_type == 1)
            {
                $retail_shipment->admin_discount_type = 0;
            }
            elseif ($request->admin_discount_type == 0)
            {
                $retail_shipment->admin_discount_type = 1;
            }
        }

        if($request->has('discount_code') && $request->discount_code != null)
        {
            $isCodeValid = $this->is_discount_available_to_apply($request->discount_code);
            $isCodeValid = $isCodeValid->getData();
            if($request->has('retail_discount_amount') && $isCodeValid->status == 1)
            {
                $retail_shipment->retail_discount_amount = $request->retail_discount_amount;
//                $retail_shipment->discount = $request->retail_discount_amount;
//                $retail_shipment->total_charges = $retail_shipment->total_charges - $request->retail_discount_amount;
                RetailDiscountCode::where('code', '=', $request->discount_code)->update(['shipment_id' => $shipment_id]);
            }
        }

        $retail_shipment->parcel_amount = $parcelAmoutInShipment;
        $retail_shipment->quantity = $quantityForShipmentItem;

        $retail_shipment->save();

        foreach($request->get('flyer', []) as $flyer) {

            $data = New RetailShipmentFlyerNumber();
            $data->retail_shipment_id = $retail_shipment->id;
            $data->shipment_id = $shipment_id;
            $data->flyer_number = $flyer;
            $data->save();
        }

        // retail user history
        $shipping_mode_id = $retail_shipment->shipping_mode;
        $shipping_mode = RetailShippingMode::where('id', $shipping_mode_id)->first();
        $product_commission = RetailUserProductPercentage::where('retail_user_id', $retail_shipment->retail_user_id)
            ->where('retail_shipping_mode_id', $shipping_mode->id)->first();
        $retail_user_history = RetailUserHistory::where('retail_user_id', Auth::user()->id);

        if ($retail_user_history->exists()) {
            $history_data = $retail_user_history->latest()->first();
            $joining_date = $history_data->joining_date; 
        } else {
            $joining_date = null;
        }

        $data = [
            'retail_user_id' => $retail_shipment->retail_user_id,
            'trax_center_name' => Auth::user()->store->name,
            'trax_center_code' => Auth::user()->store->code,
            'trax_center_id' => Auth::user()->store->id,
            'joining_date' => $joining_date,
            'retail_shipping_mode_id' => $shipping_mode_id,
            'retail_shipping_mode_name' => $shipping_mode->name ?? NULL,
            'product_commission' => $product_commission->product_percentage ?? NULL,
            'booking_date' => $retail_shipment->created_at
        ];
        RetailUserHistory::create($data);

        $shipment = Shipment::find($shipment_id);
        if($shipment->charges_mode_id != 2){
            $date = Carbon::today()->toDateString();
            $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', Auth::user()->category)->where('retail_user_id', Auth::id())->where('finalize', 0);
            if($cash_deposit->exists()){
                $cash_deposit = $cash_deposit->first();
                $total_shipments = $cash_deposit->total_cn + 1;
                $total_cash = floatval($cash_deposit->total_cash) + floatval($rates['total_charges']);
                $cash_deposit->total_cn = $total_shipments;
                $cash_deposit->total_cash = $total_cash;
                $cash_deposit->save();
            }
            else{
                $cash_deposit = new RetailCashDeposit();
                $cash_deposit->category = Auth::user()->category;
                $cash_deposit->retail_user_id = Auth::id();
                $cash_deposit->total_cn = 1;
                $cash_deposit->total_cash = floatval($rates['total_charges']);
                $cash_deposit->save();
            }

            $cash_deposit_shipment = new RetailCashDepositShipment();
            $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
            $cash_deposit_shipment->shipment_id = $shipment_id;
            $cash_deposit_shipment->shipping_mode_id = $request->shipping_mode;
            $cash_deposit_shipment->save();
        }


        AdminPickupsController::generate($shipment_id);
        NotificationsController::send(115, $tracking_number, $shipper_info->id);

        $retail_reference = new RetailReference;
        $retail_reference->shipment_id = $shipment_id;
        $retail_reference->ref = $ref;
        $retail_reference->save();

        $this->previous_names_verify_update($request->shipper_phone_no,$request->shipper_name,$request->shipper_cnic,$request->shipper_address, $shipper_info->id);

        try {
            // Maintaining shipper segment logs on booking
            $user_id = Shipment::where('id', $shipment_id)->select('user_id')->first();
            $user_segments = User::where('id', $user_id->user_id)
            ->select(['segment_id', 'sub_segment_id'])
            ->first();

            ShipperSegmentLogs::create([
                'shipment_id' => $shipment_id,
                'segment_id' => $user_segments->segment_id,
                'sub_segment_id' => $user_segments->sub_segment_id
            ]);

            // if ($consignee_city_id != $user_shipping_info->city_id) {

            //     $user_id = Shipment::where('id', $shipment_id)->select('user_id')->first();
            //     $user_segments = User::where('id', $user_id->user_id)
            //     ->select(['segment_id', 'sub_segment_id'])
            //     ->first();

            //     ShipperSegmentLogs::create([
            //         'shipment_id' => $shipment_id,
            //         'segment_id' => $user_segments->segment_id,
            //         'sub_segment_id' => $user_segments->sub_segment_id
            //     ]);
            // }
        } catch (\Exception $e) {
            Log::error('Error creating shipper segment log from Retail order form' . $shipment_id . ': ' . $e->getMessage());
        }

        if($request->book_button == 0){
            return response()->json(['status' => 1, 'success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'shipment_id' => $shipment_id]);
        }
        else{
            $print = $shipment_id;
            return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
        }
    }
    public function calculate_rates(Request $request){

        $pickup_city_id = Auth::user()->store->pickup_address->city_id;
        $discount =  Auth::user()->store->discount;

        if($request->has('retail_discount_applied') && $request->retail_discount_applied == 1)
        {
            if ($request->filled('retail_discount_percentage') && $request->retail_discount_percentage > 0) {
                $discount = $request->retail_discount_percentage;
            }
        }


        $insurance =  Auth::user()->store->insurance;
        if($request->weight != null){

            $weight = $request->weight;
        }
        else{
            $weight = (($request->input('length') * $request->input('breadth') * $request->input('height')) / 5000);
        }
        
        $packaging = ($request->packaging_amount != null) ?  str_replace(',', '', $request->packaging_amount) : 0;
        $insurance_amount = ($request->insurance_amount != null) ?  str_replace(',', '',$request->insurance_amount) : 0;

        if($insurance_amount > 0 ){
            $insurance_amount = round($insurance_amount * $insurance / 100,2);
        }

        $details = RetailRatesCalculationController::rates($request->shipping_mode_id, $request->business_category_id, $pickup_city_id, $request->consignee_city_id, $request->trax_box, $discount, $weight,$insurance_amount,$packaging, $request->product_id, $request->cod, $request->flyer_count, $request->charged_sms);

        if($request->has('admin_discount'))
        {
            if ($request->filled('admin_discount') && $request->admin_discount > 0) {
                if ($request->has('admin_discount_type1') && $request->admin_discount_type1 ==  1)
                {
                    $details['total_charges'] = $details['total_charges'] - ($details['total_charges'] * $request->admin_discount)/100; // todo: for %
                }
                if ($request->has('admin_discount_type1') && $request->admin_discount_type1 ==  0)
                {
                    $details['total_charges'] = $details['total_charges'] - $request->admin_discount; // todo: for flat
                }
            }
        }


        if ($details['total_charges'] <= 0)
        {
            return response()->json(['status' => 0, 'error' => 'Total charges should be greater than zero !', 'details' => $details]);
        }


        return response()->json(['status' => 1, 'success' => 'Rates Calculated!', 'details' => $details]);
    }

    public function shipper_info(Request $request){
        if($request->has('shipper_account_no')){
            if($request->shipper_account_no != null && $request->shipper_account_no != ''){
                $shipper_info = RetailShipperInfo::find($request->shipper_account_no);
            }
        }
        elseif($request->has('shipper_phone_no')){
            if($request->shipper_phone_no != null && $request->shipper_phone_no != ''){
                $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no)->first();
            }
        }

        if($shipper_info){
            $details = array();
            $details['shipper_account_no'] = $shipper_info->id;
            $details['shipper_phone_no'] = $shipper_info->shipper_phone_no;
            $details['shipper_name'] = $shipper_info->shipper_name;
            $details['shipper_cnic'] = $shipper_info->shipper_cnic;
            $details['shipper_address'] = $shipper_info->shipper_address;
            $details['iban'] = $shipper_info->iban;
            $details['account_number'] = $shipper_info->account_number;
            $details['bank_id'] = $shipper_info->bank_id;

            $shipment = RetailShipment::where('shipper_phone_no', $shipper_info->shipper_phone_no)->where('shipping_mode',3);
            if($shipment->exists()){
                $cod = true;
            }
            else{
                $cod = false;
            }
            if($shipper_info->completed_status == 1){
                $complete_info = true;
            }
            else{
                $complete_info = false;
            }
            return response()->json(['status' => 1, 'success' => 'Shipper Info Found!', 'details' => $details, 'complete_info' => $complete_info,'cod' => $cod]);
        }
        else{
            return response()->json(['status' => 2, 'error' => 'Shipper Info Not Found!']);
        }
    }

    public function slip(Request $request) {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $user_name = Auth::user()->name . ' (Retail)';

        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        $html = '';

        $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">
        ';
        $html .= '
            <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
        ';

        $html .= '
                <title>Slip</title>

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
                    font-size: 0.8rem !important;
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
                   div.page
                    {
                        page-break-after: always;
                        page-break-inside: avoid;
                    }
                    .piece_number{
                        font-size: 2.5rem;
                    }

                    .prominent{
                      font-size:25px; 
                      background-color:black !important; 
                      color:white; 
                      text-align:center; 
                      font-weight: 900;
                      position: relative;" 
                    }
                    .black-logo {
                      filter: grayscale(100%);
                    }
                </style>
              </head>
              <body>
                <div>
        ';

        $html .= '
            <style>

            @media print {
              td.prominent{
                  font-size:25px; 
                  background-color:black !important; 
                  color:white !important; 
                  text-align:center; 
                  font-weight: 900;
                  position: relative;" 
                }
          }
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

              .prominent{
                font-size:25px; 
                background-color:black !important; 
                color:white; 
                text-align:center; 
                font-weight: 900;
                position: relative;" 
              }
            </style>
        ';

        $shipment_details = '';
        $page_items = 1;
        foreach($request->ids as $id) {
            $shipment = Shipment::find($id);

            $sub_segment_name = '-';
            $sub_segment = DB::table('shipper_segment_logs')
            ->leftJoin('sub_category_segments', 'sub_category_segments.id', 'shipper_segment_logs.sub_segment_id')
            ->where('shipment_id', $shipment->id)
            ->select('sub_category_segments.name')
            ->first();

            if ($sub_segment && $sub_segment->name)
            {
                $sub_segment_name = $sub_segment->name;
            }


//            $url = 'storage/retail/shipment_'. $shipment->id.'.jpg';
//            if(!file_exists($url)){
//                $this::save_slip($shipment->id);
//            }
            if($shipment->shipment_type == 1){

               $shipping_mode = $shipment->shipping_mode->mode;
            }
            else{
                $retail_shipment = RetailShipment::where('shipment_id',$shipment->id)->first();
                if($retail_shipment){
                    $shipping_mode = $retail_shipment->shipping_modes->name;
                    $retail_user_name = $retail_shipment->retail_user->store->name;
                }
            }


                    $slip = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';

                    $slip .= '
                          <tr>
                            <td rowspan="4" colspan="2" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                            <td colspan="3" class="color primary"><strong>Shipper Account No.</strong></td>
                            <td colspan="2">' . $shipment->retail->shipper_account_no . '</td>
                            <td colspan="2" class="color primary"><strong>Origin</strong></td>
                            <td colspan="4" class="border twice-right"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                        </tr>
                          <tr>
                            <td colspan="3" class="color primary border"><strong>Airway Bill Number</strong></td>
                            <td colspan="2" class="border twice-bottom"><strong>' . $shipment->tracking_number . '</strong></td>
                            <td colspan="2" class="color primary border"><strong>Destination</strong></td>
                            <td colspan="4" class="border twice-bottom twice-right"><strong>' . $shipment->consignee_city->name . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="3" class="color primary"><strong>Order ID</strong></td>
                            <td colspan="2">'.$shipment->order_id.'</td>

                            <td colspan="2" class="color primary"><strong>Sub Segment</strong></td>
                            <td colspan="4">'. $sub_segment_name .'</td>
</tr>
                          <tr>
                            <td colspan="1" class="color primary border"><strong>#IBAN</strong></td>
                            <td colspan="2" class="border twice-bottom"><strong>' . $shipment->retail->shipper->iban . '</strong></td>
                            <td colspan="2" class="color primary border"><strong>Account Number</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right"><strong>' . $shipment->retail->shipper->account_number . '</strong></td>
                            <td colspan="1" class="color primary border"><strong>Bank</strong></td>
                            <td colspan="2" class="border twice-bottom twice-right"><strong>' . (($shipment->retail->shipper->bank_id != null) ? $shipment->retail->shipper->bank->name : '') . '</strong></td>
                          </tr>
                ';

                    $slip .= '
                          <tr>
                            <td colspan="7" class="text-center color primary border twice-top twice-left twice-right"><strong>Shipper</strong></td>
                            <td colspan="6" class="text-center color primary border twice-top twice-left twice-right"><strong>Consignee</strong></td>
                          </tr>
                ';

                    $slip .= '
                          <tr>
                            <td colspan="1" class="color secondary twice-left"><strong>Name</strong></td>
                            <td colspan="2" class="border">' . $shipment->retail->shipper_name . '</td>
                            <td colspan="2" class="color secondary border"><strong>Phone No</strong></td>
                            <td colspan="2">' . $shipment->retail->shipper_phone_no . '</td>
                            <td colspan="1" class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="1">' . $shipment->consignee_name . '</td>
                            <td colspan="2" class="color secondary border"><strong>Phone No</strong></td>
                            <td colspan="2" class="border twice-right"">' . $shipment->consignee_phone_number_1 . '</td>
                          </tr>
                ';
                    $slip .= '
                      <tr>
                        <td class="color secondary border twice-bottom"><strong>Address</strong></td>
                        <td colspan="6" class="border twice-bottom twice-right">' . $shipment->retail->shipper_address . '</td>
                        <td class="color secondary border twice-bottom twice-left"><strong>Address</strong></td>
                        <td colspan="5" class="border twice-bottom twice-right">' . $shipment->consignee_address . '</td>
                      </tr>
                ';
                    $gst = $shipment->retail->gst;
                    $packaging_and_insurance = $shipment->retail->packaging_charges + $shipment->retail->insurance_charges;
                    $r_t = ($shipment->retail->admin_discount_type == 1) ? ' %' : (($shipment->retail->admin_discount_type == 0) ? ' Flat' : ' -');
                    $slip .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Product</strong></td>
                                <td colspan="1" class="color primary"><strong>Pieces</strong></td>
                                <td colspan="1" class="color primary"><strong>Weight</strong></td>
                                <td colspan="1" class="color primary"><strong>Service Charges</strong></td>
                                <td colspan="1" class="color primary"><strong>Discount(Trax Center)</strong></td>
                                <td colspan="1" class="color primary"><strong>Discount(Consumer)</strong></td>
                                <td colspan="1" class="color primary"><strong>Charges With Discount</strong></td>
                                <td colspan="1" class="color primary"><strong>Flyer Charges</strong></td>
                                <td colspan="1" class="color primary"><strong>SMS Charges</strong></td>
                                <td colspan="1" class="color primary border"><strong>GST</strong></td>
                                <td colspan="1" class="color primary border"><strong>Packaging & Insurance </strong></td>
                                <td colspan="2" class="color primary border twice-right"><strong>Total Charges</strong></td>
                            </tr>
                              <tr>
                                <td colspan="2" class="border twice-bottom twice-left">' . $shipment->retail->shipping_modes->name . '</td>
                                <td colspan="1" class="border twice-bottom">' . $shipment->pieces . '</td>
                                <td colspan="1" class="border twice-bottom">' . floatval($shipment->estimated_weight) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($shipment->retail->weight_charges,2) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($shipment->retail->discount,2) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($shipment->retail->admin_discount,2) .$r_t.'</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($shipment->retail->charges_with_discount,2) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($shipment->retail->flyer_charges,2) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($shipment->retail->sms_charges,2) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($gst,2) . '</td>
                                <td colspan="1" class="border twice-bottom">' . number_format($packaging_and_insurance,2) . '</td>
                                <td colspan="2" class="border twice-bottom twice-right">' . number_format(ROUND($shipment->retail->total_charges)) . '</td>
                              </tr>';

                    foreach($shipment->items as $item){
                        if($item->insurance == 1){
                            $insurance = '<i class="la la-check-square "> <b>Yes</b></i> <i class="la la-minus-square"> No</i>';
                        }
                        else{
                            $insurance = '<i class="la la-minus-square"> Yes</i> <i class="la la-check-square"> <b>No</b></i>';
                        }
                        $slip .= '
                              <tr>
                                <td colspan="1" class="color primary border twice-left twice-bottom"><strong>Product Name</strong></td>
                                <td colspan="3" class="color border twice-bottom">'. $item->product->product_name . '</td>
                                <td colspan="5" class="color border twice-bottom text-center mr-3"><strong>Insurance: Do you required coverage</strong> '. $insurance . '</td>
                                <td colspan="2" class="color primary border twice-bottom"><strong>Declared Value</strong></td>
                                <td colspan="2" class="color border twice-bottom twice-right">' . number_format(ROUND($item->price, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>';
                    }

                    if($shipment->length != null && $shipment->breadth != null && $shipment->height != null){
                        $dimensions = $shipment->length . 'x' . $shipment->breadth . 'x' . $shipment->height;
                    }
                    else{
                        $dimensions = '';
                    }

                    $slip .= '
                              <tr>
                                <td colspan="4" class="color primary border twice-left twice-bottom"><strong>DIMENSIONS OF SHIPMENT (LxWxD)</strong></td>
                                <td colspan="5" class="color border twice-bottom">'. $dimensions . '</td>
                                <td colspan="2" class="color primary border twice-left"><strong>Collection By</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->name . '</td>
                            </tr>';
                    $slip .= '
                              <tr>
                                <td colspan="4" rowspan="2" class="color primary border twice-left"><strong>Shipper\'s Signature</strong></td>
                                <td colspan="5" rowspan="2" class="color border twice-bottom"></td>
                                <td colspan="2" class="color primary border twice-left"><strong>Code</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->store->code . '</td>
                            </tr>';
                    $slip .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Date</strong></td>
                                <td colspan="4" class="color border twice-bottom twice-right">' . $shipment->created_at . '</td>
                            </tr>';

                            $slip .= '
                            <tr>
                              <td rowspan="2" colspan="4" class="color primary border twice-top twice-bottom"></td>
                              <td rowspan="2" colspan="4" class="border twice-top twice-bottom twice-right"></td>';
                    
                      if ($shipment->charges_mode_id != 2) {
                          $slip .= '
                              <td colspan="2" class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                              <td colspan="2" class="border twice-top twice-bottom twice-left"><strong>' . $shipment->retail->payment_mode->name . '</strong></td>
                      ';
                      }
  
                      if ($shipment->booking_type_id != 5 && $shipment->booking_type_id != 3) {
                          $slip .= '
                                </tr>
                                <tr>
                                  <td colspan="2" class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                      ';
                          $amount = $shipment->amount;
  
                          if ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1) {
                              $slip .= '
                                  <td colspan="2" class="align-middle border twice-top twice-bottom twice-left"><strong>Rs '. $amount .'</strong></td>
                          ';
                          } else {
                              $slip .= '
                                  <td colspan="2" class="align-middle border twice-top twice-bottom twice-left"><strong>Rs '. $amount .'</strong></td>
                          ';
                          }
                      }
  
                      $slip .= '
                            </tr>
                            </tbody>
                            </table>
                            </div>
                            <div class="col m-1 row">
                              <h1 style="
                             overflow: hidden;
                             margin-top: -220px;
                             margin-left: 300px;
                             opacity: 0.3;
                             transform: rotate(350deg);
                             font-size: 700%; 
                             color: #636e72;"     
                              >RETAIL</h1>
                            </div>
                           ';

            $slip .= '
                <div class="col m-1 row justify-content-center">
                    <div class="col"><hr></div>
                    <div class=""><p>Sales and Income tax has been deducted from the total COD amount as per applicable tax laws.</p></div>
                    <div class="col"><hr></div>
                </div>
                  <div class="col m-1 row justify-content-center">
                    <div class="col"><hr></div>
                    <div class=""><p>Shipper Copy</p></div>
                    <div class="col"><hr></div>
                    <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div>
                  </div>
                ';
            $shipment_details .= $slip;

            //airway_bill_start


                if ($shipment->booking_type_id == 3) {
                    foreach ($shipment->items as $shipment_item){
                        if($page_items == 0){
                            $table_start = '
                    <div class="page position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>
            ';
                        }
                        else{
                            $table_start = '
                    <div class="position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>
            ';
                        }

                        $table_start .= '
                                <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto black-logo">' . $print_details . '</td>
                    ';
                        $table_start .= '
                            <td rowspan="4" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment_item->id . '</strong></span>
                            </td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG((string)$shipment_item->id, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                            <tr>
                                <td class="color secondary border twice-top twice-left"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $shipment_item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td colspan="1" class="border twice-top">' . $shipment_item->quantity . '</td>
                                <td colspan="2" class="color secondary border twice-top"><b>Tracking Number</b></td>
                            </tr>
                ';

                        $table_start .= '
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="2" class="border twice-bottom">' . $shipment_item->description  . '</td>
                                <td class="color secondary border twice-bottom"><strong>Price</strong></td>
                                <td class="border twice-bottom">Rs ' . number_format($shipment_item->price) . '</td>';

                        $table_start .= '
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG((string)$shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                          </tr>
                        </tbody>
                    </table></div>
                    ';
                        $shipment_details .= $table_start;
                        $page_items++;
                        if($page_items >= 3){
                            $page_items = 0;
                        }
                    }
                } else {
                    $page_items = $page_items + 3;
                    if($page_items >= 5){
                        $page_items = 0;
                    }
                    $table_start = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';
                    $table_start .= '
                            <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto black-logo">' . $print_details . '</td>
                ';
                    $table_start .= '
                            <td rowspan="4" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG((string)$shipment->tracking_number, 'QRCODE', 12, 12) . '" class="d-block mx-auto">
                            </td>
                    ';

                          //   if($shipment->business_category->id==2){
                          //     $table_start .='<td class="color primary border twice-left"><strong>Service Type</strong></td>
                          //     ';
                          // }else{
                          //   $table_start .='<td class="color primary border twice-left"><strong>Service</strong></td>
                          //   ';
                          // }
                            

                    // if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                    //     $table_start .= '
                    //             <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    // ';
                    // } else if ($shipment->booking_type_id == 2) {
                    //         $table_start .= '
                    //             <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                    //     ';
                    // }
                    // else {
                    //     $table_start .= '
                    //             <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    // ';
                    // }
                    $table_start .= '
                            <td class="color primary" ><strong>Datetime</strong></td>
                            <td colspan="3">' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                          </tr>
                          <tr>';
                //           if($shipment->business_category->id==1){
                //             $table_start .='<td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                //             <td><strong>' . $shipping_mode . '</strong></td>
                // ';
                //         }
                           

                    $table_start .= '
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td colspan="3">' . $shipment->order_id . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                            <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Business Category</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right"><strong>' . $shipment->business_category->name . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="3" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                            <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Name</strong></td>
                ';

                    $table_start .= '
                            <td colspan="2" class="border twice-right">' . $shipment->retail->shipper_name . '</td>
                ';

                    $table_start .= '
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                ';

                    $table_start .= '
                        <td class="color secondary"><strong>Address</strong></td>
                        <td colspan="2" class="border twice-right">' . $shipment->retail->shipper_address . '</td>
                ';

                    $table_start .= '
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                ';

                    $table_start .= '
                    <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                    <td colspan="2" class="border twice-bottom twice-right">' . $shipment->retail->shipper_phone_no . '</td>
                ';

                    $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                          </tr>
                ';

                    $table_end = '
                          <tr>
                            <td rowspan="2" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                            <td rowspan="2" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>';
                    if($shipment->shipping_mode_id == 2 && $shipment->estimated_weight != null ) {
                        $table_end .= ' <td class="color primary border twice-top twice-bottom twice-left"><strong>Weight</strong></td>
                        <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . '</strong></td>
                          </tr>
                          <tr>
                ';
                    }
                    else{
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
                    }  elseif ($shipment->charges_mode_id != 2) {
                        $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->retail->payment_mode->name . '</strong></td>
                    ';
                    } else {
                        $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                    ';
                    }

                    if ($shipment->booking_type_id != 5 && $shipment->booking_type_id != 3) {
                        $table_end .= '
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                    ';
                        $amount = $shipment->amount;

                        if ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1) {
                            $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs '. $amount .'</strong></td>
                        ';
                        } else {
                            $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs '. $amount .'</strong></td>
                        ';
                        }
                    }

                    $shiping_mode = "";
                    $service_type = "";
                    if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                        $service_type .= '<span ><strong>' . $shipment->booking_type->booking_type . '</span></td>';
                    } 
                    else if ($shipment->booking_type_id == 2) {
                        $service_type .= '<span class=" replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></span>';
                    }
                    else {
                        $service_type .= '<span><strong>' . $shipment->booking_type->booking_type . '</strong></span>';
                    }

                    if($shipment->business_category->id==1){
                            $shiping_mode .='<td colspan="2" class="prominent"><strong>' . $shipping_mode . '</strong></td>';
                    }
                    
                    $table_end .= '
                    <tr>
                      <td colspan="1" style="font-size:13px;" class=""><strong>Shipping Mode</strong></td>
                     '.$shiping_mode.'
                      <td colspan="1" style="font-size:13px;" class=""><strong>Service - '. $service_type .'</strong></td>
                      <td colspan="4"  class="prominent"><strong>Centre Name - ' .$retail_user_name. '</strong></td>
                    </tr> ';

                    $table_end .= '
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>برائے مہربانی رائڈر / کورئیر کو کوئی اضافی پیسہ نہ دیں۔ اگر پارسل / پیکٹ خراب یا خراب حالت میں ہے تو ، براہ کرم اسے وصول نہ کریں۔</em></td>
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>ٹریکس لاجسٹک کا اس پارسل / پیکٹ میں موجود کسی آئٹم یا مواد سے کوئی تعلق نہیں ہے۔ ہم سامان ایک جگہ سے دوسری جگہ بھیجتے ہیں۔ اگر آپ کو اس بارے میں کوئی شکایت ہے تو ، براہ کرم متعلقہ آن لائن اسٹور / شپر  سے رابطہ کریں۔</em></td>
                          </tr>
                        </tbody>
                    </table>
                ';

                    $table_end .= '
                      </div>
                ';

                    $table_end .= '
                    <div class="col m-1 row">
                              <h1 style="
                              overflow: hidden;
                              margin-top: -304px;
                              margin-left: 348px;
                              opacity: 0.3;
                              transform: rotate(340deg);
                              font-size: 1200%;
                              color: #636e72;"
                              >RETAIL</h1>
                            </div>
                    <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Trax Copy</p></div><div class="col"><hr></div>
                  <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                ';

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
                                <td colspan="1" class="color secondary border twice-top"><strong>Piece(s)</strong></td>
                                <td>'. $shipment->pieces .'</td>
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
                                <td colspan="6" class="border twice-bottom">' . $item->description  . '</td>
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
                          <td colspan="6" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-bottom">' . $item->description  . '</td>
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

                    }

                    if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                        $shipment_pieces = '';

                        foreach ($shipment->shipment_pieces as $piece){
                            $shipment_pieces .= '<table class="table table-sm table-bordered border twice">
                        <tbody><tr>';
                            $shipment_pieces .= '<td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto black-logo">' . $print_details . '</td>';
                            $shipment_pieces .= '<td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($piece->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $piece->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG((string)$piece->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
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
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG((string)$shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right"><span class="piece_number"><strong>' . $piece->numbering. '/' .$shipment->pieces . '</strong></span>
                            </td>
                                </tr>
                                <tr>
                                <td class="color primary border twice-left"><strong>Shipper</strong></td>
                                <td class="border">'. $shipment->retail->shipper_name .'</td>
                                <td class="color primary border "><strong>Booking Date</strong></td>
                                <td class="border">'. $shipment->created_at .'</td>
</tr>
                              ';
                            $shipment_pieces .= '</tbody></table>
                    <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Trax Copy</p></div><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>';

                        }


                        $shipment_details .= $shipment_pieces;
                    }
                }


            //airway_bill_end

        }

        $html .= $shipment_details;

        $html .= '
                </div>
        ';

            $html .= '
            <script>
              window.onload = function() {
                window.print();
              }
            </script>
            ';

        $html .= '
              </body>
            </html>
        ';
        return $html;
    }
    public static function save_slip($shipment_id){
        $html = '';

        $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . file_get_contents(public_path('app-assets/fonts/line-awesome/css/line-awesome.min.css')) . '">
        ';
        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
                <title>Slip</title>

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
                       div.page
                        {
                            page-break-after: always;
                            page-break-inside: avoid;
                        }
                        .piece_number{
                            font-size: 2.5rem;
                        }
                    </style>
              </head>
              <body>
                <div>
        ';

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

            $shipment = Shipment::find($shipment_id);
            $html = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';

            $html .= '
                          <tr>
                            <td rowspan="3" colspan="2" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td colspan="3" class="color primary"><strong>Shipper Account No.</strong></td>
                            <td colspan="2">' . $shipment->retail->shipper_account_no . '</td>
                            <td colspan="2" class="color primary"><strong>Origin</strong></td>
                            <td colspan="4" class="border twice-right"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                        </tr>
                          <tr>
                            <td colspan="3" class="color primary border"><strong>Airway Bill Number</strong></td>
                            <td colspan="2" class="border twice-bottom"><strong>' . $shipment->tracking_number . '</strong></td>
                            <td colspan="2" class="color primary border"><strong>Destination</strong></td>
                            <td colspan="4" class="border twice-bottom twice-right"><strong>' . $shipment->consignee_city->name . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="1" class="color primary border"><strong>#IBAN</strong></td>
                            <td colspan="2" class="border twice-bottom"><strong>' . $shipment->retail->shipper->iban . '</strong></td>
                            <td colspan="2" class="color primary border"><strong>Account Number</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right"><strong>' . $shipment->retail->shipper->account_number . '</strong></td>
                            <td colspan="1" class="color primary border"><strong>Bank</strong></td>
                            <td colspan="2" class="border twice-bottom twice-right"><strong>' . $shipment->retail->shipper->bank->name . '</strong></td>
                          </tr>
                ';

            $html .= '
                          <tr>
                            <td colspan="7" class="text-center color primary border twice-top twice-left twice-right"><strong>Shipper</strong></td>
                            <td colspan="6" class="text-center color primary border twice-top twice-left twice-right"><strong>Consignee</strong></td>
                          </tr>
                ';

            $html .= '
                          <tr>
                            <td colspan="1" class="color secondary twice-left"><strong>Name</strong></td>
                            <td colspan="2" class="border">' . $shipment->retail->shipper_name . '</td>
                            <td colspan="2" class="color secondary border"><strong>Phone No</strong></td>
                            <td colspan="2">' . $shipment->retail->shipper_phone_no . '</td>
                            <td colspan="1" class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="1">' . $shipment->consignee_name . '</td>
                            <td colspan="2" class="color secondary border"><strong>Phone No</strong></td>
                            <td colspan="2" class="border twice-right"">' . $shipment->consignee_phone_number_1 . '</td>
                          </tr>
                ';
            $html .= '
                      <tr>
                        <td class="color secondary border twice-bottom"><strong>Address</strong></td>
                        <td colspan="6" class="border twice-bottom twice-right">' . $shipment->retail->shipper_address . '</td>
                        <td class="color secondary border twice-bottom twice-left"><strong>Address</strong></td>
                        <td colspan="5" class="border twice-bottom twice-right">' . $shipment->consignee_address . '</td>
                      </tr>
                ';
            $fuel_and_gst = $shipment->retail->fuel_surcharge + $shipment->retail->gst;
            $html .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Product</strong></td>
                                <td colspan="2" class="color primary"><strong>Pieces</strong></td>
                                <td colspan="2" class="color primary"><strong>Weight</strong></td>
                                <td colspan="2" class="color primary"><strong>Service Charges</strong></td>
                                <td colspan="2" class="color primary border"><strong>Fuel and GST</strong></td>
                                <td colspan="2" class="color primary border twice-right"><strong>Total Charges</strong></td>
                            </tr>
                              <tr>
                                <td colspan="2" class="border twice-bottom twice-left">' . $shipment->retail->shipping_modes->name . '</td>
                                <td colspan="2" class="border twice-bottom">' . $shipment->pieces . '</td>
                                <td colspan="2" class="border twice-bottom">' . number_format($shipment->estimated_weight) . '</td>
                                <td colspan="2" class="border twice-bottom">' . number_format(ROUND($shipment->retail->weight_charges, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                <td colspan="2" class="border twice-bottom">' . number_format(ROUND($fuel_and_gst, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                <td colspan="2" class="border twice-bottom twice-right">' . number_format(ROUND($shipment->retail->total_charges, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                              </tr>';

            foreach($shipment->items as $item){
                if($item->insurance == 1){
                    $insurance = '<i class="la la-check-square "> <b>Yes</b></i>';
                }
                else{
                    $insurance = '<i class="la la-check-square"> <b>No</b></i>';
                }
                $html .= '
                              <tr>
                                <td colspan="1" class="color primary border twice-left twice-bottom"><strong>Product Name</strong></td>
                                <td colspan="3" class="color border twice-bottom">'. $item->product->product_name . '</td>
                                <td colspan="5" class="color border twice-bottom text-center mr-3"><strong>Insurance: Do you required coverage</strong> '. $insurance . '</td>
                                <td colspan="2" class="color primary border twice-bottom"><strong>Declared Value</strong></td>
                                <td colspan="2" class="color border twice-bottom twice-right">' . number_format(ROUND($item->price, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>';
            }

            if($shipment->length != null && $shipment->breadth != null && $shipment->height != null){
                $dimensions = $shipment->length . 'x' . $shipment->breadth . 'x' . $shipment->height;
            }
            else{
                $dimensions = '';
            }

            $html .= '
                              <tr>
                                <td colspan="4" class="color primary border twice-left twice-bottom"><strong>DIMENSIONS OF SHIPMENT (LxWxD)</strong></td>
                                <td colspan="5" class="color border twice-bottom">'. $dimensions . '</td>
                                <td colspan="2" class="color primary border twice-left"><strong>Collection By</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->name . '</td>
                            </tr>';
            $html .= '
                              <tr>
                                <td colspan="4" rowspan="2" class="color primary border twice-left"><strong>Shipper\'s Signature</strong></td>
                                <td colspan="5" rowspan="2" class="color border twice-bottom"></td>
                                <td colspan="2" class="color primary border twice-left"><strong>Code</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->store->code . '</td>
                            </tr>';
            $html .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Date</strong></td>
                                <td colspan="4" class="color border twice-bottom twice-right">' . $shipment->created_at . '</td>
                            </tr>
                            </tbody>
                            </table>
                            </div>
                           ';

            $html .= '
                  <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Shipper Copy</p></div><div class="col"><hr></div>
                  <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                ';
            $slip_image = $html;
            $slip_image .= '</div></body></html>';
            SnappyImage::loadHTML($slip_image)->save('storage/retail/shipment_'. $shipment->id.'.jpg');
    }

    public function tracking_slip_index(){
        return view('retail.shipment.tracking_slip');
    }

    public function tracking_slip_list(Request $request){
        $shipments = RetailShipment::join('shipments as s', 's.id', '=', 'retail_shipments.shipment_id')
            ->select('retail_shipments.id', 's.tracking_number as tracking_number', 'retail_shipments.slip_image')
            ->where('retail_user_id', Auth::id())
        ->orderBy('retail_shipments.created_at', 'desc');
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('retail.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('slip_image', function ($data) {
                if ($data->slip_image != null) {
                    return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('storage/retail_slip/' . $data->slip_image) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                }
                else {
                    return '';
                }
            })
            ->addColumn('action', function ($runner_details){
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                $dropdown .= '<button type="button" class="dropdown-item upload" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Upload Slip</div></button>';

                return $dropdown;
            });
        return  $datatable
        ->rawColumns(['tracking_number_link', 'slip_image', 'action'])
        ->make(true);
    }

    public function tracking_slip_upload(Request $request){
        $retail_shipment_id = $request->retail_shipment_id;
        $retail_shipment = RetailShipment::find($retail_shipment_id);
        if ($request->hasFile('upload_attachment')) {
            $filename = 'shipment_'. $retail_shipment->shipment_id.'.jpg';

            $file = $request->file('upload_attachment');

            Storage::disk('public')->putFileAs('retail_slip', $file, $filename);

            $retail_shipment->slip_image = $filename;
            $retail_shipment->save();
        }
        return redirect()->back()->with('success', 'Slip uploaded successfully');
    }

    public function other_booking_index(){
        if(session('category') == 2){
            return view('retail.shipment.other_booking');
        }else{
            return redirect()->route('retail.404');
        }
    }

    public function other_booking_list(Request $request){
        $retail_user_id = Auth::id();
        $retail_user = RetailUser::find($retail_user_id);
        $retail_trax_center = RetailTraxCenter::find($retail_user->category_id);
        $shipments = RetailShipment::join('shipments as s', 's.id', '=', 'retail_shipments.shipment_id')
            ->select('retail_shipments.id', 's.tracking_number as tracking_number', 'retail_shipments.created_at as created_at')
            ->where('s.pickup_address_id', $retail_trax_center->pickup_address_id)
            ->wherenull('retail_shipments.retail_user_id')
            ->orderBy('retail_shipments.created_at', 'desc');
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('retail.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            });

        if ($request->get('search_date')) {
            $date = $request->get('search_date');
            $shipments->whereDate('retail_shipments.created_at', $date);
        }

        return $datatable
        ->rawColumns(['tracking_number_link'])
        ->make(true);
    }

    public function excel_index() {
        $products = Product::all();
        $business_categories = BusinessCategory::where('id', 1)->get();
        $shipping_modes = RetailShippingMode::where('id','!=',3)->where('business_category_id',1)->get();
        $domestic_cities = City::where('business_category_id', 1)->where('booking_enable_status', 1)->where('status', 1)->get();
        $international_cities = City::where('business_category_id', 2)->where('permanent_disabled',0)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', 1)->get();
        $charges_modes = ChargesMode::whereIn('id', [1, 2])->get();
        $trax_boxes = RetailTraxBox::all();
        $banks = BanksList::all();
        return view('retail.shipment.booking.excel')->with(['products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'international_cities' => $international_cities, 'domestic_overland_cities' => $domestic_overland_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes, 'banks' => $banks, 'charges_modes' => $charges_modes]);
    }
    public function excel_store(Request $request) {
        $retail_user_id = Auth::id();
        $user_id = session('user_id');
        $pickup_address_id = session('pickup_address_id');
        $category = session('category');
        $category_id = session('category_id');

        $names = [
            'product_id' => 'Shipment ID',
            'business_category_id' => 'Business Category ID',
            'shipping_mode_id' => 'Product ID',
            'destination' => 'Destination',
            'volumetric_weight' => 'Volumetric Weight',
            'weight' => 'Weight (kg)',
            'length' => 'Length (cm)',
            'breadth' => 'Breadth (cm)',
            'height' => 'Height (cm)',
            'pieces' => 'Pieces',
            'payment_mode_id' => 'Payment Mode ID',
            'charges_mode_id' => 'Charges Mode ID',
            'shipper_cell_number' => 'Shipper Cell Number',
            'shipper_name' => 'Shipper Name',
            'shipper_cnic' => 'Shipper CNIC',
            'shipper_address' => 'Shipper Address',
            'consignee_cell_number' => 'Consignee Cell Number',
            'consignee_name' => 'Consignee Name',
            'consignee_cnic' => 'Consignee CNIC',
            'consignee_address' => 'Consignee Address',
            'order_id' => 'Order ID',
            'insurance_offered' => 'Insurance Offered',
            'insurance_value' => 'Insurance Value',
            'packaging_charges' => 'Packaging Charges',
            'trax_box_id' => 'Trax Box ID',
        /*    'weight_charges' => 'Weight Charges',
            'fuel_surcharge' => 'Fuel Surcharge',*/
            'iban_number' => 'IBAN Number',
            'account_number' => 'Account Number',
            'bank_id' => 'Bank ID',
            'special_instruction' => 'Special Instruction',
            'admin_discount' => 'Admin Discount',
            'admin_discount_type' => 'Admin Discount Type',
            'parcel_amount' => 'Parcel Value',
            'quantity' => 'Quantity',
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

            'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.'
        ];

        $rules = [
            'product_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('products', 'id')],
            'business_category_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('business_categories', 'id')->where('id', 1)],
            'shipping_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('retail_shipping_modes', 'id')->whereNotIn('id', [3])],
            'destination' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('booking_enable_status', 1)->where('business_category_id', 1)],
            'volumetric_weight' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'weight' => ['nullable', 'numeric', 'between:0.1,100000'],
            'length' => ['nullable', 'numeric', 'between:0.1,100000'],
            'breadth' => ['nullable', 'numeric', 'between:0.1,100000'],
            'height' => ['nullable', 'numeric', 'between:0.1,100000'],
            'pieces' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,50'],
            'payment_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('retail_payment_modes', 'id')->where('id', 1)],
            'charges_mode_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->whereIn('id', [1, 2])],
            'shipper_cell_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'shipper_name' => ['required', 'between:1,100'],
            'shipper_cnic' => ['nullable', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
            'shipper_address' => ['required', 'between:1,255'],
            'consignee_cell_number' => ['required', 'regex:/^[0][0-9]{10}$/'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_cnic' => ['nullable', 'regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/'],
            'consignee_address' => ['required', 'between:1,255'],
            'order_id' => ['nullable'],
            'insurance_offered' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'insurance_value' => ['required_if:insurance_offered,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],
            'packaging_charges' => ['nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],
            'trax_box_id' => ['required_if:shipping_mode_id,5', 'nullable', 'integer', Rule::exists('retail_trax_boxes', 'id')],
          /*  'weight_charges' => ['required', 'numeric'],
            'fuel_surcharge' => ['required', 'numeric'],*/
            'iban_number' => ['nullable', 'between:1,50'],
            'account_number' => ['nullable', 'numeric'],
            'bank_id' => ['nullable', 'integer', 'between:1,100', Rule::exists('banks_lists', 'id')],
            'special_instruction' => ['nullable', 'between:1,190'],
            'admin_discount_type' => ['nullable'],
            'admin_discount' => ['nullable', 'between:1,100'],
            'parcel_amount' => ['required'],
            'quantity' => ['required'],
        ];

        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        }

        if (isset($spreadsheet)) {
                // $fields = [0 => 'product_id', 1 => 'business_category_id', 2 => 'shipping_mode_id', 3 => 'destination', 4 => 'volumetric_weight', 5 => 'weight', 6 => 'length', 7 => 'breadth', 8 => 'height', 9 => 'pieces', 10 => 'payment_mode_id', 11 => 'charges_mode_id', 12 => 'shipper_cell_number', 13 => 'shipper_name', 14 => 'shipper_cnic', 15 => 'shipper_address', 16 => 'consignee_cell_number', 17 => 'consignee_name', 18 => 'consignee_cnic', 19 => 'consignee_address', 20 => 'order_id', 21 =>'insurance_offered',22 => 'insurance_value', 23 =>'packaging_charges',24 => 'trax_box_id', 25 => 'iban_number', 26 => 'account_number', 27 => 'bank_id', 28 => 'special_instruction'];

                $fields = [
                    0 => 'product_id',
                    1 => 'business_category_id',
                    2 => 'shipping_mode_id',
                    3 => 'destination',
                    4 => 'volumetric_weight',
                    5 => 'weight',
                    6 => 'length',
                    7 => 'breadth',
                    8 => 'height',
                    9 => 'pieces',
                    10 => 'payment_mode_id',
                    11 => 'charges_mode_id',
                    12 => 'shipper_cell_number',
                    13 => 'shipper_name',
                    14 => 'shipper_cnic',
                    15 => 'shipper_address',
                    16 => 'consignee_cell_number',
                    17 => 'consignee_name',
                    18 => 'consignee_cnic',
                    19 => 'consignee_address',
                    20 => 'order_id',
                    21 => 'insurance_offered',
                    22 => 'insurance_value',
                    23 => 'packaging_charges',
                    24 => 'trax_box_id',
                    25 => 'iban_number',
                    26 => 'account_number',
                    27 => 'bank_id',
                    28 => 'special_instruction',
                    29 => 'admin_discount',
                    30 => 'admin_discount_type',
                    31 => 'parcel_amount',
                    32 => 'quantity'
                ];

            // if (count($spreadsheet[0]) != 29){
            //     return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            // }
            if (count($spreadsheet[0]) != count($fields)){
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
            }

            $errors = array();
            $check = NonServiceArea::pluck('name')->toArray();
            $nsa_error = array();

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if(!isset($row['pieces']) || $row['pieces'] == null){
                    $row['pieces'] = 1;
                }

                $rows[$key]['pieces'] = $row['pieces'];
                $rows[$key]['packaging_charges'] = $row['packaging_charges'];

                if(!isset($row['insurance_offered']) || $row['insurance_offered'] == null){
                    //$row['insurance_offered'] = 'no';
                    $errors[$row_id]['insurance_offered'] = 'Select option for insurance as yes/no';
                }

                $rows[$key]['insurance_offered'] = $row['insurance_offered'];

                if(in_array($row['insurance_offered'],['YES','YEs','YeS','Yes','yES','yEs','yeS','yes']) && !isset($row['insurance_value'])){
                    $errors[$row_id]['insurance_value'] = 'Insurance value is required';
                }
                else{
                    $rows[$key]['insurance_value'] = $row['insurance_value'];
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
                    $shipping_mode_id = $row['shipping_mode_id'];
                    $city_name = $row['destination'];
                    if($shipping_mode_id == 1){
                        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->pluck('c.name')->toArray();
                        $domestic_overland_cities = array_map('strtolower', $domestic_overland_cities);
                        if(!in_array(strtolower($city_name), $domestic_overland_cities)){
                            $errors[$row_id]['destination'] = 'City ' . $city_name . ' is not allowed for Product ID# ' . $shipping_mode_id;
                        }
                    }

                    if(strtolower($row['volumetric_weight']) == 'yes'){
                        if($row['length'] == null){
                            $errors[$row_id]['length'] = 'Length is required when Volumetric Weight is set to Yes';
                        }
                        if($row['breadth'] == null){
                            $errors[$row_id]['breadth'] = 'Breadth is required when Volumetric Weight is set to Yes';
                        }
                        if($row['height'] == null){
                            $errors[$row_id]['height'] = 'Height is required when Volumetric Weight is set to Yes';
                        }
                        $row['weight'] = null;
                        $rows[$key]['weight'] = $row['weight'];
                    }
                    else{
                        if($row['weight'] == null){
                            $errors[$row_id]['weight'] = 'Weight is required when Volumetric Weight is set to No';
                        }
                        $row['length'] = null;
                        $row['breadth'] = null;
                        $row['height'] = null;
                        $row['business_category_id'] = null;
                        $rows[$key]['length'] = $row['length'];
                        $rows[$key]['breadth'] = $row['breadth'];
                        $rows[$key]['height'] = $row['height'];
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
            }

            if (empty($errors)) {
                if (empty($nsa_error)) {
                    foreach ($rows as $key => $row) {
                        $row['user_id'] = $user_id;
                        $row['retail_user_id'] = $retail_user_id;
                        $row['pickup_address_id'] = $pickup_address_id;
                        $row['category'] = $category;
                        $row['category_id'] = $category_id;
                        dispatch(new ProcessRetailShipmentBookingDB($row));
                    }

                    return redirect()->route('retail.shipment.book.excel')->with(['success' => 'Booking of ' . count($rows) . ' Shipment(s) is being Processed']);
                } else{
                    return view('retail.shipment.booking.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error]);
                }

            }
            else {
                $products = Product::pluck('product_name', 'id');
                $business_categories = BusinessCategory::where('id', '!=', 2)->pluck('name', 'id');
                $shipping_modes = RetailShippingMode::where('id', '!=', 3)->pluck('name', 'id');
                $domestic_cities = City::where('business_category_id', 1)->where('booking_enable_status', 1)->where('status', 1)->get();
                //                $international_cities = City::where('business_category_id', 2)->where('permanent_disabled',0)->where('status', 1)->get();
                $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.name')->get();
                $payment_modes = RetailPaymentMode::where('id', 1)->pluck('name', 'id');
                $charges_modes = ChargesMode::whereIn('id', [1, 2])->pluck('charges_mode', 'id');
                $trax_boxes = RetailTraxBox::pluck('name', 'id');
                $banks = BanksList::pluck('name', 'id');
                $domestic_city_name = array();
                $domestic_overland_city_name = array();
                foreach ($domestic_cities as $domestic_city) {
                    $domestic_city_name[$domestic_city->name] = $domestic_city->name;
                }
                foreach ($domestic_overland_cities as $domestic_overland_city) {
                    $domestic_overland_city_name[$domestic_overland_city->name] = $domestic_overland_city->name;
                }

                return view('retail.shipment.booking.errors')->with(['data' => $rows, 'errors' => $errors, 'domestic_cities' => $domestic_city_name, 'domestic_overland_cities' => $domestic_overland_city_name, 'business_categories' => $business_categories, 'trax_boxes' => $trax_boxes, 'products' => $products, 'shipping_modes' => $shipping_modes, 'banks' => $banks, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes]);
            }
        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }
    public function add_city_req(Request $request){
      $city_name = '';
      if($request->city_domestic == null){
        if($request->city_international == 'other'){
          $city_name = $request->other_cities_international;
        }else{
          $city_name = $request->city_international;

        }
      }else{
        if($request->city_domestic == 'other'){
          $city_name = $request->other_city_domestic;
        }else{
          $city_name = $request->city_domestic;

        }
      }
      RetailRequestCity::create([
        'added_by' => Auth::id(),
        'business_category_id' => $request->city_business_category,
        'shipping_mode_id' => $request->city_shipping_mode,
        'city_name' => $city_name,
        'phone_number' => $request->city_phone_number,
      ]);

      return response()->json(['status' => 1, 'success' => 'City Request Added']);

    }

    public function consignee_info(Request $request){
      $phone = $request->phone;
      // $phone = substr_replace($request->phone, '-', 4, 0);
        $message = '';
        $consignee_information = ConsigneeInformation::where('phone', $phone);
        if ($consignee_information->exists()) {
          $consignee_information = $consignee_information->first();
          $consignee_info = ConsigneeInformationLog::where('consignee_information_id',$consignee_information->id)->get();
          
          $blacklist = BlacklistedConsignee::where('consignee_information_id', $consignee_information->id);
          if ($blacklist->exists()) {
                  // dd($blacklist->get());
                  
                  return response()->json(['status' => 0, 'consignee' => $consignee_info, 'blacklist'=> 1]);
                }else{
                  
                  return response()->json(['status' => 0, 'consignee' => $consignee_info, 'blacklist'=> 0]);
                }
        }
        return response()->json(['status' => 1, 'message' => 'Consignee Not Found']);

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

    public function is_discount_available_to_apply($discountCode = null)
    {

        $data = RetailDiscountCode::where('code', $discountCode)->first();

        if (!$data) {
            return response()->json([
                'status' => 0,
                'message' => 'Discount code is not valid',
                'data' => null,
            ]);
        }

        if ($data->shipment_id) {
            return response()->json([
                'status' => 0,
                'message' => 'Discount code is already used',
                'data' => $data,
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Discount code is valid',
            'data' => $data,
        ]);
    }

    static function previous_names_verify_update($phone_number,$shipper_name,$shipper_cnic,$shipper_address, $id)
    {
        $phone_number = str_replace('-', '', $phone_number);
        $shipper_cnic = str_replace('-', '', $shipper_cnic);

        $existing_records = RetailShipperNameVerification::where('phone_number', $phone_number)->get();

        if ($existing_records->isNotEmpty()) {
            $foundDuplicate = false;

            foreach ($existing_records as $existing_record) {
                if (
                    $existing_record->phone_number == $phone_number &&
                    $existing_record->shipper_name == $shipper_name &&
                    $existing_record->shipper_cnic == $shipper_cnic &&
                    $existing_record->shipper_address == $shipper_address
                ) {
                    $foundDuplicate = true;
                    break; // Exit the loop as soon as an exact match is found
                }
            }

            if (!$foundDuplicate) {
                // No exact match found, create a new record
                $new_shipper = new RetailShipperNameVerification();
                $new_shipper->phone_number = $phone_number;
                $new_shipper->shipper_name = $shipper_name;
                $new_shipper->shipper_cnic = $shipper_cnic;
                $new_shipper->shipper_address = $shipper_address;
                $new_shipper->retail_shipper_info_id = $id;
                $new_shipper->save();
            }
        } else {
            // No existing record found, create a new one
            $new_shipper = new RetailShipperNameVerification();
            $new_shipper->phone_number = $phone_number;
            $new_shipper->shipper_name = $shipper_name;
            $new_shipper->shipper_cnic = $shipper_cnic;
            $new_shipper->shipper_address = $shipper_address;
            $new_shipper->retail_shipper_info_id = $id;
            $new_shipper->save();
        }

//        if (RetailShipperNameVerification::where('phone_number',$phone_number)->where('shipper_name',$shipper_name)->where('shipper_cnic',$shipper_cnic)->where('shipper_address',$shipper_address))
//        {
//            return 0;
//        }
//        else
//        {
            // $record_exist = RetailShipperNameVerification::where('phone_number',$phone_number)->where('shipper_name',$shipper_name);
            // if($record_exist->exists())
            // {
            //     $record_exist = $record_exist->first();
            //     $record_exist->shipper_cnic = $shipper_cnic;
            //     $record_exist->shipper_address = $shipper_address;
            //     $record_exist->save();
            // }
            // else
            // {
            //     $update_shipper = new RetailShipperNameVerification();
            //     $update_shipper->phone_number = $phone_number;
            //     $update_shipper->shipper_name = $shipper_name;
            //     $update_shipper->shipper_cnic = $shipper_cnic;
            //     $update_shipper->shipper_address = $shipper_address;
            //     $update_shipper->save();
            // }
//        }
    }

    public function previous_names_verify(Request $request)
    {
        $retail_shipper = array();
        $phone_number = $request->phone_number;
        $phone_number_without_hyphen = str_replace('-', '', $phone_number);
        $phone_number_without_hyphen = str_replace('_', '', $phone_number_without_hyphen);
        $length = strlen($phone_number_without_hyphen);

        if ($length == 11)
        {

            $retail_shipper_verification = RetailShipperNameVerification::where('phone_number',$phone_number_without_hyphen)->get();
            $retail_shipper_info = RetailShipperInfo::where('shipper_phone_no',$phone_number)->get();
            $verify_retail_array = $retail_shipper_verification->pluck('phone_number')->toArray();

            //if(in_array($phone_number_without_hyphen,$verify_retail_array) == true){
            if(isset($retail_shipper_verification[0]->phone_number)){
                $retail_shipper = $retail_shipper_verification;
            }else{
                $retail_shipper = $retail_shipper_info;
            }
            return response()->json(['status' => 1, 'success' => 'Shipper info found: ', 'data' => $retail_shipper]);
        }
        else
        {
            return response()->json(['status' => 0, 'error' => 'Not found: ', 'data' => $retail_shipper]);
        }
    }

    public function retail_commission_index()
    {
        return view('retail.retail_commission.index');
    }

    public function retail_commission_list(Request $request)
    {
        $results = null;
        $month = $request->month;
        $user = auth()->user();
        $paid_status = $request->paid_status;

        $retail_user_commission = RetailUserCommission::where('franchise_id', $user->id)->first();
        $franchise_commission = RetailFranchiseCommission::where('franchise_code', $user->store->code)->first(); 

        if ($retail_user_commission && $user->id == $retail_user_commission->franchise_id){
            $query = RetailUserCommission::where('month', $month)
            ->where('franchise_id', $user->id)
            ->where('month', $month);
            if (!empty($franchise)) {
                $query->where('franchise_id', $franchise);
            }
            if (!empty($paid_status) || $paid_status == '0') {
                $query->where('is_paid', $paid_status);
            }
            $retail_trax_center_commission = $query->get();
            $results = $retail_trax_center_commission;
            return response()->json([
                'data' => $results,
            ]);
        }
        else
        {
            $query = RetailFranchiseCommission::where('month', $month)
            ->where('franchise_code', $user->store->code)
            ->where('month', $month);
            if (!empty($franchise)) {
                $query->where('franchise_id', $franchise);
            }
            if (!empty($paid_status) || $paid_status == '0') {
                $query->where('is_paid', $paid_status);
            }
            $retail_franchise_commission = $query->get();
            $results = $retail_franchise_commission;
            return response()->json([
                'data' => $results,
            ]);
        }   
    }

    public function user_commission_invoice_print(Request $request)
    {
        $trax_user = $request->trax_users;
        $data = explode(', ', $trax_user);
        $retail_commissions = RetailUserCommission::whereIn('id', $data)->get();
    
        $html = '';
        $html .= '<!doctype html>';
        $html .= '<html lang="en">';
        $html .= '<head>';
        $html .= '<meta charset="utf-8">';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
        $html .= '<title>Invoice</title>';
        
        $html .= '<style>';
        $html .= file_get_contents(public_path('app-assets/css/bootstrap.min.css'));
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}';
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}';
        $html .= '</style>';
        
        $html .= '</head>';
        $html .= '<body style="padding:98px;">';
    
        $grouped_data = [];
        foreach ($retail_commissions as $record) {
            $grouped_data[$record->trax_center_name][] = $record;
        }
    
        foreach ($grouped_data as $franchise_name => $records) {
            $monthNumber = $records[0]->month;
            $monthNames = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ];
            $monthName = isset($monthNames[$monthNumber]) ? $monthNames[$monthNumber] : '';

            // Start the main container for a franchise
            $html .= '<div class="row align-items-start justify-content-between summary my-4">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<tbody>';

            $html .= '<tr>';
            $html .= '<td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            $html .= '<td class="text-center align-middle color primary"><strong>User Details</strong></td>';
            $html .= '<td class="text-center align-middle color secondary">Created at ' . $records[0]->created_at . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '</tr>';
            
            // Franchise details
            $html .= '<tr><td>Retail User Name:</td><td>' . $franchise_name . '</td></tr>';
            $html .= '<tr><td>Address:</td><td>' . $records[0]->franchise_address . '</td></tr>';
            $html .= '<tr><td>Code:</td><td>' . $records[0]->franchise_code . '</td></tr>';
            $html .= '<tr><td>CNIC:</td><td>' . $records[0]->trax_center_cnic . '</td></tr>';
            $html .= '<tr><td>Phone #:</td><td>' . $records[0]->trax_center_phone . '</td></tr>';
            $html .= '<tr><td><strong>Payment Month:</strong></td><td>' . $monthName . '</td></tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        
            // Start the table for product details
            $html .= '<div class="row align-items-start justify-content-between summary">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th class="color primary">Product</th>';
            $html .= '<th class="color primary">Approved Percentage</th>';
            $html .= '<th class="color primary">Shipments</th>';
            $html .= '<th class="color primary">Total Charges</th>';
            $html .= '<th class="color primary">GST</th>';
            $html .= '<th class="color primary">Weight Charges</th>';
            $html .= '<th class="color primary">Commission</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
    
            // Product records
            $total_shipments = 0;
            $total_charges = 0;
            $total_gst = 0;
            $total_weight_charges = 0;
            $total_commission = 0;
    
            foreach ($records as $record) {
                $data = TotalSumRetailTraxCenter::where('retail_user_id', $record->franchise_id)->first();

                $html .= '<tr>';
                $html .= '<td>' . $record->retail_shipping_mode_name . '</td>';
                $html .= '<td>' . ($record->commission ?? '0') . '%</td>';
                $html .= '<td>' . number_format(round($record->number_of_shipments)) . '</td>';
                $html .= '<td>' . number_format(round($record->total_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->franchise_gst_amount)) . '</td>';
                $html .= '<td>' . number_format(round($record->weight_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->net_commission)) . '</td>';
                $html .= '</tr>';
    
                // Summing up totals
                $total_shipments += $record->number_of_shipments;
                $total_charges += $record->total_charges;
                $total_gst += $record->franchise_gst_amount;
                $total_weight_charges += $record->weight_charges;
                $total_commission += $record->net_commission;
            }
    
            // Totals row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="2"><strong>Total</strong></td>';
            $html .= '<td><strong>' . $total_shipments . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_gst)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_weight_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_commission)) . '</strong></td>';
            $html .= '</tr>';
    
            $gross_commission = $total_commission;
    
            // Gross commission row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Gross Commission</strong></td>';
            $html .= '<td><strong>' . number_format(round($gross_commission)) . '</strong></td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        
            // Third table: Deposits
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-12">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<thead>';
            // $html .= '<tr>';
            // $html .= '<th class="color primary">Deposits</th>';
            // $html .= '<th class="color primary">Amount</th>';
            // $html .= '<th class="color primary">Bank Name</th>';
            // $html .= '<th class="color primary">Cheque #</th>';
            // $html .= '</tr>';
            // $html .= '</thead>';
            // $html .= '<tbody>';
            // $html .= '<tr><td>Security Deposit</td><td>25,000</td><td>Meezan Bank</td><td>C-0123456789</td></tr>';
            // $html .= '<tr><td>License Fees</td><td>25,000</td><td>Meezan Bank</td><td>C-0123456789</td></tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
        
            // // Fourth table: Pending Sales
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;">Pending Sales:</td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // Prepared by and Checked by
            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Prepared By:</strong>';
            // $html .= '<strong>Checked By:</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left:40px;">';
            // $html .= '<strong>Verified By:</strong>';
            // $html .= '<strong>Approved By:</strong>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // $html .= '<div class="row col-6">';
            // $html .= '<div class="col-6"><div class="w-100"><strong><hr></strong></div></div>';
            // $html .= '<div class="col-6" style="padding-left: 40px;"><div style="width: 16.3rem;"><strong><hr></strong></div></div>';
            // $html .= '</div>';
    
            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Retail Team</strong>';
            // $html .= '<strong>Finance Team</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left: 40px;">';
            // $html .= '<strong>Head of Retail</strong>';
            // $html .= '<strong>COO</strong>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // // Empty tables
            // $html .= '<div class="row align-items-start summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
    
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
    
            // Disclaimer after empty tables with page break
            $html .= '<div class="my-2 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>';
            $html .= '<div style="page-break-after: always;"></div>';
        }

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }

    public function franchise_commission_invoice_print(Request $request)
    {
        $franchise_code = $request->franchise;
        $franchise = explode(', ', $franchise_code);
        $franchise_names = RetailFranchiseCommission::whereIn('id', $franchise)->get();

        $html = '';
        $html .= '<!doctype html>';
        $html .= '<html lang="en">';
        $html .= '<head>';
        $html .= '<meta charset="utf-8">';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
        $html .= '<title>Invoice</title>';
        
        $html .= '<style>';
        $html .= file_get_contents(public_path('app-assets/css/bootstrap.min.css'));
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}';
        $html .= '</style>';
        
        $html .= '<style>';
        $html .= '@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}';
        $html .= '</style>';
        
        $html .= '</head>';
        $html .= '<body>';
        
        $html .= '<div>';
        $html .= '<div class="p-1">';
        
        $html .= '<div>';
        $html .= '<div class="p-1">';
        
        $html .= '<div>';
        $html .= '<div class="p-1">';
        $html .= '<div>';
        $html .= '<div class="p-1">';
        $html .= '<div>';
        $html .= '<div class="p-1">';
        $html .= '<div>';
        $html .= '<div class="p-1">';
    
        $grouped_data = [];
        foreach ($franchise_names as $data) {
            $grouped_data[$data->franchise_name][] = $data;
        }

        foreach ($grouped_data as $franchise_name => $records) {
            $franchise_charges = RetailFranchiseCharge::where('franchise_id', $records[0]->franchise_id)->first();
            $monthNumber = $records[0]->month;
            $monthNames = [
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ];
            $monthName = isset($monthNames[$monthNumber]) ? $monthNames[$monthNumber] : '';
            // Start the main container for a franchise
            $html .= '<div class="row align-items-start justify-content-between summary my-4">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<tbody>';

            $html .= '<tr>';
            $html .= '<td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>';
            $html .= '<td class="text-center align-middle color primary"><strong>Franchise Details</strong></td>';
            $html .= '<td class="text-center align-middle color secondary">Created at ' . $records[0]->created_at . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>';
            $html .= '</tr>';
            
            
            // Franchise details
            $html .= '<tr><td>Franchise Name:</td><td>' . $franchise_name . '</td></tr>';
            $html .= '<tr><td>Address:</td><td>' . $records[0]->franchise_address . '</td></tr>';
            $html .= '<tr><td>Code:</td><td>' . $records[0]->franchise_code . '</td></tr>';
            $html .= '<tr><td>CNIC:</td><td>' . $records[0]->franchise_cnic . '</td></tr>';
            $html .= '<tr><td>Phone #</td><td>' . $records[0]->franchise_phone . '</td></tr>';
            $html .= '<tr><td><strong>Payment Month:</strong></td><td>' . $monthName . '</td></tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            // Start the table for product details
            $html .= '<div class="row align-items-start justify-content-between summary">';
            $html .= '<div class="col-12">';
            $html .= '<table class="table table-sm table-bordered border">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th class="color primary">Product</th>';
            $html .= '<th class="color primary">Approved Percentage</th>';
            $html .= '<th class="color primary">Shipments</th>';
            $html .= '<th class="color primary">Total Charges</th>';
            $html .= '<th class="color primary">GST</th>';
            $html .= '<th class="color primary">Weight Charges</th>';
            $html .= '<th class="color primary">Commission</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            // Product records
            $total_shipments = 0;
            $total_charges = 0;
            $total_gst = 0;
            $total_weight_charges = 0;
            $total_commission = 0;

            foreach ($records as $record) {
                $data = TotalSumFranchiseCommission::where('franchise_id', $record->franchise_id)->first();

                $html .= '<tr>';
                $html .= '<td>' . $record->retail_shipping_mode_name . '</td>';
                $html .= '<td>' . $record->product_percentage . '%</td>';
                $html .= '<td>' . $record->number_of_shipments . '</td>';
                $html .= '<td>' . number_format(round($record->total_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->franchise_gst_amount)) . '</td>';
                $html .= '<td>' . number_format(round($record->weight_charges)) . '</td>';
                $html .= '<td>' . number_format(round($record->commission)) . '</td>';
                $html .= '</tr>';

                $total_shipments += $record->number_of_shipments;
                $total_charges += $record->total_charges;
                $total_gst += $record->franchise_gst_amount;
                $total_weight_charges += $record->weight_charges;
                $total_commission += $record->commission;
            }

            // Totals row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="2"><strong>Total</strong></td>';
            $html .= '<td><strong>' . $total_shipments . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_gst)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_weight_charges)) . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($total_commission)) . '</strong></td>';
            $html .= '</tr>';

            $withholding_amount = ($record->franchise_withholding_percentage / 100) * $total_commission;
            $deduction_amount = ($record->deduction_percentage / 100) * $total_commission;
            $gross_commission = $total_commission - ($withholding_amount + $deduction_amount);

            // Withholding tax row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Withholding Income Tax ' . $data->withholding_tax_percent . '%</strong></td>';
            $html .= '<td><strong>' . number_format(round($withholding_amount)) . '</strong></td>';
            $html .= '</tr>';

            // Deduction GST tax row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Deduction ' . $data->commission_gst_deduction_percent . '</strong></td>';
            $html .= '<td><strong>' . number_format(round($deduction_amount)) . '</strong></td>';
            $html .= '</tr>';

            // Gross commission row
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="6"><strong>Gross Commission</strong></td>';
            $html .= '<td><strong>' . number_format(round($gross_commission)) . '</strong></td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';

            // Third table: Deposits
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-12">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<thead>';
            // $html .= '<tr>';
            // $html .= '<th class="color primary">Deposits</th>';
            // $html .= '<th class="color primary">Amount</th>';
            // $html .= '<th class="color primary">Bank Name</th>';
            // $html .= '<th class="color primary">Cheque #</th>';
            // $html .= '</tr>';
            // $html .= '</thead>';
            // $html .= '<tbody>';
            // $html .= '<tr><td>Security Deposit</td><td>' . $franchise_charges->security_deposit . '</td><td>' . $franchise_charges->bank_name . '</td><td>' . $franchise_charges->security_cheque_number . '</td></tr>';
            // $html .= '<tr><td>License Fees</td><td>' . $franchise_charges->license_fees . '</td><td>' . $franchise_charges->bank_name . '</td><td>' . $franchise_charges->license_cheque_number . '</td></tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';

            // Fourth table: Pending Sales
            // $html .= '<div class="row align-items-start justify-content-between summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;">Pending Sales:</td>';
            // $html .= '<td class="w-100" style="text-align: center;padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';

            // // Prepared by and Checked by
            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Prepared By:</strong>';
            // $html .= '<strong>Checked By:</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left:40px;">';
            // $html .= '<strong>Verified By:</strong>';
            // $html .= '<strong>Approved By:</strong>';
            // $html .= '</div>';
            // $html .= '</div>';

            // $html .= '<div class="row col-6">';
            // $html .= '<div class="col-6"><div class="w-100"><strong><hr></strong></div></div>';
            // $html .= '<div class="col-6" style="padding-left: 40px;"><div style="width: 16.3rem;"><strong><hr></strong></div></div>';
            // $html .= '</div>';

            // $html .= '<div class="row align-items-start justify-content-between summary col-6">';
            // $html .= '<div class="col-6 d-flex justify-content-between">';
            // $html .= '<strong>Retail Team</strong>';
            // $html .= '<strong>Finance Team</strong>';
            // $html .= '</div>';
            // $html .= '<div class="col-6 d-flex justify-content-between" style="padding-left: 40px;">';
            // $html .= '<strong>Head of Retail</strong>';
            // $html .= '<strong>COO</strong>';
            // $html .= '</div>';
            // $html .= '</div>';

            // // Empty tables
            // $html .= '<div class="row align-items-start summary">';
            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center; padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';

            // $html .= '<div class="col-3">';
            // $html .= '<table class="table table-sm table-bordered border" style="margin: 0px 0px 0px 12px;">';
            // $html .= '<tbody>';
            // $html .= '<tr>';
            // $html .= '<td class="w-50" style="height: 3rem; padding-top: 1rem;"></td>';
            // $html .= '<td class="w-100" style="text-align: center; padding: 1rem 0rem 0rem 0rem;"></td>';
            // $html .= '</tr>';
            // $html .= '</tbody>';
            // $html .= '</table>';
            // $html .= '</div>';
            // $html .= '</div>';
            
            // Disclaimer after empty tables with page break
            // $html .= '<div class="my-2 text-center font-italic"><strong>Disclaimer:</strong> * Cheque Will be made in favor of Mohammad Awais Rana</div>';
            $html .= '<div style="page-break-after: always;"></div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</body>';
        $html .= '</html>';
        return $html;
    }
    
    public function get_products(Request $request) {

        $products = Product::where('parent_product_id', $request->parent_product_id)->get();
        if ($products) {
            return response()->json(['status' => 0, 'products' => $products]);
        } else {
            return response()->json(['status' => 1]);
        }
    }
}
