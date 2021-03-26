<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeAttachment;
use App\Http\Models\HR\EmployeeBankInformation;
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
use App\Http\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Rider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

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

        // $riders = Rider::where('status', 1)->get();
        //         $admins = Admin::where('status', 1)->get();
        $roles = ['Admin', 'Rider'];
        $roles = collect($roles);
        return view('admin.human_resource.allusers')->with(['roles' => $roles]);
    }
    public function all_user_ajax()
    {


        $assigned_hubs = session('hubs');
        if (session('role_id') != 1) {
            if (count($assigned_hubs) > 0) {
                // $riders = Rider::where('status',1)->where('rider_type_id',1)->whereIn('city_id', $assigned_hubs)->get();
                $riders = Rider::join('cities', 'riders.city_id', '=', 'cities.id')
                    ->join('cities as c', 'cities.hub_id', '=', 'c.id')
                    ->select('c.name as hub', 'riders.id', 'riders.name', 'riders.trax_id', 'riders.phone', 'riders.cnic', 'riders.created_at as created_at')
                    ->where('rider_type_id', 1)
                    ->where('status', 1)
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
        return view('admin.human_resource.employee_directory.index');
    }

    public function employee_directory_list(Request $request){
        $employees = Employee::join('cities', 'employees.city_id', '=', 'cities.id')
            ->join('employee_genders as eg','eg.id','=','employees.employee_gender_id')
            ->join('employee_types as et','et.id','=','employees.employee_type_id')
            ->join('employee_request_statuses as ers','ers.id','=','employees.request_status_id')
            ->join('employee_statuses as es','es.id','=','employees.status_id')
            ->select(['employees.id as employee_id', 'employees.name as employee_name', 'cities.name as city' ,'employees.trax_id', 'eg.name as gender', 'employees.cnic', 'employees.phone_number', 'et.name as employee_type','ers.name as request_status', 'es.name as status', 'employees.created_at as requested_at']);

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
                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item" data-target-id="' . $result->employee_id . '" data-toggle="modal" data-target="#BankInfoModal"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Bank Info</div></button>';

                $route = route("admin.human_resourse.employee_directory.edit",$result->employee_id);
                $dropdown .= '<a href="' . $route . '" class="dropdown-item" ><i class="ft-edit"></i> Update Details</a>';

                $dropdown .= '
                </div>
              </div>
            ';

                return $dropdown;
            })
            ->make(true);
    }

    public function employee_directory_store(Request $request){
        return $request;
    }

    public function employee_directory_edit(Employee $employee)
    {
        $religions = EmployeeReligion::all();
        $nationalities = EmployeeNationality::all();
        $domiciles = EmployeeDomicile::all();
        $maritial_statuses = EmployeeMaritalStatus::all();
        $designations = EmployeeDesignation::all();
        $hubs = City::where('hub',1)->get();
        $zones = Zone::all();
        $departments = AdminDepartment::all();
        $relationships = EmployeeRelationship::all();
        $banks = BanksList::where('status',1)->get();
        $medical_infos = $employee->medical_infos;
        $bank_info = $employee->bank_info;
        $reference = $employee->reference;
        $educations = $employee->education_infos;
        $employments = $employee->employment_history;
        $attachments = $employee->attachments;
        return view('admin.human_resource.employee_directory.update',compact('employments','attachments','educations','reference','bank_info','banks','medical_infos','employee','religions','nationalities','domiciles','maritial_statuses','designations','hubs','departments','zones','relationships'));
    }

    public function employee_directory_profile_update (Employee $employee, Request $request)
    {
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
        $employee->city_id = $request->hub;
        $employee->department_id = $request->department;
        $employee->zone_id = $request->zone;
        $employee->official_email = $request->official_email;
        $employee->official_phone_number = $request->official_number;
        $employee->sonic_id = $request->sonic_id;
        $employee->pin = $request->bolt_pin;
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


        return back()->with(['success'=>'Employee Reference Updated Successfully']);
    }

    public function employee_directory_attachments_update(Employee $employee, Request $request)
    {
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


        $attachments->save();

        return back()->with(['success'=>'Employee Attachments Updated Successfully']);
    }
}
