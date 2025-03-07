@extends('admin.layout.master')

@section('title', 'Bulk Booking (Regular) & Tracking')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">Bulk Booking (Regular) & Tracking</h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')

                            <div class="row justify-content-center">
                                <div class="col-6">

                                    <form id="settings_form" class="form-horizontal text-center" method="POST" action="{{ route('admin.settings.shippers.bulk_booking_and_tracking.store') }}" novalidate="novalidate">
                                        {{ csrf_field() }}

                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <label for="tracking_select">Select Bulk Tracking Shipper</label>
                                                <select name="tracking_select[]" id="tracking_select" class="form-control select2" multiple="multiple" >
                                                    <option value="" disabled>Select a shipper</option>
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{ $shipper->id }}"
                                                                @if(in_array($shipper->id, $bulk_tracking_shippers)) selected @endif>
                                                            {{ $shipper->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-2 justify-content-center">
                                            <div class="col-12 form-group">
                                                <label for="booking_select">Select Bulk Booking (Regular) Shipper</label>
                                                <select name="booking_select[]" id="booking_select" class="form-control select2" multiple="multiple" >
                                                    <option value="" disabled>Select a shipper</option>
                                                    @foreach($shippers as $shipper)
                                                        <option value="{{ $shipper->id }}"
                                                                @if(in_array($shipper->id, $bulk_booking_shippers)) selected @endif>
                                                            {{ $shipper->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <button type="submit" class="btn btn-primary" id="submit_button" disabled>Update</button>
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
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {

            function initSelect2(selector, placeholder) {
                $(selector).select2({
                    placeholder: placeholder,
                    width: '100%',
                    allowClear: true
                }).on('change', function() {
                    toggleSubmitButton();
                });
            }

            initSelect2('#tracking_select', 'Select Bulk Tracking Shipper');
            initSelect2('#booking_select', 'Select Bulk Booking (Regular) Shipper');

            function toggleSubmitButton() {
                const isTrackingSelected = $('#tracking_select').val().length > 0;
                const isBookingSelected = $('#booking_select').val().length > 0;
                $('#submit_button').prop('disabled', !(isTrackingSelected || isBookingSelected));
            }

            $('#settings_form').validate({
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to update Permssions!',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function(confirm) {
                        if (confirm) {
                            blockPagePermanently();
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection
