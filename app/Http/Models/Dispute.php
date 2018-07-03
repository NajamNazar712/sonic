<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'description','admin_id','dispute_type_id','shipments_count','status'
    ];
    public function comments(){
        return $this->hasMany('App\Http\Models\DisputeComment');
    }
}
