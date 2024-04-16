<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Models\Admin\Logistic\TraxPieceSetting;
use App\Http\Models\Admin\Logistic\TraxParentProduct;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxService;
use App\Http\Models\Admin\Logistic\TraxShipperDetail;
use App\Http\Models\Rider;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        $shippers = User::select('id','name')->where('status',3)->get();
        $riders = Rider::select('id','name','trax_id')->where('status',1)->whereNotNull('route_id')->get();
        $products =  TraxProduct::select('id','product_code','product_name','parent_id')
            ->where('status',1)->get();

        $services = TraxService::select('id','service_code','service_name','product_id')
            ->where('status',1)->get();

        $piece_settings = TraxPieceSetting::where('status',1)->get();

        return view('admin.logistic.shipper_tagging')->with(['shippers'=>$shippers,'riders'=>$riders,'products'=>$products,'services'=>$services,'piece_settings'=>$piece_settings]);
    }
    public function shipper_tagging_list()
    {
        $trax_shipper_detail = TraxShipperDetail::join('users as u','u.id','trax_shipper_details.user_id')
            ->join('riders as rd','rd.id','=','trax_shipper_details.rider_id')
            ->join('trax_products as tp','tp.id','=','trax_shipper_details.trax_product_id')
            ->join('trax_services as ts','ts.id','=','trax_shipper_details.trax_service_id')
            ->join('trax_piece_settings as ps','ps.id','=','trax_shipper_details.piece_setting_id')
            ->leftjoin('routes as r','r.id','=','rd.route_id')
            ->SELECT('trax_shipper_details.id','u.id as shipper_id','u.name as shipper_name','rd.trax_id as rider_trax_id','rd.name as rider_name','tp.product_name','ts.service_name','r.code as route_code','r.id as route_id','ps.description as piece_setting')
            ->where('trax_shipper_details.status',1);

        $datatables = Datatables::of($trax_shipper_detail)
            ->addColumn('action',function ($trax_shipper_detail){
                if (session('role_id') == 1 || count(array_intersect([83, 84, 507], session('permissions'))) !== 0) {
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
            'trax_service_id' => ['required','integer']
        ],$this->validation_messages);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {

            $shipper_detail = new TraxShipperDetail();
            $shipper_detail->user_id = $request->user_id;
            $shipper_detail->trax_product_id = $request->trax_product_id;
            $shipper_detail->trax_service_id = $request->trax_service_id;
            $shipper_detail->rider_id = $request->rider_id;
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
    public function shipper_tagging_update()
    {

    }
     public function master_product_index()
     {
        return view('admin.logistic.master_product');
     }

     public  function master_product_list()
     {
         $master_product = TraxParentProduct::all();
         $datatables = Datatables::of($master_product)
         ->editColumn('status',function ($master_product){
             if($master_product->status == 1)
             {
                return 'Active';
             }
             return  'Inactive';
         });
         return $datatables->make(true);
     }

     public function master_product_store(Request $request)
     {
         $validate = Validator::make($request->all(),[
             'parent_code' => ['required','max:255'],
             'parent_name' => ['required','max:255'],
         ]);


         if($validate->fails())
         {
             return redirect()->back()->withErrors($validate)->withInput();
         }
         try {
             $parent_product = new TraxParentProduct();
             $parent_product->parent_code = $request->parent_code;
             $parent_product->parent_name = $request->parent_name;
             $parent_product->save();
             return redirect()->back()->with('success','Master product added successfully');
         } catch (\Exception $ex) {
             return redirect()->back()->with('error','Master product not add');
         }

     }

     public  function  product_index()
     {
         $parent_products = TraxParentProduct::select('id','parent_name')->where('status',1)->get();
         return view('admin.logistic.product')->with(['parent_products'=>$parent_products]);

     }

    public  function  product_list()
    {
        $product = TraxProduct::join('trax_parent_products as pp','pp.id','trax_products.parent_id')
        ->select('trax_products.id','trax_products.product_code','trax_products.product_name','pp.parent_name as master_product_name','trax_products.status');

        $datatables = Datatables::of($product)
            ->editColumn('status',function ($product){
                if($product->status == 1)
                {
                    return 'Active';
                }
                return  'Inactive';
            });
        return $datatables->make(true);
    }

    public  function  product_store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'product_code' => ['required','max:255'],
            'product_name' => ['required','max:255'],
            'parent_id' => ['required','integer']
        ]);


        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        try {
            $product = new TraxProduct();
            $product->parent_code = $request->parent_code;
            $product->parent_name = $request->parent_name;
            $product->parent_id = $request->parent_id;
            $product->save();
            return redirect()->back()->with('success','Product added successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error','Product not add');
        }
    }


    public  function service_index()
    {
        $products = TraxProduct::select('id','product_name')->where('status',1)->get();
        return view('admin.logistic.service')->with(['products'=>$products]);
    }

    public  function  service_list()
    {
        $product = TraxService::join('trax_products as p','p.id','trax_services.product_id')
            ->select('trax_services.id','trax_services.service_code','trax_services.service_name','p.product_name as product_name','trax_services.status');

        $datatables = Datatables::of($product)
            ->editColumn('status',function ($product){
                if($product->status == 1)
                {
                    return 'Active';
                }
                return  'Inactive';
            });
        return $datatables->make(true);
    }

    public function service_store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'service_code' => ['required','max:255'],
            'service_name' => ['required','max:255'],
            'product_id' => ['required','integer']
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
            $service->save();
            return redirect()->back()->with('success','Service added successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error','Service not add');
        }
    }
}
