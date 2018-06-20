<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class DeliveryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function pending_delivery_index(Request $request){

        return view('admin.delivery.pending.index');
    }
    public function pending_list(Request $request)
    {
        $status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
//        $latest = DB::raw('(select remarks as latest_remarks,status_reason_id as latest_reason from shipments_journey leftjoin shipments on shipments.id = shipments_journey.shipment_id where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)');

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', function ($join) {
                $join->on('shipments.pickup_address_id', '=', 'usi.id')
                ->on('shipments.consignee_city_id', '=', 'usi.city_id');
            })
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));

            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
                ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date')
            ->whereIn('shipments.shipper_status_id',$status)
            ->groupBy('shipments.id');
        return Datatables::of($shipments)
            ->addColumn("action", function ($result) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id=''><i class='ft-plus-circle primary'></i> Dispute</a>                                         
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
        if($request->tracking != ''){
            $shipment = Shipment::where('tracking_number', $request->tracking);

            if($shipment->exists()){
                $shipment = $shipment->first();
                if($request->has('hub_id') ){
                    $hub_id = $shipment->consignee_city->hub_id;
                    if($request->hub_id == $hub_id){
                        $destination = $shipment->consignee_city->name;
                        $hub = City::find($shipment->consignee_city->hub_id)->name;
                        $service = $shipment->booking_type->booking_type;
                        return response()->json(['status'=>0,'shId'=>$shipment->id,'tracking_number'=>$shipment->tracking_number,'destination'=>$destination,'hub'=>$hub,'consignee_name'=>$shipment->consignee_name,'phone'=>$shipment->consignee_phone_number_1,'address'=>$shipment->consignee_address,'amount'=>$shipment->amount,'service_type'=>$service]);

                    }else{
                        return ['status' => 1, 'error' => 'Different hub, Select shipments from same hub!','hub_old'=>$request->hub_id,'newHub'=>$hub_id];
                    }

                }else{
                    $destination = $shipment->consignee_city->name;
                    $hub = City::find($shipment->consignee_city->hub_id)->id;
                    $service = $shipment->booking_type->booking_type;
                    return response()->json(['status'=>0,'shId'=>$shipment->id,'tracking_number'=>$shipment->tracking_number,'destination'=>$destination,'hub'=>$hub,'consignee_name'=>$shipment->consignee_name,'phone'=>$shipment->consignee_phone_number_1,'address'=>$shipment->consignee_address,'amount'=>$shipment->amount,'service_type'=>$service]);
                }
            }else{
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
            }


        }
    }
    public function create_delivery_note(Request $request){
        $shipments = explode(',',$request->shipment_ids);
        $count = count($shipments);
        $cod = Shipment::whereIn('id',$shipments)->sum('amount');
//        $dt = new \DateTime();
        $admin = Auth::id();
//        $deliveryno = $dt->format('YdmHs');
//        $deliveryno = $deliveryno.$admin;
//        return $request;
       $note = DeliveryNote::create([
//            'delivery_note_no'=>$deliveryno,
            'hub_id'=>$request->hub_id,
            'rider_id'=>$request->selected_rider_id,
            'route_id'=>$request->selected_route_id,
            'shipments_count'=>$count,
            'admin_id'=>$admin,
            'total_cod_amount'=>$cod
        ]);
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
                    'consignee_status_id'=>6,
                    'admin_id'=>$admin
                ]);
            }
        }
        return redirect()->route('admin.delivery.receive.index');
    }
    public function delivery_note_receive_index(){

        return view('admin.delivery.receive.index');
    }
    public function receive_deliveries_list(){
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','delivery_notes.created_at','delivery_notes.total_cod_amount as amount','delivery_notes.shipments_count'])
            ->where('delivery_notes.status',0)
            ->get();
        return Datatables::of($deliveries)


            ->editColumn('delivery_note', function ($deliveries) {
                return "<a href='#' class='printdeliverynote'><u>$deliveries->delivery_note</u></a>";
            })
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
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
            })
            ->make(true);

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
            ->where('delivery_notes.id',$id)
            ->get();
        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
               return "<a href='#' class='deliverynoterow'>Remove</a>";

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

    public function receive_delivery_status_view(Request $request,$id){
        $shipments_count = DeliveryNote::where('id',$id)->select('shipments_count')->first();
//        return $shipments_count;
        return view('admin.delivery.receive.add_status')->with(['delivery_note_id'=>$id,'shipments_count'=>$shipments_count->shipments_count]);
    }
    public function receive_delivery_status_list(Request $request,$id){
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns','dns.delivery_note_id','=','delivery_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->select(['delivery_notes.id as delivery_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_address as address','delivery_notes.total_cod_amount as amount','users.name as shipper','bt.booking_type as service_type','ss.name as current_status'])
            ->where('delivery_notes.id',$id)
            ->get();

        return Datatables::of($deliveries)

            ->addColumn('status', function ($deliveries) {
                $where = array(7,8,9,10,11,12,14,15,16,18);
                $statuses = ShipmentStatus::whereIn('id',$where)->get();
                $drops = '';
                foreach ($statuses as $status){
                    $drops .= '<option value="'.$status->id.'">'.$status->name.'</option>';
                }
                $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" placeholder="Select a Status"><option></option>'.$drops.'</select>';
                return $select;
            })
            ->addColumn('reason', function ($deliveries) {
                $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" placeholder="Select a Reason"><option></option></select>';
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
//        return $request;
        $shipments = explode(',',$request->shipment_ids);
        $delivery_note_id = $request->delivery_note_id;
        if($delivery_note_id != ''){
            foreach ($shipments as $shipment){
//                if($request->status_drop[$shipment] != null && $request->reason_drop[$shipment] != null && $request->remarks[$shipment] != null){
//                    if($request->status_drop[$shipment] == 6 || $request->status_drop[$shipment] == 18){
//                        ShipmentsJourney::create([
//                            'shipment_id'=>$shipment,
//                            'shipper_status_id'=>$request->status_drop[$shipment],
//                            'consignee_status_id'=>null,
//                            'status_reason_id'=>$request->reason_drop[$shipment],
//                            'remarks'=>$request->remarks[$shipment],
//                            'admin_id'=>Auth::id()
//                        ]);
//                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>$request->status_drop[$shipment]]);
//                    }else{
//                        ShipmentsJourney::create([
//                            'shipment_id'=>$shipment,
//                            'shipper_status_id'=>$request->status_drop[$shipment],
//                            'consignee_status_id'=>$request->status_drop[$shipment],
//                            'status_reason_id'=>$request->reason_drop[$shipment],
//                            'remarks'=>$request->remarks[$shipment],
//                            'admin_id'=>Auth::id()
//                        ]);
//                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>$request->status_drop[$shipment],'consignee_status_id'=>$request->status_drop[$shipment]]);
//                    }
//                }else
                    if($request->status_drop[$shipment] != null){
                    if($request->status_drop[$shipment] == 7 || $request->status_drop[$shipment] == 18){

                        ShipmentsJourney::create([
                            'shipment_id'=>$shipment,
                            'shipper_status_id'=>$request->status_drop[$shipment],
                            'consignee_status_id'=>null,
                            'status_reason_id'=>$request->reason_drop[$shipment],
                            'remarks'=>$request->remarks[$shipment],
                            'admin_id'=>Auth::id()
                        ]);
                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>$request->status_drop[$shipment]]);
                        DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
                    }elseif($request->status_drop[$shipment] == 14){
                        $parcel = Shipment::where('id',$shipment)->first();
                        if($parcel->booking_type_id == 2){
                            ShipmentsJourney::create([
                                'shipment_id'=>$shipment,
                                'shipper_status_id'=>30,
                                'consignee_status_id'=>30,
                                'status_reason_id'=>$request->reason_drop[$shipment],
                                'remarks'=>$request->remarks[$shipment],
                                'admin_id'=>Auth::id()
                            ]);
                            Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>30,'consignee_status_id'=>30]);
                            DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>2]);
                        }elseif($parcel->booking_type_id == 3){
                            if($parcel->package_type == 0){
                                ShipmentsJourney::create([
                                    'shipment_id'=>$shipment,
                                    'shipper_status_id'=>37,
                                    'consignee_status_id'=>37,
                                    'status_reason_id'=>$request->reason_drop[$shipment],
                                    'remarks'=>$request->remarks[$shipment],
                                    'admin_id'=>Auth::id()
                                ]);
                                Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>37,'consignee_status_id'=>37]);
                                DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>5]);
                            }else{
                                ShipmentsJourney::create([
                                    'shipment_id'=>$shipment,
                                    'shipper_status_id'=>36,
                                    'consignee_status_id'=>36,
                                    'status_reason_id'=>$request->reason_drop[$shipment],
                                    'remarks'=>$request->remarks[$shipment],
                                    'admin_id'=>Auth::id()
                                ]);
                                Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>36,'consignee_status_id'=>36]);
                                DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
                            }
                        }else{
                            ShipmentsJourney::create([
                                'shipment_id'=>$shipment,
                                'shipper_status_id'=>$request->status_drop[$shipment],
                                'consignee_status_id'=>$request->status_drop[$shipment],
                                'status_reason_id'=>$request->reason_drop[$shipment],
                                'remarks'=>$request->remarks[$shipment],
                                'admin_id'=>Auth::id()
                            ]);
                            Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>$request->status_drop[$shipment],'consignee_status_id'=>$request->status_drop[$shipment]]);
                            DeliveryNoteShipment::where(['delivery_note_id'=>$delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
                        }

                    }else{

                        ShipmentsJourney::create([
                            'shipment_id'=>$shipment,
                            'shipper_status_id'=>$request->status_drop[$shipment],
                            'consignee_status_id'=>$request->status_drop[$shipment],
                            'status_reason_id'=>$request->reason_drop[$shipment],
                            'remarks'=>$request->remarks[$shipment],
                            'admin_id'=>Auth::id()
                        ]);
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
                        'shipper_status_id'=>29,
                        'consignee_status_id'=>29,
                        'status_reason_id'=>null,
                        'remarks'=>null,
                        'admin_id'=>Auth::id()
                    ]);
                    Shipment::where('id',$shipment)->update(['received_amount'=>$parcel->amount,'shipper_status_id'=>29,'consignee_status_id'=>29]);
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
                        DeliveryNoteShipment::where(['delivery_note_id'=>$request->delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
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
                    DeliveryNoteShipment::where(['delivery_note_id'=>$request->delivery_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);
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
            $item_ids = explode(',',$request->trybuy_id_list);
            foreach ($item_ids as $item_id) {
                $product = ShipmentItem::where('id',$item_id)->first();
                Shipment::where('id',$product->id)->update(['received_amount'=>$cod]);
                ShipmentItem::where('id',$item_id)->update(['bought'=>1]);
                DeliveryNoteShipment::where('shipment_id', $product->id)->update(['status' => 5]);
            }
            return redirect()->back()->with('success','Try & Buy shipment updated');
        }

    }
    //verify delivery page
    public function receive_delivery_note_verify_view(Request $request,$id){

        $shipments_count = DeliveryNote::where('id',$id)->select('shipments_count')->first();
//        return $shipments_count;
        return view('admin.delivery.receive.verify_status')->with(['delivery_note_id'=>$id,'shipments_count'=>$shipments_count->shipments_count]);
    }
    public function receive_delivery_verify_status_list(Request $request,$id){
        $deliveries = DeliveryNote::join('delivery_note_shipments as dns','dns.delivery_note_id','=','delivery_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->select(['delivery_notes.id as delivery_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_address as address','delivery_notes.total_cod_amount as amount','users.name as shipper','bt.booking_type as service_type','ss.name as current_status'])
            ->where('delivery_notes.id',$id)
            ->get();

        return Datatables::of($deliveries)

            ->addColumn('status', function ($deliveries) {
                $where = array(7,8,9,10,11,12,14,15,16,18);
                $statuses = ShipmentStatus::whereIn('id',$where)->get();
                $drops = '';
                $shipment_data = Shipment::find($deliveries->shId);
                $status_id = $shipment_data->shipment_journey()->latest()->first();
//                $status = ShipmentStatus::where()
//                $verifyStatus = ShipmentsJourney::where('shipment_id',$deliveries->shId)->orderBy('created_at','desc')->first();
                foreach ($statuses as $status){
                    $drops .= '<option value="'.$status->id.'">'.$status->name.'</option>';
                }
                $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" placeholder="Select a Status"><option>'.$status_id.'</option>'.$drops.'</select>';
                return $select;
            })
            ->addColumn('reason', function ($deliveries) {
                $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" placeholder="Select a Reason"><option></option></select>';
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

        //delivery note verify
    public function receive_delivery_note_verify(Request $request){
    $note_id = $request->note_id;
    $dn = DeliveryNote::where('id',$note_id);
    if($dn->exists()){
        $shipmentStatus =  DeliveryNoteShipment::where(['delivery_note_id'=>$note_id,'status'=>0])->get();
        if($shipmentStatus->isEmpty()){
            $result = DeliveryNote::where('id',$note_id)->update(['status'=>1]);
            if($result){
                return ['status'=>0,'success'=>'Delivery note verified!'];
            }else{
                return ['status'=>1,'error'=>'Something went wrong try again!'];

            }
        }else{
            return ['status'=>1,'error'=>'All shipments are not updated yet, try again later!'];

        }
    }else{
        return ['status'=>1,'error'=>'Delivery note doesn\'t exist!','delivery_note_id'=>$note_id];

    }
    }

    //completed deliveries
    public function completed_deliveries_index(){
        return view('admin.delivery.complete.index');
    }

}
