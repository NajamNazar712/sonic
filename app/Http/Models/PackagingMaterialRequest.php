<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialRequest extends Model
{
    protected $fillable = [
        'user_id','city_id','address','poc','phone','amount','status_id','packaging_payment_mode_id','tracking_number'
    ];
    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }
    public function items() {
        return $this->hasMany('App\Http\Models\PackagingMaterialRequestDetail');
    }

}
