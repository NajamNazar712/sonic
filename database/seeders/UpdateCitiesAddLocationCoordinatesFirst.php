<?php

use Illuminate\Database\Seeder;

class UpdateCitiesAddLocationCoordinatesFirst extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->where('id', 202)->update(['location_latitude' => 24.8659, 'location_longitude' => 67.07732]);
        DB::table('cities')->where('id', 223)->update(['location_latitude' => 31.4328, 'location_longitude' => 74.316]);
        DB::table('cities')->where('id', 174)->update(['location_latitude' => 33.63032, 'location_longitude' => 73.05018]);
        DB::table('cities')->where('id', 302)->update(['location_latitude' => 32.07574, 'location_longitude' => 72.67675]);
        DB::table('cities')->where('id', 144)->update(['location_latitude' => 31.42095, 'location_longitude' => 73.10403]);
        DB::table('cities')->where('id', 251)->update(['location_latitude' => 30.19671, 'location_longitude' => 71.46555]);
        DB::table('cities')->where('id', 158)->update(['location_latitude' => 32.17122, 'location_longitude' => 74.1852]);
        DB::table('cities')->where('id', 315)->update(['location_latitude' => 32.48857, 'location_longitude' => 74.51017]);
        DB::table('cities')->where('id', 271)->update(['location_latitude' => 34.03034, 'location_longitude' => 71.533]);
        DB::table('cities')->where('id', 186)->update(['location_latitude' => 32.93183, 'location_longitude' => 73.7337]);
        DB::table('cities')->where('id', 284)->update(['location_latitude' => 28.41389, 'location_longitude' => 70.31872]);
        DB::table('cities')->where('id', 318)->update(['location_latitude' => 27.71009, 'location_longitude' => 68.85089]);
        DB::table('cities')->where('id', 293)->update(['location_latitude' => 30.68688, 'location_longitude' => 73.09575]);
        DB::table('cities')->where('id', 172)->update(['location_latitude' => 25.40383, 'location_longitude' => 68.36534]);
        DB::table('cities')->where('id', 110)->update(['location_latitude' => 29.35435, 'location_longitude' => 71.69106]);
        DB::table('cities')->where('id', 283)->update(['location_latitude' => 30.19749, 'location_longitude' => 67.02037]);
        DB::table('cities')->where('id', 101)->update(['location_latitude' => 34.18666, 'location_longitude' => 73.23255]);
    }
}
