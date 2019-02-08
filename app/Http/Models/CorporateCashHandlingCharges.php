<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateCashHandlingCharges extends Model
{
    protected $table = 'corporate_cash_handling_charges';
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','charges'
    ];
}
