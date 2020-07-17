@extends('admin.layout.master')

@section('title', 'Over Payment Limit')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Over Payment Limit
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<div class="row justify-content-center">
								<div class="col-5 col-sm-4 col-md-3 col-lg-2">
									<form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.over_payment_limit.store') }}" novalidate="novalidate">
										{{ csrf_field() }}

										<div class="form-group">
											<input type="text" name="over_payment_limit" class="form-control over_payment_limit" placeholder="Over Payment Limit*" data-rule-required="true" data-msg-required="Over Payment Limit is required" value="{{ $settings->setting_value }}" data-rule-min="1" data-msg-min="Over Payment Limit can not be less than 1">
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
			$('#settings_form input.over_payment_limit').inputmask({
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