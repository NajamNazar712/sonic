<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsPendingShipmentPackaging extends Model
{
    public function type(){
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes', 'type_id', 'id');
    }

    public function size(){
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes', 'size_id', 'id');
    }
}
