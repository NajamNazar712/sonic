<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\VehicleType;
use App\Http\Models\City;
use App\Http\Models\FtlCostTypes;
use App\Http\Models\TransportModeVendor;
use App\Http\Models\Admin\FtlComment;
use App\Http\Models\Admin\FtlRequest;
use App\Http\Models\Admin\FtlRequestAdditionalCost;
use App\Http\Models\Admin\FtlRequestStatus;
use App\Http\Models\Admin\FtlRequestStatusHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\Admin\WalkInStandardWeightCharge;
use App\Http\Models\CorporateDefaultDiscountCharge;
use App\Http\Models\CorporateDefaultRateStatus;
use App\Http\Models\CorporateDefaultWeightCharge;
use App\Http\Models\CorporateDiscountCharge;
use App\Http\Models\CorporateMinChargeableWeight;
use App\Http\Models\CorporateRateStatus;
use App\Http\Models\CorporateWeightCharge;
use App\Http\Models\CorporateWeightChargeZoneWise;
use App\Http\Models\WeightCharge;
use App\Http\Models\ZoneClassCity;

class FTLController extends Controller
{
    public $sale_role_ids = array();
    public $finance_role_ids = array();
    public $operation_role_ids = array();

    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');

        $this->sale_role_ids = AdminRole::where('department_id',7)->pluck('id')->toArray();
        $this->finance_role_ids = AdminRole::where('department_id',4)->pluck('id')->toArray();
        $this->operation_role_ids = AdminRole::where('department_id',6)->pluck('id')->toArray();
    }

    public function ftl_request_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),259);
        $shippers = User::leftjoin('sale_person_tags as spt',function ($join){
                $join->on('spt.user_id','users.id')
                    ->where('spt.status',0);
        })->leftjoin('admins as sale_person','sale_person.id','spt.admin_id')
            ->where('users.status',3)
            ->where('users.blacklist',0)
            ->where('users.account_type_id',2)
            ->get(['users.id as id','users.name as name','sale_person.id as sale_person_id']);

        $cities = City::where('status',1)->where('business_category_id',1)->get(['id','name']);

        $sale_persons = Admin::where('status',1)->whereIn('role_id',$this->sale_role_ids)->get(['id','name']);

        $vehicles = VehicleType::get(['id','name']);

        $statuses = FtlRequestStatus::get(['status']);

        return view('admin.ftl.request.index',compact('shippers','cities','sale_persons','vehicles','statuses'));
    }

    public function ftl_request_list(Request $request)
    {
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),260);
        }

        $data = FtlRequest::leftjoin('ftl_request_statuses as status','status.id','ftl_requests.status_id')
            ->leftjoin('cities as origin','origin.id','ftl_requests.origin_id')
            ->leftjoin('cities as destination','destination.id','ftl_requests.destination_id')
            ->leftjoin('admins as updated_by','updated_by.id','ftl_requests.updated_by')
            ->leftjoin('shipments as s','s.id','ftl_requests.shipment_id')
            ->leftjoin('vehicle_types as vt','vt.id','ftl_requests.vehicle_id')
            ->leftjoin('transport_mode_vendors as tmv','tmv.id','ftl_requests.vendor_id')
            ->select(['ftl_requests.id as id','ftl_requests.id as req_id','ftl_requests.shipper_id as shipper_id','origin.name as origin','destination.name as destination','s.tracking_number as tracking_number','ftl_requests.weight as weight','vt.name as vehicle','ftl_requests.quantity as quantity','ftl_requests.date as date','ftl_requests.updated_at as updated_on','updated_by.name as updated_by','ftl_requests.status_id as status_id','status.status as status','tmv.name as vendor','ftl_requests.freight_charges as freight_charges','ftl_requests.total_charges as total_charges','ftl_requests.gst as gst',DB::raw("((select COALESCE(SUM(amount), 0) from ftl_request_additional_costs where ftl_request_id = ftl_requests.id) + ftl_requests.freight_cost) as total_cost")]);

        $datatables = Datatables::of($data)
            ->editColumn('req_id',function ($data){
                return str_pad($data->req_id, 3, '0', STR_PAD_LEFT);
            })
            ->addColumn('tracking_number_link', function ($data) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$data->tracking_number' class='tracking' target='_blank'>$data->tracking_number</a></u>";
            })
            ->addColumn('action', function($data) {
                $dropdown = '';
                if (session('role_id') == 1 || count(array_intersect([513, 516], session('permissions'))) !== 0){
                    $dropdown .= '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">';
                    if(session('role_id') == 1 || in_array(513,session('permissions'))) {
                        $route = route('admin.ftl.request.view', ['id' => $data->id]);
                        if ($data->status_id == 1 || $data->status_id == 4) {
                            $dropdown .= '<button onclick="window.open(\'' . $route . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Estimate</div></button>';
                        } else {
                            $dropdown .= '<button onclick="window.open(\'' . $route . '\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View</div></button>';
                        }
                    }
                    if(session('role_id') == 1 || in_array(516,session('permissions'))) {
                        if ($data->status_id == 3 && $data->shipper_id == null) {
                            $route = route('admin.shipment.book.ftl.walk_in').'?ftl_req='.$data->req_id;
                            $dropdown .= '<button type="button" onclick="window.open(\''.$route.'\')" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Book</div></button>';
                        }
                    }
                    $dropdown .= '</div>
                  </div>
                ';
                }
                return $dropdown;
            });

        return $datatables->make(true);
    }

    public function ftl_request_add(Request $request)
    {
        $ftl_request = new FtlRequest();
       
        $origin_city = City::find($request->origin);
        $destination_city = City::find($request->destination);
        if($request->shipper == 0)
        {
            $ftl_request->shipper_name = $request->shipper_name;
            $walk_in_weight = WalkInStandardWeightCharge::where(['shipping_mode_id' => 2, 'delivery_type_id' => 1])->first();
            if($walk_in_weight){
                if ($origin_city->zone_id == $destination_city->zone_id) {
                    $walk_in_weight_charges = $walk_in_weight->chargeable_weight_local;
                } else {
                    
                        $zone_class = ZoneClassCity::where(['zone_id' => $destination_city->zone_id, 'city_id' => $destination_city->id])->first();
                        // ->where('zone_classification_id', 2)
                    if ($zone_class->class == 1) {
                        $walk_in_weight_charges = $walk_in_weight->chargeable_weight_charges_class_1;
                    } elseif ($zone_class->class == 2) {
                        $walk_in_weight_charges = $walk_in_weight->chargeable_weight_charges_class_2;
                    } elseif ($zone_class->class == 3) {
                        $walk_in_weight_charges = $walk_in_weight->chargeable_weight_charges_class_3;
                    } else {
                        $walk_in_weight_charges = $walk_in_weight->chargeable_weight_charges_class_0;
                    }
                    if($walk_in_weight_charges==0){
                        $walk_in_weight_charges = $walk_in_weight->chargeable_weight_local;
                    }
                }
                $result = $walk_in_weight_charges * $request->weight ;
            }else{
                return back()->with(['error'=>'Weight Charges Not Set']);
            }

        }
        else{
            $ftl_request->shipper_id = $request->shipper;
            $weight = $request->weight;

            // ftl weight charges code
            $rate_type_id = User::find($request->shipper)->corporate_rate_type_id;

            if($rate_type_id == 1 || $rate_type_id == 2 ){
                $rate_status = CorporateRateStatus::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('status', 1);
            }
            else{
                $rate_status = CorporateDefaultRateStatus::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('status', 1);
            }
            if ($rate_status->exists()) {
                if( $rate_type_id != 3 ){
                    $min_chargeable_weight = CorporateMinChargeableWeight::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('delivery_type_id', 1);

                        if ($min_chargeable_weight->exists()) {
                            $min_chargeable_weight = $min_chargeable_weight->first();

                            $min_chargeable_weight = $min_chargeable_weight->min_chargeable_weight;

                            if ($weight < $min_chargeable_weight) {
                                $weight = $min_chargeable_weight;
                            }
                        }
                    }


                    if($rate_type_id == 1){
                        $weight_charge = CorporateWeightCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('delivery_type_id', 1)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                    }
                    else if($rate_type_id == 2){
                        $weight_charge = CorporateWeightChargeZoneWise::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('delivery_type_id', 1)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);
                    }
                    else{
                        $weight_charge = CorporateDefaultWeightCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('range_up', '<=', $weight)->where('range_down', '>=', $weight);

                    }
                    if ($weight_charge->exists()) {
                        $weight_charge = $weight_charge->first();

                        $base = FALSE;
                        if ($rate_type_id != 3) {
                            $base_weight_charge = CorporateWeightCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('delivery_type_id', 1)->where('id', '<', $weight_charge->id)->where('base', 1)->orderBy('id', 'DESC');

                            if ($base_weight_charge->exists()) {
                                $base_weight_charge = $base_weight_charge->first();

                                $base = TRUE;
                            }
                        }

                        $today = Carbon::today();
                        if($rate_type_id == 3){
                            $discount_charge = CorporateDefaultDiscountCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                        }
                        else{
                            $discount_charge = CorporateDiscountCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->whereDate('to', '<=', $today)->whereDate('from', '>=', $today);
                        }
                        if ($discount_charge->exists()) {
                            $discount_charge = $discount_charge->first();
                            $discount = $discount_charge->weight;
                        }
                        else {
                            $discount = 0;
                        }

                        $class = 0;
                        $zone_wise = 1; // same zone
                        if ($origin_city->id == $destination_city->id) {
                            $type_of_charges = 0;
                        }
                        else {
                            $type_of_charges = 1;
                            if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                $zone_class_city = ZoneClassCity::where('zone_id', $origin_city->zone_id)->where('city_id', $destination_city->zone_id);
                                $zone_class_city = $zone_class_city->where('zone_classification_id', 2);
                                if ($zone_class_city->exists()) {
                                    $zone_class_city = $zone_class_city->first();
                                    $class = $zone_class_city->class;
                                }
                            }
                            else{
                                if($destination_city->zone_id == $origin_city->zone_id){
                                    $zone_wise = 1;
                                }else{
                                    $zone_wise = 2;
                                }
                            }
                    }
                    if ($weight_charge->weight_addition == 0 || $rate_type_id != 3) {
                        if ($type_of_charges == 0) {
                            if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                $charges = $weight_charge->local_or_6hr;
                            }
                            else{
                                $charges = $weight_charge->local;
                            }
                        }else{
                            if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                if ($class == 1) {
                                    if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                    } else {
                                        $charges = intval($weight_charge->national_charges_class_1);
                                    }
                                } else if ($class == 2) {
                                    if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                    } else {
                                        $charges = intval($weight_charge->national_charges_class_2);
                                    }
                                } else if ($class == 3) {
                                    if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                    } else {
                                        $charges = intval($weight_charge->national_charges_class_3);
                                    }
                                } else {
                                    $charges = $weight_charge->national_charges_class_0;
                                }
                            }else{
                                if($zone_wise == 1){
                                    if (strpos($weight_charge->same_zone, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->same_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                    } else {
                                        $charges = intval($weight_charge->same_zone);
                                    }
                                }else{
                                    if (strpos($weight_charge->different_zone, '%') !== FALSE) {
                                        $charges = ((floatval(str_replace('%', '', $weight_charge->different_zone)) / 100) * $weight_charge->local) + $weight_charge->local;
                                    } else {
                                        $charges = intval($weight_charge->different_zone);
                                    }
                                }
                            }
                        }
                        if ($rate_type_id != 3) {
                            if ($base) {
                                $weight_difference = $weight - $base_weight_charge->range_down;
                                if ($weight_difference > 0) {
                                    $charges = $charges * (ROUND($weight_difference, 0));
                                }
                                else {
                                    $charges = 0;
                                }

                                if ($type_of_charges == 0) {
                                    if($rate_type_id == null || $rate_type_id == 1){
                                        $charges += $base_weight_charge->local_or_6hr;
                                    }
                                    else{
                                        $charges += $weight_charge->local;
                                    }
                                } else {
                                    if($rate_type_id == null || $rate_type_id == 1 || $rate_type_id == 3){
                                        if ($class == 1) {
                                            if (strpos($base_weight_charge->national_charges_class_1, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_1)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                            } else {
                                                $charges += intval($base_weight_charge->national_charges_class_1);
                                            }
                                        } else if ($class == 2) {
                                            if (strpos($base_weight_charge->national_charges_class_2, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_2)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                            } else {
                                                $charges += intval($base_weight_charge->national_charges_class_2);
                                            }
                                        } else if ($class == 3) {
                                            if (strpos($base_weight_charge->national_charges_class_3, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $base_weight_charge->national_charges_class_3)) / 100) * $base_weight_charge->national_charges_class_0) + $base_weight_charge->national_charges_class_0;
                                            } else {
                                                $charges += intval($base_weight_charge->national_charges_class_3);
                                            }
                                        } else {
                                            $charges += $base_weight_charge->national_charges_class_0;
                                        }
                                    }
                                    else{
                                        if($zone_wise == 1){
                                            if (strpos($base_weight_charge->same_zone, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $base_weight_charge->same_zone)) / 100) * $base_weight_charge->local) + $base_weight_charge->local;
                                            } else {
                                                $charges += intval($base_weight_charge->same_zone);
                                            }
                                        }
                                        else{
                                            if (strpos($base_weight_charge->different_zone, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $base_weight_charge->different_zone)) / 100) * $base_weight_charge->local) + $base_weight_charge->local;
                                            } else {
                                                $charges += intval($base_weight_charge->different_zone);
                                            }
                                        }
                                    }

                                }
                            }   
                            else {
                                $charges = $charges * ROUND($weight, 0);
                            }
                        }
                        if (strpos($discount, '%') !== FALSE) {
                            $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                        }
                        else {
                            $discount = floatval($discount);
                        }

                        

                        if ($charges < $discount) {
                            
                            $result = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                        }
                        else {
                            $result = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                        }


                    }else {
                        $multiplier = (intval($weight - $weight_charge->range_up) / $weight_charge->spkg) + 1;
                        if ($type_of_charges == 0) {
                            $charges = ($weight_charge->local_or_6hr * $multiplier);
                        }
                        else{
                            if ($class == 1) {
                                if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_1) * $multiplier;
                                }
                            }
                            else if ($class == 2) {
                                if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_2) * $multiplier;
                                }
                            }
                            else if ($class == 3) {
                                if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                    $charges = (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                }
                                else {
                                    $charges = intval($weight_charge->national_charges_class_3) * $multiplier;
                                }
                            }
                            else {
                                $charges = ($weight_charge->national_charges_class_0 * $multiplier);
                            }
                        }
                        
                        $previous = TRUE;
                        while ($previous) {
                            if($rate_type_id == 3){
                                $weight_charge = CorporateDefaultWeightCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');
                            }
                            else{
                                $weight_charge = WeightCharge::where('user_id', $request->shipper)->where('shipping_mode_id', 2)->where('id', '<', $weight_charge->id)->orderBy('id', 'desc');  
                            }
    
                            if ($weight_charge->exists()) {
                                $weight_charge = $weight_charge->first();
    
                                if ($weight_charge->weight_addition == 0) {
                                    if ($type_of_charges == 0) {
                                        $charges += $weight_charge->local_or_6hr;
                                    }
                                    else {
                                        if ($class == 1) {
                                            if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                            }
                                            else {
                                                $charges += intval($weight_charge->national_charges_class_1);
                                            }
                                        }
                                        else if ($class == 2) {
                                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                            }
                                            else {
                                                $charges += intval($weight_charge->national_charges_class_2);
                                            }
                                        }
                                        else if ($class == 3) {
                                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                                $charges += ((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0;
                                            }
                                            else {
                                                $charges += intval($weight_charge->national_charges_class_3);
                                            }
                                        }
                                        else {
                                            $charges += $weight_charge->national_charges_class_0;
                                        }
                                    }
    
                                    $previous = FALSE;
                                }
                                else {
                                    $multiplier = (intval($weight_charge->range_down - $weight_charge->range_up) / $weight_charge->spkg) + 1;
    
                                    if ($type_of_charges == 0) {
                                        $charges += ($weight_charge->local_or_6hr * $multiplier);
                                    }
                                    else {
                                        if ($class == 1) {
                                            if (strpos($weight_charge->national_charges_class_1, '%') !== FALSE) {
                                                $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_1)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                            }
                                            else {
                                                $charges += intval($weight_charge->national_charges_class_1) * $multiplier;
                                            }
                                        }
                                        else if ($class == 2) {
                                            if (strpos($weight_charge->national_charges_class_2, '%') !== FALSE) {
                                                $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_2)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                            }
                                            else {
                                                $charges += intval($weight_charge->national_charges_class_2) * $multiplier;
                                            }
                                        }
                                        else if ($class == 3) {
                                            if (strpos($weight_charge->national_charges_class_3, '%') !== FALSE) {
                                                $charges += (((floatval(str_replace('%', '', $weight_charge->national_charges_class_3)) / 100) * $weight_charge->national_charges_class_0) + $weight_charge->national_charges_class_0) * $multiplier;
                                            }
                                            else {
                                                $charges += intval($weight_charge->national_charges_class_3) * $multiplier;
                                            }
                                        }
                                        else {
                                            $charges += ($weight_charge->national_charges_class_0 * $multiplier);
                                        }
                                    }
                                }
                            }
                            else {
                                $previous = FALSE;
                            }
                        }
                        if (strpos($discount, '%') !== FALSE) {
                            $discount = (floatval(str_replace('%', '', $discount)) / 100) * $charges;
                        }
                        else {
                            $discount = floatval($discount);
                        }
    
                        if ($charges < $discount) {
                            $result = ROUND($charges, 2, PHP_ROUND_HALF_DOWN);
                        }
                        else {
                            $result = ROUND(($charges - $discount), 2, PHP_ROUND_HALF_DOWN);
                        }
    
                    }    

                }else{
                    return back()->with(['error'=>'Shipper Weight Charges Not Set']);
                }
        }
        else {
            return back()->with(['error'=>'Shipper Weight Charges Not Set']);
        }




        }
        $ftl_request->salesperson_id = $request->sale_person;
        $ftl_request->origin_id = $request->origin;
        $ftl_request->destination_id = $request->destination;
        $ftl_request->weight = $request->weight;
        $ftl_request->quantity = $request->quantity;
        $ftl_request->vehicle_id = $request->vehicle;
        $ftl_request->date = $request->date_formatted;
        $ftl_request->updated_by = Auth::id();
        $ftl_request->updated_on = now();
        $ftl_request->calculated_charges = $result;
        $ftl_request->save();
        $this::FTLRequestStatusHistory($ftl_request->id,1,Auth::id());
        return back()->with(['success'=>'Request Generated Successfully']);
    }

    public function ftl_request_view($id)
    {
        $ftl = FtlRequest::leftjoin('cities as origin','origin.id','ftl_requests.origin_id')
            ->leftjoin('zones as z',function ($join){
                $join->on('z.id','origin.zone_id')
                    ->where('z.status',1);
            })
            ->leftjoin('cities as destination','destination.id','ftl_requests.destination_id')
            ->leftjoin('admins as updated_by','updated_by.id','ftl_requests.updated_by')
            ->leftjoin('vehicle_types as vt','vt.id','ftl_requests.vehicle_id')
            ->leftjoin('admins as sale_person','sale_person.id','ftl_requests.salesperson_id')
            ->leftjoin('users as shipper','shipper.id','ftl_requests.shipper_id')
            ->select(['ftl_requests.id as id','ftl_requests.calculated_charges','origin.name as origin','destination.name as destination','ftl_requests.weight as weight','vt.name as vehicle','ftl_requests.quantity as quantity','ftl_requests.date as date','ftl_requests.status_id as status_id','ftl_requests.vendor_id as vendor_id','ftl_requests.freight_charges as freight_charges','ftl_requests.total_charges as total_charges','ftl_requests.gst as gst','shipper.name as shipper','ftl_requests.shipper_name as shipper_name','sale_person.name as sale_person','ftl_requests.shipper_id as shipper_id','ftl_requests.freight_cost as freight_cost','z.gst as gst'])
            ->where('ftl_requests.id',$id);
       if($ftl->doesntExist())
       {
           return back()->with(['error'=>'Invalid FTL Request']);
       }

       $ftl = $ftl->first();
       $ftl_status_history = FtlRequestStatusHistory::leftjoin('admins as updated_by','updated_by.id','ftl_request_status_histories.updated_by')
           ->leftjoin('ftl_request_statuses as frs','frs.id','ftl_request_status_histories.status_id')
           ->select(['frs.status as status','updated_by.name as admin','ftl_request_status_histories.created_at as updated_at'])
           ->where('ftl_request_id',$id)
           ->orderBy('updated_at','asc')
           ->get();

        $shippers = User::leftjoin('sale_person_tags as spt',function ($join){
            $join->on('spt.user_id','users.id')
                ->where('spt.status',0);
        })->leftjoin('admins as sale_person','sale_person.id','spt.admin_id')
            ->where('users.status',3)
            ->where('users.blacklist',0)
            ->where('users.account_type_id',2)
            ->get(['users.id as id','users.name as name','sale_person.id as sale_person_id']);

        $sale_persons = Admin::where('status',1)->whereIn('role_id',$this->sale_role_ids)->get(['id','name']);

        $vendors = TransportModeVendor::get(['id','name']);

        $ftl_costs = FtlRequestAdditionalCost::where('ftl_request_id',$ftl->id)->get(['amount','cost_type']);

        $comments = FtlComment::leftjoin('admins as a','a.id','ftl_comments.comment_by_id')
            ->where('ftl_comments.ftl_request_id',$ftl->id)
            ->select(['ftl_comments.id as id','ftl_comments.comment as comment','ftl_comments.comment_by as comment_by','ftl_comments.comment_by_id as commenter_id','ftl_comments.created_at as created_at','a.name as commenter'])
            ->get();
        $cost_types = FtlCostTypes::all();

       return view('admin.ftl.request.view',compact('ftl','ftl_status_history','shippers','sale_persons','vendors','ftl_costs','comments','cost_types'));
    }

     public function ftl_request_update_shipper($id,Request $request)
     {
        $ftl = FtlRequest::find($id);
        if(!$ftl)
        {
            return back()->with(['error'=>'Invalid FTL Request']);
        }
        $ftl->shipper_id = $request->shipper;
        $ftl->salesperson_id = $request->sale_person;
        $ftl->update();
        return back()->with(['success'=>'Shipper Updated Successfully']);
     }

     public function ftl_request_update_status($id,Request $request)
     {
//        return $request;
        if($request->btn == "Update")
        {
            if(session('role_id') == 1 || in_array(514,session('permissions'))) {
                $ftl = FtlRequest::find($id);
                if (!$ftl) {
                    return back()->with(['error' => 'Invalid FTL Request']);
                }

                $cost_types = FtlCostTypes::pluck('name')->toArray();
              
                $ftl->additional_cost()->delete();
                if ($request->has('other_cost') && $request->has('other_cost_type')) {
                    $other_cost_count = count($request->other_cost);
                    for ($i = 0; $i < $other_cost_count; $i++) {
                        $cost = new FtlRequestAdditionalCost();
                        $cost->ftl_request_id = $ftl->id;
                        $cost->amount = $request->other_cost[$i];
                        $cost->cost_type = $request->other_cost_type[$i];
                        $cost->save();

                        if(!in_array($request->other_cost_type[$i],$cost_types )){
                            FtlCostTypes::create([
                                'name' => $request->other_cost_type[$i],
                            ]);
                        }
                    }
                }
                $ftl->vendor_id = $request->vendor;
                $ftl->freight_cost = $request->freight_cost;
                $ftl->freight_charges = $request->freight_charges;
                $ftl->gst = $request->gst;
                $ftl->total_charges = $request->total_charges;

                if ($request->freight_charges > 0) {
                    $ftl->status_id = 2;
                    $ftl->updated_by = Auth::id();
                    $this::FTLRequestStatusHistory($ftl->id, 2, Auth::id());
                }

                $ftl->update();
                return back()->with(['success' => 'FTL Request Updated Successfully']);
            }
            else{
                return redirect()->route('admin.access_denied');
            }
        }
        else if($request->btn == "Approve")
        {
            if(session('role_id') == 1 || in_array(515,session('permissions'))) {
                $ftl = FtlRequest::find($id);
                if (!$ftl) {
                    return back()->with(['error' => 'Invalid FTL Request']);
                }

                $ftl->status_id = 3;
                $ftl->updated_by = Auth::id();
                $ftl->update();
                $this::FTLRequestStatusHistory($ftl->id, 3, Auth::id());
                return back()->with(['success' => 'FTL Request Approved Successfully']);
            }
            else{
                return redirect()->route('admin.access_denied');
            }
        }
        else if($request->btn == "Reject")
        {
            if(session('role_id') == 1 || in_array(515,session('permissions'))) {
                $ftl = FtlRequest::find($id);
                if (!$ftl) {
                    return back()->with(['error' => 'Invalid FTL Request']);
                }

                $ftl->status_id = 4;
                $ftl->updated_by = Auth::id();
                $ftl->update();
                $this::FTLRequestStatusHistory($ftl->id, 4, Auth::id());
                return back()->with(['success' => 'FTL Request Rejected Successfully']);
            }
            else{
                return redirect()->route('admin.access_denied');
            }
        }
        else{
            return back()->with(['error'=>'Invalid Action']);
        }
     }

     public function ftl_request_get_comments(Request $request)
     {
         $comments = FtlComment::leftjoin('admins as a','a.id','ftl_comments.comment_by_id')
             ->where('ftl_comments.ftl_request_id',$request->request_id)
             ->select(['ftl_comments.id as id','ftl_comments.comment as comment','ftl_comments.comment_by as comment_by','ftl_comments.comment_by_id as commenter_id','ftl_comments.created_at as created_at','a.name as commenter']);
        if($comments->exists())
         {
             $comments = $comments->get();
             return response()->json(['status'=>1,'comments'=>$comments]);
         }
     }

     public function ftl_request_add_comment(Request $request)
     {
         if(in_array(Auth::user()->role_id,$this->sale_role_ids))
         {
             $comment_by = 0;
         }
         elseif(in_array(Auth::user()->role_id,$this->finance_role_ids))
         {
             $comment_by = 2;
         }
         elseif(in_array(Auth::user()->role_id,$this->operation_role_ids))
         {
             $comment_by = 1;
         }
         else {
             return response()->json(['status'=>0,'error'=>'You are not allowed to comment on the request']);
         }

         $comment = new FtlComment();
         $comment->ftl_request_id = $request->request_id;
         $comment->comment_by_id = Auth::id();
         $comment->comment_by = $comment_by;
         $comment->comment = $request->comment;
         $comment->save();

         $data = FtlComment::leftjoin('admins as a','a.id','ftl_comments.comment_by_id')
             ->where('ftl_comments.id',$comment->id)
             ->select(['ftl_comments.comment as comment','ftl_comments.comment_by as commented_by','ftl_comments.created_at as created_at','a.name as commenter'])
             ->first();

         return response()->json(['status'=>1,'comment'=>$data]);
     }

    static public function FTLRequestStatusHistory($request_id,$status_id,$user_id)
    {
        $history = new FtlRequestStatusHistory();
        $history->ftl_request_id = $request_id;
        $history->status_id = $status_id;
        $history->updated_by = $user_id;
        $history->save();
    }
}
