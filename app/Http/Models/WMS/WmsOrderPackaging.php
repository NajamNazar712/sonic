<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsOrderPackaging extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment');
    }

    public function type(){
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes');
    }

    public function size(){
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes');
    }
}
