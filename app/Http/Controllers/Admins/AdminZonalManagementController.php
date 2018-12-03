<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Zone;
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

            if (session('role_id') == 1 || in_array(1, session('permissions'))) {
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

    public function add_store(Request $request) {}
}