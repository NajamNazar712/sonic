<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingCharge extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','sm_flyer','md_flyer','lg_flyer','box_flyer'
    ];

    public function packaging_type() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypes','type_id', 'id');
    }
    public function packaging_size() {
        return $this->belongsTo('App\Http\Models\PackagingMaterialTypeSizes','size_id', 'id');
    }
}
