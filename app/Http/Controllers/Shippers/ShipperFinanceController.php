<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\BanksList;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\BookingType;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentPaymentStatus;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\Shipper\UserBankInfo;
use App\Http\Models\Admin\ChangeShipmentWeightLog;

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

    public function payments_list(Request $request) {
      $done_payments = DonePayment::join('users as u', 'done_payments.user_id', '=', 'u.id')
        ->join('cities as c', 'u.city_id', '=', 'c.id')
        ->join('done_payment_shipments as dps', 'done_payments.id', '=', 'dps.done_payment_id')
        ->leftJoin('user_bank_infos as ubi', function ($join) {
            $join->on('ubi.id', '=', 'done_payments.user_bank_info_id');
        })
        ->leftJoin('user_bank_infos as ubi_default', function ($join) {
            $join->on('ubi_default.user_id', '=', 'u.id')
                ->where('ubi_default.default_bank', DB::raw(1));
        })
        ->leftJoin('banks_lists as ub', function ($join) {
            $join->where(function($sub_query) {
                $sub_query->whereNotNull('done_payments.user_bank_info_id')
                ->where('ubi.bank_name', '=', DB::raw('`ub`.`id`'));
            })->orWhere(function($sub_query) {
                $sub_query->whereNull('done_payments.user_bank_info_id')
                ->where('ubi_default.bank_name', '=', DB::raw('`ub`.`id`'));
            });
        })
        ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
        ->select('done_payments.id as id', 'u.id as user_id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(dps.amount) as total_amount'), DB::raw('SUM(dps.charges) as total_charges'), DB::raw('SUM(dps.gst) as total_gst'), DB::raw('SUM(dps.payable) as total_payable'), 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status')
//        ->where('done_payments.user_id', session('user_id'))
//        ->orwhereIn('done_payments.user_id', session('sister_users'))
        ->groupBy('done_payments.id');
        $done_payments = $done_payments->where(function ($query) {
            $query->where('done_payments.user_id', session('user_id'))
                ->orwhereIn('done_payments.user_id', session('sister_users'));
        });

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
            return number_format($done_payment->total_amount, 2);
        })
        ->editColumn('total_charges', function($done_payment) {
            return number_format($done_payment->total_charges, 2);
        })
        ->editColumn('total_gst', function($done_payment) {
            return number_format($done_payment->total_gst, 2);
        })
        ->editColumn('total_payable', function($done_payment) {
            return number_format(ROUND($done_payment->total_payable, 0, PHP_ROUND_HALF_DOWN));
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
                if($shipments > 0){
                    $aging = round(($days / $shipments), 2) . 'd';
                }
                else{
                    $aging = 0 . 'd';
                }

                return $aging;
            }
            else {
                return '-';
            }
        })
        ->addColumn('action', function($done_payment) {
            if($done_payment->user_id == session('user_id')){
                return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>
                    <button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>
                    <button type="button" class="dropdown-item request_add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Request</div></button>
                  </div>
                </div>
            ';
            }
            else{
                return '';
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

        if ($tracking_number = $request->get('tracking_number')) {
            $datatables->join('shipments as s', 'dps.shipment_id', '=', 's.id')
            ->where('s.tracking_number', '=', $tracking_number);
        }

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

      if($done_payment->user_bank_info_id == null){
          $shipper_bank = UserBankInfo::where('user_id', session('user_id'))->where('default_bank', 1)->first();
      }else{
          $shipper_bank = UserBankInfo::find($done_payment->user_bank_info_id);
      }
      

      $account_type_id = $shipper->account_type_id;

      $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Payment Details / Sales Tax Invoice</title>

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
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Payment Details / Sales Tax Invoice</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Payment / Invoice ID</strong></td>
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
                            <tr>
                              <td class="color secondary"><strong>NTN</strong></td>
                              <td>' . $shipper->ntn_no . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>STRN</strong></td>
                              <td>' . $shipper->strn_no . '</td>
                            </tr>
      ';

      $shipment_details = '';

      $serial_number = 1;

      $total_collection_amount = 0;
      $total_weight_charges = 0;
      $total_cash_handling_charges = 0;
      $total_insurance_charges = 0;
      $total_replacement_charges = 0;
      $total_try_and_buy_charges = 0;
      $total_return_charges = 0;
      $total_packaging_material_charges = 0;
      $total_fuel_surcharge = 0;
      $total_intercept_charges = 0;
      $total_nsa_osa_charges = 0;
      $total_gst = 0;
      $total_charges = 0;
      $total_adjustments = 0;
      $total_payable = 0;

      foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_weight = $shipment->actual_weight;
            $weight_charges = $shipment->weight_charges;

            if($done_payment_shipment->type != 2){
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if($change_shipment_weight_log->exists()){
                    $change_shipment_weight_log = $change_shipment_weight_log->first();
                    $shipment_weight = $change_shipment_weight_log->old_weight;
                    $weight_charges = $change_shipment_weight_log->old_charges;
                }
            }


            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            }
            else if ($done_payment_shipment->type == 1) {
                $type = 'Returned';
            }
            else {
                $type = 'Adjusted';
            }

            $item = $shipment->items->first();

            $pickup_address = $shipment->pickup_address;

            $shipment_details .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $type . '</td>
                              <td>' . $shipment->order_id . '</td>
                              <td>' . (($pickup_address->vendor) ? $pickup_address->vendor : '') . '</td>
                              <td>' . $pickup_address->city->name . '</td>
                              <td>' . $shipment->consignee_name . ' ' . $shipment->consignee_phone_number_1 . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->booking_type->booking_type . '</td>
                              <td>' . $shipment_weight . '</td>
                              <td>' . $item->description . '</td>
                              <td>' . number_format($done_payment_shipment->amount) . '</td>
                              <td>' . (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? number_format($weight_charges, 2) : '0') . '</td>
                              <td>' . (($account_type_id == 1 && $done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? number_format($shipment->cash_handling_charges, 2) : '0') . '</td>
                              <td>' . (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                              <td>' . (($done_payment_shipment->type == 2) ? number_format($done_payment_shipment->payable, 2) : '0') . '</td>
                            </tr>
            ';

            $serial_number++;

            if ($account_type_id == 1) {
              if ($done_payment_shipment->type != 2) {
                  if ($done_payment_shipment->type == 0) {
                      $total_collection_amount += $done_payment_shipment->amount;
                      $total_cash_handling_charges += $shipment->cash_handling_charges;
                      $total_replacement_charges += $shipment->replacement_charges;
                       $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                  }
                  else {
                      $total_return_charges += $shipment->return_charges;
                  }

                  $total_weight_charges += $weight_charges;

                  if ($shipment->packaging_material_request) {
                      $total_packaging_material_charges += $shipment->packaging_material_charges;
                  }

                  $total_insurance_charges += $shipment->insurance_charges;
                  $total_fuel_surcharge += $shipment->fuel_surcharge;
                  $total_intercept_charges += $shipment->intercept_charges;
                  $total_nsa_osa_charges += $shipment->nsa_osa_charges;
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
                                <td colspan="10"></td>
                                <td class="color primary"><strong>Total</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_weight_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_cash_handling_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_nsa_osa_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_adjustments, 2) . '</strong></td>
                            </tr>
      ';

      $html .= '
                            <tr>
                              <td class="color secondary"><strong>Total Collection Amount (PKR)</strong></td>
                              <td>' . number_format($total_collection_amount) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total Payable (PKR)</strong></td>
                              <td>' . number_format(ROUND($total_payable, 0, PHP_ROUND_HALF_DOWN), 2) . '</td>
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
                              <td class="color primary"><strong>Vendor</strong></td>
                              <td class="color primary"><strong>Origin</strong></td>
                              <td class="color primary"><strong>Consignee</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Service Type</strong></td>
                              <td class="color primary"><strong>Weight (kg)</strong></td>
                              <td class="color primary"><strong>Item Description</strong></td>
                              <td class="color primary"><strong>Collection Amount (PKR)</strong></td>
                              <td class="color primary"><strong>Weight Charges (PKR)</strong></td>
                              <td class="color primary"><strong>Cash Handling Charges (PKR)</strong></td>
                              <td class="color primary"><strong>OSA Charges (PKR)</strong></td>
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
                                        <td>' . number_format($total_weight_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Cash Handling Charges</strong></td>
                                        <td>' . number_format($total_cash_handling_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Insurance Charges</strong></td>
                                        <td>' . number_format($total_insurance_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Replacement Charges</strong></td>
                                        <td>' . number_format($total_replacement_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Try & Buy Charges</strong></td>
                                        <td>' . number_format($total_try_and_buy_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Return Charges</strong></td>
                                        <td>' . number_format($total_return_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Fuel Surcharge</strong></td>
                                        <td>' . number_format($total_fuel_surcharge, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Intercept Charges</strong></td>
                                        <td>' . number_format($total_intercept_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total OSA Charges</strong></td>
                                        <td>' . number_format($total_nsa_osa_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Charges (w/o GST)</strong></td>
                                        <td class="color secondary">' . number_format(($total_charges - $total_packaging_material_charges), 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total GST</strong></td>
                                        <td>' . number_format($total_gst, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Packaging Material Charges</strong></td>
                                        <td>' . number_format($total_packaging_material_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>Total Adjustments</strong></td>
                                        <td>' . number_format($total_adjustments, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color primary"><strong>Overall Charges</strong></td>
                                        <td class="color secondary"><strong>' . number_format(($total_charges + $total_gst - $total_adjustments), 2) . '</strong></td>
                                    </tr>
                                  </tbody>
                                </table>
                                <span style="color: red">* 13% GST is applicable for Sindh Region 16% GST for Punjab .KPK</span>
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

    public function payments_export_to_excel(Request $request) {
        $done_payment = DonePayment::find($request->id);

        $filename = 'sonic_payment_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Booking Date', 'Type', 'Order ID', 'Vendor', 'Origin', 'Consignee Name', 'Consignee Phone', 'Destination', 'Service Type', 'Weight (kg)', 'Collection Amount (PKR)', 'Weight Charges (PKR)', 'Cash Handling Charges (PKR)', 'OSA Charges (PKR)', 'Adjustments (PKR)'];

        if ($done_payment && $done_payment->user_id == session('user_id')) {
          $account_type_id = $done_payment->shipper->account_type_id;

          $serial_number = 1;

          $total_collection_amount = 0;
          $total_weight_charges = 0;
          $total_cash_handling_charges = 0;
          $total_insurance_charges = 0;
          $total_replacement_charges = 0;
          $total_try_and_buy_charges = 0;
          $total_return_charges = 0;
          $total_packaging_material_charges = 0;
          $total_fuel_surcharge = 0;
          $total_intercept_charges = 0;
          $total_nsa_osa_charges = 0;
          $total_gst = 0;
          $total_charges = 0;
          $total_adjustments = 0;
          $total_payable = 0;

          foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
              $shipment = $done_payment_shipment->shipment;

              $shipment_weight = $shipment->actual_weight;
              $weight_charges = $shipment->weight_charges;

              if($done_payment_shipment->type != 2){
                  $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                  if($change_shipment_weight_log->exists()){
                      $change_shipment_weight_log = $change_shipment_weight_log->first();
                      $shipment_weight = $change_shipment_weight_log->old_weight;
                      $weight_charges = $change_shipment_weight_log->old_charges;
                  }
              }

              if ($done_payment_shipment->type == 0) {
                  $type = 'Delivered';
              }
              else if ($done_payment_shipment->type == 1) {
                  $type = 'Returned';
              }
              else {
                  $type = 'Adjusted';
              }

              $pickup_address = $shipment->pickup_address;

              $row = array();

              $row[] = $serial_number;
              $row[] = $shipment->tracking_number;
              $row[] = $shipment->created_at;
              $row[] = $type;
              $row[] = $shipment->order_id;
              $row[] = $pickup_address->vendor;
              $row[] = $pickup_address->city->name;
              $row[] = $shipment->consignee_name;
              $row[] = $shipment->consignee_phone_number_1;
              $row[] = $shipment->consignee_city->name;
              $row[] = $shipment->booking_type->booking_type;
              $row[] = $shipment_weight;
              $row[] = $done_payment_shipment->amount;
              $row[] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $weight_charges : 0);
              $row[] = (($account_type_id == 1 && $done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? $shipment->cash_handling_charges : 0);
              $row[] = (($account_type_id == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->nsa_osa_charges : 0);
              $row[] = (($done_payment_shipment->type == 2) ? $done_payment_shipment->payable : 0);

              $details[] = $row;

              $serial_number++;

              if ($account_type_id == 1) {
                  if ($done_payment_shipment->type != 2) {
                      if ($done_payment_shipment->charges != 0) {
                          if ($done_payment_shipment->type == 0) {
                              $total_collection_amount += $done_payment_shipment->amount;
                              $total_cash_handling_charges += $shipment->cash_handling_charges;
                              $total_replacement_charges += $shipment->replacement_charges;
                               $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                          }
                          else {
                              $total_return_charges += $shipment->return_charges;
                          }

                          $total_weight_charges += $weight_charges;

                          if ($shipment->packaging_material_request) {
                              $total_packaging_material_charges += $shipment->packaging_material_charges;
                          }

                          $total_insurance_charges += $shipment->insurance_charges;
                          $total_fuel_surcharge += $shipment->fuel_surcharge;
                          $total_intercept_charges += $shipment->intercept_charges;
                          $total_nsa_osa_charges += $shipment->nsa_osa_charges;
                      }
                      else if ($done_payment_shipment->type == 0) {
                          $total_collection_amount += $done_payment_shipment->amount;
                      }
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

          $total_columns = count($details[0]);

          $summary = ['Total Weight Charges' => $total_weight_charges, 'Total Cash Handling Charges' => $total_cash_handling_charges, 'Total Insurance Charges' => $total_insurance_charges, 'Total Replacement Charges' => $total_replacement_charges, 'Total Try & Buy Charges' => $total_try_and_buy_charges,'Total Return Charges' => $total_return_charges, 'Total Fuel Surcharge' => $total_fuel_surcharge, 'Total Intercept Charges' => $total_intercept_charges, 'Total OSA Charges' => $total_nsa_osa_charges, 'Total Charges (w/o GST)' => ($total_charges - $total_packaging_material_charges), 'Total GST' => $total_gst, 'Total Packaging Material Charges' => $total_packaging_material_charges, 'Total Adjustments' => $total_adjustments, 'Overall Charges' => ($total_charges + $total_gst - $total_adjustments)];

          $details[] = [];

          $row = array();

          for ($c = 0; $c < $total_columns; $c++) {
              $row[] = '';
          }

          $row[] = 'Charges Summary (PKR)';
          $row[] = '';

          $details[] = $row;

          foreach ($summary as $name => $value) {
              $row = array();

              for ($c = 0; $c < $total_columns; $c++) {
                  $row[] = '';
              }

              $row[] = $name;
              $row[] = $value;

              $details[] = $row;
          }
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('S')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function payments_reconcile_through_receiving_sheet_index() {
      $service_types = BookingType::whereNotIn('id', [3, 4])->select('id', 'booking_type')->get();
      $shipment_statuses = ShipmentStatus::select('id', 'name')->get();
      $shipment_payment_statuses = ShipmentPaymentStatus::select('id', 'name')->get();

      return view('client.finance.payments.reconcile_through_receiving_sheet.index')->with(['service_types' => $service_types, 'shipment_statuses' => $shipment_statuses, 'shipment_payment_statuses' => $shipment_payment_statuses]);
    }

    public function payments_reconcile_through_receiving_sheet_list(Request $request) {
      $tracking_route = route('cod.tracking.index');

      $shipments = ReceivingSheetShipment::join('shipments as s', 'receiving_sheet_shipments.shipment_id', 's.id')
      ->join('receiving_sheets as rs', 'receiving_sheet_shipments.receiving_sheet_id', 'rs.id')
      ->join('booking_types as bt', 's.booking_type_id', 'bt.id')
      ->join('cities as c', 's.consignee_city_id', 'c.id')
      ->join('shipments_journey as sj', function ($join) {
        $join->on('sj.shipment_id', '=', 's.id')
          ->where('sj.id', '=',
            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1)'));
      })
      ->join('shipment_status as ss', 'sj.consignee_status_id', 'ss.id')
      ->leftJoin('shipment_payment_status as sps', 's.payment_status_id', 'sps.id')
      ->leftJoin('done_payment_shipments as dps', 's.id', 'dps.shipment_id')
      ->select('s.id', 's.tracking_number', 's.order_id', 'bt.booking_type as service_type', 's.consignee_name', 's.consignee_phone_number_1', 's.consignee_phone_number_2', 's.created_at', 'c.name as destination', 's.actual_weight', 'ss.name as shipment_status', 'sps.name as shipment_payment_status', DB::raw('IF(s.tracking_number IS NULL, NULL, IFNULL(GROUP_CONCAT(dps.done_payment_id SEPARATOR ", "), NULL)) AS payment_ids'));

        if(session('user_type') == 2){
            if(session('restriction') == 1){
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                    $join->on('sus.shipment_id', '=', 's.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }
      $shipments = $shipments->where(function ($query) {
        $query->where('rs.user_id', session('user_id'))
          ->orwhereIn('rs.user_id', session('sister_users'));
      });

      if ($receiving_sheet_id = $request->receiving_sheet_id) {
        $shipments = $shipments->where('receiving_sheet_shipments.receiving_sheet_id', $request->receiving_sheet_id);
      }
      else {
        $shipments = $shipments->where('receiving_sheet_shipments.receiving_sheet_id', 0);
      }

      $shipments = $shipments->groupBy('s.id');

      $datatables = Datatables::of($shipments)
      ->editColumn('tracking_number', function($shipment) use ($tracking_route) {
        return '<u><a href="' . $tracking_route . '?tracking_number=' . $shipment->tracking_number . '" target="_blank">' . $shipment->tracking_number . '</a></u>';
      })
      ->addColumn('consignee_phone', function ($shipment) {
          $consignee_phone = $shipment->consignee_phone_number_1;

          if ($shipment->consignee_phone_number_2) {
            $consignee_phone .= ' | ' . $shipment->consignee_phone_number_2;
          }

          return $consignee_phone;
      })
      ->filterColumn('consignee_phone', function ($query, $keyword) {
        $keyword = strtolower($keyword);

        if ($keyword != '') {
            $query->where('s.consignee_phone_number_1', 'like', '%' . $keyword . '%')->orWhere('s.consignee_phone_number_2', 'like', '%' . $keyword . '%');
        }
        else {
            $query->whereRaw('false');
        }
      })
      ->orderColumn('consignee_phone', 's.consignee_phone_number_1 $1, s.consignee_phone_number_2 $1')
      ->filterColumn('payment_ids', function ($query, $keyword) {
        $keyword = strtolower($keyword);

        if ($keyword != '') {
          $query->where('dps.done_payment_id', '=', $keyword);
        }
        else {
          $query->whereRaw('false');
        }
      });

      return $datatables->make(true);
    }
}