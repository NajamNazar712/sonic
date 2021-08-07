<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialCart extends Model
{
    public function type() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes', 'type_id', 'id');
    }

    public function size() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes', 'size_id', 'id');
    }

}
