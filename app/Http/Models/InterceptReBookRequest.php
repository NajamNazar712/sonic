<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InterceptReBookRequest extends Model
{
    protected $fillable = [
        'shipment_id','consignee_city_id','consignee_name','consignee_address','consignee_phone_number_1','consignee_phone_number_2','consignee_email','amount','shipper_id','request_date','status','updated_by','updated_by_date','intercept_type' ,'city_area_id'
    ];
}
