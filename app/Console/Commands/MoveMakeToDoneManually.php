<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShipmentsPaymentJourneyController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\Jobs\WalletLogDispatchJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MoveMakeToDoneManually extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move_make_to_done';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = Admin::find(346); // Replace with actual user ID
        Auth::login($user); // Log in the user
        $pending_payment_shipment_ids = PendingPaymentShipment::where('pending_payment_id',445340)->pluck('id')->toArray();
        $pending_logs = [];
        $done_payment_id = 1579313;
        foreach ($pending_payment_shipment_ids as $pending_payment_shipment_id) {
            $pending_payment_shipment = PendingPaymentShipment::find($pending_payment_shipment_id);
            if ($pending_payment_shipment) {

                $shipment = Shipment::with(['user.wallet'])->find($pending_payment_shipment->shipment_id);
                if($shipment->user->wallet) {
                    $log_bid = $this->isWalletLogUpdated($pending_payment_shipment->shipment_id);
                    if(!$log_bid) {
                        $cod_amount = $shipment->amount;
                        $status_array = [14, 25, 31, 38, 37, 18, 20];
                        if(in_array($shipment->shipper_status_id, $status_array) && $pending_payment_shipment->type == 2) {
                            $cod_amount = 0;
                        }
                        if(!array_key_exists($pending_payment_shipment->shipment_id, $pending_logs)) {
                            $pending_logs[$pending_payment_shipment->shipment_id] = [
                                "shipmentId" => $shipment->id,
                                "wallet_id" => $shipment->user->wallet->wallet_id,
                                "client_id" =>  $shipment->user->id,
                                "reference_id" => (string) Str::uuid(),
                                "shipment_id" => $shipment->tracking_number,
                                "amount" => $cod_amount,
                                "order_created_date" => $shipment->created_at,
                            ];
                        }
                    }
                    if($pending_payment_shipment->type == 3) {
                        $finja_status = 0;
                    } elseif($pending_payment_shipment->type == 0 || $pending_payment_shipment->type == 1) {
                        $settlement_bid = $this->isWalletSettlementUpdated($pending_payment_shipment->shipment_id);
                        if(!$log_bid) {
                            $finja_status = 1;
                        } elseif($settlement_bid) {
                            $finja_status = 2;
                        } else {
                            $finja_status = 1;
                        }
                    } elseif( $pending_payment_shipment->type == 2) {
                        $finja_status = 2;
                    }
                }
                $done_payment_shipment = new DonePaymentShipment();

                $done_payment_shipment->created_at = $pending_payment_shipment->created_at;
                $done_payment_shipment->done_payment_id = $done_payment_id;
                $done_payment_shipment->shipment_id = $pending_payment_shipment->shipment_id;
                $done_payment_shipment->type = $pending_payment_shipment->type;
                $done_payment_shipment->amount = $pending_payment_shipment->amount;
                $done_payment_shipment->charges = $pending_payment_shipment->charges;
                $done_payment_shipment->gst = $pending_payment_shipment->gst;
                $done_payment_shipment->wht = $pending_payment_shipment->wht;
                $done_payment_shipment->payable = $pending_payment_shipment->payable;
                $done_payment_shipment->sms_charges = $pending_payment_shipment->sms_charges;
                $done_payment_shipment->wallet_action_bid = $finja_status;

                $done_payment_shipment->save();

                $packaging_material_charges = 0;
                $adjustment_amount = 0;
                if ($pending_payment_shipment->type == 2) {
                    $adjustment_amount = $pending_payment_shipment->payable;
                }
                $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                if ($shipment->packaging_material_request) {
                    $packaging_material_charges = $shipment->packaging_material_charges;
                    if ($packaging_material_charges == null) {
                        $packaging_material_charges = 0;
                    }
                }

                self::add_done_payment_charges($done_payment_id, $pending_payment_shipment->amount, $pending_payment_shipment->charges, $pending_payment_shipment->gst, $pending_payment_shipment->payable, $packaging_material_charges, $adjustment_amount, null, $pending_payment_shipment->wht, ($done_payment->ibft_charges ?? 0), $done_payment_shipment->sms_charges);
                $pending_payment_shipment->delete();

                self::adjustment_logs_done(1, $pending_payment_shipment_id, $done_payment_shipment->id);

                if ($done_payment_shipment->type == 0) {
                    $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                    $shipment->payment_status_id = 1;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 1, Auth::id(), '', $done_payment_id);
                } else if ($done_payment_shipment->type == 1) {
                    $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                    $shipment->payment_status_id = 5;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 5, Auth::id(), '', $done_payment_id);
                } else if ($done_payment_shipment->type == 3) {
                    $shipment = Shipment::find($pending_payment_shipment->shipment_id);

                    $shipment->payment_status_id = 10;

                    $shipment->save();

                    ShipmentsPaymentJourneyController::add($shipment->id, 10, Auth::id(), '', $done_payment_id);
                }
            }
        }
        if(count($pending_logs) > 0) {
            WalletLogDispatchJob::dispatch($pending_logs);
        }
        Auth::logout();
    }
}
