<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\BanksList;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceForReimbursement;
use App\Http\Models\InvoiceStatus;
use App\Http\Models\Notification;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\Zone;
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
use NumberToWords\NumberToWords;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ShipperFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function payments_index()
    {
        $banks = BanksList::all();
        $company_banks = BanksList::where('affiliate', 1)->get();
        return view('client.finance.payments.index')->with(['banks' => $banks, 'company_banks' => $company_banks]);
    }

    public function payments_list(Request $request)
    {
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
                $join->where(function ($sub_query) {
                    $sub_query->whereNotNull('done_payments.user_bank_info_id')
                        ->where('ubi.bank_name', '=', DB::raw('`ub`.`id`'));
                })->orWhere(function ($sub_query) {
                    $sub_query->whereNull('done_payments.user_bank_info_id')
                        ->where('ubi_default.bank_name', '=', DB::raw('`ub`.`id`'));
                });
            })
            ->leftjoin('banks_lists as b', 'done_payments.company_bank_id', '=', 'b.id')
            ->select('done_payments.id as id', 'u.id as user_id', 'u.name as shipper', 'c.name as city', 'u.phone', 'u.phone2', 'u.address', 'done_payments.total_shipments', 'done_payments.delivered_shipments', 'done_payments.delivered_shipments as delivered_shipments_count', 'done_payments.returned_shipments', 'done_payments.returned_shipments as returned_shipments_count', 'done_payments.adjusted_shipments', 'done_payments.adjusted_shipments as adjusted_shipments_count', DB::raw('SUM(dps.amount) as total_amount'), DB::raw('SUM(dps.charges) as total_charges'), DB::raw('SUM(dps.gst) as total_gst'),DB::raw('SUM(dps.wht) as total_wht'), DB::raw('SUM(dps.payable) as total_payable'), 'ub.name as bank', 'done_payments.reference_number', 'done_payments.created_at as done_at', 'b.name as company_bank', 'done_payments.status')
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
            ->editColumn('delivered_shipments', function ($done_payment) {
                if ($done_payment->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('returned_shipments', function ($done_payment) {
                if ($done_payment->returned_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->returned_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('adjusted_shipments', function ($done_payment) {
                if ($done_payment->adjusted_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $done_payment->adjusted_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('total_amount', function ($done_payment) {
                return number_format($done_payment->total_amount, 2);
            })
            ->editColumn('total_charges', function ($done_payment) {
                return number_format($done_payment->total_charges, 2);
            })
            ->editColumn('total_gst', function ($done_payment) {
                return number_format($done_payment->total_gst, 2);
            })
            ->editColumn('total_wht', function ($done_payment) {
                return number_format($done_payment->total_wht, 2);
            })
            ->editColumn('total_payable', function ($done_payment) {
                return number_format(ROUND($done_payment->total_payable, 0, PHP_ROUND_HALF_DOWN));
            })
            ->addColumn('phone_numbers', function ($done_payment) {
                $phone_numbers = $done_payment->phone;

                if (!empty($done_payment->phone2)) {
                    $phone_numbers .= ' - ' . $done_payment->phone2;
                }

                return $phone_numbers;
            })
            ->removeColumn('phone')
            ->removeColumn('phone2')
            ->editColumn('status', function ($done_payment) {
                if ($done_payment->status == 0) {
                    return 'Processed';
                } else if ($done_payment->status == 1) {
                    return 'Paid';
                } else if ($done_payment->status == 2) {
                    return 'Reverted';
                } else {
                    return 'Unknown';
                }
            })
            ->addColumn('return_shipments_average_aging', function ($done_payment) {
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
                    if ($shipments > 0) {
                        $aging = round(($days / $shipments), 2) . 'd';
                    } else {
                        $aging = 0 . 'd';
                    }

                    return $aging;
                } else {
                    return '-';
                }
            })
            ->addColumn('action', function ($done_payment) {
                if ($done_payment->user_id == session('user_id')) {
                    return '<div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item view_details"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Details</div></button>
                    <button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>
                    <button type="button" class="dropdown-item request_add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Add Request</div></button>
                  </div>
                </div>
            ';
                } else {
                    return '';
                }
            })
            ->filterColumn('phone_numbers', function ($query, $keyword) {
                $search = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('u.phone', 'like', '%' . $keyword . '%')
                            ->orWhere('u.phone2', 'like', '%' . $keyword . '%');
                    });
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('done_payments.status', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ub.id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('company_bank', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('b.id', '=', $keyword);
                } else {
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

    public function payments_delivered_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 0)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function payments_returned_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 1)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function payments_adjusted_shipments(Request $request)
    {
        $tracking_numbers = array();

        $done_payment_shipments = DonePaymentShipment::where('done_payment_id', $request->id)->where('type', 2)->get();

        foreach ($done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $tracking_numbers[] = $shipment->tracking_number;
        }

        return $tracking_numbers;
    }

    public function payments_details_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $done_payment = DonePayment::find($request->id);

        $shipper = $done_payment->shipper;

        if ($done_payment->user_bank_info_id == null) {
            $shipper_bank = UserBankInfo::where('user_id', session('user_id'))->where('default_bank', 1)->first();
        } else {
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
                              <td>' . str_pad($done_payment->id, 6, '0', STR_PAD_LEFT) . '</td>
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
                              <td class="color secondary"><strong>NTN</strong></td>
                              <td>' . $shipper->ntn_no . '</td>
                            </tr>';
        if ($shipper->strn_no) {
            $html .= '
                                        <tr>
                                          <td class="color secondary"><strong>STRN</strong></td>
                                          <td>' . $shipper->strn_no . '</td>
                                        </tr>';
        };
        $html .= '<tr>
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
        $total_wht = 0;
        $total_charges = 0;
        $total_adjustments = 0;
        $total_payable = 0;

        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;

            $shipment_weight = $shipment->actual_weight;
            $weight_charges = $shipment->weight_charges;

            if ($done_payment_shipment->type != 2) {
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if ($change_shipment_weight_log->exists()) {
                    $change_shipment_weight_log = $change_shipment_weight_log->first();
                    $shipment_weight = $change_shipment_weight_log->old_weight;
                    $weight_charges = $change_shipment_weight_log->old_charges;
                }
            }


            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            } else if ($done_payment_shipment->type == 1) {
                $type = 'Returned';
            } else {
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
                              <td>' . ((1 == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? number_format($weight_charges, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? number_format($shipment->cash_handling_charges, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                              <td>' . (($done_payment_shipment->type == 2) ? number_format($done_payment_shipment->payable, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->charges, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->gst, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->wht, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->amount - $done_payment_shipment->payable, 2) : '0') . '</td>
                              <td>' . ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->payable, 2) : '0') . '</td>
                            </tr>
            ';

            $serial_number++;

            if (1 == 1) {
                if ($done_payment_shipment->type != 2) {
                    if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                        $total_cash_handling_charges += $shipment->cash_handling_charges;
                        $total_replacement_charges += $shipment->replacement_charges;
                        $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                    } else {
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
                } else {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_gst += $done_payment_shipment->gst;
                $total_wht += $done_payment_shipment->wht;
                $total_charges += $done_payment_shipment->charges;
                $total_payable += $done_payment_shipment->payable;
            } else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                } else if ($done_payment_shipment->type == 2) {
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
                                <td class="color secondary"><strong>' . number_format($total_charges, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_gst, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_wht, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount - $total_payable, 2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_payable, 2) . '</strong></td>
                            </tr>
      ';

        $html .= '
                            <tr>
                              <td class="color secondary"><strong>Total Collection Amount (PKR)</strong></td>
                              <td>' . number_format($total_collection_amount) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Total WHT Amount (PKR)</strong></td>
                              <td>' . number_format($total_wht,2) . '</td>
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
                              <td class="color primary"><strong>Total Charges (PKR)</strong></td>
                              <td class="color primary"><strong>GST</strong></td>
                              <td class="color primary"><strong>WHT</strong></td>
                              <td class="color primary"><strong>Net Retained Amount (PKR)</strong></td>
                              <td class="color primary"><strong>Net Disbursement Amount (PKR)</strong></td>
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
                                        <td class="color secondary"><strong>Total WHT (Deductable)</strong></td>
                                        <td>' . number_format($total_wht, 2) . '</td>
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
                                        <td class="color secondary"><strong>' . number_format(($total_charges + $total_gst - $total_adjustments - $total_wht), 2) . '</strong></td>
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

    public function payments_export_to_excel(Request $request)
    {
        $done_payment = DonePayment::find($request->id);

        $filename = 'sonic_payment_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Booking Date', 'Type', 'Order ID', 'Vendor', 'Origin', 'Consignee Name', 'Consignee Phone', 'Destination', 'Service Type', 'Weight (kg)', 'Collection Amount (PKR)', 'Weight Charges (PKR)', 'Cash Handling Charges (PKR)', 'OSA Charges (PKR)', 'Adjustments (PKR)','Total Charges (PKR)','GST','WHT','Net Retained Amount (PKR)','Net Disbursement Amount (PKR)
'];

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
            $total_wht = 0;
            $total_charges = 0;
            $total_adjustments = 0;
            $total_payable = 0;

            foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
                $shipment = $done_payment_shipment->shipment;

                $shipment_weight = $shipment->actual_weight;
                $weight_charges = $shipment->weight_charges;

                if ($done_payment_shipment->type != 2) {
                    $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                    if ($change_shipment_weight_log->exists()) {
                        $change_shipment_weight_log = $change_shipment_weight_log->first();
                        $shipment_weight = $change_shipment_weight_log->old_weight;
                        $weight_charges = $change_shipment_weight_log->old_charges;
                    }
                }

                if ($done_payment_shipment->type == 0) {
                    $type = 'Delivered';
                } else if ($done_payment_shipment->type == 1) {
                    $type = 'Returned';
                } else {
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
                $row[] = ((1 == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $weight_charges : 0);
                $row[] = ((1 == 1 && $done_payment_shipment->type == 0 && $done_payment_shipment->charges != 0) ? $shipment->cash_handling_charges : 0);
                $row[] = ((1 == 1 && $done_payment_shipment->type != 2 && $done_payment_shipment->charges != 0) ? $shipment->nsa_osa_charges : 0);
                $row[] = (($done_payment_shipment->type == 2) ? $done_payment_shipment->payable : 0);

                $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->charges, 2) : '0');
                $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->gst, 2) : '0');
                $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->wht, 2) : '0');
                $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->amount - $done_payment_shipment->payable, 2) : '0');
                $row[] = ((1 == 1 && $done_payment_shipment->charges != 0) ? number_format($done_payment_shipment->payable, 2) : '0');

                $details[] = $row;

                $serial_number++;

                if (1 == 1) {
                    if ($done_payment_shipment->type != 2) {
                        if ($done_payment_shipment->charges != 0) {
                            if ($done_payment_shipment->type == 0) {
                                $total_collection_amount += $done_payment_shipment->amount;
                                $total_cash_handling_charges += $shipment->cash_handling_charges;
                                $total_replacement_charges += $shipment->replacement_charges;
                                $total_try_and_buy_charges += $shipment->try_and_buy_charges;
                            } else {
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
                        } else if ($done_payment_shipment->type == 0) {
                            $total_collection_amount += $done_payment_shipment->amount;
                        }
                    } else {
                        $total_adjustments += $done_payment_shipment->payable;
                    }

                    $total_gst += $done_payment_shipment->gst;
                    $total_wht += $done_payment_shipment->wht;
                    $total_charges += $done_payment_shipment->charges;
                    $total_payable += $done_payment_shipment->payable;
                } else {
                    if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                    } else if ($done_payment_shipment->type == 2) {
                        $total_adjustments += $done_payment_shipment->payable;
                    }

                    $total_payable += $done_payment_shipment->payable;
                }
            }

            $total_columns = count($details[0]);

            $summary = ['Total Weight Charges' => $total_weight_charges, 'Total Cash Handling Charges' => $total_cash_handling_charges, 'Total Insurance Charges' => $total_insurance_charges, 'Total Replacement Charges' => $total_replacement_charges, 'Total Try & Buy Charges' => $total_try_and_buy_charges, 'Total Return Charges' => $total_return_charges, 'Total Fuel Surcharge' => $total_fuel_surcharge, 'Total Intercept Charges' => $total_intercept_charges, 'Total OSA Charges' => $total_nsa_osa_charges, 'Total Charges (w/o GST)' => ($total_charges - $total_packaging_material_charges), 'Total GST' => $total_gst,'Total WHT (Deductable)' => $total_wht, 'Total Packaging Material Charges' => $total_packaging_material_charges, 'Total Adjustments' => $total_adjustments, 'Overall Charges' => ($total_charges + $total_gst - $total_adjustments - $total_wht)];

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
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function payments_reconcile_through_receiving_sheet_index()
    {
        $service_types = BookingType::whereNotIn('id', [3, 4])->select('id', 'booking_type')->get();
        $shipment_statuses = ShipmentStatus::select('id', 'name')->get();
        $shipment_payment_statuses = ShipmentPaymentStatus::select('id', 'name')->get();

        return view('client.finance.payments.reconcile_through_receiving_sheet.index')->with(['service_types' => $service_types, 'shipment_statuses' => $shipment_statuses, 'shipment_payment_statuses' => $shipment_payment_statuses]);
    }

    public function payments_reconcile_through_receiving_sheet_list(Request $request)
    {
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

        if (session('user_type') == 2) {
            if (session('restriction') == 1) {
                $shipments = $shipments->join('substitute_user_shipments as sus', function ($join) {
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
        } else {
            $shipments = $shipments->where('receiving_sheet_shipments.receiving_sheet_id', 0);
        }

        $shipments = $shipments->groupBy('s.id');

        $datatables = Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipment) use ($tracking_route) {
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
                } else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('consignee_phone', 's.consignee_phone_number_1 $1, s.consignee_phone_number_2 $1')
            ->filterColumn('payment_ids', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where('dps.done_payment_id', '=', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            });

        return $datatables->make(true);
    }

    public function invoice_index()
    {

        $company_banks = BanksList::where('affiliate', 1)->get();
        $invoice_statuses = InvoiceStatus::whereIn('id', [1, 3])->get();

        return view('client.finance.invoice.index')->with(['company_banks' => $company_banks, 'invoice_statuses' => $invoice_statuses]);
    }

    public function invoice_list(Request $request)
    {

        $invoice = DB::table('invoices as invoices')->join('users as u', 'invoices.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->leftjoin('banks_lists as b', 'invoices.company_bank_id', '=', 'b.id')
            ->join('invoice_statuses as is', 'invoices.status_id', '=', 'is.id')
            ->join('user_bank_infos as ubi','ubi.user_id','=','u.id')
            ->join('invoicing_cycles as ic','ic.id','=','ubi.invoicing_cycle_id')
            ->select('invoices.id as id', 'invoices.invoice_number as invoice_number', 'u.name as shipper', 'c.name as city', 'invoices.total_charges as total_charges', 'invoices.total_gst as total_gst', 'invoices.total_invoice_amount as total_invoice_amount', 'invoices.created_at as created_at', 'invoices.due_date as due_date', 'invoices.received_date as received_date', 'b.name as company_bank', 'invoices.received_amount as received_amount', 'invoices.tax_amount as tax_amount', 'invoices.deposit_date as deposit_date', 'is.name as status', 'invoices.status_id as status_id', 'invoices.invoicing_date as invoicing_date','ic.name as invoicing_cycle','invoices.invoice_type as invoice_type',DB::raw('NULL as payment_type'),DB::raw('2 as type_id'))
            ->whereIn('is.id', [1,3])
            ->where('u.id',session('user_id'))
            ->where('ubi.default_bank',1);

        if ($request->get('from_date') && $request->get('to_date')) {
            $from = Carbon::parse($request->get('from_date'))->format('Y-m-d');
            $to = Carbon::parse($request->get('to_date'))->format('Y-m-d');
            $invoice->whereBetween('invoices.invoicing_date', [$from,$to]);
        }

        $invoices = DB::table('invoice_for_reimbursements as invoices')->join('users as u', 'invoices.user_id', '=', 'u.id')
            ->join('cities as c', 'u.city_id', '=', 'c.id')
            ->select('invoices.id as id', 'invoices.invoice_number as invoice_number', 'u.name as shipper', 'c.name as city', 'invoices.total_charges as total_charges', 'invoices.total_gst as total_gst', 'invoices.total_invoice_amount as total_invoice_amount', 'invoices.created_at as created_at',DB::raw('NULL as due_date'),DB::raw('NULL as received_date'),DB::raw('NULL as company_bank'),DB::raw('NULL as received_amount'),DB::raw('NULL as tax_amount'),DB::raw('NULL as deposit_date'),DB::raw('NULL as status'),DB::raw('NULL as status_id'), 'invoices.invoicing_date as invoicing_date',DB::raw('NULL as invoicing_cycle'),DB::raw('NULL as invoice_type'),'invoices.payment_type as payment_type',DB::raw('1 as type_id'))
            ->where('u.id',session('user_id'))
            ->where('invoices.to_show',1);

         if ($request->get('from_date') && $request->get('to_date')) {
             $from = Carbon::parse($request->get('from_date'))->format('Y-m-d');
             $to = Carbon::parse($request->get('to_date'))->format('Y-m-d');
             $invoices->whereBetween('invoices.invoicing_date', [$from,$to]);
         }

        $invoices->union($invoice);

        $datatables = Datatables::of($invoices)
            ->addColumn('invoice_number_button', function ($invoice) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $invoice->invoice_number . '</button>';
            })
            ->editColumn('total_charges', function ($invoice) {
                return number_format($invoice->total_charges, 2);
            })
            ->editColumn('total_gst', function ($invoice) {
                return number_format($invoice->total_gst, 2);
            })
            ->editColumn('total_invoice_amount', function ($invoice) {
                return number_format(ROUND($invoice->total_invoice_amount, 0, PHP_ROUND_HALF_DOWN));
            })
            ->editColumn('created_at', function ($invoice) {
                return Carbon::parse($invoice->created_at)->format('Y-m-d');
            })
            ->editColumn('invoicing_date', function ($invoice) {
                return Carbon::parse($invoice->invoicing_date)->format('Y-m-d');
            })
            ->editColumn('invoice_type',function($invoice){
                if($invoice->invoice_type == 1){
                    return 'Courier Invoice';
                }
                else{
                    return 'Packaging Invoice';
                }
            })
            ->filterColumn('invoice_type', function($query, $keyword) {
                if ($keyword == 1) {
                    $query->where('invoices.invoice_type', 1);
                }
                else if($keyword == 2){
                    $query->where('invoices.invoice_type', 2);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function ($invoice) {
                $export_to_excel_button = '<button type="button" class="dropdown-item export_to_excel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-download"></i></div><div class="col-9 offset-1">Export to Excel</div></button>';
                $origin_wise_print_button = '<button type="button" class="dropdown-item print_origin_wise"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Origin Wise Print</div></button>';
                $detail_print = '<button type="button" class="dropdown-item detail_print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Detailed Print</div></button>';
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= $export_to_excel_button;

                $dropdown .= $origin_wise_print_button;
                $dropdown .= $detail_print;

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            });

        return $datatables->make(true);
    }

    /*static public function generate_invoice_print($id, $email = FALSE, $header = FALSE) {

        $invoice = Invoice::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if ($header) {
            $html .= '
            <style>@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}</style>
            ';
        }

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $html .= '
                <div>
                  <div class="p-1">
        ';

        if ($header) {
            $html .= '
                    <div class="row no-gutters align-items-center summary_header mb-2">
                        <div class="col-12 text-right">
                            <img src="' . asset('img/invoice_summary_header_logo.png') . '" class="header">
                        </div>
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_heading.png') . '" class="heading">
                        </div>
                    </div>
            ';
        }

        $html .= '
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
                                </tr>';
        if($account_type_id == 2){
            $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
        }
        $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary"><strong>NTN</strong></td>
                                    <td>7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
        ';

        $shipment_details = array();

        $serial_number = array();

        $origins = array();

        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            }
            else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_details[$origin])) {
                $shipment_details[$origin] = '';
            }

            if (!isset($serial_number[$origin])) {
                $serial_number[$origin] = 1;
            }

            $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

            $serial_number[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                }
                else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            }
            else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges += $invoice_shipment->charges;
            $total_gst += $invoice_shipment->gst;
            $total_invoice_amount += $invoice_shipment->invoice_amount;
        }

        $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="12" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Packaging Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

        foreach ($origins as $origin) {
            $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
            ';
        }

        $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
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
        ';

        if ($header) {
            $html .= '
                    <div class="row no-gutters align-items-center summary_footer mt-2">
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_footer.png') . '" class="footer">
                        </div>
                    </div>
            ';
        }

        foreach ($origins as $origin) {
            $html .= '
                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="15">Shipment(s) Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Order ID</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Packaging Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $html .= $shipment_details[$origin];

            $html .= '
                      </tbody>
                    </table>
            ';
        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }*/
    public function generate_invoice_print($id, $email = FALSE, $header = FALSE) {
    $invoice = Invoice::find($id);

    $shipper = $invoice->shipper;

    $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

    $account_type_id = $shipper->account_type_id;

    $invoice_number_serial_number = 1;
    $html = '';

    if (!$email) {
        $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
    }

    $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

    $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

    if ($header) {
        $html .= '
            <style>@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}</style>
            ';
    }

    if (!$email) {
        $html .= '
              </head>
              <body>
            ';
    }

    $html .= '
                <div>
                  <div class="p-1">
        ';

    if ($header) {
        $html .= '
                    <div class="row no-gutters align-items-center summary_header mb-2">
                        <div class="col-12 text-right">
                            <img src="' . asset('img/invoice_summary_header_logo.png') . '" class="header">
                        </div>
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_heading.png') . '" class="heading">
                        </div>
                    </div>
            ';
    }

    /*$html .= '
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
                            </tr>';
                            if($account_type_id == 2){
                                $html .= '<tr>
                                    <td class="color secondary"><strong>Shipper Name</strong></td>
                                    <td>' . $shipper->name . '</td>
                                </tr>';
                            }
                            $html .= '<tr>
                                <td class="color secondary"><strong>Name</strong></td>
                                <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                            </tr>
                            <tr>
                                <td class="color secondary"><strong>Address</strong></td>
                                <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                            </tr>
                            <tr>
                                <td class="color secondary"><strong>Contact No.</strong></td>
                                <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>NTN</strong></td>
                              <td>' . $shipper->ntn_no . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>STRN</strong></td>
                              <td>' . $shipper->strn_no . '</td>
                            </tr>
                           </tbody>
                        </table>
                    </div>

                    <div class="col-4">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                                <td class="color primary"><strong>NTN</strong></td>
                                <td>7930679-5</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>SNTN</strong></td>
                                <td>S-7930679-5</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>PNTN</strong></td>
                                <td>P-7930679-5</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Billing Period</strong></td>
                                <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Invoice No.</strong></td>
                                <td>' . $invoice->invoice_number . '</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Invoice Date</strong></td>
                                <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Due Date</strong></td>
                                <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                            </tr>
                           </tbody>
                        </table>
                    </div>
                </div>
    ';*/

    $html .= '<div>
          <div class="p-1">';

    $html .= '
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
                        </tr>';
    if($account_type_id == 2){
        $html .= '<tr>
                                <td class="color secondary"><strong>Shipper Name</strong></td>
                                <td>' . $shipper->name . '</td>
                            </tr>';
    }
    $html .= '<tr>
                            <td class="color secondary"><strong>Name</strong></td>
                            <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                        </tr>
                        <tr>
                            <td class="color secondary"><strong>Address</strong></td>
                            <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                        </tr>
                        <tr>
                            <td class="color secondary"><strong>Contact No.</strong></td>
                            <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>NTN</strong></td>
                          <td>' . $shipper->ntn_no . '</td>
                        </tr>  ';
    if($invoice->invoice_type == 1) {
        $html .= '<tr>
                          <td class="color secondary"><strong>STRN</strong></td>
                          <td>' . $shipper->strn_no . '</td>
                        </tr>';
    }

    $html .= '</tbody>
                    </table>
                </div>

                <div class="col-4">
                    <table class="table table-sm table-bordered border">
                      <tbody> ';
    if($invoice->invoice_type == 1) {
        $html .=  '<tr>
                            <td class="color primary"><strong>NTN</strong></td>
                            <td>7930679-5</td>
                        </tr>
                        <tr>
                            <td class="color primary"><strong>SNTN</strong></td>
                            <td>S-7930679-5</td>
                        </tr>
                        <tr>
                            <td class="color primary"><strong>PNTN</strong></td>
                            <td>P-7930679-5</td>
                        </tr>';

    }

    $html .= '<tr>
                            <td class="color primary"><strong>Billing Period</strong></td>
                            <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                        </tr>
                        <tr>
                            <td class="color primary"><strong>Invoice No.</strong></td>
                            <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                        </tr>
                        <tr> ';
    if($invoice->invoice_type == 1) {
        $html .=  '<td class="color primary"><strong>Invoice Date</strong></td>
                            <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                        </tr>';

    }

    $html .= '<tr>
                            <td class="color primary"><strong>Due Date</strong></td>
                            <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                        </tr>
                       </tbody>
                    </table>
                </div>
            </div>
    ';


    $shipment_details = array();

    $serial_number = array();

    $origins = array();

    $total_charges = 0;
    $total_gst = 0;
    $total_invoice_amount = 0;

    if($invoice->invoice_type == 1) {
        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            } else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_details[$origin])) {
                $shipment_details[$origin] = '';
            }

            if (!isset($serial_number[$origin])) {
                $serial_number[$origin] = 1;
            }

            $shipment_details[$origin] .= '
                        <tr>
                          <td>' . $serial_number[$origin] . '</td>
                          <td>' . $shipment->tracking_number . '</td>
                          <td>' . $shipment->order_id . '</td>
                          <td>' . $shipment->consignee_city->name . '</td>
                          <td>' . $shipment->shipping_mode->mode . '</td>
                          <td>' . $date . '</td>
                          <td>' . $shipment->actual_weight . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                          <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                          <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                          <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                          <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                        </tr>
            ';

            $serial_number[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                } else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            } else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges += $invoice_shipment->charges;
            $total_gst += $invoice_shipment->gst;
            $total_invoice_amount += $invoice_shipment->invoice_amount;
        }

        $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="12" class="color primary text-center">Invoice Summary</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Origin</th>
                            <th class="color secondary">Weight Charges (PKR)</th>
                            <th class="color secondary">Cash Handling Charges (PKR)</th>
                            <th class="color secondary">Insurance Charges (PKR)</th>
                            <th class="color secondary">Replacement Charges (PKR)</th>
                            <th class="color secondary">Try & Buy Charges (PKR)</th>
                            <th class="color secondary">Return Charges (PKR)</th>
                            <th class="color secondary">Fuel Surcharge (PKR)</th>
                            <th class="color secondary">Intercept Charges (PKR)</th>
                            <th class="color secondary">OSA Charges (PKR)</th>
                            <th class="color secondary">Packaging Charges (PKR)</th>
                            <th class="color secondary">Adjustment Charges (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

        foreach ($origins as $origin) {
            $html .= '
                        <tr>
                            <td>' . $origin . '</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
            ';
        }

        $html .= '
                      </tbody>
                    </table>

                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst, 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
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
        ';

        if ($header) {
            $html .= '
                    <div class="row no-gutters align-items-center summary_footer mt-2">
                        <div class="col-12 text-center">
                            <img src="' . asset('img/invoice_summary_header_footer.png') . '" class="footer">
                        </div>
                    </div>
            ';
        }

        foreach ($origins as $origin) {
            $html .= '
                    <table class="table table-sm table-bordered border shipments_summary">
                      <thead>
                        <tr>
                            <th class="color primary text-center" colspan="15">Shipment(s) Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                          <th class="color secondary">S. No.</th>
                          <th class="color secondary">Tracking No.</th>
                          <th class="color secondary">Order ID</th>
                          <th class="color secondary">Destination</th>
                          <th class="color secondary">Shipping Mode</th>
                          <th class="color secondary">Arrival Date</th>
                          <th class="color secondary">Weight (kg)</th>
                          <th class="color secondary">Weight Charges (PKR)</th>
                          <th class="color secondary">Fuel Surcharge (PKR)</th>
                          <th class="color secondary">OSA Charges (PKR)</th>
                          <th class="color secondary">Adjustment Charges (PKR)</th>
                          <th class="color secondary">Packaging Charges (PKR)</th>
                          <th class="color secondary">Total Charges (PKR)</th>
                          <th class="color secondary">GST (PKR)</th>
                          <th class="color secondary">Invoice Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $html .= $shipment_details[$origin];

            $html .= '
                      </tbody>
                    </table>
            ';
        }

    }
    else {
        $shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();

        $size_array = array();
        $packaging_details = array();

        if(count($shipment_ids) > 0) {

            $packaging_materials = PackagingMaterialRequest::whereIn('shipment_id',$shipment_ids)->where('packaging_material_requests.status_id', 4)
                ->where('packaging_material_requests.user_id', $shipper->id);

            if ($packaging_materials->exists()) {
                $packaging_materials = $packaging_materials->orderBy('city_id', 'asc')->get();
                foreach ($packaging_materials as $packaging_material) {
                    foreach ($packaging_material->items as $details) {
                        if (!isset($packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'])) {
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = 1;
                            $zone = Zone::find($packaging_material->city->zone_id);
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['origin'] = $packaging_material->city->name;
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['rates'] = $details->packaging_type_size->standard_charges;
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['description'] = $details->packaging_type_size->type->type . '-' . $details->packaging_type_size->size;
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = $details->quantity;
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['gst'] = $zone->gst;
                        }
                        else {
                            $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity']++;
                        }
                    }
                }
            }

        }

        if (count($packaging_details) > 0) {

            $html .= '<table class="table table-sm table-bordered border">
          <thead>
            <tr>
                <th colspan="12" class="color primary text-center">Invoice Summary</th>
            </tr>
            <tr>
                <th class="color secondary">Origin</th>
                <th class="color secondary">Description</th>
                <th class="color secondary">Rates</th>
                <th class="color secondary">Quantity</th>
                <th class="color secondary">Total Charges Without GST</th>
                <th class="color secondary">SST %</th>
                <th class="color secondary">SST Amount</th>
                <th class="color secondary">Total Amount with SST</th>
              
            </tr>
          </thead>
          <tbody>
';
            $rates_total = 0;
            $quantity_total = 0;
            $total_amount_without_gst = 0;
            $total_sst_amount = 0;
            $overall_amount = 0;

            foreach ($packaging_details as $cities) {
                foreach ($cities as $packaging_material) {
                    $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                    $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                    $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                    $rates_total = $rates_total + $packaging_material['rates'];
                    $quantity_total = $quantity_total + $packaging_material['quantity'];
                    $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                    $total_sst_amount = $total_sst_amount + $sst_amount;
                    $overall_amount = $overall_amount + $total_amount_with_sst;
                    $html .= '
                        <tr>
                            <td>' . $packaging_material['origin'] . '</td>
                            <td>' . $packaging_material['description'] . '</td>
                            <td>' . $packaging_material['rates'] . '</td>
                            <td>' . $packaging_material['quantity'] . '</td>
                            <td>' . $amount_without_gst . '</td>
                            <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                            <td>' . round($sst_amount) . '</td>
                            <td>' . number_format($total_amount_with_sst) . '</td>
                          
                        </tr>';
                    /* $html .= $packaging_material['origin'];*/
                }
            }

            $html .= '<tr>
                <td colspan="3" class="text-center">Total Amount</td>
            
                <td>' . $quantity_total . '</td>
                <td>' . $total_amount_without_gst . '</td>
                <td></td>
                <td>' . number_format($total_sst_amount) . '</td>
                <td>' . number_format($overall_amount) . '</td>
            </tr>';
            $amount_in_words = '';
            $amount_in_words = self::amount_to_words($overall_amount);

            $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
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
                          <td class="color secondary" style="width: 150px;"><strong>IBAN No.</strong></td>
                          <td>PK02MEZN0099120104111731</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Name.</strong></td>
                          <td>Liaquat Market Malir Branch</td>
                        </tr>';

            $html .= ' <tr>
                                      <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                                      <td>9912</td>
                                    </tr>
                                    </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';
        }


        $html .= '
                      </tbody>
                    </table>
            ';

        $invoice_number_serial_number++;
    }

    $html .= '
                  </div>
                </div>
        ';

    if (!$email) {
        $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
    }

    return $html;
}


    public function invoices_detail_print (Request $request) {
        $invoice = Invoice::find($request->id);
        

        if ($invoice) {
            if ($request->has('header')) {
                return self::generate_invoice_print($invoice->id, FALSE, TRUE);
            }
            else {
                return self::generate_invoice_print($invoice->id);
            }
        }
        else {
            return '';
        }
    }


    public function invoices_export_to_excel(Request $request) {
        $invoice = Invoice::find($request->id);

        $filename = 'sonic_invoice_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Origin', 'Destination', 'Arrival Date', 'Weight (kg)', 'Weight Charges (PKR)', 'Fuel Surcharge (PKR)', 'OSA Charges (PKR)', 'Adjustment Charges (PKR)', 'Total Charges (PKR)', 'GST (PKR)', 'Invoice Amount (PKR)'];

        $serial_number = 1;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            }
            else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->created_at;
            $row[] = $shipment->actual_weight;
            $row[] = (($invoice_shipment->type != 2) ? $shipment->weight_charges : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->fuel_surcharge : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->nsa_osa_charges : 0);
            $row[] = (($invoice_shipment->type == 2) ? $shipment->adjustment_charges : 0);
            $row[] = $invoice_shipment->charges;
            $row[] = $invoice_shipment->gst;
            $row[] = $invoice_shipment->invoice_amount;

            $details[] = $row;

            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function reimbursement_invoices_export_to_excel(Request $request) {
        $invoice = InvoiceForReimbursement::find($request->id);

        $filename = 'sonic_invoice_details_' . $request->id . '.xlsx';

        $details = array();

        $details[] = ['S. No.', 'Tracking No.', 'Origin', 'Destination', 'Arrival Date', 'Weight (kg)', 'Weight Charges (PKR)', 'Fuel Surcharge (PKR)', 'OSA Charges (PKR)', 'Adjustment Charges (PKR)', 'Total Charges (PKR)', 'GST (PKR)', 'Invoice Amount (PKR)'];

        $serial_number = 1;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            }
            else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $row = array();

            $row[] = $serial_number;
            $row[] = $shipment->tracking_number;
            $row[] = $shipment->pickup_address->city->name;
            $row[] = $shipment->consignee_city->name;
            $row[] = $shipment->created_at;
            $row[] = $shipment->actual_weight;
            $row[] = (($invoice_shipment->type != 2) ? $shipment->weight_charges : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->fuel_surcharge : 0);
            $row[] = (($invoice_shipment->type != 2) ? $shipment->nsa_osa_charges : 0);
            $row[] = (($invoice_shipment->type == 2) ? $shipment->adjustment_charges : 0);
            $row[] = $invoice_shipment->charges;
            $row[] = $invoice_shipment->gst;
            $row[] = $invoice_shipment->invoice_amount;

            $details[] = $row;

            $serial_number++;
        }

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode('#,##0.00');

        $spreadsheet->getActiveSheet()->fromArray($details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename .'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function invoices_email_reminder(Request $request) {
        $invoice = Invoice::find($request->id);

        if ($invoice) {
            $invoice->status_id = 2;

            $invoice->save();

            NotificationsController::send(28, $request->id);

            return ['status' => 0, 'success' => 'Reminder has been Sent'];
        }
        else {
            return ['status' => 1, 'error' => 'No such Invoice'];
        }
    }

    public function invoices_print_origin_wise(Request $request) {
        if($account_type = $request->get('account_type'))
        {
            if($account_type == 1)
            {
                $invoice = InvoiceForReimbursement::find($request->id);

                if ($invoice) {
                    return self::generate_reimbursement_invoice_print_origin_wise($invoice->id);
                }
                else {
                    return '';
                }
            }
            else if($account_type == 2)
            {
                $invoice = Invoice::find($request->id);

                if ($invoice) {
                    return self::generate_invoice_print_origin_wise($invoice->id);
                }
                else {
                    return '';
                }
            }
            else{
                return "";
            }
        }
        else{
            return "";
        }

    }
    
    static private function amount_to_words($amount) {
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

    static public function generate_invoice_print_origin_wise($id, $email = FALSE) {
        $invoice = Invoice::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $shipment_counts = array();

        $origins = array();

        $total_charges = array();
        $total_gst = array();
        $total_invoice_amount = array();

        $packaging_details = array();

        if($invoice->invoice_type == 1) {
            foreach ($invoice->invoice_shipments as $invoice_shipment) {
                $shipment = $invoice_shipment->shipment;

                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

                if ($shipment_journey->exists()) {
                    $date = $shipment_journey->first()->created_at;
                } else {
                    $date = $shipment->created_at;
                }

                $date = Carbon::parse($date)->format('Y-m-d');

                $origin = $shipment->pickup_address->city->name;

                if (!in_array($origin, $origins)) {
                    $origins[] = $origin;
                }

                if (!isset($shipment_counts[$origin])) {
                    $shipment_counts[$origin] = 1;
                }

                $shipment_counts[$origin]++;

                if (!isset($total_weight_charges[$origin])) {
                    $total_weight_charges[$origin] = 0;
                }

                if (!isset($total_cash_handling_charges[$origin])) {
                    $total_cash_handling_charges[$origin] = 0;
                }

                if (!isset($total_insurance_charges[$origin])) {
                    $total_insurance_charges[$origin] = 0;
                }

                if (!isset($total_return_charges[$origin])) {
                    $total_return_charges[$origin] = 0;
                }

                if (!isset($total_fuel_surcharge[$origin])) {
                    $total_fuel_surcharge[$origin] = 0;
                }

                if (!isset($total_replacement_charges[$origin])) {
                    $total_replacement_charges[$origin] = 0;
                }

                if (!isset($total_try_and_buy_charges[$origin])) {
                    $total_try_and_buy_charges[$origin] = 0;
                }

                if (!isset($total_packaging_material_charges[$origin])) {
                    $total_packaging_material_charges[$origin] = 0;
                }

                if (!isset($total_intercept_charges[$origin])) {
                    $total_intercept_charges[$origin] = 0;
                }

                if (!isset($total_nsa_osa_charges[$origin])) {
                    $total_nsa_osa_charges[$origin] = 0;
                }

                if (!isset($total_adjustment_charges[$origin])) {
                    $total_adjustment_charges[$origin] = 0;
                }

                if (!isset($total_charges[$origin])) {
                    $total_charges[$origin] = 0;
                }

                if (!isset($total_gst[$origin])) {
                    $total_gst[$origin] = 0;
                }

                if (!isset($total_invoice_amount[$origin])) {
                    $total_invoice_amount[$origin] = 0;
                }

                if ($invoice_shipment->type != 2) {
                    if ($invoice_shipment->type == 0) {
                        $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                        $total_replacement_charges[$origin] += $shipment->replacement_charges;
                        $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                    } else {
                        $total_return_charges[$origin] += $shipment->return_charges;
                    }

                    $total_weight_charges[$origin] += $shipment->weight_charges;

                    if ($shipment->packaging_material_request) {
                        $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                    }

                    $total_insurance_charges[$origin] += $shipment->insurance_charges;
                    $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                    $total_intercept_charges[$origin] += $shipment->intercept_charges;
                    $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
                } else {
                    $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
                }

                $total_charges[$origin] += $invoice_shipment->charges;
                $total_gst[$origin] += $invoice_shipment->gst;
                $total_invoice_amount[$origin] += $invoice_shipment->invoice_amount;
            }
        }
        else{

            $shipment_ids = $invoice->invoice_shipments->pluck('shipment_id')->toArray();

            if(count($shipment_ids) > 0) {

                $packaging_materials = PackagingMaterialRequest::whereIn('shipment_id',$shipment_ids)->where('packaging_material_requests.status_id', 4)
                    ->where('packaging_material_requests.user_id', $shipper->id);

                if ($packaging_materials->exists()) {
                    $packaging_materials = $packaging_materials->orderBy('city_id', 'asc')->get();
                    foreach ($packaging_materials as $packaging_material) {
                        foreach ($packaging_material->items as $details) {
                            if (!isset($packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'])) {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = 1;
                                $zone = Zone::find($packaging_material->city->zone_id);
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['origin'] = $packaging_material->city->name;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['rates'] = $details->packaging_type_size->standard_charges;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['description'] = $details->packaging_type_size->type->type . '-' . $details->packaging_type_size->size;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity'] = $details->quantity;
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['gst'] = $zone->gst;
                            }
                            else {
                                $packaging_details[$packaging_material->city_id][$details->packaging_type_size->id]['quantity']++;
                            }
                        }
                    }
                }

            }

        }

        $invoice_number_serial_number = 1;

        if(count($origins) > 0 && $invoice->invoice_type == 1) {
            foreach ($origins as $origin) {

                $html .= '
                <div>
                  <div class="p-1">
        ';
                $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        
                        <div class="col-6">
                            <h1><b><u>SALES TAX INVOICE</u></b></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Bill To</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
                if($account_type_id == 2){
                    $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
                }
                $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

                $html .= '<tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>';


                $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <h1 style="text-align:center"><img style="width:50%;height:90px" src="' . asset('img/invoice_summary_header_logo.png') . '" class="header"></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
                if($invoice->invoice_type == 1) {
                    $html .=  '<tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>';

                }

                $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';

                $html .=  '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';



                $html .= '<tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

                $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="3" class="color primary text-center">Invoice Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Description Of Services</th>
                            <th class="color secondary">Quantity</th>
                            <th class="color secondary">Total Charges</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Weight Charges</td>
                            <td rowspan="11">'.$shipment_counts[$origin].'</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Fuel Surcharge</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Cash Handling Charges</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Insurance Charges</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Replacement Charges</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Try & Buy Charges</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Return Charges</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Intercept Charges</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>OSA Charges</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Packaging Charges</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Adjustments</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
                      </tbody>
                      </table>
            ';
                $html .= '
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>SST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>';

                $amount_in_words = '';
                $amount_in_words = self::amount_to_words(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN));

                $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
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
                          <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                          <td>9912</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Address.</strong></td>
                          <td>Liaqatabad Market Malir Branch</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

                $invoice_number_serial_number++;
            }
        }
        else{

            $html .= '
                <div>
                  <div class="p-1">
        ';
            $html .= '
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
                                </tr>';
            if($account_type_id == 2){
                $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
            }
            $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

            $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody> ';


            $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';
            if($invoice->invoice_type == 1) {
                $html .=  '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';

            }

            $html .= '<tr>
                                    <td class="color primary"><strong>Due Date</strong></td>
                                    <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                                </tr>
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

            $html .= '<table class="table table-sm table-bordered border">
          <thead>
            <tr>
                <th colspan="12" class="color primary text-center">Invoice Summary</th>
            </tr>
            <tr>
                <th class="color secondary">Origin</th>
                <th class="color secondary">Description</th>
                <th class="color secondary">Rates</th>
                <th class="color secondary">Quantity</th>
                <th class="color secondary">Total Charges Without GST</th>
                <th class="color secondary">SST %</th>
                <th class="color secondary">SST Amount</th>
                <th class="color secondary">Total Amount with SST</th>
              
            </tr>
          </thead>
          <tbody>
';
            $rates_total = 0;
            $quantity_total = 0;
            $total_amount_without_gst = 0;
            $total_sst_amount = 0;
            $overall_amount = 0;


            foreach ($packaging_details as $cities) {
                foreach ($cities as $packaging_material) {
                    $amount_without_gst = $packaging_material['rates'] * $packaging_material['quantity'];
                    $sst_amount = round($amount_without_gst * $packaging_material['gst']);
                    $total_amount_with_sst = round($amount_without_gst + $sst_amount);

                    $rates_total = $rates_total + $packaging_material['rates'];
                    $quantity_total = $quantity_total + $packaging_material['quantity'];
                    $total_amount_without_gst = $total_amount_without_gst + $amount_without_gst;
                    $total_sst_amount = $total_sst_amount + $sst_amount;
                    $overall_amount = $overall_amount + $total_amount_with_sst;
                    $html .= '
                        <tr>
                            <td>' . $packaging_material['origin'] . '</td>
                            <td>' . $packaging_material['description'] . '</td>
                            <td>' . $packaging_material['rates'] . '</td>
                            <td>' . $packaging_material['quantity'] . '</td>
                            <td>' . $amount_without_gst . '</td>
                            <td>' . $packaging_material['gst'] * 100 . '%' . '</td>
                            <td>' . round($sst_amount) . '</td>
                            <td>' . number_format($total_amount_with_sst) . '</td>
                          
                        </tr>';
                }
            }



            $html .= '<tr>
                <td colspan="3" class="text-center">Total Amount</td>
            
                <td>'.$quantity_total.'</td>
                <td>'.$total_amount_without_gst.'</td>
                <td></td>
                <td>'.number_format($total_sst_amount).'</td>
                <td>'.number_format($overall_amount).'</td>
            </tr>';
            $amount_in_words = '';
            $amount_in_words = self::amount_to_words($overall_amount);

            $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
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
                          <td class="color secondary" style="width: 150px;"><strong>IBAN No.</strong></td>
                          <td>PK02MEZN0099120104111731</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Name.</strong></td>
                          <td>Liaquat Market Malir Branch</td>
                        </tr>';

            $html .= ' <tr>
                                      <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                                      <td>9912</td>
                                    </tr>
                                    </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';


            $html .= '
                      </tbody>
                    </table>
            ';


        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    static public function generate_reimbursement_invoice_print_origin_wise($id, $email = FALSE) {
        $invoice = InvoiceForReimbursement::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Invoice</title>
            ';
        }

        $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';

        $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

        if (!$email) {
            $html .= '
              </head>
              <body>
            ';
        }

        $shipment_details = array();

        $shipment_counts = array();

        $origins = array();

        $total_charges = array();
        $total_gst = array();
        $total_invoice_amount = array();

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            }
            else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_counts[$origin])) {
                $shipment_counts[$origin] = 1;
            }

            $shipment_counts[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if (!isset($total_charges[$origin])) {
                $total_charges[$origin] = 0;
            }

            if (!isset($total_gst[$origin])) {
                $total_gst[$origin] = 0;
            }

            if (!isset($total_invoice_amount[$origin])) {
                $total_invoice_amount[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                }
                else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            }
            else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges[$origin] += $invoice_shipment->charges;
            $total_gst[$origin] += $invoice_shipment->gst;
            $total_invoice_amount[$origin] += $invoice_shipment->invoice_amount;
        }

        $invoice_number_serial_number = 1;

        $html .= '
                <div>
                  <div class="p-1">
        ';

        foreach ($origins as $origin) {

            $html .= '
                <div>
                  <div class="p-1">
        ';
            $html .= '
                    <div class="row align-items-start justify-content-between summary">
                        
                        <div class="col-6">
                            <h1><b><u>SALES TAX INVOICE</u></b></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Bill To</strong></td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Account No.</strong></td>
                                    <td>' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . '</td>
                                </tr>';
            if($account_type_id == 2){
                $html .= '<tr>
                                        <td class="color secondary"><strong>Shipper Name</strong></td>
                                        <td>' . $shipper->name . '</td>
                                    </tr>';
            }
            $html .= '<tr>
                                    <td class="color secondary"><strong>Name</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Address</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>Contact No.</strong></td>
                                    <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary"><strong>NTN</strong></td>
                                  <td>' . $shipper->ntn_no . '</td>
                                </tr>  ';

            $html .= '<tr>
                                  <td class="color secondary"><strong>STRN</strong></td>
                                  <td>' . $shipper->strn_no . '</td>
                                </tr>';


            $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                            <h1 style="text-align:center"><img style="width:50%;height:85px" src="' . asset('img/invoice_summary_header_logo.png') . '" class="header"></h1>
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
            $html .=  '<tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>PNTN</strong></td>
                                    <td>P-7930679-5</td>
                                </tr>';

            $html .= '<tr>
                                    <td class="color primary"><strong>Billing Period</strong></td>
                                    <td>' . Carbon::parse($invoice->billing_period_from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->billing_period_to_date)->format('Y-m-d') . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>Invoice No.</strong></td>
                                    <td>' . $invoice->invoice_number . ' - ' . $invoice_number_serial_number . '</td>
                                </tr>
                                <tr> ';

            $html .=  '<td class="color primary"><strong>Invoice Date</strong></td>
                                    <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                                </tr>';



            $html .= '
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';

            $html .= '
                    <table class="table table-sm table-bordered border">
                      <thead>
                        <tr>
                            <th colspan="3" class="color primary text-center">Invoice Summary - ' . $origin . '</th>
                        </tr>
                        <tr>
                            <th class="color secondary">Description Of Services</th>
                            <th class="color secondary">Quantity</th>
                            <th class="color secondary">Total Charges</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Weight Charges</td>
                            <td rowspan="11">'.$shipment_counts[$origin].'</td>
                            <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Fuel Surcharge</td>
                            <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Cash Handling Charges</td>
                            <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Insurance Charges</td>
                            <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Replacement Charges</td>
                            <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Try & Buy Charges</td>
                            <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Return Charges</td>
                            <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Intercept Charges</td>
                            <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>OSA Charges</td>
                            <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Packaging Charges</td>
                            <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                        </tr>
                        <tr>
                            <td>Adjustments</td>
                            <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                        </tr>
                      </tbody>
                      </table>
            ';
            $html .= '
                    <div class="row justify-content-end">
                        <div class="col-4">
                            <table class="table table-sm table-bordered border">
                              <tbody>
                                <tr>
                                  <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_charges[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color secondary text-left"><strong>SST (PKR)</strong></td>
                                  <td class="text-right">' . number_format($total_gst[$origin], 2) . '</td>
                                </tr>
                                <tr>
                                  <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                                  <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>';

            $amount_in_words = '';
            $amount_in_words = self::amount_to_words(ROUND($total_invoice_amount[$origin], 0, PHP_ROUND_HALF_DOWN));

            $html .= '<table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                          <td class="color secondary">' . $amount_in_words . ' Only</td>
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
                          <td class="color secondary" style="width: 150px;"><strong>Branch Code.</strong></td>
                          <td>9912</td>
                        </tr>
                        <tr>
                          <td class="color secondary" style="width: 150px;"><strong>Branch Address.</strong></td>
                          <td>Liaqatabad Market Malir Branch</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="mb-1 text-center font-italic"><strong>Disclaimer:</strong> This is a system generated invoice. No signature required.</div>
            ';

            $invoice_number_serial_number++;
        }

        $html .= '
                  </div>
                </div>
        ';

        if (!$email) {
            $html .= '
                <script>
                  window.onload = function() {

                    window.print();
                  }
                </script>
              </body>
            </html>
            ';
        }

        return $html;
    }

    public function reimbursement_invoices_print(Request $request) {
        $invoice = InvoiceForReimbursement::find($request->id);

        if ($invoice) {
            if ($request->has('header')) {
                return self::generate_reimbursement_invoice_print($invoice->id, FALSE, TRUE);
            }
            else {
                return self::generate_reimbursement_invoice_print($invoice->id);
            }
        }
        else {
            return '';
        }
    }
    static public function generate_reimbursement_invoice_print($id, $email = FALSE, $header = FALSE) {
        $invoice = InvoiceForReimbursement::find($id);

        $shipper = $invoice->shipper;

        $shipper_bank = $shipper->bank()->where('default_bank', 1)->first();

        $account_type_id = $shipper->account_type_id;

        $html = '';

        if (!$email) {
            $html .= '
        <!doctype html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <title>Invoice</title>
        ';
        }

        $html .= '
            <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
    ';

        $html .= '
        <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
    ';

        if ($header) {
            $html .= '
        <style>@page{margin-top: 1rem; margin-bottom: 1rem;}.summary_header .header{width: 10%;}.summary_header .heading{width: 15%;}.summary_footer .footer{width: 75%;}</style>
        ';
        }

        if (!$email) {
            $html .= '
          </head>
          <body>
        ';
        }

        $html .= '
            <div>
              <div class="p-1">
    ';

        if ($header) {
            $html .= '
                <div class="row no-gutters align-items-center summary_header mb-2">
                    <div class="col-12 text-right">
                        <img src="' . asset('img/invoice_summary_header_logo.png') . '" class="header">
                    </div>
                    <div class="col-12 text-center">
                        <img src="' . asset('img/invoice_summary_header_heading.png') . '" class="heading">
                    </div>
                </div>
        ';
        }

        $html .= '
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
                            </tr>';
        if($account_type_id == 2){
            $html .= '<tr>
                                    <td class="color secondary"><strong>Shipper Name</strong></td>
                                    <td>' . $shipper->name . '</td>
                                </tr>';
        }
        $html .= '<tr>
                                <td class="color secondary"><strong>Name</strong></td>
                                <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_name : $shipper->name) . '</td>
                            </tr>
                            <tr>
                                <td class="color secondary"><strong>Address</strong></td>
                                <td>' . (($account_type_id == 2) ? $shipper_bank->billing_address : $shipper->address) . '</td>
                            </tr>
                            <tr>
                                <td class="color secondary"><strong>Contact No.</strong></td>
                                <td>' . (($account_type_id == 2) ? $shipper_bank->billing_person_phone : $shipper->phone)  . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>NTN</strong></td>
                              <td>' . $shipper->ntn_no . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>STRN</strong></td>
                              <td>' . $shipper->strn_no . '</td>
                            </tr>
                           </tbody>
                        </table>
                    </div>

                    <div class="col-4">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                                <td class="color primary"><strong>NTN</strong></td>
                                <td>7930679-5</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>SNTN</strong></td>
                                <td>S-7930679-5</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>PNTN</strong></td>
                                <td>P-7930679-5</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Billing Period</strong></td>
                                <td>' . Carbon::parse($invoice->from_date)->format('Y-m-d') . ' <-> ' . Carbon::parse($invoice->to_date)->format('Y-m-d') . '</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Invoice No.</strong></td>
                                <td>' . $invoice->invoice_number . '</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Invoice Date</strong></td>
                                <td>' . Carbon::parse($invoice->invoicing_date)->format('Y-m-d') . '</td>
                            </tr>
                            <tr>
                                <td class="color primary"><strong>Due Date</strong></td>
                                <td>' . Carbon::parse($invoice->due_date)->format('Y-m-d') . '</td>
                            </tr>
                           </tbody>
                        </table>
                    </div>
                </div>
    ';

        $shipment_details = array();

        $serial_number = array();

        $origins = array();

        $total_charges = 0;
        $total_gst = 0;
        $total_invoice_amount = 0;

        foreach ($invoice->invoice_shipments as $invoice_shipment) {
            $shipment = $invoice_shipment->shipment;

            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 2);

            if ($shipment_journey->exists()) {
                $date = $shipment_journey->first()->created_at;
            }
            else {
                $date = $shipment->created_at;
            }

            $date = Carbon::parse($date)->format('Y-m-d');

            $origin = $shipment->pickup_address->city->name;

            if (!in_array($origin, $origins)) {
                $origins[] = $origin;
            }

            if (!isset($shipment_details[$origin])) {
                $shipment_details[$origin] = '';
            }

            if (!isset($serial_number[$origin])) {
                $serial_number[$origin] = 1;
            }

            $shipment_details[$origin] .= '
                    <tr>
                      <td>' . $serial_number[$origin] . '</td>
                      <td>' . $shipment->tracking_number . '</td>
                      <td>' . $shipment->order_id . '</td>
                      <td>' . $shipment->consignee_city->name . '</td>
                      <td>' . $shipment->shipping_mode->mode . '</td>
                      <td>' . $date . '</td>
                      <td>' . $shipment->actual_weight . '</td>
                      <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->weight_charges, 2) : '0') . '</td>
                      <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->fuel_surcharge, 2) : '0') . '</td>
                      <td>' . (($invoice_shipment->type != 2) ? number_format($shipment->nsa_osa_charges, 2) : '0') . '</td>
                      <td>' . (($invoice_shipment->type == 2) ? number_format($invoice_shipment->invoice_amount, 2) : '0') . '</td>
                      <td>' . (($invoice_shipment->type == 2) ? number_format($shipment->packaging_material_charges, 2) : '0') . '</td>
                      <td>' . number_format($invoice_shipment->charges, 2) . '</td>
                      <td>' . number_format($invoice_shipment->gst, 2) . '</td>
                      <td>' . number_format($invoice_shipment->invoice_amount, 2) . '</td>
                    </tr>
        ';

            $serial_number[$origin]++;

            if (!isset($total_weight_charges[$origin])) {
                $total_weight_charges[$origin] = 0;
            }

            if (!isset($total_cash_handling_charges[$origin])) {
                $total_cash_handling_charges[$origin] = 0;
            }

            if (!isset($total_insurance_charges[$origin])) {
                $total_insurance_charges[$origin] = 0;
            }

            if (!isset($total_return_charges[$origin])) {
                $total_return_charges[$origin] = 0;
            }

            if (!isset($total_fuel_surcharge[$origin])) {
                $total_fuel_surcharge[$origin] = 0;
            }

            if (!isset($total_replacement_charges[$origin])) {
                $total_replacement_charges[$origin] = 0;
            }

            if (!isset($total_try_and_buy_charges[$origin])) {
                $total_try_and_buy_charges[$origin] = 0;
            }

            if (!isset($total_packaging_material_charges[$origin])) {
                $total_packaging_material_charges[$origin] = 0;
            }

            if (!isset($total_intercept_charges[$origin])) {
                $total_intercept_charges[$origin] = 0;
            }

            if (!isset($total_nsa_osa_charges[$origin])) {
                $total_nsa_osa_charges[$origin] = 0;
            }

            if (!isset($total_adjustment_charges[$origin])) {
                $total_adjustment_charges[$origin] = 0;
            }

            if ($invoice_shipment->type != 2) {
                if ($invoice_shipment->type == 0) {
                    $total_cash_handling_charges[$origin] += $shipment->cash_handling_charges;
                    $total_replacement_charges[$origin] += $shipment->replacement_charges;
                    $total_try_and_buy_charges[$origin] += $shipment->try_and_buy_charges;
                }
                else {
                    $total_return_charges[$origin] += $shipment->return_charges;
                }

                $total_weight_charges[$origin] += $shipment->weight_charges;

                if ($shipment->packaging_material_request) {
                    $total_packaging_material_charges[$origin] += $shipment->packaging_material_charges;
                }

                $total_insurance_charges[$origin] += $shipment->insurance_charges;
                $total_fuel_surcharge[$origin] += $shipment->fuel_surcharge;
                $total_intercept_charges[$origin] += $shipment->intercept_charges;
                $total_nsa_osa_charges[$origin] += $shipment->nsa_osa_charges;
            }
            else {
                $total_adjustment_charges[$origin] += $invoice_shipment->invoice_amount;
            }

            $total_charges += $invoice_shipment->charges;
            $total_gst += $invoice_shipment->gst;
            $total_invoice_amount += $invoice_shipment->invoice_amount;
        }

        $html .= '
                <table class="table table-sm table-bordered border">
                  <thead>
                    <tr>
                        <th colspan="12" class="color primary text-center">Invoice Summary</th>
                    </tr>
                    <tr>
                        <th class="color secondary">Origin</th>
                        <th class="color secondary">Weight Charges (PKR)</th>
                        <th class="color secondary">Cash Handling Charges (PKR)</th>
                        <th class="color secondary">Insurance Charges (PKR)</th>
                        <th class="color secondary">Replacement Charges (PKR)</th>
                        <th class="color secondary">Try & Buy Charges (PKR)</th>
                        <th class="color secondary">Return Charges (PKR)</th>
                        <th class="color secondary">Fuel Surcharge (PKR)</th>
                        <th class="color secondary">Intercept Charges (PKR)</th>
                        <th class="color secondary">OSA Charges (PKR)</th>
                        <th class="color secondary">Packaging Charges (PKR)</th>
                        <th class="color secondary">Adjustment Charges (PKR)</th>
                    </tr>
                  </thead>
                  <tbody>
    ';

        foreach ($origins as $origin) {
            $html .= '
                    <tr>
                        <td>' . $origin . '</td>
                        <td>' . number_format($total_weight_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_cash_handling_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_insurance_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_replacement_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_try_and_buy_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_return_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_fuel_surcharge[$origin], 2) . '</td>
                        <td>' . number_format($total_intercept_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_nsa_osa_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_packaging_material_charges[$origin], 2) . '</td>
                        <td>' . number_format($total_adjustment_charges[$origin], 2) . '</td>
                    </tr>
        ';
        }

        $html .= '
                  </tbody>
                </table>

                <div class="row justify-content-end">
                    <div class="col-4">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color secondary text-left"><strong>Subtotal (PKR)</strong></td>
                              <td class="text-right">' . number_format($total_charges, 2) . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary text-left"><strong>GST (PKR)</strong></td>
                              <td class="text-right">' . number_format($total_gst, 2) . '</td>
                            </tr>
                            <tr>
                              <td class="color primary text-left"><strong>Total Invoice Amount (PKR)</strong></td>
                              <td class="color secondary text-right">' . number_format(ROUND($total_invoice_amount, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                </div>

                <table class="table table-sm table-bordered border">
                  <tbody>
                    <tr>
                      <td class="color primary" style="width: 150px;"><strong>Amount in Words</strong></td>
                      <td class="color secondary">' . self::amount_to_words($total_invoice_amount) . ' Only</td>
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
    ';

        if ($header) {
            $html .= '
                <div class="row no-gutters align-items-center summary_footer mt-2">
                    <div class="col-12 text-center">
                        <img src="' . asset('img/invoice_summary_header_footer.png') . '" class="footer">
                    </div>
                </div>
        ';
        }

        foreach ($origins as $origin) {
            $html .= '
                <table class="table table-sm table-bordered border shipments_summary">
                  <thead>
                    <tr>
                        <th class="color primary text-center" colspan="15">Shipment(s) Summary - ' . $origin . '</th>
                    </tr>
                    <tr>
                      <th class="color secondary">S. No.</th>
                      <th class="color secondary">Tracking No.</th>
                      <th class="color secondary">Order ID</th>
                      <th class="color secondary">Destination</th>
                      <th class="color secondary">Shipping Mode</th>
                      <th class="color secondary">Arrival Date</th>
                      <th class="color secondary">Weight (kg)</th>
                      <th class="color secondary">Weight Charges (PKR)</th>
                      <th class="color secondary">Fuel Surcharge (PKR)</th>
                      <th class="color secondary">OSA Charges (PKR)</th>
                      <th class="color secondary">Adjustment Charges (PKR)</th>
                      <th class="color secondary">Packaging Charges (PKR)</th>
                      <th class="color secondary">Total Charges (PKR)</th>
                      <th class="color secondary">GST (PKR)</th>
                      <th class="color secondary">Invoice Amount (PKR)</th>
                    </tr>
                </thead>
                <tbody>
        ';

            $html .= $shipment_details[$origin];

            $html .= '
                  </tbody>
                </table>
        ';
        }

        $html .= '
              </div>
            </div>
    ';

        if (!$email) {
            $html .= '
            <script>
              window.onload = function() {

                window.print();
              }
            </script>
          </body>
        </html>
        ';
        }

        return $html;
    }
}

