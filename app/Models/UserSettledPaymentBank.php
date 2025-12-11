<?php

namespace App\Models;

use App\Http\Models\BanksList;
use App\Http\Models\DonePayment;
use App\Http\Models\Shipper\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSettledPaymentBank extends Model
{
    use HasFactory;

    // Table name (optional if it matches Laravel convention)
    protected $table = 'user_settled_payment_banks';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'done_payment_id',
        'bank_id',
        'bank_branch',
        'account_title',
        'account_number',
        'iban'
    ];

    // -------------------------
    // Relationships
    // -------------------------

    // The user who owns this bank info
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The done payment this bank info is linked to
    public function donePayment()
    {
        return $this->belongsTo(DonePayment::class, 'done_payment_id', 'done_payment_id');
    }

    // The bank from banks list
    public function bank()
    {
        return $this->belongsTo(BanksList::class, 'bank_id');
    }
}
