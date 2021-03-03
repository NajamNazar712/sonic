<?php

namespace App\Http\Controllers\Admins\Fuel;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Fuel\CardHolderType;
use App\Http\Models\Admin\Fuel\FleetVehicle;
use App\Http\Models\Admin\Fuel\FuelDeductionType;
use App\Http\Models\Admin\Fuel\FuelType;
use App\Http\Models\Admin\FuelCardRequest;
use App\Http\Models\Admin\FuelCardRequestType;
use App\Http\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

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
        return view('admin.user_management.fuel.fuel_management',compact('fuel_types','fuel_deduction_types','card_holder_types','card_request_types'));
    }

    public function fuel_list(Request $request)
    {

        $requests = FuelCardRequest::join('fuel_types as ft','fuel_card_requests.fuel_type_id','=','ft.id')
            ->join('fuel_deduction_types as fdt','fuel_card_requests.fuel_deduction_type_id','=','fdt.id')
            ->join('admins as requested_by','fuel_card_requests.requested_by','=','requested_by.id')
            ->join('admins as approved_by','fuel_card_requests.approved_by','=','approved_by.id')
            ->join('card_holder_types as cht','fuel_card_requests.card_holder_type_id','=','cht.id')
            ->select('fuel_card_requests.card_number as card_number','fuel_card_requests.card_holder_id as card_holder','cht.name as card_holder_type','fuel_card_requests.amount as amount','ft.name as fuel_type','fdt.name as fuel_deduction_type','requested_by.name as requested_by','approved_by.name as approved_by','fuel_card_requests.approved_at as approved_at');

        return Datatables::of($requests)->make(true);
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
            $data = FleetVehicle::query();
        }
        else{
            return response()->json(['status'=>0,'error'=>'Invalid Card Holder Type!']);
        }
        return response()->json(['status'=>1,'data'=>$data->get()]);

    }

}
