<?php

use Illuminate\Database\Seeder;

class CargoManifestBagStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_manifest_bag_statuses')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('cargo_manifest_bag_statuses')->insert(array(
            array('id' => 1, 'name' => 'Created', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Dispatched from Origin','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Received at Junction', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Onward Forwarded','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Misrouted', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'name' => 'Misrouted Forwarded', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 7, 'name' => 'Received','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 8, 'name' => 'Dispute','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 9, 'name' => 'Short Received','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 10, 'name' => 'Lost','created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
