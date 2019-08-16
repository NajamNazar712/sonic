<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipperGlobalSettingsController extends Controller
{

    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function air_waybill_printing_count_index(){
        $air_waybill_printing_count = GlobalSettings::where('type', 'air_waybill_printing_count')->first();

        return view('client.settings.air_waybill_printing')->with(['air_waybill_printing_count'=>$air_waybill_printing_count]);
    }
    public function air_waybill_printing_count_store(Request $request){
        $settings = GlobalSettings::where('type', 'air_waybill_printing_count')->first();

        $settings->setting_value = $request->air_waybill_printing_count;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
}
