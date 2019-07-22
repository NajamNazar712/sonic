<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Operataions\OperationForecast;
use App\Http\Models\Operataions\OperationForecastShipments;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOperationForecastController extends Controller
{
    static public function update_operation_forecast(){
        $to =Carbon::now();
        $from =Carbon::now()->startOfDay();
        $shipment_count['booked'] = 0;
        $shipment_count['arrived_at_origin'] = 0;
        $shipment_count['in_transit'] = 0;
        $shipment_count['arrived_at_destination'] = 0;
        $shipment_count['not_attempted'] = 0;
        $shipment_count['delivery_unsuccessful'] = 0;
        $shipment_count['on_hold'] = 0;
        $shipments = Shipment::whereIn('shipper_status_id', [1, 2, 3, 4, 7, 8, 9])->whereBetween('updated_at', [$from, $to])->get();
        foreach($shipments as $shipment){
            if ($shipment->shipper_status_id == 1){
                $shipment_count['booked'] = $shipment_count['booked'] + 1;
            }
            else if ($shipment->shipper_status_id == 2){
                $shipment_count['arrived_at_origin'] = $shipment_count['arrived_at_origin'] + 1;
            }
            else if  ($shipment->shipper_status_id == 3){
                $shipment_count['in_transit'] = $shipment_count['in_transit'] + 1;
            }
            else if  ($shipment->shipper_status_id == 4){
                $shipment_count['arrived_at_destination'] = $shipment_count['arrived_at_destination'] + 1;
            }
            else if  ($shipment->shipper_status_id == 7){
                $shipment_count['not_attempted'] = $shipment_count['not_attempted'] + 1;
            }
            else if  ($shipment->shipper_status_id == 8){
                $shipment_count['delivery_unsuccessful'] = $shipment_count['delivery_unsuccessful'] + 1;
            }
            else if  ($shipment->shipper_status_id == 9){
                $shipment_count['on_hold'] = $shipment_count['on_hold'] + 1;
            }
        }
        $operation_forecast['booked'] = OperationForecast::where('shipper_status_id', 1)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['booked']->exists()){
            $new_operation_forecast['booked'] = $operation_forecast['booked']->first();

            OperationForecast::where('shipper_status_id', 1)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['booked']
            ]);
        }
        else{
            $new_operation_forecast['booked'] = new OperationForecast();
            $new_operation_forecast['booked']->shipper_status_id = 1;
            $new_operation_forecast['booked']->count = $shipment_count['booked'];
            $new_operation_forecast['booked']->save();
        }
        $operation_forecast['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['arrived_at_origin']->exists()){
            $new_operation_forecast['arrived_at_origin'] = $operation_forecast['arrived_at_origin']->first();

            OperationForecast::where('shipper_status_id', 2)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['arrived_at_origin']
            ]);
        }
        else{
            $new_operation_forecast['arrived_at_origin'] = new OperationForecast();
            $new_operation_forecast['arrived_at_origin']->shipper_status_id = 2;
            $new_operation_forecast['arrived_at_origin']->count = $shipment_count['arrived_at_origin'];
            $new_operation_forecast['arrived_at_origin']->save();
        }
        $operation_forecast['in_transit'] = OperationForecast::where('shipper_status_id', 3)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['in_transit']->exists()){
            $new_operation_forecast['in_transit'] = $operation_forecast['in_transit']->first();

            OperationForecast::where('shipper_status_id', 3)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['in_transit']
            ]);
        }
        else{
            $new_operation_forecast['in_transit'] = new OperationForecast();
            $new_operation_forecast['in_transit']->shipper_status_id = 3;
            $new_operation_forecast['in_transit']->count = $shipment_count['in_transit'];
            $new_operation_forecast['in_transit']->save();
        }
        $operation_forecast['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['arrived_at_destination']->exists()){
            $new_operation_forecast['arrived_at_destination'] = $operation_forecast['arrived_at_destination']->first();

            OperationForecast::where('shipper_status_id', 4)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['arrived_at_destination']
            ]);
        }
        else{
            $new_operation_forecast['arrived_at_destination'] = new OperationForecast();
            $new_operation_forecast['arrived_at_destination']->shipper_status_id = 4;
            $new_operation_forecast['arrived_at_destination']->count = $shipment_count['arrived_at_destination'];
            $new_operation_forecast['arrived_at_destination']->save();
        }
        $operation_forecast['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['not_attempted']->exists()){
            $new_operation_forecast['not_attempted'] = $operation_forecast['not_attempted']->first();

            OperationForecast::where('shipper_status_id', 7)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['not_attempted']
            ]);
        }
        else{
            $new_operation_forecast['not_attempted'] = new OperationForecast();
            $new_operation_forecast['not_attempted']->shipper_status_id = 7;
            $new_operation_forecast['not_attempted']->count = $shipment_count['not_attempted'];
            $new_operation_forecast['not_attempted']->save();
        }
        $operation_forecast['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['delivery_unsuccessful']->exists()){
            $new_operation_forecast['delivery_unsuccessful'] = $operation_forecast['delivery_unsuccessful']->first();

            OperationForecast::where('shipper_status_id', 8)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['delivery_unsuccessful']
            ]);
        }
        else{
            $new_operation_forecast['delivery_unsuccessful'] = new OperationForecast();
            $new_operation_forecast['delivery_unsuccessful']->shipper_status_id = 8;
            $new_operation_forecast['delivery_unsuccessful']->count = $shipment_count['delivery_unsuccessful'];
            $new_operation_forecast['delivery_unsuccessful']->save();
        }
        $operation_forecast['on_hold'] = OperationForecast::where('shipper_status_id', 9)->whereBetween('updated_at', [$from, $to]);
        if($operation_forecast['on_hold']->exists()){
            $new_operation_forecast['on_hold'] = $operation_forecast['on_hold']->first();

            OperationForecast::where('shipper_status_id', 9)->whereBetween('updated_at', [$from, $to])->update([
                'count' => $shipment_count['on_hold']
            ]);
        }
        else{
            $new_operation_forecast['on_hold'] = new OperationForecast();
            $new_operation_forecast['on_hold']->shipper_status_id = 9;
            $new_operation_forecast['on_hold']->count = $shipment_count['on_hold'];
            $new_operation_forecast['on_hold']->save();
        }

        OperationForecastShipments::whereBetween('created_at', [$from, $to])->delete();

        foreach($shipments as $shipment){
            if($shipment->actual_weight != null){
                if($shipment->actual_weight <= 0.5){
                    $weight_range_id = 1;
                }
                else if ($shipment->actual_weight > 0.5 && $shipment->actual_weight <= 2){
                    $weight_range_id = 2;
                }
                else if ($shipment->actual_weight > 2 && $shipment->actual_weight <= 5){
                    $weight_range_id = 3;
                }
                else {
                    $weight_range_id = 4;
                }
            }
            else{
                if($shipment->estimated_weight <= 0.5){
                    $weight_range_id = 1;
                }
                else if ($shipment->estimated_weight > 0.5 && $shipment->estimated_weight <= 2){
                    $weight_range_id = 2;
                }
                else if ($shipment->estimated_weight > 2 && $shipment->estimated_weight <= 5){
                    $weight_range_id = 3;
                }
                else {
                    $weight_range_id = 4;
                }
            }
            if ($shipment->shipper_status_id == 1){
                $new_operation_forecast_shipments['booked'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['booked']->operation_forecast_id = $new_operation_forecast['booked']->id;
                $new_operation_forecast_shipments['booked']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['booked']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['booked']->save();

            }
            if ($shipment->shipper_status_id == 2){
                $new_operation_forecast_shipments['arrived_at_origin'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['arrived_at_origin']->operation_forecast_id = $new_operation_forecast['arrived_at_origin']->id;
                $new_operation_forecast_shipments['arrived_at_origin']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['arrived_at_origin']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['arrived_at_origin']->save();
            }
            if  ($shipment->shipper_status_id == 3){
                $new_operation_forecast_shipments['in_transit'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['in_transit']->operation_forecast_id = $new_operation_forecast['in_transit']->id;
                $new_operation_forecast_shipments['in_transit']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['in_transit']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['in_transit']->save();
            }
            if  ($shipment->shipper_status_id == 4){
                $new_operation_forecast_shipments['arrived_at_destination'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['arrived_at_destination']->operation_forecast_id = $new_operation_forecast['arrived_at_destination']->id;
                $new_operation_forecast_shipments['arrived_at_destination']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['arrived_at_destination']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['arrived_at_destination']->save();
            }
            if  ($shipment->shipper_status_id == 7){
                $new_operation_forecast_shipments['not_attempted'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['not_attempted']->operation_forecast_id = $new_operation_forecast['not_attempted']->id;
                $new_operation_forecast_shipments['not_attempted']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['not_attempted']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['not_attempted']->save();
            }
            if  ($shipment->shipper_status_id == 8){
                $new_operation_forecast_shipments['delivery_unsuccessful'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['delivery_unsuccessful']->operation_forecast_id = $new_operation_forecast['delivery_unsuccessful']->id;
                $new_operation_forecast_shipments['delivery_unsuccessful']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['delivery_unsuccessful']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['delivery_unsuccessful']->save();
            }
            if  ($shipment->shipper_status_id == 9){
                $new_operation_forecast_shipments['on_hold'] = new OperationForecastShipments();
                $new_operation_forecast_shipments['on_hold']->operation_forecast_id = $new_operation_forecast['on_hold']->id;
                $new_operation_forecast_shipments['on_hold']->weight_range_id = $weight_range_id;
                $new_operation_forecast_shipments['on_hold']->shipment_id = $shipment->id;
                $new_operation_forecast_shipments['on_hold']->save();
            }
        }
    }
}
