<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\City;
use App\Http\Models\Handover\HandoverResponsibilities;
use App\Http\Models\Handover\HandoverStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminShipmentHandoverController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function responsibles_index(){
        $hubs = City::select(['id','name'])->where('hub',1)->get();
        return view('admin.handover.responsibles')->with(['hubs'=>$hubs]);
    }

    public function responsibles_list(){

        $responsibles_list = HandoverResponsibilities::leftjoin('cities as c','c.id','=','handover_responsibilities.hub_id')
        //->leftjoin('handover_statuses as hos','hos.id','=','handover_responsibilities.status_id')
        ->join('admins as a', 'a.id', '=', 'handover_responsibilities.created_by')
        ->leftjoin('admins as u', 'u.id', '=', 'handover_responsibilities.updated_by')
        ->select('handover_responsibilities.name as name','c.name as hub','a.name as created','u.name as updated','handover_responsibilities.status_id as status')
        ->get();

        $datatable = Datatables::of($responsibles_list)
            ->addColumn('status', function ($data){
                if($data->status == 0){
                    return 'Disable';
                }else{
                    return 'Enable';
                }
            })
            ->addColumn('action', function ($data){

                    $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                        $dropdown .= '<button type="button"  class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                    if ($data->status == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                    } else {
                            $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                    }

                    return $dropdown;

            });

        return  $datatable->make(true);

    }

    public function responsibles_add(Request $request){
        $responsibles = new HandoverResponsibilities();
        $responsibles->name = $request->name;
        $responsibles->hub_id = $request->hub;
        $responsibles->created_by = Auth::id();
        $responsibles->updated_by = Auth::id();
        $responsibles->status_id = 1;
        $responsibles->save();
        return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Added successfully!"]);
    }
}
