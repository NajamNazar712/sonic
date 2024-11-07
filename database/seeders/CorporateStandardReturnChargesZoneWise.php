<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorporateStandardReturnChargesZoneWise extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corporate_standard_return_charge_zone_wises')->truncate();
        DB::table('corporate_standard_return_charge_zone_wises')->insert(array(
            array('shipping_mode_id'=>1,'local'=>100,'same_zone'=>100,'different_zone'=>100),
            array('shipping_mode_id'=>2,'local'=>100,'same_zone'=>100,'different_zone'=>100),
            array('shipping_mode_id'=>3,'local'=>100,'same_zone'=>100,'different_zone'=>100),
            array('shipping_mode_id'=>4,'local'=>100,'same_zone'=>100,'different_zone'=>100),
        ));
    }
}
