<?php

namespace App\Http\Models\Agent;

use App\Http\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;

class RvAgentType extends Model
{
    //

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
