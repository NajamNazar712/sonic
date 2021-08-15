<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FnfSectionHod extends Model
{
    public function fnf(){
        return $this->belongsTo('App\Http\Models\FnfSectionEmployee');
    }

    public function status(){
        return $this->belongsTo('App\FnfStatus','status_id','id');
    }
}
