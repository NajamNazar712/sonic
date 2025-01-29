<?php

use App\Http\Models\Admin\GlobalSettings;
use Illuminate\Database\Seeder;

class botCallStatusAddGlobalSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        GlobalSettings::where('type', 'bot_call_enable_disable')->update(['text'=> "1,5,6,8,19,33,38,45,49,52,60,63,68,74,77"]); 
    }
}
