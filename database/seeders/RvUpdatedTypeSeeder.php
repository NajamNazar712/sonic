<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RvUpdatedTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rv_updated_types')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rv_updated_types')->insert(array(
            array('name' => 'Admin','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('name' => 'Agent','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('name' => 'User','created_at' => $timestamp, 'updated_at'=>$timestamp),
            array('name' => 'Substitute User','created_at' => $timestamp, 'updated_at'=>$timestamp),
            array('name' => 'Retail','created_at' => $timestamp, 'updated_at'=>$timestamp),
        ));
    }
}
