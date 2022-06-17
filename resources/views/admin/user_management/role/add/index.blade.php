@extends('admin.layout.master')

@section('title', 'Add Role')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Add Role
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="role_form" class="form-horizontal" method="POST" action="{{ route('admin.user_management.roles.add.store') }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div class="form-group">
											<input type="text" name="name" class="form-control" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div class="form-group">
											<select name="department_id" class="select2" id="department" data-rule-required="true" data-msg-required="Department is required">
												@foreach($departments as $department)
													<option value="{{ $department->id }}">{{ $department->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Permissions</h4>
									</div>

									<div class="col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3">
										<div class="nav flex-column nav-pills border-info rounded-0" role="tablist" aria-orientation="vertical">
											@foreach($modules as $module)
												@if($module->id != 18)
													@if ($loop->first)
														<a class="nav-link rounded-0 active" id="module_{{ $module->id }}_tab" data-toggle="pill" href="#module_{{ $module->id }}_tabpanel" role="tab" aria-controls="module_{{ $module->id }}_tabpanel" aria-selected="true">{{ $module->name }}</a>
													@else
														<a class="nav-link rounded-0" id="module_{{ $module->id }}_tab" data-toggle="pill" href="#module_{{ $module->id }}_tabpanel" role="tab" aria-controls="module_{{ $module->id }}_tabpanel" aria-selected="false">{{ $module->name }}</a>
													@endif
												@endif
											@endforeach
										</div>
									</div>
									<div class="col-6 col-xs-6 col-sm-6 col-md-8 col-lg-9">
										<div class="tab-content">
											@foreach($modules as $module)
												@if($module->id != 18)
													@if ($loop->first)
														<div class="tab-pane fade show active" id="module_{{ $module->id }}_tabpanel" role="tabpanel" aria-labelledby="module_{{ $module->id }}_tab">
															<div class="row">
																<div class="col-12">
																	<div class=" text-center mt-2">
																		<button type="button" data-module_id="{{$module->id}}" class="selectAll btn btn-primary" >Select All</button>
																		<button type="button" data-module_id="{{$module->id}}" class="unselectAll btn btn-primary">Unselect All</button>
																	</div>
																</div>
															</div>

															@foreach($module->permissions as $permission)
																<fieldset class="d-inline-block m-1">
																	<input type="checkbox" id="permission_{{ $permission->id }}" class="permission perm_check_{{$module->id}}" name="permission_ids[]" value="{{ $permission->id }}">
																	<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
																</fieldset>
															@endforeach


														</div>
													@else
														<div class="tab-pane fade" id="module_{{ $module->id }}_tabpanel" role="tabpanel" aria-labelledby="module_{{ $module->id }}_tab">
															<div class="row">
																<div class="col-12">
																	<div class=" text-center mt-2">
																		<button type="button" data-module_id="{{$module->id}}"  class="selectAll btn btn-primary" >Select All</button>
																		<button type="button" data-module_id="{{$module->id}}"  class="unselectAll btn btn-primary">Unselect All</button>
																	</div>
																</div>
															</div>

															@foreach($module->permissions as $permission)
																<fieldset class="d-inline-block m-1">
																	<input type="checkbox" id="permission_{{ $permission->id }}" class="permission perm_check_{{$module->id}}" name="permission_ids[]" value="{{ $permission->id }}">
																	<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
																</fieldset>
															@endforeach

														</div>
													@endif
												@endif
											@endforeach
										</div>
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Add</button>
										</div>
									</div>
								</div>
								</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#role_form #department').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Department*'
			});

			$('#role_form .permission').each(function() {
				var checkbox = $(this);
				var label = checkbox.next();
				var text = label.text();

				label.remove();

				checkbox.iCheck({
					checkboxClass: 'icheckbox_line pt-1 pb-1',
					checkedClass: 'checked bg-success',
					uncheckedClass: 'bg-danger',
					insert: '<div class="icheck_line-icon"></div>' + text
				});
			});

			$('#role_form').validate({
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
						text: 'Role is being added!',
						icon: 'info',
						buttons: false,
						closeOnClickOutside: false,
						closeOnEsc: false
					});

					form.submit();
				}
			});

			$(".selectAll").on('click',function (){
				let module_id = $(this).attr('data-module_id');
				$(".perm_check_"+module_id).prop('checked',true);
				$(".perm_check_"+module_id).iCheck('update');
			});

			$(".unselectAll").on('click',function (){
				let module_id = $(this).attr('data-module_id');
				$(".perm_check_"+module_id).prop('checked',false);
				$(".perm_check_"+module_id).iCheck('update');
			});
		});
	</script>
@endsection