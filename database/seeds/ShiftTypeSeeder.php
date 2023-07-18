<?php

use Illuminate\Database\Seeder;

class ShiftTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shift_types')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('shift_types')->insert(array(
            array('id' => 1, 'name' => 'Employee Shift','created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('id' => 2, 'name' => 'Contractual Shift','created_at' => $timestamp, 'updated_at'=>$timestamp ),

        ));
    }
}
