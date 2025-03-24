<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusSharingWithWallet extends Model
{
    use HasFactory;

    protected $fillable = ['shipment_id', 'is_send', 'status_id'];

    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
}
