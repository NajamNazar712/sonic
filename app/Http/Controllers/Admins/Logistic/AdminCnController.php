<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Logistic\TraxChildCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxChildCnReceiveAdminStore;
use App\Http\Models\Admin\Logistic\TraxProduct;
use App\Http\Models\Admin\Logistic\TraxRiderChildCnDetail;
use App\Http\Models\Admin\Logistic\TraxRiderCnDetail;
use App\Http\Models\Admin\Logistic\TraxStation;
use App\Http\Models\City;
use App\Http\Models\Admin\Logistic\TraxCnIssueAreaStore;
use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxCnReceiveAdminStore;
use App\Http\Models\Rider;
use App\Http\Models\Segment;
use App\Http\Models\Shipper\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use function foo\func;

class AdminCnController extends Controller
{

    protected $validation_messages = [
            'company_code' => [
                'required' => 'The company code is required.',
                'max' => 'The company code must not exceed 255 characters.',
            ],
            'area_code' => [
                'required' => 'The area code is required.',
                'max' => 'The area code must not exceed 255 characters.',
            ],
            'cn_from' => [
                'required' => 'The "from" CN field is required.',
                'integer' => 'The "from" CN must be an integer.',
            ],
            'cn_to' => [
                'required' => 'The "to" CN field is required.',
                'integer' => 'The "to" CN must be an integer.',
            ],
            'quantity' => [
                'required' => 'The quantity field is required.',
                'integer' => 'The quantity must be an integer.',
            ],
            'receive_date'=>[
                'required' => 'The receive date field is required.',
                'date' => 'The receive date must be an date.'
            ]
        ];
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public  function  cn_area_store_index()
    {
        // ActivityTrailController::createActivityTrailLog(Auth::id(), 739);
        $products =  TraxProduct::where('status',1)->get();
        $cities = TraxStation::where('status',1)->get();
        return view('admin.logistic.cn_issue_area_store')->with(['products'=> $products,'cities'=>$cities]);
    }

    public function cn_area_store_list(Request $request)
    {
        //        if ($request->get('excel') && $request->get('excel') == true) {
        //            ActivityTrailController::createActivityTrailLog(Auth::id(), 740);
        //        }
        $trax_cn_issue_area_stores = TraxCnIssueAreaStore::Join('segments as s','trax_cn_issue_area_stores.product_id','=','s.id')
            ->Join('cities as c','c.id','=','trax_cn_issue_area_stores.area_code')
            ->SELECT('trax_cn_issue_area_stores.issue_date','trax_cn_issue_area_stores.company_code','trax_cn_issue_area_stores.area_code','c.name as area_name','trax_cn_issue_area_stores.product_id','s.name as segment_name','trax_cn_issue_area_stores.cn_from','trax_cn_issue_area_stores.cn_to','trax_cn_issue_area_stores.quantity')
            ->where('trax_cn_issue_area_stores.status',1);

        // if (session('role_id') != 1) {
        //     $cargo->where('cargo_manifest_draft_bags.origin_id', Auth::user()->default_hub_id);
        // }

        $datatables = Datatables::of($trax_cn_issue_area_stores);

        return $datatables->make(true);
    }

