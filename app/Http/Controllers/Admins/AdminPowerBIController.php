<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPowerBIController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function sales_dashboard_index(){
        $link = '';

        if (session('role_id') == 1 ||session('user_id') == 405) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNWU5YjkwNjQtZmIzZC00NDE2LWE1YzItYWY3Mzk4NjdlNjUxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }
        else if (in_array(session('user_id'), [167, 1159, 2035])) {
            $link = 'https://app.powerbi.com/view?r=eyJrIjoiNWU5YjkwNjQtZmIzZC00NDE2LWE1YzItYWY3Mzk4NjdlNjUxIiwidCI6IjkwYzY4NjAzLTEzNTgtNGViYi04OWEwLTRmMmFlMzlmMzJjMiIsImMiOjl9';
        }

        return view('admin.reports.power_bi_sales_dashboard')->with(['link' => $link]);
    }
}
