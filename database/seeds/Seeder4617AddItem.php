<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Lead\PamItem;

class Seeder4617AddItem extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            array('id'=>1,'name'=>'Double Bed'),
            array('id'=>2,'name'=>'Single Bed'),
            array('id'=>3,'name'=>'Washing Machine'),
            array('id'=>4,'name'=>'Fridge'),
            array('id'=>5,'name'=>'Deep Freezer'),
            array('id'=>6,'name'=>'Sewing Machine'),
            array('id'=>7,'name'=>'Microwave'),
            array('id'=>8,'name'=>'Gas Oven'),
            array('id'=>9,'name'=>'Split Unit'),
            array('id'=>10,'name'=>'Window AC'),
            array('id'=>11,'name'=>'Single Seater Sofa'),
            array('id'=>12,'name'=>'Two Seater Sofa'),
            array('id'=>13,'name'=>'Three Seater Sofa'),
            array('id'=>14,'name'=>'Center Table (Wooden)'),
            array('id'=>15,'name'=>'Center Table (Glass top)'),
            array('id'=>16,'name'=>'Cupboard (Two Door)'),
            array('id'=>17,'name'=>'Cupboard (Three Door)'),
            array('id'=>18,'name'=>'Dressing Table'),
            array('id'=>19,'name'=>'Dining Table Wooden'),
            array('id'=>20,'name'=>'Dining Table With Glass Top'),
            array('id'=>21,'name'=>'Dining Chair (Single)'),
            array('id'=>22,'name'=>'Geezer'),
            array('id'=>23,'name'=>'Computer'),
            array('id'=>24,'name'=>'LCD'),
            array('id'=>25,'name'=>'Crockery (75 pcs)'),
            array('id'=>26,'name'=>'Fridge'),
            array('id'=>27,'name'=>'Microwave'),
            array('id'=>28,'name'=>'Split Unit'),
            array('id'=>29,'name'=>'Window AC'),
            array('id'=>30,'name'=>'Single Seater Sofa'),
            array('id'=>31,'name'=>'Two Seater Sofa'),
            array('id'=>32,'name'=>'Three Seater Sofa'),
            array('id'=>33,'name'=>'Center Table (Wooden)'),
            array('id'=>34,'name'=>'Center Table (Glass top)'),
            array('id'=>35,'name'=>'Cabinet (Single Door)'),
            array('id'=>36,'name'=>'Cabinet (Two Door)'),
            array('id'=>37,'name'=>'Chair (Single)'),
            array('id'=>38,'name'=>'Computer'),
            array('id'=>39,'name'=>'LCD'),
        );

        PamItem::insert($items);

    }
}
