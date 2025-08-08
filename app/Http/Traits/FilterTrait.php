<?php

namespace App\Http\Traits;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Support\Facades\Auth;

trait FilterTrait
{
    public static function getFilteredIds($admin_id)
    {
        $global_setting_admin = GlobalSettings::where('type','admin_restrict')->value('setting_value');

       if($admin_id == $global_setting_admin){
           $excludedIds = GlobalSettings::whereIn('type', ['excluded_users_sst', 'excluded_users_wht'])
               ->pluck('text') // get only the 'text' column
               ->flatMap(function ($value) {
                   return explode(',', $value); // split comma-separated values
               })
               ->map(fn($id) => (int) trim($id)) // clean and cast to integer
               ->unique() // remove duplicates
               ->values() // reset array keys
               ->toArray();

           if(count($excludedIds) > 0){
               return $excludedIds;
           }else{
               return false;
           }

       }
    }

    public static function getFilteredShipperIds($shipper_id)
    {
        $excludedIds = GlobalSettings::whereIn('type', ['excluded_users_sst', 'excluded_users_wht'])
            ->pluck('text') // get only the 'text' column
            ->flatMap(function ($value) {
                return explode(',', $value); // split comma-separated values
            })
            ->map(fn($id) => (int) trim($id)) // clean and cast to integer
            ->unique() // remove duplicates
            ->values() // reset array keys
            ->toArray();

        if(count($excludedIds) > 0){
            if(in_array($shipper_id,$excludedIds)) {
                return true;
            }
        }else{
            return false;
        }
    }
}
