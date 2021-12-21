<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeConvertHistory extends Model
{
    protected $fillable = ['employee_id','converted_to','converted_by'];
}
