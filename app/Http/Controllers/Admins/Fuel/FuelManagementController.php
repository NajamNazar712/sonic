<?php

namespace App\Http\Controllers\Admins\Fuel;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Fuel\CardHolderType;
use App\Http\Models\Admin\Fuel\FleetVehicle;
use App\Http\Models\Admin\Fuel\FleetVehicleType;
use App\Http\Models\Admin\Fuel\FuelCardLog;
use App\Http\Models\Admin\Fuel\FuelDeductionType;
use App\Http\Models\Admin\Fuel\FuelFleetExternalCard;
use App\Http\Models\Admin\Fuel\FuelFleetInternalCard;
use App\Http\Models\Admin\Fuel\FuelFleetOfficeCard;
use App\Http\Models\Admin\Fuel\FuelRiderCard;
use App\Http\Models\Admin\Fuel\FuelStaffCard;
use App\Http\Models\Admin\Fuel\FuelType;
use App\Http\Models\Admin\Fuel\FuelCardRequest;
use App\Http\Models\Admin\Fuel\FuelCardRequestType;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use DB;

class FuelManagementController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function fuel_index()
    {
        $fuel_types = FuelType::all();
        $fuel_deduction_types = FuelDeductionType::all();
        $card_holder_types = CardHolderType::all();
        $card_request_types = FuelCardRequestType::all();
        $vehicle_types = FleetVehicleType::all();
        $staffs = Admin::where('status','1')->get();
        $riders = Rider::where([['status','1'],['blacklist','0']])->get();
        $fleets = FleetVehicle::where([['status',1]])->get();
        return view('admin.user_management.fuel.fuel_management',compact('fuel_types','fuel_deduction_types','card_holder_types','card_request_types','vehicle_types','staffs', 'riders','fleets'));
    }

    public function fuel_list(Request $request)
    {
        $requests = FuelCardRequest::leftjoin('fuel_types as ft','fuel_card_requests.fuel_type_id','=','ft.id')
            ->leftjoin('fuel_deduction_types as fdt','fuel_card_requests.fuel_deduction_type_id','=','fdt.id')
            ->leftjoin('admins as requested_by','fuel_card_requests.requested_by','=','requested_by.id')
            ->leftjoin('admins as approved_by','fuel_card_requests.approved_by','=','approved_by.id')
            ->leftjoin('card_holder_types as cht','fuel_card_requests.card_holder_type_id','=','cht.id')
            ->leftjoin('fuel_card_request_types as fcrt','fuel_card_requests.card_request_type_id','=','fcrt.id')
            ->leftJoin('admins as staff', function ($join) {
                $join->on('staff.id', '=', 'fuel_card_requests.card_holder_id')
                    ->where('fuel_card_requests.card_holder_type_id', '=', DB::raw(1))
                    ->whereNotNull('fuel_card_requests.card_holder_id');
            })
            ->leftJoin('riders as rider', function ($join) {
                $join->on('rider.id', '=', 'fuel_card_requests.card_holder_id')
                    ->where('fuel_card_requests.card_holder_type_id', '=', DB::raw(2))
                    ->whereNotNull('fuel_card_requests.card_holder_id');
            })
            ->leftJoin('fleet_vehicles as fleet', function ($join) {
                $join->on('fleet.id', '=', 'fuel_card_requests.card_holder_id')
                    ->where('fuel_card_requests.card_holder_type_id', '=',DB::raw(3))
                    ->whereNotNull('fuel_card_requests.card_holder_id');
            })
            ->where([['card_request_type_id',1],['fuel_card_requests.status',0]])
            ->orwhere([['card_number','!=',null],['card_request_type_id',1]])
            ->orwhere([['card_number','!=',null],['card_request_type_id',2]])
            ->orWhere([['card_number','!=',null],['card_request_type_id',3]])
            ->orWhere([['card_number','!=',null],['card_request_type_id',4]])
//            ->whereIn('fuel_card_requests.id',DB::raw("SELECT id FROM fuel_card_requests WHERE NOT ('card_number', 'card_request_type_id') IN ((null, 1)"))
            ->select('fcrt.name as card_request_type','fuel_card_requests.id as id','fuel_card_requests.fuel_request_id as fuel_request_id','fuel_card_requests.fuel_request_id as fuel_request_id_for_excel','fuel_card_requests.card_number as card_number','fuel_card_requests.card_holder_id as card_holder','cht.name as card_holder_type','fuel_card_requests.card_holder_type_id as card_holder_type_id','fuel_card_requests.amount as amount','ft.name as fuel_type','fdt.name as fuel_deduction_type','fuel_card_requests.fuel_deduction_type_id as fuel_deduction_type_id','requested_by.name as requested_by','approved_by.name as approved_by','fuel_card_requests.approved_at as approved_at','fuel_card_requests.updated_at as updated_at','staff.name as staff_name','rider.name as rider_name','fleet.name as fleet_name','fuel_card_requests.status as status','fuel_card_requests.card_request_type_id as card_request_type_id');
        
        if($request->get('card_number') && $request->get('card_number') != ''){
            $card_number = explode(',',$request->get('card_number'));
            $requests->where(function ($subquery) use($card_number) {
                for ($i = 0; $i < count($card_number); $i++){
                    $subquery->orwhere('fuel_card_requests.card_number', 'like',  '%' . $card_number[$i] .'%');
                }
            });
        }


        if($request->get('staff_card_holder') && $request->get('staff_card_holder') != ''){
            $staff_card_holder = $request->get('staff_card_holder');
            $requests->where('staff.name','like',"%".$staff_card_holder."%");
        }

        if($request->get('rider_card_holder') && $request->get('rider_card_holder') != ''){
            $rider_card_holder = $request->get('rider_card_holder');
            $requests->where('rider.name',$rider_card_holder);
        }

        if($request->get('fleet_card_holder') && $request->get('fleet_card_holder') != ''){
            $fleet_card_holder = $request->get('fleet_card_holder');
            $requests->where('fleet.name',$fleet_card_holder);
        }

        if ($request->get('aprroved_at_from') && $request->get('approved_at_to')) {
            $from = $request->get('aprroved_at_from');
            $to = $request->get('approved_at_to');
            $requests->whereBetween('fuel_card_requests.approved_at', [$from,$to]);
        }

        return Datatables::of($requests)
            ->editColumn('card_holder', function ($data) {
               if ($data->card_holder_type_id == 1)
               {
                  return $data->staff_name;
               }
               else if($data->card_holder_type_id == 2)
               {
                   return $data->rider_name;
               }
               else if($data->card_holder_type_id == 3)
               {
                   return $data->fleet_name;
               }
               else{
                   return '';
               }
            })
            ->editColumn('fuel_request_id', function ($data) {
                return '<a target="_blank" href="'.route('admin.user_management.fuel_management.history.index',['fuel_request_id'=>$data->fuel_request_id]).'">'.$data->fuel_request_id.'</a>';
            })
            ->editColumn('amount', function ($data) {
                if($data->amount != null)
                {
                    if($data->fuel_deduction_type_id == 1)
                    {
                        return $data->amount.' Rs';
                    }
                    else{
                        return $data->amount.' Liter';
                    }

                }
                else{
                    return '';
                }
            })
            ->addColumn('action',function ($fuel_card) {
                if (session('role_id') == 1 || count(array_intersect([448, 450], session('permissions'))) !== 0) {
                    if(($fuel_card->status == 0 && (count(array_intersect([448], session('permissions'))) !== 0 || session('role_id') == 1)) || (($fuel_card->card_request_type_id == 1 || $fuel_card->card_request_type_id == 2) &&($fuel_card->card_number != null) && (count(array_intersect([450], session('permissions'))) !== 0 || session('role_id') == 1))) {
                        $dropdown = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                    ';

                    if (($fuel_card->card_request_type_id == 1 || $fuel_card->card_request_type_id == 2) && ($fuel_card->card_number != null) && (session('role_id') == 1 || in_array(450, session('permissions')))) {
                        $dropdown .= '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Edit</div></button>';
                    }

                    if(session('role_id') == 1 || in_array(448, session('permissions'))) {
                        if ($fuel_card->status == 0 && $fuel_card->card_request_type_id == 1) {
                            $dropdown .= '<button type="button" class="dropdown-item approve_new"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Approve</div></button>';
                        } else if ($fuel_card->status == 0 && $fuel_card->card_request_type_id == 2) {
                            $dropdown .= '<button type="button" class="dropdown-item approve" data-msg="Are you sure you want to Re-assign this Card"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Reassign</div></button>';
                        } else if ($fuel_card->status == 0 && $fuel_card->card_request_type_id == 3) {
                            $dropdown .= '<button type="button" class="dropdown-item approve" data-msg="Are you sure you want to Block this Card"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Block</div></button>';
                        } else if ($fuel_card->status == 0 && $fuel_card->card_request_type_id == 4) {
                            $dropdown .= '<button type="button" class="dropdown-item approve" data-msg="Are you sure you want to Unblock this Card"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Unblock</div></button>';
                        }
                    }
                    $dropdown .= '
                    </div>
                  </div>
                  ';
                    }
                    else{
                        $dropdown = '';
                    }
                }
                else{
                    $dropdown = '';
                }

                return $dropdown;
            })
            ->addColumn('card_status',function ($fuel_card) {
               if($fuel_card->status == 0)
               {
                   return 'Pending';
               }
               else if($fuel_card->card_request_type_id == 3 && $fuel_card || $fuel_card->card_request_type_id == 4 || ($fuel_card->card_request_type_id == 1 && $fuel_card->card_number == '') || ($fuel_card->card_request_type_id == 2 && $fuel_card->card_number == ''))
               {
                   return 'Unassigned';
               }
               else if($fuel_card->card_request_type_id == 1 || $fuel_card->card_request_type_id == 2)
               {
                   return 'Assigned';
               }

               return '';
            })
            ->filterColumn('card_status', function($query, $keyword) {
                if($keyword == 'Pending')
                {
                    $query->where('fuel_card_requests.status',0);
                }
                else if($keyword == 'Unassigned')
                {
                    $query->where('fuel_card_requests.status',1)->where(function($q){
                        $q->orwhere('fuel_card_requests.card_request_type_id',3)
                            ->orwhere('fuel_card_requests.card_request_type_id',4)
                            ->orwhere([['fuel_card_requests.card_request_type_id',1],['fuel_card_requests.card_number',null]])
                            ->orwhere([['fuel_card_requests.card_request_type_id',2],['fuel_card_requests.card_number',null]]);
                    });
                }
                else if($keyword == 'Assigned')
                {
                    $query->where('fuel_card_requests.status',1)->where(function($q){
                        $q->orwhere([['fuel_card_requests.card_request_type_id',1],['fuel_card_requests.card_number','!=','']])
                            ->orwhere([['fuel_card_requests.card_request_type_id',2],['fuel_card_requests.card_number','!=','']]);
                    });
                }
            })
            ->make(true);
    }

    public function request_create(Request $request)
    {
        if($request->card_holder_type == 1)
        {
            $data = Admin::where('status','1');
        }
        else if($request->card_holder_type == 2){
            $data = Rider::where([['status','1'],['blacklist','0']]);
        }
        else if($request->card_holder_type == 3){
            $data = FleetVehicle::where([['status',1],['fleet_vehicle_type_id',$request->fleet_vehicle_type]]);
        }
        else{
            return response()->json(['status'=>0,'error'=>'Invalid Card Holder Type!']);
        }
        return response()->json(['status'=>1,'data'=>$data->get()]);

    }

    public function request_search_by_card(Request $request)
    {
        $fuel_card_request = FuelCardRequest::where('card_number',$request->card_number);
        if(!$fuel_card_request->exists())
        {
            return response()->json(['status' => 0, 'error' => 'Enter a valid Card Number']);
        }

        $fuel_card_request->where('card_holder_type_id',$request->card_holder_type);

        if($request->fleet_vehicle_type != null)
        {
            $fuel_card_request->where('fleet_vehicle_type_id',$request->fleet_vehicle_type);
        }

        if(!$fuel_card_request->exists())
        {
            return response()->json(['status' => 0, 'error' => 'Card not belong to this card holder type']);
        }

        $fuel_card_request->where('fuel_card_requests.status',1);

        if(!$fuel_card_request->exists()) {
            return response()->json(['status' => 0, 'error' => 'Card Previous Request is not approved']);
        }

            $data = clone($fuel_card_request->first());
            $fuel_card_request = $fuel_card_request->leftjoin('fuel_types as ft','fuel_card_requests.fuel_type_id','=','ft.id')
                ->leftjoin('fuel_deduction_types as fdt','fuel_card_requests.fuel_deduction_type_id','=','fdt.id')
                ->leftjoin('admins as requested_by','fuel_card_requests.requested_by','=','requested_by.id')
                ->leftjoin('admins as approved_by','fuel_card_requests.approved_by','=','approved_by.id')
                ->leftjoin('card_holder_types as cht','fuel_card_requests.card_holder_type_id','=','cht.id');
            if ($data->card_holder_type_id == 1) {
                    $status = FuelStaffCard::where([['name',$request->card_number],['blocked',1]])->exists();

                $fuel_card_request = $fuel_card_request->leftjoin('admins as card_holder','fuel_card_requests.card_holder_id','=','card_holder.id');
            } else if ($data->card_holder_type_id == 2) {
                $status = FuelRiderCard::where([['name',$request->card_number],['blocked',1]])->exists();

                $fuel_card_request = $fuel_card_request->leftjoin('riders as card_holder','fuel_card_requests.card_holder_id','=','card_holder.id');
            } else if ($data->card_holder_type_id == 3) {
                if($data->fleet_vehicle_type_id == 1)
                {
                    $status = FuelFleetOfficeCard::where([['name',$request->card_number],['blocked',1]])->exists();
                }
                else if($data->fleet_vehicle_type_id == 2)
                {
                    $status = FuelFleetExternalCard::where([['name',$request->card_number],['blocked',1]])->exists();
                }
                else if($data->fleet_vehicle_type_id == 3)
                {
                    $status  = FuelFleetInternalCard::where([['name',$request->card_number],['blocked',1]])->exists();
                }
                else {
                    return response()->json(['status' => 0, 'error' => 'No user assigned to this card number']);
                }
                $fuel_card_request = $fuel_card_request->leftjoin('fleet_vehicles as card_holder','fuel_card_requests.card_holder_id','=','card_holder.id');
            } else {
                return response()->json(['status' => 0, 'error' => 'No user assigned to this card number']);
            }
            $fuel_card_request =  $fuel_card_request->select('fuel_card_requests.id as id','fuel_card_requests.card_number as card_number','cht.name as card_holder_type','fuel_card_requests.amount as amount','ft.name as fuel_type','fdt.name as fuel_deduction_type','fuel_card_requests.fuel_deduction_type_id as fuel_deduction_type_id','requested_by.name as requested_by','approved_by.name as approved_by','fuel_card_requests.approved_at as approved_at','card_holder.name as card_holder');
            if(($request->request_type != 4 && !$status) || ($request->request_type == 4 && $status))
            {
                return response()->json(['status' => 1, 'data' => $fuel_card_request->first()]);
            }
            else{
                if($request->request_type != 4)
                {
                    return response()->json(['status' => 0, 'error' => 'Card is Blocked']);
                }
                else{
                    return response()->json(['status' => 0, 'error' => 'Card is already Unblocked']);
                }
            }
    }

    public function request_store(Request $request)
    {
        $messages = [
            'card_request_type.required'        =>  'Card Request Type is required',
            'card_request_type.numeric'         =>  'Invalid Card Request Type',
            'card_request_type.min'             =>  'Invalid Card Request Type',
            'card_request_type.max'             =>  'Invalid Card Request Type',
            'card_holder_type.required'         =>  'Card Holder Type is Required',
            'card_holder_type.numeric'          =>  'Invalid Card Holder Type',
            'card_holder_type.min'              =>  'Invalid Card Holder Type',
            'card_holder_type.max'              =>  'Invalid Card Holder Type',
            'card_holder.required_if'           =>  'Card Holder is Required',
            'card_number.required_if'           =>  'Card Number is Required',
        ];

        $rules = [
            'card_request_type' => 'required|numeric|min:1|max:4',
            'card_holder_type' => 'required|numeric|min:1|max:3',
            'card_holder'   => 'required_if:card_request_type,1,2',
            'card_number' => 'required_if:card_request_type,2,3,4',
        ];

        $validate = Validator::make($request->all(),$rules,$messages);

        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate);
        }

        $card_request_type  =   $request->card_request_type;
        $card_holder_type   =   $request->card_holder_type;

        if($card_request_type == 1) // New Request
        {
            $previous_card = FuelCardRequest::where([['card_holder_id',$request->card_holder],['card_holder_type_id',$card_holder_type],['card_number','!=',null]]);
            if($previous_card->exists())
            {
                return redirect()->back()->with(['error'=>'Card already assigned to this User']);
            }
            $fuel_card_request_id = $this->create_request($request,$request->card_holder);
            $this->request_log($fuel_card_request_id,$card_request_type,$card_request_type);
            return redirect()->back()->with(['success'=>'New Card Request Created Successfully']);
        }
        else if($card_request_type == 2) // Reassign Request
        {
            $previous_card = FuelCardRequest::where([['card_holder_id',$request->card_holder],['card_holder_type_id',$card_holder_type],['card_number','!=',null]]);
            if($previous_card->exists())
            {
                return redirect()->back()->with(['error'=>'Card already assigned to this User']);
            }
            $current_request = FuelCardRequest::find($request->card_request_id);
            if($current_request->exists())
            {
                $card_number = $current_request->card_number;
                $fuel_type = $current_request->fuel_type_id;
                $fuel_deduction_type = $current_request->fuel_deduction_type_id;
                $amount = $current_request->amount;
                $current_request->card_number = null;
                $current_request->fuel_type_id = null;
                $current_request->fuel_Deduction_type_id = null;
                $current_request->amount = null;
                $current_request->update();

                $fuel_card_request_id = $this->create_request($request,$request->card_holder,$card_number,$fuel_deduction_type,$fuel_type,$amount);
                $this->request_log($fuel_card_request_id,$card_request_type,$card_request_type);
                return redirect()->back()->with(['success'=>'Reassign Request Created Successfully']);
            }
            else{
                return redirect()->back()->with(['error'=>'Card not found to reassign, Check Card Number']);
            }

        }
        else if($card_request_type == 3) // Block Request
        {
            $current_request = FuelCardRequest::find($request->card_request_id);
            if($current_request->exists())
            {
                $card_number = $current_request->card_number;
                $fuel_type = $current_request->fuel_type_id;
                $fuel_deduction_type = $current_request->fuel_deduction_type_id;
                $amount = $current_request->amount;
                $current_request->card_number = null;
                $current_request->fuel_type_id = null;
                $current_request->fuel_Deduction_type_id = null;
                $current_request->amount = null;
                $current_request->update();

                $fuel_card_request_id = $this->create_request($request,null,$card_number,$fuel_deduction_type,$fuel_type,$amount);
                $this->request_log($fuel_card_request_id,$card_request_type,$card_request_type);
                return redirect()->back()->with(['success'=>'Block Request Created Successfully']);
            }
            else{
                return redirect()->back()->with(['error'=>'Card not found to reassign, Check Card Number']);
            }
        }
        else if($card_request_type == 4)  // Unblock Request
        {
            $current_request = FuelCardRequest::find($request->card_request_id);
            if($current_request->exists())
            {
                $card_number = $current_request->card_number;
                $fuel_type = $current_request->fuel_type_id;
                $fuel_deduction_type = $current_request->fuel_deduction_type_id;
                $amount = $current_request->amount;
                $current_request->card_number = null;
                $current_request->fuel_type_id = null;
                $current_request->fuel_Deduction_type_id = null;
                $current_request->amount = null;
                $current_request->update();

                $fuel_card_request_id = $this->create_request($request,null,$card_number,$fuel_deduction_type,$fuel_type,$amount);
                $this->request_log($fuel_card_request_id,$card_request_type,$card_request_type);
                return redirect()->back()->with(['success'=>'Un Block Request Created Successfully']);
            }
            else{
                return redirect()->back()->with(['error'=>'Card not found to reassign, Check Card Number']);
            }
        }
    }

    public function create_request(Request $request, $card_holder = null, $card_number = null, $fuel_deduction_type_id = null, $fuel_type_id = null, $amount = null)
    {
        $table = new FuelCardRequest();
        $table->card_holder_id = $card_holder;
        $table->card_holder_type_id = $request->card_holder_type;
        $table->fleet_vehicle_type_id = $request->fleet_vehicle_type == '' ? null : $request->fleet_vehicle_type;
        $table->card_request_type_id = $request->card_request_type;
        $table->requested_by = Auth::id();
        $table->card_number = $card_number;
        $table->fuel_deduction_type_id = $fuel_deduction_type_id;
        $table->fuel_type_id = $fuel_type_id;
        $table->amount = $amount;
        $table->save();

        $table->fuel_request_id = '000000'.$table->id;
        $table->update();

        return $table->id;
    }

    public function request_log($fuel_card_request_id,$action_type_id,$card_request_type_id = null,$remarks = null)
    {
        $table = new FuelCardLog();
        $table->fuel_card_request_id = $fuel_card_request_id;
        $table->admin_id = Auth::id();
        $table->card_request_type_id = $card_request_type_id;
        $table->action_type_id = $action_type_id;
        $table->remarks = $remarks;
        $table->save();
    }

    public function request_approve(Request $request)
    {


        $table = FuelCardRequest::where([['id',$request->request_id],['status',0]]);
        if(!$table->exists())
        {
            return redirect()->back()->with(['error'=>'No Record Found']);
        }

        $table = $table->first();

        if($table->card_request_type_id == 1) {
            $messages = [
                'fuel_deduction_type.required'      =>  'Fuel Deduction Type is required',
                'fuel_type.required'                =>  'Fuel Type is Required',
                'amount.required_'                  =>  'Amount is Required',
            ];

            $rules = [
                'fuel_deduction_type' => 'required',
                'fuel_type' => 'required',
                'amount'   => 'required',
            ];
            $validate = Validator::make($request->all(),$rules,$messages);
            if($validate->fails())
            {
                return redirect()->back()->withErrors($validate);
            }
            if ($table->card_holder_type_id == 1) {
                $card = new FuelStaffCard();
                $card->name = ' ';
                $card->save();

                $card->name = 'TRAXE0-' . ($table->admins->default_hub_id ?? '0000') . '-000000' . $card->id;
                $card->save();


            }
            else if ($table->card_holder_type_id == 2) {
                $card = new FuelRiderCard();
                $card->name = ' ';
                $card->save();

                $card->name = 'TRAXR0-' . ($table->riders->city_id ?? '0000') . '-000000' . $card->id;
                $card->save();
            }
            else if ($table->card_holder_type_id == 3) {
                if ($table->fleet_vehicle_type_id == 1) {
                    $card = new FuelFleetOfficeCard();
                    $card->name = ' ';
                    $card->save();

                    $card->name = 'TRAXO0-' . ($table->fleet->city_id ?? '0000') . '-000000' . $card->id;
                    $card->save();
                } else if ($table->fleet_vehicle_type_id == 2) {
                    $card = new FuelFleetExternalCard();
                    $card->name = ' ';
                    $card->save();

                    $card->name = 'TRAXFE-' . ($table->fleet->city_id ?? '0000') . '-000000' . $card->id;
                    $card->save();
                } else if ($table->fleet_vehicle_type_id == 3) {
                    $card = new FuelFleetInternalCard();
                    $card->name = ' ';
                    $card->save();

                    $card->name = 'TRAXRFI-' . ($table->fleet->city_id ?? '0000') . '-000000' . $card->id;
                    $card->save();
                } else {
                    return redirect()->back()->with(['error' => 'Record is corrupted']);
                }
                            }
            else {
                return redirect()->back()->with(['error' => 'Record is corrupted']);
            }

            $table->card_number = $card->name;
            $table->fuel_type_id = $request->fuel_type;
            $table->fuel_deduction_type_id = $request->fuel_deduction_type;
            $table->amount = $request->amount;
        }

        if($table->card_request_type_id == 3)
        {
            $blocked = 1;
        }
        if($table->card_request_type_id == 4)
        {
            $blocked = 0;
        }
        if($table->card_request_type_id == 3 || $table->card_request_type_id == 4)
        {
            if ($table->card_holder_type_id == 1) {
                $card = FuelStaffCard::where('name',$table->card_number)->first();
            }
            else if ($table->card_holder_type_id == 2) {
                $card = FuelRiderCard::where('name',$table->card_number)->first();
            }
            else if ($table->card_holder_type_id == 3) {
                if ($table->fleet_vehicle_type_id == 1) {
                    $card = FuelFleetOfficeCard::where('name',$table->card_number)->first();
                } else if ($table->fleet_vehicle_type_id == 2) {
                    $card = FuelFleetExternalCard::where('name',$table->card_number)->first();
                } else if ($table->fleet_vehicle_type_id == 3) {
                    $card = FuelFleetInternalCard::where('name',$table->card_number)->first();
                } else {
                    return redirect()->back()->with(['error' => 'Record is corrupted']);
                }
            }
            else {
                return redirect()->back()->with(['error' => 'Record is corrupted']);
            }

            $card->blocked = $blocked;
            $card->update();
        }

        $table->approved_by = Auth::id();
        $table->approved_at = Carbon::now();
        $table->status = 1;
        $table->update();

        $this->request_log($table->id,6,$table->card_request_type_id);

        return redirect()->back()->with(['success'=>'Card Request Approved Successfully']);
    }

    public function request_edit(Request $request)
    {
        $messages = [
            'fuel_deduction_type.required'      =>  'Fuel Deduction Type is required',
            'fuel_type.required'                =>  'Fuel Type is Required',
            'amount.required_'                  =>  'Amount is Required',
        ];

        $rules = [
            'fuel_deduction_type' => 'required',
            'fuel_type' => 'required',
            'amount'   => 'required',
        ];
        $validate = Validator::make($request->all(),$rules,$messages);
        if($validate->fails())
        {
            return redirect()->back()->withErrors($validate);
        }

        $table = FuelCardRequest::where('id',$request->request_id)->whereNotNull('card_number');

        if(!$table->exists())
        {
            return redirect()->back()->with(['error'=>'No Record Found']);
        }

        $table = $table->first();
        $table->fuel_type_id = $request->fuel_type;
        $table->fuel_deduction_type_id = $request->fuel_deduction_type;
        $table->amount = $request->amount;
        $table->update();

        $this->request_log($table->id,5);

        return redirect()->back()->with(['success'=>'Request Edited Successfully']);
    }

    public function request_history(Request $request)
    {
        $data = null;
        if(request()->ajax())
        {
            $fuel_request_id = $request->fuel_request_id;
            $data = FuelCardRequest::where('fuel_request_id',$fuel_request_id);
            if($data->exists()) {
                $request = clone($data);
                $request = $request->leftjoin('fuel_types as ft', 'fuel_card_requests.fuel_type_id', '=', 'ft.id')
                        ->leftjoin('fuel_deduction_types as fdt', 'fuel_card_requests.fuel_deduction_type_id', '=', 'fdt.id')
                        ->leftjoin('admins as requested_by', 'fuel_card_requests.requested_by', '=', 'requested_by.id')
                        ->leftjoin('admins as approved_by', 'fuel_card_requests.approved_by', '=', 'approved_by.id')
                        ->leftjoin('card_holder_types as cht', 'fuel_card_requests.card_holder_type_id', '=', 'cht.id')
                        ->leftjoin('fuel_card_request_types as fcrt', 'fuel_card_requests.card_request_type_id', '=', 'fcrt.id')
                        ->leftJoin('admins as staff', function ($join) {
                            $join->on('staff.id', '=', 'fuel_card_requests.card_holder_id')
                                ->where('fuel_card_requests.card_holder_type_id', '=', DB::raw(1))
                                ->whereNotNull('fuel_card_requests.card_holder_id');
                        })
                        ->leftJoin('riders as rider', function ($join) {
                            $join->on('rider.id', '=', 'fuel_card_requests.card_holder_id')
                                ->where('fuel_card_requests.card_holder_type_id', '=', DB::raw(2))
                                ->whereNotNull('fuel_card_requests.card_holder_id');
                        })
                        ->leftJoin('fleet_vehicles as fleet', function ($join) {
                            $join->on('fleet.id', '=', 'fuel_card_requests.card_holder_id')
                                ->where('fuel_card_requests.card_holder_type_id', '=', DB::raw(3))
                                ->whereNotNull('fuel_card_requests.card_holder_id');
                        })
                        ->select('fcrt.name as card_request_type', 'fuel_card_requests.fuel_request_id as fuel_request_id', 'fuel_card_requests.card_number as card_number', 'cht.name as card_holder_type', 'fuel_card_requests.card_holder_type_id as card_holder_type_id', 'fuel_card_requests.amount as amount', 'ft.name as fuel_type', 'fdt.name as fuel_deduction_type', 'requested_by.name as requested_by', 'approved_by.name as approved_by', 'fuel_card_requests.approved_at as approved_at', 'staff.name as staff_name', 'rider.name as rider_name', 'fleet.name as fleet_name')
                        ->first();

                $logs_data = $data->leftjoin('fuel_card_logs as logs', 'fuel_card_requests.id', '=', 'logs.fuel_card_request_id')
                                ->leftjoin('action_types as actions', 'logs.action_type_id', '=', 'actions.id')
                                ->leftjoin('admins as admin', 'logs.admin_id', '=', 'admin.id')
                                ->select('actions.name as action','logs.created_at as created_at','admin.name as approved_by')
                                ->get();
                return response()->json(['status'=> 1,'request'=>$request,'logs'=>$logs_data]);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Invalid Fuel Request Id']);
            }
        }
        return view('admin.user_management.fuel.history');
    }

}
