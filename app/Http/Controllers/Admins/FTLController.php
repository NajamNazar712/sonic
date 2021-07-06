<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\VehicleType;
use App\Http\Models\City;
use App\Http\Models\FtlCostTypes;
use App\Http\Models\TransportModeVendor;
use App\Http\Models\Admin\FtlComment;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\FtlRequestAdditionalCost;
use App\Http\Models\Admin\FtlRequestStatus;
use App\Http\Models\Admin\FtlRequestStatusHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class FTLController extends Controller
{
    public $sale_role_ids = array();
    public $finance_role_ids = array();
    public $operation_role_ids = array();

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

        $this->sale_role_ids = AdminRole::where('department_id',7)->pluck('id')->toArray();
        $this->finance_role_ids = AdminRole::where('department_id',4)->pluck('id')->toArray();
        $this->operation_role_ids = AdminRole::where('department_id',6)->pluck('id')->toArray();
    }

    public function ftl_request_index()
    {
        $shippers = User::leftjoin('sale_person_tags as spt',function ($join){
                $join->on('spt.user_id','users.id')
                    ->where('spt.status',0);
        })->leftjoin('admins as sale_person','sale_person.id','spt.admin_id')
            ->where('users.status',3)
            ->where('users.blacklist',0)
            ->where('users.account_type_id',2)
            ->get(['users.id as id','users.name as name','sale_person.id as sale_person_id']);

        $cities = City::where('status',1)->where('business_category_id',1)->get(['id','name']);

        $sale_persons = Admin::where('status',1)->whereIn('role_id',$this->sale_role_ids)->get(['id','name']);

        $vehicles = VehicleType::get(['id','name']);

        $statuses = FtlRequestStatus::get(['status']);

        return view('admin.ftl.request.index',compact('shippers','cities','sale_persons','vehicles','statuses'));
    }

    public function ftl_request_list(Request $request)
    {
        $data = FtlRequest::leftjoin('ftl_request_statuses as status','status.id','ftl_requests.status_id')
            ->leftjoin('cities as origin','origin.id','ftl_requests.origin_id')
            ->leftjoin('cities as destination','destination.id','ftl_requests.destination_id')
            ->leftjoin('admins as updated_by','updated_by.id','ftl_requests.updated_by')
            ->leftjoin('shipments as s','s.id','ftl_requests.shipment_id')
            ->leftjoin('vehicle_types as vt','vt.id','ftl_requests.vehicle_id')
            ->leftjoin('transport_mode_vendors as tmv','tmv.id','ftl_requests.vendor_id')
            ->select(['ftl_requests.id as id','ftl_requests.id as req_id','ftl_requests.shipper_id as shipper_id','origin.name as origin','destination.name as destination','s.tracking_number as tracking_number','ftl_requests.weight as weight','vt.name as vehicle','ftl_requests.quantity as quantity','ftl_requests.date as date','ftl_requests.updated_at as updated_on','updated_by.name as updated_by','ftl_requests.status_id as status_id','status.status as status','tmv.name as vendor','ftl_requests.freight_charges as freight_charges','ftl_requests.total_charges as total_charges','ftl_requests.gst as gst',DB::raw("((select COALESCE(SUM(amount), 0) from ftl_request_additional_costs where ftl_request_id = ftl_requests.id) + ftl_requests.freight_cost) as total_cost")]);

        $datatables = Datatables::of($data)
            ->editColumn('req_id',function ($data){
                return str_pad($data->req_id, 3, '0', STR_PAD_LEFT);
            })
            ->addColumn('tracking_number_link', function ($data) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$data->tracking_number' class='tracking' target='_blank'>$data->tracking_number</a></u>";
            })
            ->addColumn('action', function($data) {
                $dropdown = '';
                if (session('role_id') == 1 || count(array_intersect([513, 516], session('permissions'))) !== 0){
                    $dropdown .= '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if(session('role_id') == 1 || in_array(513,session('permissions'))) {
                        $route = route('admin.ftl.request.view', ['id' => $data->id]);
                        if ($data->status_id == 1 || $data->status_id == 4) {
                            $dropdown .= '<button onclick="window.open(\'' . $route . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Estimate</div></button>';
                        } else {
                            $dropdown .= '<button onclick="window.open(\'' . $route . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View</div></button>';
                        }
                    }
                    if(session('role_id') == 1 || in_array(516,session('permissions'))) {
                        if ($data->status_id == 3 && $data->shipper_id == null) {
                            $route = route('admin.shipment.book.ftl.walk_in').'?ftl_req='.$data->req_id;
                            $dropdown .= '<button type="button" onclick="window.open(\''.$route.'\')" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Book</div></button>';
                        }
                    }
                    $dropdown .= '</div>
                  </div>
                ';
                }
                return $dropdown;
            });

        return $datatables->make(true);
    }

    public function ftl_request_add(Request $request)
    {
        $ftl_request = new FtlRequest();
        $ftl_request->salesperson_id = $request->sale_person;
        $ftl_request->origin_id = $request->origin;
        $ftl_request->destination_id = $request->destination;
        $ftl_request->weight = $request->weight;
        $ftl_request->quantity = $request->quantity;
        $ftl_request->vehicle_id = $request->vehicle;
        $ftl_request->date = $request->date_formatted;
        $ftl_request->updated_by = Auth::id();
        $ftl_request->updated_on = now();
        if($request->shipper == 0)
        {
            $ftl_request->shipper_name = $request->shipper_name;
        }
        else{
            $ftl_request->shipper_id = $request->shipper;
        }
        $ftl_request->save();
        $this::FTLRequestStatusHistory($ftl_request->id,1,Auth::id());
        return back()->with(['success'=>'Request Generated Successfully']);
    }

    public function ftl_request_view($id)
    {
        $ftl = FtlRequest::leftjoin('cities as origin','origin.id','ftl_requests.origin_id')
            ->leftjoin('zones as z',function ($join){
                $join->on('z.id','origin.zone_id')
                    ->where('z.status',1);
            })
            ->leftjoin('cities as destination','destination.id','ftl_requests.destination_id')
            ->leftjoin('admins as updated_by','updated_by.id','ftl_requests.updated_by')
            ->leftjoin('vehicle_types as vt','vt.id','ftl_requests.vehicle_id')
            ->leftjoin('admins as sale_person','sale_person.id','ftl_requests.salesperson_id')
            ->leftjoin('users as shipper','shipper.id','ftl_requests.shipper_id')
            ->select(['ftl_requests.id as id','origin.name as origin','destination.name as destination','ftl_requests.weight as weight','vt.name as vehicle','ftl_requests.quantity as quantity','ftl_requests.date as date','ftl_requests.status_id as status_id','ftl_requests.vendor_id as vendor_id','ftl_requests.freight_charges as freight_charges','ftl_requests.total_charges as total_charges','ftl_requests.gst as gst','shipper.name as shipper','ftl_requests.shipper_name as shipper_name','sale_person.name as sale_person','ftl_requests.shipper_id as shipper_id','ftl_requests.freight_cost as freight_cost','z.gst as gst'])
            ->where('ftl_requests.id',$id);
       if($ftl->doesntExist())
       {
           return back()->with(['error'=>'Invalid FTL Request']);
       }

       $ftl = $ftl->first();
       $ftl_status_history = FtlRequestStatusHistory::leftjoin('admins as updated_by','updated_by.id','ftl_request_status_histories.updated_by')
           ->leftjoin('ftl_request_statuses as frs','frs.id','ftl_request_status_histories.status_id')
           ->select(['frs.status as status','updated_by.name as admin','ftl_request_status_histories.created_at as updated_at'])
           ->where('ftl_request_id',$id)
           ->orderBy('updated_at','asc')
           ->get();

        $shippers = User::leftjoin('sale_person_tags as spt',function ($join){
            $join->on('spt.user_id','users.id')
                ->where('spt.status',0);
        })->leftjoin('admins as sale_person','sale_person.id','spt.admin_id')
            ->where('users.status',3)
            ->where('users.blacklist',0)
            ->where('users.account_type_id',2)
            ->get(['users.id as id','users.name as name','sale_person.id as sale_person_id']);

        $sale_persons = Admin::where('status',1)->whereIn('role_id',$this->sale_role_ids)->get(['id','name']);

        $vendors = TransportModeVendor::get(['id','name']);

        $ftl_costs = FtlRequestAdditionalCost::where('ftl_request_id',$ftl->id)->get(['amount','cost_type']);

        $comments = FtlComment::leftjoin('admins as a','a.id','ftl_comments.comment_by_id')
            ->where('ftl_comments.ftl_request_id',$ftl->id)
            ->select(['ftl_comments.id as id','ftl_comments.comment as comment','ftl_comments.comment_by as comment_by','ftl_comments.comment_by_id as commenter_id','ftl_comments.created_at as created_at','a.name as commenter'])
            ->get();
        $cost_types = FtlCostTypes::all();

       return view('admin.ftl.request.view',compact('ftl','ftl_status_history','shippers','sale_persons','vendors','ftl_costs','comments','cost_types'));
    }

     public function ftl_request_update_shipper($id,Request $request)
     {
        $ftl = FtlRequest::find($id);
        if(!$ftl)
        {
            return back()->with(['error'=>'Invalid FTL Request']);
        }
        $ftl->shipper_id = $request->shipper;
        $ftl->salesperson_id = $request->sale_person;
        $ftl->update();
        return back()->with(['success'=>'Shipper Updated Successfully']);
     }

     public function ftl_request_update_status($id,Request $request)
     {
//        return $request;
        if($request->btn == "Update")
        {
            if(session('role_id') == 1 || in_array(514,session('permissions'))) {
                $ftl = FtlRequest::find($id);
                if (!$ftl) {
                    return back()->with(['error' => 'Invalid FTL Request']);
                }

                $cost_types = FtlCostTypes::pluck('name')->toArray();
              
                $ftl->additional_cost()->delete();
                if ($request->has('other_cost') && $request->has('other_cost_type')) {
                    $other_cost_count = count($request->other_cost);
                    for ($i = 0; $i < $other_cost_count; $i++) {
                        $cost = new FtlRequestAdditionalCost();
                        $cost->ftl_request_id = $ftl->id;
                        $cost->amount = $request->other_cost[$i];
                        $cost->cost_type = $request->other_cost_type[$i];
                        $cost->save();

                        if(!in_array($request->other_cost_type[$i],$cost_types )){
                            FtlCostTypes::create([
                                'name' => $request->other_cost_type[$i],
                            ]);
                        }
                    }
                }
                $ftl->vendor_id = $request->vendor;
                $ftl->freight_cost = $request->freight_cost;
                $ftl->freight_charges = $request->freight_charges;
                $ftl->gst = $request->gst;
                $ftl->total_charges = $request->total_charges;

                if ($request->freight_charges > 0) {
                    $ftl->status_id = 2;
                    $ftl->updated_by = Auth::id();
                    $this::FTLRequestStatusHistory($ftl->id, 2, Auth::id());
                }

                $ftl->update();
                return back()->with(['success' => 'FTL Request Updated Successfully']);
            }
            else{
                return redirect()->route('admin.access_denied');
            }
        }
        else if($request->btn == "Approve")
        {
            if(session('role_id') == 1 || in_array(515,session('permissions'))) {
                $ftl = FtlRequest::find($id);
                if (!$ftl) {
                    return back()->with(['error' => 'Invalid FTL Request']);
                }

                $ftl->status_id = 3;
                $ftl->updated_by = Auth::id();
                $ftl->update();
                $this::FTLRequestStatusHistory($ftl->id, 3, Auth::id());
                return back()->with(['success' => 'FTL Request Approved Successfully']);
            }
            else{
                return redirect()->route('admin.access_denied');
            }
        }
        else if($request->btn == "Reject")
        {
            if(session('role_id') == 1 || in_array(515,session('permissions'))) {
                $ftl = FtlRequest::find($id);
                if (!$ftl) {
                    return back()->with(['error' => 'Invalid FTL Request']);
                }

                $ftl->status_id = 4;
                $ftl->updated_by = Auth::id();
                $ftl->update();
                $this::FTLRequestStatusHistory($ftl->id, 4, Auth::id());
                return back()->with(['success' => 'FTL Request Rejected Successfully']);
            }
            else{
                return redirect()->route('admin.access_denied');
            }
        }
        else{
            return back()->with(['error'=>'Invalid Action']);
        }
     }

     public function ftl_request_get_comments(Request $request)
     {
         $comments = FtlComment::leftjoin('admins as a','a.id','ftl_comments.comment_by_id')
             ->where('ftl_comments.ftl_request_id',$request->request_id)
             ->select(['ftl_comments.id as id','ftl_comments.comment as comment','ftl_comments.comment_by as comment_by','ftl_comments.comment_by_id as commenter_id','ftl_comments.created_at as created_at','a.name as commenter']);
        if($comments->exists())
         {
             $comments = $comments->get();
             return response()->json(['status'=>1,'comments'=>$comments]);
         }
     }

     public function ftl_request_add_comment(Request $request)
     {
         if(in_array(Auth::user()->role_id,$this->sale_role_ids))
         {
             $comment_by = 0;
         }
         elseif(in_array(Auth::user()->role_id,$this->finance_role_ids))
         {
             $comment_by = 2;
         }
         elseif(in_array(Auth::user()->role_id,$this->operation_role_ids))
         {
             $comment_by = 1;
         }
         else {
             return response()->json(['status'=>0,'error'=>'You are not allowed to comment on the request']);
         }

         $comment = new FtlComment();
         $comment->ftl_request_id = $request->request_id;
         $comment->comment_by_id = Auth::id();
         $comment->comment_by = $comment_by;
         $comment->comment = $request->comment;
         $comment->save();

         $data = FtlComment::leftjoin('admins as a','a.id','ftl_comments.comment_by_id')
             ->where('ftl_comments.id',$comment->id)
             ->select(['ftl_comments.comment as comment','ftl_comments.comment_by as commented_by','ftl_comments.created_at as created_at','a.name as commenter'])
             ->first();

         return response()->json(['status'=>1,'comment'=>$data]);
     }

    static public function FTLRequestStatusHistory($request_id,$status_id,$user_id)
    {
        $history = new FtlRequestStatusHistory();
        $history->ftl_request_id = $request_id;
        $history->status_id = $status_id;
        $history->updated_by = $user_id;
        $history->save();
    }
}
