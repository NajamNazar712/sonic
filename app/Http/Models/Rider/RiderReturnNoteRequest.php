<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderReturnNoteRequest extends Model
{
    protected $guarded=[];

    public function rider(){
        return $this->belongsTo('App\Http\Models\Rider','rider_id');
    }
}
