<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\Shipment;
use App\ShipmentAdditionalCharges;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
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

            $shipmentsData2 = [
                ['tracking_number' => 22322350761010, 'charges' => 14],
                ['tracking_number' => 22322350756257, 'charges' => 26.57],
                ['tracking_number' => 22322350767912, 'charges' => 43.46],
                ['tracking_number' => 26714450761219, 'charges' => 24.15],
                ['tracking_number' => 26714450771978, 'charges' => 48.3],
                ['tracking_number' => 14414450773004, 'charges' => 14],
                ['tracking_number' => 14414450773000, 'charges' => 17.18],
                ['tracking_number' => 14414450773067, 'charges' => 17.18],
                ['tracking_number' => 14414450773030, 'charges' => 17.18],
                ['tracking_number' => 22314450778396, 'charges' => 14],
                ['tracking_number' => 22314450778621, 'charges' => 34.22],
                ['tracking_number' => 38314450770083, 'charges' => 14],
                ['tracking_number' => 14422350779629, 'charges' => 14],
                ['tracking_number' => 14419550747386, 'charges' => 21],
                ['tracking_number' => 20216550720704, 'charges' => 14],
                ['tracking_number' => 22322350789313, 'charges' => 36.23],
                ['tracking_number' => 22322350765058, 'charges' => 43.82],
                ['tracking_number' => 22322350766593, 'charges' => 46.92],
                ['tracking_number' => 22322350823806, 'charges' => 28.29],
                ['tracking_number' => 22322350775946, 'charges' => 25.49],
                ['tracking_number' => 22322350800752, 'charges' => 33.74],
                ['tracking_number' => 22322350824000, 'charges' => 14],
                ['tracking_number' => 20220250817861, 'charges' => 15.18],
                ['tracking_number' => 17422350819263, 'charges' => 14],
                ['tracking_number' => 20217450767882, 'charges' => 14],
                ['tracking_number' => 20217450766274, 'charges' => 14],
                ['tracking_number' => 20222350764892, 'charges' => 14.15],
                ['tracking_number' => 20215950766735, 'charges' => 15.53],
                ['tracking_number' => 20217450742721, 'charges' => 34.16],
                ['tracking_number' => 20228850721320, 'charges' => 14],
                ['tracking_number' => 20217450773212, 'charges' => 16.56],
                ['tracking_number' => 14422350793886, 'charges' => 15.17],
                ['tracking_number' => 20217450686118, 'charges' => 60.72],
                ['tracking_number' => 15822350817097, 'charges' => 13],
                ['tracking_number' => 15818650817096, 'charges' => 13],
                ['tracking_number' => 15829850839639, 'charges' => 13],
                ['tracking_number' => 15829850817082, 'charges' => 13],
                ['tracking_number' => 15834050840292, 'charges' => 13],
                ['tracking_number' => 15824050817352, 'charges' => 13],
                ['tracking_number' => 15827150839626, 'charges' => 13],
                ['tracking_number' => 15820250817983, 'charges' => 17.15],
                ['tracking_number' => 15831850817088, 'charges' => 17.15],
                ['tracking_number' => 15834050839635, 'charges' => 17.15],
                ['tracking_number' => 15827150817120, 'charges' => 17.15],
                ['tracking_number' => 158604250840295, 'charges' => 17.15],
                ['tracking_number' => 15828850840288, 'charges' => 17.15],
                ['tracking_number' => 15840250839622, 'charges' => 17.15],
                ['tracking_number' => 15815850817346, 'charges' => 17.15],
                ['tracking_number' => 15828350839652, 'charges' => 17.15],
                ['tracking_number' => 15820250839644, 'charges' => 17.15],
                ['tracking_number' => 15814450839648, 'charges' => 17.15],
                ['tracking_number' => 15822350839653, 'charges' => 17.15],
                ['tracking_number' => 15828850839646, 'charges' => 19.25],
                ['tracking_number' => 15822350840286, 'charges' => 24.15],
                ['tracking_number' => 15816550839643, 'charges' => 25.55],
                ['tracking_number' => 20238250768530, 'charges' => 14],
                ['tracking_number' => 22322350844999, 'charges' => 14],
                ['tracking_number' => 22322350845365, 'charges' => 14],
                ['tracking_number' => 14420250771892, 'charges' => 14],
                ['tracking_number' => 14428850823731, 'charges' => 49.67],
                ['tracking_number' => 20222350802761, 'charges' => 30.36],
                ['tracking_number' => 22318650842117, 'charges' => 14],
                ['tracking_number' => 21320250761431, 'charges' => 24.83],
                ['tracking_number' => 21328850821406, 'charges' => 29.49],
                ['tracking_number' => 38317450799681, 'charges' => 14],
                ['tracking_number' => 20222350807097, 'charges' => 14],
                ['tracking_number' => 14431150842231, 'charges' => 20],
                ['tracking_number' => 22317450843958, 'charges' => 34.09],
                ['tracking_number' => 20222350812727, 'charges' => 14],
                ['tracking_number' => 20214450787336, 'charges' => 14],
                ['tracking_number' => 20214450802707, 'charges' => 14],
                ['tracking_number' => 20217250833297, 'charges' => 14],
                ['tracking_number' => 20215950771066, 'charges' => 14],
                ['tracking_number' => 20222350802770, 'charges' => 15.17],
                ['tracking_number' => 20220250850549, 'charges' => 15.17],
                ['tracking_number' => 20228850804491, 'charges' => 15.56],
                ['tracking_number' => 20217450798264, 'charges' => 18.62],
                ['tracking_number' => 20222350763942, 'charges' => 20.7],
                ['tracking_number' => 22322350845362, 'charges' => 27.95],
                ['tracking_number' => 14410650827138, 'charges' => 14],
                ['tracking_number' => 20226750779477, 'charges' => 84.87],
                ['tracking_number' => 22322350850787, 'charges' => 15.15],
                ['tracking_number' => 22322350845370, 'charges' => 14],
                ['tracking_number' => 14432250748860, 'charges' => 14],
                ['tracking_number' => 14416550809954, 'charges' => 14],
                ['tracking_number' => 20216550775375, 'charges' => 14],
                ['tracking_number' => 22310150849691, 'charges' => 26.22],
                ['tracking_number' => 22322350848059, 'charges' => 14],
                ['tracking_number' => 22322350861867, 'charges' => 14],
                ['tracking_number' => 22322350881459, 'charges' => 14],
                ['tracking_number' => 22325150919637, 'charges' => 15],
                ['tracking_number' => 20215850736407, 'charges' => 115.92],
                ['tracking_number' => 25131550891808, 'charges' => 17.25],
                ['tracking_number' => 25115850891720, 'charges' => 17.25],
                ['tracking_number' => 20220250881255, 'charges' => 14.83],
                ['tracking_number' => 20220250882413, 'charges' => 24.15],
                ['tracking_number' => 11921550759411, 'charges' => 14],
                ['tracking_number' => 11925550760063, 'charges' => 21.22],
                ['tracking_number' => 144287650856927, 'charges' => 14],
                ['tracking_number' => 14422350893101, 'charges' => 15.48],
                ['tracking_number' => 14414450891823, 'charges' => 24.47],
                ['tracking_number' => 14422350894011, 'charges' => 28.98],
                ['tracking_number' => 14422350894024, 'charges' => 39.33],
                ['tracking_number' => 25115850872186, 'charges' => 20.7],
                ['tracking_number' => 25127150808993, 'charges' => 37.95],
                ['tracking_number' => 25128850830505, 'charges' => 37.95],
                ['tracking_number' => 25128850779977, 'charges' => 41.4],
                ['tracking_number' => 25117450809018, 'charges' => 41.4],
                ['tracking_number' => 25131550808984, 'charges' => 41.4],
                ['tracking_number' => 25127150754851, 'charges' => 41.4],
                ['tracking_number' => 25117450853707, 'charges' => 41.4],
                ['tracking_number' => 25122350853696, 'charges' => 41.4],
                ['tracking_number' => 25127150853708, 'charges' => 41.4],
                ['tracking_number' => 25128850853670, 'charges' => 41.4],
                ['tracking_number' => 25122350872220, 'charges' => 41.4],
                ['tracking_number' => 25127150872188, 'charges' => 41.4],
                ['tracking_number' => 25127150872219, 'charges' => 41.4],
                ['tracking_number' => 25127150780018, 'charges' => 44.85],
                ['tracking_number' => 25128450779943, 'charges' => 44.85],
                ['tracking_number' => 25127150780021, 'charges' => 44.85],
                ['tracking_number' => 25117450779956, 'charges' => 44.85],
                ['tracking_number' => 25127150808987, 'charges' => 44.85],
                ['tracking_number' => 25127150809020, 'charges' => 44.85],
                ['tracking_number' => 25128450809013, 'charges' => 44.85],
                ['tracking_number' => 25128850808964, 'charges' => 44.85],
                ['tracking_number' => 25117450809037, 'charges' => 44.85],
                ['tracking_number' => 25127150809021, 'charges' => 44.85],
                ['tracking_number' => 25127150724336, 'charges' => 44.85],
                ['tracking_number' => 25122350724367, 'charges' => 44.85],
                ['tracking_number' => 25117450830497, 'charges' => 44.85],
                ['tracking_number' => 25122350830512, 'charges' => 44.85],
                ['tracking_number' => 25125150830518, 'charges' => 44.85],
                ['tracking_number' => 25127150830446, 'charges' => 44.85],
                ['tracking_number' => 25127150830473, 'charges' => 44.85],
                ['tracking_number' => 25127150830502, 'charges' => 44.85],
                ['tracking_number' => 25127150830509, 'charges' => 44.85],
                ['tracking_number' => 25128850830483, 'charges' => 44.85],
                ['tracking_number' => 25127150746657, 'charges' => 44.85],
                ['tracking_number' => 25117450853652, 'charges' => 44.85],
                ['tracking_number' => 25117450853668, 'charges' => 44.85],
                ['tracking_number' => 25117450853688, 'charges' => 44.85],
                ['tracking_number' => 25122350853681, 'charges' => 44.85],
                ['tracking_number' => 25127150853640, 'charges' => 44.85],
                ['tracking_number' => 25127150853656, 'charges' => 44.85],
                ['tracking_number' => 25127150853664, 'charges' => 44.85],
                ['tracking_number' => 25127150853665, 'charges' => 44.85],
                ['tracking_number' => 25127150853669, 'charges' => 44.85],
                ['tracking_number' => 25127150853687, 'charges' => 44.85],
                ['tracking_number' => 25127150853697, 'charges' => 44.85],
                ['tracking_number' => 25129250853655, 'charges' => 44.85],
                ['tracking_number' => 25113450872191, 'charges' => 44.85],
                ['tracking_number' => 25115950872148, 'charges' => 44.85],
                ['tracking_number' => 25115950872208, 'charges' => 44.85],
                ['tracking_number' => 25122350872147, 'charges' => 44.85],
                ['tracking_number' => 25122350872154, 'charges' => 44.85],
                ['tracking_number' => 25122350872175, 'charges' => 44.85],
                ['tracking_number' => 25122350872181, 'charges' => 44.85],
                ['tracking_number' => 25122350872187, 'charges' => 44.85],
                ['tracking_number' => 25122350872189, 'charges' => 44.85],
                ['tracking_number' => 25122350872193, 'charges' => 44.85],
                ['tracking_number' => 25122350872194, 'charges' => 44.85],
                ['tracking_number' => 25122350872196, 'charges' => 44.85],
                ['tracking_number' => 25122350872197, 'charges' => 44.85],
                ['tracking_number' => 25122350872198, 'charges' => 44.85],
                ['tracking_number' => 25125150872160, 'charges' => 44.85],
                ['tracking_number' => 25125150872216, 'charges' => 44.85],
                ['tracking_number' => 25125150872221, 'charges' => 44.85],
                ['tracking_number' => 25127150872171, 'charges' => 44.85],
                ['tracking_number' => 25127150872173, 'charges' => 44.85],
                ['tracking_number' => 25127150872192, 'charges' => 44.85],
                ['tracking_number' => 25127150872209, 'charges' => 44.85],
                ['tracking_number' => 25131550872149, 'charges' => 44.85],
                ['tracking_number' => 25128850674510, 'charges' => 44.85],
                ['tracking_number' => 25128850746672, 'charges' => 44.85],
                ['tracking_number' => 25122350920176, 'charges' => 14],
                ['tracking_number' => 25122350919956, 'charges' => 18.62],
                ['tracking_number' => 20222350818315, 'charges' => 14],
                ['tracking_number' => 20241550793692, 'charges' => 15.18],
                ['tracking_number' => 20217450820024, 'charges' => 15.18],
                ['tracking_number' => 26714450910387, 'charges' => 24.15],
                ['tracking_number' => 26722350915588, 'charges' => 24.15],
                ['tracking_number' => 26722350910153, 'charges' => 24.15],
                ['tracking_number' => 26722350913524, 'charges' => 24.15],
                ['tracking_number' => 26722350910226, 'charges' => 24.15],
                ['tracking_number' => 26730250914072, 'charges' => 24.15],
                ['tracking_number' => 26717450913902, 'charges' => 24.15],
                ['tracking_number' => 26725150911932, 'charges' => 24.15],
                ['tracking_number' => 26722350917575, 'charges' => 24.15],
                ['tracking_number' => 26722350911213, 'charges' => 24.15],
                ['tracking_number' => 26714450910375, 'charges' => 27.6],
                ['tracking_number' => 26731150910660, 'charges' => 37.95],
                ['tracking_number' => 26712950802333, 'charges' => 58.65],
                ['tracking_number' => 26717450916467, 'charges' => 58.65],
                ['tracking_number' => 26715950915500, 'charges' => 345],
                ['tracking_number' => 22322350904841, 'charges' => 14],
                ['tracking_number' => 22322350905069, 'charges' => 14],
                ['tracking_number' => 22322350906543, 'charges' => 14],
                ['tracking_number' => 22322350905235, 'charges' => 14],
                ['tracking_number' => 22322350904952, 'charges' => 14],
                ['tracking_number' => 22322350904983, 'charges' => 14],
                ['tracking_number' => 22325150905081, 'charges' => 14],
                ['tracking_number' => 22322350917490, 'charges' => 14],
                ['tracking_number' => 22313350917503, 'charges' => 14],
                ['tracking_number' => 223611650844358, 'charges' => 14],
                ['tracking_number' => 14422350907316, 'charges' => 14],
                ['tracking_number' => 14422350906353, 'charges' => 14],
                ['tracking_number' => 14415950895602, 'charges' => 14],
                ['tracking_number' => 14428350763510, 'charges' => 14],
                ['tracking_number' => 14422350899856, 'charges' => 14],
                ['tracking_number' => 14430250910281, 'charges' => 14],
                ['tracking_number' => 14417450867466, 'charges' => 14],
                ['tracking_number' => 14425150915094, 'charges' => 15.86],
                ['tracking_number' => 14422350915048, 'charges' => 22.07],
                ['tracking_number' => 14422350915122, 'charges' => 40],
                ['tracking_number' => 14422350915046, 'charges' => 41.39],
                ['tracking_number' => 25122350893552, 'charges' => 14],
                ['tracking_number' => 25122350893167, 'charges' => 14],
                ['tracking_number' => 25144550741733, 'charges' => 14],
                ['tracking_number' => 25122350870040, 'charges' => 14],
                ['tracking_number' => 25115850818479, 'charges' => 18.63],
                ['tracking_number' => 25122350818339, 'charges' => 18.63],
                ['tracking_number' => 20225050626547, 'charges' => 24.84],
                ['tracking_number' => 27114450879569, 'charges' => 44.85],
                ['tracking_number' => 14422350894802, 'charges' => 29.66],
                ['tracking_number' => 14415950896496, 'charges' => 29.66],
                ['tracking_number' => 14422350894816, 'charges' => 65.58],
                ['tracking_number' => 14422350894935, 'charges' => 96.59],
                ['tracking_number' => 14422350879912, 'charges' => 17.18],
                ['tracking_number' => 14414450910822, 'charges' => 17.18],
                ['tracking_number' => 14414450910823, 'charges' => 17.18],
                ['tracking_number' => 14414450910790, 'charges' => 17.18],
                ['tracking_number' => 14414450910780, 'charges' => 17.18],
                ['tracking_number' => 14414450910805, 'charges' => 17.18],
                ['tracking_number' => 14414450910792, 'charges' => 20.63],
                ['tracking_number' => 14414450910828, 'charges' => 20.7],
                ['tracking_number' => 14414450910819, 'charges' => 27.81],
                ['tracking_number' => 14422350912990, 'charges' => 58.62],
                ['tracking_number' => 14413350909297, 'charges' => 23.44],
                ['tracking_number' => 26726950914721, 'charges' => 14],
                ['tracking_number' => 14422350916262, 'charges' => 29.66],
                ['tracking_number' => 14414450902762, 'charges' => 65.54],
                ['tracking_number' => 14417450902141, 'charges' => 65.54],
                ['tracking_number' => 14422350904181, 'charges' => 73.13],
                ['tracking_number' => 14422350922200, 'charges' => 72.45],
                ['tracking_number' => 14422350894186, 'charges' => 18.63],
                ['tracking_number' => 14422350899018, 'charges' => 19.32],
                ['tracking_number' => 14422350911608, 'charges' => 21.05],
                ['tracking_number' => 14422350901526, 'charges' => 18.96],
                ['tracking_number' => 14415450879260, 'charges' => 25.52],
                ['tracking_number' => 14420450865257, 'charges' => 17.04],
                ['tracking_number' => 14422350916849, 'charges' => 19.31],
                ['tracking_number' => 14422350908971, 'charges' => 20.69],
                ['tracking_number' => 14422350916825, 'charges' => 20.69],
                ['tracking_number' => 14422350908958, 'charges' => 20.69],
                ['tracking_number' => 14422350908624, 'charges' => 33.11],
                ['tracking_number' => 25122350895440, 'charges' => 14],
                ['tracking_number' => 25132150886464, 'charges' => 14],
                ['tracking_number' => 25122350895507, 'charges' => 14],
                ['tracking_number' => 25122350907197, 'charges' => 14],
                ['tracking_number' => 25131150895357, 'charges' => 14],
                ['tracking_number' => 25131150907530, 'charges' => 14],
                ['tracking_number' => 25133650726148, 'charges' => 14],
                ['tracking_number' => 17422350889023, 'charges' => 14],
                ['tracking_number' => 17417450887411, 'charges' => 14],
                ['tracking_number' => 22322350908375, 'charges' => 41.99],
                ['tracking_number' => 14422350902964, 'charges' => 14],
                ['tracking_number' => 14422350902772, 'charges' => 16.22],
                ['tracking_number' => 14422350902943, 'charges' => 22.41],
                ['tracking_number' => 202672450747900, 'charges' => 27.25],
                ['tracking_number' => 22325550823820, 'charges' => 76.59],
                ['tracking_number' => 14422350887187, 'charges' => 22.07],
                ['tracking_number' => 14422350906115, 'charges' => 22.07],
                ['tracking_number' => 14422350906107, 'charges' => 22.07],
                ['tracking_number' => 22315850920694, 'charges' => 27.6],
                ['tracking_number' => 20235250873936, 'charges' => 14],
                ['tracking_number' => 20215950842637, 'charges' => 14],
                ['tracking_number' => 20210750836787, 'charges' => 14],
                ['tracking_number' => 20220250904118, 'charges' => 14],
                ['tracking_number' => 20235250775380, 'charges' => 14],
                ['tracking_number' => 20226450764413, 'charges' => 14],
                ['tracking_number' => 20212450833310, 'charges' => 14],
                ['tracking_number' => 20235250867316, 'charges' => 14],
                ['tracking_number' => 20215450848869, 'charges' => 14],
                ['tracking_number' => 20214450857573, 'charges' => 14.76],
                ['tracking_number' => 20222350850002, 'charges' => 15.17],
                ['tracking_number' => 20220250913978, 'charges' => 15.17],
                ['tracking_number' => 20217450827115, 'charges' => 18.62],
                ['tracking_number' => 20232150852234, 'charges' => 18.62],
                ['tracking_number' => 20227150818130, 'charges' => 121.79],
                ['tracking_number' => 21322350909989, 'charges' => 23.28],
                ['tracking_number' => 21322350909938, 'charges' => 24.83],
                ['tracking_number' => 14422350920484, 'charges' => 26.14],
                ['tracking_number' => 14430750922099, 'charges' => 27.52],
                ['tracking_number' => 14422350922103, 'charges' => 69.61],
                ['tracking_number' => 22322350919346, 'charges' => 18.46],
                ['tracking_number' => 22322350919352, 'charges' => 22.8],
                ['tracking_number' => 22314450917179, 'charges' => 25.91],
                ['tracking_number' => 25112550858676, 'charges' => 17.11],
                ['tracking_number' => 25122350894927, 'charges' => 17.11],
                ['tracking_number' => 25112550814338, 'charges' => 17.24],
                ['tracking_number' => 25122350895190, 'charges' => 17.24],
                ['tracking_number' => 25150550895166, 'charges' => 17.24],
                ['tracking_number' => 25112550814156, 'charges' => 20.7],
                ['tracking_number' => 25141550878764, 'charges' => 20.7],
                ['tracking_number' => 25115850895001, 'charges' => 20.7],
                ['tracking_number' => 25122350894968, 'charges' => 20.7],
                ['tracking_number' => 25114450878486, 'charges' => 27.59],
                ['tracking_number' => 25122350920243, 'charges' => 28.97],
                ['tracking_number' => 25122350878540, 'charges' => 34.49],
                ['tracking_number' => 20222350862880, 'charges' => 16.2],
                ['tracking_number' => 14422350899182, 'charges' => 14],
                ['tracking_number' => 14424450858671, 'charges' => 14],
                ['tracking_number' => 14415950899303, 'charges' => 14],
                ['tracking_number' => 14422350899255, 'charges' => 14],
                ['tracking_number' => 14417450899260, 'charges' => 16.55],
                ['tracking_number' => 14422350899253, 'charges' => 28.97],
                ['tracking_number' => 22315850913218, 'charges' => 34.58],
                ['tracking_number' => 15922350897738, 'charges' => 14],
                ['tracking_number' => 15922350909245, 'charges' => 14],
                ['tracking_number' => 15915850897892, 'charges' => 24.15],
                ['tracking_number' => 15951250909443, 'charges' => 39.68],
                ['tracking_number' => 22322350907194, 'charges' => 14.1],
                ['tracking_number' => 22322350907041, 'charges' => 34.11],
                ['tracking_number' => 25122350895848, 'charges' => 14],
                ['tracking_number' => 20225550790937, 'charges' => 18.63],
                ['tracking_number' => 20218850855637, 'charges' => 30.36],
                ['tracking_number' => 20225550834489, 'charges' => 35.88],
                ['tracking_number' => 20222350790178, 'charges' => 46.23],
                ['tracking_number' => 20217450856389, 'charges' => 46.23],
                ['tracking_number' => 22330250911594, 'charges' => 14],
                ['tracking_number' => 22320450905632, 'charges' => 14],
                ['tracking_number' => 22322350905613, 'charges' => 14],
                ['tracking_number' => 22338350905122, 'charges' => 14],
                ['tracking_number' => 20227150826242, 'charges' => 14],
                ['tracking_number' => 20235250826262, 'charges' => 14],
                ['tracking_number' => 14416150753204, 'charges' => 14],
                ['tracking_number' => 144672450835504, 'charges' => 14],
                ['tracking_number' => 14426450826351, 'charges' => 14],
                ['tracking_number' => 14412550847677, 'charges' => 14],
                ['tracking_number' => 14419950855764, 'charges' => 14],
                ['tracking_number' => 14410750854936, 'charges' => 14],
                ['tracking_number' => 14410750849673, 'charges' => 14],
                ['tracking_number' => 14410750856832, 'charges' => 14],
                ['tracking_number' => 14419950851276, 'charges' => 14],
                ['tracking_number' => 14412550855798, 'charges' => 14],
                ['tracking_number' => 14415450885659, 'charges' => 14],
                ['tracking_number' => 14415450876549, 'charges' => 14],
                ['tracking_number' => 14415450876588, 'charges' => 14],
                ['tracking_number' => 14434050884534, 'charges' => 14],
                ['tracking_number' => 14430250874716, 'charges' => 14],
                ['tracking_number' => 14415450884558, 'charges' => 14],
                ['tracking_number' => 14432150887941, 'charges' => 14],
                ['tracking_number' => 14418850891876, 'charges' => 14],
                ['tracking_number' => 14429850891883, 'charges' => 14],
                ['tracking_number' => 14413050892747, 'charges' => 14],
                ['tracking_number' => 14418850892750, 'charges' => 14],
                ['tracking_number' => 14418550892949, 'charges' => 14],
                ['tracking_number' => 14421050892977, 'charges' => 14],
                ['tracking_number' => 14418850891973, 'charges' => 14],
                ['tracking_number' => 14415450877135, 'charges' => 14],
                ['tracking_number' => 14422350896954, 'charges' => 14],
                ['tracking_number' => 144678250916756, 'charges' => 14],
                ['tracking_number' => 14422350922739, 'charges' => 14],
                ['tracking_number' => 14422350922371, 'charges' => 14],
                ['tracking_number' => 14422350898929, 'charges' => 14],
                ['tracking_number' => 14422350895914, 'charges' => 14],
                ['tracking_number' => 14422350897839, 'charges' => 14],
                ['tracking_number' => 14422350897056, 'charges' => 14],
                ['tracking_number' => 14422350916946, 'charges' => 14],
                ['tracking_number' => 14422350916962, 'charges' => 14],
                ['tracking_number' => 14422350916014, 'charges' => 14],
                ['tracking_number' => 14415950898693, 'charges' => 14],
                ['tracking_number' => 14450350916952, 'charges' => 14],
                ['tracking_number' => 25122350919934, 'charges' => 18.62],
                ['tracking_number' => 20210750840606, 'charges' => 14],
                ['tracking_number' => 20222350864750, 'charges' => 14],
                ['tracking_number' => 20222350763135, 'charges' => 19.39],
                ['tracking_number' => 20222350820656, 'charges' => 20.01],
                ['tracking_number' => 20214450861297, 'charges' => 24.14],
                ['tracking_number' => 202630150770211, 'charges' => 14],
                ['tracking_number' => 20213550842082, 'charges' => 14],
                ['tracking_number' => 20246550831454, 'charges' => 15.18],
                ['tracking_number' => 20222350843158, 'charges' => 15.18],
                ['tracking_number' => 20222350870039, 'charges' => 15.18],
                ['tracking_number' => 20215850839150, 'charges' => 14],
                ['tracking_number' => 20215850844890, 'charges' => 14],
                ['tracking_number' => 20217450820087, 'charges' => 14],
                ['tracking_number' => 20218650827056, 'charges' => 15.17],
                ['tracking_number' => 20227150864755, 'charges' => 15.17],
                ['tracking_number' => 26722350915260, 'charges' => 24.15],
                ['tracking_number' => 26722350909614, 'charges' => 24.15],
                ['tracking_number' => 26722350911995, 'charges' => 24.15],
                ['tracking_number' => 20222350851082, 'charges' => 20.69],
                ['tracking_number' => 22328850884835, 'charges' => 14],
                ['tracking_number' => 22322350905141, 'charges' => 14],
                ['tracking_number' => 22322350904826, 'charges' => 14],
                ['tracking_number' => 20228850718736, 'charges' => 14],
                ['tracking_number' => 20220250923186, 'charges' => 26.84],
                ['tracking_number' => 22344450763632, 'charges' => 14],
                ['tracking_number' => 25122350845408, 'charges' => 14],
                ['tracking_number' => 14428850716737, 'charges' => 14],
                ['tracking_number' => 14422350911402, 'charges' => 14],
                ['tracking_number' => 144654450881273, 'charges' => 14],
                ['tracking_number' => 14422350884742, 'charges' => 14],
                ['tracking_number' => 14422350915100, 'charges' => 14.48],
                ['tracking_number' => 14420250867563, 'charges' => 15.17],
                ['tracking_number' => 14422350915128, 'charges' => 20],
                ['tracking_number' => 14422350915106, 'charges' => 28.97],
                ['tracking_number' => 14422350867574, 'charges' => 39.32],
                ['tracking_number' => 20215850863679, 'charges' => 14],
                ['tracking_number' => 25144350868300, 'charges' => 14],
                ['tracking_number' => 25127350867149, 'charges' => 14],
                ['tracking_number' => 25118650838569, 'charges' => 14],
                ['tracking_number' => 20227150843891, 'charges' => 47.61],
                ['tracking_number' => 27146550913097, 'charges' => 14],
                ['tracking_number' => 20227150827436, 'charges' => 14],
                ['tracking_number' => 20222350841263, 'charges' => 14],
                ['tracking_number' => 20222350863658, 'charges' => 14],
                ['tracking_number' => 20222350850479, 'charges' => 17.24],
                ['tracking_number' => 20220250920014, 'charges' => 30.36],
                ['tracking_number' => 20222350864514, 'charges' => 50.03],
                ['tracking_number' => 14417450879823, 'charges' => 14],
                ['tracking_number' => 14422350862231, 'charges' => 17.18],
                ['tracking_number' => 14414450910783, 'charges' => 17.18],
                ['tracking_number' => 14417450880249, 'charges' => 44.84],
                ['tracking_number' => 26744450622498, 'charges' => 14],
                ['tracking_number' => 26718650914741, 'charges' => 14],
                ['tracking_number' => 14414450903842, 'charges' => 65.54],
                ['tracking_number' => 14418650849026, 'charges' => 23.11],
                ['tracking_number' => 14420250820706, 'charges' => 48.29],
                ['tracking_number' => 14422350901423, 'charges' => 59.32],
                ['tracking_number' => 14422350909028, 'charges' => 19.31],
                ['tracking_number' => 14422350908953, 'charges' => 20.69],
                ['tracking_number' => 14422350916841, 'charges' => 20.69],
                ['tracking_number' => 14422350908426, 'charges' => 24.83],
                ['tracking_number' => 14422350908440, 'charges' => 41.39],
                ['tracking_number' => 25122350885042, 'charges' => 14],
                ['tracking_number' => 25122350908544, 'charges' => 14],
                ['tracking_number' => 14422350902936, 'charges' => 18.6],
                ['tracking_number' => 14443850879540, 'charges' => 25.52],
                ['tracking_number' => 20246550807049, 'charges' => 14],
                ['tracking_number' => 20246550872883, 'charges' => 14],
                ['tracking_number' => 20217450745866, 'charges' => 82.06],
                ['tracking_number' => 22327150885526, 'charges' => 26.22],
                ['tracking_number' => 22350550881814, 'charges' => 28.29],
                ['tracking_number' => 14429350916877, 'charges' => 18.29],
                ['tracking_number' => 14415950916858, 'charges' => 31.05],
                ['tracking_number' => 14429350916882, 'charges' => 31.74],
                ['tracking_number' => 20246550862647, 'charges' => 14],
                ['tracking_number' => 20223750836725, 'charges' => 14],
                ['tracking_number' => 20225550836765, 'charges' => 14],
                ['tracking_number' => 20222350849306, 'charges' => 14],
                ['tracking_number' => 20222350854785, 'charges' => 14],
                ['tracking_number' => 20222350863172, 'charges' => 14],
                ['tracking_number' => 20215950850531, 'charges' => 14],
                ['tracking_number' => 20233650739888, 'charges' => 14],
                ['tracking_number' => 20222350866368, 'charges' => 14],
                ['tracking_number' => 20211050850538, 'charges' => 14],
                ['tracking_number' => 20211050848780, 'charges' => 14],
                ['tracking_number' => 20217450863466, 'charges' => 14],
                ['tracking_number' => 20217450830783, 'charges' => 14],
                ['tracking_number' => 20215450852197, 'charges' => 14],
                ['tracking_number' => 20228850852404, 'charges' => 14],
                ['tracking_number' => 20225550863137, 'charges' => 15.17],
                ['tracking_number' => 20233250808142, 'charges' => 18.62],
                ['tracking_number' => 20233250825745, 'charges' => 22.08],
                ['tracking_number' => 20234050826028, 'charges' => 24.49],
                ['tracking_number' => 20227150818162, 'charges' => 36.92],
                ['tracking_number' => 20230450767485, 'charges' => 89.7],
                ['tracking_number' => 20217450822433, 'charges' => 144.56],
                ['tracking_number' => 17422350917240, 'charges' => 22.75],
                ['tracking_number' => 20215850816209, 'charges' => 15.17],
                ['tracking_number' => 20219850816166, 'charges' => 15.17],
                ['tracking_number' => 20222350813672, 'charges' => 18.98],
                ['tracking_number' => 22328850917841, 'charges' => 14],
                ['tracking_number' => 22317450865332, 'charges' => 27.36],
                ['tracking_number' => 20215950758906, 'charges' => 14.48],
                ['tracking_number' => 25122350901349, 'charges' => 20.69],
                ['tracking_number' => 25116850751579, 'charges' => 20.7],
                ['tracking_number' => 25125150859235, 'charges' => 20.7],
                ['tracking_number' => 25117250805005, 'charges' => 27.59],
                ['tracking_number' => 25128850901529, 'charges' => 34.22],
                ['tracking_number' => 25122350901471, 'charges' => 34.49],
                ['tracking_number' => 20228850876268, 'charges' => 14],
                ['tracking_number' => 14428850882586, 'charges' => 14],
                ['tracking_number' => 14413050882558, 'charges' => 14],
                ['tracking_number' => 14418650882555, 'charges' => 14],
                ['tracking_number' => 14415850837760, 'charges' => 14],
                ['tracking_number' => 20228850848637, 'charges' => 22.77],
                ['tracking_number' => 20220250915893, 'charges' => 26.91],
                ['tracking_number' => 22322650827612, 'charges' => 33.33],
                ['tracking_number' => 22317450883575, 'charges' => 43.72],
                ['tracking_number' => 22327150847488, 'charges' => 63.56],
                ['tracking_number' => 22320250824129, 'charges' => 97.71],
                ['tracking_number' => 20244450604369, 'charges' => 14],
                ['tracking_number' => 20228850790451, 'charges' => 14],
                ['tracking_number' => 20233650855884, 'charges' => 15.18],
                ['tracking_number' => 20244450604133, 'charges' => 17.94],
                ['tracking_number' => 20244450603592, 'charges' => 19.32],
                ['tracking_number' => 20229850834352, 'charges' => 19.32],
                ['tracking_number' => 20222350856245, 'charges' => 19.32],
                ['tracking_number' => 20227150856206, 'charges' => 19.32],
                ['tracking_number' => 20214450856382, 'charges' => 19.32],
                ['tracking_number' => 20216850834088, 'charges' => 23.46],
                ['tracking_number' => 20228850834395, 'charges' => 27.6],
                ['tracking_number' => 20217450833462, 'charges' => 44.16],
                ['tracking_number' => 20246550834592, 'charges' => 46.23],
                ['tracking_number' => 20218650833450, 'charges' => 46.92],
                ['tracking_number' => 20222350855636, 'charges' => 48.3],
                ['tracking_number' => 20222350856048, 'charges' => 98.67],
                ['tracking_number' => 22343850824887, 'charges' => 47.27],
                ['tracking_number' => 20218650794140, 'charges' => 14],
                ['tracking_number' => 22314450913730, 'charges' => 14],
                ['tracking_number' => 22328850905084, 'charges' => 14],
                ['tracking_number' => 14411150784715, 'charges' => 14],
                ['tracking_number' => 144138650811427, 'charges' => 14],
                ['tracking_number' => 14420250812091, 'charges' => 14],
                ['tracking_number' => 14444350806937, 'charges' => 14],
                ['tracking_number' => 14411950835510, 'charges' => 14],
                ['tracking_number' => 14417250825477, 'charges' => 14],
                ['tracking_number' => 14433650847460, 'charges' => 14],
                ['tracking_number' => 14410750848443, 'charges' => 14],
                ['tracking_number' => 14422350848433, 'charges' => 14],
                ['tracking_number' => 14428850844925, 'charges' => 14],
                ['tracking_number' => 14414450854907, 'charges' => 14],
                ['tracking_number' => 14417450885976, 'charges' => 14],
                ['tracking_number' => 14433650884167, 'charges' => 14],
                ['tracking_number' => 14434050886175, 'charges' => 14],
                ['tracking_number' => 14427250886238, 'charges' => 14],
                ['tracking_number' => 14410350888039, 'charges' => 14],
                ['tracking_number' => 14428850887529, 'charges' => 14],
                ['tracking_number' => 14410750876463, 'charges' => 14],
                ['tracking_number' => 14425550884536, 'charges' => 14],
                ['tracking_number' => 14428850874697, 'charges' => 14],
                ['tracking_number' => 14434050876637, 'charges' => 14],
                ['tracking_number' => 14413450871799, 'charges' => 14],
                ['tracking_number' => 14422350891976, 'charges' => 14],
                ['tracking_number' => 14425150889541, 'charges' => 14],
                ['tracking_number' => 14415450871810, 'charges' => 14],
                ['tracking_number' => 14410750893913, 'charges' => 14],
                ['tracking_number' => 14422350898773, 'charges' => 14],
                ['tracking_number' => 14422350915980, 'charges' => 14],
                ['tracking_number' => 14422350921630, 'charges' => 14],
                ['tracking_number' => 14422350898513, 'charges' => 14],
                ['tracking_number' => 14422350896274, 'charges' => 14],
                ['tracking_number' => 14422350898770, 'charges' => 14],
                ['tracking_number' => 14422350916928, 'charges' => 14],
                ['tracking_number' => 14422350918159, 'charges' => 14],
                ['tracking_number' => 14425150918160, 'charges' => 14],
                ['tracking_number' => 14422350916245, 'charges' => 14],
                ['tracking_number' => 14413350916237, 'charges' => 14],
                ['tracking_number' => 14422350918172, 'charges' => 14],
                ['tracking_number' => 22322350905610, 'charges' => 21.32],
                ['tracking_number' => 22322350897395, 'charges' => 14.35]
            ];

            $errors = [];
            $success = [];

            $chunkSize = 5000;

            // Process shipmentsData in chunks
            collect($shipmentsData2)->chunk($chunkSize)->each(function ($chunk) use (&$errors, &$success) {
                // Get the tracking numbers for this chunk
                $trackingNumbers = collect($chunk)->pluck('tracking_number');

                // Get all shipments for the chunk in one go
                $shipments = Shipment::whereIn('tracking_number', $trackingNumbers)->get()->keyBy('tracking_number');

                foreach ($chunk as $key => $shipmentData) {
                    $tracking_number = $shipmentData['tracking_number'];
                    $charges = $shipmentData['charges'];

                    // Check if the shipment exists in the database
                    if (!isset($shipments[$tracking_number])) {
                        $errors[$key] = [
                            'tracking_number' => $tracking_number,
                            'error' => 'Shipment not found'
                        ];
                        continue; // Skip to the next iteration if shipment not found
                    }

                    $shipment = $shipments[$tracking_number];
                    $shipment_id = $shipment->id;

                    // Check for existing charges
                    $existingCharge = ShipmentAdditionalCharges::where('shipment_id', $shipment_id)->first();

                    if ($existingCharge) {
                        // If charges are 0, 0.00, or null, update both wallet charges and timestamp
                        if ($existingCharge->wallet_charges == 0 || $existingCharge->wallet_charges == 0.00 || is_null($existingCharge->wallet_charges)) {
                            $existingCharge->update([
                                'wallet_charges' => $charges,
                                'wallet_charges_updated_at' => now(),
                            ]);
                        } elseif ($charges > 0) {
                            // If charges are greater than 0, update only wallet charges (no timestamp change)
                            $existingCharge->update([
                                'wallet_charges' => $charges,
                            ]);
                        }
                    } else {
                        // If no existing charge, create a new entry
                        ShipmentAdditionalCharges::create([
                            'shipment_id' => $shipment_id,
                            'wallet_charges' => $charges,
                            'wallet_charges_updated_at' => now(),
                        ]);
                    }

                    // Check if there is a pending payment
                    $pending_payment = PendingPaymentShipment::where('shipment_id', $shipment_id)
                        ->whereIn('type', [0, 1])
                        ->latest()
                        ->first();

                    if ($pending_payment) {
                        // Update payment if there's a pending payment
                        AdminFinanceController::update_payment($shipment_id, $pending_payment->type);
                        $success[] = $tracking_number;
                        continue; // Skip further checks for this shipment
                    }

                    // Check for done payments and pending processes
                    $done_payment_query = DonePaymentShipment::where('shipment_id', $shipment_id)
                        ->whereIn('type', [0, 1])
                        ->latest();

                    $check_pending_process = (clone $done_payment_query)->whereHas('done_payment', function ($query) {
                        $query->whereIn('status', [0, 1, 3])->where('is_wallet_payment', 1);
                    })->exists();

                    if ($check_pending_process) {
                        // If there's a pending process, update the payment
                        $done_payment = (clone $done_payment_query)->first();
                        AdminFinanceController::update_payment_done_payment($shipment_id, $done_payment->type, $done_payment->done_payment_id);
                        $success[] = $tracking_number;
                        continue; // Skip further checks for this shipment
                    }

                    // Check if payment has been paid late
                    $check_paid_late = (clone $done_payment_query)->whereHas('done_payment', function ($query) {
                        $query->where('status', 1)->where('is_wallet_payment', 1);
                    })->exists();

                    if ($check_paid_late) {
                        $errors[$key] = [
                            'tracking_number' => $tracking_number,
                            'error' => 'Payment cannot be processed now'
                        ];
                    } else {
                        // If no issues, mark this shipment as successful
                        $success[] = $tracking_number;
                    }
                }
            });

            // Final response
            echo json_encode([
                'status' => empty($errors) ? 1 : 0,
                'message' => empty($errors) ? 'All charges updated successfully.' : 'Some charges could not be updated due to errors.',
                'success' => $success,
                'errors' => $errors,
            ]);
            
    }
}
