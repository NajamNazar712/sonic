<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\BookingType;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Shipment;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminFinanceController extends Controller
{
    static private function gst($hub_id) {
        $hub_ids = [101, 106, 109, 110, 111, 119, 122, 125, 128, 130, 134, 135, 144, 158, 165, 174, 176, 186, 465, 199, 223, 414, 238, 244, 251, 255, 264, 267, 271, 281, 283, 284, 293, 302, 315, 304, 319, 339, 340];

        if (in_array($hub_id, $hub_ids)) {
            return 0.16;
        }
        else {
            return 0.13;
        }
    }

    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    public function outstanding_sdn_index() {
        $banks = BanksList::all();
      return view('admin.finance.outstanding_sdn')->with(['banks'=>$banks]);
    }

    public function outstanding_sdn_list(Request $request) {
        $station_deposit_notes = StationDepositNote::join('cities as h', 'station_deposit_notes.hub_id', '=', 'h.id')
        ->join('admins as a', 'station_deposit_notes.deposited_by', '=', 'a.id')
        ->join('banks_lists as b', 'station_deposit_notes.banks_list_id', '=', 'b.id')
        ->select('station_deposit_notes.id', 'station_deposit_notes.id as sdn_number', 'h.name as hub', 'station_deposit_notes.dncc_count', 'station_deposit_notes.sdn_delivered_shipments', 'station_deposit_notes.sdn_amount', 'a.name as deposited_by', 'b.name as bank', 'station_deposit_notes.created_at as deposited_at', 'station_deposit_notes.deposit_slip')
        ->where('station_deposit_notes.status', 1);

        if (session('role_id') != 1) {
            $station_deposit_notes = $station_deposit_notes->whereIn('h.id', session('hubs'));
        }

        $datatables = Datatables::of($station_deposit_notes)
        ->editColumn('sdn_number', function($station_deposit_note) {
            return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($station_deposit_note->sdn_number, 6, '0', STR_PAD_LEFT) . '</span></button>';
        })
        ->filterColumn('station_deposit_notes.id', function ($query, $keyword) {
            return $query->where('station_deposit_notes.id', '=', $keyword);
        })
        ->addColumn('sdn_number_padded', function ($station_deposit_note) {
            return str_pad($station_deposit_note->sdn_number, 6, '0', STR_PAD_LEFT);
        })
        ->editColumn('deposit_slip', function($station_deposit_note) {
            if ($station_deposit_note->deposit_slip) {
                return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/sdn/' . $station_deposit_note->deposit_slip) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
            }
            else {
                return '';
            }
        })
        ->filterColumn('bank', function($query, $keyword) {

                if ($keyword !='') {
                    $query->where('b.id', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
        })
        ->addColumn('action', function($station_deposit_note) {
            $reconcile_delivery_notes_button = '<button type="button" class="dropdown-item reconcile_delivery_notes"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Reconcile Delivery Notes</div></button>';
            $export_to_excel_button = '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>';

            $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

            if (session('role_id') == 1 || in_array(53, session('permissions'))) {
              $dropdown .= $reconcile_delivery_notes_button;
            }

            $dropdown .= $export_to_excel_button;

            $dropdown .= '
                </div>
              </div>
            ';

            return $dropdown;
        });

        return $datatables->make(true);
    }

    public function outstanding_sdn_delivery_notes_list(Request $request) {
        $delivery_notes = StationDepositNote::join('delivery_note_station_deposit_notes as dnsdn', 'station_deposit_notes.id', '=', 'dnsdn.station_deposit_note_id')
        ->join('delivery_notes as dn', 'dnsdn.delivery_note_id', '=', 'dn.id')
        ->join('cities as h', 'dn.hub_id', '=', 'h.id')
        ->join('riders as ri', 'dn.rider_id', '=', 'ri.id')
        ->join('admins as a', 'dn.admin_id', '=', 'a.id')
        ->join('admins as au', 'dn.updated_by', '=', 'au.id')
        ->select('dn.id', 'dn.id as delivery_note_number', 'h.name as hub', 'ri.name as rider', 'dn.shipments_count as shipments', 'dn.delivered_shipments', 'a.name as assigned_by', 'dn.created_at as assigned_at', 'au.name as updated_by', 'dn.updated_at', 'dn.received_cod_amount as dncc_amount');

        if ($request->has('id')) {
           $delivery_notes->where('station_deposit_notes.id', $request->id);
        }
        else {
            $delivery_notes->whereRaw('FALSE');
        }

        $datatables = Datatables::of($delivery_notes)
        ->editColumn('delivery_note_number', function ($delivery_notes) {
            return str_pad($delivery_notes->delivery_note_number, 6, '0', STR_PAD_LEFT);
        })
        ->filterColumn('dn.id', function ($query, $keyword) {
            return $query->where('dn.id', '=', $keyword);
        })
        ->addColumn('action', function($delivery_notes) {
            return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit_expense"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit Expense</div></button>
                  </div>
                </div>
            ';
        });

        return $datatables->make(true);
    }

    public function outstanding_sdn_reconcile_delivery_notes(Request $request) {
        $station_deposit_note = StationDepositNote::find($request->station_deposit_note_id);

        $station_deposit_note->status = 2;

        $station_deposit_note->save();

        $delivery_note_ids = explode(',', $request->delivery_note_ids);

        foreach ($delivery_note_ids as $delivery_note_id) {
            foreach (DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->get() as $delivery_note_shipment) {
                if (in_array($delivery_note_shipment->status, [4, 5, 6])) {
                    $delivery_note_shipment->status = 7;

                    $delivery_note_shipment->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Station Deposit No.' . str_pad($request->station_deposit_note_id, 6, '0', STR_PAD_LEFT) . ' has been Reconciled');
    }

    public function outstanding_sdn_export_to_excel(Request $request) {
        $delivery_note_station_deposit_notes = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->id);

        if ($delivery_note_station_deposit_notes->exists()) {
            $delivery_note_station_deposit_notes = $delivery_note_station_deposit_notes->get();

            $details = array();

            $details[] = ['S. No.', 'DNCC No.', 'Hub', 'Rider', 'Route', 'Shipments', 'Delivered Shipments', 'DNCC Amount'];

            $serial_number = 1;

            foreach ($delivery_note_station_deposit_notes as $delivery_note_station_deposit_note) {
                $delivery_note = DeliveryNote::find($delivery_note_station_deposit_note->delivery_note_id);

                $row = array();

                $row[] = $serial_number;
                $row[] = str_pad($delivery_note->id, 6, '0', STR_PAD_LEFT);
                $row[] = $delivery_note->hub->name;
                $row[] = $delivery_note->rider->name;
                $row[] = $delivery_note->route->code . ' (' . $delivery_note->route->start . ' to ' . $delivery_note->route->end . ')';
                $row[] = $delivery_note->shipments_count;
                $row[] = $delivery_note->delivered_shipments;
                $row[] = $delivery_note->received_cod_amount;

                $details[] = $row;

                $serial_number++;
            }

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->fromArray($details);

            $writer = new Xlsx($spreadsheet);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="sonic_sdn_' . $request->id . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
        }
    }

    public function outstanding_shipments_index() {
        $hubs = City::orderBy('name')->get();
        $booking_types = BookingType::all();
        $service_type = BookingType::all();
        $shipment_status = ShipmentStatus::select('id','name')->get();
        return view('admin.finance.outstanding_shipments')->with(['hubs' => $hubs, 'booking_types' => $booking_types,'service_type'=>$service_type,'shipment_status'=>$shipment_status]);
    }

    public function outstanding_shipments_list(Request $request) {
        $shipments = DeliveryNoteShipment::join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
        ->join('user_shipping_infos as usi', 's.pickup_address_id', '=', 'usi.id')
        ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
        ->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')
        ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
        ->join('users as u', 's.user_id', '=', 'u.id')
        ->join('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
        ->leftjoin('shipments_journey as sj', function($join) {
            $join->on('sj.shipment_id', '=', 's.id')
            ->where('sj.created_at', '=', DB::raw('(SELECT MAX(created_at) FROM shipments_journey WHERE shipment_id = s.id)'));
        })
        ->leftjoin('shipments_journey as sjd', function($join) {
            $join->on('sjd.shipment_id', '=', 's.id')
            ->where('sjd.created_at', '=', DB::raw('(SELECT MAX(created_at) FROM shipments_journey WHERE shipment_id = s.id AND shipper_status_id IN (14, 16, 30, 36))'));
        })
        ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
        ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
        ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as status', 'sj.updated_at as status_updated_at', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'dnsdn.station_deposit_note_id as sdn', 'sjd.created_at as delivered_at')
        ->whereIn('delivery_note_shipments.status', [4, 5, 6]);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($shipments)
        ->editColumn('tracking_number',function ($shipments){
            $route = route('admin.tracking.index');
            return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
        })
        ->editColumn('dncc', function ($shipment) {
            if ($shipment->dncc) {
                return str_pad($shipment->dncc, 6, '0', STR_PAD_LEFT);
            }
            else {
                return '';
            }
        })
        ->filterColumn('delivery_note_shipments.delivery_note_id', function ($query, $keyword) {
            return $query->where('delivery_note_shipments.delivery_note_id', '=', $keyword);
        })
        ->editColumn('sdn', function ($shipment) {
            if ($shipment->sdn) {
                return str_pad($shipment->sdn, 6, '0', STR_PAD_LEFT);
            }
            else {
                return '';
            }
        })
        ->filterColumn('dnsdn.station_deposit_note_id', function ($query, $keyword) {
            return $query->where('dnsdn.station_deposit_note_id', '=', $keyword);
        })
        ->addColumn('aging', function($shipment) {
            $updated_at = Carbon::parse($shipment->status_updated_at)->startOfDay();

            $now = Carbon::now()->startOfDay();

            return $updated_at->diffInDays($now) . 'd';
        })
        ->addColumn('action', function($shipment) {
            $resolve_button = '<button type="button" class="dropdown-item resolve"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Resolve</div></button>';
            $adjust_in_payment_button = '<button type="button" class="dropdown-item adjust_in_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Adjust in Payment</div></button>';

            if (session('role_id') == 1 || count(array_intersect([55, 56], session('permissions'))) !== 0) {
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                if (session('role_id') == 1 || in_array(55, session('permissions'))) {
                  $dropdown .= $resolve_button;
                }

                if (session('role_id') == 1 || in_array(56, session('permissions'))) {
                  $dropdown .= $adjust_in_payment_button;
                }

                $dropdown .= '
                    </div>
                  </div>
                ';

                return $dropdown;
            }
            else {
                return '';
            }
        });

        if ($hub = $request->get('hub')) {
            $datatables->where('hc.id', '=', $hub);
        }

        if ($service = $request->get('service')) {
            $datatables->where('bt.id', '=', $service);
        }

        if ($delivery_date_from = $request->get('delivery_date_from')) {
            $datatables->where('sjd.created_at', '>=', $delivery_date_from);
        }

        if ($delivery_date_to = $request->get('delivery_date_to')) {
            $datatables->where('sjd.created_at', '<', Carbon::parse($delivery_date_to)->addDay()->toDateTimeString());
        }

        return $datatables->make(true);
    }

    public function outstanding_shipments_resolved(Request $request) {
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $request->id)->whereIn('status', [4, 5, 6]);

        if ($delivery_note_shipment->exists()) {
            $delivery_note_shipment = $delivery_note_shipment->first();

            $delivery_note_shipment->status = 7;

            $delivery_note_shipment->save();

            return ['status' => 0, 'success' => 'Shipment has been marked Resolved'];
        }
        else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
    }

    public function outstanding_shipments_adjust_in_payment(Request $request) {
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $request->id)->whereIn('status', [4, 5, 6]);

        if ($delivery_note_shipment->exists()) {
            $delivery_note_shipment = $delivery_note_shipment->first();

            $delivery_note_shipment->status = 8;

            $delivery_note_shipment->save();

            $done_payment_shipment = DonePaymentShipment::where('shipment_id', $request->id);

            if ($done_payment_shipment->exists()) {
                $done_payment_shipment = $done_payment_shipment->first();

                $this->adjust_payment($done_payment_shipment->done_payment_id, $request->id);

                $shipment = Shipment::find($request->id);

                $shipment->payment_status_id = 4;

                $shipment->save();

                ShipmentsPaymentJourneyController::add($shipment->id, 4, Auth::id());

                NotificationsController::send(21, $request->id, Auth::id());
            }

            return ['status' => 0, 'success' => 'Shipment has been marked to be Adjusted in Payment'];
        }
        else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
    }

    public function change_shipment_amount_index() {
        return view('admin.finance.change_shipment_amount');
    }

    public function change_shipment_amount_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            if (!in_array($shipment->shipper_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 44, 45, 46])) {
                $details = array();

                $shipper = $shipment->user;

                $details['id'] = $shipment->id;

                $details['tracking_number'] = $shipment->tracking_number;
                $details['status'] = $shipment->status_shipper->name;

                $details['service_type'] = $shipment->booking_type->booking_type;
                $details['shipping_mode'] = $shipment->shipping_mode->mode;
                $details['weight'] = ($shipment->actual_weight) ? floatval($shipment->actual_weight) : floatval($shipment->estimated_weight);

                $details['payment_mode'] = $shipment->payment_mode->mode;
                $details['amount'] = $shipment->amount;

                $details['shipper']['name'] = $shipper->name;
                $details['shipper']['account_number'] = str_pad($shipper->id, 6, '0', STR_PAD_LEFT);
                $details['shipper']['phone_number_1'] = $shipper->phone;
                $details['shipper']['phone_number_2'] = $shipper->phone2;
                $details['shipper']['origin'] = $shipper->city->name;
                $details['shipper']['address'] = $shipper->address;

                $details['consignee']['name'] = $shipment->consignee_name;
                $details['consignee']['phone_number_1'] = $shipment->consignee_phone_number_1;
                $details['consignee']['phone_number_2'] = $shipment->consignee_phone_number_2;
                $details['consignee']['destination'] = $shipment->consignee_city->name;
                $details['consignee']['address'] = $shipment->consignee_address;

                return ['status' => 0, 'success' => 'Shipment\'s amount can be changed', 'details' => $details];
            }
            else {
                return ['status' => 1, 'error' => 'Shipment\'s Payment has already been Processed'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function change_shipment_amount_store(Request $request) {
        $shipment_id = $request->input('shipment_id');
        $amount = str_replace(',', '', $request->input('amount'));

        $shipment = Shipment::find($shipment_id);

        $shipment->amount = $amount;

        $shipment->save();

        ShipmentChargesController::cash_handling($shipment_id);

        return redirect()->route('admin.finance.change_shipment_amount.index')->with('success', 'Shipment\'s amount has been changed');
    }

    static public function add_payment($shipment_id, $type) {
        $shipment = Shipment::find($shipment_id);

        $amount = $shipment->amount;

        if (!$shipment->packaging_material_request) {
            if ($type == 0) {
                $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges;
                $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->hub_id)), 0, PHP_ROUND_HALF_DOWN);

                $payable = $amount - ($charges + $gst);
            }
            else {
                $charges = $shipment->weight_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge;
                $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->hub_id)), 0, PHP_ROUND_HALF_DOWN);

                $payable = 0 - ($charges + $gst);
            }
        }
        else {
            $charges = $shipment->packaging_material_charges;
            $gst = 0;

            $payable = $amount - ($charges + $gst);
        }

        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

        if ($pending_payment->exists()) {
            $pending_payment = $pending_payment->first();

            $pending_payment_shipment = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->where('shipment_id', $shipment_id);

            if (!$pending_payment_shipment->exists()) {
                $pending_payment->total_shipments = $pending_payment->total_shipments + 1;

                if ($type == 0) {
                    $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
                }
                else {
                    $pending_payment->returned_shipments = $pending_payment->returned_shipments + 1;
                }

                $pending_payment->save();

                $pending_payment_shipment = new PendingPaymentShipment();

                $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                $pending_payment_shipment->shipment_id = $shipment_id;
                $pending_payment_shipment->type = $type;
                $pending_payment_shipment->amount = $amount;
                $pending_payment_shipment->charges = $charges;
                $pending_payment_shipment->gst = $gst;
                $pending_payment_shipment->payable = $payable;

                $pending_payment_shipment->save();
            }
            else {
                $pending_payment_shipment = $pending_payment_shipment->first();

                if ($pending_payment_shipment->type != $type) {
                    if ($type == 0) {
                        $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
                        $pending_payment->returned_shipments = $pending_payment->returned_shipments - 1;
                    }
                    else {
                        $pending_payment->delivered_shipments = $pending_payment->delivered_shipments - 1;
                        $pending_payment->returned_shipments = $pending_payment->returned_shipments + 1;
                    }

                    $pending_payment->save();

                    $pending_payment_shipment->type = $type;

                    $pending_payment_shipment->save();
                }
            }
        }
        else {
            $pending_payment = new PendingPayment();

            $pending_payment->user_id = $shipment->user_id;
            $pending_payment->total_shipments = 1;

            if ($type == 0) {
                $pending_payment->delivered_shipments = 1;
                $pending_payment->returned_shipments = 0;
                $pending_payment->adjusted_shipments = 0;
            }
            else {
                $pending_payment->delivered_shipments = 0;
                $pending_payment->returned_shipments = 1;
                $pending_payment->adjusted_shipments = 0;
            }

            $pending_payment->save();

            $pending_payment_shipment = new PendingPaymentShipment();

            $pending_payment_shipment->pending_payment_id = $pending_payment->id;
            $pending_payment_shipment->shipment_id = $shipment_id;
            $pending_payment_shipment->type = $type;
            $pending_payment_shipment->amount = $amount;
            $pending_payment_shipment->charges = $charges;
            $pending_payment_shipment->gst = $gst;
            $pending_payment_shipment->payable = $payable;

            $pending_payment_shipment->save();
        }
    }

    private function adjust_payment($done_payment_id, $shipment_id) {
        $done_payment_shipment = DonePaymentShipment::where('done_payment_id', $done_payment_id)->where('shipment_id', $shipment_id)->first();

        ShipmentChargesController::return($shipment_id);

        $shipment = Shipment::find($shipment_id);

        $amount = 0 - $done_payment_shipment->payable;
        $charges = $shipment->weight_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge;
        $gst = ROUND(($charges * $this->gst($shipment->pickup_address->city->hub_id)), 0, PHP_ROUND_HALF_DOWN);
        $payable = $amount - ($charges + $gst);

        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

        if ($pending_payment->exists()) {
            $pending_payment = $pending_payment->first();

            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

            $pending_payment->save();

            $pending_payment_shipment = new PendingPaymentShipment();

            $pending_payment_shipment->pending_payment_id = $pending_payment->id;
            $pending_payment_shipment->shipment_id = $shipment_id;
            $pending_payment_shipment->type = 2;
            $pending_payment_shipment->amount = $amount;
            $pending_payment_shipment->charges = $charges;
            $pending_payment_shipment->gst = $gst;
            $pending_payment_shipment->payable = $payable;

            $pending_payment_shipment->save();
        }
        else {
            $pending_payment = new PendingPayment();

            $pending_payment->user_id = $shipment->user_id;
            $pending_payment->total_shipments = 1;
            $pending_payment->delivered_shipments = 0;
            $pending_payment->returned_shipments = 0;
            $pending_payment->adjusted_shipments = 1;

            $pending_payment->save();

            $pending_payment_shipment = new PendingPaymentShipment();

            $pending_payment_shipment->pending_payment_id = $pending_payment->id;
            $pending_payment_shipment->shipment_id = $shipment_id;
            $pending_payment_shipment->type = 2;
            $pending_payment_shipment->amount = $amount;
            $pending_payment_shipment->charges = $charges;
            $pending_payment_shipment->gst = $gst;
            $pending_payment_shipment->payable = $payable;

            $pending_payment_shipment->save();
        }
    }

    public function make_payments_index() {
        $banks = BanksList::all();
      return view('admin.finance.make_payments')->with(['banks'=>$banks]);
    }

    public function make_payments_list(Request $request) {
        $pending_payments = PendingPayment::join('users as u', 'pending_payments.user_id', '=', 'u.id')
        ->join('cities as c', 'u.city_id', '=', 'c.id')
        ->join('user_bank_infos as ubi', 'pending_payments.user_id', '=', 'ubi.user_id')
        ->join('banks_lists as ub', 'ubi.bank_name', '=', 'ub.id')
        ->join('cities as bc', 'ubi.city_id', '=', 'bc.id')
        ->join('pending_payment_shipments as pps', 'pending_payments.id', '=', 'pps.pending_payment_id')
        ->select('pending_payments.id as id', 'pending_payments.created_at', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'pending_payments.total_shipments', 'pending_payments.delivered_shipments', 'pending_payments.delivered_shipments as delivered_shipments_count', 'pending_payments.returned_shipments', 'pending_payments.returned_shipments as returned_shipments_count ', 'pending_payments.adjusted_shipments', 'pending_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(pps.amount) as total_amount'), DB::raw('SUM(pps.charges) as total_charges'), DB::raw('SUM(pps.gst) as total_gst'), DB::raw('SUM(pps.payable) as total_payable'), 'ub.name as bank', 'ubi.bank_branch', 'ubi.account_no', 'ubi.account_title', 'ubi.iban', 'bc.name as account_city', 'ubi.payment_mode', 'ubi.payment_cycle')
        ->groupBy('pending_payments.id');

        if (session('role_id') != 1) {
            $pending_payments = $pending_payments->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($pending_payments)
        ->addColumn('total_deductable', function($pending_payments) {
            return number_format($pending_payments->total_charges + $pending_payments->total_gst);
        })
        ->editColumn('delivered_shipments', function($pending_payment) {
            if ($pending_payment->delivered_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->delivered_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('returned_shipments', function($pending_payment) {
            if ($pending_payment->returned_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->returned_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('adjusted_shipments', function($pending_payment) {
            if ($pending_payment->adjusted_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $pending_payment->adjusted_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('total_amount', function($pending_payment) {
            return number_format($pending_payment->total_amount);
        })
        ->editColumn('total_charges', function($pending_payment) {
            return number_format($pending_payment->total_charges);
        })
        ->editColumn('total_gst', function($pending_payment) {
            return number_format($pending_payment->total_gst);
        })
        ->editColumn('total_payable', function($pending_payment) {
            return number_format($pending_payment->total_payable);
        })
        ->addColumn('phone_numbers', function($pending_payment) {
            $phone_numbers = $pending_payment->phone;

            if (!empty($pending_payment->phone2)) {
                $phone_numbers .= ' - ' . $pending_payment->phone2;
            }

            return $phone_numbers;
        })
        ->removeColumn('phone')
        ->removeColumn('phone2')
        ->addColumn('return_shipments_average_aging', function($pending_payment) {
            if ($pending_payment->returned_shipments != 0) {
                $shipments = 0;
                $days = 0;

                $now = Carbon::now()->startOfDay();

                $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id)->where('type', 1)->get();

                foreach ($pending_payment_shipments as $pending_payment_shipment) {
                    $created_at = Carbon::parse($pending_payment_shipment->created_at)->startOfDay();

                    $days += $created_at->diffInDays($now);

                    $shipments++;
                }

                $aging = ($days / $shipments) . 'd';

                return $aging;
            }
            else {
                return '-';
            }
        })
        ->addColumn('action', function($pending_payment) {
            $view_details_button = '<button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>';
            $make_payments_button = '<button type="button" class="dropdown-item make_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-credit-card"></i></div><div class="col-9 offset-1">Make Payment</div></button>';

            $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

            $dropdown .= $view_details_button;

            if (session('role_id') == 1 || in_array(60, session('permissions'))) {
              $dropdown .= $make_payments_button;
            }

            $dropdown .= '
                </div>
              </div>
            ';

            return $dropdown;
        })
        ->filterColumn('phone_numbers', function($query, $keyword) {
            $search = str_replace(' ', '', $keyword);

            if ($keyword != '') {
                $query->where('u.phone', 'like', '%'.$search.'%')->orWhere('u.phone2', 'like', '%'.$search.'%');
            }

            else {
                $query->whereRaw('false');
            }
        })
            ->filterColumn('bank', function($query, $keyword) {

                if ($keyword !='') {
                    $query->where('ub.id', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
        ->orderColumn('phone_numbers', 'u.phone $1, u.phone2 $1');

        return $datatables->make(true);
    }

    public function make_payments_delivered_shipments(Request $request) {
        $tracking_numbers = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->where('type', 0)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function make_payments_returned_shipments(Request $request) {
        $tracking_numbers = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->where('type', 1)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function make_payments_adjusted_shipments(Request $request) {
        $tracking_numbers = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->where('type', 2)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function make_payments_shipment_details(Request $request) {
        $details = array();

        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $request->id)->get();

        foreach ($pending_payment_shipments as $pending_payment_shipment) {
            $shipment = $pending_payment_shipment->shipment;

            $detail = array();

            $detail['tracking_number'] = $shipment->tracking_number;

            if ($pending_payment_shipment->type == 0) {
                $detail['type'] = 'Delivered';
            }
            else if ($pending_payment_shipment->type == 1) {
                $detail['type'] = 'Returned';
            }
            else {
                $detail['type'] = 'Adjusted';
            }

            $detail['amount'] = number_format($pending_payment_shipment->amount);
            $detail['charges'] = number_format($pending_payment_shipment->charges);
            $detail['gst'] = number_format($pending_payment_shipment->gst);
            $detail['deductable'] = number_format($pending_payment_shipment->charges + $pending_payment_shipment->gst);
            $detail['payable'] = number_format($pending_payment_shipment->payable);

            $details[] = $detail;
        }

        return $details;
    }

    public function make_payments_shipment_list(Request $request) {
        $pending_payment_shipments = PendingPaymentShipment::join('shipments as s', 'pending_payment_shipments.shipment_id', '=', 's.id')
        ->join('users as u', 's.user_id', '=', 'u.id')
        ->join('shipment_status as ss', 's.shipper_status_id', '=', 'ss.id')
        ->select('pending_payment_shipments.shipment_id as id', 'u.name as shipper', 's.tracking_number as shipment', 'pending_payment_shipments.type', 'ss.name as status', 'pending_payment_shipments.created_at', 'pending_payment_shipments.amount', 'pending_payment_shipments.charges', 'pending_payment_shipments.gst', 'pending_payment_shipments.payable');

        if ($request->has('ids')) {
           $pending_payment_shipments->whereIn('pending_payment_shipments.pending_payment_id', $request->ids);
        }
        else {
            $pending_payment_shipments->whereRaw('FALSE');
        }

        $datatables = Datatables::of($pending_payment_shipments)
        ->addColumn('deductable', function($pending_payment_shipments) {
            return number_format($pending_payment_shipments->charges + $pending_payment_shipments->gst);
        })
        ->addColumn('aging', function($pending_payment_shipments) {
            $now = Carbon::now()->startOfDay();

            $created_at = Carbon::parse($pending_payment_shipments->created_at)->startOfDay();

            return $created_at->diffInDays($now) . 'd';
        })
        ->editColumn('amount', function($pending_payment_shipment) {
            return number_format($pending_payment_shipment->amount);
        })
        ->editColumn('charges', function($pending_payment_shipment) {
            return number_format($pending_payment_shipment->charges);
        })
        ->editColumn('gst', function($pending_payment_shipment) {
            return number_format($pending_payment_shipment->gst);
        })
        ->editColumn('payable', function($pending_payment_shipment) {
            return number_format($pending_payment_shipment->payable);
        })
        ->editColumn('type', function($pending_payment_shipment) {
            if ($pending_payment_shipment->type == 0) {
                return 'Delivered';
            }
            else if ($pending_payment_shipment->type == 1) {
                return 'Returned';
            }
            else {
                return 'Adjusted';
            }
        })
        ->filterColumn('deductable', function ($query, $keyword) {
            $query->where(DB::raw('pending_payment_shipments.charges + pending_payment_shipments.gst'), '=', $keyword);
        })
        ->orderColumn('deductable', DB::raw('pending_payment_shipments.charges + pending_payment_shipments.gst') . ' $1');

        return $datatables->make(true);
    }

    public function make_payments_verify(Request $request) {
        $pending_payment_ids = explode(',', $request->pending_payment_ids);
        $shipment_ids = explode(',', $request->shipment_ids);

        $pending_payment_payables = array();

        foreach ($shipment_ids as $shipment_id) {
            $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id)->whereIn('pending_payment_id', $pending_payment_ids)->first();

            if (!isset($pending_payment_payables[$pending_payment_shipment->pending_payment_id])) {
                $pending_payment_payables[$pending_payment_shipment->pending_payment_id] = $pending_payment_shipment->payable;
            }
            else {
                $pending_payment_payables[$pending_payment_shipment->pending_payment_id] = $pending_payment_payables[$pending_payment_shipment->pending_payment_id] + $pending_payment_shipment->payable;
            }
        }

        $negative_payments = array();

        foreach ($pending_payment_payables as $pending_payment_id => $payable) {
            if ($payable < 0) {
                $negative_payments[] = PendingPayment::find($pending_payment_id)->shipper->name;
            }
        }

        if (empty($negative_payments)) {
            return ['status' => 0, 'negative_payments' => false];
        }
        else {
            return ['status' => 1, 'negative_payments' => $negative_payments];
        }
    }

    public function make_payments_export_bank_order(Request $request) {
        $done_payment_ids = explode(',', $request->done_payment_ids);

        $filename = 'sonic_bank_order';

        $details = array();

        $details[] = ['Payment ID', 'Client', 'Account Title', 'IBAN', 'Bank', 'Payable'];

        foreach ($done_payment_ids as $done_payment_id) {
            $filename .= '_' . $done_payment_id;

            $done_payment = DonePayment::find($done_payment_id);

            $shipper = User::find($done_payment->user_id);

            $payable = number_format(DonePaymentShipment::where('done_payment_id', $done_payment_id)->sum('payable'));

            $row = array();

            $row[] = str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
            $row[] = $shipper->name;
            $row[] = $shipper->bank->account_title;
            $row[] = $shipper->bank->iban;
            $row[] = $shipper->bank->bank->name;
            $row[] = $payable;

            $details[] = $row;
        }

        $filename .= '.xlsx';

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode('#,##0');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function make_payments_store(Request $request) {
        $pending_payment_ids = explode(',', $request->pending_payment_ids);
        $shipment_ids = explode(',', $request->shipment_ids);

        $pending_payment_shipment_ids = array();

        foreach ($shipment_ids as $shipment_id) {
            $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id)->whereIn('pending_payment_id', $pending_payment_ids)->first();

            $pending_payment_shipment_ids[$pending_payment_shipment->pending_payment_id][] = $shipment_id;
        }

        $done_payment_ids = array();

        foreach ($pending_payment_shipment_ids as $pending_payment_id => $shipment_ids) {
            $total_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->count();
            $selected_shipments = count($shipment_ids);

            $pending_payment = PendingPayment::find($pending_payment_id);

            if ($total_shipments == $selected_shipments) {
                $done_payment = new DonePayment();

                $done_payment->user_id = $pending_payment->user_id;
                $done_payment->total_shipments = $pending_payment->total_shipments;
                $done_payment->delivered_shipments = $pending_payment->delivered_shipments;
                $done_payment->returned_shipments = $pending_payment->returned_shipments;
                $done_payment->adjusted_shipments = $pending_payment->adjusted_shipments;

                $done_payment->save();

                $pending_payment->delete();

                foreach ($shipment_ids as $shipment_id) {
                    $pending_payment_shipment = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->where('shipment_id', $shipment_id)->first();

                    $done_payment_shipment = new DonePaymentShipment();

                    $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                    $done_payment_shipment->done_payment_id = $done_payment->id;
                    $done_payment_shipment->shipment_id = $shipment_id;
                    $done_payment_shipment->type = $pending_payment_shipment->type;
                    $done_payment_shipment->amount = $pending_payment_shipment->amount;
                    $done_payment_shipment->charges = $pending_payment_shipment->charges;
                    $done_payment_shipment->gst = $pending_payment_shipment->gst;
                    $done_payment_shipment->payable = $pending_payment_shipment->payable;

                    $done_payment_shipment->save();

                    $pending_payment_shipment->delete();

                    if ($done_payment_shipment->type == 0) {
                        $shipment = Shipment::find($shipment_id);

                        $shipment->payment_status_id = 1;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id());
                    }
                    else if ($done_payment_shipment->type == 1) {
                        $shipment = Shipment::find($shipment_id);

                        $shipment->payment_status_id = 5;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id());
                    }
                }

                $done_payment_ids[] = $done_payment->id;

                NotificationsController::send(20, $done_payment->id);
            }
            else {
                $done_payment = new DonePayment();

                $done_payment->user_id = $pending_payment->user_id;
                $done_payment->total_shipments = 0;
                $done_payment->delivered_shipments = 0;
                $done_payment->returned_shipments = 0;
                $done_payment->adjusted_shipments = 0;

                $done_payment->save();

                $total_shipments = 0;
                $delivered_shipments = 0;
                $returned_shipments = 0;
                $adjusted_shipments = 0;

                foreach ($shipment_ids as $shipment_id) {
                    $pending_payment_shipment = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->where('shipment_id', $shipment_id)->first();

                    $total_shipments++;

                    if ($pending_payment_shipment->type == 0) {
                        $delivered_shipments++;
                    }
                    else if ($pending_payment_shipment->type == 1) {
                        $returned_shipments++;
                    }
                    else {
                        $adjusted_shipments++;
                    }

                    $done_payment_shipment = new DonePaymentShipment();

                    $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                    $done_payment_shipment->done_payment_id = $done_payment->id;
                    $done_payment_shipment->shipment_id = $shipment_id;
                    $done_payment_shipment->type = $pending_payment_shipment->type;
                    $done_payment_shipment->amount = $pending_payment_shipment->amount;
                    $done_payment_shipment->charges = $pending_payment_shipment->charges;
                    $done_payment_shipment->gst = $pending_payment_shipment->gst;
                    $done_payment_shipment->payable = $pending_payment_shipment->payable;

                    $done_payment_shipment->save();

                    $pending_payment_shipment->delete();

                    if ($done_payment_shipment->type == 1) {
                        $shipment = Shipment::find($shipment_id);

                        $shipment->payment_status_id = 5;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id());
                    }
                    else {
                        $shipment = Shipment::find($shipment_id);

                        $shipment->payment_status_id = 1;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id());
                    }
                }

                $done_payment->total_shipments = $total_shipments;
                $done_payment->delivered_shipments = $delivered_shipments;
                $done_payment->returned_shipments = $returned_shipments;
                $done_payment->adjusted_shipments = $adjusted_shipments;

                $done_payment->save();

                $pending_payment->total_shipments = $pending_payment->total_shipments - $total_shipments;
                $pending_payment->delivered_shipments = $pending_payment->delivered_shipments - $delivered_shipments;
                $pending_payment->returned_shipments = $pending_payment->returned_shipments - $returned_shipments;
                $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments - $adjusted_shipments;

                $pending_payment->save();

                $done_payment_ids[] = $done_payment->id;

                NotificationsController::send(20, $done_payment->id);
            }
        }

        return redirect()->back()->with(['success' => 'Payment(s) has been Made.', 'print' => $done_payment_ids]);
    }

    public function done_payments_index() {
        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();

        return view('admin.finance.done_payments')->with(['banks'=>$banks,'company_banks'=>$company_banks]);
    }

    public function done_payments_list(Request $request) {
        $done_payments = DonePayment::join('users as u', 'done_payments.user_id', '=', 'u.id')
        ->join('cities as c', 'u.city_id', '=', 'c.id')
        ->join('user_bank_infos as ubi', 'done_payments.user_id', '=', 'ubi.user_id')
        ->join('banks_lists as ub', 'ubi.bank_name', '=', 'ub.id')
        ->join('done_payment_shipments as pps', 'done_payments.id', '=', 'pps.done_payment_id')
        ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
        ->select('done_payments.id as id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(pps.amount) as total_amount'), DB::raw('SUM(pps.charges) as total_charges'), DB::raw('SUM(pps.gst) as total_gst'), DB::raw('SUM(pps.payable) as total_payable'), 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status')
        ->groupBy('done_payments.id');

        if (session('role_id') != 1) {
            $done_payments = $done_payments->whereIn('c.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($done_payments)
        ->addColumn('id_padded', function ($done_payment) {
            return str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
        })
        ->filterColumn('done_payments.id', function ($query, $keyword) {
            return $query->where('done_payments.id', '=', $keyword);
        })
        ->addColumn('total_deductable', function($done_payment) {
            return number_format($done_payment->total_charges + $done_payment->total_gst);
        })
        ->editColumn('delivered_shipments', function($done_payment) {
            if ($done_payment->delivered_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->delivered_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('returned_shipments', function($done_payment) {
            if ($done_payment->returned_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->returned_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('adjusted_shipments', function($done_payment) {
            if ($done_payment->adjusted_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->adjusted_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('total_amount', function($done_payment) {
            return number_format($done_payment->total_amount);
        })
        ->editColumn('total_charges', function($done_payment) {
            return number_format($done_payment->total_charges);
        })
        ->editColumn('total_gst', function($done_payment) {
            return number_format($done_payment->total_gst);
        })
        ->editColumn('total_payable', function($done_payment) {
            return number_format($done_payment->total_payable);
        })
        ->addColumn('phone_numbers', function($done_payment) {
            $phone_numbers = $done_payment->phone;

            if (!empty($done_payment->phone2)) {
                $phone_numbers .= ' - ' . $done_payment->phone2;
            }

            return $phone_numbers;
        })
        ->removeColumn('phone')
        ->removeColumn('phone2')
        ->editColumn('status', function($done_payment) {
            if ($done_payment->status == 0) {
                return 'Processed';
            }
            else if ($done_payment->status == 1) {
                return 'Paid';
            }
            else if ($done_payment->status == 2) {
                return 'Reverted';
            }
            else {
                return 'Unknown';
            }
        })
        ->filterColumn('bank', function($query, $keyword) {

                if ($keyword !='') {
                    $query->where('ub.id', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
        })
            ->filterColumn('company_bank', function($query, $keyword) {

                if ($keyword !='') {
                    $query->where('b.id', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
        })
        ->addColumn('return_shipments_average_aging', function($done_payment) {
            if ($done_payment->returned_shipments != 0) {
                $shipments = 0;
                $days = 0;

                $now = Carbon::now()->startOfDay();

                $done_payment_shipments = donePaymentShipment::where('done_payment_id', $done_payment->id)->where('type', 1)->get();

                foreach ($done_payment_shipments as $done_payment_shipment) {
                    $created_at = Carbon::parse($done_payment_shipment->created_at)->startOfDay();

                    $days += $created_at->diffInDays($now);

                    $shipments++;
                }

                $aging = ($days / $shipments) . 'd';

                return $aging;
            }
            else {
                return '-';
            }
        })
        ->addColumn('action', function($done_payment) {
            return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>
                    <button type="button" class="dropdown-item update_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update Details</div></button>
                    <button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>
                  </div>
                </div>
            ';
        })
        ->filterColumn('phone_numbers', function($query, $keyword) {
            $search = str_replace(' ', '', $keyword);

            if ($keyword != '') {
                $query->where('u.phone', 'like', '%'.$search.'%')->orWhere('u.phone2', 'like', '%'.$search.'%');
            }

            else {
                $query->whereRaw('false');
            }
        })
        ->filterColumn('status', function($query, $keyword) {
            $keyword = strtolower($keyword);

            if ($keyword == 0) {
                $query->where('done_payments.status', '=', 0);
            }
            else if ($keyword == 1) {
                $query->where('done_payments.status', '=', 1);
            }
            else if ($keyword == 2) {
                $query->where('done_payments.status', '=', 2);
            }
            else {
                $query->whereRaw('false');
            }
        })
        ->orderColumn('phone_numbers', 'u.phone $1, u.phone2 $1');

        return $datatables->make(true);
    }

    public function done_payments_paid(Request $request) {
        foreach ($request->ids as $done_payment_id) {
            $done_payment = DonePayment::find($done_payment_id);

            $done_payment->status = 1;

            $done_payment->save();

            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                $shipment = $done_payment_shipment->shipment;

                if ($done_payment_shipment->type == 1) {
                    $shipment->payment_status_id = 7;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 7, Auth::id());
                }
                else {
                    $shipment->payment_status_id = 3;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 3, Auth::id());
                }
            }
        }

        return ['status' => 0, 'success' => 'Payment(s) marked Paid'];
    }

    public function done_payments_reverted(Request $request) {
        foreach ($request->ids as $done_payment_id) {
            $done_payment = DonePayment::find($done_payment_id);

            $done_payment->status = 2;

            $done_payment->save();

            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                $shipment = $done_payment_shipment->shipment;

                if ($done_payment_shipment->type == 1) {
                    $shipment->payment_status_id = 6;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 6, Auth::id());
                }
                else {
                    $shipment->payment_status_id = 2;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 2, Auth::id());
                }
            }
        }

        return ['status' => 0, 'success' => 'Payment(s) marked Reverted'];
    }

    public function done_payments_delivered_shipments(Request $request) {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 0)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function done_payments_returned_shipments(Request $request) {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 1)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function done_payments_adjusted_shipments(Request $request) {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 2)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function done_payments_details_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $done_payment = DonePayment::find($request->id);

        $shipper = $done_payment->shipper;

        $shipper_bank = $shipper->bank;

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Payment Details</title>

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
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="p-1">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Payment Details</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Payment ID</strong></td>
                              <td>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT). '</td>
                              <td rowspan="11" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($done_payment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client</strong></td>
                              <td>' . $shipper->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client Bank</strong></td>
                              <td>' . $shipper_bank->bank->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Account Title</strong></td>
                              <td>' . $shipper_bank->account_title . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>IBAN</strong></td>
                              <td>' . $shipper_bank->iban . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Company Bank</strong></td>
                              <td>' . (($done_payment->company_bank_id) ? $done_payment->company_bank->name : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Reference Number</strong></td>
                              <td>' . $done_payment->reference_number . '</td>
                            </tr>
      ';

      $shipment_details = '';

      $serial_number = 1;

      $total_amount = 0;
      $total_charges = 0;
      $total_gst = 0;
      $total_payable = 0;

      foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_details .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $shipment->order_id . '</td>
                              <td>' . $shipment->consignee_name . ' ' . $shipment->consignee_phone_number_1 . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->booking_type->booking_type . '</td>
                              <td>' . $shipment->actual_weight . '</td>
                              <td>' . number_format($done_payment_shipment->amount) . '</td>
                              <td>' . number_format($done_payment_shipment->charges) . '</td>
                              <td>' . number_format($done_payment_shipment->payable) . '</td>
                            </tr>
            ';

            $serial_number++;

            $total_amount += $done_payment_shipment->amount;
            $total_charges += $done_payment_shipment->charges;
            $total_gst += $done_payment_shipment->gst;
            $total_payable += $done_payment_shipment->payable;
      }

      $html .= '
                            <tr>
                              <td class="color secondary"><strong>Total Amount</strong></td>
                              <td>' . number_format($total_amount) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Charges</strong></td>
                              <td>' . number_format($total_charges) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total GST</strong></td>
                              <td>' . number_format($total_gst) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Payable</strong></td>
                              <td>' . number_format($total_payable) . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Order ID</strong></td>
                              <td class="color primary"><strong>Consignee</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Service Type</strong></td>
                              <td class="color primary"><strong>Actual Weight</strong></td>
                              <td class="color primary"><strong>Amount</strong></td>
                              <td class="color primary"><strong>Charges</strong></td>
                              <td class="color primary"><strong>Payable</strong></td>
                            </tr>
      ';

      $html .= $shipment_details;

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

    public function done_payments_details(Request $request) {
        $done_payment = DonePayment::find($request->id);

        $details = array();

        $details['reference_number'] = $done_payment->reference_number;
        $details['company_bank_id'] = $done_payment->company_bank_id;

        return $details;
    }

    public function done_payments_update_details(Request $request) {
        $done_payment = DonePayment::find($request->id);

        $done_payment->reference_number = $request->reference_number;
        $done_payment->company_bank_id = $request->company_bank_id;

        $done_payment->save();

        return ['status' => 0, 'success' => 'Details Updated'];
    }

    public function done_payments_export_to_excel(Request $request) {
        $done_payment = DonePayment::find($request->id);

        $filename = 'sonic_payment_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Order ID', 'Consignee Name', 'Consignee Phone', 'Destination', 'Service Type', 'Actual Weight', 'Amount', 'Charges', 'Payable'];

        $serial_number = 1;

        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->order_id;
            $row[] = $shipment->consignee_name;
            $row[] = $shipment->consignee_phone_number_1;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->booking_type->booking_type;
            $row[] = $shipment->actual_weight;
            $row[] = $done_payment_shipment->amount;
            $row[] = $done_payment_shipment->charges;
            $row[] = $done_payment_shipment->payable;

            $details[] = $row;

            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

}