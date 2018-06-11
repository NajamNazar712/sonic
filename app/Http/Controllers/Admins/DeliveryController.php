<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
    public function pending_list(Request $request){
        $status = array(2,4,6,7,8,9,12,15);
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
//            ->leftJoin('shipments_journey as sj', function ($join) {
//                $join->on('sj.shipment_id', '=', 'shipments.id')
//                    ->where('sj.id')->latest();
//            })
//            ->leftJoin('shipments_journey as sj','sj.shipment_id','=','shipments.id')
                ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status')
            ->whereIn('shipments.shipper_status_id',$status)

            ->get();
        return Datatables::of($shipments)
            ->addColumn("action", function ($result) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> Dispute</a>                                         
                                            </div></span>";
            })
            ->make(true);
    }
    public function delivery_note_index(){
        $riders = Rider::all()->where('status',1);
        $routes = Route::all()->where('status',1);
        return view('admin.delivery.note.index')->with(['riders'=>$riders,'routes'=>$routes]);
    }
//    public function get_shipment_info(Request $request){
////        return $request->tracking;
//        if($request->tracking != ''){
//            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
////                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
////                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
//                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
//                ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
////                ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
//                ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
////            ->join('shipments_journey sj','shipments.id','=','sj.shipment_id')
//
//                ->select('shipments.id as shId','shipments.tracking_number','dc.name as destination','h.id as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address as address','shipments.amount','bt.booking_type as service_type')
//                ->where('shipments.tracking_number',$request->tracking)
//                ->get();
//            return response()->json($shipments);
//        }
//    }
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
                Shipment::where('id',$shipment)->update(['shipper_status_id'=>5]);
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
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','delivery_notes.created_at','delivery_notes.total_cod_amount','delivery_notes.shipments_count'])
            ->get();
        return Datatables::of($deliveries)


            ->editColumn('delivery_note', function ($deliveries) {
                //$printreceive = route('admin.delivery.receive.print');
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
                $dropdown = "<span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='{$route}' class='dropdown-item' data-target-id='{$result->delivery_note}' class='deliverynoteupdate'><i class='ft-plus-circle primary'></i> Receive</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}'><i class='ft-plus-circle primary'></i> Shift Shipment to other DN</a>
                                              <a href='#' class='dropdown-item' data-target-id='{$result->id}'><i class='ft-plus-circle primary'></i> Verify Statuses</a>";

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
        //$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
//       return $request->ids;
        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

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

//        foreach($request->ids as $id) {
//            $pickup_note = PickupNote::find($id);
//
//            $rider = Rider::find($pickup_note->rider_id);
//            $route = $rider->route;
//
//            $html .= '
//                      <table class="table table-sm table-bordered border">
//                        <tbody>
//                          <tr>
//                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
//                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
//                            <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now()->format('d/m/Y H:i A') . '</td>
//                          </tr>
//                          <tr>
//                            <td class="color secondary"><strong>Rider Name</strong></td>
//                            <td>' . $rider->name . '</td>
//                            <td rowspan="7" class="text-center align-middle">
//                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($id, $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
//                              <span><strong>' . str_pad($id, 12, '0', STR_PAD_LEFT) . '</strong></span>
//                            </td>
//                          </tr>
//                          <tr>
//                            <td class="color secondary"><strong>Category</strong></td>
//                            <td>' . $rider->rider_category->name . '</td>
//                          </tr>
//                          <tr>
//                            <td class="color secondary"><strong>Route</strong></td>
//                            <td> ' . $route->code . ' (' . $route->start . ' to ' . $route->end . ')</td>
//                          </tr>
//                          <tr>
//                            <td class="color secondary"><strong>Total Pickups</strong></td>
//                            <td>' . $pickup_note->pickups . '</td>
//                          </tr>
//                        </tbody>
//                      </table>
//        ';
//
//            $html .= '
//                      <table class="table table-sm table-bordered border">
//                        <tbody>
//                          <tr>
//                            <td class="color primary"><strong>S. No.</strong></td>
//                            <td class="color primary"><strong>Company Name</strong></td>
//                            <td class="color primary"><strong>Contact Person</strong></td>
//                            <td class="color primary"><strong>Contact Number</strong></td>
//                            <td class="color primary"><strong>Pickup Address</strong></td>
//                            <td class="color primary"><strong>Bookings</strong></td>
//                            <td class="color primary"><strong>Pickup Date</strong></td>
//                          </tr>
//        ';
//
//            $serial_number = 1;
//
//            $pickup_note_requests = $pickup_note->pickup_note_requests;
//
//            foreach ($pickup_note_requests as $pickup_note_request) {
//                $pickup_request = $pickup_note_request->pickup_request;
//
//                $shipper = $pickup_request->shipper;
//                $pickup_address = $pickup_request->pickup_address;
//
//                $html .= '
//                          <tr>
//                            <td>' . $serial_number . '</td>
//                            <td>' . $shipper->name . '</td>
//                            <td>' . $pickup_address['poc'] . '</td>
//                            <td>' . $pickup_address['phone'] . '</td>
//                            <td>' . $pickup_address['pickup_address'] . '</td>
//                            <td>' . $pickup_request['bookings'] . '</td>
//                            <td>' . Carbon::parse($pickup_request['pickup_date'])->format('d/m/Y') . '</td>
//                          </tr>
//          ';
//
//                $serial_number++;
//            }
//
//            $html .= '
//                        </tbody>
//                      </table>
//
//                      <hr>
//        ';
//
//            $pickup_note->status_id = 3;
//
//            $pickup_note->save();
//        }

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
