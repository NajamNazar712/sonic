<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminInternationalShipmentsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
}
