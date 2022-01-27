<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForAutoCancelledShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->where('id',488)->delete();
    }
}
