<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ShipperVerificationPinCode extends Model
{
    protected $fillable = [
        'user_id',
        'otp',
        'notification_id'
    ];
}
