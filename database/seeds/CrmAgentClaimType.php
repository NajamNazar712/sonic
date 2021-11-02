<?php

use Illuminate\Database\Seeder;

class CrmAgentClaimType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_agents')->insert(array(
            array('id' => 1, 'admin_id' => 227, 'zone_id'=> 1, 'case_nature_id' => 4),
            array('id' => 2, 'admin_id' => 261, 'zone_id'=> 1, 'case_nature_id' => 4),
        ));
    }
}
