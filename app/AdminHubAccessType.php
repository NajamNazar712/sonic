<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminHubAccessType extends Model
{
    protected $fillable = ['admin_id', 'hub_access_type', 'previous_assigned_hubs', 'new_assigned_hubs', 'updated_by'];
}
