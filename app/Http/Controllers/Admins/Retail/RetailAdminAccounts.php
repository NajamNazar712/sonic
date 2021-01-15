<?php

namespace App\Http\Controllers\Admins\Retail;

use App\http\Models\Admin\Retail\RetailShipperInfo;
use App\Http\Models\BanksList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class RetailAdminAccounts extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }

    public function index(){
        $banks = BanksList::where('status',1)->get();
      return view('admin.retail.accounts.index')->with(['banks' => $banks]);
    }

    public function list(){
        $retail_shipper_info = RetailShipperInfo::leftjoin('cities as c','c.id','=','retail_shipper_infos.city_id')
            ->leftjoin('banks_lists as b','b.id','=','retail_shipper_infos.bank_id')
        ->select('retail_shipper_infos.id as id','retail_shipper_infos.shipper_name as shipper','retail_shipper_infos.shipper_address as address','retail_shipper_infos.shipper_phone_no as number','city_id','retail_shipper_infos.completed_status as document_status','c.name as city','retail_shipper_infos.created_at as added_at','retail_shipper_infos.iban as iban');

        $datatable = Datatables::of($retail_shipper_info)
            ->editColumn('document_status',function($request){
                if($request->document_status == 0){
                    return 'No Documents';
                }
                else if($request->document_status == 1){
                    return 'Incomplete';
                }
                else{
                    return 'Complete';
                }
            })
//            ->addColumn('action', function($user) {
//                if (session('role_id') == 1 || in_array(429, session('permissions'))) {
//                    $edit_button = '<button type="button" class="dropdown-item edit"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';
//
//                    $dropdown = '
//                    <div class="btn-group">
//                      <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
//                      <div class="dropdown-menu dropdown-menu-sm">
//                ';
//
//                        $dropdown .= $edit_button;
//
//                    $dropdown .= '
//                      </div>
//                    </div>
//                ';
//
//                    return $dropdown;
//                }
//                else {
//                    return '';
//                }
//            })
        ;

        return $datatable->make(true);

    }
//    public function bank_info(Request $request){
//        $id = $request->id;
//        if($id)
//        {
//            $retail_shipper_info = RetailShipperInfo::find($id);
//            $details = array();
//            $details['shipper_name'] =  $retail_shipper_info->shipper_name;
//            if($retail_shipper_info->iban != null) {
//                $details['iban'] = $retail_shipper_info->iban;
//            }
//            if($retail_shipper_info->account_number != null) {
//                $details['account_no'] = $retail_shipper_info->account_number;
//            }
//            if($retail_shipper_info->bank_id != null){
////                $bank = BanksList::find($retail_shipper_info->bank_id)->first();
//                $details['bank_id'] = $retail_shipper_info->bank_id;
//            }
//            return response()->json(['status'=>'0','details'=> $details]);
//        }
//        else{
//            return response()->json(['error'=>'No Data Found','status'=> '1']);
//        }
//    }
}
