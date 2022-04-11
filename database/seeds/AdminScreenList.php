<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdminScreenList extends Seeder
{

    public function run()
    {
        DB::table('admins_screen_list')->where('id', 396)->update(['name' => 'Supply Chain->Cargo Manifest Draft Setting' , 'url' => 'admin.cargo_manifest.draft.setting' , 'permission_id' => 682]);
    }
}
