@extends('admin.layout.master')

@section('title', 'Delivery Revert Access')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Brand and Vendor
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">

                                    <form id="brand_and_vendor_form" class="form-horizontal text-center"
                                        method="POST" action="{{ route('admin.settings.show_vendor.store') }}"
                                        novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <select name="users[]" id="brand_and_vendor_select"
                                                    class="form-control select2" multiple>
                                                    @foreach ($shippers as $shipper)
                                                        <option value="{{ $shipper->id }}"
                                                            @if ($users && $users->text && in_array($shipper->id, explode(',', $users->text))) selected @endif
                                                            >
                                                            {{ $shipper->name }}
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
            $('#brand_and_vendor_select').select2({
                placeholder: 'Shippers',
                width: '100%',
                allowClear: true
            }).bind('select2:select', function() {

                if ($(this).val().length != 0) {
                    $('#brand_and_vendor_form').find('button[type=submit]').prop('disabled', false);
                }
            });

            // $('#brand_and_vendor_select').on('select2:unselect', function() {
            //     if ($(this).val().length == 0) {
            //         $('#brand_and_vendor_form').find('button[type=submit]').prop('disabled', true);
            //     }
            // });

            $('#brand_and_vendor_form').validate({
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
