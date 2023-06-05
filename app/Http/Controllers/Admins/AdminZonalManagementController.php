<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\BusinessCategory;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use App\Http\Models\City;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AdminZonalManagementController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index() {
        ActivityTrailController::createActivityTrailLog(Auth::id(),346);
        $business_categories = BusinessCategory::all();
        return view('admin.management.zonal.index')->with(['business_categories' => $business_categories]);
    }

    public function list(Request $request) {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),347);
        }
        $zones = Zone::leftjoin('business_categories as bc', 'bc.id', '=', 'zones.business_category_id')
            ->select('zones.id', 'zones.created_at', 'zones.updated_at', 'zones.name', 'zones.gst', 'zones.status', 'zones.business_category_id', 'bc.name as business_category');

        $datatables = Datatables::of($zones)
            ->editColumn('status', function ($zone) {
                if($zone->status == 1){
                    return 'Active';
                }
                else{
                    return 'Inctive';
                }
            })
            ->addColumn('action', function($zone) {
                if($zone->business_category_id == 1){
                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                }
                else{
                    $edit_button = '<button type="button" class="dropdown-item international_edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                }
                $active = '<button type="button" class="dropdown-item activate"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Activate Zone</div></button>';
                $inactive = '<button type="button" class="dropdown-item deactivate"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Deactivate Zone</div></button>';
                $view_cities_button = '<button type="button" class="dropdown-item view_cities"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Cities</div></button>';
                $duplicate_zone = '<button type="button" class="dropdown-item duplicate_zone"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">Duplicate Zone</div></button>';

                $dropdown = '
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
            ';

                if (session('role_id') == 1 || in_array(133, session('permissions'))) {
                    $dropdown .= $edit_button;
                    if($zone->status == 1){
                        $dropdown .= $inactive;
                    }
                    else{
                        $dropdown .= $active;
                    }
                }

                $dropdown .= $view_cities_button;
                $dropdown .= $duplicate_zone;

                $dropdown .= '
                  </div>
                </div>
            ';

                return $dropdown;
            });

        return $datatables->make(true);
    }

    public function add_index() {
        $cities = City::where('status', 1)->where('business_category_id', 1)->get();

        return view('admin.management.zonal.add.index')->with('cities', $cities);
    }

    public function add_store(Request $request) {
        $zone = New Zone();

        $zone->name = $request->name;
        $zone->gst = $request->gst;

        $zone->save();

        foreach ($request->city_class as $city_id => $class) {
            $zone_class_city = new ZoneClassCity();

            $zone_class_city->zone_id = $zone->id;
            $zone_class_city->city_id = $city_id;
            $zone_class_city->class = $class;
            $zone_class_city->zone_classification_id = 1;

            $zone_class_city->save();
        }

        foreach ($request->city_class_cor as $city_id_cor => $class_cor) {
            $zone_class_city_cor = new ZoneClassCity();

            $zone_class_city_cor->zone_id = $zone->id;
            $zone_class_city_cor->city_id = $city_id_cor;
            $zone_class_city_cor->class = $class_cor;
            $zone_class_city_cor->zone_classification_id = 2;

            $zone_class_city_cor->save();
        }

        return redirect()->route('admin.management.zonal.index')->with(['success' => 'Zone: ' . $request->name . ' has been added!']);
    }

    public function update_index($id) {
        $cities = City::where('status', 1)->where('business_category_id', 1)->get();
        $zone = Zone::find($id);
        $zone_class_cities = ZoneClassCity::where(['zone_id' => $id, 'zone_classification_id' => 1])->pluck('class', 'city_id');
        $zone_class_cities_cor = ZoneClassCity::where(['zone_id' => $id, 'zone_classification_id' => 2])->pluck('class', 'city_id');

        return view('admin.management.zonal.update.index')->with(['cities' => $cities, 'zone' => $zone, 'zone_class_cities' => $zone_class_cities, 'zone_class_cities_cor' => $zone_class_cities_cor]);
    }

    public function update_store(Request $request, $id) {
        $zone = Zone::find($id);

        $zone->name = $request->name;
        $zone->gst = $request->gst;

        $zone->save();

        foreach ($request->city_class as $city_id => $class) {
            $zone_class_city = ZoneClassCity::where(['zone_id' => $zone->id, 'city_id' => $city_id, 'zone_classification_id' => 1]);
            if ($zone_class_city->exists()) {
                $zone_class_city = $zone_class_city->first();
            } else {
                $zone_class_city = new ZoneClassCity();

                $zone_class_city->zone_id = $zone->id;
                $zone_class_city->city_id = $city_id;
                $zone_class_city->zone_classification_id = 1;
            }

            $zone_class_city->class = $class;

            $zone_class_city->save();
        }

        foreach ($request->city_class_cor as $city_id_cor => $class_cor) {
            $zone_class_city_cor = ZoneClassCity::where(['zone_id' => $zone->id, 'city_id' => $city_id_cor, 'zone_classification_id' => 2]);

            if ($zone_class_city_cor->exists()) {
                $zone_class_city_cor = $zone_class_city_cor->first();
            }
            else {
                $zone_class_city_cor = new ZoneClassCity();

                $zone_class_city_cor->zone_id = $zone->id;
                $zone_class_city_cor->city_id = $city_id_cor;
                $zone_class_city_cor->zone_classification_id = 2;
            }

            $zone_class_city_cor->class = $class_cor;

            $zone_class_city_cor->save();
        }

        return redirect()->route('admin.management.zonal.index')->with(['success' => 'Zone: ' . $request->name . ' has been updated!']);
    }

    public function view_cities(Request $request) {
        $cities = City::where('zone_id', $request->id)->where('status', 1);

        if ($cities->exists()) {
            $cities = $cities->get();

            return $cities;
        }
        else {
            return 0;
        }
    }
    public function zonal_status_update(Request $request) {
        $zone_id = $request->id;
        $zone = Zone::where('id', $zone_id);
        $new_status = $request->status;
        if($zone->exists()){
            $zone = $zone->first();
            if($new_status == 1){
                $zone->status = 1;
                $status_name = 'Activated';
            }
            else{
                City::where('zone_id', $zone_id)->update([
                    'status' => 0
                ]);
                $zone->status = 0;
                $status_name = 'Deactivated';
            }
            $zone->save();
            return response()->json(['status' => 1, 'success' => 'Zone '. $status_name .' successfully!']);
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Invalid Zone selected!']);
        }
    }


    public function add_international_index() {

        return view('admin.management.zonal.add.international_index');
    }

    public function add_international_store(Request $request) {
        $zone = New Zone();

        $zone->name = $request->name;
        $zone->gst = $request->gst;
        $zone->business_category_id = 2;

        $zone->save();

        return redirect()->route('admin.management.zonal.index')->with(['success' => 'Zone: ' . $request->name . ' has been added!']);
    }

    public function update_international_index($id) {
        $zone = Zone::find($id);

        return view('admin.management.zonal.update.international_index')->with(['zone' => $zone]);
    }

    public function update_international_store(Request $request, $id) {
        $zone = Zone::find($id);

        $zone->name = $request->name;
        $zone->gst = $request->gst;

        $zone->save();

        return redirect()->route('admin.management.zonal.index')->with(['success' => 'Zone: ' . $request->name . ' has been updated!']);
    }

    public function duplicate_zone(Request $request) {
        dd('asd');
        $cities = City::where('status', 1)->where('business_category_id', 1)->get();
        $zone = Zone::find($request);
        $zone_class_cities = ZoneClassCity::where(['zone_id' => $request, 'zone_classification_id' => 1])->pluck('class', 'city_id');
        $zone_class_cities_cor = ZoneClassCity::where(['zone_id' => $request, 'zone_classification_id' => 2])->pluck('class', 'city_id');

        return view('admin.management.zonal.update.index')->with(['cities' => $cities, 'zone' => $zone, 'zone_class_cities' => $zone_class_cities, 'zone_class_cities_cor' => $zone_class_cities_cor]);
    }
}