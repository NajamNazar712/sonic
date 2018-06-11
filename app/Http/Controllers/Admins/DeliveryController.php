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
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
//            ->join('shipments_journey sj','shipments.id','=','sj.shipment_id')

            ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type')
            ->where(['shipper_status_id'=>1,'consignee_status_id'=>1])
            ->get();
        return Datatables::of($shipments)
            ->addColumn("action", function ($result) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item' data-target-id='' data-toggle='modal' data-target='#BankInfoModal'><i class='ft-plus-circle primary'></i> Dispute</a>
                                            
                                            </div>
                                            </span>";
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
                Shipment::where('id',$shipment)->update(['shipper_status_id'=>2,'consignee_status_id'=>2]);
            }
        }
        return $request;
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
            ->select(['delivery_notes.id as delivery_note','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','delivery_notes.created_at','delivery_notes.total_cod_amount','delivery_notes.shipments_count'])
            ->get();
        return Datatables::of($deliveries)
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
                return ['status' => 0, 'success' => 'Shipment is successfully removed'];
            }
        }else{
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }
}
