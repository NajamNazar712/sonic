<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\BusinessCategory;
use App\Http\Models\ZoneCitiesGst;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Zone;
use App\Http\Models\ZoneClassCity;
use App\Http\Models\City;
use App\Http\Models\InternationalDhlZone;
use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use DB;
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
            ->select('zones.id', 'zones.created_at as created', 'zones.updated_at as updated', 'zones.name', 'zones.gst', 'zones.status', 'zones.business_category_id', 'bc.name as business_category');

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
                    $duplicate_zone = '<button type="button" class="dropdown-item duplicate_zone"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">Duplicate Zone</div></button>';
                }
                else{
                    $edit_button = '<button type="button" class="dropdown-item international_edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $duplicate_zone ='';
                }
                $active = '<button type="button" class="dropdown-item activate"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Activate Zone</div></button>';
                $inactive = '<button type="button" class="dropdown-item deactivate"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Deactivate Zone</div></button>';
                $view_cities_button = '<button type="button" class="dropdown-item view_cities"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-file-text"></i></div><div class="col-9 offset-1">View Cities</div></button>';

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
            })->rawColumns(['action']);

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
        $zone_cities_gst = array();
        $cities = City::where(['status' => 1, 'business_category_id' => 1, 'zone_id' => $id])->get();
        $all_cities = City::where(['status' => 1, 'business_category_id' => 1])->get();
        $zone = Zone::find($id);
        $zones = Zone::where('id', '!=', $id)->get();
        $zone_class_cities = ZoneClassCity::where(['zone_id' => $id, 'zone_classification_id' => 1])->pluck('class', 'city_id');
        $zone_class_cities_cor = ZoneClassCity::where(['zone_id' => $id, 'zone_classification_id' => 2])->pluck('class', 'city_id');
        $zone_cities_gst = ZoneCitiesGst::join('cities as c','c.id','zone_cities_gsts.city_id')
            ->join('zones as z','z.id','zone_cities_gsts.zone_id')
            ->where('zone_cities_gsts.zone_id',$id)
            ->select('c.id as city_id','c.name as city_name','zone_cities_gsts.gst','z.id as zone_id','z.name as zone_name');

        if ($zone_cities_gst->exists())
        {
            $zone_cities_gst = $zone_cities_gst->get();
            $zone_cities_gst_count = count($zone_cities_gst);
        }
        else
        {
            $zone_cities_gst = [];
            $zone_cities_gst_count = 0;
        }

//        dump($zone_cities_gst,$zone_cities_gst_count);
        $classification = DB::table('zone_classification')->select(['id','name'])->get();
        return view('admin.management.zonal.update.index')->with(['cities' => $cities, 'zone' => $zone, 'zone_class_cities' => $zone_class_cities, 'zone_class_cities_cor' => $zone_class_cities_cor,'zone_cities_gst' => $zone_cities_gst,'zone_cities_gst_count' => $zone_cities_gst_count,'zone_id' => $id, 'all_cities' =>$all_cities, 'classifications' =>$classification ,'zones'=>$zones]);
    }

    public function update_store(Request $request, $id) {
        $zone = Zone::find($id);

        $zone->name = $request->name;
        $zone->gst = $request->gst;

        $zone->save();
        
        if($request->has('city_class')) {
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
        }
        
        if($request->has('city_class_cor')) {
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
        InternationalDhlZone::updateOrCreate(
            ['zone_id' => $zone->id], // match existing record by ID
            [
                'zone_id' => $zone->id,
                'zone_name' =>  strtolower(preg_replace('/\s*zone\s*/i', '', $request->name))
            ] // update or insert data
        );
        
        return redirect()->route('admin.management.zonal.index')->with(['success' => 'Zone: ' . $request->name . ' has been updated!']);
    }

    public function duplicate_zone(Request $request) {
        $id = $request->input('zone_id');
        $zone_class_cities = ZoneClassCity::where('zone_id', $id)->get();
           
        $new_zone = new Zone();
        $new_zone->name = $request->name;
        $new_zone->gst = $request->zone_charges;
        $new_zone->status = 1;
        $new_zone->business_category_id = 1;
        $new_zone->save();

        // get all the rows of input zone_id (which you want to duplicate) and create new rows with new id 
        foreach ($zone_class_cities as $oldRecord) {
            $newRecord = $oldRecord->replicate();
            $newRecord->zone_id = $new_zone->id;
            $newRecord->save();
        }
            return redirect()->back()->with(['success' => 'Zone: ' . $request->input('name') . ' has been added!']); 
    }

    public function check_zone_name(Request $request, $id = null) {
        
        if ($request->filled('name')) {
          $name = Zone::where('name', $request->input('name'));
  
          if ($id) {
            $name = $name->where('id', '!=', $id);
          }
  
          if (!$name->exists()) {
            return 'true';
          }
          else {
            return 'false';
          }
        }
        else {
          return 'false';
        }
      }
    public function update_zone_cities_gst(Request $request)
    {
        $zone_id = $request->zone_id;

        if ($request->has('toggle_value'))
        {
            if ($request->toggle_value)
                ZoneCitiesGst::where('zone_id',$zone_id)->update(['status' => 1]);
            else
                ZoneCitiesGst::where('zone_id',$zone_id)->update(['status' => 0]);

                return response()->json(['status' => 1, 'success' => 'Zone cities GST Updated !']);
        }


        $tableData = $request->table_data;

        ZoneCitiesGst::where('zone_id',$zone_id)->delete();

        if ($tableData)
        {
            $zoneCityGstCollection = collect(array_map(function ($row) {
                $zoneCityGst = new ZoneCitiesGst();
                $zoneCityGst->fill($row);
                return $zoneCityGst;
            }, $tableData));

            $zoneCityGstCollection->each->save();
        }

        //dump($zoneCityGstCollection->toArray());

        return response()->json(['status' => 1, 'success' => 'Zone cities GST updated !']);
    }

    public function add_cities(Request $request)
    {
        
        if($request->has('table_data')) {
            $data = $request->table_data;
            foreach($data as $d) 
            {
                $record = ZoneClassCity::where(['zone_id' => $request->zone_id , 'city_id' => $d['zone_city_id'], 'zone_classification_id' => $d['city_classification_id']]);
                if($record->doesntExist()) {
                    $new = new ZoneClassCity;
                    $new->zone_id = $request->zone_id;
                    $new->city_id =  $d['zone_city_id'];
                    $new->class = $d['city_class_id'];
                    $new->zone_classification_id = $d['city_classification_id'];
                    $new->save();
                }
            }
    
            return response()->json(['status' => 1, 'success' => 'Request completed']);
        }
    
    }

    public function search_cities(Request $request) {

        $cities = City::where(['status' => 1, 'business_category_id' => 1, 'zone_id' => $request->searchable_zone])->pluck('id')->toArray();
        $cities_class_1 = ZoneClassCity::join('cities', 'zone_class_cities.city_id', 'cities.id')->where(['zone_class_cities.zone_id' => $request->zone_id, 'zone_class_cities.zone_classification_id' => 1])->whereIn('city_id',$cities )->select(['cities.name','zone_class_cities.*'])->get();
        $cities_class_2 = ZoneClassCity::join('cities', 'zone_class_cities.city_id', 'cities.id')->where(['zone_class_cities.zone_id' => $request->zone_id, 'zone_class_cities.zone_classification_id' => 2])->whereIn('city_id',$cities )->select(['cities.name','zone_class_cities.*'])->get();
        
        return response()->json([
            'cities_class_1' => $cities_class_1,
            'cities_class_2' => $cities_class_2
        ]);
    }
}