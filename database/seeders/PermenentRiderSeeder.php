<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\ModulePermission;
class PermenentRiderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $module = ModulePermission::find(99);
        $module->name = 'Permanent Rider - Enable/Disable';
        $module->save();
    }
}
