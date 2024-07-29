@extends('admin.layout.master')

@section('title', 'SMS Logs')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-body">
                <h1 class="mb-1">
                    SMS Logs
                </h1>
                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="search_form" class="form mb-4" nonvalidate="nonvalidate">
                                @csrf
                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group input-group ">
                                            <div class="input-group-prepend">
                                                <span
                                                    class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                            </div>

                                            <input type="text" name="search_date_from"
                                                class="form-control pickadate bg-primary border-primary white rounded-right"
                                                id="search_date_from" placeholder="Date (From)" data-rule-required="true"
                                                data-msg-required="Date is required">
                                        </div>
                                    </div>

                                    <div class="col-5">
                                        <div class="form-group input-group ">
                                            <div class="input-group-prepend">
                                                <span
                                                    class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                    <span class="la la-calendar-o"></span>
                                                </span>
                                            </div>

                                            <input type="text" name="search_date_to"
                                                class="form-control pickadate bg-primary border-primary white rounded-right"
                                                id="search_date_to" placeholder="Date (To)" data-rule-required="true"
                                                data-msg-required="Date is required">
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <button type="button" id="search_btn" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr role="row" class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No.</th>
                                        <th class="border-primary border-darken-1">Name</th>
                                        <th class="border-primary border-darken-1">Tracking number</th>
                                        <th class="border-primary border-darken-1">SMS sent at</th>
                                        <th class="border-primary border-darken-1">Sent to</th>
                                        <th class="border-primary border-darken-1">SMS content</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/cryptocoins/cryptocoins.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">

    <style>
        #datatable_filter {
            display: none;
        }

        .card {
            width: 100%;
        }

        .card-body {
            width: 100%;
        }

        #datatable {
            width: 100% !important;
        }

        #datatable_paginate {
            margin-top: 1.5rem;
        }
    </style>

@endsection

@section('js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/datatable_buttons.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js') }}"
        type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/additional-methods.min.js') }}" type="text/javascript">
    </script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}" type="text/javascript"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            var from_date = $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                        var currentDate = moment(old_date_formatted);
                        var to_date_formatted = $('input[name="search_date_to_formatted"]').val();
                        var toDate = moment(to_date_formatted);
                        if (currentDate.format('x') > toDate.format('x')) {
                            to_date.pickadate('picker').clear();
                        }
                        var afterDate = currentDate.add(30, 'days');
                        to_date.pickadate('picker').set({
                            'max': afterDate.toDate()
                        }, {
                            muted: true
                        });
                    }
                }
            });
            var to_date = $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        var current_date_formatted = $('input[name="search_date_to_formatted"]').val();
                        var currentDate = moment(current_date_formatted);
                        var from_date_formatted = $('input[name="search_date_from_formatted"]').val();
                        var fromDate = moment(from_date_formatted);
                        if (currentDate.format('x') < fromDate.format('x')) {
                            from_date.pickadate('picker').clear();
                        }
                        var beforeDate = currentDate.subtract(30, 'days');
                        from_date.pickadate('picker').set({
                            'min': beforeDate.toDate()
                        }, {
                            muted: true
                        });
                    }
                }
            });

            var table = $('#datatable').DataTable({
                scrollX: true,
                scrollY: true,
                lengthMenu: [
                    [50, 100, 500, 1000, -1],
                    [50, 100, 500, 1000, 'All']
                ],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                deferLoading: 0,
                rowId: 'id',
                ajax: {
                    url: '{{ route('admin.sms_logs.list') }}',
                    type: "POST",
                    data: function(d) {
                        d['_token'] = "{{ csrf_token() }}";
                        d.search_from = $('#search_date_from').val();
                        d.search_to = $('#search_date_to').val();
                    }
                },
                columns: [{
                        data: null,
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        class: 'align-middle serial_number'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        class: 'align-middle name',
                        searchable: true
                    },
                    {
                        data: 'tracking_number',
                        name: 'tracking_number',
                        class: 'align-middle tracking_number',
                        searchable: true
                    },
                    {
                        data: 'sms_created_at',
                        name: 'sms_created_at',
                        class: 'align-middle created_at',
                        searchable: true
                    },
                    {
                        data: 'to',
                        name: 'to',
                        class: 'align-middle to',
                        searchable: true
                    },
                    {
                        data: 'body',
                        name: 'body',
                        class: 'align-middle body',
                        searchable: false
                    }
                ],
                rowCallback: function(row, data, index) {
                    var info = this.api().page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>')
                        .appendTo(this.api().table().header());
                    var td =
                        '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input =
                        '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon =
                        '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var name = '<select name="name" id="name" class="select2 form-control"></select>';

                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = $(column.header());
                        if ($(header).is('.body') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        } else if (header.is('.name')) {
                            $(name).appendTo($(search))
                                .on('change', function() {
                                    column.search($(this).val(), false, false, true).draw();
                                })
                                .wrap(td);
                        } else if (header.is('.to') || header.is('.created_at') || header.is(
                                '.tracking_number')) {
                            var inputField = $(input).appendTo($(search)).on('change',
                                function() {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td).after(icon);
                            if (column.search()) {
                                inputField.val(column.search());
                            }
                        }
                    });
                    var data = $.map({!! $notifications !!}, function(obj) {
                        return {
                            id: obj.id,
                            text: obj.name
                        };
                    });
                    $("#name").prepend('<option value="" selected></option>').select2({
                        data: data,
                        placeholder: "Select Notification Status",
                        width: '100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0',
                    });
                }
            });
            $("#search_btn").on("click", function(event) {
                event.preventDefault();
                table.draw();
            });
        });
    </script>
@endsection
