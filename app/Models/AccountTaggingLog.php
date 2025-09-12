<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTaggingLog extends Model
{
    use HasFactory;


    protected $fillable = [
        'account_id',
        'changed_by_id',
        'prev_sales_user_id',
        'new_sales_user_id',
        'type',
    ];

    public function accountUser()
    {
        return $this->belongsTo(\App\Http\Models\Shipper\User::class, 'account_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(\App\Http\Models\Admin\Admin::class, 'changed_by_id');
    }

    public function newSalesAdmin()
    {
        return $this->belongsTo(\App\Http\Models\Admin\Admin::class, 'new_sales_user_id');
    }

    public function prevSalesAdmin()
    {
        return $this->belongsTo(\App\Http\Models\Admin\Admin::class, 'prev_sales_user_id');
    }

    public static function logTagging($accountId, $changedById, $prevSalesId, $newSalesId, $type = 1)
    {
        if (!empty($accountId)) {
            return self::create([
                'account_id'        => $accountId,
                'changed_by_id'     => $changedById,
                'prev_sales_user_id' => $prevSalesId,
                'new_sales_user_id' => $newSalesId,
                'type'              => $type,
            ]);
        }
    }
}
