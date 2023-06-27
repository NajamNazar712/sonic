<?php

use Illuminate\Database\Seeder;

class AdvancePettyCashStatementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('advance_petty_cash_statement_statuses')->insert(
            array(
                array('id' => 1, 'name' => "Created"),
                array('id' => 2, 'name' => "Completed"),
            )
        );
    }
}
