<?php

use Illuminate\Database\Seeder;

class UpdateBankNameForJazzTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks_lists')->insert(array(
            array('id' => 46, 'name' => 'JAZZ Cash-Mobilink', 'code' => 'JAZZ', 'affiliate' => 0, 'status' => 1)
        ));
    }
}
