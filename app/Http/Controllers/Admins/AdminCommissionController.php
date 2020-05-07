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

}
