<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderRemark extends Model
{
    public function remark_statuses()
    {
        return $this->hasMany('App\Http\Models\RiderRemark', 'id');
    }

    public function rider_id()
    {
        return $this->belongsTo('App\Http\Models\Rider', 'rider_id', 'id');
    }
}
