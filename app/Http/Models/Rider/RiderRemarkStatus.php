<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderRemarkStatus extends Model
{
    protected $table = 'rider_remark_statuses';
    
    public function rider_remarks()
    {
        return $this->belongsTo('App\Http\Models\Rider\RiderRemark', 'id','rider_remarks_status_id');
    }
}
