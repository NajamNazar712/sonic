<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipperPackagingMaterailType extends Model
{
    protected $table = 'shipper_packaging_materails';
    protected $fillable = [
        'shipper_id','type_id'
    ];



    public function packaging_material() {
       return $this->belongsTo('App\Http\Models\PackagingMaterialTypes', 'type_id','id');
    }
}
