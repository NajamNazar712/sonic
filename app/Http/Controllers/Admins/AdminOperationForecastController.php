<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\City;
use App\Http\Models\Operataions\OperationForecast;
use App\Http\Models\Operataions\OperationForecastShipments;
use App\Http\Models\Operataions\OperationsForecastLastUpdatedTime;
use App\Http\Models\Operataions\OperationsOutgoingPickupRequests;
use App\Http\Models\Operataions\OperationsOutgoingPickupRequestShipments;
use App\Http\Models\Operataions\OperationsOutgoingTopCustomers;
use App\Http\Models\Operataions\OperationsOutgoingTopCustomersShipments;
use App\Http\Models\PickupRequest;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminOperationForecastController extends Controller
{
    static public function update_operation_forecast()
    {
        $to = Carbon::now();
        $from = Carbon::now()->startOfDay();
        $hubs = City::get();
        foreach ($hubs as $hub) {
            $shipment_count[$hub->id]['regular']['booked'] = 0;
            $shipment_count[$hub->id]['regular']['arrived_at_origin'] = 0;
            $shipment_count[$hub->id]['regular']['in_transit'] = 0;
            $shipment_count[$hub->id]['regular']['arrived_at_destination'] = 0;
            $shipment_count[$hub->id]['regular']['not_attempted'] = 0;
            $shipment_count[$hub->id]['regular']['delivery_unsuccessful'] = 0;
            $shipment_count[$hub->id]['regular']['on_hold'] = 0;
            $shipment_count[$hub->id]['replacement']['booked'] = 0;
            $shipment_count[$hub->id]['replacement']['arrived_at_origin'] = 0;
            $shipment_count[$hub->id]['replacement']['in_transit'] = 0;
            $shipment_count[$hub->id]['replacement']['arrived_at_destination'] = 0;
            $shipment_count[$hub->id]['replacement']['not_attempted'] = 0;
            $shipment_count[$hub->id]['replacement']['delivery_unsuccessful'] = 0;
            $shipment_count[$hub->id]['replacement']['on_hold'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['booked'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['arrived_at_origin'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['in_transit'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['arrived_at_destination'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['not_attempted'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['delivery_unsuccessful'] = 0;
            $shipment_count[$hub->id]['try_and_buy']['on_hold'] = 0;
            $shipment_count[$hub->id]['walk_in']['booked'] = 0;
            $shipment_count[$hub->id]['walk_in']['arrived_at_origin'] = 0;
            $shipment_count[$hub->id]['walk_in']['in_transit'] = 0;
            $shipment_count[$hub->id]['walk_in']['arrived_at_destination'] = 0;
            $shipment_count[$hub->id]['walk_in']['not_attempted'] = 0;
            $shipment_count[$hub->id]['walk_in']['delivery_unsuccessful'] = 0;
            $shipment_count[$hub->id]['walk_in']['on_hold'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['booked'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['arrived_at_origin'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['in_transit'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['arrived_at_destination'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['not_attempted'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['delivery_unsuccessful'] = 0;
            $shipment_count[$hub->id]['reverse_pickup']['on_hold'] = 0;
        }
        $shipments = Shipment::whereIn('shipper_status_id', [1, 2, 3, 4, 7, 8, 9])->whereBetween('updated_at', [$from, $to])->get();
        foreach ($shipments as $shipment) {
            if ($shipment->shipper_status_id == 1) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['booked'] = $shipment_count[$shipment->consignee_city_id]['regular']['booked'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['booked'] = $shipment_count[$shipment->consignee_city_id]['replacement']['booked'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['booked'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['booked'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['booked'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['booked'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['booked'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['booked'] + 1;
                }
            } else if ($shipment->shipper_status_id == 2) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['arrived_at_origin'] = $shipment_count[$shipment->consignee_city_id]['regular']['arrived_at_origin'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['arrived_at_origin'] = $shipment_count[$shipment->consignee_city_id]['replacement']['arrived_at_origin'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['arrived_at_origin'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['arrived_at_origin'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['arrived_at_origin'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['arrived_at_origin'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_origin'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_origin'] + 1;
                }
            } else if ($shipment->shipper_status_id == 3) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['in_transit'] = $shipment_count[$shipment->consignee_city_id]['regular']['in_transit'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['in_transit'] = $shipment_count[$shipment->consignee_city_id]['replacement']['in_transit'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['in_transit'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['in_transit'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['in_transit'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['in_transit'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['in_transit'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_origin'] + 1;
                }
            } else if ($shipment->shipper_status_id == 4) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['arrived_at_destination'] = $shipment_count[$shipment->consignee_city_id]['regular']['arrived_at_destination'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['arrived_at_destination'] = $shipment_count[$shipment->consignee_city_id]['replacement']['arrived_at_destination'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['arrived_at_destination'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['arrived_at_destination'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['arrived_at_destination'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['arrived_at_destination'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_destination'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_destination'] + 1;
                }
            } else if ($shipment->shipper_status_id == 7) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['not_attempted'] = $shipment_count[$shipment->consignee_city_id]['regular']['not_attempted'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['not_attempted'] = $shipment_count[$shipment->consignee_city_id]['replacement']['not_attempted'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['not_attempted'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['not_attempted'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['not_attempted'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['not_attempted'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['not_attempted'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['not_attempted'] + 1;
                }
            } else if ($shipment->shipper_status_id == 8) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['delivery_unsuccessful'] = $shipment_count[$shipment->consignee_city_id]['regular']['delivery_unsuccessful'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['delivery_unsuccessful'] = $shipment_count[$shipment->consignee_city_id]['replacement']['delivery_unsuccessful'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['delivery_unsuccessful'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['delivery_unsuccessful'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['delivery_unsuccessful'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['delivery_unsuccessful'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['delivery_unsuccessful'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['delivery_unsuccessful'] + 1;
                }
            } else if ($shipment->shipper_status_id == 9) {
                if ($shipment->booking_type_id == 1) {
                    $shipment_count[$shipment->consignee_city_id]['regular']['on_hold'] = $shipment_count[$shipment->consignee_city_id]['regular']['on_hold'] + 1;
                } elseif ($shipment->booking_type_id == 2) {
                    $shipment_count[$shipment->consignee_city_id]['replacement']['on_hold'] = $shipment_count[$shipment->consignee_city_id]['replacement']['on_hold'] + 1;
                } elseif ($shipment->booking_type_id == 3) {
                    $shipment_count[$shipment->consignee_city_id]['try_and_buy']['on_hold'] = $shipment_count[$shipment->consignee_city_id]['try_and_buy']['on_hold'] + 1;
                } elseif ($shipment->booking_type_id == 4) {
                    $shipment_count[$shipment->consignee_city_id]['walk_in']['on_hold'] = $shipment_count[$shipment->consignee_city_id]['walk_in']['on_hold'] + 1;
                } elseif ($shipment->booking_type_id == 5) {
                    $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['on_hold'] = $shipment_count[$shipment->consignee_city_id]['reverse_pickup']['on_hold'] + 1;
                }
            }
        }
        foreach ($hubs as $hub) {
            if (array_key_exists($hub->id, $shipment_count)) {
                if (array_key_exists('regular', $shipment_count[$hub->id])) {
                    $operation_forecast[$hub->id]['regular']['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['booked']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['booked'] = $operation_forecast[$hub->id]['regular']['booked']->first();
                        $new_operation_forecast[$hub->id]['regular']['booked']->count = $shipment_count[$hub->id]['regular']['booked'];
                        $new_operation_forecast[$hub->id]['regular']['booked']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['booked'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['booked'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['booked']->shipper_status_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['booked']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['booked']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['booked']->count = $shipment_count[$hub->id]['regular']['booked'];
                            $new_operation_forecast[$hub->id]['regular']['booked']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['regular']['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['arrived_at_origin']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['arrived_at_origin'] = $operation_forecast[$hub->id]['regular']['arrived_at_origin']->first();
                        $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->count = $shipment_count[$hub->id]['regular']['arrived_at_origin'];
                        $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['arrived_at_origin'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_origin'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->shipper_status_id = 2;
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->count = $shipment_count[$hub->id]['regular']['arrived_at_origin'];
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_origin']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['regular']['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['in_transit']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['in_transit'] = $operation_forecast[$hub->id]['regular']['in_transit']->first();
                        $new_operation_forecast[$hub->id]['regular']['in_transit']->count = $shipment_count[$hub->id]['regular']['in_transit'];
                        $new_operation_forecast[$hub->id]['regular']['in_transit']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['in_transit'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['in_transit'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['in_transit']->shipper_status_id = 3;
                            $new_operation_forecast[$hub->id]['regular']['in_transit']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['in_transit']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['in_transit']->count = $shipment_count[$hub->id]['regular']['in_transit'];
                            $new_operation_forecast[$hub->id]['regular']['in_transit']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['regular']['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['arrived_at_destination']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['arrived_at_destination'] = $operation_forecast[$hub->id]['regular']['arrived_at_destination']->first();
                        $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->count = $shipment_count[$hub->id]['regular']['arrived_at_destination'];
                        $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['arrived_at_destination'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_destination'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->shipper_status_id = 4;
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->count = $shipment_count[$hub->id]['regular']['arrived_at_destination'];
                            $new_operation_forecast[$hub->id]['regular']['arrived_at_destination']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['regular']['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['not_attempted']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['not_attempted'] = $operation_forecast[$hub->id]['regular']['not_attempted']->first();
                        $new_operation_forecast[$hub->id]['regular']['not_attempted']->count = $shipment_count[$hub->id]['regular']['not_attempted'];
                        $new_operation_forecast[$hub->id]['regular']['not_attempted']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['not_attempted'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['not_attempted'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['not_attempted']->shipper_status_id = 7;
                            $new_operation_forecast[$hub->id]['regular']['not_attempted']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['not_attempted']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['not_attempted']->count = $shipment_count[$hub->id]['regular']['not_attempted'];
                            $new_operation_forecast[$hub->id]['regular']['not_attempted']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['regular']['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful'] = $operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->first();
                        $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['regular']['delivery_unsuccessful'];
                        $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['delivery_unsuccessful'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->shipper_status_id = 8;
                            $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['regular']['delivery_unsuccessful'];
                            $new_operation_forecast[$hub->id]['regular']['delivery_unsuccessful']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['regular']['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub->id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['regular']['on_hold']->exists()) {
                        $new_operation_forecast[$hub->id]['regular']['on_hold'] = $operation_forecast[$hub->id]['regular']['on_hold']->first();
                        $new_operation_forecast[$hub->id]['regular']['on_hold']->count = $shipment_count[$hub->id]['regular']['on_hold'];
                        $new_operation_forecast[$hub->id]['regular']['on_hold']->save();
                    } else {
                        if ($shipment_count[$hub->id]['regular']['on_hold'] > 0) {
                            $new_operation_forecast[$hub->id]['regular']['on_hold'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['regular']['on_hold']->shipper_status_id = 9;
                            $new_operation_forecast[$hub->id]['regular']['on_hold']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['regular']['on_hold']->booking_type_id = 1;
                            $new_operation_forecast[$hub->id]['regular']['on_hold']->count = $shipment_count[$hub->id]['regular']['on_hold'];
                            $new_operation_forecast[$hub->id]['regular']['on_hold']->save();
                        }
                    }
                }
                if (array_key_exists('replacement', $shipment_count[$hub->id])) {
                    $operation_forecast[$hub->id]['replacement']['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['booked']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['booked'] = $operation_forecast[$hub->id]['replacement']['booked']->first();
                        $new_operation_forecast[$hub->id]['replacement']['booked']->count = $shipment_count[$hub->id]['replacement']['booked'];
                        $new_operation_forecast[$hub->id]['replacement']['booked']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['booked'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['booked'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['booked']->shipper_status_id = 1;
                            $new_operation_forecast[$hub->id]['replacement']['booked']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['booked']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['booked']->count = $shipment_count[$hub->id]['replacement']['booked'];
                            $new_operation_forecast[$hub->id]['replacement']['booked']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['replacement']['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['arrived_at_origin']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin'] = $operation_forecast[$hub->id]['replacement']['arrived_at_origin']->first();
                        $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->count = $shipment_count[$hub->id]['replacement']['arrived_at_origin'];
                        $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['arrived_at_origin'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->shipper_status_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->count = $shipment_count[$hub->id]['replacement']['arrived_at_origin'];
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_origin']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['replacement']['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['in_transit']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['in_transit'] = $operation_forecast[$hub->id]['replacement']['in_transit']->first();
                        $new_operation_forecast[$hub->id]['replacement']['in_transit']->count = $shipment_count[$hub->id]['replacement']['in_transit'];
                        $new_operation_forecast[$hub->id]['replacement']['in_transit']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['in_transit'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['in_transit'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['in_transit']->shipper_status_id = 3;
                            $new_operation_forecast[$hub->id]['replacement']['in_transit']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['in_transit']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['in_transit']->count = $shipment_count[$hub->id]['replacement']['in_transit'];
                            $new_operation_forecast[$hub->id]['replacement']['in_transit']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['replacement']['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['arrived_at_destination']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination'] = $operation_forecast[$hub->id]['replacement']['arrived_at_destination']->first();
                        $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->count = $shipment_count[$hub->id]['replacement']['arrived_at_destination'];
                        $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['arrived_at_destination'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->shipper_status_id = 4;
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->count = $shipment_count[$hub->id]['replacement']['arrived_at_destination'];
                            $new_operation_forecast[$hub->id]['replacement']['arrived_at_destination']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['replacement']['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['not_attempted']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['not_attempted'] = $operation_forecast[$hub->id]['replacement']['not_attempted']->first();
                        $new_operation_forecast[$hub->id]['replacement']['not_attempted']->count = $shipment_count[$hub->id]['replacement']['not_attempted'];
                        $new_operation_forecast[$hub->id]['replacement']['not_attempted']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['not_attempted'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['not_attempted'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['not_attempted']->shipper_status_id = 7;
                            $new_operation_forecast[$hub->id]['replacement']['not_attempted']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['not_attempted']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['not_attempted']->count = $shipment_count[$hub->id]['replacement']['not_attempted'];
                            $new_operation_forecast[$hub->id]['replacement']['not_attempted']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['replacement']['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful'] = $operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->first();
                        $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['replacement']['delivery_unsuccessful'];
                        $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['delivery_unsuccessful'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->shipper_status_id = 8;
                            $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['replacement']['delivery_unsuccessful'];
                            $new_operation_forecast[$hub->id]['replacement']['delivery_unsuccessful']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['replacement']['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub->id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['replacement']['on_hold']->exists()) {
                        $new_operation_forecast[$hub->id]['replacement']['on_hold'] = $operation_forecast[$hub->id]['replacement']['on_hold']->first();
                        $new_operation_forecast[$hub->id]['replacement']['on_hold']->count = $shipment_count[$hub->id]['replacement']['on_hold'];
                        $new_operation_forecast[$hub->id]['replacement']['on_hold']->save();
                    } else {
                        if ($shipment_count[$hub->id]['replacement']['on_hold'] > 0) {
                            $new_operation_forecast[$hub->id]['replacement']['on_hold'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['replacement']['on_hold']->shipper_status_id = 9;
                            $new_operation_forecast[$hub->id]['replacement']['on_hold']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['replacement']['on_hold']->booking_type_id = 2;
                            $new_operation_forecast[$hub->id]['replacement']['on_hold']->count = $shipment_count[$hub->id]['replacement']['on_hold'];
                            $new_operation_forecast[$hub->id]['replacement']['on_hold']->save();
                        }
                    }
                }
                if (array_key_exists('try_and_buy', $shipment_count[$hub->id])) {
                    $operation_forecast[$hub->id]['try_and_buy']['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['booked']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['booked'] = $operation_forecast[$hub->id]['try_and_buy']['booked']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['booked']->count = $shipment_count[$hub->id]['try_and_buy']['booked'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['booked']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['booked'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['booked'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['booked']->shipper_status_id = 1;
                            $new_operation_forecast[$hub->id]['try_and_buy']['booked']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['booked']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['booked']->count = $shipment_count[$hub->id]['try_and_buy']['booked'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['booked']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin'] = $operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->count = $shipment_count[$hub->id]['try_and_buy']['arrived_at_origin'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['arrived_at_origin'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->shipper_status_id = 2;
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->count = $shipment_count[$hub->id]['try_and_buy']['arrived_at_origin'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_origin']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['try_and_buy']['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['in_transit']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['in_transit'] = $operation_forecast[$hub->id]['try_and_buy']['in_transit']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->count = $shipment_count[$hub->id]['try_and_buy']['in_transit'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['in_transit'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['in_transit'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->shipper_status_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->count = $shipment_count[$hub->id]['try_and_buy']['in_transit'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['in_transit']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination'] = $operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->count = $shipment_count[$hub->id]['try_and_buy']['arrived_at_destination'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['arrived_at_destination'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->shipper_status_id = 4;
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->count = $shipment_count[$hub->id]['try_and_buy']['arrived_at_destination'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['arrived_at_destination']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['try_and_buy']['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['not_attempted']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted'] = $operation_forecast[$hub->id]['try_and_buy']['not_attempted']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->count = $shipment_count[$hub->id]['try_and_buy']['not_attempted'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['not_attempted'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->shipper_status_id = 7;
                            $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->count = $shipment_count[$hub->id]['try_and_buy']['not_attempted'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['not_attempted']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful'] = $operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['try_and_buy']['delivery_unsuccessful'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['delivery_unsuccessful'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->shipper_status_id = 8;
                            $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['try_and_buy']['delivery_unsuccessful'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['delivery_unsuccessful']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['try_and_buy']['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub->id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['try_and_buy']['on_hold']->exists()) {
                        $new_operation_forecast[$hub->id]['try_and_buy']['on_hold'] = $operation_forecast[$hub->id]['try_and_buy']['on_hold']->first();
                        $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->count = $shipment_count[$hub->id]['try_and_buy']['on_hold'];
                        $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->save();
                    } else {
                        if ($shipment_count[$hub->id]['try_and_buy']['on_hold'] > 0) {
                            $new_operation_forecast[$hub->id]['try_and_buy']['on_hold'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->shipper_status_id = 9;
                            $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->booking_type_id = 3;
                            $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->count = $shipment_count[$hub->id]['try_and_buy']['on_hold'];
                            $new_operation_forecast[$hub->id]['try_and_buy']['on_hold']->save();
                        }
                    }
                }
                if (array_key_exists('walk_in', $shipment_count[$hub->id])) {
                    $operation_forecast[$hub->id]['walk_in']['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['booked']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['booked'] = $operation_forecast[$hub->id]['walk_in']['booked']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['booked']->count = $shipment_count[$hub->id]['walk_in']['booked'];
                        $new_operation_forecast[$hub->id]['walk_in']['booked']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['booked'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['booked'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['booked']->shipper_status_id = 1;
                            $new_operation_forecast[$hub->id]['walk_in']['booked']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['booked']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['booked']->count = $shipment_count[$hub->id]['walk_in']['booked'];
                            $new_operation_forecast[$hub->id]['walk_in']['booked']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['walk_in']['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin'] = $operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->count = $shipment_count[$hub->id]['walk_in']['arrived_at_origin'];
                        $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['arrived_at_origin'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->shipper_status_id = 2;
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->count = $shipment_count[$hub->id]['walk_in']['arrived_at_origin'];
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_origin']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['walk_in']['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['in_transit']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['in_transit'] = $operation_forecast[$hub->id]['walk_in']['in_transit']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['in_transit']->count = $shipment_count[$hub->id]['walk_in']['in_transit'];
                        $new_operation_forecast[$hub->id]['walk_in']['in_transit']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['in_transit'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['in_transit'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['in_transit']->shipper_status_id = 3;
                            $new_operation_forecast[$hub->id]['walk_in']['in_transit']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['in_transit']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['in_transit']->count = $shipment_count[$hub->id]['walk_in']['in_transit'];
                            $new_operation_forecast[$hub->id]['walk_in']['in_transit']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['walk_in']['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination'] = $operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->count = $shipment_count[$hub->id]['walk_in']['arrived_at_destination'];
                        $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['arrived_at_destination'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->shipper_status_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->count = $shipment_count[$hub->id]['walk_in']['arrived_at_destination'];
                            $new_operation_forecast[$hub->id]['walk_in']['arrived_at_destination']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['walk_in']['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['not_attempted']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['not_attempted'] = $operation_forecast[$hub->id]['walk_in']['not_attempted']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->count = $shipment_count[$hub->id]['walk_in']['not_attempted'];
                        $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['not_attempted'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['not_attempted'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->shipper_status_id = 7;
                            $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->count = $shipment_count[$hub->id]['walk_in']['not_attempted'];
                            $new_operation_forecast[$hub->id]['walk_in']['not_attempted']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful'] = $operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['walk_in']['delivery_unsuccessful'];
                        $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['delivery_unsuccessful'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->shipper_status_id = 8;
                            $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['walk_in']['delivery_unsuccessful'];
                            $new_operation_forecast[$hub->id]['walk_in']['delivery_unsuccessful']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['walk_in']['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub->id)->where('booking_type_id', 4)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['walk_in']['on_hold']->exists()) {
                        $new_operation_forecast[$hub->id]['walk_in']['on_hold'] = $operation_forecast[$hub->id]['walk_in']['on_hold']->first();
                        $new_operation_forecast[$hub->id]['walk_in']['on_hold']->count = $shipment_count[$hub->id]['walk_in']['on_hold'];
                        $new_operation_forecast[$hub->id]['walk_in']['on_hold']->save();
                    } else {
                        if ($shipment_count[$hub->id]['walk_in']['on_hold'] > 0) {
                            $new_operation_forecast[$hub->id]['walk_in']['on_hold'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['walk_in']['on_hold']->shipper_status_id = 9;
                            $new_operation_forecast[$hub->id]['walk_in']['on_hold']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['walk_in']['on_hold']->booking_type_id = 4;
                            $new_operation_forecast[$hub->id]['walk_in']['on_hold']->count = $shipment_count[$hub->id]['walk_in']['on_hold'];
                            $new_operation_forecast[$hub->id]['walk_in']['on_hold']->save();
                        }
                    }
                }
                if (array_key_exists('reverse_pickup', $shipment_count[$hub->id])) {
                    $operation_forecast[$hub->id]['reverse_pickup']['booked'] = OperationForecast::where('shipper_status_id', 1)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['booked']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['booked'] = $operation_forecast[$hub->id]['reverse_pickup']['booked']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->count = $shipment_count[$hub->id]['reverse_pickup']['booked'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['booked'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['booked'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->shipper_status_id = 1;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->count = $shipment_count[$hub->id]['reverse_pickup']['booked'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['booked']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin'] = OperationForecast::where('shipper_status_id', 2)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin'] = $operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->count = $shipment_count[$hub->id]['reverse_pickup']['arrived_at_origin'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['arrived_at_origin'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->shipper_status_id = 2;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->count = $shipment_count[$hub->id]['reverse_pickup']['arrived_at_origin'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_origin']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['reverse_pickup']['in_transit'] = OperationForecast::where('shipper_status_id', 3)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['in_transit']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit'] = $operation_forecast[$hub->id]['reverse_pickup']['in_transit']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->count = $shipment_count[$hub->id]['reverse_pickup']['in_transit'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['in_transit'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->shipper_status_id = 3;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->count = $shipment_count[$hub->id]['reverse_pickup']['in_transit'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['in_transit']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination'] = OperationForecast::where('shipper_status_id', 4)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination'] = $operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->count = $shipment_count[$hub->id]['reverse_pickup']['arrived_at_destination'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['arrived_at_destination'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->shipper_status_id = 4;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->count = $shipment_count[$hub->id]['reverse_pickup']['arrived_at_destination'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['arrived_at_destination']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['reverse_pickup']['not_attempted'] = OperationForecast::where('shipper_status_id', 7)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted'] = $operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->count = $shipment_count[$hub->id]['reverse_pickup']['not_attempted'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['not_attempted'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->shipper_status_id = 7;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->count = $shipment_count[$hub->id]['reverse_pickup']['not_attempted'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['not_attempted']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful'] = OperationForecast::where('shipper_status_id', 8)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful'] = $operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['reverse_pickup']['delivery_unsuccessful'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['delivery_unsuccessful'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->shipper_status_id = 8;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->count = $shipment_count[$hub->id]['reverse_pickup']['delivery_unsuccessful'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['delivery_unsuccessful']->save();
                        }
                    }
                    $operation_forecast[$hub->id]['reverse_pickup']['on_hold'] = OperationForecast::where('shipper_status_id', 9)->where('hub_id', $hub->id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                    if ($operation_forecast[$hub->id]['reverse_pickup']['on_hold']->exists()) {
                        $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold'] = $operation_forecast[$hub->id]['reverse_pickup']['on_hold']->first();
                        $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->count = $shipment_count[$hub->id]['reverse_pickup']['on_hold'];
                        $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->save();
                    } else {
                        if ($shipment_count[$hub->id]['reverse_pickup']['on_hold'] > 0) {
                            $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold'] = new OperationForecast();
                            $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->shipper_status_id = 9;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->hub_id = $hub->id;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->booking_type_id = 5;
                            $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->count = $shipment_count[$hub->id]['reverse_pickup']['on_hold'];
                            $new_operation_forecast[$hub->id]['reverse_pickup']['on_hold']->save();
                        }
                    }
                }
            }
        }

        OperationForecastShipments::whereBetween('created_at', [$from, $to])->delete();

        foreach ($shipments as $shipment) {
            if ($shipment->actual_weight != null) {
                if ($shipment->actual_weight <= 0.5) {
                    $weight_range_id = 1;
                } else if ($shipment->actual_weight > 0.5 && $shipment->actual_weight <= 2) {
                    $weight_range_id = 2;
                } else if ($shipment->actual_weight > 2 && $shipment->actual_weight <= 5) {
                    $weight_range_id = 3;
                } else {
                    $weight_range_id = 4;
                }
            } else {
                if ($shipment->estimated_weight <= 0.5) {
                    $weight_range_id = 1;
                } else if ($shipment->estimated_weight > 0.5 && $shipment->estimated_weight <= 2) {
                    $weight_range_id = 2;
                } else if ($shipment->estimated_weight > 2 && $shipment->estimated_weight <= 5) {
                    $weight_range_id = 3;
                } else {
                    $weight_range_id = 4;
                }
            }
            if ($shipment->booking_type_id == 1) {
                if ($shipment->shipper_status_id == 1) {
                    $new_operation_forecast_shipments['regular']['booked'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['booked']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['booked']->id;
                    $new_operation_forecast_shipments['regular']['booked']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['booked']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['booked']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['booked']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['booked']->save();
                }
                if ($shipment->shipper_status_id == 2) {
                    $new_operation_forecast_shipments['regular']['arrived_at_origin'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['arrived_at_origin']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['arrived_at_origin']->id;
                    $new_operation_forecast_shipments['regular']['arrived_at_origin']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['arrived_at_origin']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['arrived_at_origin']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['arrived_at_origin']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['arrived_at_origin']->save();
                }
                if ($shipment->shipper_status_id == 3) {
                    $new_operation_forecast_shipments['regular']['in_transit'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['in_transit']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['in_transit']->id;
                    $new_operation_forecast_shipments['regular']['in_transit']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['in_transit']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['in_transit']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['in_transit']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['in_transit']->save();
                }
                if ($shipment->shipper_status_id == 4) {
                    $new_operation_forecast_shipments['regular']['arrived_at_destination'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['arrived_at_destination']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['arrived_at_destination']->id;
                    $new_operation_forecast_shipments['regular']['arrived_at_destination']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['arrived_at_destination']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['arrived_at_destination']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['arrived_at_destination']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['arrived_at_destination']->save();
                }
                if ($shipment->shipper_status_id == 7) {
                    $new_operation_forecast_shipments['regular']['not_attempted'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['not_attempted']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['not_attempted']->id;
                    $new_operation_forecast_shipments['regular']['not_attempted']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['not_attempted']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['not_attempted']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['not_attempted']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['not_attempted']->save();
                }
                if ($shipment->shipper_status_id == 8) {
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['delivery_unsuccessful']->id;
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['delivery_unsuccessful']->save();
                }
                if ($shipment->shipper_status_id == 9) {
                    $new_operation_forecast_shipments['regular']['on_hold'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['regular']['on_hold']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['regular']['on_hold']->id;
                    $new_operation_forecast_shipments['regular']['on_hold']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['regular']['on_hold']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['regular']['on_hold']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['regular']['on_hold']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['regular']['on_hold']->save();
                }
            }
            if ($shipment->booking_type_id == 2) {
                if ($shipment->shipper_status_id == 1) {
                    $new_operation_forecast_shipments['replacement']['booked'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['booked']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['booked']->id;
                    $new_operation_forecast_shipments['replacement']['booked']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['booked']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['booked']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['booked']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['booked']->save();
                }
                if ($shipment->shipper_status_id == 2) {
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['arrived_at_origin']->id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_origin']->save();
                }
                if ($shipment->shipper_status_id == 3) {
                    $new_operation_forecast_shipments['replacement']['in_transit'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['in_transit']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['in_transit']->id;
                    $new_operation_forecast_shipments['replacement']['in_transit']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['in_transit']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['in_transit']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['in_transit']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['in_transit']->save();
                }
                if ($shipment->shipper_status_id == 4) {
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['arrived_at_destination']->id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['arrived_at_destination']->save();
                }
                if ($shipment->shipper_status_id == 7) {
                    $new_operation_forecast_shipments['replacement']['not_attempted'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['not_attempted']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['not_attempted']->id;
                    $new_operation_forecast_shipments['replacement']['not_attempted']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['not_attempted']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['not_attempted']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['not_attempted']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['not_attempted']->save();
                }
                if ($shipment->shipper_status_id == 8) {
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['delivery_unsuccessful']->id;
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['delivery_unsuccessful']->save();
                }
                if ($shipment->shipper_status_id == 9) {
                    $new_operation_forecast_shipments['replacement']['on_hold'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['replacement']['on_hold']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['replacement']['on_hold']->id;
                    $new_operation_forecast_shipments['replacement']['on_hold']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['replacement']['on_hold']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['replacement']['on_hold']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['replacement']['on_hold']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['replacement']['on_hold']->save();
                }
            }
            if ($shipment->booking_type_id == 3) {
                if ($shipment->shipper_status_id == 1) {
                    $new_operation_forecast_shipments['try_and_buy']['booked'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['booked']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['booked']->id;
                    $new_operation_forecast_shipments['try_and_buy']['booked']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['booked']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['booked']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['booked']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['booked']->save();
                }
                if ($shipment->shipper_status_id == 2) {
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['arrived_at_origin']->id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_origin']->save();
                }
                if ($shipment->shipper_status_id == 3) {
                    $new_operation_forecast_shipments['try_and_buy']['in_transit'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['in_transit']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['in_transit']->id;
                    $new_operation_forecast_shipments['try_and_buy']['in_transit']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['in_transit']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['in_transit']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['in_transit']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['in_transit']->save();
                }
                if ($shipment->shipper_status_id == 4) {
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['arrived_at_destination']->id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['arrived_at_destination']->save();
                }
                if ($shipment->shipper_status_id == 7) {
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['not_attempted']->id;
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['not_attempted']->save();
                }
                if ($shipment->shipper_status_id == 8) {
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['delivery_unsuccessful']->id;
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['delivery_unsuccessful']->save();
                }
                if ($shipment->shipper_status_id == 9) {
                    $new_operation_forecast_shipments['try_and_buy']['on_hold'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['try_and_buy']['on_hold']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['try_and_buy']['on_hold']->id;
                    $new_operation_forecast_shipments['try_and_buy']['on_hold']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['try_and_buy']['on_hold']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['try_and_buy']['on_hold']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['try_and_buy']['on_hold']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['try_and_buy']['on_hold']->save();
                }
            }
            if ($shipment->booking_type_id == 4) {
                if ($shipment->shipper_status_id == 1) {
                    $new_operation_forecast_shipments['walk_in']['booked'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['booked']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['booked']->id;
                    $new_operation_forecast_shipments['walk_in']['booked']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['booked']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['booked']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['booked']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['booked']->save();
                }
                if ($shipment->shipper_status_id == 2) {
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['arrived_at_origin']->id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_origin']->save();
                }
                if ($shipment->shipper_status_id == 3) {
                    $new_operation_forecast_shipments['walk_in']['in_transit'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['in_transit']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['in_transit']->id;
                    $new_operation_forecast_shipments['walk_in']['in_transit']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['in_transit']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['in_transit']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['in_transit']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['in_transit']->save();
                }
                if ($shipment->shipper_status_id == 4) {
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['arrived_at_destination']->id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['arrived_at_destination']->save();
                }
                if ($shipment->shipper_status_id == 7) {
                    $new_operation_forecast_shipments['walk_in']['not_attempted'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['not_attempted']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['not_attempted']->id;
                    $new_operation_forecast_shipments['walk_in']['not_attempted']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['not_attempted']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['not_attempted']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['not_attempted']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['not_attempted']->save();
                }
                if ($shipment->shipper_status_id == 8) {
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['delivery_unsuccessful']->id;
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['delivery_unsuccessful']->save();
                }
                if ($shipment->shipper_status_id == 9) {
                    $new_operation_forecast_shipments['walk_in']['on_hold'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['walk_in']['on_hold']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['walk_in']['on_hold']->id;
                    $new_operation_forecast_shipments['walk_in']['on_hold']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['walk_in']['on_hold']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['walk_in']['on_hold']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['walk_in']['on_hold']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['walk_in']['on_hold']->save();
                }
            }
            if ($shipment->booking_type_id == 5) {
                if ($shipment->shipper_status_id == 1) {
                    $new_operation_forecast_shipments['reverse_pickup']['booked'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['booked']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['booked']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['booked']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['booked']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['booked']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['booked']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['booked']->save();
                }
                if ($shipment->shipper_status_id == 2) {
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_origin']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_origin']->save();
                }
                if ($shipment->shipper_status_id == 3) {
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['in_transit']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['in_transit']->save();
                }
                if ($shipment->shipper_status_id == 4) {
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['arrived_at_destination']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['arrived_at_destination']->save();
                }
                if ($shipment->shipper_status_id == 7) {
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['not_attempted']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['not_attempted']->save();
                }
                if ($shipment->shipper_status_id == 8) {
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['delivery_unsuccessful']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['delivery_unsuccessful']->save();
                }
                if ($shipment->shipper_status_id == 9) {
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold'] = new OperationForecastShipments();
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold']->operation_forecast_id = $new_operation_forecast[$shipment->consignee_city_id]['reverse_pickup']['on_hold']->id;
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold']->weight_range_id = $weight_range_id;
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold']->shipment_id = $shipment->id;
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold']->hub_id = $shipment->consignee_city_id;
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold']->booking_type_id = $shipment->booking_type_id;
                    $new_operation_forecast_shipments['reverse_pickup']['on_hold']->save();
                }
            }
        }

        $pickup_requests = PickupRequest::leftjoin('pickup_request_assigned_shipments as pras', 'pras.pickup_request_id', '=', 'pickup_requests.id')
            ->leftjoin('shipments as s', 's.id', 'pras.shipment_id', '=', 's.id')->select('pickup_requests.id as pickup_request_id', 's.id as shipment_id', 's.booking_type_id as booking_type_id', 'pickup_requests.pickup_address_id as pickup_address_id', 's.shipper_status_id as shipper_status_id', 's.actual_weight as actual_weight', 's.estimated_weight as estimated_weight', 's.user_id as user_id')
            ->whereIn('pickup_requests.status', [0, 1])
            ->whereBetween('pickup_requests.updated_at', [$from, $to])
            ->get();

        foreach ($pickup_requests as $pickup_request) {
            $user_shipping_info = UserShippingInfo::find($pickup_request->pickup_address_id);
            $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = 0;
            $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = 0;
            $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = 0;
            $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = 0;
            $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = 0;
            $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = 0;
            $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = 0;
            $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = 0;
        }

        foreach ($pickup_requests as $pickup_request) {
            $user_shipping_info = UserShippingInfo::find($pickup_request->pickup_address_id);
            if ($pickup_request->booking_type_id == 1) {
                $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] + 1;
                $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = $pickup_request->pickup_request_id;
            }
            if ($pickup_request->booking_type_id == 2) {
                $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] + 1;
                $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = $pickup_request->pickup_request_id;
            }
            if ($pickup_request->booking_type_id == 3) {
                $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] + 1;
                $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = $pickup_request->pickup_request_id;
            }
            if ($pickup_request->booking_type_id == 5) {
                $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] + 1;
                $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = $pickup_request->pickup_request_id;
            }
        }

//        $outgoing_users = User::where('status', 3)->get();

        foreach ($pickup_requests as $pickup_request) {
            $user_shipping_info = UserShippingInfo::find($pickup_request->pickup_address_id);
            if (array_key_exists($pickup_request->user_id, $outgoing_shipment_count)) {
                if (array_key_exists($user_shipping_info->city_id, $outgoing_shipment_count[$pickup_request->user_id])) {
                    if (array_key_exists('regular', $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id])) {
                        $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = OperationsOutgoingPickupRequests::where('pickup_request_id', $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['regular'])->where('hub_id', $user_shipping_info->city_id)->where('booking_type_id', 1)->whereBetween('updated_at', [$from, $to]);
                        dd($operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']);
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->first();
                        if ($operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->exists()) {
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['regular'];
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->save();
                        } else {
                            if ($outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] > 0) {
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular'] = new OperationsOutgoingPickupRequests();
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->pickup_request_id = $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['regular'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->hub_id = $user_shipping_info->city_id;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->booking_type_id = 1;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['regular'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->save();
                            }
                        }
                        print_r($operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']);die();
                    }
                    if (array_key_exists('replacement', $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id])) {
                        $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = OperationsOutgoingPickupRequests::where('pickup_request_id', $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'])->where('hub_id', $user_shipping_info->city_id)->where('booking_type_id', 2)->whereBetween('updated_at', [$from, $to]);
                        if ($operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->exists()) {
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->first();
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'];
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->save();
                        } else {
                            if ($outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] > 0) {
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'] = new OperationsOutgoingPickupRequests();
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->pickup_request_id = $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->hub_id = $user_shipping_info->city_id;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->booking_type_id = 2;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['replacement'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->save();
                            }
                        }
                    }
                    if (array_key_exists('try_and_buy', $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id])) {
                        $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = OperationsOutgoingPickupRequests::where('pickup_request_id', $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'])->where('hub_id', $user_shipping_info->city_id)->where('booking_type_id', 3)->whereBetween('updated_at', [$from, $to]);
                        if ($operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->exists()) {
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->first();
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'];
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->save();
                        } else {
                            if ($outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] > 0) {
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'] = new OperationsOutgoingPickupRequests();
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->pickup_request_id = $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->hub_id = $user_shipping_info->city_id;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->booking_type_id = 3;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->save();
                            }
                        }
                    }
                    if (array_key_exists('reverse_pickup', $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id])) {
                        $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = OperationsOutgoingPickupRequests::where('pickup_request_id', $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'])->where('hub_id', $user_shipping_info->city_id)->where('booking_type_id', 5)->whereBetween('updated_at', [$from, $to]);
                        if ($operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->exists()) {
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = $operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->first();
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$pickup_request->city_id]['reverse_pickup']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'];
                            $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->save();
                        } else {
                            if ($outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] > 0) {
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'] = new OperationsOutgoingPickupRequests();
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->pickup_request_id = $pickup_request_id[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->hub_id = $user_shipping_info->city_id;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->booking_type_id = 5;
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->shipments_count = $outgoing_shipment_count[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup'];
                                $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->save();
                            }
                        }
                    }
                }
            }
        }

        $users = User::get();

        foreach ($users as $user){
            $user_shipments[$user->id]= 0;
        }

        OperationsOutgoingPickupRequestShipments::whereBetween('created_at', [$from, $to])->delete();

        foreach ($pickup_requests as $pickup_request) {
            if ($pickup_request->actual_weight != null) {
                if ($pickup_request->actual_weight <= 0.5) {
                    $weight_range_id = 1;
                } else if ($pickup_request->actual_weight > 0.5 && $pickup_request->actual_weight <= 2) {
                    $weight_range_id = 2;
                } else if ($pickup_request->actual_weight > 2 && $pickup_request->actual_weight <= 5) {
                    $weight_range_id = 3;
                } else {
                    $weight_range_id = 4;
                }
            } else {
                if ($pickup_request->estimated_weight <= 0.5) {
                    $weight_range_id = 1;
                } else if ($pickup_request->estimated_weight > 0.5 && $pickup_request->estimated_weight <= 2) {
                    $weight_range_id = 2;
                } else if ($pickup_request->estimated_weight > 2 && $pickup_request->estimated_weight <= 5) {
                    $weight_range_id = 3;
                } else {
                    $weight_range_id = 4;
                }
            }


            $user_shipping_info = UserShippingInfo::find($pickup_request->pickup_address_id);

            if ($pickup_request->booking_type_id == 1) {
                $new_operation_outgoing_forecast_shipments = new OperationsOutgoingPickupRequestShipments();
                $new_operation_outgoing_forecast_shipments->operation_outgoing_forecast_id = $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['regular']->id;
                $new_operation_outgoing_forecast_shipments->weight_range_id = $weight_range_id;
                $new_operation_outgoing_forecast_shipments->shipment_id = $pickup_request->shipment_id;
                $new_operation_outgoing_forecast_shipments->hub_id = $user_shipping_info->city_id;
                $new_operation_outgoing_forecast_shipments->booking_type_id = $pickup_request->booking_type_id;
                $new_operation_outgoing_forecast_shipments->save();
            }
            if ($pickup_request->booking_type_id == 2) {
                $new_operation_outgoing_forecast_shipments = new OperationsOutgoingPickupRequestShipments();
                $new_operation_outgoing_forecast_shipments->operation_outgoing_forecast_id = $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['replacement']->id;
                $new_operation_outgoing_forecast_shipments->weight_range_id = $weight_range_id;
                $new_operation_outgoing_forecast_shipments->shipment_id = $pickup_request->shipment_id;
                $new_operation_outgoing_forecast_shipments->hub_id = $user_shipping_info->city_id;
                $new_operation_outgoing_forecast_shipments->booking_type_id = $pickup_request->booking_type_id;
                $new_operation_outgoing_forecast_shipments->save();
            }
            if ($pickup_request->booking_type_id == 3) {
                $new_operation_outgoing_forecast_shipments = new OperationsOutgoingPickupRequestShipments();
                $new_operation_outgoing_forecast_shipments->operation_outgoing_forecast_id = $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['try_and_buy']->id;
                $new_operation_outgoing_forecast_shipments->weight_range_id = $weight_range_id;
                $new_operation_outgoing_forecast_shipments->shipment_id = $pickup_request->shipment_id;
                $new_operation_outgoing_forecast_shipments->hub_id = $user_shipping_info->city_id;
                $new_operation_outgoing_forecast_shipments->booking_type_id = $pickup_request->booking_type_id;
                $new_operation_outgoing_forecast_shipments->save();
            }
            if ($pickup_request->booking_type_id == 5) {
                $new_operation_outgoing_forecast_shipments = new OperationsOutgoingPickupRequestShipments();
                $new_operation_outgoing_forecast_shipments->operation_outgoing_forecast_id = $new_operation_outgoing_forecast[$pickup_request->user_id][$user_shipping_info->city_id]['reverse_pickup']->id;
                $new_operation_outgoing_forecast_shipments->weight_range_id = $weight_range_id;
                $new_operation_outgoing_forecast_shipments->shipment_id = $pickup_request->shipment_id;
                $new_operation_outgoing_forecast_shipments->hub_id = $user_shipping_info->city_id;
                $new_operation_outgoing_forecast_shipments->booking_type_id = $pickup_request->booking_type_id;
                $new_operation_outgoing_forecast_shipments->save();
            }

            if (array_key_exists( $pickup_request->user_id, $user_shipments)) {
                        $user_shipments[$pickup_request->user_id] = $user_shipments[$pickup_request->user_id] + 1;
            }
        }

        foreach ($user_shipments as $user_id => $count) {
                $existing_top_customers[$user_id] = OperationsOutgoingTopCustomers::where('user_id', $user_id)->whereBetween('updated_at', [$from, $to]);

                if ($existing_top_customers[$user_id]->exists()) {
                    if ($count > 0) {
                        $top_customers[$user_id] = $existing_top_customers[$user_id]->first();
                        $top_customers[$user_id]->shipments_count = $count;
                        $top_customers[$user_id]->save();
                    }
                } else {
                    if ($count > 0) {
                        $top_customers[$user_id] = new OperationsOutgoingTopCustomers();
                        $top_customers[$user_id]->user_id = $user_id;
                        $top_customers[$user_id]->shipments_count = $count;
                        $top_customers[$user_id]->save();
                    }

                }
//            }
        }


        foreach ($pickup_requests as $pickup_request) {
            if (array_key_exists($pickup_request->user_id, $top_customers)) {
                if(!empty($top_customers[$pickup_request->user_id])){
                    $top_customer_shipments[$pickup_request->user_id] = new OperationsOutgoingTopCustomersShipments();
                    $top_customer_shipments[$pickup_request->user_id]->customer_id = $top_customers[$pickup_request->user_id]->id;
                    $top_customer_shipments[$pickup_request->user_id]->shipment_id = $pickup_request->shipment_id;
                    $top_customer_shipments[$pickup_request->user_id]->save();
                }
            }
        }

//        $operation_last_updated = OperationsForecastLastUpdatedTime::first();
//        if($operation_last_updated != null){
//            $operation_last_updated->delete();
//        }

        $last_updated_at = new OperationsForecastLastUpdatedTime();
        $last_updated_at->save();
    }
}
