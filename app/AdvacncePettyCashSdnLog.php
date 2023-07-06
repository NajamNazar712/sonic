<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvacncePettyCashSdnLog extends Model
{
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}
