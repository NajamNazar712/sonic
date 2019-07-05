<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialRequestDetail extends Model
{
    public function types() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes', 'type_id', 'id');
    }

    public function sizes() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes', 'type_size_id', 'id');
    }
}
