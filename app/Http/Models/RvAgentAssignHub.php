<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RvAgentAssignHub extends Model
{
    protected $fillable = ['agent_id', 'city_id', 'priority'];
}
