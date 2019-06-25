<?php

namespace App\Http\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    public function associated_hubs() {
        return $this->hasMany('App\Http\Models\Warehouse\WarehouseFulfilmentHubs');
    }
}
