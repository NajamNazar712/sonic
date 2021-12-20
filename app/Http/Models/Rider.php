<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rider extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'city_id', 'name', 'phone', 'cnic', 'address', 'route_id', 'rider_main_category_id', 'rider_category_id', 'status', 'pin', 'special_rider_checkbox', 'created_by', 'updated_by', 'trax_id', 'rider_type_id', 'operation_rider_id', 'employee_id', 'ccd', 'shift_id', 'reporting_location_id'
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
    public function deliverynotes(){
        return $this->hasMany('App\Http\Models\Admin\DeliveryNote');
    }
    public function open_parcel_rider(){
        return $this->hasOne('App\Http\Models\OpenParcelHistory');
    }
}
