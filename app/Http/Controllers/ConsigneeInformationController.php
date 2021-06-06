<?php

namespace App\Http\Controllers;

use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyExcluded;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\Blacklist\ConsigneeInformationLog;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Http\Request;

class ConsigneeInformationController extends Controller
{
   static public function add($phone, $name, $address, $phone2, $city_id, $user_id = NULL){
       if(!User::where('phone', $phone)->exists()){
           if(!UserShippingInfo::where('phone', $phone)->exists()){
               $consignee_information = ConsigneeInformation::where('phone', $phone);
               if(!$consignee_information->exists()){
                   $consignee_information = new ConsigneeInformation();
                   $consignee_information->phone = $phone;
                   $consignee_information->phone2 = $phone2;
                   $consignee_information->name = $name;
                   $consignee_information->address = $address;
                   $consignee_information->city_id = $city_id;
                   $consignee_information->save();
               }else{
                   $consignee_information = $consignee_information->first();
                   $consignee_information_log = new ConsigneeInformationLog();
                   $consignee_information_log->consignee_information_id = $consignee_information->id;
                   $consignee_information_log->phone = $consignee_information->phone;
                   $consignee_information_log->phone2 = $consignee_information->phone2;
                   $consignee_information_log->name = $consignee_information->name;
                   $consignee_information_log->address = $consignee_information->address;
                   $consignee_information_log->city_id = $consignee_information->city_id;
                   $consignee_information_log->user_id = $user_id;
                   $consignee_information_log->save();

                   $consignee_information->phone = $phone;
                   $consignee_information->phone2 = $phone2;
                   $consignee_information->name = $name;
                   $consignee_information->address = $address;
                   $consignee_information->city_id = $city_id;
                   $consignee_information->save();
               }
           }
       }
   }

