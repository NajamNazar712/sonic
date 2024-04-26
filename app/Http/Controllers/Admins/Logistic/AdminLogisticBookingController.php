<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Models\Admin\Logistic\TraxBookingBatchAssign;
use App\Http\Models\Admin\Logistic\TraxBookingPiece;
use App\Http\Models\Admin\Logistic\TraxItemInsurance;
use App\Http\Models\Admin\Logistic\TraxLogisticBookingImages;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxShipperDetail;
use App\Http\Models\Admin\Logistic\TraxSpecialHandlingList;
use App\Http\Models\Admin\Logistic\TraxStation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Logistic\TraxItemRefernce;
use App\Http\Models\Admin\Logistic\TraxLogisticBooking;
use App\Http\Models\City;
use App\Http\Models\PaymentMode;
use App\Http\Models\Rider;
use App\Http\Models\Segment;
use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\SubCategorySegment;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class AdminLogisticBookingController extends Controller
{
    protected $validation_messages = [
        'user_id.required' => 'The user ID field is required.',
        'user_id.integer' => 'The user ID must be an integer.',
        'rider_id.required' => 'The rider ID field is required.',
        'rider_id.integer' => 'The rider ID must be an integer.',
        'product_id.required' => 'The product ID field is required.',
        'product_id.integer' => 'The product ID must be an integer.',
        'service_id.required' => 'The service ID field is required.',
        'service_id.integer' => 'The service ID must be an integer.',
    ];

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
        ActivityTrailController::createActivityTrailLog(Auth::id(), 783 );

        return view('admin.logistic.logistic_bookings');
    }


    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 784);
        }
        $logistic_bookings = TraxLogisticBooking::Join('users as u','u.id','=','trax_logistic_bookings.shipper_id')
            ->leftjoin('user_shipping_infos as usi','usi.id','=','trax_logistic_bookings.shipper_address_id')
            ->leftjoin('segments as s','s.id','=','trax_logistic_bookings.product_id')
            ->leftjoin('sub_category_segments as sb','sb.id','=','trax_logistic_bookings.service_id')
            ->leftjoin('cities as oc','oc.id','trax_logistic_bookings.origin_id')
            ->leftjoin('cities as dc','dc.id','trax_logistic_bookings.destination_id')
            ->select('trax_logistic_bookings.id','trax_logistic_bookings.booking_date','trax_logistic_bookings.shipper_id','u.name as shipper_name','usi.pickup_address','trax_logistic_bookings.cn_number','trax_logistic_bookings.product_id','s.name as product_name','trax_logistic_bookings.service_id','sb.name as service_name','trax_logistic_bookings.total_pieces','trax_logistic_bookings.total_booking_weight','trax_logistic_bookings.origin_id','oc.name as origin_name','trax_logistic_bookings.destination_id','dc.name as destination_name','trax_logistic_bookings.consignee_name','trax_logistic_bookings.consignee_address');

        $datatables = Datatables::of($logistic_bookings)
            ->addColumn('action',function ($logistic_bookings){
                if (session('role_id') == 1 || count(array_intersect([980], session('permissions'))) !== 0) {
//                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                      $edit_button = '<a href="' . route("admin.logistic.edit", ["booking_id" => $logistic_bookings->id]) . '" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></div></a>';

                    $dropdown = '
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                              <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    $dropdown .= $edit_button;
                    $dropdown .= '
                              </div>
                            </div>
                       
                         ';
                    return $dropdown;
                } else{
                    return '';
                }
            });

        return $datatables->make(true);
    }


    public function batch_bookings($batch_id)
    {
        $admin_id = session('id');
        $batch_assign=TraxBookingBatchAssign::select('bb.id as batch_id')->join('trax_booking_batches as bb','bb.id','trax_booking_batch_assigns.batch_id')
            ->where('bb.status_id',2)->where('bb.id',$batch_id)->where('trax_booking_batch_assigns.user_id',$admin_id);
        if($batch_assign->exists())
        {
            $batch_assign = $batch_assign->first();
            return view('admin.logistic.batch_logistic_bookings')->with('batch_id',$batch_assign->batch_id);
        }
        return  redirect()->back()->with('error','Batch not assign to you');
    }

    public function batch_booking_list(Request $request)
    {
        $logistic_bookings = TraxLogisticBooking::Join('users as u','u.id','=','trax_logistic_bookings.shipper_id')
            ->join('trax_booking_batch_details as bd','bd.booking_id','trax_logistic_bookings.id')
            ->leftjoin('user_shipping_infos as usi','usi.id','=','trax_logistic_bookings.shipper_address_id')
            ->leftjoin('trax_products as p','p.id','=','trax_logistic_bookings.product_id')
            ->leftjoin('trax_services as s','s.id','=','trax_logistic_bookings.service_id')
            ->leftjoin('trax_stations as oc','oc.id','trax_logistic_bookings.origin_id')
            ->leftjoin('trax_stations as dc','dc.id','trax_logistic_bookings.destination_id')
            ->select('trax_logistic_bookings.id','trax_logistic_bookings.booking_date','trax_logistic_bookings.shipper_id','u.name as shipper_name','usi.pickup_address','trax_logistic_bookings.cn_number','trax_logistic_bookings.product_id','p.product_name','trax_logistic_bookings.service_id','s.service_name','trax_logistic_bookings.total_pieces','trax_logistic_bookings.total_booking_weight','trax_logistic_bookings.origin_id','oc.name as origin_name','trax_logistic_bookings.destination_id','dc.name as destination_name','trax_logistic_bookings.consignee_name','trax_logistic_bookings.consignee_address')
            ->where('bd.batch_id',$request->batch_id);



        $datatables = Datatables::of($logistic_bookings)
            ->addColumn('action',function ($logistic_bookings){
                if (session('role_id') == 1 || count(array_intersect([83, 84, 507], session('permissions'))) !== 0) {
                      $edit_button = '<a href="' . route("admin.logistic.edit", ["booking_id" => $logistic_bookings->id]) . '" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></div></a>';

                    $dropdown = '
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                              <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    $dropdown .= $edit_button;
                    $dropdown .= '
                              </div>
                            </div>
                       
                         ';
                    return $dropdown;
                } else{
                    return '';
                }
            });

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
        $trax_stations=TraxStation::where('status',1)->get();
        $paymentmodes=PaymentMode::all(); 
        $riders=Rider::where('status',1)->get();
        $special_handlings = TraxSpecialHandlingList::where('status',1)->get();
        return view('admin.logistic.add_logistic_book')
        ->with(['payment_modes'=>$payment_modes,'shippers'=>$shippers,'trax_stations'=>$trax_stations,'special_handlings'=>$special_handlings,'paymentmodes'=>$paymentmodes,'riders'=>$riders]);
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
                        'booking_id' => $request->id,
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
    public function edit($booking_id)
    {

        $validate=Validator::make(['booking_id'=>$booking_id],[
            'booking_id'=>['required','integer']
        ]);

        if($validate->fails())
        {
            return redirect()->back()->with(['error'=>'Booking id invalid']);
        }
        $logistic_booking = TraxLogisticBooking::where('id',$booking_id);
        if($logistic_booking->exists())
        {
            $payment_modes=PaymentMode::all();
            $paymentmodes=PaymentMode::all();
            $riders=Rider::where('status',1)->get();
            $special_handlings = TraxSpecialHandlingList::where('status',1)->get();

            //get booking data
            $logistic_booking = $logistic_booking->first();
            $item_insurance = TraxItemInsurance::where('booking_id',$booking_id)->first();
            $item_references = TraxItemRefernce::where('booking_id',$booking_id)->get();
            $booking_pieces = TraxBookingPiece::where('booking_id',$booking_id)->get();

            $shippers=User::select('id','name')->where('id',$logistic_booking->shipper_id)->where('status',3)->get();
            $product=TraxProduct::select('id','product_name')->where('id',$logistic_booking->product_id)->where('status',1)->get();
            $trax_stations=TraxStation::select('id','name')->where('status',1)->get();
            $pickup_addresses=UserShippingInfo::select('id','pickup_address','poc','phone','email')->get();
            $booking_img = TraxLogisticBookingImages::where('booking_id',$logistic_booking->id);
            $booking_img_url=null;
            if ($booking_img->exists())
            {
                $booking_img = $booking_img->first();
                $booking_img_url =  Storage::url('logistic_bookings/'. $booking_img->image_name);
            }

            return view('admin.logistic.edit_logistic_book')
                ->with(['booking_img_url'=>$booking_img_url,'logistic_booking'=>$logistic_booking,'item_insurance'=>$item_insurance,'item_references'=>$item_references,'booking_pieces'=>$booking_pieces,'payment_modes'=>$payment_modes,'shippers'=>$shippers,'product'=>$product,'trax_stations'=>$trax_stations,'pickup_addresses'=>$pickup_addresses,'special_handlings'=>$special_handlings,'paymentmodes'=>$paymentmodes,'riders'=>$riders]);
        }
        return  redirect()->back()->with('error','Booking not found!');



    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $admin_id = session('id');
        $validate = Validator::make($request->all(),[
            'booking_id'=>['required','integer'],
            'cn_number'=>['required','integer'],
            'rider_id'=>['required','integer'],
            'booking_date'=>['required','date'],
            'shipper_id'=>['required','integer'],
            'product_id'=>['required','integer'],
            'service_id'=>['required','integer'],
            'origin_id'=>['required','integer'],
            'destination_id'=>['required','integer'],
            'booking_weight'=>['required','numeric'],
            'dense_weight'=>['sometimes','numeric'],
            'volumetric_weight'=>['sometimes','numeric'],
            'shipper_address_id'=>['required','integer'],
            'consignee_name'=>['required','string','max:255'],
            'consignee_phone_1'=>['required','integer'],
            'consignee_address'=>['required','string','max:255'],
            'consignee_email'=>['sometimes','email'],
            'payment_mode_id'=>['sometimes','integer'],
            'consignment_type'=>['sometimes','max:1','in:L,H'],
            'handling_inst'=>['string','max:255'],

            'item_insurance_id'=>['sometimes','integer'],
            'special_handling_id'=>['sometimes','integer'],
            'item_insurance'=>['sometimes','numeric'],
            'insurance_item_code'=>['sometimes','string','max:255'],
        ]);
        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $logistic_booking= TraxLogisticBooking::find($request->booking_id);
        if($logistic_booking)
        {
            try {
                DB::beginTransaction();
                    $logistic_booking->cn_number=$request->cn_number;
                    $logistic_booking->rider_id=$request->rider_id;
                    $logistic_booking->booking_date=$request->booking_date;
                    $logistic_booking->shipper_id=$request->shipper_id;
                    $logistic_booking->product_id=$request->product_id;
                    $logistic_booking->service_id=$request->service_id;
                    $logistic_booking->origin_id=$request->origin_id;
                    $logistic_booking->destination_id=$request->destination_id;
                    $logistic_booking->total_booking_weight=$request->booking_weight;
                    $logistic_booking->total_dense_weight=$request->dense_weight;
                    $logistic_booking->total_volumetric_weight=$request->volumetric_weight;
                    $logistic_booking->shipper_address_id=$request->shipper_address_id;
                    $logistic_booking->consignee_name=$request->consignee_name;
                    $logistic_booking->consignee_address=$request->consignee_address;
                    $logistic_booking->consignee_phone_1=$request->consignee_phone_1;
                    $logistic_booking->consignee_email=$request->consignee_email;
                    $logistic_booking->payment_mode_id=$request->payment_mode_id;
                    $logistic_booking->consignment_type=$request->consignment_type;
                    $logistic_booking->handling_inst=$request->handling_inst;
                    $logistic_booking->updated_by = $admin_id;
                    $logistic_booking->save();

                    if(isset($request->item_insurance_id))
                    {
                        $item_insurance = TraxItemInsurance::where('id',$request->item_insurance_id)->where('booking_id',$request->booking_id);
                        if($item_insurance->exists()){
                            $item_insurance = $item_insurance->first();
                            $item_insurance->special_handling_id=$request->special_handling_id;
                            $item_insurance->insurance=$request->item_insurance;
                            $item_insurance->item_code=$request->insurance_item_code;
                            $item_insurance->updated_by = $admin_id;

                            $item_insurance->save();
                        }
                    }

                    if(isset($request->no_piece) && is_array($request->no_piece))
                    {
                        foreach ($request->no_piece as $key => $value)
                        {

                            if(isset($request->item_reference_id[$key]))
                            {
                                $item_reference = TraxItemRefernce::where('id',$request->item_reference_id[$key])->where('booking_id',$request->booking_id);
                                if($item_reference->exists())
                                {
                                    $item_reference = $item_reference->first();
                                    $item_reference->width =$request->width[$key];
                                    $item_reference->height =$request->height[$key];
                                    $item_reference->length =$request->length[$key];
                                    $item_reference->weight =$request->weight[$key];
                                    $item_reference->no_piece =$value;
                                    $item_reference->created_by =$admin_id;
                                    $item_reference->updated_by =$admin_id;
                                    $item_reference->user_type =1;
                                    $item_reference->save();
                                }
                            }else {
                                $item_reference = new TraxItemRefernce();
                                $item_reference->booking_id = $request->booking_id;
                                $item_reference->width = $request->width[$key];
                                $item_reference->height =$request->height[$key];
                                $item_reference->length =$request->length[$key];
                                $item_reference->weight =$request->weight[$key];
                                $item_reference->no_piece =$value;
                                $item_reference->created_by =$admin_id;
                                $item_reference->updated_by =$admin_id;
                                $item_reference->user_type =1;
                                $item_reference->save();
                            }
//                            TraxItemRefernce::updateOrCreate(
//                                isset($request->item_reference_id[$key]) ? // Check if item_reference_id is set
//                                    ['id' => $request->item_reference_id[$key], 'booking_id' => $request->booking_id] :
//                                    [], // Pass null if item_reference_id is not set
//                                [
//                                    'booking_id' => $request->booking_id,
//                                    'width' => $request->width[$key],
//                                    'height' => $request->height[$key],
//                                    'length' => $request->length[$key],
//                                    'weight' => $request->weight[$key],
//                                    'no_piece' => $value,
//                                    'created_by' => $admin_id,
//                                    'updated_by' => $admin_id,
//                                    'user_type' => 1
//                                ]
//                            );
                        }
                    }

                DB::commit();
                return  redirect()->back()->with('success','Logistic booking updated successfully');


            } catch (\Exception $exception) {
                // Roll back the transaction if an error occurs
                DB::rollBack();
                return redirect()->back()->with('error' , $exception->getMessage());
            }

        }
        return  redirect()->back()->with('error','Logistic booking not found!');



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
            $product=TraxProduct::join('trax_shipper_details as sd','sd.trax_product_id','trax_products.id')
                ->select('trax_products.id','trax_products.product_name')->where('sd.user_id',$shipper->id)->where('trax_products.status',1)->first();
            return response()->json(['status' => 0, 'pickup_addresses' => $pickup_addresses, 'product' => $product]);
        }else{
            return response()->json(['status' => 1, 'error' => 'Shipper not found']);
        }

    }
    public function get_product_services(Request $request){
        $product_id = $request->product_id;
        $services = TraxService::join('trax_products as p','p.id','trax_services.product_id')
            ->select('trax_services.id','trax_services.service_name')->where('trax_services.product_id',$product_id)->where('trax_services.status',1)->get();
        return response()->json(['status' => 0, 'services' => $services]);
    }

//    public function get_logistic_shipment($cn_number){
//
//
//        $validator=Validator::make(['cn_number'=>$cn_number],['cn_number'=>'required|integer|min:1']);
//        if(!$validator->fails()){
//           $logisticshipment=TraxLogisticBooking::join('segments as s','s.id','=','trax_logistic_bookings.product_id')
//           ->join('sub_category_segments as ss','ss.segment_id','=','s.id')
//           ->join('cities as oc','oc.id','=','trax_logistic_bookings.origin_id')
//           ->join('cities as dc','dc.id','=','trax_logistic_bookings.destination_id')
//           ->select('trax_logistic_bookings.*','oc.name as origin_name','dc.name as destination_name','s.name as product_name','ss.name as service_name')
//           ->where('cn_number',$cn_number);
//           if($logisticshipment->exists()) {
//                $logisticshipment=$logisticshipment->first();
//                return response()->json(['status'=>0,'logisticshipment'=>$logisticshipment]);
//           }
//           return response()->json(['status'=>1,'error'=>'Shipment not found!']);
//        }
//        return response()->json(['status'=>1,'error'=>'Consignment number not correct!']);
//
//    }

   
}
