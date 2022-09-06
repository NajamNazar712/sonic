<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\BookingType;
use App\Http\Models\CityDelivery;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\RateStatus;
use App\Http\Models\ShipmentInformationLog;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\Shipper\ShipperReceivingSheetSetting;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\ShippingMode;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use App\Http\Models\SubstituteUserReceivingSheet;
use App\Http\Models\SubstituteUserShipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;

use App\Http\Models\Shipment;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\ReceivingSheetShipment;
use App\Http\Models\GulAhmedPickupAddress;


use Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

use App\Jobs\ProcessGulAhmedShipmentConfirmation;

class ShipperReceivingSheetController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function index() {
        $service_type = BookingType::all();
        return view('client.shipment.receiving_sheet.index')->with('service_type',$service_type);
    }

    static public function create($shipment_ids, $user_id) {
        $pickup_address_id = 0;

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);

            if ($shipment->shipper_status_id != 1) {
                unset($shipment_ids[$key]);

                // return ['status' => 1, 'error' => $shipment->tracking_number . ' can no longer be added to a Receiving Sheet'];
            }
            else {
                if ($shipment->user_id != $user_id) {
                    return ['status' => 1, 'error' => $shipment->tracking_number . ' doesn\'t belong to you'];
                }

                if (ReceivingSheetShipment::where('shipment_id', $shipment_id)->exists()) {
                    unset($shipment_ids[$key]);

                    // return ['status' => 1, 'error' => $shipment->tracking_number . ' is already in a Receiving Sheet'];
                }

                if ($pickup_address_id == 0) {
                    $pickup_address_id = $shipment->pickup_address_id;
                }
                else {
                    if ($pickup_address_id != $shipment->pickup_address_id) {
                        return ['status' => 1, 'error' => 'Given Shipments Pickup Addresses are different from one another and cannot be added to the same Receiving Sheet'];
                    }
                }
            }
        }

        if (!count($shipment_ids)) {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }

        $receiving_sheet = new ReceivingSheet();

        $receiving_sheet->user_id = $user_id;
        $receiving_sheet->pickup_address_id = $pickup_address_id;
        $receiving_sheet->booked = count($shipment_ids);
        $receiving_sheet->status = 0;

        $receiving_sheet->save();

        if(session('user_type') == 2){
            $substitute_user_receiving_sheet = new SubstituteUserReceivingSheet();
            $substitute_user_receiving_sheet->substitute_user_id = Auth::id();
            $substitute_user_receiving_sheet->receiving_sheet_id = $receiving_sheet->id;
            $substitute_user_receiving_sheet->save();
        }

        $receiving_sheet_id = $receiving_sheet->id;

        foreach ($shipment_ids as $shipment_id) {
            $receiving_sheet_shipment = new ReceivingSheetShipment();

            $receiving_sheet_shipment->shipment_id = $shipment_id;
            $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;

            $receiving_sheet_shipment->save();
        }

        if ($user_id == 7828) {
            $confirmation_datetime = Carbon::now()->toDateTimeString();

            $confirmation_shipments = array();

            foreach ($shipment_ids as $shipment_id) {
                $shipment = Shipment::find($shipment_id);

                $confirmation_shipment = array();

                $confirmation_shipment['CNN'] = $shipment->tracking_number;
                $confirmation_shipment['reference_number'] = $shipment->order_id;
                $confirmation_shipment['ConfirmationDateTime'] = $confirmation_datetime;

                $confirmation_shipments[] = $confirmation_shipment;
            }

            dispatch(new ProcessGulAhmedShipmentConfirmation($confirmation_shipments));
        }

        return ['status' => 0, 'success' => 'Receiving Sheet has been Created', 'receiving_sheet_id' => $receiving_sheet_id];
    }

    public function store(Request $request) {
        if (!empty($request->input('shipment_ids'))) {
            return $this->create($request->input('shipment_ids'), session('user_id'));
        }
        else {
            return ['status' => 1, 'error' => 'No Shipment Selected'];
        }
    }

    public function list() {
        $shipments = Shipment::join('booking_types AS bt', 'shipments.booking_type_id', '=', 'bt.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('receiving_sheet_shipments as rss', 'shipments.id', '=', 'rss.shipment_id')
            ->leftjoin('receiving_sheets AS rs', 'rss.receiving_sheet_id', '=', 'rs.id')
            ->leftjoin('users as u', 'shipments.user_id', '=', 'u.id')
            ->select('shipments.id', 'shipments.tracking_number', 'shipments.order_id', 'bt.booking_type AS service_type', 'usi.pickup_address', 'oc.name AS origin_city', 'dc.name AS destination_city', 'shipments.created_at AS booking_date', 'rs.id AS receiving_sheet', 'rs.id AS receiving_sheet_no','shipments.amount', 'u.name as user', 'u.id as user_id', 'shipments.consignee_name as consignee_name', 'shipments.consignee_phone_number_1 as consignee_phone_no', 'shipments.consignee_address as address', 'shipments.shipper_status_id as status_id', 'shipments.special_instructions as special_instructions', 'oc.id as origin_id', 'dc.id as destination_id')
            ->where('shipments.shipper_status_id', 1)
            ->where('shipments.packaging_material_request', 0)
            ->where(function ($query) {
                $query->whereNull('rs.status')->orWhere('rs.status', 0);
            })
            ->where(function ($query) {
                $query->where('shipments.user_id', session('user_id'))
                ->orwhereIn('shipments.user_id', session('sister_users'));
            });

            if(session('user_type') == 2){
                if(session('restriction') == 1){
                    $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                }
            }

        return Datatables::of($shipments)
            ->editColumn('receiving_sheet', function($shipment) {
                if ($shipment->receiving_sheet) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle id">' . str_pad($shipment->receiving_sheet, 6, "0", STR_PAD_LEFT) . '</span></button>';
                }
                else {
                    return '';
                }
            })
            ->editColumn('amount', function($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('action', function($shipment) {

                if($shipment->user_id == session('user_id')){
                    $dropdown = '
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
            ';

                    if ($shipment->receiving_sheet) {
                        $dropdown .= '<button type="button" class="dropdown-item void"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Void</div></button>';
                    }
                    else {
                        $dropdown .= '<button type="button" class="dropdown-item add"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Add</div></button>';
                        $dropdown .= '<button type="button" class="dropdown-item edit_cn"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Edit CN</div></button>';
                    }

                    $dropdown .= '
                    </div>
                </div>
            ';
                }
                else{
                    $dropdown = '';
                }


                return $dropdown;
            })
            ->filterColumn('receiving_sheet', function($query, $keyword) {
                $keyword = intval($keyword);

                if ($keyword != 0) {
                    $query->where('rs.id', '=', $keyword);
                }
                else {
                    $query->whereNotNull('rs.id');
                }
            })
            ->make(true);
    }

    public function all() {
        return ReceivingSheet::where('user_id', session('user_id'))->where('status', 0)->select('id')->get();
    }

    public function add(Request $request) {
        $shipment = Shipment::find($request->input('shipment_id'));

        if ($shipment->shipper_status_id != 1) {
            return ['status' => 1, 'error' => $shipment->tracking_number . ' can no longer be added to a Receiving Sheet'];
        }

        if ($shipment->user_id == session('user_id')) {
            $receiving_sheet = ReceivingSheet::find($request->input('receiving_sheet_id'));

            if ($receiving_sheet->user_id == session('user_id')) {
                if ($receiving_sheet->status == 0) {
                    $first_receiving_sheet_shipment = ReceivingSheetShipment::where('receiving_sheet_id', $request->input('receiving_sheet_id'))->first();

                    if ($first_receiving_sheet_shipment) {
                        $first_shipment = Shipment::find($first_receiving_sheet_shipment->shipment_id);

                        if ($shipment->pickup_address_id == $first_shipment->pickup_address_id) {
                            $receiving_sheet_shipment = new ReceivingSheetShipment();

                            $receiving_sheet_shipment->shipment_id = $request->input('shipment_id');
                            $receiving_sheet_shipment->receiving_sheet_id = $request->input('receiving_sheet_id');

                            $receiving_sheet_shipment->save();

                            $receiving_sheet->booked = $receiving_sheet->booked + 1;

                            $receiving_sheet->save();

                            if ($shipment->user_id == 7828) {
                                $confirmation_datetime = Carbon::now()->toDateTimeString();

                                $confirmation_shipments = array();

                                $confirmation_shipment = array();

                                $confirmation_shipment['CNN'] = $shipment->tracking_number;
                                $confirmation_shipment['reference_number'] = $shipment->order_id;
                                $confirmation_shipment['ConfirmationDateTime'] = $confirmation_datetime;

                                $confirmation_shipments[] = $confirmation_shipment;

                                dispatch(new ProcessGulAhmedShipmentConfirmation($confirmation_shipments));
                            }

                            return ['status' => 0, 'success' => 'Shipment has been Added to the Receiving Sheet'];
                        }
                        else {
                            return ['status' => 1, 'error' => 'Given Shipment\'s Pickup Address is different from the other Shipments of the selected Receiving Sheet'];
                        }
                    }
                    else {
                        $receiving_sheet_shipment = new ReceivingSheetShipment();

                        $receiving_sheet_shipment->shipment_id = $request->input('shipment_id');
                        $receiving_sheet_shipment->receiving_sheet_id = $request->input('receiving_sheet_id');

                        $receiving_sheet_shipment->save();

                        $receiving_sheet->booked = $receiving_sheet->booked + 1;

                        $receiving_sheet->save();

                        if ($shipment->user_id == 7828) {
                            $confirmation_datetime = Carbon::now()->toDateTimeString();

                            $confirmation_shipments = array();

                            $confirmation_shipment = array();

                            $confirmation_shipment['CNN'] = $shipment->tracking_number;
                            $confirmation_shipment['reference_number'] = $shipment->order_id;
                            $confirmation_shipment['ConfirmationDateTime'] = $confirmation_datetime;

                            $confirmation_shipments[] = $confirmation_shipment;

                            dispatch(new ProcessGulAhmedShipmentConfirmation($confirmation_shipments));
                        }

                        return ['status' => 0, 'success' => 'Shipment has been Added to the Receiving Sheet'];
                    }
                }
                else {
                    return ['status' => 1, 'error' => 'This Shipment is already Received by Trax'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'This Receving Sheet doesn\'t belong to you'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'This Shipment doesn\'t belong to you'];
        }
    }

    public function void(Request $request) {
        $shipment = Shipment::find($request->input('shipment_id'));

        if ($shipment->user_id == session('user_id')) {
            $receiving_sheet_shipment = ReceivingSheetShipment::find($request->input('shipment_id'));

            if ($receiving_sheet_shipment->receiving_sheet_id == $request->input('receiving_sheet_id')) {
                $receiving_sheet = ReceivingSheet::find($request->input('receiving_sheet_id'));

                if ($receiving_sheet->status == 0) {
                    $receiving_sheet_shipment->delete();

                    if (!ReceivingSheetShipment::where('receiving_sheet_id', $request->input('receiving_sheet_id'))->exists()) {
                        $receiving_sheet->booked = $receiving_sheet->booked - 1;
                        $receiving_sheet->status = 2;

                        $receiving_sheet->save();
                    }

                    return ['status' => 0, 'success' => 'Shipment has been Voided'];
                }
                else {
                    return ['status' => 1, 'error' => 'This Shipment is already Received by Trax'];
                }
            }
            else {
                return ['status' => 1, 'error' => 'Shipment isn\'t part of the given Receiving Sheet'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'This Shipment doesn\'t belong to you'];
        }
    }

    static public function view($id, $user_type, $body_only = FALSE) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '';

        if (!$body_only) {
            $html .= '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            ';

            if ($user_type != 4) {
                $html .= '
                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                ';
            }
            else {
                $html .= '
                    <style>' . file_get_contents(public_path('app-assets/css/bootstrap.min.css')) . '</style>
                ';
            }

            $html .= '
                    <title>Receiving Sheet</title>

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
                        font-size: 0.7rem !important;
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
                    <div class="p-1">
            ';
        }

        $receiving_sheet_shipments = ReceivingSheetShipment::where('receiving_sheet_id', $id);

        if ($receiving_sheet_shipments->exists()) {
            $total_shipments = 0;
            $total_cod = 0;
            $item_description_flag = TRUE;
            $setting = ShipperReceivingSheetSetting::where('user_id', session('user_id'));
            if($setting->exists()){
                $setting = $setting->first();
                $item_description_flag = $setting->item_description;
            }

            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Product Type</strong></td>
                            <td class="color primary"><strong>Booking Date</strong></td>';
            if($item_description_flag){
                $shipment_details .= '<td class="color primary"><strong>Description</strong></td>';
            }
            $shipment_details .=
                '<td class="color primary"><strong>Quantity</strong></td>
                            <td class="color primary"><strong>Destination</strong></td>
                            <td class="color primary"><strong>Estimated Weight</strong></td>
                            ';
            if(in_array(session('user_id'),[6693,12412])){
                $shipment_details .=
                    ' <td class="color primary"><strong>Actual Weight</strong></td> ';
            }
            
            $shipment_details .= '<td class="color primary"><strong>Pieces</strong></td>
                            <td class="color primary"><strong>Amount</strong></td>
                          </tr>';

            foreach ($receiving_sheet_shipments->orderBy('shipment_id')->get() as $receiving_sheet_shipment) {
                $total_shipments++;
                $shipment_details_row_start = '';
                $shipment = Shipment::find($receiving_sheet_shipment->shipment_id);
                if($shipment->shipper_status_id != 17){
                    if ($shipment->booking_type_id != 3) {
                        $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->order_id . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                    ';

                        $shipment_details_row_end = '
                            <td>' . $shipment->consignee_city->name . '</td>
                            <td>' . $shipment->estimated_weight . '</td> ';
                        if(in_array(session('user_id'),[6693,12412])) {
                            $shipment_details_row_end .= '<td>' . $shipment->actual_weight . '</td> ';
                        }
                        $shipment_details_row_end .= '<td>' . $shipment->pieces . '</td>
                            <td>Rs ' . number_format($shipment->amount) . '</td>
                          </tr>';
                    }
                    else {
                        $number_of_items = $shipment->items->count();

                        $shipment_details_row_start = '
                          <tr>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $total_shipments . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->tracking_number . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->order_id . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                    ';

                        $shipment_details_row_end = '
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . $shipment->consignee_city->name . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">' . number_format($shipment->estimated_weight) . '</td>
                            <td>' . $shipment->pieces . '</td>
                            <td rowspan=' . $number_of_items . ' class="align-middle">Rs ' . number_format($shipment->amount) . '</td>
                          </tr>
                    ';
                    }

                    if ($shipment->booking_type_id == 1) {
                        $shipment_details .= $shipment_details_row_start;

                        $item = $shipment->items->first();

                        $shipment_details .= '
                                <td>' . $item->product->product_name . '</td>
                                <td>' . $item->created_at . '</td>';
                        if($item_description_flag){
                            $shipment_details .= '<td>' . $item->description . '</td>';
                        }
                        $shipment_details .='
                                 <td>' . $item->quantity . '</td>
                        ';

                        $shipment_details .= $shipment_details_row_end;
                    }
                    else if ($shipment->booking_type_id == 2) {
                        $shipment_details .= $shipment_details_row_start;

                        $item = $shipment->items()->where('type', 0)->first();

                        $shipment_details .= '
                                <td>' . $item->product->product_name . '</td>
                                <td>' . $item->created_at . '</td>';
                        if($item_description_flag){
                            $shipment_details .= '<td>' . $item->description . '</td>';
                        }
                        $shipment_details .='
                                 <td>' . $item->quantity . '</td>
                        ';

                        $shipment_details .= $shipment_details_row_end;
                    }
                    else if ($shipment->booking_type_id == 3) {
                        $first = TRUE;

                        foreach ($shipment->items as $item) {
                            if ($first) {
                                $shipment_details .= $shipment_details_row_start;
                            }
                            else {
                                $shipment_details .= '
                              <tr>
                                ';
                            }

                            $shipment_details .= '
                                <td>' . $item->product->product_name . '</td>
                                <td>' . $item->created_at . '</td>';
                                if($item_description_flag){
                                    $shipment_details .= '<td>' . $item->description . '</td>';
                                }
                                $shipment_details .='
                                         <td>' . $item->quantity . '</td>
                                ';

                            if ($first) {
                                $shipment_details .= $shipment_details_row_end;
                            }
                            else {
                                $shipment_details .= '
                              </tr>
                                ';
                            }

                            $first = FALSE;
                        }
                    }
                    else {
                        $shipment_details .= $shipment_details_row_start;

                        $item = $shipment->items->first();

                        $shipment_details .= '
                                <td>' . $item->product->product_name . '</td>
                                <td>' . $item->created_at . '</td>';
                        if($item_description_flag){
                            $shipment_details .= '<td>' . $item->description . '</td>';
                        }
                        $shipment_details .='
                                 <td>' . $item->quantity . '</td>
                        ';

                        $shipment_details .= $shipment_details_row_end;
                    }

                    $total_cod += $shipment->amount;
                }
            }

            $shipment_details .= '
                        </tbody>
                      </table>
            ';

            $first_shipment = $receiving_sheet_shipments->first();

            $shipment = Shipment::find($first_shipment->shipment_id);

            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
            ';

            if ($user_type != 4) {
                $main_details .= '
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                ';
            }
            else {
                $main_details .= '
                            <td class="text-center align-middle"><img src="' . public_path('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                ';
            }

            $main_details .= '
                            <td class="text-center align-middle color primary"><strong>Receiving Sheet</strong></td>
            ';

            if ($user_type != 4) {
                $main_details .= '
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                ';
            }
            else {
                $main_details .= '
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst($shipment->user->name) . '</td>
                ';
            }

            $main_details .= '
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Shipper</strong></td>
                            <td>' . $shipment->user->name . '</td>
            ';

            $user_id = $shipment->user_id;

            if ($user_id == 7828) {
                $main_details .= '
                            <td rowspan="8" class="text-center align-middle">
                ';
            }
            else {
                $main_details .= '
                            <td rowspan="7" class="text-center align-middle">
                ';
            }

            $main_details .= '
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
            ';

            if ($user_id == 7828) {
                $warehouse = GulAhmedPickupAddress::where('pickup_address_id', $shipment->pickup_address_id)->first();

                $main_details .= '
                          <tr>
                            <td class="color secondary"><strong>Warehouse/Store ID</strong></td>
                            <td>' . $warehouse->warehouse_id . '</td>
                          </tr>
                ';
            }

            $main_details .= '
                          <tr>
                            <td class="color secondary"><strong>Person of Contact</strong></td>
                            <td>' . $shipment->pickup_address->poc . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Pickup Address</strong></td>
                            <td>' . $shipment->pickup_address->pickup_address . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Phone Number</strong></td>
                            <td>' . $shipment->pickup_address->phone . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Origin</strong></td>
                            <td>' . $shipment->pickup_address->city->name  . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Amount</strong></td>
                            <td>Rs ' . number_format($total_cod) . '</td>
                          </tr>
                        </tbody>
                      </table>
            ';

            $html .= $main_details;

            $html .= $shipment_details;

            $html .= '
                      <div class="mt-2 manual_form">
                        <div class="row align-items-end">
                          <div class="col">
                            <strong class="d-inline-block w-200">No. of Shipments Received:</strong>
                            <span class="d-inline-block w-200 line"></span>
                          </div>

                          <div class="col text-right">
                            <div class="d-inline-block text-center mt-2">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Client Signature</strong>
                            </div>
                          </div>
                        </div>

                        <strong class="d-block text-center mt-2">For Office Use</strong>

                        <hr>

                        <div class="row mt-2">
                          <div class="col">
                            <div>
                              <strong class="d-inline-block w-200">Rider Name:</strong>
                              <span class="d-inline-block w-200 line"></span>
                            </div>

                            <div class="mt-2">
                              <strong class="d-inline-block w-200">Shipments picked at:</strong>
                              <span class="d-inline-block w-200 line"></span>
                            </div>
                          </div>
                        </div>

                        <div class="row mt-4">
                          <div class="col text-left">
                            <div class="d-inline-block text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>

                          <div class="col text-right">
                            <div class="d-inline-block text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Office Signature</strong>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col text-center mt-2">
                            <span class="d-block">Plot 105, Mehran Town Sector 7 A Korangi Karachi, Karachi, Karachi City, Sindh, Pakistan</span>
                            <span class="d-block">Phone: 0304-11-11-232 | Email: info@trax.pk | URL: www.trax.pk</span>
                          </div>
                        </div>
                      </div>
            ';
        }

        if (!$body_only) {
            $html .= '
                    </div>
            ';

            if ($user_type != 4) {
                $html .= '
                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                ';
            }

            $html .= '
                  </body>
                </html>
            ';
        }

        return $html;
    }

    public function print(Request $request) {
        $user_type = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;
        }
        else if (Auth::guard('web')->check()) {
            $user_type = 1;
        }
        else if (Auth::guard('substitute_users')->check()) {
            $user_type = 2;
        }

        if ($user_type) {
            return $this->view($request->id, $user_type);
        }
    }

    public function create_view() {
        return view('client.shipment.receiving_sheet.create');
    }

    public function get_shipment_details(Request $request) {
        $tracking_number = $request->tracking;

        if($tracking_number != ''){
            $shipment = Shipment::where('tracking_number',$tracking_number);
            if($shipment->exists()){
                $shipment = $shipment->first();
                $pickup_id = $shipment->pickup_address_id;

                if(session('user_type') == 2){
                    if(session('restriction') == 1){
                        $sub_check = SubstituteUserShipment::where('substitute_user_id', Auth::id())->where('shipment_id', $shipment->id);
                        if(!$sub_check->exists()){
                            return ['status' => 1, 'error' => $shipment->tracking_number . ' is restricted'];
                        }
                    }
                }
                if ($shipment->shipper_status_id != 1) {
                    return ['status' => 1, 'error' => $shipment->tracking_number . ' can no longer be added to a Receiving Sheet'];
                }

                if ($shipment->user_id != session('user_id')) {
                    return ['status' => 1, 'error' => $shipment->tracking_number . ' doesn\'t belong to you'];
                }

                if (ReceivingSheetShipment::where('shipment_id', $shipment->id)->exists()) {
                    return ['status' => 1, 'error' => $shipment->tracking_number . ' is already in a Receiving Sheet'];
                }

                if($request->has('pickup_address_id')){
                    if($request->pickup_address_id == $pickup_id){
                        $service = $shipment->booking_type->booking_type;
                        $origin = $shipment->pickup_address->city->name;
                        $destination = $shipment->consignee_city->name;
                        $address = $shipment->pickup_address->pickup_address;
                        $booking = $shipment->created_at->toDateTimeString();
                        return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number,'order_id'=>$shipment->order_id,'service_type'=>$service,'origin'=>$origin,'destination'=>$destination, 'address' => $address,'booking'=>$booking]);
                    }else{
                        return ['status' => 1, 'error' => 'Given Shipments Pickup Addresses are different from one another and cannot be added to the same Receiving Sheet'];
                    }
                }else{
                    $service = $shipment->booking_type->booking_type;
                    $origin = $shipment->pickup_address->city->name;
                    $destination = $shipment->consignee_city->name;
                    $address = $shipment->pickup_address->pickup_address;
                    $booking = $shipment->created_at->toDateTimeString();
                    return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number,'order_id'=>$shipment->order_id,'service_type'=>$service,'origin'=>$origin,'destination'=>$destination, 'address' => $address,'booking'=>$booking,'pickup_address'=>$pickup_id]);
                }
            }else{
                return ['status' => 1, 'error' => 'Shipment with given tracking number doesn\'t exists'];
            }
        }
    }

    public function print_receiving_sheet_and_air_waybill(Request $request) {
        $user_type = NULL;
        $user_id = NULL;

        if (Auth::guard('admin')->check()) {
            $user_type = 3;

            $user_id = Auth::id();
        }
        else if (Auth::guard('web')->check()) {
            $user_type = 1;

            $user_id = session('user_id');
        }
        else if (Auth::guard('substitute_users')->check()) {
            $user_type = 2;

            $user_id = session('user_id');
        }

        $html = '';

        $receiving_sheet = ReceivingSheet::find($request->receiving_sheet_id);

        if ($user_type && $receiving_sheet) {
            $html .= '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Receiving Sheet and Air Waybill(s)</title>

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
                        font-size: 0.7rem !important;
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

                      .air_waybill table.table-bordered tbody tr td {
                        width: 12.5% !important;
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

                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
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

                      .receiving_sheet {
                        page-break-after: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div class="p-1">
                        <div class="receiving_sheet">
            ';

            $html .= $this->view($receiving_sheet->id, $user_type, TRUE);

            $html .= '
                        </div>
                        <div class="air_waybill">
            ';

            $shipment_ids = array();

            foreach ($receiving_sheet->receiving_sheet_shipments as $receiving_sheet_shipment) {
                $shipment_ids[] = $receiving_sheet_shipment->shipment->id;
            }

            $html .= ShipperShipmentBookController::air_waybill($user_type, $user_id, $shipment_ids, FALSE, NULL);

            $html .= '
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
        }

        return $html;
    }

    public function update_cn_info(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id != null){
            $shipment = Shipment::find($shipment_id);
            if($shipment){
                $consignee_city_id = $shipment->consignee_city_id;
                $service_type_id = $shipment->booking_type_id;
                $pickup_city_id = $shipment->pickup_address->city_id;
                $shipment_info = array();
                if(session('account_type') == 1){
                    $shipper_shipping_modes = RateStatus::where('user_id', session('user_id'))->where('status', 1);
                }else{
                    $shipper_shipping_modes = CorporateRateStatus::where('user_id', session('user_id'))->where('status', 1);
                }
                $shipping_modes = null;
                if ($shipper_shipping_modes->exists()) {
                    $shipper_shipping_modes = $shipper_shipping_modes->pluck('shipping_mode_id')->toArray();

                    $city_shipping_modes = CityDelivery::where('city_id', $consignee_city_id)->where('booking_type_id', $service_type_id)->whereIn('shipping_mode_id', $shipper_shipping_modes);
                    if ($city_shipping_modes->exists()) {
                        $city_shipping_modes = $city_shipping_modes->pluck('shipping_mode_id')->toArray();

                        if ($pickup_city_id != $consignee_city_id) {
                            $city_shipping_modes = array_diff($city_shipping_modes, [4]);
                        }

                        if (!empty($city_shipping_modes)) {
                            $shipping_modes = ShippingMode::whereIn('id', $city_shipping_modes)->get();
                        }
                    }
                }

                $shipment_info['tracking_number'] = $shipment->tracking_number;
                $shipment_info['consignee_name'] = $shipment->consignee_name;
                $shipment_info['consignee_address'] = $shipment->consignee_address;
                $shipment_info['consignee_phone_number_1'] = $shipment->consignee_phone_number_1;
                $shipment_info['order_id'] = $shipment->order_id;
                $shipment_info['special_instructions'] = $shipment->special_instructions;
                $shipment_info['amount'] = $shipment->amount;
                $shipment_info['pieces'] = $shipment->pieces;
                $shipment_info['shipping_mode_id'] = $shipment->shipping_mode_id;
                $shipment_info['consignee_city_id'] = $consignee_city_id;
                $shipment_info['service_type_id'] = $service_type_id;
                $shipment_info['pickup_city_id'] = $pickup_city_id;

                return ['status' => 0, 'shipment_info' => $shipment_info, 'shipping_modes' => $shipping_modes];
            }
            else{
                return ['status' => 1, 'error' => 'Shipment not found'];
            }
        }
        else{
            return ['status' => 1, 'error' => 'Shipment not selected'];
        }
    }

    public function update_consignee_info_and_special_instructions(Request $request){
        $shipment_id = $request->update_consignee_info_shipment_id;
        $consignee_name = $request->update_consignee_name;
        $consignee_address = $request->update_consignee_address;
        $consignee_phone = $request->update_consignee_phone;
        $special_instructions = $request->update_special_instructions;
        $pieces = $request->update_pieces;
        $amount = $request->update_amount;
        if($shipment_id != null){
            if($consignee_name != null && $consignee_address != null && $consignee_phone != null){
                $shipment = Shipment::find($shipment_id);

                $shipment->consignee_name = $consignee_name;
                $shipment->consignee_address = $consignee_address;
                $shipment->consignee_phone_number_1 = $consignee_phone;
                $shipment->special_instructions = $special_instructions;
                if($request->has('update_order_id')){
                    $shipment->order_id = $request->update_order_id;
                }
                if($request->has('shipping_mode') && $request->shipping_mode > 0){
                    $shipment->shipping_mode_id = $request->shipping_mode;
                }
                $shipment->amount = $amount;
                $shipment->pieces = $pieces;
                $shipment->save();
                ShipmentPiece::where('shipment_id', $shipment->id)->delete();
                $total_pieces= 0;
                if($pieces > 1){
                    for($i=1; $i<=$pieces; $i++){
                        $shipment_piece = new ShipmentPiece();
                        $shipment_piece->shipment_id = $shipment->id;
                        $total_pieces++;
                        $shipment_piece->numbering=$total_pieces;
                        $shipment_piece->tracking_number= $shipment->id . $total_pieces;
                        $shipment_piece->save();
                    }

                }
                return redirect()->back()->with('success', 'Shipment updated successfully!');
            }
            else{
                return redirect()->back()->with('error', 'Please fill required fields');
            }
        }
        else{
            return redirect()->back()->with('error', 'Shipment not selected!');
        }
    }

    public function shipments_index() {
        return view('client.shipment.receiving_sheet.shipments');
    }

    public function shipments_list(Request $request) {
        $shipments = Shipment::join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('receiving_sheet_shipments as rss', 'shipments.id', '=', 'rss.shipment_id')
            ->join('receiving_sheets AS rs', 'rss.receiving_sheet_id', '=', 'rs.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftJoin('gul_ahmed_pickup_addresses as gapa', 'usi.id', '=', 'gapa.pickup_address_id')
            ->select('shipments.tracking_number', 'rs.id AS receiving_sheet', 'shipments.order_id', 'gapa.warehouse_id', 'oc.name AS origin_city', 'dc.name AS destination_city', 'shipments.created_at AS booking_date', 'shipments.estimated_weight', 'shipments.amount', 'usi.pickup_address')
            ->where('shipments.packaging_material_request', 0)
            ->where(function ($query) {
                $query->whereNull('rs.status')->orWhere('rs.status', 0);
            })
            ->where(function ($query) {
                $query->where('shipments.user_id', session('user_id'))
                ->orwhereIn('shipments.user_id', session('sister_users'));
            });

        if (session('user_type') == 2) {
            if(session('restriction') == 1) {
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }

        $datatable = Datatables::of($shipments);

        if ($request->get('search_to') && $request->get('search_from')) {
            $datatable->whereBetween('rs.created_at', [$request->get('search_from'), $request->get('search_to')]);
        }
        else {
            $datatable->whereRaw('false');
        }

        return $datatable->make(true);
    }

    public function shipments_excel_index(){
        $pickup_addresses = UserShippingInfo::whereHas('city', function ($query) {
            $query->where('pickup', 1)->where('status', 1)->whereNotNull('zone_id');
        })->where('user_id', session('user_id'))->where('hidden', 0)->where('status', 1)->get();

        return view('client.shipment.receiving_sheet.excel')->with(['pickup_addresses' => $pickup_addresses]);
    }

    public function shipments_excel_store(Request $request){
        $user_id = session('user_id');
        $pickup_address_id = $request->pickup_address;
        if(!$pickup_address_id){
            return redirect()->back()->with('error', 'Please select pickup_address');
        }
        $names = [
            'tracking_number' => 'Tracking Number'
        ];

        $messages = [
            'required' => ':attribute is Required.',
            'required_if' => ':attribute is Required when :other is :value.',
            'filled' => ':attribute is Optional but cannot be Empty if Present.',
            'integer' => ':attribute must be an Integer.',
            'numeric' => ':attribute must be a Number.',
            'boolean' => ':attribute must be 0 or 1.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'email' => ':attribute must be a Valid Email Address.',
            'exists' => 'Given :attribute is of Invalid ID.',
            'unique' => ':attribute is already Present.',
            'date_format' => ':attribute must be of valid Format, required Format is: YYYY-MM-DD.',
            'in' => ':attribute must be No or Yes.',
            'phone_number.regex' => ':attribute format is Invalid, required Format is: 03000000000.',
        ];

        $rules = [
            'tracking_number' => ['required', 'integer', 'distinct', 'digits_between:10,20', Rule::exists('shipments', 'tracking_number')->where(function ($query) use ($user_id, $pickup_address_id) {
                $query->where('user_id', $user_id)->where('shipper_status_id', 1)->where('pickup_address_id', $pickup_address_id);
            })]
        ];

        $fields = [0 => 'tracking_number'];

        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number'];
        }

        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if($index == 1){
                }
                elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }
            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            else {
                unset($spreadsheet[0]);
            }
        }

        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }
            unset($spreadsheet);
            $errors = array();
            $tracking_numbers = array();
            $row_errors = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }

            }
            if(!empty($errors)){
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);
                return redirect()->back()->withErrors($errors);
            }
            else{

                $shipment_ids = array();
                $receiving_sheet_id = NULL;
                foreach($rows as $key => $row){
                    $shipment = Shipment::where('tracking_number', $row['tracking_number'])->where('shipper_status_id', 1)->first();
                    if(session('user_type') == 2){
                        if(session('restriction') == 1){
                            $sub_check = SubstituteUserShipment::where('substitute_user_id', Auth::id())->where('shipment_id', $shipment->id);
                            if(!$sub_check->exists()){
                                $row_errors[] = $shipment->tracking_number . ' is restricted';
                                continue;
                            }
                        }
                    }
                    if (ReceivingSheetShipment::where('shipment_id', $shipment->id)->exists()) {
                        $row_errors[] = $shipment->tracking_number . ' is already in a Receiving Sheet';
                        continue;
                    }

                    if($shipment->pickup_address_id == $pickup_address_id){
                        if ($receiving_sheet_id == NULL) {
                            $receiving_sheet = new ReceivingSheet();

                            $receiving_sheet->user_id = $user_id;
                            $receiving_sheet->pickup_address_id = $pickup_address_id;
                            $receiving_sheet->booked = 1;
                            $receiving_sheet->status = 0;

                            $receiving_sheet->save();

                            $receiving_sheet_id = $receiving_sheet->id;
                        }
                        else {
                            $receiving_sheet = ReceivingSheet::find($receiving_sheet_id);

                            $receiving_sheet->booked = $receiving_sheet->booked + 1;

                            $receiving_sheet->save();
                        }

                        if(session('user_type') == 2){
                            $substitute_user_receiving_sheet = new SubstituteUserReceivingSheet();
                            $substitute_user_receiving_sheet->substitute_user_id = Auth::id();
                            $substitute_user_receiving_sheet->receiving_sheet_id = $receiving_sheet->id;
                            $substitute_user_receiving_sheet->save();
                        }
                        // $receiving_sheet_id = $receiving_sheet->id;

                        $receiving_sheet_shipment = new ReceivingSheetShipment();

                        $receiving_sheet_shipment->shipment_id = $shipment->id;
                        $receiving_sheet_shipment->receiving_sheet_id = $receiving_sheet_id;

                        $receiving_sheet_shipment->save();
                        $tracking_numbers[] = $shipment->tracking_number;
                        $shipment_ids[] = $shipment->id;

                    }
                }

                if ($user_id == 7828) {
                    $confirmation_datetime = Carbon::now()->toDateTimeString();

                    $confirmation_shipments = array();

                    foreach ($shipment_ids as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);

                        $confirmation_shipment = array();

                        $confirmation_shipment['CNN'] = $shipment->tracking_number;
                        $confirmation_shipment['reference_number'] = $shipment->order_id;
                        $confirmation_shipment['ConfirmationDateTime'] = $confirmation_datetime;

                        $confirmation_shipments[] = $confirmation_shipment;
                    }

                    dispatch(new ProcessGulAhmedShipmentConfirmation($confirmation_shipments));
                }

                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));


                return redirect()->back()->with(['success' => 'Total ' . count($shipment_ids) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers, 'shipment_errors' => $row_errors]);
            }

        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    static public function print_receiving_sheet_and_air_waybill_api($id, $user_type, $body_only = FALSE) {
        // $user_type = NULL;

        

        $html = '';

        $receiving_sheet = ReceivingSheet::find($id);

        if ($user_type && $receiving_sheet) {
            
            $user_id = $receiving_sheet->user_id;

            $html .= '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Receiving Sheet and Air Waybill(s)</title>

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
                        font-size: 0.7rem !important;
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

                      .air_waybill table.table-bordered tbody tr td {
                        width: 12.5% !important;
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

                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
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

                      .receiving_sheet {
                        page-break-after: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div class="p-1">
                        <div class="receiving_sheet">
            ';

            $html .= $this->view($receiving_sheet->id, $user_type, TRUE);

            $html .= '
                        </div>
                        <div class="air_waybill">
            ';

            $shipment_ids = array();

            foreach ($receiving_sheet->receiving_sheet_shipments as $receiving_sheet_shipment) {
                $shipment_ids[] = $receiving_sheet_shipment->shipment->id;
            }

            $html .= ShipperShipmentBookController::air_waybill($user_type, $user_id, $shipment_ids, FALSE, NULL);

            $html .= '
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
        }

        return $html;
    }
}