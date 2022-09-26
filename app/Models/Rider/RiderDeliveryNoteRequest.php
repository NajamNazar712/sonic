<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderDeliveryNoteRequest extends Model
{
    protected $fillable=[
        'hub_id','rider_id','route_id','shipments_count','updated_by','total_cod_amount','approved_by','approved_at','status','ordering'];
}
