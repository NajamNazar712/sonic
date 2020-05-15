<?php

namespace App\Http\Controllers\Admins;

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
use DB;

class AdminCommissionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

<<<<<<< HEAD
        $this->middleware('Permission'); 
    }	
=======
        $this->middleware('Permission');
    }
>>>>>>> 92c708419b3c0bb6a8ed75445e5deaf00da64a72


    public function index(){

<<<<<<< HEAD
    	 $TierType = TierType::all(['id','name']);
         $commission_percentage = GlobalSettings::where('type',"commission_percentage")->get();
         return view('admin.settings.commission.index')->with(['TierType'=>$TierType, 'commission_percentage'=>$commission_percentage]);
    
=======
        $tier_type = TierType::all(['id','name']);
        $commission_percentage = GlobalSettings::where('type',"commission_percentage")->first();
        $commission_percentage = $commission_percentage->text;
        return view('admin.settings.commission.index')->with(['TierType'=>$tier_type, 'commission_percentage'=>$commission_percentage]);

>>>>>>> 92c708419b3c0bb6a8ed75445e5deaf00da64a72
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
<<<<<<< HEAD
        $tier = SalesTier::where('id',$request->id)->first();
         $commission_percentage = GlobalSettings::where('type',"commission_percentage")->get();
         $tier->tier_name = $request->tier_name;
         $tier->tier_type = $request->tier_type;
         $tier->updated_by = Auth::id();
         $tier->sales_status = $request->has('sales_person_checkbox')? 1:0;
         $tier->commission = $request->tier_commission;
        
        // foreach($commission_percentage as $percentage){
        //      if($percentage->setting_value >= $request->tier_commission)
         
        //     {
                
                 $tier->save();
                 return redirect()->back()->with(['status'=>1,'success'=>"Tier has been Edited successfully!"]);
          //  }
            // else
            // {
            //      return redirect()->back()->with(['status'=>0,'error'=>"You have enter greater value of commission percentage"]);
            // }
            //  }
        
        
=======
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
>>>>>>> 92c708419b3c0bb6a8ed75445e5deaf00da64a72
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

}
