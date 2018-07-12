<?php

namespace App\Http\Controllers\Admins;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\City;
use App\Http\Models\Dispute;
use App\Http\Models\DisputeComment;
use App\Http\Models\DisputeShipment;
use App\Http\Models\DisputeType;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class DisputeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function dispute_index(){
        $cities = City::all();
        $dispute_types = DisputeType::all();
        return view('admin.dispute.index')->with(['cities'=>$cities,'dispute_types'=>$dispute_types]);
    }
    public function dispute_list(Request $request){
        $dispute = Dispute::join('cities','cities.id','=','disputes.city_id')
            ->join('dispute_types as dt','dt.id','=','disputes.dispute_type_id')
            ->leftjoin('admins as ad',function ($join){
                $join->on('ad.id','=','disputes.raised_by')
                    ->where('disputes.raised_by_status',0);
            })
            ->leftjoin('users as us',function ($join){
                $join->on('us.id','=','disputes.raised_by')
                    ->where('disputes.raised_by_status',1);
            })
//            ->join('dispute_comments as dc','dc.dispute_id','=','disputes.id')
            ->leftJoin('dispute_comments as dc', function ($join) {
                $join->on('dc.dispute_id', '=', 'disputes.id')
                ->where('dc.created_at', '=',
                    DB::raw('(select max(created_at) from dispute_comments where dispute_comments.dispute_id = disputes.id)'));
                })
            ->leftJoin('admins as au', 'dc.admin_id', '=', 'au.id')
            ->select(['disputes.id as dispute_id','disputes.created_at as created_at','disputes.description','cities.name as originated_at','dt.type as dispute_type','disputes.shipments_count as no_of_shipments','ad.name as admin','us.name as shipper','disputes.raised_by_status as rbstatus','au.name as updated_by','disputes.status as status']);
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
            ->editColumn('launched_by',function($dispute){
                if($dispute->rbstatus == 0){
                    return $dispute->admin;
                }
                else if($dispute->rbstatus == 1){
                    return $dispute->shipper;
                }
            })
            ->addColumn("action", function ($dispute) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                                                                       
                                              <a href='#' class='dropdown-item update'><i class='ft-plus-circle primary'></i> Update</a>                                         
                                              <a href='#' class='dropdown-item resolve'><i class='ft-plus-circle primary'></i> Resolve</a>                                         
                                            </div></span>";
            })

            ->make(true);
    }
    static public function add_short_received_shipments($id,$count){
        $admin = Auth::id();
        $admin_details = Admin::where('id',$admin)->first();
        $city_id = $admin_details->city->id;
       $dispute = Dispute::create([
            'description'=>'Shipment short received',
            'raised_by'=>$admin,
            'raised_by_status'=>0,
            'city_id'=>$city_id,
            'dispute_type_id'=>1,
            'shipments_count'=>$count
        ]);
       if($dispute){
           DisputeShipment::create([
               'dispute_id'=>$dispute->id,
               'shipment_id'=>$id
           ]);
       }

    }
    public function dispute_create(Request $request){
        $tracking_numbers = explode(',',$request->tracking_number);
//        print_r($shipments);
        if(!empty($request->tracking_number)) {
            $count = 0;
            $dispute = Dispute::create([
               'description'=>$request->description,
                'raised_by'=>Auth::id(),
                'raised_by_status'=>0,
                'city_id'=>$request->city_select,
                'dispute_type_id'=>$request->dispute_type_select
            ]);
            foreach ($tracking_numbers as $tracking) {
                $shipment = Shipment::where('tracking_number',$tracking);
                if($shipment->exists()){
                    $shipment = $shipment->first();
                    DisputeShipment::create([
                        'dispute_id'=>$dispute->id,
                        'shipment_id'=>$shipment->id
                    ]);
                    $count++;
                }
            }
            Dispute::where('id',$dispute->id)->update(['shipments_count'=>$count]);
            return redirect()->back()->with('success','Dispute successfully created!');
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
    public function resolve_dispute(Request $request){
        $dispute = Dispute::where('id',$request->id);
        if($dispute->exists()){
            $dispute->update(['status'=>2,'updated_by'=>Auth::id()]);
            $admin = Auth::id();
            DisputeComment::create([
               'dispute_id'=>$request->id,
               'comment'=>'Dispute resolved by '.$admin,
                'admin_id'=>$admin
            ]);
            return response()->json(['status'=>1,'success'=>"Dispute resolved successfully!"]);
        }else{
            return response()->json(['status'=>0,'error'=>"Dispute not found!"]);
        }

    }
    public function update_dispute_view(Request $request){
        $dispute_id = $request->id;
        $dispute = Dispute::where('id',$dispute_id);
        $trackings = array();
        if($dispute->exists()){
            $dispute = $dispute->first();
            $cities = City::all();
            $dispute_types = DisputeType::all();
            $comments = $dispute->comments;
            $dispute_cns = DisputeShipment::where('dispute_id',$dispute_id)->get();
            foreach ($dispute_cns as $shipment){
                $trackings[] = Shipment::where('id',$shipment->shipment_id)->select('tracking_number')->first();
            }
            $returnHTML = view('admin.dispute.update')->with(['dispute'=>$dispute,'cities'=>$cities,'dispute_types'=>$dispute_types,'shipments'=>$trackings,'comments'=>$comments])->render();
            return response()->json(['status'=>1,'view'=>$returnHTML]);
        }else{
            return response()->json(['status'=>0,'error'=>"No shipments exist!"]);
        }
    }
    public function update_dispute(Request $request){
        $tracking_numbers = explode(',',$request->update_tracking_number);
        if($request->dispute_id != ''){
            $dispute = Dispute::where('id',$request->dispute_id);
            if($dispute->exists()){
                $shipment_count = 0;
                $dispute_details = $dispute->first();
                $shipment_count = $dispute_details->shipments_count;
                $city_id = $dispute_details->city_id;
                $dispute_type = $dispute_details->dispute_type_id;
                $count = $dispute_details->shipments_count;
                if($city_id !== $request->city_select){
                        $dispute->update(['city_id'=>$request->city_select]);
                }
                if($dispute_type !== $request->dispute_type_select){
                    $dispute->update(['dispute_type_id'=>$request->dispute_type_select]);
                }
                if(!empty($request->update_tracking_number)){
                    foreach ($tracking_numbers as $tracking){
                        $shipment = Shipment::where('tracking_number',$tracking);
                        if($shipment->exists()){
                            $shipment = $shipment->first();
                            $dispute_shipment = DisputeShipment::where(['dispute_id'=>$request->dispute_id,'shipment_id'=>$shipment->id])->exists();
                            if(!$dispute_shipment){
                                DisputeShipment::create([
                                    'dispute_id'=>$dispute_details->id,
                                    'shipment_id'=>$shipment->id
                                ]);
                                $shipment_count++;
                            }

                        }
                    }

                }
                if($shipment_count > $dispute_details->shipments_count){
                    $dispute->update(['shipments_count'=>$shipment_count,'status'=>1]);
                }else{
                    $dispute->update(['status'=>1]);
                }
                DisputeComment::create([
                    'dispute_id'=>$dispute_details->id,
                    'comment'=>$request->dispute_comment,
                    'admin_id'=>Auth::id()
                ]);
                return redirect()->back()->with('success',"Dispute updated successfully!");
            }else{
                return redirect()->back()->with('error','Dispute not found!');
            }
        }else{
            return redirect()->back()->with('error','Something went wrong, try again!');
        }

    }

}
