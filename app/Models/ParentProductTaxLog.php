<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentProductTaxLog extends Model
{
    use HasFactory;

    protected $table = 'parent_product_percentage_logs';
    protected $fillable = [
        'parent_product_id',
        'field_changed',
        'old_value',
        'new_value',
        'text',
        'updated_by',
    ];
}
