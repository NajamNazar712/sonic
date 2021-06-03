<?php

use Illuminate\Database\Seeder;

class UpdateCitiesForHubLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('cities')->where('id',110 )->update(['hub_location_latitude' => 29.410435170723034, 'hub_location_longitude' => 71.67343862450134]);
        DB::table('cities')->where('id',134 )->update(['hub_location_latitude' => 30.061586181124063, 'hub_location_longitude' => 70.6401851686933]);
        DB::table('cities')->where('id',172 )->update(['hub_location_latitude' => 25.406326665332546, 'hub_location_longitude' => 68.3654705426428]);
        DB::table('cities')->where('id',471 )->update(['hub_location_latitude' => 24.946541854382314, 'hub_location_longitude' => 67.05008072625306]);
        DB::table('cities')->where('id',202 )->update(['hub_location_latitude' => 24.857919990193942,  'hub_location_longitude' => 67.12471271280567]);
        DB::table('cities')->where('id',243 )->update(['hub_location_latitude' => 25.529638013163563,  'hub_location_longitude' => 69.0199541109871]);
        DB::table('cities')->where('id',251 )->update(['hub_location_latitude' => 30.196725773722676, 'hub_location_longitude' => 71.4656350859155 ]);
        DB::table('cities')->where('id',257 )->update(['hub_location_latitude' => 26.238891474987867,  'hub_location_longitude' => 68.3828114975087]);
        DB::table('cities')->where('id',283 )->update(['hub_location_latitude' => 24.857700253226778, 'hub_location_longitude' =>  67.12465412698594]);
        DB::table('cities')->where('id',281 )->update(['hub_location_latitude' => 24.857700253226778,  'hub_location_longitude' => 67.12465412698594]);
        DB::table('cities')->where('id',284 )->update(['hub_location_latitude' => 28.413990218876656, 'hub_location_longitude' => 70.31869495519581]);
        DB::table('cities')->where('id',318 )->update(['hub_location_latitude' => 27.71127170939684,  'hub_location_longitude' => 68.81774441103306]);
        DB::table('cities')->where('id',501 )->update(['hub_location_latitude' => 27.71127170939684,  'hub_location_longitude' => 68.81774441103306]);
        DB::table('cities')->where('id',293 )->update(['hub_location_latitude' => 30.652716187899415,  'hub_location_longitude' => 73.11573394573925]);
        DB::table('cities')->where('id',101 )->update(['hub_location_latitude' => 34.19252983150448, 'hub_location_longitude' =>  73.23013618413798]);
        DB::table('cities')->where('id',122 )->update(['hub_location_latitude' => 32.936161149947274, 'hub_location_longitude' =>  72.85695409028109]);
        DB::table('cities')->where('id',135 )->update(['hub_location_latitude' => 31.838543472527952,  'hub_location_longitude' => 70.89745601478474]);
        DB::table('cities')->where('id',144 )->update(['hub_location_latitude' => 31.42377659031008,  'hub_location_longitude' => 73.10508326088159 ]);
        DB::table('cities')->where('id',158 )->update(['hub_location_latitude' => 32.0887097355782,  'hub_location_longitude' => 74.19692435341929]);
        DB::table('cities')->where('id',159 )->update(['hub_location_latitude' => 32.571072968766984,  'hub_location_longitude' => 74.07160649945766 ]);
        DB::table('cities')->where('id',165 )->update(['hub_location_latitude' => 34.01703054815718, 'hub_location_longitude' => 72.91225668228587]);
        DB::table('cities')->where('id',542 )->update(['hub_location_latitude' => 33.668031195808474, 'hub_location_longitude' => 73.05088166284519]);
        DB::table('cities')->where('id',174 )->update(['hub_location_latitude' => 33.668031195808474,  'hub_location_longitude' => 73.05088166284519]);
        DB::table('cities')->where('id',186 )->update(['hub_location_latitude' => 32.94255872100424,  'hub_location_longitude' => 73.7244769245873]);
        DB::table('cities')->where('id',199 )->update(['hub_location_latitude' => 33.86788056849498,  'hub_location_longitude' => 72.43570861365738 ]);
        DB::table('cities')->where('id',221 )->update(['hub_location_latitude' => 33.51567710849962,  'hub_location_longitude' => 73.90079142644953]);
        DB::table('cities')->where('id',223 )->update(['hub_location_latitude' => 31.437723200698887,  'hub_location_longitude' => 74.32139414548327]);
        DB::table('cities')->where('id',237 )->update(['hub_location_latitude' => 34.33529069419194,  'hub_location_longitude' => 73.20251006880032]);
        DB::table('cities')->where('id',255 )->update(['hub_location_latitude' => 34.34624375720906, 'hub_location_longitude' =>  73.46623078229456 ]);
        DB::table('cities')->where('id',264 )->update(['hub_location_latitude' => 34.00985202475311,  'hub_location_longitude' => 71.87544399762704 ]);
        DB::table('cities')->where('id',271 )->update(['hub_location_latitude' => 34.0467132686096,  'hub_location_longitude' => 71.5224280552988 ]);
        DB::table('cities')->where('id',504 )->update(['hub_location_latitude' => 34.0467132686096,  'hub_location_longitude' => 71.5224280552988]);
        DB::table('cities')->where('id',302 )->update(['hub_location_latitude' => 32.06864293758758,  'hub_location_longitude' => 72.69677519757732 ]);
        DB::table('cities')->where('id',315 )->update(['hub_location_latitude' => 32.501533089874634,  'hub_location_longitude' => 74.50236527060041 ]);
        DB::table('cities')->where('id',340 )->update(['hub_location_latitude' => 33.759574757352894, 'hub_location_longitude' =>  72.7555189246084 ]);
        DB::table('cities')->where('id',319 )->update(['hub_location_latitude' => 35.03747758062868,  'hub_location_longitude' => 72.23622158329121 ]);
        DB::table('cities')->where('id',304 )->update(['hub_location_latitude' => 34.12778409877913,  'hub_location_longitude' => 72.47128512594996]);

    }
}
