<?php

namespace App\Http\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }
}
