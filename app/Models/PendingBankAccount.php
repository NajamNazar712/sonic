<?php

namespace App\Models;

use App\Http\Models\Shipper\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'bank_name',
        'bank_branch',
        'bank_city',
        'account_no',
        'account_title',
        'iban_no',
        'blank_cheque_image',
        'email_status',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'email_status' => 'boolean',
    ];

    /**
     * Optional: Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
