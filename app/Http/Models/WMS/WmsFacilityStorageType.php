<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsFacilityStorageType extends Model
{
    public function warehouse_type(){
        return $this->belongsTo('App\Http\Models\WMS\WmsStorageType', 'warehouse_type_id', 'id');
    }

}
