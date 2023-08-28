@extends('admin.layout.master')

@section('title', 'Petty Cash Statements Report')

@section('content')
    <h1 class="mb-1">
        Petty Cash Statements Report
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
                        <fieldset class="form-group">
                            <select name="search_status" id="search_status" class="form-control select2">
                                    <option value="3">Created</option>
                                    <option value="2">Approved</option>
                                    <option value="1">Rejected</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4 ">

                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)" title="Date (From)" data-value="{{ Carbon\Carbon::today() }}">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)" title="Date (To)" data-value="{{ Carbon\Carbon::today() }}">
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
                        <th class="border-primary border-darken-1">Entry Date</th>
                        <th class="border-primary border-darken-1">Head Of Account</th>
                        <th class="border-primary border-darken-1">Title Of Account</th>
                        <th class="border-primary border-darken-1">Entry City</th>
                        <th class="border-primary border-darken-1">Expense Details</th>
                        <th class="border-primary border-darken-1">Amount</th>
                        <th class="border-primary border-darken-1">Station Amount</th>
                        <th class="border-primary border-darken-1">Operation Amount</th>
                        <th class="border-primary border-darken-1">Finance Amount</th>
                        <th class="border-primary border-darken-1">Reference No.</th>
                        <th class="border-primary border-darken-1">Remarks</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1">Statement No.</th>
                        <th class="border-primary border-darken-1">Hub</th>
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Statement Reference No.</th>
                        <th class="border-primary border-darken-1">Statement Created At</th>
                        <th class="border-primary border-darken-1">Statement Created By</th>
                        <th class="border-primary border-darken-1">Statement Checked At</th>
                        <th class="border-primary border-darken-1">Statement Checked By</th>
                        <th class="border-primary border-darken-1">Employee Id</th>
                        <th class="border-primary border-darken-1">Employee Name</th>
                        <th class="border-primary border-darken-1">Employee Designation</th>
                        <th class="border-primary border-darken-1">SDN No.</th>
                        <th class="border-primary border-darken-1">DNCC/RNCC Count</th>
                        <th class="border-primary border-darken-1">Delivery Note No</th>
                        <th class="border-primary border-darken-1">Delivered Shipments</th>
                        <th class="border-primary border-darken-1">Delivery Note Amount</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


    <div class="modal fade" id="pncc_modal" data-backdrop="static" role="dialog" aria-labelledby="pncc_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="pncc_modal_title">No. Of RNCC(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dncc_modal" data-backdrop="static" role="dialog" aria-labelledby="dncc_modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="dncc_modal_title">No. Of DNCC(s)</h4>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
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

            $('#search_status').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Select Status',
                width:'100%',
                allowClear:true
            });

            $('#search_date_from').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_to').pickadate('picker').set('min', $('#search_date_from').pickadate('picker').get('select'));
                    }
                }
            });
            $('#search_date_to').pickadate({
                firstDay: 1,
                clear: '',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onSet: function(context) {
                    if (context.select) {
                        $('#search_date_from').pickadate('picker').set('max', $('#search_date_to').pickadate('picker').get('select'));
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
                        url: '{{ route('admin.reports.petty_cash.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Entry Date');
                            head.push('Head Of Account');
                            head.push('Title Of Account');
                            head.push('Entry City');
                            head.push('Expense Details');
                            head.push('Amount');
                            head.push('Station Amount');
                            head.push('Operation Amount');
                            head.push('Finance Amount');
                            head.push('Reference No.');
                            head.push('Remarks');
                            head.push('Status');
                            head.push('Statement No.');
                            head.push('Hub');
                            head.push('Tracking Number');
                            head.push('Statement Reference No.');
                            head.push('Statement Created At');
                            head.push('Statement Created By');
                            head.push('Statement Checked At');
                            head.push('Statement Checked By');
                            head.push('Employee Id');
                            head.push('Employee Name');
                            head.push('Employee Designation');
                            head.push('SDN No.');
                            head.push('DNCC/RNCC Count');
                            head.push('Delivery Note No');
                            head.push('Delivered Shipments');
                            head.push('Delivery Note Amount');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.entry_date);
                                row.push(values.account_head);
                                row.push(values.account_title);
                                row.push(values.entry_city);
                                row.push(values.expense_details);
                                row.push(values.amount);
                                row.push(values.station_amount);
                                row.push(values.operation_amount);
                                row.push(values.finance_amount);
                                row.push(values.entry_reference_no);
                                row.push(values.remarks);
                                row.push(values.status);
                                row.push(values.statement_id);
                                row.push(values.hub_name);
                                row.push(values.tracking_number);
                                row.push(values.statement_reference_no);
                                row.push(values.created_at);
                                row.push(values.created_by);
                                row.push(values.checked_at);
                                row.push(values.checked_by);
                                row.push(values.employee_id);
                                row.push(values.employee_name);
                                row.push(values.employee_designation);
                                row.push(values.sdn_id_padded);
                                row.push(values.dncc_count);
                                row.push(values.delivery_note);
                                row.push(values.delivered_shipments);
                                row.push(values.delivery_note_amount);

                                body.push(row);
                            });
                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            } );
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                scrollX: true, scrollY: '500px',
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
                deferLoading: 0,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax:{
                    url: '{{ route('admin.reports.petty_cash.list') }}',
                    data: function (d) {
                        d.search_head = $('#search_head').val();
                        d.search_title = $('#search_title').val();
                        d.search_hub = $('#search_hub').val();
                        d.search_status = $('#search_status').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                order: [[13, 'desc']],
                rowId:'statement_id',
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'entry_date', name: 'petty_cash_statement_details.date', class: 'align-middle entry_date'},
                    {data: 'account_head', name: 'pch.name', class: 'align-middle account_head'},
                    {data: 'account_title', name: 'pct.name', class: 'align-middle account_title'},
                    {data: 'entry_city', name: 'dc.name', class: 'align-middle entry_city'},
                    {data: 'expense_details', name: 'petty_cash_statement_details.expense_details', class: 'align-middle expense_details'},
                    {data: 'amount', name: 'petty_cash_statement_details.amount', class: 'align-middle amount'},
                    {data: 'station_amount', name: 'petty_cash_statement_details.station_amount', class: 'align-middle station_amount'},
                    {data: 'operation_amount', name: 'petty_cash_statement_details.operation_amount', class: 'align-middle operation_amount'},
                    {data: 'finance_amount', name: 'petty_cash_statement_details.finance_amount', class: 'align-middle finance_amount'},
                    {data: 'entry_reference_no', name: 'petty_cash_statement_details.reference_no', class: 'align-middle entry_reference_no'},
                    {data: 'remarks', name: 'petty_cash_statement_details.remarks', class: 'align-middle remarks'},
                    {data: 'status', name: 'petty_cash_statements.status', class: 'align-middle status'},
                    {data: 'statement_link', name: 'pcs.id', class: 'align-middle statement_link'},
                    {data: 'hub_name', name: 'h.name', class: 'align-middle hub_name'},
                    {data: 'petty_cash_statement_link', name: 'shipments.tracking_number', class: 'align-middle petty_cash_statement_link'},
                    {data: 'statement_reference_no', name: 'pcs.reference_no', class: 'align-middle statement_reference_no'},
                    {data: 'created_at', name: 'pcs.created_at', class: 'align-middle created_at'},
                    {data: 'created_by', name: 'cb.name', class: 'align-middle created_by'},
                    {data: 'checked_at', name: 'pcs.checked_at', class: 'align-middle checked_at'},
                    {data: 'checked_by', name: 'chb.name', class: 'align-middle checked_by'},
                    {data: 'employee_id', name: 'employee.trax_id', class: 'align-middle employee_id'},
                    {data: 'employee_name', name: 'petty_cash_statement_details.employee_name', class: 'align-middle employee_name'},
                    {data: 'employee_designation', name: 'petty_cash_statement_details.employee_designation', class: 'align-middle employee_designation'},
                    {data: 'sdn_id_link', name: 'sdn.id', class: 'align-middle text-center sdn_id_link'},
                    {data: 'dncc_link', name: 'sdn.dncc_count', class: 'align-middle text-center dncc_link'},
                    {data: 'delivery_note', name: 'petty_cash_statement_details.dncc_id', class: 'align-middle text-center delivery_note'},
                    {data: 'delivered_shipments', name: 'petty_cash_statement_details.delivered_shipments', class: 'align-middle text-center delivered_shipments'},
                    {data: 'delivery_note_amount', name: 'dn.received_cod_amount', class: 'align-middle text-center delivery_note_amount'},
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



            $('#datatable tbody').on('click','tr td.dncc_link button',function () {
                var id = parseInt($(this).attr('data-sdn_id'));
                if(id) {
                    $.ajax({
                        url: '{!! route('admin.delivery.sdn.dn') !!}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'sdn_id': id,
                        }
                    })
                        .done(function (data) {
                            if (data.status == 1) {
                                var notes = '<div>RNCC Number(s) :</div>';

                                if (data.pickup_notes) {
                                    $.each(data.pickup_notes, function (index, value) {
                                        notes += '<span class="pncc_print" dnid="' + value + '">' + value + '</span><br>';
                                    });
                                }
                                $('#pncc_modal .modal-body').html('');
                                $('#pncc_modal').modal('show');
                                $('#pncc_modal .modal-body').html(notes);
                            } else if (data.status == 2) {
                                var notes = '<div>DNCC Number(s) :</div>';

                                if (data.delivery_notes) {
                                    $.each(data.delivery_notes, function (index, value) {
                                        notes += '<u><a href="javascript:void(0);" class="dncc_print" dnid="' + value + '">' + value + '</a></u><br>';
                                    });
                                }
                                $('#dncc_modal .modal-body').html('');
                                $('#dncc_modal').modal('show');
                                $('#dncc_modal .modal-body').html(notes);
                            } else if (data.status == 0) {
                                toastr.success(data.success, 'Success!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            } else {
                                toastr.error('Something went wrong!', 'Error!', {
                                    positionClass: 'toast-top-center',
                                    containerId: 'toast-top-center'
                                });
                            }
                        });
                }
            });

            $('body').on('click','a.dncc_print',function(){
                var id = parseInt($(this).attr('dnid'));
                printDNCC(id);
            });
            function printDNCC(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.dncc.print') !!}',
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

            function printSDN(id) {
                $.ajax({
                    url: '{!! route('admin.delivery.sdn.print') !!}',
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

            $('body').on('click','.printSDN',function () {
                var sdn = parseInt($(this).attr('data-sdn_id'));
                // console.log(sdn);
                printSDN(sdn);
            });
        });
    </script>
@endsection