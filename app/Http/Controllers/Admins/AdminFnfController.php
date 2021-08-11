<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\FnfSectionEmployee;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\HR\EmployeeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminFnfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
        $departments = AdminDepartment::all();
        return view('admin.human_resource.employee_clearance.index')->with(['departments' => $departments]);
    }

    public function list(Request $request){
     $employee = Employee::join('fnf_section_employees as fnf','fnf.employee_id','=','employees.id')
         ->join('admins as a','a.id','=','fnf.line_manager')
         ->join('admins as ah','ah.id','=','fnf.hod')
         ->join('admins as h','h.id','=','fnf.created_by')
         ->join('admin_departments as d','d.id','=','employees.department_id')
         ->join('employee_designations as ed','ed.id','=','employees.designation_id')
         ->join('cities as c','c.id','=','employees.city_id')
         ->select(['fnf.id as id','employees.name','employees.city_id','employees.phone_number','a.name as line_manager','ah.name as hod','d.name as department','ed.name  as designation','employees.id as employee_id','employees.trax_id','c.id as city_id','c.name as city','employees.name as employee_name','fnf.status_id','fnf.joining_date','fnf.resign_date','fnf.created_at','h.name as created_by','employees.id as employee']);

     $datatables = Datatables::of($employee)
     ->editColumn('employee',)
     ;
     return $datatables->make(true);
    }

    public function add(){
        $employee = Employee::whereNotNull('trax_id')->select('trax_id')->get();
        if(session('department_id') == 1){
            $departments = AdminDepartment::get();
        }
        else{
            $departments = AdminDepartment::where('id',session('department'))->get();
        }
        $designations = EmployeeDesignation::where('status',1)->get();
        $employee_statuses = EmployeeStatus::all();
        return view('admin.human_resource.employee_clearance.add',compact('departments','employee_statuses','employee','designations'));
    }

    public function submit(Request $request){

        $trax_id = $request->trax_id;
        if($trax_id){
            $line_manager = Admin::where('email',$request->line_manager);
            $hod = Admin::where('email',$request->hod);
            if($line_manager->exists()){
                $line_manager = $line_manager->first();
            }
            else{
                return redirect()->back()->with('error','No Line Manager Found for the given email');
            }

            if($hod->exists()){
                $hod = $hod->first();
            }
            else{
                return redirect()->back()->with('error','No Line Manager Found for the given email');
            }

            $employee = Employee::where('trax_id',$trax_id)->first();

            if(FnfSectionEmployee::where('employee_id',$employee->id)->exists()){
                return redirect()->back()->with('error','Data already exists for this trax id');
            }

            $fnf = new FnfSectionEmployee();
            $fnf->employee_id = $employee->id;
            $fnf->line_manager = $line_manager->id;
            $fnf->hod = $hod->id;
            $fnf->joining_date = $request->joining_date_formatted;
            $fnf->resign_date = $request->resign_date_formatted;
            $fnf->resign_date = $request->resign_date_formatted;
            $fnf->created_by = Auth::id();
            $fnf->status_id = 1;
            $fnf->save();
            return redirect()->route('admin.human_resource.fnf.index')->with(['success','Request Added Successfully']);
        }
    }

    public function employee_data(Request $request){
        $trax_id = $request->id;
        $employee = Employee::where('trax_id',$trax_id)->first();
        if($employee){
            $data = array();
            $data['name'] =  $employee->name;
            $data['designation'] =  $employee->designation_id;
            $data['department'] =  $employee->department_id;
            return response()->json(['status' => 1, 'data' => $data]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'No Data Found']);
        }
    }



}
