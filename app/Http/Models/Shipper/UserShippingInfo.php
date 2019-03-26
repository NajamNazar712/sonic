<?php

namespace App\Http\Models\Shipper;

use Illuminate\Database\Eloquent\Model;

class UserShippingInfo extends Model
{
    protected $fillable = [
        'user_id', 'pickup_address','poc','phone','email','city_id','hidden'
    ];

    public function user(){
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
}
