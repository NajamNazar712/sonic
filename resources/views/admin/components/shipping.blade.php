<h1>Shipping Information of {{$user->name}}</h1>

@foreach ($shipping as $shipping_details)
	<table class="table table-sm table-bordered mb-1">
		<tbody>
			<tr role="row">
				<th class="border-primary border-darken-1 align-middle text-center">Pickup Address</th>
				<td class="align-middle text-center">{{$shipping_details->pickup_address}}</td>
			</tr>
			<tr role="row">
				<th class="border-primary border-darken-1 align-middle text-center">Person of Contact</th>
				<td class="align-middle text-center">{{$shipping_details->poc}}</td>
			</tr>
			<tr role="row">
				<th class="border-primary border-darken-1 align-middle text-center">Phone Number</th>
				<td class="align-middle text-center">{{$shipping_details->phone}}</td>
			</tr>
			<tr role="row">
				<th class="border-primary border-darken-1 align-middle text-center">Email Address</th>
				<td class="align-middle text-center">{{$shipping_details->email}}</td>
			</tr>
			<tr role="row">
				<th class="border-primary border-darken-1 align-middle text-center">City Name</th>
				<td class="align-middle text-center">{{$shipping_details->city->name}}</td>
			</tr>
		</tbody>
	</table>
@endforeach