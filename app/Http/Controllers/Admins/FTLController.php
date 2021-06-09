<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\VehicleType;
use App\Http\Models\City;
use App\Models\Admin\FtlRequest;
use App\Models\Admin\FtlRequestStatus;
use App\Models\Admin\FtlRequestStatusHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class FTLController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function ftl_request_index()
    {
        $shippers = User::leftjoin('sale_person_tags as spt',function ($join){
                $join->on('spt.user_id','users.id')
                    ->where('spt.status',0);
        })->leftjoin('admins as sale_person','sale_person.id','spt.admin_id')
            ->where('users.status',3)->where('users.blacklist',0)->get(['users.id as id','users.name as name','sale_person.id as sale_person_id']);

        $cities = City::where('status',1)->where('business_category_id',1)->get(['id','name']);

        $sale_persons = Admin::where('status',1)->where('role_id',7)->get(['id','name']);

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
            ->select(['ftl_requests.id as id','ftl_requests.id as req_id','origin.name as origin','destination.name as destination','s.tracking_number as tracking_number','ftl_requests.weight as weight','vt.name as vehicle','ftl_requests.quantity as quantity','ftl_requests.date as date','ftl_requests.updated_at as updated_on','updated_by.name as updated_by','status.status as status','tmv.name as vendor','ftl_requests.status_id','ftl_requests.freight_charges as freight_charges','ftl_requests.total_charges as total_charges','ftl_requests.gst as gst',DB::raw("((select SUM(amount) from ftl_request_additional_costs where ftl_request_id = ftl_requests.id) + ftl_requests.freight_cost) as total_cost")]);

        $datatables = Datatables::of($data)
            ->editColumn('req_id',function ($data){
                return str_pad($data->req_id, 4, '0', STR_PAD_LEFT);
            })
            ->addColumn('tracking_number_link', function ($data) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$data->tracking_number' class='tracking' target='_blank'>$data->tracking_number</a></u>";
            })
            ->addColumn('action', function ($data) {
                return "";
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
        if($request->shipper = 0)
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

    static public function FTLRequestStatusHistory($request_id,$status_id,$user_id)
    {
        $history = new FtlRequestStatusHistory();
        $history->ftl_request_id = $request_id;
        $history->status_id = $status_id;
        $history->updated_by = $user_id;
        $history->save();
    }
}
