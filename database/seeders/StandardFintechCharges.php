<?php

use Illuminate\Database\Seeder;

class StandardFintechCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_fintech_charges')->insert(
            [
            ['id' => 1,'standard_fintech_charges' => '4', 'standard_fed_charges' => '1.8'],
            ]
        );
    }
}
