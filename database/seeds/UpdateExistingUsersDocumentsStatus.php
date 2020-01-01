<?php

use Illuminate\Database\Seeder;
use \App\Http\Models\Shipper\User;

class UpdateExistingUsersDocumentsStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('status', 3)->update([
            'documents_status' => 2
        ]);
    }
}
