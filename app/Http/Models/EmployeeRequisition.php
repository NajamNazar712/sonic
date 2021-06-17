<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRequisition extends Model
{
    public function status() {
        return $this->belongsTo('App\Http\Models\EmployeeRequisitionStatus', 'status_id');
    }
    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }
    public function department() {
        return $this->belongsTo('App\Http\Models\Admin\AdminDepartment');
    }
    public function designation() {
        return $this->belongsTo('App\Http\Models\HR\EmployeeDesignation');
    }
    public function manager() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'department_head_id', 'id');
    }
    public function hub() {
        return $this->belongsTo('App\Http\Models\City','hub_id','id');
    }
    public function replacement() {
        return $this->hasMany('App\Http\Models\EmployeeRequisitionReplacement','er_id','id');
    }


}
