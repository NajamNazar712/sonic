<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeShipment extends Model
{
    public $timestamps = false;
    protected $fillable = ['dispute_id','shipment_id'];
}
