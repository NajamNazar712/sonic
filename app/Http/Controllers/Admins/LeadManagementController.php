<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\http\Models\Admin\Lead\Lead;
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

        return view('admin.leads.index')->with(['sale_name'=>$salesperson]);
    }

    public function list(Request $request){
        $leads = Lead::join('cities as c', 'c.id', '=', 'leads.city_id')
            ->leftjoin('admins as sp', 'sp.id', '=', 'leads.sale_person_id')
            ->leftjoin('admins as rp', 'rp.id', '=', 'leads.reference_person_id')
            ->select('leads.id', 'leads.lead_id', 'leads.contact_person', 'leads.phone_number', 'leads.email_address', 'leads.requested_date', 'leads.message', 'leads.status_id', 'leads.updated_by', 'sp.name as sale_person', 'rp.name as sale_person', 'c.name as city');

        return Datatables::of($leads)->make(true);
    }
}
