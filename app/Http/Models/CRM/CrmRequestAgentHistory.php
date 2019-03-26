<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestAgentHistory extends Model
{
    protected $fillable = [
        'crm_request_id','agent_id'
    ];
}
