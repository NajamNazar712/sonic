<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturnConfirmationPendingSmsAttempt extends Model
{
    protected  $fillable = ['shipment_id','status','count'];

}
