<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeConvertHistory extends Model
{
    protected $fillable = ['rider_id','converted_by'];
}
