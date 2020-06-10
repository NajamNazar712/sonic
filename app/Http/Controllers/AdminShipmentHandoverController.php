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
        ->join('admins as a', 'a.id', '=', 'handover_responsibilities.created_by')
        ->leftjoin('admins as u', 'u.id', '=', 'handover_responsibilities.updated_by')
        ->select('handover_responsibilities.id as responsible_id','handover_responsibilities.name as name','c.name as hub','a.name as created','u.name as updated','handover_responsibilities.status as status')
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
        $responsibles->status = 1;
        $responsibles->save();
        return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Added successfully!"]);
    }

    public function responsibles_status(Request $request){
        $id = $request->id;
        $status = $request->status;
        $responsible = HandoverResponsibilities::find($id);
        if(!$responsible){
            return response()->json(['status' => 1, 'error' => 'Not found!']);
        }

        if($status == 1){
            $responsible->status = 1;
        }else if($status == 0){
            $responsible->status = 0;
        }
        $responsible->save();

        return response()->json(['status' => 0, 'success' => 'Status updated successfully!']);
    }

    public function responsibles_editview(Request $request){

        $responsible_id = HandoverResponsibilities::where('id',$request->id)->first();
        // $hub_id=HandoverResponsibilities::where('hub_id',$responsible_id->hub)->first();
        return response()->json(['status' => 1, 'responsible' => $responsible_id]);
    }
    public function responsibles_edit(Request $request){
        $responsible = HandoverResponsibilities::find($request->id);
         if($responsible){
             $responsible->name = $request->name;
             $responsible->hub_id = $request->hub;
             $responsible->updated_by = Auth::id();
             $responsible->save();
             return redirect()->back()->with(['status'=>1,'success'=>"Responsible has been Edited successfully!"]);
         }
         return redirect()->back()->with(['status'=>0,'error'=>"Responsible not found!"]);
    }
}
