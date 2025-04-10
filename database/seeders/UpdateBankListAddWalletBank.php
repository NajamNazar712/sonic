<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class UpdateBankListAddWalletBank extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $codes = array('Fin');

        $names = array(
            ' Telenor Microfinance Bank / EasyPaisa',
        );

        $affilate = array(1);

        for($i=0;$i<count($names);$i++)
        {
            DB::table('banks_lists')->insert(array(
                array('name' => $names[$i], 'code' => $codes[$i], 'affiliate' => $affilate[$i], 'status' => 1),
            ));
        }
    }
}
