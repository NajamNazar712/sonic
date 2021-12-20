<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name','city_code','hub','hub_id','zone_id','pickup','status','gc_area','attempt_tat','location_latitude','location_longitude','address','business_category_id','hub_location_latitude','hub_location_longitude'
    ];
    public function hub(){
       return $this->belongsTo(self::class, 'hub_id');
    }
    public function hub_city(){
       return $this->belongsTo(self::class, 'hub_id');
    }
    public function hub_cities()
    {
        return $this->hasMany(self::class,'hub_id','id')->where('id','!=',$this->id);
    }
    public function hub_cities_including_self()
    {
        return $this->hasMany(self::class,'hub_id','id');
    }
    public function routes(){
        return $this->hasMany('App\Http\Models\Route');
    }
    public function riders(){
        return $this->hasMany('App\Http\Models\Rider')->withTrashed();
    }
    public function admins(){
        return $this->hasMany('App\Http\Models\Admin\Admin');
    }
    public function disputes(){
        return $this->hasMany('App\Http\Models\Dispute');
    }

    public function deliveries() {
        return $this->hasMany('App\Http\Models\CityDelivery');
    }

    public function zone() {
       return $this->belongsTo('App\Http\Models\Zone', 'zone_id');
    }
}
