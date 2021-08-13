<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FnfSectionEmployee extends Model
{
    public function employee() {
        return $this->belongsTo('App\Http\Models\HR\Employee','employee_id','id');
    }
}
