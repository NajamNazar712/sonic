<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Http\Models\Shipper\UserBankInfo;

class UpdateUserBankInfoForDefaultBankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserBankInfo::where('default_bank', 0)->update([
            'default_bank' => 1
        ]);
    }
}
