<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\BanksList;
use App\Http\Models\City;
use App\Http\Models\International\Wholesale\WholesaleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;

class InternationalWholesaleController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function accounts_index(){
//        ActivityTrailController::createActivityTrailLog(Auth::id(),53);
        $cities = City::where('business_category_id', 2)->get();
        $banks = BanksList::where('status', 1)->select('id', 'name')->get();
        return view('admin.international.wholesale.accounts_index')->with(['cities' => $cities, 'banks' => $banks]);
    }

    public function accounts_list(Request $request){
        $shippers = WholesaleUser::join('cities as c', 'c.id', '=', 'wholesale_users.city_id')
            ->join('admins as cb', 'cb.id', '=', 'wholesale_users.created_by')
            ->leftjoin('admins as ub', 'ub.id', '=', 'wholesale_users.updated_by')
            ->select('wholesale_users.id as shipper_id', 'wholesale_users.name as shipper_name', 'wholesale_users.phone', 'wholesale_users.address', 'c.name as city', 'wholesale_users.email', 'wholesale_users.bank_name', 'wholesale_users.bank_account', 'wholesale_users.ntn', 'cb.name as created_by', 'up.name as updated_by', 'wholesale_users.margin', 'wholesale_users.created_at', 'wholesale_users.updated_at', 'wholesale_users.is_document');

        if (session('role_id') != 1) {
            $shippers = $shippers->whereIn('c.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($shippers)
            ->addColumn('shipper_id_padded', function ($shippers) {
                return str_pad($shippers->shipper_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('margin_percentage', function ($shippers) {
                if($shippers->margin != null){
                    return $shippers->margin . '%';
                }
                else{
                    return '';
                }
            })
            ->addColumn('document', function ($shippers) {
                if ($shippers->is_document != 0) {
                    return '<a class="btn btn-sm btn-outline-info align-middle document_view" href="javascript:void(0);"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                } else {
                    return 0;
                }
            });

        return $datatable->make(true);
    }

    public function accounts_store(Request $request){
        return $request;
    }
}
