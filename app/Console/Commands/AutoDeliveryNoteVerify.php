<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\GlobalSettingsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\Webhook\FinalChargesWebhookController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\RvShipmentTicket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RvTrait;

class AutoDeliveryNoteVerify extends Command
{
    use RvTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:deliverynoteverification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Delivery Note Verification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $date = Carbon::today()->subDays(9);
    
            $excluded_hubs = array();
            $setting = GlobalSettings::where('type', 'delivery_note_auto_verification');
    
            if($setting->exists()){
                $setting = $setting->first();
    
                if($setting->setting_value){
                    $excluded_hubs_setting = GlobalSettings::where('type', 'delivery_note_auto_verification_exclude_hubs');
    
                    if ($excluded_hubs_setting->exists()) {
                        $excluded_hubs_setting = $excluded_hubs_setting->first();
                        $excluded_hubs = array_map('intval', explode(',', $excluded_hubs_setting->text));
                    }
    
                    $delivery_notes = DeliveryNote::whereDate('created_at', '>=', $date)->where(['status' => 0, 'pending_status' => 1])->whereNotIn('hub_id', $excluded_hubs);
                    if ($delivery_notes->exists()) {
                        $deliveryNoteShipmentsId = DeliveryNoteShipment::whereIn('delivery_note_id', $delivery_notes->pluck('id'))
                            ->distinct()
                            ->pluck('shipment_id')
                            ->toArray(); // Convert to array for chunking
                        $totalShipments = count($deliveryNoteShipmentsId); // Total number of shipments
                        $chunkSize = 1000; // Define the chunk size

                        for ($offset = 0; $offset < $totalShipments; $offset += $chunkSize) {
                            // Get the current chunk of shipment IDs
                            $shipmentChunk = array_slice($deliveryNoteShipmentsId, $offset, $chunkSize);
                            // Fetch all relevant shipments in one go 
                            $shipments = Shipment::whereIn('id', $shipmentChunk)
                                ->whereNotIn('shipper_status_id', [30, 36, 37, 56])
                                ->where('booking_type_id', '!=', 6)
                                ->select('id', 'shipper_status_id', 'booking_type_id', 'packaging_material_request', 'packaging_material_charges', 'shipment_type')
                                ->get(); 
                            // Fetch shipments journey data for the current chunk
                            // $shipmentIds = array_column($shipments->toArray(), 'id');
                            // $shipments_journey =
                            // ShipmentsJourney::whereIn('shipment_id', $shipmentIds)
                            // ->orderBy('id', 'desc')
                            // ->get()
                            // ->unique('shipment_id') // Keeps the first occurrence (highest id) per shipment_id
                            //     ->keyBy('shipment_id');
                                
                            foreach ($shipments as $shipment) {
                                $journey =  $shipment->latest_shipment_journey;
                                ShipmentsJourneyController::add($journey->shipment_id, $journey->shipper_status_id, $journey->consignee_status_id, $journey->status_reason_id, $journey->remarks, $journey->user_id, 346, $journey->reference_1_id, $journey->reference_2_id, 1, $journey->received_or_refused_by, $journey->rider_id, $journey->cnic, $journey->relation);
    
                                if ($shipment->shipper_status_id == 14) {
                                   
                                    if ($shipment->booking_type_id == 1) {
                                        if (($shipment->packaging_material_request == 1 && $shipment->packaging_material_charges != '') || $shipment->packaging_material_request == 0) {
                                            AdminFinanceController::add_payment($shipment->id, 0);
    
                                        }
                                    } elseif ($shipment->booking_type_id == 4) {
                                        AdminFinanceController::done_payment($shipment->id, 0);
                                    }
                                 FinalChargesWebhookController::webhook_subscription($shipment->id);
    
                                }
                                if ($shipment->shipper_status_id == 12 && RvShipmentTicket::where(['shipment_id' => $shipment->id, 'permanent_disable' => '1'])->exists()) {
                                    $this->conditionalRvSarUpdate($shipment, $journey->status_reason_id);
                                }
                                // Mark Return Confirm if $shipper_status_id == 12 And $status_reason_id == (27 or 35)
                                // 27 = Shipment Damaged
                                // 35 = Delivery Stopped
                                if($shipment->shipper_status_id == 12 && in_array($journey->status_reason_id, [27, 35]) && RvShipmentTicket::where(['shipment_id' => $shipment->id, 'permanent_disable' => '0'])->exists()) {

                                    $journeys = ShipmentsJourney::where('shipment_id', $shipment->id)->whereIn('status_reason_id', [27, 35])->count();
                                    if ($journeys > 2) {
                                        Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                                        if ($shipment->shipment_type == 1) {
                                            if ($shipment->booking_type_id != 4) {
                                                ShipmentChargesController::return($shipment->id);
                                                if ($shipment->packaging_material_request != 1) {
                                                    AdminFinanceController::add_payment($shipment->id, 1);
                                                }
                                            } else {
                                                ShipmentChargesController::walk_in_return($shipment->id);
                                                $shipment->walk_in_status = 2;
                                                $shipment->save();
                                                AdminFinanceController::done_payment($shipment->id, 1);
                                            }
                                        }
                                        ShipmentsJourneyController::add($shipment->id, 20, 20, $journey->status_reason_id, $journey->remarks ?? NULL, NULL, 346, null, null, 1, null, null, null, null, null);
                                    } else {
                                        if ($shipment->shipper_status_id == 12 && RvShipmentTicket::where(['shipment_id' => $shipment->id, 'permanent_disable' => '0'])->exists()) {
                                                $this->conditionalRvSarUpdate($shipment, $journey->status_reason_id, 1);
                                        }
                                    }
                                    //Remove Shipment from RV Shipment Ticket
                                    RvShipmentTicket::where('shipment_id', $shipment->id)->delete();
                                }
    
                            }
                        }
                        $current_time = Carbon::now();
                        DeliveryNote::whereIn('id', $delivery_notes->pluck('id'))->update(['verified_by' => 346, 'status' => 1, 'last_updated_at' => $current_time, 'status_verified_at' => $current_time]);
                        
                    }
                    // if($delivery_notes->exists()){
                    //     $delivery_notes = $delivery_notes->get();
                    //     foreach ($delivery_notes as $delivery_note) {
    
                    //         $shipment_ids = $delivery_note->delivery_note_shipments()->pluck('shipment_id');
                    //         $shipments = Shipment::whereIn('id', $shipment_ids)->where('booking_type_id', 6);
                    //         if($shipments->exists()){
                    //             continue;
                    //         }
    
                    //         $delivered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', [30, 36, 37, 56]);
                    //         if($delivered_shipments->exists()){
                    //             continue;
                    //         }
    
                    //         $shipments = Shipment::whereIn('id', $shipment_ids)->select('id', 'shipper_status_id', 'booking_type_id', 'packaging_material_request', 'packaging_material_charges')->get();
    
                    //         foreach ($shipments as $shipment){
                    //             $shipments_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->latest('id')->first();
                    //             ShipmentsJourneyController::add($shipments_journey->shipment_id, $shipments_journey->shipper_status_id, $shipments_journey->consignee_status_id, $shipments_journey->status_reason_id, $shipments_journey->remarks, $shipments_journey->user_id, 346, $shipments_journey->reference_1_id, $shipments_journey->reference_2_id, 1, $shipments_journey->received_or_refused_by, $shipments_journey->rider_id, $shipments_journey->cnic, $shipments_journey->relation);
                    //             if($shipment->shipper_status_id == 14){
                    //                 if($shipment->booking_type_id == 1){
                    //                         if (($shipment->packaging_material_request == 1 && $shipment->packaging_material_charges != '') || $shipment->packaging_material_request == 0) {
                    //                             AdminFinanceController::add_payment($shipment->id, 0);
                    //                         }
    
                    //                 }
                    //                 else if($shipment->booking_type_id == 4){
                    //                     AdminFinanceController::done_payment($shipment->id, 0);
                    //                 }
                    //                 FinalChargesWebhookController::webhook_subscription($shipment->id);
    
                    //             }
    
                    //             // Mark Return Confirm if $shipper_status_id == 12 And $status_reason_id == (27 or 35)
                    //             // 27 = Shipment Damaged
                    //             // 35 = Delivery Stopped
                    //             if ($shipment->shipper_status_id == 12 && in_array($shipments_journey->status_reason_id, [27, 35])) {
                    //                 $globalAdminId = 346;
    
                    //                 Shipment::where('id', $shipment->id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
    
                    //                 $shipment_details = Shipment::find($shipment->id);
                    //                 if ($shipment_details->shipment_type == 1) {
                    //                     if ($shipment_details->booking_type_id != 4) {
                    //                         ShipmentChargesController::return($shipment->id);
                    //                         if ($shipment_details->packaging_material_request != 1) {
                    //                             AdminFinanceController::add_payment($shipment->id, 1);
                    //                         }
                    //                     } else {
                    //                         ShipmentChargesController::walk_in_return($shipment->id);
                    //                         $shipment_details->walk_in_status = 2;
                    //                         $shipment_details->save();
                    //                         AdminFinanceController::done_payment($shipment->id, 1);
                    //                     }
                    //                 }
                                    
                    //                 ShipmentsJourneyController::add($shipment->id, 20, 20, $shipments_journey->status_reason_id, $shipments_journey->remarks, NULL, $globalAdminId, null, null, 1, null, null, null, null, null);
    
                    //                 //Remove Shipment from RV Shipment Ticket
                    //                 RvShipmentTicket::where('shipment_id', $shipment->id)->delete();
    
                    //                 // dispatch(new ProcessRemoveShipmentFromRvShipmentTicket($shipment->id));
                    //             }
    
                    //         }
                    //         $current_time = Carbon::now();
                    //         DeliveryNote::where('id', $delivery_note->id)->update(['verified_by' => 346, 'status' => 1, 'last_updated_at' => $current_time, 'status_verified_at' => $current_time]);
                    //     }
                    // }
                }
            }
        
        } catch (\Throwable $th) {
            Log::channel('cronJobLog')->info('s ' . 'auto:deliverynoteverification Failed' . $th->getMessage());
            $this->createRvCronLog($th->getMessage());
        }
    }
}
