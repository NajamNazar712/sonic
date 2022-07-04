<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryLocationMapping extends Model
{
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }

    public function delivery_location_mapping_keywords(){
        return $this->hasMany('App\Http\Models\Admin\DeliveryLocationMappingKeyword');
    }
}
