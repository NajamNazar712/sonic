<?php

use Illuminate\Database\Seeder;

class UpdateLeadReasonsForBlockStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lead_reasons')->insert(array(
            array('id' => 8, 'name' => 'Unresponsive'),
            array('id' => 9, 'name' => 'Customer Wants To Be Contacted Later'),
            array('id' => 11, 'name' => 'Customer Needs More Time'),
            array('id' => 12, 'name' => 'General Query'),
            array('id' => 13, 'name' => 'Rates Negotiations')
        ));

        DB::table('lead_status_reasons')->insert(array(
            array('id' => 11, 'status_id' => 11,'reason_id' => 8),
            array('id' => 12, 'status_id' => 11,'reason_id' => 9),
            array('id' => 13, 'status_id' => 11,'reason_id' => 11),
            array('id' => 14, 'status_id' => 11,'reason_id' => 12),
            array('id' => 15, 'status_id' => 11,'reason_id' => 13),
        ));
    }
}
