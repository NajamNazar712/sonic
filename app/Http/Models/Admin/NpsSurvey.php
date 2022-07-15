<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class NpsSurvey extends Model
{
    //
    protected $table = 'nps_survey';

    public function nps_quest(){
        return $this->hasMany('App\Http\Models\Admin\NpsSurveyQuestion','nps_survey_id','id');
    }
    public function nps_report(){
        return $this->hasMany('App\Http\Models\NpsSurveyReport','nps_survey_id','id');
    }
}
