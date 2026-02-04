<?php

namespace App\Http\Models;

use App\Http\Models\Admin\OperationRidersCategory;
use App\Http\Models\HR\Employee;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
     // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    protected $fillable = [
        'city_id', 'name', 'phone', 'cnic', 'address', 'route_id', 'rider_main_category_id', 'rider_category_id', 'status', 'pin', 'special_rider_checkbox', 'created_by', 'updated_by', 'trax_id', 'rider_type_id', 'operation_rider_id', 'employee_id', 'ccd', 'shift_id', 'reporting_location_id','incentive_amount','area_id' ,'allow_delivered_status'
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

    public function area()
    {
        return $this->belongsTo('App\Http\Models\CityArea', 'area_id', 'id');
    }

    public function rider_operation_category()
    {
        return $this->belongsTo(OperationRidersCategory::class,'operation_rider_id','id');
    }
}
