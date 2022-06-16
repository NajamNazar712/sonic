<?php

use Illuminate\Database\Seeder;

class UpdateSelfCollectionCitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('self_collection_cities')->insert(array(
            array('id' => 1, 'city_id' => 202, 'address'=> ''),
            array('id' => 2, 'city_id' => 223, 'address'=> ''),
            array('id' => 3, 'city_id' => 144, 'address'=> 'P-499, Hasnain Market, Near Mughal Paint Store, Abdullah Pur, Faisalabad.'),
            array('id' => 4, 'city_id' => 158, 'address'=> 'Model town house # 1 block A-1 near Jinnah hospital mukarram plaza Gujranwala'),
            array('id' => 5, 'city_id' => 172, 'address'=> 'Plot no 329, street no 7, pathan colony, near OPD chare, hyderabad'),
            array('id' => 6, 'city_id' => 283, 'address'=> 'Spini road, near arbab cng pump, opposite ZK motors, Quetta'),
            array('id' => 7, 'city_id' => 318, 'address'=> 'Bungalow No. E-409, Sector 4, Township, Sukkur.'),
            array('id' => 8, 'city_id' => 188, 'address'=> 'Bhakkar Road, Opposite MAIN Daewoo Terminal, Jhang.'),
            array('id' => 9, 'city_id' => 215, 'address'=> 'KDA Gate No. 2, Al Manzoor Plaza, Room No. 5, Kohat.'),
            array('id' => 10, 'city_id' => 243, 'address'=> 'Gharibabad, opp agra taj hotel, near rehman madical, mir pur khas'),
            array('id' => 11, 'city_id' => 250, 'address'=> 'Shop no:5 near Bismillah qalandri hotel, main national highway road. Nausharo feroze Sindh-Pakistan.'),
            array('id' => 12, 'city_id' => 257, 'address'=> 'Manwabad Near Wapda office nawabshah'),
            array('id' => 13, 'city_id' => 226, 'address'=> 'Lahore Colony, Street No. 03, New Bus Stand, Larkana.'),
            array('id' => 14, 'city_id' => 319, 'address'=> 'muslim mobile market opp spinzar plaza suhrab khan chowk mingora swat'),
            array('id' => 15, 'city_id' => 304, 'address'=> 'mardan road opp meezan bank swabi khas'),
            array('id' => 16, 'city_id' => 271, 'address'=> 'Sardar garhi stop, main GT road, near ICMS COLLEGE, PIMS COLLEGE, Peshawar'),
            array('id' => 17, 'city_id' => 264, 'address'=> 'Main Gt Road Amjid Khan Market Opposite to Pirpiai Azakhel Dry Port.'),
            array('id' => 18, 'city_id' => 238, 'address'=> 'bypass road near city school system mardan'),
            array('id' => 19, 'city_id' => 237, 'address'=> 'Near city thana main bypass road, mansehra.'),
            array('id' => 20, 'city_id' => 199, 'address'=> 'Shop #30, Mini Plaza, Qutba More, Cantonament Board Markit, GT Road Kamra Cantt'),
            array('id' => 21, 'city_id' => 165, 'address'=> 'TCS Office, Sector No. 01 , Khalabhat Township, Haripur.'),
            array('id' => 22, 'city_id' => 101, 'address'=> 'House num 52 al qaum house , Street num 11,near usamania restaurant mandiyan kaghan colony, Abbotabad.'),
            array('id' => 23, 'city_id' => 340, 'address'=> 'House no CB -10 street no 21 new city near shell petrol pump wah cantt'),
            array('id' => 24, 'city_id' => 445, 'address'=> 'offc no 11 opp mcb bank baldya adda branch Rawalakot ajk'),
            array('id' => 25, 'city_id' => 255, 'address'=> 'Bank square, chattar market, near zahid ameen hote,l MUZAFFARABAD'),
            array('id' => 26, 'city_id' => 244, 'address'=> '"Police Line, Market Opposite HBL Main Branch, MirPur Azad Kashmir'),
            array('id' => 27, 'city_id' => 221, 'address'=> 'Pak kashmir link cargo service near dreamland hotel housing scheme road shop no 4 kotli'),
            array('id' => 28, 'city_id' => 186, 'address'=> 'Office near Globel Medical Store Muhammadi Chowk JHELUM'),
            array('id' => 29, 'city_id' => 174, 'address'=> 'Plot no 91, street no 7, sector no I 10 / 3, Islamabad'),
            array('id' => 30, 'city_id' => 159, 'address'=> 'Shaheen chowk opposite shaheen police station sargodha road gujrat'),
            array('id' => 31, 'city_id' => 122, 'address'=> 'Tehsil Chowk, Near Bank Alfalah, Raja Akbar Plaza, Basement Shop, Pindi Road, Chakwal.'),
            array('id' => 32, 'city_id' => 293, 'address'=> 'Plot # 04 Abu Bakar Block, Shadab Town, Near Baithak Café, Opp. Bin Fazal Brost- SAHIWAL'),
            array('id' => 33, 'city_id' => 284, 'address'=> 'Thalli Chowk ,by Pass Road ,Opp Tehkam Floor Mill, Near Surgical center, Rahim Yar Khan'),
            array('id' => 34, 'city_id' => 251, 'address'=> 'Chowk Kumharan Wala, Near Daewood Terminal Office, Opposite Orient Mall, Near Bata Shop, Multan'),
            array('id' => 35, 'city_id' => 135, 'address'=> 'Office #62, Ashiyana Shopping Center, North Circular Road, Dera Ismail Khan'),
            array('id' => 36, 'city_id' => 134, 'address'=> 'Block X, Near Police Station Division, Dera Ghazi Khan.'),
            array('id' => 37, 'city_id' => 116, 'address'=> 'Mandi Town Bhakkar Peyala Chowk Bahal Road Near ali hospitel , Nice lock Hair salon ki back sid pr'),
            array('id' => 38, 'city_id' => 110, 'address'=> 'Shop No. 64, Bindra Pulli, Multan Road, Bahawalpur.'),
            array('id' => 39, 'city_id' => 109, 'address'=> 'Near Bahawali Adda & Pardise Hotel, Chishtian Road, Bahawalnagar'),
            array('id' => 40, 'city_id' => 336, 'address'=> 'Back site of larri adda, near edhi welfare center, civil lines, toba tek singh'),
            array('id' => 41, 'city_id' => 315, 'address'=> 'Habib mall near passport office oppo G1 hotel kashmir road sialkot'),
            array('id' => 42, 'city_id' => 302, 'address'=> 'House #58, Phase 2,khayaban e Asad Opposite Ghousia Service Station Near Bismillah Homes, 47 Pull, Sargodha'),
            array('id' => 43, 'city_id' => 384, 'address'=> 'Post Office Saddowala Uncha, Near Bright Future Islamia High School, Village Totay Wali, Zafarwal Road, Narowal'),
            array('id' => 44, 'city_id' => 241, 'address'=> 'Shop No. 01, Committee Chowk, Near Underpass, Balloo Khel Road, Mianwali'),
            array('id' => 45, 'city_id' => 204, 'address'=> 'Chowk Bhatta, Goraye Wala College Road, Opposite Jamia Masjid, Hazure Kasur, Kasur.'),
            array('id' => 46, 'city_id' => 383, 'address'=> 'model town, khan hotal wali gali, opp balay balay dahi balay shop, daska'),
            array('id' => 47, 'city_id' => 129, 'address'=> 'Najaf Street, Muhallah Thathi Sharqi , City Thana Road , Chiniot.'),
            array('id' => 48, 'city_id' => 210, 'address'=> '1st floor dinga road al shams bakers Kharian'),
            array('id' => 49, 'city_id' => 157, 'address'=> 'Main GT road near daewoo express opp pearl In hotel Okara'),
            array('id' => 50, 'city_id' => 267, 'address'=> 'Main GT road near daewoo express opp pearl In hotel Okara'),
            array('id' => 51, 'city_id' => 339, 'address'=> 'club road next to pakeza revari vehari'),
            array('id' => 52, 'city_id' => 108, 'address'=> 'Hyderabad road , Naseem city center(trax office inside) near united hotel badin.'),
            array('id' => 53, 'city_id' => 167, 'address'=> 'trax Office , Opposite , Main Jamia masjid, Bahawalnagar'),
            array('id' => 54, 'city_id' => 443, 'address'=> 'Faisal colony main azar tailor street near akhwat bank pattoki'),
            array('id' => 55, 'city_id' => 213, 'address'=> 'Faisalabad road, leopard office, opposite mcb bank Khurrianwala'),
            array('id' => 56, 'city_id' => 227, 'address'=> 'Fawara chowk near modern mobile market opposite 15 office Layyah'),
        ));
    }
}
