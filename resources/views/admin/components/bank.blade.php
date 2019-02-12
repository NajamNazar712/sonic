<h1>Bank Information of {{$user->name}}</h1>

<table class="table table-sm table-bordered mb-1">
    <tbody>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Bank Name</th>
            <td class="align-middle text-center">{{$bank->bank_name}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Bank Branch</th>
            <td class="align-middle text-center">{{$bank->bank_branch}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Account Number</th>
            <td class="align-middle text-center">{{$bank->account_no}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Account Title</th>
            <td class="align-middle text-center">{{$bank->account_title}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">IBAN Number</th>
            <td class="align-middle text-center">{{$bank->iban}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">City Name</th>
            <td class="align-middle text-center">{{$bank->city->name}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Payment Cycle</th>
            <td class="align-middle text-center">{{$bank->payment_cycle}}</td>
        </tr>
        @if($bank->invoicing_cycle_id != null)
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Invoicing Cycle </th>
            <td class="align-middle text-center">{{$bank->invoicing->name}}</td>
        </tr>
        @if($bank->invoicing_cycle_id == 1 || $bank->invoicing_cycle_id == 3)
            <tr role="row">
                <th class="border-primary border-darken-1 align-middle text-center">Generation Date</th>
                <td class="align-middle text-center">{{$bank->generation_date}}</td>
            </tr>
            @endif
        @endif
    @if($user->account_type_id == 2)
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Billing Person Name</th>
            <td class="align-middle text-center">{{$bank->billing_person_name}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Billing Person Phone</th>
            <td class="align-middle text-center">{{$bank->billing_person_phone}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Billing Person Email</th>
            <td class="align-middle text-center">{{$bank->billing_person_email}}</td>
        </tr>
        <tr role="row">
            <th class="border-primary border-darken-1 align-middle text-center">Billing Address</th>
            <td class="align-middle text-center">{{$bank->billing_address}}</td>
        </tr>
        @endif
    </tbody>
</table>

