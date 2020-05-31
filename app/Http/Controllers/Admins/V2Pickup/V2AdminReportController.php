<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\PickupRequest;
use App\Http\Models\Rider;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestAttempt;
use App\Http\Models\V2Pickup\V2PickupRequestLegend;
use App\Http\Models\V2Pickup\V2PickupRequestNotPickReason;
use App\Http\Models\V2Pickup\V2PickupRequestRiderStatus;
use App\Http\Models\V2Pickup\V2PickupRequestStatus;use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Auth;

class V2AdminPickupsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');


        $this->middleware('Permission');
    }
   
}
