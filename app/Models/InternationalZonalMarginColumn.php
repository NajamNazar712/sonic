<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalZonalMarginColumn extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'margin_column', // Add this field
        'zone_column', // Add this field
        // Add other fields that need to be mass-assigned
    ];
}
