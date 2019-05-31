
@if (!$shipment->packaging_material_request)
    @if ($shipment->weight_charges != null)
        <h6 style="text-align: left"><b>Weight</b></h6>
        <table class="table table-sm table-bordered">
            <thead>
            <tr role="row" class="bg-primary white">
                <th class="border-primary border-darken-1 align-middle text-center">Estimated</th>
                <th class="border-primary border-darken-1 align-middle text-center">Actual</th>
                <th class="border-primary border-darken-1 align-middle text-center">Chargeable</th>
                <th class="border-primary border-darken-1 align-middle text-center">Charges</th>
                @if ($shipment->fuel_surcharge != null)
                    <th class="border-primary border-darken-1 align-middle text-center">Fuel Surcharge</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @if (($shipment->weight_charges == null))
                <tr>
                    <td>No Charges!</td>
                </tr>
            @else
                <tr>
                    <td class="align-middle text-center"> {{ $shipment->estimated_weight }}</td>
                    <td class="align-middle text-center"> {{ $shipment->actual_weight }}</td>
                    <td class="align-middle text-center"> {{ $shipment->chargeable_weight }}</td>
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->weight_charges) }}</td>
                    @if ($shipment->fuel_surcharge != null)
                        <td class="align-middle text-center">Rs. {{ floatval($shipment->fuel_surcharge) }}</td>
                    @endif
                </tr>
            @endif
            </tbody>
        </table>
    @endif
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
    @if ($shipment->return_charges != null)
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
                        <td class="align-middle text-center">Rs. {{ floatval($shipment->intercept_charges) }}</td>
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