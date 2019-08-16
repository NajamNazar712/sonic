@extends('admin.layout.master')

@section('title', 'Invoice for Reimbursement')

@section('content')
	<h1 class="mb-1">
		Invoices
	</h1>

	<div class="card">
		<div class="card-content" aria-expanded="true">
			<div class="card-body">
				@include('admin.inc.messages')

				<form id="search_form" class="form row mb-1 justify-content-center" novalidate="novalidate">
					<div class="form-group col-2">
						<select name="payment_type" class="form-control select2 payment_type" data-rule-required="true" data-msg-required="Payment Type is required">
                            @foreach($payment_types as $payment_type)
                                <option value="{{ $payment_type['id'] }}">{{ $payment_type['name'] }}</option>
                            @endforeach
                        </select>
					</div>

					<div class="form-group col-2">
						<select name="shipper" class="form-control select2 shipper" data-rule-required="true" data-msg-required="Shipper is required">
                            @foreach($shippers as $shipper)
                                <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>
                            @endforeach
                        </select>
					</div>

                    <div class="form-group input-group col-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                        </div>
                        <input type="text" name="from" class="form-control from" placeholder="From*" data-rule-required="true" data-msg-required="From is required">
                    </div>

                    <div class="form-group input-group col-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                        </div>
                        <input type="text" name="to" class="form-control to" placeholder="To*" data-rule-required="true" data-msg-required="To is required">
                    </div>

					<div class="form-group col-2">
						<button type="submit" name="search" class="btn btn-primary generate" value="Generate">Generate</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#search_form .payment_type').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Payment Type*'
            });

            $('#search_form .shipper').prepend('<option value="" selected="selected"></option>').select2({
                width: '100%',
                placeholder: 'Shipper*'
            });

            $('#search_form .from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form .to').pickadate('picker').set('min', $('#search_form .from').pickadate('picker').get('select'));

                        $('#search_form .from').valid();
                    }
                }
            });

            $('#search_form .to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form .from').pickadate('picker').set('max', $('#search_form .to').pickadate('picker').get('select'));

                        $('#search_form .to').valid();
                    }
                }
            });

            $('#search_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('text-center w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    $(form).find('button.search').prop('disabled', true);

                    var payment_type = $(form).find('select.payment_type').val();
                    var shipper = $(form).find('select.shipper').val();
                    var from = $(form).find('input.from').pickadate('picker').get('select', 'yyyy-mm-dd 00:00:00');
                    var to = $(form).find('input.to').pickadate('picker').get('select', 'yyyy-mm-dd 23:59:59');

                    window.open('{!! route('admin.finance.invoice_for_reimbursement.generate') !!}?payment_type=' + payment_type + '&shipper=' + shipper + '&from=' + from + '&to=' + to, '_blank');

                    return false;
                }
            });
		});
	</script>
@endsection