<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name','hub','hub_id','pickup','status'
    ];
    public function hub(){
        $this->belongsTo(self::class, 'hub_id');
    }
}
