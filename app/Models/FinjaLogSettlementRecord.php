<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinjaLogSettlementRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'shipment_id',
        'wallet_log_updated',
        'wallet_settlement_updated',
        'wallet_adjustment_updated',
        'wallet_log_updated_at',
        'wallet_settlement_updated_at',
        'wallet_adjustment_updated_at',
        'logged_cod_charges',
        'wallet_log_charges_updated',
        'wallet_log_charges_updated_at',
        'wallet_charges_finova_settled',
        'wallet_charges_finova_settled_updated_at'
    ];

    static function check_wallet_charges_type($shipment_id){

        $type = FinjaLogSettlementRecord::where('shipment_id', $shipment_id)->where('wallet_charges_finova_settled', 2)->latest()->first();

        if($type) {
            return true;
        } else {
            return false;
        }
    }
}
