<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Commission\TierType;
use App\Http\Models\Commission\SalesTier;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use DB;

class AdminCommissionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }	


    public function index(){

    	 $TierType = TierType::all(['id','name']);
      return view('admin.settings.commission.index')->with(['TierType'=>$TierType]);
    
    }

     public function tier_list(Request $request){
        $salesTier = SalesTier::join('admins as a', 'a.id', '=', 'sales_tiers.added_by')
            ->leftjoin('admins as u', 'u.id', '=', 'sales_tiers.updated_by')
            ->join('tier_types as tt', 'tt.id', '=', 'sales_tiers.tier_type')
            ->select('sales_tiers.id as tier_id', 'sales_tiers.tier_name as name', 'tt.name as tier_type', 'a.name as added_by', 'u.name as updated_by', 'sales_tiers.status', 'sales_tiers.created_at as added_at', 'sales_tiers.updated_at');
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

                        $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    if ($data->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    } else {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }

                    return $dropdown;

            });

        return  $datatable->make(true);
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
         $tier->tier_type = 1;
         $tier->added_by = Auth::id();
         $tier->updated_by = Auth::id();
         $tier->sales_status =1;
         $tier->commission = $request->tier_commission;
        $tier->status = 1;
        
        $tier->save();
         return redirect()->back()->with(['status'=>1,'success'=>"Tier has been Added successfully!"]);
    }

      public function commission_percentage_index(){
        $settings = GlobalSettings::where('type', 'commission_percentage')->first();
        $percentage = '';
        if($settings){
            $percentage = $settings->setting_value;
        }
        return view('admin.settings.commission.commission_percentage')->with(['commission_percentage' => $percentage]);
    }

    public function commission_percentage_update(Request $request){
        $percentage = $request->projection_percentage;
        $setting = GlobalSettings::where('type', 'commission_percentage');
        if($setting->exists()){
            $setting = $setting->first();
            $setting->setting_value = $percentage;
            $setting->save();
        }else{
            $setting = new GlobalSettings();
            $setting->setting_value = $percentage;
            $setting->type = 'commission_percentage';
            $setting->save();
        }
        return redirect()->back()->with('success', 'Setting updated');
    }
}
