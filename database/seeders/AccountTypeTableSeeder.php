<?php

use Illuminate\Database\Seeder;

class AccountTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('account_types')->truncate();

        DB::table('account_types')->insert(array(
            array('id' => 1, 'name' => 'Reimbursement Account'),
            array('id' => 2, 'name' => 'Corporate Invoicing Account')
        ));
    }
}
