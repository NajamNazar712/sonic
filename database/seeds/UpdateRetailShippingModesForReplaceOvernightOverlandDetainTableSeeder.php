<?php

use Illuminate\Database\Seeder;
use App\http\Models\Admin\Retail\RetailShippingMode;

class UpdateRetailShippingModesForReplaceOvernightOverlandDetainTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RetailShippingMode::where('id', 1)->update(['name'=>'Rush']);
        RetailShippingMode::where('id', 2)->update(['name'=>'Swift']);
        RetailShippingMode::where('id', 4)->update(['name'=>'Saver+']);
    }
}
