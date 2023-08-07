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
                                <input type="hidden" name="petty_account_switch" id="petty_account_input" value="head">
                                <div class="row mb-2 justify-content-center ">

                                        <div class="col-3">
                                            <div class="form-group text-center">
                                                <label for="petty_account" class="font-medium-2 text-bold-600 mr-1">Heads</label>
                                                <input type="checkbox" id="petty_account_switch" class="switchery petty_account_switch" data-color="success" data-size="sm" name="petty_account"/>
                                                <label for="petty_account" class="font-medium-2 text-bold-600 ml-1">Titles</label>
                                            </div>
                                        </div>

                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                                <span class="la la-calendar-o small-calender-icon"></span>
                                            </span>
                                            </div>
                                            <input type="text" name="search_date_from"  class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_from" placeholder="Expense From" title="Expense From" data-value="{{ Carbon\Carbon::today() }}">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group input-group">
                                            <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                            </div>
                                            <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right pickadate" id="search_date_to" placeholder="Expense To" title="Expense To" data-value="{{ Carbon\Carbon::today() }}">
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
                                    <th class="border-primary border-darken-1">Account Head / Titles</th>
                                    <th class="border-primary border-darken-1">Amount</th>
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

    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            var petty_cash_account_switch = document.querySelector('.switchery.petty_account_switch');
            petty_cash_account_switch.onchange = function () {
                if(petty_cash_account_switch.checked === true){
                    $('#petty_account_input').val('title');
                }else if(petty_cash_account_switch.checked === false){
                    $('#petty_account_input').val('head');
                }
            };
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.petty_cash_expense_summary.petty_cash_summary_report') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S. No.');
                            head.push('Account Head / Title');
                            head.push('Amount');

                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.name);
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
            var total_amount = 0;
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
                    'reset'
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: -1,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.petty_cash_expense_summary.petty_cash_summary_report') }}',
                    data: function (d) {
                        d.petty_account_switch = $('#petty_account_input').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();

                    }
                },
                order: [1, 'desc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'name', name: 'pca.name', class: 'align-middle account_head_or_title'},
                    {data: 'amount', name: 'pcsd.amount', class: 'align-middle amount'}
                ],

                rowCallback: function(row, data, index) {

                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                drawCallback: function (settings) {
                    total_amount = 0;
                    var api = new $.fn.dataTable.Api( settings );
                    var sum_data = api.rows().data();
                    if(sum_data.length > 0){
                        for(var i=0;i<sum_data.length; i++)
                        {
                            total_amount += parseFloat(sum_data[i].amount);
                        }

                    }
                    else{
                        total_amount = 0;
                    }
                    $('#statements_total_amount').text(total_amount);
                },

                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
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

            $('#track_form').bind('submit', function (e) {
                e.preventDefault();
                $('#statements_total_amount').text(0);
                table.draw();
            });

        });
    </script>
@endsection
