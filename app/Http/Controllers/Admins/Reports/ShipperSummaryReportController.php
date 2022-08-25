<?php

namespace App\Http\Controllers\Admins\Reports;

use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class ShipperSummaryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 583);
    }

    public function list(Request $request){

        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 584);
        }

        $users = DB::connection('reports')->table('users')->join('sale_person_tags as spt', 'spt.user_id', '=', 'users.id')
            ->join('admins AS sp', 'sp.id', '=', 'spt.admin_id')
            ->select(['users.id', 'users.name as shipper', 'sp.name as sale_person', 'users.activated_at'])
            ->where('spt.status', '=', 0)
            ->where('users.status', '=', 3);
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $users = $users->whereBetween('users.activated_at', [$from, $to]);
        }
        $datatable = Datatables::of($users)->make(true);
        return $datatable;


    }
}
