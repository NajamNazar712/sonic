<?php

namespace App\Http\Controllers\Admins\Fuel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FuelManagementController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function fuel_index()
    {
        return view('admin/user_management/fuel/fuel_management');
    }
}
