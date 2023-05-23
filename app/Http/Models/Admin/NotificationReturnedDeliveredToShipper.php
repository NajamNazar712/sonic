<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class NotificationReturnedDeliveredToShipper extends Model
{
    protected $table = 'notification_returned_delivered_to_shipper';
    protected $fillable = ['user_id', 'return_note_id', 'status'];
}
