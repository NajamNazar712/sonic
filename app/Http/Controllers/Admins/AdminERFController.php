<?php

namespace App\Http\Controllers\Admins;


use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Allowances;
use App\Http\Models\City;
use App\Http\Models\EmployeeRequisition;
use App\Http\Models\EmployeeRequisitionAllowances;
use App\Http\Models\EmployeeRequisitionReplacement;
use App\Http\Models\EmployeeRequisitionStatus;
use App\Http\Models\EmployeeRequisitionStatusLog;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use App\Models\Admin\AdminPositionTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminERFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
       
        $erf_status = EmployeeRequisitionStatus::select('id','name')->get();
        return view('admin.human_resource.erf.index')->with(['erf_status' => $erf_status]);
    }

    public function list(Request $request){
        $erf = EmployeeRequisition::join('admins as a', 'a.id', '=', 'employee_requisitions.department_head_id')
            ->join('cities as c', 'c.id', '=', 'employee_requisitions.city_id')
            ->join('cities as h', 'h.id', '=', 'employee_requisitions.hub_id')
            ->join('employee_designations as d', 'd.id', '=', 'employee_requisitions.designation_id')
            ->join('admin_departments as dp', 'dp.id', '=', 'employee_requisitions.department_id')
            ->join('employee_requisition_statuses as s', 's.id', '=', 'employee_requisitions.status')
            ->select(['employee_requisitions.id as erf_id', 'a.name as admin','c.name as city','h.name as hub','d.name as designation','dp.name as department','s.name as status','s.id']);



        $datatables = Datatables::of($erf)
            ->addColumn('actions',function ($erf) {
              /*  $view_charges_button = '<button type="button" class="dropdown-item view_charges"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Charges</div></button>';*/
                $view_button = '<button type="button" class="dropdown-item view_print"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-crosshair"></i></div><div class="col-9 offset-1">View</div></button>';
               /* $dispute_button = '<button type="button" class="dropdown-item dispute_modal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-circle"></i></div><div class="col-9 offset-1">Dispute</div></button>';*/



                    $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                    ';

                        $dropdown .= $view_button;

                    $dropdown .= '
                            </div>
                        </div>
                    ';
                return $dropdown;
            });

        if($status = $request->get('status')){
            $erf->where('employee_registrations.status', '=',$status);
        }


        return $datatables->make(true);

    }

    public function add(){
        $cities = City::where('status',1)->where('business_category_id',1)->select('id','name')->get();
        $hubs =  City::where('hub',1)->select('id','name')->get();
        $departments = AdminDepartment::where('id', '!=', 1)->select('id', 'name')->get();
        $designations = EmployeeDesignation::select('id','name')->get();
        $department_heads = Admin::whereIn('role_id', [2, 3, 4, 6, 15, 18, 19, 22, 25, 34, 36])->where('status', 1)->select('id','name')->get();
        $admin_positions = AdminPositionTypes::select('id','name')->get();
        $allowances = Allowances::all();
        $employee_trax_id = Employee::select('trax_id')->get();

        return view('admin.human_resource.erf.add')->with(['cities' => $cities,'hubs' => $hubs,'departments' => $departments,'designations' => $designations,'department_heads' => $department_heads,'admin_positions' => $admin_positions,'allowances' => $allowances,'employee_trax_id' => $employee_trax_id]);
    }

    public function submit(Request $request){
         return $request;
        $erf = new EmployeeRequisition();
        $erf->department_id = $request->department;
        $erf->designation_id = $request->designation;
        $erf->hub_id = $request->hub;
        $erf->city_id =  $request->city;
        $erf->department_head_id =  $request->department_head;
        $erf->status = 1;
        $erf->type = $request->erf_type;
        $erf->submitted_by = Auth::id();
        $erf->vacancies = $request->vacancies;
        $erf->position_type_id = $request->position;
        $erf->salary_from = $request->salary_from;
        $erf->salary_to = $request->salary_to;
        $erf->qualifications = $request->qualification;
        $erf->skills = $request->skills;
        $erf->job_description = $request->job_description;
        $erf->save();

       if($request->erf_type == 1){
           foreach($request->allowances as $allowance){
               $assigned_benefits = new EmployeeRequisitionAllowances();
               $assigned_benefits->er_id = $erf->id;
               $assigned_benefits->allowance_id = $allowance;
               $assigned_benefits->save();
           }
       }
       else{

          foreach($request->addmore as $data){

             $replacement = new EmployeeRequisitionReplacement();
             $replacement->er_id = $erf->id;
             $replacement->trax_id = $data['trax_id'];
             $replacement->last_gross_salary = $data['salary'];
             $replacement->date = $data['date'];
             $replacement->save();
          }
       }

       $log = new EmployeeRequisitionStatusLog();
       $log->er_id = $erf->id ;
       $log->admin_id = Auth::id();
       $log->status_id = 1;
        $log->save();

        $email = $request->admin_email;
        NotificationsController::send('133',$email,$erf->id);
        return view('admin.human_resource.erf.index')->with(['success' => 'Request Submitted']);

    }

    public function print(Request $request){
        //
    }


}
