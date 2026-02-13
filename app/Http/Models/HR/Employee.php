<?php

namespace App\Http\Models\HR;

use App\Http\Models\Admin\Admin;
use App\Http\Models\EmployeeShift;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RiderRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\EmployeeLog;


class Employee extends Model
{
    protected $fillable = ['first_inactive','is_line_manager','line_manager_id'];

    public function medical_infos() {
        return $this->HasMany('App\Http\Models\HR\EmployeeMedicalInformation');
    }

    public function education_infos() {
        return $this->HasMany('App\Http\Models\HR\EmployeeEducationalBackground');
    }

    public function employment_history() {
        return $this->HasMany('App\Http\Models\HR\EmployeeEmployementHistory');
    }

    public function bank_info() {
        return $this->HasOne('App\Http\Models\HR\EmployeeBankInformation');
    }

    public function reference() {
        return $this->HasOne('App\Http\Models\HR\EmployeeReference');
    }

    public function attachments() {
        return $this->HasOne('App\Http\Models\HR\EmployeeAttachment');
    }

    public function designation() {
        return $this->belongsTo('App\Http\Models\HR\EmployeeDesignation','designation_id','id');
    }

    public function department() {
        return $this->belongsTo('App\Http\Models\Admin\AdminDepartment');
    }

    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }

    public function rider_request()
    {
        return $this->belongsTo(RiderRequest::class,'rider_request_id');
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class,'trax_id','trax_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class,'trax_id','trax_id');
    }
    public function employee_type()
    {
        return $this->belongsTo(EmployeeType::class,'employee_type_id','id');
    }
    public function shift()
    {
        return $this->belongsTo(EmployeeShift::class,'shift_id','id');
    }
    public function employee_nature()
    {
        return $this->belongsTo(EmployeeNature::class,'employee_nature_id','id');
    }
    public function replacement_employee()
    {
        return $this->belongsTo(Employee::class,'replacement_employee_id','id');
    }
    public function line_manager()
    {
        return $this->belongsTo(Employee::class,'line_manager_id','id');
    }

    public function area()
    {
        return $this->belongsTo('App\Http\Models\CityArea', 'area_id', 'id');
    }

    public function employee_status()
    {
        return $this->belongsTo('App\Http\Models\HR\EmployeeStatus', 'status_id', 'id');
    }


    protected static function booted()
    {
        static::updated(function (Employee $employee) {

            $trackFields = [
                'name','phone_number','official_phone_number','guardian_name','mother_name',
                'religion_id','nationality_id','domicile_id','employee_gender_id','marital_status_id',
                'blood_group','personal_email','official_email','address','emergency_contact',
                'cnic','cnic_issue_date','cnic_expiry_date','education_id',
                'designation_id','department_id',
                'city_id','zone_id','place_of_birth','date_of_birth',
                'status_id','shift_id',
                'rider_sub_category','rider_main_category',
                'joining_date','emergency_contact_person',
                'replacement_employee_id','replacement_last_working_day',
                'is_line_manager','line_manager_id',
                'fuel','employee_nature_id','sub_department','confirmation_status',
                'area_id',
                 'rider_type_id',
            ];

            // user friendly labels (keys shown to users)
            $fieldLabels = [
                'name' => 'Employee Name',
                'phone_number' => 'Personal Number',
                'official_phone_number' => 'Official Number',
                'guardian_name' => 'Guardian Name',
                'mother_name' => 'Mother Name',
                'religion_id' => 'Religion',
                'nationality_id' => 'Nationality',
                'domicile_id' => 'Domicile',
                'employee_gender_id' => 'Gender',
                'marital_status_id' => 'Marital Status',
                'blood_group' => 'Blood Group',
                'personal_email' => 'Personal Email',
                'official_email' => 'Official Email',
                'address' => 'Address',
                'emergency_contact' => 'Emergency Contact',
                'emergency_contact_person' => 'Emergency Contact Person',
                'cnic' => 'CNIC',
                'cnic_issue_date' => 'CNIC Issue Date',
                'cnic_expiry_date' => 'CNIC Expiry Date',
                'education_id' => 'Education',
                'designation_id' => 'Designation',
                'department_id' => 'Department',
                'city_id' => 'City',
                'zone_id' => 'Zone',
                'area_id' => 'Area',
                'place_of_birth' => 'Place of Birth',
                'date_of_birth' => 'Date of Birth',
                'status_id' => 'Employment Status',
                'shift_id' => 'Shift',
                'rider_main_category' => 'Rider Main Category',
                'rider_sub_category' => 'Rider Sub Category',
                'joining_date' => 'Joining Date',
                'replacement_employee_id' => 'Replacement Employee',
                'replacement_last_working_day' => 'Replacement Last Working Day',
                'is_line_manager' => 'Is Line Manager',
                'line_manager_id' => 'Line Manager',
                'employee_nature_id' => 'Employee Nature',
                'confirmation_status' => 'Confirmation Status',
                'fuel' => 'Fuel',
                'sub_department' => 'Sub Department',
                'rider_type_id' => 'Rider Type',
            ];

            // lookup for ID fields -> show name instead of id
            $lookup = [
                'religion_id' => ['table' => 'employee_religions', 'key' => 'id', 'label' => 'name'],
                'nationality_id' => ['table' => 'employee_nationalities', 'key' => 'id', 'label' => 'name'],
                'domicile_id' => ['table' => 'employee_domiciles', 'key' => 'id', 'label' => 'name'],
                'employee_gender_id' => ['table' => 'employee_genders', 'key' => 'id', 'label' => 'name'],
                'marital_status_id' => ['table' => 'employee_marital_statuses', 'key' => 'id', 'label' => 'name'],
                'education_id' => ['table' => 'education_lists', 'key' => 'id', 'label' => 'name'],
                'designation_id' => ['table' => 'employee_designations', 'key' => 'id', 'label' => 'name'],
                'department_id' => ['table' => 'admin_departments', 'key' => 'id', 'label' => 'name'],
                'city_id' => ['table' => 'cities', 'key' => 'id', 'label' => 'name'],
                'zone_id' => ['table' => 'zones', 'key' => 'id', 'label' => 'name'],
                'area_id' => ['table' => 'city_areas', 'key' => 'id', 'label' => 'name'],
                'status_id' => ['table' => 'employee_statuses', 'key' => 'id', 'label' => 'name'],
                'shift_id' => ['table' => 'employee_shifts', 'key' => 'id', 'label' => 'name'],
                'line_manager_id' => ['table' => 'employees', 'key' => 'id', 'label' => 'name'],
                'replacement_employee_id' => ['table' => 'employees', 'key' => 'id', 'label' => 'name'],
                'rider_main_category' => ['table' => 'rider_main_categories', 'key' => 'id', 'label' => 'name'],
                'rider_sub_category' => ['table' => 'rider_categories', 'key' => 'id', 'label' => 'name'],
                'blood_group' => ['table' => 'employee_blood_groups' , 'key' => 'id' , 'label' => 'name'],
                'employee_nature_id' => ['table' => 'employee_natures', 'key' => 'id', 'label' => 'name'],
                'rider_type_id' => ['table' => 'rider_types', 'key' => 'id' , 'label' => 'name']

            ];

            $labelOf = function ($field, $value) use ($lookup) {
                if ($value === null || $value === '') {
                    return null;
                }

                if (!isset($lookup[$field])) {
                    return $value;
                }

                $row = $lookup[$field];
                $label = DB::table($row['table'])->where($row['key'], $value)->value($row['label']);
                return $label !== null ? $label : $value;
            };

            //  only changed fields in this update
            $dirty = $employee->getDirty();
            if (empty($dirty)) {
                return;
            }

            $changes = [];

            foreach ($trackFields as $field) {
                if (!array_key_exists($field, $dirty)) {
                    continue;
                }

                $oldValue = $employee->getOriginal($field);
                $newValue = $employee->{$field};

                $key = $fieldLabels[$field] ?? $field;

                //  Human readable for some special fields
                if ($field === 'is_line_manager') {
                    $oldHuman = ($oldValue == 1 ? 'Yes' : 'No');
                    $newHuman = ($newValue == 1 ? 'Yes' : 'No');
                } else {
                    $oldHuman = $labelOf($field, $oldValue);
                    $newHuman = $labelOf($field, $newValue);
                }

                $oldHuman = ($oldHuman === null || $oldHuman === '') ? '-' : $oldHuman;
                $newHuman = ($newHuman === null || $newHuman === '') ? '-' : $newHuman;

                $changes[$key] = [
                    'old' => $oldHuman,
                    'new' => $newHuman,
                ];
            }

            if (empty($changes)) {
                return;
            }

            $route = request()->route();
            $routeName = $route ? $route->getName() : '';

            if (strpos($routeName, 'employee_directory') !== false) {
                $screen = 'Employee Directory';
            } elseif (strpos($routeName, 'riders') !== false) {
                $screen = 'Rider';
            } else {
                $screen = 'Employee Directory';
            }

            // safe changed_by for cron/job
            $changedBy = Auth::id() ?? ($employee->updated_by ?? 346);
            try {
                $log = new EmployeeLog();
                $log->employee_id = $employee->id;
                $log->updated_by = $changedBy;
                $log->log_data = json_encode($changes);
                $log->screen = $screen;
                $log->save();
            } catch (\Throwable $e) {
                // do not break update if logging fails
            }


        });
    }

    

}
