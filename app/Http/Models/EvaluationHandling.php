<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationHandling extends Model
{
    public function campaign(){
        return $this->belongsTo('App\Http\Models\EvaluationCampaign','campaign_id','id');
    }
}
