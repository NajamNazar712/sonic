<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestStatusHistory extends Model
{
    protected $fillable = [
        'crm_request_id', 'status_id', 'agent_id'
    ];
}
