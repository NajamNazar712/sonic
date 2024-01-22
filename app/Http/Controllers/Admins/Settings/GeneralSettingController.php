<?php

namespace App\Http\Controllers\admins\settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\http\models\admin\settings\GeneralSetting;
use Carbon\Carbon;

class GeneralSettingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function ticker_index()
    {
        $admin_ticker = null;
        $shipper_ticker = null;

        $settings = GeneralSetting::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $admin_ticker = $settings;

            // if (!empty($ticker)) {
            //     $admin_ticker = $ticker;
            // }
        }

        $settings = GeneralSetting::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $shipper_ticker = $settings;

            // if (!empty($ticker)) {
            //     $shipper_ticker = $ticker;
            // }
        }

        return view('admin.settings.ticker')->with(['admin_ticker' => $admin_ticker, 'shipper_ticker' => $shipper_ticker]);
    }

    public function ticker_store(Request $request)
    {

        $settings = GeneralSetting::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GeneralSetting();

            $settings->type = 'admin_ticker';
        }
        $start_date = Carbon::parse(($request->admin_start_date_formatted . ' ' . $request->admin_start_time))->format('Y-m-d H:i:s');
        $end_date =Carbon::parse(( $request->admin_end_date_formatted . ' ' . $request->admin_end_time))->format('Y-m-d H:i:s');

     
        $settings->description = ($request->admin_ticker) ? $request->admin_ticker : '';
        $settings->start_date = $request->admin_start_date_formatted;
        $settings->end_date = $request->admin_end_date_formatted;


        $settings->save();

        $settings = GeneralSetting::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        } else {
            $settings = new GeneralSetting();

            $settings->type = 'shipper_ticker';
        }

        $settings->description = ($request->shipper_ticker) ? $request->shipper_ticker : '';
        $settings->start_date = $request->shipper_start_date_formatted;
        $settings->end_date = $request->shipper_end_date_formatted;


        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
}
