<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\http\Models\Admin\Retail\RetailPaymentMode;
use App\http\Models\Admin\Retail\RetailProduct;
use App\http\Models\Admin\Retail\RetailShipment;
use App\http\Models\Admin\Retail\RetailShipperInfo;
use App\http\Models\Admin\Retail\RetailShippingMode;
use App\http\Models\Admin\Retail\RetailTraxBox;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RetailShipmentBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

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
        $products = Product::all();
        $business_categories = BusinessCategory::where('id', '!=', 2)->get();
        $shipping_modes = RetailShippingMode::all();
        $domestic_cities = City::where('business_category_id', 1)->where('status', 1)->get();
        $domestic_overland_cities = CityDelivery::join('cities as c', 'c.id', '=', 'city_deliveries.city_id')->where('city_deliveries.booking_type_id', 1)->where('city_deliveries.shipping_mode_id', 2)->where('c.business_category_id', 1)->where('c.status', 1)->select('c.id', 'c.name')->get();
        $payment_modes = RetailPaymentMode::where('id', '=', 1)->get();
        $trax_boxes = RetailTraxBox::all();
        $banks = BanksList::all();
        return view('retail.shipment.booking.index')->with(['products' => $products, 'business_categories' => $business_categories, 'shipping_modes' => $shipping_modes, 'domestic_cities' => $domestic_cities, 'domestic_overland_cities' => $domestic_overland_cities, 'payment_modes' => $payment_modes, 'trax_boxes' => $trax_boxes, 'banks' => $banks]);
    }

    public function store(Request $request){
        $user_id = session('user_id');
        $pickup_address_id = session('pickup_address_id');
        $user_shipping_info = UserShippingInfo::find($pickup_address_id);
        $pickup_city_id = $user_shipping_info->city_id;
        $information_display = TRUE;

        $consignee_name = $request->input('consignee_name');
        $consignee_address = $request->input('consignee_address');
        $consignee_phone_number_1 = $request->input('consignee_phone_no');
        $consignee_phone_number_2 = NULL;
        $consignee_email_address = NULL;
        $order_id = NULL;
        $package_type = FALSE;
        $special_instructions = NULL;


        $shipping_mode_check = $request->input('shipping_mode');
        if ($shipping_mode_check == 1) {
            $consignee_city_id = $request->input('domestic_overland_destination');
            $shipping_mode_id = 2;
        }
        elseif ($shipping_mode_check == 4){
            $consignee_city_id = $request->input('domestic_destination');
            $shipping_mode_id = 3;
        }
        else{
            $consignee_city_id = $request->input('domestic_destination');
            $shipping_mode_id = 1;
        }
        $same_day_timing_id = NULL;

        $city = City::find($consignee_city_id);
        $gst = $city->zone->gst;
        $total_charges = $request->weight_charges + $request->cash_handling_charges + $request->fuel_surcharge;
        $gst_charges = $gst * $total_charges;
        $total_amount = $total_charges + $gst_charges;

        $amount = $total_amount;
        $r_amount = $total_amount;
        $payment_mode_id = 1;
        $try_and_buy_charges = NULL;

        $pieces_quantity = $request->input('pieces');
        $business_category_id = $request->input('business_category');

        $charges_mode_id = 1;

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

        $shipment_id = $this->book($user_id, 1, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $r_amount, $payment_mode_id, $charges_mode_id , $try_and_buy_charges, $pieces_quantity, $business_category_id, $length, $breadth, $height);

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

        if($request->shipping_mode == 1){
            $destination = $request->domestic_overland_destination;
        }
        else{
            $destination = $request->domestic_destination;
        }

        $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no);
        if($shipper_info->exists()){
            $shipper_info = $shipper_info->first();
            $shipper_info->shipper_phone_no = $request->shipper_phone_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;

            if ($request->hasFile('cheque_image') && $request->iban_no != null && $request->account_no != null && $request->bank != null) {
                $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';

                $file = $request->file('cheque_image');

                Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);

                $shipper_info->bank_id = $request->bank;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                $shipper_info->cheque_image = $filename;
                $shipper_info->completed_status = 1;
            }
            $shipper_info->save();
        }
        else{
            $shipper_info = new RetailShipperInfo();
            $shipper_info->shipper_phone_no = $request->shipper_phone_no;
            $shipper_info->shipper_name = $request->shipper_name;
            $shipper_info->shipper_cnic = $request->shipper_cnic;
            $shipper_info->shipper_address = $request->shipper_address;
            if ($request->hasFile('cheque_image') && $request->iban_no != null && $request->account_no != null && $request->bank != null) {
                $filename = 'retail_shipper_' . $shipper_info->id . '_cheque_image.png';

                $file = $request->file('cheque_image');

                Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);

                $shipper_info->bank_id = $request->bank;
                $shipper_info->iban = $request->iban_no;
                $shipper_info->account_number = $request->account_no;
                $shipper_info->cheque_image = $filename;
                $shipper_info->completed_status = 1;
            }
            $shipper_info->save();
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
        $retail_shipment->total_charges = $total_charges;
        $retail_shipment->gst_charges = $gst_charges;
        $retail_shipment->total_amount = $total_amount;
        $retail_shipment->weight_charges = $request->weight_charges;
        $retail_shipment->cash_handling_charges = $request->cash_handling_charges;
        $retail_shipment->fuel_surcharge = $request->fuel_surcharge;
        $retail_shipment->shipper_account_no = $shipper_info->id;
        $retail_shipment->weight = $estimated_weight;
        $retail_shipment->length = $length;
        $retail_shipment->breadth = $breadth;
        $retail_shipment->height = $height;
        $retail_shipment->retail_user_id = Auth::id();
        $retail_shipment->save();

        NotificationsController::send(115, $tracking_number, $shipper_info->id);

        if($request->book_button == 0){
            return response()->json(['status' => 1, 'success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'shipment_id' => $shipment_id]);
        }
        else{
            $print = $shipment_id;
            return redirect()->back()->with(['success' => 'Shipment Booked with Tracking Number: ' . $tracking_number, 'print' => $print]);
        }
    }
    public function calculate_rates(Request $request){
        if($request->has('total_charges') && $request->has('city_id')){
            $city = City::find($request->city_id);
            $total_charges = $request->total_charges;
            $gst = $city->zone->gst;
            $gst_charges = $gst * $total_charges;
            $total_amount = $gst_charges + $total_charges;
            $details = array();

            $details['total_charges'] = $total_charges;
            $details['gst_charges'] = $gst_charges;
            $details['total_amount'] = $total_amount;
            return response()->json(['status' => 1, 'success' => 'Rates Calculated!', 'details' => $details]);
        }
    }

    public function shipper_info(Request $request){
        if($request->has('shipper_account_no')){
            if($request->shipper_account_no != null && $request->shipper_account_no != ''){
                $shipper_info = RetailShipperInfo::find($request->shipper_account_no);
            }
        }
        elseif($request->has('shipper_phone_no')){
            if($request->shipper_phone_no != null && $request->shipper_phone_no != ''){
                $shipper_info = RetailShipperInfo::where('shipper_phone_no', $request->shipper_phone_no);
                if($shipper_info->exists()){
                    $shipper_info = $shipper_info->first();
                }
            }
        }

        if($shipper_info){
            $details = array();
            $details['shipper_account_no'] = $shipper_info->id;
            $details['shipper_phone_no'] = $shipper_info->shipper_phone_no;
            $details['shipper_name'] = $shipper_info->shipper_name;
            $details['shipper_cnic'] = $shipper_info->shipper_cnic;
            $details['shipper_address'] = $shipper_info->shipper_address;

            if($shipper_info->completed_status == 1){
                $complete_info = true;
            }
            else{
                $complete_info = false;
            }
            return response()->json(['status' => 1, 'success' => 'Shipper Info Found!', 'details' => $details, 'complete_info' => $complete_info]);
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

        $shipment_details = '';
        foreach($request->ids as $id) {
            $shipment = Shipment::find($id);
                    $table_start = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';

                    $table_start .= '
                          <tr>
                            <td rowspan="2" colspan="2" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
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
                ';

                    $table_start .= '
                          <tr>
                            <td colspan="7" class="text-center color primary border twice-top twice-left twice-right"><strong>Shipper</strong></td>
                            <td colspan="6" class="text-center color primary border twice-top twice-left twice-right"><strong>Consignee</strong></td>
                          </tr>
                ';

                    $table_start .= '
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
                    $table_start .= '
                      <tr>
                        <td class="color secondary border twice-bottom"><strong>Address</strong></td>
                        <td colspan="6" class="border twice-bottom twice-right">' . $shipment->retail->shipper_address . '</td>
                        <td class="color secondary border twice-bottom twice-left"><strong>Address</strong></td>
                        <td colspan="5" class="border twice-bottom twice-right">' . $shipment->consignee_address . '</td>
                      </tr>
                ';
                    $fuel_and_gst = $shipment->retail->fuel_surcharge + $shipment->retail->gst;
                    $table_start .= '
                              <tr>
                                <td colspan="3" class="color primary border twice-left"><strong>Destination</strong></td>
                                <td colspan="2" class="color primary"><strong>Pieces</strong></td>
                                <td colspan="3" class="color primary"><strong>Weight</strong></td>
                                <td colspan="2" class="color primary border"><strong>Fuel and GST</strong></td>
                                <td colspan="3" class="color primary border twice-right"><strong>Total Amount</strong></td>
                            </tr>
                              <tr>
                                <td colspan="3" class="border twice-bottom twice-left">' . $shipment->consignee_city->name . '</td>
                                <td colspan="2" class="border twice-bottom">' . $shipment->pieces . '</td>
                                <td colspan="3" class="border twice-bottom">' . $shipment->estimated_weight . '</td>
                                <td colspan="2" class="border twice-bottom">' . $fuel_and_gst . '</td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->retail->total_amount . '</td>
                              </tr>';

                    foreach($shipment->items as $item){
                        if($item->insurance == 1){
                            $insurance = '<b><i class="la la-check-square "> Yes</i></b> <i class="la la-minus-square"> No</i>';
                        }
                        else{
                            $insurance = '<i class="la la-minus-square"> Yes</i> <b><i class="la la-check-square"> No</i></b>';
                        }
                        $table_start .= '
                              <tr>
                                <td colspan="1" class="color primary border twice-left twice-bottom"><strong>Product Name</strong></td>
                                <td colspan="3" class="color border twice-bottom">'. $shipment->retail->product->name . '</td>
                                <td colspan="5" class="color border twice-bottom text-center mr-3"><strong>Insurance: Do you required coverage</strong> '. $insurance . '</td>
                                <td colspan="2" class="color primary border twice-bottom"><strong>Declared Value</strong></td>
                                <td colspan="2" class="color border twice-bottom twice-right">' . $item->price . '</td>
                            </tr>';
                    }

                    if($shipment->length != null && $shipment->breadth != null && $shipment->height != null){
                        $dimensions = $shipment->length . 'x' . $shipment->breadth . 'x' . $shipment->height;
                    }
                    else{
                        $dimensions = '';
                    }

                    $table_start .= '
                              <tr>
                                <td colspan="4" class="color primary border twice-left twice-bottom"><strong>DIMENSIONS OF SHIPMENT (LxWxD)</strong></td>
                                <td colspan="5" class="color border twice-bottom">'. $dimensions . '</td>
                                <td colspan="2" class="color primary border twice-left"><strong>Collection By</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->name . '</td>
                            </tr>';
                    $table_start .= '
                              <tr>
                                <td colspan="4" rowspan="2" class="color primary border twice-left"><strong>Shipper\'s Signature</strong></td>
                                <td colspan="5" rowspan="2" class="color border twice-bottom"></td>
                                <td colspan="2" class="color primary border twice-left"><strong>Code</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->store->code . '</td>
                            </tr>';
                    $table_start .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Date</strong></td>
                                <td colspan="4" class="color border twice-bottom twice-right">' . Carbon::now() . '</td>
                            </tr>
                            </tbody>
                            </table>
                            </div>
                           ';

            $table_start .= '
                  <div class="col m-1 row justify-content-center"><div class="col"><hr></div>
                  <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                ';
            $shipment_details .= $table_start;
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

}
