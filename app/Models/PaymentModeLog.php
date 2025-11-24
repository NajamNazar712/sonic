<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentModeLog extends Model
{
    use HasFactory;

    protected $table = 'payment_mode_logs';

    protected $fillable = [
        'shipment_id',
        'user_id',
        'payment_mode_id'
    ];
}
