<?php

namespace App\Http\Models\V3Pickup;

use Illuminate\Database\Eloquent\Model;

class V3PickupRequestService extends Model
{
   protected $fillable=['pickup_request_id','pickup_request_service_id','count'];
}
