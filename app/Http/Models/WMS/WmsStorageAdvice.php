<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsStorageAdvice extends Model
{
    public function stocker(){
        return $this->belongsTo('App\Http\Models\Admin\Admin','stocker_id','id');
    }

    public function items() {
        return $this->hasMany('App\Http\Models\WMS\WmsStorageProduct', 'storage_advice_id', 'id');
    }
}
