<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderRemarksResponse extends Model
{
    protected $table = 'rider_remarks_response';

    public function remark()
    {
        return $this->belongsTo('App\Http\Models\Rider\RiderRemark', 'id', 'rider_remarks_id');
    }
}
