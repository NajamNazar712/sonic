@extends('admin.layout.master')

@section('title', 'Logs')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Logs
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">Screen Name</th>
										<th class="border-primary border-darken-1">Changed By</th>
										<th class="border-primary border-darken-1">Changed By (Trax ID)</th>
										<th class="border-primary border-darken-1">Changed In Record</th>
										<th class="border-primary border-darken-1">Changed In Record (Trax ID)</th>
										<th class="border-primary border-darken-1">Data</th>
										<th class="border-primary border-darken-1">Created At</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
		
			var table = $('#datatable').DataTable({
	            scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.user_management.logs.list') }}',
				rowId: 'id',
				//order: [[5, 'desc']],
				columns: [
					{data: 'screen_name', name: 'user_role_management_logs.screen_name', class: 'align-middle screen_name'},
					{data: 'changed_by_name', name: 'changed_by.name', class: 'align-middle changed_by_name'},
					{data: 'changed_by_trax_id', name: 'changed_by.trax_id', class: 'align-middle changed_by_trax_id'},
					{data: 'changed_in_name', name: 'changed_in.name', class: 'align-middle changed_in_name'},
					{data: 'trax_id', name: 'changed_in.trax_id', class: 'align-middle trax_id'},
					{data: 'data', name: 'user_role_management_logs.data', class: 'align-middle data'},
					{data: 'created_at', name: 'user_role_management_logs.created_at', class: 'align-middle created_at'},
				],
				rowCallback: function(row, data, index) {
					// var info = table.page.info();
					// $('td:eq(0)', row).addClass('select-checkbox');
					// if ($.inArray(data.id, selected_rows) !== -1) {
					// 	table.row(row).select();
					// }
					// $('td:eq(0)', row).html(index + 1 + info.page * info.length);
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());
					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.screen_name') || $(header).is('.changed_by_name') || $(header).is('.changed_in_name') || $(header).is('.changed_by_trax_id ') || $(header).is('.trax_id')  ) {
							var current = $(input).appendTo($(search)).on('change', function() {
								column.search($(this).val(), false, false, true).draw();
							}).wrap(td).after(icon);

							if (column.search()) {
								current.val(column.search());
							}
						} else {
							$(td).appendTo($(search));
						}
					});

					this.api().table().columns.adjust();
				}
			});
		});
	</script>
@endsection