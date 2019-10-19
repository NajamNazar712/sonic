<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialTypes extends Model
{
    protected $table = 'packaging_material_types';
    public function sizes() {
        return $this->hasMany('App\Http\Models\PackagingMaterialTypeSizes', 'type_id', 'id');
    }
}
