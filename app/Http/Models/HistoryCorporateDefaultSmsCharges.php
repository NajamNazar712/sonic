<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateDefaultSmsCharges extends Model
{
    protected $fillable = [
        'user_id',
        'sms_charges_type_id' , 'sms_charges','sms_charges_status'
    ];
}
