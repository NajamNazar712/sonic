
<div class="bs-callout-primary  callout-transparent p-1">

    <table class="table table-sm table-bordered">

        <tbody>
        @if($shipment->weight_charges != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Weight Charges</th>
                <td>{{$shipment->weight_charges}}</td>
            </tr>
        @endif
        @if($shipment->cash_handling_charges != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Cash Handling Charges</th>
                <td>{{$shipment->cash_handling_charges}}</td>
            </tr>
        @endif
        @if($shipment->insurance_charges != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Insurance Charges</th>
                <td>{{$shipment->insurance_charges}}</td>
            </tr>
        @endif
        @if($shipment->return_charges != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Return Charges</th>
                <td>{{$shipment->return_charges}}</td>
            </tr>
        @endif
        @if($shipment->fuel_surcharge != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Fuel Charges</th>
                <td>{{$shipment->fuel_surcharge}}</td>
            </tr>
        @endif
        @if($shipment->replacement_charges != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Replacement Charges</th>
                <td>{{$shipment->replacement_charges}}</td>
            </tr>
        @endif
        @if($shipment->try_and_buy_charges != null)
            <tr class="bg-primary white border-primary border-darken-1">
                <th scope="row">Try & Buy Charges</th>
                <td>{{$shipment->try_and_buy_charges}}</td>
            </tr>
        @endif
        @if(($shipment->weight_charges == null) && ($shipment->cash_handling_charges == null) && ($shipment->insurance_charges == null) && ($shipment->return_charges == null) && ($shipment->fuel_surcharge == null) && ($shipment->replacement_charges == null) && ($shipment->try_and_buy_charges == null))
            <tr class="bg-primary white border-primary border-darken-1">
                No charges found!
            </tr>
        @endif

        </tbody>
    </table>
</div>

