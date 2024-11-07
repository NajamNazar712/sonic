<?php

use Illuminate\Database\Seeder;

class CreateStaffCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('staff_categories')->insert(array(
            array('id' => 1, 'name' => 'Staff', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Intern', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
