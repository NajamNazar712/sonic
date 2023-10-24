@extends('admin.layout.master')

@section('title', 'Operations Performance Report')

@section('content')
    <h1 class="mb-1">
        Operations Performance Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <form id="search_form" class="form-inline mb-1 justify-content-center search_form" novalidate="novalidate">
                    @csrf
                    <div class="row mb-2 justify-content-center">
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_shippers[]" id="search_shippers" class="form-control select2"
                                    multiple="multiple">

                                </select>
                            </fieldset>
                        </div>

                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_origin" id="search_origin" class="form-control select2">

                                </select>
                            </fieldset>
                        </div>
                        <div class="col-4">
                            <fieldset class="form-group">
                                <select name="search_destination" id="search_destination" class="form-control select2">

                                </select>
                            </fieldset>
                        </div>
                        <div class="col-4 mt-2">
                            <fieldset class="form-group">
                                <select name="search_hub" id="search_hub" class="form-control select2">

                                </select>
                            </fieldset>
                        </div>
                        <div class="col-4 mt-2">
                            <fieldset class="form-group">
                                <select name="search_status" id="search_status" class="form-control select2">

                                </select>
                            </fieldset>
                        </div>

                        <div class="col-4 mt-2">
                            <fieldset class="form-group">
                                <input type="text" class="form-control search_tracking_no" name="search_tracking_no"
                                    id="search_tracking_no" placeholder="Search Tracking Number">
                            </fieldset>
                        </div>

                        <div class="col-3 mt-2">

                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>

                                <input type="text" name="search_date_from"
                                    class="form-control pickadate bg-primary border-primary white rounded-right"
                                    id="search_date_from" placeholder="Date (From)" 
                                    data-rule-required="true" data-msg-required="This field is required">
                            </div>
                        </div>
                        <div class="col-3 mt-2">
                            <div class="form-group input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                        <span class="la la-calendar-o"></span>
                                    </span>
                                </div>

                                <input type="text" name="search_date_to"
                                    class="form-control pickadate bg-primary border-primary white rounded-right"
                                    id="search_date_to" placeholder="Date (To)" 
                                    data-rule-required="true" data-msg-required="This field is required">
                            </div>
                        </div>
                        <div class="col-2 mt-2">
                            <button type="submit" id="search_filter_btn"
                                class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i>
                                Search</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">

    <style>
        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }

        .search_tracking_no {
            width: 100% !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/selectize.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            var select = $('#search_tracking_no').selectize({
                placeholder: 'Tracking Number(s)*',
                delimiter: ',',
                createOnBlur: true,
                persist: false,
                plugins: ['remove_button'],
                onDropdownOpen: function(dropdown) {
                    dropdown.remove();
                },
                onType: function(str) {
                    var regex = /^[0-9,]+$/;

                    if (!regex.test(str)) {
                        select[0].selectize.setTextboxValue('');
                    }
                },
                create: function(input) {
                    if (input.length >= 6 && Math.floor(input) == input && $.isNumeric(input)) {
                        return {
                            value: input,
                            text: input
                        }
                    } else {
                        return false;
                    }
                }
            });


            var from_date = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var maxDate = new Date(context.select);
                        maxDate.setMonth(maxDate.getMonth() + 2);
                        $('#search_date_to').pickadate('picker').set('max', maxDate);
                    }
                }
            });

            var to_date = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
            });

            
            $.ajax({
                url: '{!! route('admin.reports.operations_performance.data') !!}',
                method: 'GET',
                success: function(data) {
                    // Reinitialize and set placeholders for the select2 elements
                    initializeSelect2WithPlaceholder('#search_shippers', data.shippers,
                        'Select Shippers');
                    initializeSelect2WithPlaceholder('#search_origin', data.cities,
                        'Select Origin City');
                    initializeSelect2WithPlaceholder('#search_destination', data.cities,
                        'Select Destination City');
                    initializeSelect2WithPlaceholder('#search_hub', data.hubs, 'Select Hub');
                    initializeSelect2WithPlaceholder('#search_status', data.statuses, 'Select Status');
                },
                error: function(error) {
                    console.log('AJAX error: ' + error);
                }
            });

            function initializeSelect2WithPlaceholder(selectId, data, placeholderText) {
                var select = $(selectId);
                select.empty();

                // Add a placeholder option
                select.append($('<option>', {
                    value: '',
                    text: placeholderText
                }));

                // Populate with data options
                data.forEach(function(item) {
                    var option = $('<option>', {
                        value: item.id,
                        text: item.name
                    });
                    select.append(option);
                });

                // Reinitialize select2 for the updated element
                select.select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: placeholderText // Set the placeholder text
                    // Other select2 options if needed
                });

                // Trigger the change event for the select2 element
                select.trigger('change');
            }

            $('#search_form').validate({
                ignore: [],
                errorClass: 'danger',
                successClass: 'success',
                errorPlacement: function(error, element) {
                    error.addClass('w-100').appendTo(element.parents('.form-group'));
                },
                submitHandler: function(form) {
                    blockPagePermanently();

                    var search_shippers = $('#search_shippers').val();
                    var search_origin = $('#search_form #search_origin').val();
                    var search_destination = $('#search_form #search_destination').val();
                    var search_hub = $('#search_form #search_hub').val();
                    var search_status = $('#search_form #search_status').val();
                    var search_tracking_no = $('#search_form #search_tracking_no').val();

                    var search_date_from = $('#search_form input[name="search_date_from_formatted"]')
                        .val();
                    var search_date_to = $('#search_form input[name="search_date_to_formatted"]').val();


                    $.ajax({
                        url: '{!! route('admin.reports.operations_performance.export_to_excel') !!}',
                        method: 'post',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'search_shippers': search_shippers,
                            'search_origin': search_origin,
                            'search_destination': search_destination,
                            'search_hub': search_hub,
                            'search_status': search_status,
                            'search_tracking_no': search_tracking_no,
                            'search_date_from': search_date_from,
                            'search_date_to': search_date_to,
                        }
                    }).done(function(data) {
                        if (data.status === 1) {

                            var route = '{!! url('/') !!}' + '/' + data.file_name;
                            window.open(route, '_black');
                            UnblockPagePermanently();

                        } else {

                            UnblockPagePermanently();
                            toastr.error(data.message, 'Error!', {
                                positionClass: 'toast-top-center',
                                containerId: 'toast-top-center'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
