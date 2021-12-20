<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightCharge;
use App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub;
use App\Http\Models\Admin\WalkinShipmentWeightCharges;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Models\ChargesModes;
use App\Http\Models\DeliveryType;
use App\Http\Models\InternationalRatesHub;
use App\Http\Models\InternationalShipment;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\WalkInCities;
use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\WalkinFtlInvoice;
use App\Http\Models\BookingType;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\City;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Models\PaymentMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Models\PackagingMaterialTypes;
use App\Http\Models\WalkInShipmentPackagingMaterialHistory;
use Auth;
use App\Http\Models\PackagingMaterialTypeSizes;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\Http\Models\WarehouseStock;
use App\Http\Models\Warehouse\Warehouse;
use Validator;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\FTLController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\FtlRequestAdditionalCost;
use App\Http\Models\Admin\Retail\RetailFranchise;
use App\Http\Models\Admin\Retail\RetailTraxCenter;
use DNS2D;

class AdminWalkInBookShipmentController extends Controller
{
//    private function unique_order_id($user_id, $order_id) {
//        return !(Shipment::where('user_id', $user_id)->where('order_id', $order_id)->exists());
//    }

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

    static public function book($user_id, $service_type_id, $pickup_address_id, $pickup_city_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $fuel_surcharge, $actual_weight, $gst, $weight_charges, $r_amount, $delivery_type, $charges_mode_id, $packaging_charges, $pickup, $business_category_id, $open_shipment) {
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
        $shipment->packaging_charges = $packaging_charges;
        $shipment->business_category_id = $business_category_id;
        
        $self_collection = FALSE;

        if($pickup == 1){
            $shipment->shipper_status_id = 1;
            $shipment->consignee_status_id = 1;
        }else{
            if ($delivery_type == 2 && City::find($pickup_city_id)->hub_id == City::find($consignee_city_id)->hub_id) {
                $shipment->shipper_status_id = 15;
                $shipment->consignee_status_id = 15;

                $self_collection = TRUE;
            }
            else {
                $shipment->shipper_status_id = 2;
                $shipment->consignee_status_id = 2;
            }
        }


        $shipment->save();
        
        $shipment_id = $shipment->id;

        $shipment_info = new ShipmentDetail();
        $shipment_info->shipment_id = $shipment_id;
        $shipment_info->is_open = $open_shipment;
        $shipment_info->save();

        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, NULL, Auth::id());
        if($pickup == 0){
            ShipmentsJourneyController::add($shipment_id, 2, 2, NULL, NULL, NULL, Auth::id());
        }else{
            AdminPickupsController::generate($shipment_id);
        }

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
        $charges_modes = ChargesModes::whereIn('id', [1, 2])->get();
        $packaging_type = PackagingMaterialTypes::with('sizes')->where('status', 1)->get();
        $packaging_sizes = PackagingMaterialTypeSizes::all();
        return view('admin.shipment.book.walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode , 'user_shipping_infos' => $user_shipping_infos, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes,'consignee_cities' => $consignee_cities, 'packaging_types' => $packaging_type, 'packaging_sizes' => $packaging_sizes]);
    }

