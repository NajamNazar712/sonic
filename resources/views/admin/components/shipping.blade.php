<h1>Shipping Information of {{$user->name}}</h1>

@foreach ($shipping as $shipping_details)
	<div class="bs-callout-primary callout-border-left callout-transparent p-1">
	    <p><b>Pickup Address :</b> {{$shipping_details->pickup_address}}</p>
	    <p><b>Person of Contact :</b> {{$shipping_details->poc}}</p>
	    <p><b>Phone :</b> {{$shipping_details->phone}}</p>
	    <p><b>Email :</b> {{$shipping_details->email}}</p>
	    <p><b>City :</b> {{$shipping_details->city->city_name}}</p>
	</div>
@endforeach