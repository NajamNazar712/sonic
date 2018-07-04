<?php

namespace App\Http\Controllers\Admins;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\City;
use App\Http\Models\Dispute;
use App\Http\Models\DisputeShipment;
use App\Http\Models\DisputeType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $dispute = Dispute::join('admins','disputes.admin_id','=','admins.id')
            ->join('cities','cities.id','=','disputes.city_id')
            ->join('dispute_types as dt','dt.id','=','disputes.dispute_type_id')
            ->leftjoin('dispute_comments as dc','dc.dispute_id','=','disputes.id')
            ->select(['disputes.id as dispute_id','disputes.created_at as created_at','disputes.description','cities.name as originated_at','dt.type as dispute_type','disputes.shipments_count as no_of_shipments','admins.name as launched_by','dc.admin_id as updated_by','disputes.status as status']);
        return Datatables::of($dispute)

            ->editColumn('created_at', function ($dispute) {
                return $dispute->created_at ? with(new Carbon($dispute->created_at))->format('d/m/Y H:i:s A') : '';
            })
            ->editColumn('status',function($dispute){
                return $dispute->status == 0? 'Dispute Launched': ($dispute->status == 1? 'Dispute Updated' : ($dispute->status == 2? 'Dispute Resolved':''));

            })
            ->addColumn("action", function ($dispute) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item dispute_details'><i class='ft-plus-circle primary'></i> View Details</a>                                         
                                              <a href='#' class='dropdown-item update'><i class='ft-plus-circle primary'></i> Update</a>                                         
                                              <a href='#' class='dropdown-item resolve'><i class='ft-plus-circle primary'></i> Resolve</a>                                         
                                            </div></span>";
            })

            ->make(true);
    }
    static public function add_short_received_shipments($id,$count){
        $admin = Auth::id();
        $admin_details = Admin::find($admin);
        $city_id = $admin_details->city->id;
       $dispute = Dispute::create([
            'description'=>'Shipment short received',
            'admin_id'=>$admin,
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

    }


    //for dispute start
//          $receiving_sheets = ReceivingSheet::where('user_id', $pickup_request->shipper_id)->where('status', 1);
//
//          $short_shipments = array();
//
//          if ($receiving_sheets->exists()) {
//              $receiving_sheets = $receiving_sheets->get();
//
//              foreach ($receiving_sheets as $receiving_sheet) {
//                  foreach ($receiving_sheet->receiving_sheet_shipments as $receiving_sheet_shipment) {
//                      $shipment = $receiving_sheet_shipment->shipment;
//
//                      if ($shipment->shipper_status_id == 1 && $pickup_request->pickup_address_id == $shipment->pickup_address_id) {
//                          $short_shipments[] = $shipment->tracking_number;
//                      }
//                  }
//              }
//              $count = count($short_shipments);
//              foreach ($short_shipments as $short_shipment) {
//                  DisputeController::add_short_received_shipments($short_shipment, $count);
//              }
//          }
    //dispute end
}
