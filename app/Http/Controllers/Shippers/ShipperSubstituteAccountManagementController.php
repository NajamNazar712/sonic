<?php

namespace App\Http\Controllers\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\Shipper\SubstituteUserModulePermission;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\SubstituteUserPermission;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class ShipperSubstituteAccountManagementController extends Controller
{
    public function __construct() {
      $this->middleware('auth:web,substitute_users');

      $this->middleware('Permission');
    }

    public function index() {
      return view('client.substitute_account_management.index');
    }

    public function list() {
      $substitute_users = SubstituteUser::select('substitute_users.id', 'substitute_users.name', 'substitute_users.phone_number', 'substitute_users.email', 'substitute_users.cnic', 'substitute_users.created_at', 'substitute_users.updated_at', 'substitute_users.status', 'substitute_users.restriction')
      ->where('substitute_users.user_id', session('user_id'));

      $datatables = Datatables::of($substitute_users)
      ->editColumn('status', function ($substitute_user) {
        return (($substitute_user->status) ? 'Enabled' : 'Disabled');
      })
      ->editColumn('restriction', function ($substitute_user) {
        return (($substitute_user->restriction) ? 'Enabled' : 'Disabled');
      })
      ->addColumn('action', function($substitute_user) {
        $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
        $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
        $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

        $dropdown = '
          <div class="btn-group">
            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
            <div class="dropdown-menu dropdown-menu-sm">
        ';

        $dropdown .= $edit_button;

        if ($substitute_user->status) {
            $dropdown .= $disable_button;
        }
        else {
            $dropdown .= $enable_button;
        }

        $dropdown .= '
            </div>
          </div>
        ';

        return $dropdown;
      })
      ->filterColumn('status', function($query, $keyword) {
        $keyword = strtolower($keyword);

        if ($keyword != '') {
            $query->where('substitute_users.status', '=', $keyword);
        }
        else {
            $query->whereRaw('FALSE');
        }
      });

      return $datatables->make(true);
    }

    public function email(Request $request) {
      if ($request->filled('email')) {
        $email = SubstituteUser::where('email', $request->input('email'));

        if ($request->has('id')) {
          $email = $email->where('id', '!=', $request->input('id'));
        }

        if (!$email->exists()) {
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

    public function status(Request $request) {
      $substitute_user = SubstituteUser::find($request->id);

      if ($substitute_user) {
        $substitute_user->status = $request->status;

        $substitute_user->save();

        if ($request->status) {
          return ['status' => 0, 'success' => 'Substitute User has been enabled'];
        }
        else {
          return ['status' => 0, 'success' => 'Substitute User has been disabled'];
        }
      }
      else {
        return ['status' => 1, 'error' => 'No Substitute User with given ID is present'];
      }
    }

    public function add_index() {
      $permissions = SubstituteUserModulePermission::whereNotIn('id', [6, 7, 20, 21, 22])->get();

      return view('client.substitute_account_management.add.index')->with(['permissions' => $permissions]);
    }

    public function add_store(Request $request) {
      $substitute_user = new SubstituteUser();

      $substitute_user->user_id = session('user_id');
      $substitute_user->name = $request->input('name');
      $substitute_user->email = $request->input('email');
      $substitute_user->phone_number = $request->input('phone_number');
      $substitute_user->cnic = $request->input('cnic');
      $substitute_user->password = bcrypt($request->input('password'));
      $substitute_user->restriction = $request->input('restriction');

      $substitute_user->save();

      if ($request->has('permission_ids')) {
        foreach($request->input('permission_ids') as $permission_id) {
          $substitute_user_permission = new SubstituteUserPermission();

          $substitute_user_permission->substitute_user_id = $substitute_user->id;
          $substitute_user_permission->permission_id = $permission_id;

          $substitute_user_permission->save();
        }
      }

      return redirect()->route('cod.substitute_account_management.index')->with(['success' => 'Substitute User: ' . $request->input('name') . ' has been added!']);
    }

    public function update_index($id) {
      $permissions = SubstituteUserModulePermission::whereNotIn('id', [6, 7])->get();
      $substitute_user = SubstituteUser::find($id);

      if ($substitute_user->user_id == session('user_id')) {
        $substitute_user_permissions = $substitute_user->permissions->pluck('permission_id')->toArray();

        return view('client.substitute_account_management.update.index')->with(['permissions' => $permissions, 'substitute_user' => $substitute_user, 'substitute_user_permissions' => $substitute_user_permissions]);
      }
      else {
            return redirect()->route('cod.access_denied');
        }
    }

    public function update_store(Request $request, $id) {
      $substitute_user = SubstituteUser::find($id);

      $substitute_user->user_id = session('user_id');
      $substitute_user->name = $request->input('name');
      $substitute_user->email = $request->input('email');
      $substitute_user->phone_number = $request->input('phone_number');
      $substitute_user->cnic = $request->input('cnic');
      $substitute_user->restriction = $request->input('restriction');

      if ($request->filled('password')) {
        $substitute_user->password = bcrypt($request->input('password'));
      }

      $substitute_user->save();

      if ($request->has('permission_ids')) {
        $current_permission_ids = SubstituteUserPermission::where('substitute_user_id', $id)->pluck('permission_id')->toArray();

        $delete_permission_ids = array_diff($current_permission_ids, $request->input('permission_ids'));
        $new_permission_ids = array_diff($request->input('permission_ids'), $current_permission_ids);

        SubstituteUserPermission::where('substitute_user_id', $id)->whereIn('permission_id', $delete_permission_ids)->delete();

        foreach($new_permission_ids as $permission_id) {
          $substitute_user_permission = new SubstituteUserPermission();

          $substitute_user_permission->substitute_user_id = $id;
          $substitute_user_permission->permission_id = $permission_id;

          $substitute_user_permission->save();
        }
      }
      else {
          SubstituteUserPermission::where('substitute_user_id', $id)->delete();
      }

      return redirect()->route('cod.substitute_account_management.index')->with(['success' => 'Substitute User: ' . $request->input('name') . ' has been updated!']);
    }
}