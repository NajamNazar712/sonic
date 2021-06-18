<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;


class AllowanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('allowances')->truncate();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('allowances')->insert(array(
            array('id' => 1, 'name' => 'Laptop', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Mobile(Cash)','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Vehicle','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Conveyance','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Fuel','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 6, 'name' => 'Medical','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 7, 'name' => 'Email ID','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 8, 'name' => 'Sonic ID','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 9, 'name' => 'Others','created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
