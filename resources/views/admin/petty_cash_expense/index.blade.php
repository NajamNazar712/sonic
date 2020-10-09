@extends('admin.layout.master')

@section('title', 'Petty Cash Expense Summary Report ')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Petty Cash Expense Summary Report
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('admin.inc.messages')
                            <form id="track_form" class=" mb-1 justify-content-center" novalidate="novalidate">

                                <div class="row mb-2 justify-content-center ">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group">
                                                {{-- <input type="text" class="form-control" placeholder="Search Shipper Name" name="shipper_name" id="shipper_name">--}}
                                                <select name="search_acount_title" id="search_acount_title" class="form-control select2">
                                                    <option value="">Select Account Title</option>
                                                    @forelse($petty_cash_account_title as $account_title)
                                                        <option value="{{ $account_title->id }}">{{ $account_title->name }}</option>
                                                    @empty
                                                        <option value="">Account Titles Not Found!</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Expanse From">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                            </div>
                                            <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="Expanse To">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <button type="submit" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width search"><i class="la la-search"></i> Search</button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                            <div class="row text-center">
                                <div class="col">
                                    <b class="total_amount_span"> Total Amount : <span id="statements_total_amount">0</span></b>
                                </div>
                            </div>
                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                <tr role="row" class="bg-primary white">
                                    <th class="border-primary border-darken-1">S. No.</th>
                                    <th class="border-primary border-darken-1">Account Head</th>
                                    <th class="border-primary border-darken-1">Account Title</th>
                                    <th class="border-primary border-darken-1">City / Location</th>
                                    <th class="border-primary border-darken-1"> Amount </th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">


    <style>
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

        .selectize-control {
            width: 100%;
        }

    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pagination/moment.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {
            function print(id) {
                $.ajax({
                    url: '{!! route('admin.shipment.receiving_sheet.print') !!}',
                    method: 'POST',
                    data: {
                        'id': id,
                        '_token': '{{ csrf_token() }}'
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

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.petty_cash_expense_summary.petty_cash_summary_report') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Account Head');
                            head.push('Account Title');
                            head.push('City');
                            head.push('Amount');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.account_head);
                                row.push(values.account_title);
                                row.push(values.city);
                                row.push(values.amount);
                                body.push(row);
                            });
                            body.push()
                        },
                        async: false
                    });

                    return {body: body, header:head};
                }
            } );
            var search_date_from = $('#track_form #search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_to').pickadate('picker').set('min', $('#track_form #search_date_from').pickadate('picker').get('select'));
                    }
                }
            });

            var search_date_to = $('#track_form #search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#track_form #search_date_from').pickadate('picker').set('max', $('#track_form #search_date_to').pickadate('picker').get('select'));
                    }
                }
            });

            var table = $('#datatable').DataTable({
                scrollX: false, scrollY: '500px',
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Petty Cash Expense Summary Report',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o "></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.petty_cash_expense_summary.petty_cash_summary_report') }}',
                    data: function (d) {
                        d.petty_cash_account_titles = $('select[name="search_acount_title"]').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();

                    }
                },
                rowId: 'title_id',
                order: [1, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'account_head', name: 'petty_cash_account_heads.name', class: 'align-middle account_head'},
                    {data: 'account_title', name: 'petty_cash_account_heads.name', class: 'align-middle account_title'},
                    {data: 'city', name: 'petty_cash_account_heads.name', class: 'align-middle city'},
                    {data: 'amount', name: 'petty_cash_statement_details.amount', class: 'align-middle amount'},
                ],

                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                    var api = this.api();
                    var intVal = function ( i ) {

                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;

                    };

                    if (api.column(4).data().length){
                        var totalAmount = api
                            .column( 4, { page: 'current'} )
                            .data()
                            .reduce( function (startValue, endValue) {
                                return intVal(startValue) + intVal(endValue);
                            } ) }
                    else{totalAmount = 0};

                    $( api.column(4).footer() ).html(
                        '$'+totalAmount,
                        $('#statements_total_amount').text(totalAmount),

                    );
                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control"></select>';
                    var mode_drop_select = '<select name="mode_select" id="mode_select" class="select2 form-control">' +
                        '</select>';
                    var service_drop_select = '<select name="service_select" id="service_select" class="select2 form-control"></select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.action') || $(header).is('.serial_number')) {
                            $(td).appendTo($(search));
                        }
                        else {
                            var current = $(input).appendTo($(search)).on('change', function() {
                                column.search($(this).val(), false, false, true).draw();
                            }).wrap(td).after(icon);

                            if (column.search()) {
                                current.val(column.search());
                            }
                        }
                    });
                    this.api().table().columns.adjust();
                }

            });

            $('#track_form #search_acount_title').prepend('<option value="" selected="selected"></option>').select2({
                placeholder: 'Account Title Name',
                allowClear:true
            });


            $('#track_form').bind('submit', function (e) {
                e.preventDefault();
                $('#statements_total_amount').text('0'),

                    table.draw();
            });

        });
    </script>
@endsection
