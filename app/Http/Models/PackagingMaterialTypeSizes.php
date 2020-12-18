<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialTypeSizes extends Model
{
    protected $table = 'packaging_material_type_sizes';

    public function type() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes', 'type_id', 'id');
    }
}
