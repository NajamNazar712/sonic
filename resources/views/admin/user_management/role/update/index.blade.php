@extends('admin.layout.master')

@section('title', 'Update Role')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Update Role
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="role_form" class="form-horizontal" method="POST" action="{{ route('admin.user_management.roles.update.store', ['id' => $role->id]) }}" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div class="form-group">
											<input type="text" name="name" class="form-control" placeholder="Designation*" data-rule-required="true" data-msg-required="Designation is required" value="{{ $role->name }}">
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
										<div class="form-group">
											<select name="department_id" class="select2" id="department" data-rule-required="true" data-msg-required="Department is required">
												@foreach($departments as $department)
													@if ($department->id == $role->department_id)
														<option value="{{ $department->id }}" selected="selected">{{ $department->name }}</option>
													@else
														<option value="{{ $department->id }}">{{ $department->name }}</option>
													@endif
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Permissions</h4>
									</div>

									<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
										<div class="nav flex-column nav-pills border-info rounded-0" role="tablist" aria-orientation="vertical">
											@foreach($modules as $module)
												@if($module->id != 18)
													@php
														$moduleTotalCount = $module->permissions->count();
														$modulePermCount = count(array_intersect($module->permissions->pluck('id')->toArray(), $permissions));
														$moduleHasPermission = $modulePermCount > 0;
													@endphp
													@if ($loop->first)
														<a class="nav-link rounded-0 active {{ $moduleHasPermission ? 'module-has-permissions' : '' }}" id="module_{{ $module->id }}_tab" data-toggle="pill" href="#module_{{ $module->id }}_tabpanel" role="tab" aria-controls="module_{{ $module->id }}_tabpanel" aria-selected="true">
															{{ $module->name }}
															<span class="badge badge-primary">{{ $modulePermCount }} / {{ $moduleTotalCount }}</span>
														</a>
													@else
														<a class="nav-link rounded-0 {{ $moduleHasPermission ? 'module-has-permissions' : '' }}" id="module_{{ $module->id }}_tab" data-toggle="pill" href="#module_{{ $module->id }}_tabpanel" role="tab" aria-controls="module_{{ $module->id }}_tabpanel" aria-selected="false">
															{{ $module->name }}
															<span class="badge badge-primary">{{ $modulePermCount }} / {{ $moduleTotalCount }}</span>
														</a>
													@endif
												@endif
											@endforeach
										</div>
									</div>
									<div class="col-xs-6 col-sm-6 col-md-8 col-lg-9">
										<div class="tab-content">
											@foreach($modules as $module)
												@if($module->id != 18)
													@if ($loop->first)
														<div class="tab-pane fade show active" id="module_{{ $module->id }}_tabpanel" role="tabpanel" aria-labelledby="module_{{ $module->id }}_tab">

															<div class="row">
																<div class="col-12">
																	<div class=" text-center mt-2">
																		<button type="button" data-module_id="{{$module->id}}"  class="selectAll btn btn-primary" >Select All</button>
																		<button type="button" data-module_id="{{$module->id}}"  class="unselectAll btn btn-primary">Unselect All</button>
																	</div>
																</div>
															</div>

															@foreach($module->permissions as $permission)
																@if($permission->id != 183 && $permission->id != 184 && $permission->id != 185 && $permission->id != 186 && $permission->id != 187)
																	<fieldset class="d-inline-block m-1">
																		@if (in_array($permission->id, $permissions))
																			<input type="checkbox" id="permission_{{ $permission->id }}" class="permission perm_check_{{$module->id}}" name="permission_ids[]" value="{{ $permission->id }}" checked="checked">
																		@else
																			<input type="checkbox" id="permission_{{ $permission->id }}" class="permission perm_check_{{$module->id}}" name="permission_ids[]" value="{{ $permission->id }}">
																		@endif
																		<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
																	</fieldset>
																@endif
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
																@if($permission->id != 179 && $permission->id != 180 && $permission->id != 181 && $permission->id != 182 && $permission->id != 183 && $permission->id != 184 && $permission->id != 185 && $permission->id != 186 && $permission->id != 187)
																	<fieldset class="d-inline-block m-1">
																		@if (in_array($permission->id, $permissions))
																			<input type="checkbox" id="permission_{{ $permission->id }}" class="permission perm_check_{{$module->id}}" name="permission_ids[]" value="{{ $permission->id }}" checked="checked">
																		@else
																			<input type="checkbox" id="permission_{{ $permission->id }}" class="permission perm_check_{{$module->id}}" name="permission_ids[]" value="{{ $permission->id }}">
																		@endif
																		<label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
																	</fieldset>
																@endif
															@endforeach


														</div>
													@endif
												@endif
											@endforeach
										</div>
									</div>

									<div class="col-12">
										<div class="form-group text-center">
											<button type="submit" class="btn btn-primary">Update</button>
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
	<style>
		/* Highlight sidebar modules that have at least one permission enabled */
		.nav-pills .nav-link {
			display: flex !important;
			justify-content: space-between;
			align-items: center;
		}
		.nav-pills .nav-link.module-has-permissions {
			background-color: #eaf2f8 !important;
			border-left: 4px solid #64a0d2 !important;
			color: #2c5f8a !important;
			font-weight: 600;
		}
		.nav-pills .nav-link.module-has-permissions.active {
			background-color: #64a0d2 !important;
			border-left: 4px solid #4a86bb !important;
			color: #fff !important;
		}
		.nav-pills .nav-link .badge-primary {
			background-color: #64a0d2;
			font-size: 0.75rem;
			margin-left: 4px;
			flex-shrink: 0;
		}
		.nav-pills .nav-link.active .badge-primary {
			background-color: #fff;
			color: #64a0d2;
		}
		/* Muted badge for 0/total modules */
		.nav-pills .nav-link:not(.module-has-permissions) .badge-primary {
			background-color: #b0bec5 !important;
			color: #fff !important;
		}
	</style>
@endsection

@section('js')
	<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/icheck/icheck.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#role_form #department').select2({
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
						text: 'Role is being updated!',
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