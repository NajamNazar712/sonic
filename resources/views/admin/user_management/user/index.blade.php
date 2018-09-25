@extends('admin.layout.master')

@section('title', 'Users')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Users
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Name</th>
										<th class="border-primary border-darken-1">Phone Number</th>
										<th class="border-primary border-darken-1">Email</th>
										<th class="border-primary border-darken-1">CNIC</th>
										<th class="border-primary border-darken-1">Role</th>
										<th class="border-primary border-darken-1">Updated Datetime</th>
										<th class="border-primary border-darken-1">Updated by</th>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<style type="text/css">
		a.btn.btn-secondary{
			border-radius: 20px;
			background: #64a0d2;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.user_management.users.list') }}',
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
                            head.push('Role');
                            head.push('Updated Datetime');
                            head.push('Updated by');
                            head.push('Status');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.phone_number);
                                row.push(values.email);
                                row.push(values.cnic);
                                row.push(values.role);
                                row.push(values.updated_at);
                                row.push(values.updated_by);
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
				@if (session('role_id') == 1 || in_array(82, session('permissions')))
					buttons: [{
						text: '<i class="la la-user-plus"></i> Add',
						className: 'btn btn-primary add',
						action: function (e, dt, node, config) {
							window.location = '{{ route('admin.user_management.users.add.index') }}';
						}
					},{
                        extend: 'excel',
                        title: 'Users',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    }],
				@else
                buttons: [{
						extend: 'excel',
						title: 'Users',
                    	className: 'btn btn-primary',
						text: '<i class="la la-file-excel-o"></i> Excel',
					}],
	            @endif
	            scrollX: true, scrollY: '350px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.user_management.users.list') }}',
				rowId: 'id',
				order: [[1, 'asc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'name', name: 'admins.name', class: 'align-middle name'},
					{data: 'phone_number', name: 'admins.phone_number', class: 'align-middle phone_number'},
					{data: 'email', name: 'admins.email', class: 'align-middle email'},
					{data: 'cnic', name: 'admins.cnic', class: 'align-middle cnic'},
					{data: 'role', name: 'role', class: 'align-middle role'},
					{data: 'updated_at', name: 'admins.updated_at', class: 'align-middle updated_at'},
					{data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data: 'status', name: 'admins.status', class: 'align-middle status'},
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

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action')) {
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

			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var id = parseInt($(this).parents('tr').attr('id'));

				@if (session('role_id') == 1 || in_array(83, session('permissions')))
					if ($(this).hasClass('edit')) {
						var link = '{{ route('admin.user_management.users.update.index', ["id" => 0]) }}';

						window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
					}
				@endif

				@if (session('role_id') == 1 || in_array(84, session('permissions')))
					if ($(this).hasClass('enable')) {
						$.ajax({
							url: '{!! route('admin.user_management.users.status') !!}',
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
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
					else if ($(this).hasClass('disable')) {
						$.ajax({
							url: '{!! route('admin.user_management.users.status') !!}',
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
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
				@endif
			});
		});
	</script>
@endsection