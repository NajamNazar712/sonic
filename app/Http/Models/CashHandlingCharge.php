<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CashHandlingCharge extends Model
{
    protected $fillable = [
          'user_id','shipping_mode_id','range_up','range_down','charges'
    ];

}
