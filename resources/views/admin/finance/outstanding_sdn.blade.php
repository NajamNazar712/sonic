@extends('admin.layout.master')

@section('title', 'Outstanding SDN')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Outstanding SDN
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')
							<form id="excel_upload_form" class="form-horizontal" method="POST" action="{{ route('admin.finance.outstanding_sdn.reconcile_delivery_notes_excel') }}" novalidate="novalidate" enctype="multipart/form-data">
								{{ csrf_field() }}

								<div class="row align-items-center justify-content-center">
									<div class="col">
										<div class="form-group">
											<input type="file" name="excel" class="w-100 p-1 border-primary" title="Select File" data-rule-required="true" data-msg-required="File is required" data-rule-extension="xls|xlsx" data-msg-extension="Only file with extension xls or xlsx allowed" data-rule-accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-msg-accept="Only Excel file allowed" data-rule-maxsize="5242880" data-msg-maxsize="File Size must not exceed 5 MB (5120 KB).">
										</div>
									</div>
									<div class="col">
										<div class="form-group text-left">
											<button type="submit" name="upload" class="btn btn-primary">Upload</button>
										</div>
									</div>

									<div class="col ml-auto">
										<div class="form-group text-right">
											<a href="{{ asset('file/Reconcile Outstanding SDN Template.xlsx') }}?v=14_09_2021" class="btn btn-primary"><i class="la la-download"></i> Download Template</a>
										</div>
									</div>
								</div>
							</form>

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">SDN No.</th>
										<th class="border-primary border-darken-1">Hub</th>
										<th class="border-primary border-darken-1">DNCCs</th>
										<th class="border-primary border-darken-1">Delivered Shipments</th>
										<th class="border-primary border-darken-1">DNCC Amount</th>
										<th class="border-primary border-darken-1">Deposited Amount</th>
										<th class="border-primary border-darken-1">Deposited by</th>
										{{--<th class="border-primary border-darken-1">Company Bank</th>--}}
										<th class="border-primary border-darken-1">Deposited Datetime</th>
										<th class="border-primary border-darken-1">Adjustment Date</th>
										<th class="border-primary border-darken-1">Adjustment Amount</th>
										<th class="border-primary border-darken-1">Adjustment Reference</th>
										<th class="border-primary border-darken-1">Deposit Slip</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="modal fade" id="reconcile_delivery_notes" role="dialog" aria-labelledby="reconcile_delivery_notes_title" aria-hidden="true">
								<div class="modal-dialog modal-lg modal-full-length" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="reconcile_delivery_notes_title">Reconcile Delivery Notes of SDN</h4>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">×</span>
											</button>
										</div>
										<div class="modal-body">
											<table class="table table-bordered datatable" id="reconcile_delivery_notes_datatable" style="z-index: 3;">
												<thead>
													<tr role="row" class="bg-primary white">
														<th class="border-primary border-darken-1"></th>
														<th class="border-primary border-darken-1">S. No.</th>
														<th class="border-primary border-darken-1">Delivery Note No.</th>
														<th class="border-primary border-darken-1">Hub</th>
														<th class="border-primary border-darken-1">Rider</th>
														<th class="border-primary border-darken-1">Shipments</th>
														<th class="border-primary border-darken-1">Shipments Delivered</th>
														<th class="border-primary border-darken-1">Assigned by</th>
														<th class="border-primary border-darken-1">Assigned Datetime</th>
														<th class="border-primary border-darken-1">Updated by</th>
														<th class="border-primary border-darken-1">Updated Datetime</th>
														<th class="border-primary border-darken-1">DNCC Amount</th>
													</tr>
												</thead>
											</table>

											<form id="reconcile_delivery_notes_form" class="form-inline mt-2 mb-1 justify-content-center" novalidate="novalidate" method="POST" action="{{ route('admin.finance.outstanding_sdn.reconcile_delivery_notes') }}">
												{{ csrf_field() }}

												<input type="hidden" name="station_deposit_note_id" class="station_deposit_note_id">
												<input type="hidden" name="delivery_note_ids" class="delivery_note_ids">
												<div class="col-8">
													<table class="table table-bordered mb-0">
														<thead>
														<tr class="border-bottom-active border-custom-color">
															<th>Total Amount</th>
															<th>Deposited</th>
															<th>Difference</th>
														</tr>
														</thead>
														<tbody>
														<tr class="" id="sdn_amount_row">
															<td class="total"></td>
															<td class="deposited"></td>
															<td class="difference"></td>
														</tr>
														</tbody>
													</table>
												</div>
												<div class="col-2 text-right">
													<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
													<button type="submit" name="reconcile" class="btn btn-primary reconcile">Reconcile</button>
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
	</div>

	<!--Shipments popup -->
	<div class="modal fade" id="dncc_modal" data-backdrop="static" role="dialog" aria-labelledby="dncc_modal" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="dncc_modal_title">No. Of DNCC(s)</h4>

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
	<!--Shipments popup -->
    <!--Shipments popup -->
    <div class="modal fade" id="delivered_shipments_modal" data-backdrop="static" role="dialog" aria-labelledby="delivered_shipments_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="delivered_shipments_modal_title">Delivered Shipment(s)</h4>

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
    <!--Shipments popup -->
	<div class="modal fade text-left" id="ViewDepositSlip" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ViewDepositSlip"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary white">
					<h4 class="modal-title white">Deposit Slips View</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<table class="table table-bordered datatable" id="deposit_slip_table" style="z-index: 3;">
						<thead>
						<tr role="row" class="bg-primary white">

							<th class="border-primary border-darken-1">S. No.</th>
							<th class="border-primary border-darken-1">Date</th>
							<th class="border-primary border-darken-1">Bank Name</th>
							<th class="border-primary border-darken-1">Amount </th>
							<th class="border-primary border-darken-1">Deposit Slip</th>

						</tr>
						</thead>
					</table>

					<hr>
					<div class="row justify-content-center">
						<div class="col-3">
							<button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--Deposit Slip Modal -->
	<div class="modal fade text-left" id="EditDepositSlip" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="EditDepositSlip"
		 aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary white">
					<h4 class="modal-title white">Edit Deposit Slip</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<form id="edit_deposit_slip_form" class="form" action="{{route('admin.finance.outstanding_sdn.edit')}}" method="post" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="sdn_id" id="sdn_id"/>
						<input type="hidden" name="deposit_rows" id="deposit_rows"/>
						<input type="hidden" name="new_deposit_rows" id="new_deposit_rows"/>
						
						<table class="table table-bordered datatable" id="edit_deposit_slip_table" style="z-index: 3;">
							<thead>
							<tr role="row" class="bg-primary white">

								<th class="border-primary border-darken-1">S. No.</th>
								<th class="border-primary border-darken-1">Date</th>
								<th class="border-primary border-darken-1">Bank Name</th>
								<th class="border-primary border-darken-1">Amount </th>
								<th class="border-primary border-darken-1">Deposit Slip</th>
								<th class="border-primary border-darken-1"></th>

							</tr>
							</thead>
						</table>
						<hr>
						<div class="row justify-content-center">
							<div class="col-3">
								<button type="submit" class="btn btn-primary btn-block">Update</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--Deposit Slip Modal -->

	<div class="modal fade" id="adjustment_reference_modal" data-backdrop="static" role="dialog" aria-labelledby="adjustment_reference_modal" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="adjustment_reference_modal_title">Adjustment Reference(s)</h4>

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

	<div class="modal fade text-left" id="AddAdjustmentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddAdjustmentModal"
		 aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary white">
					<h4 class="modal-title white">Add Adjustment For SDN</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<form id="sdn_adjustment_add" class="form" action="{{route('admin.delivery.sdn.adjustment.add')}}" method="post">
						@csrf
						<input type="hidden" name="sdn_id" id="sdn_id_for_adjustment">
						<input type="hidden" name="sdn_rows" id="sdn_rows_for_adjustment">
						<table class="table table-bordered datatable" id="sdn_adjustment_table" style="z-index: 3;width: 100%;">
							<thead>
							<tr role="row" class="bg-primary white">
								<th class="border-primary border-darken-1"></th>
								<th class="border-primary border-darken-1">Statement #</th>
								<th class="border-primary border-darken-1">Statement Creation Date</th>
								<th class="border-primary border-darken-1">Amount</th>
								<th class="border-primary border-darken-1">Action</th>

							</tr>
							</thead>
							<tfoot>
							<tr>
								<td colspan="3">Total</td>
								<td></td>
								<td></td>
							</tr>
							</tfoot>
						</table>

						<hr>
						<div class="row justify-content-center">
							<div class="col-3">
								<button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Close</button>
							</div>
							<div class="col-3">
								<button type="submit" class="btn btn-primary btn-block">Add Adjustment</button>
							</div>

						</div>
					</form>
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
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {

			

			function print(id) {
				$.ajax({
					url: '{!! route('admin.delivery.sdn.print') !!}',
					method: 'POST',
					data: {
						'id': id,
						'_token': '{{ csrf_token() }}'
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
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.finance.outstanding_sdn.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('SDN No.');
                            head.push('Hub');
                            head.push('DNCCs');
                            head.push('Delivered Shipments');
                            head.push('DNCC Amount');
                            head.push('Deposited Amount');
                            head.push('Deposited By');
                            // head.push('Company Bank');
                            head.push('Deposited Datetime');
                            head.push('Adjustment Date');
                            head.push('Adjustment Amount');
                            head.push('Adjustment Reference');



                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.sdn_number_padded);
                                row.push(values.hub);
                                row.push(values.dncc_count);
                                row.push(values.sdn_delivered_shipments);
                                row.push(values.sdn_amount);
                                row.push(values.sdn_deposit_amount);
                                row.push(values.deposited_by);
                                // row.push(values.bank);
                                row.push(values.deposited_at);
                                row.push(values.adjustment_date);
                                row.push(values.adjustment_amount);
                                row.push(values.adjusted_reference_count);
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
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Outstanding SDN',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
					'reset'],
				scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.finance.outstanding_sdn.list') }}',
				rowId: 'id',
				order: [[8, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'sdn_number', name: 'station_deposit_notes.id', class: 'align-middle text-center sdn_number'},
					{data:'hub', name: 'h.id', class: 'align-middle hub'},
					{data:'dncc_count_link', name: 'station_deposit_notes.dncc_count', class: 'align-middle dnccs dncc_count_link text-center'},
					{data:'delivered_shipments_link', name: 'sdn_delivered_shipments', class: 'align-middle delivered_shipments_link text-center'},
					{data:'sdn_amount', name: 'station_deposit_notes.sdn_amount', class: 'align-middle amount total_amount'},
					{data:'sdn_deposit_amount', name: 'station_deposit_notes.sdn_deposit_amount', class: 'align-middle amount deposited_amount'},
					{data:'deposited_by', name: 'a.name', class: 'align-middle deposited_by'},
					// {data:'bank', name: 'bank', class: 'align-middle bank'},
					{data:'deposited_at', name: 'station_deposit_notes.created_at', class: 'align-middle deposited_at'},
                    {data:'adjustment_date' ,name: 'station_deposit_notes.adjustment_date', class: 'align-middle adjustment_date'},
                    {data:'sdn_adjustment_amount' ,name: 'station_deposit_notes.adjustment_amount', class: 'align-middle adjustment_amount'},
                    {data:'adjusted_reference_link' ,name: 'station_deposit_notes.adjustment_ref', class: 'align-middle text-center adjustment_ref'},
					{data:'deposit_slip', name: 'deposit_slip', class: 'align-middle deposit_slip', orderable: false, searchable: false},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();

					$('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var bank_select = '<select name="bank_select" id="bank_select" class="select2 form-control"></select>';
                    var hub_select = '<select name="hub_select" id="hub_select" class="select2 form-control"></select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.deposit_slip') || $(header).is('.action')) {
							$(td).appendTo($(search));
						}else if($(header).is('.bank')){
                            $(bank_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }else if($(header).is('.hub')){
                            $(hub_select).appendTo($(search))
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
                    var data1 = $.map({!! $hubs !!}, function (obj) {
                        obj.id = obj.id;
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#hub_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Hub",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

			var station_deposit_note_id = null;

			var selected_rows = [];

			var reconcile_delivery_notes_table = $('#reconcile_delivery_notes #reconcile_delivery_notes_datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [{
					extend: 'selectAll',
                    text: 'Select All',
                    className: 'select_all',
                    action : function(e) {
                        e.preventDefault();

                        reconcile_delivery_notes_table.rows().nodes().each(function(index) {
                            var row = reconcile_delivery_notes_table.row(index);

                            if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                row.select();

                                id = parseInt(row.id());

                                var index = $.inArray(id, selected_rows);

                                if (index === -1) {
                                    selected_rows.push(id);
                                }
                            }
                        });

                        $('#reconcile_delivery_notes #reconcile_delivery_notes_form .delivery_note_ids').val(selected_rows);
                    }
                }, {
                    extend: 'selectNone',
                    text: 'Select None',
                    className: 'select_none',
                    action : function(e) {
                        e.preventDefault();

                        reconcile_delivery_notes_table.rows().nodes().each(function(index) {
                          var row = reconcile_delivery_notes_table.row(index);

                          if ($(row.node().firstChild).hasClass('select-checkbox')) {
                            row.deselect();

                            id = parseInt(row.id());

                            var index = $.inArray(id, selected_rows);

                            if (index !== -1) {
                                selected_rows.splice(index, 1);
                            }
                          }
                        });

                        $('#reconcile_delivery_notes #reconcile_delivery_notes_form .delivery_note_ids').val(selected_rows);
                    }
                }],
				scrollX: true, scrollY: false,
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
					url: '{{ route('admin.finance.outstanding_sdn.delivery_notes_list') }}',
					data: function (d) {
						d.id = station_deposit_note_id;
					}
				},
				rowId: 'id',
				order: [[10, 'desc']],
				columns: [
					{data: 'dn.id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data:'delivery_note_number', name: 'dn.id', class: 'align-middle text-center delivery_note_number'},
					{data:'hub', name: 'h.name', class: 'align-middle hub'},
					{data:'rider', name: 'ri.name', class: 'align-middle rider'},
					{data:'shipments', name: 'dn.shipments_count', class: 'align-middle shipments'},
					{data:'delivered_shipments', name: 'dn.delivered_shipments', class: 'align-middle delivered_shipments'},
					{data:'assigned_by', name: 'a.name', class: 'align-middle assigned_by'},
					{data:'assigned_at', name: 'dn.created_at', class: 'align-middle assigned_at'},
					{data:'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data:'updated_at', name: 'dn.updated_at', class: 'align-middle updated_at'},
					{data:'dncc_amount', name: 'dn.received_cod_amount', class: 'align-middle dncc_amount'}
				],
				rowCallback: function(row, data, index) {
					$('td:eq(1)', row).html(index + 1);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.select') || $(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
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

					this.api().table().columns.adjust();
				}
			});

			$('#datatable tbody').on('click', 'tr td.sdn_number button', function() {
				print(parseInt($(this).parents('tr').attr('id')));
			});

            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('reconcile_delivery_notes')) {
				    var total_amount = $(this).parents('tr').find('td.total_amount').text();
				    var number_total_amount = total_amount.split(',').join('');
				    var deposited_amount = $(this).parents('tr').find('td.deposited_amount').text();
                    var number_deposited_amount = deposited_amount.split(',').join('');
                    var difference_amount = parseInt(number_total_amount) - parseInt(number_deposited_amount);
                    difference_amount = numberWithCommas(difference_amount);
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_dncc_amount').val('');
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_expense').val('');
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .total_net_amount').val('');
					$('#reconcile_delivery_notes #reconcile_delivery_notes_form .station_deposit_note_id').val(id);
                    $('#reconcile_delivery_notes #sdn_amount_row td.total').text('');
                    $('#reconcile_delivery_notes #sdn_amount_row td.deposited').text('');
                    $('#reconcile_delivery_notes #sdn_amount_row td.difference').text('');
					station_deposit_note_id = id;

					selected_rows = [];

					reconcile_delivery_notes_table.clear().draw();
					$('#reconcile_delivery_notes #sdn_amount_row td.total').text(total_amount);
					$('#reconcile_delivery_notes #sdn_amount_row td.deposited').text(deposited_amount);
					$('#reconcile_delivery_notes #sdn_amount_row td.difference').text(difference_amount);
					$('#reconcile_delivery_notes').modal('show');
				}
				else if ($(this).hasClass('export_to_excel')) {
					window.open('{!! route('admin.finance.outstanding_sdn.export_to_excel') !!}?id=' + id, '_blank');
				}
			});

			$('#reconcile_delivery_notes #reconcile_delivery_notes_datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var parent = $(this).parent('tr');

				var id = parseInt(parent.attr('id'));

				var index = $.inArray(id, selected_rows);

				if (index === -1) {
					selected_rows.push(id);
				}
				else {
					selected_rows.splice(index, 1);
				}

				$('#reconcile_delivery_notes #reconcile_delivery_notes_form .delivery_note_ids').val(selected_rows);
			});

            var route = '{!! route('admin.tracking.index') !!}';
            $('#datatable tbody').on('click','tr td.dncc_count_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#dncc_modal .modal-body').html('');
                $('#dncc_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.finance.outstanding_sdn.dncc') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sdn_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var notes = '<div>DNCC Number(s) :</div>';

                            if (data.delivery_notes) {
                                $.each(data.delivery_notes, function(index, value) {
                                    notes += '<u><a href="javascript:void(0);" class="dncc_print" dnid="'+value+'">'+value+'</a></u><br>';
                                });
                            }
                            $('#dncc_modal .modal-body').html(notes);
                        }
                    });

            });
            $('#datatable tbody').on('click','tr td.delivered_shipments_link button',function () {
                var id = parseInt($(this).parents('tr').attr('id'));
                $('#delivered_shipments_modal .modal-body').html('');
                $('#delivered_shipments_modal').modal('show');

                $.ajax({
                    url: '{!! route('admin.finance.outstanding_sdn.shipments.delivered') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'sdn_id': id
                    }
                })
                    .done(function(data) {
                        if (data) {
                            var html = '<div><b>Delivered Shipment(s) :</b></div>';

                            if (data.shipments) {
                                $.each(data.shipments, function(index, value) {
                                    html += 'DNCC Number '+ index +': <br>';
                                    $.each(value, function (ind, tracking_number) {
                                        html += '<u><a href='+route+'?tracking_number='+tracking_number+' target="_blank">'+tracking_number+'</a></u><br>';
                                    });
                                });
                            }
                            $('#delivered_shipments_modal .modal-body').html(html);

                        }
                    });

            });
            $('body').on('click','a.dncc_print',function(){
                var id = parseInt($(this).attr('dnid'));
                printDNCC(id);
            });
            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.dncc.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
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

            var deposit_slip_table;
            $('body').on('click','.deposit_slip_view', function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
                    $.ajax({
                        url:'{!! route('admin.delivery.sdn.slip_view') !!}',
                        type:'POST',
                        data: {
                            'sdn_id':id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        if(data.status){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                        }else{

                            $('#ViewDepositSlip').modal('show');
                            deposit_slip_table = $('#deposit_slip_table').DataTable({
                                dom: 'ltipr',
                                ordering:false,
                                paging:false,
                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'date', class: 'align-middle date date-col-width form-group'},
                                    {name: 'bank_name', class: 'align-middle bank_name form-group'},
                                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                                    {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'}
                                ],

                                rowCallback: function(row, data, index) {
                                    var info = deposit_slip_table.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                }
                            });
							deposit_slip_table.clear();
                            $.each(data.slips, function (index, value) {
                                deposit_slip_table.row.add([0, value.date, value.bank, value.amount, value.image]);
                                deposit_slip_table.draw(true);
                            });
                        }
                    });
                }
            });

            $('#ViewDepositSlip').on('hidden.bs.modal', function () {
                deposit_slip_table.clear();
                deposit_slip_table.destroy();
            });

            var banks_list = $.map({!! $banks !!}, function (obj) {
                obj.id = obj.id;
                obj.text = obj.name;
                return obj;
            });
			var selected_deposit_ids = [];
			var new_selected_deposit_ids = [];
			var rows_count = 0;

			function add_row() {
                    rows_count++;
                    var date_input = '<div class="form-group input-group input-group-sm mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="new_date['+rows_count+']" id="new_deposit_date_' + rows_count + '" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required"></div>';
                    var bank_select = '<select class="form-control hub_select select2" name="new_bank['+rows_count+']" data-rule-required="true" data-msg-required="Bank is required"></select>';
                    var amount_input = '<input class="form-control form-control-sm amount" name="new_amount['+rows_count+']" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required">';
                    var deposit_slip = '<input class="form-control form-control-sm" type="file" name="new_deposit_slip_'+rows_count+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB)." data-rule-required="true" data-msg-required="Deposit Slip is required">';
                        var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger remove_row"><i class="la la-close"></i></a>';

                    // deposit_table.row.add(0,1,2,3,4,5);
                    edit_deposit_table.row.add([0, date_input,bank_select,amount_input,deposit_slip,remove]).node().id = rows_count;
                    edit_deposit_table.draw(true);
                    // $('#sdn_upload_table tbody').append(html);
                    // $('#DepositSlipButton').attr('disabled', false);
                    new_selected_deposit_ids.push(rows_count);
                    $('select[name="new_bank['+rows_count+']"]').prepend('<option value="" selected="selected"></option>').select2({
                        data:banks_list,
                        placeholder:'Select Bank',
                        allowClear:true,
                        width:'100%',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    $('#new_deposit_date_' + rows_count).pickadate({
                        firstDay: 1,
                        today: '',
                        clear: '',
                        close: '',
                        weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                        showMonthsShort: true,
                        formatSubmit: 'yyyy-mm-dd 00:00:00',
                        hiddenSuffix: '_formatted',
                        onOpen: function() {
                            // $('#deposit_date_' + rows_count+'_root').css('top', '-262px');
                        },
                    });
                    $('input.amount').inputmask({
                        'alias': 'decimal',
                        'allowMinus': false,
                        'allowPlus': false,
                        'rightAlign': false,
                        'digits': 2,
                        'min': 0.00,
                        'max': 10000000.00
                    });
                }


            var edit_deposit_table;
            $('#datatable tbody').on('click','.edit_deposit_slip', function(){
                var sdn_id = $(this).parents('tr').attr('id');
                if(sdn_id){
                    $.ajax({
                        url:'{!! route('admin.finance.outstanding_sdn.deposit_slip_list') !!}',
                        type:'POST',
                        data: {
                            'sdn_id':sdn_id,
                            '_token': '{{ csrf_token() }}'
                        }
                    }).done(function (data) {
                        $('#sdn_id').val(sdn_id);
						if(data.status){
                            toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}else{

						    $('#EditDepositSlip').modal('show');
                            edit_deposit_table = $('#edit_deposit_slip_table').DataTable({
                                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                                 buttons:[{
			                        title: 'Add Row',
			                        className: 'btn btn-primary mb-1',
			                        text: '<i class="la la-plus"></i> Add Row',
			                        action:function (e) {
			                            add_row();
			                        }
			                    }],
                                ordering:false,
                                paging:false,
                                columns: [
                                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                                    {name: 'date', class: 'align-middle date date-col-width form-group'},
                                    {name: 'bank_name', class: 'align-middle bank_name form-group'},
                                    {name: 'amount', class: 'align-middle expense_amount form-group'},
                                    {name: 'deposit_slip', class: 'align-middle deposit_slip form-group'},
                        			{name: 'action', class: 'align-middle action'}

                                ],

                                rowCallback: function(row, data, index) {
                                    var info = edit_deposit_table.page.info();

                                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                                },
                                initComplete: function() {

                                    // this.api().table().columns.adjust();
                                }
                            });
                            var image_url = '{{asset('uploads/sdn/')}}';
                            $.each(data.slips, function (index, value) {
                            	
                                var date_input = '<div class="form-group input-group input-group-sm mb-0"><div class="input-group-prepend"><span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left"><span class="la la-calendar-o"></span></span></div><input type="text" name="date['+index+']" id="deposit_date_' + index + '" class="form-control pickadate-short-string bg-primary border-primary white rounded-right" placeholder="Date*" data-rule-required="true" data-msg-required="Date is required" data-value="'+ value.date +'"></div>';
                                var bank_select = '<select class="form-control hub_select select2" name="bank['+index+']" data-rule-required="true" data-msg-required="Bank is required"></select>';
                                var amount_input = '<input class="form-control form-control-sm amount" name="amount['+index+']" placeholder="Amount*" data-rule-required="true" data-msg-required="Amount is required" value="'+ value.amount +'">';
                                var deposit_slip = '<div class="text-center"><button type="button" class="btn btn-primary btn-sm"><a class="white" href="'+ image_url +'/'+ value.image +'" target="_blank">View</a></button><input type="hidden" name="old_deposit_slip_'+ index +'" value="'+value.image+'">';
                                deposit_slip += '<input class="form-control form-control-sm" type="file" name="deposit_slip_'+index+'" data-rule-extension="jpeg|jpg|png" data-msg-extension="Only file with extension jpeg, jpg or png allowed" data-rule-accept="image/*" data-msg-accept="Only Image file allowed" data-rule-maxsize="2097152" data-msg-maxsize="File Size must not exceed 2 MB (2048 KB).">';
                                var remove = '';
                                edit_deposit_table.row.add([0, date_input,bank_select,amount_input,deposit_slip, remove]).node().id = index;
                                edit_deposit_table.draw(true);

                                selected_deposit_ids.push(index);
                                $('select[name="bank['+index+']"]').prepend('<option value="" selected="selected"></option>').select2({
                                    data:banks_list,
                                    placeholder:'Select Bank',
                                    allowClear:true,
                                    width:'100%',
                                    dropdownCssClass: 'form-control-sm p-0'
                                });
                                $('select[name="bank['+index+']"]').val(value.bank).trigger('change');
                                $('#deposit_date_' + index).pickadate({
                                    firstDay: 1,
                                    today: '',
                                    clear: '',
                                    close: '',
                                    weekdaysShort: ['S', 'M', 'Tu', 'W', 'Th', 'F', 'S'],
                                    showMonthsShort: true,
                                    formatSubmit: 'yyyy-mm-dd 00:00:00',
                                    hiddenSuffix: '_formatted',
                                    onOpen: function() {
                                        // $('#deposit_date_' + rows_count+'_root').css('top', '-262px');
                                    },
                                });
                                $('input.amount').inputmask({
                                    'alias': 'decimal',
                                    'allowMinus': false,
                                    'allowPlus': false,
                                    'rightAlign': false,
                                    'digits': 2,
                                    'min': 0.00,
                                    'max': 10000000.00
                                });
                            });

						}
					});
				}
			});
            $('#EditDepositSlip').on('hidden.bs.modal', function () {
                edit_deposit_table.clear();
                edit_deposit_table.destroy();
                selected_deposit_ids = [];
                new_selected_deposit_ids = [];
            });
            $('#edit_deposit_slip_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var pressed_button = $(this.submitButton);
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Deposit Slip!',
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
                    }).then(function (confirm) {
                        if (confirm) {
                            // $(form).append('<input type="hidden" name="' + pressed_button.attr('name') + '" value="' + pressed_button.attr('value') + '">');
                            $('#deposit_rows').val(selected_deposit_ids);
                            $('#new_deposit_rows').val(new_selected_deposit_ids);
                            // console.log($('#upload_image').val());
                            form.submit();
                        }
                    });

                }
            });

			$('#datatable tbody').on('click','tr td.adjustment_ref button',function () {
				var id = parseInt($(this).parents('tr').attr('id'));
				if(id) {
					$.ajax({
						url: '{!! route('admin.delivery.sdn.get.adjustment_reference') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'sdn_id': id,
						}
					})
							.done(function (data) {
								if (data.status == 1) {
									var notes = '';

									notes += data.html
									$('#adjustment_reference_modal .modal-body').html('');
									$('#adjustment_reference_modal').modal('show');
									$('#adjustment_reference_modal .modal-body').html(notes);
								} else {
									toastr.error('Something went wrong!', 'Error!', {
										positionClass: 'toast-top-center',
										containerId: 'toast-top-center'
									});
								}
							});
				}

			});

            $('#reconcile_delivery_notes_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parent('.form-group'));
                },
                submitHandler: function(form) {
                    var count = 0;
                    reconcile_delivery_notes_table.rows().nodes().each(function(index) {
                        count++;
                            var row = reconcile_delivery_notes_table.row(index);

                            
                        });
                    var pressed_button = $(this.submitButton);
                    // console.log(selected_rows.length);
                    // console.log('total rows');
                    // console.log(count);
                    if(selected_rows.length!=count){

                        toastr.error('Please Select all delivery notes or sheets', 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }else{
                            form.submit();

                    }

                   
                }
            });


            $('body').on('click', '#edit_deposit_slip_table td a.remove_row',function () {
                var rid = parseInt($(this).parents('tr').attr('id'));
                var index = $.inArray(rid, new_selected_deposit_ids);

                if (index !== -1) {
                    new_selected_deposit_ids.splice(index, 1);
                }
                edit_deposit_table.row( $(this).parents('tr') ).remove().draw();
                console.log(new_selected_deposit_ids);
            });


			var sdn_adjustment_table;
			var selected_adjustment_rows = [];
			$('#datatable tbody').on('click', 'button.adjustment_add', function () {
				var sdn_id = $(this).parents('tr').attr('id');
				var options_html = "";
				if(sdn_id){
					var adjustment_rows_count = 0;
					$.ajax({
						url: '{!! route('admin.delivery.sdn.get.petty_cash_statements') !!}',
						method: 'POST',
						data: {
							'id': sdn_id,
							'_token': '{{ csrf_token() }}'
						}
					})
							.done(function(data) {
								if(data.status == 1)
								{
									$.each(data.data,function (key,value) {
										options_html += "<option value='"+value.id+"' data-date='"+value.date+"' data-amount='"+value.amount+"' >"+value.id+"</option>";
									});
									sdn_adjustment_table = $('#sdn_adjustment_table').DataTable({
										dom: '<"d-inline-block"l><"pull-right"B>tipr',
										buttons:[{
											title: 'Add Row',
											className: 'btn btn-primary mb-1',
											text: '<i class="la la-plus"></i> Add Row',
											action:function (e) {
												add_adjustment_row();
											}
										}],
										ordering:false,
										paging:false,
										columns: [
											{orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
											{name: 'statement', class: 'align-middle statement form-group', width: '20%'},
											{name: 'date', class: 'align-middle date form-group'},
											{name: 'amount', class: 'align-middle amount form-group'},
											{name: 'action', class: 'align-middle action'},
										],

										rowCallback: function(row, data, index) {
											var info = sdn_adjustment_table.page.info();

											$('td:eq(0)', row).html(index + 1 + info.page * info.length);

										},
										footerCallback: function(row, data, start, end, display) {
											var api = this.api();
											api.columns('.statement', {
												page: 'current'
											}).every(function() {
												$(this.footer()).html('Total Amount');
											});
											api.columns('.amount', {
												page: 'current'
											}).every(function() {
												amount = this
														.data()
														.reduce(function(a, b) {
															var x = parseFloat(a) || 0;
															var y = parseFloat(b) || 0;
															return x + y;
														}, 0);
												$(this.footer()).html(amount);
											});
										}
									});


									$('#sdn_id_for_adjustment').val(sdn_id);
									$('#AddAdjustmentModal').modal('show');

									function add_adjustment_row() {
										adjustment_rows_count++;
										var statement_select = '<select class="form-control statement_select select2 unique_statement" name="statement['+adjustment_rows_count+']" data-rule-required="true" data-msg-required="Statement is required"></select>';
										var amount_input = "<div class='amount_input'><div>";
										if(adjustment_rows_count == 1){
											var remove = '';
										}else{
											var remove = '<a href="javascript:void(0);" class="btn btn-icon btn-sm btn-danger adjustment_remove_row"><i class="la la-close"></i></a>';

										}
										sdn_adjustment_table.row.add([0, statement_select,'','',remove]).node().id = adjustment_rows_count;
										sdn_adjustment_table.draw(true);
										selected_adjustment_rows.push(adjustment_rows_count);
										$('select[name="statement['+adjustment_rows_count+']"]').prepend('<option value="" selected="selected" data-date="" data-amount=""></option>'+options_html).select2({
											placeholder:'Select Statement',
											width:'100%',
											dropdownCssClass: 'form-control-sm p-0'
										}).bind('change',function (){
											var date = $(this).find("option:selected").attr("data-date");
											var amount = $(this).find("option:selected").attr("data-amount");
											sdn_adjustment_table.row($(this).closest("tr")).data()[2] = date;
											sdn_adjustment_table.row($(this).closest("tr")).data()[3] = amount;
											$(this).closest("td").next("td").html(date);
											$(this).closest("td").next("td").next("td").html(amount);
											sdn_adjustment_table.draw();
										});
									}


									add_adjustment_row();
								}
								else{
									toastr.error(data.message, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							});


				}
			});

			$('body').on('click', 'a.adjustment_remove_row',function () {
				var rid = parseInt($(this).parents('tr').attr('id'));
				var index = $.inArray(rid, selected_adjustment_rows);

				if (index !== -1) {
					selected_adjustment_rows.splice(index, 1);
				}
				sdn_adjustment_table.row( $(this).parents('tr') ).remove().draw();
			});

			$('#AddAdjustmentModal').on('hidden.bs.modal', function () {
				sdn_adjustment_table.clear();
				sdn_adjustment_table.destroy();
				selected_adjustment_rows = [];
			});

			$.validator.addMethod("unique_statement", function(value, element) {
				var parentForm = $(element).closest('form');
				var timeRepeated = 0;
				if (value != '') {
					$(parentForm.find('.unique_statement')).each(function () {
						if ($(this).val() === value && value != 0) {
							timeRepeated++;
						}
					});
				}
				return timeRepeated === 1 || timeRepeated === 0;

			}, "Statement Can Not Be Duplicate");

			var sdn_form;
			sdn_form = $('#sdn_adjustment_add').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				},
				submitHandler: function(form) {
					swal({
						title: 'Are You Sure?',
						text: 'Select Yes to Adjust SDN!',
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
					}).then(function (confirm) {
						if (confirm) {
							$('#sdn_rows_for_adjustment').val(selected_adjustment_rows);
							form.submit();
						}
					});

				}
			});

			$('#excel_upload_form').validate({
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
						text: 'File is being Upload!',
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