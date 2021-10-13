<?php

namespace App\Http\Models\HR;

use Illuminate\Database\Eloquent\Model;

class EmployeeDesignation extends Model
{
    public function hubs()
    {
        return $this->hasMany(EmployeeDesignationHub::class,'designation_id');
    }
}