    public function add_cn_area_store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'area_code' => ['required','max:255'],
            'product_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer'],
            'quantity' => ['required','integer'],
            'issue_date' => ['required','date'],
        ]);

        if($validate->fails())
        {
            return redirect()->back()->with('error',$validate->errors());
        }

        try {
            $cn_issue_area_store = new TraxCnIssueAreaStore();
            $cn_issue_area_store->company_code = $request->company_code;
            $cn_issue_area_store->area_code = $request->area_code;
            $cn_issue_area_store->product_id = $request->product_id;
            $cn_issue_area_store->cn_from = $request->cn_from;
            $cn_issue_area_store->cn_to = $request->cn_to;
            $cn_issue_area_store->quantity = $request->quantity;
            $cn_issue_area_store->issue_date = $request->issue_date;
            $cn_issue_area_store->save();
            return redirect()->back()->with('success','CN issue to area store successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed CN issue to area store');
        }
    }

    public  function cn_receive_admin_store_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 771);

        $products =  TraxProduct::where('status',1)->get();
        $cities = TraxStation::where('status',1)->get();
        return view('admin.logistic.cn_receive_admin_store')->with(['products'=> $products,'cities'=>$cities]);
    }

    public  function cn_receive_admin_store_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 772);
        }
        $trax_cn_receive_admin_stores = TraxCnReceiveAdminStore::Join('trax_products as s','trax_cn_receive_admin_stores.product_id','=','s.id')
            ->Join('cities as c','c.id','=','trax_cn_receive_admin_stores.area_code')
            ->SELECT('trax_cn_receive_admin_stores.id','trax_cn_receive_admin_stores.receive_date','trax_cn_receive_admin_stores.company_code','trax_cn_receive_admin_stores.area_code','c.name as area_name','trax_cn_receive_admin_stores.product_id','s.product_name as segment_name','trax_cn_receive_admin_stores.cn_from','trax_cn_receive_admin_stores.cn_to','trax_cn_receive_admin_stores.quantity')
            ->where('trax_cn_receive_admin_stores.status',1);


        $datatables = Datatables::of($trax_cn_receive_admin_stores)
            ->addColumn('action',function ($trax_cn_receive_admin_stores){
                if (session('role_id') == 1 || count(array_intersect([962], session('permissions'))) !== 0) {
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

    public  function cn_receive_admin_store_store(Request  $request)
    {
        $user_id = Auth::id();
        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'area_code' => ['required','max:255'],
            'product_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer']
//            'quantity' => ['required','integer'],
//            'receive_date' => ['required','date'],
        ],$this->validation_messages);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {

            if($request->cn_to < $request->cn_from)
            {
                return  redirect()->back()->with('error','CN To Must be grater than or equal to  CN From!');
            }
            $cn_store =  TraxCnReceiveAdminStore::where('area_code', $request->area_code)
                ->where(function ($query) use ($request) {
                    $query->where('cn_from', '<=', $request->cn_from)
                        ->where('cn_to', '>=', $request->cn_from)
                        ->orWhere('cn_from', '<=', $request->cn_to)
                        ->where('cn_to', '>=', $request->cn_to)
                        ->orWhere('cn_from', '>=',$request->cn_from)
                        ->where('cn_to', '<=', $request->cn_to);
                })->where('status',1);

            if($cn_store->exists()){
                return  redirect()->back()->with('error','Duplicate Cn found!');
            }

            $date= Carbon::now()->toDateString();
            $quantity = ($request->cn_to-$request->cn_from+1);

            $cn_receive_admin_store = new TraxCnReceiveAdminStore();
            $cn_receive_admin_store->company_code = $request->company_code;
            $cn_receive_admin_store->area_code = $request->area_code;
            $cn_receive_admin_store->product_id = $request->product_id;
            $cn_receive_admin_store->cn_from = $request->cn_from;
            $cn_receive_admin_store->cn_to = $request->cn_to;
            $cn_receive_admin_store->quantity = $quantity;
            $cn_receive_admin_store->receive_date = $date;
            $cn_receive_admin_store->user_id = $user_id;
            $cn_receive_admin_store->save();

            return redirect()->back()->with('success','CN Receive Admin store successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed CN receive admin store');
        }
    }

    public function cn_receive_admin_store_edit($id)
    {
        $cn_receive_admin_store= TraxCnReceiveAdminStore::where('id',$id)->where('status',1);
        if($cn_receive_admin_store->exists())
        {
            $cn_receive_admin_store = $cn_receive_admin_store->first();
            return response()->json(['status'=>0,'cn_receive_admin_store'=>$cn_receive_admin_store]);
        }

        return  response()->json(['status'=>1,'error'=>'No CN found in admin store!']);
    }

    public  function cn_receive_admin_store_update(Request  $request)
    {

        $user_id=session('id');
        $validate = Validator::make($request->all(),[
            'id' => ['required','integer'],
            'company_code' => ['required','max:255'],
            'area_code' => ['required','max:255'],
            'product_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer']
        ],$this->validation_messages);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $date= Carbon::now()->toDateString();
            $quantity = ($request->cn_to-$request->cn_from+1);

            $cn_receive_admin_store = TraxCnReceiveAdminStore::find($request->id);
            $cn_receive_admin_store->company_code = $request->company_code;
            $cn_receive_admin_store->area_code = $request->area_code;
            $cn_receive_admin_store->product_id = $request->product_id;
            $cn_receive_admin_store->cn_from = $request->cn_from;
            $cn_receive_admin_store->cn_to = $request->cn_to;
            $cn_receive_admin_store->quantity = $quantity;
            $cn_receive_admin_store->receive_date = $date;
            $cn_receive_admin_store->updated_by=$user_id;
            $cn_receive_admin_store->save();

            return redirect()->back()->with('success','CN update admin store successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed CN update admin store');
        }
    }

    public  function cn_issue_to_rider_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 773);

        $products =  TraxProduct::where('status',1)->get();
        $riders =  Rider::where('status',1)->get();
        return view('admin.logistic.cn_issue_rider')->with(['products'=> $products,'riders'=>$riders]);
    }

    public function  cn_issue_to_rider_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 774);
        }
        $trax_cn_issue_rider = TraxCnIssueToRider::Join('trax_products as s','trax_cn_issue_to_riders.product_id','=','s.id')
            ->join('riders as rd','rd.id','=','trax_cn_issue_to_riders.rider_id')
            ->SELECT('trax_cn_issue_to_riders.id','trax_cn_issue_to_riders.issue_date','trax_cn_issue_to_riders.company_code','rd.name as rider_name','rd.trax_id as rider_trax_id','trax_cn_issue_to_riders.product_id','s.product_name as segment_name','trax_cn_issue_to_riders.cn_from','trax_cn_issue_to_riders.cn_to','trax_cn_issue_to_riders.quantity')
            ->where('trax_cn_issue_to_riders.status',1);

        $datatables = Datatables::of($trax_cn_issue_rider)
            ->addColumn('action',function ($trax_cn_issue_rider){
                if (session('role_id') == 1 || count(array_intersect([965], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $cn_list_link = '<a href="'.route('admin.logistic.cn.issue_to_rider.cn_index',['issue_id'=>$trax_cn_issue_rider->id]).'" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-search"></i></div><div class="col-9 offset-1">View CN List</div></div></a>';



                    $dropdown = '
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                              <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    $dropdown .= $edit_button;
                    $dropdown .= $cn_list_link;
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
    public function cn_issue_to_rider_store(Request $request)
    {
        $user_id = session('id');
        $admin = Admin::where('id',$user_id)->whereNotNull('default_hub_id');
        $cn_exist=0;
        if(!$admin->exists())
        {
            return redirect()->back()->with('error','Hub not found!');
        }
        $admin = $admin->first();
        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'rider_id' => ['required','integer'],
            'product_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer'],
//            'quantity' => ['required','integer'],
//            'issue_date' => ['required','date']
        ]);

        if($validate->fails())
        {
            return redirect()->back()->with('error',$validate->errors());
        }

        try {

            if($request->cn_to < $request->cn_from)
            {
                return  redirect()->back()->with('error','CN To Must be grater than or equal to  CN From!');
            }

            $cn_issue =  TraxCnIssueToRider::where('area_code',  $admin->default_hub_id)
                ->where(function ($query) use ($request) {
                    $query->where('cn_from', '<=', $request->cn_from)
                        ->where('cn_to', '>=', $request->cn_from)
                        ->orWhere('cn_from', '<=', $request->cn_to)
                        ->where('cn_to', '>=', $request->cn_to)
                        ->orWhere('cn_from', '>=',$request->cn_from)
                        ->where('cn_to', '<=', $request->cn_to);
                })->where('status',1);


            if($cn_issue->exists()){
                return  redirect()->back()->with('error','CN Issued Already to Rider');
            }

//            $cn_store =  TraxCnReceiveAdminStore::where('area_code', $admin->default_hub_id)
//                ->where(function ($query) use ($request) {
//                    $query->where('cn_from', '<=', $request->cn_from)
//                        ->where('cn_to', '>=', $request->cn_from)
//                        ->orWhere('cn_from', '<=', $request->cn_to)
//                        ->where('cn_to', '>=', $request->cn_to)
//                        ->orWhere('cn_from', '>=',$request->cn_from)
//                        ->where('cn_to', '<=', $request->cn_to);
//                })->where('status',1)
//                ->latest('id');
//            $cn_store = TraxCnReceiveAdminStore::where('area_code', 202)
//                ->where('cn_from', '<=', 20202000021)
//                ->where('cn_to', '>=', 20202000029)
//                ->where('status',1)
//                ->latest('id');
            $cn_store =  TraxCnReceiveAdminStore::select('cn_from','cn_to')->where('status',1)->where('area_code', $admin->default_hub_id);
            if($cn_store->exists())
            {
                $cn_store = $cn_store->get();
                foreach ($cn_store as $cn)
                {
                    if($request->cn_from >= $cn->cn_from  &&  $request->cn_to <= $cn->cn_to)
                    {
                        $cn_exist=1;
                        break;
                    }else{
                        $cn_exist=0;
                    }
                }
            }else{
                return redirect()->back()->with('error','CN not found in admin store!');
            }
            if($cn_exist==1){

                $time_stamp = now();
                $quantity = ($request->cn_to - $request->cn_from + 1);
                $child_cn = [];

                DB::beginTransaction();

                $trax_cn_issue_rider = new TraxCnIssueToRider();
                $trax_cn_issue_rider->company_code = $request->company_code;
                $trax_cn_issue_rider->rider_id = $request->rider_id;
                $trax_cn_issue_rider->product_id = $request->product_id;
                $trax_cn_issue_rider->area_code = $admin->default_hub_id;
                $trax_cn_issue_rider->cn_from = $request->cn_from;
                $trax_cn_issue_rider->cn_to = $request->cn_to;
                $trax_cn_issue_rider->quantity = $quantity;
                $trax_cn_issue_rider->issue_date = $time_stamp->toDateString();
                $trax_cn_issue_rider->save();

                for ($i = $request->cn_from; $i <= $request->cn_to; $i++) {

                    $child_cn[] = [
                        'cn_issue_id' => $trax_cn_issue_rider->id,
                        'cn_number' => $i,
                        'created_at' => $time_stamp,
                        'updated_at' => $time_stamp
                    ];
                }
                TraxRiderCnDetail::insert($child_cn);
                DB::commit();


                return redirect()->back()->with('success','CN issue to rider successfully');
            } else {
                return redirect()->back()->with('error','CN not found in admin store!');
            }



        } catch (\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error','Failed CN issue to rider');
        }
    }
    public function cn_issue_to_rider_edit($id)
    {
        $trax_cn_issue_rider= TraxCnIssueToRider::where('id',$id)->where('status',1);
        if($trax_cn_issue_rider->exists())
        {
            $trax_cn_issue_rider = $trax_cn_issue_rider->first();
            return response()->json(['status'=>0,'trax_cn_issue_rider'=>$trax_cn_issue_rider]);
        }

        return  response()->json(['status'=>1,'error'=>'No CN issue to rider found!']);
    }

    public  function cn_issue_to_rider_update(Request  $request)
    {

        $user_id=session('id');
        $validate = Validator::make($request->all(),[
            'id' => ['required','integer'],
            'company_code' => ['required','max:255'],
            'rider_id' => ['required','integer'],
            'product_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer'],
        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $time_stamp = now();
//            $quantity = ($request->cn_to-$request->cn_from+1);
//            $child_cn = [];

//            DB::beginTransaction();

            $trax_cn_issue_rider = TraxCnIssueToRider::find($request->id);
            $trax_cn_issue_rider->company_code = $request->company_code;
            $trax_cn_issue_rider->rider_id = $request->rider_id;
            $trax_cn_issue_rider->product_id = $request->product_id;
//            $trax_cn_issue_rider->cn_from = $request->cn_from;
//            $trax_cn_issue_rider->cn_to = $request->cn_to;
//            $trax_cn_issue_rider->quantity = $quantity;
            $trax_cn_issue_rider->issue_date = $time_stamp->toDateString();
            $trax_cn_issue_rider->updated_by=$user_id;
            $trax_cn_issue_rider->save();

            // delete exiting issue rider cns
            // $trax_rider_cn = TraxRiderCnDetail::where('cn_issue_id',$trax_cn_issue_rider->id);
            //  if($trax_rider_cn->exists())
            //    $trax_rider_cn->delete();


        //            for ($i = $request->cn_from; $i <= $request->cn_to; $i++) {
        //                $child_cn[] = [
        //                    'cn_issue_id' => $trax_cn_issue_rider->id,
        //                    'cn_number' => $i,
        //                    'created_at' => $time_stamp,
        //                    'updated_at' => $time_stamp
        //                ];
        //            }
        //
        //            TraxRiderCnDetail::insert($child_cn);



//            DB::commit();
            return redirect()->back()->with('success','CN update issue to rider successfully');

        } catch (\Exception $exception){
//            DB::rollBack();
            return redirect()->back()->with('error','Failed CN update issue to rider');
        }
    }

    public  function rider_cn_index($issue_id)
    {
        return view('admin.logistic.rider_cn_list')->with('issue_id',$issue_id);
    }
    public  function rider_cn_list(Request $request)
    {
        $rider_cn_list = TraxRiderCnDetail::Join('trax_cn_issue_to_riders as ir','ir.id','trax_rider_cn_details.cn_issue_id')
            ->join('riders as r','r.id','ir.rider_id')
            ->select('trax_rider_cn_details.id','trax_rider_cn_details.cn_number','trax_rider_cn_details.is_used','ir.rider_id','r.name as rider_name','r.trax_id')
            ->where('trax_rider_cn_details.cn_issue_id',$request->rider_issue_id)
            ->where('ir.status',1)
            ->where('trax_rider_cn_details.is_hold',0);


        $datatables = Datatables::of($rider_cn_list)
        ->editColumn('is_used',function ($rider_cn_list){
            if($rider_cn_list->is_used==1)
            {
                return 'Used';
            }
            return  'Not Used';
        })->addColumn('barcode',function($rider_cn_list){
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $cn_number = (string)($rider_cn_list->cn_number);
                $html = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($cn_number, $generator::TYPE_CODE_128, 2, 70)) . '" class="img-fluid mx-auto d-block h-auto">';
                return $html;
        });
        return $datatables->make(true);
    }

    public function cn_barcodes_print(Request $request)
    {
        $ids = $request->ids;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '<!doctype html>
            <html lang="en">
              <head>
                <meta charset="utsf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">
                <title>Logistic CN Barcodes</title>
                <style type="text/css">
                  * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                  }
                  body {
                    background: none !important;
                    color: #000 !important;
                  }
                  .pwrapper {margin: auto; page-break-inside: avoid;}
                  .logo {margin-bottom:5px;}
                  .logo img {margin-bottom:2.5px; filter: brightness(0);}
                  .logo span {font-size: 8px;}
                  .barcode span {font-size: 12px;}
                  @media print {
                   html, body {min-width:auto!important; min-height:auto!important;}
                   @page {margin:0 !important; size: landscape;}
                   .pwrapper {margin: auto; page-break-inside: avoid;}
                   .logo span {font-size: 8px;}
                   .barcode span {font-size: 12px;}
                  }
                </style>
              </head>
              <body>
        ';

        $barcodes = '';

        foreach ($ids as $id) {
            $record = TraxRiderCnDetail::find($id);
            $cn_number = (string)($record->cn_number);
            $barcodes .= '
                <div class="text-center pwrapper p-1">
                    <div class="logo">
                        <img src="' . asset('img/trax_logo_new.png') . '" width="75" class="d-block mx-auto">
                    </div>
                    <div class="barcode">
                        <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($cn_number, $generator::TYPE_CODE_128, 2, 70)) . '" class="img-fluid mx-auto d-block h-auto">
                        <span class="d-block"><strong>* ' . $cn_number . ' *</strong></span>
                    </div>
                </div>
            ';
        }

        $html .= $barcodes;

        $html .= '
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


    public function cn_child_receive_admin_store_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 775);

        $cities = TraxStation::where('status',1)->get();
        return view('admin.logistic.cn_child_receive_admin_store')->with(['cities'=>$cities]);
    }

    public function cn_child_receive_admin_store_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 776);
        }
        $trax_child_cn_receive_admin_stores = TraxChildCnReceiveAdminStore::Join('cities as c','c.id','=','trax_child_cn_receive_admin_stores.area_code')
            ->SELECT('trax_child_cn_receive_admin_stores.id','trax_child_cn_receive_admin_stores.receive_date','trax_child_cn_receive_admin_stores.company_code','trax_child_cn_receive_admin_stores.area_code','c.name as area_name','trax_child_cn_receive_admin_stores.cn_from','trax_child_cn_receive_admin_stores.cn_to','trax_child_cn_receive_admin_stores.quantity')
            ->where('trax_child_cn_receive_admin_stores.status',1);

        $datatables = Datatables::of($trax_child_cn_receive_admin_stores)
            ->addColumn('action',function ($trax_child_cn_receive_admin_stores){
                if (session('role_id') == 1 || count(array_intersect([968], session('permissions'))) !== 0) {
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

    public function cn_child_receive_admin_store_store(Request  $request)
    {
        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'area_code' => ['required','max:255'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer']
        ],$this->validation_messages);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }


        try {
            if($request->cn_to < $request->cn_from)
            {
                return  redirect()->back()->with('error','CN To Must be grater than or equal to  CN From!');
            }
            $cn_store =  TraxChildCnReceiveAdminStore::where('area_code', $request->area_code)
                ->where(function ($query) use ($request) {
                    $query->where('cn_from', '<=', $request->cn_from)
                        ->where('cn_to', '>=', $request->cn_from)
                        ->orWhere('cn_from', '<=', $request->cn_to)
                        ->where('cn_to', '>=', $request->cn_to)
                        ->orWhere('cn_from', '>=',$request->cn_from)
                        ->where('cn_to', '<=', $request->cn_to);
                })->where('status',1);

            if($cn_store->exists()){
                return  redirect()->back()->with('error','Duplicate Cn found!');
            }

            $date= Carbon::now()->toDateString();
            $quantity = ($request->cn_to-$request->cn_from +1);

            $child_cn_admin = new TraxChildCnReceiveAdminStore();
            $child_cn_admin->company_code = $request->company_code;
            $child_cn_admin->area_code = $request->area_code;
            $child_cn_admin->cn_from = $request->cn_from;
            $child_cn_admin->cn_to = $request->cn_to;
            $child_cn_admin->quantity = $quantity;
            $child_cn_admin->receive_date = $date;
            $child_cn_admin->save();

            return redirect()->back()->with('success','Child CN Receive Admin store successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed Child CN receive admin store');
        }
    }

    public function cn_child_receive_admin_store_edit($id)
    {
        $child_cn_admin= TraxChildCnReceiveAdminStore::where('id',$id)->where('status',1);
        if($child_cn_admin->exists())
        {
            $child_cn_admin = $child_cn_admin->first();
            return response()->json(['status'=>0,'child_cn_admin'=>$child_cn_admin]);
        }

        return  response()->json(['status'=>1,'error'=>'No Child CN found in admin store!']);
    }

    public  function cn_child_receive_admin_store_update(Request  $request)
    {

        $user_id=session('id');
        $validate = Validator::make($request->all(),[
            'id' => ['required','integer'],
            'company_code' => ['required','max:255'],
            'area_code' => ['required','max:255'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer']
        ],$this->validation_messages);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $date= Carbon::now()->toDateString();
//            $quantity = ($request->cn_to-$request->cn_from+1);

            $child_cn_admin = TraxChildCnReceiveAdminStore::find($request->id);
            $child_cn_admin->company_code = $request->company_code;
            $child_cn_admin->area_code = $request->area_code;
//            $cn_receive_admin_store->product_id = $request->product_id;
//            $cn_receive_admin_store->cn_from = $request->cn_from;
//            $cn_receive_admin_store->cn_to = $request->cn_to;
//            $cn_receive_admin_store->quantity = $quantity;
            $child_cn_admin->receive_date = $date;
            $child_cn_admin->updated_by=$user_id;
            $child_cn_admin->save();

            return redirect()->back()->with('success','Child CN update admin store successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed Child CN update admin store');
        }
    }


    public function cn_child_issue_to_rider_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 777);

        $riders =  Rider::where('status',1)->get();
        return view('admin.logistic.child_cn_issue_rider')->with(['riders'=>$riders]);
    }

    public function cn_child_issue_to_rider_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 778);
        }
        $trax_child_cn_issue_rider = TraxChildCnIssueToRider::join('riders as rd','rd.id','=','trax_child_cn_issue_to_riders.rider_id')
            ->SELECT('trax_child_cn_issue_to_riders.id','trax_child_cn_issue_to_riders.issue_date','trax_child_cn_issue_to_riders.company_code','rd.name as rider_name','rd.trax_id as rider_trax_id','trax_child_cn_issue_to_riders.cn_from','trax_child_cn_issue_to_riders.cn_to','trax_child_cn_issue_to_riders.quantity')
            ->where('trax_child_cn_issue_to_riders.status',1);

        $datatables = Datatables::of($trax_child_cn_issue_rider)
            ->addColumn('action',function ($trax_child_cn_issue_rider){
                if (session('role_id') == 1 || count(array_intersect([971], session('permissions'))) !== 0) {
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $cn_list_link = '<a href="'.route('admin.logistic.cn.child_issue_to_rider.cn_index',['issue_id'=>$trax_child_cn_issue_rider->id]).'" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-search"></i></div><div class="col-9 offset-1">View CN List</div></div></a>';

                    $dropdown = '
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                              <div class="dropdown-menu dropdown-menu-sm">
                        ';
                    $dropdown .= $edit_button;
                    $dropdown .= $cn_list_link;

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
    public  function rider_child_cn_index($issue_id)
    {
        return view('admin.logistic.rider_child_cn_list')->with('issue_id',$issue_id);
    }
    public  function rider_child_cn_list(Request $request)
    {
        $rider_cn_list = TraxRiderChildCnDetail::Join('trax_child_cn_issue_to_riders as ir','ir.id','trax_rider_child_cn_details.child_cn_issue_id')
            ->join('riders as r','r.id','ir.rider_id')
            ->select('trax_rider_child_cn_details.id','trax_rider_child_cn_details.cn_number','trax_rider_child_cn_details.is_used','ir.rider_id','r.name as rider_name','r.trax_id')
            ->where('trax_rider_child_cn_details.child_cn_issue_id',$request->rider_issue_id)
            ->where('ir.status',1)
            ->where('trax_rider_child_cn_details.is_hold',0);


        $datatables = Datatables::of($rider_cn_list)
            ->editColumn('is_used',function ($rider_cn_list){
                if($rider_cn_list->is_used==1)
                {
                    return 'Used';
                }
                return  'Not Used';
            })->addColumn('barcode',function($rider_cn_list){
                $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                $cn_number = (string)($rider_cn_list->cn_number);
                $html = '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($cn_number, $generator::TYPE_CODE_128, 2, 70)) . '" class="img-fluid mx-auto d-block h-auto">';
                return $html;
            });
        return $datatables->make(true);
    }
    public function cn_child_issue_to_rider_store(Request $request)
    {
        $user_id = session('id');
        $admin = Admin::where('id',$user_id)->whereNotNull('default_hub_id');
        if(!$admin->exists())
        {
            return redirect()->back()->with('error','Hub not found!');
        }
        $admin = $admin->first();

        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'rider_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer'],
        ]);

        if($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            if($request->cn_to < $request->cn_from)
            {
                return  redirect()->back()->with('error','CN To Must be grater than or equal to  CN From!');
            }
            $cn_issue =  TraxChildCnIssueToRider::where('area_code', $admin->default_hub_id)
                ->where(function ($query) use ($request) {
                    $query->where('cn_from', '<=', $request->cn_from)
                        ->where('cn_to', '>=', $request->cn_from)
                        ->orWhere('cn_from', '<=', $request->cn_to)
                        ->where('cn_to', '>=', $request->cn_to)
                        ->orWhere('cn_from', '>=',$request->cn_from)
                        ->where('cn_to', '<=', $request->cn_to);
                })->where('status',1);

            if($cn_issue->exists()){
                return  redirect()->back()->with('error','CN Issued Already to Rider');
            }

            $cn_store =  TraxChildCnReceiveAdminStore::where('area_code', $admin->default_hub_id)
                ->where(function ($query) use ($request) {
                    $query->where('cn_from', '<=', $request->cn_from)
                        ->where('cn_to', '>=', $request->cn_from)
                        ->orWhere('cn_from', '<=', $request->cn_to)
                        ->where('cn_to', '>=', $request->cn_to)
                        ->orWhere('cn_from', '>=',$request->cn_from)
                        ->where('cn_to', '<=', $request->cn_to);
                })->where('status',1)
                ->latest('id');

            if(!$cn_store->exists()){
                return redirect()->back()->with('error','Child CN not found in admin store!');
            }



            $time_stamp = now();
            $quantity = ($request->cn_to - $request->cn_from +1);
            $child_cn = [];

            DB::beginTransaction();

            $trax_child_cn_issue_rider = new TraxChildCnIssueToRider();
            $trax_child_cn_issue_rider->company_code = $request->company_code;
            $trax_child_cn_issue_rider->rider_id = $request->rider_id;
            $trax_child_cn_issue_rider->area_code = $admin->default_hub_id;
            $trax_child_cn_issue_rider->cn_from = $request->cn_from;
            $trax_child_cn_issue_rider->cn_to = $request->cn_to;
            $trax_child_cn_issue_rider->quantity = $quantity;
            $trax_child_cn_issue_rider->issue_date = $time_stamp->toDateString();
            $trax_child_cn_issue_rider->save();

            for ($i = $request->cn_from; $i <= $request->cn_to; $i++) {
                $child_cn[] = [
                    'child_cn_issue_id' => $trax_child_cn_issue_rider->id,
                    'cn_number' => $i,
                    'created_at' => $time_stamp,
                    'updated_at' => $time_stamp
                ];
            }

            TraxRiderChildCnDetail::insert($child_cn);

            DB::commit();

            return redirect()->back()->with('success', 'Child CN issue to rider successfully');

        } catch (\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed Child CN issue to rider');
        }
    }

    public function cn_child_issue_to_rider_edit($id)
    {
        $trax_child_cn_issue_rider = TraxChildCnIssueToRider::where('id',$id)->where('status',1);
        if($trax_child_cn_issue_rider->exists())
        {
            $trax_child_cn_issue_rider = $trax_child_cn_issue_rider->first();
            return response()->json(['status'=>0,'trax_child_cn_issue_rider'=>$trax_child_cn_issue_rider]);
        }

        return  response()->json(['status'=>1,'error'=>'No Child CN issue to rider found!']);
    }

    public  function cn_child_issue_to_rider_update(Request $request)
    {

        $user_id=session('id');
        $validate = Validator::make($request->all(),[
            'id' => ['required','integer'],
            'company_code' => ['required','max:255'],
            'rider_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer'],
        ]);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        try {
            $time_stamp = now();
//            $quantity = ($request->cn_to-$request->cn_from+1);
//            $child_cn = [];

//            DB::beginTransaction();

            $trax_child_cn_issue_rider = TraxChildCnIssueToRider::find($request->id);
            $trax_child_cn_issue_rider->company_code = $request->company_code;
            $trax_child_cn_issue_rider->rider_id = $request->rider_id;
//            $trax_child_cn_issue_rider->cn_from = $request->cn_from;
//            $trax_child_cn_issue_rider->cn_to = $request->cn_to;
//            $trax_child_cn_issue_rider->quantity = $quantity;
            $trax_child_cn_issue_rider->issue_date = $time_stamp->toDateString();
            $trax_child_cn_issue_rider->updated_by=$user_id;
            $trax_child_cn_issue_rider->save();

            // delete exiting issue rider cns
            // $trax_rider_cn = TraxRiderCnDetail::where('cn_issue_id',$trax_cn_issue_rider->id);
            //  if($trax_rider_cn->exists())
            //    $trax_rider_cn->delete();


            //            for ($i = $request->cn_from; $i <= $request->cn_to; $i++) {
            //                $child_cn[] = [
            //                    'cn_issue_id' => $trax_cn_issue_rider->id,
            //                    'cn_number' => $i,
            //                    'created_at' => $time_stamp,
            //                    'updated_at' => $time_stamp
            //                ];
            //            }
            //
            //            TraxRiderCnDetail::insert($child_cn);



//            DB::commit();
            return redirect()->back()->with('success','Child CN update issue to rider successfully');

        } catch (\Exception $exception){
//            DB::rollBack();
            return redirect()->back()->with('error','Failed Child CN update issue to rider');
        }
    }



}
