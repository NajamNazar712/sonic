<?php

namespace App\Http\Controllers\Admins;

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
        
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.sales.incentive.territoryindex')->with(['hubs' => $hubs]);
    }

    public function territory_list(Request $request){
        
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),373);
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

    public function territory_add(Request $request)
    {
        $territory = SalesTerritory::where('name', $request->name);

        if (!$territory->exists()) {

            $this->add_territory($request->name,$request->city,$request->code);

            return redirect()->back()->with('success', 'Territory Added Successfully!');
        } else {
            return redirect()->back()->with('success', 'Territory already exists!');
        }
    }
    public function territory_edit($id)
    {
        $hubs = City::where('hub', 1)->where('status', 1)->where('business_category_id', 1)->get();
        $territory = SalesTerritory::find($id);
        return view('admin.sales.incentive.territoryedit')->with(['territory' => $territory,'hubs'=>$hubs]);
    }
    public function territory_update(Request $request, $id)
    {
        $territory = SalesTerritory::find($id);
        $territory->name = $request->edit_name;
        $territory->cityid = $request->edit_city;
        $territory->code = $request->edit_code;
        $territory->updated_by = Auth::id();
        $territory->save();

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
        
        $roles = AdminRole::where('department_id', '=', 7)->get();
        return view('admin.sales.incentive.designationindex')->with(['roles' => $roles]);
    }
    public function designation_list(Request $request){
        
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),373);
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

}
