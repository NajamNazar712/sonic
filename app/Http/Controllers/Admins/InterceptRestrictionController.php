<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Shipper\User;

class InterceptRestrictionController extends Controller
{
    public function intercept_restriction_index()
    {
        $shippers = User::get();
        // return view('admin.settings.interception_restriction.shipper_exclude', compact('shippers'));
    }
}
