<?php

namespace App\Http\Controllers\Admins\Reports;

use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use DB;

class ShipperSummaryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 583);
        return view('admin.reports.shipper_summary');
    }

    public function list(Request $request){

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 584);
        }

        $users = DB::connection('reports')->table('users')
            ->join('cities', 'users.city_id', '=', 'cities.id')
            ->leftjoin('zones as z','cities.zone_id','=','z.id')
            ->leftjoin('territories as t', 't.id', '=', 'users.territory_id')
            ->leftjoin('segments as seg', 'seg.id', '=', 'users.segment_id')
            ->join('products as p', 'p.id', '=', 'users.product_id')
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'users.id')
                    ->leftjoin('admins as ad', 'ad.id', '=', 'spt.admin_id')
                    ->where('spt.status', '=', 0);
            })
            ->select(['users.id', 'users.name as shipper', 'ad.name as sale_person', 'users.activated_at', 'users.disable_at', 'seg.name as segment', 'cities.name as city', 'z.name as zone', 't.name as territory', 'p.product_name', 'users.status as status']);
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $users = $users->whereBetween('users.activated_at', [$from, $to]);
        }
        $datatable = Datatables::of($users)
            ->addColumn('id_padded', function ($user) {
                return str_pad($user->id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('status', function ($users) {
                if ($users->status == 3) {
                    return "Enable";
                } else {
                    return "Disable";
                }
            })
            ->make(true);
        return $datatable;


    }
}
