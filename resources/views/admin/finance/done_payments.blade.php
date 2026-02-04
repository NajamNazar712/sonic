@extends('admin.layout.master')

@section('title', 'Done Payments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Done Payments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')
							<div class="row justify-content-center mb-1">
								<div class="col-12">
									<div class="row">
										<div class="col-2">
											<fieldset class="form-group">
												<select name="search_shipper" id="search_shipper" class="form-control select2">
													{{--                                            @foreach ($shippers as $shipper)--}}
													{{--                                                <option value="{{ $shipper->id }}">{{ $shipper->name }}</option>--}}
													{{--                                            @endforeach--}}
												</select>
											</fieldset>
										</div>
										<div class="col-2">
											<div class="form-group">
												<select name="search_region" id="search_region" class="search_region form-control select2">
													@foreach($region as $r) 
													<option value="{{ $r->id }}">{{ $r->name }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-2">
											<fieldset class="form-group">
												<select name="wallet_filter" id="wallet_filter" class="form-control select2">
													<option value="1">Wallet Users</option>
													<option value="2">Non-Wallet Users</option>
												</select>
											</fieldset>
										</div>
										<div class="col-2">
											<fieldset class="form-group">
												<select name="search_shipper_status" id="search_shipper_status" class="form-control select2">
													@foreach($shipper_status as $id => $status)
														<option value="{{$id}}">{{$status}}</option>
													@endforeach
												</select>
											</fieldset>
										</div>
										<div class="col-2 text-center">
											<form id="tracking_number_search_form"
												  class="form" novalidate="novalidate">
												<div class="form-group">
													<input type="text" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
												</div>
											</form>
										</div>
										<div class="col-2 text-center">
											<form id="done_payment_id_form"
												  class="form" novalidate="novalidate">
												<div class="form-group">
													<input type="text" name="done_payment_ids" class="done_payment_ids" placeholder="Payment ID(s)*" data-tags-input-name="done_payment_ids" data-rule-required="true" data-msg-required="Payment ID(s) is required">
												</div>
											</form>
										</div>
										<div class="col-3">
											<div class="form-group input-group ml">
												<div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o"></span>
													</span>
												</div>
												<input type="text" name="search_from"
													   data-value="{{$from}}"
													   class="form-control pickadate bg-primary border-primary white rounded-right"
													   id="search_date_from" placeholder="Date (From)">
											</div>
										</div>
										<div class="col-3">
											<div class="form-group input-group ml">
												<div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o"></span>
													</span>
												</div>
												<input type="text" name="search_to"
													   data-value="{{$to}}"
													   class="form-control pickadate bg-primary border-primary white rounded-right"
													   id="search_date_to" placeholder="Date (To)">
											</div>
										</div>
										<div class="col-3">
											<div class="form-group input-group">
												<div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o small-calender-icon"></span>
													</span>
												</div>
												<input type="text" name="search_date_status_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_status_from" placeholder="Status Date From">
											</div>
										</div>
										<div class="col-3">
											<div class="form-group input-group">
												<div class="input-group-prepend">
													<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
														<span class="la la-calendar-o small-calender-icon"></span>
													</span>
												</div>
												<input type="text" name="search_date_status_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_status_to" placeholder="Status Date To">
											</div>
										</div>
										<div class="col mb-1 text-center">
											<button type="button" id="search_filter_btn"
													class="btn btn-outline-primary w-25"><i
														class="la la-search"></i> Search
											</button>
										</div>
									</div>
								</div>
							</div>
							@if(session('role_id') == 1 || in_array(268, session('permissions')))
								<form id="payment_form" class="form-horizontal" method="POST" action="{{ route('admin.finance.done_payments.excel_store') }}" novalidate="novalidate" enctype="multipart/form-data">
									{{ csrf_field() }}

									<div class="row align-items-center justify-content-center">
										<div class="col">
											<div class="form-group">
												<input type="file" name="payments" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
											</div>
										</div>

										<div class="col">
											<div class="form-group text-left">
												<button type="submit" name="upload" class="btn btn-primary">Upload</button>
											</div>
										</div>

										<div class="col ml-auto">
											<div class="form-group text-right">
												<a href="{{ asset('file/Done Payment Update Status Template.xlsx') }}" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>

												<a type="button" class="btn btn-primary white" data-toggle="modal" data-target="#company_banks"><i class="la la-bank"></i> Company Banks</a>
											</div>
										</div>
									</div>
								</form>
							@endif

							<div class="col justify-content-end mb-3">
								<div class="card-header">
									<div class="heading-elements">
										<ul class="list-inline">
											<li class="primary border-primary round" value="0" id="star_shippers_filter"><a>
													Star Shippers</a>
											</li>
										</ul>
									</div>
								</div>
							</div>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Payment ID</th>
										<th class="border-primary border-darken-1">Account ID</th>
										<th class="border-primary border-darken-1">Wallet Error Logs</th>
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">Sale Person</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Territory </th>
										<th class="border-primary border-darken-1">Phone No(s).</th>
										<th class="border-primary border-darken-1">Financing Product Type</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Total Shipments</th>
										<th class="border-primary border-darken-1">Delivered Shipments</th>
										<th class="border-primary border-darken-1">Returned Shipments</th>
										<th class="border-primary border-darken-1">Adjusted Shipments</th>
										<th class="border-primary border-darken-1">Arrival Shipments</th>
										<th class="border-primary border-darken-1">Fintech Charges</th>
										<th class="border-primary border-darken-1">Total Amount</th>
										<th class="border-primary border-darken-1">Total Charges</th>
										<th class="border-primary border-darken-1">Total GST</th>
										<th class="border-primary border-darken-1">Total WHT</th>
										<th class="border-primary border-darken-1">Total COD SST</th>
										<th class="border-primary border-darken-1">Total Per SMS Charges</th>
										<th class="border-primary border-darken-1">Packing Charges</th>
										<th class="border-primary border-darken-1">Total Deductible</th>
										<th class="border-primary border-darken-1">Ibft Charges</th>
										<th class="border-primary border-darken-1">Adjustment Charges</th>
										<th class="border-primary border-darken-1">Total Payable</th>
										<th class="border-primary border-darken-1">Bank</th>
										<th class="border-primary border-darken-1">IBN No</th>
										<th class="border-primary border-darken-1">Reference No.</th>
										<th class="border-primary border-darken-1">Created By</th>
										<th class="border-primary border-darken-1">Done Datetime</th>
										<th class="border-primary border-darken-1">Company Bank</th>
										<th class="border-primary border-darken-1">Payment Cycle</th>
                                        <th class="border-primary border-darken-1">Payment Cycle Days</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Paid / Reverted / Hold Datetime</th>
										{{--										<th class="border-primary border-darken-1">Aging</th>--}}
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="modal fade" id="wallet_error_logs" data-backdrop="static" role="dialog" aria-labelledby="wallet_error_logs" aria-hidden="true">
								
							</div>

							<div class="modal fade" id="delivered_shipments" role="dialog" aria-labelledby="delivered_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="delivered_shipments_title">Delivered Shipment(s)</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body text-center">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="view_status_history_modal" role="dialog" aria-labelledby="view_status_history_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="view_status_history_title"></h4>&nbsp;&nbsp;
											<b><span style="font-size: 19px;" id="view_status_history_id"></span></b>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body text-center">
											<div class="modal-body">
												<table class="table table-striped" id="view_status_history">
													<thead>
													<tr>
														<th>S.No#</th>
{{--														<th>Shipment ID</th>--}}
														<th>Payment Status</th>
														<th>Updated At</th>
														<th>Updated By</th>
{{--														<th>Attempts</th>--}}
													</tr>
													</thead>
													<tbody>

													</tbody>
												</table>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="returned_shipments" role="dialog" aria-labelledby="returned_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="returned_shipments_title">Returned Shipment(s)</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body text-center">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="adjusted_shipments" role="dialog" aria-labelledby="adjusted_shipments_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="adjusted_shipments_title">Adjusted Shipment(s)</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body text-center">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
							<div class="modal fade" id="arrival_shipment" role="dialog" aria-labelledby="arrival_shipment_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="arrival_shipment_title">Arrival Shipment(s)</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body text-center">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="update_details" role="dialog" aria-labelledby="update_details_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<form class="form-horizontal" novalidate="novalidate">
											<input type="hidden" name="id" class="id">

											<div class="modal-header">
												<h4 class="modal-title" id="update_details_title">Update Details<span></span></h4>

												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">×</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="form-group">
													<input type="text" name="reference_number" class="form-control reference_number" placeholder="Reference Number*" data-rule-required="true" data-msg-required="Reference Number is required">
												</div>

												<div class="form-group">
													<select name="company_bank" class="select2 company_bank" data-rule-required="true" data-msg-required="Company Bank is required">
														@foreach($banks as $bank)
															<option value="{{ $bank->id }}">{{ $bank->name }}</option>
														@endforeach
													</select>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary ml-auto">Update</button>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="modal fade text-left" id="AddRequestModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRequestModal"
								 aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header bg-primary white">
											<h4 class="modal-title white">Get Support</h4>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body text-center">
											<form id="add_request_form" method="post">
												@csrf
												<div class="container">
													<div class="row ml-1">
														<h2 class="heading">Payment ID(s)</h2>
													</div>

													<input type="hidden" id="payment_id">
													<div class="row old_scroll" id="requested_payment_id">

													</div>
													<hr>
													<div class="complaints" id="request_complaints">
														<div class="row justify-content-center">
															<div class="col-6">
																<fieldset class="form-group">
																	<select name="complaint_channel" id="complaint_channels" class="form-control select2">
																		@foreach($case_nature_channels as $channel1)
																			<option value="{{$channel1->id}}">{{$channel1->channel}}</option>
																		@endforeach
																	</select>
																</fieldset>
															</div>
														</div>
														<div class="row justify-content-center">
															<div class="col">
																<fieldset class="form-group">
																	<textarea class="form-control" name="complaint_description" id="complaint_description" rows="5" placeholder="Enter Description Here..."></textarea>
																</fieldset>
															</div>
														</div>
													</div>
													<div class="row justify-content-center">
														<div class="col-3">
															<button id="AddNewRequest" type="submit" class="btn btn-primary btn-block">Submit</button>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="company_banks" role="dialog" aria-labelledby="company_banks_title" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="company_banks_title">Company Bank(s)</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body text-center">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>ID</th>
															<th>Bank</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($company_banks as $company_bank)
															<tr>
																<td>{{ $company_bank->id }}</td>
																<td>{{ $company_bank->name }}</td>
															</tr>
														@endforeach
													</tbody>
												</table>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="done_payment_reports" role="dialog" aria-labelledby="done_payment_reports" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content col">
										<div class="modal-header">
											<h4 class="modal-title" id="company_banks_title">Done Payment Reports</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<form method="post" class="form-horizontal" id="dps_payment_form" novalidate="novalidate">
											@csrf
											<div class="modal-body  text-center">
												<div class="row justify-content-center">
													<div class="col-6">
														<label>Select Date</label>
														<div class="form-group input-group ml">
															<div class="input-group-prepend">
															<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																<span class="la la-calendar-o"></span>
															</span>
															</div>
															<input type="text" name="dps_from"
																   data-value="{{date('Y-m-d')}}"
																   class="form-control pickadate bg-primary border-primary white rounded-right"
																   id="dps_date_from" placeholder="Date (From)">
														</div>
													</div>

													<div class="col-md-12">
														<table id="dps_report_table" class="table table-responsive">

														</table>
													</div>

												</div>
											</div>
											<div class="modal-footer">
												<button tabindex="-1" type="submit" class="btn btn-primary ml-1 text-left" id="dps_submit_report">Generate</button>
											</div>
										</form>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#search_shipper').select2({
				width:'100%',
				placeholder:"Select Shipper",
				allowClear:true,
				minimumInputLength: 2,
				ajax: {
					dataType: 'json',
					url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
					data: function (params) {
						return {
							search: params.term,
						}
					},
					processResults: function (data) {
						return {
							results: data
						};
					},
					delay: 700,
				}
			}).bind('change', function() {
				table.draw(false);
			});

            $('#search_shipper_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Shipper Status',
                width:'100%',
                allowClear:true
            });

			$('#wallet_filter').prepend(
                '<option value="" selected></option>').select2({
                placeholder: 'Filter Wallet Users',
                width: '100%',
                allowClear: true
            });
			$('#search_region').prepend(
                '<option value="" selected></option>').select2({
                placeholder: 'Search By Region',
                width: '100%',
                allowClear: true
            });

			$('#update_details .company_bank').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Company Bank*'
			}).bind('change', function() {
				if ($(this).hasClass('danger')) {
					$(this).valid();
				}
			});
            $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
                    }
                }
            });


			$('#dps_date_from').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_date_to').pickadate('picker').set('min', $('#dps_date_from').pickadate('picker').get('select'));
					}
				}
			});


			$('#dps_payment_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				normalizer: function (value) {
					return $.trim(value);
				},
				errorPlacement: function (error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function (form) {
					$.ajax({
						url: '{!! route('admin.finance.done_payments.generate_or_find_report') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'dps_date_from': $('input[name="dps_from_formatted"]').val(),
						}
					})
							.done(function (data) {

								// 🔹 Base URL for reports (public/reports)
								var reportsBaseUrl = '{{ url('reports') }}';

								// 🔹 Target table body (or table) where rows will be pushed
								var $table = $('#dps_report_table');
								$table.empty(); // clear previous results

								if (data && data.files && data.files.length) {
									// Build a row for each file
									$.each(data.files, function (index, fileName) {
										var fileUrl = reportsBaseUrl + '/' + fileName;

										var rowHtml =
												'<tr>' +
												'<td>' + (index + 1) + '</td>' +
												'<td>' + (data.date || '') + '</td>' +
												'<td>' + fileName + '</td>' +
												'<td>' +
												'<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-primary">' +
												'View / Download' +
												'</a>' +
												'</td>' +
												'</tr>';

										$table.append(rowHtml);
									});

									// Optional toast
									toastr.success('Reports found for ' + (data.date || ''), 'Success!', {
										positionClass: 'toast-bottom-center',
										containerId: 'toast-bottom-center'
									});

								} else {
									// No files found → show a single row
									var emptyRow =
											'<tr>' +
											'<td colspan="4" class="text-center text-muted">No reports found for this date.</td>' +
											'</tr>';

									$table.append(emptyRow);

									toastr.warning('No reports found for selected date.', 'Info', {
										positionClass: 'toast-bottom-center',
										containerId: 'toast-bottom-center'
									});
								}

								// If you still want to hide the modal:
								// $('#done_payment_reports').modal('hide');
							});
				}
			});



			$('#search_date_status_from').pickadate({
				firstDay: 1,
				clear: '',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_date_status_to').pickadate('picker').set('min', $('#search_date_status_from').pickadate('picker').get('select'));
					}
				}
			});

			$('#search_date_status_to').pickadate({
				firstDay: 1,
				clear: '',
				max: '{{ Carbon\Carbon::now() }}',
				format:'dd mmmm, yyyy',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 23:59:59',
				hiddenSuffix: '_formatted',
				onSet: function(context) {
					if (context.select) {
						$('#search_date_status_from').pickadate('picker').set('max', $('#search_date_status_to').pickadate('picker').get('select'));
					}
				}
			});
            $('#complaint_channels').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder:"Select Channel",
                allowClear:true,
                dropdownParent:$('#add_request_form')
            });

			var selected_rows = [];
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.done_payments.list') }}',
                        data: params,
						method: 'POST',
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Payment ID');
                            head.push('Account ID');
                            head.push('Shipper');
							head.push('Sale Person');
                            head.push('City');
							head.push('Territory');
                            head.push('Phone No(s).');
                            head.push('Financing Product Type');
                            head.push('Address');
                            head.push('Total Shipments');
                            head.push('Delivered Shipments');
                            head.push('Returned Shipments');
                            head.push('Adjusted Shipments');
							head.push('Fintech Charges');
                            head.push('Arrival Shipments');
                            head.push('Total Amount');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total WHT');
							head.push('Total COD SST');
							head.push('Total Per SMS Charges');
                            head.push('Packing Charges');
                            head.push('Total Deductable');
                            head.push('Ibft Charges');
							head.push('Adjustment Charges');
                            head.push('Total Payable');
                            head.push('Bank');
							head.push('IBN No.');
                            head.push('Reference No.');
                            head.push('Created By');
                            head.push('Done Datetime');
                            head.push('Company Bank');
							head.push('Payment Cycle');
                            head.push('Payment Cycle Days');
                            head.push('Status');
							head.push('Paid / Reverted Datetime');
							// head.push('Updated By');
							// head.push('Aging');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.id_padded);
                                row.push(values.user_id_padded);
                                row.push(values.shipper);
								row.push(values.sale_person_name);
                                row.push(values.city);
                                row.push(values.territory);
                                row.push(values.phone_numbers);
                                row.push(values.finova_account_type);
                                row.push(values.address);
                                row.push(values.total_shipments);
                                row.push(values.delivered_shipments_count);
                                row.push(values.returned_shipments_count);
                                row.push(values.adjusted_shipments_count);
								row.push(values.done_fintech_charges);
                                row.push(values.arrival_shipment_shipments_count);
                                row.push(values.total_amount);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_wht);
								row.push(values.total_cod_sst);
								row.push(values.total_sms_charges);
                                row.push(values.packaging_charges);
                                row.push(values.total_deductable);
                                row.push(values.ibft_charges);
								row.push(values.adjustment_charges);
                                row.push(values.total_payable);iban
                                row.push(values.bank);
								row.push(values.iban);
                                row.push(values.reference_number);
                                row.push(values.created_by);
                                row.push(values.done_at);
                                row.push(values.company_bank);
								row.push(values.payment_cycle);
                                row.push(values.payment_cycle_days);
                                row.push(values.status);
								row.push(values.status_updated_at);
								// row.push(values.updated_by);
								// row.push(values.aging);


                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
			var table = $('#datatable').DataTable({
				scrollX: true, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				@if (session('role_id') == 1 || count(array_intersect([62, 63, 597], session('permissions'))) !== 0)

					buttons: [
						@if (session('role_id') == 1 || in_array(597, session('permissions')))
					{
						text: 'Generate Report',
						className: 'btn btn-primary',
						enabled: true,
						action: function (e, dt, node, config) {
							$.ajax({
								url: '{!! route('admin.finance.done_payments.generate_report_to_email') !!}',
								method: 'GET',
							})
									.done(function(data) {
										if (data.status) {
											toastr.success(data.success, 'Success!', {
												positionClass: 'toast-bottom-center',
												containerId: 'toast-bottom-center'
											});
										}
										else {
											toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
										}

									});

						}
					},
					@endif
					@if (session('role_id') == 1 || in_array(1052, session('permissions')))
					{
						text: 'Generate Done Payment Report',
						className: 'btn btn-primary',
						enabled: true,
						action: function (e, dt, node, config) {
							$('#done_payment_reports').modal('show');

						}
					},
						@endif

					@if (session('role_id') == 1 || in_array(62, session('permissions')))
							{
							text: 'Paid',
							className: 'btn btn-primary paid',
							enabled: false,
							action: function (e, dt, node, config) {
								$.ajax({
									url: '{!! route('admin.finance.done_payments.paid') !!}',
									method: 'PUT',
									data: {
										'_token': '{{ csrf_token() }}',
										'ids': selected_rows
									}
								})
								.done(function(data) {
									if (data.status == 0) {
										if (data.payment_paid) {
											toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
										}

										// Show list of already paid IDs, if any
											if (data.hold_payments_ids && data.hold_payments_ids.length > 0) {
												let paidIdsText = data.hold_payments_ids.join(', ');
												toastr.error('The following payment IDs should not be marked as Paid: ' + paidIdsText, 'Info', {
													positionClass: 'toast-top-center',
													containerId: 'toast-top-center',
													timeOut: 8000
												});
											}
									}
									else {
										toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
									}

									table.rows().deselect();

									selected_rows = [];

									table.button('.paid').disable();
									table.button('.tax_paid').disable();
									table.button('.reverted').disable();
									table.button('.hold').disable();
                                    table.button('.un_hold').disable();
									table.draw('false');
								});
							}
						},
					@endif

					@if (session('role_id') == 1 || in_array(1038, session('permissions')))
							{
							text: 'Tax Paid',
							className: 'btn btn-primary tax_paid',
							enabled: false,
							action: function (e, dt, node, config) {
								$.ajax({
									url: '{!! route('admin.finance.done_payments.tax_paid') !!}',
									method: 'PUT',
									data: {
										'_token': '{{ csrf_token() }}',
										'ids': selected_rows,
										'type': 1
									}
								})
								.done(function(data) {
									if (data.status == 0) {
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
									else {
										toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
									}

									table.rows().deselect();

									selected_rows = [];

									table.button('.paid').disable();
									table.button('.tax_paid').disable();
									table.button('.reverted').disable();
									table.button('.hold').disable();
                                    table.button('.un_hold').disable();
									table.draw('false');
								});
							}
						},
					@endif

					@if (session('role_id') == 1 || in_array(63, session('permissions')))
						{
							text: 'Reverted',
							className: 'btn btn-primary reverted',
							enabled: false,
							action: function (e, dt, node, config) {
								$.ajax({
									url: '{!! route('admin.finance.done_payments.reverted') !!}',
									method: 'PUT',
									data: {
										'_token': '{{ csrf_token() }}',
										'ids': selected_rows
									}
								})
								.done(function(data) {
									if (data.status == 0) {
										toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
									}
									else {
										toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
									}

									table.rows().deselect();

									selected_rows = [];

									table.button('.paid').disable();
									table.button('.tax_paid').disable();
									table.button('.reverted').disable();
									table.button('.hold').disable();
                                    table.button('.un_hold').disable();
									table.draw('false');
								});
							}
						},
						@endif
					@if (session('role_id') == 1 || in_array(1044, session('permissions')))
					{
						text: 'Hold',
						className: 'btn btn-primary hold',
						enabled: false,
						action: function (e, dt, node, config) {
							$.ajax({
								url: '{!! route('admin.finance.done_payments.hold') !!}',
								method: 'PUT',
								data: {
									'_token': '{{ csrf_token() }}',
									'ids': selected_rows,
								},
								beforeSend: function () {
									node.prop('disabled', true);
								}
							}).done(function(data) {
										if (data.status == 0) {
											if (data.payment_paid) {
												toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
											}

											// Show list of already paid IDs, if any
											if (data.paid_payment_ids && data.paid_payment_ids.length > 0) {
												let paidIdsText = data.paid_payment_ids.join(', ');
												toastr.info('The following payment IDs were already paid and skipped: ' + paidIdsText, 'Info', {
													positionClass: 'toast-top-center',
													containerId: 'toast-top-center',
													timeOut: 8000
												});
											}
										}
										else {
											toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
										}

										table.rows().deselect();

										selected_rows = [];

										table.button('.paid').disable();
										table.button('.tax_paid').disable();
										table.button('.reverted').disable();
										table.button('.hold').disable();
                                        table.button('.un_hold').disable();
										table.draw('false');
									});
						}
					},
					@endif
                     @if (session('role_id') == 1 || in_array(1044, session('permissions')))
                    {
                        text: 'UnHold',
                        className: 'btn btn-primary un_hold',
                        enabled: false,
                        action: function (e, dt, node, config) {
                            $.ajax({
                                url: '{!! route('admin.finance.done_payments.un_hold') !!}',
                                method: 'PUT',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'ids': selected_rows,
                                },
                                beforeSend: function () {
                                    node.prop('disabled', true);
                                }
                            }).done(function(data) {
                                if (data.status == 0) {
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});

                                    // Show list of already paid IDs, if any
                                    if (data.payment_ids && data.payment_ids.length > 0) {
                                        let paidIdsText = data.payment_ids.join(', ');
                                        toastr.info('The following payment IDs are not in "Hold" status and were skipped: ' + paidIdsText, 'Info', {
                                            positionClass: 'toast-top-center',
                                            containerId: 'toast-top-center',
                                            timeOut: 8000
                                        });
                                    }
                                }
                                else {
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }

                                table.rows().deselect();

                                selected_rows = [];

                                table.button('.paid').disable();
                                table.button('.tax_paid').disable();
                                table.button('.reverted').disable();
                                table.button('.hold').disable();
                                table.button('.un_hold').disable();

                                table.draw('false');
                            });
                        }
                    },
                        @endif
                    {
                        extend: 'excel',
                        title: 'Done Payments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }, {
	                    extend: 'selectAll',
	                    text: 'Select All',
	                    className: 'select_all',
	                    action : function(e) {
	                        e.preventDefault();

	                        table.rows().nodes().each(function(index) {
	                            var row = table.row(index);

	                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
	                                row.select();

	                                id = parseInt(row.id());

	                                var index = $.inArray(id, selected_rows);

	                                if (index === -1) {
	                                    selected_rows.push(id);
	                                }

	                                table.button('.paid').enable();
	                                table.button('.tax_paid').enable();
	                                table.button('.reverted').enable();
									table.button('.hold').enable();
                                    table.button('.un_hold').enable();
	                            }
	                        });
	                    }
	                }, {
	                    extend: 'selectNone',
	                    text: 'Select None',
	                    className: 'select_none',
	                    action : function(e) {
	                        e.preventDefault();

	                        table.rows().nodes().each(function(index) {
	                          var row = table.row(index);

	                          if ($(row.node().firstChild).hasClass('select-checkbox')) {
	                            row.deselect();

	                            id = parseInt(row.id());

	                            var index = $.inArray(id, selected_rows);

	                            if (index !== -1) {
	                                selected_rows.splice(index, 1);
	                            }

	                            if (selected_rows.length == 0) {
	                                table.button('.paid').disable();
									table.button('.tax_paid').disable();
	                                table.button('.reverted').disable();
									table.button('.hold').disable();
                                    table.button('.un_hold').disable();
	                            }
	                          }
	                        });
	                    }
	                },'reset'],
				@else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Done Payments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
				@endif
				scrollX: true, scrollY: '500px',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[10,50, 100, 500, 1000, -1], [10,50, 100, 500, 1000, 'All']],
				pageLength: 10,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				deferLoading: 0,
				ajax: {
					url: '{{ route('admin.finance.done_payments.list') }}',
					method: 'POST',
					data: function (d) {
						d._token = '{{ csrf_token() }}'; 
						d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
                        d.search_shipper = $('#search_shipper').val();
                        d.search_shipper_status = $('#search_shipper_status').val();
                        d.search_from = $('input[name="search_from_formatted"]').val();
                        d.search_to = $('input[name="search_to_formatted"]').val();
                        d.search_date_from = $('input[name="search_date_status_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_status_to_formatted"]').val();
						d.tracking_numbers = $('#tracking_number_search_form .tracking_numbers').val();
						d.search_payment_ids = $('#done_payment_id_form .done_payment_ids').val();
						d.star_shipper_filter = $('#star_shippers_filter').val();
						d.wallet_filter = $('#wallet_filter').val();
						d.search_region = $('#search_region').val();
					}
				},
				rowId: 'id',
				order: [[2, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'payment_id', name: 'done_payments.id', class: 'align-middle text-center payment_id'},
					{data:'user_id_padded', name: 'done_payments.user_id', class: 'align-middle text-center user_id_padded'},
					{data:'wallet_error_logs', name: 'wallet_error_logs', class: 'align-middle text-center wallet_error_logs'},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'sale_person_name', name: 'sale_admin.name', class: 'align-middle text-center sale_person_name'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'territory', name: 't.name', class: 'align-middle text-center territory'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'finova_account_type', name: 'finova_account_type', class: 'align-middle text-center finova_account_type'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'total_shipments', name: 'done_payments.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'delivered_shipments', name: 'done_payments.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
					{data:'returned_shipments', name: 'done_payments.returned_shipments', class: 'align-middle text-center returned_shipments'},
					{data:'adjusted_shipments', name: 'done_payments.adjusted_shipments', class: 'align-middle text-center adjusted_shipments'},
					{data:'arrival_shipment', name: 'done_payments.arrival_shipment', class: 'align-middle text-center arrival_shipment'},
					{data:'done_fintech_charges', name: 'done_fintech_charges', class: 'align-middle text-center done_fintech_charges', orderable: false},
					
					{data:'total_amount', name: 'dpc.amount', class: 'align-middle text-center total_amount', orderable: false},
					{data:'total_charges', name: 'dpc.charges', class: 'align-middle text-center total_charges', orderable: false},
					{data:'total_gst', name: 'dpc.gst', class: 'align-middle text-center total_gst', orderable: false},
					{data:'total_wht', name: 'dpc.wht', class: 'align-middle text-center total_wht', orderable: false},
					{data:'total_cod_sst', name: 'dpc.cod_sst', class: 'align-middle text-center total_cod_sst', orderable: false},
					{data:'total_sms_charges', name:'dpc.sms_charges', class: 'align-middle text-center total_sms_charges', orderable: false},
					{data:'packaging_charges', name: 'dpc.packaging_charges', class: 'align-middle text-center packaging_charges', orderable: false},
					{data:'total_deductable', name: 'total_deductable', class: 'align-middle text-center total_deductable', orderable: false},
					{data:'ibft_charges', name: 'done_payments.ibft_charges', class: 'align-middle text-center ibft_charges', orderable: false},
					{data:'adjustment_charges', name: 'dpc.adjustment', class: 'align-middle text-center adjustment_charges', orderable: false},
					{data:'total_payable', name: 'dpc.payable', class: 'align-middle text-center total_payable', orderable: false},
					{data:'bank', name: 'bank', class: 'align-middle text-center bank'},
					{data:'iban', name: 'ubi.iban', class: 'align-middle text-center iban'},
					{data:'reference_number', name: 'done_payments.reference_number', class: 'align-middle text-center reference_number'},

					{data:'created_by', name: 'done_payments.created_by', class: 'align-middle text-center created_by'},

					{data:'done_at', name: 'done_payments.created_at', class: 'align-middle text-center done_at'},
					{data:'company_bank', name: 'company_bank', class: 'align-middle text-center company_bank'},
					{data:'payment_cycle', name: 'pc.id', class: 'align-middle text-center payment_cycle'},
					{data:'payment_cycle_days', name: 'u.payment_cycle_days', class: 'align-middle text-center payment_cycle_days'},
					{data:'status', name: 'status', class: 'align-middle text-center status'},
					{data:'paid_reverted_at', name: 'done_payments.status_updated_at', class: 'align-middle text-center paid_reverted_at'},
					// {data:'aging', name: 'aging', class: 'align-middle text-center aging'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if (data.status != 'Paid' || data.tax_status != 1) {
						$('td:eq(0)', row).addClass('select-checkbox');

						if ($.inArray(data.id, selected_rows) !== -1) {
	                        table.row(row).select();
	                    }
					}
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
                    var company_bank_select = '<select name="company_bank_select" id="company_bank_select" class="select2 form-control"></select>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Processed</option>' +
                        '<option value="1">Paid</option>' +
                        '<option value="2">Reverted</option>' +
						'<option value="4">Hold</option>' +
                        '</select>';
					var payment_cycle_select =
                        '<select name="payment_cycle_select" id="payment_cycle_select" class="select2 form-control">' +
                        '<option value="1">Daily</option>' +
                        '<option value="2">Weekly</option>' +
                        '<option value="3">Monthly</option>' +
                        '<option value="4">Twice A Week</option>' +
                        '<option value="5">Thrice A Week</option>' +
                        '<option value="6">Fortnite</option>' +

                        '</select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.total_amount') || $(header).is('.total_charges') || $(header).is('.total_gst') || $(header).is('.total_deductable') || $(header).is('.total_payable') || $(header).is('.return_shipments_average_aging') || $(header).is('.action') || $(header).is('.packaging_charges') || $(header).is('.adjustment_charges') || $(header).is('.total_wht') || $(header).is('.total_sms_charges') || $(header).is('.wallet_error_logs') || $(header).is('.total_cod_sst') ) {
							$(td).appendTo($(search));
						}else if($(header).is('.bank')){
                            $(bank_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.company_bank')){
                            $(company_bank_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if ($(header).is('.payment_cycle')) {
                            $(payment_cycle_select).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
                        }else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
						else {
							var current = $(input).appendTo($(search)).on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td).after(icon);

							if (column.search()) {
								current.val(column.search());
							}
						}
					});
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data = $.map({!! $banks !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#bank_select").prepend('<option value="" selected></option>').select2({
                        data:data,
                        placeholder: "Select Bank",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    var data1 = $.map({!! $company_banks !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $company_banks !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#company_bank_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Bank",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });

					$("#payment_cycle_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Cycle",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0',
                        allowClear:true,

                    });
					
					this.api().table().columns.adjust();
				}
			});

			$('#tracking_number_search_form').bind('submit', function(e) {
				e.preventDefault();

				length = $('#tracking_number_search_form #tracking_number').val().length;

				if (length == 0 || length >= 6) {
					table.draw();
				}
			});

			$('#done_payment_id_form').bind('submit', function(e) {
				e.preventDefault();

				length = $('#done_payment_id_form #done_payment_ids').val().length;

				if (length == 0 || length >= 6) {
					table.draw();
				}
			});

			$('#tracking_number_search_form #tracking_number').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			}).bind('input', function() {
				if (this.value.length == 0 || this.value.length >= 6) {
					table.draw();
				}
			});

			$('#done_payment_id_form #done_payment_ids').inputmask({
				'alias': 'integer',
				'allowMinus': false,
				'allowPlus': false
			}).bind('input', function() {
				if (this.value.length == 0 || this.value.length >= 6) {
					table.draw();
				}
			});

			$('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var id = parseInt($(this).parent('tr').attr('id'));

				var index = $.inArray(id, selected_rows);

				if (index === -1) {
					selected_rows.push(id);
				}
				else {
					selected_rows.splice(index, 1);
				}

				if (selected_rows.length > 0) {
					table.button('.paid').enable();
					table.button('.tax_paid').enable();
					table.button('.reverted').enable();
					table.button('.hold').enable();
                    table.button('.un_hold').enable();
				}
				else {
					table.button('.paid').disable();
					table.button('.tax_paid').disable();
					table.button('.reverted').disable();
					table.button('.hold').disable();
                    table.button('.un_hold').disable();
				}
			});
            var route = '{!! route('admin.tracking.index') !!}';

			$('#datatable tbody').on('click', 'tr td.delivered_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#delivered_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.done_payments.delivered_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
                            tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
						});

						$('#delivered_shipments .modal-body').html(tracking_numbers);

						$('#delivered_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.returned_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#returned_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.done_payments.returned_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
                            tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
						});

						$('#returned_shipments .modal-body').html(tracking_numbers);

						$('#returned_shipments').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.wallet_error_logs button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));
				$.ajax({
					url:  '{{ route('admin.finance.done_payments.wallet_error_logs') }}',
					type: 'GET', 
					data: { id: id }, 
					success: function(response) {
						var modalContent =  
							'<div class="modal-dialog modal-xl" role="document">' +
							'<div class="modal-content">' +
							'<div class="modal-header bg-primary white">' +
							'<h4 class="modal-title white">Wallet Error Logs</h4>' +
							'<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
							'<span aria-hidden="true">&times;</span>' +
							'</button>' +
							'</div>' +
							'<div class="modal-body text-center">' +
							'<table class="table table-bordered datatable">' +
							'<thead>' +
							'<tr role="row" class="bg-primary white">' +
							'<input type="hidden" id="wallet_payment_id" value = '+ id +'>'+
							'<th class="border-primary border-darken-1">S. No.</th>' +
							'<th class="border-primary border-darken-1">Tracking Number</th>' +
							'<th class="border-primary border-darken-1">Type</th>' +
							'<th class="border-primary border-darken-1">Error</th>' +
							'<th class="border-primary border-darken-1">Created At</th>' +
							

							'</tr>' +
							'</thead>' +
							'<tbody>'; 

							$.each(response.data, function(index, item) {
								var data = item;
									modalContent += '<tr>';
									modalContent += '<td>' + (index + 1) + '</td>'; 
									modalContent += '<td>' + data.tracking_number + '</td>'; 
									modalContent += '<td>' + data.type + '</td>'; 
									modalContent += '<td>' + data.error + '</td>'; 
									modalContent += '<td>' + data.created_at + '</td>'; 
									modalContent += '</tr>';                            
							});

						modalContent += '</tbody>' + // End of tbody
							'</table>' +
							'<button type="button" class="btn btn-primary" id="re-try" ' +
    						(response.data && response.data.length > 0 ? '' : 'disabled') + '>Re-Try</button>' +
							'</div>' +
							'</div>' +
							'</div>' +
							
						$('#wallet_error_logs').html('');
						$('#wallet_error_logs').append(modalContent);
						$('#wallet_error_logs').modal('show');


					},
					error: function(xhr, status, error) {
						// Handle errors if any
					}
            	});
			});

			$(document).on('click', '#re-try', function(){
				
				var id = $('#wallet_payment_id').val();
				console.log(id)
				$.ajax({
						url: '{!! route('admin.finance.done_payments.mark_settlement') !!}',
						method: 'GET',
						data: {
							'id': id
						}
					})
					.done(function(data) {
						
						if (data.status == 1) {
							$('#wallet_error_logs').modal('hide');
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
					});

			});
			

			$('#datatable tbody').on('click', 'tr td.adjusted_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#adjusted_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.done_payments.adjusted_shipments') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
                            tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
						});

						$('#adjusted_shipments .modal-body').html(tracking_numbers);

						$('#adjusted_shipments').modal('show');
					}
				});
			});
			$('#datatable tbody').on('click', 'tr td.arrival_shipment button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#arrival_shipment .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.done_payments.arrival_shipment') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'id': id
					}
				})
				.done(function(data) {
					if (data) {
						var tracking_numbers = '';

						$.each(data, function(index, tracking_number) {
                            tracking_numbers += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
						});

						$('#arrival_shipment .modal-body').html(tracking_numbers);

						$('#arrival_shipment').modal('show');
					}
				});
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('view_details')) {
					$.ajax({
						url: '{!! route('admin.finance.done_payments.details_print') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						var tab = window.open('', '_blank');

						if(!tab) {
							swal({
								title: 'Popup Blocker Enabled!',
								text: 'Please add this site to your exception list.',
								icon: 'error',
								closeOnClickOutside: false,
								closeOnEsc: false
							});
						}
						else {
							tab.document.write(data);
							tab.document.close();
							tab.focus();
						}
					});
				}
				else if ($(this).hasClass('view_details_archive')) {
					$.ajax({
						url: '{!! route('admin.finance.done_payments.details_print') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id,
							'old':1,
						}
					})
					.done(function(data) {
						var tab = window.open('', '_blank');

						if(!tab) {
							swal({
								title: 'Popup Blocker Enabled!',
								text: 'Please add this site to your exception list.',
								icon: 'error',
								closeOnClickOutside: false,
								closeOnEsc: false
							});
						}
						else {
							tab.document.write(data);
							tab.document.close();
							tab.focus();
						}
					});
				}
				else if ($(this).hasClass('update_details')) {
					$.ajax({
						url: '{!! route('admin.finance.done_payments.details') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						if (data) {
							$('#update_details form .id').val(id);

							$('#update_details form .reference_number').val(data.reference_number);

							$('#update_details form .company_bank').val(data.company_bank_id).trigger('change');

							$('#update_details').modal('show');
						}
					});
				}
				// todo view status history
				else if ($(this).hasClass('view_status_history')) {
					$.ajax({
						url: '{!! route('admin.finance.done_payments.view_status_history') !!}',
						method: 'GET',
						data: {
							'id': id
						}
					})
					.done(function(data) {
						var result = JSON.parse(data);
						var count = JSON.parse(data).length;
						$('#view_status_history_modal').modal('show');
						$('#view_status_history tbody ').html('');
						$('#view_status_history_modal #view_status_history_title').html('Status History');
						$('#view_status_history_id').text(`(${id})`);


						var sc = 1;
						$.each(result.payment_id,function(index, value){
							// console.log(result.payment_id);

							if (result.status[index] == 2) {
								$('#view_status_history tbody ').append(`
							<tr>
							<td>${sc++}</td>

							<td>${result.payment_status[index]}</td>
							<td>${result.status_updated_at[index]}</td>
							<td>${result.admin[index]}</td>
							</tr>`)
							} else {
								$('#view_status_history tbody ').html('');
							}
							if (result.status == 0) {
								toastr.error(result.error, 'error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
							if (result.status == 1) {
								toastr.error(result.error, 'error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
					});
				}
				// todo view status history end
				else if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('admin.finance.done_payments.export_to_excel') !!}?id=' + id, '_blank');
				}
				else if ($(this).hasClass('request_add')) {
                    var selected_id = id.toString().padStart(6, 0);
                    var description = 'Payment not received against ID-' + selected_id + '.';
                    $('#complaint_description').val(description);
                    $('#AddRequestModal').modal('show');
                    $('#payment_id').val(id);
					var html_rows = '<div class="col-4"><span class="mr-1"><i class="la la-angle-right align-bottom"></i><b> '+ selected_id +'</b></span></div>';
                    $('#requested_payment_id').html(html_rows);

				} else if ($(this).hasClass('wallet_settlement')) {

					swal({
                        title: "Processing...",
                        text: "Please wait while we process your request.",
                        content: (() => {
                            // Create a container for the spinner
                            let content = document.createElement("div");
                            content.innerHTML = `
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <div class="spinner" style="width: 30px; height: 30px; border: 4px solid rgba(0,0,0,0.2); border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                            </div>
                        `;
                            return content;
                        })(),
                        buttons: false, // Disable buttons
                        closeOnClickOutside: false, // Disable outside click
                        closeOnEsc: false // Disable escape key
                    });

                    const style = document.createElement("style");
                    style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }`;
                    document.head.appendChild(style);

					$.ajax({
						url: '{!! route('admin.finance.done_payments.mark_settlement') !!}',
						method: 'GET',
						data: {
							'id': id
						}
					})
					.done(function(data) {
						
						swal.close();
						if (data.status == 1) {
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}

						table.draw('false');
					});
				}
				
			});

			$('#update_details').on('show.bs.modal', function (e) {
				$(this).find('input').removeClass('danger');
				$(this).find('select').removeClass('danger');

				$(this).find('label').remove();
			});

			$('#update_details form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					var id = parseInt($(form).find('input.id').val());
					var reference_number = $(form).find('input.reference_number').val();
					var company_bank_id = parseInt($(form).find('select.company_bank').val());

					$.ajax({
						url: '{!! route('admin.finance.done_payments.update_details') !!}',
						method: 'PUT',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id,
							'reference_number': reference_number,
							'company_bank_id': company_bank_id
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}

						table.draw('false');

						$('#update_details').modal('hide');
					});
				}
			});
			function print(id){
                $.ajax({
                    url: '{!! route('admin.finance.done_payments.details_print') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
			}
			
            $('#datatable tbody').on('click', 'tr td.payment_id button', function() {
                var id = parseInt($(this).parents('tr').attr('id'));
                if(id){
                    print(id);
                }else{
                    var error = "Payment Details not found!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
			var select = $('#tracking_number_search_form .tracking_numbers').selectize({
				placeholder: 'Tracking Number(s)',
				delimiter: ',',
				createOnBlur: true,
				persist: false,
				plugins: ['remove_button'],
				onDropdownOpen: function(dropdown) {
					dropdown.remove();
				},
				onType: function(str) {
					var regex = /^[0-9,]+$/;

					if (!regex.test(str)) {
						select[0].selectize.setTextboxValue('');
					}
				},
				create: function(input) {
					if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
						return {
							value: input,
							text: input
						}
					}
					else {
						return false;
					}
				},
			});

			var payment_select = $('#done_payment_id_form .done_payment_ids').selectize({
				placeholder: 'Payment ID(s)',
				delimiter: ',',
				createOnBlur: true,
				persist: false,
				plugins: ['remove_button'],
				onDropdownOpen: function(dropdown) {
					dropdown.remove();
				},
				onType: function(str) {
					var regex = /^[0-9,]+$/;

					if (!regex.test(str)) {
						payment_select[0].selectize.setTextboxValue('');
					}
				},
				create: function(input) {
					if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
						return {
							value: input,
							text: input
						}
					}
					else {
						return false;
					}
				},
			});

            var max_char = 245;
            $('#complaint_description').on('keypress copy paste',function (e) {
                if ($(this).val().length == max_char) {
                    e.preventDefault();
                } else if ($(this).val().length > max_char) {
                    // Maximum exceeded
                    this.value = this.value.substring(0, max_char);
                }
            });
            $('body').on('change','#add_request_form textarea',function() {
                $(this).val($(this).val().trim());
            });
            $( "#add_request_form" ).bind('submit', function (e) {
                e.preventDefault();

				var nature_flag = true;
				var case_nature_id = 1;
				var case_nature_complaint_id = 1;
                var case_nature_channel_id = $('#complaint_channels').val();
				var complaint_description = $('#complaint_description').val();
				var requested_payment_id = $('#payment_id').val();

                if(!case_nature_channel_id){
                    nature_flag = false;
                    var error = "Please select Channel!";
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
				if(!complaint_description){
					nature_flag = false;
					var error = "Please select Description!";
					toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
				}
				if(nature_flag){
					$('#AddNewRequest').attr('disabled',true);
					$.ajax({
						url: '{!! route('admin.crm.request.add') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'payment_id': requested_payment_id,
							'case_nature_id' : case_nature_id,
							'complaint_id' : case_nature_complaint_id,
							'channel_id': case_nature_channel_id,
							'description' : complaint_description,
							'payment_request' : 1
						}
					})
							.done(function(data) {
								if (data.status) {
									toastr.success(data.success, 'Success!', {
										positionClass: 'toast-bottom-center',
										containerId: 'toast-bottom-center'
									});
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}

								table.button('.paid').disable();
								table.button('.reverted').disable();

								selected_rows = [];

								table.rows().deselect();

								table.draw('false');

								$('#AddRequestModal').modal('hide');
								$('#AddNewRequest').attr('disabled',false);
							});

                }
            });
            $('#AddRequestModal').on('hide.bs.modal', function (e) {
                $('#add_request_form')[0].reset();
                $('#complaint_description').val('');
                $('#complaint_channels').val('').trigger('change');
            });
			@if(session('role_id') == 1 || in_array(268, session('permissions')))
				$('#payment_form').validate({
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
							text: 'Your Payment(s) are being updated!',
							icon: 'info',
							buttons: false,
							closeOnClickOutside: false,
							closeOnEsc: false
						});

						form.submit();
					}
				});
            @endif

			$('#star_shippers_filter').on('click',function () {
				$('#star_shippers_filter').val(1);
				table.draw(true);
				$('#star_shippers_filter').val(0);
			});
		});
	</script>
@endsection