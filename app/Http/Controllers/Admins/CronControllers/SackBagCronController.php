<?php

namespace App\Http\Controllers\admins\CronControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\CargoManifest\IssueSackBagOrigin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SackBagCronController extends Controller
{

    // get all data day minus one date 

    static public function sackbag_status_update()
    {

        $previous_day_date = Carbon::now()->subDay()->toDateString();

        //get created sack bags one day back date

        $cbs = DB::table('cargo_manifest_bags as cmb')
            ->join('issue_sack_bag_origins as isb', 'isb.id', '=', 'cmb.sack_bag_id')
            ->where('cmb.is_sack_bag', 1)
            ->where('cmb.status_id', 1)
            ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
            ->select('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at')
            ->groupBy('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at');

        // if ($cbs->exists()) {

        //     $cbs = $cbs->get();

        //     foreach ($cbs as $cb) {

        //         // $sackbag = DB::table('issue_sack_bag_origins as isb')->where('isb.id', '=', $cb->sack_bag_id);
        //         $sackbag = IssueSackBagOrigin::find($cb->sack_bag_id);

        //         if ($sackbag) {
        //             if ($cb->created_at > $sackbag->reporting_date || $cb->created_at = $sackbag->reporting_date) {
        //                 $sackbag->sack_status_id = 2;
        //                 $sackbag->reporting_date = $cb->created_at;
        //                 $sackbag->sack_destination_id = $cb->destination_hub_id;
        //                 $sackbag->save();
        //             }
        //         }
        //     }
        // }


        //get transit sack bags one day back date

        $tm = DB::table('cargo_manifest_bags as cmb')
            ->join('issue_sack_bag_origins as isb', 'isb.id', '=', 'cmb.sack_bag_id')
            ->where('cmb.is_sack_bag', 1)
            ->where('cmb.status_id', 2)
            ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
            ->groupBy('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at')
            ->select('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at')
            ->get();

        dd($tm);
    }
}
