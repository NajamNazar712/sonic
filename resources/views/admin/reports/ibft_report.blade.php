@extends('admin.layout.master')

@section('title', 'Ibft Report')

@section('content')
    <h1 class="mb-1">
        Ibft Report
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div id="search_form" class="row mb-2 justify-content-center">

                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_tracking_no" id="search_tracking_no" placeholder="Search Tracking Number">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <input type="text" class="form-control" name="search_done_payment_id" id="search_done_payment_id" placeholder="Search Done Payment ID">
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <select name="search_shipper" id="search_shipper" class="form-control select2">
                                @foreach($shippers as $shipper)
                                <option value="{{$shipper->id}}">{{$shipper->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group ">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ Carbon\Carbon::now() }}">
                        </div>
                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
                </div>


                <table class="table table-bordered w-100" id="datatable" style="z-index: 3;">
                    <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S.No.</th>
                            <th class="border-primary border-darken-1">Done Payment ID</th>
                            <th class="border-primary border-darken-1">Shipper Name</th>
                            <th class="border-primary border-darken-1">Total Amount</th>
                            <th class="border-primary border-darken-1">Total Charges</th>
                            <th class="border-primary border-darken-1">Gst</th>
                            <th class="border-primary border-darken-1">Ibft Charges</th>
                            <th class="border-primary border-darken-1">Payable</th>
                            <th class="border-primary border-darken-1">Created At</th>
                            <th class="border-primary border-darken-1">Updated At</th>
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
            $('#search_tracking_no').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_done_payment_id').inputmask({
                'alias': 'integer',
                'allowMinus': false,
                'allowPlus': false
            });
            $('#search_shipper').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Shipper',
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
                     blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.ibft_report.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No');
                            head.push('Done Payment ID');
                            head.push('Shipper Name');
                            head.push('Total Amount');
                            head.push('Total Charges');
                            head.push('Gst');
                            head.push('Ibft Charges');
                            head.push('Payable');
                            head.push('Created At');
                            head.push('Updated At');
                            head.push('Status');
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.done_payment_id);
                                row.push(values.shipper);
                                row.push(values.amount);
                                row.push(values.charges);
                                row.push(values.gst);
                                row.push(values.ibft_charges);
                                row.push(values.payable);
                                row.push(values.created_at);
                                row.push(values.updated_at);
                                row.push(values.status);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var index_column = 0;
            var table = $('#datatable').DataTable({
                scrollX: true, scrollY: '500px',
               dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Ibft Report',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },
                ],
               lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.ibft_report.list') }}',
                    data: function (d) {
                        d.tracking_no = $('#search_tracking_no').val();
                        d.done_payment_id = $('#search_done_payment_id').val();
                        d.shipper = $('#search_shipper').val();
                        d.date_from = $('input[name="search_date_from_formatted"]').val();
                        d.date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'account_id',
                order: [[9, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'done_payment_id', name: 'dp.id', class: 'align-middle done_payment_id'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'amount', name: 'done_payment_calculations.amount', class: 'align-middle amount'},
                    {data: 'charges', name: 'done_payment_calculations.charges', class: 'align-middle charges'},
                    {data: 'gst', name: 'done_payment_calculations.gst', class: 'align-middle text-center gst'},
                    {data: 'ibft_charges', name: 'done_payment_calculations.ibft_charges', class: 'align-middle ibft_charges'},
                    {data: 'payable', name: 'done_payment_calculations.payable', class: 'align-middle payable'},
                    {data: 'created_at', name: 'done_payment_calculations.created_at', class: 'align-middle created_at'},
                    {data: 'updated_at', name: 'done_payment_calculations.updated_at', class: 'align-middle updated_at'},
                    {data: 'status', name: 'dp.status', class: 'align-middle status'},
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

        });


    </script>
@endsection