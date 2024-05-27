<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BaseRateRevision extends Model
{
    //
    protected $guarded = [];

    protected $dates = ['approval1_at','approval2_at'];


    //Relationships
    public function shippersWithRateChange()
    {
        return $this->hasMany('App\Models\Admin\BaseRateRevisionShipper','base_rate_revision_id');
    }

    public function rateType()
    {
        return $this->belongsTo('App\Models\Admin\BaseRateType','rate_type_id');
    }

    public function addedByAdmin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin','added_by_admin_id');
    }

    public function approved1ByAdmin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin','approval1_by_admin_id');
    }

    public function approved2ByAdmin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin','approval2_by_admin_id');
    }

    public function approval1Status()
    {
        return $this->belongsTo('App\Models\Admin\BaseRateRevisionApprovalStatus','approval1_status');
    }

    public function approval2Status()
    {
        return $this->belongsTo('App\Models\Admin\BaseRateRevisionApprovalStatus','approval2_status');
    }
}
