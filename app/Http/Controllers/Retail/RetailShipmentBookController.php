<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ConsigneeInformationController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\http\Models\Admin\Retail\RetailPaymentMode;
use App\http\Models\Admin\Retail\RetailProduct;
use App\http\Models\Admin\Retail\RetailShippingMode;
use App\http\Models\Admin\Retail\RetailTraxBox;
use App\http\Models\Admin\Retail\RetailUser;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\ConsigneeLocation;
use App\Http\Models\ConsigneeShipmentLocation;
use App\http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\http\Models\ShipmentOrderDate;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\http\Models\SubstituteUserShipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RetailShipmentBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    static public function book($user_id, $service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id, $try_and_buy_charges, $pieces, $business_category_id) {

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
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->charges_mode_id = $charges_mode_id;
        $shipment->shipper_status_id = 1;
        $shipment->consignee_status_id = 1;

        $shipment->try_and_buy_fees = $try_and_buy_charges;
        $shipment->pieces = $pieces;
        $shipment->business_category_id = $business_category_id;

        $shipment->save();

        $shipment_id = $shipment->id;

        AdminPickupsController::generate($shipment_id);
        $reference_1_id = null;
        ShipmentsJourneyController::add($shipment_id, 1, 1, NULL, NULL, $user_id, NULL, $reference_1_id);

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
        $total_pieces= 0;
        if($pieces > 1){

            for($i=1; $i<=$pieces; $i++){
                $shipment_piece = new ShipmentPiece();
                $shipment_piece->shipment_id = $shipment_id;
                $total_pieces++;
                $shipment_piece->numbering=$total_pieces;
                $shipment_piece->tracking_number= $shipment_id . $total_pieces;
                $shipment_piece->save();
            }

        }
    }

    public function index(){
        $products = RetailProduct::all();
        $business_categories = BusinessCategory::where('id', '!=', 2)->get();
        $shipping_modes = RetailShippingMode::all();
        $domestic_cities = City::where('business_category_id', 1)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', '=', 1)->get();
        $trax_boxes = RetailTraxBox::all();
        return view('retail.shipment.booking.index')->with(['products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'domestic_overland_cities' => $domestic_overland_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes]);
    }

    public function store(Request $request){
        return $request;
//        dd($request);
        $user_id = session('user_id');
        $pickup_address_id = session('pickup_address_id');
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;

        $consignee_city_id = $request->input('domestic_destination');
        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_phone_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = NULL;
        $package_type = FALSE;
        $special_instructions = NULL;

        $estimated_weight = $request->input('weight');
        $shipping_mode_check = $request->input('shipping_mode');
        if ($shipping_mode_check == 2) {
            $shipping_mode_id = 2;
        }
        elseif ($shipping_mode_check == 4){
            $shipping_mode_id = 3;
        }
        else{
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        $amount = str_replace(',', '', $request->input('total_amount'));
        $payment_mode_id = 1;
        $retail_payment_mode_id = $request->input('payment_mode');
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category');

        $charges_mode_id = 1;

        $shipment_id = $this->book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id, $charges_mode_id , $try_and_buy_charges, $pieces_quantity, $business_category_id);

        $tracking_number = $this->generate_tracking_number($shipment_id, $pickup_city_id, $consignee_city_id);

        $product_type_id = 24;

        $item_description = NULL;

        $item_quantity = 1;

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
        if($request->book_button == 0){
            return response()->json(['status' => 1, 'success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'shipment_id' => $shipment_id]);
        }
        else{
            $print = $shipment_id;
            return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
        }
    }
}
