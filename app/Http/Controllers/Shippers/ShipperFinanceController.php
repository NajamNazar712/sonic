<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\BanksList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;

use Auth;
use DB;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ShipperFinanceController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function payments_index() {
        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
      return view('client.finance.payments.index')->with(['banks'=>$banks,'company_banks'=>$company_banks]);
    }

    public function payments_list() {
      $done_payments = DonePayment::join('users as u', 'done_payments.user_id', '=', 'u.id')
        ->join('cities as c', 'u.city_id', '=', 'c.id')
        ->join('user_bank_infos as ubi', 'done_payments.user_id', '=', 'ubi.user_id')
        ->join('banks_lists as ub', 'ubi.bank_name', '=', 'ub.id')
        ->join('done_payment_shipments as pps', 'done_payments.id', '=', 'pps.done_payment_id')
        ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
        ->select('done_payments.id as id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(pps.amount) as total_amount'), DB::raw('SUM(pps.charges) as total_charges'), DB::raw('SUM(pps.gst) as total_gst'), DB::raw('SUM(pps.payable) as total_payable'), 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status')
        ->where('done_payments.user_id', session('user_id'))
        ->groupBy('done_payments.id');

        $datatables = Datatables::of($done_payments)
        ->addColumn('id_padded', function ($done_payment) {
            return str_pad($done_payment->id, 6, '0', STR_PAD_LEFT);
        })
        ->filterColumn('done_payments.id', function ($query, $keyword) {
            return $query->where('done_payments.id', '=', $keyword);
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

            if ($keyword != '') {
                $query->where('done_payments.status', '=', $keyword);
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
        ->filterColumn('company_bank', function($query, $keyword) {

                if ($keyword !='') {
                    $query->where('b.id', '=', $keyword);
                }
                else {
                    $query->whereRaw('false');
                }
        })
        ->orderColumn('phone_numbers', 'u.phone $1, u.phone2 $1');

        return $datatables->make(true);
    }

    public function payments_delivered_shipments(Request $request) {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 0)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function payments_returned_shipments(Request $request) {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 1)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function payments_adjusted_shipments(Request $request) {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 2)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function payments_details_print(Request $request) {
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

    public function payments_export_to_excel(Request $request) {
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