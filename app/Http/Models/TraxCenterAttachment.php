<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class TraxCenterAttachment extends Model
{
    protected $fillable = [
        'retail_trax_center_id',
        'advance_amount',
        'rental',
        'landlord_name',
        'landlord_contact_number',
        'shop_address',
        'agreement_start_date',
        'agreement_end_date',
        'attachment_1',
        'attachment_2',
        'attachment_3',
        'attachment_4',
        'attachment_5',
    ];
}
