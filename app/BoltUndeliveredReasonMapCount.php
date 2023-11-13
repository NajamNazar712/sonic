<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoltUndeliveredReasonMapCount extends Model
{
    protected $fillable = ['shipment_id', 'delivery_note_id','reason_id','count'];
}
