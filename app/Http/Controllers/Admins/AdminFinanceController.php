<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Rider;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\Zone;
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
use App\Http\Models\PendingInvoiceShipment;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceShipment;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use NumberToWords\NumberToWords;

class AdminFinanceController extends Controller
{
    private function amount_to_words($amount) {
        $number_to_words = new NumberToWords();
        $number_transformer = $number_to_words->getNumberTransformer('en');

        $amount_in_words = $number_transformer->toWords($amount, 'PKR');

        $last_position = strrpos($amount_in_words, ' ');

        if ($last_position !== FALSE) {
            $amount_in_words = substr_replace($amount_in_words, ' & ', $last_position, strlen(' '));
        }

        $amount_in_words = str_replace('-', ' ', $amount_in_words);

        $amount_in_words = ucwords($amount_in_words);

        return $amount_in_words;
    }

    static private function gst($zone_id) {
        $zone = Zone::find($zone_id);

        if ($zone) {
            return $zone->gst;
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
        $banks = BanksList::where('affiliate', 1)->get();

        return view('admin.finance.outstanding_sdn')->with(['banks'=>$banks]);
    }

    public function outstanding_sdn_list(Request $request) {
        $station_deposit_notes = StationDepositNote::join('cities as h', 'station_deposit_notes.hub_id', '=', 'h.id')
        ->join('admins as a', 'station_deposit_notes.deposited_by', '=', 'a.id')
        ->join('banks_lists as b', 'station_deposit_notes.banks_list_id', '=', 'b.id')
        ->select('station_deposit_notes.id', 'station_deposit_notes.id as sdn_number', 'h.name as hub', 'station_deposit_notes.dncc_count', 'station_deposit_notes.dncc_count as dncc_count_link', 'station_deposit_notes.sdn_delivered_shipments', 'station_deposit_notes.sdn_delivered_shipments as delivered_shipments_link', 'station_deposit_notes.sdn_amount', 'a.name as deposited_by', 'b.name as bank', 'station_deposit_notes.created_at as deposited_at', 'station_deposit_notes.deposit_slip')
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
        ->editColumn('dncc_count_link', function($station_deposit_note) {
            if ($station_deposit_note->dncc_count != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $station_deposit_note->dncc_count . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('delivered_shipments_link', function($station_deposit_note) {
            if ($station_deposit_note->sdn_delivered_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $station_deposit_note->sdn_delivered_shipments . '</button>';
            }
            else {
                return 0;
            }
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
    //sdn_list
    public function outstanding_sdn_dncc(Request $request){
        $sdn_id = $request->input('sdn_id');
        $sdn_details = StationDepositNote::find($sdn_id);
        $dn_list = $sdn_details->delivery_notes_list;
        if ($dn_list->count() != 0) {
            $delivery_notes = array();
            foreach ($dn_list as $notes) {
                $delivery_notes[] = $notes->delivery_note_id;
            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'delivery_notes' => $delivery_notes];
        }
        else {
            return ['status' => 0, 'success' => 'No Booked Shipments', 'delivery_notes' => FALSE];
        }
    }
    public function outstanding_sdn_shipments_delivered(Request $request){
        $sdn_id = $request->input('sdn_id');
        $sdn_details = StationDepositNote::find($sdn_id);
        $dn_list = $sdn_details->delivery_notes_list;
        if($dn_list->count() != 0){
            $shipments = array();
            foreach ($dn_list as $note){
                $shipment_ids = DeliveryNoteShipment::where('delivery_note_id',$note->delivery_note_id)->where('status','>',1)->select('shipment_id')->get();
                foreach ($shipment_ids as $id){
                    $shipments[$note->delivery_note_id][] = Shipment::find($id)->pluck('tracking_number');
                }
            }
            return ['status' => 0, 'success' => 'Delivered Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivered Shipments', 'shipments' => FALSE];

        }

    }
    //dncc print
    public function outstanding_sdn_dncc_print(Request $request)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note Cash Collection</title>

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
                      
                      .w-150 {
                        width: 150px;
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
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $delivery_note_data = $delivery_note->first();
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->save();
            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(14, 16, 30, 36, 37);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status','>',1)->where('status','!=',8)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
//                    $shipment = Shipment::find($parcel->shipment_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '') . '</td>
                            <td>' . (($shipment->booking_type_id == 2) ? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                            
                          </tr>
            ';
                $total_cod_amount += $shipment->received_amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>';
            if ($request->has('temporary') && ($request->temporary != null)) {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Temporary Cash Collection</strong></td>';
            } else {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>';
            }


            $main_details .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
            $html .= '
                      <div class="mt-2 manual_form">
                      <div class="row  mt-1">
                         <div class="col">
                            <div class="text-right">
                                <span class="d-inline-block w-150 text-left"><strong>DNCC Amount</strong></span>
                                <strong>Rs. '.number_format($total_cod_amount).'</strong>
                            </div>
                          </div>
                        </div>
                        <hr>
                        
                        <div class="row justify-content-center align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Signature</strong>
                            </div>
                          </div>
                        </div>
                      </div>
        ';
        }

//        return $shipments;


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
        ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as status', 'sj.updated_at as status_updated_at', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'delivery_note_shipments.delivery_note_id as dncc_link', 'dnsdn.station_deposit_note_id as sdn', 'dnsdn.station_deposit_note_id as sdn_link', 'sjd.created_at as delivered_at')
        ->whereIn('delivery_note_shipments.status', [4, 5, 6]);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'data-dncc' => function ($shipments) {
                    return $shipments->dncc;
                },
                'data-sdn' => function ($shipments) {
                    return $shipments->sdn;
                },
            ])
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
        ->editColumn('dncc_link', function($shipment) {
            if ($shipment->dncc) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipment->dncc, 6, '0', STR_PAD_LEFT) . '</span></button>';
            }
            else {
                return '';
            }
        })
        ->editColumn('sdn', function ($shipment) {
            if ($shipment->sdn) {
                return str_pad($shipment->sdn, 6, '0', STR_PAD_LEFT);
            }
            else {
                return '';
            }
        })
        ->editColumn('sdn_link', function($shipment) {
            if ($shipment->sdn) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipment->sdn, 6, '0', STR_PAD_LEFT) . '</span></button>';
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

    //print dncc
    public function outstanding_shipments_dncc_print(Request $request)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note Cash Collection</title>

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
                      
                      .w-150 {
                        width: 150px;
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
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $delivery_note_data = $delivery_note->first();
            $delivery_note_data->last_updated_at= Carbon::now();
            $delivery_note_data->save();
            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(14, 16, 30, 36, 37);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status','>',1)->where('status','!=',8)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
//                    $shipment = Shipment::find($parcel->shipment_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '') . '</td>
                            <td>' . (($shipment->booking_type_id == 2) ? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                            
                          </tr>
            ';
                $total_cod_amount += $shipment->received_amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>';
            if ($request->has('temporary') && ($request->temporary != null)) {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Temporary Cash Collection</strong></td>';
            } else {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>';
            }


            $main_details .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
            $html .= '
                      <div class="mt-2 manual_form">
                      <div class="row  mt-1">
                         <div class="col">
                            <div class="text-right">
                                <span class="d-inline-block w-150 text-left"><strong>DNCC Amount</strong></span>
                                <strong>Rs. '.number_format($total_cod_amount).'</strong>
                            </div>
                          </div>
                        </div>
                        <hr>
                        
                        <div class="row justify-content-center align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Signature</strong>
                            </div>
                          </div>
                        </div>
                      </div>
        ';
        }

//        return $shipments;


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
    public function outstanding_shipments_sdn_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Station Deposit Note</title>

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
      ';

        $sdn = StationDepositNote::where('id', $request->id);
        if ($sdn->exists()) {
            $total_dncc = 0;

//            $shipments = DeliveryNoteShipment::where('delivery_note_id',$request->id)->select('shipment_id')->get();
            $dncc_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->id)->select('delivery_note_id')->get();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>DN No.</strong></td>
                            <td class="color primary"><strong>Rider Name</strong></td>
                            <td class="color primary"><strong>Route</strong></td>
                            <td class="color primary"><strong>Total No. Of Shipments</strong></td>
                            <td class="color primary"><strong>No. Of Delivered Shipments</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($dncc_ids as $dncc) {
                $total_dncc++;
                $dncc_note = DeliveryNote::find($dncc->delivery_note_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_dncc . '</td>
                            <td>' . str_pad($dncc_note->id, 6, '0', STR_PAD_LEFT) . '</td>
                            <td>' . $dncc_note->rider->name . '</td>
                            <td>' . $dncc_note->route->code . '( ' . $dncc_note->route->start . ' to ' . $dncc_note->route->end . ' )' . '</td>
                            <td>' . $dncc_note->shipments_count . '</td>
                            <td>' . $dncc_note->delivered_shipments . '</td>
                            <td>Rs ' . number_format($dncc_note->received_cod_amount) . '</td>
                            
                          </tr>
            ';

                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $station_note_details = StationDepositNote::where('id', $request->id)->first();
            $city_name = $station_note_details->hub->name;
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Station Deposit Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Hub Name</strong></td>
                            <td>' . $city_name . '</td>
                            <td rowspan="7" class="text-center align-middle p-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>                         
                          <tr>
                            <td class="color secondary"><strong>Total DNCC Amount</strong></td>
                            <td>Rs ' . number_format($station_note_details->sdn_amount) . '</td>
                          </tr>
                         <!-- <tr>
                            <td class="color secondary"><strong>Total Expenses</strong></td>
                            <td>' . number_format($station_note_details->sdn_expense) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Net Amount</strong></td>
                            <td>' . number_format($station_note_details->sdn_net_amount) . '</td>
                          </tr>-->
                          
                          <tr>
                            <td class="color secondary"><strong>Bank Name</strong></td>
                            <td>' . $station_note_details->bank->name . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;

        }

//        return $shipments;


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
    //outstanding shipments print end
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

            $shipment = Shipment::find($request->id);

            $delivery_note_shipment->status = 8;

            $delivery_note_shipment->save();

            $delivery_note = $delivery_note_shipment->delivery_note;

            $delivery_note_amount = $delivery_note->received_cod_amount - $shipment->amount;

            if ($delivery_note_amount < 0) {
                $delivery_note_amount = 0;
            }

            $delivery_note->delivered_shipments = $delivery_note->delivered_shipments - 1;
            $delivery_note->received_cod_amount = $delivery_note_amount;

            $delivery_note->save();

            $delivery_note_station_deposit_note = $delivery_note->delivery_note_station_deposit_note;

            if ($delivery_note_station_deposit_note) {
                $station_deposit_note = $delivery_note_station_deposit_note->station_deposit_note;

                $station_deposit_note_amount = $station_deposit_note->sdn_amount - $shipment->amount;

                if ($station_deposit_note_amount < 0) {
                    $station_deposit_note_amount = 0;
                }

                $station_deposit_note->sdn_delivered_shipments = $station_deposit_note->sdn_delivered_shipments - 1;
                $station_deposit_note->sdn_amount = $station_deposit_note_amount;
                $station_deposit_note->sdn_net_amount = $station_deposit_note_amount;

                $station_deposit_note->save();
            }

            $shipment->shipper_status_id = 13;
            $shipment->consignee_status_id = 13;

            $shipment->payment_status_id = 4;

            $shipment->save();

            $payment_mode = $shipment->user->bank->payment_mode;

            if ($payment_mode == 'IBFT') {
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $request->id);

                if ($pending_payment_shipment->exists()) {
                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                    $this->adjust_payment($pending_payment_shipment->pending_payment_id, $request->id, 0);
                }
                else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $request->id);

                    if ($done_payment_shipment->exists()) {
                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                        $this->adjust_payment($done_payment_shipment->done_payment_id, $request->id, 1);
                    }
                }
            }
            else {
                $payment_shipment_id = NULL;
                $payment_type = NULL;
                $invoice_shipment_id = NULL;
                $invoice_type = NULL;

                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                if ($pending_payment_shipment->exists()) {
                    $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                    $payment_shipment_id = $pending_payment_shipment->id;
                    $payment_type = 0;
                }
                else {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                    if ($done_payment_shipment->exists()) {
                        $done_payment_shipment = $done_payment_shipment->latest()->first();

                        $payment_shipment_id = $done_payment_shipment->id;
                        $payment_type = 1;
                    }
                }

                $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                if ($pending_invoice_shipment->exists()) {
                    $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                    $invoice_shipment_id = $pending_invoice_shipment->id;
                    $invoice_type = 0;
                }
                else {
                    $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                    if ($done_invoice_shipment->exists()) {
                        $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                        $invoice_shipment_id = $done_invoice_shipment->id;
                        $invoice_type = 1;
                    }
                }

                if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                    self::adjust_invoice($shipment->id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type);
                }
            }

            ShipmentsPaymentJourneyController::add($request->id, 4, Auth::id());

            ShipmentsJourneyController::add($request->id, 13, 13, NULL, NULL, NULL, Auth::id());

            NotificationsController::send(21, $request->id, Auth::id());

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
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                if (!$pending_payment_shipment->exists()) {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                    if (!$done_payment_shipment->exists()) {
                        $payment_mode = $shipment->user->bank->payment_mode;

                        if ($payment_mode != 'IBFT') {
                            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                            if ($pending_invoice_shipment->exists()) {
                                return ['status' => 1, 'error' => 'A Invoice Payment of given Shipment is in Pending'];
                            }
                            else {
                                $invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                if ($invoice_shipment->exists()) {
                                    return ['status' => 1, 'error' => 'A Invoice Payment of given Shipment has already been Processed'];
                                }
                            }
                        }

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
                        return ['status' => 1, 'error' => 'A Payment of given Shipment has already been Processed'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'A Payment of given Shipment is in Pending'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Shipment has already been Delivered'];
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

    public function change_shipment_weight_index() {
        return view('admin.finance.change_shipment_weight');
    }

    public function change_shipment_weight_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

            if (!in_array($shipment->shipper_status_id, [14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 44, 45, 46])) {
                $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment->id);

                if (!$pending_payment_shipment->exists()) {
                    $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment->id);

                    if (!$done_payment_shipment->exists()) {
                        $payment_mode = $shipment->user->bank->payment_mode;

                        if ($payment_mode != 'IBFT') {
                            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment->id);

                            if ($pending_invoice_shipment->exists()) {
                                return ['status' => 1, 'error' => 'A Invoice Payment of given Shipment is in Pending'];
                            }
                            else {
                                $invoice_shipment = InvoiceShipment::where('shipment_id', $shipment->id);

                                if ($invoice_shipment->exists()) {
                                    return ['status' => 1, 'error' => 'A Invoice Payment of given Shipment has already been Processed'];
                                }
                            }
                        }

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

                        return ['status' => 0, 'success' => 'Shipment\'s weight can be changed', 'details' => $details];
                    }
                    else {
                        return ['status' => 1, 'error' => 'A Payment of given Shipment has already been Processed'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'A Payment of given Shipment is in Pending'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Shipment has already been Delivered'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function change_shipment_weight_store(Request $request) {
        $shipment_id = $request->input('shipment_id');
        $weight = $request->input('weight');

        $shipment = Shipment::find($shipment_id);

        $shipment->actual_weight = $weight;

        $shipment->save();

        ShipmentChargesController::weight($shipment_id);
        ShipmentChargesController::fuel_surcharge($shipment_id);

        return redirect()->route('admin.finance.change_shipment_weight.index')->with('success', 'Shipment\'s weight has been changed');
    }

    public function add_shipment_adjustment_index() {
        return view('admin.finance.add_shipment_adjustment');
    }

    public function add_shipment_adjustment_shipment_details(Request $request) {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);

        if ($shipment->exists()) {
            $shipment = $shipment->first();

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

            return ['status' => 0, 'success' => 'Shipment\'s weight can be changed', 'details' => $details];
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment exists with given Tracking Number'];
        }
    }

    public function add_shipment_adjustment_store(Request $request) {
        $shipment_id = $request->input('shipment_id');
        $payable = str_replace(',', '', $request->input('payable'));

        $shipment = Shipment::find($shipment_id);

        $amount = 0;
        $charges = 0;
        $gst = 0;

        if ($payable > 0) {
            $payment_type = 0;
        }
        else {
            $payment_mode = $shipment->user->bank->payment_mode;

            if ($payment_mode == 'IBFT') {
                $payment_type = 0;
            }
            else {
                $payment_type = 1;
            }
        }

        if ($payment_type == 0) {
            $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

            if ($pending_payment->exists()) {
                $pending_payment = $pending_payment->first();

                $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                $pending_payment->save();
            }
            else {
                $pending_payment = new PendingPayment();

                $pending_payment->user_id = $shipment->user_id;
                $pending_payment->total_shipments = 1;
                $pending_payment->delivered_shipments = 0;
                $pending_payment->returned_shipments = 0;
                $pending_payment->adjusted_shipments = 1;

                $pending_payment->save();
            }

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
            $pending_invoice_shipment = new PendingInvoiceShipment();

            $pending_invoice_shipment->shipment_id = $shipment_id;
            $pending_invoice_shipment->type = 2;
            $pending_invoice_shipment->charges = $charges;
            $pending_invoice_shipment->gst = $gst;
            $pending_invoice_shipment->invoice_amount = $payable;

            $pending_invoice_shipment->save();
        }

        return redirect()->route('admin.finance.add_shipment_adjustment.index')->with('success', 'Shipment\'s adjustment has been added');
    }

    static public function return_confirmed_revert($shipment_id) {
        $shipment = Shipment::find($shipment_id);

        $shipment->return_charges = NULL;
        $shipment->payment_status_id = 4;

        $shipment->save();

        $payment_mode = $shipment->user->bank->payment_mode;

        if ($payment_mode == 'IBFT') {
            $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

            if ($pending_payment_shipment->exists()) {
                $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                self::adjust_payment($pending_payment_shipment->pending_payment_id, $shipment_id, 0);
            }
            else {
                $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                if ($done_payment_shipment->exists()) {
                    $done_payment_shipment = $done_payment_shipment->latest()->first();

                    self::adjust_payment($done_payment_shipment->done_payment_id, $shipment_id, 1);
                }
            }
        }
        else {
            $payment_shipment_id = NULL;
            $payment_type = NULL;
            $invoice_shipment_id = NULL;
            $invoice_type = NULL;

            $pending_payment_shipment = PendingPaymentShipment::where('shipment_id', $shipment_id);

            if ($pending_payment_shipment->exists()) {
                $pending_payment_shipment = $pending_payment_shipment->latest()->first();

                $payment_shipment_id = $pending_payment_shipment->id;
                $payment_type = 0;
            }
            else {
                $done_payment_shipment = DonePaymentShipment::where('shipment_id', $shipment_id);

                if ($done_payment_shipment->exists()) {
                    $done_payment_shipment = $done_payment_shipment->latest()->first();

                    $payment_shipment_id = $done_payment_shipment->id;
                    $payment_type = 1;
                }
            }

            $pending_invoice_shipment = PendingInvoiceShipment::where('shipment_id', $shipment_id);

            if ($pending_invoice_shipment->exists()) {
                $pending_invoice_shipment = $pending_invoice_shipment->latest()->first();

                $invoice_shipment_id = $pending_invoice_shipment->id;
                $invoice_type = 0;
            }
            else {
                $done_invoice_shipment = InvoiceShipment::where('shipment_id', $shipment_id);

                if ($done_invoice_shipment->exists()) {
                    $done_invoice_shipment = $done_invoice_shipment->latest()->first();

                    $invoice_shipment_id = $done_invoice_shipment->id;
                    $invoice_type = 1;
                }
            }

            if ($payment_shipment_id != NULL || $invoice_shipment_id != NULL) {
                self::adjust_invoice($shipment_id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type);
            }
        }

        ShipmentsPaymentJourneyController::add($shipment_id, 4, Auth::id());
    }

    static public function add_payment($shipment_id, $type) {
        $shipment = Shipment::find($shipment_id);

        $amount = $shipment->amount;

        if (!$shipment->packaging_material_request) {
            if ($type == 0) {
                $charges = $shipment->weight_charges + $shipment->cash_handling_charges + $shipment->insurance_charges + $shipment->fuel_surcharge + $shipment->replacement_charges + $shipment->try_and_buy_charges;
                $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->zone_id)), 0, PHP_ROUND_HALF_DOWN);

                $payable = $amount - ($charges + $gst);
            }
            else {
                $amount = 0;
                $charges = $shipment->weight_charges + $shipment->insurance_charges + $shipment->return_charges + $shipment->fuel_surcharge;
                $gst = ROUND(($charges * self::gst($shipment->pickup_address->city->zone_id)), 0, PHP_ROUND_HALF_DOWN);

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

            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;

            if ($type == 0) {
                $pending_payment->delivered_shipments = $pending_payment->delivered_shipments + 1;
            }
            else {
                $pending_payment->returned_shipments = $pending_payment->returned_shipments + 1;
            }

            $pending_payment->save();
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
        }

        $payment_mode = $shipment->user->bank->payment_mode;

        $pending_payment_shipment = new PendingPaymentShipment();

        if ($payment_mode == 'IBFT') {
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
            if (!$shipment->packaging_material_request) {
                if ($amount != 0) {
                    $pending_payment_shipment->pending_payment_id = $pending_payment->id;
                    $pending_payment_shipment->shipment_id = $shipment_id;
                    $pending_payment_shipment->type = $type;
                    $pending_payment_shipment->amount = $amount;
                    $pending_payment_shipment->charges = 0;
                    $pending_payment_shipment->gst = 0;
                    $pending_payment_shipment->payable = $amount;

                    $pending_payment_shipment->save();
                }

                $pending_invoice_shipment = new PendingInvoiceShipment();

                $pending_invoice_shipment->shipment_id = $shipment_id;
                $pending_invoice_shipment->type = $type;
                $pending_invoice_shipment->charges = $charges;
                $pending_invoice_shipment->gst = $gst;
                $pending_invoice_shipment->invoice_amount = $charges + $gst;

                $pending_invoice_shipment->save();
            }
            else {
                $pending_invoice_shipment = new PendingInvoiceShipment();

                $pending_invoice_shipment->shipment_id = $shipment_id;
                $pending_invoice_shipment->type = $type;
                $pending_invoice_shipment->charges = $charges;
                $pending_invoice_shipment->gst = $gst;
                $pending_invoice_shipment->invoice_amount = $charges + $gst;

                $pending_invoice_shipment->save();
            }
        }
    }

    static private function adjust_payment($payment_id, $shipment_id, $payment_type) {
        if ($payment_type == 0) {
            $payment_shipment = PendingPaymentShipment::where('pending_payment_id', $payment_id)->where('shipment_id', $shipment_id)->latest()->first();
        }
        else {
            $payment_shipment = DonePaymentShipment::where('done_payment_id', $payment_id)->where('shipment_id', $shipment_id)->latest()->first();
        }

        $shipment = Shipment::find($shipment_id);

        $amount = 0;
        $charges = 0;
        $gst = 0;
        $payable = 0 - $payment_shipment->payable;

        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

        if ($pending_payment->exists()) {
            $pending_payment = $pending_payment->first();

            $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
            $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

            $pending_payment->save();
        }
        else {
            $pending_payment = new PendingPayment();

            $pending_payment->user_id = $shipment->user_id;
            $pending_payment->total_shipments = 1;
            $pending_payment->delivered_shipments = 0;
            $pending_payment->returned_shipments = 0;
            $pending_payment->adjusted_shipments = 1;

            $pending_payment->save();
        }

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

    static private function adjust_invoice($shipment_id, $payment_shipment_id, $payment_type, $invoice_shipment_id, $invoice_type) {
        if ($payment_shipment_id) {
            if ($payment_type == 0) {
                $payment_shipment = PendingPaymentShipment::find($payment_shipment_id);
            }
            else {
                $payment_shipment = DonePaymentShipment::find($payment_shipment_id);
            }

            if ($payment_shipment) {
                $pending_invoice_shipment = new PendingInvoiceShipment();

                $pending_invoice_shipment->shipment_id = $shipment_id;
                $pending_invoice_shipment->type = 2;
                $pending_invoice_shipment->charges = 0;
                $pending_invoice_shipment->gst = 0;
                $pending_invoice_shipment->invoice_amount = $payment_shipment->payable;

                $pending_invoice_shipment->save();
            }
        }

        if ($invoice_shipment_id) {
            if ($invoice_type == 0) {
                $invoice_shipment = PendingInvoiceShipment::find($invoice_shipment_id);
            }
            else {
                $invoice_shipment = InvoiceShipment::find($invoice_shipment_id);
            }

            if ($invoice_shipment) {
                $shipment = Shipment::find($shipment_id);

                $amount = 0;
                $charges = 0;
                $gst = 0;
                $payable = $invoice_shipment->invoice_amount;

                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);

                if ($pending_payment->exists()) {
                    $pending_payment = $pending_payment->first();

                    $pending_payment->total_shipments = $pending_payment->total_shipments + 1;
                    $pending_payment->adjusted_shipments = $pending_payment->adjusted_shipments + 1;

                    $pending_payment->save();
                }
                else {
                    $pending_payment = new PendingPayment();

                    $pending_payment->user_id = $shipment->user_id;
                    $pending_payment->total_shipments = 1;
                    $pending_payment->delivered_shipments = 0;
                    $pending_payment->returned_shipments = 0;
                    $pending_payment->adjusted_shipments = 1;

                    $pending_payment->save();
                }

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

                $aging = round(($days / $shipments), 2) . 'd';

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
            $search = str_replace('-', '', $keyword);

            if ($keyword != '') {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                    ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                });
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


        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('shipments as s', 'pps.shipment_id', '=', 's.id')
            ->where('s.tracking_number', '=', $tracking_number);
        }

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
        ->select('pending_payment_shipments.id', 'u.name as shipper', 's.tracking_number as shipment', 'pending_payment_shipments.type', 'ss.name as status', 'pending_payment_shipments.created_at', 'pending_payment_shipments.amount', 'pending_payment_shipments.charges', 'pending_payment_shipments.gst', 'pending_payment_shipments.payable');

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
        ->filterColumn('type',function ($query,$keyword){
            if ($keyword == 0 || $keyword == 1 || $keyword == 2) {
                $query->where('pending_payment_shipments.type', '=', $keyword);
            }
            else{
                $query->whereIn('pending_payment_shipments.type', [0,1,2]);
            }
        })
        ->filterColumn('deductable', function ($query, $keyword) {
            $query->where(DB::raw('pending_payment_shipments.charges + pending_payment_shipments.gst'), '=', $keyword);
        })
        ->orderColumn('deductable', DB::raw('pending_payment_shipments.charges + pending_payment_shipments.gst') . ' $1');

        return $datatables->make(true);
    }

    public function make_payments_verify(Request $request) {
        $pending_payment_shipment_ids = explode(',', $request->pending_payment_shipment_ids);

        $pending_payment_payables = array();

        foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
            $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

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
        $pending_payment_shipment_ids = PendingPaymentShipment::whereIn('id', explode(',', $request->pending_payment_shipment_ids))->select('pending_payment_id', 'id')->get()->mapToGroups(function ($item, $key) {
                return [$item['pending_payment_id'] => $item['id']];
            })->toArray();

        $done_payment_ids = array();

        foreach ($pending_payment_shipment_ids as $pending_payment_id => $pending_payment_shipment_ids) {
            $total_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_id)->count();
            $selected_shipments = count($pending_payment_shipment_ids);

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

                foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
                    $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

                    $done_payment_shipment = new DonePaymentShipment();

                    $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                    $done_payment_shipment->done_payment_id = $done_payment->id;
                    $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                    $done_payment_shipment->type = $pending_payment_shipment->type;
                    $done_payment_shipment->amount = $pending_payment_shipment->amount;
                    $done_payment_shipment->charges = $pending_payment_shipment->charges;
                    $done_payment_shipment->gst = $pending_payment_shipment->gst;
                    $done_payment_shipment->payable = $pending_payment_shipment->payable;

                    $done_payment_shipment->save();

                    $pending_payment_shipment->delete();

                    if ($done_payment_shipment->type == 0) {
                        $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                        $shipment->payment_status_id = 1;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id());
                    }
                    else if ($done_payment_shipment->type == 1) {
                        $shipment = Shipment::find($pending_payment_shipment->shipment_id);

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

                foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
                    $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);

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
                    $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                    $done_payment_shipment->type = $pending_payment_shipment->type;
                    $done_payment_shipment->amount = $pending_payment_shipment->amount;
                    $done_payment_shipment->charges = $pending_payment_shipment->charges;
                    $done_payment_shipment->gst = $pending_payment_shipment->gst;
                    $done_payment_shipment->payable = $pending_payment_shipment->payable;

                    $done_payment_shipment->save();

                    $pending_payment_shipment->delete();

                    if ($done_payment_shipment->type == 1) {
                        $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                        $shipment->payment_status_id = 5;

                        $shipment->save();

                        ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id());
                    }
                    else {
                        $shipment = Shipment::find($pending_payment_shipment->shipment_id);

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

                $pending_payment->total_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipment->pending_payment_id)->count();
                $pending_payment->delivered_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipment->pending_payment_id)->where('type', 0)->count();
                $pending_payment->returned_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipment->pending_payment_id)->where('type', 1)->count();
                $pending_payment->adjusted_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment_shipment->pending_payment_id)->where('type', 2)->count();

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
        ->join('done_payment_shipments as dps', 'done_payments.id', '=', 'dps.done_payment_id')
        ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
        ->select('done_payments.id as id','done_payments.id as payment_id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(dps.amount) as total_amount'), DB::raw('SUM(dps.charges) as total_charges'), DB::raw('SUM(dps.gst) as total_gst'), DB::raw('SUM(dps.payable) as total_payable'), 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status')
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
        ->editColumn('payment_id', function($done_payment) {
            return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
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

                $aging = round(($days / $shipments), 2) . 'd';

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
            $search = str_replace('-', '', $keyword);

            if ($keyword != '') {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                    ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                });
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

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('shipments as s', 'dps.shipment_id', '=', 's.id')
            ->where('s.tracking_number', '=', $tracking_number);
        }

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

        $payment_mode = $shipper_bank->payment_mode;

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

                      .summary {
                        page-break-inside: avoid;
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

      $total_collection_amount = 0;
      $total_weight_charges = 0;
      $total_cash_handling_charges = 0;
      $total_insurance_charges = 0;
      $total_replacement_charges = 0;
      // $total_try_and_buy_charges = 0;
      $total_return_charges = 0;
      $total_packaging_material_charges = 0;
      $total_fuel_surcharge = 0;
      $total_gst = 0;
      $total_charges = 0;
      $total_adjustments = 0;
      $total_payable = 0;

      foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            }
            else if ($done_payment_shipment->type == 1) {
                $type = 'Returned';
            }
            else {
                $type = 'Adjusted';
            }

            $shipment_details .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $type . '</td>
                              <td>' . $shipment->order_id . '</td>
                              <td>' . $shipment->consignee_name . ' ' . $shipment->consignee_phone_number_1 . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->booking_type->booking_type . '</td>
                              <td>' . $shipment->actual_weight . '</td>
                              <td>' . number_format($done_payment_shipment->amount) . '</td>
                              <td>' . (($payment_mode == 'IBFT') ? number_format($shipment->weight_charges) : '0') . '</td>
                              <td>' . (($payment_mode == 'IBFT' && $done_payment_shipment->type == 0) ? number_format($shipment->cash_handling_charges) : '0') . '</td>
                              <td>' . (($done_payment_shipment->type == 2) ? number_format($done_payment_shipment->payable) : '0') . '</td>
                            </tr>
            ';

            $serial_number++;

            if ($payment_mode == 'IBFT') {
                if ($done_payment_shipment->type != 2) {
                    if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                        $total_cash_handling_charges += $shipment->cash_handling_charges;
                        $total_replacement_charges += $shipment->replacement_charges;
                        // $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                    }
                    else {
                        $total_return_charges += $shipment->return_charges;
                    }

                    $total_weight_charges += $shipment->weight_charges;

                    if ($shipment->packaging_material_request) {
                        $total_packaging_material_charges += $shipment->packaging_material_charges;
                    }

                    $total_insurance_charges += $shipment->insurance_charges;
                    $total_fuel_surcharge += $shipment->fuel_surcharge;
                }
                else {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_gst += $done_payment_shipment->gst;
                $total_charges += $done_payment_shipment->charges;
                $total_payable += $done_payment_shipment->payable;
            }
            else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                }
                else if ($done_payment_shipment->type == 2) {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            }
      }

      $shipment_details .= '
                            <tr>
                                <td colspan="7"></td>
                                <td class="color primary"><strong>Total</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_weight_charges) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_cash_handling_charges) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_adjustments) . '</strong></td>
                            </tr>
      ';

      $html .= '
                            <tr>
                              <td class="color secondary"><strong>Total Collection Amount (PKR)</strong></td>
                              <td>' . number_format($total_collection_amount) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Payable (PKR)</strong></td>
                              <td>' . number_format($total_payable) . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Type</strong></td>
                              <td class="color primary"><strong>Order ID</strong></td>
                              <td class="color primary"><strong>Consignee</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Service Type</strong></td>
                              <td class="color primary"><strong>Weight (kg)</strong></td>
                              <td class="color primary"><strong>Collection Amount (PKR)</strong></td>
                              <td class="color primary"><strong>Weight Charges (PKR)</strong></td>
                              <td class="color primary"><strong>Cash Handling Charges (PKR)</strong></td>
                              <td class="color primary"><strong>Adjustments (PKR)</strong></td>
                            </tr>
      ';

      $html .= $shipment_details;

      $html .= '
                          </tbody>
                        </table>

                        <div class="row">
                            <div class="col-6">
                                <table class="table table-sm table-bordered border summary">
                                  <tbody>
                                    <tr>
                                        <td class="color primary" colspan="2"><strong>Charges Summary (PKR)</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Weight Charges</strong></td>
                                        <td>' . number_format($total_weight_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Cash Handling Charges</strong></td>
                                        <td>' . number_format($total_cash_handling_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Insurance Charges</strong></td>
                                        <td>' . number_format($total_insurance_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Replacement Charges</strong></td>
                                        <td>' . number_format($total_replacement_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Return Charges</strong></td>
                                        <td>' . number_format($total_return_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Fuel Surcharge</strong></td>
                                        <td>' . number_format($total_fuel_surcharge) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Charges (w/o GST)</strong></td>
                                        <td>' . number_format($total_charges - $total_packaging_material_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total GST</strong></td>
                                        <td>' . number_format($total_gst) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Packaging Material Charges</strong></td>
                                        <td>' . number_format($total_packaging_material_charges) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Adjustments</strong></td>
                                        <td>' . number_format($total_adjustments) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color primary"><strong>Overall Charges</strong></td>
                                        <td class="color secondary"><strong>' . number_format($total_charges + $total_gst - $total_adjustments) . '</strong></td>
                                    </tr>
                                  </tbody>
                                </table>
                            </div>
                        </div>
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

        $details[] = ['S. No.', 'Tracking No.', 'Type', 'Order ID', 'Consignee Name', 'Consignee Phone', 'Destination', 'Service Type', 'Weight (kg)', 'Collection Amount (PKR)', 'Weight Charges (PKR)', 'Cash Handling Charges (PKR)', 'Adjustments (PKR)'];

        $serial_number = 1;

        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            }
            else if ($done_payment_shipment->type == 1) {
                $type = 'Returned';
            }
            else {
                $type = 'Adjusted';
            }

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $type;
            $row[] = $shipment->order_id;
            $row[] = $shipment->consignee_name;
            $row[] = $shipment->consignee_phone_number_1;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->booking_type->booking_type;
            $row[] = $shipment->actual_weight;
            $row[] = $done_payment_shipment->amount;
            $row[] = $shipment->weight_charges;
            $row[] = (($done_payment_shipment->type == 0) ? $shipment->cash_handling_charges : 0);
            $row[] = (($done_payment_shipment->type == 2) ? $done_payment_shipment->payable : 0);

            $details[] = $row;

            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function generate_invoices_index() {
        $users = User::whereIn('status', [3, 4])->get();

        $banks = BanksList::all();

        return view('admin.finance.generate_invoices')->with(['users' => $users, 'banks' => $banks]);
    }

    public function generate_invoices_list(Request $request) {
        $pending_invoice_shipments = PendingInvoiceShipment::join('shipments as s', 'pending_invoice_shipments.shipment_id', '=', 's.id')
        ->join('users as u', 's.user_id', '=', 'u.id')
        ->join('cities as c', 'u.city_id', '=', 'c.id')
        ->join('user_bank_infos as ubi', 's.user_id', '=', 'ubi.user_id')
        ->join('banks_lists as ub', 'ubi.bank_name', '=', 'ub.id')
        ->join('cities as bc', 'ubi.city_id', '=', 'bc.id')
        ->select('pending_invoice_shipments.id', 'u.name as shipper', 's.tracking_number', 's.tracking_number as tracking_number_link', 'pending_invoice_shipments.type', 'pending_invoice_shipments.created_at', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'pending_invoice_shipments.charges', 'pending_invoice_shipments.gst', 'pending_invoice_shipments.invoice_amount', 'ub.name as bank', 'ubi.bank_branch', 'ubi.account_no', 'ubi.account_title', 'ubi.iban', 'bc.name as account_city');

        if ($request->shipper && $request->from_date && $request->to_date) {
            $pending_invoice_shipments = $pending_invoice_shipments->where('s.user_id', $request->shipper)
            ->whereDate('pending_invoice_shipments.created_at', '>=', $request->from_date)
            ->whereDate('pending_invoice_shipments.created_at', '<=', $request->to_date);
        }
        else {
            $pending_invoice_shipments = $pending_invoice_shipments->whereRaw('FALSE');
        }

        $datatables = Datatables::of($pending_invoice_shipments)
            ->editColumn('tracking_number_link', function ($pending_invoice_shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$pending_invoice_shipments->tracking_number' class='tracking' target='_blank'>$pending_invoice_shipments->tracking_number</a></u>";
            })
        ->editColumn('type', function($pending_invoice_shipment) {
            if ($pending_invoice_shipment->type == 0) {
                return 'Delivered';
            }
            else if ($pending_invoice_shipment->type == 1) {
                return 'Returned';
            }
            else {
                return 'Adjusted';
            }
        })
        ->editColumn('charges', function($pending_invoice_shipment) {
            return number_format($pending_invoice_shipment->charges);
        })
        ->addColumn('phone_numbers', function($pending_invoice_shipment) {
            $phone_numbers = $pending_invoice_shipment->phone;

            if (!empty($pending_invoice_shipment->phone2)) {
                $phone_numbers .= ' - ' . $pending_invoice_shipment->phone2;
            }

            return $phone_numbers;
        })
        ->removeColumn('phone')
        ->removeColumn('phone2')
        ->filterColumn('type', function ($query, $keyword) {
            if ($keyword == 0 || $keyword == 1 || $keyword == 2) {
                $query->where('pending_invoice_shipments.type', '=', $keyword);
            }
        })
        ->filterColumn('phone_numbers', function($query, $keyword) {
            $search = str_replace('-', '', $keyword);

            if ($keyword != '') {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                    ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                });
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

    public function generate_invoices_store(Request $request) {
        $pending_invoice_shipment_ids = explode(',', $request->pending_invoice_shipment_ids);

        $invoice = new Invoice();

        $invoice->user_id = $request->shipper_id;
        $invoice->billing_period_from_date = $request->from_date;
        $invoice->billing_period_to_date = $request->to_date;
        $invoice->due_date = $request->due_date_formatted;

        $invoice->save();

        $total_shipments = 0;
        $total_delivered_shipments = 0;
        $total_returned_shipments = 0;
        $total_adjusted_shipments = 0;
        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($pending_invoice_shipment_ids as $pending_invoice_shipment_id) {
            $pending_invoice_shipment = PendingInvoiceShipment::find($pending_invoice_shipment_id);

            $shipment = Shipment::find($pending_invoice_shipment->shipment_id);

            $invoice_shipment = new InvoiceShipment();

            $invoice_shipment->created_at = $pending_invoice_shipment->created_at;
            $invoice_shipment->invoice_id = $invoice->id;
            $invoice_shipment->shipment_id = $pending_invoice_shipment->shipment_id;
            $invoice_shipment->type = $pending_invoice_shipment->type;

            if ($pending_invoice_shipment->type == 2) {
                $invoice_shipment->adjustment_charges = $pending_invoice_shipment->charges;
            }
            else {
                if ($shipment->packaging_material_charges) {
                    $invoice_shipment->packaging_material_charges = $shipment->packaging_material_charges;
                }
                else {
                    $invoice_shipment->weight_charges = $shipment->weight_charges;
                    $invoice_shipment->insurance_charges = $shipment->insurance_charges;
                    $invoice_shipment->fuel_surcharge = $shipment->fuel_surcharge;

                    if ($pending_invoice_shipment->type == 0) {
                        $invoice_shipment->cash_handling_charges = $shipment->cash_handling_charges;
                        $invoice_shipment->replacement_charges = $shipment->replacement_charges;
                        // $invoice_shipment->try_and_buy_charges = $shipment->try_and_buy_charges;
                    }
                    else {
                        $invoice_shipment->return_charges = $shipment->return_charges;
                    }
                }
            }

            $invoice_shipment->charges = $pending_invoice_shipment->charges;
            $invoice_shipment->gst = $pending_invoice_shipment->gst;
            $invoice_shipment->invoice_amount = $pending_invoice_shipment->invoice_amount;

            $invoice_shipment->save();

            $pending_invoice_shipment->delete();

            $total_shipments++;

            if ($pending_invoice_shipment->type == 0) {
                $total_delivered_shipments++;
            }
            else if ($pending_invoice_shipment->type == 1) {
                $total_returned_shipments++;
            }
            else {
                $total_adjusted_shipments++;
            }

            $total_charges = $total_charges + $pending_invoice_shipment->charges;
            $total_gst = $total_gst + $pending_invoice_shipment->gst;
            $total_invoice_amount = $total_invoice_amount + $pending_invoice_shipment->charges;
        }

        $shipper = User::find($request->shipper_id);

        $invoice->invoice_number = str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '-' . $shipper->city_id . '-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);
        $invoice->total_shipments = $total_shipments;
        $invoice->total_delivered_shipments = $total_delivered_shipments;
        $invoice->total_returned_shipments = $total_returned_shipments;
        $invoice->total_adjusted_shipments = $total_adjusted_shipments;
        $invoice->total_charges = $total_charges;
        $invoice->total_gst = $total_gst;
        $invoice->total_invoice_amount = $total_invoice_amount;

        $invoice->save();

        return redirect()->back()->with(['success' => 'Invoice(s) has been Generated.', 'print' => $invoice->id]);
    }

    public function generate_invoices_print(Request $request) {
        $invoice = Invoice::find($request->id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank;

        $html = '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                <title>Invoice</title>

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

                  .summary {
                    page-break-inside: avoid;
                  }

                  .shipments_summary {
                    page-break-before: always;
                  }
                </style>
              </head>
              <body>
                <div>
                  <div class="p-1">
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-left align-middle">
                            <img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mb-1">
                            <div><strong>TRAX ONLINE PRIVATE LIMITED</strong></div>
                            <div><strong>Address:</strong> Plot #4, DMCHS, Block #7/8, Adjacent to IBL Building Centre, Tipu Sultan Road, Karachi.</div>
                            <div><strong>NTN:</strong> 7930679-5</div>
                          </td>
                          <td class="text-center align-middle color primary"><strong>INVOICE</strong></td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="row align-items-start justify-content-between summary">
                        <div class="col-6">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Customer Details</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . $shipper->name . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . $shipper->address . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . $shipper->phone_number_1 . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper_bank->ntn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->from_date)->format('d/m/Y') . ' - ' . Carbon::parse($invoice->to_date)->format('d/m/Y') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('d/m/Y') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
        ';

        $shipment_details = '';

        $serial_number = 1;

        $total_weight_charges = 0;
        $total_cash_handling_charges = 0;
        $total_insurance_charges = 0;
        $total_return_charges = 0;
        $total_fuel_surcharge = 0;
        $total_replacement_charges = 0;
        // $total_try_and_buy_charges = 0;
        $total_packaging_material_charges = 0;
        $total_adjustment_charges = 0;
        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            if ($invoice_shipment->type == 0) {
                $type = 'Delivered';
            }
            else if ($invoice_shipment->type == 1) {
                $type = 'Returned';
            }
            else {
                $type = 'Adjusted';
            }

            $shipment_details .= '
                        <tr>
                          <td>' . $serial_number . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $invoice_shipment->created_at . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . number_format($invoice_shipment->weight_charges) . '</td>
                          <td>' . number_format($invoice_shipment->cash_handling_charges) . '</td>
                          <td>' . number_format($invoice_shipment->insurance_charges) . '</td>
                          <td>' . number_format($invoice_shipment->replacement_charges) . '</td>
                          <td>' . number_format($invoice_shipment->return_charges) . '</td>
                          <td>' . number_format($invoice_shipment->fuel_surcharge) . '</td>
                          <td>' . number_format($invoice_shipment->packaging_material_charges) . '</td>
                          <td>' . number_format($invoice_shipment->adjustment_charges) . '</td>
                          <td>' . number_format($invoice_shipment->charges) . '</td>
                          <td>' . number_format($invoice_shipment->gst) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount) . '</td>
                        </tr>
            ';

            $serial_number++;

            $total_weight_charges += $invoice_shipment->weight_charges;
            $total_cash_handling_charges += $invoice_shipment->cash_handling_charges;
            $total_insurance_charges += $invoice_shipment->insurance_charges;
            $total_return_charges += $invoice_shipment->return_charges;
            $total_fuel_surcharge += $invoice_shipment->fuel_surcharge;
            $total_replacement_charges += $invoice_shipment->replacement_charges;
            // $total_try_and_buy_charges += $invoice_shipment->try_and_buy_charges;
            $total_packaging_material_charges += $invoice_shipment->packaging_material_charges;
            $total_adjustment_charges += $invoice_shipment->adjustment_charges;
            $total_charges += $invoice_shipment->charges;
            $total_gst += $invoice_shipment->gst;
            $total_invoice_amount += $invoice_shipment->invoice_amount;
        }

        $html .= '
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                            <td class="color primary text-left"><strong>Invoice Summary</strong></td>
                            <td class="color primary text-right" style="width: 20%;"><strong>Amount (PKR)</strong></td>
                        </tr>
                        <tr>
                          <td class="text-left">Weight Charges</td>
                          <td class="text-right">' . number_format($total_weight_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Cash Handling Charges</td>
                          <td class="text-right">' . number_format($total_cash_handling_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Insurance Charges</td>
                          <td class="text-right">' . number_format($total_insurance_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Replacement Charges</td>
                          <td class="text-right">' . number_format($total_return_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Return Charges</td>
                          <td class="text-right">' . number_format($total_fuel_surcharge) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Fuel Surcharge</td>
                          <td class="text-right">' . number_format($total_replacement_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Packaging Charges</td>
                          <td class="text-right">' . number_format($total_packaging_material_charges) . '</td>
                        </tr>
                        <tr>
                          <td class="text-left">Adjustment Charges</td>
                          <td class="text-right">' . number_format($total_adjustment_charges) . '</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format($total_invoice_amount) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $this->amount_to_words($total_invoice_amount) . ' Only</td>
                        </tr>
                      </tbody>
                    </table>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary text-center" colspan="2"><strong>Bank Account Details</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Benificiary Name</strong></td>
                          <td>Trax Online Private Limited</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Bank</strong></td>
                          <td>Meezan Bank</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Account No.</strong></td>
                          <td>0102951143</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch No.</strong></td>
                          <td>9912</td>
                        </tr>
                      </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>

                    <table class="table table-sm table-bordered border shipments_summary">
                      <tbody>
                        <tr>
                            <td class="color primary text-center" colspan="16"><strong>Shipment(s) Summary</strong></td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>S. No.</strong></td>
                          <td class="color secondary"><strong>Tracking No.</strong></td>
                          <td class="color secondary"><strong>Destination</strong></td>
                          <td class="color secondary"><strong>Datetime</strong></td>
                          <td class="color secondary"><strong>Weight (kg)</strong></td>
                          <td class="color secondary"><strong>Weight Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Cash Handling Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Insurance Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Replacement Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Return Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Fuel Surcharge (PKR)</strong></td>
                          <td class="color secondary"><strong>Packaging Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Adjustment Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>Total Charges (PKR)</strong></td>
                          <td class="color secondary"><strong>GST (PKR)</strong></td>
                          <td class="color secondary"><strong>Invoice Amount (PKR)</strong></td>
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
                    history.replaceState(history.state, "", "/");

                    window.print();
                  }
                </script>
              </body>
            </html>
        ';

        return $html;
    }

    public function invoices_history_index() {
        return view('admin.finance.invoices_history');
    }

    public function invoices_history_list(Request $request) {
        $invoices = Invoice::join('users as u', 'invoices.user_id', '=', 'u.id')
        ->select('invoices.id', 'invoices.invoice_number', 'u.name as shipper', 'invoices.total_shipments', 'invoices.total_delivered_shipments', 'invoices.total_returned_shipments', 'invoices.total_adjusted_shipments', 'invoices.total_charges', 'invoices.total_gst', 'invoices.total_invoice_amount', 'invoices.billing_period_from_date', 'invoices.billing_period_to_date', 'invoices.due_date');

        $datatables = Datatables::of($invoices)
        ->addColumn('id_padded', function($invoice) {
            return '<button class="btn btn-sm btn-outline-info align-middle">' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) . '</button>';
        })
        ->filterColumn('invoices.id', function ($query, $keyword) {
            return $query->where('invoices.id', '=', $keyword);
        })
        ->editColumn('total_delivered_shipments', function($invoice) {
            if ($invoice->total_delivered_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->total_delivered_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('total_returned_shipments', function($invoice) {
            if ($invoice->total_returned_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->total_returned_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('total_adjusted_shipments', function($invoice) {
            if ($invoice->total_adjusted_shipments != 0) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->total_adjusted_shipments . '</button>';
            }
            else {
                return 0;
            }
        })
        ->editColumn('total_charges', function($invoice) {
            return number_format($invoice->total_charges);
        })
        ->editColumn('total_gst', function($invoice) {
            return number_format($invoice->total_gst);
        })
        ->editColumn('total_invoice_amount', function($invoice) {
            return number_format($invoice->total_invoice_amount);
        })
        ->editColumn('billing_period_from_date', function($invoice) {
            return Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d');
        })
        ->editColumn('billing_period_to_date', function($invoice) {
            return Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d');
        })
        ->editColumn('due_date', function($invoice) {
            return Carbon::parse($invoice->due_date)->format('Y-m-d');
        });

        return $datatables->make(true);
    }

    public function invoices_history_delivered_shipments(Request $request) {
        $tracking_numbers = array();

        $invoice_shipments = InvoiceShipment::where('invoice_id', $request->id)->where('type', 0)->get();

        foreach ($invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function invoices_history_returned_shipments(Request $request) {
        $tracking_numbers = array();

        $invoice_shipments = InvoiceShipment::where('invoice_id', $request->id)->where('type', 1)->get();

        foreach ($invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function invoices_history_adjusted_shipments(Request $request) {
        $tracking_numbers = array();

        $invoice_shipments = InvoiceShipment::where('invoice_id', $request->id)->where('type', 2)->get();

        foreach ($invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

}