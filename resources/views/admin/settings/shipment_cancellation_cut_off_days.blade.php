@extends('admin.layout.master')

@section('title', 'Shipment Cancellation Cut-Off Days')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Shipment Cancellation Cut-Off Days
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<div class="row justify-content-center">
								<div class="col-5 col-sm-4 col-md-3 col-lg-2">
									<form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.shipment_cancellation_cut_off_days.store') }}" novalidate="novalidate">
										{{ csrf_field() }}

										<div class="form-group">
											<input type="text" name="shipment_cancellation_cut_off_days" class="form-control shipment_cancellation_cut_off_days" placeholder="Shipment Cancellation Cut-Off Days*" data-rule-required="true" data-msg-required="Shipment Cancellation Cut-Off Days is required" value="{{ $settings->setting_value }}">
										</div>

										<button type="submit" class="btn btn-primary">Update</button>
									</form>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#settings_form input.shipment_cancellation_cut_off_days').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			});

			$('#settings_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				}
			});
		});
	</script>
@endsection