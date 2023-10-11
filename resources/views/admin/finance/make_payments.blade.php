@extends('admin.layout.master')

@section('title', 'Make Payments')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Make Payments
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<div class="row text-center">
								<div class="col-2">
									<fieldset class="form-group">
										<select name="search_shipper" id="search_shipper" class="form-control select2">
											@foreach($shippers as $shipper)
												<option value="{{$shipper->id}}">{{$shipper->name}}</option>
											@endforeach
										</select>
									</fieldset>
								</div>
								<div class="col-3">
									<form id="payment_cycle_filter_form" class="mb-1 justify-content-center" novalidate="novalidate">
										<div class="form-group">
											<select name="payment_cycle_filter" class="select2 payment_cycle_filter">
												<option value="1" selected>Filtered</option>
												<option value="2">All</option>
											</select>
										</div>
									</form>
								</div>
								<div class="col-3">
									<form id="tracking_number_search_form" class="mb-1 justify-content-center" novalidate="novalidate">
										<div class="form-group">
										<input type="text" name="tracking_number" class="form-control tracking_number" id="tracking_number" placeholder="Tracking Number">
										</div>
									</form>
								</div>		

								<div class="col-3">
									<form id="positive_negative_filter_form" class="mb-1 justify-content-center" novalidate="novalidate">
									<div class="form-group">
										<select name="positive_negative_filter" class="select2 positive_negative_filter">
											<option value="1">Positive</option>
											<option value="2">Negative</option>
										</select>
									</div>
								</form>
								</div>
								
								<div class="col-3">
									<form id="shipper_status_form" class="mb-1 justify-content-center" novalidate="novalidate">
									<div class="form-group">
										<select name="shipper_status" class="select2 shipper_status">
											@foreach($shipper_status as $id => $status)
												<option value="{{$id}}">{{$status}}</option>
											@endforeach
										</select>
									</div>
									</form>
								</div>
								<div class="col-3">
									<form id="shipper_document_status_form" class="mb-1 justify-content-center" novalidate="novalidate">
									<div class="form-group">
										<select name="shipper_document_status" class="select2 shipper_document_status">
												<option value="0">Pending</option>
												<option value="1">Unverified</option>
												<option value="2">Verified</option>
												<option value="3">Rejected</option>
										</select>
									</div>
									</form>
								</div>
							</div>

							<div class="row text-center">
								<div class="col-4">
									<h4 class="bg-primary mb-0 text-white border">Total Amount</h4><p class="border" id="stats_total_amount">Rs. {{ $total_amount }}</p>
								</div>

								<div class="col-4">
									<h4 class="bg-primary mb-0 text-white border">Total Charges</h4><p class="border" id="stats_total_charges">Rs. {{ $total_charges }}</p>
								</div>

								<div class="col-4">
									<h4 class="bg-primary mb-0 text-white border">Total Payable</h4><p class="border" id="stats_total_payable">Rs. {{ $total_payable }}</p>
								</div>
							</div>

							<div class="col justify-content-end mb-3">
								<div class="card-header">
									<div class="heading-elements">
										<ul class="list-inline" >
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
										<th class="border-primary border-darken-1">Shipper</th>
										<th class="border-primary border-darken-1">City</th>
										<th class="border-primary border-darken-1">Phone No(s).</th>
										<th class="border-primary border-darken-1">Address</th>
										<th class="border-primary border-darken-1">Created Datetime</th>
										<th class="border-primary border-darken-1">Total Shipments</th>
										<th class="border-primary border-darken-1">Total Pending Shipments</th>
										<th class="border-primary border-darken-1">Delivered Shipments</th>
										<th class="border-primary border-darken-1">Returned Shipments</th>
										<th class="border-primary border-darken-1">Adjusted Shipments</th>
										<th class="border-primary border-darken-1">Total Amount</th>
										<th class="border-primary border-darken-1">Total Charges</th>
										<th class="border-primary border-darken-1">Total GST</th>
										<th class="border-primary border-darken-1">Total WHT</th>
{{--										<th class="border-primary border-darken-1">Packing Charges</th>--}}
{{--										<th class="border-primary border-darken-1">Fintech Charges</th>--}}
										<th class="border-primary border-darken-1">Total Deductable</th>
										<th class="border-primary border-darken-1">Total Payable</th>
										<th class="border-primary border-darken-1">Bank</th>
										<th class="border-primary border-darken-1">Bank Branch</th>
										<th class="border-primary border-darken-1">Account No.</th>
										<th class="border-primary border-darken-1">Account Title</th>
										<th class="border-primary border-darken-1">IBAN</th>
										<th class="border-primary border-darken-1">Account City</th>
										<th class="border-primary border-darken-1">Payment Cycle</th>
										
										<th class="border-primary border-darken-1">Return Shipments Avg. Aging</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

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

							<div class="modal fade" id="view_details" role="dialog" aria-labelledby="view_details_title" aria-hidden="true">
								<div class="modal-dialog modal-xl" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="view_details_title">Details</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>

							<div class="modal fade" id="make_payments" role="dialog" aria-labelledby="make_payments_title" aria-hidden="true">
								<div class="modal-dialog modal-lg modal-full-length" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="make_payments_title">Make Payments<span></span></h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>

										<div class="container mt-5">
											<div class="row">
												<div class="col-md-4">
													<div class="form-group input-group">
														<div class="input-group-prepend">
																					<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																					<span class="la la-calendar-o small-calender-icon"></span>
																					</span>
														</div>
														<input type="text" name="requested_from_date"
															class="form-control bg-primary border-primary white rounded-right"
															id="requested_from_date" placeholder="Requested Date From">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group input-group">
														<div class="input-group-prepend">
																					<span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
																					<span class="la la-calendar-o small-calender-icon"></span>
																					</span>
														</div>
														<input type="text" name="requested_to_date"
															class="form-control bg-primary border-primary white rounded-right"
															id="requested_to_date" placeholder="Requested Date To">
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group input-group" style="margin-top: -20px ">
														<button type="button" id="search_filter_btn"
																class="float-right mb-1 mt-2 btn btn-outline-primary btn-min-width"><i
																	class="la la-search" style="margin-right: 10px"></i> Search
														</button>
													</div>
												</div>
											</div>
										</div>

										<div class="modal-body">
											<table class="table table-bordered datatable" id="make_payments_datatable" style="z-index: 3;">
												<thead>
													<tr role="row" class="bg-primary white">
														<th class="border-primary border-darken-1"></th>
														<th class="border-primary border-darken-1">S. No.</th>
														<th class="border-primary border-darken-1">Shipper</th>
														<th class="border-primary border-darken-1">Shipment</th>
														<th class="border-primary border-darken-1">Origin</th>
														<th class="border-primary border-darken-1">Type</th>
														<th class="border-primary border-darken-1">Status</th>
														<th class="border-primary border-darken-1">Delivery / Return Datetime</th>
														<th class="border-primary border-darken-1">Aging</th>
														<th class="border-primary border-darken-1">Amount</th>
														<th class="border-primary border-darken-1">Charges</th>
														<th class="border-primary border-darken-1">GST</th>
														<th class="border-primary border-darken-1">WHT</th>
														<th class="border-primary border-darken-1">Fintech Charges</th>
														<th class="border-primary border-darken-1">Packging Charges</th>
														<th class="border-primary border-darken-1">Deductable</th>
														<th class="border-primary border-darken-1">Payable</th>
														<th class="border-primary border-darken-1">Arrival Date</th>
													</tr>
												</thead>
											</table>

											<form id="make_payments_form" class="form-inline mt-1 mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.finance.make_payments.store') }}">
												{{ csrf_field() }}

												<input type="hidden" name="pending_payment_shipment_ids" class="pending_payment_shipment_ids">

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Amount</label>
														<input type="text" name="total_amount" class="form-control text-center total_amount" placeholder="Total Amount" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Charges</label>
														<input type="text" name="total_charges" class="form-control text-center total_charges" placeholder="Total Charges" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total GST</label>
														<input type="text" name="total_gst" class="form-control text-center total_gst" placeholder="Total GST" readonly="readonly">
													</div>
												</div>
												<div class="col-2 mt-2">
													<div class="form-group">
														<fieldset class="form-group">
															<select name="company_bank_id" id="company_bank" class="form-control select2 company_bank" data-rule-required="true" data-msg-required="Bank is required" >
																@foreach($company_banks as $bank)
																	<option value="{{$bank->id}}">{{$bank->name}}</option>
																@endforeach
															</select>
														</fieldset>
													</div>
												</div>

												<div class="w-100 mt-2"></div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">WHT(3%)</label>
														<input type="text" name="wht" class="form-control text-center wht" placeholder="With Holding Tax" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Deductable</label>
														<input type="text" name="total_deductable" class="form-control text-center total_deductable" placeholder="Total Deductable" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Payable</label>
														<input type="text" name="total_payable" class="form-control text-center total_payable" placeholder="Total Payable" readonly="readonly">
													</div>
												</div>

												<div class="col-2">
													<div class="form-group">
														<label class="mx-auto">Total Hold</label>
														<input type="text" name="total_hold" class="form-control text-center total_hold" placeholder="Total Hold" readonly="readonly">
													</div>
												</div>

												<div class="w-100"></div>

												<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
												<button type="submit" name="make" class="mr-1 btn btn-primary make">Make & Export Bank Order</button>
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
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {

			$('#requested_from_date').pickadate({
				firstDay: 1,
				clear: '',
				max: '{{ Carbon\Carbon::now() }}',
				// format: 'dd mmmm, yyyy',
				format: 'yyyy-mm-dd',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 00:00:00',
				hiddenSuffix: '_formatted',
				onSet: function (context) {
					if (context.select) {
						$('#requested_to_date').pickadate('picker').set('min', $('#requested_from_date').pickadate('picker').get('select'));
					}
				}
			});
			
			$('#requested_to_date').pickadate({
				firstDay: 1,
				clear: '',
				max: '{{ Carbon\Carbon::now() }}',
				// format: 'dd mmmm, yyyy',
				format: 'yyyy-mm-dd',
				selectYears: true,
				selectMonths: true,
				formatSubmit: 'yyyy-mm-dd 23:59:59',
				hiddenSuffix: '_formatted',
				onSet: function (context) {
					if (context.select) {
						$('#requested_from_date').pickadate('picker').set('max', $('#requested_to_date').pickadate('picker').get('select'));
					}
				}
			});

			@if (session('print'))
				window.open('{!! route('admin.finance.make_payments.export_bank_order') !!}?done_payment_ids=' + '{{ implode(',', session('print')) }}', '_blank');
			@endif

			function stats_calculate() {
				positive_negative_filter = $('#positive_negative_filter_form select.positive_negative_filter').val();

				$.ajax({
					url: '{!! route('admin.finance.make_payments.stats_calculate') !!}',
					data: {
						'positive_negative_filter': positive_negative_filter
					}
				})
				.done(function(data) {
					$('#stats_total_amount').html(data.total_amount);
					$('#stats_total_charges').html(data.total_charges);
					$('#stats_total_payable').html(data.total_payable);
				});
			}

			var selected_rows = [];

			var selected_rows_shipments = [];

			var initial_total_hold = 0;

			$('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
				placeholder:'Shipper',
				width:'100%',
				allowClear:true
			}).bind('change', function() {
				table.draw(false);
			});;
			$('#positive_negative_filter_form select.positive_negative_filter').prepend('<option value="" selected></option>').select2({
                placeholder: 'Select Positive/Negative Filter',
                width:'100%',
                allowClear: true
            }).bind('change', function() {
            	stats_calculate();
				table.draw(false);
			});

			$('#shipper_status_form select.shipper_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Shipper Status',
                width:'100%',
                allowClear:true
            }).bind('change', function() {
				table.draw(false);
			});

			$('#shipper_document_status_form select.shipper_document_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Shipper Document Status',
                width:'100%',
                allowClear:true
            }).bind('change', function() {
				table.draw(false);
			});

			$('#payment_cycle_filter_form select.payment_cycle_filter').select2({
                placeholder:'Payment Cycle Filter',
                width:'100%',
            }).bind('change', function() {
				table.draw();
			});
			$('#make_payments_form #company_bank').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Company Bank',
                width:'100%',
				dropdownParent:$('#make_payments_form')
            });


			// $('#payment_cycle_filter_form select.payment_cycle_filter').val(1).trigger('change');

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.make_payments.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Shipper');
                            head.push('City');
                            head.push('Phone No(s).');
                            head.push('Address');
                            head.push('Created Datetime');
                            head.push('Total Shipments');
                            head.push('Total Pending Shipments');
                            head.push('Delivered Shipments');
                            head.push('Returned Shipments');
                            head.push('Adjusted Shipments');
                            head.push('Total Amount');
                            head.push('Total Charges');
                            head.push('Total GST');
                            head.push('Total WHT');
                            // head.push('Packing Charges');
                            head.push('Total Deductable');
                            head.push('Total Payable');
                            head.push('Bank');
                            head.push('Bank Branch');
                            head.push('Account No.');
                            head.push('Account Title');
                            head.push('IBAN');
                            head.push('Account City');
                            head.push('Payment Cycle');
                            head.push('Return Shipments Avg. Aging');





                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.shipper);
                                row.push(values.city);
                                row.push(values.phone_numbers);
                                row.push(values.address);
                                row.push(values.created_at);
                                row.push(values.total_shipments);
                                row.push(values.total_pending_shipments);
                                row.push(values.delivered_shipments_count);
                                row.push(values.returned_shipments_count);
                                row.push(values.adjusted_shipments_count);
                                row.push(values.total_amount);
                                row.push(values.total_charges);
                                row.push(values.total_gst);
                                row.push(values.total_wht);
                                // row.push(values.packaging_charges);
                                row.push(values.total_deductable);
                                row.push(values.total_payable);
                                row.push(values.bank);
                                row.push(values.bank_branch);
                                row.push(values.account_no);
                                row.push(values.account_title);
                                row.push(values.iban);
                                row.push(values.account_city);
                                row.push(values.payment_cycle);
                                row.push(values.return_shipments_average_aging);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				@if (session('role_id') == 1 || in_array(60, session('permissions')))

					buttons: [{
						text: 'Make Payment(s)',
						className: 'btn btn-primary make_payment',
						enabled: false,
						action: function (e, dt, node, config) {
							$('#make_payments #make_payments_form .total_amount').val(0);
							$('#make_payments #make_payments_form .total_charges').val(0);
							$('#make_payments #make_payments_form .total_gst').val(0);
							$('#make_payments #make_payments_form .total_deductable').val(0);
							$('#make_payments #make_payments_form .total_payable').val(0);
							$('#make_payments #make_payments_form .total_hold').val(0);
							$('#make_payments #make_payments_form .wht').val(0);


							$('#make_payments #make_payments_form button.make').prop('disabled', true);
							$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

							$('#make_payments #make_payments_form .pending_payment_shipment_ids').val('');

							selected_rows_shipments = [];

							make_payments_table.clear().draw();

							$('#make_payments').modal('show');
						}
					},
                    {
                        extend: 'excel',
                        title: 'Make Payments',
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

	                                table.button('.make_payment').enable();
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
	                                table.button('.make_payment').disable();
	                            }
	                          }
	                        });
	                    }
	                },
				'reset'],
				@else
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Make Payments',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
				'reset'],
				@endif
				scrollX: true, scrollY: '500px',
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.make_payments.list') }}',
					data: function (d) {
						d.payment_filter = $('#payment_cycle_filter_form select.payment_cycle_filter').val();
						d.tracking_number = $('#tracking_number_search_form #tracking_number').val();
						d.positive_negative_filter = $('#positive_negative_filter_form select.positive_negative_filter').val();
						d.search_shipper = $('#search_shipper').val();
						d.shipper_status = $('#shipper_status_form select.shipper_status').val();
						d.shipper_document_status = $('#shipper_document_status_form select.shipper_document_status').val();
						d.star_shipper_filter = $('#star_shippers_filter').val();
					}
				},
				rowId: 'id',
				order: [[6, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'shipper', name: 'u.name', class: 'align-middle text-center shipper'},
					{data:'city', name: 'c.name', class: 'align-middle text-center city'},
					{data:'phone_numbers', name: 'phone_numbers', class: 'align-middle text-center phone_numbers'},
					{data:'address', name: 'u.address', class: 'align-middle text-center address'},
					{data:'created_at', name: 'pending_payments.created_at', class: 'align-middle text-center created_at'},
					{data:'total_shipments', name: 'pending_payments.total_shipments', class: 'align-middle text-center total_shipments'},
					{data:'total_pending_shipments', name: 'total_pending_shipments', class: 'align-middle text-center total_pending_shipments', orderable: false},
					{data:'delivered_shipments', name: 'pending_payments.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
					{data:'returned_shipments', name: 'pending_payments.returned_shipments', class: 'align-middle text-center returned_shipments'},
					{data:'adjusted_shipments', name: 'pending_payments.adjusted_shipments', class: 'align-middle text-center adjusted_shipments'},
					{data:'total_amount', name: 'ppc.amount', class: 'align-middle text-center total_amount', orderable: false},
					{data:'total_charges', name: 'ppc.charges', class: 'align-middle text-center total_charges', orderable: false},
					{data:'total_gst', name: 'ppc.gst', class: 'align-middle text-center total_gst', orderable: false},
					{data:'total_wht', name: 'ppc.wht', class: 'align-middle text-center total_wht', orderable: false},
					// {data:'packaging_charges', name: 's.packaging_charges', class: 'align-middle text-center packaging_charges', orderable: false},
					// {data:'fintech_charges', name: 's.fintech_charges', class: 'align-middle text-center fintech_charges'},
					{data:'total_deductable', name: 'total_deductable', class: 'align-middle text-center total_deductable', orderable: false},
					{data:'total_payable', name: 'ppc.payable', class: 'align-middle text-center total_payable', orderable: false},
					{data:'bank', name: 'bank', class: 'align-middle text-center bank'},
					{data:'bank_branch', name: 'ubi.bank_branch', class: 'align-middle text-center bank_branch'},
					{data:'account_no', name: 'ubi.account_no', class: 'align-middle text-center account_no'},
					{data:'account_title', name: 'ubi.account_title', class: 'align-middle text-center account_title'},
					{data:'iban', name: 'ubi.iban', class: 'align-middle text-center iban'},
					{data:'account_city', name: 'bc.name', class: 'align-middle text-center account_city'},
					{data:'payment_cycle', name: 'pc.id', class: 'align-middle text-center payment_cycle'},
				
					{data:'return_shipments_average_aging', name: 'return_shipments_average_aging', class: 'align-middle text-center return_shipments_average_aging', orderable: false},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					if (data.documents_status == 2) {
                        $('td:eq(0)', row).addClass('select-checkbox');
                    }

					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);

					if (selected_rows.length != 0) {
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
                    // var payment_mode_select = '<select name="payment_mode_select" id="payment_mode_select" class="select2 form-control">' +
                    //     '<option value="ibft">IBFT</option>' +
                    //     '<option value="invoices">Invoices</option>' +
                    //     '</select>';
                    var payment_cycle_select = '<select name="payment_cycle_select" id="payment_cycle_select" class="select2 form-control">' +
                        '<option value="1">Daily</option>' +
                        '<option value="2">Weekly</option>' +
                        '<option value="3">Monthly</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.total_amount') || $(header).is('.total_charges') || $(header).is('.total_gst') || $(header).is('.total_deductable') || $(header).is('.total_payable') || $(header).is('.total_adjustments') || $(header).is('.return_shipments_average_aging') || $(header).is('.action') || $(header).is('.total_pending_shipments') || $(header).is('.packaging_charges') || $(header).is('.total_wht')) {
							$(td).appendTo($(search));
						}else if($(header).is('.bank')){
                            $(bank_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        // }else if($(header).is('.payment_mode')){
                        //     $(payment_mode_select).appendTo($(search))
                        //         .on( 'change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         } ).wrap(td);
                        }else if($(header).is('.payment_cycle')){
                            $(payment_cycle_select).appendTo($(search))
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
                    // $("#payment_mode_select").prepend('<option value="" selected></option>').select2({
                    //     placeholder: "Select Mode",
                    //     width:'100%',
                    //     containerCssClass: 'select-xs',
                    //     dropdownCssClass: 'form-control-sm p-0'
                    // });
                    $("#payment_cycle_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Cycle",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

			$('#tracking_number_search_form').bind('submit', function(e) {
				e.preventDefault();

				length = $('#tracking_number_search_form #tracking_number').val().length;

				if (length == 0 || length >= 12) {
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

			var make_payments_table = $('#make_payments #make_payments_datatable').DataTable({
				dom: '<"pull-right"B>tr',
				buttons: [{
					text: 'Export Selected',
					className: 'export_selected',
					action: function(e) {
						e.preventDefault();

						if (selected_rows_shipments.length != 0) {
							window.open('{!! route('admin.finance.make_payments.shipment_export_selected') !!}?ids=' + selected_rows_shipments, '_blank');
						}
					}
				}, {
					extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action : function(e) {
                        e.preventDefault();

                        make_payments_table.rows().nodes().each(function(index) {
                            var row = make_payments_table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
                                row.select();

                                var parent = $(row.node());

								calculation(parent);
                            }
                        });
                    }
                }, {
                    extend: 'selectNone',
                    text: 'Select None',
                    className: 'select_none',
                    action : function(e) {
                        e.preventDefault();

                        make_payments_table.rows().nodes().each(function(index) {
                          var row = make_payments_table.row(index);

                          if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {
                            row.deselect();

                            var parent = $(row.node());

							calculation(parent);
                          }
                        });
                    }
                }],
				scrollX: true,
				paging: false,
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
				},
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: {
					url: '{{ route('admin.finance.make_payments.shipment_list') }}',
					data: function (d) {
						d.ids = selected_rows;
						d.requested_from_date =$('input[name="requested_from_date_formatted"]').val();
                        d.requested_to_date = $('input[name="requested_to_date_formatted"]').val();
					}
				},
				rowId: 'id',
				order: [[2, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'shipper', name: 'u.name', class: 'align-middle shipper'},
					{data:'shipment', name: 's.tracking_number', class: 'align-middle shipment'},
					{data:'origin', name: 'oc.name', class: 'align-middle origin'},
					{data:'type', name: 'type', class: 'align-middle type'},
					{data:'status', name: 'ss.name', class: 'align-middle status'},
					{data:'created_at', name: 'pending_payment_shipments.created_at', class: 'align-middle created_at'},
					{data:'aging', name: 'aging', class: 'align-middle aging', orderable: false},
					{data:'amount', name: 'pending_payment_shipments.amount', class: 'align-middle amount'},
					{data:'charges', name: 'pending_payment_shipments.charges', class: 'align-middle charges'},
					{data:'gst', name: 'pending_payment_shipments.gst', class: 'align-middle gst'},
					{data:'wht', name: 'pending_payment_shipments.wht', class: 'align-middle wht'},
					{data:'fintech_charges', name: 'fintech_charges', class: 'align-middle fintech_charges'},
					{data:'packaging_charges', name: 'packaging_charges', class: 'align-middle packaging_charges'},
					{data:'deductable', name: 'deductable', class: 'align-middle deductable'},
					{data:'payable', name: 'pending_payment_shipments.payable', class: 'align-middle payable'},
					{data:'arrival_date', name: 'sj.created_at', class: 'align-middle arrival_date'},
				],
				rowCallback: function(row, data, index) {
					$('td:eq(1)', row).html(index + 1);

					if (selected_rows_shipments.length != 0) {
						if ($.inArray(data.id, selected_rows_shipments) !== -1) {
							make_payments_table.row(row).select();
						}
					}
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="3">All</option>' +
                        '<option value="0">Delivered</option>' +
                        '<option value="1">Returned</option>' +
                        '<option value="2">Adjusted</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.aging')) {
							$(td).appendTo($(search));
						}else if($(header).is('.type')){
                        $(drop_select).appendTo($(search))
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				},
				drawCallback: function() {
					if (selected_rows_shipments.length == 0) {
						initial_total_hold = this.api().column('.payable').data().reduce(function (a, b) {
							return parseFloat(a.toString().replace(/,/g, '')) + parseFloat(b.toString().replace(/,/g, ''));
						}, 0);

						$('#make_payments #make_payments_form .total_hold').val(parseFloat(initial_total_hold).toFixed(2));
					}
				}
			});

			$('#search_filter_btn').on('click',function () {
                make_payments_table.draw(true);
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
					table.button('.make_payment').enable();
				}
				else {
					table.button('.make_payment').disable();
				}
			});

            var route = '{!! route('admin.tracking.index') !!}';

			$('#datatable tbody').on('click', 'tr td.delivered_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#delivered_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.make_payments.delivered_shipments') !!}',
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
			$('#make_payments_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form){
					var zero_charges = false;
					make_payments_table.rows().nodes().each(function(index) {
						var row = make_payments_table.row(index);
						if ($(row.node().firstChild).hasClass('select-checkbox') && $(row.node()).hasClass('selected')) {

							// var shipments = parseInt($(row.node()).find('td.shipment').text());
							// var charges = parseInt($(row.node()).find('td.charges').text());
							// if(charges == 0){
							// 	console.log(shipments);
							// }


							var shipments = parseInt($(row.node()).find('td.shipment').text());
							var account_type = parseInt($(row.node()).attr('account_type'));
							if(account_type == 1){
								var type_id = parseInt($(row.node()).attr('type_id'));
								var row_id = $(row.node()).attr('id');
								if (type_id != 2) {
									var amount = parseInt($(row.node()).find('td.deductable').text());
									if(amount == 0){
										zero_charges = true;
										shipments_array.push(shipments);
									}
								}
							}

						}
					});
					if(zero_charges){
						var html = '';

						html += 'Charges are zero for the following Shipments<br/>';
						$.each(shipments_array, function(index, tracking_number) {
							html += tracking_number + '<br/>';
						});

						html += '<br/>Select yes to pay!';

						content = document.createElement('div');
						content.innerHTML = html;

						swal({
							title: 'Are You Sure?',
							content: content,
							icon: 'warning',
							buttons: {
								cancel: {
									text: 'No',
									value: null,
									visible: true,
									closeModal: true,
								},
								confirm: {
									text: 'Yes',
									value: true,
									visible: true,
									closeModal: true
								}
							},
							closeOnClickOutside: false,
							closeOnEsc: false,
							dangerMode: true
						}).then(function(confirm) {
							if (confirm) {
								verify_make_payments(form);
							}
						});
					}else{
						verify_make_payments(form);
					}
				}
			});

			$('#datatable tbody').on('click', 'tr td.returned_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#returned_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.make_payments.returned_shipments') !!}',
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

			$('#datatable tbody').on('click', 'tr td.adjusted_shipments button', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				$('#adjusted_shipments .modal-body').html('');

				$.ajax({
					url: '{!! route('admin.finance.make_payments.adjusted_shipments') !!}',
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

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('view_details')) {
					$('#view_details .modal-body').html('');

					$.ajax({
						url: '{!! route('admin.finance.make_payments.shipment_details') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						var details = '<table class="table table-sm table-bordered"><thead><tr role="row" class="bg-primary white"><th class="border-primary border-darken-1 align-middle text-center">Shipment</th><th class="border-primary border-darken-1 align-middle text-center">Type</th><th class="border-primary border-darken-1 align-middle text-center">Amount</th><th class="border-primary border-darken-1 align-middle text-center">Charges</th><th class="border-primary border-darken-1 align-middle text-center">GST</th><th class="border-primary border-darken-1 align-middle text-center">Fintech Charges</th> <th class="border-primary border-darken-1 align-middle text-center">Packging Charges</th> <th class="border-primary border-darken-1 align-middle text-center">Deductable</th><th class="border-primary border-darken-1 align-middle text-center">Payable</th></tr></thead><tbody>';

						$.each(data, function(index, detail) {
							details += '<tr>';
							details += '<td class="align-middle text-center">' + detail.tracking_number + '</td>';
							details += '<td class="align-middle text-center">' + detail.type + '</td>';
							details += '<td class="align-middle text-center">' + detail.amount + '</td>';
							details += '<td class="align-middle text-center">' + detail.charges + '</td>';
							details += '<td class="align-middle text-center">' + detail.gst + '</td>';
							details += '<td class="align-middle text-center">' + detail.fintech_charges + '</td>';
							details += '<td class="align-middle text-center">' + detail.packaging_charges + '</td>';
							details += '<td class="align-middle text-center">' + detail.deductable + '</td>';
							details += '<td class="align-middle text-center">' + detail.payable + '</td>';
							details += '</tr>';
						});

						details += '</tbody></table>';

						$('#view_details .modal-body').html(details);

						$('#view_details').modal('show');
					});
				}
				else if ($(this).hasClass('make_payment')) {
					selected_rows = [];

					table.rows().deselect();

					table.button('.make_payment').disable();

					selected_rows.push(id);

					$('#make_payments #make_payments_form .total_amount').val(0);
					$('#make_payments #make_payments_form .total_charges').val(0);
					$('#make_payments #make_payments_form .total_gst').val(0);
					$('#make_payments #make_payments_form .total_deductable').val(0);
					$('#make_payments #make_payments_form .total_payable').val(0);
					$('#make_payments #make_payments_form .total_hold').val(0);
					$('#make_payments #make_payments_form .wht').val(0);

					$('#make_payments #make_payments_form button.make').prop('disabled', true);
					$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);

					$('#make_payments #make_payments_form .pending_payment_shipment_ids').val('');


					selected_rows_shipments = [];

					make_payments_table.clear().draw();

					$('#make_payments').modal('show');
				}
			});

			$('#make_payments').on('hide.bs.modal', function () {
				selected_rows = [];
				table.rows().deselect();
				table.button('.make_payment').disable();
			});

			function calculation(parent) {
				var id = parseInt(parent.attr('id'));

				var index = $.inArray(id, selected_rows_shipments);

				var total_amount_selector = $('#make_payments #make_payments_form .total_amount');
				var total_charges_selector = $('#make_payments #make_payments_form .total_charges');
				var total_gst_selector = $('#make_payments #make_payments_form .total_gst');
				var total_deductable_selector = $('#make_payments #make_payments_form .total_deductable');
				var total_payable_selector = $('#make_payments #make_payments_form .total_payable');
				var total_hold_selector = $('#make_payments #make_payments_form .total_hold');
				var total_wht_selector = $('#make_payments #make_payments_form .wht');

				if (index === -1) {
					selected_rows_shipments.push(id);

					var total_amount = ((total_amount_selector.val() != '') ? parseInt(total_amount_selector.val()) : 0) + ((parent.children('td.amount').html() != '') ? parseInt(parent.children('td.amount').html().replace(/,/g, '')) : 0);
					var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector.val()) : 0) + ((parent.children('td.charges').html() != '') ? parseFloat(parent.children('td.charges').html().replace(/,/g, '')) : 0);
					var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) + ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html().replace(/,/g, '')) : 0);
					var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(total_deductable_selector.val()) : 0) + ((parent.children('td.deductable').html() != '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g, '')) : 0);
					var total_wht = ((total_wht_selector.val() != '') ? parseFloat(total_wht_selector.val()) : 0) + ((parent.children('td.wht').html() != '') ? parseFloat(parent.children('td.wht').html().replace(/,/g, '')) : 0);
					var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector.val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html().replace(/,/g, '')) : 0);
					var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html().replace(/,/g, '')) : 0);
				}
				else {
					selected_rows_shipments.splice(index, 1);

					var total_amount = ((total_amount_selector.val() != '') ? parseInt(total_amount_selector.val()) : 0) - ((parent.children('td.amount').html() != '') ? parseInt(parent.children('td.amount').html().replace(/,/g, '')) : 0);
					var total_charges = ((total_charges_selector.val() != '') ? parseFloat(total_charges_selector.val()) : 0) - ((parent.children('td.charges').html() != '') ? parseFloat(parent.children('td.charges').html().replace(/,/g, '')) : 0);
					var total_gst = ((total_gst_selector.val() != '') ? parseFloat(total_gst_selector.val()) : 0) - ((parent.children('td.gst').html() != '') ? parseFloat(parent.children('td.gst').html().replace(/,/g, '')) : 0);
					var total_deductable = ((total_deductable_selector.val() != '') ? parseFloat(total_deductable_selector.val()) : 0) - ((parent.children('td.deductable').html() != '') ? parseFloat(parent.children('td.deductable').html().replace(/,/g, '')) : 0);
					var total_payable = ((total_payable_selector.val() != '') ? parseFloat(total_payable_selector.val()) : 0) - ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html().replace(/,/g, '')) : 0);
					var total_hold = ((total_hold_selector.val() != '') ? parseFloat(total_hold_selector.val()) : 0) + ((parent.children('td.payable').html() != '') ? parseFloat(parent.children('td.payable').html().replace(/,/g, '')) : 0);
				}

				if (selected_rows_shipments.length > 0) {
					total_amount_selector.val(parseInt(total_amount));
					total_charges_selector.val(parseFloat(total_charges).toFixed(2));
					total_gst_selector.val(parseFloat(total_gst).toFixed(2));
					total_deductable_selector.val(parseFloat(total_deductable).toFixed(2));
					total_wht_selector.val(parseFloat(total_wht).toFixed(2));
					total_payable_selector.val(parseFloat(total_payable).toFixed(2));
					total_hold_selector.val(parseFloat(total_hold).toFixed(2));

					$('#make_payments #make_payments_form button.make').prop('disabled', false);
					$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', false);
				}
				else {
					total_amount_selector.val(0);
					total_charges_selector.val(0);
					total_gst_selector.val(0);
					total_deductable_selector.val(0);
					total_wht_selector.val(0);
					total_payable_selector.val(0);
					total_hold_selector.val(parseFloat(initial_total_hold).toFixed(2));

					$('#make_payments #make_payments_form button.make').prop('disabled', true);
					$('#make_payments #make_payments_form button.export_bank_order').prop('disabled', true);
				}

				$('#make_payments #make_payments_form .pending_payment_shipment_ids').val(selected_rows_shipments);
			}

			$('#make_payments #make_payments_datatable tbody').on('click', 'tr td.select-checkbox', function() {

				var parent = $(this).parent('tr');
				var selected_id = $(this).parent('tr').attr('id');
				var con_id = parseInt($(this).parent('tr').attr('consolidation_id'));
				if(con_id){
					var count = 0;
					make_payments_table.rows().nodes().each(function(index) {
						var row = make_payments_table.row(index);
						var consolidation_id = $(row.node()).attr('consolidation_id');
						var row_id = $(row.node()).attr('id');
						if(con_id == consolidation_id){
							if ($(row.node().firstChild).hasClass('select-checkbox') && !$(row.node()).hasClass('selected')) {
								var parent = $(row.node());

								calculation(parent);
								if(selected_id != row_id){
									row.select();
								}
								count++;
							}else{
								var parent = $(row.node());

								calculation(parent);
								if(selected_id != row_id){
									row.deselect();
								}
								count++;
							}
						}

					});

				}else{
					calculation(parent);
				}

			});

			var shipments_array = [];
			function verify_make_payments(form){
				$.ajax({
					url: '{!! route('admin.finance.make_payments.verify') !!}',
					method: 'POST',
					data: {
						'_token': '{{ csrf_token() }}',
						'pending_payment_shipment_ids': $('#make_payments #make_payments_form .pending_payment_shipment_ids').val()
					}
				})
				.done(function(data) {
					if (data.status == 0) {
						if (data.duplicate_shipments && data.over_payments) {
							var html = 'The following Shipment(s) have Duplicate Same Type Payments:<br/>';

							$.each(data.duplicate_shipments, function(index, duplicate_shipment) {
								html += duplicate_shipment + '<br/>';
							});

							html += 'The following Shipment(s) have Payments above the Limit:<br/>';

							$.each(data.over_payments, function(index, over_payment) {
								html += over_payment.shipper + ' ' + '<b>' + over_payment.payable + '</b>' + '<br/>';
							});

							html += '<br/>Are you sure, you want to make the Payments?';

							content = document.createElement('div');
							content.innerHTML = html;

							swal({
								content: content,
								icon: 'warning',
								buttons: {
									cancel: {
										text: 'No',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Yes',
										value: true,
										visible: true,
										closeModal: true
									}
								},
								closeOnClickOutside: false,
								closeOnEsc	: false,
								dangerMode: true
							}).then(function(confirm) {
								if (confirm) {
									$('#make_payments #make_payments_form button').remove();

									form.submit();
								}
							});
						}
						else if (data.duplicate_shipments) {
							var html = 'The following Shipment(s) have Duplicate Same Type Payments:<br/>';

							$.each(data.duplicate_shipments, function(index, duplicate_shipment) {
								html += duplicate_shipment + '<br/>';
							});

							html += '<br/>Are you sure, you want to make the Payments?';

							content = document.createElement('div');
							content.innerHTML = html;

							swal({
								content: content,
								icon: 'warning',
								buttons: {
									cancel: {
										text: 'No',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Yes',
										value: true,
										visible: true,
										closeModal: true
									}
								},
								closeOnClickOutside: false,
								closeOnEsc	: false,
								dangerMode: true
							}).then(function(confirm) {
								if (confirm) {
									$('#make_payments #make_payments_form button').remove();

									form.submit();
								}
							});
						}
						else if (data.over_payments) {
							var html = 'The following Shipment(s) have Payments above the Limit:<br/>';

							$.each(data.over_payments, function(index, over_payment) {
								html += over_payment.shipper + ': ' + '<b>' + over_payment.payable + '</b>' + '<br/>';
							});

							html += '<br/>Are you sure, you want to make the Payments?';

							content = document.createElement('div');
							content.innerHTML = html;

							swal({
								content: content,
								icon: 'warning',
								buttons: {
									cancel: {
										text: 'No',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Yes',
										value: true,
										visible: true,
										closeModal: true
									}
								},
								closeOnClickOutside: false,
								closeOnEsc	: false,
								dangerMode: true
							}).then(function(confirm) {
								if (confirm) {
									$('#make_payments #make_payments_form button').remove();

									form.submit();
								}
							});
						}
						else {
							swal({
								text: 'Are you sure, you want to make the Payments?',
								icon: 'warning',
								buttons: {
									cancel: {
										text: 'No',
										value: null,
										visible: true,
										closeModal: true,
									},
									confirm: {
										text: 'Yes',
										value: true,
										visible: true,
										closeModal: true
									}
								},
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true
							}).then(function(confirm) {
								if (confirm) {
									$('#make_payments #make_payments_form button').remove();

									form.submit();
								}
							});
						}
					}
					else if (data.status == 1) {
						var html = 'Cannot proceed since following Shipper(s) have Overall Negative Payment(s) Selected:<br/>';

						$.each(data.negative_payments, function(index, shipper) {
							html += shipper + '<br/>';
						});

						content = document.createElement('div');
						content.innerHTML = html;

						swal({
							content: content,
							icon: 'warning',
							buttons: {
								cancel: {
									text: 'Close',
									value: null,
									visible: true,
									closeModal: true,
								}
							},
							closeOnClickOutside: false,
							closeOnEsc: false,
							dangerMode: true
						});
					}
					else {
						var html = 'Shipper: <b>' + data.merged_account_negative.shipper + '</b> Sister Account(s) have Overall Negative Payable:<br/>';

						$.each(data.merged_account_negative.merged_account, function(index, account) {
							html += account + ': ' + data.merged_account_negative.payable[index];
						});

						html += '<br/>Are you sure, you want to make the Payments?';

						content = document.createElement('div');
						content.innerHTML = html;

						swal({
							content: content,
							icon: 'warning',
							buttons: {
								cancel: {
									text: 'No',
									value: null,
									visible: true,
									closeModal: true,
								},
								confirm: {
									text: 'Yes',
									value: true,
									visible: true,
									closeModal: true
								}
							},
							closeOnClickOutside: false,
							closeOnEsc: false,
							dangerMode: true
						}).then(function(confirm) {
							if (confirm) {
								$('#make_payments #make_payments_form button').remove();

								form.submit();
							}
						});
					}
				});
			}

			$('#star_shippers_filter').on('click',function () {
				$('#star_shippers_filter').val(1);
				table.draw(true);
				$('#star_shippers_filter').val(0);
			});

		});
	</script>
@endsection