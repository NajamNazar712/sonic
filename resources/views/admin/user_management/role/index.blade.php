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
										<th class="border-primary border-darken-1"></th>
										<th class="border-primary border-darken-1">Table ID</th>
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

	<div class="modal fade" id="duplicate_role_modal" data-backdrop="static" role="dialog" aria-labelledby="duplicate_role_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
			<form id="duplicate_role_form" method="POST" action="{{ route('admin.user_management.roles.add.duplicate_role') }}" class="form-horizontal mb-1 justify-content-center" novalidate="novalidate">
				@csrf
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<h4 class="modal-title white">Duplicate Role</h4>
					</div>
					<div class="modal-body text-center form-group">
						<input type="hidden" name="role_id" id="role_id">
						<input type="text" name="designation" id="designation" class="form-control" placeholder="Enter Designation*" data-rule-required="true">
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">Create</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>
        </div>
    </div>

	<div class="modal fade text-left" id="AddRoleModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddRoleModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Add Roles</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <form id="add_permission_form" action="" method="post"  novalidate="novalidate">
						@csrf
                        <div class="row mb-2">
                            <div class="col-12 form-group">
                                <select name="module_select" id="module_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                    @foreach($modules as $module)
                                        <option value="{{$module->id}}">{{$module->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
						<div class="row mb-2">
                            <div class="col-12 form-group">
                                <select name="permission_select" id="permission_select" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">
                                </select>
                            </div>
                        </div>
						<div class="row mb-2 justify-content-center">
                               <a href="javascript:void(0);" id="add_permission" class="btn btn-primary"><i class="ft-plus-circle"></i></a>
                        </div>
						<div id="inner_permission"></div>
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <button id="AddPermission" type="submit" class="btn btn-primary btn-block">Add</button>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

	<style>
		
        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/tags/tagging.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {

			$('#add_permission').on('click',function(){
				
				var modules = {!! $modules !!};
				var counter = document.getElementById('inner_permission').childElementCount;
            	var html='<div class="row mb-2">';
                html += '<div class="col-12 form-group">';
                html += '<select name="module_select'+counter+'" id="module_select_'+counter+'" class="select2 form-control" style="width:100%;" data-rule-required="true" data-msg-required="This field is required">';
				$.each(modules, function (i, v) {
					
					html += "<option value='" + v.id + "' >" + v.name + "</option>";
                });
				html += '</select></div></div>';
                $('#inner_permission').append(html);

        	});

			$("#module_select").prepend('<option value="" selected></option>').select2({
                    placeholder: "Select Module",
                    width:'100%',
                }).bind('change', function () {
					var module_id = $(this).val();
					var data = $.map({!! $permissions !!}, function (obj) {

						$("#permission_select").html('');
						if(obj.module_id == module_id){
							obj.id = obj.id;
							obj.text = obj.name;
							return obj;
						}

					});
					$("#permission_select").prepend('<option value="" selected></option>').select2({
						placeholder: "Select Permission",
						width:'100%',
						data:data,
					});
				});
            var selected_rows = [];
			var table = $('#datatable').DataTable({
				@if (session('role_id') == 1 || in_array(86, session('permissions')))
					dom: '<"d-inline-block"l><"pull-right"B>tipr',
					buttons: [{
						text: 'Add',
						className: 'btn btn-primary add',
						enabled: false,
						action: function (e, dt, node, config) {
							
							$('#AddRoleModal').modal('show');
						}
					},{
						text: 'Remove',
						className: 'btn btn-primary remove',
						enabled: false,
						action: function (e, dt, node, config) {
							$('#AddRoleModal').modal('show');

						}
					},{
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

                                table.button('.add').enable();
                                table.button('.remove').enable();
								
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
                                    table.button('.add').disable();
									table.button('.remove').disable();

                                }
                            }
                        });
                    }
                },'reset'],
				@else
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: ['reset'],
	            @endif
				select: {
					info: false,
					style: 'multi',
					selector: 'td.select-checkbox',
					className: 'selected bg-primary bg-lighten-5 primary'
            	},
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
				order: [[4, 'desc']],
				columns: [
					{data: 'id', orderable: false, searchable: false, class: 'text-center align-middle select p-1', targets: 0, render: function (data, type, row) {return '';}},
					{data: 'id', name: 'admin_roles.id', class: 'align-middle id'},
					{data: 'name', name: 'admin_roles.name', class: 'align-middle name'},
					{data: 'department', name: 'ad.id', class: 'align-middle department'},
					{data: 'created_at', name: 'admin_roles.created_at', class: 'align-middle created_at'},
					{data: 'updated_at', name: 'admin_roles.updated_at', class: 'align-middle updated_at'},
					{data: 'updated_by', name: 'a.name', class: 'align-middle updated_by'},
					{data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}
				],
				rowCallback: function(row, data, index) {
					var info = table.page.info();
					$('td:eq(0)', row).addClass('select-checkbox');
					if ($.inArray(data.id, selected_rows) !== -1) {
						table.row(row).select();
					}
					// $('td:eq(0)', row).html(index + 1 + info.page * info.length);
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

						if ($(header).is('.serial_number') || $(header).is('.action') ||  $(header).is('.select')) {
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

				$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item.duplicate', function() {
					var id = parseInt($(this).parents('tr').attr('id'));

					$('#role_id').val(id);
					$('#duplicate_role_modal').modal('show');
				});
			@endif

			$('#duplicate_role_form').validate({
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
			$('#add_permission_form').validate({
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
                    table.button('.add').enable();
                    table.button('.remove').enable();
                }
                else {
                    table.button('.add').disable();
                    table.button('.remove').disable();
                }
				
            });
		});

		
		
			

		
	</script>
@endsection