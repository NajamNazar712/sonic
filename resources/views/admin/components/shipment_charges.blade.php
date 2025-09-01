
@if (!$shipment->packaging_material_request)
    @if (($shipment->shipment_type == 1 && $shipment->weight_charges != null ) || ($shipment->shipment_type == 2 && $retail_shipment->weight_charges != null))
        <h6 style="text-align: left"><b>Weight</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Estimated</th>
                <th class="border-primary border-darken-1 align-middle text-center">Actual</th>
                @if($shipment->shipment_type == 1)
                    <th class="border-primary border-darken-1 align-middle text-center">Chargeable</th>
                @endif
                @if(($shipment->shipment_type == 1 && $shipment->length != null && $shipment->breadth != null && $shipment->height != null) || ($shipment->shipment_type == 2 && $retail_shipment->length != null && $retail_shipment->breadth != null && $retail_shipment->height != null))
                    <th class="border-primary border-darken-1 align-middle text-center">Length</th>
                    <th class="border-primary border-darken-1 align-middle text-center">Breadth</th>
                    <th class="border-primary border-darken-1 align-middle text-center">Height</th>
                @endif

                @if ($shipment->shipment_type == 1)
                    @if (
                        ($exists && !in_array($shipment->shipper_status_id, [20,21,22,23,24,25]))
                        || 
                        (!$exists)
                    )
                        <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
                    @endif
                @else
                    <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
                @endif

                @if($shipment->shipment_type == 1)
                    @if (
                        ($exists && !in_array($shipment->shipper_status_id, [20,21,22,23,24,25]) &&  $shipment->fuel_surcharge != null ) 
                        || 
                        (!$exists &&  $shipment->fuel_surcharge != null )
                    )
                        <th class="border-primary border-darken-1 align-middle text-center">Fuel Surcharge</th>

                    @endif
                @else
                    @if ($retail_shipment->fuel_surcharge != null)
                        <th class="border-primary border-darken-1 align-middle text-center">Fuel Surcharge</th>
                    @endif
                @endif
            </tr>
            </thead>
            <tbody>
            @if (($shipment->shipment_type == 1 && $shipment->weight_charges == null) || ($shipment->shipment_type == 2 && $retail_shipment->weight_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center"> {{ $shipment->estimated_weight }}</td>
                    <td class="align-middle text-center"> {{ $shipment->actual_weight }}</td>
                    @if($shipment->shipment_type == 1)
                        <td class="align-middle text-center"> {{ $shipment->chargeable_weight }}</td>
                    @endif

                    @if($shipment->length != null && $shipment->breadth != null && $shipment->height != null)
                        <td class="align-middle text-center"> {{ $shipment->length }}</td>
                        <td class="align-middle text-center"> {{ $shipment->breadth }}</td>
                        <td class="align-middle text-center"> {{ $shipment->height }}</td>
                    @endif
                    @if($shipment->shipment_type == 1)
                        @if  (
                            ($exists && !in_array($shipment->shipper_status_id, [20,21,22,23,24,25]) ) 
                            || 
                            (!$exists)
                        )
                            <td class="align-middle text-center">Rs. {{ floatval($shipment->weight_charges) }}</td>
                        @endif

                    @else
                        <td class="align-middle text-center">Rs. {{ floatval($retail_shipment->weight_charges) }}</td>
                    @endif
                    @if($shipment->shipment_type == 1)
                        @if (
                            ($exists && !in_array($shipment->shipper_status_id, [20,21,22,23,24,25]) &&  $shipment->fuel_surcharge != null ) 
                            || 
                            (!$exists &&  $shipment->fuel_surcharge != null )
                        )
                            <td class="align-middle text-center">Rs. {{ floatval($shipment->fuel_surcharge) }}</td>
                        @endif
                    @else
                        @if ($retail_shipment->fuel_surcharge != null)
                            <td class="align-middle text-center">Rs. {{ floatval($retail_shipment->fuel_surcharge) }}</td>
                        @endif
                    @endif
                </tr>
            @endif
            </tbody>
        </table>
    @endif
    @if($shipment->shipment_type == 1)
        @if ($shipment->cash_handling_charges != null && $shipment->return_charges == null)
        <div style="margin-top:15px">
        <h6 style="text-align: left"><b>Cash</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Collection Amount</th>
                <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
                @if ($shipment->insurance_charges != null)
                    <th class="border-primary border-darken-1 align-middle text-center">Insurance Charges</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @if (($shipment->cash_handling_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->amount) }}</td>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->cash_handling_charges) }}</td>
                    @if ($shipment->insurance_charges != null)
                        <td class="align-middle text-center">Rs. {{ floatval($shipment->insurance_charges) }}</td>
                    @endif
                </tr>
            @endif
            </tbody>
        </table>
        </div>
    @endif
    @else
        @if ($shipment->amount != null)
            <div style="margin-top:15px">
                <h6 style="text-align: left"><b>Cash</b></h6>
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1 align-middle text-center">Collection Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="align-middle text-center">Rs. {{ floatval($shipment->amount) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif
    @if (
            ($exists && !in_array($shipment->shipper_status_id, [20,21,22,23,24,25]) && $shipment->return_charges !== null) 
            || 
            (!$exists && $shipment->return_charges !== null)
        )
        <div style="margin-top:15px">
        <h6 style="text-align: left"><b>Return</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
            </tr>
            </thead>
            <tbody>
            @if (($shipment->return_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->return_charges) }}</td>
                </tr>
            @endif
            </tbody>
        </table>
        </div>
    @endif

    @if ($shipment->replacement_charges != null)
        <div style="margin-top:15px">
        <h6 style="text-align: left"><b>Replacement</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Weight</th>
                <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
            </tr>
            </thead>
            <tbody>
            @if (($shipment->replacement_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center">{{ $shipment->replacement_weight }}</td>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->replacement_charges) }}</td>
                </tr>
            @endif
            </tbody>
        </table>
        </div>
    @endif

    @if ($shipment->try_and_buy_charges != null)
        <div style="margin-top:15px">
        <h6 style="text-align: left"><b>Try & Buy</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
            </tr>
            </thead>
            <tbody>
            @if (($shipment->try_and_buy_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->try_and_buy_charges) }}</td>
                </tr>
            @endif
            </tbody>
        </table>
        </div>
    @endif
    @if ($shipment->intercept_charges != null)
        <div style="margin-top:15px">
        <h6 style="text-align: left"><b>Intercept</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
            </tr>
            </thead>
            <tbody>
            @if (($shipment->intercept_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->intercept_charges) }}</td>
                </tr>
            @endif
            </tbody>
        </table>
        </div>
    @endif
    @if ($shipment->nsa_osa_charges != null)
        <div style="margin-top:15px">
            <h6 style="text-align: left"><b>NSA/OSA</b></h6>
            <table class="table table-sm table-bordered">
                <thead>
                <tr role="row" class="bg-primary white">
                    <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-middle text-center">Rs. {{ floatval($shipment->nsa_osa_charges) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
@else
    <div style="margin-top:15px">
    <h6 style="text-align: left"><b>Packaging Material</b></h6>
    <table class="table table-sm table-bordered">
        <thead>
        <tr role="row" class="bg-primary white">
            <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
        </tr>
        </thead>
        <tbody>
        @if (($shipment->packaging_material_charges == null))
            <tr>
                <td>No Charges!</td>
            </tr>
        @else
            <tr>
                <td class="align-middle text-center">Rs. {{ floatval($shipment->packaging_material_charges) }}</td>
            </tr>
        @endif
        </tbody>
    </table>
    </div>
@endif