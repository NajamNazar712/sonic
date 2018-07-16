<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeComment extends Model
{
    protected $fillable = [
        'dispute_id','comment','admin_id'
    ];
    public function dispute(){
        return $this->belongsTo('App\Http\Models\Dispute');
    }
    public function admin(){
        return $this->belongsTo('App\Http\Models\Admin\Admin');
    }
}
