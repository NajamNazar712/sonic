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

				<div class="modal fade text-left" id="AssignHubModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddTierModal"
					aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header bg-primary white">
								<h4 class="modal-title white">Assign Hubs</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body text-center">
                                <button id="selectAllBtn" class="btn btn-primary">Select All</button>
                                <button id="deSelectAllBtn" class="btn btn-primary">Un Select All</button>
								<form id="assign_hub_form" action="{{route('admin.user_management.users.assign_hubs')}}" method="post">
									@method('POST')
									@csrf
									<div class="container">
									    <input type="hidden" name="id"/>
										<div class="col-12 form-group">
                                            <select name="hubs[]" id="hub_select" class="form-control select2" multiple="multiple">
                                                @foreach($hubs as $hub)
                                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="d-none text-danger" id="assign_hubs_msg_error">Please Select Hub(s)</div>

                                        </div>
										<div class="row justify-content-center">
											<div class="col-6">
												<button id="edit" type="submit" class="btn btn-primary btn-block">Assign Hub</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>



				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')
							<div class="row justify-content-center mb-4">
								<div class="col-4">
									<fieldset class="form-group">
										<select name="search_roles[]" id="search_roles" class="form-control select2" multiple="multiple" required data-rule-required="true" data-msg-required="This field is required">
											@foreach($roles as $role)
												<option value="{{$role->id}}">{{$role->name}} - {{$role->department->name}}</option>
											@endforeach
										</select>
									</fieldset>
								</div>
								<div class="col-2">
									<button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
								</div>
							</div>


							<table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">S. No.</th>
										<th class="border-primary border-darken-1">Employee Id</th>
										<th class="border-primary border-darken-1">Name</th>
										<th class="border-primary border-darken-1">Phone Number</th>
										<th class="border-primary border-darken-1">Official Phone Number</th>
										<th class="border-primary border-darken-1">Outlook ID</th>
										<th class="border-primary border-darken-1">CNIC</th>
										<th class="border-primary border-darken-1">Designation</th>
										<th class="border-primary border-darken-1">Role</th>
										<th class="border-primary border-darken-1">Default Hub</th>
										<th class="border-primary border-darken-1">Created Datetime</th>
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

	<div class="modal fade text-left" id="PhoneUpdateModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="PhoneUpdateModal"
		 aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary white">
					<h4 class="modal-title white">Phone Update</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<form id="update_phone_form" action="{{route('admin.user_management.users.phone_update')}}" method="post">
						@method('POST')
						@csrf
						<div class="container">
							<input type="hidden" name="id" id="phone_admin_id"/>
							<div class="form-group">
								<input type="text" name="phone_number" id="phone" class="form-control" data-rule-required="true" data-msg-required="Phone Number required">
							</div>
							<div class="row justify-content-center">
								<div class="col-6">
									<button type="submit" class="btn btn-primary btn-block">Update Phone</button>
								</div>
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

    <style>
        #selectAllBtn {

            margin-bottom: 10px
        }

        #deSelectAllBtn {

            margin-bottom: 10px
        }
    </style>

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {

            var selectedValue = [];

$('#hub_select').select2().on('change', function() {
    selectedValue = $('#hub_select').val();
    $('#assign_hubs_msg_error').addClass('d-none')

});

$('#selectAllBtn').on('click', function() {
    $('#hub_select').val($('#hub_select option').map(function() {
        return $(this).val();
    })).trigger('change');

});

$('#deSelectAllBtn').on('click', function() {
                $('#hub_select').val([]).trigger('change');
            });

            $('#selectAllBtn').click(function() {
                $('#hub_select option').prop('selected', true);
            });
			$('#hub_select').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });
			$('#search_roles').select2({
				width:'100%',
				placeholder:"Search Roles",
				allowClear:true,
			});
			$('#update_phone_form #phone').inputmask({
				'mask': '9999-9999999',
				'clearIncomplete': true
			});
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
					var params = table.ajax.params();
					params.start = 0;
					params.length = -1;
					params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.user_management.users.list') }}',
						data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Employee Id');
                            head.push('Name');
                            head.push('Phone Number');
                            head.push('Official Phone Number');
                            head.push('Outlook ID');
                            head.push('CNIC');
                            head.push('Designation');
                            head.push('Role');
                            head.push('Default Hub');
                            head.push('Updated Datetime');
                            head.push('Updated by');
                            head.push('Status');
                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.trax_id);
                                row.push(values.name);
                                row.push(values.phone_number);
                                row.push(values.official_phone_number);
                                row.push(values.email);
                                row.push(values.cnic);
                                row.push(values.designation);
                                row.push(values.role);
                                row.push(values.default_hub);
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
			var selected_rows = [];
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
						text: '<i class="la la-cogs"></i> Assign Hub(s)',
						className: 'btn btn-primary assign',
						enabled:false,
						action: function (e, dt, node, config) {

							$('input:hidden[name=id]').val(selected_rows);
							$('#AssignHubModal').modal('show');

						}
					},
					{
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

									table.button('.assign').enable();
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
										table.button('.assign').disable();
									}
								}
							});
						}
					},{
                        extend: 'excel',
                        title: 'Users',
                    	className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset'],
				@else
                buttons: [{
						extend: 'excel',
						title: 'Users',
                    	className: 'btn btn-primary',
						text: '<i class="la la-file-excel-o"></i> Excel',
					},'reset'],
	            @endif
	            scrollX: true, scrollY: '500px',
				lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
				pageLength: 50,
				pagingType: 'full_numbers',
				processing: true,
                language: {
                    processing: data_table_loader
				},
				select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
				serverSide: true,
				ajax: {
					url: '{{ route('admin.user_management.users.list') }}',
					data: function (d) {
						d.search_roles = $('#search_roles').val();
				}
				},
				rowId: 'id',
				order: [[2, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'serial_number', orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 1, render: function (data, type, row) {return '';}},
					{data: 'trax_id', name: 'admins.trax_id', class: 'align-middle trax_id'},
					{data: 'name', name: 'admins.name', class: 'align-middle name'},
					{data: 'phone_number', name: 'admins.phone_number', class: 'align-middle phone_number'},
					{data: 'official_phone_number', name: 'admins.official_phone_number', class: 'align-middle official_phone_number'},
					{data: 'email', name: 'admins.email', class: 'align-middle email'},
					{data: 'cnic', name: 'admins.cnic', class: 'align-middle cnic'},
					{data: 'designation', name: 'ed.id', class: 'align-middle designation'},
					{data: 'role', name: 'role', class: 'align-middle role'},
					{data: 'default_hub', name: 'h.name', class: 'align-middle default_hub'},
					{data: 'created_at', name: 'admins.created_at', class: 'align-middle created_at'},
					{data: 'updated_at', name: 'admins.updated_at', class: 'align-middle updated_at'},
					{data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data: 'status', name: 'admins.status', class: 'align-middle status'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					// var info = table.page.info();

					// $('td:eq(0)', row).html(index + 1 + info.page * info.length);

					var info = table.page.info();

					$('td:eq(1)', row).html(index + 1 + info.page * info.length);
					if ($.inArray(data.id, selected_rows) !== -1) {
						table.row(row).select();
					}
				},
				initComplete: function() {
					var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

					var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
					var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
					var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var status_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Disable</option>' +
                        '<option value="1">Enable</option>' +
                        '</select>';
					var employee_designation_select = '<select name="employee_designation_select" id="employee_designation_select" class="select2 form-control"></select>';

					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.serial_number') || $(header).is('.action') || $(header).is('.select-checkbox')) {
							$(td).appendTo($(search));
						}else if($(header).is('.status')){
                            $(status_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
                        }
						else if ($(header).is('.designation')) {
							$(employee_designation_select).appendTo($(search))
									.on('change', function () {
										column.search($(this).val(), false, false, true).draw();
									}).wrap(td);
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

					var data = $.map({!! $employee_designations !!}, function (obj) {
						obj.id = obj.id;
						obj.text = obj.name;

						return obj;
					});

					$('#employee_designation_select').prepend('<option value="" selected></option>').select2({
						data:data,
						placeholder: "Select Designation",
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
					this.api().table().columns.adjust();
				}
			});
			$('#search_filter_btn').on('click',function () {
				table.draw();
			});

			$('body').on('click', '.rejoin', function (e) {
				var id = $(this).data('target-id');
				console.log(id);
				swal({
					title: 'Are You Sure?',
					text: 'Select Yes To Rejoin Admin!',
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
						swal({
							title: 'Please Wait!',
							text: 'Admin is being Rejoin',
							icon: 'info',
							buttons: false,
							closeOnClickOutside: false,
							closeOnEsc: false
						});

						$.ajax({
							url: '{!! route('admin.user_management.users.rejoin') !!}',
							method: 'POST',
							data: {
								'employee_id': id,
								'_token': '{{ csrf_token() }}'
							}
						})
								.done(function (data) {
									if (data.status == 0) {
										toastr.success(data.success, 'Success!', {
											positionClass: 'toast-bottom-center',
											containerId: 'toast-bottom-center'
										});
									} else {
										toastr.error(data.error, 'Error!', {
											positionClass: 'toast-top-center',
											containerId: 'toast-top-center'
										});
									}
									swal.close();
									table.draw('false');
								});
					}
				});
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
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
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
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
					}
				@endif


				@if (session('role_id') == 1 || in_array(542, session('permissions')))
				if ($(this).hasClass('phone')) {

					$.ajax({
						url: '{!! route('admin.user_management.users.user_info') !!}',
						method: 'POST',
						data: {
							'admin_id': id,
							'_token': '{{ csrf_token() }}'
						}
					})
							.done(function(data) {
								if (data.status == 0) {

									$('#update_phone_form #phone').val(data.phone);
									$('#update_phone_form #phone_admin_id').val(id);
								}
								else {
									toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
								}
							});
					$('#PhoneUpdateModal').modal('show');
				}
				@endif

			});

			$('#PhoneUpdateModal').on('hidden.bs.modal', function (e) {
				$('#update_phone_form #phone').val('');
				$('#update_phone_form #phone_admin_id').val('');
			});


			$('#update_phone_form').validate({
				errorClass: 'danger',
				successClass: 'success',
				errorPlacement: function(error, element) {
					error.addClass('w-100').appendTo(element.parent('.form-group'));
				}
			});

            //bulk assigning of hub work start
			$('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
				var id = parseInt($(this).parent('tr').attr('id'));
                console.log(id);
				var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }

                if (selected_rows.length > 0) {
                    table.button('.assign').enable();
                }
                else {
                    table.button('.assign').disable();
                }
			});
			
            $("#assign_hub_form").validate({

errorClass: "danger",
errorPlacement: function(error, element) {
    error.addClass('w-100').appendTo(element.parent('.form-group'));
},
submitHandler: function(form) {
    if (selectedValue.length > 0) {

        $(form).find('button[type=submit]').attr('disabled', 'disabled');

        swal({
            title: 'Please Wait!',
            text: 'Multiple Hub has been assigned!',
            icon: 'info',
            buttons: false,
            closeOnClickOutside: false,
            closeOnEsc: false
        });

        form.submit();

    } else {
        $('#assign_hubs_msg_error').removeClass('d-none');
    }
}
});

			

		});
	</script>
@endsection