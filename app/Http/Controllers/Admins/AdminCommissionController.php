<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Commission\TierType;
use App\Http\Models\Commission\SalesTier;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Http\Models\Shipper\User;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use App\Http\Models\Shipment;
use App\Http\Models\Admin\SalePersonTag;
use DB;

class AdminCommissionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission'); 
    }	


    public function index(){

    	 $TierType = TierType::all(['id','name']);
         $commission_percentage = GlobalSettings::where('type',"commission_percentage")->get();
         return view('admin.settings.commission.index')->with(['TierType'=>$TierType, 'commission_percentage'=>$commission_percentage]);
    
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
          $commission_percentage = GlobalSettings::where('type',"commission_percentage")->get();
        $tier->tier_name = $request->tier_name;
         $tier->tier_type = $request->tier_type;
         $tier->added_by = Auth::id();
         $tier->updated_by = Auth::id();
         // $tier->sales_status =1;
         $tier->sales_status = $request->has('sales_person_checkbox')? 1:0;
          $tier->commission = $request->tier_commission;
            $tier->status = 1;
        //  foreach($commission_percentage as $percentage){
        //      if($percentage->setting_text >= $request->tier_commission)
         
        //     {
                
                 $tier->save();
                 return redirect()->back()->with(['status'=>1,'success'=>"Tier has been Added successfully!"]);
            }
            // else
            // {
            //      return redirect()->back()->with(['status'=>0,'error'=>"You have enter greater value of commission percentage"]);
            // }
            //  }
        
     //   }

     public function dashboard_userwise_index(){
            $user = Auth::user()->name;
            $userId = Auth::user()->id;
             if (session('department_id') == 7){
              // $sale_person_shipments =SalePersonTag::where('sale_person_tags.status', 0)->first();
              $sales_commission_users = 
             }
             else{

             }



            // $user_type = DB::connection('reports')->table('admins')->where('role_id','=',1)->get();
            $today = Carbon::now()->endOfDay();
            $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
            $shippers = DB::connection('reports')->table('users')->where('status','>=',3)->get();
//revenue
            $revenue = array();
            $sale_person_shipments = User::leftjoin('shipments as s','s.user_id','=','users.id')
            ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
            ->leftjoin('sales_commissions as sc','sc.shipper_id','=','users.id')
            ->select('sc.commission as commission',
            // DB::raw('(SELECT count(s.user_id) FROM shipments as sh
            // where sh.shipper_status_id = 1 AND sh.id = sj.shipment_id) 
            // as booked')
            DB::raw('(SELECT count(id) from shipments ) as booked '),
            DB::raw('(SELECT count(shipment_id) from shipments_journey where shipper_status_id = 2 ) as received ')
             ,DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'), DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->get();
            foreach ($sale_person_shipments as $sale_person_shipment) {
              $booked=$sale_person_shipment->booked;
              $received=$sale_person_shipment->received;
              $comm=$sale_person_shipment->commission;
              dd($received);
              //dd($comm);
              $revenue[$sale_person_shipment->id] = $sale_person_shipment->weight_charges + $sale_person_shipment->cash_handling_charges + $sale_person_shipment->insurance_charges + $sale_person_shipment->return_charges + $sale_person_shipment->fuel_surcharge + $sale_person_shipment->replacement_charges + $sale_person_shipment->try_and_buy_charges + $sale_person_shipment->packaging_material_charges + $sale_person_shipment->intercept_charges + $sale_person_shipment->nsa_osa_charges;    
             
            }
            $total_revenue = 0;
            foreach ($revenue as $rev) {
                $total_revenue = $total_revenue + $rev;
                
            }
//commission
            $commission_earned=0;
             foreach ($sale_person_shipments as $commission_datas) {
                 $commission_earned = ($commission_datas->commission * $total_revenue) / 100;
                 dd($commission_earned);
                 
             }

//shipment booked
                     
           
            return view('admin.commission.dashboard_userwise')->with(['commission_earned'=>$commission_earned,'total_revenue'=>$total_revenue,'currentuser'=>$user,'shippers'=>$shippers,'today' => $today, 'thirtyday' => $thirtyDays]);

     }

     public function dashboard_userwise_list(Request $request){
        $revenue = array();
         $commission_data = User::
         leftjoin('shipments as s','s.user_id','=','users.id')
         //Shipment::join('users as u','u.id','=','shipments.user_id')
       //  ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')

        //  $sale_person_shipments = SalePersonTag::leftjoin('admins as a', 'a.id', '=', 'sale_person_tags.admin_id')
        //  ->leftjoin('shipments as s', 's.user_id', '=', 'sale_person_tags.user_id')
        //  ->leftjoin('shipments_journey as sj', 'sj.shipment_id', '=', 's.id')
        //  ->select('a.id as admin_id', 'a.name as admin', DB::raw('count(s.id) as shipment_count'),
        //   DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'),
        //    DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), 
        //    DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), 
        //    DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) 
        //    as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), 
        //    DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'))->groupBy('sale_person_tags.admin_id')
        //    ->where('sale_person_tags.status', 0)->where('s.packaging_material_request', 0)
        // ->where('sj.shipper_status_id', 2)->whereBetween('sj.created_at', [$date_from, $date_to])->get();

        //  leftjoin('shipments as s', function ($join) {
        //     $join->on('s.user_id', '=', 'users.id')
        //       //  ->leftjoin('admins as adsp','adsp.id','=','spt.admin_id')
        //         ->where('s.shipper_status_id','=',2);
        // })
           ->leftjoin('sales_commissions as sc','sc.shipper_id','=','users.id')
           ->select(['users.id as account_id','users.name as shipper','sc.commission as commission',
        //   's.weight_charges','s.cash_handling_charges','s.insurance_charges','s.packaging_charges',
        //   's.return_charges','s.replacement_charges','s.fuel_surcharge','s.try_and_buy_charges',
        //   's.packaging_material_charges','s.intercept_charges','s.nsa_osa_estimated_charges','s.nsa_osa_charges',

           DB::raw('sum(s.weight_charges) as weight_charges'), DB::raw('sum(s.cash_handling_charges) as cash_handling_charges'),
           DB::raw('sum(s.insurance_charges) as insurance_charges'), DB::raw('sum(s.return_charges) as return_charges'), 
           DB::raw('sum(s.fuel_surcharge) as fuel_surcharge'), DB::raw('sum(s.replacement_charges) as replacement_charges'), 
           DB::raw('sum(s.try_and_buy_charges) as try_and_buy_charges'), DB::raw('sum(s.packaging_material_charges) 
           as packaging_material_charges'), DB::raw('sum(s.intercept_charges) as intercept_charges'), 
           DB::raw('sum(s.nsa_osa_charges) as nsa_osa_charges'),DB::raw('sum(s.nsa_osa_estimated_charges) as nsa_osa_estimated_charges'),

                    DB::raw('(SELECT count(id) FROM shipments_journey
                    AS sj WHERE sj.shipment_id = s.id AND sj.shipper_status_id=1) 
                    AS booked'),
                    DB::raw('(SELECT count(id) FROM shipments_journey
                    AS str WHERE str.shipment_id = s.id AND str.shipper_status_id=2) 
                    AS received'),
                   
           ]);

        //    foreach ($sale_person_shipments as $sale_person_shipment) {
        //     $revenue[$sale_person_shipment] = $sale_person_shipment->weight_charges + $sale_person_shipment->cash_handling_charges + $sale_person_shipment->insurance_charges + $sale_person_shipment->return_charges + $sale_person_shipment->fuel_surcharge + $sale_person_shipment->replacement_charges + $sale_person_shipment->try_and_buy_charges + $sale_person_shipment->packaging_material_charges + $sale_person_shipment->intercept_charges + $sale_person_shipment->nsa_osa_charges;
        //    }
          // ->groupBy('users.name');

           $datatable = Datatables::of($commission_data)
       
        ->addColumn('revenue',function($sale){
            $revenue = '';
            $revenue = (($sale->weight_charges != null)? $sale->weight_charges:0) + 
            (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) +
             (($sale->insurance_charges != null)? $sale->insurance_charges:0) +  
             (($sale->return_charges != null)? $sale->return_charges:0) + 
             (($sale->replacement_charges != null)? $sale->replacement_charges:0) + 
             (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + 
             (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + 
             (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0) +
             (($sale->intercept_charges != null)? $sale->intercept_charges:0) +
             (($sale->nsa_osa_estimated_charges != null)? $sale->nsa_osa_estimated_charges:0) +
             (($sale->nsa_osa_charges != null)? $sale->nsa_osa_charges:0) +
             (($sale->packaging_charges != null)? $sale->packaging_charges:0);
            return number_format($revenue);
        })

        ->addColumn('commission_amount',function($sale){
            $amount='';
             $amount = (
                 (($sale->weight_charges != null)? $sale->weight_charges:0) + 
            (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) +
             (($sale->insurance_charges != null)? $sale->insurance_charges:0) +  
             (($sale->return_charges != null)? $sale->return_charges:0) + 
             (($sale->replacement_charges != null)? $sale->replacement_charges:0) + 
             (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + 
             (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + 
             (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0) +
             (($sale->intercept_charges != null)? $sale->intercept_charges:0) +
             (($sale->nsa_osa_estimated_charges != null)? $sale->nsa_osa_estimated_charges:0) +
             (($sale->nsa_osa_charges != null)? $sale->nsa_osa_charges:0) +
             (($sale->packaging_charges != null)? $sale->packaging_charges:0)
             ) * ($sale->commission) / 100 ;


             return number_format((float)$amount, 2);
            });

            if($shipper = $request->get('search_shipper')){
                $datatable->where('users.id', '=', $shipper);
            }

           
           return  $datatable->make(true);
     }
     public function dashboard_userwise_data(Request $request){
        // $stats = array();
        // $shipper = $request->shipper;

        // $stats['booked'] = Shipment::where('shipper_status_id',1);
        //dd($shipper);
        //$stats['booked'] = DB::connection('reports')->table('shipments_journey')->where('shipper_status_id',1)->where('shipment_id', $shipper->id);
        // $stats['booked'] = DB::connection('reports')->table('shipments_journey')->where('shipper_status_id',1)->where('shipment_id', $shipper->id);
        // $stats['received'] = DB::connection('reports')->table('shipments_journey')->where('shipper_status_id',2)->where('shipment_id', $shipper->id);
        //$stats['revenue'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',2)->where('id', $shipper->id);
        // $stats['commission'] = DB::connection('reports')->table('sales_commissions')->where('shipper_id', $shipper);
       //   if ($shipper) {

            // $stats['booked'] = $stats['booked']->where(function($query) {
            //     $query->whereHas('pickup_address.city', function ($sub_query) {
            //         $sub_query->whereIn('hub_id', session('hubs'));
            //     })->orWhereHas('consignee_city', function ($sub_query) {
            //         $sub_query->whereIn('hub_id', session('hubs'));
            //     });
            // });


            // $stats['booked'] = $stats['booked']->whereExists(function($query) use ($shipper) {
            //     $query->from('shipments_journey')
            //         ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'));
            //         // ->whereExists(function ($sub_query) use ($origin) {
            //         //     $sub_query->from('users')
            //         //         ->where('user_shipping_infos.user_id', '=', DB::raw('`users`.`id`'))
            //         //         ->where('users.id', $origin);
            //         // });
            // })->where('shipper_status_id',1)->count(DB::connection('reports')->raw('id'));

         //   $stats['revenue'] = DB::connection('reports')->table('shipments')
            // ->whereExists(function($query) use ($shipper) {
            //     $query->from('users')
            //     ->where('shipments.user_id', '=', DB::raw('`users`.`id`'));
                // ->whereExists(function($sub_query) use ($hub) {
                //     $sub_query->from('cities')
                //     ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                //     ->where('cities.id', $hub->id);
                // });
          //  })
            // ->whereExists(function ($query) use ($date_from, $date_to) {
            //     $query->from('shipments_journey')
            //     ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
            //     ->whereBetween('created_at', [$date_from, $date_to])
            //     ->where('shipper_status_id', 2);
           // })
          //  ->where('shipper_status_id', '=', 2)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

        // $stats['booked'] = $stats['booked']->whereExists(function($query) use ($shipper) {
        //     $query->from('shipments_journey')
        //     ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
        //     ->whereBetween('created_at', [$date_from, $date_to])
        //     ->where('shipper_status_id', 2);
        // })->where('shipments.packaging_material_request', '=', 0)
        //->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
        
        
    //    // });

    //     $stats['received'] = $stats['received']->whereExists(function($query) use ($shipper) {
    //         $query->from('shipments_journey')
    //             ->where('shipments_journey.shipment_id', '=', 'shipments.id');
    //     });
    //     $stats['revenue'] = $stats['revenue']->whereExists(function($query) use ($shipper) {
    //         $query->from('shipments')
    //             ->where('shipments.user_id', '=', 'users.id');
    //     });
    //     $stats['commission'] = $stats['commission']->whereExists(function($query) use ($shipper) {
    //         $query->from('shipments_journey')
    //             ->where('shipments_journey.shipment_id', '=', 'shipments.id');
    //     });

        //  }
//$stats['booked'] = number_format($stats['booked']->count());
       //   $stats['booked'] = number_format($stats['booked']->count());
    //      $stats['received'] = number_format($stats['received']->count());
    //      $stats['revenue'] = number_format($stats['revenue']->count());
    //      $stats['commission'] = number_format($stats['commission']->count());


     //     return response()->json(['status' => 1, 'stats' => $stats]);
    }



     public function dashboard_overall_index(){

        return view('admin.commission.dashboard_overall');

    }
       
}
