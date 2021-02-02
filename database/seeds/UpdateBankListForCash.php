<?php

use App\Http\Models\BanksList;
use Illuminate\Database\Seeder;

class UpdateBankListForCash extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      BanksList::create([
            'name' => 'Cash',
            'code' => '',
            'affiliate' => 1,
            'status' => 1,
        ]);

    }
}
