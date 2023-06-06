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
										<th class="border-primary border-darken-1">Business Category</th>
										<th class="border-primary border-darken-1">Status</th>
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
							<div class="modal fade" id="duplicate_zone" data-backdrop="static" role="dialog" aria-labelledby="duplicate_zone" aria-hidden="true">
								<div class="modal-dialog modal-sm" role="document">
									<form id="duplicate_zone_form" method="POST" action="{{ route('admin.management.zonal.duplicate_zone') }}" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
										@csrf
										<div class="modal-content">
											<div class="modal-header bg-primary">
												<h4 class="modal-title white">Duplicate Zone</h4>
											</div>
											<div class="modal-body">
												<input type="hidden" name="zone_id" id="zone_id">
												<input type="text" name="zone_name" id="zone_name" class="form-control" placeholder="Enter Zone*" data-rule-required="true">
												
											</div>
											<div class="modal-body">
												<input type="text" name="zone_charges" id="zone_charges" class="form-control" placeholder="Enter Charges*" data-rule-required="true">
											</div>
											<div class="modal-footer">
												<button type="submit" class="btn btn-primary">Create</button>
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
											</div>
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
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<style>
		#toast-bottom-center.toast-container {
			text-align: center;
		}

		#toast-bottom-center.toast-container .toast {
			display: table;
			width: auto !important;
			text-align: left;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
	<script>
		$(document).ready(function() {


			jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
					params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.management.zonal.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Name');
                            head.push('GST');
                            head.push('Business Category');
                            head.push('Status');
                            head.push('Created Datetime');
                            head.push('Updated Datetime');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
                                row.push(values.GST);
                                row.push(values.business_category);
                                row.push(values.status);
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
					{
						text: 'Add International',
						className: 'btn btn-primary add_international',
						action: function (e, dt, node, config) {
							window.location = '{{ route('admin.management.zonal.add.international.index') }}';
						}
					},
					@endif
					{
						extend: 'excel',
	                    title: 'Zonal Management',
	                    className: 'btn btn-primary',
	                    text: '<i class="la la-file-excel-o"></i> Excel',
					},
					'reset'
				],
				scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
                },
				serverSide: true,
				ajax: '{{ route('admin.management.zonal.list') }}',
				rowId: 'id',
				order: [[1, 'desc']],
				columns: [
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'zones.id', class: 'align-middle id'},
					{data: 'name', name: 'zones.name', class: 'align-middle name'},
					{data: 'gst', name: 'zones.gst', class: 'align-middle gst'},
					{data: 'business_category', name: 'bc.id', class: 'align-middle business_category'},
					{data: 'status', name: 'zones.status', class: 'align-middle status'},
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
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Inactive</option>' +
                        '<option value="1">Active</option>' +
                        '</select>';
					var business_category = '<select name="business_category" id="business_category" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action')) {
							$(td).appendTo($(search));
                        }
                        else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
                        else if($(header).is('.business_category')){
                            $(business_category).appendTo($(search))
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
					var data = $.map({!! $business_categories !!}, function (obj) {
						obj.id = obj.id;
						obj.text = obj.name;

						return obj;
					});
					$("#business_category").prepend('<option value="" selected></option>').select2({
						data: data,
						placeholder: "Select Business Category",
						width:'100%',
						containerCssClass: 'select-xs',
						dropdownCssClass: 'form-control-sm p-0'
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
					if ($(this).hasClass('international_edit')) {
						var international_link = '{{ route('admin.management.zonal.update.international.index', ["id" => 0]) }}';

						window.location = international_link.substr(0, international_link.lastIndexOf('/')) + '/' + id;
					}
					if ($(this).hasClass('activate')) {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to activate this Zone!',
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
                                $.ajax({
                                    url: '{!! route('admin.management.zonal.status_update') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'id': id,
                                        'status': 1
                                    }
                                })
                                    .done(function (data) {
                                        if (data != 0) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                        table.draw();
                                    });
                            }
                        });
					}
					if ($(this).hasClass('deactivate')) {
                        swal({
                            title: 'Are You Sure?',
                            text: 'Select Yes to Deactivate this Zone!',
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
                                $.ajax({
                                    url: '{!! route('admin.management.zonal.status_update') !!}',
                                    method: 'POST',
                                    data: {
                                        '_token': '{{ csrf_token() }}',
                                        'id': id,
                                        'status': 0
                                    }
                                })
                                    .done(function (data) {
                                        if (data != 0) {
                                            toastr.success(data.success, 'Success!', {
                                                positionClass: 'toast-bottom-center',
                                                containerId: 'toast-bottom-center'
                                            });
                                        }
                                        else {
                                            toastr.error(data.error, 'Error!', {
                                                positionClass: 'toast-top-center',
                                                containerId: 'toast-top-center'
                                            });
                                        }
                                        table.draw();
                                    });
                            }
                        });
					}

					
				
				@endif

				if ($(this).hasClass('duplicate_zone')) {
						// $('#duplicate_zone .modal-body').html('');
						var id = parseInt($(this).parents('tr').attr('id'));

						$('#zone_id').val(id);
						$('#duplicate_zone').modal('show');
					}
				
						$('#duplicate_zone_form').validate({
						ignore: [],
						errorClass: 'danger',
						successClass: 'success',
						errorPlacement: function(error, element) {
							error.addClass('w-100').appendTo(element.parents('.form-group'));
						},
						submitHandler: function(form) {
							form.submit();
						}
					});

					$('#zone_charges').inputmask({
						'alias': 'decimal',
						'allowMinus': false,
						'allowPlus': false,
						'rightAlign': false,
						'digits': 2,
						'min': 0.00,
						'max': 1000000.00
					});

					$('#duplicate_zone').on('hide.bs.modal', function(e) {
					$('#zone_name').val('');
					$('#zone_charges').val('');
					});


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