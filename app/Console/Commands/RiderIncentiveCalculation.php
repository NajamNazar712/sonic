<?php

namespace App\Console\Commands;

use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RiderIncentiveCalculation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rider_incentive:calculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rider Incentive Calculation Version 2';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //SELECT DISTINCT(`s`.`id`), `s`.`actual_weight` FROM `shipments_journey` AS `sj` INNER JOIN `shipments` AS `s` ON `sj`.`shipment_id` = `s`.`id` WHERE `s`.`packaging_material_request` = 0 AND `sj`.`created_at` >= '2022-09-05 00:00:00' AND `sj`.`created_at` < '2022-09-06 00:00:00' AND `sj`.`shipper_status_id` IN (14, 30, 36, 37) AND `s`.`shipper_status_id` IN (14, 30, 36, 37, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 45, 46) AND NOT FIND_IN_SET(`s`.`user_id`, (SELECT `text` FROM `global_settings` WHERE `type` = 'foc_account_tag')) AND `sj`.`rider_id` IS NOT NULL;
        $foc_accounts = GlobalSettings::where('type', 'foc_account_tag')->first();
        $foc_accounts = explode(',', $foc_accounts);
        $yesterday = Carbon::yesterday()->toDateString();
        $shipments = ShipmentsJourney::join('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->distinct('s.id')
            ->select('s.id as id', 's.actual_weight as actual_weight', 'u.segment_id as segment_id', 'u.sub_segment_id as sub_segment_id')
            ->where('s.packaging_material_request', 0)
            ->whereIn('shipments_journey.shipper_status_id', [14, 30, 36, 37])
            ->whereIn('s.shipper_status_id', [14, 30, 36, 37, 26, 27, 28, 29, 31, 32, 33, 34, 35, 38, 45, 46])
            ->whereNotIn('s.user_id', $foc_accounts)
            ->whereDate('sj.date', $yesterday);

        $already_processed_shipments = array();

        foreach ($shipments as $shipment){
            if(!in_array($shipment->id, $already_processed_shipments)){

                //Work Here..

                $already_processed_shipments[] = $shipment->id;
            }
        }
    }
}
