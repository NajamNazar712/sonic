<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RvStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rv_states')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rv_states')->insert(array(
            array('id' => 1, 'name' => 'Assigned','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('id' => 2, 'name' => 'UnAssigned','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('id' => 3, 'name' => 'Open','created_at' => $timestamp, 'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Completed','created_at' => $timestamp, 'updated_at'=>$timestamp),

        ));
    }
}
