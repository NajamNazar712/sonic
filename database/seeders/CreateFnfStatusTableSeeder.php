<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateFnfStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fnf_statuses')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('fnf_statuses')->insert(array(
            array('id' => 1, 'name' => 'In-Process','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('id' => 2, 'name' => 'Approved','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('id' => 3, 'name' => 'Rejected','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('id' => 4, 'name' => 'Completed','created_at' => $timestamp, 'updated_at'=>$timestamp )
        ));
    }
}
