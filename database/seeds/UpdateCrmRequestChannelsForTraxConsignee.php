<?php

use Illuminate\Database\Seeder;

class UpdateCrmRequestChannelsForTraxConsignee extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_channels')->insert(array(
            array('id' => 7, 'channel' => 'Trax Consignee App')
        ));
    }
}
