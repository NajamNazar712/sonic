@extends('client.layout.master')

@section('title', 'Daraz MIS')

@section('content')
    <h1 class="mb-1">
        Daraz MIS
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('client.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">
                    <div class="col-3">
                        <fieldset class="form-group">
                            <input type="text"  id="search_tracking_no" name="tracking_numbers" class="tracking_numbers" placeholder="Tracking Number(s)*" data-tags-input-name="tracking_number" data-rule-required="true" data-msg-required="Tracking Number is required">
                        </fieldset>
                    </div>
                    <div class="col-3">
                        <fieldset class="form-group">
                            <select name="search_user" id="search_user" class="form-control select2">
                                @foreach($sister_accounts as $sister_account)
                                    <option value="{{$sister_account->id}}">{{$sister_account->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-3">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Arrival Date</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Clubbed Status</th>
                        <th class="border-primary border-darken-1">Actual Weight</th>
                        {{-- <th class="border-primary border-darken-1">Return Reason</th> --}}
                        <th class="border-primary border-darken-1">Last Reason</th>
                        <th class="border-primary border-darken-1">Attempts</th>
                        <th class="border-primary border-darken-1">Return Attempts</th>
                        <th class="border-primary border-darken-1">Return Attempt Date/Time</th>
                        <th class="border-primary border-darken-1">Return Status</th>
                        <th class="border-primary border-darken-1">Last Remarks</th>
                        <th class="border-primary border-darken-1">Last Attempt Date</th>
                        <th class="border-primary border-darken-1">Delivered Returned Date</th>
                        <th class="border-primary border-darken-1">Received Refused by</th>
                        <th class="border-primary border-darken-1">Sister Account</th>

                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}"><style>
        table.dataTable {
            font-size: 12px;
        }

        table.dataTable thead tr th {
            padding-left: 0.5em;
            white-space: normal;
            word-wrap: break-word;
        }

        table.dataTable thead tr th:before,
        table.dataTable thead tr th:after {
            height: 20px;
            margin-bottom: -10px;
            bottom: 50% !important;
        }

        table.dataTable tbody tr td {
            padding-left: 0.5em;
            padding-right: 0.5em;
        }

        table.dataTable tbody tr td.select-checkbox:before {
            top: 50%;
            border-color: #64a0d2;
        }

        table.dataTable tbody tr.selected td.select-checkbox:after {
            top: 50%;
            text-shadow: none;
        }

        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }
        a.btn.btn-secondary {
            border-radius: 20px;
            background: #64a0d2;
        }
        .btn-group .dropdown-menu .dropdown-item {
            white-space: normal;
        }
        #toast-bottom-center.toast-container {
            text-align: center;
        }

        #toast-bottom-center.toast-container .toast {
            display: table;
            width: auto !important;
            text-align: left;
        }
    </style>
@endsection

