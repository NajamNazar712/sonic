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
            array('id' => 2, 'name' => 'Received at Junction', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Received', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Dispute', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Cancelled', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'name' => 'Received at Junction 1', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 7, 'name' => 'Received at Junction 2', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 8, 'name' => 'Lost', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 9, 'name' => 'Sent From Junction', 'created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
