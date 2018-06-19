<h1>Shipping Information of {{$user->name}}</h1>

@foreach ($shipping as $shipping_details)
	<div class="bs-callout-primary callout-border-left callout-transparent p-1">
	    {{--<p><b>Pickup Address:</b> {{$shipping_details->pickup_address}}</p>--}}
	    {{--<p><b>Person of Contact:</b> {{$shipping_details->poc}}</p>--}}
	    {{--<p><b>Phone Number:</b> {{$shipping_details->phone}}</p>--}}
	    {{--<p><b>Email Address:</b> {{$shipping_details->email}}</p>--}}
	    {{--<p><b>City Name:</b> {{$shipping_details->city->name}}</p>--}}
	{{--</div>--}}

	<table class="table table-sm table-bordered">

		<tbody>
		<tr class="bg-primary white border-primary border-darken-1">
			<th scope="row">Pickup Address</th>
			<td>{{$shipping_details->pickup_address}}</td>
		</tr>
		<tr class="bg-primary white border-primary border-darken-1">
			<th scope="row">Person of Contact</th>
			<td>{{$shipping_details->poc}}</td>
		</tr>
		<tr class="bg-primary white border-primary border-darken-1">
			<th scope="row">Phone Number</th>
			<td>{{$shipping_details->phone}}</td>
		</tr>
		<tr class="bg-primary white border-primary border-darken-1">
			<th scope="row">Email Address</th>
			<td>{{$shipping_details->email}}</td>
		</tr>
		<tr class="bg-primary white border-primary border-darken-1">
			<th scope="row">City Name</th>
			<td>{{$shipping_details->city->name}}</td>
		</tr>

		</tbody>
	</table>
	</div>
@endforeach