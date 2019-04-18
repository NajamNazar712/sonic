<?php

use Illuminate\Database\Seeder;

class UpdateChargesModeTableReimbursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('charges_modes')->insert(array(
            array('id' => 4, 'charges_mode' => 'Reimbursement')
        ));
    }
}
