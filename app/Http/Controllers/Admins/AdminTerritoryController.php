<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Models\City;
use App\Territory;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AdminTerritoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        return view('admin.management.territory.index');
    }

    public function list(){
        $territory = Territory::leftjoin('cities as c','c.id','=','territories.city_id')
            ->select(['c.name as city','territories.id as id','territories.area as area','territories.name as name','territories.created_at as created_at','territories.updated_at as updated_at']);

        $datatable = Datatables::of($territory)
        ->addColumn('action', function($data) {

                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                        $dropdown .= $edit_button;

                    $dropdown .= '
                      </div>
                    </div>
                ';

                    return $dropdown;
            });
        return $datatable->make(true);
    }
    public function add(){
        $cities = City::select('id','name')->where('status',1)->get();
        return view('admin.management.territory.add')->with(['cities' => $cities]);
    }

    public function store(Request $request){
        $city = $request->city_id;
        $area = $request->area;
        $territory_name = $request->territory;

        $territory = new Territory();
        $territory->city_id = $city;
        $territory->area = $area;
        $territory->name = $territory_name;
        $territory->save();

        return redirect()->route('admin.management.territory.index')->with(['success' => 'Territory: ' . $territory_name . ' has been added!']);
    }

    public function edit($id){
        $cities = City::select('id','name')->where('status',1)->get();
        $territory = Territory::find($id);
        return view('admin.management.territory.edit')->with(['cities' => $cities,'territory'=>$territory]);
    }

    public function update(Request $request , $id){
        $city = $request->city_id;
        $area = $request->area;
        $territory_name = $request->territory;

        $territory = Territory::find($id);
        $territory->city_id = $city;
        $territory->area = $area;
        $territory->name = $territory_name;
        $territory->save();

        return redirect()->route('admin.management.territory.index')->with(['success' => 'Territory: ' . $territory_name . ' has been edited!']);
    }

}
