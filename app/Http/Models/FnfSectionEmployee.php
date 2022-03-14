<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FnfSectionEmployee extends Model
{
    public function employee() {
        return $this->belongsTo('App\Http\Models\HR\Employee','employee_id','id');
    }

    public function manager(){
        return $this->hasOne('App\FnfSectionReportingManager','fnf_id','id');
    }

    public function customer_experience(){
        return $this->hasOne('App\FnfSectionCustomerExperience','fnf_id','id');
    }

    public function administration(){
        return $this->hasOne('App\FnfSectionAdministration','fnf_id','id');
    }

    public function it_support(){
        return $this->hasOne('App\FnfSectionItSupport','fnf_id','id');
    }
    public function finance(){
        return $this->hasOne('App\FnfSectionFinance','fnf_id','id');
    }
    public function hod_approval(){
        return $this->hasOne('App\FnfSectionHod','fnf_id','id')->orderBy('created_at','desc');
    }
    public function reporting_manager(){
        return $this->belongsTo('App\Http\Models\Admin\Employee','line_manager','id');
    }
    public function department_head(){
        return $this->belongsTo('App\Http\Models\Admin\Employee','hod','id');
    }
    public function requested_by(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','created_by','id');
    }


}
