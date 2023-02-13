<?php

use Illuminate\Database\Seeder;

class BagIntransitErrorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seal_numbers = ['1785475', '14431820822381', '1556931', '1698578', '327290210204', '1651885', '28330218212778', '1544765', '31520217580081', '1433145', '327290144636', '1559192', '1363178', '22338116662998', '20220416262563', '22331516043485', '1117374', '31520215657196', '1160646', '132729087443', '1163459', '22312713368036', '14438314015898', '22338419088673', '1698578', '1654012', '1362010', '1588714', '1448563', '1459435', '1363178', '1117808', '1476050', '1654037', '1362065', '1117374', '1160646', '1131959', '1874122', '327290210204', '22338419088673'];

        $cargo_manifest_bags = \App\Http\Models\Admin\CargoManifest\CargoManifestBag::whereIn('seal_number', $seal_numbers);

        if($cargo_manifest_bags->exists()){
            $cargo_manifest_bags = $cargo_manifest_bags->get();

            foreach ($cargo_manifest_bags as $bag){
                $bag->received_shipments = $bag->shipments;
                $bag->completed = 1;
                $bag->save();

                if(count($bag->shipment) > 0){
                    foreach($bag->shipment as $bag_shipment){
                        $bag_shipment->status = 1;
                        $bag_shipment->save();
                    }
                }
            }
        }
    }
}
