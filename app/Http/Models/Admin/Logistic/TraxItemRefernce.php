<?php

namespace App\Http\Models\Admin\Logistic;

use Illuminate\Database\Eloquent\Model;

class TraxItemRefernce extends Model
{
    protected $fillable=['booking_id','item_code','width','height','length','weight','no_piece','user_type','created_by','updated_by'];
}
