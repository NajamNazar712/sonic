<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class BookingDestinationMappingKeyword extends Model
{
    public function delivery_location_mapping(){
        return $this->belongsTo('App\Http\Models\Admin\BookingDestinationMapping', 'mapping_id', 'id');
    }
}
