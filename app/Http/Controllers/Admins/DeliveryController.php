<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\BanksList;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class DeliveryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function pending_delivery_index(Request $request){
        return view('admin.delivery.pending.index');
    }
    public function pending_list(Request $request)
    {
        $status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
        $normal = 2;
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                ->where('shipments_journey.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                ->where('sj.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
                ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','sj.created_at as arrival')

            ->whereRaw('IF (shipments.shipper_status_id = 2, (oc.hub_id = dc.hub_id), TRUE)')
            ->whereIn('shipments.shipper_status_id',$status)
            ->groupBy('shipments.id');

        return Datatables::of($shipments)
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        $older = Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                        return "<span class='danger font-weight-bold'>$older</span>";
                    } else {
                        return Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return Carbon::parse($shipments->arrival)->format('d/m/Y H:i A');
                }else{
                    return " - ";
                }
            })
            ->addColumn("action", function ($result) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item dispute_modal'><i class='ft-alert-circle primary'></i> Dispute</a>                                         
                                            </div></span>";
            })
            ->make(true);
    }
    public function delivery_note_index(){
        $riders = Rider::all()->where('status',1);
        $routes = Route::all()->where('status',1);
        return view('admin.delivery.note.index')->with(['riders'=>$riders,'routes'=>$routes]);
    }

    public function get_shipment_details(Request $request){
        $pending_status = array(2, 4, 6, 7, 8, 9, 13, 15);
        if($request->tracking != ''){
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id',$pending_status);
            $remarks = '';$status = '';
            if($shipment->exists()){
                $shipment = $shipment->first();
//                return $shipment->consignee_city_id;
                if(($shipment->consignee_city_id != $shipment->pickup_address->city_id) && $shipment->shipper_status_id == 2){

                        return ['status' => 1, 'error' => 'Cargo not arrived at destination center!'];


                }
                if($request->has('hub_id') ){
                    $hub_id = $shipment->consignee_city->hub_id;
                    if($request->hub_id == $hub_id){
                        $destination = $shipment->consignee_city->name;
                        $hub = City::find($shipment->consignee_city->hub_id)->name;
                        $service = $shipment->booking_type->booking_type;
                        $shipment_journey = ShipmentsJourney::where('shipment_id',$shipment->id);
                        if($shipment_journey->exists()){
                            $shipment_journey = ShipmentsJourney::where('shipment_id',$shipment->id)->select('shipper_status_id','remarks')->latest()->first();

                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                            $status_id = ($shipment_journey->shipper_status_id)? $shipment_journey->shipper_status_id: '';
                            if($status_id != ''){
                                $status_name = ShipmentStatus::where('id',$status_id)->select('name')->first();
                                $status = $status_name->name;
                            }else{
                                $status = ' - ';
                            }
                        }
                        return response()->json(['status'=>0,'shId'=>$shipment->id,'tracking_number'=>$shipment->tracking_number,'destination'=>$destination,'hub'=>$hub,'consignee_name'=>$shipment->consignee_name,'phone'=>$shipment->consignee_phone_number_1,'address'=>$shipment->consignee_address,'amount'=>$shipment->amount,'service_type'=>$service,'status'=>$status,'remarks'=>$remarks]);

                    }else{
                        return ['status' => 1, 'error' => 'Different hub, Select shipments from same hub!','hub_old'=>$request->hub_id,'newHub'=>$hub_id];
                    }

                }else{
                    $destination = $shipment->consignee_city->name;
                    $hub = City::find($shipment->consignee_city->hub_id)->id;
                    $service = $shipment->booking_type->booking_type;
                    $shipment_journey = ShipmentsJourney::where('shipment_id',$shipment->id);
                    if($shipment_journey->exists()){
                        $shipment_journey = $shipment_journey->select('shipper_status_id','remarks')->latest()->first();

                        $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                        $status_id = ($shipment_journey->shipper_status_id)? $shipment_journey->shipper_status_id: '';
                        if($status_id != ''){
                            $status_name = ShipmentStatus::where('id',$status_id)->select('name')->first();
                            $status = $status_name->name;
                        }else{
                            $status = ' - ';
                        }
                    }

                    return response()->json(['status'=>0,'shId'=>$shipment->id,'tracking_number'=>$shipment->tracking_number,'destination'=>$destination,'hub'=>$hub,'consignee_name'=>$shipment->consignee_name,'phone'=>$shipment->consignee_phone_number_1,'address'=>$shipment->consignee_address,'amount'=>$shipment->amount,'service_type'=>$service,'status'=>$status,'remarks'=>$remarks]);
                }
            }else{
                return ['status' => 1, 'error' => 'This Shipment is not ready for delivery yet or already in delivery note, please check tracking!'];
            }


        }
    }
    public function create_delivery_note(Request $request){
        $shipments = explode(',',$request->shipment_ids);
        $count = count($shipments);
        $cod = Shipment::whereIn('id',$shipments)->sum('amount');

        $admin = Auth::id();

       $note = DeliveryNote::create([
            'hub_id'=>$request->hub_id,
            'rider_id'=>$request->selected_rider_id,
            'route_id'=>$request->selected_route_id,
            'shipments_count'=>$count,
            'admin_id'=>$admin,
            'total_cod_amount'=>$cod
        ]);
//        session('delivery_note_print', $note);
        if($note){
            foreach ($shipments as $shipment){
                DeliveryNoteShipment::create([
                    'delivery_note_id'=>$note->id,
                    'shipment_id'=>$shipment
                ]);
                Shipment::where('id',$shipment)->update(['shipper_status_id'=>5,'consignee_status_id'=>5]);
                ShipmentsJourney::create([
                    'shipment_id'=>$shipment,
                    'shipper_status_id'=>5,
                    'consignee_status_id'=>5,
                    'admin_id'=>$admin,
                    'reference_1_id'=>$note->id,
                    'reference_2_id'=>$note->rider_id
                ]);

                NotificationsController::send(10, $note->id, $shipment);
                NotificationsController::send(11, $note->id, $shipment);
                NotificationsController::send(12, $note->id, $shipment);
            }
        }
        return redirect()->back()->with(['success'=>'Delivery note created successfully','print'=>$note->id]);
    }
    public function delivery_note_receive_index(){

        return view('admin.delivery.receive.index');
    }
    public function receive_deliveries_list(Request $request){
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','delivery_notes.created_at','delivery_notes.total_cod_amount as amount','delivery_notes.shipments_count'])
            ->where('delivery_notes.status',0);
        $datatables = Datatables::of($deliveries)


            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='#' class='printdeliverynote'><u>$deliveries->delivery_note</u></a>";
            })
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('created_at', function ($rider) {
                return $rider->created_at ? with(new Carbon($rider->created_at))->format('d/m/Y H:i:s A') : '';
            })
            ->addColumn("action", function ($result) {
                $route = route('admin.delivery.receive.update',['note'=>$result->delivery_note]);
                $verifyStatus = route('admin.delivery.receive.status.verify',['note'=>$result->delivery_note]);
                $statusUpdate = route('admin.delivery.receive.status',['id'=>$result->delivery_note]);
                $dropdown = "<span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='{$statusUpdate}' class='dropdown-item' data-target-id='{$result->delivery_note}' class=''><i class='ft-plus-circle primary'></i> Receive</a>
                                              <a href='{$route}' class='dropdown-item deliverynoteupdate' data-target-id='{$result->delivery_note}'><i class='ft-plus-circle primary'></i> Shift Shipment</a>";
                $statusCheck = DeliveryNoteShipment::where(['delivery_note_id'=>$result->delivery_note,'status'=>0])->get();
                if($statusCheck->isEmpty()){

                    $dropdown .= "<a href='{$verifyStatus}' class='dropdown-item' data-target-id='{$result->id}'><i class='ft-plus-circle primary'></i> Verify Statuses</a>";
                }

                $dropdown .="</div></span>";
                return $dropdown;
            });

            if ($tracking_number = $request->get('search_tracking')) {
                $datatables->join('delivery_note_shipments as dns', 'delivery_notes.id', '=', 'dns.delivery_note_id')
                    ->join('shipments as s', 'dns.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking_number);
            }
            if ($delivery_note_number = $request->get('delivery_note_number')) {
                $datatables->where('delivery_notes.id', '=', $delivery_note_number);
            }
            return $datatables->make(true);

    }
    public function receive_delivery_search(Request $request){
            $search = Shipment::where('tracking_number',$request->tracking);
            if($search->exists()) {
                $search = $search->first();
                $note = DeliveryNoteShipment::where('shipment_id', $search->id);

                if ($note->exists()) {
                    $note = $note->first();
                    return response()->json(['status' => 0, 'delivery_note' => $note->delivery_note_id]);
                } else {
                    return ['status' => 1, 'error' => 'No Shipment with given Tracking Number in delivery notes'];
                }
            }else{
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number Exist'];

            }
    }
    public function receive_delivery_update(Request $request,$id){
        return view('admin.delivery.receive.update')->with('delivery_note_id',$id);
    }
    public function receive_delivery_notes_list(Request $request,$id){
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns','dns.delivery_note_id','=','delivery_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->select(['delivery_notes.id as delivery_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address as address','delivery_notes.total_cod_amount as amount','bt.booking_type as service_type'])
            ->where('delivery_notes.id',$id);
        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
               return "<a href='javascript:void(0);' class='deliverynoterow'>Remove</a>";

            })
            ->make(true);

    }
    public function receive_delivery_remove(Request $request){
        $shipment = DeliveryNoteShipment::where('shipment_id',$request->shipment_id);
        if($shipment->exists()){
            $shipment = $shipment->first();
            $delivery_note = $shipment->delivery_note_id;
            $delivery = DeliveryNote::where('id',$delivery_note);
            if($delivery->exists()){
                $parcel = Shipment::where('id',$request->shipment_id);
                $parcel = $parcel->first();
                DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note,'shipment_id'=>$request->shipment_id])->delete();
                $delivery = $delivery->first();
                $count = $delivery->shipments_count;
                $cod = $delivery->total_cod_amount;
                $count = $count-1;
                $cod = $cod - $parcel->amount;
                DeliveryNote::where('id',$delivery_note)->update(['shipments_count'=>$count,'total_cod_amount'=>$cod]);
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>4]);
                return ['status' => 0, 'success' => 'Shipment is successfully removed'];
            }else{
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        }else{
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }
    public function received_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note</title>

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

                      .color {
                        color: #09262e !important;
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
                    <div class="p-1">
      ';
        $delivery_note = DeliveryNote::where('id',$request->id);
        if($delivery_note->exists()) {
            $total_shipments = 0;
            $total_cod_amount = 0;
            $shipments = DeliveryNoteShipment::where('delivery_note_id',$request->id)->select('shipment_id')->get();

            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Collect Amount</strong></td>
                            <td class="color primary"><strong>Sign</strong></td>
                          </tr>
        ';


            foreach ($shipments as $parcel) {
                $total_shipments++;
                $shipment = Shipment::find($parcel->shipment_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>Rs ' . number_format($shipment->amount) . '</td>
                            <td></td>
                          </tr>
            ';
                $total_cod_amount +=$shipment->amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id',$request->id)->first();
            $rider = Rider::where('id',$delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code .'( '.$delivery_note_details->route->start.' to '.$delivery_note_details->route->end.' )';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Delivery Note</strong></td>
                            <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($request->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 12, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name  . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total COD Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
        $html .= $main_details;
        $html .= $shipment_details;

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

        return $html;
    }

    public function receive_delivery_status_view(Request $request,$id){
        $note_data = DeliveryNote::where('id',$id)->first();
        if($note_data){

            return view('admin.delivery.receive.add_status')->with(['delivery_note_id'=>$id,'shipments_count'=>$note_data->shipments_count,'delivery_note_status'=>$note_data->status]);
        }else{
            return redirect()->back()->with('error','Delivery note not found!');
        }
    }
    public function receive_delivery_status_list(Request $request,$id){
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns','dns.delivery_note_id','=','delivery_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->select(['delivery_notes.id as delivery_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_address as address','shipments.amount as amount','users.name as shipper','bt.booking_type as service_type','ss.name as current_status'])
            ->where('delivery_notes.id',$id);

        return Datatables::of($deliveries)

            ->addColumn('status', function ($deliveries) {
                $where = array(7,8,9,10,11,12,14,15,16,18);
                $statuses = ShipmentStatus::whereIn('id',$where)->get();
                $drops = '';
                foreach ($statuses as $status){
                    $drops .= '<option value="'.$status->id.'">'.$status->name.'</option>';
                }
                $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" ><option></option>'.$drops.'</select>';
                return $select;
            })
            ->addColumn('reason', function ($deliveries) {
                $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" ><option></option></select>';
                return $reason;
            })
            ->addColumn('remarks', function ($deliveries) {
                $reason = '<input class="form-control form-control-sm" name="remarks['.$deliveries->shId.']" placeholder="Enter Remarks">';
                return $reason;
            })
            ->addColumn('action',function($deliveries){
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item clear'><i class='ft-rotate-cw primary'></i> Clear</a>                                         
                                            </div></span>";
            })
            ->make(true);
    }
    public function receive_delivery_reason(Request $request){
        $status_id = $request->status;
        $statuses = ShipmentStatus::find($status_id)->reasons()->select('id','name')->orderBy('name')->get();

        if(!$statuses->isEmpty()){
            return response()->json(['status'=>0,'reasons'=>$statuses]);
        }else{
            return ['status'=>1,'error'=>'No reasons are defined for this status!'];
        }

    }
    public function receive_delivery_status_submit(Request $request){
//            return $request;
        $shipments = explode(',',$request->shipment_ids);
        $delivery_note_id = $request->delivery_note_id;
        if($delivery_note_id != ''){
            foreach ($shipments as $shipment){
                $statusId = "reason_drop.$shipment";
                   $shipment_status = Shipment::where('id',$shipment)->first();
                    if($request->status_drop[$shipment] != null){
                    if($request->status_drop[$shipment] == 7 || $request->status_drop[$shipment] == 18){
                        if($shipment_status->shipper_status_id != $request->status_drop[$shipment]){
                            ShipmentsJourney::create([
                                'shipment_id'=>$shipment,
                                'shipper_status_id'=>$request->status_drop[$shipment],
                                'consignee_status_id'=>null,
                                'status_reason_id'=>($request->has($statusId)? $request->reason_drop[$shipment]:null),
                                'remarks'=>$request->remarks[$shipment],
                                'admin_id'=>Auth::id()
                            ]);
                        }

                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>$request->status_drop[$shipment]]);
                        DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
                    }elseif($request->status_drop[$shipment] == 14 || $request->status_drop[$shipment] == 16){
                        $parcel = Shipment::where('id',$shipment)->first();
                        if($parcel->booking_type_id == 2){
                            if($shipment_status->shipper_status_id != 30){
                            ShipmentsJourney::create([
                                'shipment_id'=>$shipment,
                                'shipper_status_id'=>30,
                                'consignee_status_id'=>30,
                                'status_reason_id'=>($request->has($statusId)? $request->reason_drop[$shipment]:null),
                                'remarks'=>$request->remarks[$shipment],
                                'admin_id'=>Auth::id()
                            ]);
                            }
                            Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>30,'consignee_status_id'=>30]);
                            DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>2]);
                        }elseif($parcel->booking_type_id == 3){
                            if($parcel->package_type == 0){
                                if($shipment_status->shipper_status_id != 36){
                                ShipmentsJourney::create([
                                    'shipment_id'=>$shipment,
                                    'shipper_status_id'=>36,
                                    'consignee_status_id'=>36,
                                    'status_reason_id'=>($request->has($statusId)? $request->reason_drop[$shipment]:null),
                                    'remarks'=>$request->remarks[$shipment],
                                    'admin_id'=>Auth::id()
                                ]);
                                }
                                Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>36,'consignee_status_id'=>36]);
                                DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>3]);
                            }else{
                                if($shipment_status->shipper_status_id != 36){
                                ShipmentsJourney::create([
                                    'shipment_id'=>$shipment,
                                    'shipper_status_id'=>36,
                                    'consignee_status_id'=>36,
                                    'status_reason_id'=>($request->has($statusId)? $request->reason_drop[$shipment]:null),
                                    'remarks'=>$request->remarks[$shipment],
                                    'admin_id'=>Auth::id()
                                ]);
                                }
                                Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>36,'consignee_status_id'=>36]);
                                DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>6]);
                            }
                            }else{
                            if($shipment_status->shipper_status_id != $request->status_drop[$shipment]){
                            ShipmentsJourney::create([
                                'shipment_id'=>$shipment,
                                'shipper_status_id'=>$request->status_drop[$shipment],
                                'consignee_status_id'=>$request->status_drop[$shipment],
                                'status_reason_id'=>($request->has($statusId)? $request->reason_drop[$shipment]:null),
                                'remarks'=>$request->remarks[$shipment],
                                'admin_id'=>Auth::id()
                            ]);
                            }
                            Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>$request->status_drop[$shipment],'consignee_status_id'=>$request->status_drop[$shipment]]);
                            DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>6]);
                        }

                    }else{
                        if($shipment_status->shipper_status_id != $request->status_drop[$shipment]) {
                            ShipmentsJourney::create([
                                'shipment_id' => $shipment,
                                'shipper_status_id' => $request->status_drop[$shipment],
                                'consignee_status_id' => $request->status_drop[$shipment],
                                'status_reason_id' => ($request->has($statusId)? $request->reason_drop[$shipment]:null),
                                'remarks' => $request->remarks[$shipment],
                                'admin_id' => Auth::id()
                            ]);
                        }
                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>$request->status_drop[$shipment],'consignee_status_id'=>$request->status_drop[$shipment]]);
                        DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
                    }
                }

            }
            return redirect()->back()->with('success','Statuses updated successfully!');
        }else{
            return redirect()->back()->with('error','Delivery note not found!');
        }
    }
    public function receive_delivery_status_delivered(Request $request){
//        return $request->shipment_ids;
        if(!empty($request->shipment_ids)){
            foreach ($request->shipment_ids as $shipment){
                $parcel = Shipment::where('id',$shipment)->first();
                if($parcel->booking_type_id == 2){

                    ShipmentsJourney::create([
                        'shipment_id'=>$shipment,
                        'shipper_status_id'=>30,
                        'consignee_status_id'=>30,
                        'status_reason_id'=>null,
                        'remarks'=>null,
                        'admin_id'=>Auth::id()
                    ]);
                    Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>30,'consignee_status_id'=>30]);
                    DeliveryNoteShipment::where(['delivery_note_id'=>$request->delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>2]);
                }elseif($parcel->booking_type_id == 3){
                    if($parcel->package_type == 0){
                        ShipmentsJourney::create([
                            'shipment_id'=>$shipment,
                            'shipper_status_id'=>37,
                            'consignee_status_id'=>37,
                            'status_reason_id'=>null,
                            'remarks'=>null,
                            'admin_id'=>Auth::id()
                        ]);
                        Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>37,'consignee_status_id'=>37]);
                        DeliveryNoteShipment::where(['delivery_note_id'=>$request->delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>3]);
                    }else{
                        ShipmentsJourney::create([
                            'shipment_id'=>$shipment,
                            'shipper_status_id'=>36,
                            'consignee_status_id'=>36,
                            'status_reason_id'=>null,
                            'remarks'=>null,
                            'admin_id'=>Auth::id()
                        ]);
                        Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>36,'consignee_status_id'=>36]);
                        DeliveryNoteShipment::where(['delivery_note_id'=>$request->delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>6]);
                    }

                }else{
                    ShipmentsJourney::create([
                        'shipment_id'=>$shipment,
                        'shipper_status_id'=>14,
                        'consignee_status_id'=>14,
                        'status_reason_id'=>null,
                        'remarks'=>null,
                        'admin_id'=>Auth::id()
                    ]);
                    Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>14,'consignee_status_id'=>14]);
                    DeliveryNoteShipment::where(['delivery_note_id'=>$request->delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>6]);
                }

            }
            return ['status'=>0,'success'=>'Shipments status Delivered updated!'];
        }else{
            return ['status'=>1,'error'=>'No Shipments selected'];
        }
    }


    //ajax function
    //status 1 -> update , status 1 -> regular , status 2 -> replacement, status 3 -> try & buy
    public function receive_delivery_status_check(Request $request){

        $note_id = $request->delivery_note_id;
        $shipments = DeliveryNoteShipment::where(['delivery_note_id'=>$note_id])->count();
        if($shipments >0 ){
            $replacements = DeliveryNoteShipment::where(['delivery_note_id'=>$note_id,'status'=>2])->get();
            foreach ($replacements as $shipment){
                $shipment_data =Shipment::where('id',$shipment->shipment_id);
                $data = $shipment_data->first();
                if($data->booking_type_id == 2){
                    $replacement_ids[] = $data->id;
                }
            }
            $trybuy = DeliveryNoteShipment::where(['delivery_note_id'=>$note_id,'status'=>3])->get();
            foreach ($trybuy as $try){
                $try_data =Shipment::where('id',$try->shipment_id);
                $trydata = $try_data->first();
                if($trydata->booking_type_id == 3){
                    if(!isset($trybuy_id)){
                        $trybuy_id = $trydata->id;
                    }
                }
            }
            if(!empty($replacement_ids)){
                return ['status'=>2,'success'=>'Shipment is replacement!','booking_type'=>2,'replacement'=>$replacement_ids];
            }elseif(!empty($trybuy_id)){
                return ['status'=>3,'success'=>'Shipment is try and buy!','booking_type'=>3,'try'=>$trybuy_id];
            }
        }else{
            return ['status'=>0,'error'=>'No shipments updated!'];
        }
    }
    public function receive_delivery_get_replacements(Request $request){
        $shipments = $request->replacements;
        $parcel = array();
        foreach ($shipments as $shipment) {
            $parcel[] = Shipment::select('id','tracking_number','booking_type_id')->where('id',$shipment)->first();
            foreach ($parcel as $p){
                $p['booking_type_id'] = $p->booking_type->booking_type;
            }
        }
        return ['status'=>0,'data'=>$parcel];
    }
    public function receive_delivery_replacements_submit(Request $request){
        $shipments = explode(',',$request->shipment_id_list);
        foreach ($shipments as $shipment){
            if($request->weight[$shipment] != '') {
                Shipment::where('id', $shipment)->update(['replacement_weight' => $request->weight[$shipment]]);
                DeliveryNoteShipment::where('shipment_id', $shipment)->update(['status' => 4]);
            }
        }
        return redirect()->back()->with(['success'=>'Selected Replacement\'s weight updated!']);
    }
    //try buy modal
    public function receive_delivery_get_trybuys(Request $request){
        $shipment = $request->trybuy;
        if(isset($shipment)) {
            $product = array();
            $amount = Shipment::where('id',$shipment)->select('amount')->first();
            $parcel = ShipmentItem::where('shipment_id', $shipment)->get();
            foreach ($parcel as $item) {
                $product[] = ['pid'=>$item->id,'type'=>$item->product->product_name,'description'=>($item->description == '')? ' - ':$item->description ,'price'=>$item->price];
//
            }
            return ['status'=>0,'data'=>$product,'total_cod'=>$amount->amount];
        }else{
            return ['status'=>1,'error'=>'No Shipment found'];
        }
    }
    public function receive_delivery_trybuys_submit(Request $request){

        if(!empty($request->trybuy_id_list)){
            $cod = $request->trybuy_cod;
            $checked = $request->item_checked;
            $unchecked = $request->item_unchecked;
            $item_ids = explode(',',$request->trybuy_id_list);
            foreach ($item_ids as $item_id) {
                ShipmentItem::where('id',$item_ids)->update(['bought'=>1]);
            }
            if($checked != $unchecked){
                Shipment::where('id',$request->trybuy_shipment_id)->update(['received_amount'=>$cod,'shipper_status_id'=>37,'consignee_status_id'=>37]);
                ShipmentsJourney::create([
                    'shipment_id'=>$request->trybuy_shipment_id,
                    'shipper_status_id'=>37,
                    'consignee_status_id'=>37,
                    'status_reason_id'=>null,
                    'remarks'=>null,
                    'admin_id'=>Auth::id()
                ]);
            }elseif($checked == $unchecked){
                Shipment::where('id',$request->trybuy_shipment_id)->update(['received_amount'=>$cod,'shipper_status_id'=>36,'consignee_status_id'=>36]);
            }

            DeliveryNoteShipment::where(['shipment_id'=>$request->trybuy_shipment_id,'delivery_note_id'=>$request->delivery_note_trybuy])->update(['status' => 5]);

            return redirect()->back()->with('success','Try & Buy shipment updated');
        }

    }
    //verify delivery page
    public function receive_delivery_note_verify_view(Request $request,$id){

        $note_data = DeliveryNote::where('id',$id)->first();

        return view('admin.delivery.receive.verify_status')->with(['delivery_note_id'=>$id,'shipments_count'=>$note_data->shipments_count,'delivery_note_status'=>$note_data->status]);
    }
    public function receive_delivery_verify_status_list(Request $request,$id){
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns','dns.delivery_note_id','=','delivery_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->select(['delivery_notes.id as delivery_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_address as address','shipments.amount as amount','users.name as shipper','bt.booking_type as service_type','ss.name as current_status','ss.id as current_status_id'])
            ->where('delivery_notes.id',$id);

        return Datatables::of($deliveries)

            ->addColumn('status', function ($deliveries) {
                $where = array(7,8,9,10,11,12,14,15,16,18,30,35,36,37);
                $statuses = ShipmentStatus::whereIn('id',$where)->get();
                $drops = '';

//                $shipment_data = Shipment::find($deliveries->shId);
//                $status_id = $shipment_data->shipment_journey()->latest()->first();
//                $status_data = ShipmentStatus::where('id',$status_id->shipper_status_id)->select('id','name')->first();
                $selected_status = '';
                foreach ($statuses as $status){
                    if($status->id == $deliveries->current_status_id){
                        $selected_status = 'selected';
                    }else{
                        $selected_status = '';
                    }
                    $drops .= '<option value="'.$status->id.'" '.$selected_status.'>'.$status->name.'</option>';
                }
                $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" placeholder="Select a Status">'.$drops.'</select>';
                return $select;
            })
            ->addColumn('reason', function ($deliveries) {
                $status_reason = '';
                $reason_name = '';
                $reason_id = '';
                $shipment_data = Shipment::find($deliveries->shId);
                $status_id = $shipment_data->shipment_journey()->latest()->first();
                $status_data = ShipmentStatus::where('id',$status_id->shipper_status_id)->select('id','name')->first();
                if($status_id->status_reason_id != ''){

                    $status_reason = ShipmentStatusReason::where('id',$status_id->status_reason_id)->first();
                    $reason_name = $status_reason->name;
                    $reason_id = $status_reason->id;
                }
                $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" placeholder="Select a Reason"><option value="'.$reason_id.'">'.$reason_name.'</option></select>';
                return $reason;
            })
            ->addColumn('remarks', function ($deliveries) {
                $shipment_data = Shipment::find($deliveries->shId);
                $status_id = $shipment_data->shipment_journey()->latest()->first();

                $reason = '<input class="form-control form-control-sm" name="remarks['.$deliveries->shId.']" placeholder="Enter Remarks" value="'.$status_id->remarks.'">';
                return $reason;
            })
            ->make(true);
    }

        //delivery note verify
