<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\AdjustmentType;
use Carbon\Carbon;
class AddAdjustmentTypeSeederReturnDiscountCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        AdjustmentType::insert(array(
            array('id' => 17, 'name' => 'Manual Adjustment Return Discount Weight Charges', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
