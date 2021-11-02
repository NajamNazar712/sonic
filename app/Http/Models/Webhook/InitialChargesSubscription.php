<?php

namespace App\Http\Models\Webhook;

use Illuminate\Database\Eloquent\Model;

class InitialChargesSubscription extends Model
{
    protected $fillable = ['user_id', 'url', 'status'];
}
