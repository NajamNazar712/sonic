<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BookingDestinationMapping extends Model
{
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }

    public function mappings(){
        return $this->hasMany('App\Http\Models\Admin\BookingDestinationMappingKeyword','mapping_id');
    }
}
