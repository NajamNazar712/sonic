@extends('admin.layout.master')

@section('title', 'Petty Cash Statements')

@section('content')
    <h1 class="mb-1">
        Petty Cash Statements
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}">{{$hub->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_head" id="search_head" class="form-control select2">
                                @foreach($heads as $head)
                                    <option value="{{$head->id}}">{{$head->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_title" id="search_title" class="form-control select2">
                                @foreach($titles as $title)
                                    <option value="{{$title->id}}">{{$title->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_created" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_created" placeholder="Date (Creation Date)">
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
                        <th class="border-primary border-darken-1">Statement No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Statement Reference No.</th>
                        <th class="border-primary border-darken-1">Date (From - To)</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Station Approved By</th>
                        <th class="border-primary border-darken-1">Station Approved At</th>
                        <th class="border-primary border-darken-1">Operation Approved By</th>
                        <th class="border-primary border-darken-1">Operation Approved At</th>
                        <th class="border-primary border-darken-1">Status</th>
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
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#search_head').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Account Head',
                width:'100%',
                allowClear:true
            });
            $('#search_title').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Account Title',
                width:'100%',
                allowClear:true
            });

            $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Hub',
                width:'100%',
                allowClear:true
            });

            $('#search_date_created').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.petty_cash.list') }}',
                        data: {
                             'page': 'all',
                             'search_head': $('#search_head').val(),
                             'search_title': $('#search_title').val(),
                             'search_hub': $('#search_hub').val(),
                             'search_date_created': $('input[name="search_date_created_formatted"]').val(),

                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Statement No.');
                            head.push('Hub');
                            head.push('Statement Reference No.');
                            head.push('Date (From - To)');
                            head.push('Total Amount');
                            head.push('Created By');
                            head.push('Created At');
                            head.push('Station Approved By');
                            head.push('Station Approved At');
                            head.push('Operation Approved By');
                            head.push('Operation Approved At');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.statement_id);
                                row.push(values.hub_name);
                                row.push(values.reference_no);
                                row.push(values.date);
                                row.push(values.total_amount);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.station_approved_by);
                                row.push(values.station_approved_at);
                                row.push(values.operation_approved_by);
                                row.push(values.operation_approved_at);
                                row.push(values.status);

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
                scrollX: true, scrollY: '350px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Petty Cash Statement Report',
                        text:'<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.petty_cash.list') }}',
                    data: function (d) {
                        d.search_head = $('#search_head').val();
                        d.search_title = $('#search_title').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_date_created = $('input[name="search_date_created_formatted"]').val();

                    }
                },
                order: [[1, 'asc']],
                rowId:'statement_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'statement_link', name: 'petty_cash_statements.id', class: 'align-middle statement_link'},
                    {data: 'hub_name', name: 'h.name', class: 'align-middle hub_name'},
                    {data: 'reference_no', name: 'petty_cash_statements.reference_no', class: 'align-middle reference_no'},
                    {data: 'date', name: 'date', class: 'align-middle date', orderable:false},
                    {data: 'total_amount', name: 'petty_cash_statements.total_amount', class: 'align-middle total_amount'},
                    {data: 'created_by', name: 'cb.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'petty_cash_statements.created_at', class: 'align-middle created_at'},
                    {data: 'station_approved_by', name: 'sab.name', class: 'align-middle station_approved_by'},
                    {data: 'station_approved_at', name: 'petty_cash_statements.station_approved_at', class: 'align-middle station_approved_at'},
                    {data: 'operation_approved_by', name: 'oab.name', class: 'align-middle operation_approved_by'},
                    {data: 'operation_approved_at', name: 'petty_cash_statements.operation_approved_at', class: 'align-middle operation_approved_at'},
                    {data: 'status', name: 'petty_cash_statements.status', class: 'align-middle status'}
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });

            $('body').on('click','#datatable td.statement_link button', function () {
                var statement_id = parseInt($(this).parents('tr').attr('id'));
                if(statement_id){
                    printStatement(statement_id);
                }
            });

            function printStatement(id) {
                $.ajax({
                    url: '{!! route('admin.petty_cash.statements.print') !!}',
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
        });
    </script>
@endsection