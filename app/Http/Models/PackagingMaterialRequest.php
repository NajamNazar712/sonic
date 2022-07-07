<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialRequest extends Model
{
    protected $fillable = [
        'user_id','city_id','address','poc','reference_id','phone','amount','status_id','packaging_payment_mode_id','tracking_number','requested_by'
    ];
    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }
    public function items() {
        return $this->hasMany('App\Http\Models\PackagingMaterialRequestDetail');
    }

}
