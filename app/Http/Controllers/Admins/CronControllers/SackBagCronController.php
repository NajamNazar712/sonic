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



        if ($cbs->exists()) {

            $cbs = $cbs->get();

            foreach ($cbs as $cb) {

                // $sackbag = DB::table('issue_sack_bag_origins as isb')->where('isb.id', '=', $cb->sack_bag_id);
                $sackbag = IssueSackBagOrigin::find($cb->sack_bag_id);

                if ($sackbag) {

                    if ($cb->created_at > $sackbag->reporting_date || $cb->created_at = $sackbag->reporting_date) {
                        $sackbag->sack_status_id = 2;
                        $sackbag->reporting_date = $cb->created_at;
                        $sackbag->sack_destination_id = $cb->destination_hub_id;
                        $sackbag->save();
                    }
                }
            }
        }


        //get transit sack bags one day back date

        $tms = DB::table('cargo_manifest_bags as cmb')
            ->join('issue_sack_bag_origins as isb', 'isb.id', '=', 'cmb.sack_bag_id')
            ->where('cmb.is_sack_bag', 1)
            ->where('cmb.status_id', 2)
            ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
            ->groupBy('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at')
            ->select('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at');


        if ($tms->exists()) {

            $tms = $tms->get();

            foreach ($tms as  $tm) {

                $sackbag = IssueSackBagOrigin::find($tm->sack_bag_id);

                if ($sackbag) {

                    if ($tm->created_at >= $sackbag->reporting_date) {
                        $sackbag->sack_status_id = 3;
                        $sackbag->reporting_date = $tm->created_at;
                        $sackbag->sack_destination_id = $tm->destination_hub_id;
                        $sackbag->save();
                    }
                }
            }
        }

        //get received sack bags one day back date

        $brs = DB::table('cargo_manifest_bags as cmb')
            ->join('issue_sack_bag_origins as isb', 'isb.id', '=', 'cmb.sack_bag_id')
            ->where('cmb.is_sack_bag', 1)
            ->where('cmb.status_id', 7)
            ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
            ->groupBy('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at')
            ->select('cmb.sack_bag_id', 'cmb.destination_hub_id', 'cmb.created_at');

        if ($brs->exists()) {

            $brs = $brs->get();

            foreach ($brs as  $br) {

                $sackbag = IssueSackBagOrigin::find($br->sack_bag_id);

                if ($sackbag) {

                    if ($br->created_at >= $sackbag->reporting_date) {

                        $sackbag->sack_status_id = 4;
                        $sackbag->reporting_date = $br->created_at;
                        $sackbag->sack_destination_id = $br->destination_hub_id;
                        $sackbag->save();
                    }
                }
            }
        }


        //SDM Sack BAG one day back date

        //received shipments
        $rev_shipments = DB::table('cargo_manifest_bags as cmb')
            ->join('cargo_manifest_bag_shipments as cmbs', 'cmb.id', '=', 'cmbs.cargo_manifest_bag_id')
            ->join(
                'shipments_journey as sj',
                'cmbs.shipment_id',
                '=',
                'sj.shipment_id'
            )
            ->join('issue_sack_bag_origins as isbo', 'isbo.id', '=', 'cmb.sack_bag_id')
            ->select(
                'cmb.sack_bag_id',
                'sj.city_id as received_city',
                DB::raw('COUNT(sj.shipment_id) as received_shipment'),
                DB::raw('MAX(sj.created_at) as received_created'),
                'cmb.id'
            )
            ->where('sj.shipper_status_id', 4)
            ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
            ->groupBy('cmb.sack_bag_id', 'isbo.sack_bag_no', 'sj.city_id', 'cmb.created_at')
            ->get();




        //misrouted shipments
        $mis_shipments  = DB::table('cargo_manifest_bags as cmb')
            ->join('cargo_manifest_bag_shipments as cmbs', 'cmb.id', '=', 'cmbs.cargo_manifest_bag_id')
            ->join('shipments_journey as sj', 'cmbs.shipment_id', '=', 'sj.shipment_id')
            ->join('issue_sack_bag_origins as isbo', 'isbo.id', '=', 'cmb.sack_bag_id')
            ->select(
                'cmb.sack_bag_id',
                'sj.city_id as misrouted_city',
                DB::raw('COUNT(sj.shipment_id) as misrouted_shipment'),
                DB::raw('MAX(sj.created_at) as misrouted_created')
            )
            ->where('sj.shipper_status_id', 11)
            ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
            ->groupBy('cmb.sack_bag_id', 'isbo.sack_bag_no', 'sj.city_id')
            ->get();




        $rev_count = count($rev_shipments);
        $mis_count = count($mis_shipments);
        if (($rev_count >= $mis_count) || (count($mis_shipments) == 0)) {

            foreach ($rev_shipments as $key => $rev_shipment) {

                if ($mis_count != 0) {

                    foreach ($mis_shipments as $key => $mis_shipment) {


                        if ($rev_shipment->sack_bag_id == $mis_shipment->sack_bag_id) {

                            if ($rev_shipment->received_shipment >= $mis_shipment->misrouted_shipment) {

                                $sackbag = IssueSackBagOrigin::find($rev_shipment->sack_bag_id);

                                if ($sackbag) {

                                    if ($rev_shipment->received_created >= $sackbag->reporting_date) {

                                        $sackbag->sack_status_id = 5;
                                        $sackbag->reporting_date = $rev_shipment->received_created;
                                        $sackbag->sack_destination_id = $rev_shipment->received_city;
                                        $sackbag->bag_count = ($sackbag->bag_count + 1);
                                        $sackbag->save();
                                    }
                                }
                            } else {
                                $sackbag = IssueSackBagOrigin::find($mis_shipment->sack_bag_id);

                                if ($sackbag) {

                                    if ($mis_shipment->misrouted_created >= $sackbag->reporting_date) {

                                        $sackbag->sack_status_id = 5;
                                        $sackbag->reporting_date = $mis_shipment->misrouted_created;
                                        $sackbag->sack_destination_id = $mis_shipment->misrouted_city;
                                        $sackbag->bag_count = ($sackbag->bag_count + 1);
                                        $sackbag->save();
                                    }
                                }
                            }
                        } else {

                            $sackbag = IssueSackBagOrigin::find($rev_shipment->sack_bag_id);

                            if ($sackbag) {

                                if ($rev_shipment->received_created >= $sackbag->reporting_date) {

                                    $sackbag->sack_status_id = 5;
                                    $sackbag->reporting_date = $rev_shipment->received_created;
                                    $sackbag->sack_destination_id = $rev_shipment->received_city;
                                    $sackbag->bag_count = ($sackbag->bag_count + 1);
                                    $sackbag->save();
                                }
                            }
                        }
                    }
                } else {

                    $sackbag = IssueSackBagOrigin::find($rev_shipment->sack_bag_id);

                    if ($sackbag) {
                        if ($rev_shipment->received_created >= $sackbag->reporting_date) {


                            $sackbag->sack_status_id = 5;
                            $sackbag->reporting_date = $rev_shipment->received_created;
                            $sackbag->sack_destination_id = $rev_shipment->received_city;
                            $sackbag->bag_count = ($sackbag->bag_count + 1);
                            $sackbag->save();
                        }
                    }
                }
            }
        } else {

            foreach ($mis_shipments as $key => $mis_shipment) {

                if ($rev_count != 0) {
                    foreach ($rev_shipments as $key => $rev_shipment) {

                        if ($mis_shipment->sack_bag_id == $rev_shipment->sack_bag_id) {

                            if ($mis_shipment->misrouted_shipment >= $rev_shipment->received_shipment) {

                                $sackbag = IssueSackBagOrigin::find($mis_shipment->sack_bag_id);

                                if ($sackbag) {

                                    if ($mis_shipment->misrouted_created >= $sackbag->reporting_date) {

                                        $sackbag->sack_status_id = 5;
                                        $sackbag->reporting_date = $mis_shipment->misrouted_created;
                                        $sackbag->sack_destination_id = $mis_shipment->misrouted_city;
                                        $sackbag->bag_count = ($sackbag->bag_count + 1);
                                        $sackbag->save();
                                    }
                                }
                            } else {
                                $sackbag = IssueSackBagOrigin::find($rev_shipment->sack_bag_id);

                                if ($sackbag) {

                                    if ($rev_shipment->received_created >= $sackbag->reporting_date) {

                                        $sackbag->sack_status_id = 5;
                                        $sackbag->reporting_date = $rev_shipment->received_created;
                                        $sackbag->sack_destination_id = $rev_shipment->received_city;
                                        $sackbag->bag_count = ($sackbag->bag_count + 1);
                                        $sackbag->save();
                                    }
                                }
                            }
                        } else {
                            $sackbag = IssueSackBagOrigin::find($mis_shipment->sack_bag_id);

                            if ($sackbag) {

                                if ($mis_shipment->misrouted_created >= $sackbag->reporting_date) {

                                    $sackbag->sack_status_id = 5;
                                    $sackbag->reporting_date = $mis_shipment->misrouted_created;
                                    $sackbag->sack_destination_id = $mis_shipment->misrouted_city;
                                    $sackbag->bag_count = ($sackbag->bag_count + 1);
                                    $sackbag->save();
                                }
                            }
                        }
                    }
                } else {
                    $sackbag = IssueSackBagOrigin::find($mis_shipment->sack_bag_id);

                    if ($sackbag) {

                        if ($mis_shipment->misrouted_created >= $sackbag->reporting_date) {

                            $sackbag->sack_status_id = 5;
                            $sackbag->reporting_date = $mis_shipment->misrouted_created;
                            $sackbag->sack_destination_id = $mis_shipment->misrouted_city;
                            $sackbag->bag_count = ($sackbag->bag_count + 1);
                            $sackbag->save();
                        }
                    }
                }
            }
        }

        // old work

        // // $sdms = DB::table('cargo_manifest_bags as cmb')
        // //     ->join('cargo_manifest_bag_shipments as cmbs', 'cmbs.cargo_manifest_bag_id', '=', 'cmb.id')
        // //     ->join('issue_sack_bag_origins as isb', 'isb.id', '=', 'cmb.sack_bag_id')
        // //     ->join('shipments_journey as sj', 'sj.shipment_id', '=', 'cmbs.shipment_id')
        // //     ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
        // //     ->select('cmb.sack_bag_id', 'sj.shipment_id', 'sj.shipper_status_id', 'sj.city_id', 'sj.created_at');

        // $sdms = DB::table('cargo_manifest_bags as cmb')
        //     ->join('cargo_manifest_bag_shipments as cmbs', 'cmb.id', '=', 'cmbs.cargo_manifest_bag_id')
        //     ->join('issue_sack_bag_origins as isb', 'isb.id', '=', 'cmb.sack_bag_id')
        //     ->join(
        //         'shipments_journey as sj',
        //         'sj.shipment_id',
        //         '=',
        //         'cmbs.shipment_id'
        //     )
        //     ->whereIn('sj.shipper_status_id', [4, 11])
        //     ->whereBetween('cmb.created_at', [$previous_day_date . ' 00:00:01', $previous_day_date . ' 23:59:59'])
        //     ->groupBy('cmb.sack_bag_id', 'sj.city_id', 'sj.shipper_status_id')
        //     ->select(
        //         'cmb.sack_bag_id',
        //         DB::raw('COUNT(sj.shipment_id) as shipment_count'),
        //         'sj.city_id',
        //         DB::raw('MAX(sj.created_at) as max_created_at'),
        //         'sj.shipper_status_id'
        //     );


        // if ($sdms->exists()) {

        //     $sdms = $sdms->get();
        //     $total_received = 0;
        //     $total_misrouted = 0;
        //     $max_created = '';
        //     $old_sack_bag_id = 0;
        //     $arr = array();
        //     $count = 0;
        //     $sack_bag_list = IssueSackBagOrigin::where('status', 1)->get();
        //     $sdms = $sdms->toArray();

        //     foreach ($sdms as $sdm) {

        //         dd(in_array(10, $sdms));
        //         // if (in_array($sdm['sack_bag_id'], $sdms)) {
        //         //     echo 1;
        //         // }

        //         // foreach ($sack_bag_list as  $sack_bag) {

        //         //     if ($sack_bag->id == $sdm->sack_bag_id) {

        //         //         echo 'bag_id' . $sack_bag->id;
        //         //         echo 'sdm bag_id' . $sdm->sack_bag_id;
        //         //     }
        //         // }


        //     }
        //     // $old_sack_bag_id = $sdm;
        // }
    }
}
