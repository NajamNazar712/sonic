<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddAreaTerritoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('area_territories')->truncate();

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        $areas = array('Tariq Road','Airport','Malir','MODEL COLONY', 'Port Qasim','Shah Faisal','ii Chundirgarh','Soldier Bazar','johar','Korangi','Saddar Karachi','Boultan Market','Sindhi Muslim','Baloch colony','Bhadurabad','Dhoraji karachi','DMCHS','Jamshed Road','PECHS','Head Office','PECHS','Telenor ','Clifton','Warehouse','Clifton','DHA','Manzoor Colony','Baldia','Arif Town','Chungi amar sindhu','Ferozepur road','green colony','Ichhra','Kahna Nau','Kalma chowk','Qanchi','Rohi nala','Satellite Town', 'Lower mall ','Queens road','anar kali','Anarkali','Azam market','Badami Bagh','Baghban Pura','Begamkot Lahore','Bilal Gunj','Bogiwal Singh Pura','Brandreth Road ','Bund Road','Central park ','Chah Miran','Daroghewala','Davis Road','Dharampura ','Divine Garden','Durand road','Empress Road ','Factory area','Garhi Shahu','Gujjar Pura','Hall road','Hurbanspura','Iqbal Park','Jallo Mor','Lahore Hotel','Lower Mall','main bazar','Main City','main defence','montgomery road','Mozang','Mughal Pura','New Chuburji','one mustahoead','Pehalwana Park','PUECH Lahore','Qila Gujjar Singh','Queens Road','Raiwind road','Rajgarh','ravi road','Rehman Pura','Salamat pura','Sanda','SANDA LAHORE','Sant Nagar','Shadbagh','Shah Alam','Shah Alam Market','Shahdara','shalamar town','Sultan Pura','Super Town','UET','Upper Mall','Urdu Bazar','Wassan Pura','Bahria town','Defence Road','gulbahar block','Nasheman Iqbal','Eden Values Homes','Defence road','Eden value homes','Izmir Town','EME society','Eden lane villas DHA Rahber','Becon house state Ada plot','Canal View Housing Society','Islampura','Samanabad','Sabzazar','Multan Road','Gulshan ravi','Mansoorah','canel view','Sacheme mor','Samnabad','town','Wahdat Road','Bllock ','Allama ','Muslim town','sabzar','Factory area','allah ','Sodiwal','Chauburji','Thokar niaz baig','Pak arab housing society','mustafa town','canel bank','margzhar colony','Band road','Pakarab Housing Scheme','thokar niaz','Punjab University Town','Janak Nagar','Islamia park','Canal View Society','Hanjerwal','Park view Villas ','Shahkamal Colony','Tech society','Tricon Village','Nishat colony','New ','Iqbal','Gulshan e Ravi','Nishter Colony','Chowk yateem khana','Maraghzar Colony','Canal road','Askari 10','Askari 11','Askari 9','Banker Society','Barki road','Bedian road','Bhatta chowk','Cantt','Defence road','dha','DHA 1','DHA 3','DHA 4','DHA 6','DHA Phase 1','DHA Phase 3','DHA Phase 4','DHA Phase 5','DHA Phase 6','DHA Phase 8','Factory area','Ghazi road','guldshat town','iqbal park','Johar town','lahore cantt','Lahore pakistan','Nadirabad','Paragon society','park view','PGCES','phase 5, dha.','Punjab Cooperative Housing Society','Punjab Society','sadar bazar','Shalimar Town','State life Housing Society','State life society','sui gas colony','Sui gas society','Walton','PCSIR','Valencia town','Airline Housing Society','Al Rehman Garden','Ameer chowk','Architect Housing Society','Audit & accounts society','College Road','Faisal Town','Green town','Johar Town','Kot lakhpat','Judicial colony','Maragzar Colony','Military accounts society','NFC Society','OPF Housing Society','PCSIR ','Peco Road','peoples colony 59','PIA Society','Township','Valencia town','Wapda Town','cavalry ground','Garden Town','Green town','Gulberg ','Gulberg 2','Gulberg 3','Gulberg III','Jail Road','Liberty','Main Boulevard Gulberg','MM Alam Road','Model town','Model Town Link Road','New Garden Town','Plaza barkat','Shadman','Shah Jamal','Shah jamal colony','Shalimar Garden','Susan Road','Waris Road','Azeem Town','Bahria Enclave','banigala','Bhara Kahu','Bilal Town','Chak Shehzad','Chattha Bakhtawar','Ghauri Town','Khanna Pull','Kurri Road','Mulpur Road','Taramri Chowk','F-15/1','F-17','G-11/2','G-13','G-13/1','G-13/2','G-15/1','G-15/4','G-8/2','G-9/4','Gulshan e taleem', 'Blue Area','F-6','F-7','F-7/2','Fazal-ul-Haq Road','jinnah Super','Golra Mor','I-10/1','I-11/3','I-9/1','I-9/2','Khayaban e Sirsyed','Margalla Town','Westridge','Bahria Town','DHA','Dist Court','lalazar','Model Town','Morgah','Naval Anchorage','sector 8','Soan Garden','Azeem Town','Bahria Enclave','banigala','Bhara Kahu','Bilal Town','Chak Shehzad','Chattha Bakhtawar','Ghauri Town','Khanna Pull','Kurri Road','Mulpur Road','Taramri Chowk', 'AECHS','Airport Housing Society','Chaklala','Gulraiz','Jilaniabad ','Judicial Housing Society','Lawyer Town','Raheemabad','Scheme 3','6th Road','Asghar Mall Road','Banni Chowk','Chandni Chowk','College Road','commettie Chowk','Dhok Gangal','Gorden College Road','Millat Colony','Murree Road','Pindora','Raja Bazar','Rasheed Colony','Rehmanabad','Sadiaqabad','Saidpur Road','sattelie town','Tipu Road','Golra Mor','I-10/1','I-11/3','I-9/1','I-9/2','I-9/2','Khayaban e Sirsyed','Margalla Town','Westridge','Bahria Town','DHA','DHA','Dist Court','lalazar','Model Town','Morgah','Naval Anchorage','sector 8','Soan Garden',








        );

        $territory_id = array(1,2,2,2,2,2,3,3,4,5,6,6,7,7,7,7,7,7,7,8,8,8,8,8,9,9,9,10,10,10,10,10,10,10,10,11,11,11,12,12,12,12,13,13,13,13,13,13,13,13,13,13,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,14,15,15,15,15,15,15,15,15,15,15,15,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,17,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,18,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,19,20,20,20,20,20,20,20,20,20,20,20,20, 21,21,21,21,21,21,21,21,21,21,21,22,22,22,22,22,22,23,23,23,23,23,23,23,23,24,24,24,24,24,24,24,24,24,25,25,25,25,25,25,25,25,25,25,25,25,26,26,26,26,26,26,26,26,26,27,27,27,27,27,27,27,27,27,27,27,27,27,27,27,27,27,27,28,28,28,







        );

        for($i=0;$i<count($areas);$i++) {

            DB::table('area_territories')->insert(array(
                array('name' => $areas[$i], 'territory_id' => $territory_id[$i],'created_at'=> $timestamp,'updated_at'=>$timestamp),
            ));
        }
    }
}
