<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\Admin\RiderType;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeBankInformation;
use App\Http\Models\HR\EmployeeBloodGroup;
use App\Http\Models\HR\EmployeeDesignation;
use App\Http\Models\HR\EmployeeDomicile;
use App\Http\Models\HR\EmployeeEducationalBackground;
use App\Http\Models\HR\EmployeeEmployementHistory;
use App\Http\Models\HR\EmployeeMaritalStatus;
use App\Http\Models\HR\EmployeeMedicalInformation;
use App\Http\Models\HR\EmployeeNationality;
use App\Http\Models\HR\EmployeeReference;
use App\Http\Models\HR\EmployeeRelationship;
use App\Http\Models\HR\EmployeeReligion;
use App\http\Models\ReportingLocation;
use App\Http\Models\Rider\RiderRequest;
use App\Http\Models\RiderCategory;
use App\Http\Models\Route;
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Rider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;
use Auth;

class AdminHumanResourseController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function download_docs()
    {
        return view('admin.human_resource.download_docs');
    }
    public function allusers()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),54);
        // $riders = Rider::where('status', 1)->get();
        //         $admins = Admin::where('status', 1)->get();
        $roles = ['Admin', 'Rider'];
        $roles = collect($roles);
        return view('admin.human_resource.allusers')->with(['roles' => $roles]);
    }
    public function all_user_ajax(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),114);
        }
        $assigned_hubs = session('hubs');
        if (session('role_id') != 1) {
            if (count($assigned_hubs) > 0) {
                // $riders = Rider::where('status',1)->where('rider_type_id',1)->whereIn('city_id', $assigned_hubs)->get();
                $riders = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
                    ->join('cities as c', 'cities.hub_id', '=', 'c.id')
                    ->select('c.name as hub', 'riders.id', 'riders.name', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.created_at as created_at')
                    ->where('rider_type_id', 1)
                    ->where('riders.status', 1)
                    ->whereIn('cities.hub_id', $assigned_hubs)->get();


                $admins = Admin::whereIn('default_hub_id', $assigned_hubs)->where('status', 1)->get();

                $users = array();
                if (count($riders) > 0) {
                    foreach ($riders as $rider) {
                        $user = array();
                        $user['id'] = $rider->id;
                        $user['name'] = $rider->name;
                        $user['cnic'] = $rider->cnic;
                        $user['phone'] = $rider->phone;
                        $user['trax_id'] = $rider->trax_id;
                        $user['role'] = 'Rider';
                        if ($rider->created_at) {
                            $user['created_at'] = date_format($rider->created_at, "Y/m/d H:i:s");
                        } else {
                            $user['created_at'] = $rider->created_at;
                        }
                        $users[] = $user;
                        $users = collect($users);
                    }
                }
                if (count($admins) > 0) {
                    foreach ($admins as $admin) {
                        $user = array();
                        $user['id'] = $admin->id;
                        $user['name'] = $admin->name;
                        $user['cnic'] = $admin->cnic;
                        $user['phone'] = $admin->phone_number;
                        $user['trax_id'] = $admin->trax_id;
                        $user['role'] = 'Admin';
                        if ($admin->created_at) {
                            $user['created_at'] = date_format($admin->created_at, "Y/m/d H:i:s");
                        } else {
                            $user['created_at'] = $admin->created_at;
                        }

                        $users[] = $user;
                        $users = collect($users);
                    }
                }

                return Datatables::of($users)
                    ->make(true);
            } else {
                $user = array();
                $user['id'] = NULL;
                $user['name'] = '';
                $user['cnic'] = '';
                $user['phone'] = '';
                $user['trax_id'] = '';
                $user['role'] = '';
                $user['created_at'] = '';
                $users[] = $user;
                $users = collect($users);
                return Datatables::of($users)
                    ->make(true);
            }
        } else {
           
            $riders = Rider::where('status', 1)->where('rider_type_id', 1)->get();

            $admins = Admin::where('status', 1)->get();

            $users = array();
            if (count($riders) > 0) {
                foreach ($riders as $rider) {
                    $user = array();
                    $user['id'] = $rider->id;
                    $user['name'] = $rider->name;
                    $user['cnic'] = $rider->cnic;
                    $user['phone'] = $rider->phone;
                    $user['trax_id'] = $rider->trax_id;
                    $user['role'] = 'Rider';
                    if ($rider->created_at) {
                        $user['created_at'] = date_format($rider->created_at, "Y/m/d H:i:s");
                    } else {
                        $user['created_at'] = $rider->created_at;
                    }
                    $users[] = $user;
                    $users = collect($users);
                }
            }
            if (count($admins) > 0) {
                foreach ($admins as $admin) {
                    $user = array();
                    $user['id'] = $admin->id;
                    $user['name'] = $admin->name;
                    $user['cnic'] = $admin->cnic;
                    $user['phone'] = $admin->phone_number;
                    $user['trax_id'] = $admin->trax_id;
                    $user['role'] = 'Admin';
                    if ($admin->created_at) {
                        $user['created_at'] = date_format($admin->created_at, "Y/m/d H:i:s");
                    } else {
                        $user['created_at'] = $admin->created_at;
                    }

                    $users[] = $user;
                    $users = collect($users);
                }
            }

            return Datatables::of($users)
                ->make(true);
        }
    }

    public function employee_directory_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),57);
        $rider_type = RiderType::all();
        $route = Route::all();
        $operation_rider_category = OperationRidersCategory::all();
        $rider_categories = RiderCategory::all();
        return view('admin.human_resource.employee_directory.index')->with(['rider_categories' => $rider_categories, 'rider_types'=>$rider_type, 'routes' => $route,'operation_rider_category' => $operation_rider_category]);
    }

    public function employee_directory_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),117);
        }
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->join('employee_genders as eg','eg.id','=','employees.employee_gender_id')
            ->join('employee_types as et','et.id','=','employees.employee_type_id')
            ->join('employee_request_statuses as ers','ers.id','=','employees.request_status_id')
            ->join('employee_statuses as es','es.id','=','employees.status_id')
            ->select(['employees.id as employee_id', 'employees.name as employee_name', 'cities.name as city' ,'employees.trax_id' ,'employees.request_status_id' ,'employees.employee_type_id', 'eg.name as gender', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type','ers.name as request_status', 'es.name as status', 'employees.created_at as requested_at']);

        if (session('role_id') != 1) {
            $employees = $employees->whereIn('cities.hub_id', session('hubs'));
        }

        return Datatables::of($employees)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->employee_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('users.id', function ($query, $keyword) {
                return $query->where('users.id', '=', $keyword);
            })
            ->addColumn("action", function ($result) {
                if(session('role_id') == 1 || (in_array(468, session('permissions')) || (in_array(469, session('permissions')) && ($result->request_status_id == 1 || $result->request_status_id == 2)))){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(468, session('permissions'))) {
                        $route = route("admin.human_resource.employee_directory.edit", $result->employee_id);
                        $dropdown .= '<a href="' . $route . '" class="dropdown-item" ><i class="ft-edit"></i> Update Details</a>';
                    }
                    if($result->request_status_id == 1 || $result->request_status_id == 2){
                        if (session('role_id') == 1 || in_array(469, session('permissions'))) {

                            $dropdown .= '<button type="button" class="dropdown-item approve" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve</div></button>';

                            $dropdown .= '<button type="button" class="dropdown-item reject" data-target-id=' . $result->employee_id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reject</div></button>';

                        }
                    }
                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            })
            ->make(true);
    }

    public function employee_directory_approve(Request $request){
        if(is_array($request->employee_ids)){
            foreach ($request->employee_ids as $employee_id) {
                $employee = Employee::find($employee_id);
                if(in_array($employee->request_status_id, [1, 2])) {
                    $global_setting = GlobalSettings::where('type', 'latest_employee_id');

                    if ($global_setting->exists()) {
                        $global_setting = $global_setting->first();
                        $trax_id = $global_setting->setting_value + 1;
                        $global_setting->setting_value = $trax_id;
                        $global_setting->save();
                        $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);
                    } else {
                        $trax_id = null;
                    }

                    $employee->trax_id = $trax_id;
                    $employee->request_status_id = 3;
                    $employee->save();
                }
            }

            return response()->json(['status' => 0, 'success' => 'Employee(s) Approved Successfully!']);
        }
        else{
            return response()->json(['status' => 1, 'success' => 'Invalid Selection!']);
        }
    }
    public function employee_directory_reject(Request $request){
        if(is_array($request->employee_ids)) {
            foreach ($request->employee_ids as $employee_id) {
                $employee = Employee::find($employee_id);
                if(in_array($employee->request_status_id, [1, 2])){
                    $employee->request_status_id = 4;
                    $employee->save();

                    if ($employee->employee_type_id == 2) {
                        RiderRequest::where('id', $employee->rider_request_id)->delete();
                    }
                }
            }
            return response()->json(['status' => 0, 'success' => 'Employee(s) Rejected Successfully!']);
        }
        else{
            return response()->json(['status' => 1, 'success' => 'Invalid Selection!']);
        }

    }

    public function employee_directory_edit(Employee $employee)
    {
        $religions = EmployeeReligion::all();
        $nationalities = EmployeeNationality::all();
        $domiciles = EmployeeDomicile::all();
        $maritial_statuses = EmployeeMaritalStatus::all();
        $blood_groups = EmployeeBloodGroup::all();
        $designations = EmployeeDesignation::all();
        $cities = City::where('status',1)->where('business_category_id',1)->get();
        $zones = Zone::where('status', 1)->get();
        $departments = AdminDepartment::all();
        $relationships = EmployeeRelationship::all();
        $banks = BanksList::where('status',1)->get();
        $medical_infos = $employee->medical_infos;
        $bank_info = $employee->bank_info;
        $reference = $employee->reference;
        $educations = $employee->education_infos;
        $employments = $employee->employment_history;
        $attachments = $employee->attachments;
        $place_of_birth_cities = City::where('business_category_id',1)->get();
        $reporting_locations = ReportingLocation::where('status',1)->get();
        return view('admin.human_resource.employee_directory.update',compact('employments','blood_groups','attachments','educations','reference','bank_info','banks','medical_infos','employee','religions','nationalities','domiciles','maritial_statuses','designations','departments','zones','relationships', 'place_of_birth_cities','cities','reporting_locations'));
    }

    public function employee_directory_profile_update (Employee $employee, Request $request)
    {
        $employee->request_status_id = 2;
        $employee->guardian_name = $request->name;
        $employee->religion_id = $request->religion;
        $employee->nationality_id = $request->nationality;
        $employee->domicile_id = $request->domicile;
        $employee->marital_status_id = $request->marital_status;
        $employee->blood_group = $request->blood_group;
        $employee->personal_email = $request->personal_email;
        $employee->address = $request->address;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->cnic_issue_date = $request->cnic_issue_date_formatted;
        $employee->cnic_expiry_date = $request->cnic_expiry_date_formatted;
        $employee->designation_id = $request->designation;
        $employee->city_id = $request->city;
        $employee->department_id = $request->department;
        $employee->zone_id = $request->zone;
        $employee->official_email = $request->official_email;
        $employee->official_phone_number = $request->official_number;
        $employee->sonic_id = $request->sonic_id;
        $employee->pin = $request->bolt_pin;
        $employee->place_of_birth = $request->place_of_birth;
        $employee->date_of_birth = $request->date_of_birth_formatted;
        $employee->reporting_location_id = $request->reporting_location;
        $employee->update();

        return back()->with(['success'=>'Employee Profile Updated Successfully']);
    }

    public function employee_directory_medical_update(Employee $employee, Request $request)
    {
        if($employee->medical_infos->count() > 0)
        {
            $employee->medical_infos()->delete();
        }
        foreach ($request->name as $key => $value)
        {
            $medical_info = new EmployeeMedicalInformation();
            $medical_info->employee_id = $employee->id;
            $medical_info->name = $request->name[$key];
            $medical_info->relationship_id = $request->relationship[$key];
            $medical_info->date_of_birth = $request->formatted_dob[$key];
            $medical_info->marital_status = $request->marital_status[$key];
            $medical_info->save();

            echo $request->formatted_dob[$key];
        }
        if(count($request->name) > 0)
        {
            $employee->request_status_id = 2;
            $employee->update();
        }

        return back()->with(['success'=>'Employee Medical Information Updated Successfully']);
    }

    public function employee_directory_education_update(Employee $employee, Request $request)
    {
        if($employee->education_infos->count() > 0)
        {
            $employee->education_infos()->delete();
        }
        foreach ($request->name as $key => $value)
        {
            $education = new EmployeeEducationalBackground();
            $education->employee_id = $employee->id;
            $education->name = $request->name[$key];
            $education->degree = $request->degree[$key];
            $education->grade = $request->grade[$key];
            $education->passing_year = $request->formatted_passing_year[$key];
            $education->save();
        }

        if(count($request->name) > 0)
        {
            $employee->request_status_id = 2;
            $employee->update();
        }
        return back()->with(['success'=>'Employee Educational Information Updated Successfully']);
    }

    public function employee_directory_employment_update(Employee $employee, Request $request)
    {
        if($employee->employment_history->count() > 0)
        {
            $employee->employment_history()->delete();
        }
        foreach ($request->name as $key => $value)
        {
            $employment = new EmployeeEmployementHistory();
            $employment->employee_id = $employee->id;
            $employment->name = $request->name[$key];
            $employment->designation = $request->position[$key];
            $employment->from = $request->formatted_from[$key];
            $employment->to = $request->formatted_to[$key];
            $employment->reason = $request->reason[$key];
            $employment->save();
        }

        if(count($request->name) > 0)
        {
            $employee->request_status_id = 2;
            $employee->update();
        }
        return back()->with(['success'=>'Employee Employment History Updated Successfully']);
    }

    public function employee_directory_bank_update(Employee $employee, Request $request)
    {
        if($employee->bank_info()->exists())
        {
            $bank_info = $employee->bank_info->first();
        }
        else{
            $bank_info = new EmployeeBankInformation();
        }

        $bank_info->employee_id = $employee->id;
        $bank_info->account_title = $request->account_title;
        $bank_info->branch_code = $request->branch_code;
        $bank_info->account_no = $request->account_number;
        $bank_info->bank_id = $request->bank_name;
        $bank_info->branch_name = $request->branch_name;
        $bank_info->iban = $request->iban_number;
        $bank_info->save();

        $employee->request_status_id = 2;
        $employee->update();


        return back()->with(['success'=>'Employee Bank Information Updated Successfully']);
    }

    public function employee_directory_reference_update(Employee $employee, Request $request)
    {
        if($employee->reference()->exists())
        {
            $reference = $employee->reference->first();
        }
        else{
            $reference = new EmployeeReference();
        }

        $reference->employee_id = $employee->id;
        $reference->name = $request->name;
        $reference->occupation = $request->occupation;
        $reference->relationship = $request->relationship;
        $reference->years = $request->years;
        $reference->phone_number = $request->phone;
        $reference->email = $request->email;
        $reference->save();

        $employee->request_status_id = 2;
        $employee->update();

        return back()->with(['success'=>'Employee Reference Updated Successfully']);
    }

    public function employee_directory_attachments_update(Employee $employee, Request $request)
    {
        $request->validate([
            'cv'=>'mimes:pdf,png,jpeg,jpg',
            'academic_credentials'=>'mimes:pdf,png,jpeg,jpg',
            'cnic'=>'mimes:pdf,png,jpeg,jpg',
            'photo'=>'mimes:pdf,png,jpeg,jpg',
            'experience_certificates'=>'mimes:pdf,png,jpeg,jpg',
            'pay_slip'=>'mimes:pdf,png,jpeg,jpg',
            'nikkah_nama'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_spouse'=>'mimes:pdf,png,jpeg,jpg',
            'bform'=>'mimes:pdf,png,jpeg,jpg',
            'cnic_nominee'=>'mimes:pdf,png,jpeg,jpg',
            'utility_bill'=>'mimes:pdf,png,jpeg,jpg',
            'affidavit'=>'mimes:pdf,png,jpeg,jpg',
            'cheque'=>'mimes:pdf,png,jpeg,jpg',
        ]);
        if($employee->attachments()->exists())
        {
            $attachments = $employee->attachments->first();
        }
        else{
            $attachments = new EmployeeAttachment();
            $attachments->employee_id = $employee->id;
        }

        $date = Carbon::now()->format('Y_m_d');

        if ($request->hasFile('cv')) {
            if($attachments->cv != NULL) {
                Storage::disk('public')->delete($attachments->cv);
            }

            $file = $request->file('cv');
            $filename = 'cv_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->cv = $directory.'/'.$filename;
        }

        if ($request->hasFile('cnic')) {
            if($attachments->cnic != NULL) {
                Storage::disk('public')->delete($attachments->cnic);
            }

            $file = $request->file('cnic');
            $filename = 'cnic_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->cnic = $directory.'/'.$filename;
        }

        if ($request->hasFile('photo')) {
            if($attachments->photo != NULL) {
                Storage::disk('public')->delete($attachments->cnic);
            }

            $file = $request->file('photo');
            $filename = 'photo_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->photo = $directory.'/'.$filename;
        }

        if ($request->hasFile('academic_credentials')) {
            if($attachments->academic != NULL) {
                Storage::disk('public')->delete($attachments->academic);
            }

            $file = $request->file('academic_credentials');
            $filename = 'academic_credentials_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->academic = $directory.'/'.$filename;
        }

        if ($request->hasFile('experience_certificates')) {
            if($attachments->experience != NULL) {
                Storage::disk('public')->delete($attachments->experience);
            }

            $file = $request->file('experience_certificates');
            $filename = 'experience_certificates_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->experience = $directory.'/'.$filename;
        }

        if ($request->hasFile('pay_slip')) {
            if($attachments->last_pay_slip != NULL) {
                Storage::disk('public')->delete($attachments->last_pay_slip);
            }

            $file = $request->file('pay_slip');
            $filename = 'pay_slip_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->last_pay_slip = $directory.'/'.$filename;
        }

        if ($request->hasFile('nikkah_nama')) {
            if($attachments->nikkah_nama != NULL) {
                Storage::disk('public')->delete($attachments->nikkah_nama);
            }

            $file = $request->file('nikkah_nama');
            $filename = 'nikkah_nama_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->nikkah_nama = $directory.'/'.$filename;
        }

        if ($request->hasFile('cnic_spouse')) {
            if($attachments->cnic_spouse != NULL) {
                Storage::disk('public')->delete($attachments->cnic_spouse);
            }

            $file = $request->file('cnic_spouse');
            $filename = 'cnic_spouse_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->cnic_spouse = $directory.'/'.$filename;
        }

        if ($request->hasFile('bform')) {
            if($attachments->child_b_form != NULL) {
                Storage::disk('public')->delete($attachments->child_b_form);
            }

            $file = $request->file('bform');
            $filename = 'child_b_form_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->child_b_form = $directory.'/'.$filename;
        }

        if ($request->hasFile('cnic_nominee')) {
            if($attachments->cnic_nominee != NULL) {
                Storage::disk('public')->delete($attachments->cnic_nominee);
            }

            $file = $request->file('cnic_nominee');
            $filename = 'cnic_nominee_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->cnic_nominee = $directory.'/'.$filename;
        }

        if ($request->hasFile('utility_bill')) {
            if($attachments->utility_bill != NULL) {
                Storage::disk('public')->delete($attachments->utility_bill);
            }

            $file = $request->file('utility_bill');
            $filename = 'utility_bill_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->utility_bill = $directory.'/'.$filename;
        }

        if ($request->hasFile('affidavit')) {
            if($attachments->affidavit != NULL) {
                Storage::disk('public')->delete($attachments->affidavit);
            }

            $file = $request->file('affidavit');
            $filename = 'affidavit_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->affidavit = $directory.'/'.$filename;
        }

        if ($request->hasFile('cheque')) {
            if($attachments->cheque != NULL) {
                Storage::disk('public')->delete($attachments->cheque);
            }

            $file = $request->file('cheque');
            $filename = 'cheque_' . $date . '.'.$file->extension();
            $directory = 'employee_directory/employee_'. $employee->id .'';
            Storage::disk('public')->putFileAs($directory, $file, $filename);
            $attachments->cheque = $directory.'/'.$filename;
        }

        $employee->request_status_id = 2;
        $employee->update();
        
        $attachments->save();

        return back()->with(['success'=>'Employee Attachments Updated Successfully']);
    }
    public function reporting_location_index(){
        $cities = City::where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.human_resource.reporting_location')->with(['cities' => $cities]);
    }

    public function reporting_location_list(Request $request){
        $location = ReportingLocation::join('cities as c', 'reporting_locations.city_id', '=', 'c.id')
            ->select(['reporting_locations.id as id', 'reporting_locations.name as location_name', 'c.name as city', 'c.id as city_id', 'reporting_locations.lat', 'reporting_locations.long', 'reporting_locations.status', 'reporting_locations.address', 'reporting_locations.radius']);

        if (session('role_id') != 1) {
            $location = $location->whereIn('c.hub_id', session('hubs'));
        }

        return Datatables::of($location)
            ->addColumn('map', function ($data) {
               return '<button type="button" class="btn btn-primary btn-sm"><a class="white" href="http://www.google.com/maps/place/' . $data->lat . ',' . $data->long . '" target="_blank"><i class="la la-map-marker align-middle"></i></a></button>';
            })
            ->editColumn('status', function ($data) {
                if($data->status == 0){
                    return 'In-Active';
                }
                else{
                    return 'Active';
                }
            })
            ->addColumn("action", function ($data) {
                if(session('role_id') == 1 || in_array(468, session('permissions')) || in_array(468, session('permissions'))){
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if (session('role_id') == 1 || in_array(468, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                    if (session('role_id') == 1 || in_array(469, session('permissions'))) {
                        if($data->status == 0) {
                            $dropdown .= '<button type="button" class="dropdown-item enable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                        }
                        else{
                            $dropdown .= '<button type="button" class="dropdown-item disable" data-target-id=' . $data->id . '><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                        }
                    }
                    $dropdown .= '
                </div>
              </div>
            ';
                    return $dropdown;
                }
                else{
                    return '';
                }
            })
            ->make(true);
    }

    public function reporting_location_status(Request $request){
        $id = $request->id;
        $location = ReportingLocation::find($id);
        if($request->status == 0){
            $status = 'Disabled';
        }
        else{
            $status = 'Enabled';
        }
        $location->status = $request->status;
        $location->save();
        return response()->json(['status' => 1, 'success' => 'Location '. $status .' successfully!']);
    }

    public function reporting_location_add(Request $request){
        $location = new ReportingLocation();
        $location->city_id = $request->city;
        $location->name = $request->name;
        $location->address = $request->address;
        $location->lat = $request->lat;
        $location->long = $request->long;
        $location->radius = $request->radius;
        $location->save();
        return redirect()->back()->with('success', 'Location Added Successfully!');
    }

    public function reporting_location_edit(Request $request){
        $location = ReportingLocation::find($request->location_id);
        $location->city_id = $request->city;
        $location->name = $request->name;
        $location->address = $request->address;
        $location->lat = $request->lat;
        $location->long = $request->long;
        $location->radius = $request->radius;
        $location->save();
        return redirect()->back()->with('success', 'Location Updated Successfully!');
    }
}
