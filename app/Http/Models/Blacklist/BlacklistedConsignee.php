<?php

namespace App\Http\Models\Blacklist;

use Illuminate\Database\Eloquent\Model;

class BlacklistedConsignee extends Model
{
    public function blacklist(){
        return $this->belongsTo('App\Http\Models\Blacklist\BlacklistSetting', 'blacklist_setting_id', 'id');
    }
}
