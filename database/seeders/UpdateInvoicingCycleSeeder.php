<?php

use App\Http\Models\Shipper\UserBankInfo;
use Illuminate\Database\Seeder;

class UpdateInvoicingCycleSeeder extends Seeder
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
            array('id' => 3,'name'=>'Monthly'),
            array('id' => 4,'name'=>'Daily')
        ));


        UserBankInfo::where('invoicing_cycle_id', 1)->update(['invoicing_cycle_id' => 3]);
    }
}
