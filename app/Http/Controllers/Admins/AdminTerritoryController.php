<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\Admin\AreaTerritory;
use App\Http\Models\City;
use App\Http\Models\Admin\Territory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminTerritoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 352);
        $cities = City::select('id', 'name')->where('status', 1)->where('business_category_id', 1)->get();
        return view('admin.management.territory.index')->with(['cities' => $cities]);
    }

    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 353);
        }

        $territory = Territory::leftjoin('cities as c', 'c.id', '=', 'territories.city_id')
            ->leftjoin('admins as a', 'a.id', '=', 'territories.created_by')
            ->leftjoin('admins as ad', 'ad.id', '=', 'territories.updated_by')
            ->select(['c.name as city', 'territories.id as id', 'territories.name as name', 'territories.created_at as created_at', 'territories.updated_at as updated_at', 'a.name as created_by', 'ad.name as updated_by', 'territories.territory_status as status']);

        $datatable = Datatables::of($territory)
            ->editColumn('status', function ($data) {
                if ($data->status == 0) {
                    return 'Disable';
                } else {
                    return 'Enable';
                }
            })
            ->filterColumn('status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('enable', $keyword) !== FALSE) {
                    $query->where('territories.territory_status', '=', 1);
                }
                else if (strpos('disable', $keyword) !== FALSE) {
                    $query->where('territories.territory_status', '=', 0);
                }
                else {
                    $query->whereRaw('FALSE');
                }
            })
            ->addColumn('action', function ($data) {

                $edit_button = '<button  type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
<button type="button" class="dropdown-item disable_territory"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-circle" style="color: red"></i></div><div class="col-9 offset-1" style="color: red">Disable</div></button>';

                $enable_button = '<button  type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
<button type="button" class="dropdown-item enable_territory"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-circle" style="color: blue"></i></div><div class="col-9 offset-1" style="color: blue">Enable</div></button>';


                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';
                if ($data->status == '1') {
                    $dropdown .= $edit_button;
                }
                else {
                    $dropdown .= $enable_button;
                }
//                $dropdown .= $edit_button;

                $dropdown .= '
                      </div>
                    </div>
                ';

                return $dropdown;
            });

        return $datatable->make(true);
    }


    public function store(Request $request)
    {
        $city = $request->city_id;
        $territory_name = $request->territory;

        $territory = new Territory();
        $territory->name = $territory_name;
        $territory->city_id = $city;
        $territory->created_by = Auth::id();
        $territory->save();

        return redirect()->route('admin.management.territory.index')->with(['success' => 'Territory: ' . $territory_name . ' has been added!']);
    }


    public function edit_territory_ajax(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $territory = Territory::find($id);
            $name = $territory->name;
            $city_id = $territory->city_id;
            $data = (['city_id' => $city_id, 'name' => $name]);
            return response()->json(['details' => $data]);
        }

    }

    public function disable_territory(Request $request)
    {
        $id = $request->id;
        $disable = 0;
        if ($id) {
            $territory = Territory::find($id);
            $territory->territory_status = $disable;
            $territory->save();
            return response()->json(['status' => 1, 'success' => 'Territory disable']);
        }
        return response()->json(['status' => 0, 'error' => 'No Territory found']);
    }

    public function enable_territory(Request $request)
    {
        $id = $request->id;
        $enable = 1;
        if ($id) {
            $territory = Territory::find($id);
            $territory->territory_status = $enable;
            $territory->save();
            return response()->json(['status' => 1, 'success' => 'Territory enable']);
        }
        return response()->json(['status' => 0, 'error' => 'No Territory found']);
    }

    public function update(Request $request, $id)
    {

        $city = $request->city_id;
        $territory_name = $request->territory;

        $territory = Territory::find($id);
        $territory->city_id = $city;
        $territory->updated_by = Auth::id();
        $territory->name = $territory_name;
        $territory->save();

        return redirect()->route('admin.management.territory.index')->with(['success' => 'Territory: ' . $territory_name . ' has been edited!']);
    }

    public function area_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 354);
        $territories = Territory::select('id', 'name')->where('territory_status', '=', '1')->get();

        return view('admin.management.territory.area.index')->with(['territories' => $territories]);
    }

    public function area_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 355);
        }
        $areas = AreaTerritory::join('territories as t', 't.id', '=', 'area_territories.territory_id')
            ->leftjoin('admins as a', 'a.id', '=', 'area_territories.created_by')
            ->leftjoin('admins as ad', 'ad.id', '=', 'area_territories.updated_by')
            ->select(['area_territories.id as id', 't.name as territory', 'area_territories.name as area', 'area_territories.created_at as created_at', 'a.name as created_by', 'ad.name as updated_by', 'area_territories.updated_at as updated_at', 'area_territories.area_territory_status as area_status'])
            ->where('t.territory_status', '=', '1');

        $datatable = Datatables::of($areas)
            ->editColumn('area_status', function ($data) {
                if ($data->area_status == 0) {
                    return 'Disable';
                } else {
                    return 'Enable';
                }
            })
            ->addColumn('action', function ($data) {

                $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
<button type="button" class="dropdown-item disable_area_status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-circle" style="color: red"></i></div><div class="col-9 offset-1" style="color: red">Disable</div></button>';

                $enable_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
<button type="button" class="dropdown-item enable_area_status"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-circle" style="color: blue"></i></div><div class="col-9 offset-1" style="color: blue">Enable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                if ($data->area_status == '1') {
                    $dropdown .= $edit_button;
                }
                else {
                    $dropdown .= $enable_button;
                }

//                $dropdown .= $edit_button;

                $dropdown .= '
                      </div>
                    </div>
                ';

                return $dropdown;
            })
            ->filterColumn('area_status', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('enable', $keyword) !== FALSE) {
                    $query->where('area_territories.area_territory_status', '=', 1);
                }
                else if (strpos('disable', $keyword) !== FALSE) {
                    $query->where('area_territories.area_territory_status', '=', 0);
                }
                else {
                    $query->whereRaw('FALSE');
                }
            });
        return $datatable->make(true);

    }

    public function area_store(Request $request)
    {
        $territory = $request->territory;
        $area_name = $request->area;

        $area = new AreaTerritory();
        $area->territory_id = $territory;
        $area->name = $area_name;
        $area->created_by = Auth::id();
        $area->save();

        return redirect()->route('admin.management.area.index')->with(['success' => 'Area: ' . $area_name . ' has been added!']);
    }

    public function area_edit(Request $request)
    {
        $id = $request->id;
        if ($id) {
            $area = AreaTerritory::find($id);
            $name = $area->name;
            $territory_id = $area->territory_id;
            $data = (['territory_id' => $territory_id, 'name' => $name]);
            return response()->json(['details' => $data]);
        }

    }
    public function disable_area_status(Request $request)
    {
        $id = $request->id;
        $disable = 0;
        if ($id) {
            $territory = AreaTerritory::find($id);
            $territory->area_territory_status = $disable;
            $territory->save();
            return response()->json(['status' => 1, 'success' => 'Territory disable']);
        }
        return response()->json(['status' => 0, 'error' => 'No Territory found']);
    }
    public function enable_area_status(Request $request)
    {
        $id = $request->id;
        $enable = 1;
        if ($id) {
            $territory = AreaTerritory::find($id);
            $territory->area_territory_status = $enable;
            $territory->save();
            return response()->json(['status' => 1, 'success' => 'Territory enable']);
        }
        return response()->json(['status' => 0, 'error' => 'No Territory found']);
    }

    public function area_update(Request $request, $id)
    {

        $territory = $request->territory;
        $area = $request->area;

        $area_territory = AreaTerritory::find($id);
        $area_territory->territory_id = $territory;
        $area_territory->name = $area;
        $area_territory->updated_by = Auth::id();
        $area_territory->save();

        return redirect()->route('admin.management.area.index')->with(['success' => 'Area: ' . $area . ' has been edited!']);

    }

    public function area_tag(Request $request)
    {

        $territory_id = $request->territory;
        $areas = $request->areas;
        if ($areas) {
            foreach ($areas as $area) {
                $area_territory = AreaTerritory::find($area);
                $area_territory->territory_id = $territory_id;
                $area_territory->save();
            }
            return response()->json(['status' => 1, 'success' => "Territory has been assigned"]);
        } else {
            return response()->json(['status' => 0, 'error' => "Area not selected"]);
        }


    }

}
