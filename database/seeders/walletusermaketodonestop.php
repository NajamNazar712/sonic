<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class walletusermaketodonestop extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'wallet_user_make_to_done_stop', 'setting_value' => 1, 'text' => '46611,47392,47394,47813,33952,27424,44309,44149,3719,2634,18179,49456,043576,22071,2121,39282,49028,12240,1458,23009,7626,23009', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
