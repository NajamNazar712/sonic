<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LastMileStatusReportController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function create_report($from, $to){

        $date = Carbon::today()->toDateString();

        $delivery_note_status = array(7, 8, 9, 12, 15, 18, 56);

        $total_status_updated_count = 0;
        $bolt_status_updated_count = 0;
        $sonic_status_updated_count = 0;

        $total_status_updated = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivery_note_status)->where('verification', 0)->whereDate('created_at', $date)->whereTime('created_at', '>=', $from)->whereTime('created_at', '<=', $to)->count();

        $bolt_status_updated = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivery_note_status)->where('verification', 0)->whereNotNull('rider_id')->whereDate('created_at', $date)->whereTime('created_at', '>=', $from)->whereTime('created_at', '<=', $to)->count();

        $sonic_status_updated = ShipmentsJourney::whereNotNull('reference_1_id')->whereIn('shipper_status_id', $delivery_note_status)->where('verification', 0)->whereNull('rider_id')->whereDate('created_at', $date)->whereTime('created_at', '>=', $from)->whereTime('created_at', '<=', $to)->count();

        $total_status_updated_count = $total_status_updated;

        $bolt_status_updated_count = $bolt_status_updated;

        $sonic_status_updated_count = $sonic_status_updated;

        $sonic_status_percentage = 0;
        $bolt_status_percentage = 0;
        if($total_status_updated_count > 0){
            $bolt_status_percentage = ($bolt_status_updated_count / $total_status_updated_count) * 100;
            $sonic_status_percentage = ($sonic_status_updated_count / $total_status_updated_count) * 100;

        }

        $data = array();

        $data['time'] = Carbon::parse($from)->format('h A') . ' - ' .Carbon::parse($to)->format('h A');
        $data['total_status_updated'] = $total_status_updated_count;
        $data['bolt_status_updated'] = $bolt_status_updated_count;
        $data['bolt_status_percentage'] = round($bolt_status_percentage, 2) .'%';
        $data['sonic_status_updated'] = $sonic_status_updated_count;
        $data['sonic_status_percentage'] = round($sonic_status_percentage, 2) .'%';

        return $data;

    }
}
