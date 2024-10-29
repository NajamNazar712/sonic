<?php

namespace App\Jobs;

use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Models\Admin\CargoManifest\V2JunctionRoutes;
use App\Http\Models\Admin\CargoManifest\V2Junctions;
use App\Http\Models\Admin\CargoManifest\V2JunctionVehicles;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class MakeDynamicHubsMapping implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestVehicles;
    protected $closestHubId;
    protected $city;
    protected $authId;


    public function __construct(array $requestVehicles, int $closestHubId, $city, $authId)
    {
        $this->queue = 'make_dynamic_hubs_mapping';
        $this->requestVehicles = $requestVehicles;
        $this->closestHubId = $closestHubId;
        $this->city = $city;
        $this->authId = $authId;

    }

    public function handle()
    {
        //take this hub as reference hub
        $closestHubId = $this->closestHubId;
        $city = $this->city;
        $requestVehicles = $this->requestVehicles;
        $authId = $this->authId;

        DB::beginTransaction();

        try {

            // for creating mappings from origin to destination (1st way)
            $closestHubOriginMappings = V2JunctionMapping::where('origin_id', $closestHubId)->get();

            $closestHubDestinationMappings = V2JunctionMapping::where('destination_id', $closestHubId)->get();

            $newJunctions1 = [];
            $newRouteVehicles1 = [];

            foreach ($closestHubOriginMappings as  $closestHubMapping) {

                $mapping = new V2JunctionMapping();

                $mapping->origin_id = $city->id; //here origin will be the newly created hub for all mappings
                $mapping->destination_id = $closestHubMapping->destination_id;//destinations will be of the closest hub
                $mapping->status = $closestHubMapping->status;
                $mapping->updated_by = $authId;
                $mapping->save();

                $junctions = V2Junctions::where('junction_mapping_id', $closestHubMapping->id)->get();


                //--------------x---------x---------x--------x-------x---------x----------x--------x
                // TO-6836 (Adding the reference hub as junction in new mappings)
                $newJunctions1[] = [
                    'junction_mapping_id' => $mapping->id,
                    'junction_id' => $closestHubId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $junctionRoute = new V2JunctionRoutes();
                $junctionRoute->junction_mapping_id = $mapping->id;
                $junctionRoute->starting_hub_id = $mapping->origin_id;
                $junctionRoute->ending_hub_id = $mapping->destination_id;
                $junctionRoute->created_at = now();
                $junctionRoute->save();

                foreach ($requestVehicles as $vehicle) {
                    $junctionRouteVehicles[] = [
                        'junction_route_id' => $junctionRoute->id,
                        'vehicle_id' => $vehicle,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                V2JunctionVehicles::insert($junctionRouteVehicles);

                //--------------x---------x---------x--------END TO-6836-------x---------x----------x--------x

                foreach ($junctions as $j) {
                    $newJunctions1[] = [
                        'junction_mapping_id' => $mapping->id,
                        'junction_id' => $j->junction_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                $previous = $mapping->destination_id;

                $routeJunctions = V2JunctionRoutes::where('junction_mapping_id', $closestHubMapping->id)->get();

                foreach ($routeJunctions as $rj) {
                    $route_junction = new V2JunctionRoutes();
                    $route_junction->junction_mapping_id = $mapping->id;
                    $route_junction->starting_hub_id = $previous;
                    $route_junction->ending_hub_id = $rj->ending_hub_id;
                    $route_junction->save();

                    $previous = $rj->ending_hub_id;

                    $vehicles = V2JunctionVehicles::where('junction_route_id', $rj->id)->get();

                    foreach ($vehicles as $vehicle) {
                        $newRouteVehicles1[] = [
                            'junction_route_id' => $route_junction->id,
                            'vehicle_id' => $vehicle->vehicle_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

            }

            V2Junctions::insert($newJunctions1);
            V2JunctionVehicles::insert($newRouteVehicles1);

            //for creating mapping between the newly created hub and the closest hub
            $mapping1 = new V2JunctionMapping();

            $mapping1->origin_id = $city->id;
            $mapping1->destination_id = $closestHubId;
            $mapping1->updated_by = $authId;
            $mapping1->save();

            $route_junction1 = new V2JunctionRoutes();
            $route_junction1->junction_mapping_id = $mapping1->id;
            $route_junction1->starting_hub_id = $mapping1->origin_id;
            $route_junction1->ending_hub_id = $mapping1->destination_id;
            $route_junction1->save();

            $singleRouteVehicles1 = [];
            foreach ($requestVehicles as $vehicle) {
                $singleRouteVehicles1[] = [
                    'junction_route_id' => $route_junction1->id,
                    'vehicle_id' => $vehicle,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            V2JunctionVehicles::insert($singleRouteVehicles1);

            // --------------x-------------------x-------------x---------------

            // --------------x-------------------x-------------x---------------

            // for creating mappings from destination to origin (2nd way)

            $newJunctions2 = [];
            $newRouteVehicles2 = [];

            foreach ($closestHubDestinationMappings as  $closestHubMapping) {

                $mapping = new V2JunctionMapping();

                $mapping->origin_id = $closestHubMapping->origin_id; //here origin will be the closest hub for all mappings
                $mapping->destination_id = $city->id;//here destination will be the newly created hub for all mappings
                $mapping->status = $closestHubMapping->status;
                $mapping->updated_by = $authId;
                $mapping->save();

                $junctions = V2Junctions::where('junction_mapping_id', $closestHubMapping->id)->get();

                foreach ($junctions as $j) {
                    $newJunctions2[] = [
                        'junction_mapping_id' => $mapping->id,
                        'junction_id' => $j->junction_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                //------x--------x------x-------x------x------x-------x-------x--------x
                // TO-6836 (Adding the reference hub as junction in new mappings)
                $newJunctions2[] = [
                    'junction_mapping_id' => $mapping->id,
                    'junction_id' => $closestHubId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];


                $junctionRoute2 = new V2JunctionRoutes();
                $junctionRoute2->junction_mapping_id = $mapping->id;
                $junctionRoute2->starting_hub_id = $mapping->origin_id;
                $junctionRoute2->ending_hub_id = $mapping->destination_id;
                $junctionRoute2->created_at = now();
                $junctionRoute2->save();

                foreach ($requestVehicles as $vehicle) {
                    $junctionRoute2Vehicles[] = [
                        'junction_route_id' => $junctionRoute2->id,
                        'vehicle_id' => $vehicle,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                V2JunctionVehicles::insert($junctionRoute2Vehicles);

                //------x--------x------x-------x------END TO-6836------x-------x-------x--------x

                $previous = $mapping->destination_id;

                $routeJunctions = V2JunctionRoutes::where('junction_mapping_id', $closestHubMapping->id)->get();

                foreach ($routeJunctions as $rj) {
                    $route_junction = new V2JunctionRoutes();
                    $route_junction->junction_mapping_id = $mapping->id;
                    $route_junction->starting_hub_id = $previous;
                    $route_junction->ending_hub_id = $rj->ending_hub_id;
                    $route_junction->save();

                    $previous = $rj->ending_hub_id;

                    $vehicles = V2JunctionVehicles::where('junction_route_id', $rj->id)->get();

                    foreach ($vehicles as $vehicle) {
                        $newRouteVehicles2[] = [
                            'junction_route_id' => $route_junction->id,
                            'vehicle_id' => $vehicle->vehicle_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

            }

            V2Junctions::insert($newJunctions2);
            V2JunctionVehicles::insert($newRouteVehicles2);

            //for creating second way mapping between the newly created hub and the closest hub
            $mapping2 = new V2JunctionMapping();

            $mapping2->origin_id = $closestHubId;
            $mapping2->destination_id = $city->id;
            $mapping2->updated_by = $authId;
            $mapping2->save();

            $route_junction2 = new V2JunctionRoutes();
            $route_junction2->junction_mapping_id = $mapping2->id;
            $route_junction2->starting_hub_id = $mapping2->origin_id;
            $route_junction2->ending_hub_id = $mapping2->destination_id;
            $route_junction2->save();

            $singleRouteVehicles2 = [];
            foreach ($requestVehicles as $vehicle) {
                $singleRouteVehicles2[] = [
                    'junction_route_id' => $route_junction2->id,
                    'vehicle_id' => $vehicle,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            V2JunctionVehicles::insert($singleRouteVehicles2);

            // --------------x-------------------x-------------x---------------

            // --------------x-------------------x-------------x----------

            // Commit the transaction
            DB::commit();

        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage(), [
                'exception' => $e,
                'stack' => $e->getTraceAsString()
            ]);
            // Rollback the transaction if any error occurs
            DB::rollBack();
            throw $e;
        }
    }
}
