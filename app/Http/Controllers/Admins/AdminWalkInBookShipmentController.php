<?php

namespace App\Http\controllers\Admins;

use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\StandardFuelSurcharge;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\DeliveryType;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;

use App\Http\Models\BookingType;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\RateStatus;
use App\Http\Models\City;
use App\Http\Models\CityDelivery;
use App\Http\Models\Product;
use App\Http\Models\ShippingMode;
use App\Http\Models\ShippingModeSameDayTiming;
use App\Http\Models\PaymentMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;

use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Validator;
use Illuminate\Validation\Rule;


class AdminWalkInBookShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index() {
        $booking_types = BookingType::select('id')->where('id', '=', 4)->first();
        $use = User::with('shipping.city')->find(session('user_id'));
        $cities = City::where('pickup', 1)->where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $consignee_cities = City::where('status', 1)->whereNotNull('zone_id')->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        $shipping_mode = ShippingMode::where('id','!=', 4)->get();
        $delivery_type = DeliveryType::orderBy('delivery_type')->get();
        $payment_modes = PaymentMode::whereIn('id', [4,1])->get();


        return view('admin.shipment.book.walk_in')->with(['booking_types' => $booking_types, 'shipping_mode' => $shipping_mode , 'u' => $use, 'cities' => $cities, 'products' => $products, 'delivery_type' => $delivery_type, 'payment_modes' => $payment_modes,'consignee_cities' => $consignee_cities]);
    }

    public function walk_in_store(Request $request){
//        dd($request);
    }

    public function add_fuel_surcharge_gst_total(Request $request){
        $fuel = StandardFuelSurcharge::where('shipping_mode_id',$request->shipping_mode_id)->first();
        $fuel_surcharge = ($fuel['fuel_surcharge']/100)*($request->total_c);
        $city = City::where('id',$request->city_id)->first();
        $zone = Zone::where('id',$city['zone_id'])->first();
        $gst = $zone['gst']*($request->total_c + $fuel_surcharge);
        $receivable = $fuel_surcharge + $request->total_c + $gst;
        return response()->json(['gst'=>$gst, 'fuel'=>$fuel_surcharge, 'receivable'=>$receivable]);
    }

    public function print_air_waybill(Request $request) {
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
                    </style>
                  </head>
                  <body>
                    <div>
      ';

        $shipment_details = '';

        foreach($request->ids as $id) {
            $shipment = Shipment::find($id);

            if ($request->has('admin') || session('user_id') == $shipment->user_id) {
                $table_start = '
                      <table class="table table-sm table-bordered border twice">
                        <tbody>
                          <tr>
                            <td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td rowspan="3" colspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>

                            <td class="color primary border twice-left"><strong>Serivce</strong></td>
          ';

                if ($shipment->booking_type_id == 1) {
                    $table_start  .= '
                            <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
            ';
                }
                else if ($shipment->booking_type_id == 2) {
                    $table_start  .= '
                            <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
            ';
                }
                else {
                    $table_start  .= '
                            <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
            ';
                }

                $table_start  .= '
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
                            <td colspan="3" class="border twice-right">' . $shipment->user->name . '</td>
                            <td class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="3">' . $shipment->consignee_name . '</td>
                          </tr>

                          <tr>
                            ' . (($shipment->information_display == 1) ?
                        '<td class="color secondary"><strong>Address</strong></td>
                                <td colspan="3" class="border twice-right">' . $shipment->pickup_address->pickup_address . '</td>'
                        :
                        '<td rowspan="2" colspan="4" class="border twice-bottom twice-right"></td>'
                    ) . '
                            <td class="color secondary border twice-left"><strong>Address</strong></td>
                            <td colspan="3">' . $shipment->consignee_address . '</td>
                          </tr>
                          <tr>
                            ' . (($shipment->information_display == 1) ?
                        '<td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                                <td colspan="3" class="border twice-bottom twice-right">' . $shipment->pickup_address->phone . '</td>'
                        :
                        ''
                    ) . '
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
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->payment_mode->mode . '</strong></td>
                          </tr>
                          <tr>
                            <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                            <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs ' . number_format($shipment->amount) . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top"><em>Kindly do not give any addtional charges to the Rider/Courier. If shipment is found in torn or damaged condition, please do not receive.</em></td>
                          </tr>
                        </tbody>
                      </table>

                      <hr>
          ';

                if ($shipment->booking_type_id == 1) {
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
                else if ($shipment->booking_type_id == 2) {
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
                          <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Replacement Item</strong></td>
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
                else if ($shipment->booking_type_id == 3) {
                    $shipment_details .= $table_start;

                    foreach ($shipment->items as $item) {
                        $shipment_details .= '
                        <tr>
                          <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                          <td class="color secondary border twice-top"><strong>Type</strong></td>
                          <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                          <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                          <td>' . $item->quantity . '</td>
                          <td class="color secondary border twice-top"><strong>Price</strong></td>
                          <td class="border twice-top">Rs ' . number_format($item->price) . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                          <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                        </tr>
              ';
                    }

                    $shipment_details .= $table_end;
                }
            }
        }

        $html .= $shipment_details;

        if ($request->has('twice')) {
            $html .= $shipment_details;
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
