<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialRequestDetail extends Model
{
    protected $fillable = [
        'packaging_material_request_id','type_id','type_size_id','quantity'
    ];
    public function packaging_type() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes','type_id', 'id');
    }
    public function packaging_type_size() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes','type_size_id', 'id');
    }
}
