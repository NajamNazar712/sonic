<?php

namespace App\Http\Controllers\Admins\Shippers\Accounts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KAMBulkTaggingController extends Controller
{

    public function __construct()
    {   
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }

    public function index(){
        return view('admin.accounts.kam_bulk_tagging');
    }
}
