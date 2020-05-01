<?php

namespace App\Http\Controllers;

use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyExcluded;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Http\Request;

class ConsigneeInformationController extends Controller
{
   static public function add($phone, $name, $address, $phone2, $city_id){
       if(!User::where('phone', $phone)->exists()){
           if(!UserShippingInfo::where('phone', $phone)->exists()){
               if(!ConsigneeInformation::where('phone', $phone)->exists()){
                   $consignee_information = new ConsigneeInformation();
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

           $blacklist_settings = BlacklistSetting::where('status', 1)->get();
            $conditions = array();
           foreach ($filtered_consignee_information_ids as $id) {
                $match = FALSE;
                $match_setting = NULL;
                $total_shipments = 0;
                $delivered_shipments = 0;
                $returned_shipments = 0;
                $undelivered_shipments = 0;
                $delivered_shipments_ratio = 0;
                $returned_shipments_ratio = 0;
                $undelivered_shipments_ratio = 0;
                $consignee_phone = ConsigneeInformation::find($id)->phone;

                $total_shipments = Shipment::where('consignee_phone_number_1', $consignee_phone)->count();
                $delivered_shipments = Shipment::where('consignee_phone_number_1', $consignee_phone)->whereIn('shipper_status_id', $delivered_statuses)->count();
                $returned_shipments = Shipment::where('consignee_phone_number_1', $consignee_phone)->whereIn('shipper_status_id', $return_statuses)->count();
                $undelivered_shipments = $total_shipments - $delivered_shipments;
                $delivered_shipments_ratio = ($delivered_shipments / $total_shipments) * 100;
                $returned_shipments_ratio = ($returned_shipments / $total_shipments) * 100;
                $undelivered_shipments_ratio = ($undelivered_shipments / $total_shipments) * 100;

                foreach ($blacklist_settings as $setting){
                    foreach ($setting->conditions as $condition){
                        $condition_id = $condition->blacklist_condition_id;

                        $range_id = $condition->blacklist_shipment_range_id;
                        $range_value = $condition->blacklist_shipment_range_value;

                        $logic_id = $condition->blacklist_logic_id;
                        $logic_value = $condition->blacklist_logic_value;
                        if (strpos($logic_value, '%') !== FALSE) {
                            $logic_value = floatval(str_replace('%', '', $logic_value)) / 100;
                        }

                        if($condition_id == 1){
                            if ($range_id == 1) {
                                if ($logic_id == 1) {
                                    if ($returned_shipments_ratio == $logic_value) {
                                        $match = TRUE;
                                        break;
                                    }
                                }else if ($logic_id == 2){
                                    if ($returned_shipments_ratio != $logic_value) {
                                        $match = TRUE;
                                        break;
                                    }
                                }else if ($logic_id == 3){
                                    if ($returned_shipments_ratio >= $logic_value) {
                                        $match = TRUE;
                                        break;
                                    }
                                }else if ($logic_id == 4){
                                    if ($returned_shipments_ratio > $logic_value) {
                                        $match = TRUE;
                                        break;
                                    }
                                }else if ($logic_id == 5){
                                    if ($returned_shipments_ratio <= $logic_value) {
                                        $match = TRUE;
                                        break;
                                    }
                                }else if ($logic_id == 6){
                                    if ($returned_shipments_ratio < $logic_value) {
                                        $match = TRUE;
                                        break;
                                    }
                                }
                            }else if ($range_id == 2){
                                if($total_shipments == $range_value){
                                    if ($logic_id == 1) {
                                        if ($returned_shipments_ratio == $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 2){
                                        if ($returned_shipments_ratio != $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 3){
                                        if ($returned_shipments_ratio >= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 4){
                                        if ($returned_shipments_ratio > $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 5){
                                        if ($returned_shipments_ratio <= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 6){
                                        if ($returned_shipments_ratio < $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }
                            else if ($range_id == 3){
                                if($total_shipments != $range_value){
                                    if ($logic_id == 1) {
                                        if ($returned_shipments_ratio == $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 2){
                                        if ($returned_shipments_ratio != $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 3){
                                        if ($returned_shipments_ratio >= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 4){
                                        if ($returned_shipments_ratio > $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 5){
                                        if ($returned_shipments_ratio <= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 6){
                                        if ($returned_shipments_ratio < $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }else if ($range_id == 4){
                                if($total_shipments >= $range_value){
                                    if ($logic_id == 1) {
                                        if ($returned_shipments_ratio == $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 2){
                                        if ($returned_shipments_ratio != $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 3){
                                        if ($returned_shipments_ratio >= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 4){
                                        if ($returned_shipments_ratio > $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 5){
                                        if ($returned_shipments_ratio <= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 6){
                                        if ($returned_shipments_ratio < $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }else if ($range_id == 5){
                                if($total_shipments > $range_value){
                                    if ($logic_id == 1) {
                                        if ($returned_shipments_ratio == $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 2){
                                        if ($returned_shipments_ratio != $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 3){
                                        if ($returned_shipments_ratio >= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 4){
                                        if ($returned_shipments_ratio > $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 5){
                                        if ($returned_shipments_ratio <= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 6){
                                        if ($returned_shipments_ratio < $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }else if ($range_id == 6){
                                if($total_shipments <= $range_value){
                                    if ($logic_id == 1) {
                                        if ($returned_shipments_ratio == $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 2){
                                        if ($returned_shipments_ratio != $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 3){
                                        if ($returned_shipments_ratio >= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 4){
                                        if ($returned_shipments_ratio > $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 5){
                                        if ($returned_shipments_ratio <= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 6){
                                        if ($returned_shipments_ratio < $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }else if ($range_id == 7){
                                if($total_shipments < $range_value){
                                    if ($logic_id == 1) {
                                        if ($returned_shipments_ratio == $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 2){
                                        if ($returned_shipments_ratio != $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 3){
                                        if ($returned_shipments_ratio >= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 4){
                                        if ($returned_shipments_ratio > $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 5){
                                        if ($returned_shipments_ratio <= $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }else if ($logic_id == 6){
                                        if ($returned_shipments_ratio < $logic_value) {
                                            $match = TRUE;
                                            break;
                                        }
                                    }
                                }
                            }
                        }else if ($condition_id == 2){

                        }else{

                        }


                    }
                    if($match == TRUE){
                        $match_setting = $setting->id;
                        break;
                    }
                }





           }
       }
   }

}
