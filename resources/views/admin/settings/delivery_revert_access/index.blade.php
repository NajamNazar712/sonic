@extends('admin.layout.master')

@section('title', 'Delivery Revert Access')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Delivery Revert Access
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">

                                    <form id="delivery_revert_access_form" class="form-horizontal text-center"
                                        method="POST" action="{{ route('admin.settings.delivery_revert_access.store') }}"
                                        novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <select name="finance_admins[]" id="finance_admins_select"
                                                    class="form-control select2" multiple>
                                                    @foreach ($finance_admins as $finance_admin)
                                                        <option value="{{ $finance_admin->id }}"
                                                            @if ($admins && $admins->text && in_array($finance_admin->id, explode(',', $admins->text))) selected @endif>
                                                            {{ $finance_admin->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Assign</button>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('#finance_admins_select').select2({
                placeholder: 'Finance Administrators',
                width: '100%',
                allowClear: true
            }).bind('select2:select', function() {

                if ($(this).val().length != 0) {
                    $('#delivery_revert_access_form').find('button[type=submit]').prop('disabled', false);
                }
            });

            // $('#finance_admins_select').on('select2:unselect', function() {
            //     if ($(this).val().length == 0) {
            //         $('#delivery_revert_access_form').find('button[type=submit]').prop('disabled', true);
            //     }
            // });

            $('#delivery_revert_access_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to allow these admins',
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
                    }).then(function(confirm) {
                        if (confirm) {
                            $(form).find('button[type=submit]').attr('disabled', 'disabled');
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection
