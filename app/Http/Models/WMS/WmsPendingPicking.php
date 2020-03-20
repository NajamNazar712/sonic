<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsPendingPicking extends Model
{
    public function product() {
        return $this->belongsTo('App\Http\Models\WMS\WmsProduct', 'product_id', 'id');
    }
    public function leaf(){
        return $this->belongsTo('App\Http\Models\WMS\WmsFacilityStorageType', 'leaf_id', 'id');
    }

}
