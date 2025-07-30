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
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class GeoCodesController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware('auth:admin');
//
//        $this->middleware('Permission');
//    }
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
//        if ($request->get('excel') && $request->get('excel') == true) {
//            ActivityTrailController::createActivityTrailLog(Auth::id(), 367);
//        }

        $shipments = Shipment::select('shipments.id','shipments.consignee_city_id','shipments.consignee_name','shipments.consignee_address','shipments.consignee_phone_number_1','shipments.tracking_number','sgc.latitude','sgc.longitude')
            ->leftjoin('shipments_geo_codes as sgc','sgc.shipment_id','=','shipments.id')
        ->where('shipments.created_at','>=' ,Carbon::now()->subMonths(12)->startOfMonth());

        if($request->get('tracking_number')) {
            $shipments = $shipments->where('shipments.tracking_number', '=', $request->get('tracking_number'));
        }

        if($request->get('origin_id')) {
            $shipments = $shipments->leftjoin('user_shipping_infos as usi','usi.id','shipments.pickup_address_id')
            ->where('usi.id', '=', $request->get('origin_id'));
        }
        if($request->get('destination_id')) {
            $shipments = $shipments->where('consignee_city_id',$request->get('destination_id'));
        }

        if($request->get('account_type_id')) {
            $shipments = $shipments->leftjoin('users as u','u.id','shipments.user_id')
                ->where('u.account_type_id', '=', $request->get('account_type_id'));
        }

        if($request->get('region_id')) {
            $shipments = $shipments->leftjoin('cities as cr','cr.id','shipments.consignee_city_id')
                ->leftjoin('zone_regions as zr','zr.zone_id','cr.zone_id')
                ->where('zr.region_id', '=', $request->get('region_id'));
        }

        if($request->get('segment_id')) {
            $shipments = $shipments->leftjoin('users as u','u.id','shipments.user_id')
                ->where('u.segment_id', '=', $request->get('segment_id'));
        }

        if($request->get('sub_segment_id')) {
            $shipments = $shipments->leftjoin('users as u','u.id','shipments.user_id')
                ->where('u.sub_segment_id', '=', $request->get('sub_segment_id'));
        }

        $datatable = Datatables::of($shipments)
            ->addColumn('action', function ($data) {

                $dropdown = '
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                <div class="dropdown-menu dropdown-menu-sm">
            ';

                $dropdown .= '<button type="button" class="dropdown-item edit" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-edit"></i></div><div class="col-9 offset-1">Edit</div></button>';

                if ($data->status == 1) {
                    $dropdown .= '<button type="button" class="dropdown-item disable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-alert-octagon"></i></div><div class="col-9 offset-1">Disable</div></button>';
                } else {
                    $dropdown .= '<button type="button" class="dropdown-item enable" ><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-check"></i></div><div class="col-9 offset-1">Enable</div></button>';
                }

                return $dropdown;
            })->rawColumns(['action']);

        return $datatable->make(true);
    }

    public function get_shipment_lat_long(Request $request)
    {

        $shipment_ids = $request->shipment_ids;

        $shipments =  Shipment::leftjoin('cities as ds','ds.id','shipments.consignee_city_id')
            ->select('shipments.id as shipment_id','shipments.consignee_address','ds.name as city')
            ->whereIn('shipments.id',$shipment_ids)->get();

        $unique_address = [];
        foreach ($shipments as $shipment) {
            $city = $shipment->city;
            $address = trim($shipment->consignee_address);

            if(!isset($unique_address[$city])) {
                $unique_address[$city] = [];
            }

            $unique_address[$city][$address] = true;
        }

        $client = new Client([
            'base_uri' => 'https://api1.tplmaps.com:8888/',
            'http_errors' => false,
            'connect_timeout' => 60,
            'timeout' => 60,
        ]);
        foreach ($unique_address as $city => $addresses) {

              foreach ($addresses as $address => $value) {

                  $response = $client->get('search', [
                      'headers' => [
                          'Accept' => 'application/json'
                      ],
                      'query' => [
                          'name' => 'D2/276 Malir Saudabad Nashter Square Karachi',
                          'city' => 'Karachi',
                          'output' => 'name,parent,parent1,parent2,parent3,country,compound_address_parents,id,lat,lng,subcat_name,cat_name',
                          'apikey' => '$2a$10$ixuhTqrlyD8pJfDY8FjO9OovMcIrBXIp2sUSHaJqeIjcNrpCyvHJ2'
                      ],
//                      'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
//                          dump((string) $stats->getEffectiveUri()); // <-- This gives full URL
//                      }
                  ]);

                  $bestMatch = null;
                  $highestSimilarity = 0;
                  $json = (string) $response->getBody();
                  $data = json_decode($json, true);
//                  $data = json_decode($response->getBody(), true, 512, JSON_BIGINT_AS_STRING);



                  if(is_array($data) && !empty($data)) {
                      foreach ($data as $unit) {
                             $compound = $unit['compound_address_parents'] ?? '';
//                          $match_terms = implode(' ',$unit['matched_terms'] ?? []);

                          // Combine both fields for broader match
//                          $searchText = $compound . ' ' . $match_terms;
                          $searchText = $compound;
                          similar_text(strtolower($address), strtolower($searchText), $percent);

//                          Log::info($percent.'-'.$highestSimilarity);
                          if ($percent > $highestSimilarity) {
                              $highestSimilarity = $percent;
                              $bestMatch = $unit;
                          }
                      }

//                      if ($highestSimilarity > 40 && $bestMatch) {
//                          // Extract accurate lat/lng from original JSON string using bestMatch ID
//                          $matchedId = $bestMatch['id'];
//
//                          if (preg_match('/\{[^}]*"id":\s*' . $matchedId . '[^}]*\}/', $json, $match)) {
//                              $entryJson = $match[0];
//
//                              preg_match('/"lat":\s*([0-9\.\-eE+]+)/', $entryJson, $latMatch);
//                              preg_match('/"lng":\s*([0-9\.\-eE+]+)/', $entryJson, $lngMatch);
//
//                              if (isset($latMatch[1]) && isset($lngMatch[1])) {
//                                  $lat = $latMatch[1];
//                                  $lng = $lngMatch[1];
//                                  $unique_address[$city][$address] = $lat . ',' . $lng;
//                              }
//                          } else {
//                              // fallback in case regex failed
//                              $lat = $bestMatch['lat'];
//                              $lng = $bestMatch['lng'];
//                              $unique_address[$city][$address] = $lat . ',' . $lng;
//                          }
//
//                      }
//                      else {
//                          // fallback to first result if no best match found
//                          $first = $data[0] ?? null;
//
//                          if ($first && isset($first['id'])) {
//                              $matchedId = $first['id'];
//
//                              if (preg_match('/\{[^}]*"id":\s*' . $matchedId . '[^}]*\}/', $json, $match)) {
//                                  $entryJson = $match[0];
//
//                                  preg_match('/"lat":\s*([0-9\.\-eE+]+)/', $entryJson, $latMatch);
//                                  preg_match('/"lng":\s*([0-9\.\-eE+]+)/', $entryJson, $lngMatch);
//
//                                  if (isset($latMatch[1]) && isset($lngMatch[1])) {
//                                      $lat = $latMatch[1];
//                                      $lng = $lngMatch[1];
//                                      $unique_address[$city][$address] = $lat . ',' . $lng;
//                                  }
//                              } else {
//                                  // fallback to parsed float values if regex fails
//                                  $lat = $first['lat'];
//                                  $lng = $first['lng'];
//                                  $unique_address[$city][$address] = $lat . ',' . $lng;
//                              }
//                          } else {
//                              $unique_address[$city][$address] = null;
//                          }
//                      }

//                      if($highestSimilarity > 50 && $bestMatch) {
//                          Log::info($data[0]);
////                          $lat = number_format((float) $bestMatch['lat'], 7, '.', '');
////                          $lng = number_format((float) $bestMatch['lng'], 7, '.', '');
//                          $lat = $bestMatch['lat'];
//                          $lng = $bestMatch['lng'];
//                          $unique_address[$city][$address] = $lat . ',' . $lng;
//                      } else {
//
//                          Log::info($data[0]);
////                          $lat = number_format((float) $data[0]['lat'], 7, '.', '');
////                          $lng = number_format((float) $data[0]['lng'], 7, '.', '');
//                          $lat = $data[0]['lat'];
//                          $lng = $data[0]['lng'];
//                          $unique_address[$city][$address] = $lat . ',' . $lng;
//                      }
                  } else {
                      $unique_address[$city][$address] = null;
                  }
             }

        }
        dd($unique_address);

    }
}
