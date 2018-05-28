<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipment;
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
      return view('admin.pickups.pending.index');
    }

    public function pending_list(Request $request) {
      $pickup_requests = PickupRequest::join('users as u', 'pickup_requests.shipper_id', '=', 'u.id')
      ->join('user_shipping_infos as usi', 'pickup_requests.pickup_address_id', '=', 'usi.id')
      ->join('city_infos AS ci', 'usi.city_code', '=', 'ci.city_code')
      ->select('pickup_requests.id', 'pickup_requests.created_at as requested_at', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.city_name AS city', 'pickup_requests.bookings', 'pickup_requests.pending_bookings', 'pickup_requests.total_estimated_weight', 'pickup_requests.pickup_type', 'pickup_requests.pickup_date')
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
        return '<button class="btn btn-sm btn-danger cancel">Cancel</button>';
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

      foreach ($pickup_request_ids as $pickup_request_id) {
        $pickup_note_request = new PickupNoteRequest();

        $pickup_note_request->pickup_note_id = $pickup_note_id;
        $pickup_note_request->pickup_request_id = $pickup_request_id;

        $pickup_note_request->save();
      }

      PickupNotesJourneyController::add($pickup_note_id, 0, 'Pickup Note has been Created!', Auth::id());

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

        $pickup_request->status = 2;

        $pickup_request->save();
      }

      return ['status' => 0, 'success' => 'Pickup Request(s) has been Cancelled'];
    }

    public function pending_cancel(Request $request) {
      $pickup_request_id = $request->input('pickup_request_id');

      $pickup_request = PickupRequest::find($pickup_request_id);

      if ($pickup_request->status == 0) {
        $pickup_request->status = 2;

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
      $pickup_notes = PickupNote::join('admins as a', 'pickup_notes.assigned_by_user_id', '=', 'a.id')
      ->join('pickup_note_statuses as pns', 'pickup_notes.status_id', '=', 'pns.id')
      ->select('pickup_notes.id', 'pickup_notes.rider_id', 'pickup_notes.pickups', 'pickup_notes.bookings', 'pickup_notes.total_estimated_weight', 'pickup_notes.pickup_type', 'pickup_notes.created_at as assigned_date', 'a.name as assigned_by', 'pickup_notes.id as pickup_note_no', 'pickup_notes.status_id', 'pns.name as status')
      ->where('pickup_notes.status_id', '<', 3);

      $datatables = Datatables::of($pickup_notes)
      ->editColumn('assigned_date', function($pickup_note) {
        return Carbon::parse($pickup_note->assigned_date)->format('d/m/Y H:i A');
      })
      ->editColumn('total_estimated_weight', '{{ floatval($total_estimated_weight) }}')
      ->editColumn('pickup_type', function($pickup_note) {
        return ($pickup_note->pickup_type == 0) ? 'Light' : 'Heavy';
      })
      ->addColumn('pickup_note_no', function($pickup_note) {
        return ($pickup_note->status_id == 2) ? $pickup_note->id : '';
      })
      ->addColumn('action', function($pickup_note) {
        $cancel_button = '<button class="btn btn-sm btn-danger d-block mx-auto cancel">Cancel</button>';
        $view_details_button = '<button class="btn btn-sm btn-info d-block mx-auto mt-1 view_details">View Details</button>';
        $generate_pickup_note_button = '<button class="btn btn-sm btn-primary d-block mx-auto mt-1 generate_pickup_note">Generate Pickup Note</button>';
        $print_pickup_note_button = '<button class="btn btn-sm btn-primary d-block mx-auto mt-1 print_pickup_note">Print Pickup Note</button>';
        $sms_rider_button = '<button class="btn btn-sm btn-primary d-block mx-auto mt-1 sms_rider">SMS Rider</button>';

        if ($pickup_note->status_id == 1) {
          return $cancel_button . $view_details_button . $generate_pickup_note_button;
        }
        else {
          return $cancel_button . $view_details_button . $print_pickup_note_button . $sms_rider_button;
        }
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
        $pickup_note->status_id = 5;

        $pickup_note->save();

        foreach (PickupNote::find($pickup_note_id)->pickup_note_requests as $pickup_note_request) {
          $pickup_request = $pickup_note_request->pickup_request;

          $pickup_request->status = 0;

          $pickup_request->pending_bookings = $pickup_request->bookings;

          $pickup_request->save();
        }

        PickupNotesJourneyController::add($pickup_note_id, 5, 'Pickup Note has been Cancelled!', Auth::id());

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

        //Integrate with Rider Information
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
                            <td>Temporary Rider</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 12, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>-</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>-</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>-</td>
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