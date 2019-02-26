<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateInsuranceCharge extends Model
{
    protected $table = 'corporate_insurance_charges';
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','charges'
    ];
}
