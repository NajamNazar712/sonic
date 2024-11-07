<?php

use Illuminate\Database\Seeder;

class BagScanningLocation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('bag_scanning_screen_locations')->insert(array(
            array('id' => 1, 'name' => 'Create Cargo Manifest', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Quick Receive Bag(s)', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
