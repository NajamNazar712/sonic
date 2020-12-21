@extends('client.layout.master')

@section('title', 'Telenor - Data Conversion')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Telenor - Data Conversion
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<form id="data_conversion_form" class="form-horizontal" method="POST" action="{{ route('cod.telenor.data_conversion.store') }}" novalidate="novalidate" enctype="multipart/form-data">
								{{ csrf_field() }}

								<div class="row align-items-center justify-content-center">
									<div class="col">
										<div class="form-group">
											<label>Stationary File (UP)</label>
											<input type="file" name="stationary_file_up" class="w-100 p-1 border-primary" title="Select Stationary File (UP)" data-rule-required="true" data-msg-required="Stationary File (UP) is required" data-rule-extension="txt" data-msg-extension="Only file with extension txt allowed" data-rule-accept="text/plain" data-msg-accept="Only Text file allowed" data-rule-maxsize="15728640" data-msg-maxsize="File Size must not exceed 15 MB (15360 KB).">
										</div>
									</div>

									<div class="col">
										<div class="form-group">
											<label>Stationary File (PayPak)</label>
											<input type="file" name="stationary_file_paypak" class="w-100 p-1 border-primary" title="Select Stationary File (PayPak)" data-rule-required="true" data-msg-required="Stationary File (PayPak) is required" data-rule-extension="txt" data-msg-extension="Only file with extension txt allowed" data-rule-accept="text/plain" data-msg-accept="Only Text file allowed" data-rule-maxsize="15728640" data-msg-maxsize="File Size must not exceed 15 MB (15360 KB).">
										</div>
									</div>

									<div class="w-100"></div>

									<div class="col">
										<div class="form-group">
											<label>Card File (UP)</label>
											<input type="file" name="card_file_up" class="w-100 p-1 border-primary" title="Select Card File (UP)" data-rule-required="true" data-msg-required="Card File (UP) is required" data-rule-extension="txt" data-msg-extension="Only file with extension txt allowed" data-rule-accept="text/plain" data-msg-accept="Only Text file allowed" data-rule-maxsize="15728640" data-msg-maxsize="File Size must not exceed 15 MB (15360 KB).">
										</div>
									</div>

									<div class="col">
										<div class="form-group">
											<label>Card File (PayPak)</label>
											<input type="file" name="card_file_paypak" class="w-100 p-1 border-primary" title="Select Card File (PayPak)" data-rule-required="true" data-msg-required="Card File (PayPak) is required" data-rule-extension="txt" data-msg-extension="Only file with extension txt allowed" data-rule-accept="text/plain" data-msg-accept="Only Text file allowed" data-rule-maxsize="15728640" data-msg-maxsize="File Size must not exceed 15 MB (15360 KB).">
										</div>
									</div>

									<div class="w-100"></div>

									<div class="col">
										<div class="form-group">
											<label>Recon File</label>
											<input type="file" name="recon" class="w-100 p-1 border-primary" title="Select Recon File" data-rule-required="true" data-msg-required="Recon File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="15728640" data-msg-maxsize="File Size must not exceed 15 MB (15360 KB).">
										</div>
									</div>

									<div class="col-auto">
										<div class="form-group text-right my-1">
											<button type="submit" name="upload" class="btn btn-primary">Upload</button>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/modal/sweetalert.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/sweetalert.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$.validator.addMethod('maxsize', function(value, element, params) {
				if ($(element).attr('type') === 'file') {
					if (element.files && element.files.length) {
						console.log(element.files);
						for (var c = 0; c < element.files.length; c++) {
							if (element.files[c].size > params) {
								return false;
							}
						}
					}
				}

				return true;
			}, $.validator.format("File Size must not exceed {0} bytes."));

			$('#data_conversion_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				normalizer: function(value) {
					return $.trim(value);
				},
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					$(form).find('button[type=submit]').attr('disabled', 'disabled');

					swal({
						title: 'Please Wait!',
						text: 'Your data will be converted shortly!',
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