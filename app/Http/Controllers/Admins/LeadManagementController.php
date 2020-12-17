<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\http\Models\Admin\Lead\Lead;
use App\http\Models\Admin\Lead\LeadStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        return view('admin.leads.index')->with(['sale_name'=>$salesperson, 'statuses' => $statuses]);
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
                $dropdown .= '<button type="button"  class="dropdown-item lead_log" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Lead Log</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item forward_lead" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Forward Lead</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item add_remarks" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Add Remarks</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item tag_sale_person" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Tag Sales Person</div></button>';
                $dropdown .= '<button type="button"  class="dropdown-item view_remarks" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Remarks</div></button>';
                if($lead->status_id == 1 || $lead->status_id == 4){
                    $dropdown .= '<button type="button"  class="dropdown-item follow_up" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Follow-Up</div></button>';
                }
                elseif($lead->status_id == 2){
                    $dropdown .= '<button type="button"  class="dropdown-item sent_proposal" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Proposal Sent</div></button>';
                    $dropdown .= '<button type="button"  class="dropdown-item unresponsive" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Unresponsive</div></button>';
                }
                elseif($lead->status_id == 3){
                    $dropdown .= '<button type="button"  class="dropdown-item account_activation" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Proceed to Account Registration</div></button>';
                    $dropdown .= '<button type="button"  class="dropdown-item unresponsive" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Unresponsive</div></button>';
                }
                elseif($lead->status_id == 5){
                    $dropdown .= '<button type="button"  class="dropdown-item follow_up" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Account Activated</div></button>';
                }

                return $dropdown;
            })->make(true);
    }
}
