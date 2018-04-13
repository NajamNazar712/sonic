<h1>Shipping Information Of {{$user->name}}</h1>
<div class="bs-callout-primary callout-border-left callout-transparent p-1">

    <p><b>Pickup Address :</b> {{$shipping->pickup_address}}</p>
    <p><b>Person Of Contact :</b> {{$shipping->poc}}</p>
    <p><b>Phone :</b> {{$shipping->phone}}</p>
    <p><b>Email :</b> {{$shipping->email}}</p>
    <p><b>City :</b> {{$shipping->city->city_name}}</p>

</div>