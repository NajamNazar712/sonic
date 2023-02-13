<?php

use Illuminate\Database\Seeder;

class UpdateBankCashTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks_lists')->insert(array(
//            array('id' => 47, 'name' => 'Cash', 'code' => 'CA', 'affiliate' => 1, 'status' => 1)
            array('id' => 48, 'name' => 'Konnect', 'code' => 'KNT', 'affiliate' => 1, 'status' => 1)
        ));
    }
}
