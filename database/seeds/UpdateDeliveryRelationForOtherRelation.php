<?php

use Illuminate\Database\Seeder;

class UpdateDeliveryRelationForOtherRelation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_relations')->insert(array(
            array('id' => 7 ,'name'=>'Other Relation'),
        ));
    }
}
