<?php

use Illuminate\Database\Seeder;

class MasterCargoStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_cargo_statuses')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('master_cargo_statuses')->insert(array(
            array('id' => 1, 'name' => 'In Transit', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Received', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Dispute', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Cancelled', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Lost', 'created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
