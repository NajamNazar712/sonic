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
        'nature_id',
        'shipment_id',
        'expiry_time',
        'log_id'
    ];

    public function one_link_log() {
        return $this->hasOne('App\Models\OneLinkApiLog', 'id', 'log_id');
    }
    public function shipment()
    {
        return $this->belongsTo(\App\Http\Models\Shipment::class, 'shipment_id', 'id');
    }
    
}
