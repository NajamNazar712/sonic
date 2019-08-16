@extends('admin.layout.master')

@section('title', 'Roles')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Roles
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Designation</th>
										<th class="border-primary border-darken-1">Department</th>
										<th class="border-primary border-darken-1">Created Datetime</th>
										<th class="border-primary border-darken-1">Updated Datetime</th>
										<th class="border-primary border-darken-1">Updated by</th>
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
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			var table = $('#datatable').DataTable({
				@if (session('role_id') == 1 || in_array(86, session('permissions')))
					dom: '<"d-inline-block"l><"pull-right"B>tipr',
					buttons: [{
						text: 'Add',
						className: 'btn btn-primary add',
						action: function (e, dt, node, config) {
							window.location = '{{ route('admin.user_management.roles.add.index') }}';
						}
					},'reset'],
				@else
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: ['reset'],
	            @endif
	            scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.user_management.roles.list') }}',
				rowId: 'id',
				order: [[3, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'name', name: 'admin_roles.name', class: 'align-middle name'},
					{data: 'department', name: 'ad.id', class: 'align-middle department'},
					{data: 'created_at', name: 'admin_roles.created_at', class: 'align-middle created_at'},
					{data: 'updated_at', name: 'admin_roles.updated_at', class: 'align-middle updated_at'},
					{data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
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
                    var departments_select = '<select name="departments_select" id="departments_select" class="select2 form-control"></select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
						}else if($(header).is('.department')){
                            $(departments_select).appendTo($(search))
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
                    var data1 = $.map({!! $departments !!}, function (obj) {
                        obj.id = obj.id;

                        return obj;
                    });
                    var data1 = $.map({!! $departments !!}, function (obj) {
                        obj.text = obj.name;

                        return obj;
                    });

                    $("#departments_select").prepend('<option value="" selected></option>').select2({
                        data:data1,
                        placeholder: "Select Department",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
					this.api().table().columns.adjust();
				}
			});

			@if (session('role_id') == 1 || in_array(87, session('permissions')))
				$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.edit', function() {
					var id = parseInt($(this).parents('tr').attr('id'));
					var link = '{{ route('admin.user_management.roles.update.index', ["id" => 0]) }}';

					window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
				});
			@endif
		});
	</script>
@endsection