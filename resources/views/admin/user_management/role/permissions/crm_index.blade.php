@extends('admin.layout.master')

@section('title', 'CRM | Role Permissions')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    CRM | Role Permissions
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
                                            <select name="permissions[]" class="select2" multiple="multiple"
                                                id="permissions">
                                                @foreach ($modules as $module)
                                                    @if ($module->id != 18)
                                                        <option value="{{ $module->id }}">{{ $module->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <div class="alert alert-danger d-none" id="text_msg">No Permission Added</div>
                                        </div>
                                    </div>

                                    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block">Search</button>

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form id="role_form_update" class="form-horizontal" method="POST"
                                action="{{ route('admin.user_management.roles.permissions.store') }}"
                                novalidate="novalidate">
                                {{ csrf_field() }}
                                <input type="hidden" name="update_module_id" id="update_module_id" value="">
                                <input type="hidden" name="update_module_permission" id="update_module_permission"
                                    value="">
                                <div class="row d-none" id="permission_area">
                                    <div class="col-12">
                                        <h4 class="form-section mb-2">Permissions</h4>
                                    </div>

                                    <div class="col-6 col-xs-6 col-sm-6 col-md-4 col-lg-3">
                                        <div class="nav flex-column nav-pills border-info rounded-0" id="admin_roles"
                                            role="tablist" aria-orientation="vertical">

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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/icheck/icheck.css') }}">
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/icheck/icheck.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            $('#permissions').select2({
                placeholder: 'Search Permission',
                width: '100%',
                allowClear: true
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
                            url: '{!! route('admin.crm_permission.list') !!}',
                            method: 'POST',
                            data: {
                                'permissions': permissions,
                                '_token': '{{ csrf_token() }}'
                            }
                        })
                        .done(function(data) {
                            if (data.admin_perm.length == 0) {
                                $('#permission_area').addClass('d-none');
                                $('#text_msg').removeClass('d-none');

                            } else {
                                $('#permission_area').removeClass('d-none');
                                $('#text_msg').addClass('d-none');

                                module_id = data.module_id;
                                permissions = data.permissions;
                                admin_perm = data.admin_perm;
                                permissions_data = data.permissions_data;

                                $('#update_module_id').val(module_id);

                                $('#admin_roles').html('');
                                $('#module_permissions').html('');

                                var appendedIDs = []; // Array to store appended IDs

                                $.each(admin_perm, function(id, details) {
                                    var isAlreadyAppended = appendedIDs.includes(details
                                        .id);

                                    if (!isAlreadyAppended) {
                                        appendedIDs.push(details.id);

                                        var activeClass = id == 0 ? 'active' : '';

                                        var linkHTML =
                                            '<a class="nav-link rounded-0 ' + activeClass +
                                            '" id="module_' +
                                            details.id +
                                            '_tab" data-toggle="pill" href="#module_' +
                                            details.id +
                                            '_tabpanel" role="tab" aria-controls="module_' +
                                            details.id +
                                            '_tabpanel" aria-selected="' +
                                            (activeClass === 'active') + '">(' +
                                            details.id + ') ' + details.name + ' (' +
                                            details.department + ')' + '</a>';

                                        $('#admin_roles').append(linkHTML);
                                    }
                                });


                                var appendedModuleIDs = [];

                                $.each(admin_perm, function(id, details) {
                                    if (!appendedModuleIDs.includes(details.id)) {
                                        appendedModuleIDs.push(details.id);

                                        var activeClass = id == 0 ? 'show active' : '';

                                        $('#module_permissions').append(
                                            '<div class="tab-pane fade ' + activeClass +
                                            '" id="module_' +
                                            details.id +
                                            '_tabpanel" role="tabpanel" aria-labelledby="module_' +
                                            details.id +
                                            '_tab"> <div class="row"> <div class="col-12"> <div class="text-center mt-2"> <button type="button" data-module_id="' +
                                            details.id +
                                            '" class="selectAll d-none btn btn-primary">Select All</button> <button type="button" data-module_id="' +
                                            details.id +
                                            '" class="unselectAll d-none btn btn-primary">Unselect All</button> </div> </div> </div>'
                                        );

                                        $.each(permissions_data, function(key, value) {
                                            var checked = details.permissions
                                                .includes(value.id) ? 'checked' :
                                                '';

                                            $('#module_' + details.id + '_tabpanel')
                                                .append(
                                                    '<fieldset class="d-inline-block m-1"> <input type="checkbox" ' +
                                                    checked + ' id="permission_' +
                                                    value.id +
                                                    '" class="permission perm_check_' +
                                                    details.id +
                                                    '" name="permission_ids[' +
                                                    details.id + '][]" value="' +
                                                    value.id +
                                                    '"> <label for="permission_' +
                                                    value.id + '">' + value.name +
                                                    '</label> </fieldset>');
                                        });

                                        $('#module_permissions').append('</div></div>');
                                    }
                                });

                            }


                            $('.permission').each(function() {
                                var checkbox = $(this);
                                var label = checkbox.next();
                                var text = label.text();

                                label.remove();

                                checkbox.iCheck({
                                    checkboxClass: 'icheckbox_line pt-1 pb-1',
                                    checkedClass: 'checked bg-success',
                                    uncheckedClass: 'bg-danger',
                                    insert: '<div class="icheck_line-icon"></div>' +
                                        text
                                });
                            });

                            $(".selectAll").on('click', function() {
                                let module_id = $(this).attr('data-module_id');
                                $(".perm_check_" + module_id).prop('checked', true);
                                $(".perm_check_" + module_id).iCheck('update');
                            });

                            $(".unselectAll").on('click', function() {
                                let module_id = $(this).attr('data-module_id');
                                $(".perm_check_" + module_id).prop('checked', false);
                                $(".perm_check_" + module_id).iCheck('update');
                            });

                        });

                }
            });



            $('#role_form_update').validate({
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

                    form.submit()

                }
            });
        });
    </script>
@endsection
