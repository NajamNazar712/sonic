@extends('client.layout.master')

@section('title', 'Subsitute Accounts')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Substitute Accounts
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('client.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Name</th>
										<th class="border-primary border-darken-1">Phone Number</th>
										<th class="border-primary border-darken-1">Email</th>
										<th class="border-primary border-darken-1">CNIC</th>
										<th class="border-primary border-darken-1">Created Datetime</th>
										<th class="border-primary border-darken-1">Updated Datetime</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>


	<script>
		$(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('cod.substitute_account_management.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Name');
                            head.push('Phone Number');
                            head.push('Email');
                            head.push('CNIC');
                            head.push('Created Datetime');
                            head.push('Updated Datetime');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.phone_number);
                                row.push(values.email);
                                row.push(values.cnic);
                                row.push(values.created_at);
                                row.push(values.updated_at);
                                row.push(values.status);

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
				scrollX: true, scrollY: '350px',
				buttons: [{
					text: '<i class="la la-user-plus"></i> Add',
					className: 'btn btn-primary add',
					action: function (e, dt, node, config) {
						window.location = '{{ route('cod.substitute_account_management.add.index') }}';
					}
				},{
                    extend: 'excel',
                    title: 'Substitute Accounts',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }],
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('cod.substitute_account_management.list') }}',
				rowId: 'id',
				order: [[5, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'name', name: 'substitute_users.name', class: 'align-middle name'},
					{data: 'phone_number', name: 'substitute_users.phone_number', class: 'align-middle phone_number'},
					{data: 'email', name: 'substitute_users.email', class: 'align-middle email'},
					{data: 'cnic', name: 'substitute_users.cnic', class: 'align-middle cnic'},
					{data: 'created_at', name: 'substitute_users.created_at', class: 'align-middle created_at'},
					{data: 'updated_at', name: 'substitute_users.updated_at', class: 'align-middle updated_at'},
					{data: 'status', name: 'status', class: 'align-middle status'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disabled</option>' +
                        '<option value="1">Enabled</option>' +
                        '</select>';
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
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
                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				if ($(this).hasClass('edit')) {
					var link = '{{ route('cod.substitute_account_management.update.index', ["id" => 0]) }}';

					window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
				}
				else if ($(this).hasClass('enable')) {
					$.ajax({
						url: '{!! route('cod.substitute_account_management.status') !!}',
						method: 'POST',
						data: {
							'id': id,
							'status': 1,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							table.draw(false);

							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}
					});
				}
				else if ($(this).hasClass('disable')) {
					$.ajax({
						url: '{!! route('cod.substitute_account_management.status') !!}',
						method: 'POST',
						data: {
							'id': id,
							'status': 0,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {
						if (data.status == 0) {
							table.draw(false);

							toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
						}
						else {
							toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
						}
					});
				}
			});
		});
	</script>
@endsection