<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\V2Pickup\V2AdminPickupsController;
use App\Http\Models\Admin\Retail\OtherParcelReceiving;
use App\Http\Models\Admin\Retail\OtherParcelReceivingShipment;
use App\Http\Models\Admin\Retail\OtherRetailShipment;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\Shipment;
use App\Http\Models\WMS\WmsCurrentStock;
use App\Http\Models\WMS\WmsPendingPicking;
use App\Http\Models\WMS\WmsShipmentProduct;
use App\Http\Requests\BulkShipmentCancelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperOrderManagementApiController extends Controller
{
   public  function order_list(Request $request)
   {

       $order_list = Shipment::join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
           ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
           ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
           ->join('cities as oc', 'usi.city_id', '=', 'oc.id')
           ->join('cities as dc', 'shipments.consignee_city_id', '=', 'dc.id')
           ->leftJoin('shipment_payment_status as sps', 'sps.id', '=', 'shipments.payment_status_id')
           ->leftJoin('shipments_journey as sj', function ($join) {
               $join->on('sj.shipment_id', '=', 'shipments.id')
                   ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 1)'));
           })
           ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'sj.status_reason_id')
           ->select([
               'shipments.id as shipment_id',
               'shipments.tracking_number',
               'shipments.order_id',
               'shipments.booked_by',
               'shipments.shipping_mode_id',
               'sm.mode as service_type',
               'shipments.shipper_status_id',
               'ss.name as status',
               'sj.status_reason_id',
               'ssr.name as reason',
               'shipments.payment_status_id',
               'sps.name as payment_status',
               'usi.city_id as origin_id',
               'oc.name as origin',
               'shipments.consignee_city_id',
               'dc.name as destination',
               'shipments.consignee_name',
               'shipments.consignee_phone_number_1',
               'shipments.amount as collection_amount',
               'shipments.pickup_date as booking_date',
               'sj.remarks as cancellation_remarks',
           ])
           ->where('shipments.user_id',$request->shipper_id)
           ->groupBy('shipments.id')
           ->orderBy('shipments.id', 'desc')
           ->get();

       if($order_list->isNotEmpty()) {
           return response()->json(['status' => 0 , 'message' => 'Success' ,'order_list'=>$order_list]);
       }
       return response()->json(['status' => 1 , 'message' => 'Shipments Order not found!']);



