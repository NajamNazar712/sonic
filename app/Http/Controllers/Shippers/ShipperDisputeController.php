<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\City;
use App\Http\Models\Dispute;
use App\Http\Models\DisputeComment;
use App\Http\Models\DisputeShipment;
use App\Http\Models\DisputeType;
use App\Http\Models\PaymentMode;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Shippers\ShipperShipmentBookController;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperDisputeController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }
    public function dispute_index(){
        $cities = City::all();
        $dispute_types = DisputeType::whereIn('id',[5,9])->get();
        return view('client.dispute.index')->with(['cities'=>$cities,'dispute_types'=>$dispute_types]);
    }
    public function dispute_list(){
        $dispute = Dispute::join('cities','cities.id','=','disputes.city_id')
            ->join('dispute_types as dt','dt.id','=','disputes.dispute_type_id')
            ->select(['disputes.id as dispute_id','disputes.created_at as created_at','disputes.description','cities.name as originated_at','dt.type as dispute_type','disputes.shipments_count as no_of_shipments','disputes.status as status'])
            ->where('disputes.raised_by',session('user_id'))
            ->where('disputes.raised_by_status',1);
        return Datatables::of($dispute)

            ->editColumn('created_at', function ($dispute) {
                return $dispute->created_at ? with(new Carbon($dispute->created_at))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('status',function($dispute){
                return $dispute->status == 0? 'Dispute Launched': ($dispute->status == 1? 'Dispute Updated' : ($dispute->status == 2? 'Dispute Resolved':''));

            })
            ->editColumn('no_of_shipments',function($dispute){
                return "<a class='font-weight-bold shipment_count' href='#'>{$dispute->no_of_shipments}</a>";
            })
            ->addColumn("action", function ($dispute) {
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                        <button type="button" class="dropdown-item view-comments"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Comments</div></button>
                    </div>
                  </div>
                ';

                return $dropdown;
            })

            ->make(true);
    }
    public function dispute_create(Request $request){
//        return $request;
        $tracking_numbers = explode(',',$request->tracking_number);
        if(!empty($request->tracking_number)) {
            $count = 0;
            $dispute_id = '';
            $dispute_created = 0;
            $tracking_number = array();
            foreach ($tracking_numbers as $tracking) {
                $shipment = Shipment::where('tracking_number',$tracking);
                if($shipment->exists()){
                    $shipment = $shipment->first();
                    if (session('user_id') == $shipment->user_id){
                    if($dispute_created == 0){
                        $dispute = Dispute::create([
                            'description'=>$request->description,
                            'raised_by'=>session('user_id'),
                            'raised_by_status'=>1,
                            'city_id'=>$request->city_select,
                            'dispute_type_id'=>$request->dispute_type_select
                        ]);
                        $dispute_id = $dispute->id;
                        $dispute_created = 1;
                        $tracking_number['success'] = 'Dispute successfully created!';
                    }

                        DisputeShipment::create([
                            'dispute_id'=>$dispute->id,
                            'shipment_id'=>$shipment->id
                        ]);
                        $count++;
                    }else{
                        $tracking_number['disallowed'][] = $tracking;
                    }

                }else{
                    $tracking_number['invalid'][] = $tracking;
                }
            }
            if($dispute_id != ''){

                Dispute::where('id',$dispute_id)->update(['shipments_count'=>$count]);

                NotificationsController::send(19, $dispute_id);
            }
            return response()->json($tracking_number);
//            return $tracking_number;
//            return redirect()->back()->with(['success'=>'Dispute successfully created!','error'=>$tracking_number]);
        }else{
            return redirect()->back()->with('error','No shipments selected!');

        }

    }
    public function get_shipments(Request $request){
        $dispute_id = $request->id;
        $trackings = array();
        $dispute = DisputeShipment::where('dispute_id',$dispute_id)->select('shipment_id');
        if($dispute->exists()){
            $dispute_shipment_ids = $dispute->get();
            $trackings = Shipment::whereIn('id',$dispute_shipment_ids)->select('tracking_number')->get();

            return response()->json(['status'=>1,'shipments'=>$trackings]);
        }else{
            return response()->json(['status'=>0,'error'=>"No shipments exist!"]);
        }
    }
    public function get_comments(Request $request){
        $comments = DisputeComment::where('dispute_id',$request->id);
        if($comments->exists()){
            $comments = $comments->orderBy('created_at', 'desc')->get();
            $returnHTML = view('client.dispute.comments')->with(['comments'=>$comments])->render();
            return response()->json(['status'=>1,'view'=>$returnHTML]);
        }else{
            return response()->json(['status'=>0,'error'=>"No comments!"]);
        }
    }
    public function get_data(Request $request){
        $shipment_id = $request->shipment_id;
        $shipment = Shipment::where('id',$shipment_id);
        if($shipment->exists()){
            $shipment = $shipment->select('tracking_number')->first();
            return response()->json(['success'=>1,'tracking'=>$shipment->tracking_number]);

        }else{
            return response()->json(['success'=>0,'error'=>"Shipment Not Found!"]);

        }
    }

    //Rebook Starts
    public function rebook_index(Request $request){
        return view('client.dispute.rebook.index');
    }
    public function rebook_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('shipment_items as si','si.shipment_id','=','shipments.id')
            ->join('products','products.id','=','si.product_type_id')
            ->select(['shipments.id as shipment_id','shipments.tracking_number as tracking_number','shipments.order_id','bt.booking_type','ss.name as status','oc.name as origin','dc.name as destination','shipments.consignee_name as consignee','shipments.consignee_phone_number_1 as phone','shipments.consignee_address as address','products.product_name','shipments.created_at as created_at'])
            ->where('shipments.shipper_status_id',11)
            ->where('shipments.user_id',session('user_id'));
        return Datatables::of($shipments)
            ->editColumn('tracking_number', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('created_at', function ($shipments) {
                return $shipments->created_at ? with(new Carbon($shipments->created_at))->format('d/m/Y h:i:s A') : '';
            })
            ->addColumn("action", function ($result) {
                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                        <button type="button" class="dropdown-item print_airway"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>
                        <button type="button" class="dropdown-item rebook"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Re-book</div></button>
                    </div>
                  </div>
                ';

                return $dropdown;
            })
            ->make(true);
    }
    public function get_shipment_info(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id != ''){
            $shipment = Shipment::where('id',$shipment_id);
            if($shipment->exists()){
                $data = array();
                $shipment = $shipment->first();
                $cities = City::where('status',1)->select('id','name')->get();
                $payment_mode = PaymentMode::all();
                $data['tracking_number'] = $shipment->tracking_number;
                $data['consignee_city_id'] = $shipment->consignee_city->id;
//                $data['consignee_city_name'] = $shipment->consignee_city->name;
                $data['consignee_name'] = $shipment->consignee_name;
                $data['consignee_address'] = $shipment->consignee_address;
                $data['consignee_phone1'] = $shipment->consignee_phone_number_1;
                $data['consignee_phone2'] = $shipment->consignee_phone_number_2;
                $data['consignee_email'] = $shipment->consignee_email;
                $data['amount'] = $shipment->amount;
                $data['payment_mode'] = $shipment->payment_mode->id;

                return response()->json(['status'=>1,'data'=>$data,'cities'=>$cities,'payment'=>$payment_mode]);

            }else{
                return response()->json(['status'=>0,'error'=>'Shipment not found']);
            }

        }
    }

    //Re-Book a shipment
    private function book($service_type_id, $pickup_address_id, $information_display, $consignee_city_id, $consignee_name, $consignee_address, $consignee_phone_number_1, $consignee_phone_number_2, $consignee_email_address, $order_id, $package_type, $pickup_date, $special_instructions, $estimated_weight, $shipping_mode_id, $same_day_timing_id, $amount, $payment_mode_id,$shipper_status_id,$consignee_status_id) {
        $shipment = new Shipment();

        $shipment->user_id = session('user_id');
        $shipment->booking_type_id = $service_type_id;
        $shipment->pickup_address_id = $pickup_address_id;
        $shipment->information_display = $information_display;

        $shipment->consignee_city_id = $consignee_city_id;
        $shipment->consignee_name = $consignee_name;
        $shipment->consignee_address = $consignee_address;
        $shipment->consignee_phone_number_1 = $consignee_phone_number_1;
        $shipment->consignee_phone_number_2 = $consignee_phone_number_2;
        $shipment->consignee_email = $consignee_email_address;

        $shipment->order_id = $order_id;
        $shipment->package_type = $package_type;
        $shipment->pickup_date = $pickup_date;
        $shipment->special_instructions = $special_instructions;


        $shipment->estimated_weight = $estimated_weight;
        $shipment->shipping_mode_id = $shipping_mode_id;
        $shipment->same_day_timing_id = $same_day_timing_id;

        $shipment->amount = $amount;
        $shipment->payment_mode_id = $payment_mode_id;
        $shipment->shipper_status_id = $shipper_status_id;
        $shipment->consignee_status_id = $consignee_status_id;

        $shipment->save();
        return $shipment;
    }
    //rebook shipment
    public function rebook_shipment_update(Request $request){
        $shipment_id = $request->shipment_id;
        $newAddress = "Trax Office";
        if($shipment_id != ''){
            $shipment = Shipment::where('id',$shipment_id);
            if($shipment->exists()){
                $shipment = $shipment->first();

                if(($request->consignee_city_id != $shipment->consignee_city_id) && $shipment->shipper_status_id == 11){
                    $shipment->shipper_status_id = 19;
                    $shipment->consignee_status_id = 19;
                    $shipment->save();
                    $consigneeCity =City::where('id',$shipment->consignee_city_id)->first();
                    $traxOffice = UserShippingInfo::where(['user_id'=>session('user_id'),'city_id'=>$consigneeCity->hub_id,'hidden'=>1]);
                    if(!$traxOffice->exists()){
                        $shipper_details = User::where('id',session('user_id'))->select('poc','phone','email')->first();
                        $pickup_address = UserShippingInfo::create(['user_id'=>session('user_id'),'pickup_address'=>$newAddress,'poc'=>$shipper_details->poc,'phone'=>$shipper_details->phone,'email'=>$shipper_details->email,'city_id'=>$shipment->consignee_city_id,'hidden'=>1]);
                    }else{
                    $traxOffice = UserShippingInfo::where(['user_id'=>session('user_id'),'city_id'=>$consigneeCity->hub_id,'hidden'=>0]);
                        $pickup_address = $traxOffice->first();
                    }

                  $newShipment =  $this->book($shipment->booking_type_id,$pickup_address->id,1,$request->consignee_city_id,$request->consignee,$request->address,$request->phone1,$request->phone2,$request->email,$shipment->order_id,$shipment->package_type,$shipment->pickup_date,$shipment->special_instructions,$shipment->estimated_weight,$request->mode,$shipment->same_day_timing_id,$request->amount,$shipment->payment_mode_id,2,2);

                 $newTracking = ShipperShipmentBookController::generate_tracking_number($newShipment->id,$shipment->consignee_city_id,$newShipment->consignee_city_id);
                    ShipmentsJourneyController::add($shipment->id, 19, 19, NULL, 'Shipment # '.$shipment->tracking_number.' has been Re-Booked as new Shipment # '.$newTracking, session('user_id'), NULL);

                    NotificationsController::send(17, $shipment->id, $newShipment->id);
                    NotificationsController::send(18, $shipment->id, $newShipment->id);

                        foreach ($shipment->items as $item) {
                            ShipperShipmentBookController::add_item($newShipment->id, $item->product_type_id, $item->description, $item->quantity, $item->price, $item->insurance, $item->type);
                        }

                    ShipmentsJourneyController::add($newShipment->id, 1, 1, NULL, 'Shipment has been Re-Booked against Tracking # '.$shipment->tracking_number, session('user_id'), NULL);
                    ShipmentsJourneyController::add($newShipment->id, 2, 2, NULL, 'Shipment has been Re-Booked and arrived at origin center', session('user_id'), NULL);
                    return response()->json(['status'=>1,'success'=>'Shipment has been rebooked successfully']);

                }else{
                    return response()->json(['status'=>0,'error'=>'Please select new city!']);
                }
            }else{
                return response()->json(['status'=>0,'error'=>'Shipment doesn\'t exist!']);

            }
        }
    }
}
