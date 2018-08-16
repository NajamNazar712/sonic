<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function access_denied() {
        return view('admin.access_denied');
    }
}
