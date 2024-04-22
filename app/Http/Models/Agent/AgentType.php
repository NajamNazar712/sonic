<?php

namespace App\Http\Models\Agent;

use App\Http\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;

class AgentType extends Model
{
    //

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