//       with('shipping_mode','status_shipper','payment_status')
   }

    public function order_cancel_all(BulkShipmentCancelRequest $request)
    {

        $correct = FALSE;
        $reason = $request->reason;
        $app_type = $request->app_type;
        $user_id = $app_type == 2 ? $request->retail_user_id : $request->shipper_id;
        $shipment_ids = explode(',', $request->input('shipment_ids'));

        if(count($shipment_ids) > 0) {
            foreach ($shipment_ids as $id) {
                $shipment = Shipment::where('id', $id)->where('user_id', $user_id);
                if ($shipment->exists()) {
                    $shipment = $shipment->first();

                    if ($shipment->shipper_status_id == 1 && $shipment->shipment_type == 1) {

                        $other_retail_shipment = OtherRetailShipment::where('shipment_id',$id);
                        if ($other_retail_shipment->exists()) {
                            $other_retail_shipment = $other_retail_shipment->first();
                            $other_retail_shipment->delete();
                        }
                        $other_parcel_receiving_shipment = OtherParcelReceivingShipment::where('shipment_id', $id);
                        if ($other_parcel_receiving_shipment->exists()) {
                            $other_parcel_receiving_shipment = $other_parcel_receiving_shipment->get()->first();
                            $other_parcel_receiving_id = $other_parcel_receiving_shipment->other_parcel_receiving_id;
                            $other_parcel_receiving_shipment->delete();
                            $other_parcel_receiving = OtherParcelReceiving::find($other_parcel_receiving_id);
                            $other_parcel_receiving->total_cn = $other_parcel_receiving->total_cn-1;
                            $other_parcel_receiving->save();
                            if($other_parcel_receiving->total_cn < 1){
                                $other_parcel_receiving->total_cn = 0;
                                $other_parcel_receiving->save();
                            }
                        }
                        if($shipment->warehouse == 1){
                            continue;
                        }
                        //Consolidated Shipments
                        $consolidated_shipment = ConsolidationShipments::where('shipment_id', $shipment->id)->first();
                        if($consolidated_shipment){
                            $consolidation_id = $consolidated_shipment->consolidation_id;
                            ConsolidationShipments::where('id', $consolidated_shipment->id)->delete();
                            $remaining_consolidated_shipments = ConsolidationShipments::where('consolidation_id', $consolidation_id)->get();

                            if(count($remaining_consolidated_shipments) == 1){
                                ConsolidationShipments::where('consolidation_id', $consolidation_id)->delete();
                                Consolidation::where('id', $consolidation_id)->delete();
                            }
                            else{
                                foreach ($remaining_consolidated_shipments as $index => $remaining_consolidated_shipment){
                                    $new_order_consolidated_shipment = ConsolidationShipments::find($remaining_consolidated_shipment->id);
                                    $new_order_consolidated_shipment->order = $index + 1;
                                    $new_order_consolidated_shipment->save();
                                }
                                $consolidation = Consolidation::find($consolidation_id);
                                $consolidation->count = count($remaining_consolidated_shipments);
                                if($consolidation->default_shipment_id == $shipment->id){
                                    $consolidation->default_shipment_id = $remaining_consolidated_shipments[0]->shipment_id;
                                }
                                $consolidation->save();
                            }
                        }
                        //Consolidated Shipments
                        $shipment->shipper_status_id = 17;
                        $shipment->consignee_status_id = 17;
                        ShipmentsJourneyController::add($shipment->id, 17, 17, NULL, 'Cancelled by Shipper ' .'- '. $reason, $user_id, NULL);
                        $packaging_material = PackagingMaterialRequest::where('tracking_number', $shipment->tracking_number)->first();
                        if($packaging_material){
                            $packaging_material->status_id = 6;
                            $packaging_material->save();
                        }
                        if($shipment->warehouse == 1){
                            $shipment->warehouse_order_status = 9;
                            $shipment_products = WmsShipmentProduct::where('shipment_id', $shipment->id)->get();
                            if($shipment_products){
                                foreach ($shipment_products as $shipment_product){
                                    $pending_pickings_products = WmsPendingPicking::leftjoin('wms_pending_picking_shipments as wpps', 'wpps.picking_id', '=', 'wms_pending_pickings.id')
                                        ->select('wms_pending_pickings.id as id')
                                        ->where('wpps.shipment_id', $shipment->id)
                                        ->where('wms_pending_pickings.product_id', $shipment_product->product_id)->first();
                                    if($pending_pickings_products){
                                        $pending_picking = WmsPendingPicking::find($pending_pickings_products->id);
                                        $pending_picking->quantity = $pending_picking->quantity - $shipment_product->quantity;
                                        $pending_picking->save();

                                        $current_stock_addition = WmsCurrentStock::where('product_id', $shipment_product->product_id)->where('warehouse_pickup_address_id', $shipment->pickup_address_id)->first();
                                        if($current_stock_addition){
                                            $current_stock_addition->stock = $current_stock_addition->stock + $shipment_product->quantity;
                                            $current_stock_addition->save();
                                        }

                                        if($pending_picking->quantity <= 0){
                                            $pending_picking->status = 1;
                                            $pending_picking->save();
                                        }
                                    }
                                }
                            }
                        }

                        $shipment->save();

                        V2AdminPickupsController::cancel($shipment->id);

                        $correct = TRUE;
                    }
                }

            }
        }
        if ($correct) {
            return response()->json(['status' => 1, 'success' => 'Shipment(s) has been cancelled successfully']);
        }
        else {
            return response()->json(['status' => 0, 'error' => 'No Shipment could be cancelled']);
        }
    }

}
