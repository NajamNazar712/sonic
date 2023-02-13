<?php

namespace App\Http\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeLate extends Model
{
    protected $table = 'employee_lates';
    protected $fillable = ['attendence_id','attendence_date','date'];
}
