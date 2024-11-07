<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\UserBankInfo;

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
            array('id' => 1,'name'=>'Monthly')
            ));

        UserBankInfo::where('invoicing_cycle_id', 2)->update(['generation_date' => 1]);
        UserBankInfo::whereIn('invoicing_cycle_id', [2, 3])->update(['invoicing_cycle_id' => 1]);
    }
}
