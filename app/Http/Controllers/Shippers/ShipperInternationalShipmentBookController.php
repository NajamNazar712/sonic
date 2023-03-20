<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyBlacklisted;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\ChargesModes;
use App\Http\Models\City;
use App\Http\Models\InternationalRatesHub;
use App\Http\Models\InternationalShipment;
use App\Http\Models\InternationalUsersCreditLimit;
use App\Http\Models\PaymentMode;
use App\Http\Models\Product;
use App\Http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentShipperReference;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\SubstituteUserShipment;
use App\Jobs\ProcessShipmentBookingDB;
use App\Jobs\ProcessShipmentBookingDBPriority;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Session;
use Validator;
use App\Http\Models\Admin\GlobalSettings;

class ShipperInternationalShipmentBookController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users')->except(['print_air_waybill', 'corporate_invoice']);

        $this->middleware('auth:admin,web,substitute_users')->only(['print_air_waybill', 'corporate_invoice']);

        $this->middleware('Permission');
    }


    public function index() {
        $date = Carbon::today();
        $user = User::with('shipping.city')->find(session('user_id'));
        $multi_piece = $user->multipiece_status;
        $pickup_cities = City::where('pickup', 1)->where('status', 1)->where('business_category_id', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $cities = City::where('status', 1)->where('hub', 1)->where('business_category_id', 2)->whereNotNull('zone_id')->groupBy('id')->orderBy('name')->select('name','id')->get();
        $products = Product::orderBy('product_name')->get();
        $payment_modes = PaymentMode::whereNotIn('id', [2, 3, 5])->get();
        $check = NonServiceArea::pluck('name')->toArray();
        if(session('account_type') == 1){
            $charges_modes = ChargesModes::where('id' , 4)->get();
        }
        else{
            $charges_modes = ChargesModes::where('id' , 3)->get();
        }
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if($air_waybill->exists()){
            $air_waybill = $air_waybill->first();
        }
        else{
            $air_waybill = null;
        }
//        $countries = City::where('hub', 1)->where('status', 1)->where('business_category_id', 2)->select(['id', 'name'])->get();
//        $cities = City::where('business_category_id', 2)->where('permanent_disabled',0)->where('status', 1)->whereNotNull('c.zone_id')->orderBy('c.name')->select('name','id')->get();

//        $countries = InternationalRatesHub::join('cities as c', 'international_rates_hubs.hub_id', '=', 'c.hub_id')->groupBy('c.id')->where('international_rates_hubs.user_id', session('user_id'))->where('c.status', 1)->where('c.hub', 1)->where('c.business_category_id', 2)->whereNotNull('c.zone_id')->orderBy('c.name')->select('c.id', 'c.name', 'c.hub_id')->get();
        $credit_msg = '';
        $allow_booking = TRUE;
        $credit_limit = NULL;
        if($credit_data = $this->user_credit_limit(session('user_id'))){
            $credit_percentage = round($credit_data['percentage'], 2);
            $credit_limit = $credit_data['limit'];
            if($credit_percentage >= 90){
                $allow_booking = FALSE;
                $credit_msg = "Dear Customer, you have utilized $credit_percentage% (or the corresponding percentage) of your credit limit. Kindly clear your dues to avoid interruption in the services.";
            }
            else if($credit_percentage >= 70){
                $credit_msg = "Dear Customer, you have utilized $credit_percentage% (or the corresponding percentage) of your credit limit. Kindly clear your dues to avoid interruption in the services.";
            }
        }
        return view('client.shipment.book.international.index')->with(['user' => $user, 'multi_piece' => $multi_piece, 'cities' => $cities, 'products' => $products, 'payment_modes' => $payment_modes, 'check' => $check, 'charges_modes' => $charges_modes, 'date'=> $date, 'air_waybill' => $air_waybill, 'allow_booking' => $allow_booking, 'credit_msg' => $credit_msg, 'credit_limit' => $credit_limit, 'pickup_cities' => $pickup_cities]);
    }

    public function store(Request $request) {
        $user_id = session('user_id');
        $service_type_id = 1;

        if ($request->input('pickup_address') == 0) {
            $pickup_city_id = $request->input('new_pickup_city');
            if($request->input('make_default_address') == 1){
                $default = 1;
            }
            else{
                $default = 0;
            }
            UserShippingInfo::where('user_id', $user_id)->update(['default_address' => 0]);

            $pickup_address_id = ShipperShipmentBookController::add_pickup_address($user_id, $request->input('new_pickup_address'), $request->input('new_pickup_person_of_contact'), $request->input('new_pickup_vendor'), $request->input('new_pickup_phone_number'), $request->input('new_pickup_email_address'), $pickup_city_id, $default);
        }
        else {
            $pickup_address_id = $request->input('pickup_address');

            $user_shipping_info = UserShippingInfo::find($pickup_address_id);

            $pickup_city_id = $user_shipping_info->city_id;
        }
        if ($request->filled('information_display')) {
            $information_display = TRUE;
        } else {
            $information_display = FALSE;
        }

        if ($request->filled('self_collection')) {
            $self_collection = TRUE;
        } else {
            $self_collection = FALSE;
        }
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

        if ($request->filled('special_instructions')) {
            $special_instructions = $request->input('special_instructions');
        }
        else {
            $special_instructions = NULL;
        }

        $estimated_weight = $request->input('estimated_weight');
        $shipping_mode_id = 2;
        $charges_mode_id = $request->charges_mode;
        $same_day_timing_id = NULL;
        $amount = str_replace(',', '', $request->input('amount'));
        $payment_mode_id = $request->input('payment_mode');
        $try_and_buy_charges = NULL;
        $pieces_quantity = $request->pieces_quantity;
        $business_category_id = 2;
		$open_shipment = 0;

        if ($payment_mode_id == 4) {
            $amount = 0;
        }

        $shipment_id = ShipperShipmentBookController::book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id , $try_and_buy_charges, $pieces_quantity, $self_collection, $business_category_id, $open_shipment, NULL);
        if(session('user_type') == 2){
            $substitute_user_shipment = new SubstituteUserShipment();
            $substitute_user_shipment->substitute_user_id = Auth::id();
            $substitute_user_shipment->shipment_id = $shipment_id;
            $substitute_user_shipment->save();
        }

        $international_shipment = new InternationalShipment();
        $international_shipment->shipment_id = $shipment_id;
        $international_shipment->postal_code = $request->input('postal_code');
        $international_shipment->save();

        ShipperShipmentBookController::add_consignee_info($user_id, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address);
        if(Session::has('prefix')){
            $tracking_number = ShipperShipmentBookController::generate_prefix_tracking_number($shipment_id, $request->order_id);
        }
        else{
            $tracking_number = ShipperShipmentBookController::generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);
        }
        if($request->has('order_date_formatted')){
            if($request->order_date_formatted != null){
                $order_date = new ShipmentOrderDate();
                $order_date->shipment_id = $shipment_id;
                $order_date->order_date = $request->order_date_formatted;
                $order_date->save();
            }
        }
        if($request->shipper_reference_1 != null || $request->shipper_reference_2 != null || $request->shipper_reference_3 != null || $request->shipper_reference_4 != null || $request->shipper_reference_5 != null){
            $shipper_reference = new ShipmentShipperReference();
            $shipper_reference->shipment_id = $shipment_id;
            if($request->shipper_reference_1 != null) {
                $shipper_reference->reference_1 = $request->shipper_reference_1;
            }
            if($request->shipper_reference_2 != null) {
                $shipper_reference->reference_2 = $request->shipper_reference_2;
            }
            if($request->shipper_reference_3 != null) {
                $shipper_reference->reference_3 = $request->shipper_reference_3;
            }
            if($request->shipper_reference_4 != null) {
                $shipper_reference->reference_4 = $request->shipper_reference_4;
            }
            if($request->shipper_reference_5 != null) {
                $shipper_reference->reference_5 = $request->shipper_reference_5;
            }
            $shipper_reference->save();
        }
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

        ShipperShipmentBookController::add_item($shipment_id, $product_type_id, $item_description, $item_quantity, $price, $insurance, $type);
        if($service_type_id == 1 && $pieces_quantity > 1){
            ShipperShipmentBookController::create_shipment_pieces($shipment_id, $pieces_quantity);
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
            $settingsfortime = GlobalSettings::where('type', 'pickup_request_cut_off_time')->first();
            $now = Carbon::now()->format('H:i:s');
            $cutofftime = $settingsfortime->setting_value.":00:00";
            if($now>$cutofftime)
            {
                NotificationsController::send(152, $shipment_id);
                NotificationsController::send(153, $shipment_id);
            }
        }
        return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
    }
    public function excel_index() {
        $user = User::find(session('user_id'));
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();
        $cities = City::where('status', 1)->where('hub', 0)->where('business_category_id', 2)->whereNotNull('zone_id')->groupBy('id')->orderBy('name')->pluck('name');
        $products = Product::all();

        $payment_modes = PaymentMode::whereNotIn('id', [2, 3, 5])->get();
        if(session('account_type') == 1){
            $charges_modes = ChargesModes::where('id' , 4)->get();
        }
        else{
            $charges_modes = ChargesModes::where('id' , 3)->get();
        }
        $credit_msg = '';
        $allow_booking = TRUE;
        $credit_limit = NULL;
        if($credit_data = $this->user_credit_limit(session('user_id'))){
            $credit_percentage = round($credit_data['percentage'], 2);
            $credit_limit = $credit_data['limit'];
            if($credit_percentage >= 90){
                $allow_booking = FALSE;
                $credit_msg = "Dear Customer, you have utilized $credit_percentage% (or the corresponding percentage) of your credit limit. Kindly clear your dues to avoid interruption in the services.";
            }
            else if($credit_percentage >= 70){
                $credit_msg = "Dear Customer, you have utilized $credit_percentage% (or the corresponding percentage) of your credit limit. Kindly clear your dues to avoid interruption in the services.";
            }
        }
        return view('client.shipment.book.international.excel')->with(['user' => $user, 'pickup_addresses' => $pickup_addresses, 'cities' => $cities, 'products' => $products, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes, 'allow_booking' => $allow_booking, 'credit_msg' => $credit_msg, 'credit_limit' => $credit_limit]);
    }
    public function excel_store(Request $request) {
        $user_id = session('user_id');
        $account_type_id = session('account_type');
        $names = [
            'pickup_address_id' => 'Pickup Address ID',
            'information_display' => 'Information Display',
            'consignee_city_name' => 'Consignee City Name',
            'postal_code' => 'Postal Code',
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

            'special_instructions' => 'Special Instructions',
            'estimated_weight' => 'Estimated Weight',
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

            'phone_number.regex' => ':attribute format is Invalid.',

            'consignee_phone_number_1.regex' => ':attribute format is Invalid',
            'consignee_phone_number_2.regex' => ':attribute format is Invalid'
        ];

        $rules = [
            'pickup_address_id' => ['required', 'integer', 'digits_between:1,10', Rule::exists('user_shipping_infos', 'id')->where(function($query) use($user_id) {
                $query->where('user_id', $user_id);
            })->where('hidden', 0)],
            'information_display' => ['required', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'consignee_city_name' => ['required', 'string', 'between:1,100', Rule::exists('cities', 'name')->where('business_category_id', 2)->where('hub',0)->where('status', 1)],
            'postal_code' => ['required', 'between:1,10'],
            'consignee_name' => ['required', 'between:1,100'],
            'consignee_address' => ['required', 'between:1,255'],
            'consignee_phone_number_1' => ['required', 'regex:/^[+|0][0-9]/'],
            'consignee_phone_number_2' => ['nullable', 'regex:/^[+|0][0-9]/'],
            'consignee_email_address' => ['nullable', 'email', 'between:0,100'],
            'self_collection' => ['nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'order_date' => ['nullable', 'date_format:Y-m-d'],

            'item_product_type_id' => ['required', 'nullable', 'integer', 'digits_between:1,10', 'exists:products,id'],
            'item_description' => ['required', 'nullable', 'between:0,1000'],
            'item_quantity' => ['required', 'nullable', 'integer', 'digits_between:1,10', 'between:1,10000'],
            'item_insurance' => ['required', 'nullable', 'string', 'in:NO,No,nO,no,YES,YEs,YeS,Yes,yES,yEs,yeS,yes'],
            'item_price' => ['required_if:item_insurance,YES,YEs,YeS,Yes,yES,yEs,yeS,yes', 'nullable', 'integer', 'digits_between:1,20', 'between:1,100000'],
            'special_instructions' => ['nullable', 'between:0,190'],
            'estimated_weight' => ['required', 'numeric', 'between:0.1,10000'],
            'amount' => ['required_if:service_type_id,1,2', 'nullable', 'integer', 'digits_between:1,20', 'min:0'],
            'payment_mode_id' => ['required', 'nullable', 'integer', 'digits_between:1,10', Rule::exists('payment_modes', 'id')->where(function($query) {
                $query->whereNotIn('id', [2, 3]);
            })],
            'pieces_quantity' => ['nullable', 'integer', 'digits_between:1,10', 'between:1,10'],

            'shipper_reference_number_1' => ['nullable', 'between:0,190'],
            'shipper_reference_number_2' => ['nullable', 'between:0,190'],
            'shipper_reference_number_3' => ['nullable', 'between:0,190'],
            'shipper_reference_number_4' => ['nullable', 'between:0,190'],
            'shipper_reference_number_5' => ['nullable', 'between:0,190']

        ];
        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();
        }

        if (isset($spreadsheet)) {
            if (count($spreadsheet[0]) == 27){
                $fields = [0 => 'pickup_address_id', 1 => 'information_display', 2 => 'consignee_city_name', 3 => 'postal_code', 4 => 'consignee_name', 5 => 'consignee_address', 6 => 'consignee_phone_number_1', 7 => 'consignee_phone_number_2', 8 => 'consignee_email_address', 9 => 'order_id', 10 => 'order_date', 11 => 'item_product_type_id', 12 => 'item_description', 13 => 'item_quantity', 14 => 'item_insurance', 15 => 'item_price', 16 => 'special_instructions', 17 => 'estimated_weight', 18 => 'amount', 19 => 'payment_mode_id', 20 => 'charges_mode_id', 21 => 'pieces_quantity', 22 => 'shipper_reference_number_1', 23 => 'shipper_reference_number_2', 24 => 'shipper_reference_number_3', 25 => 'shipper_reference_number_4', 26 => 'shipper_reference_number_5'];
            }
            else{
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
                    $rules['order_id'] = ['nullable', 'between:0,100', Rule::unique('shipments', 'order_id')->where(function($query) use($user_id) {
                        $query->where('user_id', $user_id);
                    })];
                }
                else{
                    $rules['order_id'] = ['nullable', 'filled', 'between:0,100'];
                }
            }

            if($account_type_id == 1){
                $rules['charges_mode_id'] = ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function($query) {
                    $query->where('id', 4);
                })];
            }
            else{
                $rules['charges_mode_id'] = ['nullable', 'integer', 'digits_between:1,10', Rule::exists('charges_modes', 'id')->where(function($query) {
                    $query->where('id', 3);
                })];
            }

            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                if (!isset($row['charges_mode_id'])) {
                    if($account_type_id == 1){
                        $rows[$key]['charges_mode_id'] = 4;
                    }
                    else{
                        $rows[$key]['charges_mode_id'] = 3;
                    }
                }
                $rows[$key]['service_type_id'] = 1;
                $row['service_type_id'] = 1;
                $rows[$key]['shipping_mode_id'] = 2;
                $row['shipping_mode_id'] = 2;
                $rows[$key]['same_day_timing_id'] = null;
                $row['same_day_timing_id'] = null;


                if(!isset($row['pieces_quantity']) || $row['pieces_quantity'] == null){
                    $row['pieces_quantity'] = 1;
                }

                $rows[$key]['pieces_quantity'] = $row['pieces_quantity'];

                if(!isset($row['self_collection']) || $row['self_collection'] == null){
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
                            if (in_array($row['order_id'], $order_ids)) {
                                $errors[$row_id]['order_id'] = 'Same Order ID as of Row #' . $order_id_row[$row['order_id']];
                            }
                            else {
                                $order_ids[] = $row['order_id'];
                                $order_id_row[$row['order_id']] = $row_id;
                            }
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
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }

                    if (!$consignee_city->zone_id) {
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }
                    if (!$consignee_city->zone_id) {
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is deactivated';
                    }

                    $international_rate_hub = City::where('id', $consignee_city->id);
                    if(!$international_rate_hub->exists()){
                        $errors[$row_id]['consignee_city_name'] = 'Consignee City: ' . $consignee_city->name . ' is unavailable';
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
            }

            if (empty($errors)) {
                if (empty($nsa_error)) {
                    if(empty($blacklist_errors)){
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

                            if ($row['payment_mode_id'] == 4) {
                                $row['amount'] = 0;
                            }

                            $row['business_category_id'] = 2;
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
                        return view('client.shipment.book.international.blacklist')->with(['data' => $rows, 'blacklist_errors' => $blacklist_errors, 'blacklist_found_categories' => $blacklist_found_categories]);
                    }
                }
                else {
                    return view('client.shipment.book.international.nsa')->with(['data' => $rows, 'nsa_error' => $nsa_error]);
                }
            }
            else {
                $cities = City::where('status', 1)->where('hub', 0)->where('business_category_id', 2)->whereNotNull('zone_id')->groupBy('id')->orderBy('name')->get();
                $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
                    $query->where('pickup', 1)->where('business_category_id', 1)->where('status', 1)->whereNotNull('zone_id');
                })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->pluck('id');
                $products = Product::pluck('product_name', 'id');

                $payment_modes = PaymentMode::whereNotIn('id', [2, 3])->pluck('mode', 'id');
                if(session('account_type') == 1){
                    $charges_modes = ChargesModes::where('id' , 4)->pluck('charges_mode','id');
                }
                else{
                    $charges_modes = ChargesModes::where('id' , 3)->pluck('charges_mode','id');
                }
                $city_name = array();
                foreach ($cities as $city) {
                    $city_name[$city->name] = $city->name;
                }
                return view('client.shipment.book.international.errors')->with(['data' => $rows, 'errors' => $errors, 'cities' => $city_name, 'pickup_addresses' => $pickup_addresses, 'products' => $products, 'payment_modes' => $payment_modes, 'charges_modes' => $charges_modes]);
            }
        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function user_credit_limit($user_id){
        $credit_user = InternationalUsersCreditLimit::where('user_id', $user_id);
        if($credit_user->exists()){
            $credit_user = $credit_user->first();
            $limit_used = $credit_user->limit_usage;
            $data = array();
            $limit = $credit_user->limit;
            $data['limit'] = $limit;
            $data['limit_used'] = $limit_used;
            if($limit_used != NULL && $limit != 0){
                $percentage = ($limit_used / $limit) * 100;
                $data['percentage'] = $percentage;
            }
            else{
                $data['percentage'] = 0;
            }
            return $data;
        }
        return FALSE;
    }
}