   static public function calculate_ratio_for_consignees(){
       $consignee_information_ids = ConsigneeInformation::pluck('id')->toArray();
       $filtered_consignee_information_ids = array();
       if(count($consignee_information_ids) > 0){
           $excluded_consignees_ids = BlacklistedConsigneeManuallyExcluded::pluck('consignee_information_id')->toArray();
           $filtered_consignee_information_ids = array_merge(array_diff($consignee_information_ids, $excluded_consignees_ids), array_diff($excluded_consignees_ids, $consignee_information_ids));

           $delivered_statuses = array(14, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 45, 46);
           $return_statuses = array(20, 22, 24, 25, 44, 47, 48, 57, 60);

           $blacklist_settings = BlacklistSetting::where('labeling_id', 2)->where('status', 1)->get();
            $conditions = array();
           foreach ($filtered_consignee_information_ids as $id) {
                $match = FALSE;
                $match_and_break = FALSE;
                $total_shipments = 0;
                $delivered_shipments = 0;
                $returned_shipments = 0;
                $undelivered_shipments = 0;
                $delivered_shipments_ratio = 0;
                $returned_shipments_ratio = 0;
                $undelivered_shipments_ratio = 0;
                $consignee_phone = ConsigneeInformation::find($id)->phone;

                $total_shipments = Shipment::where('consignee_phone_number_1', $consignee_phone)->count();
                if($total_shipments > 0){
                    $delivered_shipments = Shipment::where('consignee_phone_number_1', $consignee_phone)->whereIn('shipper_status_id', $delivered_statuses)->count();
                    $returned_shipments = Shipment::where('consignee_phone_number_1', $consignee_phone)->whereIn('shipper_status_id', $return_statuses)->count();
                    $undelivered_shipments = $total_shipments - $delivered_shipments;
                    $delivered_shipments_ratio = round(($delivered_shipments / $total_shipments) * 100, 2);
                    $returned_shipments_ratio = round(($returned_shipments / $total_shipments) * 100, 2);
                    $undelivered_shipments_ratio = round(($undelivered_shipments / $total_shipments) * 100,2);
                }


                foreach ($blacklist_settings as $setting){
                    $blacklist_conditions = $setting->conditions->groupBy('blacklist_condition_id');
                    $present_condition_return_ratio = null;
                    foreach($blacklist_conditions as $blacklist_condition){
                        foreach($blacklist_condition as $condition){
                            $condition_id = $condition->blacklist_condition_id;
                            $operation_id = $condition->blacklist_operation_id;
                            $range_id = $condition->blacklist_shipment_range_id;
                            $range_value = $condition->blacklist_shipment_range_value;
                            $logic_id = $condition->blacklist_logic_id;
                            $logic_value = $condition->blacklist_logic_value;

                            if($condition_id == 1){
                                $present_condition_return_ratio = 1;
                                if($operation_id == null || $operation_id == 2){
                                    if ($range_id == 1) {
                                        if ($logic_id == 1) {
                                            if ($returned_shipments_ratio == $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 2){
                                            if ($returned_shipments_ratio != $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 3){
                                            if ($returned_shipments_ratio >= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 4){
                                            if ($returned_shipments_ratio > $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 5){
                                            if ($returned_shipments_ratio <= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 6){
                                            if ($returned_shipments_ratio < $logic_value) {
                                                $match = TRUE;
                                            }
                                        }
                                    }else if ($range_id == 2){
                                        if($total_shipments == $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    else if ($range_id == 3){
                                        if($total_shipments != $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 4){
                                        if($total_shipments >= $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 5){
                                        if($total_shipments > $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                    break;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 6){
                                        if($total_shipments <= $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 7){
                                        if($total_shipments < $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }
                                }else if($operation_id == 1){
                                    if($match == FALSE){
                                        $match_and_break = TRUE;
                                        break;
                                    }
                                    if ($range_id == 1) {
                                        if ($logic_id == 1) {
                                            if ($returned_shipments_ratio == $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 2){
                                            if ($returned_shipments_ratio != $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 3){
                                            if ($returned_shipments_ratio >= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 4){
                                            if ($returned_shipments_ratio > $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 5){
                                            if ($returned_shipments_ratio <= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 6){
                                            if ($returned_shipments_ratio < $logic_value) {
                                                $match = TRUE;
                                            }
                                        }
                                    }else if ($range_id == 2){
                                        if($total_shipments == $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    else if ($range_id == 3){
                                        if($total_shipments != $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 4){
                                        if($total_shipments >= $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 5){
                                        if($total_shipments > $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                    break;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 6){
                                        if($total_shipments <= $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 7){
                                        if($total_shipments < $range_value){
                                            if ($logic_id == 1) {
                                                if ($returned_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($returned_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($returned_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($returned_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($returned_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($returned_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }

                                }

                            }
                            if($condition_id == 2){
                                if(($match == FALSE) && ($present_condition_return_ratio != null)){
                                    $match_and_break = TRUE;
                                    break;
                                }

                                if($operation_id == null || $operation_id == 2){
                                    if ($range_id == 1) {
                                        if ($logic_id == 1) {
                                            if ($delivered_shipments_ratio == $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 2){
                                            if ($delivered_shipments_ratio != $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 3){
                                            if ($delivered_shipments_ratio >= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 4){
                                            if ($delivered_shipments_ratio > $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 5){
                                            if ($delivered_shipments_ratio <= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 6){
                                            if ($delivered_shipments_ratio < $logic_value) {
                                                $match = TRUE;
                                            }
                                        }
                                    }else if ($range_id == 2){
                                        if($total_shipments == $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    else if ($range_id == 3){
                                        if($total_shipments != $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 4){
                                        if($total_shipments >= $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 5){
                                        if($total_shipments > $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                    break;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 6){
                                        if($total_shipments <= $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 7){
                                        if($total_shipments < $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }
                                }else if($operation_id == 1){
                                    if($match == FALSE){
                                        break;
                                    }
                                    if ($range_id == 1) {
                                        if ($logic_id == 1) {
                                            if ($delivered_shipments_ratio == $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 2){
                                            if ($delivered_shipments_ratio != $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 3){
                                            if ($delivered_shipments_ratio >= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 4){
                                            if ($delivered_shipments_ratio > $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 5){
                                            if ($delivered_shipments_ratio <= $logic_value) {
                                                $match = TRUE;
                                            }
                                        }else if ($logic_id == 6){
                                            if ($delivered_shipments_ratio < $logic_value) {
                                                $match = TRUE;
                                            }
                                        }
                                    }else if ($range_id == 2){
                                        if($total_shipments == $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }
                                    else if ($range_id == 3){
                                        if($total_shipments != $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 4){
                                        if($total_shipments >= $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 5){
                                        if($total_shipments > $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                    break;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 6){
                                        if($total_shipments <= $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }else if ($range_id == 7){
                                        if($total_shipments < $range_value){
                                            if ($logic_id == 1) {
                                                if ($delivered_shipments_ratio == $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 2){
                                                if ($delivered_shipments_ratio != $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 3){
                                                if ($delivered_shipments_ratio >= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 4){
                                                if ($delivered_shipments_ratio > $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 5){
                                                if ($delivered_shipments_ratio <= $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }else if ($logic_id == 6){
                                                if ($delivered_shipments_ratio < $logic_value) {
                                                    $match = TRUE;
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                        }
                        if($match_and_break == TRUE){
                            break;
                        }
                    }
                    if($match == TRUE){
                        $blacklist_consignee = BlacklistedConsignee::where('id', $id);
                        if($blacklist_consignee->exists()){
                            $blacklist_consignee = $blacklist_consignee->first();
                            $blacklist_consignee->shipments = $total_shipments;
                            $blacklist_consignee->delivered = $delivered_shipments;
                            $blacklist_consignee->delivered_ratio = $delivered_shipments_ratio;
                            $blacklist_consignee->undelivered = $undelivered_shipments;
                            $blacklist_consignee->undelivered_ratio = $undelivered_shipments_ratio;
                            $blacklist_consignee->return = $returned_shipments;
                            $blacklist_consignee->return_ratio = $returned_shipments_ratio;
                            $blacklist_consignee->blacklist_setting_id = $setting->id;
                            $blacklist_consignee->save();

                        }else{
                            $blacklist_consignee = new BlacklistedConsignee();
                            $blacklist_consignee->consignee_information_id = $id;
                            $blacklist_consignee->shipments = $total_shipments;
                            $blacklist_consignee->delivered = $delivered_shipments;
                            $blacklist_consignee->delivered_ratio = $delivered_shipments_ratio;
                            $blacklist_consignee->undelivered = $undelivered_shipments;
                            $blacklist_consignee->undelivered_ratio = $undelivered_shipments_ratio;
                            $blacklist_consignee->return = $returned_shipments;
                            $blacklist_consignee->return_ratio = $returned_shipments_ratio;
                            $blacklist_consignee->blacklist_setting_id = $setting->id;
                            $blacklist_consignee->save();
                        }
                    }

                }
           }
       }
   }

}
