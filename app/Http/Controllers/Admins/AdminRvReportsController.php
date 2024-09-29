<?php

namespace App\Http\Controllers\Admins;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Controllers\Controller;
use App\Http\Traits\RvTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class AdminRvReportsController extends Controller
{
    use RvTrait;
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

   
}
