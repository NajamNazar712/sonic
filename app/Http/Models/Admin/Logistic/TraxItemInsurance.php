<?php

namespace App\Http\Models\Admin\Logistic;

use Illuminate\Database\Eloquent\Model;

class TraxItemInsurance extends Model
{
    protected $fillable=['booking_id','special_handling_id','insurance','item_code','user_type','created_by','updated_by'];

}
