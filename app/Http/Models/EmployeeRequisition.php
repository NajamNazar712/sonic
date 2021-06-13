<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRequisition extends Model
{
    public function status() {
        return $this->belongsTo('App\Http\Models\EmployeeRequisitionStatus', 'status_id');
    }
}
