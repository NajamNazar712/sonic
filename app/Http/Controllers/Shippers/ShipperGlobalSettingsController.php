<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Shipper\ShipperAirWaybillSettings;
use App\Http\Models\Shipper\ShipperGlobalSettings;
use App\Http\Models\Shipper\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShipperGlobalSettingsController extends Controller
{

    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function air_waybill_printing_count_index(){
        $air_waybill = ShipperAirWaybillSettings::where('user_id', session('user_id'));
        if($air_waybill->exists()){
            $air_waybill = $air_waybill->first();
        }
        else{
            $air_waybill = null;
        }

        return view('client.settings.air_waybill_printing')->with(['air_waybill'=>$air_waybill]);
    }
    public function air_waybill_printing_count_store(Request $request){
        $settings = ShipperAirWaybillSettings::where('user_id', session('user_id'));

        if($settings->exists()){
            $settings = $settings->first();
            $settings->print_count = $request->air_waybill_printing_count;
            if($request->information_display){
                $settings->information = 1;
            }
            else{
                $settings->information = 0;
            }

            $settings->save();
        }
        else{
            $new_settings = new ShipperAirWaybillSettings();
            $new_settings->user_id = session('user_id');
            $new_settings->print_count = $request->air_waybill_printing_count;
            if($request->information_display == "on"){
                $new_settings->information = 1;
            }
            else{
                $new_settings->information = 0;
            }
            $new_settings->save();
        }

        return redirect()->back()->with('success', 'Settings Updated!');
}

    public function upload_logo_index(){
        $logo = Storage::url('shippers_logo/logo_' . Auth::id() . '.png');
        return view('client.settings.logo')->with(['logo'=>$logo]);
    }

    public function upload_logo_submit(Request $request){
        if ($request->hasFile('upload_logo')) {
            $shipper = User::find(Auth::id());
            $filename = 'logo_' . Auth::id() . '.png';

            $file = $request->file('upload_logo');

            Storage::disk('public')->putFileAs('shippers_logo', $file, $filename);
            $shipper->logo = $filename;
            $shipper->logo_status = 1;
            $shipper->save();
            return redirect()->back()->with('success', 'Logo Successfully Updated!');
        }
    }
}
