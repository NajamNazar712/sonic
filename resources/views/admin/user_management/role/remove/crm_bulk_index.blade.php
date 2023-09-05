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

                            <form id="role_form" class="form-horizontal" method="GET" action="{{ route('admin.crm_permission.bulk_remove.remove', ['ids' => $ids]) }}" novalidate="novalidate">

                                <div class="row justify-content-center">

                                    <div class="col-12">
                                        <h4 class="form-section mb-2">Permissions</h4>
                                    </div>


                                    <input type="hidden" value="{{ $ids }}" name="ids">
                                    <div class="col-xs-6 col-sm-6 col-md-8 col-lg-9">
                                        <div class="tab-content">
                                            <div class="tab-pane fade show active">
                                                @foreach ($modules as $module)
                                                    <fieldset class="d-inline-block m-1">
                                                        <input type="checkbox" id="permission_{{ $module->id }}"
                                                            class="permission" name="module_ids[]"
                                                            value="{{ $module->id }}">
                                                        <label for="module_{{ $module->id }}">{{ $module->name }}</label>
                                                    </fieldset>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary">Delete</button>
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
                    checkedClass: 'checked bg-danger',
                    uncheckedClass: 'bg-success',
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
