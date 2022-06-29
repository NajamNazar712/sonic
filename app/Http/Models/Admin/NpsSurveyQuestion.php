<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class NpsSurveyQuestion extends Model
{
    //
    protected $table = 'nps_survey_question';

    public function nps_sur() {
        return $this->belongsTo('App\Http\Models\Admin\NpsSurvey', 'id', 'nps_survey_id');
    }
}
