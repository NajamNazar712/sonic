<?php

namespace App;

use App\Http\Models\DonePaymentShipment;
use App\Http\Models\InvoiceShipment;
use App\Http\Models\PendingInvoiceShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Models\FinjaLogSettlementRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class ShipmentAdditionalCharges extends Model
{

    protected $fillable = [
        'faf_charges',
        'shipment_id',
        'payment_type',
        'return_cod_discount_applied',
        'zero_cod_discount_applied',
        'arrival_charges_applied',
        'apollo_shipment_id',
        'apollo_is_piece',
        'wallet_charges',
        'wallet_charges_updated_at',
    ];

    static function fetch_faf_charges($shipment_id){
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->latest()->first();
        if(!empty($shipment_additional_charges)){
            $faf_charges = $shipment_additional_charges->faf_charges;
        }else{
            $faf_charges = 0;
        }
        return $faf_charges;
    }
    static function apply_faf_charges($shipment_id,$payment_type){
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->latest()->first();
        if(!empty($shipment_additional_charges)){
            $shipment_additional_charges->payment_type = $payment_type;
            $shipment_additional_charges->save();
            $faf_charges = $shipment_additional_charges->faf_charges;
        }else{
            $faf_charges = 0;
        }
        return $faf_charges;
    }

    static function additional_charges_apply($shipment_ids,$arrival = false ,$zero_cod = false,$return_discount =false){
        foreach ($shipment_ids as $shipment_id) {
            // Use updateOrCreate to simplify the process
            ShipmentAdditionalCharges::updateOrCreate(
                ['shipment_id' => $shipment_id], // The condition to check if the record exists
                array_merge(
                    ['faf_charges' => DB::raw('IFNULL(faf_charges, 0)')], // Ensure faf_charges is set to 0 if null
                    $arrival ? ['arrival_charges_applied' => 1] : [],
                    $zero_cod ? ['zero_cod_discount_applied' => 1] : [],
                    $return_discount ? ['return_cod_discount_applied' => 1] : []
                )
            );
        }
    }
    static function additional_charges_undo($shipment_ids,$arrival = false ,$zero_cod = false,$return_discount =false){
        foreach ($shipment_ids as $shipment_id) {
            // Use updateOrCreate to simplify the process
            ShipmentAdditionalCharges::updateOrCreate(
                ['shipment_id' => $shipment_id], // The condition to check if the record exists
                array_merge(
                    $arrival ? ['arrival_charges_applied' => 0] : [],
                    $zero_cod ? ['zero_cod_discount_applied' => 0] : [],
                    $return_discount ? ['return_cod_discount_applied' => 0] : []
                )
            );
        }
    }

    static function check_additional_charges($shipment_id, $arrival = false, $zero_cod = false, $return_discount = false) {
        $charges = 0; // Default to 0 to handle cases where no charge is set
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id);

        if ($shipment_additional_charges->exists()) {
            $shipment_additional_charges = $shipment_additional_charges->first();

            if ($arrival) {
                $charges = $shipment_additional_charges->arrival_charges_applied;
            }
            if ($zero_cod) {
                $charges = $shipment_additional_charges->zero_cod_discount_applied;
            }
            if ($return_discount) {
                $charges = $shipment_additional_charges->return_cod_discount_applied;
            }
        } else {
            if ($arrival) {
                $shipment = Shipment::find($shipment_id);

                if (!$shipment) {
                    return $charges; // Return default if shipment is not found
                }

                $account_type_id = $shipment->user->account_type_id;

                if ($account_type_id == 1) {
                    $pending = PendingPaymentShipment::where('shipment_id', $shipment_id)->where('type', 3)->first();

                    if ($pending) {
                        $charges = $pending->charges;
                    } else {
                        $done = DonePaymentShipment::where('shipment_id', $shipment_id)->where('type', 3)->first();
                        $charges = $done ? $done->charges : 0;
                    }
                } else {
                    $pending = PendingInvoiceShipment::where('shipment_id', $shipment_id)->where('type', 3)->first();

                    if ($pending) {
                        $charges = $pending->charges;
                    } else {
                        $done = InvoiceShipment::where('shipment_id', $shipment_id)->where('type', 3)->first();
                        $charges = $done ? $done->charges : 0;
                    }
                }
            }
        }

        return $charges;
    }

    static function fetch_wallet_charges($shipment_id){
        $wallet_charges = 0;
        $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->whereNotNull('wallet_charges_updated_at')->latest()->first();
        if (!empty($shipment_additional_charges)) {
            $wallet_charges = $shipment_additional_charges->wallet_charges;
        }
        return $wallet_charges;
    }
    static function get_wallet_charges_if_applicable($shipment_id) {
        $has_unsettled_record = FinjaLogSettlementRecord::where('shipment_id', $shipment_id)
            ->whereNull('wallet_charges_finova_settled')
            ->exists();

        $has_updated_wallet_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)
            ->whereNotNull('wallet_charges_updated_at')
            ->exists();

        return $has_unsettled_record && $has_updated_wallet_charges;
    }

    static function show_wallet_charges_by_type($shipment_id,$type){
        $wallet_charges = 0;
        $exists = FinjaLogSettlementRecord::where('shipment_id', $shipment_id)->where('wallet_charges_finova_settled',$type)->exists();
        if ($exists) {
            $shipment_additional_charges = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->latest()->first();
            if (!empty($shipment_additional_charges)) {
                $wallet_charges = $shipment_additional_charges->wallet_charges;
            }
        }
        return $wallet_charges;
    }

    static function settle_wallet_finova_charges($shipment_id,$type){
        FinjaLogSettlementRecord::where('shipment_id', $shipment_id)->whereNull('wallet_charges_finova_settled')->update(['wallet_charges_finova_settled'=>$type,'wallet_charges_finova_settled_updated_at'=>date('Y-m-d H:i:s')]);
    }

}