@section('js')

    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>

    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>

    {{--<script src="{{asset('js/main-1.0.js')}}" type="text/javascript"></script>--}}

    <script type="text/javascript">
        $(document).ready(function () {
            // $('#search_tracking_no').inputmask({
            //     'alias': 'integer',
            //     'allowMinus': false,
            //     'allowPlus': false
            // });
            $('#search_user').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Sister Account',
                width:'100%',
            });
            var select = $('.tracking_numbers').selectize({
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
                    }
                    else {
                        return false;
                    }
                }
            });
            $('#search_type').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Adjustment Type',
                width:'100%',
                allowClear:true
            });
            $('#search_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_to').pickadate('picker').set('min', $('#search_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_form #search_date_from').pickadate('picker').set('max', $('#search_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('cod.reports.daraz_mis.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Tracking Number');
                            head.push('Arrival Date');
                            head.push('Status');
                            head.push('Clubbed Status');

                            head.push('Actual Weight');
                            // head.push('Return Reason');
                            head.push('Last Reason');
                            head.push('Attempts');
                            head.push('Return Attempts');
                            head.push('Return Attempt Time');
                            head.push('Return Status');

                            head.push('Last Remarks');
                            head.push('Last Attempt Date');
                            head.push('Delivered Returned Date');
                            head.push('Received Refused by');
                            head.push('Sister Account');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.arrival_date);
                                row.push(values.status_name);
                                row.push(values.current_status);
                                row.push(values.actual_weight);
                                // row.push(values.return_reason);
                                row.push(values.last_reason);
                                row.push(values.attempts);
                                row.push(values.return_attempts);
                                row.push(values.return_attempt_time);
                                row.push(values.return_status);
                                row.push(values.rider_remarks);
                                row.push(values.last_attempt_date);
                                row.push(values.delivered_or_returned);
                                row.push(values.received_or_refused_by);
                                row.push(values.shipper_name);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Daraz MIS Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                autoWidth: false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('cod.reports.daraz_mis.list') }}',
                    data: function (d) {
                        d.search_tracking = $('#search_tracking_no').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_user = $('#search_user').val();

                    }
                },
                rowId: 'shipment_id',
                order: [[2, 'desc']],
                columns: [

                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    { data:'tracking_number_link' ,name: 'shipments.tracking_number', class: 'align-middle text-center tracking_number'},
                    { data:'arrival_date' ,name: 'sj.created_at', class: 'align-middle text-center not_search'},
                    { data:'status_name' ,name: 'ss.name', class: 'align-middle text-center not_search'},
                    { data:'current_status' ,name: 'ss.name', class: 'align-middle text-center not_search'},
                    { data:'actual_weight' ,name: 'shipments.actual_weight', class: 'align-middle not_search'},
                    // { data:'return_reason' ,name: 'ssr.name', class: 'align-middle not_search'},
                    { data:'last_reason' ,name: 'last_reason', class: 'align-middle not_search', orderable: false, searchable: false},
                    { data:'attempts' ,name: 'attempts', class: 'align-middle not_search', orderable: false, searchable: false},
                    { data:'return_attempts' ,name: 'return_attempts', class: 'align-middle not_search', orderable: false, searchable: false},
                    { data:'return_attempt_time' ,name: 'return_attempt_time', class: 'align-middle not_search', orderable: false, searchable: false},
                    { data:'return_status' ,name: 'return_status', class: 'align-middle not_search', orderable: false, searchable: false},
                    { data:'rider_remarks' ,name: 'rider_remarks', class: 'align-middle not_search', orderable: false, searchable: false},
                    { data:'last_attempt_date' ,name: 'atmpdate.created_at', class: 'align-middle not_search'},
                    { data:'delivered_or_returned' ,name: 'dr.created_at', class: 'align-middle not_search'},
                    { data:'received_or_refused_by' ,name: 'dr.received_or_refused_by', class: 'align-middle not_search'},
                    { data:'shipper_name' ,name: 'u.name', class: 'align-middle shipper_name'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    // var drop_select = '<select name="shipper_name" id="shipper_name" class="select2 form-control">' +
                    //         '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.tracking_number') || $(header).is('.serial_number') || $(header).is('.not_search')  || $(header).is('.shipper_name')) {
                            $(td).appendTo($(search));
                        }
                        // else if ($(header).is('.shipper_name')) {
                        //         $(drop_select).appendTo($(search))
                        //         .on('change', function () {
                        //             column.search($(this).val(), false, false, true).draw();
                        //         }).wrap(td);
                        // }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });

                    // var data = $.map({!! $sister_accounts !!}, function (obj) {
                    //         obj.id = obj.id;

                    //         return obj;
                    //     });

                    //     var data = $.map({!! $sister_accounts !!}, function (obj) {
                    //         obj.text = obj.name;

                    //         return obj;
                    //     });

                    // $('#shipper_name').prepend('<option value="" selected></option>').select2({
                    //     data:data,
                    //     placeholder: "Select Sister Account",
                    //     width:'100%',
                    //     containerCssClass: 'select-xs',
                    //     dropdownCssClass: 'form-control-sm p-0'
                    // });
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            function print(id){
                $.ajax({
                    url: '{!! route('cod.finance.payments.details_print') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'id': id
                    }
                })
                    .done(function(data) {
                        var tab = window.open('', '_blank');

                        if(!tab) {
                            swal({
                                title: 'Popup Blocker Enabled!',
                                text: 'Please add this site to your exception list.',
                                icon: 'error',
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            });
                        }
                        else {
                            tab.document.write(data);
                            tab.document.close();
                            tab.focus();
                        }
                    });
            }
            // $('#datatable tbody').on('click', 'tr td.done_payment_id button', function() {
            //     var id = parseInt($(this).parents('tr').attr('id'));
            //     if (id) {
            //         print(id);
            //     } else {
            //         var error = "Payment Details not found!";
            //         toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

            //     }
            // });

        });
    </script>
@endsection