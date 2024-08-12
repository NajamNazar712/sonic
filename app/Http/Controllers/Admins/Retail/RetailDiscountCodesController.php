<?php

namespace App\Http\Controllers\Admins\Retail;

use App\RetailDiscountCode;
use App\Http\Controllers\Admins\ActivityTrailController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class RetailDiscountCodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 801);

        $discounts = RetailDiscountCode::all();

        return view('admin.settings.retail.retail_discount_codes.index', ['discounts' => $discounts]);
    }

    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 802);
        }

        $baseRateRevisions = RetailDiscountCode::all();

        $datatable = Datatables::of($baseRateRevisions);

        return $datatable->make(true);
    }
}
