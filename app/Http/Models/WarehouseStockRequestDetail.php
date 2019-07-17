<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStockRequestDetail extends Model
{
    public function packaging_type() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes','type_id', 'id');
    }
    public function packaging_size() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes','size_id', 'id');
    }
}
