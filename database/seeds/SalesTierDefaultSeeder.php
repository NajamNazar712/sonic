<?php

use Illuminate\Database\Seeder;

class SalesTierDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = \Carbon\Carbon::now();
        DB::table('sales_tiers')->truncate();

        DB::table('sales_tiers')->insert(array(
            array('id' => 1, 'tier_name' => 'Sales Person', 'tier_type' => 1, 'added_by' => 6, 'sales_status' => 1, 'commission' => 2.5 ,'created_at' => $time, 'updated_at' => $time),
        ));
    }
}
