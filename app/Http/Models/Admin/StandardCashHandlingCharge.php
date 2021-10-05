<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StandardCashHandlingCharge extends Model
{
    public $timestamps = FALSE;
    protected $fillable = ['shipping_mode_id','range_up','range_down','charges'];
}
