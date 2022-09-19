<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\City;
use App\Http\Models\International\Wholesale\WholesaleInvoice;
use App\Http\Models\International\Wholesale\WholesaleInvoiceHistory;
use App\Http\Models\International\Wholesale\WholesaleInvoiceShipment;
use App\Http\Models\International\Wholesale\WholesaleShipment;
use App\Http\Models\International\Wholesale\WholesaleUser;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use NumberToWords\NumberToWords;
use Yajra\Datatables\Datatables;
use Auth;

class InternationalWholesaleInvoiceController extends Controller
{
    static private function amount_to_words($amount)
    {

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
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),598);
        return view('admin.international.wholesale.invoices.index');
    }
    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),599);
        }
        $invoices = WholesaleInvoice::join('wholesale_users as wu', 'wu.id', '=', 'wholesale_invoices.wholesale_user_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'wholesale_invoices.updated_by')
            ->select('wu.id as shipper_id', 'wu.name as shipper_name', 'wholesale_invoices.id as invoice_id', 'wholesale_invoices.invoice_number', 'ub.name as updated_by', 'wholesale_invoices.updated_at','wholesale_invoices.status', 'wholesale_invoices.total_courier_charges', 'wholesale_invoices.service_charges', 'wholesale_invoices.gst', 'wholesale_invoices.created_at');

        $datatable = Datatables::of($invoices)
            ->addColumn('shipper_id_padded', function ($invoice) {
                return str_pad($invoice->shipper_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('invoice_id_padded', function ($invoice) {
                return str_pad($invoice->invoice_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('invoice_status', function ($invoice) {
                if ($invoice->status == 1) {
                    return 'Pending';
                } else {
                    return 'Received';
                }
            })
            ->filterColumn('invoice_status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('wholesale_invoices.status', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || count(array_intersect([808, 809, 810, 811, 813], session('permissions'))) !== 0) {

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if ((session('role_id') == 1 || in_array(808, session('permissions'))) && ($result->status != 2)) {
                        $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit </div></button>';
                    }

                    if (session('role_id') == 1 || in_array(809, session('permissions'))) {

                        $dropdown .= '<button type="button" class="dropdown-item view_history"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-list"></i></div><div class="col-9 offset-1">View History </div></button>';

                    }

                    if (session('role_id') == 1 || in_array(810, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item general_print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Reimbursement Print </div></button>';
                    }

                    if (session('role_id') == 1 || in_array(811, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item consolidated_print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-printer"></i></div><div class="col-9 offset-1">Consolidated Service Commission Print </div></button>';
                    }

                    if ((session('role_id') == 1 || in_array(813, session('permissions'))) && ($result->status != 2)) {
                        $dropdown .= '<button type="button" class="dropdown-item resolved" data-target-id=' . $result->invoice_id . ' data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Mark as Received </div></button>';
                    }


                    $dropdown .= '
                        </div>
                      </div>
                    ';

                    return $dropdown;
                } else {
                    return '';
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable = $datatable->whereBetween('wholesale_users.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }
    //Invoice Status => 1 - Pending  => 2 - Received
    static public function generate_invoice($shipper_id, $shipment_ids){

        $user = WholesaleUser::find($shipper_id);

        $invoice_serial_number = 1;

        $invoice_number = 0;
        $count = WholesaleInvoice::select('id')->count();
        $invoice_number = 10000 + $count + 1 . '-' . $invoice_serial_number;

        $wholesale_invoice = new WholesaleInvoice();
        $wholesale_invoice->wholesale_user_id = $shipper_id;
        $wholesale_invoice->invoice_number = $invoice_number;
        $wholesale_invoice->total_courier_charges = 0;
        $wholesale_invoice->service_charges = 0;
        $wholesale_invoice->gst = 0;
        $wholesale_invoice->status = 1;
        $wholesale_invoice->save();
        $invoice_id = $wholesale_invoice->id;
        $wholesale_shipments = WholesaleShipment::whereIn('id', $shipment_ids)->get();

        $total_courier_charges = 0;
        $total_service_charges = 0;
        $total_gst = 0;

        foreach ($wholesale_shipments as $shipment){

            $invoice_shipment = new WholesaleInvoiceShipment();
            $invoice_shipment->wholesale_invoice_id = $invoice_id;
            $invoice_shipment->wholesale_shipment_id = $shipment->id;
            $invoice_shipment->save();

            $total_courier_charges += $shipment->bill_amount;
        }

        $margin = $user->margin;
        if($margin){
            $total_service_charges = ($total_courier_charges * $margin) / 100;
            $total_service_charges = ROUND($total_service_charges, 2, PHP_ROUND_HALF_DOWN);
        }

        $zone_id = City::find($user->city_id)->zone_id;

        $zone = Zone::find($zone_id);
        if ($zone) {
            $gst = $zone->gst;
        } else {
            $gst = 0.13;
        }

        $total_gst = $total_service_charges * $gst;
        $wholesale_invoice->total_courier_charges = $total_courier_charges;
        $wholesale_invoice->service_charges = $total_service_charges;
        $wholesale_invoice->gst = $total_gst;
        $wholesale_invoice->save();

    }

    static public function recalculate_invoice($shipment_id){
        $invoice_shipment = WholesaleInvoiceShipment::where('wholesale_shipment_id', $shipment_id);
        if($invoice_shipment->exists()){
            $invoice_shipment = $invoice_shipment->first();
            $invoice_id = $invoice_shipment->wholesale_invoice_id;
            $invoice = WholesaleInvoice::find($invoice_id);

            $invoice_shipments = $invoice->invoice_shipments;
            $total_courier_charges = 0;
            $total_service_charges = 0;
            $total_gst = 0;


            $invoice_history = new WholesaleInvoiceHistory();
            $invoice_history->invoice_id = $invoice->id;
            $invoice_history->wholesale_user_id = $invoice->wholesale_user_id;
            $invoice_history->invoice_number = $invoice->invoice_number;
            $invoice_history->total_courier_charges = $invoice->total_courier_charges;
            $invoice_history->service_charges = $invoice->service_charges;
            $invoice_history->gst = $invoice->gst;
            $invoice_history->status = $invoice->status;
            $invoice_history->updated_by = $invoice->updated_by;
            $invoice_history->save();


            foreach ($invoice_shipments as $shipment){
                $total_courier_charges += $shipment->bill_amount;
            }
            $invoice_user = $invoice->wholesale_user_id;
            $user = WholesaleUser::find($invoice_user);
            $margin = $user->margin;
            if($margin){
                $total_service_charges = ($total_courier_charges * $margin) / 100;
                $total_service_charges = ROUND($total_service_charges, 2, PHP_ROUND_HALF_DOWN);
            }


            $zone_id = City::find($user->city_id)->zone_id;

            $zone = Zone::find($zone_id);
            if ($zone) {
                $gst = $zone->gst;
            } else {
                $gst = 0.13;
            }
            $invoice_numbers = explode('-',$invoice->invoice_number);
            $invoice_serial = (int)$invoice_numbers[1] + 1;

            $new_invoice_number = $invoice_numbers[0] . '-' . $invoice_serial;
            $invoice->invoice_number = $new_invoice_number;
            $total_gst = $total_service_charges * $gst;
            $invoice->total_courier_charges = $total_courier_charges;
            $invoice->service_charges = $total_service_charges;
            $invoice->gst = $total_gst;
            $invoice->save();
        }
    }

    public function resolved(Request $request){
        $invoice_id = $request->invoice_id;

        $invoice = WholesaleInvoice::find($invoice_id);
        if($invoice){
            $invoice->status = 2;
            $invoice->updated_by = Auth::id();
            $invoice->save();

            $invoice_shipments = $invoice->invoice_shipments;
            foreach ($invoice_shipments as $invoice_shipment){
                $invoice_shipment->shipment->status_id = 4;
                $invoice_shipment->shipment->save();

            }

            return response()->json(['status' => 1, 'success' => 'Invoice resolved successfully!']);
        }
        return response()->json(['status' => 0, 'error' => 'Invoice not found, please refresh and try again!']);

    }

    public function edit_info(Request $request){
        $invoice_id = $request->invoice_id;
        if($invoice_id){
            $data = array();
            $invoice = WholesaleInvoice::find($invoice_id);
            if($invoice){

                $data['invoice_number'] = $invoice->invoice_number;
                $data['shipper_name'] = $invoice->shipper->name;
                $data['total_courier_charges'] = $invoice->total_courier_charges;
                $data['service_charges'] = $invoice->service_charges;
                $data['gst'] = $invoice->gst;

                return response()->json(['status' => 0, 'details' => $data]);

            }
            else{
                return response()->json(['status' => 1, 'error' => 'Invoice not found, please refresh and try again!']);
            }

        }
    }

    public function edit(Request $request){
        $invoice_id = $request->invoice_id;
        if($invoice_id){

            $invoice = WholesaleInvoice::find($invoice_id);
            if($invoice){

                $invoice_history = new WholesaleInvoiceHistory();
                $invoice_history->invoice_id = $invoice->id;
                $invoice_history->wholesale_user_id = $invoice->wholesale_user_id;
                $invoice_history->invoice_number = $invoice->invoice_number;
                $invoice_history->total_courier_charges = $invoice->total_courier_charges;
                $invoice_history->service_charges = $invoice->service_charges;
                $invoice_history->gst = $invoice->gst;
                $invoice_history->status = $invoice->status;
                $invoice_history->updated_by = $invoice->updated_by;
                $invoice_history->save();

                $invoice_numbers = explode('-',$invoice->invoice_number);
                $invoice_serial = (int)$invoice_numbers[1] + 1;

                $new_invoice_number = $invoice_numbers[0] . '-' . $invoice_serial;

                $invoice->invoice_number = $new_invoice_number;
                $invoice->total_courier_charges = $request->total_courier_charges;
                $invoice->service_charges = $request->service_charges;
                $invoice->gst = $request->gst;
                $invoice->updated_by = Auth::id();
                $invoice->save();

                return redirect()->back()->with('success', 'Invoice updated successfully!');

            }
            else{
                return redirect()->back()->with('error', 'Invoice not found!');
            }

        }
    }

    public function view_history(Request $request){
        $invoice_id = $request->invoice_id;

        if($invoice_id){
            $histories = WholesaleInvoiceHistory::where('invoice_id', $invoice_id)->get();

            $history_data = array();
            if(count($histories) > 0){
                foreach ($histories as $history){
                    $row = array();
                    $row['invoice_number'] = $history->invoice_number;
                    $row['shipper_name'] = $history->shipper->name;
                    $row['total_courier_charges'] = $history->total_courier_charges;
                    $row['service_charges'] = $history->service_charges;
                    $row['gst'] = $history->gst;
                    $row['status'] = ($history->status == 1)? 'Pending':'Received';
                    $row['updated_by'] = $history->admin->name;
                    $row['updated_at'] = Carbon::parse($history->created_at)->toDateTimeString();
                    $history_data[] = $row;
                }

                return response()->json(['status' => 0, 'details' => $history_data]);
            }
            else{
                return response()->json(['status' => 1, 'error' => 'History not found!']);
            }
        }
    }

    public function bulk_resolved(Request $request){
        $invoice_ids = $request->invoice_ids;
        foreach($invoice_ids as $invoice_id){
            $invoice = WholesaleInvoice::find($invoice_id);
            if($invoice){
                $invoice->status = 2;
                $invoice->updated_by = Auth::id();
                $invoice->save();

                $invoice_shipments = $invoice->invoice_shipments;
                foreach ($invoice_shipments as $invoice_shipment){
                    $invoice_shipment->shipment->status_id = 4;
                    $invoice_shipment->shipment->save();

                }
            }
        }
        return response()->json(['status' => 1, 'success' => 'Invoice(s) resolved successfully!']);
    }
    public function general_print(Request $request){

        $invoice_id = $request->invoice_id;
        if($invoice_id){
            $invoice = WholesaleInvoice::find($invoice_id);

            $invoice_shipments = $invoice->invoice_shipments;

            $html = '';


            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Wholesale General Invoice</title>
            ';


            $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';
            $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

            $html .= '
              </head>
              <body>
            ';

            $total_courier_charges = 0;

            $shipment_details = array();
            foreach($invoice_shipments as $invoice_shipment){
                $row = array();
                $shipment = $invoice_shipment->shipment;
                $row['booking_date'] = Carbon::parse($shipment->created_at)->format('Y-m-d');
                $row['dhl_waybill'] = $shipment->dhl_waybill;
                $row['weight'] = $shipment->weight;
                $row['type'] = $shipment->type;
                $row['destination'] = $shipment->destination_city->name;
                $row['courier_charges'] = $shipment->courier_charges;
                $row['other_charges'] = $shipment->other_charges;
                $row['bill_amount'] = $shipment->bill_amount;

                $total_courier_charges += $shipment->bill_amount;
                $shipment_details[] = $row;
            }


            $html .= '
                <div>
                  <div class="p-1">
        ';

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
                                    <td class="color primary" colspan="2"><strong>TRAX ONLINE PVT LTD</strong></td>
                                </tr>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>105, Sector 7A, Mehran Town, KIA.</strong></td>
                                </tr>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Karachi - Pakistan</strong></td>
                                </tr>
                                ';

            $html .= '<tr>
                                    <td class="color secondary"><strong>To</strong></td>
                                    <td>' . $invoice->shipper->name . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>NTN</strong></td>
                                    <td>' . $invoice->shipper->ntn . '</td>
                                </tr>
                                ';


            $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                           
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
            $html .= '          <tr>
                                    <td class="color primary"><strong>Date</strong></td>
                                    <td>' . Carbon::parse($invoice->created_at)->toFormattedDateString() . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>INV NO.</strong></td>
                                    <td>' . $invoice->invoice_number . '</td>
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
                            <th class="color secondary">S.#</th>
                            <th class="color secondary">Date</th>
                            <th class="color secondary">DHL AWB#</th>
                            <th class="color secondary">Weight</th>
                            <th class="color secondary">Type</th>
                            <th class="color secondary">Destination</th>
                            <th class="color secondary">Courier Charges (PKR)</th>
                            <th class="color secondary">Other Charges (PKR)</th>
                            <th class="color secondary">Bill Amount (PKR)</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

            $invoice_serial_number = 1;
            foreach ($shipment_details as $shipment_detail) {
                $html .= '
                        <tr>
                            <td>' . $invoice_serial_number . '</td>
                            <td>' . $shipment_detail['booking_date'] . '</td>
                            <td>' . $shipment_detail['dhl_waybill'] . '</td>
                            <td>' . $shipment_detail['weight'] . '</td>
                            <td>' . $shipment_detail['type'] . '</td>
                            <td>' . $shipment_detail['destination'] . '</td>
                            <td>' . number_format($shipment_detail['courier_charges'], 2) . '</td>
                            <td>' . number_format($shipment_detail['other_charges'], 2) . '</td>
                            <td>' . number_format($shipment_detail['bill_amount'], 2) . '</td>
                        </tr>
                ';

                $invoice_serial_number++;
            }

            $html .= '
                <tr>
                    <td colspan="9" class="color primary text-center">Summary</td>
                </tr>
                <tr>
                    <td colspan="7" class="color primary text-center">Total Courier Charges</td>
                    <td colspan="1" class="color primary text-center"></td>
                    <td colspan="1" class="color primary text-center">' . $total_courier_charges . '</td>
                </tr>
                <tr>
                  
                  <td colspan="9" class="color secondary text-center"><strong>Amount in Words : </strong> ' . self::amount_to_words($total_courier_charges) . ' Only</td>
                </tr>
            </tbody>
            </table>';

            $html .= '
                  </div>
                </div>
        ';


            $html .= '
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

    public function consolidated_print(Request $request){
        $invoice_id = $request->invoice_id;
        if($invoice_id){
            $invoice = WholesaleInvoice::find($invoice_id);
            $total_courier_charges = 0;
            $invoice_shipments = $invoice->invoice_shipments;
            $grand_total = $invoice->service_charges + $invoice->gst;
            $shipment_details = array();
            foreach($invoice_shipments as $invoice_shipment){

                $shipment = $invoice_shipment->shipment;
                $total_courier_charges += $shipment->bill_amount;
            }

            $html = '';


            $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                <title>Wholesale Service Invoice</title>
            ';


            $html .= '
                <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
        ';
            $html .= '
            <style>@page{size:A4 portrait; margin-top: 12rem; margin-bottom: 2rem; margin-left: 0rem; margin-right: 0rem;}*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important}body{background:none!important;color:#09262e!important;font-size:0.7rem!important}hr{border-top:1px dashed #000}table.table-bordered{page-break-inside:avoid}table.table-bordered thead tr th, table.table-bordered tbody tr td{border:1px solid #09262e!important}.color.primary{background:#c8c8c8!important}.color.secondary{background:#ebebeb!important}.border{border:1px solid #09262e!important}.summary{page-break-before:always;page-break-inside:avoid}.shipments_summary{page-break-before:always}</style>
        ';

            $html .= '
              </head>
              <body>
            ';





            $html .= '
                <div>
                  <div class="p-1">
        ';

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
                                    <td class="color primary" colspan="2"><strong>TRAX ONLINE PVT LTD</strong></td>
                                </tr>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>105, Sector 7A, Mehran Town, KIA.</strong></td>
                                </tr>
                                <tr>
                                    <td class="color primary" colspan="2"><strong>Karachi - Pakistan</strong></td>
                                </tr>
                                ';

            $html .= '<tr>
                                    <td class="color secondary"><strong>To</strong></td>
                                    <td>' . $invoice->shipper->name . '</td>
                                </tr>
                                <tr>
                                    <td class="color secondary"><strong>NTN</strong></td>
                                    <td>' . $invoice->shipper->ntn . '</td>
                                </tr>
                                ';


            $html .= '</tbody>
                            </table>
                        </div>

                        <div class="col-4">
                           
                            <table class="table table-sm table-bordered border">
                              <tbody> ';
            $html .= '          <tr>
                                    <td class="color primary"><strong>Date</strong></td>
                                    <td>' . Carbon::parse($invoice->created_at)->toFormattedDateString() . '</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>SNTN</strong></td>
                                    <td>S-7930679-5</td>
                                </tr>
                                <tr>
                                    <td class="color primary"><strong>S.T.INV NO.</strong></td>
                                    <td>' . $invoice->invoice_number . '</td>
                                </tr>';


            $html .= '
                               </tbody>
                            </table>
                        </div>
                    </div>
            ';


            $html .= '
                    <table class="table table-sm table-bordered border text-center">
                      <thead>
                       
                        <tr>
                            <th class="color secondary">S.#</th>
                            <th class="color secondary">Date</th>
                            <th class="color secondary">Invoice #</th>
                            <th class="color secondary">Service</th>
                            <th class="color secondary">Inclusive Amount</th>
                        </tr>
                      </thead>
                      <tbody>
        ';

            $invoice_serial_number = 1;

                $html .= '
                        <tr>
                            <td>' . $invoice_serial_number . '</td>
                            <td>' . Carbon::parse($invoice->created_at)->toFormattedDateString() . '</td>
                            <td>' . $invoice->invoice_number . '</td>
                            <td>DHL PAKISTAN (PRIVATE) LTD</td>
                            <td><strong>' . $total_courier_charges . '</strong></td>
                        </tr>';
            $html .= '
                        <tr">
                            <td colspan="3" style="height:50px; vertical-align: bottom;"></td>
                            <td  style="height:50px; vertical-align: bottom;"><strong>Service Charges on Invoice Amount</strong></td>
                            <td  style="height:50px; vertical-align: bottom;"><strong>' . number_format($invoice->service_charges, 2) . '</strong></td>
                        </tr>
                        <tr></tr>
                        <tr>
                            <td colspan="3"  style="height:50px;"></td>
                            <td  style="height:50px; vertical-align: bottom;"><strong>Sales Tax of Service Charges @ 13%</strong></td>
                            <td  style="height:50px; vertical-align: bottom;"><strong>' . number_format($invoice->gst, 2) . '</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td><strong>Grand Total:</strong></td>
                            <td><strong>' . number_format($grand_total, 2)  . '</strong></td>
                        </tr>
                ';




            $html .= '
                
                
                <tr>
                  <td colspan="5" class="color secondary text-center"><strong>Amount in Words : </strong> ' . self::amount_to_words($grand_total) . ' Only</td>
                </tr>
            </tbody>
            </table>';

            $html .= '
                  </div>
                </div>
        ';


            $html .= '
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
}
