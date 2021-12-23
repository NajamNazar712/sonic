<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class OpenParcelHistory extends Model
{
    protected $fillable = [
        'shipment_id','user_id','remarks','amount', 'date'
    ];
    public function rider(){
        return $this->belongsTo('App\Http\Models\Rider');
    }
    public function user(){
        return $this->belongsTo('App\Http\Models\Shipper\User');
    }
    public function shipment(){
        return $this->belongsTo('App\Http\Models\Shipment');
    }
}
