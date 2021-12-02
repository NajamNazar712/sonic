<?php

namespace App\Http\Models\Webhook;

use App\Http\Models\Shipper\User;
use Illuminate\Database\Eloquent\Model;

class ShipmentStatusSubscription extends Model
{
    protected $fillable = ['user_id', 'url', 'status'];

    public function shipper(){
        return $this->belongsTo(User::class,'user_id');
    }
}
