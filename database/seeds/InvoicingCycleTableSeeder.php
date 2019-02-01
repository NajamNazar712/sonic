<?php

use Illuminate\Database\Seeder;

class InvoicingCycleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoicing_cycles')->truncate();

        DB::table('invoicing_cycles')->insert(array(
            array('id' => 1,'name'=>'Weekly'),
            array('id' => 2,'name'=>'Fortnightly'),
            array('id' => 3,'name'=>'Weekly'),
            ));
    }
}
