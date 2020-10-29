<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminInternationalRatesController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function add_rates_index($id){
        if($id){
            $user = User::find($id);
            if($user){
                $cities = City::where('business_category_id', 2)->select('id', 'name')->get();

                return view('admin.international.rates.add_rates')->with(['cities' => $cities, 'user' => $user]);
            }
            return redirect()->back()->with('error', 'No User Found!');
        }
        return redirect()->back()->with('error', 'No data found!');
    }

    public function add_rates_submit(Request $request){

    }
}
