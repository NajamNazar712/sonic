<?php

namespace App\Http\Controllers\Admins\Settings\Shippers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseRateRivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index()
    {
        return view('admin.settings.shippers.base_rate_revision.index');
    }
}
