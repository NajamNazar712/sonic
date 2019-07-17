<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\FuelFactorHistory;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountHeadAccountTitle;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\WalkInStandardWeightCharge;

use App\Http\Models\CorporateFuelSurcharge;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\FuelSurcharge;
use App\Http\Models\Rates\HistoryCorporateFuelSurcharge;
use App\Http\Models\Rates\HistoryCorporateWeightCharge;
use App\Http\Models\Rates\HistoryFuelSurcharge;
use App\Http\Models\Rates\HistoryWeightCharge;
use App\Http\Models\RateStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\WeightCharge;
use App\Http\Models\WeightChargeFactorHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $current_nsa = NonServiceArea::all();

        if ($current_nsa) {
            $current_nsa = $current_nsa->pluck('name')->toArray();

            $current_nsa = implode(',', $current_nsa);
        }

        return view('admin.settings.non_service_area')->with('current_nsa', $current_nsa);
    }

    public function non_service_area_store(Request $request) {
        $new_nsa = explode(',', $request->non_service_areas);

        $current_nsa = NonServiceArea::pluck('name')->toArray();

        $add_nsa = array_diff($new_nsa, $current_nsa);
        $delete_nsa = array_diff($current_nsa, $new_nsa);

        if (!empty($delete_nsa)) {
            NonServiceArea::whereIn('name', $delete_nsa)->delete();
        }

        foreach ($add_nsa as $name) {
            $nsa = new NonServiceArea();

            $nsa->name = $name;

            $nsa->save();
        }

        return redirect()->back()->with('success', 'Non Service Area(s) Updated!');
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
            'chargeable_weight_local' => $request->walk_in_door_on_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_door_on_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_door_on_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_door_on_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_door_on_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_door_on_a_local,
            'national_charges_class_0'=> $request->walk_in_door_on_return_class_0_charges,
            'national_charges_class_1'=> $request->walk_in_door_on_return_class_1_charges,
            'national_charges_class_2'=> $request->walk_in_door_on_return_class_2_charges,
            'national_charges_class_3'=> $request->walk_in_door_on_return_class_3_charges
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 1, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_on_a,
            'chargeable_weight_local' => $request->walk_in_hub_on_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_hub_on_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_hub_on_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_hub_on_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_hub_on_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_hub_on_a_local,
            'national_charges_class_0'=> $request->walk_in_hub_on_return_class_0_charges,
            'national_charges_class_1'=> $request->walk_in_hub_on_return_class_1_charges,
            'national_charges_class_2'=> $request->walk_in_hub_on_return_class_2_charges,
            'national_charges_class_3'=> $request->walk_in_hub_on_return_class_3_charges
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_ol_a,
            'chargeable_weight_local' => $request->walk_in_door_ol_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_door_ol_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_door_ol_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_door_ol_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_door_ol_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_door_ol_a_local,
            'national_charges_class_0'=> $request->walk_in_door_ol_return_class_0_charges,
            'national_charges_class_1'=> $request->walk_in_door_ol_return_class_1_charges,
            'national_charges_class_2'=> $request->walk_in_door_ol_return_class_2_charges,
            'national_charges_class_3'=> $request->walk_in_door_ol_return_class_3_charges
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_ol_a,
            'chargeable_weight_local' => $request->walk_in_hub_ol_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_hub_ol_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_hub_ol_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_hub_ol_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_hub_ol_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_hub_ol_a_local,
            'national_charges_class_0'=> $request->walk_in_hub_ol_return_class_0_charges,
            'national_charges_class_1'=> $request->walk_in_hub_ol_return_class_1_charges,
            'national_charges_class_2'=> $request->walk_in_hub_ol_return_class_2_charges,
            'national_charges_class_3'=> $request->walk_in_hub_ol_return_class_3_charges
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 1])->update([
            'actual_weight' => $request->walk_in_door_dn_a,
            'chargeable_weight_local' => $request->walk_in_door_dn_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_door_dn_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_door_dn_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_door_dn_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_door_dn_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_door_dn_a_local,
            'national_charges_class_0'=> $request->walk_in_door_dn_return_class_0_charges,
            'national_charges_class_1'=> $request->walk_in_door_dn_return_class_1_charges,
            'national_charges_class_2'=> $request->walk_in_door_dn_return_class_2_charges,
            'national_charges_class_3'=> $request->walk_in_door_dn_return_class_3_charges
        ]);

        WalkInStandardWeightCharge::where(['shipping_mode_id' => 3, 'delivery_type_id' => 2])->update([
            'actual_weight' => $request->walk_in_hub_dn_a,
            'chargeable_weight_local' => $request->walk_in_hub_dn_chargeable_weight_local,
            'chargeable_weight_charges_class_0' => $request->walk_in_hub_dn_chargeable_weight_class_0_charges,
            'chargeable_weight_charges_class_1' => $request->walk_in_hub_dn_chargeable_weight_class_1_charges,
            'chargeable_weight_charges_class_2' => $request->walk_in_hub_dn_chargeable_weight_class_2_charges,
            'chargeable_weight_charges_class_3' => $request->walk_in_hub_dn_chargeable_weight_class_3_charges,
            'local' => $request->walk_in_hub_dn_a_local,
            'national_charges_class_0'=> $request->walk_in_hub_dn_return_class_0_charges,
            'national_charges_class_1'=> $request->walk_in_hub_dn_return_class_1_charges,
            'national_charges_class_2'=> $request->walk_in_hub_dn_return_class_2_charges,
            'national_charges_class_3'=> $request->walk_in_hub_dn_return_class_3_charges
        ]);

        return redirect()->back()->with('success', 'Settings Updated!');
    }

	

	public function debriefing_report_cut_off_time_index() {
        $settings = GlobalSettings::where('type', 'debriefing_report_arrival_cut_off_time')->first();

        if ($settings) {
            $arrival_cut_off_time = $settings->setting_value;
        }
        else {
            $arrival_cut_off_time = 12;
        }

        $settings = GlobalSettings::where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        }
        else {
            $day_cut_off_time = 12;
        }

        return view('admin.settings.debriefing_report_cut_off_time')->with(['arrival_cut_off_time' => $arrival_cut_off_time, 'day_cut_off_time' => $day_cut_off_time]);
    }

    public function debriefing_report_cut_off_time_store(Request $request) {
        $settings = GlobalSettings::where('type', 'debriefing_report_arrival_cut_off_time');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'debriefing_report_arrival_cut_off_time';
        }

        $settings->setting_value = $request->debriefing_report_arrival_cut_off_time;

        $settings->save();

        $start = GlobalSettings::where('type', 'debriefing_report_day_cut_off_time');

        if ($start->exists()) {
            $start = $start->first();
        }
        else {
            $start = new GlobalSettings();

            $start->type = 'debriefing_report_day_cut_off_time';
        }

        $start->setting_value = $request->debriefing_report_day_cut_off_time;

        $start->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }
	public function auto_invoice_generation_and_due_date_index(){
        $auto_invoice_generation = GlobalSettings::where('type', 'auto_invoice_generation_time')->first();
        $due_date_days = GlobalSettings::where('type', 'due_date_days')->first();
        return view('admin.settings.auto_invoice_generation_and_due_date_index')->with(['auto_invoice_generation_time' => $auto_invoice_generation,'due_date_days' => $due_date_days]);
    }

    public function auto_invoice_generation_and_due_date_store(Request $request){

        $settings_invoice = GlobalSettings::where('type', 'auto_invoice_generation_time')->first();
        $settings_due_date = GlobalSettings::where('type', 'due_date_days')->first();

        $settings_invoice->setting_value = $request->auto_invoice_generation_hours;
        $settings_due_date->setting_value = $request->due_date_days;

        $settings_invoice->save();
        $settings_due_date->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

	public function fuel_factor_index(){
        return view('admin.settings.fuel_factor');
    }

    public function fuel_factor_store(Request $request)
    {
        $fuel_factor = $request->fuel_factor;
        if ($fuel_factor != null) {
            $shipping_modes = ShippingMode::all();
            $users = User::where('status', 3)->select('id', 'account_type_id')->get();
            if (!$users->isEmpty()) {
                foreach ($users as $user) {
                    foreach ($shipping_modes as $shipping_mode) {
                        if ($user->account_type_id == 1) {
                            $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        } else {
                            $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        }

                        if ($rate_status->exists()) {
                            $rate_status = $rate_status->first();

                            if ($user->account_type_id == 1) {
                                $fuel_surcharge = FuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            } else {
                                $fuel_surcharge = CorporateFuelSurcharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                            }
                            if ($fuel_surcharge->exists()) {
                                $fuel_surcharge = $fuel_surcharge->first();

                                if ($rate_status->fuel_charges == 1) {
                                    $update_fuel_surcharge = $fuel_surcharge->fuel_surcharge + $fuel_factor;
                                } else {
                                    $update_fuel_surcharge = $fuel_factor;

                                    $rate_status->fuel_charges = 1;
                                    $rate_status->save();
                                }

                                if ($update_fuel_surcharge >= 0) {
                                    $fuel_surcharge->fuel_surcharge = $update_fuel_surcharge;
                                } else {
                                    $fuel_surcharge->fuel_surcharge = 0;
                                }
                                $fuel_surcharge->save();

                                if ($user->account_type_id == 1) {
                                    $fuel_surcharge_history = new HistoryFuelSurcharge();
                                } else {
                                    $fuel_surcharge_history = new HistoryCorporateFuelSurcharge();
                                }

                                $fuel_surcharge_history->user_id = $user->id;
                                $fuel_surcharge_history->shipping_mode_id = $shipping_mode->id;
                                $fuel_surcharge_history->fuel_surcharge = $update_fuel_surcharge;
                                $fuel_surcharge_history->save();

                            } else {
                                $rate_status->fuel_charges = 1;
                                $rate_status->save();
                                if ($user->account_type_id == 1) {
                                    $fuel_surcharge = new FuelSurcharge();
                                    $fuel_surcharge->user_id = $user->id;
                                    $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                    $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                    $fuel_surcharge->save();
                                } else {
                                    $fuel_surcharge = new CorporateFuelSurcharge();
                                    $fuel_surcharge->user_id = $user->id;
                                    $fuel_surcharge->shipping_mode_id = $shipping_mode->id;
                                    $fuel_surcharge->fuel_surcharge = $fuel_factor;
                                    $fuel_surcharge->save();
                                }

                            }
                        }

                    }
                }

                $fuel_factor_history = new FuelFactorHistory();
                $fuel_factor_history->fuel_factor = $fuel_factor;
                $fuel_factor_history->admin_id = Auth::id();
                $fuel_factor_history->save();

                return redirect()->back()->with('success', 'Fuel Factor Updated!');
            } else {
                return redirect()->back()->with('error', 'Fuel Factor failed to update!');
            }
        }
    }

    public function return_note_restriction_bypass_index(){
        $role_ids = array();

        $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

        if($settings->exists()){
            $settings = $settings->first();
            $role_ids = array_map('intval', explode(',', $settings->text));
        }

        $roles = AdminRole::with('department')->where('id', '!=', 1)->get();

        return view('admin.settings.return_note_restriction_bypass')->with(['roles'=>$roles,'role_ids' => $role_ids]);
    }

    public function return_note_restriction_bypass_store(Request $request){
        if($request->has('roles')){
            $roles = implode(',', $request->roles);
            $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

            if ($settings->exists()) {
                $settings = $settings->first();
            }
            else {
                $settings = new GlobalSettings();

                $settings->type = 'return_note_restriction_bypass';
                $settings->setting_value = 0;
                $settings->text = $roles;
            }
            $settings->text = $roles;
            $settings->save();
        }else{
            GlobalSettings::where('type', 'return_note_restriction_bypass')->delete();
        }

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function cod_cap_zones_index(){
        $class_a = GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->first();
        $class_b = GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->first();
        $class_c = GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->first();
        $class_d = GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->first();

        return view('admin.settings.cod_cap_zone')->with(['class_a' => $class_a, 'class_b' => $class_b ,'class_c' => $class_c, 'class_d' => $class_d]);
    }

    public function cod_cap_zones_update(Request $request){
        if($request->class_a != null && $request->class_b != null && $request->class_c != null && $request->class_d != null)
        {
            GlobalSettings::where('type', 'cod_cap_for_zone_class_0')->update([
                'setting_value' =>  $request->class_a
            ]);
            GlobalSettings::where('type', 'cod_cap_for_zone_class_1')->update([
                'setting_value' =>  $request->class_b
            ]);
            GlobalSettings::where('type', 'cod_cap_for_zone_class_2')->update([
                'setting_value' =>  $request->class_c
            ]);
            GlobalSettings::where('type', 'cod_cap_for_zone_class_3')->update([
                'setting_value' =>  $request->class_d
            ]);
            return redirect()->back()->with('success', 'Settings Updated!');
        }
        else{
            return redirect()->back()->with('error', 'Settings can\'t be updated');
        }
    }

    public function ibft_charges_index() {
        $settings = GlobalSettings::where('type', 'ibft_charges');

        if ($settings->exists()) {
            $settings = $settings->first();

            $ibft_charges = $settings->setting_value;
        }
        else {
            $ibft_charges = 0;
        }

        return view('admin.settings.ibft_charges')->with('ibft_charges', $ibft_charges);
    }

    public function ibft_charges_store(Request $request) {
        $settings = GlobalSettings::where('type', 'ibft_charges');

        if ($settings->exists()) {
            $settings = $settings->first();
        }
        else {
            $settings = new GlobalSettings();

            $settings->type = 'ibft_charges';
        }

        $settings->setting_value = $request->ibft_charges;

        $settings->save();

        return redirect()->back()->with('success', 'Settings Updated!');
    }

    public function weight_factor_index(Request $request){
        $settings = GlobalSettings::where('type', 'weight_charges_factor')->first();
        $weight_factor = '';
        if($settings){
            $weight_factor = $settings->setting_value;
        }
        return view('admin.settings.weight_factor')->with(['weight_factor' => $weight_factor]);
    }

    public function weight_factor_update(Request $request){

        $weight_factor = $request->weight_factor;
        if ($weight_factor != null) {
            $settings = GlobalSettings::where('type', 'weight_charges_factor');
            if($settings->exists()){
                $settings = $settings->first();
                $settings->setting_value = $weight_factor;
                $settings->save();
            }else{
                $global_settings = new GlobalSettings();
                $global_settings->setting_value = $weight_factor;
                $global_settings->type = 'weight_charges_factor';
                $global_settings->save();
            }
            $this->weight_factor_account_charges_update($weight_factor);

            return redirect()->back()->with('success', 'Weight Charges Factor is Updated!');

        }
        return redirect()->back()->with('error', 'Settings can\'t be updated');

    }

    public function weight_factor_account_charges_update($weight_factor){
        $shipping_modes = ShippingMode::all();
        $users = User::where('status', 3)->select('id', 'account_type_id')->get();
        if (!$users->isEmpty()) {
            foreach ($users as $user) {
                foreach ($shipping_modes as $shipping_mode) {
                    if ($user->account_type_id == 1) {
                        $rate_status = RateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                    } else {
                        $rate_status = CorporateRateStatus::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                    }

                    if ($rate_status->exists()) {
                        $rate_status = $rate_status->first();

                        if ($user->account_type_id == 1) {
                            $weight_charge = WeightCharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        } else {
                            $weight_charge = CorporateWeightCharge::where('user_id', $user->id)->where('shipping_mode_id', $shipping_mode->id);
                        }
                        if ($weight_charge->exists()) {
                            $weight_charges = $weight_charge->get();

                            foreach($weight_charges as $charge){
                                $local_or_6hr = self::calculate_weight_charges_factor($charge->local_or_6hr);
                                $national_charges_class_0 = self::calculate_weight_charges_factor($charge->national_charges_class_0);

                                if (strpos($charge->national_charges_class_1, '%') == FALSE) {
                                    $national_charges_class_1 = self::calculate_weight_charges_factor($charge->national_charges_class_1);
                                }
                                else {
                                    $national_charges_class_1 = $charge->national_charges_class_1;
                                }

                                if (strpos($charge->national_charges_class_2, '%') == FALSE) {
                                    $national_charges_class_2 = self::calculate_weight_charges_factor($charge->national_charges_class_2);
                                }
                                else {
                                    $national_charges_class_2 = $charge->national_charges_class_2;
                                }

                                if (strpos($charge->national_charges_class_3, '%') == FALSE) {
                                    $national_charges_class_3 = self::calculate_weight_charges_factor($charge->national_charges_class_3);
                                }
                                else {
                                    $national_charges_class_3 = $charge->national_charges_class_3;
                                }

                                if ($user->account_type_id == 1) {
                                    $weight_charge_history = new HistoryWeightCharge();
                                    $weight_charge_history->user_id = $charge->user_id;
                                    $weight_charge_history->shipping_mode_id = $charge->shipping_mode_id;
                                    $weight_charge_history->range_up = $charge->range_up;
                                    $weight_charge_history->range_down = $charge->range_down;
                                    $weight_charge_history->weight_addition = $charge->weight_addition;
                                    $weight_charge_history->spkg = $charge->spkg;
                                    $weight_charge_history->local_or_6hr = $charge->local_or_6hr;
                                    $weight_charge_history->national_charges_class_0 = $charge->national_charges_class_0;
                                    $weight_charge_history->national_charges_class_1 = $charge->national_charges_class_1;
                                    $weight_charge_history->national_charges_class_2 = $charge->national_charges_class_2;
                                    $weight_charge_history->national_charges_class_3 = $charge->national_charges_class_3;
                                    $weight_charge_history->save();

                                    WeightCharge::where('id', $charge->id)->update(['local_or_6hr' => $local_or_6hr, 'national_charges_class_0' => $national_charges_class_0, 'national_charges_class_1' => $national_charges_class_1, 'national_charges_class_2' => $national_charges_class_2, 'national_charges_class_3' => $national_charges_class_3]);

                                } else {
                                    $weight_charge_history = new HistoryCorporateWeightCharge();
                                    $weight_charge_history->user_id = $charge->user_id;
                                    $weight_charge_history->shipping_mode_id = $charge->shipping_mode_id;
                                    $weight_charge_history->delivery_type_id = $charge->delivery_type_id;
                                    $weight_charge_history->range_up = $charge->range_up;
                                    $weight_charge_history->range_down = $charge->range_down;
                                    $weight_charge_history->local_or_6hr = $charge->local_or_6hr;
                                    $weight_charge_history->national_charges_class_0 = $charge->national_charges_class_0;
                                    $weight_charge_history->national_charges_class_1 = $charge->national_charges_class_1;
                                    $weight_charge_history->national_charges_class_2 = $charge->national_charges_class_2;
                                    $weight_charge_history->national_charges_class_3 = $charge->national_charges_class_3;
                                    $weight_charge_history->save();
                                    CorporateWeightCharge::where('id', $charge->id)->update(['local_or_6hr' => $local_or_6hr, 'national_charges_class_0' => $national_charges_class_0, 'national_charges_class_1' => $national_charges_class_1, 'national_charges_class_2' => $national_charges_class_2, 'national_charges_class_3' => $national_charges_class_3]);

                                }

                            }

                        }
                    }

                }
            }

            $weight_factor_history = new WeightChargeFactorHistory();
            $weight_factor_history->weight_factor = $weight_factor;
            $weight_factor_history->admin_id = Auth::id();
            $weight_factor_history->save();

        }
    }

    private function calculate_weight_charges_factor($charges){
        if($charges != 0){
            $weight_factor = GlobalSettings::where('type', 'weight_charges_factor');
            if($weight_factor->exists()){
                $weight_factor = $weight_factor->first();
                $weight_factor_percentage = (floatval($weight_factor->setting_value) / 100) * $charges;
                $charges += $weight_factor_percentage;
                return ROUND($charges, 0, PHP_ROUND_HALF_DOWN);
            }else{
                return $charges;
            }
        }else{
            return $charges;
        }
    }
	public function stock_movement_index(Request $request){
        $settings = GlobalSettings::where('type', 'packaging_material_stock_movement_account_id')->first();
        $account_id = '';
        $account_name = '';
        if($settings){
            $account_id = $settings->setting_value;
            $account_name = User::find($account_id)->name;
        }
        return view('admin.settings.stock_movement')->with(['account_id' => $account_id, 'account_name' => $account_name]);
    }

    public function stock_movement_update(Request $request){

        $stock_movement_account_id = $request->stock_movement_account_id;
        if ($stock_movement_account_id != null) {
            $settings = GlobalSettings::where('type', 'packaging_material_stock_movement_account_id');
            if($settings->exists()){
                $settings = $settings->first();
                $settings->setting_value = $stock_movement_account_id;
                $settings->save();
            }else{
                $global_settings = new GlobalSettings();
                $global_settings->setting_value = $stock_movement_account_id;
                $global_settings->type = 'packaging_material_stock_movement_account_id';
                $global_settings->save();

            }

            return redirect()->back()->with('success', 'Packaging Material Stock Movement Account Updated!');

        }
        return redirect()->back()->with('error', 'Settings can\'t be updated');

    }

}