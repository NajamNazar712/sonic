<?php

namespace App\Http\Controllers\Admins;

use App\FnfSectionAdministration;
use App\FnfSectionCustomerExperience;
use App\FnfSectionFinance;
use App\FnfSectionHod;
use App\FnfSectionHr;
use App\FnfSectionItSupport;
use App\FnfSectionReportingManager;
use App\FnfStatusJourney;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\FnfSectionEmployee;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\HR\EmployeeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Nullable;
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
        ActivityTrailController::createActivityTrailLog(Auth::id(),419);
        return view('admin.human_resource.fnf.index')->with(['departments' => $departments]);
    }

    public function list(Request $request){

    if($request->get('excel') && $request->get('excel') == true)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),420);
    }
     $employee = Employee::join('fnf_section_employees as fnf','fnf.employee_id','=','employees.id')
         ->join('admins as a','a.id','=','fnf.line_manager')
         ->join('admins as ah','ah.id','=','fnf.hod')
         ->join('admins as h','h.id','=','fnf.created_by')
         ->join('admin_departments as d','d.id','=','employees.department_id')
         ->join('employee_designations as ed','ed.id','=','employees.designation_id')
         ->join('cities as c','c.id','=','employees.city_id')
         ->join('fnf_statuses as fs','fs.id','=','fnf.status_id')
         ->select(['fnf.id as id','fnf.id as fnf_id','employees.name','employees.city_id','employees.phone_number','a.name as line_manager','ah.name as hod','d.name as department','ed.name  as designation','employees.id as employee_id','employees.trax_id','c.id as city_id','c.name as city','employees.name as employee_name','fnf.status_id as status_id','fnf.joining_date','fnf.resign_date','fnf.created_at','h.name as created_by','employees.id as employee','fs.name as status','fnf.hod as hod_id','fnf.line_manager as reporting_manager']);

     $datatables = Datatables::of($employee)
         ->editColumn('fnf_id',function ($fnf) {
            return 'FNF'.$fnf->fnf_id;
         })
         ->addColumn("actions", function ($result) {
             if (session('role_id') == 1 || count(array_intersect([570,571,572,573,574,575,576,577,578], session('permissions'))) !== 0) {
                 $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                 if (session('role_id') == 1 || $result->reporting_manager == Auth::id() || in_array(570, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item rm_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reporting Manager View</div></button>';

                 }
                 if (session('role_id') == 1 || in_array(571, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item cs_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Customer Experience View</div></button>';

                 }
                 if (session('role_id') == 1 || in_array(572, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item admin_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Administration View</div></button>';

                 }
                 if (session('role_id') == 1 || in_array(573, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item it_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">IT Support View</div></button>';

                 }
                 if (session('role_id') == 1 || in_array(574, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item finance_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Finance View</div></button>';

                 }
                 if (session('role_id') == 1 || $result->hod_id == Auth::id() || in_array(575, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item hod_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">HOD View</div></button>';

                 }
                 if (session('role_id') == 1 || in_array(576, session('permissions'))) {
                    $dropdown .= '<button type="button" class="dropdown-item hr_view" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">HR View</div></button>';
                    $dropdown .= '<button type="button" class="dropdown-item hr_print" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">HR Print</div></button>';

                 }
                 if ((session('role_id') == 1 || $result->reporting_manager == Auth::id() || in_array(577, session('permissions'))) && $result->status_id != 4 ) {
                     $dropdown .= '<button type="button" class="dropdown-item update" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Update FNF Request</div></button>';

                 }
                 if (session('role_id') == 1 || in_array(578, session('permissions'))) {
                     $dropdown .= '<button type="button" class="dropdown-item history" data-target-id=' . $result->id . ' rel="#" data-toggle="modal" data-target="#"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">FNF Status History</div></button>';

                 }

                 $dropdown .= '
                        </div>
                      </div>
                    ';

                 return $dropdown;
             }
             else {
                 return '';
             }
         });
     ;
     return $datatables->make(true);
    }

    public function add(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),421);
        $employee = Employee::whereNotNull('trax_id')->select('trax_id')->get();
        if(session('department_id') == 1){
            $departments = AdminDepartment::get();
        }
        else{
            $departments = AdminDepartment::where('id',session('department'))->get();
        }
        $designations = EmployeeDesignation::where('status',1)->get();
        $employee_statuses = EmployeeStatus::all();
        return view('admin.human_resource.fnf.add',compact('departments','employee_statuses','employee','designations'));
    }

    public function submit(Request $request){

        $trax_id = $request->trax_id;
        if($trax_id){
            
            $line_manager = Employee::where('official_email',$request->line_manager)->where('department_id',$request->department_id);
           
            $hod = Employee::where('official_email',$request->hod)->where('department_id',$request->department_id);

           
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
                return redirect()->back()->with('error','No HOD Found for the given email');
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

            $employee->joining_date = $request->joining_date_formatted;
            $employee->save();
            $line_manager_trax_id = Admin::find($fnf->line_manager)->trax_id;
            $hod_trax_id = Admin::find($fnf->hod)->trax_id;
            $admins = array('Trax01099','Trax04484','Trax00043','Trax03840','Trax02533',$line_manager_trax_id,$hod_trax_id);
            NotificationsController::send(146,$fnf->id,$admins);
            return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request Added Successfully']);
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
            $data['city'] =  $employee->city->name;
            $data['joining_date'] =  $employee->joining_date;
            
            return response()->json(['status' => 1, 'data' => $data]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'No Data Found']);
        }
    }

    public function reporting_manager_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),422);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            $employee = Employee::where('id',$fnf->employee_id)->first();
            $rm = FnfSectionReportingManager::where('fnf_id',$id);
            if(!$rm->exists()){
                $rm = Null;
            }
            else{
                $rm = $rm->first();
            }
            return view('admin.human_resource.fnf.manager_index',compact('employee','fnf','rm'));
        }
    }

    public function reporting_manager_submit(Request $request){
        $fnf_id = $request->fnf_id;
        if($fnf_id){
            $rm = new FnfSectionReportingManager();
            $rm->fnf_id = $fnf_id;
            $rm->overtime = $request->overtime;
            $rm->holiday = $request->holiday;
            $rm->pickup_incentive = $request->pickup_incentive;
            $rm->fixed_incentive = $request->fixed_incentive;
            $rm->delivery_incentive = $request->delivery_incentive;
            $rm->extra_duty = $request->extra_duty;
            $rm->iou = $request->iou;
            $rm->penalty = $request->penalty;
            $rm->comments = $request->comments;
            $rm->created_by = Auth::id();
            $rm->status_id = 1;
            $rm->save();

            $this::AddFnfStatusJourney($fnf_id, 1,Auth::id(),1 );

            return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Data Added Successfully']);
        }


    }

    public function cs_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),423);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            $employee = Employee::where('id',$fnf->employee_id)->first();
            $cs = FnfSectionCustomerExperience::where('fnf_id',$id);
            if(!$cs->exists()){
                $cs = Null;
            }
            else{
                $cs = $cs->first();
            }

            return view('admin.human_resource.fnf.cs_index',compact('employee','fnf','cs'));
        }
    }

    public function cs_submit(Request $request){
       $fnf_id = $request->fnf_id;
       if($fnf_id){
           $cs = new FnfSectionCustomerExperience();
           $cs->fnf_id = $fnf_id;
           $cs->call_deduction = $request->phone_call_deduction;
           $cs->parcel = $request->open_parcel;
           $cs->fake_status = $request->fake_status;
           $cs->month_closing = $request->month_closing;
           $cs->comments = $request->comments;
           $cs->created_by = Auth::id();
           $cs->status_id = 1;
           $cs->save();

           $this::AddFnfStatusJourney($fnf_id, 1,Auth::id(),2 );

           return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Data Added Successfully']);
       }
    }

    public function administration_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),424);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            $employee = Employee::where('id',$fnf->employee_id)->first();
            $admin = FnfSectionAdministration::where('fnf_id',$id);
            if(!$admin->exists()){
                $admin = Null;
            }
            else{
                $admin = $admin->first();
            }
            return view('admin.human_resource.fnf.admin_index',compact('employee','fnf','admin'));
        }
    }

    public function administration_submit(Request $request){
        $fnf_id = $request->fnf_id;
        if($fnf_id){
            $admin = new FnfSectionAdministration();
            $admin->fnf_id = $fnf_id;
            $admin->auction = $request->auction_sell;
            $admin->shirt = $request->tshirts;
            $admin->maintenance = $request->maintenance;
            $admin->comments = $request->comments;
            $admin->created_by = Auth::id();
            $admin->status_id = 1;
            $admin->save();

            $this::AddFnfStatusJourney($fnf_id, 1,Auth::id(),3 );

            return redirect()->route('admin.human_resource.fnf.index')->with(['success'=>'Data Added Successfully']);
        }
    }

    public function it_support_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),425);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            $employee = Employee::where('id',$fnf->employee_id)->first();
            $support = FnfSectionItSupport::where('fnf_id',$id);
            if(!$support->exists()){
                $support = Null;
            }
            else{
                $support = $support->first();
            }
            return view('admin.human_resource.fnf.it_support',compact('employee','fnf','support'));
        }
    }

    public function it_support_submit(Request $request){
        $fnf_id = $request->fnf_id;
        if($fnf_id){
            $admin = new FnfSectionItSupport();
            $admin->fnf_id = $fnf_id;
            $admin->comments = $request->comments;
            $admin->created_by = Auth::id();
            $admin->status_id = 1;
            $admin->save();

            $this::AddFnfStatusJourney($fnf_id, 1,Auth::id(),4);

            return redirect()->route('admin.human_resource.fnf.index')->with(['success'=>'Data Added Successfully']);
        }
    }

    public function finance_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),426);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            $employee = Employee::where('id',$fnf->employee_id)->first();
            $finance = FnfSectionFinance::where('fnf_id',$id);
            if(!$finance->exists()){
                $finance = Null;
            }
            else{
                $finance = $finance->first();
            }
            return view('admin.human_resource.fnf.finance',compact('employee','fnf','finance'));
        }
    }

    public function finance_submit(Request $request){
      
        $fnf_id = $request->fnf_id;
        if($fnf_id){
            $finance = new FnfSectionFinance();
            $finance->fnf_id = $fnf_id;
            $finance->advance_salary = $request->salary;
            $finance->loan_outstanding = $request->loan;
            $finance->short_cash = $request->cash;
            $finance->cod_recovery = $request->cod;
            $finance->iou = $request->iou;
            $finance->tax = $request->tax;
            $finance->comments = $request->comments;
            $finance->created_by = Auth::id();
            $finance->status_id = 1;
            $finance->save();

            $this::AddFnfStatusJourney($fnf_id, 1,Auth::id(),5);

            return redirect()->route('admin.human_resource.fnf.index')->with(['success'=>'Data Added Successfully']);
        }
    }

    public function hr_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),428);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            if($fnf->hod_approval){
                $hod_approval = $fnf->hod_approval->status_id;
            }
            else{
                $hod_approval = Null;
            }
            $hr = FnfSectionHr::where('fnf_id',$id);
            if(!$hr->exists()){
                $hr = Null;
            }
            else{
                $hr = $hr->first();
            }
            $trax_ids = Employee::whereNotNull('trax_id')->select('trax_id')->get();
            $designations = EmployeeDesignation::where('status',1)->get();
            $departments = AdminDepartment::all();
            return view('admin.human_resource.fnf.hr',compact('designations','fnf','hod_approval','departments','trax_ids','hr'));
        }
    }

    public function hr_submit(Request $request){

        $fnf_id = $request->fnf_id;
        if($fnf_id){
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

            $fnf = FnfSectionEmployee::where('id',$fnf_id)->first();

            $fnf->joining_date = $request->joining_date_formatted;
            $fnf->resign_date = $request->resign_date_formatted;
            $fnf->line_manager = $line_manager->id;
            $fnf->hod = $hod->id;
            $fnf->status_id = 1;
            $fnf->updated_by = Auth::id();

            $hr = new FnfSectionHr();
            $hr->fnf_id = $fnf_id;
            $hr->medical = $request->medical;
            $hr->notice_period = $request->notice_period;
            $hr->penalty = $request->penalty;
            $hr->van_deduction = $request->deduction;
            //$hr->comments = $request->comments;
            $hr->created_by = Auth::id();
            $hr->status_id = 1;
            $hr->save();

            $this::AddFnfStatusJourney($fnf_id,1,Auth::id(),7);

            return redirect()->route('admin.human_resource.fnf.index')->with(['success' =>'Data Added Successfully']);
        }
    }

    public function rm_status_edit(Request $request){

        $rm = FnfSectionReportingManager::where('fnf_id',$request->fnf_id)->first();
        $rm->overtime = $request->overtime;
        $rm->holiday = $request->holiday;
        $rm->pickup_incentive = $request->pickup_incentive;
        $rm->fixed_incentive = $request->fixed_incentive;
        $rm->delivery_incentive = $request->delivery_incentive;
        $rm->extra_duty = $request->extra_duty;
        $rm->iou = $request->iou;
        $rm->penalty = $request->penalty;
        $rm->comments = $request->comments;
        if($request->approval == 'approved'){
            $rm->status_id = 2;
        }
        elseif ($request->approval == 'rejected'){
            $rm->status_id = 3;
        }
        $rm->save();

        $this::AddFnfStatusJourney($request->fnf_id,$rm->status_id,Auth::id(),1);
        
        return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request has been ' . $request->approval]);
    }

    public function cs_status_edit(Request $request){

        $cs = FnfSectionCustomerExperience::where('fnf_id',$request->fnf_id)->first();
        $cs->call_deduction = $request->phone_call_deduction;
        $cs->parcel = $request->open_parcel;
        $cs->fake_status = $request->fake_status;
        $cs->month_closing = $request->month_closing;
        $cs->comments = $request->comments;
        if($request->approval == 'approved'){
            $cs->status_id = 2;
        }
        elseif ($request->approval == 'rejected'){
            $cs->status_id = 3;
        }
        $cs->save();

        $this::AddFnfStatusJourney($request->fnf_id,$cs->status_id,Auth::id(),2);

        return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request has been ' . $request->approval]);
    }

    public function administration_status_edit(Request $request){
      
        $admin = FnfSectionAdministration::where('fnf_id',$request->fnf_id)->first();
        $admin->auction = $request->auction_sell;
        $admin->shirt = $request->tshirts;
        $admin->maintenance = $request->maintenance;
        $admin->comments = $request->comments;
        if($request->approval == 'approved'){
            $admin->status_id = 2;
        }
        elseif ($request->approval == 'rejected'){
            $admin->status_id = 3;
        }
        $admin->save();

        $this::AddFnfStatusJourney($request->fnf_id,$admin->status_id,Auth::id(),3);

        return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request has been ' . $request->approval]);
    }

    public function it_support_status_edit(Request $request){

        $admin = FnfSectionItSupport::where('fnf_id',$request->fnf_id)->first();
        $admin->comments = $request->comments;
        if($request->approval == 'approved'){
            $admin->status_id = 2;
        }
        elseif ($request->approval == 'rejected'){
            $admin->status_id = 3;
        }
        $admin->save();

        $this::AddFnfStatusJourney($request->fnf_id,$admin->status_id,Auth::id(),4);

        return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request has been ' . $request->approval]);
    }

    public function finance_status_edit(Request $request){

        $finance = FnfSectionFinance::where('fnf_id',$request->fnf_id)->first();
        $finance->advance_salary = $request->salary;
        $finance->loan_outstanding = $request->loan;
        $finance->short_cash = $request->cash;
        $finance->cod_recovery = $request->cod;
        $finance->iou = $request->iou;
        $finance->tax = $request->tax;
        $finance->comments = $request->comments;
        if($request->approval == 'approved'){
            $finance->status_id = 2;
        }
        elseif ($request->approval == 'rejected'){
            $finance->status_id = 3;
        }
        $finance->save();

        $this::AddFnfStatusJourney($request->fnf_id,$finance->status_id,Auth::id(),5);

        return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request has been ' . $request->approval]);
    }

    public function hod_approval_index($id){
        ActivityTrailController::createActivityTrailLog(Auth::id(),4227);
      $fnf = FnfSectionEmployee::find($id);
      $employee = Employee::where('id',$fnf->employee_id)->first();
      if($fnf->hod_approval){
          $approval = $fnf->hod_approval;
      }
      else{
          $approval = Null;
      }
      return view('admin.human_resource.fnf.hod_view',compact('fnf','employee','approval'));
    }

    public function hod_approval_submit(Request $request){
     
      $hod = new FnfSectionHod();
      $hod->fnf_id = $request->fnf_id;
      $hod->comments = $request->hod_comments;
      $hod->approved_by = Auth::id();
      if($request->approval == 'approved'){
          $hod->status_id = 2;
      }
      else{
          $hod->status_id = 3;
      }
      $hod->save();

      $this::AddFnfStatusJourney($request->fnf_id, $hod->status_id,Auth::id(),6);

      return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request has been ' . $request->approval]);
    }

   public function hr_status_edit(Request $request){

       $fnf_id = $request->fnf_id;
       if($fnf_id) {
           $line_manager = Admin::where('email', $request->line_manager);
           $hod = Admin::where('email', $request->hod);
           if ($line_manager->exists()) {
               $line_manager = $line_manager->first();
           } else {
               return redirect()->back()->with('error', 'No Line Manager Found for the given email');
           }

           if ($hod->exists()) {
               $hod = $hod->first();
           } else {
               return redirect()->back()->with('error', 'No Line Manager Found for the given email');
           }

           $hr = FnfSectionHr::where('fnf_id',$fnf_id)->first();
           $hr->medical = $request->medical;
           $hr->notice_period = $request->notice_period;
           $hr->penalty = $request->penalty;
           $hr->van_deduction = $request->deduction;
           $hr->comments = $request->comments;
           $hr->status_id = 4;
           $hr->save();

           $fnf = FnfSectionEmployee::where('id', $fnf_id)->first();

           $fnf->joining_date = $request->joining_date_formatted;
           $fnf->resign_date = $request->resign_date_formatted;
           $fnf->line_manager = $line_manager->id;
           $fnf->hod = $hod->id;
           $fnf->status_id = 4;
           $fnf->updated_by = Auth::id();
           $fnf->save();

           $this::AddFnfStatusJourney($fnf_id,$hr->status_id,Auth::id(),7);

           return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Data Added Successfully']);
       }
   }

   public function edit_fnf_request($id){
       ActivityTrailController::createActivityTrailLog(Auth::id(),429);
        $fnf = FnfSectionEmployee::find($id);
        $designations = EmployeeDesignation::all();
        $departments  = AdminDepartment::all();
        $trax_ids = Employee::select('trax_id')->get();
        return view('admin.human_resource.fnf.edit',compact('fnf','designations','departments','trax_ids'));
   }

   public function update_fnf_request(Request $request){

       $trax_id = $request->trax_id;
       if($trax_id) {
           $line_manager = Admin::where('email', $request->line_manager);
           $hod = Admin::where('email', $request->hod);
           if ($line_manager->exists()) {
               $line_manager = $line_manager->first();
           } else {
               return redirect()->back()->with('error', 'No Line Manager Found for the given email');
           }

           if ($hod->exists()) {
               $hod = $hod->first();
           } else {
               return redirect()->back()->with('error', 'No HOD Found for the given email');
           }

           $employee = Employee::where('trax_id', $trax_id)->first();

           $fnf = FnfSectionEmployee::where('employee_id',$employee->id)->first();
           $fnf->joining_date = $request->joining_date_formatted;
           $fnf->resign_date = $request->resign_date_formatted;
           $fnf->line_manager =$line_manager->id;
           $fnf->hod = $hod->id;
           $fnf->updated_by = Auth::id();
           $fnf->save();

           return redirect()->route('admin.human_resource.fnf.index')->with(['success' => 'Request Updated Successfully']);
       }
   }

   static public function AddFnfStatusJourney($fnf_id,$status_id,$admin_id,$section_id = NULL)
    {
        $history = new FnfStatusJourney();
        $history->fnf_id = $fnf_id;
        $history->status_id = $status_id;
        $history->admin_id = $admin_id;
        $history->section_id = $section_id;
        $history->save();
    }

    public function fnf_history_index($id){
    ActivityTrailController::createActivityTrailLog(Auth::id(),430);
     return view('admin.human_resource.fnf.history',compact('id'));
    }

    public function status_history_list(Request $request){
        $fnf = FnfStatusJourney::join('fnf_section_employees as fnf','fnf.id','=','fnf_status_journeys.fnf_id')
            ->join('fnf_sections as fs','fs.id','=','fnf_status_journeys.section_id')
            ->join('fnf_statuses as fss','fss.id','=','fnf_status_journeys.status_id')
            ->join('admins as a','a.id','=','fnf_status_journeys.admin_id')
            ->select(['fnf.id as fnf_id','fs.name as section_name','fss.name as status_name','a.name as admin','fnf_status_journeys.created_at'])
        ->where('fnf.id',$request->id);

        $datatable = Datatables::of($fnf);
        return $datatable->make(true);
    }

    public function hr_print(Request $request){
        $id = $request->id;
        ActivityTrailController::createActivityTrailLog(Auth::id(),515);
        $fnf = FnfSectionEmployee::find($id);
        if($fnf){
            $hr = FnfSectionHr::where('fnf_id',$id);
            if(!$hr->exists()){
                $hr = Null;
            }
            else{
                $hr = $hr->first();
            }
           
           
            $html = '<!doctype html>
            <html lang="en">
              <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') . '">

                <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                <title>Digital Sales Performa</title>

            <style>
              @page {
                size: A4 portrait;
              }

              * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
              }

              body {
                background: none !important;
                color: #09262e !important;
                font-size: 0.9rem !important;
              }

              hr {
                border-top: 1px dashed #000000;
              }

              table.table-bordered {
                page-break-inside: avoid;
              }

              table.table-bordered tbody tr td {
                width: 12.5% !important;
                border: 1px solid #09262e !important;
              }

              .color.primary {
                background: #c8c8c8 !important;
              }

              .color.secondary {
                background: #ebebeb !important;
              }

              .border {
                border: 1px solid #09262e !important;
              }

              .border.twice {
                border-width: 2px !important;
              }

              .border.twice-top {
                border-top-width: 2px !important;
              }

              .border.twice-bottom {
                border-bottom-width: 2px !important;
              }

              .border.twice-left {
                border-left-width: 2px !important;
              }

              .border.twice-right {
                border-right-width: 2px !important;
              }

              td.replacement span {
                width: 22px;
              }

              td.replacement span img {
                display: block;
                width: 100%;
                margin: auto;
                background: #c8c8c8;
                border-radius: 25px;
              }

              .void {
                top: 0;
                bottom: 0;
                right: 0;
                left: 0;
                height: 80px;
                font-size: 5rem;
                line-height: 3.5rem;
                opacity: 0.25;
              }
               div.page
                {
                    page-break-after: always;
                    page-break-inside: avoid;
                }
                .piece_number{
                    font-size: 2.5rem;
                }
            </style>
              </head>
              <body>
                <div>';

        $html .= '<div class="container-fluid text-center p-3">
                        <div class="row justify-content-end mb-2">
                            <div class="col">
                                <img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto">
                            </div>
                            <div class="col">
                                <h2>FNF Request Details</h2>
                            </div>
                        </div>
                        <div class="row justify-content-end mb-2">
                            <div class="col">
                                    <table class="table table-bordered border">
                                        <tbody>
                                        <tr><td class="color primary w-50">Employee ID:</td><td class=" w-50">'. $fnf->employee->trax_id .'</td> <td class="color primary w-50">Employee Name:</td><td class=" w-50">'. $fnf->employee->name .'</td></tr>
                                        <tr><td class="color primary w-50">Designation:</td><td class=" w-50">'. $fnf->employee->designation->name .'</td> <td class="color primary w-50">Department:</td><td class=" w-50">'. $fnf->employee->department->name .'</td></tr>
                                        <tr><td class="color primary w-50">Date of Joining:</td><td class=" w-50">'. $fnf->joining_date .'</td> <td class="color primary w-50">Date of Resign:</td><td class=" w-50">'. $fnf->resign_date .'</td></tr>
                                        <tr><td class="color primary w-50">Line Manager Email:</td><td class=" w-50">'. $fnf->reporting_manager->email .'</td> <td class="color primary w-50">HOD Email:</td><td class=" w-50">'. $fnf->department_head->email .'</td></tr>
                                        </tbody>
                                    </table>
                            </div>
                           
                        </div>
                        <div class="col border mb-2">
                                <div class="row justify-content-center m-1">
                                    <h5><b>FNF Status</b></h5>
                                </div>
                                <table class="table table-bordered border">
                                    <tbody>
                                        <tr>
                                            <td class="color primary"><b>Heads</b></td>
                                            <td class="color primary"><b>Status</b></td>
                                            <td class="color primary"><b>Comments</b></td>
                                        </tr>';

                               
                                    $html .= '
                                        <tr>
                                            <td style="border-bottom: none !important;"><b>Reporting Manager</b></td>';
                                            if($fnf->manager){

                                                $html .='
                                                <td>Inprocess</td><td>'.$fnf->manager->comments.'</td>';
                                            }else{
                                                $html .='
                                                <td>Pending</td><td></td>';
                                            }
                                    $html .=' </tr>';

                                    $html .= '
                                        <tr>
                                            <td style="border-bottom: none !important;"><b>Customer Experience</b></td>';
                                            if($fnf->customer_experience){

                                                $html .='
                                                <td>Inprocess</td><td>'.$fnf->customer_experience->comments.'</td>';
                                            }else{
                                                $html .='
                                                <td>Pending</td><td></td>';
                                            }
                                    $html .=' </tr>';

                                    $html .= '
                                        <tr>
                                            <td style="border-bottom: none !important;"><b>Administration</b></td>';
                                            if($fnf->administration){

                                                $html .='
                                                <td>Inprocess</td><td>'.$fnf->administration->comments.'</td>';
                                            }else{
                                                $html .='
                                                <td>Pending</td><td></td>';
                                            }
                                    $html .=' </tr>';

                                    $html .= '
                                        <tr>
                                            <td style="border-bottom: none !important;"><b>IT Support</b></td>';
                                            if($fnf->it_support){

                                                $html .='
                                                <td>Inprocess</td><td>'.$fnf->it_support->comments.'</td>';
                                            }else{
                                                $html .='
                                                <td>Pending</td><td></td>';
                                            }
                                    $html .=' </tr>';

                                    $html .= '
                                        <tr>
                                            <td style="border-bottom: none !important;"><b>Finance</b></td>';
                                            if($fnf->finance){

                                                $html .='
                                                <td>Inprocess</td><td>'.$fnf->finance->comments.'</td>';
                                            }else{
                                                $html .='
                                                <td>Pending</td><td></td>';
                                            }
                                    $html .=' </tr>';

                                    $html .= '
                                        <tr>
                                            <td style="border-bottom: none !important;"><b>HOD</b></td>';
                                            if($fnf->hod_approval){

                                                $html .='
                                                <td>Inprocess</td><td>'.$fnf->hod_approval->comments.'</td>';
                                            }else{
                                                $html .='
                                                <td>Pending</td><td></td>';
                                            }
                                    $html .=' </tr>';
                                
      

        $html .= '
                                    </tbody>
                                </table>
                              
                        </div>';

        $html .= '
                        </div>';
        $html .= '
            <script>
              window.onload = function() {
                window.print();
              }
            </script>
            ';

        $html .= '
              </body>
            </html>
        ';
    return $html;
        }
    }
}
