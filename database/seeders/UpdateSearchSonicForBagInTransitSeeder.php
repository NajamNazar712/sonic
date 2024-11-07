<?php

use Illuminate\Database\Seeder;

class UpdateSearchSonicForBagInTransitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Bag In Transit', 'url'=>'admin.cargo_manifest.index', 'permission_id' => 564),
        ));
    }
}
