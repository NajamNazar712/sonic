<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BaseRateType extends Model
{
    //relationships
    public function baseRateRevisions()
    {
        return $this->hasMany('App\Models\Admin\BaseRateRevision','rate_type_id');
    }
}
