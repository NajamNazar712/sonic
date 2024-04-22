<?php

namespace App\Http\Controllers\Admins\Settings;

use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Agent\AgentType;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AgentTypesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }



    public function agent_types_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 495);
        $agent_types = AgentType::get();

        return view('admin.settings.agent_types.index')->with(['agent_types' => $agent_types]);
    }


    public function agent_types_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 496);
        }
        $roles = AgentType::select('agent_types.id', 'agent_types.name');

        $datatables = Datatables::of($roles)
            ->addColumn('action', function ($roles) {
                if (session('role_id') == 1 || in_array(666, session('permissions'))) {
                    $dropdown = '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                    <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                    ';
            
                    $dropdown .= '</div>
                  </div>
          ';

                    return $dropdown;
                } else {
                    return '';
                }
            });

        return $datatables->make(true);
    }
    
    //Add New Agent Type
    public function agent_type_store(Request $request)
    {
        try {

            $shipper_cap = new AgentType();
            $shipper_cap->name = $request->name;
            $shipper_cap->save();
            return redirect()->back()->with(['status'=>0, 'success'=>'New Agent Type Inserted Successfully']);


       } catch (\Exception $th) {
            return redirect()->back()->with(['status' => 1,  'error' => 'Unable to Insert Agent Type']);
       }
    }

    //Load Agent Type With Id
    public function agent_types_data(Request $request)
    {
        $agent_type = AgentType::find($request->id);

        $agent_type_id = $agent_type->id;
        $name = $agent_type->name;

        return response()->json(['status' => 1, 'agent_type_id' => $agent_type_id, 'name' => $name]);
    }

    //Update Agent Type With Id
    public function agent_type_update(Request $request)
    {
        if ($agent_type = AgentType::find($request->agent_type_id)) {
            $agent_type->name = $request->name;
            $agent_type->save();
            return redirect()->back()->with('success', 'Agent Type Updated!');
        } else {
            return redirect()->back()->with('error', 'Error Updating Agent Type!');
        }
    }
}
