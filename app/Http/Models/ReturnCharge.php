<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnCharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','local','national'
    ];
}
