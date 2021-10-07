<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestEscalationTagging extends Model
{
    public function role() {
        return $this->belongsTo('App\Http\Models\Admin\AdminRole', 'role_id', 'id');
    }

    public function hub() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
