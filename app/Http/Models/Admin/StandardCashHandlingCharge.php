<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StandardCashHandlingCharge extends Model
{
    protected $fillable = ['shipping_mode_id','range_up','range_down','charges'];
}
