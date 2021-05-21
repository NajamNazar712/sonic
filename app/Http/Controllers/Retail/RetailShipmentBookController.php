<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\Admin;
use App\http\Models\Admin\Retail\RetailCashDeposit;
use App\http\Models\Admin\Retail\RetailCashDepositShipment;
use App\http\Models\Admin\Retail\RetailPaymentMode;
use App\http\Models\Admin\Retail\RetailShipment;
use App\http\Models\Admin\Retail\RetailShipperInfo;
use App\http\Models\Admin\Retail\RetailShippingMode;
use App\http\Models\Admin\Retail\RetailTraxBox;
use App\http\Models\Admin\Retail\RetailTraxCenter;
use App\http\Models\Admin\Retail\RetailUser;
use App\Http\Models\BanksList;
use App\Http\Models\BusinessCategory;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\Product;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\Shipper\UserShippingInfo;
use Barryvdh\Snappy\Facades\SnappyImage;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

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
        $order_id = $request->input('order_id');
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

        $request->weight_charges = str_replace(',', '', $request->input('weight_charges'));
        $request->fuel_surcharge = str_replace(',', '', $request->input('fuel_surcharge'));

        $city = City::find($pickup_city_id);
        $gst = $city->zone->gst;
        $total_charges_without_gst = $request->weight_charges + $request->fuel_surcharge;
        $gst = $gst * $total_charges_without_gst;
        $total_charges = $total_charges_without_gst + $gst;
        if($shipping_mode_check == 3){
            $amount = str_replace(',', '', $request->input('cod'));
            $r_amount = 0;
        }
        else{
            $amount = 0;
            $r_amount = 0;
        }
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

        $product_type_id = $request->product;

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
            $shipper_info->city_id = $pickup_city_id;


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
        $retail_shipment->retail_user_id = Auth::id();
        $retail_shipment->save();


        $date = Carbon::today()->toDateString();
        $cash_deposit = RetailCashDeposit::whereDate('created_at', $date)->where('category', Auth::user()->category)->where('retail_user_id', Auth::id());
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
            $cash_deposit->category = Auth::user()->category;
            $cash_deposit->retail_user_id = Auth::id();
            $cash_deposit->total_cn = 1;
            $cash_deposit->total_cash = $total_charges;
            $cash_deposit->save();
        }

        $cash_deposit_shipment = new RetailCashDepositShipment();
        $cash_deposit_shipment->cash_deposit_id = $cash_deposit->id;
        $cash_deposit_shipment->shipment_id = $shipment_id;
        $cash_deposit_shipment->shipping_mode_id = $request->shipping_mode;
        $cash_deposit_shipment->save();


        AdminPickupsController::generate($shipment_id);
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
        if($request->has('total_charges_without_gst')){
            $pickup_address_id = session('pickup_address_id');
            $user_shipping_info = UserShippingInfo::find($pickup_address_id);
            $pickup_city_id = $user_shipping_info->city_id;
            $city = City::find($pickup_city_id);
            $total_charges_without_gst = $request->total_charges_without_gst;
            $gst = $city->zone->gst;
            $gst = $gst * $total_charges_without_gst;
            $total_charges = $gst + $total_charges_without_gst;
            $details = array();

            $details['total_charges_without_gst'] = number_format(ROUND($total_charges_without_gst, 0, PHP_ROUND_HALF_DOWN));
            $details['gst'] = number_format(ROUND($gst, 0, PHP_ROUND_HALF_DOWN));
            $details['total_charges'] = number_format(ROUND($total_charges, 0, PHP_ROUND_HALF_DOWN));
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
        $page_items = 1;
        foreach($request->ids as $id) {
            $shipment = Shipment::find($id);
//            $url = 'storage/retail/shipment_'. $shipment->id.'.jpg';
//            if(!file_exists($url)){
//                $this::save_slip($shipment->id);
//            }
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
                            <td colspan="8">'.$shipment->order_id.'</td>
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
                    $fuel_and_gst = $shipment->retail->fuel_surcharge + $shipment->retail->gst;
                    $slip .= '
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
                            </tr>
                            </tbody>
                            </table>
                            </div>
                           ';

            $slip .= '
                  <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Shipper Copy</p></div><div class="col"><hr></div>
                  <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
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
                                <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                        $table_start .= '
                            <td rowspan="4" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment_item->id . '</strong></span>
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
                                <td colspan="2" class="border twice-bottom">' . $shipment_item->description . '</td>
                                <td class="color secondary border twice-bottom"><strong>Price</strong></td>
                                <td class="border twice-bottom">Rs ' . number_format($shipment_item->price) . '</td>';

                        $table_start .= '
                            <td rowspan="3" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
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
                            <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                ';
                    $table_start .= '
                            <td rowspan="4" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
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
                            <td class="color primary border twice-bottom twice-left"><strong>Business Category</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right"><strong>' . $shipment->business_category->name . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                            <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Name</strong></td>
                ';

                    $table_start .= '
                            <td colspan="3" class="border twice-right">' . $shipment->retail->shipper_name . '</td>
                ';

                    $table_start .= '
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                ';

                    $table_start .= '
                        <td class="color secondary"><strong>Address</strong></td>
                        <td colspan="3" class="border twice-right">' . $shipment->retail->shipper_address . '</td>
                ';

                    $table_start .= '
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                ';

                    $table_start .= '
                    <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                    <td colspan="3" class="border twice-bottom twice-right">' . $shipment->retail->shipper_phone_no . '</td>
                ';

                    $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                          </tr>
                ';

                    $table_end = '
                          <tr>
                            <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                            <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>';
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
                    }  elseif ($shipment->booking_type_id != 4) {
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

                    if ($shipment->booking_type_id != 4 && $shipment->charges_mode_id == 2 && $shipment->shipper_status_id == 1) {
                        $table_end .= '
                        <div class="void position-absolute m-auto text-center font-weight-bold">Void Air Waybill after Arrival</div>
                    ';
                    }

                    $table_end .= '
                      </div>
                ';

                    $table_end .= '
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

                    }

                    if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                        $shipment_pieces = '';

                        foreach ($shipment->shipment_pieces as $piece){
                            $shipment_pieces .= '<table class="table table-sm table-bordered border twice">
                        <tbody><tr>';
                            $shipment_pieces .= '<td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>';
                            $shipment_pieces .= '<td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($piece->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                  <span><strong>' . $piece->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="1" class="color primary border twice-left"><strong>Origin</strong></td>
                                <td rowspan="1" class="border">' . $shipment->pickup_address->city->name . '</td>
                                <td rowspan="1" class="color primary border "><strong>Destination</strong></td>
                                <td rowspan="1" class="border">' . $shipment->consignee_city->name . '</td>
                                
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . $shipment->tracking_number . '</strong></span>
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
        return  $datatable->make(true);
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
        return view('retail.shipment.other_booking');
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
            $datatable->whereDate('retail_shipments.created_at', $date);
        }

        return  $datatable->make(true);
    }
}
