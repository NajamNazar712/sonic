@extends('admin.layout.master')

@section('title', 'Update CRM Permission')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Update CRM Permission
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <form id="role_form" class="form-horizontal" method="POST" action="{{ route('admin.crm.update.store', ['id' => $role->id]) }}" novalidate="novalidate">
                                {{ csrf_field() }}

                                <div class="row justify-content-center">

                                    <div class="col-12">
                                        <h4 class="form-section mb-2">Permissions</h4>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9">
                                        <div class="tab-content">
                                            @foreach($modules as $module)
                                                @if ($loop->first)
                                                    <div class="tab-pane fade show active" id="module_{{ $module->id }}_tabpanel" role="tabpanel" aria-labelledby="module_{{ $module->id }}_tab">
                                                        @foreach($module->permissions as $permission)
                                                            @if($permission->id != 188)
                                                                <fieldset class="d-inline-block m-1">
                                                                    @if (in_array($permission->id, $permissions))
                                                                        <input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}" checked="checked">
                                                                    @else
                                                                        <input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
                                                                    @endif
                                                                    <label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                                                </fieldset>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="tab-pane fade" id="module_{{ $module->id }}_tabpanel" role="tabpanel" aria-labelledby="module_{{ $module->id }}_tab">
                                                        @foreach($module->permissions as $permission)
                                                            @if($permission->id != 188)
                                                                <fieldset class="d-inline-block m-1">
                                                                    @if (in_array($permission->id, $permissions))
                                                                        <input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}" checked="checked">
                                                                    @else
                                                                        <input type="checkbox" id="permission_{{ $permission->id }}" class="permission" name="permission_ids[]" value="{{ $permission->id }}">
                                                                    @endif
                                                                    <label for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                                                </fieldset>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
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
        });
    </script>
@endsection