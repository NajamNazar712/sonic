<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingSmsCharges extends Model
{
    protected $fillable = [
        'user_id',
        'sms_charges','sms_charges_status'
    ];
}
