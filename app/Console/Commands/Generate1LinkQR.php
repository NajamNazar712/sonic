<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Generate1LinkQR extends Command
{
    protected $signature = 'onelink:generate-qr';
    protected $description = 'Generate a QR code via 1LINK production API';

    public function handle()
    {
        $clientId = '7e1320f3627079431658e4dfacab4056'; // 7e1320f3627079431658e4dfacab4056
        $clientSecret = 'ed02b938add93a952c326560faa6a84a';
        $base         = 'https://public-interface.1link.net.pk/onelink/production';



        // 1️⃣ Get Bearer Token
        $tokenResponse = Http::asForm()->post($base.'/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => '1LinkApi',
        ]);

        if ($tokenResponse->failed()) {
            $this->error('Failed to get token: ' . $tokenResponse->body());
            return 1;
        }

        $accessToken = $tokenResponse->json('access_token');

//        $res = Http::withToken($accessToken)
//            ->withHeaders(['X-IBM-Client-Id' => $clientId])
//            ->get("$base/1Link/getMerchantProfile", [
//                'merchantID' => '854710236963454', // IMPORTANT
//            ]);
//
//
//        $this->line(json_encode($res->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // 2️⃣ Prepare timestamps
        $executionDateTime = Carbon::now()->format('Y-m-d\TH:i:s');
        $expiryDateTime = Carbon::now()->addMinutes(40)->format('Y-m-d\TH:i:s');


        // 3️⃣ Build payload
        $payload = [
            "merchantDetails" => [
                "dbaName" => "Sonic",
                "merchantName" => "TRAX ONLINE PRIVATE LIMITED",
                "iban" => "PK15ALFH5692005002464647",
                "bankBic" => "ALFH",
                "merchantCategoryCode" => "4215",
                "merchantID" => "854710236963454",
                "postalAddress" => [
                    "townName" => "KARACHI",
                    "subDept" => "96010001",
                    "addressLine" => "Plot 105, Sector 7-A, Mehran Town, Korangi, Karachi"
                ],
                "contactDetails" => [
                    "phoneNo" => "+92-2138772222",
                    "mobileNo" => "",
                    "email" => "info@trax.pk",
                    "dept" => "Head office",
                    "website" => "www.trax.pk",
                    "merchantChannelId" => "400"
                ],
                "geoLocation" => [
                    "lat" => "24.875061",
                    "longt" => "67.038332"
                ]
            ],
            "payerDetails" => [
                "additionalRequiredDetails" => "AME",
                "identificationDetails" => [
                    "loyaltyNo" => "SOME LOYALTY NUMB",
                    "customerLabel" => "SOME CUST LABEL"
                ]
            ],
            "paymentDetails" => [
                "executionDateTime" => $executionDateTime,
                "expiryDateTime" => $expiryDateTime,
                "instructedAmount" => 1,
                "transactionType" => "064"
            ],
            "info" => [
                "stan" => "223768",
                "rrn" => "000049223768"
            ]
        ];

        // 4️⃣ Call the generateDQRCMerchant endpoint
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'X-IBM-Client-Id' => $clientId,
                'Content-Type' => 'application/json'
            ])
            ->post($base.'/1Link/generateDQRCMerchant', $payload);

        // 5️⃣ Echo response
        $this->info($response->body());

        return 0;
    }
}
