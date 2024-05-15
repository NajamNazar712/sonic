<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Logistic\TraxPieceSetting;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxShipperDetail;
use App\Http\Models\Rider;
use App\Http\Models\Segment;
use App\Http\Models\ShippingMode;
use App\Http\Models\SubCategorySegment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class AdminLogisticSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }


    public function shipper_tagging_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 779);

        $shippers = User::select('id','name')->where('status',3)->get();
        $riders = Rider::select('id','name','trax_id')->where('status',1)->whereNotNull('route_id')->get();
        $products =  TraxProduct::select('id','product_code','product_name','parent_id')
            ->where('status',1)->get();

        $services = TraxService::select('id','service_code','service_name','product_id')
            ->where('status',1)->get();

        $piece_settings = TraxPieceSetting::where('status',1)->get();

        return view('admin.logistic.shipper_tagging')->with(['shippers'=>$shippers,'riders'=>$riders,'products'=>$products,'services'=>$services,'piece_settings'=>$piece_settings]);
    }
    public function shipper_tagging_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 780);
        }

        $trax_shipper_detail = TraxShipperDetail::join('users as u','u.id','trax_shipper_details.user_id')
            ->leftjoin('riders as rd','rd.id','=','trax_shipper_details.rider_id')
            ->join('trax_products as tp','tp.id','=','trax_shipper_details.trax_product_id')
            ->join('trax_services as ts','ts.id','=','trax_shipper_details.trax_service_id')
