<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Models\Admin\Retail\RetailCashDeposit;
use App\Http\Models\Admin\Retail\RetailCashDepositShipment;
use App\Http\Models\Admin\Retail\RetailParcelReceiving;
use App\Http\Models\Admin\Retail\RetailParcelReceivingShipment;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\RetailPickupNote;
use App\Http\Models\Admin\RetailPickupNoteShipment;
use App\Http\Models\Admin\RetailPickupNoteStatus;
use Auth;

class RetailCancelShipmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');
    }
    public function add_index(){
        return view('retail.cancelled_shipments.add_index');
    }

    public function get_shipment_info(Request $request)
    {
        $tracking_number = $request->tracking_number;
        if ($tracking_number != '') {
            $shipment = Shipment::whereHas('retail', function ($query) {
                $query->where('retail_user_id', Auth::id());
            })->where('tracking_number', $tracking_number)->where('shipper_status_id', 1)->where('shipment_type', 2);
            if ($shipment->exists()) {
                $data = array();
                $shipment = $shipment->first();

                $data['id'] = $shipment->id;
                $data['tracking_number'] = $shipment->tracking_number;
                $data['shipper_name'] = $shipment->user->name.' (' . $shipment->pickup_address->poc . ')';
                $data['origin'] = $shipment->consignee_city->name;
                $data['destination'] = $shipment->pickup_address->city->name;
                $data['hub'] = $shipment->pickup_address->city->hub_city->name;
                $data['amount'] = number_format($shipment->amount);
                $data['mode'] = $shipment->shipping_mode->mode;
                $data['service_type'] = $shipment->booking_type->booking_type;

                ShipmentScanningJourneyController::add($shipment->id, 19, 4, Auth::id(), null,null);
                return response()->json(['status' => 1, 'details' => $data]);

            } else {
                return response()->json(['status' => 0, 'error' => 'Shipment can not be Cancelled!']);
            }

        }
        return response()->json(['status' => 0, 'error' => 'Tracking number empty!']);

    }

    public function cancelled_shipments_store(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        $shipments_count = count($shipment_ids);

        if($shipments_count > 0){

            $shipments = Shipment::where('shipper_status_id', 1)->whereIn('id', $shipment_ids);

            if ($shipments->exists()) {
                foreach ($shipments->get() as $shipment) {


                    $shipment->shipper_status_id = 17;
                    $shipment->consignee_status_id = 17;
                    $shipment->save();

                    ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, NULL, NULL, Auth::id());

                    ShipmentsPickupJourneyController::add($shipment->id, 4);
                    V2AdminPickupsController::cancel($shipment->id);

                    $total_deductable_amount = 0;
                    $retail_cash_deposit_shipment = RetailCashDepositShipment::where('shipment_id', $shipment->id);
                    if($retail_cash_deposit_shipment->exists()){
                        $retail_cash_deposit_shipment = $retail_cash_deposit_shipment->first();
                        $cash_deposit_id = $retail_cash_deposit_shipment->cash_deposit_id;
                        $retail_cash_deposit_shipment->delete();
                        $retais_cash_deposit = RetailCashDeposit::find($cash_deposit_id);

                        $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                        if($retail_shipment->exists()){
                            $retail_shipment = $retail_shipment->first();
                            $total_deductable_amount = $retail_shipment->total_charges;
                            $retais_cash_deposit->total_cn = $retais_cash_deposit->total_cn - 1;
                            $retais_cash_deposit->total_cash = $retais_cash_deposit->total_cash - $total_deductable_amount;
                            $retais_cash_deposit->save();
                        }

                    }

                    $retail_parcel_receiving_shipment = RetailParcelReceivingShipment::where('shipment_id', $shipment->id);
                    if($retail_parcel_receiving_shipment->exists()){
                        $retail_parcel_receiving_shipment = $retail_parcel_receiving_shipment->latest()->first();
                        $parcel_receiving_id = $retail_parcel_receiving_shipment->parcel_receiving_id;
                        $retail_parcel_receiving_shipment->delete();
                        $parcel_receiving = RetailParcelReceiving::find($parcel_receiving_id);
                        $parcel_receiving->total_cn = $parcel_receiving->total_cn - 1;
                        $parcel_receiving->total_cash = $parcel_receiving->total_cash - $total_deductable_amount;
                        $parcel_receiving->save();
                    }
                    $retail_pickup_note_shipments = RetailPickupNoteShipment::where('shipment_id',$shipment->id);
                    if($retail_pickup_note_shipments->exists()){
                        $retail_pickup_note_shipments = $retail_pickup_note_shipments->latest()->first();
                        $retail_pickup_note_id = $retail_pickup_note_shipments->retail_pickup_note_id;
                        $retail_pickup_note_shipments->delete();

                        $retail_pickup_note = RetailPickupNote::find($retail_pickup_note_id);
                        $retail_pickup_note->shipments = $retail_pickup_note->shipments - 1;
                        $retail_pickup_note->amount = $retail_pickup_note->amount - $total_deductable_amount;
                        $retail_pickup_note->save();
                    }

                }
                return redirect()->back()->with('success', 'Shipment(s) cancelled successfully!');
            }
            else{
                return redirect()->back()->with('error', 'Shipment(s) can not be cancelled!');
            }
        }
    }
}
