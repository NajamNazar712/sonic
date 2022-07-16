<?php

namespace App\Http\Models;

use App\Http\Models\HR\Employee;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $fillable = [
        'city_id', 'name', 'phone', 'cnic', 'address', 'route_id', 'rider_main_category_id', 'rider_category_id', 'status', 'pin', 'special_rider_checkbox', 'created_by', 'updated_by', 'trax_id', 'rider_type_id', 'operation_rider_id', 'employee_id', 'ccd', 'shift_id', 'reporting_location_id','incentive_amount'
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
    public function employee(){
        return $this->belongsTo(Employee::class,'trax_id','trax_id');
    }
}
