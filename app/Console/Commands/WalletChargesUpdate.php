<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class WalletChargesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet_charges_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $data = array(
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15810949400920",
        "created_at"=> "March 5, 2025, 6:32 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15828849390197",
        "created_at"=> "March 5, 2025, 6:35 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15822349400800",
        "created_at"=> "March 5, 2025, 6:38 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20220249347845",
        "created_at"=> "March 5, 2025, 6:41 PM"
    ],
    [
        "amount"=> "20.73",
        "wallet_id"=> "9",
        "shipment_id"=> "20225149345200",
        "created_at"=> "March 6, 2025, 11:33 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20217249301898",
        "created_at"=> "March 4, 2025, 11:03 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20220249310303",
        "created_at"=> "March 4, 2025, 11:05 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20220249301975",
        "created_at"=> "March 4, 2025, 10:33 AM"
    ],
    [
        "amount"=> "23.49",
        "wallet_id"=> "2",
        "shipment_id"=> "22325749303231",
        "created_at"=> "March 3, 2025, 5:06 PM"
    ],
    [
        "amount"=> "23.49",
        "wallet_id"=> "2",
        "shipment_id"=> "22325149302459",
        "created_at"=> "March 3, 2025, 5:08 PM"
    ],
    [
        "amount"=> "382.89",
        "wallet_id"=> "8",
        "shipment_id"=> "20231549169673",
        "created_at"=> "March 3, 2025, 7:18 PM"
    ],
    [
        "amount"=> "16.65",
        "wallet_id"=> "8",
        "shipment_id"=> "20225749271929",
        "created_at"=> "March 4, 2025, 8:59 AM"
    ],
    [
        "amount"=> "23.45",
        "wallet_id"=> "8",
        "shipment_id"=> "20225749272706",
        "created_at"=> "March 4, 2025, 9:02 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "8",
        "shipment_id"=> "20294049272714",
        "created_at"=> "March 4, 2025, 9:05 AM"
    ],
    [
        "amount"=> "22.05",
        "wallet_id"=> "8",
        "shipment_id"=> "202341049271918",
        "created_at"=> "March 4, 2025, 9:09 AM"
    ],
    [
        "amount"=> "23.45",
        "wallet_id"=> "8",
        "shipment_id"=> "20226949271942",
        "created_at"=> "March 4, 2025, 9:11 AM"
    ],
    [
        "amount"=> "109.2",
        "wallet_id"=> "8",
        "shipment_id"=> "20231749273126",
        "created_at"=> "March 4, 2025, 9:15 AM"
    ],
    [
        "amount"=> "247.43",
        "wallet_id"=> "8",
        "shipment_id"=> "20227149331987",
        "created_at"=> "March 4, 2025, 9:17 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15817449309487",
        "created_at"=> "March 4, 2025, 9:21 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15816949316181",
        "created_at"=> "March 4, 2025, 9:24 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15820249316164",
        "created_at"=> "March 4, 2025, 9:26 AM"
    ],
    [
        "amount"=> "18.9",
        "wallet_id"=> "11",
        "shipment_id"=> "15820249309493",
        "created_at"=> "March 4, 2025, 9:28 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15813449309116",
        "created_at"=> "March 4, 2025, 9:31 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15828849309168",
        "created_at"=> "March 4, 2025, 9:33 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15825149316075",
        "created_at"=> "March 4, 2025, 9:35 AM"
    ],
    [
        "amount"=> "22.08",
        "wallet_id"=> "13",
        "shipment_id"=> "20222349245417",
        "created_at"=> "March 4, 2025, 9:40 AM"
    ],
    [
        "amount"=> "15.17",
        "wallet_id"=> "14",
        "shipment_id"=> "20220249283483",
        "created_at"=> "March 4, 2025, 12:03 PM"
    ],
    [
        "amount"=> "28.98",
        "wallet_id"=> "13",
        "shipment_id"=> "20220249318738",
        "created_at"=> "March 4, 2025, 12:33 PM"
    ],
    [
        "amount"=> "23.18",
        "wallet_id"=> "9",
        "shipment_id"=> "20211049226473",
        "created_at"=> "March 4, 2025, 2:03 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "9",
        "shipment_id"=> "20236649178653",
        "created_at"=> "March 4, 2025, 2:06 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "9",
        "shipment_id"=> "20222349226475",
        "created_at"=> "March 4, 2025, 2:08 PM"
    ],
    [
        "amount"=> "16.39",
        "wallet_id"=> "9",
        "shipment_id"=> "20222349181160",
        "created_at"=> "March 4, 2025, 2:11 PM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "15",
        "shipment_id"=> "26731149244031",
        "created_at"=> "March 4, 2025, 2:15 PM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "15",
        "shipment_id"=> "26722349298148",
        "created_at"=> "March 4, 2025, 2:19 PM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "15",
        "shipment_id"=> "26722349295054",
        "created_at"=> "March 4, 2025, 2:21 PM"
    ],
    [
        "amount"=> "20.7",
        "wallet_id"=> "15",
        "shipment_id"=> "26715849308558",
        "created_at"=> "March 4, 2025, 2:25 PM"
    ],
    [
        "amount"=> "30.98",
        "wallet_id"=> "7",
        "shipment_id"=> "28820249239288",
        "created_at"=> "March 4, 2025, 2:33 PM"
    ],
    [
        "amount"=> "21.63",
        "wallet_id"=> "8",
        "shipment_id"=> "20217449360216",
        "created_at"=> "March 4, 2025, 4:34 PM"
    ],
    [
        "amount"=> "23.1",
        "wallet_id"=> "8",
        "shipment_id"=> "20222449352111",
        "created_at"=> "March 4, 2025, 4:38 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15834049367111",
        "created_at"=> "March 4, 2025, 4:41 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15826749366932",
        "created_at"=> "March 4, 2025, 4:44 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15831549366966",
        "created_at"=> "March 4, 2025, 4:47 PM"
    ],
    [
        "amount"=> "20.01",
        "wallet_id"=> "12",
        "shipment_id"=> "20217249294728",
        "created_at"=> "March 4, 2025, 4:52 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "6",
        "shipment_id"=> "25131949362202",
        "created_at"=> "March 4, 2025, 5:33 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "6",
        "shipment_id"=> "25114449362760",
        "created_at"=> "March 4, 2025, 5:36 PM"
    ],
    [
        "amount"=> "23.45",
        "wallet_id"=> "8",
        "shipment_id"=> "20222649352102",
        "created_at"=> "March 4, 2025, 5:39 PM"
    ],
    [
        "amount"=> "116.71",
        "wallet_id"=> "8",
        "shipment_id"=> "20222349329545",
        "created_at"=> "March 4, 2025, 6:33 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20225149301652",
        "created_at"=> "March 5, 2025, 8:34 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "14",
        "shipment_id"=> "20222349289941",
        "created_at"=> "March 5, 2025, 8:36 AM"
    ],
    [
        "amount"=> "21.01",
        "wallet_id"=> "9",
        "shipment_id"=> "20231549290335",
        "created_at"=> "March 5, 2025, 2:03 PM"
    ],
    [
        "amount"=> "15.18",
        "wallet_id"=> "12",
        "shipment_id"=> "20226649292944",
        "created_at"=> "March 5, 2025, 2:06 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "14",
        "shipment_id"=> "20228849286806",
        "created_at"=> "March 5, 2025, 2:10 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20220249292843",
        "created_at"=> "March 5, 2025, 2:33 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20220249346478",
        "created_at"=> "March 5, 2025, 2:35 PM"
    ],
    [
        "amount"=> "24.5",
        "wallet_id"=> "7",
        "shipment_id"=> "28822349185108",
        "created_at"=> "March 5, 2025, 5:32 PM"
    ],
    [
        "amount"=> "44.1",
        "wallet_id"=> "8",
        "shipment_id"=> "20211049389769",
        "created_at"=> "March 5, 2025, 5:35 PM"
    ],
    [
        "amount"=> "22.05",
        "wallet_id"=> "8",
        "shipment_id"=> "20238249397486",
        "created_at"=> "March 5, 2025, 5:39 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "8",
        "shipment_id"=> "20222349389800",
        "created_at"=> "March 5, 2025, 5:41 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15821449398880",
        "created_at"=> "March 5, 2025, 5:44 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15822349398867",
        "created_at"=> "March 5, 2025, 5:47 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15822349400547",
        "created_at"=> "March 5, 2025, 5:49 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15828849398877",
        "created_at"=> "March 5, 2025, 5:52 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20217249392340",
        "created_at"=> "March 6, 2025, 6:33 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "14",
        "shipment_id"=> "20224449247989",
        "created_at"=> "March 6, 2025, 6:35 AM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15817449399691",
        "created_at"=> "March 5, 2025, 5:56 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15822349398897",
        "created_at"=> "March 5, 2025, 5:58 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15822349393139",
        "created_at"=> "March 5, 2025, 6:03 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15830249400781",
        "created_at"=> "March 5, 2025, 6:01 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15817749401452",
        "created_at"=> "March 5, 2025, 6:07 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15833649401459",
        "created_at"=> "March 5, 2025, 6:10 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15812249400564",
        "created_at"=> "March 5, 2025, 6:13 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15831549400798",
        "created_at"=> "March 5, 2025, 6:15 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15817249400602",
        "created_at"=> "March 5, 2025, 6:18 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15820249390172",
        "created_at"=> "March 5, 2025, 6:21 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15851049398862",
        "created_at"=> "March 5, 2025, 6:26 PM"
    ],
    [
        "amount"=> "13",
        "wallet_id"=> "11",
        "shipment_id"=> "15817449390226",
        "created_at"=> "March 5, 2025, 6:28 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20222349346864",
        "created_at"=> "March 6, 2025, 8:05 AM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "15",
        "shipment_id"=> "26717949386117",
        "created_at"=> "March 6, 2025, 8:08 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20222349348119",
        "created_at"=> "March 6, 2025, 8:34 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20234349361233",
        "created_at"=> "March 6, 2025, 8:36 AM"
    ],
    [
        "amount"=> "30.01",
        "wallet_id"=> "13",
        "shipment_id"=> "20220249385756",
        "created_at"=> "March 6, 2025, 8:38 AM"
    ],
    [
        "amount"=> "21.73",
        "wallet_id"=> "12",
        "shipment_id"=> "20225149346650",
        "created_at"=> "March 6, 2025, 9:04 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20215849346654",
        "created_at"=> "March 6, 2025, 9:06 AM"
    ],
    [
        "amount"=> "25.52",
        "wallet_id"=> "12",
        "shipment_id"=> "20217249384441",
        "created_at"=> "March 6, 2025, 9:08 AM"
    ],
    [
        "amount"=> "15.18",
        "wallet_id"=> "13",
        "shipment_id"=> "20231849346802",
        "created_at"=> "March 6, 2025, 9:11 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "20225149356448",
        "created_at"=> "March 6, 2025, 9:13 AM"
    ],
    [
        "amount"=> "15.18",
        "wallet_id"=> "12",
        "shipment_id"=> "20217449311011",
        "created_at"=> "March 6, 2025, 9:33 AM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "15",
        "shipment_id"=> "26722349391584",
        "created_at"=> "March 6, 2025, 9:36 AM"
    ],
    [
        "amount"=> "14.83",
        "wallet_id"=> "12",
        "shipment_id"=> "20225149352691",
        "created_at"=> "March 6, 2025, 11:37 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20220249387115",
        "created_at"=> "March 6, 2025, 11:40 AM"
    ],
    [
        "amount"=> "15.18",
        "wallet_id"=> "14",
        "shipment_id"=> "20220249378351",
        "created_at"=> "March 6, 2025, 11:43 AM"
    ],
    [
        "amount"=> "27.6",
        "wallet_id"=> "15",
        "shipment_id"=> "26715849385810",
        "created_at"=> "March 6, 2025, 11:46 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20215849318519",
        "created_at"=> "March 6, 2025, 12:34 PM"
    ],
    [
        "amount"=> "22.07",
        "wallet_id"=> "12",
        "shipment_id"=> "20220249384562",
        "created_at"=> "March 6, 2025, 12:36 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "18",
        "shipment_id"=> "20220249393771",
        "created_at"=> "March 6, 2025, 12:40 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "29",
        "shipment_id"=> "20217249384923",
        "created_at"=> "March 6, 2025, 1:17 PM"
    ],
    [
        "amount"=> "37.95",
        "wallet_id"=> "15",
        "shipment_id"=> "26714449317310",
        "created_at"=> "March 6, 2025, 1:46 PM"
    ],
    [
        "amount"=> "42.08",
        "wallet_id"=> "20",
        "shipment_id"=> "20220249350858",
        "created_at"=> "March 6, 2025, 1:48 PM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "13",
        "shipment_id"=> "202613349356352",
        "created_at"=> "March 6, 2025, 2:35 PM"
    ],
    [
        "amount"=> "15.18",
        "wallet_id"=> "13",
        "shipment_id"=> "20217249386535",
        "created_at"=> "March 6, 2025, 2:38 PM"
    ],
    [
        "amount"=> "23.85",
        "wallet_id"=> "22",
        "shipment_id"=> "11014449411411",
        "created_at"=> "March 6, 2025, 6:32 PM"
    ],
    [
        "amount"=> "38.98",
        "wallet_id"=> "18",
        "shipment_id"=> "20222349393754",
        "created_at"=> "March 7, 2025, 6:32 AM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "15",
        "shipment_id"=> "26722349249666",
        "created_at"=> "March 7, 2025, 7:32 AM"
    ],
    [
        "amount"=> "16.21",
        "wallet_id"=> "12",
        "shipment_id"=> "20233949295334",
        "created_at"=> "March 7, 2025, 8:02 AM"
    ],
    [
        "amount"=> "15.48",
        "wallet_id"=> "12",
        "shipment_id"=> "20222349325279",
        "created_at"=> "March 7, 2025, 8:04 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "12",
        "shipment_id"=> "20220249384133",
        "created_at"=> "March 7, 2025, 8:07 AM"
    ],
    [
        "amount"=> "14",
        "wallet_id"=> "14",
        "shipment_id"=> "20213549345033",
        "created_at"=> "March 7, 2025, 8:10 AM"
    ],
    [
        "amount"=> "24.15",
        "wallet_id"=> "29",
        "shipment_id"=> "20220249386316",
        "created_at"=> "March 7, 2025, 8:12 AM"
    ]
);

        foreach ($data as $item) {
            // Step 1: Fetch token using wallet_id
            $tokenResponse = Http::post('https://sonic.pk/api/fintech_getToken', [
                'wallet_id' => $item['wallet_id']
            ]);

            if ($tokenResponse->failed()) {
                Log::error("Failed to fetch token for wallet_id: " . $item['wallet_id']);
                continue;
            }

            $token = $tokenResponse->json()['token'] ?? null;

            if (!$token) {
                Log::error("Token missing in response for wallet_id: " . $item['wallet_id']);
                continue;
            }

            // Step 2: Call charges API with the fetched token
            $chargeResponse = Http::withHeaders([
                'Authorization' => "$token"
            ])->post('https://sonic.pk/api/fintech_charges', [
                'wallet_id' => $item['wallet_id'],
                'tracking_number' => $item['shipment_id'],
                'charges' => $item['amount']
            ]);

            if ($chargeResponse->failed()) {
                Log::error("Charge API failed for wallet_id: " . $item['wallet_id'], [
                    'status' => $chargeResponse->status(),
                    'response' => $chargeResponse->body()
                ]);
            } else {
                Log::info("Charge successful for wallet_id: " . $item['wallet_id'], [
                    'status' => $chargeResponse->status(),
                    'response' => $chargeResponse->body()
                ]);
            }
        }

    }
}
