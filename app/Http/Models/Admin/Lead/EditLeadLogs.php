<?php

namespace App\Http\Models\Admin\Lead;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditLeadLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'trax_id',
        'admin_name',
        'edited_fields',
    ];

    // Format dates as 'YYYY-MM-DD HH:mm:ss'
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
