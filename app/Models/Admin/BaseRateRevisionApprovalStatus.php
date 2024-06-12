<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BaseRateRevisionApprovalStatus extends Model
{
    //Relationships
    public function baseRateRevisionsAsApproval1()
    {
        return $this->hasMany('App\Models\Admin\BaseRateRevision','approval1_status');
    }

    public function baseRateRevisionsAsApproval2()
    {
        return $this->hasMany('App\Models\Admin\BaseRateRevision','approval2_status');
    }

}
