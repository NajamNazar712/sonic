<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderRemark extends Model
{
    protected $table = 'rider_remarks';
    
    public function remark_statuses()
    {
        return $this->hasMany('App\Http\Models\Rider\RiderRemarkStatus','rider_remarks_status_id','id');
    }

    public function rider_id()
    {
        return $this->belongsTo('App\Http\Models\Rider', 'rider_id', 'id');
    }
}
