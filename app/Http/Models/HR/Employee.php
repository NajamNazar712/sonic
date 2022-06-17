<?php

namespace App\Http\Models\HR;

use App\Http\Models\Admin\Admin;
use App\Http\Models\EmployeeShift;
use App\Http\Models\Rider;
use App\Http\Models\Rider\RiderRequest;
use Illuminate\Database\Eloquent\Model;

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

}
