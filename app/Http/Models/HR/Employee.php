<?php

namespace App\Http\Models\HR;

use App\Http\Models\Rider\RiderRequest;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['first_inactive'];

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

}
