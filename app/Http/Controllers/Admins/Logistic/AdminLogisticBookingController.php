<?php

namespace App\Http\Controllers\Admins\Logistic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\http\models\admin\Logistic\LogisticBookingTrack;
use App\Http\Models\Admin\Logistic\TraxItemRefernce;
use App\http\models\admin\Logistic\TraxLogisticBooking;
use App\Http\Models\City;
use App\Http\Models\PaymentMode;
use App\Http\Models\Rider;
use App\Http\Models\Segment;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\SubCategorySegment;
use App\User;
use CreateItemsRefernceTrackTable;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class AdminLogisticBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.logistic.logistics');
    }

    public function list()
    {
        $logistic_bookings = TraxLogisticBooking::Join('users as u','u.id','=','trax_logistic_bookings.shipper_id')
            ->join('user_shipping_infos as usi','usi.id','=','trax_logistic_bookings.shipper_address_id')
            ->join('segments as s','s.id','=','trax_logistic_bookings.product_id')
            ->join('sub_category_segments as sb','sb.id','=','trax_logistic_bookings.service_id')
            ->join('cities as oc','oc.id','trax_logistic_bookings.origin_id')
            ->join('cities as dc','dc.id','trax_logistic_bookings.destination_id')
            ->select('trax_logistic_bookings.booking_date','trax_logistic_bookings.shipper_id','u.name as shipper_name','usi.pickup_address','trax_logistic_bookings.cn_number','trax_logistic_bookings.product_id','s.name as product_name','trax_logistic_bookings.service_id','sb.name as service_name','trax_logistic_bookings.total_pieces','trax_logistic_bookings.booking_weight','trax_logistic_bookings.origin_id','oc.name as origin_name','trax_logistic_bookings.destination_id','dc.name as destination_name','trax_logistic_bookings.consignee_name','trax_logistic_bookings.consignee_address');

        $datatables = Datatables::of($logistic_bookings);

        return $datatables->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
        $payment_modes=PaymentMode::all();
        $shippers=User::where('status',3)->get();
        $cities=City::where('status',1)->get(); 
        $paymentmodes=PaymentMode::all(); 
        $riders=Rider::where('status',1)->get();
        return view('admin.logistic.add_logistic_book')
        ->with(['payment_modes'=>$payment_modes,'shippers'=>$shippers,'cities'=>$cities,'paymentmodes'=>$paymentmodes,'riders'=>$riders]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $admin_id=session('id');
        $request->validate([
            'cn_number'=>'required|integer',
            'rider_id'=>'required|integer',
            'booking_date'=>'required|date',
            'shipper_id'=>'required|integer',
            'product_id'=>'required|integer',
            'service_id'=>'required|integer',
            'origin_id'=>'required|integer',
            'destination_id'=>'required|integer',
            'booking_weight'=>'required|integer',
            'dense_weight'=>'sometimes|nullable|integer',
            'volumetric_weight'=>'sometimes|nullable|integer',
            'shipper_address_id'=>'required|integer',
            // 'shipper_name'=>'required|string|max:255',
            'consignee_address'=>'required',
            'consignee_name'=>'required',
            // 'payment_mode_id'=>'required|integer',
            // 'route_id'=>'required|integer',

        ]); 
        $logistic_booking=new TraxLogisticBooking();
        $logistic_booking->cn_number=$request->cn_number;
        $logistic_booking->rider_id=$request->rider_id;
        $logistic_booking->booking_date=$request->booking_date;
        $logistic_booking->shipper_id=$request->shipper_id;
        $logistic_booking->product_id=$request->product_id;
        $logistic_booking->service_id=$request->service_id;
        $logistic_booking->origin_id=$request->origin_id;
        $logistic_booking->destination_id=$request->destination_id;
        $logistic_booking->booking_weight=$request->booking_weight;
        $logistic_booking->dense_weight=$request->dense_weight;
        $logistic_booking->volumetric_weight=$request->volumetric_weight;
        $logistic_booking->consignee_name=$request->consignee_name;
        $logistic_booking->shipper_address_id=$request->shipper_address_id;
        $logistic_booking->consignee_address=$request->consignee_address;
        $logistic_booking->consignee_phone_1=$request->consignee_phone_1;
        $logistic_booking->consignee_email=$request->consignee_email;
        $logistic_booking->payment_mode_id=$request->payment_mode_id;
        $logistic_booking->handling_inst=$request->handling_inst;
//        $logistic_booking->ship_ref_no=$request->ship_ref_no;
        $logistic_booking->route_id=$request->route_id;
        // $logistic_booking->ot_service=$request->ot_service;
        // $logistic_booking->gst=$request->gst;
        // $logistic_booking->descl=$request->descl;
        // $logistic_booking->psc=$request->psc;
        // $logistic_booking->pst=$request->pst;
        // $logistic_booking->insurance=$request->insurance;
        // $logistic_booking->handling=$request->handling;
        // $logistic_booking->total=$request->total;
        // $logistic_booking->other_charges=$request->other_charges;
        // $logistic_booking->fuel_charges=$request->fuel_charges;
        $logistic_booking->save();
        if(is_array($request->no_piece)){
            foreach ($request->no_piece as  $key => $value) {
                if(!is_null($value)){
                    TraxItemRefernce::create([
                        'cn_number' => $request->cn_number,
                        'width' => $request->width[$key],
                        'height' => $request->height[$key],
                        'length' => $request->length[$key],
                        'weight' => $request->weight[$key],
                        'no_piece' => $value,
                        'created_by'=>$admin_id,
                        'updated_by'=>$admin_id,
                        'user_type'=>1
                    ]);
                }
                 
            }
        }
        return redirect()->back()->with('success','Booking Successfully Complete');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get_shipper_info(Request $request){
        $shipper_id = $request->shipper_id;
        $shipper=User::find($shipper_id);
        if($shipper){
            $pickup_addresses = UserShippingInfo::with('city')->where('user_id', $shipper->id)->where('status', 1)->where('hidden', 0)->get();
            $product=Segment::find($shipper->segment_id);
            return response()->json(['status' => 0, 'pickup_addresses' => $pickup_addresses, 'product' => $product]);
        }else{
            return response()->json(['status' => 1, 'error' => 'Shipper not found']);
        }

    }
    public function get_product_services(Request $request){
        $segment_id = $request->product_id;
        $services=SubCategorySegment::where('segment_id',$segment_id)->get();
        return response()->json(['status' => 0, 'services' => $services]);
    }

    public function get_logistic_shipment($cn_number){

     
        $validator=Validator::make(['cn_number'=>$cn_number],['cn_number'=>'required|integer|min:1']);
        if(!$validator->fails()){
           $logisticshipment=TraxLogisticBooking::join('segments as s','s.id','=','trax_logistic_bookings.product_id')
           ->join('sub_category_segments as ss','ss.segment_id','=','s.id')
           ->join('cities as oc','oc.id','=','trax_logistic_bookings.origin_id')
           ->join('cities as dc','dc.id','=','trax_logistic_bookings.destination_id')
           ->select('trax_logistic_bookings.*','oc.name as origin_name','dc.name as destination_name','s.name as product_name','ss.name as service_name')
           ->where('cn_number',$cn_number);
           if($logisticshipment->exists()) {
                $logisticshipment=$logisticshipment->first();
                return response()->json(['status'=>0,'logisticshipment'=>$logisticshipment]);
           }
           return response()->json(['status'=>1,'error'=>'Shipment not found!']);
        }
        return response()->json(['status'=>1,'error'=>'Consignment number not correct!']);
     
    }
   
}
