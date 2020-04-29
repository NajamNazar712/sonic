<?php

namespace App\Http\Controllers;

use App\Http\Models\Blacklist\ConsigneeInformation;
use Illuminate\Http\Request;

class ConsigneeInformationController extends Controller
{
   static public function add($phone, $name, $address, $phone2, $city_id){
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
