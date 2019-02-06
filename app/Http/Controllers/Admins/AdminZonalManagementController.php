<?php

namespace App\Http\Controllers\Admins;

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
        return view('admin.management.zonal.index');
    }

    public function list(Request $request) {
        $zones = Zone::select('zones.id', 'zones.created_at', 'zones.updated_at', 'zones.name', 'zones.gst');

        $datatables = Datatables::of($zones)
        ->addColumn('action', function($zone) {
            $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
            $view_cities_button = '<button type="button" class="dropdown-item view_cities"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Cities</div></button>';

            $dropdown = '
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                  <div class="dropdown-menu dropdown-menu-sm">
            ';

            if (session('role_id') == 1 || in_array(133, session('permissions'))) {
                $dropdown .= $edit_button;
            }

            $dropdown .= $view_cities_button;

            $dropdown .= '
                  </div>
                </div>
            ';

            return $dropdown;
        });

        return $datatables->make(true);
    }

    public function add_index() {
        $cities = City::where('status', 1)->get();

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
            $zone_class_city->account_type = 1;

            $zone_class_city->save();
        }

        foreach ($request->city_class_cor as $city_id_cor => $class_cor) {
            $zone_class_city = new ZoneClassCity();

            $zone_class_city->zone_id = $zone->id;
            $zone_class_city->city_id = $city_id_cor;
            $zone_class_city->class = $class_cor;
            $zone_class_city->account_type = 2;

            $zone_class_city->save();
        }

        return redirect()->route('admin.management.zonal.index')->with(['success' => 'Zone: ' . $request->name . ' has been added!']);
    }

    public function update_index($id) {
        $cities = City::where('status', 1)->get();
        $zone = Zone::find($id);
        $zone_class_cities = ZoneClassCity::where('zone_id', $id)->pluck('class', 'city_id');

        return view('admin.management.zonal.update.index')->with(['cities' => $cities, 'zone' => $zone, 'zone_class_cities' => $zone_class_cities]);
    }

    public function update_store(Request $request, $id) {
        $zone = Zone::find($id);

        $zone->name = $request->name;
        $zone->gst = $request->gst;

        $zone->save();

        foreach ($request->city_class as $city_id => $class) {
            $zone_class_city = ZoneClassCity::where('zone_id', $zone->id)->where('city_id', $city_id);

            if ($zone_class_city->exists()) {
                $zone_class_city = $zone_class_city->first();
            }
            else {
                $zone_class_city = new ZoneClassCity();

                $zone_class_city->zone_id = $zone->id;
                $zone_class_city->city_id = $city_id;
            }

            $zone_class_city->class = $class;

            $zone_class_city->save();
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
}