<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestCaseNatureAndTypeHistory extends Model
{
    protected $fillable = [
        'crm_request_id','case_nature_id','case_nature_type_id','description','edited_by'
    ];
}
