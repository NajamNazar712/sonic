<?php

use Illuminate\Database\Seeder;

class AccountTypeTableChange extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('account_types')->where('id', 1)->update(['name' => 'Cash on Delivery Account']);
    }
}
