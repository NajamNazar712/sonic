<?php

namespace App\Http\Controllers\Shippers;

use Auth;
use Carbon\Carbon;
use App\Http\Models\City;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Models\Shipper\User;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\SubstituteUser;

use App\Http\Models\Shipper\UserShippingInfo;
use App\Http\Models\Shipper\SubstituteUserPermission;
use App\Http\Models\Shipper\SubstituteUserModulePermission;

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
      ->where('substitute_users.user_id', session('user_id'))->where('substitute_users.is_created_by_admin','!=',1);

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

      return $datatables->rawColumns(['action'])->make(true);
    }

   public function email(Request $request) {
      if (!$request->filled('email')) {
          return 'false';
      }

      $email = $request->input('email');
      $id = $request->input('id');

      $subUserQuery = SubstituteUser::where('email', $email);
      if ($id) {
          $subUserQuery->where('id', '!=', $id);
      }
      $subUserExists = $subUserQuery->exists();

      $userExists = User::where('email', $email)->exists();

      if (!$subUserExists && !$userExists) {
          return 'true';
      }

      return 'false';
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
      $permissions = SubstituteUserModulePermission::whereNotIn('id', [6, 7])->get();

      $pickup_addresses = UserShippingInfo::where('user_id', auth()->user()->id)
      ->where('hidden', 0)
      ->where('status', 1)
      ->select([
        'id',
        'pickup_address'
      ])
      ->get();

      $cities = City::where('pickup', 1)
        ->where('booking_enable_status', 1)
        ->where('status', 1)
        ->where('business_category_id', 1)
        ->whereNotNull('zone_id')
        ->orderBy('name')
      ->get();

      return view('client.substitute_account_management.add.index')
        ->with([
          'permissions' => $permissions,
          'pickup_addresses' => $pickup_addresses,
          'cities' => $cities
        ]);
    }

    public function add_store(Request $request) {
      $request->validate([
        'pickup_address' => 'required|array|min:1',
      ]);

      $substitute_user = new SubstituteUser();
      $selected_ids = $request->input('pickup_address');

      $substitute_user->user_id = session('user_id');
      $substitute_user->name = $request->input('name');
      $substitute_user->email = $request->input('email');
      $substitute_user->phone_number = $request->input('phone_number');
      $substitute_user->cnic = $request->input('cnic');
      $substitute_user->password = bcrypt($request->input('password'));
      $substitute_user->restriction = $request->input('restriction');
      
      $pickup_address_id = implode(',', $selected_ids);

      if ($pickup_address_id == 0) {
        $user_shipping_info = new UserShippingInfo();
        $user_shipping_info->user_id = auth()->user()->id;
        $user_shipping_info->pickup_address = $request->input('new_pickup_address');
        $user_shipping_info->poc = $request->input('new_pickup_person_of_contact');
        $user_shipping_info->vendor = $request->input('new_pickup_vendor');
        $user_shipping_info->phone = $request->input('new_pickup_phone_number');
        $user_shipping_info->email = $request->input('new_pickup_email_address');
        $user_shipping_info->city_id = $request->input('new_pickup_city');
        $user_shipping_info->hidden = 0;

        if ($request->input('make_default_address') == 1) {
          $default = 1;
        } else {
          $default = 0;
        }
        $user_shipping_info->default_address = $default;

        $user_shipping_info->save();
        $substitute_user->pickup_address_id = $user_shipping_info->id; 
      } else {
        $substitute_user->pickup_address_id = $pickup_address_id;
      }
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
        $pickup_addresses = UserShippingInfo::where('user_id', auth()->user()->id)
        ->where('hidden', 0)
        ->where('status', 1)
        ->select([
          'id',
          'pickup_address'
        ])
        ->get();

        return view('client.substitute_account_management.update.index')->with([
          'permissions' => $permissions, 
          'substitute_user' => $substitute_user, 
          'substitute_user_permissions' => $substitute_user_permissions, 
          'pickup_addresses' => $pickup_addresses
        ]);
      }
      else {
            return redirect()->route('cod.access_denied');
        }
    }

    public function update_store(Request $request, $id) {
      $request->validate([
        'pickup_address' => 'required|array|min:1',
      ]);

      $substitute_user = SubstituteUser::find($id);
      $selected_ids = $request->input('pickup_address');

      $substitute_user->user_id = session('user_id');
      $substitute_user->name = $request->input('name');
      $substitute_user->email = $request->input('email');
      $substitute_user->phone_number = $request->input('phone_number');
      $substitute_user->cnic = $request->input('cnic');
      $substitute_user->restriction = $request->input('restriction');
      $substitute_user->pickup_address_id = implode(',', $selected_ids);

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