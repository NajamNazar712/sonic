<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinjaLogSettlementRecord extends Model
{
    use HasFactory;
    protected $fillable = ['shipment_id', 'wallet_log_updated','wallet_settlement_updated', 'wallet_adjustment_updated', 'wallet_log_updated_at','wallet_settlement_updated_at', 'wallet_adjustment_updated_at' , 'logged_cod_charges', 'wallet_log_charges_updated'];
    
}
