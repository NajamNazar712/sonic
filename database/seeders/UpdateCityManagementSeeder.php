<?php

use App\Http\Models\City;
use Illuminate\Database\Seeder;

class UpdateCityManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //disable cities
       $city_ids = array(679,2571,636,661,674,2447,1582,1547,1321,2762,577,1717,567,1324,2825,1572,611,1716,604,2606,1675,609,2829,2752,1785,585,612,641,2486,619,1826,1656,554,1890,1318,1314,639,1722,1688,1624,2788,1700,2246,608,591,2284,2389,1912,1905,1698,2421,2720,603,1640,637,712,708,1596,1327,2268,2951,1704,2230,1263,719,1858,576,2847,2791,602,1828,683,1920,1951,562,651,621,1714,610,598,606,1782,558,2277,607,2957,633,1423,560,2195,1784,2288,3003,582,2574,2419,555,620,618,1532,570,2145,1201,2624,2386,2139,584,1309,2388,1687,634,572,1701,2693,1436,571,747,624,2956,599,1697,1448,667,568,563,1748,556,1645,596,1334,565,706,2944,714,2816,1245,2269,1646,1969,1854,1684,1651,2267,2315,671,1725,1723,2282,580,746,1644,1643,581,2243,594,592,723);

       foreach($city_ids as $city_id){
           $city = City::where('id',$city_id)->first();
           if($city){
               $city->update(['status' => 0,'permanent_disabled' => 1]);
           }
       }

       //update hubs
        City::where('id',679)->update(['hub_id' => 665]);
        City::where('id',636)->update(['hub_id' => 631]);
        City::where('id',684)->update(['hub_id' => 665]);
        City::where('id',687)->update(['hub_id' => 665]);
        City::where('id',675)->update(['hub_id' => 665]);
        City::where('id',676)->update(['hub_id' => 665]);
        City::where('id',674)->update(['hub_id' => 665]);
        City::where('id',688)->update(['hub_id' => 665]);
        City::where('id',642)->update(['hub_id' => 631]);
        City::where('id',681)->update(['hub_id' => 665]);
        City::where('id',682)->update(['hub_id' => 665]);
        City::where('id',577)->update(['hub_id' => 553]);
        City::where('id',567)->update(['hub_id' => 553]);
        City::where('id',586)->update(['hub_id' => 982]);
        City::where('id',614)->update(['hub_id' => 605]);
        City::where('id',705)->update(['hub_id' => 704]);
        City::where('id',559)->update(['hub_id' => 553]);
        City::where('id',604)->update(['hub_id' => 982]);
        City::where('id',617)->update(['hub_id' => 605]);
        City::where('id',635)->update(['hub_id' => 631]);
        City::where('id',609)->update(['hub_id' => 605]);
        City::where('id',646)->update(['hub_id' => 631]);
        City::where('id',653)->update(['hub_id' => 631]);
        City::where('id',678)->update(['hub_id' => 665]);
        City::where('id',647)->update(['hub_id' => 631]);
        City::where('id',640)->update(['hub_id' => 631]);
        City::where('id',585)->update(['hub_id' => 982]);
        City::where('id',612)->update(['hub_id' => 605]);
        City::where('id',641)->update(['hub_id' => 631]);
        City::where('id',619)->update(['hub_id' => 605]);
        City::where('id',729)->update(['hub_id' => 704]);
        City::where('id',564)->update(['hub_id' => 553]);
        City::where('id',601)->update(['hub_id' => 982]);
        City::where('id',710)->update(['hub_id' => 704]);
        City::where('id',554)->update(['hub_id' => 553]);
        City::where('id',715)->update(['hub_id' => 704]);
        City::where('id',578)->update(['hub_id' => 553]);
        City::where('id',644)->update(['hub_id' => 631]);
        City::where('id',629)->update(['hub_id' => 605]);
        City::where('id',677)->update(['hub_id' => 665]);
        City::where('id',670)->update(['hub_id' => 665]);
        City::where('id',639)->update(['hub_id' => 631]);
        City::where('id',575)->update(['hub_id' => 553]);
        City::where('id',608)->update(['hub_id' => 605]);
        City::where('id',591)->update(['hub_id' => 982]);
        City::where('id',656)->update(['hub_id' => 631]);
        City::where('id',945)->update(['hub_id' => 631]);
        City::where('id',649)->update(['hub_id' => 631]);
        City::where('id',630)->update(['hub_id' => 631]);
        City::where('id',603)->update(['hub_id' => 982]);
        City::where('id',648)->update(['hub_id' => 631]);
        City::where('id',637)->update(['hub_id' => 631]);
        City::where('id',712)->update(['hub_id' => 704]);
        City::where('id',709)->update(['hub_id' => 704]);
        City::where('id',689)->update(['hub_id' => 665]);
        City::where('id',685)->update(['hub_id' => 665]);
        City::where('id',588)->update(['hub_id' => 982]);
        City::where('id',597)->update(['hub_id' => 982]);
        City::where('id',708)->update(['hub_id' => 704]);
        City::where('id',722)->update(['hub_id' => 704]);
        City::where('id',655)->update(['hub_id' => 631]);
        City::where('id',638)->update(['hub_id' => 631]);
        City::where('id',1034)->update(['hub_id' => 1034]);
        City::where('id',576)->update(['hub_id' => 553]);
        City::where('id',625)->update(['hub_id' => 605]);
        City::where('id',593)->update(['hub_id' => 553]);
        City::where('id',680)->update(['hub_id' => 665]);
        City::where('id',666)->update(['hub_id' => 665]);
        City::where('id',654)->update(['hub_id' => 631]);
        City::where('id',602)->update(['hub_id' => 982]);
        City::where('id',683)->update(['hub_id' => 665]);
        City::where('id',562)->update(['hub_id' => 553]);
        City::where('id',651)->update(['hub_id' => 631]);
        City::where('id',621)->update(['hub_id' => 605]);
        City::where('id',720)->update(['hub_id' => 704]);
        City::where('id',727)->update(['hub_id' => 704]);
        City::where('id',610)->update(['hub_id' => 605]);
        City::where('id',598)->update(['hub_id' => 605]);
        City::where('id',558)->update(['hub_id' => 553]);
        City::where('id',726)->update(['hub_id' => 704]);
        City::where('id',669)->update(['hub_id' => 665]);
        City::where('id',668)->update(['hub_id' => 665]);
        City::where('id',607)->update(['hub_id' => 605]);
        City::where('id',633)->update(['hub_id' => 631]);
        City::where('id',560)->update(['hub_id' => 553]);
        City::where('id',582)->update(['hub_id' => 982]);
        City::where('id',686)->update(['hub_id' => 665]);
        City::where('id',713)->update(['hub_id' => 704]);
        City::where('id',566)->update(['hub_id' => 553]);
        City::where('id',645)->update(['hub_id' => 605]);
        City::where('id',561)->update(['hub_id' => 553]);
        City::where('id',555)->update(['hub_id' => 553]);
        City::where('id',620)->update(['hub_id' => 605]);
        City::where('id',587)->update(['hub_id' => 982]);
        City::where('id',618)->update(['hub_id' => 605]);
        City::where('id',570)->update(['hub_id' => 553]);
        City::where('id',1201)->update(['hub_id' => 1200]);
        City::where('id',584)->update(['hub_id' => 982]);
        City::where('id',615)->update(['hub_id' => 605]);
        City::where('id',600)->update(['hub_id' => 982]);
        City::where('id',634)->update(['hub_id' => 631]);
        City::where('id',572)->update(['hub_id' => 553]);
        City::where('id',628)->update(['hub_id' => 605]);
        City::where('id',652)->update(['hub_id' => 631]);
        City::where('id',571)->update(['hub_id' => 553]);
        City::where('id',725)->update(['hub_id' => 704]);
        City::where('id',583)->update(['hub_id' => 982]);
        City::where('id',624)->update(['hub_id' => 605]);
        City::where('id',599)->update(['hub_id' => 982]);
        City::where('id',667)->update(['hub_id' => 665]);
        City::where('id',568)->update(['hub_id' => 553]);
        City::where('id',563)->update(['hub_id' => 553]);
        City::where('id',556)->update(['hub_id' => 553]);
        City::where('id',596)->update(['hub_id' => 982]);
        City::where('id',574)->update(['hub_id' => 553]);
        City::where('id',565)->update(['hub_id' => 553]);
        City::where('id',706)->update(['hub_id' => 704]);
        City::where('id',724)->update(['hub_id' => 704]);
        City::where('id',714)->update(['hub_id' => 704]);
        City::where('id',573)->update(['hub_id' => 553]);
        City::where('id',595)->update(['hub_id' => 982]);
        City::where('id',622)->update(['hub_id' => 605]);
        City::where('id',717)->update(['hub_id' => 704]);
        City::where('id',632)->update(['hub_id' => 631]);
        City::where('id',672)->update(['hub_id' => 665]);
        City::where('id',671)->update(['hub_id' => 665]);
        City::where('id',718)->update(['hub_id' => 704]);
        City::where('id',580)->update(['hub_id' => 982]);
        City::where('id',650)->update(['hub_id' => 631]);
        City::where('id',690)->update(['hub_id' => 665]);
        City::where('id',581)->update(['hub_id' => 982]);
        City::where('id',589)->update(['hub_id' => 982]);
        City::where('id',627)->update(['hub_id' => 605]);
        City::where('id',557)->update(['hub_id' => 553]);
        City::where('id',590)->update(['hub_id' => 982]);
        City::where('id',594)->update(['hub_id' => 982]);
        City::where('id',626)->update(['hub_id' => 982]);
        City::where('id',592)->update(['hub_id' => 982]);
        City::where('id',643)->update(['hub_id' => 631]);
        City::where('id',721)->update(['hub_id' => 704]);
        City::where('id',723)->update(['hub_id' => 704]);
        City::where('id',707)->update(['hub_id' => 704]);
        City::where('id',673)->update(['hub_id' => 665]);
        City::where('id',711)->update(['hub_id' => 704]);
        City::where('id',613)->update(['hub_id' => 605]);
        City::where('id',716)->update(['hub_id' => 704]);
        City::where('id',728)->update(['hub_id' => 704]);

    }

    
}
