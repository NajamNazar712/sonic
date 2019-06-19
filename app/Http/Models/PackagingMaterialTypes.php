<?php

namespace App\Http\models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialTypes extends Model
{
    public function sizes() {
        return $this->hasMany('App\Http\Models\PackagingMaterialTypeSizes', 'type_id', 'id');
    }
}
