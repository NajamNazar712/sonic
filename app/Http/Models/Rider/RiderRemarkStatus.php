<?php

namespace App\Http\Models\Rider;

use App\Http\Models\Admin\RiderRemark;
use Illuminate\Database\Eloquent\Model;

class RiderRemarkStatus extends Model
{
    public function rider_remark()
    {
        return $this->belongsTo('App\Http\Models\RiderRemark', 'rider_remarks_status_id', 'id');
    }
}
