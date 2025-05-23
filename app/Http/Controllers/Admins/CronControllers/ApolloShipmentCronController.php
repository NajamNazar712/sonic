<?php

namespace App\Http\Controllers\Admins\CronControllers;

use App\ApolloCronJobLog;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use http\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApolloShipmentCronController extends Controller
{

    public static function fetch_shipment_statuses()
    {
        $time_stamp = Carbon::now();
        $chunkSize = 500;
        $hasSentAny = false;

        $apollo_cron = ApolloCronJobLog::select('last_run_time')->where('id', 1)->first();

        // Build initial query
        $shipmentJourneysQuery = DB::table('shipments_journey as sj')
            ->join('shipments as s', 's.id', '=', 'sj.shipment_id')
            ->join('shipment_additional_charges as sd', 'sj.shipment_id', '=', 'sd.shipment_id')
            ->whereNotNull('sd.apollo_shipment_id');

        if ($apollo_cron && $apollo_cron->last_run_time) {
            $minId = DB::table('shipments_journey as sj')
                ->leftJoin('shipments as s', 's.id', '=', 'sj.shipment_id')
                ->leftJoin('shipment_additional_charges as sd', 'sj.shipment_id', '=', 'sd.shipment_id')
                ->whereNotNull('sd.apollo_shipment_id')
                ->where('sj.updated_at', '>=', $apollo_cron->last_run_time)
                ->min('sj.id');

            $shipmentJourneysQuery->where('sj.id', '>=', $minId);
        } else {
            $shipmentJourneysQuery->whereBetween('sj.updated_at', [
                $time_stamp->toDateString() . ' 00:00:01',
                $time_stamp->toDateString() . ' 23:59:59'
            ]);
        }

        // Fetch records
        $shipmentJourneys = $shipmentJourneysQuery
            ->select('sj.*', 'sd.apollo_shipment_id', 's.actual_weight', 'sd.apollo_is_piece')
            ->orderBy('sj.id')
            ->get();

        // Create Guzzle HTTP client
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api-apollo.sonic.pk/api/',
            'http_errors' => false,
            'connect_timeout' => 60,
            'timeout' => 60
        ]);

        // Process records in chunks
        $shipmentJourneys->chunk($chunkSize)->each(function ($chunk) use ($client, $time_stamp, &$hasSentAny) {
            try {
                $response = $client->post('sonic/shipments/journeys/bulk-create', [
                    'json' => [
                        'journeys' => $chunk,
                    ]
                ]);

                $responseBody = $response->getBody()->getContents();
                Log::channel('apolloJobLog')->info('Apollo API Response', [
                    'status' => $response->getStatusCode(),
                    'body' => $responseBody
                ]);
                $responseData = json_decode($responseBody, true);

                if (!empty($responseData['response.success'])) {
                    $hasSentAny = true;
                } else {
                    Log::channel('apolloJobLog')->error('API response does not indicate success', [
                        'response' => $responseData,
                    ]);
                }
            } catch (RequestException $e) {
                Log::channel('apolloJobLog')->error('Request to API failed', [
                    'error' => $e->getMessage(),
                    'exception' => $e
                ]);
            } catch (\Exception $e) {
                Log::channel('apolloJobLog')->error('An error occurred', [
                    'error' => $e->getMessage(),
                    'exception' => $e
                ]);
            }
        });

        // Update last run time if any data was sent
        if ($hasSentAny) {
            ApolloCronJobLog::where('id', 1)->update(['last_run_time' => $time_stamp]);
        }
    }

    public static function fetch_shipment_statuses_backup()
    {


        $time_stamp = Carbon::now();
        $apollo_booking_journeys=[];
        $apollo_piece_journeys=[];

        $apollo_cron = ApolloCronJobLog::select('last_run_time')->where('id', 1)->first();

        $shipmentJourneys = DB::table('shipments_journey as sj')
            ->join('shipments as s','s.id','sj.shipment_id')
//            ->join('user_shipping_infos as us','us.id','sj.pickup_address_id')
            ->join('shipment_additional_charges as sd', 'sj.shipment_id', '=', 'sd.shipment_id')
            ->whereNotNull('sd.apollo_shipment_id');
//            ->whereNotIn('sj.shipper_status_id',[1]);

        if($apollo_cron && $apollo_cron->last_run_time) {
            $shipmentJourneys->where('sj.updated_at', '>=', $apollo_cron->last_run_time);
        }else {
            $shipmentJourneys->whereBetween('sj.updated_at', [$time_stamp->toDateString() . ' 00:00:01', $time_stamp->toDateString() . ' 23:59:59']);
        }
        $shipmentJourneys->select('sj.*','sd.apollo_shipment_id','s.actual_weight','sd.apollo_is_piece');
        $journeys = $shipmentJourneys->orderBy('sj.id')->get();
        if($journeys->isNotEmpty()) {

            $client = new \GuzzleHttp\Client([
//                'base_uri' => 'https://movere-staging.sonic.pk/api/',
                'base_uri' => 'https://api-apollo.sonic.pk/api/',
                'http_errors' => FALSE,
                'connect_timeout' => 60,
                'timeout' => 60
            ]);

            try {
                $response = $client->post('sonic/shipments/journeys/bulk-create', [
                    'json' => [
                        'journeys' => $journeys,
                    ]
                ]);

                $responseBody = $response->getBody()->getContents();

                $responseData = json_decode($responseBody, true);
                // Check for success
                if (isset($responseData['response.success'])) {
                    ApolloCronJobLog::where('id', 1)->update(['last_run_time' => $time_stamp]);
                } else {
                    // Log failure response if necessary
                    Log::channel('apolloJobLog')->error('API response does not indicate success', [
                        'response' => $responseData,
//                        'journeys' => $journeys
                    ]);
                }
            } catch (RequestException $e) {
                // Catch Guzzle request-specific exceptions
                Log::channel('apolloJobLog')->error('Request to API failed', [
                    'error' => $e->getMessage(),
                    'exception' => $e
                ]);
            } catch (\Exception $e) {
                // Catch any other general exceptions
                Log::channel('apolloJobLog')->error('An error occurred', [
                    'error' => $e->getMessage(),
                    'exception' => $e
                ]);
            }


//            $url = 'http://localhost:9001/api/sonic/shipments/journeys/bulk-create'; // Replace with your actual API URL
////            $url = 'https://api-apollo-staging.sonic.pk/api/sonic/shipments/journeys/bulk-create';
//            $response = $client->post('shipments/journeys/bulk-create', [
//                'json' => [
//                    'journeys' => $journeys,
//                ]
//            ]);
//
//            $responseBody = $response->getBody()->getContents();
//            $responseData = json_decode($responseBody, true); // Decode JSON response into an associative array
//
//            if (isset($responseData['response.success'])) {
//                ApolloCronJobLog::where('id', 1)->update(['last_run_time' => $time_stamp]);
//            }
        }

//            // Collect unique IDs for batch queries
//            $rider_ids = $journeys->pluck('rider_id')->filter()->unique()->toArray();
//            $admin_ids = $journeys->pluck('admin_id')->filter()->unique()->toArray();
//            $user_ids = $journeys->pluck('user_id')->filter()->unique()->toArray();
//
//            // Perform batch queries
//            $riders = DB::connection('apollo_db')->table('riders')
//                ->whereIn('sonic_rider_id', $rider_ids)
//                ->pluck('id', 'sonic_rider_id'); // key: sonic_rider_id, value: id
//
//            $admins = DB::connection('apollo_db')->table('admins')
//                ->whereIn('sonic_admin_id', $admin_ids)
//                ->pluck('id', 'sonic_admin_id'); // key: sonic_admin_id, value: id
//
//            $users = DB::connection('apollo_db')->table('shippers')
//                ->whereIn('sonic_shipper_id', $user_ids)
//                ->pluck('id', 'sonic_shipper_id'); // key: sonic_shipper_id, value: id
//
//            foreach ($journeys as $journey) {
//
//                $rider_id = $journey->rider_id ? $riders->get($journey->rider_id) : null;
//                $admin_id = $journey->admin_id ? $admins->get($journey->admin_id) : null;
//                $user_id = $journey->user_id ? $users->get($journey->user_id) : null;
//
//                if($journey->apollo_is_piece == 0) {
//
//                    $apollo_booking_journeys[]= [
//                        //                            'id' => $journey->id,
//                        'booking_id'=> $journey->apollo_shipment_id,
//                        'verification' => 1,
//                        'origin_id' => null,
//                        'destination_id' =>null,
//                        'rider_id' => $rider_id,
//                        'user_id' => $user_id,
//                        'admin_id' => $admin_id,
//                        'city_id' => $journey->city_id,
//                        'shipper_status_id' => $journey->shipper_status_id,
//                        'consignee_status_id' => $journey->consignee_status_id,
//                        'ip_address' => $journey->ip_address,
//                        'created_at' => $journey->created_at,
//                        'updated_at' => $journey->updated_at,
//                    ];
//                }
//                else if($journey->apollo_is_piece == 1){
//
//                    $apollo_piece_journeys[]= [
//                        //                            'id' => $journey->id,
//                        'piece_id'=> $journey->apollo_shipment_id,
//                        'verification' => 1,
//                        'origin_id' => null,
//                        'destination_id' =>null,
//                        'rider_id' => $rider_id,
//                        'user_id' => $user_id,
//                        'admin_id' => $admin_id,
//                        'city_id' => $journey->city_id,
//                        'shipper_status_id' => $journey->shipper_status_id,
//                        'consignee_status_id' => $journey->consignee_status_id,
//                        'ip_address' => $journey->ip_address,
//                        'created_at' => $journey->created_at,
//                        'updated_at' => $journey->updated_at,
//                    ];
//                }
//
//            }
//
//            try {
//
//                DB::connection('apollo_db')->beginTransaction();
//
//                if(!empty($apollo_booking_journeys)) {
//
//                    DB::connection('apollo_db')->table('booking_journeys')->insert($apollo_booking_journeys);
//                    $booking_ids = array_unique(array_column($apollo_booking_journeys, 'booking_id'));
//
////                                $max_booking_statuses = DB::connection('apollo_db')->table('booking_journeys')
////                                    ->whereIn('booking_id', $booking_ids)
////                                    ->select('booking_id', DB::raw('MAX(shipper_status_id) as shipper_status_id'), DB::raw('MAX(created_at) as created_at'), DB::raw('MAX(id) as max_id'))
////                                    ->groupBy('booking_id')
////                                    ->get();
//
//                    $max_booking_statuses = DB::connection('apollo_db')->table('booking_journeys as bj')
//                        ->joinSub(
//                            DB::table('booking_journeys')
//                                ->select('booking_id', DB::raw('MAX(id) as max_id'))
//                                ->groupBy('booking_id'), 'subquery',
//                            function ($join) {
//                                $join->on('bj.booking_id', '=', 'subquery.booking_id')
//                                    ->on('bj.id', '=', 'subquery.max_id');
//                            }
//                        )->whereIn('bj.booking_id',$booking_ids)
//                        ->select('bj.booking_id', 'bj.shipper_status_id', 'bj.id as max_id','bj.created_at')
//                        ->get();
//
//                    foreach ($max_booking_statuses as $booking_status) {
//                        DB::connection('apollo_db')->table('trax_logistic_bookings')
//                            ->where('id', $booking_status->booking_id) // Match the correct booking id
//                            ->update([
//                                'shipper_status_id' => $booking_status->shipper_status_id,
//                                'consignee_status_id' => $booking_status->shipper_status_id,
//                                'updated_at' => $booking_status->created_at, // You might want to update this field as well
//                            ]);
//                    }
//                }
//
//                if(!empty($apollo_piece_journeys)) {
//
//                    DB::connection('apollo_db')->table('booking_piece_journeys')->insert($apollo_piece_journeys);
//                    $piece_ids = array_unique(array_column($apollo_piece_journeys, 'piece_id'));
//
////                                $max_piece_statuses = DB::connection('apollo_db')->table('booking_piece_journeys')
////                                    ->whereIn('piece_id', $piece_ids)
////                                    ->select('piece_id', DB::raw('MAX(shipper_status_id) as shipper_status_id'), DB::raw('MAX(created_at) as created_at'), DB::raw('MAX(id) as max_id'))
////                                    ->groupBy('piece_id')
////                                    ->get();
//                    $max_piece_statuses = DB::connection('apollo_db')
//                        ->table('booking_piece_journeys as bpj')
//                        ->joinSub(
//                            DB::connection('apollo_db')
//                                ->table('booking_piece_journeys')
//                                ->select('piece_id', DB::raw('MAX(id) as max_id'))
//                                ->groupBy('piece_id'),
//                            'max_rows',
//                            function ($join) {
//                                $join->on('bpj.piece_id', '=', 'max_rows.piece_id')
//                                    ->on('bpj.id', '=', 'max_rows.max_id');
//                            }
//                        )
//                        ->whereIn('bpj.piece_id', $piece_ids) // Ensure $piece_ids is a valid array of IDs
//                        ->select('bpj.piece_id', 'bpj.shipper_status_id', 'bpj.created_at', 'bpj.id as max_id')
//                        ->get();
//
//                    foreach ($max_piece_statuses as $piece_status) {
//                        DB::connection('apollo_db')->table('trax_booking_pieces')
//                            ->where('id', $piece_status->piece_id) // Match the correct booking id
//                            ->update([
//                                'shipper_status_id' => $piece_status->shipper_status_id,
//                                'updated_at' => $piece_status->created_at, // You might want to update this field as well
//                            ]);
//                    }
//                }
//
//                ApolloCronJobLog::where('id',1)->update(['last_run_time'=>$time_stamp]);
//
//                DB::connection('apollo_db')->commit();
//
//            } catch (\Throwable $th) {
//                DB::connection('apollo_db')->rollBack();
//                Log::channel('cronJobLog')->error('apollo-sync-shipment_journey:failed', [
//                    'error_message' => $th->getMessage(),
//                    'line' => $th->getLine(),
//                ]);
//            }



//

    }

}
