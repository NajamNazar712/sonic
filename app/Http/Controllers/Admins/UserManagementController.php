<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\ModulePermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Models\City;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\AdminRoleModulePermission;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\Module;

use Auth;

use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');

      $this->middleware('Permission');
    }

    public function user_index() {
      return view('admin.user_management.user.index');
    }

    public function user_list(Request $request) {
        $users = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')
        ->join('admin_departments as ad', 'ar.department_id', '=', 'ad.id')
        ->leftjoin('admins as a', 'admins.updated_by', '=', 'a.id')
            ->leftjoin('cities as h', 'h.id', '=', 'admins.default_hub_id')
        ->select('admins.id', 'admins.name', 'admins.phone_number', 'admins.email', 'admins.cnic', 'ar.name as role', 'ad.name as department', 'admins.created_at', 'admins.updated_at', 'a.name as updated_by', 'admins.status', 'h.name as default_hub')
        ->where('ar.id', '!=', 1);

        $datatables = Datatables::of($users)
        ->editColumn('role', function($user) {
            return $user->role . ' - ' . $user->department;
        })
        ->editColumn('status', function ($user) {
            return (($user->status) ? 'Enabled' : 'Disabled');
        })
        ->removeColumn('department')
        ->addColumn('action', function($user) {
            if (session('role_id') == 1 || count(array_intersect([83, 84], session('permissions'))) !== 0) {
                $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
                $enable_button = '<button type="button" class="dropdown-item enable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check-circle"></i></div><div class="col-9 offset-1">Enable</div></button>';
                $disable_button = '<button type="button" class="dropdown-item disable"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Disable</div></button>';

                $dropdown = '
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                      <div class="dropdown-menu dropdown-menu-sm">
                ';

                if (session('role_id') == 1 || in_array(83, session('permissions'))) {
                    $dropdown .= $edit_button;
                }

                if (session('role_id') == 1 || in_array(84, session('permissions'))) {
                    if ($user->status) {
                        $dropdown .= $disable_button;
                    }
                    else {
                        $dropdown .= $enable_button;
                    }
                }

                $dropdown .= '
                      </div>
                    </div>
                ';

                return $dropdown;
            }
            else {
                return '';
            }
        })
        ->filterColumn('status', function($query, $keyword) {
            $keyword = strtolower($keyword);

            if (strpos('enabled', $keyword) !== FALSE) {
                $query->where('admins.status', '=', 1);
            }
            else if (strpos('disabled', $keyword) !== FALSE) {
                $query->where('admins.status', '=', 0);
            }
            else {
                $query->whereRaw('FALSE');
            }
        })
        ->filterColumn('role', function($query, $keyword) {
            $keyword = str_replace(' ', '', str_replace('-', '', strtolower($keyword)));

            if ($keyword != '') {
                $query->where('ar.name', 'like', '%' . $keyword . '%')->orWhere('ad.name', 'like', '%' . $keyword . '%');
            }
        });

        return $datatables->make(true);
    }

    public function user_email(Request $request) {
        if ($request->filled('email')) {
            $email = Admin::where('email', $request->input('email'));

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

    public function user_status(Request $request) {
        $admin = Admin::find($request->id);

        if ($admin) {
            $admin->status = $request->status;

            $admin->save();

            if ($request->status) {
                return ['status' => 0, 'success' => 'Admin has been enabled'];
            }
            else {
                return ['status' => 0, 'success' => 'Admin has been disabled'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Admin with given ID is present'];
        }
    }

    public function user_add_index() {
        $roles = AdminRole::with('department')->where('id', '!=', 1)->get();
        $hubs = City::where('hub', 1)->where('status', 1)->get();

        return view('admin.user_management.user.add.index')->with(['roles' => $roles, 'hubs' => $hubs]);
    }

    public function user_add_store(Request $request) {
        $admin = new Admin();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->cnic = $request->input('cnic');
        $admin->role_id = $request->input('role_id');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->password = bcrypt($request->input('password'));

        $admin->save();

        if ($request->has('hub_ids')) {
            foreach($request->input('hub_ids') as $hub_id) {
                $admin_hub = new AdminHub();

                $admin_hub->admin_id = $admin->id;
                $admin_hub->hub_id = $hub_id;

                $admin_hub->save();
            }
        }

        return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been added!']);
    }

    public function user_update_index($id) {
        $roles = AdminRole::with('department')->where('id', '!=', 1)->get();
        $hubs = City::where('hub', 1)->where('status', 1)->get();
        $user = Admin::find($id);
        $user_hubs = $user->hubs->pluck('hub_id')->toArray();

        if ($user->role_id != 1) {
            return view('admin.user_management.user.update.index')->with(['roles' => $roles, 'hubs' => $hubs, 'user' => $user, 'user_hubs' => $user_hubs]);
        }
        else {
            return redirect()->route('admin.access_denied');
        }
    }

    public function user_update_store(Request $request, $id) {
        if ($request->input('role_id') != 1) {
            $admin = Admin::find($id);

            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->phone_number = $request->input('phone_number');
            $admin->cnic = $request->input('cnic');
            $admin->role_id = $request->input('role_id');
            $admin->default_hub_id = $request->input('default_hub');
            $admin->updated_by = Auth::id();

            if ($request->filled('password')) {
                $admin->password = bcrypt($request->input('password'));
            }

            $admin->save();

            if ($request->has('hub_ids')) {
                $current_hub_ids = AdminHub::where('admin_id', $id)->pluck('hub_id')->toArray();

                $delete_hub_ids = array_diff($current_hub_ids, $request->input('hub_ids'));
                $new_hub_ids = array_diff($request->input('hub_ids'), $current_hub_ids);

                AdminHub::where('admin_id', $id)->whereIn('hub_id', $delete_hub_ids)->delete();

                foreach($new_hub_ids as $hub_id) {
                    $admin_hub = new AdminHub();

                    $admin_hub->admin_id = $id;
                    $admin_hub->hub_id = $hub_id;

                    $admin_hub->save();
                }
            }
            else {
                AdminHub::where('admin_id', $id)->delete();
            }

            return redirect()->route('admin.user_management.users.index')->with(['success' => 'User: ' . $request->input('name') . ' has been updated!']);
        }
        else {
            return redirect()->route('admin.access_denied');
        }
    }

    public function role_index() {
        $departments = AdminDepartment::all();
        return view('admin.user_management.role.index')->with(['departments'=>$departments]);
    }

    public function role_list(Request $request) {
        $roles = AdminRole::join('admin_departments as ad', 'admin_roles.department_id', '=', 'ad.id')
        ->join('admins as a', 'admin_roles.updated_by', '=', 'a.id')
        ->select('admin_roles.id', 'admin_roles.name', 'ad.name as department', 'admin_roles.created_at', 'admin_roles.updated_at', 'a.name as updated_by')
        ->where('admin_roles.id', '!=', 1);

        $datatables = Datatables::of($roles)
        ->addColumn('action', function($role) {
            if (session('role_id') == 1 || in_array(87, session('permissions'))) {
                return '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                          <div class="dropdown-menu dropdown-menu-sm">
                            <button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>
                          </div>
                        </div>
                ';
            }
            else {
                return '';
            }
        });

        return $datatables->make(true);
    }

    public function role_add_index() {
        $departments = AdminDepartment::where('id', '!=', 1)->get(['id', 'name']);
        $modules = Module::with('permissions')->get();

        return view('admin.user_management.role.add.index')->with(['departments' => $departments, 'modules' => $modules]);
    }

    public function role_add_store(Request $request) {
        if ($request->input('department_id') != 1) {
            $admin_role = new AdminRole();

            $admin_role->name = $request->input('name');
            $admin_role->department_id = $request->input('department_id');
            $admin_role->updated_by = Auth::id();

            $admin_role->save();

            if ($request->has('permission_ids')) {
                foreach($request->input('permission_ids') as $permission_id) {
                    $admin_role_module_permission = new AdminRoleModulePermission();

                    $admin_role_module_permission->role_id = $admin_role->id;
                    $admin_role_module_permission->permission_id = $permission_id;

                    $admin_role_module_permission->save();
                }
            }

            return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Role: ' . $request->input('name') . ' has been added!']);
        }
        else {
            return redirect()->route('admin.access_denied');
        }
    }

    public function role_update_index($id) {
        $departments = AdminDepartment::where('id', '!=', 1)->get(['id', 'name']);
        $modules = Module::with('permissions')->get();
        $role = AdminRole::find($id);
        $permissions = $role->module_permissions->pluck('permission_id')->toArray();
        if ($role->id != 1) {
            return view('admin.user_management.role.update.index')->with(['departments' => $departments, 'modules' => $modules, 'role' => $role, 'permissions' => $permissions]);
        }
        else {
            return redirect()->route('admin.access_denied');
        }
    }

    public function role_update_store(Request $request, $id) {
        $admin_role = AdminRole::find($id);

        $admin_role->name = $request->input('name');
        $admin_role->department_id = $request->input('department_id');
        $admin_role->updated_by = Auth::id();

        $admin_role->save();

        if ($request->has('permission_ids')) {
            $current_permission_ids = AdminRoleModulePermission::where('role_id', $id)->pluck('permission_id')->toArray();

//            $crm_module_permission = ModulePermission::where('module_id', '=', 18)->pluck('id');
            $delete_permission_ids = array_diff($current_permission_ids, $request->input('permission_ids'));
            $new_permission_ids = array_diff($request->input('permission_ids'), $current_permission_ids);

            AdminRoleModulePermission::where('role_id', $id)->whereIn('permission_id', $delete_permission_ids)->delete();

            foreach($new_permission_ids as $permission_id) {
                $admin_role_module_permission = new AdminRoleModulePermission();

                $admin_role_module_permission->role_id = $id;
                $admin_role_module_permission->permission_id = $permission_id;

                $admin_role_module_permission->save();
            }
        }
        else {
            AdminRoleModulePermission::where('role_id', $id)->delete();
        }

        return redirect()->route('admin.user_management.roles.index')->with(['success' => 'Role: ' . $request->input('name') . ' has been updated!']);
    }
}