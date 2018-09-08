<table class="table table-sm table-bordered">
    <thead>
        <tr role="row" class="bg-primary white">
            @if ($shipment->weight_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">Weight</th>
            @endif

            @if ($shipment->cash_handling_charges != null && $shipment->return_charges == null)
            <th class="border-primary border-darken-1 align-middle text-center">Cash Handling</th>
            @endif

            @if ($shipment->insurance_charges != null)
            <th class="border-primary border-darken-1 align-middle text-center">Insurance</th>
            @endif

            @if ($shipment->return_charges != null)
            <th class="border-primary border-darken-1 align-middle text-center">Return</th>
            @endif

            @if ($shipment->fuel_surcharge != null)
            <th class="border-primary border-darken-1 align-middle text-center">Fuel</th>
            @endif

            @if ($shipment->replacement_charges != null)
            <th class="border-primary border-darken-1 align-middle text-center">Replacement</th>
            @endif

            @if ($shipment->try_and_buy_charges != null)
            <th class="border-primary border-darken-1 align-middle text-center">Try & Buy</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if (($shipment->weight_charges == null) && ($shipment->cash_handling_charges == null) && ($shipment->insurance_charges == null) && ($shipment->return_charges == null) && ($shipment->fuel_surcharge == null) && ($shipment->replacement_charges == null) && ($shipment->try_and_buy_charges == null))
            <tr>
                <td>No Charges!</td>
            </tr>
        @else
            <tr>
                @if ($shipment->weight_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->weight_charges) }}</td>
                @endif

                @if ($shipment->cash_handling_charges != null && $shipment->return_charges == null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->weight_charges) }}</td>
                @endif

                @if ($shipment->insurance_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->insurance_charges) }}</td>
                @endif

                @if ($shipment->return_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->return_charges) }}</td>
                @endif

                @if ($shipment->fuel_surcharge != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->fuel_surcharge) }}</td>
                @endif

                @if ($shipment->replacement_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->replacement_charges) }}</td>
                @endif

                @if ($shipment->try_and_buy_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->try_and_buy_charges) }}</td>
                @endif
            </tr>
        @endif
    </tbody>
</table>