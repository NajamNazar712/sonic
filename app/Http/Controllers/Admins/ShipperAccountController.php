<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipper\User;
use App\Http\Models\Shipment;

use Auth;
use DB;

use Carbon\Carbon;

class ShipperAccountController extends Controller
{
    //
    static public function disable()
        {
            $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();
            $days = $settings->setting_value;
            $date = Carbon::now()->subDays($days);
            $active_users = User::where(['status' => 3,'created_at' < $date])->pluck('id')->toArray();
            $shipments = Shipment::where('created_at', '>', $date)->groupBy('user_id')->pluck('user_id')->toArray();
            $result = array_diff($active_users,$shipments);
            if (count($result) > 0)
            {
                foreach ($result as $status) {
                    User::where('id', $status)->Update(['status' => 4,'disable_remarks' => 'Auto Disabled after ' . $days . ' Day(s)']);
                }
            }
        }
}