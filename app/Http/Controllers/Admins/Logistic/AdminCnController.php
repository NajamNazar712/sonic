<?php

namespace App\Http\Controllers\Admins\Logistic;

use App\Http\Models\City;
use App\Http\Models\Admin\Logistic\TraxCnIssueAreaStore;
use App\Http\Models\Admin\Logistic\TraxCnIssueToRider;
use App\Http\Models\Admin\Logistic\TraxCnReceiveAdminStore;
use App\Http\Models\Rider;
use App\Http\Models\Segment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class AdminCnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public  function  cn_area_store_index()
    {
        // ActivityTrailController::createActivityTrailLog(Auth::id(), 739);
        $products =  Segment::all();
        $cities = City::where('status',1)->get();
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
        $products =  Segment::all();
        $cities = City::where('status',1)->get();
        return view('admin.logistic.cn_receive_admin_store')->with(['products'=> $products,'cities'=>$cities]);
    }

    public  function cn_receive_admin_store_list(Request  $request)
    {
        $trax_cn_receive_admin_stores = TraxCnReceiveAdminStore::Join('segments as s','trax_cn_receive_admin_stores.product_id','=','s.id')
            ->Join('cities as c','c.id','=','trax_cn_receive_admin_stores.area_code')
            ->SELECT('trax_cn_receive_admin_stores.receive_date','trax_cn_receive_admin_stores.company_code','trax_cn_receive_admin_stores.area_code','c.name as area_name','trax_cn_receive_admin_stores.product_id','s.name as segment_name','trax_cn_receive_admin_stores.cn_from','trax_cn_receive_admin_stores.cn_to','trax_cn_receive_admin_stores.quantity')
            ->where('trax_cn_receive_admin_stores.status',1);


        $datatables = Datatables::of($trax_cn_receive_admin_stores);

        return $datatables->make(true);
    }

    public  function cn_receive_admin_store_store(Request  $request)
    {
        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'area_code' => ['required','max:255'],
            'product_id' => ['required','integer'],
            'cn_from' => ['required','integer'],
            'cn_to' => ['required','integer'],
            'quantity' => ['required','integer'],
            'receive_date' => ['required','date'],
        ]);

        if($validate->fails())
        {
            return redirect()->back()->with('error',$validate->errors());
        }

        try {
            $cn_receive_admin_store = new TraxCnReceiveAdminStore();
            $cn_receive_admin_store->company_code = $request->company_code;
            $cn_receive_admin_store->area_code = $request->area_code;
            $cn_receive_admin_store->product_id = $request->product_id;
            $cn_receive_admin_store->cn_from = $request->cn_from;
            $cn_receive_admin_store->cn_to = $request->cn_to;
            $cn_receive_admin_store->quantity = $request->quantity;
            $cn_receive_admin_store->receive_date = $request->receive_date;
            $cn_receive_admin_store->save();
            return redirect()->back()->with('success','CN Receive Admin store successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed CN receive admin store');
        }
    }

    public  function cn_issue_to_rider_index()
    {
        $products =  Segment::all();
        $riders =  Rider::where('status',1)->get();
        return view('admin.logistic.cn_issue_rider')->with(['products'=> $products,'riders'=>$riders]);
    }

    public function  cn_issue_to_rider_list()
    {
        $trax_cn_issue_rider = TraxCnIssueToRider::Join('segments as s','trax_cn_issue_to_riders.product_id','=','s.id')
            ->join('riders as rd','rd.id','=','trax_cn_issue_to_riders.rider_id')
            ->SELECT('trax_cn_issue_to_riders.issue_date','trax_cn_issue_to_riders.company_code','rd.name as rider_name','rd.trax_id as rider_trax_id','trax_cn_issue_to_riders.product_id','s.name as segment_name','trax_cn_issue_to_riders.cn_from','trax_cn_issue_to_riders.cn_to','trax_cn_issue_to_riders.quantity')
            ->where('trax_cn_issue_to_riders.status',1);

        $datatables = Datatables::of($trax_cn_issue_rider);

        return $datatables->make(true);
    }
    public function cn_issue_to_rider_store(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'company_code' => ['required','max:255'],
            'rider_id' => ['required','integer'],
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
            $trax_cn_issue_rider = new TraxCnIssueToRider();
            $trax_cn_issue_rider->company_code = $request->company_code;
            $trax_cn_issue_rider->rider_id = $request->rider_id;
            $trax_cn_issue_rider->product_id = $request->product_id;
            $trax_cn_issue_rider->cn_from = $request->cn_from;
            $trax_cn_issue_rider->cn_to = $request->cn_to;
            $trax_cn_issue_rider->quantity = $request->quantity;
            $trax_cn_issue_rider->issue_date = $request->issue_date;
            $trax_cn_issue_rider->save();
            return redirect()->back()->with('success','CN issue to rider successfully');

        } catch (\Exception $exception){
            return redirect()->back()->with('error','Failed CN issue to rider');
        }
    }


}
