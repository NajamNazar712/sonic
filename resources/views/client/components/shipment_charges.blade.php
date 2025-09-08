<table class="table table-sm table-bordered">
    <thead>
        <tr role="row" class="bg-primary white">
            @if (!$shipment->packaging_material_request)
                @if (
                    ($exists && !in_array($shipment->current_status, [20,21,22,23,24,25]) && $shipment->weight_charges !== null) 
                    || 
                    (!$exists && $shipment->weight_charges !== null)
                )
                    <th class="border-primary border-darken-1 align-middle text-center">Weight</th>
                @endif

                @if ($shipment->cash_handling_charges != null && $shipment->return_charges == null)
                <th class="border-primary border-darken-1 align-middle text-center">Cash Handling</th>
                @endif

                @if ($shipment->insurance_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">Insurance</th>
                @endif

                @if (($exists && !in_array($shipment->current_status, [20,21,22,23,24,25]) && $shipment->return_charges != null) 
                    || 
                    (!$exists && $shipment->return_charges != null)
                )
                <th class="border-primary border-darken-1 align-middle text-center">Return</th>
                @endif

                @if (($exists && !in_array($shipment->current_status, [20,21,22,23,24,25]) && $shipment->fuel_surcharge != null)
                    ||
                    (!$exists && $shipment->fuel_surcharge != null)
                )
                <th class="border-primary border-darken-1 align-middle text-center">Fuel</th>
                @endif

                @if ($shipment->replacement_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">Replacement</th>
                @endif

                @if ($shipment->try_and_buy_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">Try & Buy</th>
                @endif

                @if ($shipment->intercept_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">Intercept</th>
                @endif

                @if ($shipment->nsa_osa_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">NSA/OSA Charges</th>
                @endif
                @if ($shipment->nsa_osa_charges != null)
                <th class="border-primary border-darken-1 align-middle text-center">NSA/OSA Charges</th>
                @endif
                @if($shipment->account_type == 1)
                    @if($shipment->pps_sms_charge != null || $shipment->dps_sms_charge != null)
                    <th class="border-primary border-darken-1 align-middle text-center">SMS Charges</th>   
                    @endif
                
                @elseif($shipment->account_type == 2)
                    @if($shipment->pis_sms_charge != null || $shipment->is_sms_charge != null)
                    <th class="border-primary border-darken-1 align-middle text-center">SMS Charges</th>   
                    @endif
                @endif
            @else
                <th class="border-primary border-darken-1 align-middle text-center">Packaging Material</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if (($shipment->weight_charges == null) && ($shipment->cash_handling_charges == null) && ($shipment->insurance_charges == null) && ($shipment->return_charges == null) && ($shipment->fuel_surcharge == null) && ($shipment->replacement_charges == null) && ($shipment->try_and_buy_charges == null) && ($shipment->intercept_charges == null) && ($shipment->nsa_osa_charges == null) && ($shipment->packaging_material_charges == null))
            <tr>
                <td>No Charges!</td>
            </tr>
        @else
            <tr>
                @if  (
                    ($exists && !in_array($shipment->current_status, [20,21,22,23,24,25]) && $shipment->weight_charges !== null) 
                    || 
                    (!$exists && $shipment->weight_charges !== null)
                )
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->weight_charges) }}</td>
                @endif

                @if ($shipment->cash_handling_charges != null && $shipment->return_charges == null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->cash_handling_charges) }}</td>
                @endif

                @if ($shipment->insurance_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->insurance_charges) }}</td>
                @endif

                @if (($exists && !in_array($shipment->current_status, [20,21,22,23,24,25]) && $shipment->return_charges != null) 
                    || 
                    (!$exists && $shipment->return_charges != null)
                )
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->return_charges) }}</td>
                @endif

                @if(($exists && !in_array($shipment->current_status, [20,21,22,23,24,25]) && $shipment->fuel_surcharge != null)
                    ||
                    (!$exists && $shipment->fuel_surcharge != null)
                )
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->fuel_surcharge) }}</td>
                @endif

                @if ($shipment->replacement_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->replacement_charges) }}</td>
                @endif

                @if ($shipment->try_and_buy_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->try_and_buy_charges) }}</td>
                @endif

                @if ($shipment->intercept_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->intercept_charges) }}</td>
                @endif

                @if ($shipment->nsa_osa_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->nsa_osa_charges) }}</td>
                @endif

                @if($shipment->account_type == 1)
                    @if($shipment->pps_sms_charge != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->pps_sms_charge) }}</td>
                    @elseif($shipment->dps_sms_charge != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->dps_sms_charge) }}</td>
                    @endif
                
                @elseif($shipment->account_type == 2)
                    @if($shipment->pis_sms_charge != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->pis_sms_charge) }}</td>
                    @elseif($shipment->is_sms_charge != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->is_sms_charge) }}</td>
                    @endif
                @endif

                @if ($shipment->packaging_material_charges != null)
                    <td class="align-middle text-center">Rs. {{ floatval($shipment->packaging_material_charges) }}</td>
                @endif
            </tr>
        @endif
    </tbody>
</table>