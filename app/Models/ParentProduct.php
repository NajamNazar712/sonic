<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_percentage',
        'sst_percentage'
    ];
}
