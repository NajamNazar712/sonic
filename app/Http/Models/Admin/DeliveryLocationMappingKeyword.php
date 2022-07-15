<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryLocationMappingKeyword extends Model
{
    public function delivery_location_mapping(){
        return $this->belongsTo('App\Http\Models\Admin\DeliveryLocationMapping', 'mapping_id', 'id');
    }
    
}
