@extends('admin.layout.master')

@section('title', 'Disable Account Intimation Survey')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Disable Account Intimation Survey
				</h1>

				<div class="modal fade text-left" id="SendSurveyModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddTierModal"
					aria-hidden="true">
					<div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header bg-primary white">
								<h4 class="modal-title white">Send Survey</h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body text-center">
								<form id="send_survey_form" method="post" novalidate="novalidate">
									@method('POST')
									@csrf
									<div class="container">
										<div class="row justify-content-center">
											<div class="col-6 form-group">
												<label class="font-medium-2 font-weight-bold block">Send Via</label>
												<select name="send_via" id="send_via" class="form-control select2" data-rule-required="true" data-msg-required="Field Required">
													<option value="email">Email</option>
													<option value="sms">SMS</option>
													<option value="both">Both</option>
												</select>
											</div>

										</div>
										
										<div class="row justify-content-center">

											<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
												<label class="font-medium-2 font-weight-bold block">All Shippers</label>
												<div class="form-group">
													<label for="all_shippers_checkbox" class="font-medium-2 text-bold-600 mr-1">No</label>
													<input type="checkbox" name="all_shipper" id="all_shippers_checkbox" class="switchery all_shippers_checkbox" data-size="sm" data-switchery="true">
													<label for="all_shippers_checkbox" class="font-medium-2 text-bold-600 ml-1">Yes</label>
												</div>
											</div>
	
											<div class="col-6 form-group">
												<div class="form-group input-group ">
													<label class="font-medium-2 font-weight-bold block">Shippers</label>
													<select name="shipper_ids[]" id="shippers_select" class="form-control select2"
															multiple="multiple"
															data-msg-required="Atleast one shipper is required"
															data-rule-required="true" required="required">
														@foreach($disabled_shippers as $shippers)
															<option value="{{$shippers->id}}">{{$shippers->name}}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
										
										<div class="row justify-content-center">
											<div class="col-6">
												<button type="submit" class="btn btn-primary btn-block">Send Survey</button>
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
							<table class="table table-bordered datatable" id="datatable" style="z-index: 3; width:100%;">
								<thead>
									<tr role="row" class="bg-primary white">
										<th class="border-primary border-darken-1">Q.ID</th>
										<th class="border-primary border-darken-1">Question</th>
										<th class="border-primary border-darken-1">Option 1</th>
										<th class="border-primary border-darken-1">Option 2</th>
										<th class="border-primary border-darken-1">Option 3</th>
										<th class="border-primary border-darken-1">Option 4</th>
										<th class="border-primary border-darken-1">Created by</th>
										<th class="border-primary border-darken-1">Updated by</th>
										<th class="border-primary border-darken-1">Created at</th>
										<th class="border-primary border-darken-1">Status</th>
										<th class="border-primary border-darken-1">Action</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@if (session('role_id') == 1 || in_array(763, session('permissions')))
		<div class="modal fade" id="add" role="dialog" aria-labelledby="add" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<form class="form-horizontal" novalidate="novalidate">
						<input type="hidden" name="id" class="id">

						<div class="modal-header">
							<h4 class="modal-title" id="add_title">Add Question</h4>

							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Question</label>
								<textarea type="text" name="question" class="form-control question" placeholder="Enter Question*" data-rule-required="true" data-msg-required="Question is required" data-rule-field="true"></textarea>
							</div>

							<div class="form-group">
								<label>Option 1</label>
								<input type="text" name="option1" class="form-control option1" placeholder="Enter Option 1*" data-rule-required="true" data-msg-required="Option 1 is required" data-rule-field="true">
							</div>

							<div class="form-group">
								<label>Option 2</label>
								<input type="text" name="option2" class="form-control option2" placeholder="Enter Option 2*" data-rule-required="true" data-msg-required="Option 2 is required" data-rule-field="true">
							</div>

							<div class="form-group">
								<label>Option 3</label>
								<input type="text" name="option3" class="form-control option3" placeholder="Enter Option 3*" data-rule-required="true" data-msg-required="Option 3 is required" data-rule-field="true">
							</div>

							<div class="form-group">
								<label>Option 4</label>
								<input type="text" name="option4" class="form-control option4" placeholder="Enter Option 4*" data-rule-required="true" data-msg-required="Option 4 is required" data-rule-field="true">
							</div>

						</div>
						<div class="modal-footer">
							<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
							<button type="submit" name="edit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@endif

	@if (session('role_id') == 1 || in_array(764, session('permissions')))
		<div class="modal fade" id="edit" role="dialog" aria-labelledby="edit" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<form class="form-horizontal" novalidate="novalidate">
						<input type="hidden" name="id" class="id">

						<div class="modal-header">
							<h4 class="modal-title" id="edit_title">Edit Question</h4>

							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label>Question</label>
								<textarea type="text" name="question" class="form-control question" placeholder="Enter Question*" data-rule-required="true" data-msg-required="Question is required" data-rule-field="true"></textarea>
							</div>

							<div class="form-group">
								<label>Option 1</label>
								<input type="text" name="option1" class="form-control option1" placeholder="Enter Option 1*" data-rule-required="true" data-msg-required="Option 1 is required" data-rule-field="true">
							</div>

							<div class="form-group">
								<label>Option 2</label>
								<input type="text" name="option2" class="form-control option2" placeholder="Enter Option 2*" data-rule-required="true" data-msg-required="Option 2 is required" data-rule-field="true">
							</div>

							<div class="form-group">
								<label>Option 3</label>
								<input type="text" name="option3" class="form-control option3" placeholder="Enter Option 3*" data-rule-required="true" data-msg-required="Option 3 is required" data-rule-field="true">
							</div>

							<div class="form-group">
								<label>Option 4</label>
								<input type="text" name="option4" class="form-control option4" placeholder="Enter Option 4*" data-rule-required="true" data-msg-required="Option 4 is required" data-rule-field="true">
							</div>

						</div>
						<div class="modal-footer">
							<button type="button" class="mr-auto btn btn-secondary" data-dismiss="modal">Close</button>
							<button type="submit" name="edit" class="btn btn-primary">Update</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@endif
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">

