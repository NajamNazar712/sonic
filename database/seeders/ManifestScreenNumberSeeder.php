<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManifestScreenNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('manifest_screen_numbers')->insert(array(
            array('id' => 1, 'name' => 'Create Bag'),
            array('id' => 2, 'name' => 'Create Open Bag'),
            array('id' => 3, 'name' => 'Create Manifest'),
            array('id' => 4, 'name' => 'Quick Receive Bag'),
            array('id' => 5, 'name' => 'Quick Receive Bag Shipment'),
        ));
    }
}
