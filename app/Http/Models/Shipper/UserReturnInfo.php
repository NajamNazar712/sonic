<?php

namespace App\Http\Models\Shipper;

use Illuminate\Database\Eloquent\Model;

class UserReturnInfo extends Model
{
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
}