@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
	<script src="{{asset('/app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

	<script type="text/javascript">
		$(document).ready(function() {

			$('#shippers_select').select2({
				placeholder:'Shippers',
				width:'100%',
				allowClear:true
			}).bind('select2:select', function () {

				if($(this).val() != null){
					if($("#all_shippers_checkbox").is(":checked")){
						$("#all_shippers_checkbox").trigger('click');
					}
				}
			});

			$('#shippers_select').on('select2:unselect', function () {
				if($(this).val().length == 0){
					var all_switch_check = document.querySelector('.switchery.all_shippers_checkbox');

					if(all_switch_check.checked === false){
						$("#all_shippers_checkbox").trigger('click');
					}
				}
			});

			var all_switch = document.querySelector('.switchery.all_shippers_checkbox');
			$('#all_shippers_checkbox').on('change',function(){

				var all_switch_change = document.querySelector('.switchery.all_shippers_checkbox');

				if (all_switch_change.checked === true) {
					$('#shippers_select').val(null).trigger('change');
					$('#shippers_select').attr('disabled', true);

				}else if (all_switch_change.checked === false) {
					$('#shippers_select').attr('data-rule-required', true);
					$('#shippers_select').attr('disabled', false);
				}
			});

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
					var params = table.ajax.params();
					params.start = 0;
					params.length = -1;
					params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.accounts.disable.account.intimation.survey.list') }}',
						data: params,
                        success: function (result) {
                            head = [];
							
                            head.push('Q.ID');
                            head.push('Questions');
                            head.push('Option 1');
                            head.push('Option 2');
                            head.push('Option 3');
                            head.push('Option 4');
                            head.push('Created by');
                            head.push('Updated by');
                            head.push('Created at');
                            head.push('Status');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(values.id);
                                row.push(values.questions);
                                row.push(values.option1);
                                row.push(values.option2);
                                row.push(values.option3);
                                row.push(values.option4);
                                row.push(values.created_by_name);
                                row.push(values.updated_by_name);
                                row.push(values.created_at);
                                row.push(values.status);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
			});

			var selected_rows = [];
			var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
				
				buttons: [
				@if (session('role_id') == 1 || in_array(763, session('permissions')))
				{
					text: '<i class="la la-plus"></i> Add Question',
					className: 'btn btn-primary add',
					action: function (e, dt, node, config) {
						$('#add').modal('show');
					}
				},
				@endif
				@if (session('role_id') == 1 || in_array(767, session('permissions')))
				{
					text: '<i class="la la-send"></i> Send Survey',
					className: 'btn btn-primary',
					enabled:true,
					action: function (e, dt, node, config) {

						$('#SendSurveyModal').modal('show');

					}
				},
				@endif
				{
					extend: 'excel',
					title: 'Disable Account Intimation Questions',
					className: 'btn btn-primary',
					text: '<i class="la la-file-excel-o"></i> Excel',
				},'reset'],
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
					url: '{{ route('admin.accounts.disable.account.intimation.survey.list') }}',
					data: function (d) {
				}
				},
				rowId: 'id',
				order: [[0, 'Asc']],
				columns: [
					{data: 'id', name: 'id', class: 'align-middle id',sortable:false,orderable:false},
					{data: 'questions', name: 'questions', class: 'align-middle questions'},
					{data: 'option1', name: 'option1', class: 'align-middle option1'},
					{data: 'option2', name: 'option2', class: 'align-middle option2'},
					{data: 'option3', name: 'option3', class: 'align-middle option3'},
					{data: 'option4', name: 'option4', class: 'align-middle option4'},
					{data: 'created_by_name', name: 'created_user.name', class: 'align-middle created_by_name'},
					{data: 'updated_by_name', name: 'updated_user.name', class: 'align-middle updated_by_name'},
					{data: 'created_at', name: 'created_at', class: 'align-middle created_at'},
					{data: 'status', name: 'status', class: 'align-middle status'},
					{data: 'action', name: 'action', class: 'align-middle action',sortable:false,orderable:false}
				],
				rowCallback: function(row, data, index) {

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
					this.api().columns().every(function(column_id) {
						var column = this;
						var header = column.header();

						if ($(header).is('.id') || $(header).is('.action') || $(header).is('.option1') || $(header).is('.option2') || $(header).is('.option3') || $(header).is('.option4') || $(header).is('.created_at')) {
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

			$('#send_survey_form #send_via').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder: 'Select Send Via',
                allowClear:true,
				dropdownParent:$('#send_survey_form')
            });

			$('#send_survey_form #disabled_shippers').prepend('<option value="" selected="selected"></option>').select2({
                width:'100%',
                placeholder: 'Select Shippers',
                allowClear:true,
				dropdownParent:$('#send_survey_form')
            });

			$('#send_survey_form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value);
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					},
					submitHandler: function(form) {

						var form = $("#send_survey_form");
						var send_via = $(form).find('#send_via').val();
						
						var all_shippers_checkbox = ($('#all_shippers_checkbox').is(':checked') ?  $(form).find('#all_shippers_checkbox').val() : 'off'); 
						var shippers_select = $(form).find('#shippers_select').val();
						
						$.ajax({
							url: '{!! route('admin.accounts.disable.account.intimation.survey.send_survey') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'send_via': send_via,
								'all_shippers_checkbox': all_shippers_checkbox,
								'shipper_ids': shippers_select,
							}
						})
						.done(function(data) {

							if(data.success)
							{
								toastr.success(data.success, 'Success!', {
									positionClass: 'toast-bottom-center',
									containerId: 'toast-bottom-center'
								});

								$('#SendSurveyModal').modal('hide');
								
							}
							else
							{
								toastr.error(data.error, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
						});
					}
			});	

			$('#add form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value);
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					},
					submitHandler: function(form) {
						var question = $(form).find('.question').val();
						var option1 = $(form).find('.option1').val();
						var option2 = $(form).find('.option2').val();
						var option3 = $(form).find('.option3').val();
						var option4 = $(form).find('.option4').val();

						$.ajax({
							url: '{!! route('admin.accounts.disable.account.intimation.survey.add') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'question': question,
								'option1': option1,
								'option2': option2,
								'option3': option3,
								'option4': option4,
							}
						})
						.done(function(data) {
							
							if (data.status == 0) {
								table.draw(false);
								$('#add').modal('hide');

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
					}
				});	
			$('#edit form').validate({
					errorClass: 'danger',
					successClass: 'success',
					normalizer: function(value) {
						return $.trim(value);
					},
					errorPlacement: function(error, element) {
						error.addClass('w-100').appendTo(element.parent('.form-group'));
					},
					submitHandler: function(form) {
						var id = $(form).find('.id').val();
						var question = $(form).find('.question').val();
						var option1 = $(form).find('.option1').val();
						var option2 = $(form).find('.option2').val();
						var option3 = $(form).find('.option3').val();
						var option4 = $(form).find('.option4').val();

						$.ajax({
							url: '{!! route('admin.accounts.disable.account.intimation.survey.edit') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'id': id,
								'question': question,
								'option1': option1,
								'option2': option2,
								'option3': option3,
								'option4': option4,
							}
						})
						.done(function(data) {
							
							if (data.status == 0) {
								table.draw(false);
								$('#edit').modal('hide');

								toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
							}
							else {
								toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
							}
						});
					}
				});


			$('#datatable tbody').on('click', 'tr td.action .btn-group .dropdown-menu .dropdown-item', function() {
				var question_id = parseInt($(this).parents('tr').attr('id'));
				var question_status = parseInt($(this).parents('tr').attr('data-type'));

				@if (session('role_id') == 1 || in_array(764, session('permissions')))
					if ($(this).hasClass('edit')) {
						$.ajax({
							url: '{!! route('admin.accounts.disable.account.intimation.survey.details') !!}',
							method: 'POST',
							data: {
								'_token': '{{ csrf_token() }}',
								'id': question_id
							}
						})
						.done(function(data) {
							$('#edit .id').val(data.id);
							$('#edit .question').val(data.questions);
							$('#edit .option1').val(data.option1);
							$('#edit .option2').val(data.option2);
							$('#edit .option3').val(data.option3);
							$('#edit .option4').val(data.option4);

							$('#edit').modal('show');
						});
					}
				@endif

				@if (session('role_id') == 1 || in_array(765, session('permissions')))
					if ($(this).hasClass('enable')) {
						$.ajax({
							url: '{!! route('admin.accounts.disable.account.intimation.survey.status') !!}',
							method: 'POST',
							data: {
								'id': question_id,
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
							url: '{!! route('admin.accounts.disable.account.intimation.survey.status') !!}',
							method: 'POST',
							data: {
								'id': question_id,
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
			});

		});
	</script>
@endsection