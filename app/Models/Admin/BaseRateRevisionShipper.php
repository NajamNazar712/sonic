<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BaseRateRevisionShipper extends Model
{
    protected $guarded = [];

    //relationships
    public function baseRateRevision()
    {
        return $this->belongsTo('App\Models\Admin\BaseRateRevision','base_rate_revision_id');
    }

    public function shipper()
    {
        return $this->belongsTo('App\User');
    }
}
