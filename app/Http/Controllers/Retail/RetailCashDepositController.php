<?php

namespace App\Http\Controllers\Retail;

use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class RetailCashDepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function index(){
        $shipping_modes = RetailShippingMode::all();
        return view('retail.cash_deposit.cash_deposit')->with(['shipping_modes' => $shipping_modes]);
    }

    public function list(Request $request){
        $cash_deposit = RetailCashDeposit::join('retail_users as ru', 'ru.id', '=', 'retail_cash_deposits.retail_user_id')
            ->select('retail_cash_deposits.id as performa_no', 'retail_cash_deposits.category as category', 'ru.name as user', 'retail_cash_deposits.total_cn as total_shipments', 'retail_cash_deposits.total_cash as total_cash', DB::raw('DATE(retail_cash_deposits.created_at) AS booking_date'), 'ru.id as employee_id', 'retail_cash_deposits.status as status')
        ->where('retail_cash_deposits.retail_user_id', Auth::id());
        $datatable = Datatables::of($cash_deposit)
            ->addColumn('shipments_button', function ($data) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $data->total_shipments . '</button>';
            })
            ->editColumn('performa_no', function ($data) {
                return str_pad($data->performa_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('category', function ($data) {
                if($data->category == 1){
                    return 'Franchise';
                }
                else{
                    return 'Trax Center';
                }
            })
            ->editColumn('total_cash', function ($data) {
                return number_format(ROUND($data->total_cash, 0, PHP_ROUND_HALF_DOWN));
            })
            ->addColumn('booking_code', function ($data) {
                return str_pad($data->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('performa_button', function ($data) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . str_pad($data->performa_no, 6, '0', STR_PAD_LEFT) . '</button>';
            })
            ->editColumn('status', function ($data) {
                if($data->status == 0){
                    return 'Pending';
                }elseif($data->status == 1){
                    return 'Cash Collected';
                }else{
                    return 'Deposited';
                }
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $cash_deposit->whereBetween('retail_cash_deposits.created_at', [$from,$to]);
        }
        return  $datatable->make(true);
    }

    public function shipments(Request $request){
        $cash_deposit_id = $request->input('performa_no');
        $cash_deposit_shipments = RetailCashDeposit::find($cash_deposit_id)->shipments()->get();
        $shipments = array();
        if($cash_deposit_shipments->count() != 0){
            foreach ($cash_deposit_shipments as $cash_deposit_shipment){
                $shipment = Shipment::find($cash_deposit_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 1, 'success' => 'Cash Deposit Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Cash Deposit Shipments', 'shipments' => FALSE];
        }
    }

    public function print(Request $request){
        $cash_deposit_id = $request->id;
        $cash_deposit = RetailCashDeposit::find($cash_deposit_id);
        $cash_deposit_shipments = $cash_deposit->shipments;
        $shipping_mode_data = array();
        foreach ($cash_deposit_shipments as $cash_deposit_shipment){
            if(array_key_exists($cash_deposit_shipment->shipping_mode_id, $shipping_mode_data)){
                $shipping_mode_data[$cash_deposit_shipment->shipping_mode_id]['total_cn']++;
                $shipping_mode_data[$cash_deposit_shipment->shipping_mode_id]['total_cash'] = $shipping_mode_data[$cash_deposit_shipment->shipping_mode_id]['total_cash'] + $cash_deposit_shipment->retail_shipment->total_charges;
            }
            else{
                $shipping_mode_data[$cash_deposit_shipment->shipping_mode_id]['name'] = $cash_deposit_shipment->shipping_mode->name;
                $shipping_mode_data[$cash_deposit_shipment->shipping_mode_id]['total_cn'] = 1;
                $shipping_mode_data[$cash_deposit_shipment->shipping_mode_id]['total_cash'] = $cash_deposit_shipment->retail_shipment->total_charges;
            }
        }
        $html = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Digital Sales Performa</title>

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
                    width: 12.5% !important;
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

                  .border.twice {
                    border-width: 2px !important;
                  }

                  .border.twice-top {
                    border-top-width: 2px !important;
                  }

                  .border.twice-bottom {
                    border-bottom-width: 2px !important;
                  }

                  .border.twice-left {
                    border-left-width: 2px !important;
                  }

                  .border.twice-right {
                    border-right-width: 2px !important;
                  }

                  td.replacement span {
                    width: 22px;
                  }

                  td.replacement span img {
                    display: block;
                    width: 100%;
                    margin: auto;
                    background: #c8c8c8;
                    border-radius: 25px;
                  }

                  .void {
                    top: 0;
                    bottom: 0;
                    right: 0;
                    left: 0;
                    height: 80px;
                    font-size: 5rem;
                    line-height: 3.5rem;
                    opacity: 0.25;
                  }
                   div.page
                    {
                        page-break-after: always;
                        page-break-inside: avoid;
                    }
                    .piece_number{
                        font-size: 2.5rem;
                    }
                </style>
                  </head>
                  <body>
                    <div>';

            $html .= '<div class="container-fluid text-center p-3">
                            <div class="row justify-content-end mb-2">
                                <div class="col">
                                    <img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">
                                </div>
                                <div class="col">
                                    <h2>Digital Sales Performa</h2>
                                </div>
                            </div>
                            <div class="row justify-content-end mb-2">
                                <div class="col">
                                        <table class="table table-bordered border">
                                            <tbody>
                                            <tr><td class="color primary w-50">Performa No.</td><td class=" w-50">'. str_pad($cash_deposit->id, 6, '0', STR_PAD_LEFT) .'</td></tr>
                                            <tr><td class="color primary w-50">Branch Name</td><td class=" w-50">'. $cash_deposit->user->store->name .'</td></tr>
                                            <tr><td class="color primary w-50">Staff</td><td class=" w-50">'. $cash_deposit->user->name .'</td></tr>
                                            <tr><td class="color primary w-50">Booking Code</td><td class=" w-50">'. str_pad($cash_deposit->user->id, 6, '0', STR_PAD_LEFT) .'</td></tr>
                                            </tbody>
                                        </table>
                                </div>
                                <div class="col">
                                        <table class="table table-sm table-bordered border">
                                            <tbody>
                                            <tr><td class="color primary w-50">Code</td><td class=" w-50">'. $cash_deposit->user->store->code .'</td></tr>
                                            <tr><td class="color primary w-50">Date</td><td class=" w-50">'. $cash_deposit->created_at .'</td></tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                            <div class="col border mb-2">
                                    <div class="row justify-content-center m-1">
                                        <h5><b>SALES SUMMARY</b></h5>
                                    </div>
                                    <table class="table table-bordered border">
                                        <tbody>
                                            <tr>
                                                <td class="color primary"><b>Product</b></td>
                                                <td class="color primary"><b>CN. Numbers Used</b></td>
                                                <td class="color primary"><b>Cash</b></td>
                                            </tr>';

                                    foreach ($shipping_mode_data as $data){
                                        $html .= '
                                            <tr>
                                                <td style="border-bottom: none !important;">' . $data['name'] . '</td>
                                                <td>' . $data['total_cn'] . '</td>
                                                <td>' . number_format(ROUND($data['total_cash'], 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                            </tr>';
                                    }

            $html .= '
                                            <tr>
                                                <td><b>Total</b></td>
                                                <td><b>' . $cash_deposit->total_cn . '</b></td>
                                                <td><b>' . number_format(ROUND($cash_deposit->total_cash, 0, PHP_ROUND_HALF_DOWN)) . '</b></td>
                                            </tr>';

            $html .= '
                                        </tbody>
                                    </table>
                                    <div class="m-1">
                                        <p><b>IT IS CERTIFIED THAT THE MENTIONED CASH COLLECTION OF PKR ' . number_format(ROUND($cash_deposit->total_cash, 0, PHP_ROUND_HALF_DOWN)) . ' HAS BEEN MADE.</b></p>
                                    </div>
                            </div>';

            $html .= '
                                <div class="col border">
                                    <div class="row justify-content-start p-2">
                                        <p>Collection Staff Name and Salary Code_____________________________________</p>
                                    </div>
                                    <div class="row justify-content-start p-2">
                                        <p>Collection Staff Signature___________________________________________________</p>
                                    </div>
                                    <div class="row justify-content-end p-2">
                                        <p>Cashier Signature_________________________________________________</p>
                                    </div>
                                    <div class="row justify-content-end p-2">
                                        <p>Cashier Name__________________________________________________</p>
                                    </div>
                                </div>
                            </div>';
            $html .= '
                <script>
                  window.onload = function() {
                    window.print();
                  }
                </script>
                ';

            $html .= '
                  </body>
                </html>
            ';
        return $html;
    }
}