//    public function receive_delivery_note_verify(Request $request){
//    $note_id = $request->note_id;
//    $dn = DeliveryNote::where('id',$note_id);
//        if($dn->exists()){
//            $shipmentStatus =  DeliveryNoteShipment::where(['delivery_note_id'=>$note_id,'status'=>0]);
//            if($shipmentStatus->exists()){
//                $admin = Auth::id();
//                $result = DeliveryNote::where('id',$note_id)->update(['updated_by'=>$admin,'status'=>1]);
//                if($result){
//                    return ['status'=>0,'success'=>'Delivery note verified!'];
//                }else{
//                    return ['status'=>1,'error'=>'Something went wrong try again!'];
//
//                }
//            }else{
//                return ['status'=>1,'error'=>'All shipments are not updated yet, try again later!'];
//
//            }
//        }else{
//            return ['status'=>1,'error'=>'Delivery note doesn\'t exist!','delivery_note_id'=>$note_id];
//
//        }
//    }

    public function receive_delivery_verify_status_submit(Request $request){

            $shipments = explode(',',$request->shipment_ids);
            $delivery_note_id = $request->delivery_note_id;
            $shipment_count = 0;
            $dispute_shipments = array();
            $delivered_status_array = array(14,16,30,36,37);
            if($delivery_note_id != ''){
                foreach ($shipments as $shipment){

                    if($request->status_drop[$shipment] != null) {
                        $reasonId = "reason_drop.$shipment";
                        $shipper_status_id = Shipment::where('id', $shipment)->select('shipper_status_id')->first();
                        if($shipper_status_id->shipper_status_id != $request->status_drop[$shipment]){
                            if ($request->status_drop[$shipment] == 7 || $request->status_drop[$shipment] == 18) {

                                ShipmentsJourney::create([
                                    'shipment_id' => $shipment,
                                    'shipper_status_id' => $request->status_drop[$shipment],
                                    'consignee_status_id' => null,
                                    'status_reason_id' => ($request->has($reasonId)? $request->reason_drop[$shipment]:null),
                                    'remarks' => $request->remarks[$shipment],
                                    'admin_id' => Auth::id()
                                ]);
                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment]]);
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                            } elseif (in_array($request->status_drop[$shipment],$delivered_status_array)) {
                                $parcel = Shipment::where('id', $shipment)->first();
                                if ($parcel->booking_type_id == 2) {
                                    ShipmentsJourney::create([
                                        'shipment_id' => $shipment,
                                        'shipper_status_id' => 30,
                                        'consignee_status_id' => 30,
                                        'status_reason_id' => ($request->has($reasonId)? $request->reason_drop[$shipment]:null),
                                        'remarks' => $request->remarks[$shipment],
                                        'admin_id' => Auth::id()
                                    ]);
                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 30, 'consignee_status_id' => 30]);
                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 2]);
                                } elseif ($parcel->booking_type_id == 3) {
                                    if ($parcel->package_type == 0) {
                                        ShipmentsJourney::create([
                                            'shipment_id' => $shipment,
                                            'shipper_status_id' => 36,
                                            'consignee_status_id' => 36,
                                            'status_reason_id' => ($request->has($reasonId)? $request->reason_drop[$shipment]:null),
                                            'remarks' => $request->remarks[$shipment],
                                            'admin_id' => Auth::id()
                                        ]);
                                        Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 3]);
                                    } else {
                                        ShipmentsJourney::create([
                                            'shipment_id' => $shipment,
                                            'shipper_status_id' => 36,
                                            'consignee_status_id' => 36,
                                            'status_reason_id' => ($request->has($reasonId)? $request->reason_drop[$shipment]:null),
                                            'remarks' => $request->remarks[$shipment],
                                            'admin_id' => Auth::id()
                                        ]);
                                        Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => 36, 'consignee_status_id' => 36]);
                                        DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 6]);
                                    }
                                } else {
                                    ShipmentsJourney::create([
                                        'shipment_id' => $shipment,
                                        'shipper_status_id' => $request->status_drop[$shipment],
                                        'consignee_status_id' => $request->status_drop[$shipment],
                                        'status_reason_id' => ($request->has($reasonId)? $request->reason_drop[$shipment]:null),
                                        'remarks' => $request->remarks[$shipment],
                                        'admin_id' => Auth::id()
                                    ]);
                                    Shipment::where('id', $shipment)->update(['received_amount' => $parcel->amount, 'shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                                    DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                }

                                AdminFinanceController::add_payment($shipment, 0);
                            } else {

                                ShipmentsJourney::create([
                                    'shipment_id' => $shipment,
                                    'shipper_status_id' => $request->status_drop[$shipment],
                                    'consignee_status_id' => $request->status_drop[$shipment],
                                    'status_reason_id' => ($request->has($reasonId)? $request->reason_drop[$shipment]:null),
                                    'remarks' => $request->remarks[$shipment],
                                    'admin_id' => Auth::id()
                                ]);
                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
                                DeliveryNoteShipment::where(['delivery_note_id' => $delivery_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                            }
                            $dispute_shipments[] = $shipment;
                        }
                        else {
                            if (in_array($shipper_status_id->shipper_status_id, [14, 16, 30, 36, 37])) {
                                $parcel = Shipment::find($shipment);
                                if ($parcel->booking_type_id == 2) {
                                    ShipmentChargesController::replacement($shipment);
                                }
                                else if ($parcel->booking_type_id == 3) {
                                    ShipmentChargesController::try_and_buy($shipment);
                                }

                                AdminFinanceController::add_payment($shipment, 0);
                            }
                        }
                    }//main if condition

                }
                if(!empty($dispute_shipments)){
                    DisputeController::add_delivery_wrong_status_dispute($delivery_note_id,$dispute_shipments);
                }
                $dncc_status = array(14,16,30,36,37);
                $shipment_ids = DeliveryNoteShipment::where('delivery_note_id',$delivery_note_id)->select('shipment_id')->get();
                $filtered_shipments = Shipment::whereIn('id',$shipment_ids)->whereIn('shipper_status_id',$dncc_status);
                $dncc_amount = $filtered_shipments->sum('received_amount');
                $delivered_shipments = $filtered_shipments->count();
                DeliveryNote::where('id',$delivery_note_id)->update(['delivered_shipments'=>$delivered_shipments,'updated_by'=>Auth::id(),'received_cod_amount'=>$dncc_amount,'status'=>1]);

                NotificationsController::send(13, $delivery_note_id);
                NotificationsController::send(14, $delivery_note_id);
                return redirect()->back()->with('success','Delivery Note verified and updated successfully!');
            }else{
                return redirect()->back()->with('error','Delivery note not found!');
            }


    }
    //print dncc
    public function dncc_print(Request $request){

            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

            $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note</title>

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

                      .color {
                        color: #09262e !important;
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
                    <div class="p-1">
      ';
            $delivery_note = DeliveryNote::where('id',$request->id);
            if($delivery_note->exists()) {
                $total_shipments = 0;
                $total_cod_amount = 0;
                $dncc_status = array(14,16,30,36,37);
                $shipment_ids = DeliveryNoteShipment::where('delivery_note_id',$request->id)->select('shipment_id')->get();
                $filtered_shipments = Shipment::whereIn('id',$shipment_ids)->whereIn('shipper_status_id',$dncc_status)->get();
                //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
                $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collect Amount</strong></td>
                          </tr>
        ';


                foreach ($filtered_shipments as $shipment) {
                    $total_shipments++;
//                    $shipment = Shipment::find($parcel->shipment_id);

                    $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->user->name . ' | '. $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '') .'</td>
                            <td>' . (($shipment->booking_type_id == 2)? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                            
                          </tr>
            ';
                    $total_cod_amount +=$shipment->received_amount;
                    $shipment_details .= $shipment_details_row_start;
                }
                $shipment_details .= '
                        </tbody>
                      </table>
        ';
                $delivery_note_details = DeliveryNote::where('id',$request->id)->first();
                $rider = Rider::where('id',$delivery_note_details->rider_id)->first();
                $city_name = $delivery_note_details->hub->name;
                $rider_name = $rider->name;
                $category = $rider->rider_category->name;
                $route_name = $delivery_note_details->route->code .' ('.$delivery_note_details->route->start.' to '.$delivery_note_details->route->end.')';
                $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>
                            <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . $delivery_note_details->id . '</td>
                          </tr>
                          <tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($request->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 12, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name  . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
                $html .= $main_details;
                $html .= $shipment_details;

            }

//        return $shipments;


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

            return $html;


    }
    //completed deliveries
    public function completed_deliveries_index(){
        return view('admin.delivery.complete.index');
    }

    public function completed_receive_deliveries_list(){
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->leftjoin('admins as ub','ub.id','=','delivery_notes.updated_by')
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.id as hub_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','ub.name as updated_by','delivery_notes.updated_at as updated_at','delivery_notes.delivered_shipments','delivery_notes.created_at','delivery_notes.total_cod_amount as amount','delivery_notes.shipments_count'])
            ->where('delivery_notes.status',1)
            ->where('delivery_notes.dncc_status',0);
        return Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='#' class='printdeliverynote'><u>$deliveries->delivery_note</u></a><br><a href='#' class='printDNCC'><u>DNCC</u></a>";
            })
            ->setRowAttr([
                'data-hub' => function($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('created_at', function ($rider) {
                return $rider->created_at ? with(new Carbon($rider->created_at))->format('d/m/Y H:i:s A') : '';
            })
            ->editColumn('updated_at', function ($rider) {
                return $rider->updated_at ? with(new Carbon($rider->updated_at))->format('d/m/Y H:i:s A') : '';
            })
            ->make(true);

    }
    //for ajax select dncc
    public function completed_deliveries_selected_dncc(Request $request){
        $note_ids = explode(',',$request->delivery_note_ids);
//        return $note_ids;
        $updated = DeliveryNote::where('dncc_status',1)->whereIn('id',$note_ids)->exists();
        if(!$updated){
//            dd($updated);
//            return 132;
            session(['dncc_ids'=> $note_ids]);
            $delivery_note = DeliveryNote::find($note_ids[0]);
            $hub_name = $delivery_note->hub->name;
            $banks_list = BanksList::where(['affiliate'=>1,'status'=>1])->select('id','name')->get();
            return view('admin.delivery.complete.sdn_create')->with(['hub_name'=>$hub_name,'banks_list'=>$banks_list,'dncc_ids'=>session('dncc_ids')]);
        }else{
            return redirect(route('admin.delivery.sdn.index'))->with('error','SDN already created!');
        }
    }
    public function get_sdn_list(Request $request){
        $dncc_ids = session('dncc_ids');
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->select(['delivery_notes.id as delivery_note_id','oc.id as hub_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','delivery_notes.received_cod_amount','delivery_notes.shipments_count','delivery_notes.delivered_shipments'])
            ->whereIn('delivery_notes.id',$dncc_ids);
        return Datatables::of($deliveries)
            ->setRowAttr([
                'data-hub' => function($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('expense',function($deliveries){
                return "<input id='expense' class='form-control expense numeric' placeholder='Expense' name='expense[{$deliveries->delivery_note_id}]'>";
            })
            ->editColumn('net_amount',function($deliveries){
                return "<input class='form-control net_amount' readonly placeholder='Net Amount' name='net_amount[{$deliveries->delivery_note_id}]'>";
            })
            ->addColumn('remarks', function ($deliveries) {
                $reason = '<input class="form-control" name="remarks['.$deliveries->delivery_note_id.']" placeholder="Enter Remarks" data-rule-required="true" data-msg-required="This field is required">';
                return $reason;
            })
            ->make(true);
    }
    public function create_sdn_submit(Request $request){
        if($request->sdn_hub_id){
                $dncc_ids = explode(',',$request->sdn_dncc_ids);
                $check_status = DeliveryNote::whereIn('id',$dncc_ids)->where('dncc_status',1)->exists();

                if(!$check_status) {
                    $sdn_id = StationDepositNote::create([
                        'hub_id' => $request->sdn_hub_id,
                        'dncc_count' => $request->sdn_count,
                        'sdn_delivered_shipments' => $request->sdn_delivered_shipments,
                        'sdn_amount' => $request->total_dncc_amount,
                        'sdn_expense' => $request->total_expenses,
                        'sdn_net_amount' => $request->total_amount,
                        'deposited_by' => Auth::id(),
                        'banks_list_id' => $request->bank_select
                    ]);
                    foreach ($dncc_ids as $dncc) {
                        DeliveryNoteStationDepositNote::create([
                            'station_deposit_note_id' => $sdn_id->id,
                            'delivery_note_id' => $dncc
                        ]);
                        DeliveryNote::where('id', $dncc)->update(['expense' => $request->expense[$dncc], 'net_amount' => $request->net_amount[$dncc], 'remarks' => $request->remarks[$dncc], 'dncc_status' => 1]);
                    }

                    return redirect(route('admin.delivery.sdn.index'));
                }else{
                    return redirect(route('admin.delivery.sdn.index'))->with('error','SDN already created!');
                }
            }
    }
    public function sdn_view(Request $request){
        return view('admin.delivery.sdn.index');
    }
    public function sdn_list(Request $request){
        $sdn = StationDepositNote::
        join('cities AS oc', 'station_deposit_notes.hub_id', '=', 'oc.id')
            ->join('admins','admins.id','=','station_deposit_notes.deposited_by')
            ->join('banks_lists','banks_lists.id','=','station_deposit_notes.banks_list_id')
            ->select(['station_deposit_notes.id as sdn','station_deposit_notes.id as sdn_id','oc.name as hub','station_deposit_notes.dncc_count','station_deposit_notes.sdn_delivered_shipments','station_deposit_notes.sdn_amount','station_deposit_notes.sdn_expense','station_deposit_notes.sdn_net_amount','admins.name as deposited_by','station_deposit_notes.created_at','station_deposit_notes.deposit_slip','station_deposit_notes.status','banks_lists.name as bank']);
        return Datatables::of($sdn)
            ->editColumn('sdn', function ($sdn) {
                return "<a href='#' class='printSDN'><u>{$sdn->sdn_id}</u></a>";
            })
            ->editColumn('created_at', function ($sdn) {
                return $sdn->created_at ? with(new Carbon($sdn->created_at))->format('d/m/Y H:i:s A') : '';
            })
            ->addColumn('deposit_slip',function ($sdn){
                if($sdn->deposit_slip != null){
                    $img = asset('uploads/sdn/' . $sdn->deposit_slip);
                    return "<a href='{$img}' target='_blank'>Deposit Slip</a>";

                }else{
                    return "-";
                }
            })
            ->addColumn("action", function ($result) {
                $route = route('admin.delivery.sdn.details',['id'=>$result->sdn_id]);
                $dropdown = "<span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='{$route}' class='dropdown-item' data-target-id='{$result->sdn_id}' class=''><i class='ft-plus-circle primary'></i> Details</a>";
                if($result->status == 0){

                    $dropdown .=  "<a href='#' class='dropdown-item' data-target-id='{$result->sdn_id}' class='' data-target='#uploadDepositSlip' data-toggle='modal'><i class='ft-plus-circle primary'></i> Upload Deposit Slip</a></div></span>";
                }

                return $dropdown;
            })
            ->editColumn('status', function ($sdn) {
                return ($sdn->status == 1)? 'Deposited': 'Created';
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('deposited', $keyword) !== FALSE) {
                    $query->where('station_deposit_notes.status', '=', 1);
                }
                else if (strpos('created', $keyword) !== FALSE) {
                    $query->where('station_deposit_notes.status', '=', 0);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }
    public function sdn_details(Request $request,$id){
        return view('admin.delivery.sdn.details')->with('sdn_id',$id);
    }
    public function sdn_details_ajax(Request $request,$id){
        $deliveries = StationDepositNote::
        join('delivery_note_station_deposit_notes as dnsdn','dnsdn.station_deposit_note_id','=','station_deposit_notes.id')
            ->join('delivery_notes','delivery_notes.id','=','dnsdn.delivery_note_id')
            ->join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->select(['delivery_notes.id as dncc','oc.id as hub_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','delivery_notes.received_cod_amount','delivery_notes.shipments_count','delivery_notes.delivered_shipments','delivery_notes.expense','delivery_notes.net_amount','delivery_notes.remarks'])
            ->where('station_deposit_notes.id',$id);
        return Datatables::of($deliveries)

            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }
    public function sdn_deposit_slip(Request $request){
//        return $request;
        $messages = [
            'deposit_slip.required' => 'No Image file selected!.',
            'deposit_slip.mimes' => 'Image file not supported!.',
            'deposit_slip.size' => 'Image file size exceded!.',
            ];
        $validation = [
            'deposit_slip' => 'required | mimes:jpeg,png,jpg | max:2048',
        ];
        $validate = Validator::make($request->all(),$validation,$messages);

        if ($validate->fails()) {
            return response()->json(['status' => 0, 'error' => $validate->errors()]);
        }
        if($request->has('deposit_slip')){
        $image = $request->file('deposit_slip');
        $imageName = $image->getClientOriginalName();
        $image_size = $image->getClientSize();

        $imageName = explode('.', $imageName);
        $random = rand(1000, 100000);
        $now = Carbon::now();
        $time = $now->year . '_' . $now->month;
        $slip = $time . $random . Auth::id() . '.' . $imageName[1];
        $image->move(public_path('uploads/sdn'), $slip);

        $imageUpload = StationDepositNote::find($request->sdn_id);
        $imageUpload->deposit_slip = $slip;
        $imageUpload->status = 1;
        $imageUpload->save();
        return response()->json(['status' => 1, 'success' => 'Deposit Slip uploaded successfully']);
        }else{
            return response()->json(['status' => 0, 'error' => 'No image selected!']);
        }
    }
    public function sdn_deposit_slip_print(Request $request){
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note</title>

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

                      .color {
                        color: #09262e !important;
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
                    <div class="p-1">
      ';

        $sdn = StationDepositNote::where('id',$request->id);
        if($sdn->exists()) {
            $total_dncc = 0;

//            $shipments = DeliveryNoteShipment::where('delivery_note_id',$request->id)->select('shipment_id')->get();
            $dncc_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id',$request->id)->select('delivery_note_id')->get();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>DN No.</strong></td>
                            <td class="color primary"><strong>Rider Name</strong></td>
                            <td class="color primary"><strong>Route</strong></td>
                            <td class="color primary"><strong>Total No of Shipments</strong></td>
                            <td class="color primary"><strong>No of Delivered Shipments</strong></td>
                            <td class="color primary"><strong>Collected Amount</strong></td>
                            <td class="color primary"><strong>Expense</strong></td>
                            <td class="color primary"><strong>Net Amount</strong></td>
                          </tr>
        ';


            foreach ($dncc_ids as $dncc) {
                $total_dncc++;
                $dncc_note = DeliveryNote::find($dncc->delivery_note_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_dncc . '</td>
                            <td>' . $dncc_note->id . '</td>
                            <td>' . $dncc_note->rider->name. '</td>
                            <td>' . $dncc_note->route->code .'( '.$dncc_note->route->start.' to '.$dncc_note->route->end.' )' . '</td>
                            <td>' . $dncc_note->shipments_count . '</td>
                            <td>' . $dncc_note->delivered_shipments . '</td>
                            <td>Rs ' . number_format($dncc_note->received_cod_amount) . '</td>
                            <td>Rs ' . number_format($dncc_note->expense) . '</td>
                            <td>Rs ' . number_format($dncc_note->net_amount) . '</td>
                            
                          </tr>
            ';

                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $station_note_details = StationDepositNote::where('id',$request->id)->first();
            $city_name = $station_note_details->hub->name;
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Station Deposit Note</strong></td>
                            <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $city_name . '</td>
                            <td rowspan="7" class="text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($request->id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 12, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>                         
                          <tr>
                            <td class="color secondary"><strong>Total DNCC Amount</strong></td>
                            <td>Rs ' . number_format($station_note_details->sdn_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Expenses</strong></td>
                            <td>' . number_format($station_note_details->sdn_expense) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Net Amount</strong></td>
                            <td>' . number_format($station_note_details->sdn_net_amount) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Bank Name</strong></td>
                            <td>' . $station_note_details->bank->name . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;

        }

//        return $shipments;


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

        return $html;
    }

}
