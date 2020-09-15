<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\http\Models\Admin\NsaAccountShipment;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminNsaAccountShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    static public function nsa_account_shipment($shipment_id)
    {
        $shipment = Shipment::find($shipment_id);

        $nsa_shipment = new NsaAccountShipment();
        $nsa_shipment->shipment_id = $shipment->id;
        $nsa_shipment->hub_id = $shipment->pickup_address->city->id;
        $nsa_shipment->save();
    }

    static public function nsa_account_shipment_process()
    {
        $nsa_shipments = NsaAccountShipment::where('status', 0);
        if ($nsa_shipments->exists()) {
            $nsa_shipments = $nsa_shipments->get();
            $settings = GlobalSettings::where('type', 'nsa_accounts')->first();
            $rider_id = $settings->setting_value;
            $valid_shipments = array();
            $shipments_count = 0;
            $total_cod_amount = 0;
            foreach ($nsa_shipments as $nsa_shipment) {
                if (!in_array($nsa_shipment->shipment_id, $valid_shipments)) {
                    $shipment_details = Shipment::find($nsa_shipment->shipment_id);
                    if ($shipment_details) {
                        $valid_shipments[] = $nsa_shipment->shipment_id;
                        $shipments_count++;

                        if ($shipment_details->booking_type_id != 4 || ($shipment_details->booking_type_id == 4 && $shipment_details->charges_mode_id == 2)) {
                            $total_cod_amount += $shipment_details->amount;
                        }
                    }
                }
                $nsa_shipment->status = 1;
                $nsa_shipment->save();
            }

            $order = false;
            if ($shipments_count != 0) {
                $note = DeliveryNote::create([
                    'hub_id' => 202,
                    'rider_id' => $rider_id,
                    'route_id' => 2,
                    'shipments_count' => $shipments_count,
                    'admin_id' => 50,
                    'total_cod_amount' => $total_cod_amount,
                    'password' => NULL,
                    'last_updated_at' => Carbon::now(),
                    'special_rider' => 0,
                    'order' => $order
                ]);

                if ($note) {
                    if (!$order) {  //Default
                        sort($valid_shipments); //sort_valid_shipments;
                    }
                    $serial = 1;
                    foreach ($valid_shipments as $index => $shipment) {
                        DeliveryNoteShipment::create([
                            'delivery_note_id' => $note->id,
                            'shipment_id' => $shipment,
                            'notification' => 0,
                            'rider_information' => 0,
                            'ordering' => $serial
                        ]);
                        $serial++;
                    }

                    foreach ($valid_shipments as $index => $shipment) {
                        $shipment_data = Shipment::find($shipment);
                        $shipment_data->shipper_status_id = 20;
                        $shipment_data->consignee_status_id = 20;
                        $shipment_data->save();

                        ShipmentsJourneyController::add($shipment, 5, 5, NULL, NULL, NULL, 50, $note->id, $rider_id);
                        ShipmentsJourneyController::add($shipment, 12,12, 34, NULL, NULL, 50, $note->id, NULL, 0);
                        ShipmentsJourneyController::add($shipment, 20, 20, 34, NULL, NULL, 50, $note->id, NULL, 1);

                        DeliveryNoteShipment::where(['delivery_note_id' => $note->id, 'shipment_id' => $shipment_data->id])->update(['status' => 1]);
                    }

                    DeliveryNote::where('id', $note->id)->update(['delivered_shipments' => 0, 'verified_by' => 50, 'received_cod_amount' => 0, 'status' => 1, 'last_updated_at' => Carbon::now(), 'status_verified_at' => Carbon::now()]);
                }

                $note = ReturnNote::create(['hub_id' => 202, 'rider_id' => 274, 'route_id' => 2, 'shipments_count' => $shipments_count, 'admin_id' => 50]);
                if ($note) {
                    foreach ($valid_shipments as $shipment_id) {
                        $shipment = Shipment::where('id', $shipment_id);

                        $shipment = $shipment->first();
                        ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                        $shipment->shipper_status_id = 23;
                        $shipment->consignee_status_id = 23;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, 50, $note->id, $rider_id);
                    }
                }
            }
        }
    }
}
