<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BusinessProjectionHub extends Model
{
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id');
    }
}
