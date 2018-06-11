<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $fillable = [
        'city_id','name','phone','cnic','address','route_id','rider_category_id','status'
    ];
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
    public function route(){
        return $this->belongsTo('App\Http\Models\Route');
    }
    public function rider_category(){
        return $this->belongsTo('App\Http\Models\RiderCategory');
    }
}
