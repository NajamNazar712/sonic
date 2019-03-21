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
        DB::table('crm_request_channel')->truncate();

        DB::table('crm_request_channel')->insert(array(
            array('id' => 1, 'channel' => 'Call'),
            array('id' => 2, 'channel' => 'Email'),
            array('id' => 3, 'channel' => 'Facebook'),
            array('id' => 4, 'channel' => 'Sonic'),
            array('id' => 5, 'channel' => 'Website')
        ));
    }
}
