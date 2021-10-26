<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalesIncentive;
use App\Http\Models\Admin\SalesIncentiveShipper;
use App\Http\Models\Admin\SalesTerritoryAdmin;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use App\Http\Models\Admin\SalesTerritory;
use App\Http\Models\Admin\SalesDesignation;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\City;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class SalesIncentiveController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function territoryindex(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),458);
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        $designations = SalesDesignation::where('status', 1)->get();
        $admins = Admin::join('admin_roles as ad', 'ad.id', '=', 'admins.role_id')->where('ad.department_id', 7)->select('admins.id', 'admins.name', 'admins.role_id')->get();
        return view('admin.sales.incentive.territoryindex')->with(['hubs' => $hubs, 'designations' => $designations, 'admins' => $admins]);
    }

    public function territory_list(Request $request){
        
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),463);
        }
        $franchise = SalesTerritory::leftjoin('cities as c', 'c.id', '=', 'sales_territories.cityid')
            ->join('admins as a', 'a.id', '=', 'sales_territories.created_by')
            ->join('admins as b', 'b.id', '=', 'sales_territories.updated_by')
            ->select('sales_territories.status as status','sales_territories.id as id','sales_territories.name as name','c.name as city','sales_territories.code as code','a.name as created_by','sales_territories.created_at as created_at','b.name as updated_by','sales_territories.updated_at as updated_at');
        
        $datatables = Datatables::of($franchise)
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return 'Active';
                } else {
                    return 'In-Active';
                }
            })
            ->addColumn('users', function ($data) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-users align-middle"></i> <span class="align-middle">View</span></button>';
            })
            ->addColumn('action', function ($data) {
                $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                     if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $data->id . ' rel="editterritory" data-toggle="modal" data-target="#editterritory"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $dropdown .= '
                    </div>
                  </div>
          ';
                    return $dropdown;
            
        });
          
        return $datatables->make(true);
    }

    public function territory_users(Request $request){
        $territory_admins = SalesTerritoryAdmin::where('territory_id', $request->id);
        if($territory_admins->exists()){
            $territory_admins = $territory_admins->get();
            $details = array();
            foreach ($territory_admins as $territory_admin){
                $details[$territory_admin->designation_id]['designation'] = $territory_admin->sale_designation->role->name;
                $details[$territory_admin->designation_id]['code'] = $territory_admin->sale_designation->code;
                $details[$territory_admin->designation_id]['admins'][] = $territory_admin->user->name;
            }
            return ['status' => 1, 'details' => $details];
        }
        else{
            return ['status' => 0, 'error' => 'Users not found!'];
        }
    }

    public function territory_add(Request $request)
    {
        $territory = SalesTerritory::where('name', $request->name);

        if (!$territory->exists()) {

            $territory_id = $this->add_territory($request->name,$request->city,$request->code);
            if($request->has('designations')){
                if(count($request->designations) > 0){
                    foreach ($request->designations as $designation_id => $admin_ids){
                        if(count($admin_ids) > 0){
                            foreach ($admin_ids as $admin_id){
                                $territory_admins = new SalesTerritoryAdmin();
                                $territory_admins->territory_id = $territory_id;
                                $territory_admins->designation_id = $designation_id;
                                $territory_admins->admin_id = $admin_id;
                                $territory_admins->save();
                            }
                        }
                    }
                }
            }
            return redirect()->back()->with('success', 'Territory Added Successfully!');
        } else {
            return redirect()->back()->with('success', 'Territory already exists!');
        }
    }
    public function territory_edit($id)
    {
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        $territory = SalesTerritory::find($id);
        $territory_admins = SalesTerritoryAdmin::where('territory_id', $territory->id)->get();
        $designations = SalesDesignation::where('status', 1)->get();
        $admins = Admin::join('admin_roles as ad', 'ad.id', '=', 'admins.role_id')->where('ad.department_id', 7)->select('admins.id', 'admins.name', 'admins.role_id')->get();
        return view('admin.sales.incentive.territoryedit')->with(['territory' => $territory, 'territory_admins' => $territory_admins, 'hubs'=>$hubs, 'designations'=>$designations, 'admins'=>$admins]);
    }
    public function territory_update(Request $request, $id)
    {
        $territory = SalesTerritory::find($id);
        $territory->name = $request->edit_name;
        $territory->cityid = $request->edit_city;
        $territory->code = $request->edit_code;
        $territory->updated_by = Auth::id();
        $territory->save();

        SalesTerritoryAdmin::where('territory_id', $territory->id)->delete();

        if($request->has('designations')){
            if(count($request->designations) > 0){
                foreach ($request->designations as $designation_id => $admin_ids){
                    if(count($admin_ids) > 0){
                        foreach ($admin_ids as $admin_id){
                            $territory_admins = new SalesTerritoryAdmin();
                            $territory_admins->territory_id = $territory->id;
                            $territory_admins->designation_id = $designation_id;
                            $territory_admins->admin_id = $admin_id;
                            $territory_admins->save();
                        }
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Territory Updated Successfully!');

    }
    public function territory_enable_disable(Request $request)
    {
        $territory = SalesTerritory::find($request->id);
        if ($request->status == 1) {
            
            $territory->status = 1;
            $territory->updated_by = Auth::id();
            $territory->save();

            return response()->json(['status' => 1, 'success' => 'Territory Enabled Successfully']);
            
        }
        elseif ($request->status == 0) {

            $territory->status = 0;
            $territory->updated_by = Auth::id();
            $territory->save();

            return response()->json(['status' => 1, 'success' => 'Territory Disabled Successfully']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Request']);
        }
    }
    public static function add_territory($name, $city, $code)
    {
        $territory = new SalesTerritory();
        $territory->name = $name;
        $territory->cityid = $city;
        $territory->code = $code;
        $territory->status = 1;
        $territory->created_by = Auth::id();
        $territory->updated_by = Auth::id();
        $territory->save();

        return $territory->id;
    }
    public function designationindex(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),459);
        $roles = AdminRole::where('department_id', '=', 7)->get();
        return view('admin.sales.incentive.designationindex')->with(['roles' => $roles]);
    }
    public function designation_list(Request $request){
        
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),464);
        }
        $franchise = SalesDesignation::join('admins as a', 'a.id', '=', 'sales_designations.created_by')
            ->join('admins as b', 'b.id', '=', 'sales_designations.updated_by')
            ->join('admin_roles as ar', 'ar.id', '=', 'sales_designations.designation')
            ->select('sales_designations.status as status','sales_designations.id as id','ar.name as name','sales_designations.code as code','a.name as created_by','sales_designations.created_at as created_at','b.name as updated_by','sales_designations.updated_at as updated_at');
        
        $datatables = Datatables::of($franchise)
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return 'Active';
                } else {
                    return 'In-Active';
                }
            })
            ->addColumn('action', function ($data) {
                $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                     if ($data->status == 0) {
                        $dropdown .= '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    } else {
                        $dropdown .= '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    }
                    $dropdown .= '<button type="button" class="dropdown-item" data-target-id=' . $data->id . ' rel="editdesignation" data-toggle="modal" data-target="#editdesignation"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $dropdown .= '
                    </div>
                  </div>
          ';
                    return $dropdown;
            
        });
          
        return $datatables->make(true);
    }

    public function designation_add(Request $request)
    {
        $designation = SalesDesignation::where('designation', $request->designation);

        if (!$designation->exists()) {

            $this->add_designation($request->designation,$request->code);

            return redirect()->back()->with('success', 'Designation Added Successfully!');
        } else {
            return redirect()->back()->with('success', 'Designation already exists!');
        }
    }
    public function designation_edit($id)
    {
        $designation = SalesDesignation::find($id);
        $roles = AdminRole::where('department_id', '=', 7)->get();
        return view('admin.sales.incentive.designationedit')->with(['designation' => $designation,'roles' => $roles]);
    }
    public function designation_update(Request $request, $id)
    {
        $designations = SalesDesignation::find($id);
        $designations->designation = $request->designation;
        $designations->code = $request->edit_code;
        $designations->updated_by = Auth::id();
        $designations->save();

        return redirect()->back()->with('success', 'Designation Updated Successfully!');

    }
    public function designation_enable_disable(Request $request)
    {
        $designation = SalesDesignation::find($request->id);
        if ($request->status == 1) {
            
            $designation->status = 1;
            $designation->updated_by = Auth::id();
            $designation->save();

            return response()->json(['status' => 1, 'success' => 'Designation Enabled Successfully']);
            
        }
        elseif ($request->status == 0) {

            $designation->status = 0;
            $designation->updated_by = Auth::id();
            $designation->save();

            return response()->json(['status' => 1, 'success' => 'Designation Disabled Successfully']);
        } else {
            return response()->json(['status' => 0, 'error' => 'Invalid Request']);
        }
    }
    public static function add_designation($name, $code)
    {
        $designation = new SalesDesignation();
        $designation->designation = $name;
        $designation->code = $code;
        $designation->status = 1;
        $designation->created_by = Auth::id();
        $designation->updated_by = Auth::id();
        $designation->save();

        return $designation->id;
    }

    static public function sale_incentive_report_calculation(){

        $from = Carbon::today()->subMonth(1)->firstOfYear()->toDateTimeString();
        $to = Carbon::today()->subMonth(1)->endOfMonth()->toDateTimeString();
        $sales = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id and pending_payment_shipments.type != 2)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id and done_payment_shipments.type != 2)'));
            })
            ->leftJoin('sale_person_tags as spt', function($join){
                $join->on('spt.user_id', '=', 'u.id')
                    ->where('spt.id', '=', DB::raw('(select max(id) from sale_person_tags where sale_person_tags.user_id = u.id and sale_person_tags.status = 0)'));
            })
            ->select('u.id as account_no','oc.id as origin_id','pps.charges as p_total_charges','dps.charges as d_total_charges', 'spt.admin_id as admin_id')
            ->whereNotIn('shipments.shipper_status_id',[1,17])
            ->whereNotIn('u.id', [8761, 9358])
            ->whereBetween('sj.created_at', [$from,$to])
            ->get();

        $incentive_data = array();
        $admin_territories = SalesTerritoryAdmin::join('sales_territories as st', 'st.id', '=', 'sales_territory_admins.territory_id')
            ->join('sales_designations as sd', 'sd.id', '=', 'sales_territory_admins.designation_id')
            ->select('st.id as territory_id', 'st.cityid as city_id', 'sales_territory_admins.admin_id as admin_id', 'sd.id as designation_id')
            ->where('st.status', 1)->get();
        foreach ($admin_territories as $admin_territory){
            if(array_key_exists($admin_territory->city_id, $incentive_data)){
                if(!array_key_exists($admin_territory->admin_id, $incentive_data[$admin_territory->city_id])){
                    $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['territory_id'] = $admin_territory->territory_id;
                    $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['designation_id'] = $admin_territory->designation_id;
                    $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['shipments'] = 0;
                    $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['revenue'] = 0;
                    $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['commission'] = 0.01;
                    $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['total_shippers'] = array();
                }
            }
            else{
                $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['territory_id'] = $admin_territory->territory_id;
                $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['designation_id'] = $admin_territory->designation_id;
                $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['shipments'] = 0;
                $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['revenue'] = 0;
                $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['commission'] = 0.01;
                $incentive_data[$admin_territory->city_id][$admin_territory->admin_id]['total_shippers'] = array();
            }
        }
        foreach ($sales as $index => $sale)
        {
            $total = '';
            if ($sale->p_total_charges != null) {
                $total = $sale->p_total_charges;
            } else if ($sale->d_total_charges != null) {
                $total = $sale->d_total_charges;
            }
            if($total != ''){
                if(array_key_exists($sale->origin_id, $incentive_data)){
                    if(array_key_exists($sale->admin_id, $incentive_data[$sale->origin_id])) {
                        if (!in_array($sale->account_no, $incentive_data[$sale->origin_id][$sale->admin_id]['total_shippers'])) {
                            $incentive_data[$sale->origin_id][$sale->admin_id]['total_shippers'][] = $sale->account_no;
                        }
                        $incentive_data[$sale->origin_id][$sale->admin_id]['revenue'] += $total;
                        $incentive_data[$sale->origin_id][$sale->admin_id]['shipments']++;
                    }
                }
            }
        }
        $date = Carbon::yesterday()->format('Y-m-d');
        foreach ($incentive_data as $city_wise_data){
            foreach ($city_wise_data as $admin_id => $data){
                $commission = $data['revenue'] * $data['commission'];
                $existing_incentive = SalesIncentive::where('territory_id', $data['territory_id'])
                    ->where('designation_id', $data['designation_id'])
                    ->where('admin_id', $admin_id)
                    ->where('from', '<=', $date)
                    ->where('to', '>=', $date);
                if($existing_incentive->exists()){
                    $incentive = $existing_incentive->first();
                    $incentive_shippers = SalesIncentiveShipper::where('incentive_id', $incentive->id)->pluck('shipper_id')->toArray();
                    $extra_shippers = array_diff($data['total_shippers'], $incentive_shippers);
                    if(count($extra_shippers) > 0){
                        foreach ($extra_shippers as $shipper_id){
                            $incentive_shipper = new SalesIncentiveShipper();
                            $incentive_shipper->incentive_id = $incentive->id;
                            $incentive_shipper->shipper_id = $shipper_id;
                        }
                        $incentive_shippers = array_merge($incentive_shippers, $extra_shippers);
                    }
                    $total_shipments = $incentive->shipment_count + $data['shipments'];
                    $revenue = $incentive->revenue + $data['revenue'];
                    $incentive->shipper_count = count($incentive_shippers);
                    $incentive->shipment_count = $total_shipments;
                    $incentive->revenue = $revenue;
                    $incentive->commission = $commission;
                    $incentive->save();
                }
                else{
                    $total_shipments = $data['shipments'];
                    $revenue = $data['revenue'];
                    $incentive = new SalesIncentive();
                    $incentive->territory_id = $data['territory_id'];
                    $incentive->designation_id = $data['designation_id'];
                    $incentive->admin_id = $admin_id;
                    $incentive->shipper_count = count($data['total_shippers']);
                    $incentive->shipment_count = $total_shipments;
                    $incentive->revenue = $revenue;
                    $incentive->commission = $commission;
                    $incentive->from = $from;
                    $incentive->to = $to;
                    $incentive->save();
                    if(count($data['total_shippers']) > 0){
                        foreach ($data['total_shippers'] as $shipper_id){
                            $incentive_shipper = new SalesIncentiveShipper();
                            $incentive_shipper->incentive_id = $incentive->id;
                            $incentive_shipper->shipper_id = $shipper_id;
                        }
                    }
                }
            }
        }
    }
}
