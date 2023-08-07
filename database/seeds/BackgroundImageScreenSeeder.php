<?php

use Illuminate\Database\Seeder;

class BackgroundImageScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('background_image_screens')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('background_image_screens')->insert(array(
            array('id' => 1, 'name' => 'Login - Admin', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 2, 'name' => 'Login - Shipper','created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 3, 'name' => 'Login - Retail', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
