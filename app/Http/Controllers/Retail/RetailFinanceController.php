<?php

namespace App\Http\Controllers\Retail;


use App\Http\Controllers\Controller;
use App\Http\Models\Admin\ChangeShipmentWeightLog;
use App\Http\Models\RetailDonePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Models\Admin\Retail\RetailShipment;

class RetailFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function retail_done_payments_details_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $done_payment = RetailDonePayment::find($request->id);

        $shipper = $done_payment->shipper;


        $shipper_bank = $shipper;



//        $account_type_id = $shipper->account_type_id;

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Retail Payment Details</title>

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
                              <td class="text-center align-middle color primary"><strong>Retail Payment Details</strong></td>
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
                              <td>' . $shipper->shipper_name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Client Bank</strong></td>
                              <td>' . $shipper_bank->bank->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Account Number</strong></td>
                              <td>' . $shipper_bank->account_number . '</td>
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
        $total_adjustments = 0;
        $total_payable = 0;
        $total_wht = 0;
        $total_cod_sst = 0;
        foreach ($done_payment->done_payment_shipments as $done_payment_shipment) {
            $shipment = $done_payment_shipment->shipment;
            $rs = RetailShipment::where('shipment_id', $done_payment_shipment->shipment_id)->first();
            $shipment_weight = $shipment->actual_weight;

            if($done_payment_shipment->type != 2){
                $change_shipment_weight_log = ChangeShipmentWeightLog::where('shipment_id', $shipment->id);
                if($change_shipment_weight_log->exists()){
                    $change_shipment_weight_log = $change_shipment_weight_log->first();
                    $shipment_weight = $change_shipment_weight_log->old_weight;
                }
            }

            if ($done_payment_shipment->type == 0) {
                $type = 'Delivered';
            }
            else {
                $type = 'Adjusted';
            }
            $account_type_id = 1;
            $pickup_address = $shipment->pickup_address;

            $shipment_details .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $type . '</td>
                              <td>' . $pickup_address->city->name . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->retail->shipping_modes->name . '</td>
                              <td>' . $shipment->consignee_name . ' ' . $shipment->consignee_phone_number_1 . '</td>
                              <td>' . $shipment_weight   . '</td>
                              <td>' . $rs->wht . '</td>
                              <td>' . $rs->cod_sst . '</td>
                              <td>' . number_format($done_payment_shipment->amount) . '</td>
                              <td>' . (($done_payment_shipment->type == 1) ? number_format($done_payment_shipment->payable, 2) : '0') . '</td>
                            </tr>
            ';

            $serial_number++;

            if ($account_type_id == 1) {
                if ($done_payment_shipment->type != 1) {
                    if ($done_payment_shipment->charges != 0) {
                        if ($done_payment_shipment->type == 0) {
                            $total_collection_amount += $done_payment_shipment->amount;
                        }
                    }
                    else if ($done_payment_shipment->type == 0) {
                        $total_collection_amount += $done_payment_shipment->amount;
                    }
                }
                else {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
                $total_wht += $rs->wht;
                $total_cod_sst += $rs->cod_sst;
            }
            else {
                if ($done_payment_shipment->type == 0) {
                    $total_collection_amount += $done_payment_shipment->amount;
                }
                else if ($done_payment_shipment->type == 1) {
                    $total_adjustments += $done_payment_shipment->payable;
                }

                $total_payable += $done_payment_shipment->payable;
            }
        }

        $shipment_details .= '
                            <tr>
                                <td colspan=7></td>
                                <td class="color primary"><strong>Total</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_wht,2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_cod_sst,2) . '</strong></td>
                                <td class="color secondary"><strong>' . number_format($total_collection_amount) . '</strong></td>
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
                              <td>' . number_format(ROUND(($total_payable - $done_payment->ibft_charges), 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Type</strong></td>
                              <td class="color primary"><strong>Origin</strong></td>
                              <td class="color primary"><strong>Destination</strong></td>
                              <td class="color primary"><strong>Shipping Mode</strong></td>
                              <td class="color primary"><strong>Consignee</strong></td>
                              <td class="color primary"><strong>Weight (kg)</strong></td>
                              <td class="color primary"><strong>WHT</strong></td>
                              <td class="color primary"><strong>COD SST</strong></td>
                              <td class="color primary"><strong>Collection Amount (PKR)</strong></td>
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
                                        <td class="color secondary"><strong>Total Adjustments</strong></td>
                                        <td class="color secondary">' . number_format($total_adjustments, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>IBFT Charges</strong></td>
                                        <td>' . number_format($done_payment->ibft_charges, 2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>WHT</strong></td>
                                        <td>' .  number_format($total_wht,2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color secondary"><strong>COD SST</strong></td>
                                        <td>' .  number_format($total_cod_sst,2) . '</td>
                                    </tr>
                                    <tr>
                                        <td class="color primary"><strong>Overall Charges</strong></td>
                                        <td class="color secondary"><strong>' . number_format(($total_adjustments + $done_payment->ibft_charges + $total_wht + $total_cod_sst), 2) . '</strong></td>
                                    </tr>
                                  </tbody>
                                </table>
                                <span style="color: red">* 13% GST is applicable for Sindh Region 16% GST for Punjab & KPK</span>
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

    public function retail_done_payments_details(Request $request) {
        $done_payment = RetailDonePayment::find($request->id);
        dd($done_payment);

        $details = array();

        $details['reference_number'] = $done_payment->reference_number;
        $details['company_bank_id'] = $done_payment->company_bank_id;

        return $details;
    }
}
