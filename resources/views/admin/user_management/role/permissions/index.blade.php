@extends('admin.layout.master')

@section('title', 'Role Permissions')

@section('content')
	<div class="app-content content">
		<div class="content-wrapper">
			<div class="content-header row">
			</div>
			<div class="content-body">
				<h1 class="mb-1">
					Role Permissions
				</h1>

				<div class="card">
					<div class="card-content" aria-expanded="true">
						<div class="card-body">
							@include('admin.inc.messages')

							<form id="role_form" class="form-horizontal" novalidate="novalidate">
								{{ csrf_field() }}

								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
										<div class="form-group">
											<select name="module_id" class="select2" id="module_id" data-rule-required="true" data-msg-required="Module is Required">
												@foreach($modules as $module)
													<option value="{{ $module->id }}">{{ $module->name }}</option>
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
										<div class="form-group">
											<select name="permissions[]" class="select2" disabled multiple="multiple" id="permissions" data-rule-required="true" data-msg-required="Permission is Required">
												@foreach($modules as $module)
													@if($module->id != 18)
														<option value="{{ $module->id }}">{{ $module->name }}</option>
													@endif
												@endforeach
											</select>
										</div>
									</div>

									<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
										<div class="form-group">
											<button type="submit" class="btn btn-primary btn-block">Search</button>
										</div>
									</div>

									<div class="col-12">
										<h4 class="form-section mb-2">Permissions</h4>
									</div>

									<div class="col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3">
										<div class="nav flex-column nav-pills border-info rounded-0" id="admin_roles" role="tablist" aria-orientation="vertical">
											
										</div>
									</div>
									<div class="col-6 col-xs-6 col-sm-6 col-md-8 col-lg-9">
										<div class="tab-content" id="module_permissions">
											
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
	<script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#role_form #module_id').prepend('<option value="" selected="selected"></option>').select2({
				width: '100%',
				placeholder: 'Select Module*'
			});

			$('#role_form #permissions').select2({
                placeholder:'Search Shipment Status',
                width:'100%',
                allowClear:true
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

					module_id = $("#module_id").val();
					permissions = $("#permissions").val();

					$.ajax({
						url: '{!! route('admin.user_management.roles.permissions.list') !!}',
						method: 'POST',
						data: {
							'module_id': module_id,
							'permissions': permissions,
							'_token': '{{ csrf_token() }}'
						}
					})
					.done(function(data) {

						module_id = data.module_id;
						permissions = data.permissions;
						admin_perm = data.admin_perm;

						$('#admin_roles').html('');
						$('#module_permissions').html('');

						$.each(admin_perm, function(id,details) {
							if(id == 0)
							{
								$('#admin_roles').append('<a class="nav-link rounded-0 active" id="module_'+details.id+'_tab" data-toggle="pill" href="#module_'+details.id+'_tabpanel" role="tab" aria-controls="module_'+details.id+'_tabpanel" aria-selected="true">('+details.id + ') '+  details.name +'</a>');
							}
							else{
								$('#admin_roles').append('<a class="nav-link rounded-0" id="module_'+details.id+'_tab" data-toggle="pill" href="#module_'+details.id+'_tabpanel" role="tab" aria-controls="module_'+details.id+'_tabpanel" aria-selected="false">('+details.id + ') '+details.name+'</a>');
							}
						});

						$.each(admin_perm, function(id,details) {

							if(id == 0)
							{
								$('#module_permissions').append('<div class="tab-pane fade show active" id="module_'+details.id+'_tabpanel" role="tabpanel" aria-labelledby="module_'+details.id+'_tab">');
									
									$.each(details.permissions, function(key,value) {
										$('#module_'+details.id+'_tabpanel').append('<fieldset class="d-inline-block m-1"> <input type="checkbox" checked id="permission_'+value.id+'" class="permission perm_check_'+details.id+'" name="permission_ids[]" value="'+value.id+'"> <label for="permission_'+value.id+'">'+value.name+'</label> </fieldset>');
									});

								$('#module_permissions').append('</div>');
								
							}
							else{
								
								$('#module_permissions').append('<div class="tab-pane fade" id="module_'+details.id+'_tabpanel" role="tabpanel" aria-labelledby="module_'+details.id+'_tab">');
									$.each(details.permissions, function(key,value) {

										$('#module_'+details.id+'_tabpanel').append('<fieldset class="d-inline-block m-1"> <input type="checkbox" checked id="permission_'+value.id+'" class="checkbox permission perm_check_'+details.id+'" name="permission_ids[]" value="'+value.id+'"> <label for="permission_'+value.id+'">'+value.name+'</label> </fieldset>');

									});

								$('#module_permissions').append('</div>');

							}

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

						$('input').iCheck('disable');
					});
					
				}
			});

			$("#role_form #module_id").on('change',function (){

				_this = $("#module_id").val();

				

				$.ajax({
                    url: '{!! route('admin.user_management.roles.permissions.modulepermission') !!}',
                    method: 'POST',
                    data: {
                        'module_id': _this,
                        '_token': '{{ csrf_token() }}'
                    }
                })
				.done(function(data) {
					if(data)
					{
						$("#role_form #permissions").removeAttr('disabled');
						$("#role_form #permissions").html('');

						$.each(data, function(id,details) {

							$("#role_form #permissions").append('<option value="'+details.id+'"> '+details.name+' </option>');

						});


					}
					else{

						$("#role_form #permissions").attr('disabled','disabled');
						var message = "Invalid Response Data Not load ! please contact administrator";

						toastr.error(message, 'Error!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
					}
				});
			});
		});
	</script>
@endsection