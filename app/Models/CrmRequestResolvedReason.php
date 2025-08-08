<?php

namespace App\Models;

use App\Http\Models\CRM\CrmRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmRequestResolvedReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_request_id',
        'resolved_reason_id',
        'resolved_sub_reason_id',
    ];

    public function crmRequest()
    {
        return $this->belongsTo(CrmRequest::class);
    }

    public function resolvedReason()
    {
        return $this->belongsTo(ClaimResolvedReason::class, 'resolved_reason_id');
    }

    public function resolvedSubReason()
    {
        return $this->belongsTo(ClaimResolvedSubReason::class, 'resolved_sub_reason_id');
    }
}
