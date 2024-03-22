<?php

namespace App\Http\Controllers\Admins\Logistic;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminCnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public  function  cn_area_store_index()
    {
        return view('admin.logistic.cn_issue_area_store');
    }

}
