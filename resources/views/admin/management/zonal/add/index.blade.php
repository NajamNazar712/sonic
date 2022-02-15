@extends('admin.layout.master')

@section('title', 'Add Zone')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Add Zone
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="zone_form" class="form-horizontal" method="POST" action="{{ route('admin.management.zonal.add.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-8">
										<div class="form-group">
											<label>Name</label>
											<input type="text" name="name" class="form-control subject" placeholder="Name*" data-rule-required="true" data-msg-required="Name is required">
										</div>
									</div>

									<div class="col-4">
										<div class="form-group">
											<label>GST</label>
											<input type="text" name="gst" class="form-control gst" placeholder="GST*" data-rule-required="true" data-msg-required="GST is required">
										</div>
									</div>

									<div class="col-12" style="text-align: center">
										<h3 class="form-section mb-2">City Class Categorization</h3>
									</div>

									<div class="col-12 col-lg-6 mt-2 mt-lg-0">
										<h3 class="form-section mb-2">Rush/Same-day</h3>

										@foreach($cities as $city)
											<div class="form-group">
												<div class="row align-items-center justify-content-between">
													<div class="col">
														<label class="mb-0 mr-1">{{ $city->name }}</label>
													</div>

													<div class="col text-right">
														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="0" data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class_{{ $city->id }}">Class A</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="1"data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class_{{ $city->id }}">Class B</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="2"data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class_{{ $city->id }}">Class C</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class_{{ $city->id }}" class="city_class" name="city_class[{{ $city->id }}]" value="3"data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class_{{ $city->id }}">Class D</label>
														</fieldset>
													</div>
												</div>
											</div>

											<hr/>
										@endforeach
									</div>

									<div class="col-12 col-lg-6 mt-2 mt-lg-0" style="margin-top: 100px">
										<h3 class="form-section mb-2">Overland/Detain</h3>

										@foreach($cities as $city)
											<div class="form-group">
												<div class="row align-items-center justify-content-between">
													<div class="col">
														<label class="mb-0 mr-1">{{ $city->name }}</label>
													</div>

													<div class="col text-right">
														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="0" data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class{{ $city->id }}">Class A</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="1"data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class{{ $city->id }}">Class B</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="2"data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class{{ $city->id }}">Class C</label>
														</fieldset>

														<fieldset class="d-inline-block mt-1 mb-1 ml-1 mr-0">
															<input type="radio" id="city_class{{ $city->id }}" class="city_class" name="city_class_cor[{{ $city->id }}]" value="3"data-rule-required="true" data-msg-required="Class is required">
															<label for="city_class{{ $city->id }}">Class D</label>
														</fieldset>
													</div>
												</div>
											</div>

											<hr/>
										@endforeach
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Add</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

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
						text: 'Zone is being created!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});
		});
	</script>
@endsection