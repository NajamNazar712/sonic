<?php

namespace App\Http\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class BlacklistSetting extends Model
{
    public function conditions() {
        return $this->hasMany('App\Http\Models\Blacklist\BlacklistSettingCondition', 'blacklist_setting_id');
    }
}
