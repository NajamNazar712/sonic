<?php

use Illuminate\Database\Seeder;

class AddAppTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now()->format("Y-m-d H:i:s");
        DB::table('app_types')->insert(array(
            array('id' => 1, 'name' => 'Bolt', 'created_at' => $now, 'updated_at' => $now),
            array('id' => 2, 'name' => 'Trax', 'created_at' => $now, 'updated_at' => $now)
        ));
    }
}
