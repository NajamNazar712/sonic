<?php

namespace App\Http\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeePenalty extends Model
{
    protected $table = 'employee_penalties';

    protected $fillable = ['employee_id','attendence_id','status','date','no_of_late','salary_deduction','leave_deduction','deduction_count','is_current_record'];
}
