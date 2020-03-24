<?php

use Illuminate\Database\Seeder;

class UpdateReferenceForRemoveSalePersonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Http\Models\Reference::where('id', 1)->delete();
    }
}
