<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SdnActionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sdn_action_statuses')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('sdn_action_statuses')->insert(array(
            array('id' => 1, 'name' => 'Edited SDN amount', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Edited deposit slips', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Added SDN adjustment', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Edited SDN adjustment', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Reverted from Resolved to Deposited', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
