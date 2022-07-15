<?php

namespace App\Http\Controllers\Admins\Retail;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\BanksList;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use DNS2D;

class RetailAdminAccounts extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public function index(){

        ActivityTrailController::createActivityTrailLog(Auth::id(),3);
        $banks = BanksList::where('status',1)->get();
      return view('admin.retail.accounts.index')->with(['banks' => $banks]);
    }

    public function list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),63);
        }
        $retail_shipper_info = RetailShipperInfo::leftjoin('cities as c','c.id','=','retail_shipper_infos.city_id')
            ->leftjoin('banks_lists as b','b.id','=','retail_shipper_infos.bank_id')
        ->select('retail_shipper_infos.id as id','retail_shipper_infos.shipper_name as shipper','retail_shipper_infos.shipper_address as address','retail_shipper_infos.shipper_phone_no as number','city_id','retail_shipper_infos.completed_status as document_status','c.name as city','retail_shipper_infos.created_at as added_at','retail_shipper_infos.iban as iban');

        $datatable = Datatables::of($retail_shipper_info)
            ->editColumn('document_status',function($request){
                if($request->document_status == 0){
                    return 'Incomplete';
                }
                else{
                    return 'Complete';
                }
            })
            ->addColumn('action', function($user) {
                if (session('role_id') == 1 || in_array(486, session('permissions'))) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $view_button = '<button type="button" class="dropdown-item view"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View</div></button>';

                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                        $dropdown .= $edit_button;
                        $dropdown .= $view_button;

                    $dropdown .= '
                      </div>
                    </div>
                ';

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
        ;

        return $datatable->make(true);

    }
    public function retail_bank_info(Request $request){
        $id = $request->id;
        if($id)
        {
            $retail_shipper_info = RetailShipperInfo::find($id);
            $details = array();
            $details['shipper_name'] =  $retail_shipper_info->shipper_name;
            if($retail_shipper_info->iban != null) {
                $details['iban'] = $retail_shipper_info->iban;
            }
            if($retail_shipper_info->account_number != null) {
                $details['account_no'] = $retail_shipper_info->account_number;
            }
            if($retail_shipper_info->bank_id != null){
//                $bank = BanksList::find($retail_shipper_info->bank_id)->first();
                $details['bank_id'] = $retail_shipper_info->bank_id;
                $details['bank_name'] = $retail_shipper_info->bank->name;
            }
            $image = '';
            if($retail_shipper_info->cheque_image != null){
                $image .= '<div class="text-center" id="picture_div"><a class="btn btn-sm btn-outline-info align-middle" href="' . asset(Storage::url('retail_shipper_cheque/'. $retail_shipper_info->cheque_image)) . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle"> Cheque Image</span></a></div>';
                $details['image'] = $image;
            }
            return response()->json(['status'=>'0','details'=> $details]);
        }
        else{
            return response()->json(['error'=>'No Data Found','status'=> '1']);
        }
    }

    public function retail_bank_info_update(Request $request){
        $shipper_account = RetailShipperInfo::find($request->id);
        if($shipper_account){
            $shipper_account->iban = $request->iban;
            $shipper_account->account_number = $request->account_no;
            $shipper_account->bank_id = $request->bank_info;
            if ($request->hasFile('cheque_image')){
                $filename = 'retail_shipper_' . $shipper_account->id . '_cheque_image.png';

                $file = $request->file('cheque_image');

                Storage::disk('public')->putFileAs('retail_shipper_cheque', $file, $filename);
                $shipper_account->cheque_image = $filename;
                $shipper_account->completed_status = 1;
            }
            $shipper_account->save();
        }
        return redirect()->back()->with('success', 'Shipper details updated successfully');
    }

    public function retail_slip(Request $request) {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $user_name = Auth::user()->name . ' (Retail)';

        $print_details = '
            <div class="small mt-1">Printed By: ' . $user_name . '</div>
        ';

        $html = '';

        $html .= '
            <!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">
        ';
        $html .= '
            <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
        ';

        $html .= '
                <title>Slip</title>

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
                <div>
        ';

        $html .= '
            <style>
              @font-face {
                font-family: "Fajer Noori Nastalique";
                src: url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.eot') . '");
                src: url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.eot?#iefix') . '") format("embedded-opentype"),
                url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.woff') . '") format("woff"),
                url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.otf') . '") format("opentype"),
                url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.ttf') . '") format("truetype"),
                url("' . asset('fonts/urdu/Fajer-Noori-Nastalique.svg#FajerNooriNastalique') . '") format("svg");
                font-weight: normal;
                font-style: normal;
                unicode-range: U+0600-06FF, U+0750-077F, U+FB50-FDFF, U+FE70-FEFF;
              }

              .urdu {
                font-family: "Fajer Noori Nastalique";
              }
            </style>
        ';

        $shipment_details = '';
        $page_items = 1;
        foreach($request->ids as $id) {
            $shipment = Shipment::find($id);

            if($shipment->shipment_type == 1){

                $shipping_mode = $shipment->shipping_mode->mode;
            }
            else{
                $retail_shipment = RetailShipment::where('shipment_id',$shipment->id)->first();
                if($retail_shipment){
                    $shipping_mode = $retail_shipment->shipping_modes->name;
                }
            }


            $slip = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';

            $slip .= '
                          <tr>
                            <td rowspan="3" colspan="2" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                            <td colspan="3" class="color primary"><strong>Shipper Account No.</strong></td>
                            <td colspan="2">' . $shipment->retail->shipper_account_no . '</td>
                            <td colspan="2" class="color primary"><strong>Origin</strong></td>
                            <td colspan="4" class="border twice-right"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                        </tr>
                          <tr>
                            <td colspan="3" class="color primary border"><strong>Airway Bill Number</strong></td>
                            <td colspan="2" class="border twice-bottom"><strong>' . $shipment->tracking_number . '</strong></td>
                            <td colspan="2" class="color primary border"><strong>Destination</strong></td>
                            <td colspan="4" class="border twice-bottom twice-right"><strong>' . $shipment->consignee_city->name . '</strong></td>
                          </tr>
                           <tr>
                            <td colspan="3" class="color primary"><strong>Order ID</strong></td>
                            <td colspan="8">'.$shipment->order_id.'</td>
                           </tr>
                          <tr>
                            <td colspan="1" class="color primary border"><strong>#IBAN</strong></td>
                            <td colspan="2" class="border twice-bottom"><strong>' . $shipment->retail->shipper->iban . '</strong></td>
                            <td colspan="2" class="color primary border"><strong>Account Number</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right"><strong>' . $shipment->retail->shipper->account_number . '</strong></td>
                            <td colspan="1" class="color primary border"><strong>Bank</strong></td>
                            <td colspan="2" class="border twice-bottom twice-right"><strong>' . (($shipment->retail->shipper->bank_id != null) ? $shipment->retail->shipper->bank->name : '') . '</strong></td>
                          </tr>
                ';

            $slip .= '
                          <tr>
                            <td colspan="7" class="text-center color primary border twice-top twice-left twice-right"><strong>Shipper</strong></td>
                            <td colspan="6" class="text-center color primary border twice-top twice-left twice-right"><strong>Consignee</strong></td>
                          </tr>
                ';

            $slip .= '
                          <tr>
                            <td colspan="1" class="color secondary twice-left"><strong>Name</strong></td>
                            <td colspan="2" class="border">' . $shipment->retail->shipper_name . '</td>
                            <td colspan="2" class="color secondary border"><strong>Phone No</strong></td>
                            <td colspan="2">' . $shipment->retail->shipper_phone_no . '</td>
                            <td colspan="1" class="color secondary border twice-left"><strong>Name</strong></td>
                            <td colspan="1">' . $shipment->consignee_name . '</td>
                            <td colspan="2" class="color secondary border"><strong>Phone No</strong></td>
                            <td colspan="2" class="border twice-right"">' . $shipment->consignee_phone_number_1 . '</td>
                          </tr>
                ';
            $slip .= '
                      <tr>
                        <td class="color secondary border twice-bottom"><strong>Address</strong></td>
                        <td colspan="6" class="border twice-bottom twice-right">' . $shipment->retail->shipper_address . '</td>
                        <td class="color secondary border twice-bottom twice-left"><strong>Address</strong></td>
                        <td colspan="5" class="border twice-bottom twice-right">' . $shipment->consignee_address . '</td>
                      </tr>
                ';
            $fuel_and_gst = $shipment->retail->fuel_surcharge + $shipment->retail->gst;
            $slip .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Product</strong></td>
                                <td colspan="2" class="color primary"><strong>Pieces</strong></td>
                                <td colspan="2" class="color primary"><strong>Weight</strong></td>
                                <td colspan="2" class="color primary"><strong>Service Charges</strong></td>
                                <td colspan="2" class="color primary border"><strong>Fuel and GST</strong></td>
                                <td colspan="2" class="color primary border twice-right"><strong>Total Charges</strong></td>
                            </tr>
                              <tr>
                                <td colspan="2" class="border twice-bottom twice-left">' . $shipment->retail->shipping_modes->name . '</td>
                                <td colspan="2" class="border twice-bottom">' . $shipment->pieces . '</td>
                                <td colspan="2" class="border twice-bottom">' . number_format($shipment->estimated_weight) . '</td>
                                <td colspan="2" class="border twice-bottom">' . number_format(ROUND($shipment->retail->weight_charges, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                <td colspan="2" class="border twice-bottom">' . number_format(ROUND($fuel_and_gst, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                                <td colspan="2" class="border twice-bottom twice-right">' . number_format(ROUND($shipment->retail->total_charges, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                              </tr>';

            foreach($shipment->items as $item){
                if($item->insurance == 1){
                    $insurance = '<i class="la la-check-square "> <b>Yes</b></i> <i class="la la-minus-square"> No</i>';
                }
                else{
                    $insurance = '<i class="la la-minus-square"> Yes</i> <i class="la la-check-square"> <b>No</b></i>';
                }
                $slip .= '
                              <tr>
                                <td colspan="1" class="color primary border twice-left twice-bottom"><strong>Product Name</strong></td>
                                <td colspan="3" class="color border twice-bottom">'. $item->product->product_name . '</td>
                                <td colspan="5" class="color border twice-bottom text-center mr-3"><strong>Insurance: Do you required coverage</strong> '. $insurance . '</td>
                                <td colspan="2" class="color primary border twice-bottom"><strong>Declared Value</strong></td>
                                <td colspan="2" class="color border twice-bottom twice-right">' . number_format(ROUND($item->price, 0, PHP_ROUND_HALF_DOWN)) . '</td>
                            </tr>';
            }

            if($shipment->length != null && $shipment->breadth != null && $shipment->height != null){
                $dimensions = $shipment->length . 'x' . $shipment->breadth . 'x' . $shipment->height;
            }
            else{
                $dimensions = '';
            }

            $slip .= '
                              <tr>
                                <td colspan="4" class="color primary border twice-left twice-bottom"><strong>DIMENSIONS OF SHIPMENT (LxWxD)</strong></td>
                                <td colspan="5" class="color border twice-bottom">'. $dimensions . '</td>
                                <td colspan="2" class="color primary border twice-left"><strong>Collection By</strong></td>
                                <td colspan="4" class="color border twice-right">' . Auth::user()->name . '</td>
                            </tr>';
            $slip .= '
                              <tr>
                                <td colspan="4" rowspan="2" class="color primary border twice-left"><strong>Shipper\'s Signature</strong></td>
                                <td colspan="5" rowspan="2" class="color border twice-bottom"></td>
                                <td colspan="2" class="color primary border twice-left"><strong>Code</strong></td>
                              
                            </tr>';
            $slip .= '
                              <tr>
                                <td colspan="2" class="color primary border twice-left"><strong>Date</strong></td>
                                <td colspan="4" class="color border twice-bottom twice-right">' . $shipment->created_at . '</td>
                            </tr>
                            </tbody>
                            </table>
                            </div>
                           ';

            $slip .= '
                  <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Shipper Copy</p></div><div class="col"><hr></div>
                  <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                ';

            $shipment_details .= $slip;

            //airway_bill_start


            if ($shipment->booking_type_id == 3) {
                foreach ($shipment->items as $shipment_item){
                    if($page_items == 0){
                        $table_start = '
                    <div class="page position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>
            ';
                    }
                    else{
                        $table_start = '
                    <div class="position-relative"><table class="table table-sm table-bordered border twice">
                        <tbody>
            ';
                    }

                    $table_start .= '
                                <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                    ';
                    $table_start .= '
                            <td rowspan="4" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment_item->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment_item->id . '</strong></span>
                            </td>
                            <td rowspan="4" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment_item->id, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                            <tr>
                                <td class="color secondary border twice-top twice-left"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $shipment_item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td colspan="1" class="border twice-top">' . $shipment_item->quantity . '</td>
                                <td colspan="2" class="color secondary border twice-top"><b>Tracking Number</b></td>
                            </tr>
                ';

                    $table_start .= '
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="2" class="border twice-bottom">' . $shipment_item->description . '</td>
                                <td class="color secondary border twice-bottom"><strong>Price</strong></td>
                                <td class="border twice-bottom">Rs ' . number_format($shipment_item->price) . '</td>';

                    $table_start .= '
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                          </tr>
                        </tbody>
                    </table></div>
                    ';
                    $shipment_details .= $table_start;
                    $page_items++;
                    if($page_items >= 3){
                        $page_items = 0;
                    }
                }
            } else {
                $page_items = $page_items + 3;
                if($page_items >= 5){
                    $page_items = 0;
                }
                $table_start = '
                      <div class="position-relative">
                        <table class="table table-sm table-bordered border twice">
                            <tbody>
                ';
                $table_start .= '
                            <td rowspan="4" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>
                ';
                $table_start .= '
                            <td rowspan="4" colspan="2" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="4" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                ';
                            if($shipment->business_category->id==2){
                              $table_start .='<td class="color primary border twice-left"><strong>Service Type</strong></td>
                ';
                          }else{
                            $table_start .='<td class="color primary border twice-left"><strong>Service</strong></td>
                            ';
                          }
                           

                if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4) {
                    $table_start .= '
                                <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
                } else if ($shipment->booking_type_id == 2) {
                    $table_start .= '
                                <td class="replacement"><strong class="align-middle">' . $shipment->booking_type->booking_type . '</strong><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                        ';
                }
//                    else if ($shipment->booking_type_id == 3) {
//                        $table_start .= '
//                                <td><strong>' . $shipment->booking_type->booking_type . ' (' . (($shipment->package_type == 1) ? 'Complete' : 'Partial') . ')' . '</strong></td>
//                    ';
//                    }
                else {
                    $table_start .= '
                                <td><strong>' . $shipment->booking_type->booking_type . '</strong></td>
                    ';
                }
                $table_start .= '
                            <td class="color primary"><strong>Datetime</strong></td>
                            <td>' . $shipment->created_at->format('Y-m-d H:i:s') . '</td>
                          </tr>
                          <tr>';
                          if($shipment->business_category->id==1){
                            $table_start.='<td class="color primary border twice-left"><strong>Shipping Mode</strong></td>
                            <td><strong>' . $shipping_mode . '</strong></td>
                ';
                        }

                          

                $table_start .= '
                            <td class="color primary"><strong>Order ID</strong></td>
                            <td>' . $shipment->order_id . '</td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Origin</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->pickup_address->city->name . '</strong></td>
                            <td class="color primary border twice-bottom"><strong>Destination</strong></td>
                            <td class="border twice-bottom"><strong>' . $shipment->consignee_city->name . '</strong></td>
                          </tr>
                          <tr>
                            <td class="color primary border twice-bottom twice-left"><strong>Business Category</strong></td>
                            <td colspan="3" class="border twice-bottom twice-right"><strong>' . $shipment->business_category->name . '</strong></td>
                          </tr>
                          <tr>
                            <td colspan="4" class="text-center color primary border twice-top twice-right"><strong>Shipper</strong></td>
                            <td colspan="4" class="text-center color primary border twice-top twice-left"><strong>Consignee</strong></td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Name</strong></td>
                ';

                $table_start .= '
                            <td colspan="3" class="border twice-right">' . $shipment->retail->shipper_name . '</td>
                ';

                $table_start .= '
                                <td class="color secondary border twice-left"><strong>Name</strong></td>
                                <td colspan="3">' . $shipment->consignee_name . '</td>
                              </tr>

                              <tr>
                ';

                $table_start .= '
                        <td class="color secondary"><strong>Address</strong></td>
                        <td colspan="3" class="border twice-right">' . $shipment->retail->shipper_address . '</td>
                ';

                $table_start .= '
                                <td class="color secondary border twice-left"><strong>Address</strong></td>
                                <td colspan="3">' . $shipment->consignee_address . '</td>
                              </tr>
                              <tr>
                ';

                $table_start .= '
                    <td class="color secondary border twice-bottom"><strong>Phone Number(s)</strong></td>
                    <td colspan="3" class="border twice-bottom twice-right">' . $shipment->retail->shipper_phone_no . '</td>
                ';

                $table_start .= '
                            <td class="color secondary border twice-bottom twice-left"><strong>Phone Number(s)</strong></td>
                            <td colspan="3" class="border twice-bottom">' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                          </tr>
                ';

                $table_end = '
                          <tr>
                            <td rowspan="3" colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Special Instruction(s)</strong></td>
                            <td rowspan="3" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->special_instructions . '</td>';
                if($shipment->shipping_mode_id == 2 && $shipment->estimated_weight != null ) {
                    $table_end .= ' <td class="color primary border twice-top twice-bottom twice-left"><strong>Weight</strong></td>
                        <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->estimated_weight . '</strong></td>
                          </tr>
                          <tr>
                ';
                }
                else{
                    $table_end .= ' <td colspan="2" class="border twice-top twice-bottom twice-left" style="height: 20px;"></td>
                          </tr>
                          <tr>';
                }

                if ($shipment->booking_type_id == 5) {
                    $table_end .= '
                            <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                          </tr>
                          <tr>
                    ';
                } elseif ($shipment->booking_type_id == 3) {
                    $table_end .= '
                            <td class="border twice-top twice-bottom twice-left" colspan="2" rowspan="2" style="height: 32px;"></td>
                          </tr>
                          <tr>
                    ';
                }  elseif ($shipment->booking_type_id != 4) {
                    $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Payment Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->retail->payment_mode->name . '</strong></td>
                    ';
                } else {
                    $table_end .= '
                            <td class="color primary border twice-top twice-bottom twice-left"><strong>Charges Mode</strong></td>
                            <td class="border twice-top twice-bottom twice-left"><strong>' . $shipment->charges_mode->charges_mode . '</strong></td>
                    ';
                }

                if ($shipment->booking_type_id != 5 && $shipment->booking_type_id != 3) {
                    $table_end .= '
                              </tr>
                              <tr>
                                <td class="align-middle color primary border twice-top twice-bottom twice-left"><strong>Collection Amount</strong></td>
                    ';
                    $amount = $shipment->amount;

                    if ($shipment->booking_type_id == 4 && $shipment->charges_mode_id == 1) {
                        $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs '. $amount .'</strong></td>
                        ';
                    } else {
                        $table_end .= '
                                <td class="align-middle border twice-top twice-bottom twice-left"><strong>Rs '. $amount .'</strong></td>
                        ';
                    }
                }

                $table_end .= '
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>برائے مہربانی رائڈر / کورئیر کو کوئی اضافی پیسہ نہ دیں۔ اگر پارسل / پیکٹ خراب یا خراب حالت میں ہے تو ، براہ کرم اسے وصول نہ کریں۔</em></td>
                          </tr>
                          <tr>
                            <td colspan="8" class="text-center border twice-top urdu h5" dir="rtl"><em>ٹریکس لاجسٹک کا اس پارسل / پیکٹ میں موجود کسی آئٹم یا مواد سے کوئی تعلق نہیں ہے۔ ہم سامان ایک جگہ سے دوسری جگہ بھیجتے ہیں۔ اگر آپ کو اس بارے میں کوئی شکایت ہے تو ، براہ کرم متعلقہ آن لائن اسٹور / شپر  سے رابطہ کریں۔</em></td>
                          </tr>
                        </tbody>
                    </table>
                ';

                if ($shipment->booking_type_id != 4 && $shipment->charges_mode_id == 2 && $shipment->shipper_status_id == 1) {
                    $table_end .= '
                        <div class="void position-absolute m-auto text-center font-weight-bold">Void Air Waybill after Arrival</div>
                    ';
                }

                $table_end .= '
                      </div>
                ';

                $table_end .= '
                    <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Trax Copy</p></div><div class="col"><hr></div>
                  <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>
                ';

                if ($shipment->booking_type_id == 1 || $shipment->booking_type_id == 4 || $shipment->booking_type_id == 5) {
                    $shipment_details .= $table_start;

                    $item = $shipment->items->first();

                    $shipment_details .= '
                              <tr>
                                <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Item</strong></td>
                                <td class="color secondary border twice-top"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td>' . $item->quantity . '</td>
                                <td colspan="1" class="color secondary border twice-top"><strong>Piece(s)</strong></td>
                                <td>'. $shipment->pieces .'</td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                              </tr>
                    ';

                    $shipment_details .= $table_end;
                } else if ($shipment->booking_type_id == 2) {
                    $shipment_details .= $table_start;

                    $items = $shipment->items;

                    $item = $items[0];

                    $shipment_details .= '
                              <tr>
                                <td rowspan="2" class="align-middle color primary border twice-top twice-bottom"><strong>Delivery Item</strong></td>
                                <td class="color secondary border twice-top"><strong>Type</strong></td>
                                <td colspan="2" class="border twice-top">' . $item->product->product_name . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td>' . $item->quantity . '</td>
                                <td colspan="2" class="border twice-top"></td>
                              </tr>
                              <tr>
                                <td class="color secondary border twice-bottom"><strong>Description</strong></td>
                                <td colspan="6" class="border twice-bottom">' . $item->description . '</td>
                              </tr>
                    ';

                    $item = $items[1];

                    $shipment_details .= '
                        <tr>
                          <td rowspan="2"  style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="align-middle  border twice-top twice-bottom"><strong>Replacement Item</strong></td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-top"><strong>Type</strong></td>
                          <td colspan="2" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-top">' . $item->product->product_name . '</td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-top"><strong>Quantity</strong></td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important">' . $item->quantity . '</td>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" colspan="2" class="border twice-top"></td>
                        </tr>
                        <tr>
                          <td style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class=" border twice-bottom"><strong>Description</strong></td>
                          <td colspan="6" style="color:#ffffff !important; background-color: #000000 !important;border-color:#ffffff !important" class="border twice-bottom">' . $item->description . '</td>
                        </tr>
                    ';

                    $shipment_details .= $table_end;
                } else if ($shipment->booking_type_id == 3) {
                    $shipment_details .= $table_start;

                    $item_quantity = 0;
                    foreach ($shipment->items as $item) {
                        $item_quantity += $item->quantity;
                    }
                    $shipment_details .= '
                              <tr>
                                <td rowspan="1" class="align-middle color primary border twice-top twice-bottom"><strong>Try & Buy Products</strong></td>
                                <td class="color secondary border twice-top"><strong>Products</strong></td>
                                <td colspan="2" class="border twice-top">' . count($shipment->items) . '</td>
                                <td class="color secondary border twice-top"><strong>Quantity</strong></td>
                                <td>' . $item_quantity . '</td>
                                <td colspan="4" class=""></td>
                              </tr>
                        ';
                    $shipment_details .= ' <tr>
                                <td colspan="2" class="color primary border twice-top twice-bottom twice-right"><strong>Try & Buy Fees</strong></td>
                                <td colspan="6" colspan="4" class="border twice-top twice-bottom twice-right">' . $shipment->try_and_buy_fees . '</td>
                              </tr>';
                    $shipment_details .= $table_end;

                }

                if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                    $shipment_pieces = '';

                    foreach ($shipment->shipment_pieces as $piece){
                        $shipment_pieces .= '<table class="table table-sm table-bordered border twice">
                        <tbody><tr>';
                        $shipment_pieces .= '<td rowspan="3" class="text-center align-middle border twice-bottom twice-right"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">' . $print_details . '</td>';
                        $shipment_pieces .= '<td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                  <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($piece->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                  <span><strong>' . $piece->tracking_number . '</strong></span>
                                </td>
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                    <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($piece->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                                </td>
                                <td rowspan="1" class="color primary border twice-left"><strong>Origin</strong></td>
                                <td rowspan="1" class="border">' . $shipment->pickup_address->city->name . '</td>
                                <td rowspan="1" class="color primary border "><strong>Destination</strong></td>
                                <td rowspan="1" class="border">' . $shipment->consignee_city->name . '</td>
                                
                                <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->tracking_number, $generator::TYPE_CODE_128, 1.5, 45)) . '" class="d-block mx-auto">
                                <span><strong>' . $shipment->tracking_number . '</strong></span>
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right">
                                <img src="data:image/png;base64,' . DNS2D::getBarcodePNG($shipment->tracking_number, 'QRCODE', 4, 4) . '" class="d-block mx-auto">
                            </td>
                            <td rowspan="3" class="text-center align-middle pl-1 pr-1 border twice-bottom twice-left twice-right"><span class="piece_number"><strong>' . $piece->numbering. '/' .$shipment->pieces . '</strong></span>
                            </td>
                                </tr>
                                <tr>
                                <td class="color primary border twice-left"><strong>Shipper</strong></td>
                                <td class="border">'. $shipment->retail->shipper_name .'</td>
                                <td class="color primary border "><strong>Booking Date</strong></td>
                                <td class="border">'. $shipment->created_at .'</td>
</tr>
                              ';
                        $shipment_pieces .= '</tbody></table>
                    <div class="col m-1 row justify-content-center"><div class="col"><hr></div><div class=""><p>Trax Copy</p></div><div class="col"><hr></div>
                      <div class=""><i class="la la-cut la-rotate-180 align-middle"></i></div></div>';

                    }


                    $shipment_details .= $shipment_pieces;
                }
            }


            //airway_bill_end

        }

        $html .= $shipment_details;

        $html .= '
                </div>
        ';

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
