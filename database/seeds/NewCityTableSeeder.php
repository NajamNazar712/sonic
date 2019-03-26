<?php

use Illuminate\Database\Seeder;

class NewCityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('cities')->truncate();
        $ids= array(101,165,237,122,138,157,168,174,186,192,194,215,221,238,244,255,264,271,288,319,332,340,290,304,107,329,176,125,163,111,335,337,320,236,102,103,104,106,110,112,113,114,115,116,117,109,119,126,128,129,130,132,133,134,135,140,141,143,144,145,146,149,150,153,154,158,159,160,161,167,164,169,170,171,173,177,180,181,183,184,188,185,190,206,193,196,197,198,191,209,210,212,213,214,216,217,218,224,225,227,229,231,232,233,234,239,240,241,242,249,251,252,254,258,259,260,261,267,268,269,274,277,282,285,289,293,294,296,298,302,309,313,314,315,317,326,334,336,331,338,339,341,342,343,275,195,137,308,300,121,124,303,321,105,280,127,139,178,179,265,330,272,235,108,118,120,131,136,142,148,151,152,155,156,162,172,175,182,187,189,200,201,202,203,204,205,207,208,211,219,220,223,226,228,243,245,246,247,248,250,256,257,263,266,270,278,283,284,286,287,291,292,295,297,299,301,305,306,307,310,311,312,318,322,324,325,323,327,328,333,344,123,346,276,230,345,281,316,262,279,166,253,199,355,380,389,400,401,368,377,371,397,399,398,348,383,396,147,350,372,374,353,354,367,366,352,386,361,359,363,395,349,360,357,379,370,347,394,388,358,362,375,384,393,351,381,373,392,391,382,365,385,402,376,364,356,378,369,387,403,404,405,406,407,408,409,410,411,412,413,414,415,416,417,418,419,420,421,422,423,424,425,426,427,428,429,430,431,432,433,434,435,436,437,438,439,440,441,442,443,444,445,446,448,449,450,452,453,454,455,456,457,458,459,460,461,462,463,464,273,465,390,466,467,468,469,470);
        $names = array('Abbottabad','Haripur','Mansehra','Chakwal','Dina','Gujar Khan','Hasan Abdal','Islamabad','Jhelum','Kahuta','Kallar Kahar','Kohat  ','Kotli','Mardan','Mirpur Azad Kashmir ','Muzaffarabad ','Nowshera','Peshawar','Rawalpindi','Swat','Taxila','Wah Cantt','Risalpur','Swabi','Attock','Tarbela','Jehangira','Charsadda','Hangu','Bannu','Timergarah','Topi','Talagang','Mandrah','Abdul Hakim','Ahmed Pur East','Alipur ','Arifwala','Bahawalpur','Basir Pur','Basti Shorkot','Behra','Bhagtanwala','Bhakkar','Bhalwal','Bahawalnagar','Burewala','Chashma','Chicha watni','Chiniot','Chishtian','Dahranwala','Depalpur ','Dera Ghazi Khan','Dera Ismail Khan','Donga Bonga','Dunyapur','Isakhel ','Faisalabad','Faqir Wali','Farooka','Fort Abbas ','Gaggoo Mandi','Girot','Gojra','Gujranwala','Gujrat','Hadli','Hafizabad','Haroonabad','Harrapa','Hasil Pur','Haveli Lakha','Hujra Shah Muqeem','Iskanderabad','Jahanian','Jalalpur Pir Wala','Jampur','Jaranwala','Jatoi','Jhang','Jauharabad','Kabir Wala','Khairpur Tamiwali','Kala Bagh','Kamar Mushani','Kamir','Kamoke ','Kahror Pakka ','Khanewal ','Kharian','Khichi Wala','Khurrianwala','Khushab','Kot Addu','Kot Chandna','Kot Momin','Lalamusa','Lalian','Layyah','Lodhran','Luddan','Machi Wal','Mailsi','Malka Hans','Marot','Mian Channu','Mianwali','Minchin Abad','Mitro','Multan','Musafir Khana','Muzaffar Garh','Nihang','Noor Pur Thal','Noor Shah','Naushera Soon','Okara','Okara Cantt','Pakpattan','Piplan','Qaim Pur','Quaidabad','Rajan Pur','Renala Khurd','Sahiwal','Sahiwal Chota','Sakhi Sarwar','Samundri','Sargodha','Shah Pur','Shorkot Cantt','Shuja Abad','Sialkot','Sillanwali','Tarag','Tiba Sultan Pur','Toba Tek Singh','Taunsa Sharif','Uch Sharif','Vehari','Wan Bhachran','Wazirabad','Yazman','Pir Mahal','Kamaliya','Dijkot','Shah Kot','Sangla Hill','Chak Jhumra','Chenab Nagar','Satiana','Tandlianwala','Ali Pur Chatta','Qila Didar Singh','Chawinda','Dinga','Jalal Pur','Jalal Pur Jattan','Nowshera Virkan','Tatlay Aali','Pindi Bhatiyan','Mamu Kanjan','Badin','Bhiria','Barki','Dadu','Daharki','Daur','Feroza','Gambat','Ghotki','Goth Machi','Guddu','Hala','Hyderabad','Jacobabad','Jamshoro','Jetha Bhutta','Khairpur Nathan Shah','Kandh Kot','Kandiaro','Karachi','Kashmore','Kasur','Khairpur Mirs','Khan Bela','Khan Pur','Khebar','Kot Sabzal','Kot Samaba','Lahore','Larkana','Liaquat Pur','Mirpur Khas','Mirpur Mathelo','Mithi','Matiari ','Matiari Sugar Mill','Moro','Nankana Sahib','Nawabshah','Naudero','Naushahro Feroze','Pano Akil','Qazi Ahmed','Quetta ','Rahim Yar Khan','Ranipur','Rato Dero','Rohri','Sadiqabad','Sujawal','Sakrand','Sanghar','Sanjar Pur','Sehwan','Sekhat','Shahdara','Shahdad Pur','Sheikhupura','Shikarpur','Sukkur','Tando Adam','Tando Jam','Tando Muhammad Khan','Tando Allah Yar','Taranda Muhammad Panah ','Taranda Saway Khan','Thatta','Zahir Pir','Chaman','Ziarat','Pishin','Loralai','Zhob','Quetta Allied','Sibi','Nushki','Qila Abdullah','Harnai','Muslim Bagh','Kamra','Akora Khattak','Ambor','Ban Bajwa ','Basti Lar','Basti Malook','Center Plate-Muzaffarabad','Chattar','Chella Bandi ','Chowk Azam','Chowk Munda','Chowk Qureshi','Darya Khan','Daska','Fateh Pur','Fatima Fertilizer Company','Fazilpur','Garhi Dupatta','Gojra Muzaffrabad','Gondal','Hatiyan','Hattar ','Havelian','Hazro','Head Marralla ','Islamkot','Jamesabad','Jhuddo','Kacha Khuh','Kot Chutta','Kot Ghulam Muhammad','Kunri','Lower Chattar-Muzaffarabad','Lower Plate-Azad Kashmir','Makli','Mehmood Kot','Merajke','Mirwah','Nagarparkar','Naluchi','Narowal','Pahar Pur','Paigah','Pasrur','Pir Bala ','Qadirpur Rawan','Qasba Gujrat','Sambrial','Shahpur Chakar','Shakargarh','Shorkot','Tahli Mandi','Tando Jan Mohammad','Umerkot','Upper Chattar-Muzaffarabad','Upper Plate-Muzaffarabad','Zaffarwal','Bala Pir','Batkhela','Bhai Pheru','Phool Nagar','Dera Murad Jamali','Dera Allah Yar','Upper Dir','Lower Dir','Jafarabad','Khari Sharif','Khewra','Mandi Bahauddin','Mingora','Nagyal','Panjeri','Pind Dadan Khan','Bhan Syedabad','Choa Saidan Shah','Dadyal','Daluwali','Dhoria','Ghazi','Jatlan','Jund','Kaman Wala','Khalabat Township','Khanpur Mahar','Kharota Syedan','Kotli Loharan','Lakhra','Mandi','Mangla','Marakiwal','Murree','Petaro','Phalia','Sokasan','Sann','Saidan','Ugoki','Pattoki','Bagh','Rawalakot','Kotli Sattian','Qaboola','Kassowal','Iqbal Nagar','Mandi Madressa','Shabqadar ','Takht Bhai','Katlang ','Toru','Rustam','Ambar','Shewa Adda','Chota Lahore','Kabal','Saidu Sharif','Shahdadkot','Sanawan','Sarai Alamgir','Kallar Syedan','Mandrah','Rawat','Malakwal','Khairabad','Kala Shah Kaku','Kotri');
        $hubids = array(101,165,237,122,186,465,340,174,186,465,122,271,174,238,244,255,264,271,174,319,340,340,264,304,199,304,176,125,271,111,271,304,122,174,251,110,251,106,110,267,251,302,302,135,302,109,119,302,128,144,130,130,267,134,135,293,110,302,144,293,302,109,119,302,144,158,158,302,158,109,293,110,267,267,302,251,251,134,144,251,144,302,251,110,302,302,106,158,110,251,186,293,144,302,251,302,302,158,302,251,110,293,293,339,293,293,293,302,109,293,251,110,251,302,302,293,302,267,267,293,302,110,302,134,267,293,302,134,144,302,302,144,251,315,302,302,293,144,134,110,339,302,158,110,144,293,144,144,144,144,144,144,144,158,158,158,414,158,158,158,302,158,144,172,318,223,131,270,257,284,318,270,284,318,172,172,318,172,284,131,318,318,202,318,223,318,284,284,172,284,284,223,318,284,243,270,243,172,172,318,144,257,318,318,270,257,283,284,318,318,318,284,333,257,257,284,131,172,223,172,223,318,318,172,172,172,172,284,284,333,284,281,281,281,281,281,281,281,281,281,281,281,199,176,255,315,251,251,255,255,255,251,251,251,135,315,251,284,134,255,255,199,199,165,165,199,315,243,243,243,251,134,243,243,255,255,333,251,315,243,243,255,315,251,134,315,255,251,251,315,257,315,144,255,243,243,255,255,315,255,271,223,223,318,318,271,271,318,244,414,414,319,244,244,414,131,122,465,315,186,165,244,186,315,165,270,315,315,172,186,244,315,174,172,414,244,131,315,315,223,174,174,174,106,128,128,130,125,238,238,238,238,304,304,304,319,176,318,251,186,465,465,465,414,176,223,172);
        $hub = array(1,1,1,1,0,0,0,1,1,0,0,0,0,1,1,1,1,1,0,1,0,1,0,1,0,0,1,1,0,1,0,0,0,0,0,0,0,1,1,0,0,0,0,0,0,1,1,0,1,0,1,0,0,1,1,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,1,0,0,1,0,0,0,0,0,0,1,0,0,1,0,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0);
        $pickup = array(1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        $status = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);

        for($i=0;$i<count($ids);$i++) {

            DB::table('cities')->insert(array(
                array('id' => $ids[$i], 'name' => $names[$i], 'hub' => $hub[$i], 'hub_id' => $hubids[$i], 'pickup' => $pickup[$i], 'status' => $status[$i]),
            ));
        }

        DB::table('city_deliveries')->truncate();
        DB::table('city_deliveries')->insert(array(
            array('city_id'=>174,'booking_type_id'=>1,'shipping_mode_id'=>4),
            array('city_id'=>202,'booking_type_id'=>1,'shipping_mode_id'=>4),
            array('city_id'=>223,'booking_type_id'=>1,'shipping_mode_id'=>4),
            array('city_id'=>174,'booking_type_id'=>3,'shipping_mode_id'=>1),
            array('city_id'=>288,'booking_type_id'=>3,'shipping_mode_id'=>1),
            array('city_id'=>202,'booking_type_id'=>3,'shipping_mode_id'=>1),
            array('city_id'=>223,'booking_type_id'=>3,'shipping_mode_id'=>1),

        ));

        for($i=0;$i<count($ids);$i++) {

            DB::table('city_deliveries')->insert(array(
                array('city_id' => $ids[$i], 'booking_type_id' => 2, 'shipping_mode_id' => 1),
            ));
        }

        for($i=0;$i<count($ids);$i++) {

            DB::table('city_deliveries')->insert(array(
                array('city_id' => $ids[$i], 'booking_type_id' => 1, 'shipping_mode_id' => 1),
            ));
        }

    }
}
