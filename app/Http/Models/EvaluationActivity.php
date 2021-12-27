<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationActivity extends Model
{
    //
    protected $guarded = [];
    
    public function handling(){
        return $this->belongsTo('App\Http\Models\EvaluationHandling','evaluation_handling_id','id');
    }

}
