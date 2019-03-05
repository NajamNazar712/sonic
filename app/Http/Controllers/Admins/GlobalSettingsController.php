<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountHeadAccountTitle;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\WalkInStandardWeightCharge;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class GlobalSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function pickup_index(){
        $settings = GlobalSettings::where('type','=','pickup_weight')->first();
        return view('admin.settings.pickup')->with('settings',$settings);
    }
    public function add_pickup_weight(Request $request){

        if($request->isMethod('post')){
            $result = GlobalSettings::create([
                'setting_value'=>$request->pickup_weight,
                'type'=>'pickup_weight'
            ]);
            if($result){
                return redirect()->back()->with('success','Pickup request weight updated');
            }
        }else{
           $record = GlobalSettings::where('type','pickup_weight')->get();
            $result = GlobalSettings::where('id',$record[0]->id)->update([
                'setting_value'=>$request->pickup_weight,
                'type'=>'pickup_weight'
            ]);
            if($result){
                return redirect()->back()->with('success','Pickup request weight updated');
            }
        }
    }

    public function shipment_cancellation_cut_off_days_index() {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        return view('admin.settings.shipment_cancellation_cut_off_days')->with('settings', $settings);
    }


    public function shipment_cancellation_cut_off_days_store(Request $request) {
        $settings = GlobalSettings::where('type', 'shipment_cancellation_cut_off_days')->first();

        $settings->setting_value = $request->shipment_cancellation_cut_off_days;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
    public function auto_account_disabled_days_index() {
        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();

        return view('admin.settings.auto_account_disabled_days')->with('settings', $settings);
    }
    public function auto_account_disabled_days_store(Request $request) {
        $settings = GlobalSettings::where('type', 'auto_account_disabled_days')->first();

        $settings->setting_value = $request->auto_account_disabled_days;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
    public function non_service_area_index() {
    return view('admin.settings.non_service_area');
    }

    public function non_service_area_store(Request $request) {
        $nsa = NonServiceArea::where('name',$request->non_service_area)->first();
        if($nsa['name'] == $request->non_service_area)
        {
            return redirect()->back()->with('error', 'Non Service Area Is Already Updated!');
        }
        else
        {
            $create=NonServiceArea::create(['name' => $request->non_service_area]);
            return redirect()->back()->with('success', 'Non Service Area Updated!');
        }
    }

    public function daily_pickup_sales_cron_index(){
        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time')->first();

        return view('admin.settings.daily_pickup_sales_cron_time')->with('settings', $settings);
    }
    public function daily_pickup_sales_cron_store(Request $request) {
        $settings = GlobalSettings::where('type', 'daily_pickup_sales_cron_time')->first();

        $settings->setting_value = $request->daily_pickup_sales_cron_time;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }


    public function ticker_index() {
        $admin_ticker = NULL;
        $shipper_ticker = NULL;

        $settings = GlobalSettings::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ticker = $settings->text;

            if (!empty($ticker)) {
                $admin_ticker = $ticker;
            }
        }

        $settings = GlobalSettings::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ticker = $settings->text;

            if (!empty($ticker)) {
                $shipper_ticker = $ticker;
            }
        }

        return view('admin.settings.ticker')->with(['admin_ticker' => $admin_ticker, 'shipper_ticker' => $shipper_ticker]);
    }

    public function ticker_store(Request $request) {
        $settings = GlobalSettings::where('type', 'admin_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'admin_ticker';
        }

        $settings->text = ($request->admin_ticker) ? $request->admin_ticker : '';

        $settings->save();

        $settings = GlobalSettings::where('type', 'shipper_ticker');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'shipper_ticker';
        }

        $settings->text = ($request->shipper_ticker) ? $request->shipper_ticker : '';

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function petty_cash_heads_index(){
        return view('admin.settings.petty_cash.account_head');
    }
    public function petty_cash_heads_list(Request $request){
        $heads = PettyCashAccountHead::select('id','name','status');
        return Datatables::of($heads)
            ->editColumn('status', function ($heads){
                if($heads->status == 0){
                    return 'Inactive';
                }else{
                    return 'Active';
                }
            })
            ->addColumn('action', function ($heads){
                if (session('role_id') == 1 || count(array_intersect([160,161,162], session('permissions'))) !== 0) {

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if(session('role_id') == 1 || in_array(160, session('permissions'))){
                        $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    }
                    if ($heads->status == 1) {
                        if(session('role_id') == 1 || in_array(162, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item inactive" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Inactive</div></button>';
                        }else{
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';
                        }
                    } else {
                        if(session('role_id') == 1 || in_array(161, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Active</div></button>';
                        }else{
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';
                        }
                    }

                    return $dropdown;
                }else{
                    return '';
                }
            })
        
        ->make(true);
    }

    public function petty_cash_heads_add(Request $request){
        $head = trim($request->head);
        if($head){
            $account_head = new PettyCashAccountHead();
            $account_head->name = $head;
            $account_head->save();

            return response()->json(['status' => 1, 'success' => 'Head of Account successfully added!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty']);
        }
    }

    public function petty_cash_heads_edit(Request $request){
        $head = $request->head_id;
        if($head){
            $account_head = PettyCashAccountHead::find($head);
            $account_head->name = $request->account_head;
            $account_head->save();

            return response()->json(['status' => 1, 'success' => 'Head of Account successfully updated!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty!']);
        }
    }

    public function petty_cash_heads_active(Request $request){
        $head = $request->head_id;
        if($head){
            $account_head = PettyCashAccountHead::find($head);
            if($account_head->status == 0){
                $account_head->status = 1;
                $account_head->save();
                return response()->json(['status' => 1, 'success' => 'Head of Account successfully activated!']);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Head of Account is already active!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty!']);
        }
    }

    public function petty_cash_heads_inactive(Request $request){
        $head = $request->head_id;
        if($head){
            $account_head = PettyCashAccountHead::find($head);
            if($account_head->status == 1){
                $account_head->status = 0;
                $account_head->save();
                return response()->json(['status' => 1, 'success' => 'Head of Account successfully inactivated!']);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Head of Account is already inactive!']);
            }

        }
        else{
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty!']);
        }
    }

    public function petty_cash_titles_index(){
        $heads = PettyCashAccountHead::where('status',1)->get();
        return view('admin.settings.petty_cash.account_title')->with(['heads' => $heads]);
    }
    public function petty_cash_titles_list(Request $request){
        $heads = PettyCashAccountTitle::select('id','name','status');
        return Datatables::of($heads)
            ->editColumn('status', function ($heads){
                if($heads->status == 0){
                    return 'Inactive';
                }else{
                    return 'Active';
                }
            })
            ->addColumn('action', function ($heads){
                if (session('role_id') == 1 || count(array_intersect([160,161,162], session('permissions'))) !== 0) {
                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';
                    if(session('role_id') == 1 || in_array(164, session('permissions'))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }
                    if ($heads->status == 1) {
                        if(session('role_id') == 1 || in_array(166, session('permissions'))) {
                            $dropdown .= '<button type="button" class="dropdown-item inactive" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Inactive</div></button>';
                        }else{
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';
                        }
                    } else {
                        if(session('role_id') == 1 || in_array(165, session('permissions'))) {

                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Active</div></button>';
                        }else{
                            $dropdown .= '<button type="button" class="dropdown-item" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x"></i></div><div class="col-9 offset-1">No Action</div></button>';

                        }
                    }

                    return $dropdown;
                }else{
                    return '';
                }
            })

            ->make(true);
    }

    public function petty_cash_titles_add(Request $request){
        $title = trim($request->title);
        $heads = array();
        $heads = $request->heads;
        if($title){
            $account_title = new PettyCashAccountTitle();
            $account_title->name = $title;
            $account_title->save();
            foreach($heads as $head){
                $head_title = new PettyCashAccountHeadAccountTitle();
                $head_title->petty_cash_account_head_id = $head;
                $head_title->petty_cash_account_title_id = $account_title->id;
                $head_title->save();
            }
            return response()->json(['status' => 1, 'success' => 'Head of Account successfully added!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Head of Account is empty']);
        }
    }

    public function petty_cash_titles_info(Request $request){
        $title_id = $request->title_id;
        $title = PettyCashAccountTitle::find($title_id);
        if($title){
            $heads = PettyCashAccountHeadAccountTitle::where('petty_cash_account_title_id',$title_id)->pluck('petty_cash_account_head_id')->toArray();

            return response()->json(['status' => 1, 'heads' => $heads, 'title'=>$title]);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Title not found!']);
        }

    }

    public function petty_cash_titles_edit(Request $request){
        $title = trim($request->account_title);
        $heads = array();
        $heads = $request->heads;
        $title_id = $request->title_id;
        if(!$title){
            return response()->json(['status' => 0, 'error' => 'Title not found!']);
        }
        if(!$title_id){
            return response()->json(['status' => 0, 'error' => 'Title ID not found!']);
        }
        if(empty($heads)){
            return response()->json(['status' => 0, 'error' => 'Heads not selected!']);
        }
        $title_details = PettyCashAccountTitle::find($title_id);
        $title_details->name = $title;
        $title_details->save();
        PettyCashAccountHeadAccountTitle::where('petty_cash_account_title_id',$title_id)->delete();
        foreach ($heads as $head){
            $title_heads = new PettyCashAccountHeadAccountTitle();
            $title_heads->petty_cash_account_head_id = $head;
            $title_heads->petty_cash_account_title_id = $title_id;
            $title_heads->save();
        }
        return response()->json(['status' => 1, 'success' => 'Title successfully edited!']);
    }

    public function petty_cash_titles_active(Request $request){
        $title = $request->title_id;
        if($title){
            $account_title = PettyCashAccountTitle::find($title);
            if($account_title->status == 0){
                $account_title->status = 1;
                $account_title->save();
                return response()->json(['status' => 1, 'success' => 'Title of Account successfully activated!']);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Title of Account is already active!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Title of Account is empty!']);
        }
    }

    public function petty_cash_titles_inactive(Request $request){
        $title = $request->title_id;
        if($title){
            $account_title = PettyCashAccountTitle::find($title);
            if($account_title->status == 1){
                $account_title->status = 0;
                $account_title->save();
                return response()->json(['status' => 1, 'success' => 'Title of Account successfully inactivated!']);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Title of Account is already inactive!']);
            }

        }
        else{
            return response()->json(['status' => 0, 'error' => 'Title of Account is empty!']);
        }
    }

	public function walk_in_index(){
        $walk_in_hub_ol=WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 2])->first();
        $walk_in_hub_on=WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 2])->first();
        $walk_in_hub_dn=WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 2])->first();
        $walk_in_door_ol=WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 1])->first();
        $walk_in_door_on=WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 1])->first();
        $walk_in_door_dn=WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 1])->first();
        return view('admin.settings.walk_in')->with(['walk_in_hub_ol' => $walk_in_hub_ol, 'walk_in_hub_on' => $walk_in_hub_on, 'walk_in_hub_dn' => $walk_in_hub_dn, 'walk_in_door_ol' => $walk_in_door_ol, 'walk_in_door_on' => $walk_in_door_on, 'walk_in_door_dn' => $walk_in_door_dn]);
    }
    public function walk_in_store(Request $request){

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_on_a,
            'chargeable_weight' => $request->walk_in_door_on_c,
            'local' => $request->walk_in_door_on_a_local,
            'national' => $request->walk_in_door_on_c_national
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_on_a,
            'chargeable_weight' => $request->walk_in_hub_on_c,
            'local' => $request->walk_in_hub_on_a_local,
            'national' => $request->walk_in_hub_on_c_national
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_ol_a,
            'chargeable_weight' => $request->walk_in_door_ol_c,
            'local' => $request->walk_in_door_ol_a_local,
            'national' => $request->walk_in_door_ol_c_national
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_ol_a,
            'chargeable_weight' => $request->walk_in_hub_ol_c,
            'local' => $request->walk_in_hub_ol_a_local,
            'national' => $request->walk_in_hub_ol_c_national
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_dn_a,
            'chargeable_weight' => $request->walk_in_door_dn_c,
            'local' => $request->walk_in_door_dn_a_local,
            'national' => $request->walk_in_door_dn_c_national
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_dn_a,
            'chargeable_weight' => $request->walk_in_hub_dn_c,
            'local' => $request->walk_in_hub_dn_a_local,
            'national' => $request->walk_in_hub_dn_c_national
        ]);

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function debriefing_report_cut_off_time_index() {
        $settings = GlobalSettings::where('type', 'debriefing_report_cut_off_time')->first();
        $start = GlobalSettings::where('type', 'debriefing_report_cut_off_time_start')->first();

        if ($settings) {
            $cut_off_time = $settings->setting_value;
        }
        else {
            $cut_off_time = 12;
        }
        if ($start) {
            $cut_off_time_start = $start->setting_value;
        }
        else {
            $cut_off_time_start = 12;
        }

        return view('admin.settings.debriefing_report_cut_off_time')->with(['cut_off_time' => $cut_off_time, 'cut_off_time_start' => $cut_off_time_start]);
    }

    public function debriefing_report_cut_off_time_store(Request $request) {
        $settings = GlobalSettings::where('type', 'debriefing_report_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'debriefing_report_cut_off_time';
        }

        $settings->setting_value = $request->debriefing_report_cut_off_time;

        $settings->save();

        $start = GlobalSettings::where('type', 'debriefing_report_cut_off_time_start');

        if ($start->exists()) {
            $start = $start->first();
        }
        else {
            $start = new GlobalSettings();

            $start->type = 'debriefing_report_cut_off_time_start';
        }

        $start->setting_value = $request->debriefing_report_cut_off_time_start;

        $start->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
}