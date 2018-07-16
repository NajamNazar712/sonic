<?php

namespace App\Http\Controllers\Admins;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminNotificationsController extends Controller
{
    public function __construct() {
      $this->middleware('auth:admin');
    }

    public function index() {
      return view('admin.notifications.index');
    }

}