<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestTaggingHistory extends Model
{
    protected $fillable = [
        'crm_request_id','crm_request_tagging_type_id','tagged_id','agent_id'
    ];
}
