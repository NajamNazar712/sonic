<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\City;
use App\Http\Models\Dispute;
use App\Http\Models\DisputeShipment;
use App\Http\Models\DisputeType;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperDisputeController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
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
            ->where('disputes.raised_by',Auth::id())
            ->where('disputes.raised_by_status',1);
        return Datatables::of($dispute)

            ->editColumn('created_at', function ($dispute) {
                return $dispute->created_at ? with(new Carbon($dispute->created_at))->format('d/m/Y H:i:s A') : '';
            })
            ->editColumn('status',function($dispute){
                return $dispute->status == 0? 'Dispute Launched': ($dispute->status == 1? 'Dispute Updated' : ($dispute->status == 2? 'Dispute Resolved':''));

            })
            ->editColumn('no_of_shipments',function($dispute){
                return "<a class='font-weight-bold shipment_count' href='#'>{$dispute->no_of_shipments}</a>";
            })
//            ->editColumn('launched_by',function($dispute){
//                if($dispute->rbstatus == 0){
//                    return $dispute->admin;
//                }
//                else if($dispute->rbstatus == 1){
//                    return $dispute->shipper;
//                }
//            })
            ->addColumn("action", function ($dispute) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                            <a href='#' class='dropdown-item view-comments'><i class='ft-plus-circle primary'></i> In Progress</a>                                   
                                            </div></span>";
            })

            ->make(true);
    }
    public function dispute_create(Request $request){
        $tracking_numbers = explode(',',$request->tracking_number);
        if(!empty($request->tracking_number)) {
            $count = 0;
            $dispute = Dispute::create([
                'description'=>$request->description,
                'raised_by'=>Auth::id(),
                'raised_by_status'=>1,
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
            return redirect()->back()->with('success','Shipment successfully created!');
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
}
