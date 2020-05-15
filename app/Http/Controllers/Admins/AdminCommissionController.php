<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\http\Models\WarehouseStock;
use Illuminate\Http\Request;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Commission\TierType;
use App\Http\Models\Commission\SalesTier;
use App\Http\Models\Shipper\User;
use App\Http\Models\Commission\SalesCommission;
use App\Http\Models\Commission\SalesCommissionExternalUser;
use App\Http\Models\Commission\SalesCommissionUser;
use App\Http\Models\Admin\Admin;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

class AdminCommissionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }


    public function index(){

        $tier_type = TierType::all(['id','name']);
        $commission_percentage = GlobalSettings::where('type',"commission_percentage")->first();
        $commission_percentage = $commission_percentage->text;
        return view('admin.settings.commission.index')->with(['TierType'=>$tier_type, 'commission_percentage'=>$commission_percentage]);

    }

     public function tier_list(Request $request){
        $salesTier = SalesTier::join('admins as a', 'a.id', '=', 'sales_tiers.added_by')
            ->leftjoin('admins as u', 'u.id', '=', 'sales_tiers.updated_by')
            ->join('tier_types as tt', 'tt.id', '=', 'sales_tiers.tier_type')
            ->select('sales_tiers.id as tier_id', 'sales_tiers.tier_name as name', 'tt.name as tier_type', 'a.name as added_by', 'u.name as updated_by', 'sales_tiers.status', 'sales_tiers.created_at as added_at', 'sales_tiers.updated_at', 'tt.id as type_id','sales_tiers.sales_status');
        $datatable = Datatables::of($salesTier)
            ->addColumn('category_status', function ($data){
                if($data->status == 0){
                    return 'Disable';
                }else{
                    return 'Enable';
                }
            })
            ->addColumn('action', function ($data){

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                        $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    if ($data->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    } else {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }

                    return $dropdown;

            });

        return  $datatable->make(true);
    }

     public function editSalesTierView(Request $request){

        $salesTier = SalesTier::where('id',$request->id)->first();
        $tierType=TierType::where('id',$salesTier->tier_type)->first();
        return response()->json(['status' => 1, 'salesTiers' => $salesTier,'tierType'=>$tierType]);
    }
    public function editSalesTier(Request $request){
        $tier = SalesTier::find($request->id);
         if($tier){
             $tier->tier_name = $request->tier_name;
             $tier->tier_type = $request->tier_type;
             $tier->updated_by = Auth::id();
             $tier->sales_status = $request->has('sales_person_checkbox')? 1:0;
             $tier->commission = $request->tier_commission;
             $tier->save();
             return redirect()->back()->with(['status'=>1,'success'=>"Tier has been Edited successfully!"]);
         }
         return redirect()->back()->with(['status'=>0,'error'=>"Sales Tier not found!"]);
    }

    public function commission_status(Request $request){
        $id = $request->id;
        $status = $request->status;
        $salesTier = SalesTier::find($id);
        if(!$salesTier){
            return response()->json(['status' => 1, 'error' => 'Setting not found!']);
        }

        if($status == 1){
            $salesTier->status = 1;
        }else if($status == 0){
            $salesTier->status = 0;
        }
        $salesTier->save();

        return response()->json(['status' => 0, 'success' => 'Setting updated successfully!']);
    }
    public function add_sales_tier(Request $request){

            $tier = new SalesTier();
            $tier->tier_name = $request->tier_name;
            $tier->tier_type = $request->tier_type;
            $tier->added_by = Auth::id();
            $tier->updated_by = Auth::id();
            $tier->sales_status = $request->has('sales_person_checkbox')? 1:0;
            $tier->commission = $request->tier_commission;
            $tier->status = 1;
            $tier->save();
            return redirect()->back()->with(['status'=>1,'success'=>"Tier has been Added successfully!"]);
    }

    public function setCommission($ids){

        $commission_percentage = '';
        $user_ids = explode(',' , $ids);
        $users = User::whereIn('id', $user_ids)->select('id', 'name')->get();
        $user_names = '';
        foreach($users as $user){
            $user_names = $user_names . $user->name . ' (' . $user->id . ') ';
        }
        $settings = GlobalSettings::where('type', 'commission_percentage');
        if($settings->exists()){
            $settings = $settings->first();
            $commission_percentage = $settings->text;
        }
        $sales_tiers = SalesTier::where('status', 1)->get(['id', 'tier_name', 'tier_type', 'commission','sales_status']);
        $admin_users = Admin::leftjoin('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name','ar.department_id'])->where('admins.status', 1)->get();
        $users = array();
        $sales = array();
        $all_users = array();
        foreach ($admin_users as $u){
            if($u->department_id != 7){
                $users[] = array('id' => $u->id, 'text' => $u->name);
            }else{
                $sales[] = array('id' => $u->id, 'text' => $u->name);
            }
        }
        $all_users['results'][0]['id'] = 'sales';
        $all_users['results'][0]['text'] = 'Sales';
        $all_users['results'][0]['children'] = $sales;
        $all_users['results'][1]['id'] = 'admin';
        $all_users['results'][1]['text'] = 'Admins';
        $all_users['results'][1]['children'] = $users;
        $all_users['pagination']['more'] = true;

        return view('admin.settings.commission.set_commission')->with(['user_ids' => $user_ids,'ids' => $ids, 'user_names' => $user_names,'commission_percentage' => $commission_percentage, 'sales_tiers' => $sales_tiers, 'users' => $all_users]);
    } 
    
    public function set_commission_submit(Request $request){
       // $user_ids = $ids;
    
       $user_ids =explode(',' , $request->user_ids);
       $users='';
     //  $status = 0;
        $users = User::whereIn('id', $user_ids)->select('id', 'name','status')->get();
        $users_for_status = User::whereIn('id', $user_ids)->select('id', 'name','status')->first();
        $status= $users_for_status->status;

        // $users = User::whereIn('id', $user_ids)->select('id', 'name')->get();
        foreach($users as $user){
          $shipper_id = $user->id;
          $status = $user->status;
            $total_commission = $request->total_commission;
            $users_count = count($request->user_id);

            $sales_commission = SalesCommission::where('shipper_id', $shipper_id);
            if($sales_commission->exists()){
                $sales_commission = $sales_commission->first();
                $sales_commission->commission_users_count = $users_count;
                $sales_commission->commission = $total_commission;
                $sales_commission->updated_by = Auth::id();
                $sales_commission->save();
                $sales_commission_id = $sales_commission->id;
                $actual_commission = 0;
                SalesCommissionUser::where('sales_commission_id', $sales_commission_id)->delete();
                foreach($request->tier_id as $row_id => $tier){
                    $sales_tier = SalesTier::find($tier);
                    if($sales_tier){
                        $sales_commission_user = new SalesCommissionUser();
                        $sales_commission_user->sales_commission_id = $sales_commission_id;
                        $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                        $sales_commission_user->tier_id = $tier;
                        if($sales_tier->tier_type == 1){
                            $sales_commission_user->user_id = $request->user_id[$row_id];
                        }else if($sales_tier->tier_type == 2){
                            $external_user = new SalesCommissionExternalUser();
                            $external_user->name = $request->user_id[$row_id];
                            $external_user->shipper_id = $shipper_id;
                            $external_user->save();
                            $sales_commission_user->user_id = $external_user->id;
                        }
                        $sales_commission_user->commission = $request->commission_percentage[$row_id];
                        $actual_commission += $request->commission_percentage[$row_id];
                        $sales_commission_user->save();
                    }
                }
                $sales_commission->commission = $actual_commission;
                $sales_commission->save();

            }else{
                $sales_commission = new SalesCommission();
                $sales_commission->shipper_id = $shipper_id;
                $sales_commission->commission_users_count = $users_count;
                $sales_commission->commission = $total_commission;
                $sales_commission->updated_by = Auth::id();
                $sales_commission->save();
                $sales_commission_id = $sales_commission->id;
                $actual_commission = 0;
                foreach($request->tier_id as $row_id => $tier){
                    $sales_tier = SalesTier::find($tier);
                    if($sales_tier){
                        $sales_commission_user = new SalesCommissionUser();
                        $sales_commission_user->sales_commission_id = $sales_commission_id;
                        $sales_commission_user->tier_type_id = $sales_tier->tier_type;
                        $sales_commission_user->tier_id = $tier;
                        if($sales_tier->tier_type == 1){
                            $sales_commission_user->user_id = $request->user_id[$row_id];
                        }else if($sales_tier->tier_type == 2){
                            $external_user = new SalesCommissionExternalUser();
                            $external_user->name = $request->user_id[$row_id];
                            $external_user->shipper_id = $shipper_id;
                            $external_user->save();
                            $sales_commission_user->user_id = $external_user->id;
                        }
                        $sales_commission_user->commission = $request->commission_percentage[$row_id];
                        $actual_commission += $request->commission_percentage[$row_id];
                        $sales_commission_user->save();
                    }
                }
                $sales_commission->commission = $actual_commission;
                $sales_commission->save();
            }

        }

        if($status== 3)
        {
            return redirect(route('admin.accounts.active'))->with('success','All Rates are Updated');
        }
        else{
            return redirect(route('admin.accounts.pending'))->with('success','All Rates are Updated');
        }

        //Sales Commissison End
    
    }
    public function approveCommission($ids){

        $commission_percentage = '';
        $user_ids = explode(',' , $ids);
        $users = User::whereIn('id', $user_ids)->select('id', 'name')->get();
        $user_names = '';
        foreach($users as $user){
            $user_names = $user_names . $user->name;
        }
       // dd($users);
        $existing_commission_array = array();
        foreach($user_ids  as $user_Id){
            $sale_commission = SalesCommission::where('shipper_id', $user_Id)->first();
            if($sale_commission){
                $sale_commission_users = SalesCommissionUser::where('sales_commission_id', $sale_commission->id)->get();
                if($sale_commission_users){
                    foreach($sale_commission_users as $index => $sale_commission_user){
                        $sales_tier = SalesTier::find($sale_commission_user->tier_id);
                        if($sales_tier){
                            $existing_commission_array[$user_Id][$index]['id'] = $sale_commission_user->id;
                            $existing_commission_array[$user_Id][$index]['sales_commission_id'] = $sale_commission_user->sales_commission_id;
                            $existing_commission_array[$user_Id][$index]['tier_type_id'] = $sales_tier->tier_type;
                            $existing_commission_array[$user_Id][$index]['tier_id'] = $sale_commission_user->tier_id;
                            $existing_commission_array[$user_Id][$index]['tier_name'] = $sales_tier->tier_name;
                            $existing_commission_array[$user_Id][$index]['sales_status'] = $sales_tier->sales_status;
                            if($sales_tier->tier_type == 1){
                                $com_admin = Admin::find($sale_commission_user->user_id);
                                $existing_commission_array[$user_Id][$index]['user_name'] = $com_admin->name;
                                $existing_commission_array[$user_Id][$index]['user_id'] = $com_admin->id;
                            }else if($sales_tier->tier_type == 2){
                                $external_user = SalesCommissionExternalUser::find($sale_commission_user->user_id);
                                $existing_commission_array[$user_Id][$index]['user_name'] = $external_user->name;
                            }
                            $existing_commission_array[$user_Id][$index]['commission'] = $sale_commission_user->commission;
                        }
                    } 
                }
            }
            
        }

           // dd($existing_commission_array);
        return view('admin.settings.commission.approve_commission')->with(['user_ids' => $user_ids, 'ids' => $ids,'users'=>$users,'existing_commission_array' => $existing_commission_array]);
    } 

    public function approve_commission_submit(Request $request){
        $user_ids =explode(',' , $request->user_ids);
            $users = User::whereIn('id', $user_ids)->get();
            if($users){
                foreach($users as $user){
                   if($request->rates_status != null ){
                    if(array_key_exists($user->id, $request->rates_status)){
                        if($request->rate_status[$user->id] != 1){
                            $sale_commission = SalesCommission::where('shipper_id', $user->id)->first();
                            if($sale_commission){
                                $sale_commission_users = SalesCommissionUser::where('sales_commission_id', $sale_commission->id)->get();
                                if($sale_commission_users){
                                    $total_commission = 0;
                                    foreach($sale_commission_users as $sale_commission_user){
                                        if($request->sale_commission != NULL){
                                            if(array_key_exists($sale_commission_user->id, $request->sale_commission)){
                                                $total_commission = $total_commission + $sale_commission_user->commission;
                                            }
                                            else{
                                                if($sale_commission_user->tier_type_id == 2){
                                                    SalesCommissionExternalUser::where('id', $sale_commission_user->user_id)->delete();
                                                }
                                                SalesCommissionUser::where('id', $sale_commission_user->id)->delete();                                        
                                            }
                                        }
                                    }
                                    if($total_commission > 0){
                                        $sale_commission->commission = $total_commission;
                                    }
                                    $sale_commission->status = $request->rate_status[$user->id];
                                    $sale_commission->save();
                                }
                            }
                        }
                    }
                   }   
                }
            }
        
            return redirect(route('admin.accounts.active'))->with('success','Commission updated successfully.');
    }

    public function overall_commission_dashboard(){
        $sale_commission_users = SalesCommission::where('status', 2)->pluck('shipper_id')->toArray();
        if(count($sale_commission_users) > 0){
            $sale_commissions = SalesCommission::where('status', 2)->get();
            $sale_commission = 0;
            $stats = array();
            $date = Carbon::now();
            $first_day = Carbon::parse($date)->firstOfMonth();
            $last_day = Carbon::parse($date)->lastOfMonth();
            $shippers = User::whereIn('id', $sale_commission_users)->select('id', 'name')->get();
            $admins = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.name','admins.id'])->where('status', 1)->where('ar.department_id',7)->get();;
            $stats['booked'] = ShipmentsJourney::leftjoin('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')->where('shipments_journey.shipper_status_id',1)->whereIn('s.user_id', $sale_commission_users)->whereBetween('shipments_journey.created_at',[$first_day, $last_day])->count();
            $stats['received'] = ShipmentsJourney::leftjoin('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')->where('shipments_journey.shipper_status_id',2)->whereIn('s.user_id', $sale_commission_users)->whereBetween('shipments_journey.created_at',[$first_day, $last_day])->count();
            $total_revenue = 0;
            $total_commission = 0;
            foreach ($sale_commissions as $s_commission){
                $shipment = Shipment::where('user_id', $s_commission->shipper_id);
                if($shipment->exists()){
                    $sale_commission = $sale_commission + $s_commission->commission;
                }
                $shipment_revenue = ShipmentsJourney::leftjoin('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')->select(DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->where('shipments_journey.shipper_status_id',2)->where('s.user_id', $s_commission->shipper_id)->whereBetween('shipments_journey.created_at',[$first_day, $last_day])->first();

                $revenue = $shipment_revenue->weight_charges + $shipment_revenue->cash_handling_charges + $shipment_revenue->insurance_charges + $shipment_revenue->return_charges + $shipment_revenue->fuel_surcharge + $shipment_revenue->replacement_charges + $shipment_revenue->try_and_buy_charges + $shipment_revenue->packaging_material_charges + $shipment_revenue->intercept_charges + $shipment_revenue->nsa_osa_charges;

                $commission = $revenue * ($s_commission->commission/100);
                $total_revenue = $total_revenue + $revenue;
                $total_commission = $total_commission + $commission;
            }

            $stats['revenue'] = $total_revenue;
            $stats['commission'] = number_format($total_commission,2,'.','');

            $sales_tier = SalesTier::get();
            return view('admin.commission.overall_commission_dashboard')->with(['stats' => $stats, 'sales_tier' => $sales_tier, 'shippers' => $shippers, 'first_day' => $first_day, 'last_day' => $last_day, 'admins' => $admins]);
        }
    }

    public function overall_commission_dashboard_list(Request $request){
        $date = Carbon::now();
        $sales_tier = $request->sales_tier;

        if($request->get('search_shipper')){
            $sale_commission_users = SalesCommission::where('shipper_id', $request->search_shipper)->where('status', 2)->pluck('shipper_id')->toArray();
        }
        else{
            $sale_commission_users = SalesCommission::where('status', 2)->pluck('shipper_id')->toArray();
        }
        if($request->get('search_date_from')){
            $first_day = $request->search_date_from;
        }
        else{
            $first_day = Carbon::parse($date)->firstOfMonth();
        }
        if($request->get('search_date_to')){
            $last_day = $request->search_date_to;
        }
        else{
            $last_day = Carbon::parse($date)->lastOfMonth();
        }
        if($request->get('search_admin')){
            $admin = $request->search_admin;

            $sale_tier_user = Shipment::join('shipments_journey as sj', 'shipments.id', 'sj.shipment_id')
                ->join('users as u', 'u.id', '=', 'shipments.user_id')
                ->join('sales_commissions as sc', function ($join) {
                    $join->on('sc.shipper_id', '=', 'u.id')
                        ->where('sc.status', 2);
                })
                ->join('sales_commission_users as scu', function ($join) use($admin){
                    $join->on('scu.sales_commission_id', '=', 'sc.id')
                        ->where('scu.tier_id', 1);
                })
                ->select('u.id', 'u.name as shipper_name', 'sc.commission as total_commission', DB::raw('COUNT(IF(sj.shipper_status_id = 1, 1, NULL)) as booked'), DB::raw('COUNT(IF(sj.shipper_status_id = 2, 1, NULL)) as received'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.weight_charges, NULL)) as weight_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.cash_handling_charges, NULL)) as cash_handling_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.insurance_charges, NULL)) as insurance_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.return_charges, NULL)) as return_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.fuel_surcharge, NULL)) as fuel_surcharge'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.replacement_charges, NULL)) as replacement_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.try_and_buy_charges, NULL)) as try_and_buy_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.packaging_material_charges, NULL)) as packaging_material_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.intercept_charges, NULL)) as intercept_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.nsa_osa_charges, NULL)) as nsa_osa_charges'))
                ->whereIn('u.id', $sale_commission_users)
                ->whereIn('sj.shipper_status_id', [1, 2])
                ->whereBetween('sj.created_at',[$first_day, $last_day])
                ->where('scu.user_id', $admin)
                ->groupBy('u.id');


            if (!empty($sales_tier)) {
                foreach ($sales_tier as $sale_tier) {
                    if($sale_tier['id'] == 1){
                        $name = $sale_tier['tier_name'] . 'commission';
                        $commission_name = strtolower(str_replace(' ', '', $name));
                        $sale_tier_user->addselect(DB::raw('(select admins.name from sales_commission_users JOIN admins ON admins.id = sales_commission_users.user_id WHERE sales_commission_users.sales_commission_id = sc.id and admins.id = '. $admin .' and sales_commission_users.tier_id = ' . $sale_tier['id'] . ') as ' . strtolower(str_replace(' ', '', $sale_tier['tier_name']))), DB::raw('(select commission from sales_commission_users JOIN admins ON admins.id = sales_commission_users.user_id WHERE sales_commission_users.sales_commission_id = sc.id and admins.id = '. $admin .' and sales_commission_users.tier_id = ' . $sale_tier['id'] . ') as ' . $commission_name));
                    }
                }
            }

            $datatable =  Datatables::of($sale_tier_user)
                ->addColumn('revenue' ,function($sale_tier_user){
                    $revenue = $sale_tier_user->weight_charges + $sale_tier_user->cash_handling_charges + $sale_tier_user->insurance_charges + $sale_tier_user->return_charges + $sale_tier_user->fuel_surcharge + $sale_tier_user->replacement_charges + $sale_tier_user->try_and_buy_charges + $sale_tier_user->packaging_material_charges + $sale_tier_user->intercept_charges + $sale_tier_user->nsa_osa_charges;
                    return $revenue;
                })
                ->addColumn('total_commission_amount' ,function($sale_tier_user) use($first_day,$last_day){
                    $revenue = $sale_tier_user->weight_charges + $sale_tier_user->cash_handling_charges + $sale_tier_user->insurance_charges + $sale_tier_user->return_charges + $sale_tier_user->fuel_surcharge + $sale_tier_user->replacement_charges + $sale_tier_user->try_and_buy_charges + $sale_tier_user->packaging_material_charges + $sale_tier_user->intercept_charges + $sale_tier_user->nsa_osa_charges;
                    return number_format($revenue * ($sale_tier_user->total_commission/100),2,'.','');;
                });

            if (!empty($sales_tier)) {
                foreach ($sales_tier as $sale_tier) {
                    if($sale_tier['id'] == 1) {
                        $name = $sale_tier['tier_name'] . 'amount';
                        $commission_name = $sale_tier['tier_name'] . 'commission';
                        $name = strtolower(str_replace(' ', '', $name));
                        $commission_name = strtolower(str_replace(' ', '', $commission_name));

                        $datatable->addColumn($name, function ($sale_tier_user) use ($commission_name) {
                            $revenue = $sale_tier_user->weight_charges + $sale_tier_user->cash_handling_charges + $sale_tier_user->insurance_charges + $sale_tier_user->return_charges + $sale_tier_user->fuel_surcharge + $sale_tier_user->replacement_charges + $sale_tier_user->try_and_buy_charges + $sale_tier_user->packaging_material_charges + $sale_tier_user->intercept_charges + $sale_tier_user->nsa_osa_charges;
                            return number_format($revenue * ($sale_tier_user[$commission_name] / 100), 2, '.', '');
                        });
                    }
                }
            }
            return $datatable->make(true);
        }
        else{
            $sale_tier_user = Shipment::join('shipments_journey as sj', 'shipments.id', 'sj.shipment_id')
                ->join('users as u', 'u.id', '=', 'shipments.user_id')
                ->join('sales_commissions as sc', function ($join) {
                    $join->on('sc.shipper_id', '=', 'u.id')
                        ->where('sc.status', 2);
                })
                ->select('u.id', 'u.name as shipper_name', 'sc.commission as total_commission', DB::raw('COUNT(IF(sj.shipper_status_id = 1, 1, NULL)) as booked'), DB::raw('COUNT(IF(sj.shipper_status_id = 2, 1, NULL)) as received'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.weight_charges, NULL)) as weight_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.cash_handling_charges, NULL)) as cash_handling_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.insurance_charges, NULL)) as insurance_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.return_charges, NULL)) as return_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.fuel_surcharge, NULL)) as fuel_surcharge'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.replacement_charges, NULL)) as replacement_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.try_and_buy_charges, NULL)) as try_and_buy_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.packaging_material_charges, NULL)) as packaging_material_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.intercept_charges, NULL)) as intercept_charges'), DB::raw('SUM(IF(sj.shipper_status_id = 2, shipments.nsa_osa_charges, NULL)) as nsa_osa_charges'))
                ->whereIn('u.id', $sale_commission_users)
                ->whereIn('sj.shipper_status_id', [1, 2])
                ->whereBetween('sj.created_at',[$first_day, $last_day])
                ->groupBy('u.id');

            if (!empty($sales_tier)) {
                foreach ($sales_tier as $sale_tier) {
                    $name = $sale_tier['tier_name'] . 'commission';
                    $commission_name = strtolower(str_replace(' ', '', $name));
                    $sale_tier_user->addselect(DB::raw('(select IF(sales_commission_users.tier_type_id = 1, admins.name, sales_commission_external_users.name) from sales_commission_users LEFT JOIN admins ON admins.id = sales_commission_users.user_id LEFT JOIN sales_commission_external_users ON sales_commission_external_users.id = sales_commission_users.user_id WHERE sales_commission_users.sales_commission_id = sc.id and sales_commission_users.tier_id = ' . $sale_tier['id'] . ') as ' . strtolower(str_replace(' ', '', $sale_tier['tier_name']))), DB::raw('(select commission from sales_commission_users WHERE sales_commission_users.sales_commission_id = sc.id and sales_commission_users.tier_id = ' . $sale_tier['id'] . ') as ' . $commission_name));
                }
            }
            $datatable =  Datatables::of($sale_tier_user)
                ->addColumn('revenue' ,function($sale_tier_user){
                    $revenue = $sale_tier_user->weight_charges + $sale_tier_user->cash_handling_charges + $sale_tier_user->insurance_charges + $sale_tier_user->return_charges + $sale_tier_user->fuel_surcharge + $sale_tier_user->replacement_charges + $sale_tier_user->try_and_buy_charges + $sale_tier_user->packaging_material_charges + $sale_tier_user->intercept_charges + $sale_tier_user->nsa_osa_charges;
                    return $revenue;
                })
                ->addColumn('total_commission_amount' ,function($sale_tier_user) use($first_day,$last_day){
                    $revenue = $sale_tier_user->weight_charges + $sale_tier_user->cash_handling_charges + $sale_tier_user->insurance_charges + $sale_tier_user->return_charges + $sale_tier_user->fuel_surcharge + $sale_tier_user->replacement_charges + $sale_tier_user->try_and_buy_charges + $sale_tier_user->packaging_material_charges + $sale_tier_user->intercept_charges + $sale_tier_user->nsa_osa_charges;
                    return number_format($revenue * ($sale_tier_user->total_commission/100),2,'.','');;
                });

            if (!empty($sales_tier)) {
                foreach ($sales_tier as $sale_tier) {
                    $name = $sale_tier['tier_name'] . 'amount';
                    $commission_name = $sale_tier['tier_name'] . 'commission';
                    $name = strtolower(str_replace(' ', '', $name));
                    $commission_name = strtolower(str_replace(' ', '', $commission_name));

                    $datatable->addColumn($name ,function($sale_tier_user) use($commission_name){
                        $revenue = $sale_tier_user->weight_charges + $sale_tier_user->cash_handling_charges + $sale_tier_user->insurance_charges + $sale_tier_user->return_charges + $sale_tier_user->fuel_surcharge + $sale_tier_user->replacement_charges + $sale_tier_user->try_and_buy_charges + $sale_tier_user->packaging_material_charges + $sale_tier_user->intercept_charges + $sale_tier_user->nsa_osa_charges;
                        return number_format($revenue * ($sale_tier_user[$commission_name]/100),2,'.','');
                    });
                }
            }
            return $datatable->make(true);
        }
    }

    public function overall_commission_dashboard_data(Request $request){

        $date = Carbon::now();
        if($request->get('search_shipper')){
            if($request->get('search_admin')){
                $sale_commission_users = SalesCommission::join('sales_commission_users as scu', 'scu.sales_commission_id', '=', 'sales_commissions.id')->where('scu.tier_type_id', 1)->where('scu.tier_id',1)->where('scu.user_id', $request->search_admin)->where('shipper_id', $request->search_shipper)->where('status', 2)->pluck('shipper_id')->toArray();
            }
            else{
                $sale_commission_users = SalesCommission::where('shipper_id', $request->search_shipper)->where('status', 2)->pluck('shipper_id')->toArray();
            }
        }
        else{
            if($request->get('search_admin')){
                $sale_commission_users = SalesCommission::join('sales_commission_users as scu', 'scu.sales_commission_id', '=', 'sales_commissions.id')->where('scu.tier_type_id', 1)->where('scu.tier_id',1)->where('scu.user_id', $request->search_admin)->where('status', 2)->pluck('shipper_id')->toArray();
            }
            else{
                $sale_commission_users = SalesCommission::where('status', 2)->pluck('shipper_id')->toArray();
            }
        }
        if($request->get('search_date_from')){
            $first_day = $request->search_date_from;
        }
        else{
            $first_day = Carbon::parse($date)->firstOfMonth();
        }
        if($request->get('search_date_to')){
            $last_day = $request->search_date_to;
        }
        else{
            $last_day = Carbon::parse($date)->lastOfMonth();
        }
        if(count($sale_commission_users) > 0) {
            $sale_commissions = SalesCommission::where('status', 2)->get();
            $sale_commission = 0;
            $stats = array();
            $stats['booked'] = ShipmentsJourney::leftjoin('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')->where('shipments_journey.shipper_status_id', 1)->whereIn('s.user_id', $sale_commission_users)->whereBetween('shipments_journey.created_at', [$first_day, $last_day])->count();
            $stats['received'] = ShipmentsJourney::leftjoin('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')->where('shipments_journey.shipper_status_id', 2)->whereIn('s.user_id', $sale_commission_users)->whereBetween('shipments_journey.created_at', [$first_day, $last_day])->count();
            $total_revenue = 0;
            $total_commission = 0;
            foreach ($sale_commissions as $s_commission) {
                if (in_array($s_commission->shipper_id, $sale_commission_users)) {
                    $shipment = Shipment::where('user_id', $s_commission->shipper_id);
                    if ($shipment->exists()) {
                        $sale_commission = $sale_commission + $s_commission->commission;
                    }
                    $shipment_revenue = ShipmentsJourney::leftjoin('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')->select(DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->where('shipments_journey.shipper_status_id', 2)->where('s.user_id', $s_commission->shipper_id)->whereBetween('shipments_journey.created_at', [$first_day, $last_day])->first();

                    $revenue = $shipment_revenue->weight_charges + $shipment_revenue->cash_handling_charges + $shipment_revenue->insurance_charges + $shipment_revenue->return_charges + $shipment_revenue->fuel_surcharge + $shipment_revenue->replacement_charges + $shipment_revenue->try_and_buy_charges + $shipment_revenue->packaging_material_charges + $shipment_revenue->intercept_charges + $shipment_revenue->nsa_osa_charges;

                    $commission = $revenue * ($s_commission->commission / 100);
                    $total_revenue = $total_revenue + $revenue;
                    $total_commission = $total_commission + $commission;
                }
            }

            $stats['revenue'] = $total_revenue;
            $stats['commission'] = number_format($total_commission,2,'.','');

            return response()->json(['status' => 1, 'stats' => $stats]);
        }
    }
}
