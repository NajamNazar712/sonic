<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdminPositionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admin_position_types')->truncate();
        DB::table('admin_position_types')->insert(array(
            array('id' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Permanent'),
            array('id' => 2, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Intern'),
        ));
    }
}
