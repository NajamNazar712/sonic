<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\City;
use App\Http\Models\Shipper\User;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\Shipment;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Auth;
use DB;


class AdminReceivingSheetHistoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function receiving_sheet_index()
    {   ActivityTrailController::createActivityTrailLog(Auth::id(),273);
        $shipper_name=User::select('id','name')->get();
        $origin=City::select('id','name')->where('pickup',1)->get();
        return view('admin.shipment.receiving_sheet.index')->with(['shipper_name'=>$shipper_name,'origin'=> $origin]);
    }
    public function receiving_sheet_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(),274);
        }

        $receiving_sheet = ReceivingSheet::join('user_shipping_infos as usi', 'receiving_sheets.pickup_address_id', '=', 'usi.id')
            ->leftjoin('cities as c', 'usi.city_id', '=', 'c.id')
            ->join('users as u','u.id','=','receiving_sheets.user_id')
            ->select('receiving_sheets.id as receiving_sheet_id','receiving_sheets.id as id','receiving_sheets.booked as bookings', 'receiving_sheets.received as receiving', 'c.name as origin', 'usi.pickup_address as address', 'receiving_sheets.created_at as booking_date','u.name as shipper_name');

        $datatable = Datatables::of($receiving_sheet)
            ->editColumn('receiving_sheet_id', function ($receiving_sheet) {
                if($receiving_sheet->receiving_sheet_id != null){
                    return '<button class="btn btn-sm btn-outline-info align-middle print "><i class="la la-lg la-print align-middle "></i> <span class="align-middle id">' . str_pad($receiving_sheet->receiving_sheet_id, 6, '0', STR_PAD_LEFT) . '</span></button>'
                        ;
                }
                return '-';
            });

        if($receiving_sheet_number = $request->get('receiving_sheet_id')){
            $receiving_sheet = $receiving_sheet->where('receiving_sheets.id', '=', $receiving_sheet_number);
        }

        if($origin = $request->get('origin')){
            $receiving_sheet = $receiving_sheet->where('c.id', $origin);
        }

        if($shipper_id = $request->get('shipper_name')){
            $receiving_sheet = $receiving_sheet->where('receiving_sheets.user_id', '=',$shipper_id);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('receiving_sheets.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
    static public function view($id, $user_type, $body_only = FALSE) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '';

        if (!$body_only) {
            $html .= '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            ';

            if ($user_type != 4) {
                $html .= '
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                ';
            }
            else {
                $html .= '
                    <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
                ';
            }

            $html .= '
                    <title>Receiving Sheet</title>

                    <style>
                      @page {
                        size: A4 portrait;
                        margin: 0mm;
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

                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div class="p-1">
            ';
        }

        $receiving_sheet_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $id);

        if ($receiving_sheet_shipments->exists()) {
            $total_shipments = 0;
            $total_cod = 0;

            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Product Type</strong></td>
                            <td class="color primary"><strong>Booking Date</strong></td>
                            <td class="color primary"><strong>Quantity</strong></td>
                            <td class="color primary"><strong>Destination</strong></td>
                            <td class="color primary"><strong>Estimated Weight</strong></td>
                            <td class="color primary"><strong>Pieces</strong></td>
                            <td class="color primary"><strong>Amount</strong></td>
                          </tr>
            ';

            foreach ($receiving_sheet_shipments->orderBy('shipment_id')->get() as $receiving_sheet_shipment) {
                $total_shipments++;

                $shipment = Shipment::find($receiving_sheet_shipment->shipment_id);

                if ($shipment->booking_type_id != 3) {
                    $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->order_id . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                    ';

                    $shipment_details_row_end = '
                            <td>' . $shipment->consignee_city->name . '</td>
                            <td>' . $shipment->estimated_weight . '</td>
                            <td>' . $shipment->pieces . '</td>
                            <td>Rs ' . number_format($shipment->amount) . '</td>
                          </tr>
                    ';
                }
                else {
                    $number_of_items = $shipment->items->count();

                    $shipment_details_row_start = '
                          <tr>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $total_shipments . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->tracking_number . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->order_id . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                    ';

                    $shipment_details_row_end = '
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->consignee_city->name . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . number_format($shipment->estimated_weight) . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">Rs ' . number_format($shipment->amount) . '</td>
                          </tr>
                    ';
                }

                if ($shipment->booking_type_id == 1) {
                    $shipment_details .= $shipment_details_row_start;

                    $item = $shipment->items->first();

                    $shipment_details .= '
                            <td>' . $item->product->product_name . '</td>
                            <td>' . $item->created_at . '</td>
                            <td>' . $item->quantity . '</td>
                    ';

                    $shipment_details .= $shipment_details_row_end;
                }
                else if ($shipment->booking_type_id == 2) {
                    $shipment_details .= $shipment_details_row_start;

                    $item = $shipment->items()->where('type', 0)->first();

                    $shipment_details .= '
                            <td>' . $item->product->product_name . '</td>
                            <td>' . $item->created_at . '</td>
                            <td>' . $item->quantity . '</td>
                    ';

                    $shipment_details .= $shipment_details_row_end;
                }
                else if ($shipment->booking_type_id == 3) {
                    $first = TRUE;

                    foreach ($shipment->items as $item) {
                        if ($first) {
                            $shipment_details .= $shipment_details_row_start;
                        }
                        else {
                            $shipment_details .= '
                          <tr>
                            ';
                        }

                        $shipment_details .= '
                            <td>' . $item->product->product_name . '</td>
                            <td>' . $item->created_at . '</td>
                            <td>' . $item->quantity . '</td>
                        ';

                        if ($first) {
                            $shipment_details .= $shipment_details_row_end;
                        }
                        else {
                            $shipment_details .= '
                          </tr>
                            ';
                        }

                        $first = FALSE;
                    }
                }
                else {
                    $shipment_details .= $shipment_details_row_start;

                    $item = $shipment->items->first();

                    $shipment_details .= '
                            <td>' . $item->product->product_name . '</td>
                            <td>' . $item->created_at . '</td>
                            <td>' . $item->quantity . '</td>
                    ';

                    $shipment_details .= $shipment_details_row_end;
                }

                $total_cod += $shipment->amount;
            }

            $shipment_details .= '
                        </tbody>
                      </table>
            ';

            $first_shipment = $receiving_sheet_shipments->first();

            $shipment = Shipment::find($first_shipment->shipment_id);

            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
            ';

            if ($user_type != 4) {
                $main_details .= '
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                ';
            }
            else {
                $main_details .= '
                            <td class="text-center align-middle"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                ';
            }

            $main_details .= '
                            <td class="text-center align-middle color primary"><strong>Receiving Sheet</strong></td>
            ';

            if ($user_type != 4) {
                $main_details .= '
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                ';
            }
            else {
                $main_details .= '
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst($shipment->user->name) . '</td>
                ';
            }

            $main_details .= '
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Shipper</strong></td>
                            <td>' . $shipment->user->name . '</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Person of Contact</strong></td>
                            <td>' . $shipment->pickup_address->poc . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Pickup Address</strong></td>
                            <td>' . $shipment->pickup_address->pickup_address . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Phone Number</strong></td>
                            <td>' . $shipment->pickup_address->phone . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Origin</strong></td>
                            <td>' . $shipment->pickup_address->city->name  . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Amount</strong></td>
                            <td>Rs ' . number_format($total_cod) . '</td>
                          </tr>
                        </tbody>
                      </table>
            ';

            $html .= $main_details;

            $html .= $shipment_details;

            $html .= '
                      <div class="mt-2 manual_form">
                        <div class="row align-items-end">
                          <div class="col">
                            <strong class="d-inline-block w-200">No. of Shipments Received:</strong>
                            <span class="d-inline-block w-200 line"></span>
                          </div>

                          <div class="col text-right">
                            <div class="d-inline-block text-center mt-2">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Client Signature</strong>
                            </div>
                          </div>
                        </div>

                        <strong class="d-block text-center mt-2">For Office Use</strong>

                        <hr>

                        <div class="row mt-2">
                          <div class="col">
                            <div>
                              <strong class="d-inline-block w-200">Rider Name:</strong>
                              <span class="d-inline-block w-200 line"></span>
                            </div>

                            <div class="mt-2">
                              <strong class="d-inline-block w-200">Shipments picked at:</strong>
                              <span class="d-inline-block w-200 line"></span>
                            </div>
                          </div>
                        </div>

                        <div class="row mt-4">
                          <div class="col text-left">
                            <div class="d-inline-block text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>

                          <div class="col text-right">
                            <div class="d-inline-block text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Office Signature</strong>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col text-center mt-2">
                            <span class="d-block">Plot # 105, Mehran Town Sector 7 A Korangi, Karachi, Karachi City, Sindh 74900, Pakistan</span>
                            <span class="d-block">Phone: 0304-11-11-232 | Email: info@trax.pk | URL: www.trax.pk</span>
                          </div>
                        </div>
                      </div>
            ';
        }

        if (!$body_only) {
            $html .= '
                    </div>
            ';

            if ($user_type != 4) {
                $html .= '
                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                ';
            }

            $html .= '
                  </body>
                </html>
            ';
        }

        return $html;
    }
    public function print(Request $request) {
        $user_type = 3;
        if ($user_type) {
            return $this->view($request->id, $user_type);
        }
    }

}
