<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Retail\OtherParcelReceiving;
use App\Http\Models\Admin\Retail\OtherParcelReceivingShipment;
use App\Http\Models\Admin\Retail\OtherRetailShipment;
use App\Http\Models\Admin\Retail\RetailParcelReceiving;
use App\Http\Models\Admin\Retail\RetailParcelReceivingShipment;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShippingMode;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class RetailParcelReceivingController extends Controller
{public function __construct()
    {
    $this->middleware('auth:retail');

//        $this->middleware('Permission');
}

    public function index()
    {
        $shipping_modes = RetailShippingMode::all();
        return view('retail.parcel_receiving.parcel_receiving')->with(['shipping_modes' => $shipping_modes]);
    }

    function list(Request $request) {
        $cash_deposit = RetailParcelReceiving::join('retail_users as ru', 'ru.id', '=', 'retail_parcel_receivings.retail_user_id')
            ->select('retail_parcel_receivings.id as performa_no', 'retail_parcel_receivings.category as category', 'ru.name as user', 'retail_parcel_receivings.total_cn as total_shipments', 'retail_parcel_receivings.total_cash as total_cash', DB::raw('DATE(retail_parcel_receivings.created_at) AS booking_date'), 'ru.id as employee_id')
            ->where('retail_parcel_receivings.retail_user_id', Auth::id());
        $datatable = Datatables::of($cash_deposit)
            ->addColumn('shipments_button', function ($data) {
                $retail_parcel_receiving_shipment = RetailParcelReceivingShipment::join('shipments as s','s.id','retail_parcel_receiving_shipments.shipment_id')
                ->where('retail_parcel_receiving_shipments.parcel_receiving_id',$data->performa_no)
                ->whereNotIn('s.shipper_status_id',[17,25])->count();
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $retail_parcel_receiving_shipment . '</button>';
            })
            ->editColumn('performa_no', function ($data) {
                return str_pad($data->performa_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('category', function ($data) {
                if ($data->category == 1) {
                    return 'Franchise';
                } else {
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
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $cash_deposit->whereBetween('retail_parcel_receivings.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function shipments(Request $request)
    {
        $parcel_receiving_id = $request->input('performa_no');
        $parcel_receiving_shipments = RetailParcelReceiving::find($parcel_receiving_id)->shipments()->get();
        $shipments = array();
        if ($parcel_receiving_shipments->count() != 0) {
            foreach ($parcel_receiving_shipments as $parcel_receiving_shipment) {
                $shipment = Shipment::find($parcel_receiving_shipment->shipment_id);
                if(!in_array($shipment->shipper_status_id,[17,25])){

                    $shipments[] = $shipment->tracking_number;
                }
            }
            return ['status' => 1, 'success' => 'Parcel Receiving Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Parcel Receiving Shipments', 'shipments' => false];
        }
    }

    public function generate(Request $request)
    {
        $retail_shipments = RetailShipment::where('retail_user_id', Auth::id())->where('parcel_receiving', 0);
        if ($retail_shipments->exists()) {
            $total_charges = 0;
            $total_cn = 0;
            $retail_shipments = $retail_shipments->get();
            $parcel_receiving = new RetailParcelReceiving();
            $parcel_receiving->category = Auth::user()->category;
            $parcel_receiving->retail_user_id = Auth::id();
            $parcel_receiving->total_cn = 0;
            $parcel_receiving->total_cash = 0;
            $parcel_receiving->save();

            foreach ($retail_shipments as $retail_shipment) {
                $total_cn++;
                $total_charges = $total_charges + $retail_shipment->total_charges;

                $parcel_receiving_shipment = new RetailParcelReceivingShipment();
                $parcel_receiving_shipment->parcel_receiving_id = $parcel_receiving->id;
                $parcel_receiving_shipment->shipment_id = $retail_shipment->shipment_id;
                $parcel_receiving_shipment->shipping_mode_id = $retail_shipment->shipping_mode;
                $parcel_receiving_shipment->save();

                $retail_shipment->parcel_receiving = 1;
                $retail_shipment->save();
            }

            $parcel_receiving->total_cn = $total_cn;
            $parcel_receiving->total_cash = $total_charges;
            $parcel_receiving->save();
            return response()->json(['status' => 0, 'success' => 'Parcel Receiving Sheet generated with Retail Note: ' . str_pad($parcel_receiving->id, 6, '0', STR_PAD_LEFT), 'parcel_receiving_id' => $parcel_receiving->id]);
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipments not found!']);
        }
    }

    function print(Request $request) {
        $parcel_receiving_id = $request->id;
        $parcel_receiving = RetailParcelReceiving::find($parcel_receiving_id);
        $parcel_receiving_shipments = $parcel_receiving->shipments;
        $parcel_receiving_total_cns = RetailParcelReceivingShipment::join('shipments as s','s.id','retail_parcel_receiving_shipments.shipment_id')
                ->where('retail_parcel_receiving_shipments.parcel_receiving_id',$parcel_receiving_id)
                ->whereNotIn('s.shipper_status_id',[17,25])->count();
        $parcel_receiving_total_cash = 0;
        
        $html = '<!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Parcel Receiving Performa</title>

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
                                    <h2>Parcel Receiving Performa</h2>
                                </div>
                            </div>
                            <div class="row justify-content-end mb-2">
                                <div class="col">
                                        <table class="table table-bordered border">
                                            <tbody>
                                            <tr><td class="color primary w-50">Retail Note</td><td class=" w-50">' . str_pad($parcel_receiving->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                                            <tr><td class="color primary w-50">Branch Name</td><td class=" w-50">' . $parcel_receiving->user->store->name . '</td></tr>
                                            <tr><td class="color primary w-50">Booking Code</td><td class=" w-50">' . str_pad($parcel_receiving->user->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                                            </tbody>
                                        </table>
                                </div>
                                <div class="col">
                                        <table class="table table-bordered border">
                                            <tbody>
                                            <tr><td class="color primary w-50">Staff</td><td class=" w-50">' . $parcel_receiving->user->name . '</td></tr>

                                            <tr><td class="color primary w-50">Code</td><td class=" w-50">' . $parcel_receiving->user->store->code . '</td></tr>
                                            <tr><td class="color primary w-50">Date</td><td class=" w-50">' . $parcel_receiving->created_at . '</td></tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                            <div class="mb-2">
                                <table class="table table-bordered border">
                                        <tbody>
                                            <tr>
                                                <td class="color primary"><b>Product</b></td>
                                                <td class="color primary"><b>CN. Number</b></td>
                                                <td class="color primary"><b>Booked At</b></td>
                                                <td class="color primary"><b>Cash</b></td>
                                            </tr>';

        foreach ($parcel_receiving_shipments as $parcel_receiving_shipment) {
            if(!in_array($parcel_receiving_shipment->shipment->shipper_status_id,[17,25])){

                $html .= '
                <tr>
                <td style="border-bottom: none !important;">' . $parcel_receiving_shipment->shipping_mode->name . '</td>
                <td>' . $parcel_receiving_shipment->shipment->tracking_number . '</td>
                <td>' . $parcel_receiving_shipment->shipment->created_at . '</td>
                <td>' . number_format(ROUND($parcel_receiving_shipment->retail_shipment->total_charges, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                </tr>';
                $parcel_receiving_total_cash += $parcel_receiving_shipment->retail_shipment->total_charges;
            }
        }

        $html .= '
                                            <tr>
                                                <td><b>Total</b></td>
                                                <td><b>' . $parcel_receiving_total_cns . '</b></td>
                                                <td></td>
                                                <td><b>' . number_format(ROUND($parcel_receiving_total_cash, 0, PHP_ROUND_HALF_DOWN)) . '</b></td>
                                            </tr>';

        $html .= '
                                        </tbody>
                                    </table>
                            </div>';

        $html .= '
                                <div class="col mt-5">
                                    <div class="row text-center">
                                        <div class="col-4 mt-3">
                                            <p>Staff Signature</p>
                                        </div>
                                        <div class="col mt-3">
                                            <p>Pickup Rider Sign and Code___________________________________________________</p>
                                        </div>
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

    public function other_parcel()
    {
        return view('retail.parcel_receiving.other_parcel');
    }

    public function other_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number)->where('shipper_status_id',1);
        if ($shipment->exists()) {
            
            $shipment = $shipment->first();
            if (!$shipment->retail) {

              if (!$shipment->other_retail) {

                    $details = array();
                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper_name'] = $shipment->user->name;
                    return ['status' => 0, 'success' => 'Shipment found!', 'details' => $details];

                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
                }

            }else{
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }

        }
        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function other_parcel_shipments(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);
        foreach ($shipment_ids as $key => $shipment_id) {
            $other_parcel_receiving_shipment = new OtherRetailShipment();
            $other_parcel_receiving_shipment->shipment_id = $shipment_id;
            $other_parcel_receiving_shipment->retail_user_id = Auth::id();
            $other_parcel_receiving_shipment->parcel_receiving = 0;
            $other_parcel_receiving_shipment->save();
        }
        return redirect()->back()->with(['success' => 'Other Parcel Received ']);

    }

    public function other_index()
    {
        return view('retail.parcel_receiving.other_parcel_receiving');

    }

    public function other_list(Request $request)
    {
        $other_parcel_list = OtherParcelReceiving::join('retail_users as ru', 'ru.id', '=', 'other_parcel_receivings.retail_user_id')
            ->select('other_parcel_receivings.id as performa_no', 'other_parcel_receivings.category as category', 'ru.name as user', 'other_parcel_receivings.total_cn as total_shipments', DB::raw('DATE(other_parcel_receivings.created_at) AS booking_date'), 'ru.id as employee_id')
            ->where('other_parcel_receivings.retail_user_id', Auth::id());
        $datatable = Datatables::of($other_parcel_list)
            ->addColumn('shipments_button', function ($data) {
                $other_parcel_receiving_shipment = OtherParcelReceivingShipment::join('shipments as s','s.id','other_parcel_receiving_shipments.shipment_id')
                ->where('other_parcel_receiving_shipments.other_parcel_receiving_id',$data->performa_no)
                ->whereNotIn('s.shipper_status_id',[17,25])->count();
                
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $other_parcel_receiving_shipment . '</button>';
            })
            ->editColumn('performa_no', function ($data) {
                return str_pad($data->performa_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('category', function ($data) {
                if ($data->category == 1) {
                    return 'Franchise';
                } else {
                    return 'Trax Center';
                }
            })
            ->addColumn('booking_code', function ($data) {
                return str_pad($data->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('performa_button', function ($data) {
                return '<button class="btn btn-sm btn-outline-info align-middle">' . str_pad($data->performa_no, 6, '0', STR_PAD_LEFT) . '</button>';
            });

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $other_parcel_list->whereBetween('other_parcel_receivings.created_at', [$from, $to]);
        }
        return $datatable->make(true);
    }

    public function other_generate()
    {
        $other_retail_shipments = OtherRetailShipment::where('retail_user_id', Auth::id())->where('parcel_receiving', 0);
        if ($other_retail_shipments->exists()) {
            $total_cn = 0;
            $other_retail_shipments = $other_retail_shipments->get();
            $other_parcel_receiving = new OtherParcelReceiving();
            $other_parcel_receiving->category = Auth::user()->category;
            $other_parcel_receiving->retail_user_id = Auth::id();
            $other_parcel_receiving->total_cn = 0;
            $other_parcel_receiving->save();

            foreach ($other_retail_shipments as $other_retail_shipment) {
                $total_cn++;
                $other_parcel_receiving_shipment = new OtherParcelReceivingShipment();
                $other_parcel_receiving_shipment->other_parcel_receiving_id = $other_parcel_receiving->id;
                $other_parcel_receiving_shipment->shipment_id = $other_retail_shipment->shipment_id;
                $other_parcel_receiving_shipment->save();

                $other_retail_shipment->parcel_receiving = 1;
                $other_retail_shipment->save();
            }

            $other_parcel_receiving->total_cn = $total_cn;
            $other_parcel_receiving->save();
            return response()->json(['status' => 0, 'success' => 'Other Parcel Receiving Sheet generated with Retail Note: ' . str_pad($other_parcel_receiving->id, 6, '0', STR_PAD_LEFT), 'other_parcel_receiving_id' => $other_parcel_receiving->id]);
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipments not found!']);
        }
    }

    public function other_shipments(Request $request)
    {
        $other_parcel_receiving_id = $request->input('performa_no');
        $parcel_receiving_shipments = OtherParcelReceiving::find($other_parcel_receiving_id)->shipments()->get();
        $shipments = array();
        if ($parcel_receiving_shipments->count() != 0) {
            foreach ($parcel_receiving_shipments as $parcel_receiving_shipment) {
                $shipment = Shipment::find($parcel_receiving_shipment->shipment_id);
                if(!in_array($shipment->shipper_status_id,[17,25])){

                    $shipments[] = $shipment->tracking_number;
                }
            }
            return ['status' => 1, 'success' => 'Other Parcel Receiving Shipments', 'shipments' => $shipments];
        } else {
            return ['status' => 0, 'success' => 'No Parcel Receiving Shipments', 'shipments' => false];
        }
    }

    public function other_print(Request $request)
    {
        $other_parcel_receiving_id = $request->id;
        $other_parcel_receiving = OtherParcelReceiving::find($other_parcel_receiving_id);
        $other_parcel_receiving_shipments = $other_parcel_receiving->shipments;
        
        $other_receiving_total_cns = OtherParcelReceivingShipment::join('shipments as s','s.id','other_parcel_receiving_shipments.shipment_id')
        ->where('other_parcel_receiving_shipments.other_parcel_receiving_id',$other_parcel_receiving_id)
        ->whereNotIn('s.shipper_status_id',[17,25])->count();

        $html = '<!doctype html>
              <html lang="en">
                <head>
                  <meta charset="utf-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                  <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                  <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                  <title>Other Parcel Receiving Performa</title>

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
                                  <h2>Other Parcel Receiving Performa</h2>
                              </div>
                          </div>
                          <div class="row justify-content-end mb-2">
                              <div class="col">
                                      <table class="table table-bordered border">
                                          <tbody>
                                          <tr><td class="color primary w-50">Retail Note</td><td class=" w-50">' . str_pad($other_parcel_receiving->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                                          <tr><td class="color primary w-50">Branch Name</td><td class=" w-50">' . $other_parcel_receiving->user->store->name . '</td></tr>
                                          <tr><td class="color primary w-50">Booking Code</td><td class=" w-50">' . str_pad($other_parcel_receiving->user->id, 6, '0', STR_PAD_LEFT) . '</td></tr>
                                          </tbody>
                                      </table>
                              </div>
                              <div class="col">
                                      <table class="table table-bordered border">
                                          <tbody>
                                          <tr><td class="color primary w-50">Staff</td><td class=" w-50">' . $other_parcel_receiving->user->name . '</td></tr>

                                          <tr><td class="color primary w-50">Code</td><td class=" w-50">' . $other_parcel_receiving->user->store->code . '</td></tr>
                                          <tr><td class="color primary w-50">Date</td><td class=" w-50">' . $other_parcel_receiving->created_at . '</td></tr>
                                          </tbody>
                                      </table>
                              </div>
                          </div>
                          <div class="mb-2">
                              <table class="table table-bordered border">
                                      <tbody>
                                          <tr>
                                              <td class="color primary"><b>Booked At</b></td>
                                              <td class="color primary"><b>Shipper Name</b></td>
                                              <td class="color primary"><b>CN. Number</b></td>
                                          </tr>';

        foreach ($other_parcel_receiving_shipments as $other_parcel_receiving_shipment) {
            $html .= '
                                          <tr>
                                          <td>' . $other_parcel_receiving_shipment->shipment->created_at . '</td>
                                          <td>' . $other_parcel_receiving_shipment->shipment->user->name . '</td>
                                              <td>' . $other_parcel_receiving_shipment->shipment->tracking_number . '</td>
                                          </tr>';
        }

        $html .= '
                                          <tr>
                                              <td><b>Total</b></td>
                                              <td><b>' . $other_receiving_total_cns . '</b></td>
                                          </tr>';

        $html .= '
                                      </tbody>
                                  </table>
                          </div>';

        $html .= '
                              <div class="col mt-5">
                                  <div class="row text-center">
                                      <div class="col-4 mt-3">
                                          <p>Staff Signature</p>
                                      </div>
                                      <div class="col mt-3">
                                          <p>Pickup Rider Sign and Code___________________________________________________</p>
                                      </div>
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
