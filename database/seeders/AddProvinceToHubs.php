<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Http\Models\City;

class AddProvinceToHubs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinceCities = [
            1 => [ // Sindh
                'Hyderabad',
                'Hyderabad Allied',
                'Mirpur Khas',
                'Moro',
                'Nawabshah',
                'Karachi',
                'Ghotki',
                'Jacobabad',
                'Khairpur',
                'Larkana',
                'Shikarpur',
                'Sukkur',
                'Sukkur Allied',
            ],
            2 => [ // Punjab
                'Bhakkar',
                'Chiniot',
                'Dera Ismail Khan',
                'Faisalabad',
                'Faisalabad Allied',
                'Jauharabad',
                'Jhang',
                'Khurrianwala',
                'Mianwali',
                'Sargodha',
                'Toba Tek Singh',
                'Daska',
                'Gujranwala',
                'Gujrat',
                'Hafizabad',
                'Jhelum',
                'Kharian',
                'Lalamusa',
                'Mandi Bahauddin',
                'Mirpur Azad Kashmir',
                'Narowal',
                'Pasrur',
                'Sialkot',
                'Wazirabad',
                'Chakwal',
                'Fateh Jang',
                'Gujjar Khan',
                'Islamabad',
                'Kamra',
                'Wah Cantt',
                'Kasur',
                'Lahore',
                'Pattoki',
                'Raiwind',
                'Sheikhupura',
                'Bahawalnagar',
                'Bahawalpur',
                'Burewala',
                'Chichawatni',
                'Chishtian',
                'Dera Ghazi Khan',
                'Jampur',
                'Kamaliya',
                'Khanewal',
                'Layyah',
                'Multan',
                'Multan Allied',
                'Muzaffar Garh',
                'Okara',
                'Rahim Yar Khan',
                'Sahiwal',
                'Vehari',
                'Murree',
            ],
            3 => [ // Balochistan
                'Quetta',
                'Quetta Allied',
            ],
            4 => [ // Khyber Pakhtunkhwa
                'Abbottabad',
                'Gilgit',
                'Haripur',
                'Mansehra',
                'Bannu',
                'Batkhela',
                'Charsadda',
                'Kohat',
                'Mardan',
                'Nowshera',
                'Peshawar',
                'Peshawar Allied',
                'Swabi',
                'Swat',
                'Timergarah',
            ],
            5 => [ // Azad Jammu and Kashmir
                'Kotli',
                'Muzaffarabad',
                'Rawalakot',
            ],
        ];

        foreach ($provinceCities as $provinceId => $cities) {
            foreach ($cities as $cityName) {
                // Set province for the hub city itself
                $hubCity = City::where('name', $cityName)->first();

                if ($hubCity) {
                    $hubCity->province_id = $provinceId;
                    $hubCity->save();

                    // Set province for all other cities that share this hub_id
                    City::where('hub_id', $hubCity->id)
                        ->where('id', '!=', $hubCity->id)
                        ->update(['province_id' => $provinceId]);
                }
            }
        }
    }
}
