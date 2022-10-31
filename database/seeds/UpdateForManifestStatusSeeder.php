<?php

use App\Http\Models\Admin\CargoManifest\CargoManifest;
use Illuminate\Database\Seeder;

class UpdateForManifestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manifest_ids = array(223516, 171967, 145913, 145902, 145914, 145903, 145916, 145904, 145917, 145909, 145912, 145901, 145895, 145898, 145899, 145900, 355887, 336108, 233009);

        foreach ($manifest_ids as $manifest_id){
            $cargo = CargoManifest::find($manifest_id);
            if($cargo){
                $cargo->status_id = 2;
                $cargo->save();
            }
        }
    }
}
