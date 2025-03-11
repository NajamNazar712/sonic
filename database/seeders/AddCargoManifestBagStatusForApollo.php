<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class AddCargoManifestBagStatusForApollo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_manifest_bag_statuses')->insert(array(
            array('id' => 50, 'name' => 'Completed', 'created_at' => Carbon::now())
        ));
    }
}
