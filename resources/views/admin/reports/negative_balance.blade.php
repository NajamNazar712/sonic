@extends('admin.layout.master')

@section('title', 'Negative Balance Shipper Report')

@section('content')
        <div class="card">
            <div class="card-content" aria-expanded="true">
                <div class="card-body">
                    <h1 class="text-center align-middle">Negative Balance Shipper Report</h1>
                    @include('admin.inc.messages')
                    <div class="col mt-3">
                        <form id="search_form" class="row mb-2 justify-content-center" novalidate="novalidate">
                           
                            
                            <div class="col-4">
                                <fieldset class="form-group">
                                    <select name="search_shippers[]" id="search_shippers" class="form-control select2" multiple>
                                    </select>
                                     <small class="text-danger d-none" id="shipper_error">Please select at least one shipper</small>
                                </fieldset>
                            </div>
                            
                            {{-- Search date from filter --}}
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="search_date_from" class="form-control bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)"  data-value="{{ \Carbon\Carbon::now() }}">
                                </div>
                            </div>

                            {{-- Search date to filter --}}
                            <div class="col-4">
                                <div class="form-group input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                            <span class="la la-calendar-o small-calender-icon"></span>
                                        </span>
                                    </div>
                                    <input type="text" name="search_date_to" class="form-control bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" data-value="{{ \Carbon\Carbon::now() }}">
                                </div>
                            </div>

                            {{-- Search btn --}}
                            <div class="col-2">
                                
                                    <button type="submit" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                                
                            </div>
                        </form>
                    </div>


                    
                    <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                        <thead>
                            <tr role="row" class="bg-primary white">

                                <th class="border-primary border-darken-1">S. No.</th>
                                <th class="border-primary border-darken-1">Account ID</th>
                                <th class="border-primary border-darken-1">Shipper Name</th>
                                <th class="border-primary border-darken-1">Date Time</th>
                                <th class="border-primary border-darken-1">Outstanding Amount</th>
                                <th class="border-primary border-darken-1">COD Amount</th>                                
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css/plugins/pickers/daterange/daterange.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/fonts/simple-line-icons/style.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/cryptocoins/cryptocoins.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.css') }}">
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

        #toast-top-full-width {
            position: fixed !important;
            width: 100% !important;
            top: 0 !important;
            left: 0 !important;
            text-align: center !important;
        }
        .toast-top-full-width .toast {
            width: 90rem !important;
        }
        .toast-top-full-width .toast-message {
            font-size: 24px !important;
        }
        .toast-title{
            display: none !important;
        }
    </style>
@endsection
@section('js')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    {{-- <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script> --}}
    <script src="{{asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

        
            var search_date_from = $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {

                    var old_date_formatted = $('input[name="search_date_from_formatted"]').val();
                    var contractMoment = moment(old_date_formatted);
                    var current = moment(contractMoment).add(31, 'days');
                    search_date_to.pickadate('picker').set('min', new Date(old_date_formatted),{muted:true});
                    search_date_to.pickadate('picker').set('max', new Date(current.toDate()),{muted:true});
                    search_date_to.pickadate('picker').set('select', new Date(old_date_formatted),{muted:true});
                }
            });

            var search_date_to = $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                max: '{{ Carbon\Carbon::now() }}',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                }
            });

            $('#search_shippers').select2({
                width:'100%',
                placeholder:"Select at least one shipper *",
                allowClear:true,
                multiple: true,
                minimumInputLength: 2,
                ajax: {
                    dataType: 'json',
                    url:  '{!! route('admin.accounts.shipper_names.dropdown',['type'=>'active']) !!}',
                        data: function (params) {
                            return {
                                search: params.term,
                            }
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                    delay: 700,
                }
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    if(params !== undefined){
                        params.start = 0;
                        params.length = -1;
                        params.excel = true;
                    }
                    else{
                        params = {
                            'excel':true,
                        }
                    }
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.reports.wht.list') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S. No.');
                            head.push('Account ID');
                            head.push('Shipper Name');
                            head.push('Date Time');
                            head.push('Outstanding Amount');
                            head.push('COD Amount');

            
                            $.each(result.data, function(index, values) {
                                row = [];

                                row.push(index + 1);
                                row.push(values.user_id);
                                row.push(values.shipper_name);
                                row.push(values.created);
                                row.push(values.payable);
                                row.push(values.amount);
                               
                                body.push(row);
                            });
                        
                        },
                        async: false
                    });
                    
                    return {body: body, header: head};
                    
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [{
                    extend: 'excelHtml5',
                    title: 'WHT REPORT',
                    text: '<i class="la la-file-excel-o underline"></i> Excel',
                    className: 'btn btn-primary datatable_excel_btn',
                    
                },'reset'],
                scrollX: true, scrollY: '500px',
                autoWidth: false,
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                deferLoading: 0,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.reports.negative_balance.list')}}',
                    method: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: function (d) {
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                        d.search_shippers = $('#search_shippers').val();
                    }
                },
                //order: [[1, 'desc'], [2, 'asc']], // Order by 'created_at' DESC and 'description' ASC
                order: [[3, 'desc']],
                columns: [
                    {name: 'serial_number', class: 'align-middle serial_number', orderable: false, searchable: false, targets: 0, render: function(data, type, row) {return '';}},
                    {data: 'user_id', name: 'negative_payable_daily_logs.user_id', class: 'text-center align-middle user_id', searchable: false},
                    {data: 'shipper_name', name: 'u.name', class: 'text-center align-middle shipper_name', searchable: false},
                    {data: 'created', name: 'negative_payable_daily_logs.created_at', class: 'text-center align-middle created', searchable: false},
                    {data: 'payable', name: 'negative_payable_daily_logs.payable', class: 'text-center align-middle payable', searchable: false},
                    {data: 'amount', amount: 'negative_payable_daily_logs.amount', class: 'text-center align-middle amount', searchable: false},

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
            });

        // $('#search_form').bind('submit', function (e) {
        //     e.preventDefault();
        //     var search_date_from = $('#search_form #search_date_from').val();
        //     var search_date_to = $('#search_form #search_date_to').val();

        //     if ((search_date_from != '' && search_date_to != '' )) {
        //         table.draw();
        //     }

        // });.

        $('#search_form').bind('submit', function (e) {
            e.preventDefault();

            var search_date_from = $('#search_form #search_date_from').val();
            var search_date_to = $('#search_form #search_date_to').val();
            var shippers = $('#search_shippers').val();

           $('#shipper_error').addClass('d-none');

            if (!shippers || shippers.length === 0) {
                $('#shipper_error').removeClass('d-none');
                return;
            }

            if (search_date_from == '' || search_date_to == '') {
                toastr.error('Please select date range');
                return;
            }

            table.draw();
        });

        });
    </script>
    @endsection
