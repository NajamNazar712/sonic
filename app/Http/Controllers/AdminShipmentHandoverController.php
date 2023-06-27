<?php

namespace App\Http\Controllers;

use App\HandoverShipmentPiece;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentStatus;
use Illuminate\Http\Request;
use App\Http\Models\City;
use App\Http\Models\Shipment;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\Handover\HandoverResponsibilities;
use App\Http\Models\Handover\HandoverStatus;
use App\Http\Models\Handover\HandoverShipmentsJourney;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Models\Admin\DeliveryLocationMappingKeyword;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class AdminShipmentHandoverController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function handover_create_index(){
        $hub=HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
        ->select(['c.id','c.name'])->groupBy('handover_responsibilities.hub_id')->get();

        // dd($hub);
        return view('admin.handover.index')->with(['hubs'=>$hub]);
    }

    public function handover_dropdown_val_fetch_from(Request $request){
        $value = $request->get('value');
        // $dependent = $request->get('dependent');-
        $dependent = "select From Person";
        $data = HandoverResponsibilities::where('hub_id',$value)->where('status',1)->get();
        $output = '<option value ="">' .ucfirst($dependent). '</option> ';
        foreach($data as $row){
            $output .= '<option value ="'.$row->id.'">' .$row->name. '</option> ';
        }
        echo $output;
    }


    //admin.handover.create.shipment_details
    public function arrival_bulk_shipment_details(Request $request){
      $shipment = Shipment::where('tracking_number', $request->tracking_number);
      
      if ($shipment->exists()) {
        $shipment = $shipment->first();
        $shipment_pieces1 = $shipment->pieces;
        $handover_shipment = HandoverShipments::where('shipment_id', $shipment->id)->whereIn('status', [1,3]);
        if($handover_shipment->exists()){
                return ['status' => 1, 'error' => 'Shipment is already in another Handover Note'];
            }
            $details = array();

            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['shipper'] = $shipment->user->name;
            $details['phone_number'] = $shipment->user->phone;
            $details['pickup_date'] = $shipment->pickup_date;
            $details['special_instructions'] = $shipment->special_instructions;
            ShipmentScanningJourneyController::add($shipment->id,26,1,Auth::id(),NULL,NULL);

            $check = DeliveryLocationMappingKeyword::pluck('keyword')->toArray();

            $msg_string = null;
            $str_arr = null;
            $str_arr = preg_split('/[\s.,-,_,*,?,<,>,!,@,#,$,%,^,&,(,)]+/', $shipment->consignee_address);
            // $str_arr = preg_split("/[ ,]+/", $shipment->consignee_address);
            foreach ($check as $nsa) {
                foreach ($str_arr as $arr_value) {
                    if (strtolower($nsa) == strtolower($arr_value)) {
                       
                            $msg_string = $arr_value;
                    }
                }
            }

            
            $delivery_area = null;
            if($msg_string != null){
                $found = DeliveryLocationMappingKeyword::join('delivery_location_mappings as dlm','delivery_location_mapping_keywords.mapping_id','=','dlm.id')
                            ->select('dlm.area_name as area_name','dlm.id')
                            ->where('delivery_location_mapping_keywords.keyword',$msg_string)
                            ->where('dlm.city_id',$shipment->consignee_city_id)
                            ->where('status',1);
                            if($found->exists()){
                              $found = $found->first();
                    $delivery_area = $found->id;
                    if($request->delivery_location_mapping != null){
                      if($request->delivery_location_mapping != $delivery_area){
                            return ['status' => 1, 'error' => 'Delivery Location is different'];
                      }
                    }
                }
                else{
                    $delivery_area = 0;
                    if($request->delivery_location_mapping != $delivery_area){
                      return ['status' => 1, 'error' => 'Delivery Location is different'];
                    }
                }
            }else{
              $delivery_area = 0;
              if($request->delivery_location_mapping != null){
                if($request->delivery_location_mapping != $delivery_area){
                  return ['status' => 1, 'error' => 'Delivery Location is different'];
                }
              }
            }

            $details['delivery_area'] = $delivery_area;
            
            if($shipment_pieces1 === 1)
            {
        
            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
            }
            else if($shipment_pieces1 > 1)
            {
              $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
              $details['pieces_count'] = $shipment->pieces;
              ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
              return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
            }
        }  
      else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    //receive
    public function handover_receive_index(){
      return view('admin.handover.receive');
    }

    public function arrival_bulk_shipment_details_receive(Request $request){
      $shipment = Shipment::where('tracking_number', $request->tracking_number);

      if ($shipment->exists()) {
        $shipment = $shipment->first();
        $shipment_pieces = $shipment->pieces;
          $handover_shipments = HandoverShipments::where('shipment_id',$shipment->id)->whereIn('status', [1,3]);
          $details = array();
          $details['id'] = $shipment->id;
          $details['tracking_number'] = $shipment->tracking_number;
          $details['shipper'] = $shipment->user->name;
          $details['phone_number'] = $shipment->user->phone;
          $details['pickup_date'] = $shipment->pickup_date;
          $details['special_instructions'] = $shipment->special_instructions;

          if($handover_shipments->exists() && $shipment_pieces == 1){
            ShipmentScanningJourneyController::add($shipment->id,27,1,Auth::id(),NULL,NULL);
            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
          }

          else if($shipment_pieces > 1)
            {
              $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
              $details['pieces_count'] = $shipment->pieces;
              ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
              return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
            }

          else 
          {
            return ['status' => 1, 'error' => 'Shipment not in Handover / not ready to update!'];
          }

         }
      else {
      return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
      }
}

//admin.handover.create.store
    public function bulk_handover_submit(Request $request){
      
        $shipment_ids = explode(',', $request->shipment_ids);
        // $hub_id = explode(',', $request->hub);
        $total= count($shipment_ids);
        if($total > 0){
            $handover = new Handover();
            $handover->created_by = Auth::id();
            $handover->from = $request->from;
            $handover->from_dept_area_desg = $request->from_dept_area_desg;
            $handover->to = $request->to;
            $handover->to_dept_area_desg = $request->to_dept_area_desg;
            $handover->hub = $request->hub_id;
            $handover->status_id = 1 ;
            $handover->shipments =$total;

            $handover->save();
            $handover_id = $handover->id;
            foreach ($shipment_ids as $shipment_id) {
                $handover_shipments = new HandoverShipments();
                $handover_shipments->handover_id = $handover_id;
                $handover_shipments->shipment_id = $shipment_id;
                $handover_shipments->status = 1;
                $handover_shipments->save();

                HandoverShipmentJourneyController::add($shipment_id,$handover_id,1);
            }

            return redirect()->route('admin.handover.create.index')->with('success','Handover Note created Successfully!');
        }
        return redirect()->back()->with('error', 'No shipments scanned!');

    }


    public function bulk_handover_submit_receive(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        $handover_ids = array();
        foreach ($shipment_ids as $shipment_id) {
            $handover_shipments = HandoverShipments::where('shipment_id', $shipment_id)->whereIn('status',[1,3]);
            if($handover_shipments->exists()){
                $handover_shipments = $handover_shipments->latest()->first();
                $handover_id = $handover_shipments->handover_id;
                $handover_shipments->status = 2;
                $handover_shipments->save();
                if(!in_array($handover_id, $handover_ids)){
                    $handover_ids[] = $handover_id;
                }

                HandoverShipmentJourneyController::add( $shipment_id,$handover_id,2);
            }
        }

        if(count($handover_ids) > 0){
            foreach ($handover_ids as $handover_id) {
                $not_updated_count = HandoverShipments::where('handover_id', $handover_id)->where('status', 1)->count();
                $received_count = HandoverShipments::where('handover_id', $handover_id)->whereIn('status', [2,3])->count();
                $handover = Handover::find($handover_id);
                if($not_updated_count == 0){
                    $handover->status_id = 4;
                }
                else{
                    $handover->status_id = 3;
                }
                $handover->received = $received_count;
                $handover->received_at = Carbon::now();
                $handover->received_by = Auth::id();
                $handover->save();
            }
        }
        return redirect()->route('admin.handover.receive.index')->with('success','Handover Note Received Successfully!');

    }

//list
    public function handover_list_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),383);
        $hubs = HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
            ->select(['c.id','c.name'])->groupBy('handover_responsibilities.hub_id')->get();

        $handover_admins = HandoverResponsibilities::select('id', 'name')->get();

        return view('admin.handover.list')->with(['hubs'=>$hubs, 'handover_admins'=>$handover_admins]);
    }

    public function handover_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),384);
        }
        $handover_list = Handover::leftjoin('cities as c','c.id','=','handovers.hub')
        ->leftjoin('admins as a', 'a.id', '=', 'handovers.created_by')
        ->leftjoin('admins as ad', 'ad.id', '=', 'handovers.received_by')
        ->leftjoin('handover_statuses as hs','hs.id','=','handovers.status_id')
        ->leftjoin('handover_responsibilities as hr','hr.id','=','handovers.from')
        ->leftjoin('handover_responsibilities as hor','hor.id','=','handovers.to')
        ->leftjoin('handover_shipments as hss','hss.handover_id','=','handovers.id')
        ->leftjoin('shipments as s','s.id','=','hss.shipment_id')
        ->select(['handovers.id as handover_id','a.name as created_by','ad.name as received_by',
        'hr.name as from','hor.name as to','c.name as hub',
        'handovers.shipments as shipment_count','handovers.shipments as total_shipments','hs.name as status',
        'handovers.received as received_shipments',
        'handovers.from_dept_area_desg','handovers.to_dept_area_desg','handovers.received_at','handovers.created_at',
        DB::raw('(select shipments - received_shipments from handovers where handovers.id= handover_id ) as remaining'),
        DB::raw('SUM(s.pieces) as shipment_pieces'),
      ])
      ->orderBy('handovers.id', 'DESC')
      ->groupBy('hss.handover_id');

        $datatable = Datatables::of($handover_list)

        ->editColumn('shipment_count', function($handover_list) {
              if ($handover_list->shipment_count != 0) {
                  return '<button class="btn btn-sm btn-outline-info align-middle">' . $handover_list->shipment_count . '</button>';
              }
              else {
                  return 0;
              }
            })

        ->addColumn('remaining_shipment_count', function($handover_list) {
            if ($handover_list->shipment_count != 0 && $handover_list->received_shipments != 0) {
                $remaining = $handover_list->shipment_count - $handover_list->received_shipments;
                if($remaining > 0){
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $remaining  . '</button>';
                }
                else{
                    return 0;
                }
            }
            else {
                return 0;
            }
        })

        ->editColumn('shipment_pieces', function($handover_list) {
          if ($handover_list->shipment_pieces > 0) {
            return '<button class="btn btn-sm btn-outline-info shipment_pieces align-middle">' . $handover_list->shipment_pieces . '</button>';
        } else {
            return '-';
        }
        });

        if ($tracking_number = $request->get('search_tracking')) {
            $datatable->where('s.tracking_number', '=', $tracking_number);
        }

        if ($hub = $request->get('search_hub')) {
            $datatable->where('handovers.hub', '=', $hub);
        }

        if ($from_admin = $request->get('search_from_admin')) {
            $datatable->where('handovers.from', '=', $from_admin);
        }

        if ($to_admin = $request->get('search_to_admin')) {
            $datatable->where('handovers.to', '=', $to_admin);
        }

        return  $datatable->make(true);

    }

    public function handover_shipments_count(Request $request){

        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->get();
        $shipments = array();
        if($handover_shipments->count() != 0){
            foreach ($handover_shipments as $handover_shipment){
                $shipment = Shipment::find($handover_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
                return ['status' => 0, 'success' => 'Handover Note Shipments', 'shipments' => $shipments];
        }else{
                return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }
    }

    public function handover_shipments_delivered(Request $request){

        $handover_ids = $request->input('handover_ids');
        //$handover_ids = explode(',', $request->handover_ids);

        foreach ($handover_ids as $handover_id) {
          $handover_request = Handover::find($handover_id);
  
          if ($handover_request->status_id == 2) {
            return ['status' => 1, 'error' => 'Handover No # '. $handover_request->id .' has already been modified'];
          }
        }
  
        foreach ($handover_ids as $handover_id) {
          $handover_request = Handover::where('id',$handover_id)->where('status_id', 1)->first();

          if($handover_request) {
            $handover_request->status_id = 2;
            $handover_request->save();
            $handover_shipments = $handover_request->handover_note_shipments;
            if(count($handover_shipments)){
                foreach ($handover_shipments as $shipment){
                    $shipment->status = 3;
                    $shipment->save();
                    HandoverShipmentJourneyController::add($shipment->shipment_id,$handover_request->id,3);
                }
            }
          }
          else{
            return ['status' => 1, 'error' => 'Handover note already updated!'];
          }
        }
  
        return ['status' => 0, 'success' => 'Handover Shipments has been Delivered'];
    }

    public function handover_print(Request $request) {
      $handover_note_ids = $request->ids;

      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
          
          $html='';
          $html = '
              <!doctype html>
              <html lang="en">
                <head>
                  <meta charset="utf-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                  <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                  <title>Handover Note</title>

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

                    td.try_and_buy span {
                      width: 22px;
                    }

                    td.try_and_buy span img {
                      display: block;
                      width: 100%;
                      margin: auto;
                      background: #c8c8c8;
                      border-radius: 25px;
                    }
                    
                    td.complaint {
                          background: #09262e !important;
                          color: #ffffff;
                     }
                    td.details_changed {
                          background: #000000 !important;
                          color: #ffffff;
                     }
                     div.page
                      {
                          page-break-after: always;
                          page-break-inside: avoid;
                      }
                  </style>
                </head>
                <body>
                  <div>
                  
          ';
          
      foreach ($handover_note_ids as $handover_note_id) {
          $html .= '<div class="page text-center">';
          $total_shipments = 0;
          $shipments = HandoverShipments::where('handover_id', $handover_note_id)->select('shipment_id','status')->orderBy('shipment_id','asc')->get();
          
          $shipment_details = '
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="color primary"><strong>S. No.</strong></td>
                          <td class="color primary"><strong>Shipment ID</strong></td>
                          <td class="color primary"><strong>Tracking No.</strong></td>
                          <td class="color primary"><strong>Shipper</strong></td>
                          <td class="color primary"><strong>Status</strong></td>
                        </tr>
          ';


          foreach ($shipments as $parcel) {
              $total_shipments++;
              $shipment = Shipment::find($parcel->shipment_id);
              $handover_id = HandoverShipments::find($parcel->handover_id);
              $class = null;
              $status_check = $parcel->my_status->name;
              // $status=$status_check->my_status->name;
              // if($status_check == 3){
              //     $status = 'Received';
              // }
              // else{
              //     $status = 'Not Received';
              // }

              $shipment_details_row_start = '
                        <tr>
                          <td class="'.$class.'">' . $total_shipments . '</td>
                          <td class="'.$class.'">' . $shipment->id . '</td>
                          <td class="'.$class.'">' . $shipment->tracking_number . '</td>
                          <td class="'.$class .'">' . $shipment->user->name . '</td>
                          <td class="'.$class .'">' . $status_check . '</td>
              ';

              $shipment_details .= $shipment_details_row_start;

           
          }
          $shipment_details .= '
                      </tbody>
                    </table>
          ';
          
          
         
          $handover_note_details = Handover::where('id', $handover_note_id)->first();
          $status = $handover_note_details->status->name;
          $main_details = '
                    <table class="table table-sm table-bordered border">
                      <tbody>
                        <tr>
                          <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="150" class="d-block mx-auto"></td>
                          <td class="text-center align-middle color primary"><strong>Handover Note</strong></td>
                          <td class="text-center align-middle color secondary">Created at ' . $handover_note_details->created_at . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Created By</strong></td>
                          <td>' . ucfirst(Auth::user()->name) . '</td>
                          <td colspan="2" rowspan="7" class="pl-1 pr-1 text-center align-middle">
                            <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($handover_note_id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                            <span><strong>' . str_pad($handover_note_id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                          </td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Total Shipments</strong></td>
                          <td>' . $handover_note_details->shipments . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Received Shipments</strong></td>
                          <td>' . $handover_note_details->received . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Received At</strong></td>
                          <td>' . $handover_note_details->received_at . '</td>
                        </tr>
                        <tr>
                          <td class="color secondary"><strong>Status</strong></td>
                          <td>' . $status . '</td>
                        </tr>
                      </tbody>
                    </table>
          ';
      
          $html .= $main_details;
          $html .= $shipment_details;

          $html .= '
          </div>
          ';
    
      }
      
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
     
    
      

      //$view[]=$html;
      return $html;
  }



//Responsible
    public function responsibles_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),385);
        $hubs = City::select(['id','name'])->where('hub',1)->get();
        return view('admin.handover.responsibles')->with(['hubs'=>$hubs]);
    }

    public function responsibles_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),386);
        }
        $responsibles_list = HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
        ->join('admins as a', 'a.id', '=', 'handover_responsibilities.created_by')
        ->leftjoin('admins as u', 'u.id', '=', 'handover_responsibilities.updated_by')
        ->select('handover_responsibilities.id as responsible_id','handover_responsibilities.name as name','c.name as hub','c.id as hub_id','a.name as created','u.name as updated','handover_responsibilities.status as status');

        $datatable = Datatables::of($responsibles_list)
        ->setRowAttr([
            'hub' => function ($data) {
                return $data->hub_id;
            }
        ])
            ->addColumn('status', function ($data){
                if($data->status == 0){
                    return 'Disable';
                }else{
                    return 'Enable';
                }
            })
            ->addColumn('action', function ($data){

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                        $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    if ($data->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    } else {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }

                    return $dropdown;

            });

        return  $datatable->make(true);

    }

    public function responsibles_add(Request $request){
        $responsibles = new HandoverResponsibilities();
        $responsibles->name = $request->name;
        $responsibles->hub_id = $request->hub;
        $responsibles->created_by = Auth::id();
        $responsibles->updated_by = Auth::id();
        $responsibles->status = 1;
        $responsibles->save();
        return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Added successfully!"]);
    }

    public function responsibles_status(Request $request){
        $id = $request->id;
        $status = $request->status;
        $responsible = HandoverResponsibilities::find($id);
        if(!$responsible){
            return response()->json(['status' => 1, 'error' => 'Not found!']);
        }

        if($status == 1){
            $responsible->status = 1;
        }else if($status == 0){
            $responsible->status = 0;
        }
        $responsible->save();

        return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);
    }

    public function responsibles_editview(Request $request){

        $responsible_id = HandoverResponsibilities::where('id',$request->id)->first();
        // $hub_id=HandoverResponsibilities::where('hub_id',$responsible_id->hub)->first();
        return response()->json(['status' => 1, 'responsible' => $responsible_id]);
    }
    public function responsibles_edit(Request $request){
        $responsible = HandoverResponsibilities::find($request->id);
         if($responsible){
             $responsible->name = $request->name;
             $responsible->hub_id = $request->hub;
             $responsible->updated_by = Auth::id();
             $responsible->save();
             return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Edited successfully!"]);
         }
         return redirect()->back()->with(['status'=>0,'error'=>"Responsible not found!"]);
    }

    public function handover_shipments_remaining(Request $request){
        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->where('status',1)->get();
        $shipments = array();
        if($handover_shipments->count() != 0){
            foreach ($handover_shipments as $handover_shipment){
                $shipment = Shipment::find($handover_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Handover Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }
    }

    // for Pieces Modal
    public function handover_shipments_pieces(Request $request){
        $handover_id = $request->input('id');
        $handover_shipments = HandoverShipments::where('handover_id', $handover_id)->get();
        $shipments = [];
        if($handover_shipments->count() != 0){
            foreach ($handover_shipments as $handover_shipment){
                $shipment = Shipment::where('id', $handover_shipment->shipment_id)
                ->first();
                if ($shipment) {
                $tracking_number['tracking_number'][] = $shipment->tracking_number;
                $pieces['pieces'][] = $shipment->pieces;
                }
            }
            return ['status' => 0, 'success' => 'Handover Note Shipments', 'tracking_number' => $tracking_number, 'pieces' => $pieces];
        }

        else
        {
            return ['status' => 0, 'success' => 'No Handover Note Shipments', 'shipments' => FALSE];
        }

      }

}
