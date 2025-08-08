<?php

namespace App\Models;

use App\Http\Models\CRM\CrmRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CrmInvalidReasonRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_request_id',
        'claim_invalid_reason_id',
    ];

    public function crmRequest()
    {
        return $this->belongsTo(CrmRequest::class);
    }

    public function claimInvalidReason()
    {
        return $this->belongsTo(ClaimInvalidReason::class);
    }
}
