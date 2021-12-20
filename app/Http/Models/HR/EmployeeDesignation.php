<?php

namespace App\Http\Models\HR;

use App\Http\Models\Admin\AdminDepartment;
use Illuminate\Database\Eloquent\Model;

class EmployeeDesignation extends Model
{
    public function hubs()
    {
        return $this->hasMany(EmployeeDesignationHub::class,'designation_id');
    }

    public function department()
    {
        return $this->belongsTo(AdminDepartment::class,'department_id');
    }
}
