<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateDeliveryTypeStatus extends Model
{
    public function delivery_type(){
        return $this->belongsTo('App\Http\Models\DeliveryType','delivery_type_id','id');
    }
}
