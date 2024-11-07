<?php

use App\Http\Models\Admin\InvoiceAdjustmentReasons;
use Illuminate\Database\Seeder;

class UpdateInvoiceAdjustmentReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceAdjustmentReasons::where('name','WHT Tax Deduction')->update(['name' => 'Income Tax Withhold']);
        InvoiceAdjustmentReasons::where('name','Sales Tax Withholding')->update(['name' => 'Sales Tax Withheld']);
    }
}
