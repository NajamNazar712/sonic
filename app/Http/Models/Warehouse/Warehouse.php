<?php

namespace App\Http\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    public function associated_hubs() {
        return $this->hasMany('App\Http\Models\Warehouse\WarehouseFulfilmentHubs');
    }
    public function city(){
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