    static public function generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id) {
        $shipment = Shipment::find($shipment_id);

        $tracking_number = $pickup_city_id . $consignee_city_id . str_pad($shipment_id, 6, '0', STR_PAD_LEFT);

        $shipment->tracking_number = $tracking_number;

        $shipment->save();

        return $tracking_number;
    }

    public function walk_in_store(Request $request) {
        if($request->open_shipment=='on'){
            $open_shipment=1;
        }else{
            $open_shipment=0;

        }
        $test = 0;
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();

        $user_id = $check_id['setting_value'];

        if (BookingType::where('id', '!=', 3)->where('id', $request->input('selected_service_type'))->exists()) {

            if (!empty($request->input('shipping_mode'))) {

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
                    
                    $packaging_charges = $request->packaging_charges;
                    

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

                    $delivery_type = $request->delivery_type;
                    $charges_mode_id = $request->charges_mode;
                    $pickup = 0;
                    if($request->filled('pickup')){
                        $pickup = 1;
                        $actual_weight = 0;
                        $charges_per_kg = 0;
                        $weight_charges = 0;
                        $fuel_surcharge = 0;
                        $gst = 0;
                        $amount = 0;
                        $r_amount = 0;

                    }else{
                        $actual_weight = $request->actual_weight;
                        $charges_per_kg = $request->charges_per_kg;
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

                    }
                    $business_category_id = 1;
                    $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $pickup_city_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $fuel_surcharge, $actual_weight, $gst, $weight_charges, $r_amount, $delivery_type, $charges_mode_id, $packaging_charges, $pickup, $business_category_id, $open_shipment);

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

                    if($request->filled('pickup')){
                        $walkin_weight = new WalkinShipmentWeightCharges();
                        $walkin_weight->shipment_id = $shipment_id;
                        $walkin_weight->charges_per_kg = $request->charges_per_kg;
                        $walkin_weight->save();
                    }

                     if($request->has('pack_type')){

                        $pickup_address_details = UserShippingInfo::find($pickup_address_id);
                        $hub_id = $pickup_address_details->city->hub_id;

                        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id',$hub_id);

                        $fulfilment_hub = $fulfilment_hub->first();

                        $warehouse_id = $fulfilment_hub->warehouse_id;

                        $packaging_types = PackagingMaterialTypes::all();
                        $packaging_sizes = PackagingMaterialTypeSizes::all();
                        foreach ($packaging_types as $ptype) {
                            foreach ($packaging_sizes as $psize) {
                                $index = (int)($ptype->id . $psize->id);

                                if (array_key_exists($index, $request->pack_type)) {
                                    $test += 1;
                                    $packaging_history = new WalkInShipmentPackagingMaterialHistory();
                                    $packaging_history->shipment_id = $shipment_id;
                                    $packaging_history->type_id = $request->pack_type[$index];
                                    $packaging_history->size_id = $request->pack_size[$index];
                                    $packaging_history->quantity = $request->pack_quantity[$index];
                                    $packaging_history->save();

                                    $type_id = $request->pack_type[$index];
                                    $type_size_id = $request->pack_size[$index];
                                    $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id])->first();

                                    $stock->stock = $stock['stock'] - $request->pack_quantity[$index];
                                    $stock->save();

                                }
                            }
                        }
                    }
                    NotificationsController::send(2, $shipment_id);

                    $shipment_ids = array($shipment_id);
                    if($pickup == 0){
                         NotificationsController::send(85, $shipment_ids , Auth::id());
                     }

                    if ($request->filled('book_and_print')) {
                        $print = $shipment_id;
                    }
                    else {
                        $print = FALSE;
                    }
                    return redirect()->back()->with(['success' => $test .' Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
            }
            else {
                return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
            }
        }
        else {
            return redirect()->back()->with('error', 'Invalid Service Type Selected');
        }
    }

    public function check_packing_quantity(Request $request){
        $type_id = $request->type_id;
        $size_id = $request->size_id;
        $hub_id = $request->hub_id;
        $quantity = $request->quantity;
        if($type_id == null){
            return response()->json(['status' => 1, 'error' => 'Please select Packaging Material Type']);
        }

        if($size_id == null){
            return response()->json(['status' => 1, 'error' => 'Please select Packaging Material Size']);
        }

        if($hub_id == null){
            return response()->json(['status' => 1, 'error' => 'Please select Warehouse']);
        }

        if($quantity == null){
            return response()->json(['status' => 1, 'error' => 'Please select Packaging Material Quantity']);
        }

        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id',$hub_id);
        if($fulfilment_hub->exists()){
            $fulfilment_hub = $fulfilment_hub->first();
            $warehouse_id = $fulfilment_hub->warehouse_id;
        }else{
            return response()->json(['status' => 1, 'error' => 'Origin is not linked with any Warehouse!']);
        }

        

        $warehouse = Warehouse::find($warehouse_id);
        if($warehouse){
            if($warehouse->status == 1){
               $warehouse_stock = WarehouseStock::where('warehouse_id', $warehouse_id)->where('type_id', $type_id)->where('type_size_id', $size_id);
               if($warehouse_stock->exists()){
                   $warehouse_stock = $warehouse_stock->first();
                   if($warehouse_stock->stock > $quantity){
                       return response()->json(['status' => 0]);
                   }else{
                       return response()->json(['status' => 1, 'error' => 'Warehouse does not have selected quantity!']);
                   }

               }else{
                   return response()->json(['status' => 1, 'error' => 'Warehouse does not have this packaging material!']);
               }

            }
            else{
                return response()->json(['status' => 1, 'error' => 'Warehouse is disabled, please select another warehouse!']);
            }
        }
        else{
            return response()->json(['status' => 1, 'error' => 'Warehouse is not found!']);
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
                    if($request->pickup == 'false'){
                        if ($request->actual_weight < $check['actual_weight']) {
                            return response()->json(['status' => 1, 'error' => 'Actual Weight must be greater than or equal to ' . $check['actual_weight']]);
                        }
                    }

                    if ($request->charges_per_kg < $check_zone) {
                        return response()->json(['status' => 0, 'error' => 'Charges per kg must be greater than or equal to ' . $check_zone]);
                    }

                    return response()->json(['status' => 2, 'error' => '']);

                } else {
                    return response()->json(['status' => 0, 'error' => "Zone class does'nt exists"]);
                }
            }
            else{
                if($request->pickup == 'false'){
                    if ($request->actual_weight < $check['actual_weight']) {
                        return response()->json(['status' => 1, 'error' => 'Actual Weight must be greater than or equal to ' . $check['actual_weight']]);
                    }
                }
                if ($request->charges_per_kg < $check['chargeable_weight_local']) {
                    return response()->json(['status' => 0, 'error' => 'Charges per kg must be greater than or equal to ' .  $check['chargeable_weight_local']]);
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

//    public function order_id(Request $request) {
//        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();
//
//        $user_id = $check_id['setting_value'];
//
//        if ($request->filled('order_id')) {
//            return $request->input('order_id');
//        }
//        else {
//            return 'false';
//        }
//    }

    public function print_air_waybill(Request $request) {
        $user_type = NULL;
        $user_id = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();

            $user_name = Auth::user()->name . ' (Admin) #' . $user_id;
        }
        else {
            $user_name = 'Unknown';
        }

        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        if ($user_type) {
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
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                            <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
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
                                <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name .'</strong></td>
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
                              </tr>';
                                    if($shipment->shipment_detail()->exists()){
                                        if($shipment->shipment_detail->is_open==1){

                    $table_end .= '<tr>
                    <td colspan="2" class="color primary border twice-top twice-bottom twice-left"><strong>Open Box</strong></td>
                    <td colspan="4" class="border twice-top twice-bottom twice-left"><strong> Yes <span><img src="'.asset('img/open_box_icon.png').'" ></span></strong></td>
                    
                    </tr>';
                }
            }

                $table_end .= '<tr>
                                <td colspan="8" class="text-center border twice-top"><em>Kindly do not give any addtional charges to the Rider/Courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                              </tr>
                              <tr>
                                <td colspan="8" class="text-center border twice-top"><em><strong>Disclaimer: </strong>Shipment Lost/Damaged *will be treated for 100 rs /KG according to our *Claim Policy</em></td>
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
            
            if($shipment->actual_weight > 0){
            $item = $shipment->items->first();
            $invoice = '<div class="invoice p-1">
                    <table class="table table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-left align-middle">
                            <img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mb-1">
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
                                    <td class="color primary" colspan="2"><strong>Sender Details</strong></td>
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

                        <div class="col-6">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>Tracking No.</strong></td>
                                    <td>'. $shipment->tracking_number .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row align-items-start justify-content-between summary">
                    <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Receiver Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>'. $shipment->consignee_name .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>'. $shipment->consignee_address .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>'. $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr class="color primary">
                                    <td colspan="4"><strong>Shipment Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Shipping Mode</strong></td>
                                    <td>'. $shipment->shipping_mode->mode .'</td>
                                    <td class="color secondary"><strong>Order ID</strong></td>
                                    <td>'. $shipment->order_id .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Origin</strong></td>
                                    <td>'. $shipment->pickup_address->city->name .'</td>
                                    <td class="color secondary"><strong>Destination</strong></td>
                                    <td>'. $shipment->consignee_city->name .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Booking Date</strong></td>
                                    <td>'. $shipment->created_at->format('Y-m-d H:i:s') .'</td>
                                    <td class="color secondary"><strong>Weight</strong></td>
                                    <td>'. $shipment->actual_weight .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <table class="table table-sm table-bordered border invoice">
                                  <tbody>
                                    <tr>
                                      <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                                      <td class="color secondary border twice-top"><strong>Type</strong></td>
                                      <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                                      <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                      <td class="border twice-top">' . $item->quantity . '</td>
                                    </tr>
                                    <tr>
                                      <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                      <td colspan="6" class="border twice-bottom">' . $item->description .'</td>
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
                $total_packaging_charges = $shipment->packaging_charges;
                $total_charges = 0;
                $total_charges = $total_weight_charges + $total_fuel_surcharge + $total_packaging_charges;
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
                        <tr>
                          <td class="text-left">Packaging Charges</td>
                          <td class="text-right">' . number_format($total_packaging_charges) . '</td>
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
                    </div>
                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';
                $invoice .= $invoice_details;
                $html .= $invoice;
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
    }
    public function check_min_charges(Request $request){
        if($request->pickup_city != null && $request->consignee_city != null) {
            $min_charges = WalkInStandardWeightCharge::where(['shipping_mode_id' => $request->shipping_mode, 'delivery_type_id' => $request->delivery_type])->first();
            if ($request->pickup_city == $request->consignee_city) {
                $min_charges = $min_charges['chargeable_weight_local'];
            } else {
                $city = City::where('id', $request->consignee_city)->first();
                    $zone_class = ZoneClassCity::where(['zone_id' => $city['zone_id'], 'city_id' => $request->consignee_city])->first();
                if ($zone_class['class'] == 1) {
                    $min_charges = $min_charges['chargeable_weight_charges_class_1'];
                } elseif ($zone_class['class'] == 2) {
                    $min_charges = $min_charges['chargeable_weight_charges_class_2'];
                } elseif ($zone_class['class'] == 3) {
                    $min_charges = $min_charges['chargeable_weight_charges_class_3'];
                } else {
                    $min_charges = $min_charges['chargeable_weight_charges_class_0'];
                }
            }
            return ['status' => 1, 'min_charges' => $min_charges];
        }
    }

    public function history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),289);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $charges_mode = ChargesModes::select('id','charges_mode')->get();
        return view('admin.shipment.history.walk_in_history')->with(['shipment_status' => $shipment_status, 'charges_mode' => $charges_mode]);
    }

    public function history_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),290);
        }
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 1)'));
            })
            ->join('admins as a', 'a.id', '=' , 'sj.admin_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftjoin('charges_modes as cm', 'cm.id', '=', 'shipments.charges_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.tracking_number as tracking','ss.name as status', 'a.name as booked_by', 'oc.name as origin','dc.name as destination', 'h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_address','shipments.weight_charges','shipments.fuel_surcharge','shipments.return_charges','shipments.gst','shipments.created_at as arrival_date','shipments.amount', 'shipments.received_amount', 'shipments.charges_mode_id', 'cm.charges_mode as charges_mode'])
            ->where('shipments.booking_type_id', 4);

        if (!in_array(session('role_id'), [1, 2, 3, 4, 5, 6])){
            $shipments = $shipments->where('sj.admin_id', Auth::id());
        }
            $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('aging',function ($shipments){
                $days = Carbon::now()->diffInDays($shipments->arrival_date);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('total_charges', function($shipment){
                if ($shipment->charges_mode_id == 1) {
                    return (($shipment->received_amount) ? number_format($shipment->received_amount) : '0');
                }
                else if ($shipment->charges_mode_id == 2) {
                    return (($shipment->amount) ? number_format($shipment->amount) : '0');
                }
                else {
                    return '0';
                }
            })
            ->editColumn('return_charges', function ($shipment){
                if($shipment->return_charges != null){
                    return $shipment->return_charges;
                }
                else{
                    return "-";
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->where('shipments.tracking_number', $tracking_numbers);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('shipments.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }

    public function international_book_index()
    {
        $check_id = GlobalSettings::select('setting_value')->where('type', "Walk-In")->first();
        $user_id = $check_id['setting_value'];
        $booking_types = BookingType::select('id')->where('id', '=', 4)->first();
        $user_shipping_infos = UserShippingInfo::where('user_id', $user_id)->get();
        $cities = WalkInCities::join('cities as c', 'c.id', '=', 'walk_in_cities.city_id')
            ->select('c.name as city_name', 'c.id as city_id')->where('walk_in_cities.pickup', 1)->get();
        $allowed_hubs = WalkInInternationalStandardWeightChargeHub::pluck('hub_id')->toArray();
        $consignee_cities = City::whereIn('hub_id', $allowed_hubs)->where('cities.status', 1)->where('cities.hub', 0)->where('cities.business_category_id', 2)->whereNotNull('cities.zone_id')->groupBy('cities.id')->orderBy('cities.name')->select('cities.name','cities.id')->groupBy('cities.id')->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode = ShippingMode::where('id', 2)->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $charges_modes = ChargesModes::whereIn('id', [1, 2])->get();
        $packaging_type = PackagingMaterialTypes::with('sizes')->where('status', 1)->get();
        $packaging_sizes = PackagingMaterialTypeSizes::all();
        return view('admin.shipment.book.international_book.international_walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode, 'user_shipping_infos' => $user_shipping_infos, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes, 'consignee_cities' => $consignee_cities, 'packaging_types' => $packaging_type, 'packaging_sizes' => $packaging_sizes]);
    }

    public function international_walk_in_store(Request $request) {
        $test = 0;
        $open_shipment = 0;
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();

        $user_id = $check_id['setting_value'];

        if (!empty($request->input('shipping_mode'))) {

            $service_type_id = 4;

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

            $packaging_charges = $request->packaging_charges;


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

            $delivery_type = $request->delivery_type;
            $charges_mode_id = $request->charges_mode;
            $pickup = 0;
            if($request->filled('pickup')){
                $pickup = 1;
                $actual_weight = 0;
                $charges_per_kg = 0;
                $weight_charges = 0;
                $fuel_surcharge = 0;
                $gst = 0;
                $amount = 0;
                $r_amount = 0;

            }else{
                $actual_weight = $request->actual_weight;
                $charges_per_kg = $request->charges_per_kg;
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

            }
            $business_category_id = 2;

            $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $pickup_city_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id, $amount, $fuel_surcharge, $actual_weight, $gst, $weight_charges, $r_amount, $delivery_type, $charges_mode_id, $packaging_charges, $pickup, $business_category_id, $open_shipment);

            $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);


            $international_shipment = new InternationalShipment();
            $international_shipment->shipment_id = $shipment_id;
            $international_shipment->postal_code = $request->input('postal_code');
            $international_shipment->save();

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

            if($request->filled('pickup')){
                $walkin_weight = new WalkinShipmentWeightCharges();
                $walkin_weight->shipment_id = $shipment_id;
                $walkin_weight->charges_per_kg = $request->charges_per_kg;
                $walkin_weight->save();
            }

            if($request->has('pack_type')){

                $pickup_address_details = UserShippingInfo::find($pickup_address_id);
                $hub_id = $pickup_address_details->city->hub_id;

                $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id',$hub_id);

                $fulfilment_hub = $fulfilment_hub->first();

                $warehouse_id = $fulfilment_hub->warehouse_id;

                $packaging_types = PackagingMaterialTypes::all();
                $packaging_sizes = PackagingMaterialTypeSizes::all();
                foreach ($packaging_types as $ptype) {
                    foreach ($packaging_sizes as $psize) {
                        $index = (int)($ptype->id . $psize->id);

                        if (array_key_exists($index, $request->pack_type)) {
                            $test += 1;
                            $packaging_history = new WalkInShipmentPackagingMaterialHistory();
                            $packaging_history->shipment_id = $shipment_id;
                            $packaging_history->type_id = $request->pack_type[$index];
                            $packaging_history->size_id = $request->pack_size[$index];
                            $packaging_history->quantity = $request->pack_quantity[$index];
                            $packaging_history->save();

                            $type_id = $request->pack_type[$index];
                            $type_size_id = $request->pack_size[$index];
                            $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id])->first();

                            $stock->stock = $stock['stock'] - $request->pack_quantity[$index];
                            $stock->save();

                        }
                    }
                }
            }
//                NotificationsController::send(2, $shipment_id);

            $shipment_ids = array($shipment_id);
            if($pickup == 0){
                NotificationsController::send(85, $shipment_ids , Auth::id());
            }

            if ($request->filled('book_and_print')) {
                $print = $shipment_id;
            }
            else {
                $print = FALSE;
            }
            return redirect()->back()->with(['success' => $test .' Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
        }
        else {
            return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
        }
    }
    public function check_international_standard_weight(Request $request){
        if($request->shipping_mode != null && $request->delivery_type != null && $request->consignee_city != null && $request->pickup_city != null && $request->pickup != null) {
            $city = City::find($request->consignee_city);
            $hub_id = $city->hub_id;
            $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
            $check = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);

            if($request->delivery_type == 1){
                $actual_weight = $check->door_actual_weight;
                $chargeable_weight = $check->door_chargeable_weight;
            }
            else{
                $actual_weight = $check->hub_actual_weight;
                $chargeable_weight = $check->hub_chargeable_weight;
            }
            if($request->pickup == 'false'){
                if ($request->actual_weight < $actual_weight) {
                    return response()->json(['status' => 1, 'error' => 'Actual Weight must be greater than or equal to ' . $actual_weight]);
                }
            }

            if ($request->charges_per_kg < $chargeable_weight) {
                return response()->json(['status' => 0, 'error' => 'Charges per kg must be greater than or equal to ' . $chargeable_weight]);
            }

            return response()->json(['status' => 2, 'error' => '']);
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

    public function check_international_min_charges(Request $request){
        if($request->pickup_city != null && $request->consignee_city != null) {
            $city = City::find($request->consignee_city);
            $hub_id = $city->hub_id;
            $standard_charges_hub = WalkInInternationalStandardWeightChargeHub::where('hub_id', $hub_id)->first();
            $min_charges = WalkInInternationalStandardWeightCharge::find($standard_charges_hub->international_charges_id);
            if($request->delivery_type == 1){
                $min_charges = $min_charges->door_chargeable_weight;
            }
            else{
                $min_charges = $min_charges->hub_chargeable_weight;
            }
            return ['status' => 1, 'min_charges' => $min_charges];
        }
    }

    public function ftl_book_index(){
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();
        $user_id = $check_id['setting_value'];
        $booking_types = BookingType::select('id')->where('id', '=', 4)->first();
        $user_shipping_infos = UserShippingInfo::where('user_id', $user_id)->get();
        $cities = City::where('status',1)->where('business_category_id',1)->get();
        $walk_in_cities = City::join('walk_in_cities as wc','wc.city_id','=','cities.id')
            ->join('city_deliveries as cd','cd.city_id','=','cities.id')->select('cities.name as city_name','cities.id as city_id')->where('wc.delivery', 1)->where('cities.status',1)->where('cd.booking_type_id',6)->groupBy('cities.id')->get();
        $consignee_cities = WalkInCities::join('cities as c', 'c.id', '=', 'walk_in_cities.city_id')
            ->select('c.name as city_name','c.id as city_id')->where('walk_in_cities.delivery', 1)->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode = ShippingMode::where('id','!=', 4)->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $charges_modes = ChargesModes::whereIn('id', [1, 2])->get();
        $approve_ftl_requests = FtlRequest::where('status_id',3)->get();
        return view('admin.ftl.request.walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode , 'user_shipping_infos' => $user_shipping_infos, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'charges_modes' => $charges_modes,'consignee_cities' => $consignee_cities,'approve_ftl_requests' => $approve_ftl_requests,'walk_in_cities' => $walk_in_cities]);
    }

    public function get_ftl_info(Request $request){
        if($request->id){
            $ftl_request = FtlRequest::find($request->id);
            $data = array('origin_id' => $ftl_request->origin_id,'destination_id' => $ftl_request->destination_id, 'weight' => $ftl_request->weight, 'quantity' => $ftl_request->quantity ,'total_charges' => $ftl_request->total_charges,'gst' => $ftl_request->gst);

            return response()->json(['status' => 1, 'data' => $data ]);
        }
        else{
            return response()->json(['status' => 0]);
        }
    }

    public function ftl_store(Request $request) {
        $test = 0;
        $check_id = GlobalSettings::select('setting_value')->where('type',"Walk-In")->first();

        $user_id = $check_id['setting_value'];

        if (BookingType::where('id', '!=', 3)->where('id', $request->input('selected_service_type'))->exists()) {

            if (!empty($request->input('shipping_mode'))) {

                $service_type_id = 6;

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

                $ftl_request = FtlRequest::where('id',$request->approve_frieght_request)->first();

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

                $packaging_charges = $request->packaging_charges;


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

                $delivery_type = $request->delivery_type;
                $charges_mode_id = $request->charges_mode;
                $pickup = 0;
                if($request->filled('pickup')){
                    $pickup = 1;
                    $actual_weight = 0;
                    $charges_per_kg = 0;
                    $weight_charges = 0;
                    $fuel_surcharge = 0;
                    $gst = 0;
                    $amount = 0;
                    $r_amount = 0;

                }else{
                    $actual_weight = $request->actual_weight;
                    $city = City::where('id',$pickup_city_id)->first();
                    $zone = Zone::where('id',$city['zone_id'])->first();

                }
                $ftl_request = FtlRequest::where('id',$request->approve_frieght_request)->first();
                // $other_amount = FtlRequestAdditionalCost::where('ftl_request_id',$ftl_request->id)->sum('amount');
                $business_category_id = 1;

                // $calc_total = ((($ftl_request->freight_charges/$ftl_request->weight)*$actual_weight)-$other_amount);
                // $ftl_request->total_charges

                $shipment_id = $this->book($user_id, $service_type_id, $pickup_address_id, $pickup_city_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $shipping_mode_id, $same_day_timing_id,0 , null, $actual_weight, $ftl_request->gst, 0, $ftl_request->total_charges, $delivery_type, null, null, $pickup, $business_category_id,0);

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


                if($request->has('pack_type')){

                    $pickup_address_details = UserShippingInfo::find($pickup_address_id);
                    $hub_id = $pickup_address_details->city->hub_id;

                    $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id',$hub_id);

                    $fulfilment_hub = $fulfilment_hub->first();

                    $warehouse_id = $fulfilment_hub->warehouse_id;

                }
                NotificationsController::send(2, $shipment_id);

                $shipment_ids = array($shipment_id);
                if($pickup == 0){
                    NotificationsController::send(85, $shipment_ids , Auth::id());
                }

                 FtlRequest::where('id',$request->approve_frieght_request)->update(['shipment_id' => $shipment_id,'status_id' => 5,'collection_type' => $request->ftl_collection_type]);

                FTLController::FTLRequestStatusHistory($request->approve_frieght_request,5, Auth::id());

                  $walkin_ftl_invoice = new WalkinFtlInvoice();
                  $walkin_ftl_invoice->ftl_request_id = $request->approve_frieght_request;
                  $walkin_ftl_invoice->save();
                  $invoice_number = $user_id . str_pad($walkin_ftl_invoice->id, 6, '0', STR_PAD_LEFT);
                  WalkinFtlInvoice::where('id',$walkin_ftl_invoice->id)->update(['invoice_number' => $invoice_number]);


                  $ftl_shipment = Shipment::find($shipment_id);
                  $calc_total = ((($ftl_shipment->ftl->freight_charges/$ftl_shipment->ftl->weight)*$ftl_shipment->actual_weight));
                $ftl_shipment->weight_charges = $calc_total;
                $ftl_shipment->save();
                if ($request->filled('book_and_print')) {
                    $print = $shipment_id;
                }
                else {
                    $print = FALSE;
                }
                return redirect()->back()->with(['success' => $test .' Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
            }
            else {
                return redirect()->back()->with('error', 'Shipping Mode needs to be Selected');
            }
        }
        else {
            return redirect()->back()->with('error', 'Invalid Service Type Selected');
        }
    }

    public function print_ftl_air_waybill(Request $request) {
        $user_type = NULL;
        $user_id = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();

            $user_name = Auth::user()->name . ' (Admin) #' . $user_id;
        }
        else {
            $user_name = 'Unknown';
        }

        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        if ($user_type) {
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
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                            <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>

                            <td class="color primary border twice-left"><strong>Service</strong></td>
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
                        if($shipment->ftl->collection_type == 1){
                             $type = 'invoice';
                        }
                        else{
                            $type = 'cash';
                        }
                $table_end = '
                              <tr>
                                <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                                <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Estimated Weight</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . ' kg</strong></td>
                              </tr>
                              <tr>
                                <td class="color primary border twice-top twice-bottom twice-left"><strong>Collection Type Mode</strong></td>
                                <td class="border twice-top twice-bottom twice-left"><strong>' . $type . '</strong></td>
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>' . $shipment->received_amount . '</strong></td>
                ';
/*
                if ($shipment->charges_mode_id == 1) {
                    $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs 0</strong></td>
                    ';
                }
                else {
                    $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                    ';
                }*/

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
            if($shipment->actual_weight > 0){
                $item = $shipment->items->first();
                $invoice = '<div class="invoice p-1">
                    <table class="table table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-left align-middle">
                            <img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mb-1">
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
                                    <td class="color primary" colspan="2"><strong>Sender Details</strong></td>
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

                        <div class="col-6">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>Tracking No.</strong></td>
                                    <td>'. $shipment->tracking_number .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row align-items-start justify-content-between summary">
                    <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Receiver Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>'. $shipment->consignee_name .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>'. $shipment->consignee_address .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>'. $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-sm table-bordered border invoice">
                              <tbody>
                                <tr class="color primary">
                                    <td colspan="4"><strong>Shipment Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Shipping Mode</strong></td>
                                    <td>'. $shipment->shipping_mode->mode .'</td>
                                    <td class="color secondary"><strong>Order ID</strong></td>
                                    <td>'. $shipment->order_id .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Origin</strong></td>
                                    <td>'. $shipment->pickup_address->city->name .'</td>
                                    <td class="color secondary"><strong>Destination</strong></td>
                                    <td>'. $shipment->consignee_city->name .'</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Booking Date</strong></td>
                                    <td>'. $shipment->created_at->format('Y-m-d H:i:s') .'</td>
                                    <td class="color secondary"><strong>Weight</strong></td>
                                    <td>'. $shipment->actual_weight .'</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <table class="table table-sm table-bordered border invoice">
                                  <tbody>
                                    <tr>
                                      <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                                      <td class="color secondary border twice-top"><strong>Type</strong></td>
                                      <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                                      <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                      <td class="border twice-top">' . $item->quantity . '</td>
                                    </tr>
                                    <tr>
                                      <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                      <td colspan="6" class="border twice-bottom">' . $item->description .'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
            ';

                $invoice_serial_number = 1;



                $invoice_details = '';
                $invoice_details .= '
            <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                            <td class="color primary text-left"><strong>Invoice Summary</strong></td>
                            <td class="color primary text-right" style="width: 20% !important;"><strong>Amount (PKR)</strong></td>
                        </tr>
                        <tr>
                          <td class="text-left">Freight Charges</td>
                          <td class="text-right">' . number_format($shipment->ftl->freight_charges) . '</td>
                        </tr>
                 
                       
                      </tbody>
                    </table>
            
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                              
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($shipment->ftl->gst) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format($shipment->ftl->total_charges) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';
                $invoice .= $invoice_details;
                $html .= $invoice;
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
    }

}