//            ->join('trax_piece_settings as ps','ps.id','=','trax_shipper_details.piece_setting_id')
            ->leftjoin('routes as r','r.id','=','rd.route_id')
            ->SELECT('trax_shipper_details.id','u.id as shipper_id','u.name as shipper_name','rd.trax_id as rider_trax_id','rd.name as rider_name','tp.product_name','ts.service_name','r.code as route_code','r.id as route_id')
            ->where('trax_shipper_details.git ',1);

        $datatables = Datatables::of($trax_shipper_detail)
            ->addColumn('action',function ($trax_shipper_detail){
                if (session('role_id') == 1 || count(array_intersect([975], session('permissions'))) !== 0) {
                        $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

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

    public  function  shipper_tagging_store(Request $request)
    {

        $user_id = session('id');
        $validate = Validator::make($request->all(),[
            'user_id' => ['required','integer'],
            'rider_id' => ['required','integer'],
            'trax_product_id' => ['required','integer'],
            'trax_service_id' => ['required','integer'],
//            'piece_setting_id'=>['required','integer']

        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {

            $shipper_tagging=TraxShipperDetail::where('user_id',$request->user_id)
                ->where('trax_product_id',$request->trax_product_id);
            if($shipper_tagging->exists())
            {
                return redirect()->back()->with('error','Shipper already tag with rider and product');
            }
            $shipper_detail = new TraxShipperDetail();
            $shipper_detail->user_id = $request->user_id;
            $shipper_detail->trax_product_id = $request->trax_product_id;
            $shipper_detail->trax_service_id = $request->trax_service_id;
            $shipper_detail->rider_id = $request->rider_id;
//            $shipper_detail->piece_setting_id=$request->piece_setting_id;
            $shipper_detail->created_by = $user_id;
            $shipper_detail->save();

            return redirect()->back()->with('success','Logistic Shipper Tagging Successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed Logistic Shipper Tagging');
        }
    }

    public  function shipper_tagging_edit($id)
    {
        $shipper_tagging= TraxShipperDetail::where('id',$id)->where('status',1);
        if($shipper_tagging->exists())
        {
            $shipper_tagging = $shipper_tagging->first();
            return response()->json(['status'=>0,'shipper_tagging'=>$shipper_tagging]);
        }

        return  response()->json(['status'=>1,'error'=>'No Shipper Tagging found!']);
    }
    public function shipper_tagging_update(Request $request)
    {

        $user_id = session('id');
        $validate = Validator::make($request->all(),[
            'shipper_tagging_id'=>['required','integer'],
            'user_id' => ['required','integer'],
            'rider_id' => ['required','integer'],
            'trax_product_id' => ['required','integer'],
            'trax_service_id' => ['required','integer'],
            'piece_setting_id'=>['required','integer']
        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $shipper_detail = TraxShipperDetail::find($request->shipper_tagging_id);
            $shipper_detail->user_id = $request->user_id;
            $shipper_detail->trax_product_id = $request->trax_product_id;
            $shipper_detail->trax_service_id = $request->trax_service_id;
            $shipper_detail->rider_id = $request->rider_id;
            $shipper_detail->piece_setting_id=$request->piece_setting_id;
            $shipper_detail->updated_by = $user_id;
            $shipper_detail->save();

            return redirect()->back()->with('success','Logistic Shipper Tagging Successfully Update');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed to Update Logistic Shipper Tagging');
        }
    }
     public function master_product_index()
     {
         ActivityTrailController::createActivityTrailLog(Auth::id(), 765);

         $segments=Segment::all();
         return view('admin.logistic.master_product')->with('segments',$segments);
     }

     public  function master_product_list(Request $request)
     {
         if ($request->get('excel') && $request->get('excel') == true) {
             ActivityTrailController::createActivityTrailLog(Auth::id(), 766);
         }

         $master_product = TraxParentProduct::leftjoin('segments as s','s.id','trax_parent_products.segment_id')
         ->select('trax_parent_products.id','trax_parent_products.parent_code','trax_parent_products.parent_name','s.name as segment_name','trax_parent_products.status')
             ->where('status',1);
         $datatables = Datatables::of($master_product)
         ->editColumn('status',function ($master_product){
             if($master_product->status == 1)
             {
                return 'Active';
             }
             return  'Inactive';
         }) ->addColumn('action',function ($trax_shipper_detail){
         if (session('role_id') == 1 || count(array_intersect([954], session('permissions'))) !== 0) {
             $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

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

     public function master_product_store(Request $request)
     {
         $validate = Validator::make($request->all(),[
             'parent_code' => ['required','max:255'],
             'parent_name' => ['required','max:255'],
             'segment_id'=>['required','integer']
         ]);


         if($validate->fails())
         {
             return redirect()->back()->withErrors($validate)->withInput();
         }
         try {
             $parent_product = new TraxParentProduct();
             $parent_product->parent_code = $request->parent_code;
             $parent_product->parent_name = $request->parent_name;
             $parent_product->segment_id = $request->segment_id;
             $parent_product->save();
             return redirect()->back()->with('success','Master product added successfully');
         } catch (\Exception $ex) {
             return redirect()->back()->with('error','Master product not add');
         }

     }

     public function master_product_edit($id)
     {
         $master_product= TraxParentProduct::where('id',$id)->where('status',1);
         if($master_product->exists())
         {
             $master_product = $master_product->first();
             return response()->json(['status'=>0,'master_product'=>$master_product]);
         }

         return  response()->json(['status'=>1,'error'=>'No Master Product found!']);
     }

     public function master_product_update(Request $request) {

        $validate = Validator::make($request->all(),[
            'id'=>['required','integer'],
            'parent_code' => ['required','string'],
            'parent_name' => ['required','string'],
            'segment_id'   =>['required','integer']

        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $parent_product = TraxParentProduct::find($request->id);
            $parent_product->parent_code = $request->parent_code;
            $parent_product->parent_name = $request->parent_name;
            $parent_product->segment_id = $request->segment_id;

            $parent_product->save();

            return redirect()->back()->with('success','Master Product Successfully Update');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed to Update Master Product');
        }
    }


     public  function  product_index()
     {
         ActivityTrailController::createActivityTrailLog(Auth::id(), 767);

         $parent_products = TraxParentProduct::select('id','parent_name')->where('status',1)->get();
         $sub_segments = SubCategorySegment::select('id','name')->get();

         return view('admin.logistic.product')->with(['parent_products'=>$parent_products,'sub_segments'=>$sub_segments]);

     }

    public  function  product_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 768);
        }

        $product = TraxProduct::join('trax_parent_products as pp','pp.id','trax_products.parent_id')
            ->leftjoin('sub_category_segments as sb','sb.id','trax_products.sub_segment_id')
        ->select('trax_products.id','trax_products.product_code','trax_products.product_name','pp.parent_name as master_product_name','sb.name as sub_segment_name','trax_products.status');

        $datatables = Datatables::of($product)
            ->editColumn('status',function ($product){
                if($product->status == 1)
                {
                    return 'Active';
                }
                return  'Inactive';
            }) ->addColumn('action',function ($trax_shipper_detail){
                if (session('role_id') == 1 || count(array_intersect([957], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

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

    public  function  product_store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'product_code' => ['required','max:255'],
            'product_name' => ['required','max:255'],
            'parent_id' => ['required','integer'],
            'sub_segment_id' => ['required','integer']

        ]);


        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            $product = new TraxProduct();
            $product->product_code = $request->product_code;
            $product->product_name = $request->product_name;
            $product->parent_id = $request->parent_id;
            $product->sub_segment_id = $request->sub_segment_id;
            $product->save();
            return redirect()->back()->with('success','Product added successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error','Product not add');
        }
    }

    public function product_edit($id)
    {
        $product= TraxProduct::where('id',$id)->where('status',1);
        if($product->exists())
        {
            $product = $product->first();
            return response()->json(['status'=>0,'product'=>$product]);
        }

        return  response()->json(['status'=>1,'error'=>'No Product found!']);
    }

    public function product_update(Request $request) {

        $validate = Validator::make($request->all(),[
            'product_id'=>['required','integer'],
            'parent_id'=>['required','integer'],
            'product_code' => ['required','string'],
            'product_name' => ['required','string'],
            'sub_segment_id' => ['required','integer']

        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $product = TraxProduct::find($request->product_id);
            $product->product_code = $request->product_code;
            $product->product_name = $request->product_name;
            $product->parent_id = $request->parent_id;
            $product->sub_segment_id = $request->sub_segment_id;

            $product->save();

            return redirect()->back()->with('success','Product Successfully Update');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed to Update Product');
        }
    }


    public  function service_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 769);

        $products = TraxProduct::select('id','product_name')->where('status',1)->get();
        $shipping_modes = ShippingMode::select('id','mode')->get();

        return view('admin.logistic.service')->with(['products'=>$products,'shipping_modes'=>$shipping_modes]);
    }

    public  function  service_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 770);
        }
        $product = TraxService::join('trax_products as p','p.id','trax_services.product_id')
            ->leftjoin('shipping_modes as sm','sm.id','trax_services.shipping_mode_id')
            ->select('trax_services.id','trax_services.service_code','trax_services.service_name','p.product_name as product_name','sm.mode as shipping_mode_name','trax_services.status');

        $datatables = Datatables::of($product)
            ->editColumn('status',function ($product){
                if($product->status == 1)
                {
                    return 'Active';
                }
                return  'Inactive';
            })->addColumn('action',function ($trax_shipper_detail){
                if (session('role_id') == 1 || count(array_intersect([960], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

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

    public function service_store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'service_code' => ['required','max:255'],
            'service_name' => ['required','max:255'],
            'product_id' => ['required','integer'],
            'shipping_mode_id' => ['required','integer']

        ]);


        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            $service = new TraxService();
            $service->service_code = $request->service_code;
            $service->service_name = $request->service_name;
            $service->product_id = $request->product_id;
            $service->shipping_mode_id = $request->shipping_mode_id;
            $service->save();
            return redirect()->back()->with('success','Service added successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error','Service not add');
        }
    }

    public function service_edit($id)
    {
        $service= TraxService::where('id',$id)->where('status',1);
        if($service->exists())
        {
            $service = $service->first();
            return response()->json(['status'=>0,'service'=>$service]);
        }

        return  response()->json(['status'=>1,'error'=>'No Service found!']);
    }

    public function service_update(Request $request) {

        $validate = Validator::make($request->all(),[
            'service_id'=>['required','integer'],
            'product_id'=>['required','integer'],
            'service_code' => ['required','string'],
            'service_name' => ['required','string'],
            'shipping_mode_id' => ['required','integer']

        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $service = TraxService::find($request->service_id);
            $service->service_code = $request->service_code;
            $service->service_name = $request->service_name;
            $service->product_id = $request->product_id;
            $service->shipping_mode_id = $request->shipping_mode_id;
            $service->save();

            return redirect()->back()->with('success','Service Successfully Update');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed to Update Service');
        }
    }

}
