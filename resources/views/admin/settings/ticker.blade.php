@extends('admin.layout.master')

@section('title', 'Ticker')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Ticker
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body" style="height: 400px">
							@include('admin.inc.messages')

							<div class="justify-content-center">
								{{-- <div class="col-12 col-sm-12 col-md-12 col-lg-12">  value="{{ $admin_ticker->start_date ?? ''}}"--}}
									<form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.ticker.store') }}" novalidate="novalidate">
										{{ csrf_field() }}
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<input type="text" name="admin_ticker" class="form-control admin_ticker" placeholder="Admin Ticker" value="{{ $admin_ticker->description ?? '' }}">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<input type="text" name="admin_start_date" id="admin_start_date" placeholder="Start Date" class="form-control bg-primary border-primary white rounded-right"  >
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<input type="text" name="admin_end_date" id="admin_end_date" class="form-control bg-primary border-primary white rounded-right" placeholder="End Date" >
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="admin_start_time">
														<option selected>Start Time</option>
														 <optgroup label="AM">
															<option value="12:00">12:00 AM</option>
															<option value="01:00">01:00 AM</option>
															<option value="02:00">02:00 AM</option>
															<option value="03:00">03:00 AM</option>
															<option value="04:00">04:00 AM</option>
															<option value="05:00">05:00 AM</option>
															<option value="06:00">06:00 AM</option>
															<option value="07:00">07:00 AM</option>
															<option value="08:00">08:00 AM</option>
															<option value="09:00">09:00 AM</option>
															<option value="10:00">10:00 AM</option>
															<option value="11:00">11:00 AM</option>
														</optgroup>
														<optgroup label="PM">
															<option value="12:00">12:00 PM</option>
															<option value="01:00">01:00 PM</option>
															<option value="02:00">02:00 PM</option>
															<option value="03:00">03:00 PM</option>
															<option value="04:00">04:00 PM</option>
															<option value="05:00">05:00 PM</option>
															<option value="06:00">06:00 PM</option>
															<option value="07:00">07:00 PM</option>
															<option value="08:00">08:00 PM</option>
															<option value="09:00">09:00 PM</option>
															<option value="10:00">10:00 PM</option>
															<option value="11:00">11:00 PM</option>
														</optgroup>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="admin_end_time">
														<option selected>End Time</option>
														 <optgroup label="AM">
															<option value="12:00">12:00 AM</option>
															<option value="01:00">01:00 AM</option>
															<option value="02:00">02:00 AM</option>
															<option value="03:00">03:00 AM</option>
															<option value="04:00">04:00 AM</option>
															<option value="05:00">05:00 AM</option>
															<option value="06:00">06:00 AM</option>
															<option value="07:00">07:00 AM</option>
															<option value="08:00">08:00 AM</option>
															<option value="09:00">09:00 AM</option>
															<option value="10:00">10:00 AM</option>
															<option value="11:00">11:00 AM</option>
														</optgroup>
														<optgroup label="PM">
															<option value="12:00">12:00 PM</option>
															<option value="01:00">01:00 PM</option>
															<option value="02:00">02:00 PM</option>
															<option value="03:00">03:00 PM</option>
															<option value="04:00">04:00 PM</option>
															<option value="05:00">05:00 PM</option>
															<option value="06:00">06:00 PM</option>
															<option value="07:00">07:00 PM</option>
															<option value="08:00">08:00 PM</option>
															<option value="09:00">09:00 PM</option>
															<option value="10:00">10:00 PM</option>
															<option value="11:00">11:00 PM</option>
														</optgroup>
													</select>
												</div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<input type="text" name="shipper_ticker" class="form-control shipper_ticker" placeholder="Shipper Ticker" >
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<input type="text" name="shipper_start_date" id="shipper_start_date" placeholder="Start Date" class="form-control bg-primary border-primary white rounded-right">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<input type="text" name="shipper_end_date" id="shipper_end_date" class="form-control bg-primary border-primary white rounded-right" placeholder="End Date" >
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="shipper_start_time">
														<option selected>Start Time</option>
														 <optgroup label="AM">
															<option value="12:00">12:00 AM</option>
															<option value="01:00">01:00 AM</option>
															<option value="02:00">02:00 AM</option>
															<option value="03:00">03:00 AM</option>
															<option value="04:00">04:00 AM</option>
															<option value="05:00">05:00 AM</option>
															<option value="06:00">06:00 AM</option>
															<option value="07:00">07:00 AM</option>
															<option value="08:00">08:00 AM</option>
															<option value="09:00">09:00 AM</option>
															<option value="10:00">10:00 AM</option>
															<option value="11:00">11:00 AM</option>
														</optgroup>
														<optgroup label="PM">
															<option value="12:00">12:00 PM</option>
															<option value="01:00">01:00 PM</option>
															<option value="02:00">02:00 PM</option>
															<option value="03:00">03:00 PM</option>
															<option value="04:00">04:00 PM</option>
															<option value="05:00">05:00 PM</option>
															<option value="06:00">06:00 PM</option>
															<option value="07:00">07:00 PM</option>
															<option value="08:00">08:00 PM</option>
															<option value="09:00">09:00 PM</option>
															<option value="10:00">10:00 PM</option>
															<option value="11:00">11:00 PM</option>
														</optgroup>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<select class="form-control" name="shipper_end_time">
														<option selected>End Time</option>
														 <optgroup label="AM">
															<option value="12:00">12:00 AM</option>
															<option value="01:00">01:00 AM</option>
															<option value="02:00">02:00 AM</option>
															<option value="03:00">03:00 AM</option>
															<option value="04:00">04:00 AM</option>
															<option value="05:00">05:00 AM</option>
															<option value="06:00">06:00 AM</option>
															<option value="07:00">07:00 AM</option>
															<option value="08:00">08:00 AM</option>
															<option value="09:00">09:00 AM</option>
															<option value="10:00">10:00 AM</option>
															<option value="11:00">11:00 AM</option>
														</optgroup>
														<optgroup label="PM">
															<option value="12:00">12:00 PM</option>
															<option value="01:00">01:00 PM</option>
															<option value="02:00">02:00 PM</option>
															<option value="03:00">03:00 PM</option>
															<option value="04:00">04:00 PM</option>
															<option value="05:00">05:00 PM</option>
															<option value="06:00">06:00 PM</option>
															<option value="07:00">07:00 PM</option>
															<option value="08:00">08:00 PM</option>
															<option value="09:00">09:00 PM</option>
															<option value="10:00">10:00 PM</option>
															<option value="11:00">11:00 PM</option>
														</optgroup>
													</select>
												</div>
											</div>

										</div>
										
										<button type="submit" class="btn btn-primary">Update</button>
									</form>
								{{-- </div> --}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

	<script>
		var admin_start_date = $('#admin_start_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
            // onSet: function (context) {
            //     if (context.select) {
            //         $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
            //     }
            // }
        });

		var admin_end_date = $('#admin_end_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
            // onSet: function (context) {
            //     if (context.select) {
            //         $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
            //     }
            // }
        });
		
		var shipper_start_date = $('#shipper_start_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
            // onSet: function (context) {
            //     if (context.select) {
            //         $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
            //     }
            // }
        });
		
		var shipper_end_date = $('#shipper_end_date').pickadate({
            firstDay: 1,
            clear: '',
            min: '{{ Carbon\Carbon::now() }}',
            format: 'yyyy-mm-dd',
            selectYears: true,
            selectMonths: true,
            formatSubmit: 'yyyy-mm-dd',
            hiddenSuffix: '_formatted',
            // onSet: function (context) {
            //     if (context.select) {
            //         $('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
            //     }
            // }
        });


		
	</script>
@endsection