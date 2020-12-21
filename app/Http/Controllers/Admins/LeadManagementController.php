<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\http\Models\Admin\Lead\Lead;
use App\http\Models\Admin\Lead\LeadLog;
use App\http\Models\Admin\Lead\LeadRemark;
use App\http\Models\Admin\Lead\LeadStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class LeadManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        $salesperson = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();
        $statuses = LeadStatus::all();
        $lead_statuses = LeadStatus::where('id', '!=', 1)->get();
        return view('admin.leads.index')->with(['sale_name'=>$salesperson, 'statuses' => $statuses, 'lead_statuses' => $lead_statuses]);
    }

    public function list(Request $request){
        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->leftjoin('admins as sp', 'sp.id', '=', 'leads.sale_person_id')
            ->leftjoin('admins as rp', 'rp.id', '=', 'leads.reference_person_id')
            ->leftjoin('lead_statuses as ls', 'ls.id', '=', 'leads.status_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'leads.updated_by')
            ->select('leads.id as lead_id', 'leads.contact_person', 'leads.phone_number', 'leads.email_address', 'leads.requested_date', 'leads.message', 'leads.status_id', 'ls.name as status', 'ub.name as updated_by', 'sp.name as sale_person', 'rp.name as reference_person', 'c.name as city');

        return Datatables::of($leads)
            ->addColumn('aging',function ($lead){
                $days = Carbon::now()->diffInDays($lead->requested_date);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('action', function($lead){
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                $dropdown .= '<button type="button"  class="dropdown-item update" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item lead_log" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Lead Log</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item forward_lead" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Forward Lead</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item add_remarks" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Add Remarks</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item view_remarks" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Remarks</div></button>';

                return $dropdown;
            })->make(true);
    }

    public function add_status(Request $request){
        $lead_id = $request->lead_id;
        $lead = Lead::find($lead_id);
        $status = $request->status;
        if($status != NULL){
            $lead_log = new LeadLog();
            $lead_log->lead_id = $lead->id;
            $lead_log->prev_status_id = $lead->status_id;
            $lead_log->status_id = $status;
            $lead_log->updated_by = Auth::id();
            $lead_log->save();
            return response()->json(['status' => 1, 'success' => 'Status updated Successfully!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Invalid Status!']);
        }
    }

    public function lead_log_details(Request $request){
        $lead_id = $request->lead_id;
        $lead = Lead::find($lead_id);
        $lead_logs = LeadLog::where('lead_id', $lead_id);
        if($lead_logs->exists()){
            $lead_logs = $lead_logs->get();
            $details = array();
            foreach ($lead_logs as $log){
                $detail['lead_id'] = $lead->id;
                $detail['contact_person'] = $lead->contact_person;
                $detail['phone_number'] = $lead->phone_number;
                $detail['sales_person'] = $log->sales_person->name;
                $detail['reference_person'] = $log->reference_person->name;
                $detail['status'] = $log->status->name;
                $detail['updated_by'] = $log->admin->name;
                $detail['updated_at'] = $log->created_at;

                $details[] = $detail;
            }
            return response()->json(['status' => 1, 'leads' => $details]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Logs Does\'nt exist!']);
        }
    }

    public function add_remarks(Request $request){
        $lead_id = $request->lead_id;
        $lead = Lead::find($lead_id);
        $remarks = $request->remarks;
        if($remarks != NULL){
            $lead_remarks = new LeadRemark();
            $lead_remarks->lead_id = $lead->id;
            $lead_remarks->remarks = $remarks;
            $lead_remarks->updated_by = Auth::id();
            $lead_remarks->save();
            return response()->json(['status' => 1, 'success' => 'Remarks added Successfully!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Invalid Remarks!']);
        }
    }

    public function view_remarks_details(Request $request){
        $lead_id = $request->lead_id;
        $lead_remarks = LeadRemark::where('lead_id', $lead_id);
        if($lead_remarks->exists()){
            $lead_remarks = $lead_remarks->get();
            $details = array();
            foreach ($lead_remarks as $remark){
                $detail['remarks'] = $remark->remarks;
                $detail['updated_by'] = $remark->admin->name;
                $detail['updated_at'] = Carbon::parse($remark->updated_at)->toDateTimeString();

                $details[] = $detail;
            }
            return response()->json(['status' => 1, 'leads' => $details]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Remarks Does\'nt exist!']);
        }
    }
}
