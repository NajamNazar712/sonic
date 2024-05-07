<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipperVerificationPinCode extends Model
{
    protected $table = 'shipper_verificaiton_pincodes';

    protected $fillable = [
        'user_id',
        'otp',
        'notification_id'
    ];
}
