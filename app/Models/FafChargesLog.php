<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FafChargesLog extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'old_percentage','old_status', 'changed_by', 'new_percentage', 'new_status'];
}
