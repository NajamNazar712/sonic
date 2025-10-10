<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Models\AccountType;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\City;
use App\Http\Models\Holiday;
use App\Http\Models\Region;
use App\Http\Models\Segment;
use App\Http\Models\Shipment;
use App\Http\Models\SubCategorySegment;
use App\Http\Traits\GeoCodeApiCountTrait;
use App\Models\ShipmentGeoCode;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class GeoCodesController extends Controller
{
    use GeoCodeApiCountTrait;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }
    public function index()
    {
        $cities = City::where('status', 1)->get();
        $account_types = AccountType::all();
        $segments = Segment::all();
        $regions = Region::where('status_id',1)->get();
//        $sub_segments =  SubCategorySegment::all();
        return view('admin.settings.geocodes.index')->with(['cities'=>$cities,'account_types'=>$account_types,'segments' => $segments,'regions' => $regions]);
    }

    public function list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 832);
        }

        $shipments = Shipment::select('shipments.id','shipments.consignee_city_id','shipments.consignee_name','shipments.consignee_address','shipments.consignee_phone_number_1','shipments.tracking_number','sgc.latitude','sgc.longitude','sgc.compound_address')
            ->leftjoin('shipments_geo_codes as sgc',function ($qu){
                $qu->on('sgc.shipment_id','=','shipments.id')->where('sgc.geo_code_type','=',1);
            })->where('shipments.created_at','>=' ,Carbon::now()->subMonths(12)->startOfMonth());

        if($request->get('tracking_number')) {
            $shipments->whereIn('shipments.tracking_number', explode(',', $request->get('tracking_number')));
        }

        if($request->get('origin_id')) {
            $shipments = $shipments->leftjoin('user_shipping_infos as usi','usi.id','shipments.pickup_address_id')
            ->where('usi.city_id', '=', $request->get('origin_id'));
        }
        if($request->get('destination_id')) {
            $shipments = $shipments->where('consignee_city_id',$request->get('destination_id'));
        }

        if($request->get('region_id')) {
            $shipments = $shipments->leftjoin('cities as cr','cr.id','shipments.consignee_city_id')
                ->leftjoin('zone_regions as zr','zr.zone_id','cr.zone_id')
                ->where('zr.region_id', '=', $request->get('region_id'));
        }

        if($request->get('account_type_id')) {
            $shipments = $shipments->leftjoin('users','users.id','shipments.user_id')
                ->where('users.account_type_id', '=', $request->get('account_type_id'));
        }

        if ($request->get('segment_id') || $request->get('sub_segment_id') || $request->get('account_type_id')) {
            $shipments = $shipments->leftJoin('users as u', 'u.id', '=', 'shipments.user_id');

            if ($request->get('segment_id')) {
                $shipments = $shipments->where('u.segment_id', '=', $request->get('segment_id'));
            }

            if ($request->get('sub_segment_id')) {
                $shipments = $shipments->where('u.sub_segment_id', '=', $request->get('sub_segment_id'));
            }

            if ($request->get('account_type_id')) {
                $shipments = $shipments->where('u.account_type_id', '=', $request->get('account_type_id'));
            }
        }


        $datatable = Datatables::of($shipments)
            ->addColumn('action', function ($data) {

                $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                ';

                if(!$data->latitude && !$data->longitude) {
                    $dropdown .= '<button type="button" class="dropdown-item generate_geo_code_btn" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Generate Geo Codes</div></button>';
                } else {
                    $encodedCoord = base64_encode(json_encode([$data->id]));
                    $dropdown .= '<a href="' . route('admin.settings.geo_codes.view_tpl_map') . '?coords=' . $encodedCoord . '" target="_blank" class="dropdown-item view_geo_code_map">
                        <div class="row no-gutters align-items-center">
                            <div class="col-2"><i class="fa fa-map-marker"></i></div>
                            <div class="col-9 offset-1">View Map</div>
                        </div>
                    </a>';
                }

                return $dropdown;
            })->rawColumns(['action']);

        return $datatable->make(true);
    }

    public function get_shipment_lat_long_old(Request $request)
    {

        $shipment_ids = $request->shipment_ids;
        if(!is_array($shipment_ids)) {
            return response()->json(['status' => 0,'error' => 'Shipment ids should be an array']);
        }

        $shipments = Shipment::leftJoin('cities as ds', 'ds.id', 'shipments.consignee_city_id')
            ->select(
                'shipments.id as shipment_id',
                'shipments.user_id',
                'shipments.consignee_address',
                'ds.name as city'
            )
            ->whereIn('shipments.id', $shipment_ids)
            ->get()
            ->keyBy('shipment_id'); // index by shipment_id

        $consignee_addresses = $shipments->pluck('consignee_address')->unique()->values()->toArray();

        $geoCoded = Shipment::whereIn('shipments.consignee_address', $consignee_addresses)
            ->join('shipments_geo_codes as sgo', 'sgo.shipment_id', '=', 'shipments.id')
            ->select('shipments.id as shipment_id', 'sgo.latitude', 'sgo.longitude','shipments.consignee_address')
            ->get()
            ->keyBy('consignee_address');

        $insert_data = [];
        if($shipments->count() > 0) {
            $unique_address = [];
            foreach ($shipments as $shipment_id => $shipment) {
                $city = trim($shipment->city);
                $address = trim($shipment->consignee_address);
                $geo = $geoCoded->get($address);
               if(!$geo){
                    $unique_address[$city][$address]['shipment_ids'][$shipment->shipment_id] = $shipment->user_id;
                }else{
                   $insert_data[] = [
                       'user_id' => $shipment->user_id,
                       'shipment_id' => $shipment_id,
                       'latitude' => $geo->latitude,
                       'longitude' => $geo->longitude,
                       'created_at' => now(),
                       'updated_at' => now(),
                   ];
                }
            }

            if(!empty($unique_address)) {

                // Step 2: Prepare HTTP Client
                $client = new \GuzzleHttp\Client([
                    'base_uri' => 'https://api1.tplmaps.com:8888/',
                    'http_errors' => false,
                    'connect_timeout' => 60,
                    'timeout' => 60,
                ]);
                $timestamp = Carbon::now();
                // Step 3: Loop through each city/address
                foreach ($unique_address as $city => $addresses) {
                    foreach ($addresses as $address => $info) {
                        $response = $client->get('search', [
                            'headers' => [
                                'Accept' => 'application/json'
                            ],
                            'query' => [
                                'name' => $address,
                                'city' => $city,
                                'output' => 'name,parent,parent1,parent2,parent3,country,compound_address_parents,id,lat,lng,subcat_name,cat_name',
                                'apikey' => '$2a$10$ixuhTqrlyD8pJfDY8FjO9OovMcIrBXIp2sUSHaJqeIjcNrpCyvHJ2'
                            ],
                        ]);

                        $data = json_decode($response->getBody(), true);
                        Log::channel('code_test_log')->info($data);
                        $this->geo_code_api_count(1);
                        $lat = null;
                        $lng = null;
                        $compound_address = null;

                        if (is_array($data) && !empty($data)) {
                            // Match based on address similarity
                            $bestMatch = null;
                            $highestSimilarity = 0;
                            foreach ($data as $unit) {
                                $compound = $unit['compound_address_parents'] ?? '';
//                            $match_terms = implode(' ',$unit['matched_terms'] ?? []);
                                similar_text(strtolower($address), strtolower($compound), $percent);

                                if ($percent > $highestSimilarity) {
                                    $highestSimilarity = $percent;
                                    $bestMatch = $unit;
                                }
                            }

                            // Use best match if found
                            $target = ($highestSimilarity >= 80 && $bestMatch) ? $bestMatch : $data[0];
                            $encodedTarget = json_encode($target);

                            // Extract lat/lng from raw JSON string to avoid float rounding issues
                            preg_match('/"lat"\s*:\s*([0-9\.\-eE+-]+)/', $encodedTarget, $latMatch);
                            preg_match('/"lng"\s*:\s*([0-9\.\-eE+-]+)/', $encodedTarget, $lngMatch);
                            preg_match('/"compound_address_parents"\s*:\s*([0-9\.\-eE+-]+)/', $encodedTarget, $compMatch);


                            $lat = $latMatch[1] ?? ($target['lat'] ?? null);
                            $lng = $lngMatch[1] ?? ($target['lng'] ?? null);
                            $compound_address = $compMatch[1] ?? ($target['compound_address_parents'] ?? null);

                        }

                        if ($lat && $lng) {
                            foreach ($info['shipment_ids'] as $shipment_id => $user_id) {
                                $insert_data[] = [
                                    'user_id' => $user_id,
                                    'shipment_id' => $shipment_id,
                                    'latitude' => $lat,
                                    'longitude' => $lng,
                                    'compound_address' => $compound_address,
                                    'created_at' => $timestamp,
                                    'updated_at' => $timestamp,
                                ];
                            }
                        }
                    }
                }
                // Step 5: Bulk insert into DB

            }

            if (!empty($insert_data)) {
                ShipmentGeoCode::insert($insert_data);
                return response()->json([
                    'status' => 1,
                    'success' => 'Lat/Lng fetched and saved successfully.',
                    'inserted_count' => count($insert_data),
                ]);

            } else {
                return response()->json(['status' => 0,'error' => 'No data found.']);
            }
        } else {
            return response()->json(['status' => 0,'error' => 'No data found.']);
        }

    }

    public function get_shipment_lat_long(Request $request)
    {
        $shipment_ids = $request->shipment_ids;
        if (!is_array($shipment_ids)) {
            return response()->json(['status' => 0, 'error' => 'Shipment ids should be an array']);
        }

        $shipments = Shipment::leftJoin('cities as ds', 'ds.id', 'shipments.consignee_city_id')
            ->select(
                'shipments.id as shipment_id',
                'shipments.user_id',
                'shipments.consignee_address',
                'ds.name as city'
            )
            ->whereIn('shipments.id', $shipment_ids)
            ->get()
            ->keyBy('shipment_id');

        $consignee_addresses = $shipments->pluck('consignee_address')->unique()->values()->toArray();

        $geoCoded = Shipment::whereIn('shipments.consignee_address', $consignee_addresses)
            ->join('shipments_geo_codes as sgo', 'sgo.shipment_id', '=', 'shipments.id')
            ->select('shipments.id as shipment_id', 'sgo.latitude', 'sgo.longitude', 'shipments.consignee_address')
            ->get()
            ->keyBy('consignee_address');

        $insert_data = [];

        if ($shipments->count() > 0) {
            $unique_address = [];

            foreach ($shipments as $shipment_id => $shipment) {
                $city = trim($shipment->city);
                $address = trim($shipment->consignee_address);
                $geo = $geoCoded->get($address);

                if (!$geo) {
                    $unique_address[$city][$address]['shipment_ids'][$shipment->shipment_id] = $shipment->user_id;
                } else {
                    $insert_data[] = [
                        'user_id' => $shipment->user_id,
                        'shipment_id' => $shipment_id,
                        'latitude' => $geo->latitude,
                        'longitude' => $geo->longitude,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($unique_address)) {
                $client = new \GuzzleHttp\Client([
                    'base_uri' => 'https://api1.tplmaps.com:8888/',
                    'http_errors' => false,
                    'connect_timeout' => 60,
                    'timeout' => 60,
                ]);

                $timestamp = Carbon::now();

                foreach ($unique_address as $city => $addresses) {
                    foreach ($addresses as $address => $info) {
                        $response = $client->get('search', [
                            'headers' => ['Accept' => 'application/json'],
                            'query' => [
                                'name' => $address,
                                'city' => $city,
                                'output' => 'name,parent,parent1,parent2,parent3,country,compound_address_parents,id,lat,lng,subcat_name,cat_name',
                                'apikey' => '$2a$10$ixuhTqrlyD8pJfDY8FjO9OovMcIrBXIp2sUSHaJqeIjcNrpCyvHJ2'
                            ],
                        ]);

                        $data = json_decode($response->getBody(), true);
                        Log::channel('code_test_log')->info($data);
                        $this->geo_code_api_count(1);

                        $lat = null;
                        $lng = null;
                        $compound_final = null;

                        if (is_array($data) && !empty($data)) {
                            $normalize = function ($string) {
                                $string = strtolower($string);
                                $string = preg_replace('/[^a-z0-9\s]/i', ' ', $string);
                                $string = preg_replace('/\s+/', ' ', $string);
                                return trim($string);
                            };

                            $normalizedAddress = $normalize($address);
                            $address_terms = explode(' ', $normalizedAddress);

                            $high_weight_terms = ['apartment', 'flat', 'block', 'floor', 'road', 'sector', 'phase', 'house', 'street', 'lane', 'colony', 'society', 'scheme', 'building', 'plot', 'avenue', 'extension', 'villa', 'duplex', 'suite', 'row', 'view', 'park', 'compound'];
                            $medium_weight_terms = ['no', 'number', 'unit', 'tower', 'drive', 'court', 'line', 'circle'];
                            $low_weight_terms = ['town', 'city', 'market', 'commercial', 'residential', 'garden', 'hospital', 'school', 'company', 'office', 'service', 'underpass', 'station', 'chowk', 'gate'];

                            $weighted_terms = [
                                ['terms' => $high_weight_terms, 'weight' => 3],
                                ['terms' => $medium_weight_terms, 'weight' => 2],
                                ['terms' => $low_weight_terms, 'weight' => 1],
                            ];

                            $bestMatch = null;
                            $highestScore = 0;

                            foreach ($data as $unit) {
                                $compound = $normalize($unit['compound_address_parents'] ?? '');
                                $compound_terms = explode(' ', $compound);
                                $score = 0;

                                foreach ($weighted_terms as $group) {
                                    foreach ($group['terms'] as $term) {
                                        if (strpos($compound, $term) !== false) {
                                            $score += $group['weight'];
                                        } else {
                                            foreach ($compound_terms as $compound_term) {
                                                if (levenshtein($term, $compound_term) <= 1) {
                                                    $score += $group['weight'] - 1;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }

                                similar_text($normalizedAddress, $compound, $percent);
                                if ($percent > 60) {
                                    $score += round($percent / 10);
                                }

                                Log::channel('code_test_log')->info('Geo match unit', [
                                    'unit' => $unit,
                                    'score' => $score,
                                    'similarity' => $percent
                                ]);

                                if ($score > $highestScore) {
                                    $highestScore = $score;
                                    $bestMatch = $unit;
                                }
                            }

                            $target = $bestMatch ?? $data[0];

                            $encodedTarget = json_encode($target);
                            preg_match('/"lat"\s*:\s*([0-9\.\-eE\+]+)/', $encodedTarget, $latMatch);
                            preg_match('/"lng"\s*:\s*([0-9\.\-eE\+]+)/', $encodedTarget, $lngMatch);
                            preg_match('/"compound_address_parents"\s*:\s*"([^"]*)"/', $encodedTarget, $compMatch);

                            $lat = $latMatch[1] ?? ($target['lat'] ?? null);
                            $lng = $lngMatch[1] ?? ($target['lng'] ?? null);
                            $compound_final = $compMatch[1] ?? ($target['compound_address_parents'] ?? null);
                        }

                        if ($lat && $lng) {
                            foreach ($info['shipment_ids'] as $shipment_id => $user_id) {
                                $insert_data[] = [
                                    'user_id' => $user_id,
                                    'shipment_id' => $shipment_id,
                                    'latitude' => $lat,
                                    'longitude' => $lng,
                                    'compound_address' => $compound_final,
                                    'created_at' => $timestamp,
                                    'updated_at' => $timestamp,
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($insert_data)) {
                ShipmentGeoCode::insert($insert_data);
                return response()->json([
                    'status' => 1,
                    'success' => 'Lat/Lng fetched and saved successfully.',
                    'inserted_count' => count($insert_data),
                ]);
            } else {
                return response()->json(['status' => 0, 'error' => 'No data found.']);
            }
        } else {
            return response()->json(['status' => 0, 'error' => 'No data found.']);
        }
    }






//    public function get_shipment_lat_long(Request $request)
//    {
//
//        $shipment_ids = $request->shipment_ids;
//
//        $shipments =  Shipment::leftjoin('cities as ds','ds.id','shipments.consignee_city_id')
//            ->select('shipments.id as shipment_id','shipments.consignee_address','ds.name as city')
//            ->whereIn('shipments.id',$shipment_ids)->get();
//
//        $unique_address = [];
//        foreach ($shipments as $shipment) {
//            $city = $shipment->city;
//            $address = trim($shipment->consignee_address);
//
//            $unique_address[$address][$city]['shipment_id'][] = $shipment->shipment_id;
//
//
////            if(!isset($unique_address[$city])) {
////                $unique_address[$city]['address']['shipment_ids'][] = $shipment->id; ;
////            }
//
////            $unique_address[$city][$address] = true;
//        }
//
//        $client = new Client([
//            'base_uri' => 'https://api1.tplmaps.com:8888/',
//            'http_errors' => false,
//            'connect_timeout' => 60,
//            'timeout' => 60,
//        ]);
//        $final_push = array();
//        foreach ($unique_address as $address => $addresses) {
//              foreach ($addresses as $city => $value) {
//
//
//
//                  $response = $client->get('search', [
//                      'headers' => [
//                          'Accept' => 'application/json'
//                      ],
//                      'query' => [
//                          'name' => $address,
//                          'city' => $city,
//                          'output' => 'name,parent,parent1,parent2,parent3,country,compound_address_parents,id,lat,lng,subcat_name,cat_name',
//                          'apikey' => '$2a$10$ixuhTqrlyD8pJfDY8FjO9OovMcIrBXIp2sUSHaJqeIjcNrpCyvHJ2'
//                      ],
//                  ]);
//                  $json = (string) $response->getBody();
//                  $data = json_decode($json, true);
//                  $final_push[] = array('shipment_id'=>$value['shipment_id'],'lat'=>1,'lng'=>1);
//                  continue;
//                  $bestMatch = null;
//                  $highestSimilarity = 0;
//                  $final_push[$value] = true;
//                  if(is_array($data) && !empty($data)) {
//                      foreach ($data as $unit) {
//                             $compound = $unit['compound_address_parents'] ?? '';
////                          $match_terms = implode(' ',$unit['matched_terms'] ?? []);
//
//                          // Combine both fields for broader match
////                          $searchText = $compound . ' ' . $match_terms;
//                          $searchText = $compound;
//                          similar_text(strtolower($address), strtolower($searchText), $percent);
//
//                          if ($percent > $highestSimilarity) {
//                              $highestSimilarity = $percent;
//                              $bestMatch = $unit;
//                          }
//                      }
//
////                      Log::info([$highestSimilarity , $bestMatch , isset($bestMatch['id'])]);
////                      if ($highestSimilarity == 40 && $bestMatch && isset($bestMatch['id'])) {
////                          dd(1);
////                          // ID-based JSON block extraction
////                          $matchedId = $bestMatch['id'];
////
////                          if (preg_match('/\{[^}]*"id"\s*:\s*' . $matchedId . '[^}]*\}/', $json, $match)) {
////                              $entryJson = $match[0];
////
////                              preg_match('/"lat"\s*:\s*([0-9\.\-eE+]+)/', $entryJson, $latMatch);
////                              preg_match('/"lng"\s*:\s*([0-9\.\-eE+]+)/', $entryJson, $lngMatch);
////
////                              $lat = $latMatch[1] ?? null;
////                              $lng = $lngMatch[1] ?? null;
////                          } else {
////                              // fallback if ID block not found
////                              $lat = $bestMatch['lat'];
////                              $lng = $bestMatch['lng'];
////                          }
////
////                          $unique_address[$city][$address] = $lat . ',' . $lng;
////                      } else {
////                          // fallback to first item
////                          $first = $data[0] ?? null;
////
////                          if ($first && isset($first['id'])) {
////                              $matchedId = $first['id'];
////
////                              if (preg_match('/\{[^}]*"id"\s*:\s*' . $matchedId . '[^}]*\}/', $json, $match)) {
////                                  $entryJson = $match[0];
////
////                                  preg_match('/"lat"\s*:\s*([0-9\.\-eE+]+)/', $entryJson, $latMatch);
////                                  preg_match('/"lng"\s*:\s*([0-9\.\-eE+]+)/', $entryJson, $lngMatch);
////
////                                  $lat = $latMatch[1] ?? null;
////                                  $lng = $lngMatch[1] ?? null;
////                              } else {
////                                  $lat = $first['lat'];
////                                  $lng = $first['lng'];
////                              }
////
////                              $unique_address[$city][$address] = $lat . ',' . $lng;
////                          } else {
////                              $unique_address[$city][$address] = null;
////                          }
////                      }
//                      Log::info($bestMatch);
//                      // Default fallback to first
//                      $targetId = $bestMatch && $highestSimilarity > 50 ? $bestMatch['id'] : $data[0]['id'];
//
//                      // Find exact object in original $json string by ID
//                      $pattern = '/\{[^}]*"id"\s*:\s*' . preg_quote($targetId, '/') . '[^}]*\}/';
//                      if (preg_match($pattern, $json, $matchedObject)) {
//                          preg_match('/"lat"\s*:\s*([0-9\.\-eE+]+)/', $matchedObject[0], $latMatch);
//                          preg_match('/"lng"\s*:\s*([0-9\.\-eE+]+)/', $matchedObject[0], $lngMatch);
//
//                          $lat = $latMatch[1] ?? null;
//                          $lng = $lngMatch[1] ?? null;
//                          $unique_address[$city][$address] = $lat . ',' . $lng;
//                      } else {
//                          $unique_address[$city][$address] = null; // fallback in case match fails
//                      }
//
//
//
////                      if ($highestSimilarity > 50 && $bestMatch) {
////                        Log::info(1);
////                          $encodedBestMatch = json_encode($bestMatch);
////                          // Use raw lat/lng from original JSON string via regex
////                          preg_match('/"lat"\s*:\s*([0-9\.\-eE+]+)/',$encodedBestMatch, $latMatch);
////                          preg_match('/"lng"\s*:\s*([0-9\.\-eE+]+)/',$encodedBestMatch, $lngMatch);
////
//////                          $lat = $latMatch[1] ?? null;
//////                          $lng = $lngMatch[1] ?? null;
////                          $lat = isset($latMatch[1]) ? rtrim(rtrim(number_format((float) $latMatch[1], 14, '.', ''), '0'), '.') : null;
////                          $lng = isset($lngMatch[1]) ? rtrim(rtrim(number_format((float) $lngMatch[1], 14, '.', ''), '0'), '.') : null;
////
////                          $unique_address[$city][$address] = $lat .','.$lng;
////                      }
////                      else {
////                          preg_match('/"lat"\s*:\s*([0-9\.\-eE+]+)/', $json, $latMatch);
////                          preg_match('/"lng"\s*:\s*([0-9\.\-eE+]+)/', $json, $lngMatch);
////
////                          $lat = $latMatch[1] ?? null;
////                          $lng = $lngMatch[1] ?? null;
////
////                          $unique_address[$city][$address] = $lat .','.$lng;
////                      }
//
//                  } else {
//                      $unique_address[$city][$address] = null;
//                  }
//             }
//
//        }
//
//
//
//    }

    public function view_tpl_map(Request $request)
    {
        $encodedCoords = $request->query('coords');
        $decodedCoords = [];

        if ($encodedCoords) {
            $json = base64_decode($encodedCoords);
            $decodedCoords = json_decode($json, true);

            $shipment_geo_codes = ShipmentGeoCode::whereIn('shipment_id',$decodedCoords)->select('latitude as lat','longitude as lng')->get();

            if($shipment_geo_codes->isNotEmpty()){
                $this->geo_code_api_count(1);
                return view('admin.settings.geocodes.tpl_map', [
                    'coords' => $shipment_geo_codes
                ]);
            }
        }

        return redirect()->back();

    }

    public  function get_manual_shipment_geo_codes(Request $request)
    {

        $shipment_geo_code = ShipmentGeoCode::where('shipment_id',$request->shipment_id)->where('geo_code_type',2)
            ->select('latitude','longitude')->first();

        $lat = null;
        $long = null;
        if($shipment_geo_code){
            $shipment_id = $shipment_geo_code->shipment_id;
            $lat = $shipment_geo_code->latitude;
            $long = $shipment_geo_code->longitude;
        }

        return response()->json(['status'=>0,'lat'=>$lat,'long'=>$long]);
    }

    public function update_manual_geo_codes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_id' => 'required|integer|exists:shipments,id',
            'lat'         => 'required|numeric',
            'long'        => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 1,
                'error'  => $validator->errors()->first()
            ]);
        }


        // Check existing record
        $geoCode = ShipmentGeoCode::where('shipment_id', $request->shipment_id)
            ->where('geo_code_type', 2)
            ->first();

        if ($geoCode) {
            // Update existing
            $geoCode->latitude  = $request->lat;
            $geoCode->longitude = $request->long;
        } else {
            // New insert
            $geoCode = new ShipmentGeoCode();
            $geoCode->shipment_id = $request->shipment_id;
            $geoCode->latitude    = $request->lat;
            $geoCode->longitude   = $request->long;
            $geoCode->geo_code_type        = 2;

        }
        $geoCode->save();


        return response()->json([
            'status'  => 0,
            'message' => 'Geo code saved successfully!'
        ]);
    }
}
