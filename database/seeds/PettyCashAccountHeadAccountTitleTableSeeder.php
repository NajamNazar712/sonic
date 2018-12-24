<?php

use Illuminate\Database\Seeder;

class PettyCashAccountHeadAccountTitleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('petty_cash_account_head_account_title')->truncate();

        DB::table('petty_cash_account_head_account_title')->insert(array(
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 2),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 3),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 4),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 5),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 6),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),
            array('petty_cash_account_head_id' =>1, 'petty_cash_account_title_id'=> 1),

        ));
    }
}
