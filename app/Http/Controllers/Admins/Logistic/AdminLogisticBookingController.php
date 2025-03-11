<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Logistic\TraxBookingBatch;
use App\Http\Models\Admin\Logistic\TraxBookingBatchAssign;
use App\Http\Models\Admin\Logistic\TraxBookingBatchDetail;
use App\Http\Models\Admin\Logistic\TraxBookingPiece;
use App\Http\Models\Admin\Logistic\TraxItemInsurance;
use App\Http\Models\Admin\Logistic\TraxLogisticBookingImages;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxShipperDetail;
use App\Http\Models\Admin\Logistic\TraxSpecialHandlingList;
use App\Http\Models\Admin\Logistic\TraxStation;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        ActivityTrailController::createActivityTrailLog(Auth::id(), 791);
        return view('admin.logistic.logistic_bookings');
    }


    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 792);
        }
        $hub_ids=session('hubs');
        $city_ids=City::whereIn('hub_id',$hub_ids);
        if($city_ids->exists())
        {
            $city_ids=$city_ids->pluck('id');
        }
        $logistic_bookings = TraxLogisticBooking::Join('users as u','u.id','=','trax_logistic_bookings.shipper_id')
            ->leftjoin('riders as r','r.id','=','trax_logistic_bookings.rider_id')
            ->leftjoin('user_shipping_infos as usi','usi.id','=','trax_logistic_bookings.shipper_address_id')
            ->leftjoin('trax_products as p','p.id','=','trax_logistic_bookings.product_id')
            ->leftjoin('trax_services as sb','sb.id','=','trax_logistic_bookings.service_id')
            ->leftjoin('cities as oc','oc.id','trax_logistic_bookings.origin_id')
            ->leftjoin('cities as dc','dc.id','trax_logistic_bookings.destination_id')
            ->select('trax_logistic_bookings.id','trax_logistic_bookings.booking_date','trax_logistic_bookings.shipper_id','u.name as shipper_name','usi.pickup_address','trax_logistic_bookings.cn_number','trax_logistic_bookings.product_id','p.product_name','trax_logistic_bookings.service_id','sb.service_name','trax_logistic_bookings.total_pieces','trax_logistic_bookings.total_booking_weight','trax_logistic_bookings.origin_id','oc.name as origin_name','trax_logistic_bookings.destination_id','dc.name as destination_name','trax_logistic_bookings.consignee_name','trax_logistic_bookings.consignee_address','r.name as rider_name')
            ->orderByDesc('trax_logistic_bookings.id');

        if (session('role_id') != 1)
        {
            $logistic_bookings = $logistic_bookings->whereIn('trax_logistic_bookings.origin_id',$city_ids);
        }
        $datatables = Datatables::of($logistic_bookings)
            ->addColumn('action',function ($logistic_bookings){
                if (session('role_id') == 1 || count(array_intersect([987], session('permissions'))) !== 0) {
//                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                      $edit_button = '<a href="' . route("admin.logistic.edit", ["batch_id"=>0,"booking_id" => $logistic_bookings->id]) . '" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></div></a>';

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

        ActivityTrailController::createActivityTrailLog(Auth::id(), 793);

        $admin_id = session('id');
        $batch_assign=TraxBookingBatchAssign::select('bb.id as batch_id')->join('trax_booking_batches as bb','bb.id','trax_booking_batch_assigns.batch_id')
            ->whereIn('bb.status_id',[2,3])->where('bb.id',$batch_id)->where('trax_booking_batch_assigns.user_id',$admin_id);
        if($batch_assign->exists())
        {
            $batch_assign = $batch_assign->first();
            return view('admin.logistic.batch_logistic_bookings')->with('batch_id',$batch_assign->batch_id);
        }
        return  redirect()->back()->with('error','Batch not assign to you');
    }

    public function batch_booking_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 794);
        }
        $hub_ids=session('hubs');
        $city_ids=City::whereIn('hub_id',$hub_ids);
        if($city_ids->exists())
        {
            $city_ids=$city_ids->pluck('id');
        }
        $logistic_bookings = TraxLogisticBooking::Join('users as u','u.id','=','trax_logistic_bookings.shipper_id')
            ->join('trax_booking_batch_details as bd','bd.booking_id','trax_logistic_bookings.id')
            ->join('trax_booking_batches as bb','bb.id','bd.batch_id')
            ->leftjoin('riders as r','r.id','=','trax_logistic_bookings.rider_id')
            ->leftjoin('user_shipping_infos as usi','usi.id','=','trax_logistic_bookings.shipper_address_id')
            ->leftjoin('trax_products as p','p.id','=','trax_logistic_bookings.product_id')
            ->leftjoin('trax_services as s','s.id','=','trax_logistic_bookings.service_id')
            ->leftjoin('trax_stations as oc','oc.id','trax_logistic_bookings.origin_id')
            ->leftjoin('trax_stations as dc','dc.id','trax_logistic_bookings.destination_id')
            ->leftjoin('trax_booking_batch_details as bbd','bbd.booking_id','trax_logistic_bookings.id')
            ->leftjoin('trax_booking_statuses as bs',function ($query){
                $query->on('bs.id','bbd.status_id');
            })
            ->select('trax_logistic_bookings.id','bb.status_id','trax_logistic_bookings.booking_date','trax_logistic_bookings.shipper_id','u.name as shipper_name','usi.pickup_address','trax_logistic_bookings.cn_number','trax_logistic_bookings.product_id','p.product_name','trax_logistic_bookings.service_id','s.service_name','trax_logistic_bookings.total_pieces','trax_logistic_bookings.total_booking_weight','trax_logistic_bookings.origin_id','oc.name as origin_name','trax_logistic_bookings.destination_id','dc.name as destination_name','trax_logistic_bookings.consignee_name','trax_logistic_bookings.consignee_address','r.name as rider_name','bs.name as status_name')
            ->where('bd.batch_id',$request->batch_id)
            ->orderByDesc('trax_logistic_bookings.id');


        if (session('role_id') != 1)
        {
            $logistic_bookings = $logistic_bookings->whereIn('bb.city_id',$city_ids);
        }

        $datatables = Datatables::of($logistic_bookings)
            ->addColumn('action',function ($logistic_bookings) use ($request){
                if (session('role_id') == 1 || count(array_intersect([987], session('permissions'))) !== 0) {
                    $dropdown='';
//                    if ($logistic_bookings->status_id==2)
//                    {
                        $edit_button = '<a href="' . route("admin.logistic.edit", ["batch_id"=>$request->batch_id,"booking_id" => $logistic_bookings->id]) . '" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></div></a>';

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
//                    }
                    return $dropdown;
                } else{
                    return '';
                }
            });

        return $datatables->make(true);
    }

    public function release_batch(Request $request)
    {
           $validate=Validator::make($request->all(),[
                    'batch_id'=>['required','integer']
           ]);
           if($validate->fails())
           {
               return response()->json(['status'=>1,'error'=>'Batch id invalid!']);
           }

           $booking_batch=TraxBookingBatch::where('id',$request->batch_id)->whereIn('status_id',[1,2]);
           if($booking_batch->exists())
           {
                $booking_batch=$booking_batch->first();
                if($booking_batch->total_bookings==$booking_batch->complete_bookings)
                {
                    $booking_batch->status_id=3;
                }else{
                    $booking_batch->status_id=1;
                }
                $booking_batch->save();
                return response()->json(['status'=>0,'success'=>'Batch release successfully!']);
//                return redirect()->route('admin.logistic.batch.index');
           }

           return response()->json(['status'=>1,'error'=>'Batch not found!']);
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
//    public function edit($batch_id,$booking_id)
//    {
//
//        $validate=Validator::make(['booking_id'=>$booking_id],[
//            'booking_id'=>['required','integer']
//        ]);
//
//        if($validate->fails())
//        {
//            return redirect()->back()->with(['error'=>'Booking id invalid']);
//        }
//        $logistic_booking = TraxLogisticBooking::where('id',$booking_id);
//
//        if($logistic_booking->exists())
//        {
//            try {
//                //get booking data
//                $logistic_booking = $logistic_booking->first();
//
//
//                $item_insurance = TraxItemInsurance::where('booking_id',$logistic_booking->id)->first();
//                $item_references = TraxItemRefernce::where('booking_id',$logistic_booking->id)->get();
//                $booking_pieces = TraxBookingPiece::select('trax_booking_pieces.piece_cn_number','trax_booking_pieces.booking_id','r.name as rider_name','r.id as rider_id')
//                    ->join('riders as r','r.id','trax_booking_pieces.scan_rider_id')
//                    ->where('trax_booking_pieces.booking_id',$logistic_booking->id)->get();
//
//                //list
//                $payment_modes=PaymentMode::all();
//                $riders=Rider::where('status',1)->get();
//                $special_handlings = TraxSpecialHandlingList::where('status',1)->get();
//
//                $trax_parent_product_id=0;
//                $shippers=User::select('id','name')->where('id',$logistic_booking->shipper_id)->where('status',3)->get();
//
//                $trax_shipper_detail=TraxShipperDetail::where('user_id',$logistic_booking->shipper_id)->where('status',1);
//                if($trax_shipper_detail->exists())
//                {
//                    $trax_parent_product_id=$trax_shipper_detail->first()->trax_parent_product_id;
//                } else {
//                    $segment_id=User::where('id',$logistic_booking->shipper_id)->first()->segment_id;
//                    $parent_product=TraxParentProduct::where('segment_id',$segment_id);
//                    if($parent_product->exists())
//                    {
//                        $trax_parent_product_id=$parent_product->first()->id;
//                    }
//                }
//
//                $products=TraxProduct::select('id','product_name')->where('parent_id',$trax_parent_product_id)->where('status',1)->get();
//
//                $services=TraxService::all();
//
//                $trax_stations=TraxStation::select('id','name')->where('status',1)->get();
//
//                $pickup_addresses=UserShippingInfo::select('id','pickup_address','poc','phone','email')->where('user_id',$logistic_booking->shipper_id)->get();
//
//                $booking_img = TraxLogisticBookingImages::where('booking_id',$logistic_booking->id);
//                $booking_img_url=null;
//                if ($booking_img->exists())
//                {
//                    $booking_img = $booking_img->first();
//                    $booking_img_url =  Storage::url('logistic_bookings/'. $booking_img->image_name);
//                }
//
//                return view('admin.logistic.edit_logistic_book')
//                    ->with(['batch_id'=>$batch_id,'booking_img_url'=>$booking_img_url,'logistic_booking'=>$logistic_booking,'item_insurance'=>$item_insurance,'item_references'=>$item_references,'booking_pieces'=>$booking_pieces,'payment_modes'=>$payment_modes,'shippers'=>$shippers,'products'=>$products,'services'=>$services,'trax_stations'=>$trax_stations,'pickup_addresses'=>$pickup_addresses,'special_handlings'=>$special_handlings,'riders'=>$riders]);
//            } catch (\Exception $th){
//                Log::channel('code_test_log')->error('logistic-booking'.json_encode($th->getMessage()));
//            }
//        }
//        return  redirect()->back()->with('error','Booking not found!');
//
//
//
//    }
    public function edit($batch_id,$booking_id)
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
            try {
                //get booking data
                $logistic_booking = $logistic_booking->first();


                $item_insurance = TraxItemInsurance::where('booking_id',$logistic_booking->id)->first();
                $item_references = TraxItemRefernce::where('booking_id',$logistic_booking->id)->get();
                $booking_pieces = TraxBookingPiece::select('trax_booking_pieces.piece_cn_number','trax_booking_pieces.booking_id','r.name as rider_name','r.id as rider_id')
                    ->join('riders as r','r.id','trax_booking_pieces.scan_rider_id')
                    ->where('trax_booking_pieces.booking_id',$logistic_booking->id)->get();

                $trax_parent_product_id=0;

                $shipper=User::where('id',$logistic_booking->shipper_id)->where('status',3)->first();

                $trax_shipper_detail=TraxShipperDetail::where('user_id',$logistic_booking->shipper_id)->where('status',1);
                if($trax_shipper_detail->exists())
                {
                    $trax_parent_product_id=$trax_shipper_detail->first()->trax_parent_product_id;
                } else {
                    if($shipper){
                        $parent_product=TraxParentProduct::where('segment_id',$shipper->segment_id);
                        if($parent_product->exists())
                        {
                            $trax_parent_product_id=$parent_product->first()->id;
                        }
                    }
                }

                $shipping_modes_ids=0;
                if($shipper)
                {

                    if($shipper->account_type_id==2)
                    {
                        if($shipper->corporate_rate_type_id!=3)
                        {
                            $shipping_modes_ids = CorporateRateStatus::where('user_id', $shipper->id)->where('status', 1)->select('shipping_mode_id')->get();
                        } else{
                            $shipping_modes_ids = CorporateDefaultRateStatus::where('user_id', $shipper->id)->where('status', 1)->select('shipping_mode_id')->get();
                        }

                    } else if($shipper->account_type_id==1){
                        $shipping_modes_ids = RateStatus::where('user_id', $shipper->id)->where('status', 1)->select('shipping_mode_id')->get();
                    }
                }


                //list
                $payment_modes=PaymentMode::all();
                $riders=Rider::where('status',1)->get();
                $special_handlings = TraxSpecialHandlingList::where('status',1)->get();
                $products=TraxProduct::select('id','product_name')->where('parent_id',$trax_parent_product_id)->where('status',1)->get();
                $services=TraxService::whereIn('shipping_mode_id',$shipping_modes_ids)->get();
                $trax_stations=TraxStation::select('id','name')->where('status',1)->get();
                $pickup_addresses=UserShippingInfo::select('id','pickup_address','poc','phone','email')->where('user_id',$logistic_booking->shipper_id)->get();
                $booking_img = TraxLogisticBookingImages::where('booking_id',$logistic_booking->id);

                $booking_img_url=null;
                if ($booking_img->exists())
                {
                    $booking_img = $booking_img->first();
                    $booking_img_url =  Storage::url('logistic_bookings/'. $booking_img->image_name);
                }

<<<<<<< HEAD

=======
                
              // //Log::channel('code_test_log')->error('batch_id = > '.$batch_id.' booking_img_url '.$booking_img->image_name);
               
                //return view('admin.logistic.edit_logistic_book', compact('imageUrl'));
>>>>>>> sprint_130
                return view('admin.logistic.edit_logistic_book')
                    ->with(['batch_id'=>$batch_id,'booking_img_url'=>$booking_img_url,'logistic_booking'=>$logistic_booking,'item_insurance'=>$item_insurance,'item_references'=>$item_references,'booking_pieces'=>$booking_pieces,'payment_modes'=>$payment_modes,'shipper'=>$shipper,'products'=>$products,'services'=>$services,'trax_stations'=>$trax_stations,'pickup_addresses'=>$pickup_addresses,'special_handlings'=>$special_handlings,'riders'=>$riders]);
            } catch (\Exception $th){
                Log::channel('code_test_log')->error('logistic-booking'.json_encode($th->getMessage()));
            }
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
        public function update(Request $request) {
        $admin_id = session('id');
        Validator::extend('item_insurance_required_if_handling', function ($attribute, $value, $parameters, $validator) {
            $specialHandlingId = $validator->getData()['special_handling_id'];

            if (!empty($specialHandlingId) && empty($value)) {
                return false;
            }

            return true;
        });
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
            'dense_weight'=>['nullable','numeric'],
            'volumetric_weight'=>['nullable','numeric'],
            'shipper_address_id'=>['required','integer'],
            'total_pieces'=>['required','integer'],
            'consignee_name'=>['required','string','max:255'],
//            'consignee_phone_1'=>['required','integer'],
//            'consignee_address'=>['required','string','max:255'],
            'consignee_email'=>['nullable','email'],
            'payment_mode_id'=>['nullable','integer'],
            'consignment_type'=>['nullable','max:1','in:L,H'],
            'handling_inst'=>['max:255'],

            'item_insurance_id'=>['nullable','integer'],
            'special_handling_id'=>['nullable','integer'],
            'item_insurance' => [
                'nullable',
                'numeric',
                Rule::requiredIf(function () use ($request) {
                    return !empty($request->special_handling_id);
                }),
            ],
            'insurance_item_code'=>['max:255'],
            'shipper_reference'=>['string','max:255']
        ]);
        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $logistic_booking= TraxLogisticBooking::where('id',$request->booking_id);
        if($logistic_booking->exists())
        {

//            $booking_weight=0;
//            if($request->total_volumetric_weight >= $request->total_dense_weight)
//            {
//                $booking_weight= $request->total_volumetric_weight;
//
//            } else{
//                $booking_weight= $request->total_dense_weight;
//            }


            try {
                     DB::beginTransaction();
                    $logistic_booking=$logistic_booking->first();

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
                    $logistic_booking->total_pieces=$request->total_pieces;
                    $logistic_booking->shipper_reference=$request->shipper_reference;
                    $logistic_booking->save();

                    $shipment=Shipment::where('tracking_number',$request->cn_number);

                    if($shipment->exists())
                    {
                        $shipment=$shipment->first();
//                        if($shipment->consignee_status_id==1 || $shipment->shipper_status_id==1){
//                            $shipment->estimated_weight=$booking_weight;
//                        }
                        //atif sir said shipment weight update regardless booking is arrived or not
                        $shipment->consignee_city_id=$request->destination_id;
                        $shipment->order_id=$request->shipper_reference;
                        $shipment->estimated_weight=$request->booking_weight;
                        $shipment->consignee_name=$request->consignee_name;
                        $shipment->consignee_address=$request->consignee_address;
                        $shipment->consignee_phone_number_1=$request->consignee_phone_1;
                        $shipment->consignee_email=$request->consignee_email;
                        $shipment->save();

                        //update shipment Quantity
                        $shipment_item= ShipmentItem::where('shipment_id',$shipment->id);
                        if($shipment_item->exists()){
                            $shipment_item = $shipment_item->first();
                            $shipment_item->quantity=$request->total_pieces;
                            $shipment_item->save();
                        }
                    }

                    if(isset($request->special_handling_id) && isset($request->item_insurance))
                    {
                        TraxItemInsurance::updateOrCreate(
                            ['id'=>$request->item_insurance_id,'booking_id'=>$request->booking_id],
                            [
                                'booking_id' => $request->booking_id,
                                'special_handling_id' => $request->special_handling_id,
                                'insurance' => $request->item_insurance,
                                'item_code' => $request->insurance_item_code,
                                'updated_by' => $admin_id
                            ]
                        );
                    }

//                    if(isset($request->item_insurance_id))
//                    {
//                        $item_insurance = TraxItemInsurance::where('id',$request->item_insurance_id)->where('booking_id',$request->booking_id);
//                        if($item_insurance->exists()){
//                            $item_insurance = $item_insurance->first();
//                            $item_insurance->special_handling_id=$request->special_handling_id;
//                            $item_insurance->insurance=$request->item_insurance;
//                            $item_insurance->item_code=$request->insurance_item_code;
//                            $item_insurance->updated_by = $admin_id;
//                            $item_insurance->save();
//                        }
//                    }

//                    if(isset($request->no_piece) && is_array($request->no_piece))
//                    {
//                        foreach ($request->no_piece as $key => $value)
//                        {
//
//                            if(isset($request->item_reference_id[$key]))
//                            {
//                                $item_reference = TraxItemRefernce::where('id',$request->item_reference_id[$key])->where('booking_id',$request->booking_id);
//                                if($item_reference->exists())
//                                {
//                                    $item_reference = $item_reference->first();
//                                    $item_reference->width =$request->width[$key];
//                                    $item_reference->height =$request->height[$key];
//                                    $item_reference->length =$request->length[$key];
//                                    $item_reference->weight =$request->weight[$key];
//                                    $item_reference->no_piece =$value;
//                                    $item_reference->created_by =$admin_id;
//                                    $item_reference->updated_by =$admin_id;
//                                    $item_reference->user_type =1;
//                                    $item_reference->save();
//                                }
//                            }else {
//                                $item_reference = new TraxItemRefernce();
//                                $item_reference->booking_id = $request->booking_id;
//                                $item_reference->width = $request->width[$key];
//                                $item_reference->height =$request->height[$key];
//                                $item_reference->length =$request->length[$key];
//                                $item_reference->weight =$request->weight[$key];
//                                $item_reference->no_piece =$value;
//                                $item_reference->created_by =$admin_id;
//                                $item_reference->updated_by =$admin_id;
//                                $item_reference->user_type =1;
//                                $item_reference->save();
//                            }
////                            TraxItemRefernce::updateOrCreate(
////                                isset($request->item_reference_id[$key]) ? // Check if item_reference_id is set
////                                    ['id' => $request->item_reference_id[$key], 'booking_id' => $request->booking_id] :
////                                    [], // Pass null if item_reference_id is not set
////                                [
////                                    'booking_id' => $request->booking_id,
////                                    'width' => $request->width[$key],
////                                    'height' => $request->height[$key],
////                                    'length' => $request->length[$key],
////                                    'weight' => $request->weight[$key],
////                                    'no_piece' => $value,
////                                    'created_by' => $admin_id,
////                                    'updated_by' => $admin_id,
////                                    'user_type' => 1
////                                ]
////                            );
//                        }
//                    }

                // check if booking update through the batch than status update of booking
                if(isset($request->batch_id) && $request->batch_id>0)
                {
                    $batch_booking_detail=TraxBookingBatchDetail::where('status_id',1)
                        ->where('batch_id',$request->batch_id)
                        ->where('booking_id',$request->booking_id);
                    if($batch_booking_detail->exists())
                    {
                        $batch_booking_detail=$batch_booking_detail->first();
                        $batch_booking_detail->status_id=2;
                        $batch_booking_detail->save();

                        $booking_batch =  TraxBookingBatch::find($request->batch_id);
                        if($booking_batch)
                        {
                            $booking_batch->complete_bookings+=1;
                            $booking_batch->save();
                        }
                    }

                    DB::commit();

                    return redirect()->route('admin.logistic.batch.batch_bookings', ['batch_id' => $request->batch_id])
                        ->with('success', 'Logistic booking updated successfully');
                }
                DB::commit();
                return redirect()->route('admin.logistic.index')->with('success', 'Logistic booking updated successfully');


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
