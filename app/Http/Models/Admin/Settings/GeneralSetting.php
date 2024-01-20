<?php

namespace App\http\models\admin\settings;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    public function getStartDateAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('j F Y');
    }

    public function getEndDateAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('j F Y');
    }
}
