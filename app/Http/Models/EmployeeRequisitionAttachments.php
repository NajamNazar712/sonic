<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRequisitionAttachments extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id','id');
    }
}
