<?php

use Illuminate\Database\Seeder;

class VigilanceNoteTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vigilance_note_types')->insert(
            array(
                array('id' => 1, 'name' => 'Delivery Note'),
                array('id' => 2, 'name' => 'Return Note'),
            )
        );
    }
}
