<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeComment extends Model
{
    public function comments(){
        return $this->belongsTo('App\Http\Models\Dispute');
    }
}
