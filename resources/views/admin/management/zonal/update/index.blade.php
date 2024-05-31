@extends('admin.layout.master')

@section('title', 'Update Zone')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Update Zone
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="zone_form" class="form-horizontal" method="POST" action="{{ route('admin.management.zonal.update.store', ['id' => $zone->id]) }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-3">
										<div class="form-group">
											<label>Name</label>
											{{-- <input type="text" name="name" class="form-control subject" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required" value="{{ $zone->name }}"> --}}
											<input type="text" name="name" id="zone_name" class="form-control subject" placeholder="Name*" data-rule-required="true"
											data-rule-remote="{{ route('admin.management.zonal.check_zone_name',$zone->id) }}" data-msg-remote="Zone Name must be unique" data-msg-required="Name is required" value="{{ $zone->name }}">
										</div>
									</div>

									<div class="col-3">
										<div class="form-group">
											<label>GST</label>
											<input type="text" name="gst" class="form-control gst" placeholder="GST*" data-rule-required="true" data-msg-required="GST is required"value="{{ $zone->gst }}">
										</div>
									</div>
									<div class="col-2">
										<div class="form-group mt-3" style="text-align: center">
											<label for="" class="">Individual City GST</label>
											<input type="checkbox" id="individual_city_gst" name="individual_city_gst" class="switchery"
												   data-size="sm" data-switchery="true">
											<input id="individual_city_gst_bit" value="0" name="individual_city_gst_bit" hidden>
										</div>
									</div>
									<div class="col-1">
										<div class="form-group mt-2" style="text-align: right">
											<label  id="add_cities" class="btn btn-primary"
													 data-size="sm"> Add Cities GST</label>
										</div>
									</div>
									<div class="col-1">
										<div class="form-group mt-2">
											<label  id="add_cities_in_zone" class="btn btn-primary"
													 data-size="sm"> Add Cities</label>
										</div>
									</div> 

									<div class="col-12" style="text-align: center">
										<h3 class="form-section mb-2">City Class Categorization</h3>
									</div>

									<div class="col-12 col-lg-6">
										<h3 class="form-section mb-2">Rush/Same-day</h3>

										@foreach($cities as $city)
											<div class="form-group">
												<div class="row align-items-center justify-content-between">
													<div class="col">
														<label class="mb-0 mr-1">{{ $city->name }}</label>
													</div>

													<div class="col text-right">
														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="0" data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities[$city->id]) && $zone_class_cities[$city->id] == 0) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class A</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="1"data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities[$city->id]) && $zone_class_cities[$city->id] == 1) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class B</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="2"data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities[$city->id]) && $zone_class_cities[$city->id] == 2) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class C</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="3"data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities[$city->id]) && $zone_class_cities[$city->id] == 3) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class D</label>
														</fieldset>
													</div>
												</div>
											</div>

											<hr/>
										@endforeach
									</div>

									<div class="col-12 col-lg-6 mt-2 mt-lg-0">
										<h3 class="form-section mb-2">Saver Plus/Swift</h3>

										@foreach($cities as $city)
											<div class="form-group">
												<div class="row align-items-center justify-content-between">
													<div class="col">
														<label class="mb-0 mr-1">{{ $city->name }}</label>
													</div>

													<div class="col text-right">
														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="0" data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities_cor[$city->id]) && $zone_class_cities_cor[$city->id] == 0) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class A</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="1"data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities_cor[$city->id]) && $zone_class_cities_cor[$city->id] == 1) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class B</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="2"data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities_cor[$city->id]) && $zone_class_cities_cor[$city->id] == 2) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class C</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="3"data-rule-required="true" data-msg-required="Class is required" @if (isset($zone_class_cities_cor[$city->id]) && $zone_class_cities_cor[$city->id] == 3) checked="checked" @endif>
															<label for="city_class_{{ $city->id }}">Class D</label>
														</fieldset>
													</div>
												</div>
											</div>

											<hr/>
										@endforeach
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Update</button>
										</div>
									</div>
								</div>
							</form>

							{{--city wise gst--}}
							<div class="modal fade" id="city_wise_gst" role="dialog" aria-labelledby="city_wise_gst_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="city_wise_gst_title">Cities Wise GST</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
											<div class="container">
												<div class="row">
													<div class="col-md-5">
														<select name="zone_city_enter" class="select2" id="zone_city_enter"
																data-rule-required="true" data-msg-required="City is required">
															@foreach($cities as $city)
																<option value="{{ $city->id }}">{{ $city->name }}</option>
															@endforeach
														</select>
													</div>
													<div class="col-md-5">
														<input type="text" name="zone_city_gst_enter" id="zone_city_gst_enter" value="" class="form-control gst" placeholder="GST*" data-rule-required="true" data-msg-required="GST is required">
													</div>
													<div class="col-md-2">
														<input type="button" class="btn btn-md btn-primary " id="addrow" value="Add Row" />
													</div>
												</div>
												<div class="row mt-2">
													<table id="myTable" class=" table order-list">
														<thead>
														<tr>
															<td>Zone</td>
															<td>City</td>
															<td>GST</td>
														</tr>
														</thead>
														<tbody>
														@foreach($zone_cities_gst as $zcg)
															<tr>
																<td class="col-sm-4">
																	<input type="text" name="zone_id[]"
																		   id="zone_id[]"
																		   value="{{$zcg->zone_name}}"
																		   class="form-control" readonly/>
																	<input type="hidden" name="zone_id_hidden[]"
																		   id="zone_id_hidden[]"
																		   value="{{$zcg->zone_id}}"
																		   class="form-control" readonly/>
																</td>
																<td class="col-sm-4">
																	<input type="text" name="zone_city_id[]"
																		   value="{{$zcg->city_name}}"
																		   class="form-control" readonly/>
																	<input type="hidden" name="zone_city_id_hidden[]"
																		   value="{{$zcg->city_id}}"
																		   class="form-control" readonly/>
																</td>
																<td class="col-sm-4">
																	<input type="mail" name="zone_city_gst[]"
																		   value="{{$zcg->gst}}"
																		   class="form-control"/>
																</td>
																<td class="col-sm-2"><a class="deleteRow"></a>
																	<input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete">
																</td>
															</tr>
														@endforeach
														</tbody>
														<tfoot>
														<tr>
															<td colspan="5" style="text-align: center;">
																<button id="update_all_gst" class="btn btn-primary">Update</button>
															</td>
														</tr>
														<tr>
														</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
							{{--city wise gst end--}}

							<div class="modal fade" id="add_cities_modal" role="dialog" aria-labelledby="add_cities_modal" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="add_cities_modal_title">Add Cities</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
											<div class="container">
												<div class="row">
													<div class="col-md-4">
														<select name="add_zone_cities" class="select2" id="add_zone_cities"
																data-rule-required="true" data-msg-required="City is required">
															@foreach($all_cities as $all_city)
																<option value="{{ $all_city->id }}">{{ $all_city->name }}</option>
															@endforeach
														</select>
													</div>
													<div class="col-md-3">
														<select name="city_zone_class" class="select2" id="city_zone_class"
																data-rule-required="true" data-msg-required="Zone Class is required">
															
																<option value="0">Class A</option>
																<option value="1">Class B</option>
																<option value="2">Class C</option>
																<option value="3" selected>Class D</option>
														</select>
													</div>
													<div class="col-md-3">
														<select name="city_zone_classification" class="select2" id="city_zone_classification"
																data-rule-required="true" data-msg-required="Zone Classification is required">
															@foreach($classifications as $classification)
																<option value="{{ $classification->id }}">{{ $classification->name }}</option>
															@endforeach
														</select>
													</div>
													<div class="col-md-2">
														<input type="button" class="btn btn-md btn-primary " id="add_city_row" value="Add Row" />
													</div>
												</div>
												<div class="row mt-2">
													<table id="city_table" class="table order-list">
														<thead>
														<tr>
															<td>City</td>
															<td>Zone Class</td>
															<td>Zone Classification</td>
															<td>Actions</td>														
														</tr>
														</thead>
														<tbody>
														
														</tbody>
														<tfoot>
														<tr>
															<td colspan="5" style="text-align: center;">
																<button id="update_all_cities" class="btn btn-primary">Save</button>
															</td>
														</tr>
														<tr>
														</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<style>
		.error {
			border-color: red; /* Change border color to indicate error */
			color: red;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {

			$('#zone_form .gst').inputmask({
				'alias': 'decimal',
				'allowMinus': false,
				'allowPlus': false,
				'digits': 2,
				'min': 0.1,
				'max': 100
			});

			$('#zone_form .city_class').each(function() {
				var checkbox = $(this);
				var label = checkbox.next();
				var text = label.text();

				label.remove();

				checkbox.iCheck({
					radioClass: 'iradio_line pt-1 pb-1',
					checkedClass: 'checked bg-success',
					uncheckedClass: 'bg-danger',
					insert: '<div class="icheck_line-icon"></div>' + text
				});
			});

			$('#zone_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				normalizer: function(value) {
					return $.trim(value);
				},
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parents('.form-group'));
				},
				submitHandler: function(form) {
					$(form).find('button[type=submit]').attr('disabled', 'disabled');

					swal({
						title: 'Please Wait!',
						text: 'Zone is being updated!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});

			$('#zone_city_enter').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select City*'
			});
			$('#add_zone_cities').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select City*'
			});
			$('#city_zone_class').select2({
				width: '100%',
				placeholder: 'Select Zone Class*'
			});
			$('#city_zone_classification').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Zone Classification*'
			});
			

			var toggleValue = false;
			let cities_gst_count = {{$zone_cities_gst_count}};

			$('#add_cities').on('click', function () {
				$('#city_wise_gst').modal('show');
			});

			if (cities_gst_count > 0)
			{
				$('#individual_city_gst').click();

				var toggleValue = true;
				$('#individual_city_gst_bit').val("1");
			}
			else
			{
				$('#individual_city_gst_bit').val("0");
				// $('#individual_city_gst').prop('checked');
			}

			$('#individual_city_gst').change( function () {
				toggleValue = !toggleValue;
				if(toggleValue)
				{
					$('#individual_city_gst_bit').val("1");

					$.ajax({
						url: '{!! route('admin.management.zonal.update_zone_cities_gst') !!}',
						method: 'POST',
						data: {
							toggle_value: $('#individual_city_gst_bit').val(),
							'zone_id':{{ $zone_id }},
							'_token': '{{ csrf_token() }}'
						},
					}).done(function (data) {
						if (data.status == 1) {
							toastr.success(data.success, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else{
							toastr.error(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
					});
				}
				else
				{
					$('#individual_city_gst_bit').val("0");

					$.ajax({
						url: '{!! route('admin.management.zonal.update_zone_cities_gst') !!}',
						method: 'POST',
						data: {
							toggle_value: $('#individual_city_gst_bit').val(),
							'zone_id':{{ $zone_id }},
							'_token': '{{ csrf_token() }}'
						},
					}).done(function (data) {
						if (data.status == 1) {
							toastr.success(data.success, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else{
							toastr.error(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
					});
				}
			});

			var counter = 0;

			$("#addrow").on("click", function () {
				var tableData = [];
				var city_data = [];
				var zone_id = {{ $zone_id }};
				var zone_name = $('#zone_name').val();
				var zone_city_enter_value = $("#zone_city_enter").val();
				var zone_city_enter_text = $("#zone_city_enter option:selected").text();
				var zone_city_gst_enter_value = $("#zone_city_gst_enter").val();

				if (zone_city_enter_value === '' || zone_city_gst_enter_value === '') {
					toastr.error('Select city and enter gst !', 'Error!', {
						positionClass: 'toast-top-center',
						containerId: 'toast-top-center'
					});
					return;
				}

				$("#myTable tbody tr").each(function () {
					var row = {};
					row.zone_name = $(this).find('input[name="zone_id[]"]').val();
					row.zone_id = $(this).find('input[name="zone_id_hidden[]"]').val();
					row.city_name = $(this).find('input[name="zone_city_id[]"]').val();
					row.city_id = $(this).find('input[name="zone_city_id_hidden[]"]').val();
					row.gst = $(this).find('input[name="zone_city_gst[]"]').val();

					tableData.push(row);
					city_data.push(row.city_id);
				});

				//now check duplicate entries
				if ($.inArray(zone_city_enter_value, city_data) !== -1) {
					toastr.error('Data already exists !', 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					return;
				}
				//now check duplicate entries end

				var newRow = $("<tr>");
				var cols = "";

				cols += '<td>' +
						'<input type="text" class="form-control" value="'+ zone_name +'" name="zone_id[]" readonly>' +
						'<input type="hidden" class="form-control" value="'+ zone_id +'" name="zone_id_hidden[]" readonly>' +
						'</td>';
				cols += '<td><input type="text" class="form-control" value="' + zone_city_enter_text + '" name="zone_city_id[]" readonly/>'+
						'<input type="hidden" class="form-control" value="' + zone_city_enter_value + '" name="zone_city_id_hidden[]" readonly/>' +
						'</td>';
				cols += '<td><input type="text" class="form-control" value="' + zone_city_gst_enter_value + '" name="zone_city_gst[]"/></td>';

				cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
				newRow.append(cols);
				$("#myTable.order-list").append(newRow);
				counter++;
			});

			$("#myTable.order-list").on("click", ".ibtnDel", function (event) {
				$(this).closest("tr").remove();
				counter -= 1
			});

			$("#update_all_gst").on("click", function() {
				var tableData = [];
				var hasEmptyGst = true;
				$("#myTable tbody tr").each(function () {
					var row = {};
					row.zone_name = $(this).find('input[name="zone_id[]"]').val();
					row.zone_id = $(this).find('input[name="zone_id_hidden[]"]').val();
					row.city_name = $(this).find('input[name="zone_city_id[]"]').val();
					row.city_id = $(this).find('input[name="zone_city_id_hidden[]"]').val();
					row.gst = $(this).find('input[name="zone_city_gst[]"]').val();

					if (!row.gst) {
						hasEmptyGst = false;
						console.error("Empty GST value found!");
						return false; // Exit the loop if an empty GST is found
					}
					tableData.push(row);
				});

				if (hasEmptyGst) {
					$.ajax({
						url: '{!! route('admin.management.zonal.update_zone_cities_gst') !!}',
						method: 'POST',
						data: {
							table_data: tableData,
							'zone_id':{{ $zone_id }},
							'_token': '{{ csrf_token() }}'
						},
					}).done(function (data) {
						if (data.status == 1) {
							toastr.success(data.success, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else{
							toastr.error(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
					});
				} else {
					toastr.error('GST is required !', 'Error!', {
						positionClass: 'toast-top-center',
						containerId: 'toast-top-center'
					});
				}
			});

			//add cities modal part start
			$('#add_cities_in_zone').on('click', function () {
				$('#add_cities_modal').modal('show');
			})

			$("#add_city_row").on("click", function () {
				var tableData = [];
				var city_data = [];
				var classification = [];
				var zone_id = {{ $zone_id }};
				var add_zone_cities = $("#add_zone_cities").val();
				var add_zone_cities_text = $("#add_zone_cities option:selected").text();
				var city_zone_classification = $("#city_zone_classification").val();
				var city_zone_classification_text = $("#city_zone_classification option:selected").text();
				var city_zone_class = $("#city_zone_class").val();
				var city_zone_class_text = $("#city_zone_class option:selected").text();

				if (add_zone_cities === '' || city_zone_classification === '') {
					toastr.error('Select City and zone classification !', 'Error!', {
						positionClass: 'toast-top-center',
						containerId: 'toast-top-center'
					});
					return;
				}

				$("#city_table tbody tr").each(function () {
					var row = {};
					row.zone_city_name = $(this).find('input[name="add_zone_cities[]"]').val();
					row.zone_city_id = $(this).find('input[name="add_zone_cities_hidden[]"]').val();
					row.city_class_name = $(this).find('input[name="city_zone_class[]"]').val();
					row.city_class_id = $(this).find('input[name="city_zone_class_hidden[]"]').val();
					row.city_classification_name = $(this).find('input[name="city_zone_classification[]"]').val();
					row.city_classification_id = $(this).find('input[name="city_zone_classification_hidden[]"]').val();
					tableData.push(row);
					city_data.push(row.zone_city_id);
					classification.push(row.city_classification_id);
				});

				//now check duplicate entries
				if ($.inArray(add_zone_cities, city_data) !== -1 && $.inArray(city_zone_classification, classification) !== -1 ) {
					toastr.error('Data already exists with same city and zone classification..!', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
					return;
				}
				//now check duplicate entries end

				var newRow = $("<tr>");
				var cols = "";

				cols += '<td><input type="text" class="form-control" value="' + add_zone_cities_text + '" name="add_zone_cities[]" readonly/>'+
						'<input type="hidden" class="form-control" value="' + add_zone_cities + '" name="add_zone_cities_hidden[]" readonly/>' +
						'</td>';
				
				cols += '<td><input type="text" class="form-control" value="' + city_zone_class_text + '" name="city_zone_class[]" readonly/>'+
						'<input type="hidden" class="form-control" value="' + city_zone_class + '" name="city_zone_class_hidden[]" readonly/>' +
						'</td>';
				cols += '<td><input type="text" class="form-control" value="' + city_zone_classification_text + '" name="city_zone_classification[]" readonly/>'+
						'<input type="hidden" class="form-control" value="' + city_zone_classification + '" name="city_zone_classification_hidden[]" readonly/>' +
						'</td>';

				cols += '<td><input type="button" class="row_delete btn btn-md btn-danger "  value="Delete"></td>';
				newRow.append(cols);
				$("#city_table.order-list").append(newRow);
				$('#add_zone_cities').val('').trigger('change.select2');
				$('#city_zone_classification ').val('').trigger('change.select2');
				
			});
			$("#city_table.order-list").on("click", ".row_delete", function (event) {
				$(this).closest("tr").remove();
			});
			$('#add_cities_modal').on('hide.bs.modal', function (e) {
				$('#add_zone_cities').val('').trigger('change.select2');
				$('#city_zone_classification ').val('').trigger('change.select2');
				$('#city_table tbody').empty();
			});
			

			$("#update_all_cities").on("click", function() {
				var tableData = [];
				$("#city_table tbody tr").each(function () {
					var row = {};
					row.zone_city_name = $(this).find('input[name="add_zone_cities[]"]').val();
					row.zone_city_id = $(this).find('input[name="add_zone_cities_hidden[]"]').val();
					row.city_class_name = $(this).find('input[name="city_zone_class[]"]').val();
					row.city_class_id = $(this).find('input[name="city_zone_class_hidden[]"]').val();
					row.city_classification_name = $(this).find('input[name="city_zone_classification[]"]').val();
					row.city_classification_id = $(this).find('input[name="city_zone_classification_hidden[]"]').val();
					tableData.push(row);
				});

				$.ajax({
					url: '{!! route('admin.management.zonal.add_cities') !!}',
					method: 'POST',
					data: {
						table_data: tableData,
						'zone_id':{{ $zone_id }},
						'_token': '{{ csrf_token() }}'
					},
				}).done(function (data) {
					if (data.status == 1) {
						toastr.success(data.success, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}
					else{
						toastr.error(data.error, 'Notice!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}
				});
				
			});

			//add cities modal part end
		});
	</script>
@endsection