<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\Shipment;
use App\Http\Models\City;
use App\Http\Models\ShippingMode;
use App\Http\Models\TransportMode;
use App\Http\Models\TransportModeVendor;
use App\Http\Models\Admin\Admin;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\CargoConsignmentJunctionReceival;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminCargoController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function pending_index() {
      return view('admin.cargo.pending');
    }

    public function pending_list(Request $request) {
      $shipments = Shipment::join('booking_types as bt', 'shipments.booking_type_id', '=', 'bt.id')
      ->join('shipment_status as ss', 'shipments.shipper_status_id', '=', 'ss.id')
      ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
      ->join('users as u', 'shipments.user_id', '=', 'u.id')
      ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
      ->join('cities as dc', function($join) {
        $join->on('shipments.consignee_city_id', '=', 'dc.id')
        ->on('oc.hub_id', '!=', 'dc.hub_id');
      })
      ->join('shipping_modes as sm', 'shipments.shipping_mode_id', '=', 'sm.id')
      ->join('shipments_journey', function ($join) {
        $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
        ->on('shipments_journey.shipper_status_id', '=', DB::raw(2));
      })
      ->select('shipments.tracking_number', 'shipments.order_id', 'bt.booking_type as service_type', 'ss.name as status', 'oc.name as origin', 'dc.name as destination', 'u.name as shipper', 'shipments.amount', 'sm.mode as shipping_mode', 'shipments.created_at as booked_at', 'shipments_journey.created_at as arrival_at');

      $datatables = Datatables::of($shipments);

      if ($shipment_type = $request->get('shipment_type')) {
        if ($shipment_type == 0) {
          $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 36, 37]);
        }
        else if ($shipment_type == 1) {
          $datatables->where('shipments.shipper_status_id', 2);
        }
        else if ($shipment_type == 2) {
          $datatables->whereIn('shipments.shipper_status_id', [20, 30, 36, 37]);
        }
      }
      else {
        $datatables->whereIn('shipments.shipper_status_id', [2, 20, 30, 36, 37]);
      }

      return $datatables->make(true);
    }

    public function create_index() {
      return view('admin.cargo.create')->with('print', session('print'));
    }

    public function create_shipment_details(Request $request) {
      $shipment = Shipment::where('tracking_number', $request->tracking_number);

      if ($shipment->exists()) {
        $shipment = $shipment->first();

        if ($shipment->pickup_address->city->hub_id != $shipment->consignee_city->hub_id) {
          $hub = City::find($shipment->consignee_city->hub_id);

          if ($request->hub_id == 0 || $request->hub_id == $shipment->consignee_city->hub_id) {
            if (in_array($shipment->shipper_status_id, [2, 20, 30, 36, 37])) {
              $details = array();

              if ($request->cargo_type != 0) {
                if ($request->cargo_type == 1) {
                  if ($shipment->shipper_status_id != 2) {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Return Type while the Cargo is Normal Type'];
                  }
                }
                else {
                  if (!in_array($shipment->shipper_status_id, [20, 30, 36, 37])) {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is of Normal Type while the Cargo is Return Type'];
                  }
                }
              }
              else {
                if ($shipment->shipper_status_id == 2) {
                  $details['cargo_type'] = 1;
                }
                else {
                  $details['cargo_type'] = 2;
                }
              }

              $details['id'] = $shipment->id;
              $details['tracking_number'] = $shipment->tracking_number;
              $details['order_id'] = $shipment->order_id;
              $details['service_type'] = $shipment->booking_type->booking_type;
              $details['destination'] = $shipment->consignee_city->name;
              $details['amount'] = $shipment->amount;
              $details['shipping_mode'] = $shipment->shipping_mode->mode;

              $hub = City::find($shipment->consignee_city->hub_id);

              $details['hub']['id'] = $hub->id;
              $details['hub']['name'] = $hub->name;

              if ($request->hub_id == 0) {
                $shipments = Shipment::join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities as dc', function($join) {
                  $join->on('shipments.consignee_city_id', '=', 'dc.id')
                  ->on('oc.hub_id', '!=', 'dc.hub_id');
                })
                ->select(DB::raw('count(shipments.id) as count'))
                ->whereIn('shipments.shipper_status_id', [2, 20, 30, 36, 37])
                ->where('dc.hub_id', $hub->id)
                ->first();

                $details['total'] = $shipments->count;
              }

              return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
            }
            else {
              return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
          }
          else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to another Hub'];
          }
        }
        else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment belongs to same Origin and Destination Hub'];
          }
      }
      else {
        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
      }
    }

    public function create_consignment_details(Request $request) {
      $shipment = Shipment::find(current($request->shipment_ids));

      $origin = $shipment->pickup_address->city;

      $origin_details = array();

      $origin_details['id'] = $origin->id;
      $origin_details['name'] = $origin->name;

      $destination = $shipment->consignee_city;

      $destination_details = array();

      $destination_details['id'] = $destination->id;
      $destination_details['name'] = $destination->name;

      $details = array();

      $details['junctions'] = City::select(['id', 'name'])->where('hub', 1)->where('status', 1)->get();

      $hub = City::find($destination->hub_id);

      $details['hub']['id'] = $hub->id;
      $details['hub']['name'] = $hub->name;

      $details['shipping_modes'] = ShippingMode::where('id', '!=', 4)->get();

      $details['transport_modes'] = TransportMode::all();

      $details['transport_mode_vendors'] = TransportModeVendor::get()->groupBy('transport_mode_id');

      $sender = Auth::user();

      $details['sender']['id'] = $sender->id;
      $details['sender']['username'] = $sender->username;

      $details['receivers'] = Admin::all(['id', 'username']);

      foreach ($request->shipment_ids as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        if ($shipment->pickup_address->city->id != $origin_details['id']) {
          $origin_details['id'] = 1;
          $origin_details['name'] = 'Multiple';
        }

        if ($shipment->consignee_city->id != $destination_details['id']) {
          $destination_details['id'] = 1;
          $destination_details['name'] = 'Multiple';
        }
      }

      if ($request->cargo_type == 1) {
        $details['origin'] = $origin_details;
        $details['destination'] = $destination_details;
      }
      else {
        $details['origin'] = $destination_details;
        $details['destination'] = $origin_details;
      }

      return $details;
    }

    public function create_store(Request $request) {
      $cargo_consignment = new CargoConsignment();

      $cargo_consignment->origin_city_id = $request->input('origin_city_id');
      $cargo_consignment->destination_city_id = $request->input('destination_city_id');
      $cargo_consignment->junction_city_1_id = $request->input('junction_1');
      $cargo_consignment->junction_city_2_id = $request->input('junction_2');
      $cargo_consignment->hub_id = $request->input('hub_id');
      $cargo_consignment->seal_number = $request->input('seal_number');
      $cargo_consignment->shipping_mode_id = $request->input('shipping_mode');
      $cargo_consignment->transport_mode_id = $request->input('transport_mode');

      if ($request->input('transport_mode_vendor') == 0) {
        $transport_mode_vendor = new TransportModeVendor();

        $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
        $transport_mode_vendor->name = $request->input('vendor_name');

        $transport_mode_vendor->save();

        $cargo_consignment->transport_mode_vendor_id = $transport_mode_vendor->id;
      }
      else {
        $cargo_consignment->transport_mode_vendor_id = $request->input('transport_mode_vendor');
      }

      $shipments = 0;
      $shipments_weight = 0;

      $shipment_ids = explode(',', $request->input('shipment_ids'));

      foreach ($shipment_ids as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        $shipments++;
        $shipments_weight += $shipment->actual_weight;
      }

      $cargo_consignment->shipments = $shipments;
      $cargo_consignment->shipments_weight = $shipments_weight;

      $cargo_consignment->actual_weight = $request->input('actual_weight');
      $cargo_consignment->sender_id = $request->input('sender_id');

      if ($request->filled('receiver_id')) {
        $cargo_consignment->receiver_id = $request->input('receiver_id');
      }

      $cargo_consignment->type = $request->input('cargo_type');

      $cargo_consignment->status_id = 1;

      $cargo_consignment->save();

      $id = $cargo_consignment->id;

      foreach ($shipment_ids as $shipment_id) {
        $cargo_consignment_shipment = new CargoConsignmentShipment();

        $cargo_consignment_shipment->cargo_consignment_id = $id;
        $cargo_consignment_shipment->shipment_id = $shipment_id;

        $cargo_consignment_shipment->save();

        $shipment = Shipment::find($shipment_id);

        $shipper_status_id = NULL;
        $consignee_status_id = NULL;

        if ($request->input('cargo_type') == 1) {
          $shipper_status_id = 3;
          $consignee_status_id = 3;
        }
        else {
          if ($shipment->booking_type_id == 1) {
            $shipper_status_id = 21;
            $consignee_status_id = 21;
          }
          else if ($shipment->booking_type_id == 2) {
            $shipper_status_id = 26;
            $consignee_status_id = 26;
          }
          else {
            $shipper_status_id = 32;
            $consignee_status_id = 32;
          }
        }

        $shipment->shipper_status_id = $shipper_status_id;
        $shipment->consignee_status_id = $consignee_status_id;

        $shipment->save();

        ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, 'Shipment is in Transit!', NULL, Auth::id(), $cargo_consignment->id, $cargo_consignment->builty_number);
      }

      if ($request->filled('submit_and_print')) {
        $print = $id;
      }
      else {
        $print = FALSE;
      }

      return redirect()->route('admin.cargo.in_transit.index')->with(['success' => 'Cargo Booked with Number: ' . $id, 'print' => $print]);
    }

    public function in_transit_index() {
      return view('admin.cargo.in_transit');
    }

    public function in_transit_list(Request $request) {
      $cargo_consignments = CargoConsignment::join('cities as oc', 'cargo_consignments.origin_city_id', '=', 'oc.id')
      ->join('cities as dc', 'cargo_consignments.destination_city_id', '=', 'dc.id')
      ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
      ->join('shipping_modes as sm', 'cargo_consignments.shipping_mode_id', '=', 'sm.id')
      ->join('admins as a', 'cargo_consignments.sender_id', '=', 'a.id')
      ->join('cargo_consignment_status as ccs', 'cargo_consignments.status_id', '=', 'ccs.id')
      ->select('cargo_consignments.id' , 'oc.name as origin', 'dc.name as destination', 'hc.name as hub', 'cargo_consignments.shipments', 'sm.mode as shipping_mode', 'cargo_consignments.created_at as transit_at', 'a.name as transitted_by', 'ccs.name as status')
      ->whereIn('cargo_consignments.status_id', [1, 2]);

      $datatables = Datatables::of($cargo_consignments)
      ->editColumn('transit_at', function($cargo_consignment) {
        return Carbon::parse($cargo_consignment->transit_at)->format('d/m/Y H:i A');
      })
      ->addColumn('action', function($cargo_consignment) {
        return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Print</div></button>
                    <button type="button" class="dropdown-item add_forwarding_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Forwarding Details</div></button>
                    <button type="button" class="dropdown-item view_forwarding_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Forwarding Details</div></button>
                    <button type="button" class="dropdown-item launch_dispute"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Launch Dispute</div></button>
                    <button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>
                  </div>
                </div>
        ';
      });

      if ($cargo_type = $request->get('cargo_type')) {
        if ($cargo_type != 0) {
          $datatables->where('cargo_consignments.type', $cargo_type);
        }
      }

      if ($tracking_number = $request->get('tracking_number')) {
        $datatables->join('cargo_consignment_shipments as cssh', 'cargo_consignments.id', '=', 'cssh.cargo_consignment_id')
        ->join('shipments as s', 'cssh.shipment_id', '=', 's.id')
        ->where('s.tracking_number', '=', $tracking_number);
      }

      if ($seal_number = $request->get('seal_number')) {
        $datatables->where('cargo_consignments.seal_number', '=', $seal_number);
      }

      return $datatables->make(true);
    }

    public function in_transit_print(Request $request) {
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

      $cargo_consignment = CargoConsignment::find($request->id);

      $sender = $cargo_consignment->sender;
      $receiver = ($cargo_consignment->receiver_id) ? $cargo_consignment->receiver : NULL;

      $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Cargo Slip & Checklist</title>

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

                      .cargo_checklist {
                        page-break-before: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="p-1 cargo_slip">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Slip</strong></td>
                              <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination City</strong></td>
                              <td>' . $cargo_consignment->destination_city->name . '</td>
                              <td rowspan="8" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($cargo_consignment->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . $cargo_consignment->id . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . Carbon::parse($cargo_consignment->created_at)->format('d/m/Y H:i A') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipping Mode</strong></td>
                              <td>' . $cargo_consignment->shipping_mode->mode . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transport Mode</strong></td>
                              <td>' . $cargo_consignment->transport_mode->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Vendor</strong></td>
                              <td>' . $cargo_consignment->transport_mode_vendor->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Builty Number</strong></td>
                              <td>' . $cargo_consignment->builty_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td colspan="2" class="color primary"><strong>Sender Information</strong></td>
                              <td colspan="2" class="color primary"><strong>Receiver Information</strong></td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Name</strong></td>
                              <td>' . $sender['name'] . '</td>
                              <td class="color secondary"><strong>Name</strong></td>
                              <td>' . (($receiver) ? $receiver['name'] : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Department</strong></td>
                              <td>' . $sender['department'] . '</td>
                              <td class="color secondary"><strong>Department</strong></td>
                              <td>' . (($receiver) ? $receiver['department'] : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Phone No.</strong></td>
                              <td>-</td>
                              <td class="color secondary"><strong>Phone No.</strong></td>
                              <td>-</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>City</strong></td>
                              <td>-</td>
                              <td class="color secondary"><strong>City</strong></td>
                              <td>-</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>Route Information</strong></td>
                            </tr>
                            <tr>
                              <td>' . $cargo_consignment->origin_city->name . ' - ' . $cargo_consignment->junction_city_1->name . ' - ' . $cargo_consignment->junction_city_2->name . ' - ' . $cargo_consignment->destination_city->name . '</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div class="p-1 cargo_checklist">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Checklist</strong></td>
                              <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Origin City</strong></td>
                              <td>' . $cargo_consignment->origin_city->name . '</td>
                              <td rowspan="5" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($cargo_consignment->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . $cargo_consignment->id . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination City</strong></td>
                              <td>' . $cargo_consignment->destination_city->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . Carbon::parse($cargo_consignment->created_at)->format('d/m/Y H:i A') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Consignee Name</strong></td>
                              <td class="color primary"><strong>Consignee Phone</strong></td>
                              <td class="color primary"><strong>Consignee City</strong></td>
                              <td class="color primary"><strong>Amount</strong></td>
      ';

      $serial_number = 1;

      foreach ($cargo_consignment->cargo_consignment_shipments as $cargo_consignment_shipment) {
        $shipment = $cargo_consignment_shipment->shipment;


        $html .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $shipment->consignee_name . '</td>
                              <td>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->amount . '</td>
                            </tr>
        ';

        $serial_number++;
      }

      $html .= '
                          </tbody>
                        </table>
                      </div>
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

    public function in_transit_junctions(Request $request) {
      return City::select(['id', 'name'])->where('hub', 1)->where('status', 1)->get();
    }

    public function in_transit_details(Request $request) {
      $cargo_consignment = CargoConsignment::where('seal_number', $request->seal_number);

      if ($cargo_consignment->exists()) {
        $cargo_consignment = $cargo_consignment->first();

        if (in_array($cargo_consignment->status_id, [1, 2])) {
          $details = array();

          $details['cargo_number'] = $cargo_consignment->id;
          $details['origin'] = $cargo_consignment->origin_city->name;
          $details['destination'] = $cargo_consignment->destination_city->name;
          $details['hub'] = $cargo_consignment->hub->name;
          $details['seal_number'] = $cargo_consignment->seal_number;

          return ['status' => 0, 'success' => 'Cargo has been scanned', 'details' => $details];
        }
        else {
          return ['status' => 1, 'error' => 'Given Seal Number\'s Cargo has already been modified'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Cargo with given Seal Number is present'];
      }
    }

    public function in_transit_receive_at_link(Request $request) {
      foreach ($request->cargo_consignment_ids as $cargo_consignment_id) {
        $cargo_consignment_junction_receival = new CargoConsignmentJunctionReceival();

        $cargo_consignment_junction_receival->cargo_consignment_id = $cargo_consignment_id;
        $cargo_consignment_junction_receival->junction_id = $request->junction;
        $cargo_consignment_junction_receival->receiver_id = Auth::id();

        $cargo_consignment_junction_receival->save();
      }

      return ['status' => 0, 'success' => 'Cargo(s) has been received at Junction'];
    }

    public function in_transit_forwarding_details(Request $request) {
      $cargo_consignment = CargoConsignment::find($request->cargo_consignment_id);

      $details = array();

      $details['cargo_consignment']['seal_number'] = $cargo_consignment->seal_number;
      $details['cargo_consignment']['builty_number'] = $cargo_consignment->builty_number;
      $details['cargo_consignment']['weight_charges_per_kg'] = $cargo_consignment->weight_charges_per_kg;
      $details['cargo_consignment']['extra_charges'] = $cargo_consignment->extra_charges;
      $details['cargo_consignment']['total_weight_charges'] = $cargo_consignment->total_weight_charges;
      $details['cargo_consignment']['sender_username'] = Admin::find($cargo_consignment->sender_id)->username;

      if ($request->add) {
        $details['cargo_consignment']['junction_city_1_id'] = $cargo_consignment->junction_city_1_id;
        $details['cargo_consignment']['junction_city_2_id'] = $cargo_consignment->junction_city_2_id;
        $details['cargo_consignment']['expected_arrival_date'] = $cargo_consignment->expected_arrival_date;
        $details['cargo_consignment']['shipping_mode_id'] = $cargo_consignment->shipping_mode_id;
        $details['cargo_consignment']['transport_mode_id'] = $cargo_consignment->transport_mode_id;
        $details['cargo_consignment']['transport_mode_vendor_id'] = $cargo_consignment->transport_mode_vendor_id;
        $details['cargo_consignment']['receiver_id'] = $cargo_consignment->receiver_id;

        $details['junctions'] = City::select(['id', 'name'])->where('hub', 1)->where('status', 1)->get();

        $details['shipping_modes'] = ShippingMode::where('id', '!=', 4)->get();

        $details['transport_modes'] = TransportMode::all();

        $details['transport_mode_vendors'] = TransportModeVendor::get()->groupBy('transport_mode_id');

        $details['receivers'] = Admin::all(['id', 'username']);
      }
      else {
        $details['cargo_consignment']['junction_city_1'] = $cargo_consignment->junction_city_1->name;
        $details['cargo_consignment']['junction_city_2'] = $cargo_consignment->junction_city_2->name;
        $details['cargo_consignment']['expected_arrival_date'] = Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y');
        $details['cargo_consignment']['shipping_mode'] = $cargo_consignment->shipping_mode->mode;
        $details['cargo_consignment']['transport_mode'] = $cargo_consignment->transport_mode->name;
        $details['cargo_consignment']['transport_mode_vendor'] = $cargo_consignment->transport_mode_vendor->name;
        $details['cargo_consignment']['shipments_weight'] = $cargo_consignment->shipments_weight;
        $details['cargo_consignment']['actual_weight'] = $cargo_consignment->actual_weight;
        $details['cargo_consignment']['receiver_username'] = ($cargo_consignment->receiver_id) ? Admin::find($cargo_consignment->receiver_id)->username : '';
      }

      return $details;
    }

    public function in_transit_update(Request $request) {
      $cargo_consignment = CargoConsignment::find($request->input('cargo_consignment_id'));

      $cargo_consignment->junction_city_1_id = $request->input('junction_1');
      $cargo_consignment->junction_city_2_id = $request->input('junction_2');
      $cargo_consignment->seal_number = $request->input('seal_number');
      $cargo_consignment->shipping_mode_id = $request->input('shipping_mode');
      $cargo_consignment->transport_mode_id = $request->input('transport_mode');

      if ($request->input('transport_mode_vendor') == 0) {
        $transport_mode_vendor = new TransportModeVendor();

        $transport_mode_vendor->transport_mode_id = $request->input('transport_mode');
        $transport_mode_vendor->name = $request->input('vendor_name');

        $transport_mode_vendor->save();

        $cargo_consignment->transport_mode_vendor_id = $transport_mode_vendor->id;
      }
      else {
        $cargo_consignment->transport_mode_vendor_id = $request->input('transport_mode_vendor');
      }

      $cargo_consignment->builty_number = $request->input('builty_number');
      $cargo_consignment->expected_arrival_date = $request->input('expected_arrival_date_formatted');
      $cargo_consignment->weight_charges_per_kg = $request->input('weight_charges_per_kg');
      $cargo_consignment->extra_charges = $request->input('extra_charges');
      $cargo_consignment->total_weight_charges = $request->input('total_weight_charges');

      if ($request->filled('receiver_id')) {
        $cargo_consignment->receiver_id = $request->input('receiver_id');
      }
      else {
        $cargo_consignment->receiver_id = NULL;
      }

      $cargo_consignment->save();

      return redirect()->back()->with('success', 'Cargo Number #' . $request->input('cargo_consignment_id') . ' has been Updated');
    }

    public function in_transit_receive(Request $request) {
      if ($request->has('cargo_number')) {
        $cargo_consignment = CargoConsignment::find($request->get('cargo_number'));

        if ($cargo_consignment) {
          if (in_array($cargo_consignment->status_id, [1, 2])) {
            return redirect()->route('admin.cargo.receive.index')->with('cargo_consignment_id', $cargo_consignment->id);
          }
          else {
            return back()->withErrors('Given Cargo Number has already been modified!');
          }
        }
        else {
          return back()->withErrors('Invalid Cargo Number!');
        }
      }
      else {
        return back()->withErrors('Missing Cargo Number!');
      }
    }

    public function receive_index() {
      if (session('cargo_consignment_id')) {
        $total = CargoConsignmentShipment::where('cargo_consignment_id', session('cargo_consignment_id'))->count();

        return view('admin.cargo.receive')->with('total', $total);
      }
      else {
        return redirect()->route('admin.cargo.in_transit.index')->withErrors('Kindly reselect a Cargo Number!');
      }
    }

    public function receive_shipment_details(Request $request) {
      $shipment = Shipment::where('tracking_number', $request->tracking_number);

      if ($shipment->exists()) {
        $shipment = $shipment->first();

        $cargo_consignment_shipment = CargoConsignmentShipment::where('shipment_id', $shipment->id);

        if ($cargo_consignment_shipment->exists()) {
          $cargo_consignment_shipment = $cargo_consignment_shipment->first();

          if ($cargo_consignment_shipment->cargo_consignment_id == $request->cargo_consignment_id) {
            $details = array();

            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['origin'] = $shipment->pickup_address->city->name;
            $details['destination'] = $shipment->consignee_city->name;
            $details['hub'] = City::find($shipment->consignee_city->hub_id)->name;
            $details['consignee'] = $shipment->consignee_name;
            $details['amount'] = $shipment->amount;
            $details['shipping_mode'] = $shipment->shipping_mode->mode;
            $details['service_type'] = $shipment->booking_type->booking_type;

            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
          }
          else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to current Cargo Number'];
          }
        }
        else {
          return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is not in any Cargo'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
      }
    }

    public function receive_short_received(Request $request) {
      $cargo_consignment_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $request->input('cargo_consignment_id'));

      if ($cargo_consignment_shipments->count() != count($request->input('shipment_ids'))) {
        $cargo_consignment_shipment_ids = $cargo_consignment_shipments->pluck('shipment_id')->toArray();

        $short_shipment_ids = array_diff($cargo_consignment_shipment_ids, $request->input('shipment_ids'));

        $short_shipments = array();

        foreach ($short_shipment_ids as $short_shipment_id) {
          $shipment = Shipment::find($short_shipment_id);

          $short_shipments[] = $shipment->tracking_number;
        }

        return ['status' => 0, 'success' => 'Shipments found Short Received', 'short_received' => $short_shipments];
      }
      else {
        return ['status' => 0, 'success' => 'No Short Received Shipments', 'short_received' => FALSE];
      }

      $cargo_consignment_id = PickupRequest::find($request->input('cargo_consignment_id'));

      $receiving_sheets = ReceivingSheet::where('user_id', $pickup_request->shipper_id)->where('status', 1);

      $short_shipments = array();

      if ($receiving_sheets->exists()) {
        $receiving_sheets = $receiving_sheets->get();

        foreach ($receiving_sheets as $receiving_sheet) {
          foreach ($receiving_sheet->receiving_sheet_shipments as $receiving_sheet_shipment) {
            $shipment = $receiving_sheet_shipment->shipment;

            if ($shipment->shipper_status_id == 1 && $pickup_request->pickup_address_id == $shipment->pickup_address_id) {
              $short_shipments[str_pad($receiving_sheet->id, 12, '0', STR_PAD_LEFT)][] = $shipment->tracking_number;
            }
          }
        }

        if (!empty($short_shipments)) {
          return ['status' => 0, 'success' => 'Shipments found Short Received', 'short_received' => $short_shipments];
        }
        else {
          return ['status' => 0, 'success' => 'No Short Received Shipments', 'short_received' => FALSE];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Receiving Sheet exists for given Pickup Request'];
      }
    }

    public function receive_store(Request $request) {
      $cargo_consignment_id = $request->cargo_consignment_id;

      $shipment_ids = explode(',', $request->shipment_ids);

      $cargo_consignment = CargoConsignment::find($cargo_consignment_id);

      $cargo_consignment->received_shipments = count($shipment_ids);

      if ($request->short_received) {
        $cargo_consignment->status_id = 4;
      }
      else {
        $cargo_consignment->status_id = 3;
      }

      $cargo_consignment->save();

      foreach ($shipment_ids as $shipment_id) {
        $cargo_consignment_shipment = CargoConsignmentShipment::where('cargo_consignment_id', $cargo_consignment_id)->where('shipment_id', $shipment_id)->first();

        $cargo_consignment_shipment->status = 1;

        $cargo_consignment_shipment->save();

        $shipment = Shipment::find($shipment_id);

        $shipper_status_id = NULL;
        $consignee_status_id = NULL;

        if ($cargo_consignment->type == 1) {
          $shipper_status_id = 4;
          $consignee_status_id = 4;
        }
        else {
          if ($shipment->booking_type_id == 1) {
            $shipper_status_id = 22;
            $consignee_status_id = 22;
          }
          else if ($shipment->booking_type_id == 2) {
            $shipper_status_id = 27;
            $consignee_status_id = 27;
          }
          else {
            $shipper_status_id = 33;
            $consignee_status_id = 33;
          }
        }

        $shipment->shipper_status_id = $shipper_status_id;
        $shipment->consignee_status_id = $consignee_status_id;

        $shipment->save();

        ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, NULL, 'Shipment has Arrived at Destination Centre!', NULL, Auth::id());
      }

      return redirect()->route('admin.cargo.in_transit.index')->with('success', 'Cargo No# ' . $cargo_consignment_id . ' has been Received');
    }

}