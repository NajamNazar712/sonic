<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingMaterialRequest extends Model
{
    protected $fillable = [
        'user_id','city_id','small_flyers','medium_flyers','large_flyers','boxes','address','poc','phone','amount','packaging_payment_mode_id'
    ];
    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }

}
