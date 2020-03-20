<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsShipmentProduct extends Model
{
    public function products(){
        return $this->belongsTo('App\Http\Models\WMS\WmsProduct', 'product_id', 'id');
    }
    
}
