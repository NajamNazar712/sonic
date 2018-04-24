<h1>Bank Information of {{$user->name}}</h1>
<div class="bs-callout-primary callout-border-left callout-transparent p-1">

    <p><b>Bank Name :</b> {{$bank->bank_name}}</p>
    <p><b>Bank Branch :</b> {{$bank->bank_branch}}</p>
    <p><b>Account No. :</b> {{$bank->account_no}}</p>
    <p><b>Account Title :</b> {{$bank->account_title}}</p>
    <p><b>IBAN No. :</b> {{$bank->iban}}</p>
    <p><b>City :</b> {{$bank->city->city_name}}</p>
    <p><b>Payment Mode :</b> {{$bank->payment_mode}}</p>
    <p><b>Payment Cycle :</b> {{$bank->payment_cycle}}</p>
</div>