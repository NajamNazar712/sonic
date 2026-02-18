<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageOnExpectedShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'percentage_on_expected_shipments',
        'percentage_on_expected_shipments_added_by',
        'percentage_on_expected_shipments_added_at',
    ];
}
