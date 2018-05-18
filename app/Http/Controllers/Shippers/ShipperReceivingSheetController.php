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
      $this->middleware('auth');
    }

    public function index() {
      return view('client.shipment.receiving_sheet.index');
    }

    public function store(Request $request) {
      $shipment_ids = $request->input('shipment_ids');

      $pickup_address_id = 0;

      foreach ($shipment_ids as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        if ($shipment->user_id != Auth::id()) {
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

      $receiving_sheet->user_id = Auth::id();
      $receiving_sheet->status = 0;

      $receiving_sheet->save();

      $receiving_sheet_id = $receiving_sheet->id;

      foreach ($shipment_ids as $shipment_id) {
        $receiving_sheet_shipment = new ReceivingSheetShipment();

        $receiving_sheet_shipment->shipment_id = $shipment_id;
        $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;

        $receiving_sheet_shipment->save();
      }

      return ['status' => 0, 'success' => 'Receiving Sheet has been created'];
    }

    public function list() {
      $shipments = Shipment::join('booking_types AS bt', 'shipments.booking_type_id', '=', 'bt.id')
      ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
      ->join('city_infos AS oc', 'usi.city_code', '=', 'oc.city_code')
      ->join('city_infos AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
      ->leftjoin('receiving_sheet_shipments as rss', 'shipments.id', '=', 'rss.shipment_id')
      ->leftjoin('receiving_sheets AS rs', 'rss.receiving_sheet_id', '=', 'rs.id')
      ->select('shipments.id', 'shipments.tracking_number', 'shipments.order_id', 'bt.booking_type AS service_type', 'oc.city_name AS origin_city', 'dc.city_name AS destination_city', 'shipments.created_at AS booking_date', 'rs.id AS receiving_sheet')
      ->where('shipments.user_id', Auth::id())
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
        if ($shipment->receiving_sheet) {
          return '<button class="btn btn-sm btn-danger void">Void</button>';
        }
        else {
          return '<button class="btn btn-sm btn-primary add">Add</button>';
        }
      })->make(true);
    }

    public function all() {
      return ReceivingSheet::where('user_id', Auth::id())->where('status', 0)->select('id')->get();
    }

    public function add(Request $request) {
      $shipment = Shipment::find($request->input('shipment_id'));

      if ($shipment->user_id == Auth::id()) {
        $receiving_sheet = ReceivingSheet::find($request->input('receiving_sheet_id'));

        if ($receiving_sheet->user_id == Auth::id()) {
          if ($receiving_sheet->status == 0) {
            $first_receiving_sheet_shipment = ReceivingSheetShipment::where('receiving_sheet_id', $request->input('receiving_sheet_id'))->first();

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

      if ($shipment->user_id == Auth::id()) {
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

                    <title>Air Waybill</title>

                    <style>
                      body {
                        background: none !important;
                        font-size: 0.9rem !important;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        width: 12.5% !important;
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
                    </style>
                  </head>
                  <body>
                    <div class="p-1">
      ';

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