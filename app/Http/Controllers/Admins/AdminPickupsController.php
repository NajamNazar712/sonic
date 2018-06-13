<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;

use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\PickupRequest;
use App\Http\Models\PickupNote;
use App\Http\Models\PickupNoteRequest;
use App\Http\Models\PickupNoteStatus;
use App\Http\Models\PickupNotesJourney;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminPickupsController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    static public function generate($shipment_id) {
      $shipment = Shipment::find($shipment_id);

      $pickup_request = PickupRequest::whereDate('pickup_date', $shipment->pickup_date)->where('pickup_address_id', $shipment->pickup_address_id)->where('status', 0);

      if ($pickup_request->exists()) {
        $pickup_request = $pickup_request->orderBy('id', 'DESC')->first();

        $bookings = $pickup_request->bookings + 1;
        $total_estimated_weight = $pickup_request->total_estimated_weight + $shipment->estimated_weight;

        if ($total_estimated_weight < 10) {
          $pickup_type = 0;
        }
        else {
          $pickup_type = 1;
        }

        $pickup_request->bookings = $bookings;
        $pickup_request->total_estimated_weight = $total_estimated_weight;
        $pickup_request->pickup_type = $pickup_type;

        $pickup_request->save();
      }
      else {
        $pickup_request = new PickupRequest();

        $pickup_request->shipper_id = Auth::id();
        $pickup_request->pickup_address_id = $shipment->pickup_address_id;
        $pickup_request->bookings = 1;
        $pickup_request->total_estimated_weight = $shipment->estimated_weight;

        if ($shipment->estimated_weight < 10) {
          $pickup_request->pickup_type = 0;
        }
        else {
          $pickup_request->pickup_type = 1;
        }

        $pickup_request->pickup_date = $shipment->pickup_date;
        $pickup_request->status = 0;

        $pickup_request->save();
      }
    }

    public function pending_index() {
      $riders = Rider::all(['id', 'name']);

      return view('admin.pickups.pending.index')->with(['riders' => $riders]);
    }

    public function pending_list(Request $request) {
      $pickup_requests = PickupRequest::join('users as u', 'pickup_requests.shipper_id', '=', 'u.id')
      ->join('user_shipping_infos as usi', 'pickup_requests.pickup_address_id', '=', 'usi.id')
      ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
      ->select('pickup_requests.id', 'pickup_requests.created_at as requested_at', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'pickup_requests.bookings', 'pickup_requests.pending_bookings', 'pickup_requests.total_estimated_weight', 'pickup_requests.pickup_type', 'pickup_requests.pickup_date')
      ->where('pickup_requests.status', 0);

      $datatables = Datatables::of($pickup_requests)
      ->editColumn('requested_at', function($pickup_request) {
        return Carbon::parse($pickup_request->requested_at)->format('d/m/Y H:i A');
      })
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup_request) {
        return ($pickup_request->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('pickup_date', function($pickup_request) {
        return Carbon::parse($pickup_request->pickup_date)->format('d/m/Y');
      })
      ->addColumn('action', function($receiving_sheet) {
        return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>
                  </div>
                </div>
        ';
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickup_requests.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickup_requests.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      });

      return $datatables->make(true);
    }

    public function pending_assign(Request $request) {
      $pickup_request_ids = $request->input('pickup_request_ids');
      $rider_id = $request->input('rider_id');

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        if ($pickup_request->status != 0) {
          return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        }
      }

      $pickups = 0;
      $bookings = 0;
      $total_estimated_weight = 0;

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request->status = 1;

        $pickup_request->save();

        $pickups++;
        $bookings += $pickup_request->bookings;
        $total_estimated_weight += $pickup_request->total_estimated_weight;
      }

      $existing_pickup_note = FALSE;

      $pickup_note = PickupNote::where('rider_id', $rider_id)->where('status_id', '=', 1);

      if ($pickup_note->exists()) {
        $pickup_note = $pickup_note->first();

        $pickup_note->pickups += $pickups;
        $pickup_note->bookings += $bookings;

        $pickup_note->total_estimated_weight += $total_estimated_weight;

        if ($total_estimated_weight < 10) {
          $pickup_note->pickup_type = 0;
        }
        else {
          $pickup_note->pickup_type = 1;
        }

        $pickup_note->save();

        $pickup_note_id = $pickup_note->id;

        PickupNotesJourneyController::add($pickup_note_id, $pickup_note->status_id, 'Pickup Request(s) has been added into the Pickup Note!', Auth::id());
      }
      else {
        $pickup_note = new PickupNote();

        $pickup_note->rider_id = $rider_id;
        $pickup_note->pickups = $pickups;
        $pickup_note->bookings = $bookings;
        $pickup_note->total_estimated_weight = $total_estimated_weight;

        if ($total_estimated_weight < 10) {
          $pickup_note->pickup_type = 0;
        }
        else {
          $pickup_note->pickup_type = 1;
        }

        $pickup_note->assigned_by_user_id = Auth::id();
        $pickup_note->status_id = 1;

        $pickup_note->save();

        $pickup_note_id = $pickup_note->id;

        PickupNotesJourneyController::add($pickup_note_id, 0, 'Pickup Note has been Created!', Auth::id());
      }

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_note_request = new PickupNoteRequest();

        $pickup_note_request->pickup_note_id = $pickup_note_id;
        $pickup_note_request->pickup_request_id = $pickup_request_id;

        $pickup_note_request->save();
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Assigned to the Rider'];
    }

    public function pending_multiple_cancel(Request $request) {
      $pickup_request_ids = $request->input('pickup_request_ids');

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        if ($pickup_request->status != 0) {
          return ['status' => 1, 'error' => 'One of the Pickup Request(s) has already been modified'];
        }
      }

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_request = PickupRequest::find($pickup_request_id);

        $pickup_request->status = 3;

        $pickup_request->save();
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Cancelled'];
    }

    public function pending_cancel(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 0) {
        $pickup_request->status = 3;

        $pickup_request->save();

        return ['status' => 0, 'success' => 'Pickup Request has been Cancelled'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup Request has already been modified'];
      }
    }

    public function assigned_index() {
      return view('admin.pickups.assigned.index');
    }

    public function assigned_list(Request $request) {
      $pickup_notes = PickupNote::join('riders as r', 'pickup_notes.rider_id', '=', 'r.id')
      ->join('rider_categories as rc', 'r.rider_category_id', '=', 'rc.id')
      ->join('routes as ro', 'r.route_id', '=', 'ro.id')
      ->join('cities as c', 'r.city_id', '=', 'c.id')
      ->join('admins as a', 'pickup_notes.assigned_by_user_id', '=', 'a.id')
      ->join('pickup_note_statuses as pns', 'pickup_notes.status_id', '=', 'pns.id')
      ->select('pickup_notes.id', 'r.name as rider_name', 'r.phone as rider_phone', 'rc.name as rider_type', 'ro.code as route_code', 'ro.start as route_start', 'ro.end as route_end', 'c.name as city', 'pickup_notes.rider_id', 'pickup_notes.pickups', 'pickup_notes.bookings', 'pickup_notes.total_estimated_weight', 'pickup_notes.pickup_type', 'pickup_notes.created_at as assigned_date', 'a.name as assigned_by', 'pickup_notes.id as pickup_note_no', 'pickup_notes.status_id', 'pns.name as status')
      ->where('pickup_notes.status_id', '<', 3);

      $datatables = Datatables::of($pickup_notes)
      ->addColumn('rider', function($pickup_note) {
        return $pickup_note->rider_name . '<br/>' . $pickup_note->rider_phone;
      })
      ->addColumn('route', function($pickup_note) {
        return $pickup_note->route_code . ' (' . $pickup_note->route_start . ' to ' . $pickup_note->route_end . ')';
      })
      ->editColumn('assigned_date', function($pickup_note) {
        return Carbon::parse($pickup_note->assigned_date)->format('d/m/Y H:i A');
      })
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup_note) {
        return ($pickup_note->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('pickup_note_no', function($pickup_note) {
        return ($pickup_note->status_id == 2) ? $pickup_note->id : '';
      })
      ->addColumn('action', function($pickup_note) {
        $cancel_button = '<button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>';
        $view_details_button = '<button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';
        $generate_pickup_note_button = '<button type="button" class="dropdown-item generate_pickup_note"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-target"></i></div><div class="col-9 offset-1">Generate Pickup Note</div></div></button>';
        $print_pickup_note_button = '<button type="button" class="dropdown-item print_pickup_note"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Print Pickup Note</div></button>';
        $sms_rider_button = '<button type="button" class="dropdown-item sms_rider"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-message-circle"></i></div><div class="col-9 offset-1">SMS Rider</div></button>';

        if ($pickup_note->status_id == 1) {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $cancel_button . $view_details_button . $generate_pickup_note_button . '
                    </div>
                  </div>
          ';
        }
        else {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $cancel_button . $view_details_button . $print_pickup_note_button . $sms_rider_button . '
                    </div>
                  </div>

          ';
        }
      })
      ->filterColumn('rider', function($query, $keyword) {
        $query->where('r.name', 'like', '%' . $keyword . '%')->orWhere('r.phone', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('route', function($query, $keyword) {
        $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickup_notes.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickup_notes.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      })
      ->filterColumn('pickup_note_no', function($query, $keyword) {
        $query->where('pickup_notes.status_id', '=', 2)->where('pickup_notes.id', '=', $keyword);
      });

      return $datatables->make(true);
    }

    public function assigned_cancel(Request $request) {
      $pickup_note_id = $request->input('pickup_note_id');

      $pickup_note = PickupNote::find($pickup_note_id);

      if ($pickup_note->status_id < 3) {
        $pickup_note->status_id = 6;

        $pickup_note->save();

        foreach (PickupNote::find($pickup_note_id)->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          $pickup_request->status = 0;

          $pickup_request->pending_bookings = $pickup_request->bookings;

          $pickup_request->save();
        }

        PickupNotesJourneyController::add($pickup_note_id, 6, 'Pickup Note has been Cancelled!', Auth::id());

        return ['status' => 0, 'success' => 'Pickup has been Cancelled'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

    public function assigned_view_details(Request $request) {
      $pickup_note_id = $request->input('pickup_note_id');

      $pickup_note = PickupNote::find($pickup_note_id);

      $pickup_note_requests = $pickup_note->pickup_note_requests;

      $details = array();

      foreach ($pickup_note_requests as $pickup_note_request) {
        $pickup_request = $pickup_note_request->pickup_request;

        $shipper = $pickup_request->shipper;
        $pickup_address = $pickup_request->pickup_address;

        $detail = array();

        $detail['shipper'] = $shipper->name;
        $detail['contact_person'] = $pickup_address['poc'];
        $detail['contact_number'] = $pickup_address['phone'];
        $detail['address'] = $pickup_address['pickup_address'];
        $detail['bookings'] = $pickup_request['bookings'];
        $detail['total_estimated_weight'] = floatval($pickup_request['total_estimated_weight']);
        $detail['pickup_type'] = ($pickup_request['pickup_type'] == 0) ? 'Light' : 'Heavy';

        $details[] = $detail;
      }

      return $details;
    }

    public function assigned_generate_pickup_note(Request $request) {
      $pickup_note_id = $request->input('pickup_note_id');

      $pickup_note = PickupNote::find($pickup_note_id);

      if ($pickup_note->status_id == 1) {
        $pickup_note->status_id = 2;

        $pickup_note->save();

        PickupNotesJourneyController::add($pickup_note_id, 2, 'Pickup Note has been Generated!', Auth::id());

        return ['status' => 0, 'success' => 'Pickup Note has been Generated'];
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

    public function assigned_print(Request $request) {
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

      $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

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
                    </style>
                  </head>
                  <body>
                    <div class="p-1">
      ';

      foreach($request->ids as $id) {
        $pickup_note = PickupNote::find($id);

        $rider = Rider::find($pickup_note->rider_id);
        $route = $rider->route;

        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 12, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td> ' . $route->code . ' (' . $route->start . ' to ' . $route->end . ')</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

        $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

        $serial_number = 1;

        $pickup_note_requests = $pickup_note->pickup_note_requests;

        foreach ($pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          $shipper = $pickup_request->shipper;
          $pickup_address = $pickup_request->pickup_address;

          $html .= '
                          <tr>
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['bookings'] . '</td>
                            <td>' . Carbon::parse($pickup_request['pickup_date'])->format('d/m/Y') . '</td>
                          </tr>
          ';

          $serial_number++;
        }

        $html .= '
                        </tbody>
                      </table>

                      <hr>
        ';

        $pickup_note->status_id = 3;

        $pickup_note->save();
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

    public function receive_index() {
      return view('admin.pickups.receive.index');
    }

    public function receive_list(Request $request) {
      $pickup_notes = PickupNote::join('riders as r', 'pickup_notes.rider_id', '=', 'r.id')
      ->join('rider_categories as rc', 'r.rider_category_id', '=', 'rc.id')
      ->join('routes as ro', 'r.route_id', '=', 'ro.id')
      ->join('cities as c', 'r.city_id', '=', 'c.id')
      ->join('admins as a', 'pickup_notes.assigned_by_user_id', '=', 'a.id')
      ->join('pickup_note_statuses as pns', 'pickup_notes.status_id', '=', 'pns.id')
      ->select('pickup_notes.id', 'r.name as rider_name', 'r.phone as rider_phone', 'rc.name as rider_type', 'ro.code as route_code', 'ro.start as route_start', 'ro.end as route_end', 'c.name as city', 'pickup_notes.pickups', 'pickup_notes.bookings', 'pickup_notes.pickup_type', 'pickup_notes.created_at as assigned_date', 'a.name as assigned_by', 'pickup_notes.id as pickup_note_no', 'pickup_notes.status_id', 'pns.name as status')
      ->where('pickup_notes.status_id', [3, 4]);

      $datatables = Datatables::of($pickup_notes)
      ->addColumn('rider', function($pickup_note) {
        return $pickup_note->rider_name . '<br/>' . $pickup_note->rider_phone;
      })
      ->addColumn('route', function($pickup_note) {
        return $pickup_note->route_code . ' (' . $pickup_note->route_start . ' to ' . $pickup_note->route_end . ')';
      })
      ->editColumn('pickup_type', function($pickup_note) {
        return ($pickup_note->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('assigned_date', function($pickup_note) {
        return Carbon::parse($pickup_note->assigned_date)->format('d/m/Y H:i A');
      })
      ->addColumn('action', function($pickup_note) {
        $receive_button = '<button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';
        $view_button = '<button type="button" class="dropdown-item summary"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-target"></i></div><div class="col-9 offset-1">Summary</div></button>';

        if ($pickup_note->status_id == 3) {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $receive_button . '
                    </div>
                  </div>
          ';
        }
        else {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $receive_button . $view_button . '
                    </div>
                  </div>
          ';
        }
      })
      ->filterColumn('rider', function($query, $keyword) {
        $query->where('r.name', 'like', '%' . $keyword . '%')->orWhere('r.phone', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('route', function($query, $keyword) {
        $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickup_notes.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickup_notes.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      });

      return $datatables->make(true);
    }

    public function receive_pickup_note(Request $request) {
      if ($request->has('pickup_note_no')) {
        $pickup_note = PickupNote::find($request->get('pickup_note_no'));

        if ($pickup_note) {
          if ($pickup_note->status_id == 3 || $pickup_note->status_id == 4) {
            return redirect()->route('admin.pickups.receive.arrival_of_shipments.index')->with('pickup_receive_pickup_note_id', $request->get('pickup_note_no'));
          }
          else {
            return back()->withErrors('Given Pickup Note has already been modified!');
          }
        }
        else {
          return back()->withErrors('Invalid Pickup Note!');
        }
      }
      else {
        return back()->withErrors('Missing Pickup Note ID!');
      }
    }

    public function receive_arrival_of_shipments_index() {
      if (session('pickup_receive_pickup_note_id')) {
        return view('admin.pickups.receive.arrival_of_shipments');
      }
      else {
        return redirect()->route('admin.pickups.receive.index')->withErrors('Kindly reselect a Pickup Note!');
      }
    }

    public function receive_shipment_details(Request $request) {
      $shipment = Shipment::where('tracking_number', $request->tracking_number);

      if ($shipment->exists()) {
        $shipment = $shipment->first();

        if ($shipment->shipper_status_id == 1) {
          $exists = FALSE;

          $pickup_note = PickupNote::find($request->pickup_receive_pickup_note_id);

          foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
            $pickup_request = $pickup_note_request->pickup_request;

            if ($pickup_request->seller_id == $shipment->seller_id) {
              $exists = TRUE;

              break;
            }
          }

          if ($exists) {
            if (empty($request->weight)) {
              $shipment->actual_weight = (($request->length * $request->breadth * $request->height) / 5000);
              $shipment->length = $request->length;
              $shipment->breadth = $request->breadth;
              $shipment->height = $request->height;
            }
            else {
              $shipment->actual_weight = $request->weight;
            }

            $shipment->save();

            $details = array();

            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['receiving_sheet_no'] = ($shipment->receiving_sheet_shipment) ? str_pad($shipment->receiving_sheet_shipment->receiving_sheet_id, 12, "0", STR_PAD_LEFT) : '';
            $details['order_id'] = $shipment->order_id;
            $details['destination'] = $shipment->consignee_city->name;
            $details['cod_amount'] = $shipment->amount;
            $details['estimated_weight'] = floatval($shipment->estimated_weight);
            $details['actual_weight'] = floatval($shipment->actual_weight);

            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
          }
          else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment does not belong to the Selected Pickup Note'];
          }
        }
        else {
          return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
      }
    }

    public function receive_arrival_of_shipments_store(Request $request) {
      $pickup_note = PickupNote::find($request->pickup_receive_pickup_note_id);

      $pickup_request_ids = array();

      foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
        $pickup_request = $pickup_note_request->pickup_request;

        $pickup_request_ids[] = $pickup_request->id;
      }

      $receiving_sheet_shipment_ids = array();
      $over_received_shipment_ids = array();

      foreach (explode(',', $request->shipment_ids) as $shipment_id) {
        $shipment = Shipment::find($shipment_id);

        if ($shipment->receiving_sheet_shipment) {
          $receiving_sheet_shipment_ids[$shipment->receiving_sheet_shipment->receiving_sheet_id][] = $shipment_id;
        }
        else {
          $over_received_shipment_ids[] = $shipment_id;
        }

        $shipment->shipper_status_id = 2;
        $shipment->consignee_status_id = 2;
        $shipment->save();

        ShipmentsJourneyController::add($shipment_id, 2, 2, NULL, 'Shipment has Arrived!', NULL, Auth::id());
      }

      $pickup_requests_receiving_sheets = array();

      if (!empty($receiving_sheet_shipment_ids)) {
        foreach ($receiving_sheet_shipment_ids as $receiving_sheet_id => $receiving_sheet_shipments) {
          foreach ($receiving_sheet_shipments as $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            foreach ($pickup_request_ids as $pickup_request_id) {
              $pickup_request = PickupRequest::find($pickup_request_id);

              if ($shipment->user_id == $pickup_request->shipper_id && $shipment->pickup_address_id == $pickup_request->pickup_address_id) {
                $receiving_sheet_shipments_count = ReceivingSheetShipment::where('receiving_sheet_id', $receiving_sheet_id)->count();

                if ($pickup_request->received) {
                  $pickup_request->received = $pickup_request->received + 1;
                }
                else {
                  $pickup_request->received = 1;
                }

                if ($pickup_request->short_received) {
                  if (in_array($receiving_sheet_id, $pickup_requests_receiving_sheets)) {
                    $pickup_request->short_received = $pickup_request->short_received - 1;
                  }
                  else {
                    $pickup_request->short_received = $pickup_request->short_received - 1 + $receiving_sheet_shipments_count;
                  }
                }
                else {
                  $pickup_request->short_received = $receiving_sheet_shipments_count - 1;
                }

                $pickup_request->save();

                $pickup_requests_receiving_sheets[] = $receiving_sheet_id;
              }
            }
          }
        }
      }

      if (!empty($over_received_shipment_ids)) {
        foreach ($over_received_shipment_ids as $shipment_id) {
          $shipment = Shipment::find($shipment_id);

            foreach ($pickup_request_ids as $pickup_request_id) {
              $pickup_request = PickupRequest::find($pickup_request_id);

              if ($shipment->user_id == $pickup_request->shipper_id && $shipment->pickup_address_id == $pickup_request->pickup_address_id) {
                if ($pickup_request->received) {
                  $pickup_request->received = $pickup_request->received + 1;
                }
                else {
                  $pickup_request->received = 1;
                }

                $pickup_request->save();
              }
            }
        }
      }

      $pickup_note->status_id = 4;
      $pickup_note->save();

      PickupNotesJourneyController::add($pickup_note->id, $pickup_note->status_id, 'Pickup Note has been Received!', Auth::id());

      return redirect()->route('admin.pickups.receive.summary.index')->with('pickup_receive_pickup_note_id', $request->pickup_receive_pickup_note_id);
    }

    public function receive_summary_index() {
      if (session('pickup_receive_pickup_note_id')) {
        return view('admin.pickups.receive.summary');
      }
      else {
        return redirect()->route('admin.pickups.receive.index')->withErrors('Kindly reselect a Pickup Note!');
      }
    }

    public function receive_summary_list(Request $request) {
      $pickup_requests = PickupRequest::join('users as u', 'pickup_requests.shipper_id', '=', 'u.id')
      ->join('user_shipping_infos as usi', 'pickup_requests.pickup_address_id', '=', 'usi.id')
      ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
      ->join('pickup_note_requests as pnr', 'pickup_requests.id', '=', 'pnr.pickup_request_id')
      ->join('pickup_notes as pn', 'pnr.pickup_note_id', '=', 'pn.id')
      ->join('admins as a', 'pn.assigned_by_user_id', '=', 'a.id')
      ->select('pickup_requests.id', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'pickup_requests.bookings', 'pickup_requests.received', 'pickup_requests.short_received', 'pickup_requests.pickup_type', 'pickup_requests.created_at as booking_date', 'pn.created_at as assigned_date', 'a.name as assigned_by', 'pn.id as pickup_note_no')
      ->where('pn.id', $request->pickup_receive_pickup_note_id);

      $datatables = Datatables::of($pickup_requests)
      ->editColumn('pickup_type', function($pickup_request) {
        return ($pickup_request->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->editColumn('booking_date', function($pickup_request) {
        return Carbon::parse($pickup_request->booking_date)->format('d/m/Y H:i A');
      })
      ->editColumn('assigned_date', function($pickup_request) {
        return Carbon::parse($pickup_request->assigned_date)->format('d/m/Y H:i A');
      })
      ->addColumn('action', function($pickup_request) {
        $cancel_button = '<button type="button" class="dropdown-item receive"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Receive</div></button>';
        $done_button = '<button type="button" class="dropdown-item done"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Done</div></button>';
        $not_done_button = '<button type="button" class="dropdown-item not_done"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Not Done</div></button>';

        if ($pickup_request->received > 0) {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $cancel_button . $done_button . '
                    </div>
                  </div>
          ';
        }
        else {
          return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      ' . $cancel_button . $not_done_button . '
                    </div>
                  </div>
          ';
        }
      })
      ->filterColumn('pickup_type', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if (strpos('light', $keyword) !== FALSE) {
          $query->where('pickup_requests.pickup_type', '=', 0);
        }
        else if (strpos('heavy', $keyword) !== FALSE) {
          $query->where('pickup_requests.pickup_type', '=', 1);
        }
        else {
          $query->whereRaw('false');
        }
      });

      return $datatables->make(true);
    }

    public function receive_summary_request_short_received(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      $receiving_sheets = ReceivingSheet::where('user_id', $pickup_request->shipper_id)->where('status', 0);

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

    public function receive_summary_request_done(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 1) {
        $pickup_request->status = 2;

        $pickup_request->save();

        $completed = TRUE;

        $pickup_note = $pickup_request->pickup_note_request->pickup_note;

        foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          if ($pickup_request->status < 2) {
            $completed = FALSE;

            break;
          }
        }

        if ($completed) {
          $pickup_note->status_id = 5;
          $pickup_note->save();

          PickupNotesJourneyController::add($pickup_note->id, 5, 'Pickup Note has been Completed!', Auth::id());

          return ['status' => 0, 'success' => 'Pickup has been marked Done & Pickup Note has been Completed', 'complete' => TRUE];
        }
        else {
          return ['status' => 0, 'success' => 'Pickup has been marked Done', 'complete' => FALSE];
        }
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

    public function receive_summary_request_not_done(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 1) {
        $pickup_request->status = 0;

        $pickup_request->save();

        $completed = TRUE;

        $pickup_note = $pickup_request->pickup_note_request->pickup_note;

        PickupNoteRequest::where('pickup_note_id', $pickup_note->id)->where('pickup_request_id', $pickup_request_id)->delete();

        foreach ($pickup_note->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          if ($pickup_request->status < 2) {
            $completed = FALSE;

            break;
          }
        }

        if ($completed) {
          $pickup_note->status_id = 5;
          $pickup_note->save();

          PickupNotesJourneyController::add($pickup_note->id, 5, 'Pickup Note has been Completed!', Auth::id());

          return ['status' => 0, 'success' => 'Pickup has been marked Not Done and is moved to Pending & Pickup Note has been Completed', 'complete' => TRUE];
        }
        else {
          return ['status' => 0, 'success' => 'Pickup has been marked Not Done and is moved to Pending', 'complete' => FALSE];
        }
      }
      else {
        return ['status' => 1, 'error' => 'Selected Pickup has already been modified'];
      }
    }

}