<?php

use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Jobs\BotCallDispatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RvTrait;

class ForceFullyFirstCallInitiate extends Seeder
{
    use RvTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timeStart = '2024-10-02 00:00:00'; // Get the timestamp of two hours ago
        // Carbon::parse(now())->subHour(2)->format('Y-m-d H:i') 
        $timeEnd = '2024-10-06 23:59:59';

        // Now, re-initiate process for the retrieved shipment_ids after unresponsive one
        $shipmentfirst = RvShipmentAssignAgent::join('shipments as s', 's.id', 'rv_shipment_assign_agents.shipment_id')->where(['agent_id' => 4620, ['unresponsive_attempt_time', '>=', $timeStart], ['unresponsive_attempt_time', '<=', $timeEnd], 'call_count' => 0])->whereIn('s.shipper_status_id',[12,52,65,66])->whereIn('shipment_id',[43648164,  43620841,  43815280,  43688190,  43715351,  43782465,  43565062,  43579065,  43662102,  43519422,  43503194,  43771367,  43687729,  43732488,  43733093,  43643493,  43666923,  43762845,  43693717,  42992398,  43852566,  43740802,  43106635,  43604768,  43530467,  43615609,  43755247,  43587220,  43694536,  43722425,  43329994,  43570449,  43615747,  43771155,  43776247,  43781049,  43168146,  43487438,  43526060,  43534510,  43535188,  43663669,  43718293,  43603814,  43880182,  43670932,  43606911,  43608667,  43633325,  43682331,  43721861,  43769312,  43929756,  43632854,  43672150,  43755458,  43717485,  43740914,  43770874,  43561344,  43768169,  43355491,  43577648,  43665167,  43139146,  43663754,  43666168,  43668009,  43719017,  43750218,  43785477,  43508496,  43767978,  43667875,  43553472,  43802860,  43645959,  42994199,  43392902,  43614265,  43664582,  43704828,  43495631,  43681541,  43769224,  43528125,  43174596,  43416637,  43660377,  43681080,  43681083,  43742922,  43876904,  43709558,  43213577,  43752715,  43777853,  43799395,  43618351,  43685894,  43725388,  43533499,  43728664,  43773615,  43643392,  43611206,  43693482,  43540363,  43641347,  43730677,  43668880,  43729710,  43602462,  43673375,  43770122,  43694447,  43673428,  43525799,  43658497,  43572364,  43686483,  43532367,  43567281,  43637731,  43425636,  43368301,  43522717,  43645514,  43368278,  43731530,  43731893,  43489348,  43694612,  43814679,  43496070,  43772164,  43571148,  43488766,  43476582,  43688321,  43758891])->pluck('shipment_id');
    //    $shipmentId = Shipment::whereIn('id', [43323534,  43388122,  43474185,  43555308,  43531728,  43137455,  43223268,  43493274,  43002406,  43373279,  43424776,  43259711,  43536984,  43665770,  43043131,  43296517,  43393437,  43559470,  43561801,  43536251,  43616950,  42978503,  43400250,  43303589,  43393430,  43557953])->whereIn('shipper_status_id', [12, 52, 65, 66])->get();
        
        // Log::channel('cronJobLog')->info('s ' . 'Log after  shipmentSeconds call  record' . count($shipmentfirst));
        if (count($shipmentfirst) > 0) {
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully start first-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));

            foreach ($shipmentfirst as $shipmentId) {
                if(RvShipmentAssignAgent::where('shipment_id', $shipmentId->id)->exists()){
                 dispatch(new BotCallDispatch($shipmentId));
                }else{
                    $this->rvshipmentticketInsert($shipmentId->id, $shipmentId->shipper_status_id, 8, $shipmentId->user_id);
                }
                // dispatch(new BotCallDispatch($shipmentId));
            }
            Log::channel('cronJobLog')->info('s ' . 'Call initiate forcefully end first-call' . Carbon::parse(now())->format('Y-m-d H:i:s'));
        }
    }
}
