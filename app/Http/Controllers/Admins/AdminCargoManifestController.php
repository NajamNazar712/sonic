<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\CargoManifest\V2JunctionRoutes;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\CargoManifest\V2JunctionVehicles;
use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Models\City;
use App\Http\Models\JunctionMapping;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use function foo\func;

class AdminCargoManifestController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function manifest_mapping_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),396);
        $cities = City::select(['id', 'name'])->where('business_category_id', 1)->where('hub', 1)->get();
        $vehicles = Fleet::where('status', 1)->select(['id', 'reg_number'])->get();
        return view('admin.cargo.manifest.mapping')->with(['cities' => $cities, 'vehicles' => $vehicles]);
    }

    public function manifest_mapping_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),397);
        }

        $mapping =  V2JunctionMapping::join('cities as oc','oc.id', '=', 'v2_junction_mappings.origin_id')
            ->join('cities as dc','dc.id', '=', 'v2_junction_mappings.destination_id')
            ->join('admins as a','a.id', '=', 'v2_junction_mappings.updated_by')
            ->select('v2_junction_mappings.id as id','v2_junction_mappings.status as status', 'v2_junction_mappings.updated_at as updated_at','oc.name as origin','oc.hub_location_latitude as ohllat','oc.hub_location_longitude as ohllng','dc.name as destination','dc.hub_location_latitude as dhllat','dc.hub_location_longitude as dhllng', 'a.name as updated_by');

        return Datatables::of($mapping)
            ->addColumn('origin_display',function($mapping){
                return "<a href='https://www.google.com/maps/?q=".$mapping->ohllat.",".$mapping->ohllng."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$mapping->origin;
            })
            ->addColumn('destination_display',function($mapping){
                return "<a href='https://www.google.com/maps/?q=".$mapping->dhllat.",".$mapping->dhllng."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$mapping->destination;
            })
            ->addColumn('junctions_display',function($mapping){
                $junctions = V2Junctions::where('junction_mapping_id',$mapping->id)
                    ->join('cities as j','j.id', '=', 'v2_junctions.junction_id')
                    ->select(['j.name as junction','j.hub_location_latitude as jhllat','j.hub_location_longitude as jhllng'])
                    ->get();

                $junction_data = '';
                foreach ($junctions as $j)
                {
                    $junction_data.= "<a href='https://www.google.com/maps/?q=".$j->jhllat.",".$j->jhllng."' target='_blank' class='btn btn-sm btn-outline-info align-middle'><i class='ft-map-pin'></i></a> ".$j->junction ."<br><br>";
                }

                return $junction_data;
            })
            ->addColumn('junctions',function($mapping){
                $junctions = V2Junctions::where('junction_mapping_id',$mapping->id)
                    ->join('cities as j','j.id', '=', 'v2_junctions.junction_id')
                    ->select(['j.name as junction','j.hub_location_latitude as jhllat','j.hub_location_longitude as jhllng'])
                    ->get();

                $junction_data = '';
                foreach ($junctions as $j)
                {
                    $junction_data.= $j->junction.",";
                }

                $junction_data = substr(trim($junction_data), 0, -1);
                if(!$junction_data)
                {
                    $junction_data = "";
                }
                return $junction_data;
            })
            ->addColumn('action',function ($mapping) {
                $dropdown = "";

                if (session('role_id') == 1 || count(array_intersect([549, 550], session('permissions'))) !== 0) {
                    $dropdown .= '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">';

                    if (session('role_id') == 1 || in_array(549, session('permissions'))) {
                        $edit_route = route('admin.cargo.mapping.manifest.edit',$mapping->id);
                        $dropdown .= '<a href="'.$edit_route.'" class="dropdown-item edit_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></div></a>';
                    }
                    if (session('role_id') == 1 || in_array(550, session('permissions'))) {
                        if($mapping->status == 1)
                        {
                            $text = "Disable";
                        }
                        else{
                            $text = "Enable";
                        }
                        $dropdown .= '<button type="button" class="dropdown-item status_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">'.$text.'</div></div></button>';
                    }
                    $dropdown .= '</div>
                        </div>
                    ';
                }

                return $dropdown;
            })
            ->make(true);
    }

    public function manifest_mapping_store(Request $request){

        if(count($request->junctions) > 1 && $request->junctions[1] == null)
        {
            return redirect()->back()->with('error', 'Please Select Junction 1');
        }

        $check = V2JunctionMapping::where([['origin_id', $request->origin], ['destination_id' , $request->destination],['status',1]]);
        if($check->exists()) {
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }

        $mapping = new V2JunctionMapping();

        $mapping->origin_id = $request->origin;
        $mapping->destination_id = $request->destination;
        $mapping->updated_by = Auth::id();
        $mapping->save();

        foreach ($request->junctions as $j) {
            if($j != null) {
                $junction = new V2Junctions();
                $junction->junction_mapping_id = $mapping->id;
                $junction->junction_id = $j;
                $junction->save();
            }
        }

        $previous = $request->origin;
        foreach ($request->route_junctions as $key => $rj)
        {
            $route_junction = new V2JunctionRoutes();
            $route_junction->junction_mapping_id = $mapping->id;
            $route_junction->starting_hub_id = $previous;
            $route_junction->ending_hub_id = $rj;
            $route_junction->save();

            $previous = $rj;

            foreach ($request->vehicles[$key] as $vehicle)
            {
                $route_vehicle = new V2JunctionVehicles();
                $route_vehicle->junction_route_id = $route_junction->id;
                $route_vehicle->vehicle_id = $vehicle;
                $route_vehicle->save();
            }
        }

        return redirect()->back()->with('success', 'Mapping added successfully.');
    }

    public function manifest_mapping_status(Request $request)
    {
        $mapping = V2JunctionMapping::where('id', $request->id);
        if($mapping->doesntExist())
        {
            return response()->json(['status'=>0,'error'=>'Mapping Doesn\'t Exists..']);
        }

        $mapping = $mapping->first();

        if($mapping->status == 1)
        {
            $mapping->status = 0;
            $mapping->updated_by = Auth::id();
            $mapping->update();
            return response()->json(['status'=>1,'success'=>'Mapping Disabled Successfully..']);
        }
        else if($mapping->status == 0)
        {
            $mapping->status = 1;
            $mapping->updated_by = Auth::id();
            $mapping->update();
            return response()->json(['status'=>1,'success'=>'Mapping Enabled Successfully..']);
        }
        else{
            return response()->json(['status'=>0,'error'=>'Invalid Status..']);
        }
    }

    // not done
    public function manifest_mapping_edit($id){
        $mapping = V2JunctionMapping::where('id', $id);
        if($mapping->doesntExist())
        {
            return back()->with(['error'=>'Invalid Mapping ID']);
        }

        $mapping = $mapping->with(['junctions','routes','routes.vehicles'])
            ->first();

        return view('admin.cargo.manifest.edit')->with(['mapping' => $mapping]);
    }

    // not done
    public function manifest_mapping_edit_update(Request $request){
        $mapping = JunctionMapping::where('id', $request->mapping_id);
        if($mapping) {
            $mapping = $mapping->first();

            $mapping->junction_1 = $request->junction_1;
            $mapping->junction_2 = $request->junction_2;
            $mapping->receiver = $request->receiver_id;
            $mapping->updated_by = Auth::id();

            $mapping->save();

            return redirect()->back()->with('success', 'Mapping added successfully.');
        }
        else{
            return redirect()->back()->with('error', 'Mapping against these hubs already exists!');
        }
    }
}
