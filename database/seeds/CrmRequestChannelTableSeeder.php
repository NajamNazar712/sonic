<?php

use Illuminate\Database\Seeder;

class CrmRequestChannelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_channels')->truncate();

        DB::table('crm_request_channels')->insert(array(
            array('id' => 1, 'channel' => 'Sonic'),
            array('id' => 2, 'channel' => 'Website'),
            array('id' => 3, 'channel' => 'Email'),
            array('id' => 4, 'channel' => 'Call'),
            array('id' => 5, 'channel' => 'WhatsApp'),
            array('id' => 6, 'channel' => 'Facebook'),
            array('id' => 7, 'channel' => 'Self'),
        ));
    }
}
