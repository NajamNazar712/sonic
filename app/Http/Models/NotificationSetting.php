<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    public function notification_setting_shippers() {
		return $this->hasMany('App\Http\Models\NotificationSettingShipper');
	}
}
