<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\City;
use App\Http\Models\BookingType;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Shipment;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminFinanceController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function outstanding_sdn_index() {
      return view('admin.finance.outstanding_sdn');
    }

    public function outstanding_sdn_list(Request $request) {
        $station_deposit_notes = StationDepositNote::join('cities as h', 'station_deposit_notes.hub_id', '=', 'h.id')
        ->join('admins as a', 'station_deposit_notes.deposited_by', '=', 'a.id')
        ->join('banks_lists as b', 'station_deposit_notes.banks_list_id', '=', 'b.id')
        ->select('station_deposit_notes.id', 'station_deposit_notes.id as sdn_number', 'h.name as hub', 'station_deposit_notes.dncc_count', 'station_deposit_notes.sdn_delivered_shipments', 'station_deposit_notes.sdn_amount', 'station_deposit_notes.sdn_expense', 'station_deposit_notes.sdn_net_amount', 'a.name as deposited_by', 'b.name as bank', 'station_deposit_notes.created_at as deposited_at', 'station_deposit_notes.deposit_slip')
        ->where('station_deposit_notes.status', 1);

        $datatables = Datatables::of($station_deposit_notes)
        ->editColumn('sdn_number', function($station_deposit_note) {
            return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $station_deposit_note->sdn_number . '</span></button>';
        })
        ->editColumn('deposited_at', function($station_deposit_note) {
            return Carbon::parse($station_deposit_note->deposited_at)->format('d/m/Y H:i A');
        })
        ->editColumn('deposit_slip', function($station_deposit_note) {
            if ($station_deposit_note->deposit_slip) {
                return '<a class="btn btn-sm btn-outline-info align-middle" href="' . asset('uploads/sdn/' . $station_deposit_note->deposit_slip) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
            }
            else {
                return '';
            }
        })
        ->addColumn('action', function($station_deposit_note) {
            return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item reconcile_delivery_notes"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Reconcile Delivery Notes</div></button>
                    <button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>
                  </div>
                </div>
            ';
        });

        return $datatables->make(true);
    }

    public function outstanding_sdn_delivery_notes_list(Request $request) {
        $delivery_notes = StationDepositNote::join('delivery_note_station_deposit_notes as dnsdn', 'station_deposit_notes.id', '=', 'dnsdn.station_deposit_note_id')
        ->join('delivery_notes as dn', 'dnsdn.delivery_note_id', '=', 'dn.id')
        ->join('cities as h', 'dn.hub_id', '=', 'h.id')
        ->join('riders as ri', 'dn.rider_id', '=', 'ri.id')
        ->join('routes as ro', 'dn.route_id', '=', 'ro.id')
        ->join('admins as a', 'dn.admin_id', '=', 'a.id')
        ->select('dn.id', 'dn.id as delivery_note_number', 'h.name as hub', 'ri.name as rider', 'ro.code as route_code', 'ro.start as route_start', 'ro.end as route_end', 'dn.shipments_count as shipments', 'dn.delivered_shipments', 'a.name as assigned_by', 'dn.created_at as assigned_at', 'a.name as updated_by', 'dn.updated_at', 'dn.received_cod_amount as dncc_amount', 'dn.expense');

        if ($request->has('id')) {
           $delivery_notes->where('station_deposit_notes.id', $request->id);
        }
        else {
            $delivery_notes->whereRaw('FALSE');
        }

        $datatables = Datatables::of($delivery_notes)
        ->addColumn('route', function ($delivery_note) {
            return $delivery_note->route_code . ' (' . $delivery_note->route_start . ' to ' . $delivery_note->route_end . ')';
        })
        ->filterColumn('route', function($query, $keyword) {
            $query->where('ro.code', 'like', '%' . $keyword . '%')->orWhere('ro.start', 'like', '%' . $keyword . '%')->orWhere('ro.end', 'like', '%' . $keyword . '%');
        });

        return $datatables->make(true);
    }

    public function outstanding_sdn_reconcile_delivery_notes(Request $request) {
        $station_deposit_note = StationDepositNote::find($request->station_deposit_note_id);

        $station_deposit_note->sdn_amount = $request->total_dncc_amount;
        $station_deposit_note->sdn_expense = $request->total_expense;
        $station_deposit_note->sdn_net_amount = $request->total_net_amount;
        $station_deposit_note->status = 2;

        $station_deposit_note->save();

        $station_deposit_delivery_note_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->station_deposit_note_id)->pluck('delivery_note_id')->toArray();

        $delivery_note_ids = explode(',', $request->delivery_note_ids);

        $update_delivery_note_ids = array_intersect($delivery_note_ids, $station_deposit_delivery_note_ids);
        $update_station_deposit_delivery_note_ids = array_diff($station_deposit_delivery_note_ids, $delivery_note_ids);

        foreach ($update_delivery_note_ids as $delivery_note_id) {
            $delivery_note = DeliveryNote::find($delivery_note_id);

            $delivery_note->status = 2;

            $delivery_note->save();
        }

        foreach ($update_station_deposit_delivery_note_ids as $delivery_note_id) {
            $delivery_note = DeliveryNote::find($delivery_note_id);

            $delivery_note->status = 3;

            $delivery_note->save();

            foreach (DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->get() as $delivery_note_shipment) {
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);

                if (in_array($shipment->shipper_status_id, [14, 16, 30, 31, 36, 37, 38, 39, 40, 41])) {
                    $delivery_note_shipment->status = 6;

                    $delivery_note_shipment->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Station Deposit No.' . $request->station_deposit_note_id . ' has been Reconciled');
    }

    public function outstanding_sdn_export_to_excel(Request $request) {
        $delivery_note_station_deposit_notes = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->id);

        if ($delivery_note_station_deposit_notes->exists()) {
            $delivery_note_station_deposit_notes = $delivery_note_station_deposit_notes->get();

            $details = array();

            $details[] = ['S. No.', 'DNCC No.', 'Hub', 'Rider', 'Route', 'Shipments', 'Delivered Shipments', 'DNCC Amount', 'Expense', 'Net Amount'];

            $serial_number = 1;

            foreach ($delivery_note_station_deposit_notes as $delivery_note_station_deposit_note) {
                $delivery_note = DeliveryNote::find($delivery_note_station_deposit_note->delivery_note_id);

                $row = array();

                $row[] = $serial_number;
                $row[] = $delivery_note->id;
                $row[] = $delivery_note->hub->name;
                $row[] = $delivery_note->rider->name;
                $row[] = $delivery_note->route->code . ' (' . $delivery_note->route->start . ' to ' . $delivery_note->route->end . ')';
                $row[] = $delivery_note->shipments_count;
                $row[] = $delivery_note->delivered_shipments;
                $row[] = $delivery_note->received_cod_amount;
                $row[] = $delivery_note->expense;
                $row[] = ($delivery_note->net_amount) ? $delivery_note->net_amount : '0';

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

        return view('admin.finance.outstanding_shipments')->with(['hubs' => $hubs, 'booking_types' => $booking_types]);
    }

    public function outstanding_shipments_list(Request $request) {
        $shipments = DeliveryNoteShipment::join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
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
        ->join('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
        ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as status', 'sj.updated_at as status_updated_at', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'dnsdn.station_deposit_note_id as sdn', 'sjd.created_at as delivered_at')
        ->where('delivery_note_shipments.status', '=', 6);

        $datatables = Datatables::of($shipments)
        ->editColumn('status_updated_at', function($shipment) {
            return Carbon::parse($shipment->status_updated_at)->format('d/m/Y H:i A');
        })
        ->addColumn('aging', function($shipment) {
            $updated_at = Carbon::parse($shipment->status_updated_at)->startOfDay();

            $now = Carbon::now()->startOfDay();

            return $updated_at->diffInDays($now) . 'd';
        })
        ->addColumn('action', function($shipment) {
            return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item resolve"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">Resolve</div></button>
                    <button type="button" class="dropdown-item adjust_in_payment"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Adjust in Payment</div></button>
                  </div>
                </div>
            ';
        })
        ->filterColumn('aging', function($query, $keyword) {
            $search = str_replace('d', '', str_replace(' ', '', $keyword));

            if (filter_var($search, FILTER_VALIDATE_INT)) {
                $date = Carbon::now();

                $date = $date->subDays($search);

                $query->whereDate('sj.updated_at', '>=', $date->toDateString());
            }
            else {
                $query->whereRaw($search);
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
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $request->id)->where('status', 6);

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
        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $request->id)->where('status', 6);

        if ($delivery_note_shipment->exists()) {
            $delivery_note_shipment = $delivery_note_shipment->first();

            $delivery_note_shipment->status = 8;

            $delivery_note_shipment->save();

            return ['status' => 0, 'success' => 'Shipment has been marked Resolved'];
        }
        else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
        }
    }

}