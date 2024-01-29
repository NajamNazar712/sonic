<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EducationListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp=Carbon::now();
        DB::table('education_lists')->insert(array(
            array('id' => 1, 'name' => 'Matric','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Inter','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Bachelors','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 4, 'name' => 'Master','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 5, 'name' => 'Middle','created_at'=>$timestamp,'updated_at'=>$timestamp)
        ));
    }
}
