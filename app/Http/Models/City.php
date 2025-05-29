<?php

namespace App\Http\Models;

use App\Models\CityStatusChangeLog;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name','city_code','hub','hub_id','zone_id','pickup','status','gc_area','attempt_tat','location_latitude','location_longitude','address','business_category_id','hub_location_latitude','hub_location_longitude','pickup_cut_off_time','permanent_disabled','iata_code','booking_disable_status', 'cut_off_time', 'province_id'
    ];


    public function statusChangeLogs()
    {
        return $this->hasMany(CityStatusChangeLog::class, 'city_id')->where('column_type', 2);
    }

    public function bookingEnableDisableLogs()
    {
        return $this->hasMany(CityStatusChangeLog::class, 'city_id')->where('column_type', 1);
    }

}
