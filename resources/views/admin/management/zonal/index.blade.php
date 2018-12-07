@extends('admin.layout.master')

@section('title', 'Zonal Management')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Zonal Management
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">ID</th>
										<th class="border-primary border-darken-1">Name</th>
										<th class="border-primary border-darken-1">GST</th>
										<th class="border-primary border-darken-1">Added Datetime</th>
										<th class="border-primary border-darken-1">Updated Datetime</th>
										<th class="border-primary border-darken-1"></th>
									</tr>
								</thead>
							</table>

							<div class="modal fade" id="view_cities" role="dialog" aria-labelledby="view_cities_title" aria-hidden="true">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h4 class="modal-title" id="view_cities_title">View Cities</h4>

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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
@endsection

@section('js')
	<script>
		$(document).ready(function() {
			jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.zonal.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Name');
                            head.push('GST');
                            head.push('Created Datetime');
                            head.push('Updated Datetime');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.GST);
                                row.push(values.created_at);
                                row.push(values.updated_at);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            });

			var table = $('#datatable').DataTable({
				dom: '<"d-inline-block"l><"pull-right"B>tipr',
				buttons: [
					@if (session('role_id') == 1 || in_array(132, session('permissions')))
					{
						text: 'Add',
						className: 'btn btn-primary add',
						action: function (e, dt, node, config) {
							window.location = '{{ route('admin.management.zonal.add.index') }}';
						}
					},
					@endif
					{
						extend: 'excel',
	                    title: 'Zonal Management',
	                    className: 'btn btn-primary',
	                    text: '<i class="la la-file-excel-o"></i> Excel',
					}
				],
				scrollX: true, scrollY: '350px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
				serverSide: true,
				ajax: '{{ route('admin.management.zonal.list') }}',
				rowId: 'id',
				order: [[1, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'zones.id', class: 'align-middle id'},
					{data: 'name', name: 'zones.name', class: 'align-middle name'},
					{data: 'gst', name: 'zones.gst', class: 'align-middle gst'},
					{data: 'created_at', name: 'zones.created_at', class: 'align-middle created_at'},
					{data: 'updated_at', name: 'zones.updated_at', class: 'align-middle updated_at'},
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

				@if (session('role_id') == 1 || in_array(133, session('permissions')))
					if ($(this).hasClass('edit')) {
						var link = '{{ route('admin.management.zonal.update.index', ["id" => 0]) }}';

						window.location = link.substr(0, link.lastIndexOf('/')) + '/' + id;
					}
				@endif

				if ($(this).hasClass('view_cities')) {
					$('#view_cities .modal-body').html('');

					$.ajax({
						url: '{!! route('admin.management.zonal.view_cities') !!}',
						method: 'POST',
						data: {
							'_token': '{{ csrf_token() }}',
							'id': id
						}
					})
					.done(function(data) {
						if (data != 0) {
							var details = '<table class="table table-sm table-bordered"><tbody>';

							details += '<tr><td class="border-primary border-darken-1 align-middle text-center"><strong>City</strong></td><td class="border-primary border-darken-1 align-middle text-center"><strong>Pickup</strong></td></tr>';

							$.each(data, function (index, city) {
								details += '<tr><td class="align-middle text-center">' + city.name + '</td><td class="align-middle text-center">' + ((city.pickup) ? 'Yes' : 'No') + '</td></tr>';
							});

							details += '</tbody></table>';
						}
						else {
							var details = '<div class="text-center">No City assigned to this Zone</div>';
						}

						$('#view_cities .modal-body').html(details);

						$('#view_cities').modal('show');
					});
				}
			});
		});
	</script>
@endsection