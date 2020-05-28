<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\PickupRequest;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class V2AdminPickupsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');


        $this->middleware('Permission');
    }
    public function pending_index() {
        $riders = Rider::where('status',1)->select(['id', 'name']);

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $riders = $riders->get();

        $legends = V2PickupRequestLegend::all();
        $cut_off_time = '17:30:00';
        $setting = GlobalSettings::where('type', 'pickup_request_cut_off_time');
        if($setting->exists()){
            $setting = $setting->first();
            $cut_off_time = $setting->setting_value;
        }
        return view('admin.v2_pickups.pending')->with(['riders' => $riders, 'legends' => $legends, 'cut_off_time' => $cut_off_time]);
    }

    public function pending_list(Request $request) {

        $pickup_requests = V2PickupRequest::join('users as u', 'v2_pickup_requests.shipper_id', '=', 'u.id')
            ->join('user_shipping_infos as usi', 'v2_pickup_requests.pickup_address_id', '=', 'usi.id')
            ->join('cities AS ci', 'usi.city_id', '=', 'ci.id')
            ->leftJoin('v2_rider_pickups as rp', function ($join) {
                $join->on('rp.pickup_request_id', '=', 'v2_pickup_requests.id')
                    ->where('rp.pickup_request_id', '=',
                        DB::raw('(select sum(shipments) from v2_rider_pickups where v2_rider_pickups.pickup_request_id = v2_pickup_requests.id)'));
            })
            ->select('v2_pickup_requests.id','v2_pickup_requests.id as pickup_request_id', 'v2_pickup_requests.created_at as requested_date', 'u.name as shipper', 'usi.poc AS contact_person', 'usi.phone AS contact_number', 'usi.pickup_address AS address', 'ci.name AS city', 'v2_pickup_requests.booked', 'v2_pickup_requests.booked as bookings_link' , 'v2_pickup_requests.received', 'usi.vendor')
            ->where('v2_pickup_requests.status_id', '!=', 4);

        if (session('role_id') != 1) {
            $pickup_requests = $pickup_requests->whereIn('ci.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $pickup_requests = $pickup_requests->whereIn('u.id', session('tagged_shippers'));
            }
        }
        $datatables = Datatables::of($pickup_requests)
//            ->setRowAttr([
//                'class' => function ($pickup_request) use ($today) {
//                    if ($pickup_request->vendor != null) {
//                        return 'vendor_row';
//                    }
//                    else if (Carbon::parse($pickup_request->pickup_address_created_at)->startOfDay()->diffInDays($today) <= 6) {
//                        return 'new_pickup';
//                    }
//                }
//            ])
            ->editColumn('pickup_request_id', function ($pickup_requests) {
                return str_pad($pickup_requests->pickup_request_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('bookings_link', function($pickup_request) {
                if ($pickup_request->booked != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_request->booked . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->addColumn('action', function($pickup_request) {
                if (session('role_id') == 1 || in_array(18, session('permissions'))) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                      <button type="button" class="dropdown-item cancel"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-x-circle"></i></div><div class="col-9 offset-1">Cancel</div></button>
                    </div>
                  </div>
          ';
                }
                else {
                    return '';
                }
            });
//      ->filterColumn('pickup_type', function($query, $keyword) {
//        $keyword = strtolower($keyword);
//
//        if (strpos('light', $keyword) !== FALSE) {
//          $query->where('pickup_requests.pickup_type', '=', 0);
//        }
//        else if (strpos('heavy', $keyword) !== FALSE) {
//          $query->where('pickup_requests.pickup_type', '=', 1);
//        }
//        else {
//          $query->whereRaw('false');
//        }
//      });

        return $datatables->make(true);
    }
}
