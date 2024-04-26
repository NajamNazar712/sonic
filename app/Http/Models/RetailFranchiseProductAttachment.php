<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailFranchiseProductAttachment extends Model
{
    protected $fillable = [
        'franchise_id',
        'attachment_1',
        'attachment_2',
        'attachment_3',
        'attachment_4',
        'attachment_5',
        'updated_by'
    ];
}
