<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalespersonSegmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'salesperson_id',
        'segment_id',
        'action',
        'old_values',
        'new_values',
        'updated_by',
    ];
}
