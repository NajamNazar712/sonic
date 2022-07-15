<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class NpsSurveyReport extends Model
{
    public function nps_que(){
        return $this->hasMany('App\Http\Models\Admin\NpsSurveyQuestion','nps_survey_id','nps_survey_id');
    }
}
