<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\ReceivingSheetShipment;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class ShipperReceivingSheetController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function index() {
      return view('client.shipment.receiving_sheet.index');
    }

    public function store(Request $request) {
      $shipment_ids = $request->input('shipment_ids');

      $pickup_address_id = 0;

      foreach ($shipment_ids as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        if ($shipment->user_id != session('user_id')) {
          return ['status' => 1, 'error' => 'One of the Shipment(s) doesn\'t belong to you'];
        }

        if ($pickup_address_id == 0) {
          $pickup_address_id = $shipment->pickup_address_id;
        }
        else {
          if ($pickup_address_id != $shipment->pickup_address_id) {
            return ['status' => 1, 'error' => 'Given Shipments Pickup Addresses are different from one another and cannot be added to the same Receiving Sheet'];
          }
        }
      }

      $receiving_sheet = new ReceivingSheet();

      $receiving_sheet->user_id = session('user_id');
      $receiving_sheet->status = 0;

      $receiving_sheet->save();

      $receiving_sheet_id = $receiving_sheet->id;

      foreach ($shipment_ids as $shipment_id) {
        $receiving_sheet_shipment = new ReceivingSheetShipment();

        $receiving_sheet_shipment->shipment_id = $shipment_id;
        $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;

        $receiving_sheet_shipment->save();
      }

      return ['status' => 0, 'success' => 'Receiving Sheet has been created', 'receiving_sheet_id' => $receiving_sheet_id];
    }

    public function list() {
      $shipments = Shipment::join('booking_types AS bt', 'shipments.booking_type_id', '=', 'bt.id')
      ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
      ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
      ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
      ->leftjoin('receiving_sheet_shipments as rss', 'shipments.id', '=', 'rss.shipment_id')
      ->leftjoin('receiving_sheets AS rs', 'rss.receiving_sheet_id', '=', 'rs.id')
      ->select('shipments.id', 'shipments.tracking_number', 'shipments.order_id', 'bt.booking_type AS service_type', 'oc.name AS origin_city', 'dc.name AS destination_city', 'shipments.created_at AS booking_date', 'rs.id AS receiving_sheet')
      ->where('shipments.user_id', session('user_id'))
      ->where('shipments.shipper_status_id', 1)
      ->where(function ($query) {
        $query->whereNull('rs.status')->orWhere('rs.status', 0);
      });

      return Datatables::of($shipments)
      ->editColumn('booking_date', function($shipment) {
        return Carbon::parse($shipment->booking_date)->format('d/m/Y H:i A');
      })
      ->editColumn('receiving_sheet', function($shipment) {
        if ($shipment->receiving_sheet) {
          return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle id">' . str_pad($shipment->receiving_sheet, 12, "0", STR_PAD_LEFT) . '</span></button>';
        }
        else {
          return '';
        }
      })
      ->addColumn('action', function($shipment) {
        $dropdown = '
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
        ';

        if ($shipment->receiving_sheet) {
          $dropdown .= '<button type="button" class="dropdown-item void"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Void</div></button>';
        }
        else {
          $dropdown .= '<button type="button" class="dropdown-item add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Add</div></button>';
        }

        $dropdown .= '
                </div>
            </div>
        ';

        return $dropdown;
      })
      ->filterColumn('receiving_sheet', function($query, $keyword) {
        $keyword = intval($keyword);

        if ($keyword != 0) {
          $query->where('rs.id', '=', $keyword);
        }
        else {
          $query->whereNotNull('rs.id');
        }
      })
      ->make(true);
    }

    public function all() {
      return ReceivingSheet::where('user_id', session('user_id'))->where('status', 0)->select('id')->get();
    }

    public function add(Request $request) {
      $shipment = Shipment::find($request->input('shipment_id'));

      if ($shipment->user_id == session('user_id')) {
        $receiving_sheet = ReceivingSheet::find($request->input('receiving_sheet_id'));

        if ($receiving_sheet->user_id == session('user_id')) {
          if ($receiving_sheet->status == 0) {
            $first_receiving_sheet_shipment = ReceivingSheetShipment::where('receiving_sheet_id', $request->input('receiving_sheet_id'))->first();

            if ($first_receiving_sheet_shipment) {
              $first_shipment = Shipment::find($first_receiving_sheet_shipment->shipment_id);

              if ($shipment->pickup_address_id == $first_shipment->pickup_address_id) {
                $receiving_sheet_shipment = new ReceivingSheetShipment();

                $receiving_sheet_shipment->shipment_id = $request->input('shipment_id');
                $receiving_sheet_shipment->receiving_sheet_id = $request->input('receiving_sheet_id');

                $receiving_sheet_shipment->save();

                return ['status' => 0, 'success' => 'Shipment has been Added to the Receiving Sheet'];
              }
              else {
                return ['status' => 1, 'error' => 'Given Shipment\'s Pickup Address is different from the other Shipments of the selected Receiving Sheet'];
              }
            }
            else {
              $receiving_sheet_shipment = new ReceivingSheetShipment();

              $receiving_sheet_shipment->shipment_id = $request->input('shipment_id');
              $receiving_sheet_shipment->receiving_sheet_id = $request->input('receiving_sheet_id');

              $receiving_sheet_shipment->save();

              return ['status' => 0, 'success' => 'Shipment has been Added to the Receiving Sheet'];
            }
          }
          else {
            return ['status' => 1, 'error' => 'This Shipment is already Received by Trax'];
          }
        }
        else {
          return ['status' => 1, 'error' => 'This Receving Sheet doesn\'t belong to you'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'This Shipment doesn\'t belong to you'];
      }
    }

    public function void(Request $request) {
      $shipment = Shipment::find($request->input('shipment_id'));

      if ($shipment->user_id == session('user_id')) {
        $receiving_sheet_shipment = ReceivingSheetShipment::find($request->input('shipment_id'));

        if ($receiving_sheet_shipment->receiving_sheet_id == $request->input('receiving_sheet_id')) {
          $receiving_sheet = ReceivingSheet::find($request->input('receiving_sheet_id'));

          if ($receiving_sheet->status == 0) {
            $receiving_sheet_shipment->delete();

            if (!ReceivingSheetShipment::where('receiving_sheet_id', $request->input('receiving_sheet_id'))->exists()) {
              $receiving_sheet->status = 2;

              $receiving_sheet->save();
            }

            return ['status' => 0, 'success' => 'Shipment has been Voided'];
          }
          else {
            return ['status' => 1, 'error' => 'This Shipment is already Received by Trax'];
          }
        }
        else {
          return ['status' => 1, 'error' => 'Shipment isn\'t part of the given Receiving Sheet'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'This Shipment doesn\'t belong to you'];
      }
    }

    public function print(Request $request) {
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

      $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

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

                      .color {
                        color: #09262e !important;
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

      $receiving_sheet_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $request->id);

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
                            <td class="color primary"><strong>Description</strong></td>
                            <td class="color primary"><strong>Quantity</strong></td>
                            <td class="color primary"><strong>Destination</strong></td>
                            <td class="color primary"><strong>Amount</strong></td>
                          </tr>
        ';

        foreach ($receiving_sheet_shipments->get() as $receiving_sheet_shipment) {
          $total_shipments++;

          $shipment = Shipment::find($receiving_sheet_shipment->shipment_id);

          if ($shipment->booking_type_id < 3) {
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
                            <td rowspan=' . $number_of_items . ' class="align-middle">Rs ' . number_format($shipment->amount) . '</td>
                          </tr>
            ';
          }

          if ($shipment->booking_type_id == 1) {
            $shipment_details .= $shipment_details_row_start;

            $item = $shipment->items->first();

            $shipment_details .= '
                            <td>' . $item->product->product_name . '</td>
                            <td>' . $item->description . '</td>
                            <td>' . $item->quantity . '</td>
            ';

            $shipment_details .= $shipment_details_row_end;
          }
          else if ($shipment->booking_type_id == 2) {
            $shipment_details .= $shipment_details_row_start;

            $item = $shipment->items()->where('type', 0)->first();

            $shipment_details .= '
                            <td>' . $item->product->product_name . '</td>
                            <td>' . $item->description . '</td>
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
                            <td>' . $item->description . '</td>
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
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Receiving Sheet</strong></td>
                            <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Shipper</strong></td>
                            <td>' . Auth::user()->name . '</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($request->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 12, '0', STR_PAD_LEFT) . '</strong></span>
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
                        <div class="row justify-content-between align-items-end">
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

                        <div class="row justify-content-between align-items-end mt-2">
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

                          <div class="w-100"></div>

                          <div class="col text-left mt-4">
                            <div class="d-inline-block text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>

                          <div class="col text-right mt-4">
                            <div class="d-inline-block text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Office Signature</strong>
                            </div>
                          </div>
                        </div>

                        <div class="text-center mt-2">
                          <span class="d-block">Plot # 4, BMCHS,Block 7/8, Adjacent to IBL Building Centre, Tipu Sultan Road, Karachi, Pakistan</span>
                          <span class="d-block">Phone: 03-111-555-065 | Email: info@trax.pk | URL: www.trax.pk</span>
                        </div>
                      </div>
        ';
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