<?php

use App\Http\Models\HR\StaffCategory;
use Illuminate\Database\Seeder;

class AddContractualValueInStaffCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StaffCategory::insert(['id'=> 3, 'name'=> 'Contractual', 'created_at'=> now(), 'updated_at'=> now()]);
    }
}
