<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Fleet;
use App\Http\Models\Admin\V2JunctionMapping;
use App\Http\Models\City;
use App\Http\Models\JunctionMapping;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

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

    // not done
    public function manifest_mapping_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),397);
        }

        $mapping =  V2JunctionMapping::join('cities as oc','oc.id', '=', 'junction_mappings.origin_id')
            ->join('cities as dc','dc.id', '=', 'junction_mappings.destination_id')
            ->join('admins as a','a.id', '=', 'junction_mappings.updated_by')
            ->leftjoin('fleets as f','f.id', '=', 'junction_mappings.fleet_id')
            ->select('junction_mappings.id as id', 'junction_mappings.updated_at as updated_at','oc.name as origin','dc.name as destination', 'f.reg_number as vehicle', 'a.name as updated_by');

        return Datatables::of($mapping)
            ->editColumn('junction_2',function ($mapping){
                if($mapping->junction_2 != null){
                    return $mapping->junction_2;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('receiver',function ($mapping){
                if($mapping->receiver != null){
                    return $mapping->receiver;
                }
                else{
                    return '-';
                }
            })
            ->addColumn('action',function ($mapping) {
                $dropdown = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                            <div class="dropdown-menu dropdown-menu-sm">
                                <button type="button" class="dropdown-item edit_mapping"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></div></button>
                            </div>
                        </div>
                    ';

                return $dropdown;
            })
            ->make(true);
    }

    // not done
    public function manifest_mapping_store(Request $request){
        $check = JunctionMapping::where(['origin_id' => $request->origin, 'destination_id' => $request->destiination_id])->first();
        if(!$check) {
            $mapping = new JunctionMapping();

            $mapping->origin_id = $request->origin;
            $mapping->destination_id = $request->destination;
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

    // not done
    public function manifest_mapping_edit(Request $request){
        $mapping = JunctionMapping::where('id', $request->mapping_id)->first();
        $origin = City::where('id', $mapping['origin_id'])->first();
        $destination = City::where('id', $mapping['destination_id'])->first();
        return response()->json(['details' => $mapping, 'origin' => $origin, 'destination' => $destination]);
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
