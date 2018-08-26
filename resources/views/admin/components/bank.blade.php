<h1>Bank Information of {{$user->name}}</h1>
<div class="bs-callout-primary callout-border-left callout-transparent p-1">

    <table class="table table-sm table-bordered">

        <tbody>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">Bank Name</th>
            <td>{{$bank->bank_name}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">Bank Branch</th>
            <td>{{$bank->bank_branch}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">Account Number</th>
            <td>{{$bank->account_no}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">Account Title</th>
            <td>{{$bank->account_title}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">IBAN Number</th>
            <td>{{$bank->iban}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">City Name</th>
            <td>{{$bank->city->name}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">Payment Mode</th>
            <td>{{$bank->payment_mode}}</td>
        </tr>
        <tr class="bg-primary white border-primary border-darken-1">
            <th scope="row">Payment Cycle</th>
            <td>{{$bank->payment_cycle}}</td>
        </tr>

        </tbody>
    </table>
</div>

