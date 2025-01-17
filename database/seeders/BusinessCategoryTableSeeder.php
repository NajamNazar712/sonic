<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('business_categories')->truncate();
        DB::table('business_categories')->insert(array(
            array('id' => 1, 'name'=>'Domestic', 'created_at' => $timestamp,'updated_at' => $timestamp),
            array('id' => 2, 'name'=>'International', 'created_at' => $timestamp,'updated_at' => $timestamp)
        ));
    }
}
