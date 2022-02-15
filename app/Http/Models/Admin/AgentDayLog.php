<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AgentDayLog extends Model
{
    protected $fillable = ['agent_day_id','start','end','status'];
}
