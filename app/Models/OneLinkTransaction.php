<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneLinkTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'rrn',
        'stan',
        'date_time',
        'message_id',
        'original_rrn',
        'original_stan',
        'original_rtp_id',
        'merchant_id',
        'sub_dept',
        'status',
        'original_instructed_amount',
        'net_amount',
        'iban',
        'account_title',
        'longitude',
        'latitude',
        'nature_id'
    ];
    
}
