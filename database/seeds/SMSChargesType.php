<?php

use Illuminate\Database\Seeder;

class SMSChargesType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_charges_type')->truncate();

        DB::table('sms_charges_type')->insert(array(
            array('id' => 1,'name' => 'Fixed'),
            array('id' => 2,'name' => 'Per SMS'),
            
        ));
    }
}
