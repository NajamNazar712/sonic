@extends('admin.layout.master')
@section('title','Petty Cash Statements')

@section('content')
    <h1 class="mb-1">
        Petty Cash Statements
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center" id="search_form">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-4 ">

                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_from" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_from" placeholder="Date (From)">
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="form-group input-group ml-1">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>

                            <input type="text" name="search_date_to" class="form-control pickadate bg-primary border-primary white rounded-right" id="search_date_to" placeholder="Date (To)">
                        </div>

                    </div>
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_zone" id="search_zone" class="form-control select2">
                                @foreach($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-4">
                        <div class="form-group input-group ">
                            <div class="input-group-prepend">
                            <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                <span class="la la-calendar-o"></span>
                            </span>
                            </div>
                            <input type="text" name="creation_date" class="form-control bg-primary border-primary white rounded-right" id="creation_date" placeholder="Creation Date" data-value="">
                        </div>

                    </div>
                    <div class="col-2">
                        <button type="button" id="search_filter_btn" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Search</button>
                    </div>
{{--                    <div class="col-2">--}}
{{--                        <button type="button" id="search_station" class="mr-1 mb-1 btn btn-outline-primary btn-min-width"><i class="la la-search"></i> Station Approved</button>--}}
{{--                    </div>--}}
                </div>
                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1"></th>
                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Statement No.</th>
                        <th class="border-primary border-darken-1">Origin Hub</th>
                        <th class="border-primary border-darken-1">Destination Hub</th>
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
                        <th class="border-primary border-darken-1">Tracking Number</th>
                        <th class="border-primary border-darken-1">Checked At</th>
                        <th class="border-primary border-darken-1">Checked By</th>
                        <th class="border-primary border-darken-1">SDN Update Log</th>
                        <th class="border-primary border-darken-1"></th>

                    </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>


    <div class="modal fade text-left" id="SDNLogModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="SDNLogModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h4 class="modal-title white">Petty Cash Statement SDN Log</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body  text-center">
                    <table class="table table-bordered datatable" id="sdn_log_datatable" style="z-index: 3;">
                        <thead>
                        <tr role="row" class="bg-primary white">
                            <th class="border-primary border-darken-1">S. No.</th>
                            <th class="border-primary border-darken-1">SDN ID</th>
                            <th class="border-primary border-darken-1">User</th>
                            <th class="border-primary border-darken-1">Updated At</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/additional-methods.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var hub_ids = [];
            var search_hub = $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
                width:'100%',
                allowClear:true
            });
            var search_zone = $('#search_zone').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Zone',
                width:'100%',
                allowClear:true
            });
            var creation_date = $('#creation_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#creation_date_root').css('top','40px');
                }
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
            $('#search_filter_btn').on('click',function (){
                table.draw();
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.petty_cash.statements.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];


                            head.push('S.No');
                            head.push('Statement No.');
                            head.push('Origin Hub');
                            head.push('Destination Hub');
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
                            head.push('Tracking Number');
                            head.push('Checked At');
                            head.push('Checked By');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.statement_id);
                                row.push(values.origin_hub_name);
                                row.push(values.destination_hub_name);
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
                                row.push(values.tracking_number);
                                row.push(values.checked_at);
                                row.push(values.checked_by);

                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );
            var selected_rows = [];
            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons:[
                @if (session('role_id') == 1 || in_array(190, session('permissions')))
                    {
                        className: 'btn btn-primary station',
                        text: 'Station Approved',
                        enabled: false,

                        action: function (e, dt, node, config) {
                            $('input:hidden[name=statement_ids]').val(selected_rows);

                            if(selected_rows.length === 0){
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                                return false;
                            }
                            else if(selected_rows !== ''){
                                swal({
                                    title: 'Are You Sure?',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function (confirm) {
                                    if (confirm) {
                                        $.ajax({
                                            url: '{!! route('admin.petty_cash.statements.station_operation_finance_approved') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'statement_ids': selected_rows,
                                                'action': 'station'
                                            }
                                        }).done(function(data){
                                            if(data.status == 0){
                                                table.rows().deselect();
                                                selected_rows = [];
                                                table.button('.finance').disable();
                                                table.button('.station').disable();
                                                table.button('.operation').disable();
                                                table.button('.check').disable();
                                                table.draw(true);
                                                table.columns.adjust().draw();
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else
                                            {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }

                                        });
                                    }
                                });

                            }else{
                                var error = 'Statement ID Not Found, Please Try again!';
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                            }
                        }
                    },
                        @endif

                        @if (session('role_id') == 1 || in_array(191, session('permissions')))
                    {
                        className: 'btn btn-primary operation',
                        text: 'Operation Approved',
                        enabled: false,
                        action: function (e, dt, node, config) {

                            $('input:hidden[name=statement_ids]').val(selected_rows);

                            if(selected_rows.length === 0){
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                                return false;
                            }
                            else if(selected_rows !== ''){
                                swal({
                                    title: 'Are You Sure?',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function (confirm) {
                                    if (confirm) {
                                        $.ajax({
                                            url: '{!! route('admin.petty_cash.statements.station_operation_finance_approved') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'statement_ids': selected_rows,
                                                'action': 'operation'
                                            }
                                        }).done(function(data){
                                            if(data.status == 0){
                                                table.rows().deselect();
                                                selected_rows = [];
                                                table.button('.finance').disable();
                                                table.button('.station').disable();
                                                table.button('.operation').disable();
                                                table.button('.check').disable();
                                                table.draw(true);
                                                table.columns.adjust().draw();
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else
                                            {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});

                                            }

                                        });
                                    }
                                });

                            }else{
                                var error = 'Statement ID Not Found, Please Try again!';
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                            }
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(173, session('permissions')))
                    {
                        className: 'btn btn-primary finance',
                        text: 'Finance Approved',
                        enabled: false,

                        action: function (e, dt, node, config) {
                            $('input:hidden[name=statement_ids]').val(selected_rows);

                            if(selected_rows.length === 0){
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                                return false;
                            }
                            else if(selected_rows !== ''){
                                swal({
                                    title: 'Are You Sure?',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function (confirm) {
                                    if (confirm) {
                                        $.ajax({
                                            url: '{!! route('admin.petty_cash.statements.station_operation_finance_approved') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'statement_ids': selected_rows,
                                                'action': 'finance'
                                            }
                                        }).done(function(data){
                                            if(data.status == 0){
                                                table.rows().deselect();
                                                selected_rows = [];
                                                table.button('.finance').disable();
                                                table.button('.station').disable();
                                                table.button('.operation').disable();
                                                table.button('.check').disable();
                                                table.draw(true);
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else
                                            {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }

                                        });
                                    }
                                });

                            }else{
                                var error = 'Statement ID Not Found, Please Try again!';
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                            }
                        }
                    },
                        @endif
                        @if (session('role_id') == 1 || in_array(473, session('permissions')))
                    {
                        className: 'btn btn-primary check',
                        text: 'Check',
                        enabled: false,

                        action: function (e, dt, node, config) {

                            if(selected_rows.length === 0){
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                                return false;
                            }
                            else if(selected_rows !== ''){
                                swal({
                                    title: 'Are You Sure?',
                                    icon: 'warning',
                                    buttons: {
                                        cancel: {
                                            text: 'No',
                                            value: null,
                                            visible: true,
                                            closeModal: true,
                                        },
                                        confirm: {
                                            text: 'Yes',
                                            value: true,
                                            visible: true,
                                            closeModal: true
                                        }
                                    },
                                    closeOnClickOutside: false,
                                    closeOnEsc: false,
                                    dangerMode: true
                                }).then(function (confirm) {
                                    if (confirm) {
                                        $.ajax({
                                            url: '{!! route('admin.petty_cash.statements.check') !!}',
                                            method: 'POST',
                                            data: {
                                                '_token': '{{ csrf_token() }}',
                                                'statement_ids': selected_rows,
                                            }
                                        }).done(function(data){
                                            if(data.status == 0){
                                                table.rows().deselect();
                                                selected_rows = [];
                                                table.button('.finance').disable();
                                                table.button('.station').disable();
                                                table.button('.operation').disable();
                                                table.button('.check').disable();
                                                table.draw(true);
                                                toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                            }
                                            else
                                            {
                                                toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                            }

                                        });
                                    }
                                });

                            }else{
                                var error = 'Statement ID Not Found, Please Try again!';
                                toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                table.button('.finance').disable();
                                table.button('.station').disable();
                                table.button('.operation').disable();
                                table.button('.check').disable();
                            }
                        }
                    },
                    @endif
                    {
                        className: 'btn btn-primary',
                        text: '<i class="la la-plus"></i> Make Petty Cash Statements',
                        action: function (e, dt, node, config) {
                            window.location = '{{ route('admin.petty_cash.make.index')  }}'
                        }
                    },{
                        extend: 'selectAll',
                        text: 'Select All',
                        className: 'select_all',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.select();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index === -1) {
                                        selected_rows.push(id);
                                    }

                                    table.button('.finance').enable();
                                    table.button('.station').enable();
                                    table.button('.operation').enable();
                                    table.button('.check').enable();
                                }
                            });
                        }
                    }, {
                        extend: 'selectNone',
                        text: 'Select None',
                        className: 'select_none',
                        action : function(e) {
                            e.preventDefault();

                            table.rows().nodes().each(function(index) {
                                var row = table.row(index);

                                if ($(row.node().firstChild).hasClass('select-checkbox')) {
                                    row.deselect();

                                    id = parseInt(row.id());

                                    var index = $.inArray(id, selected_rows);

                                    if (index !== -1) {
                                        selected_rows.splice(index, 1);
                                    }

                                    if (selected_rows.length == 0) {
                                        table.button('.finance').disable();
                                        table.button('.station').disable();
                                        table.button('.operation').disable();
                                        table.button('.check').disable();
                                    }
                                }
                            });
                        }
                    },
                    {
                    extend: 'excel',
                    title: 'Petty Cash Statements',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                },'reset'],
                select: {
                    info: false,
                    style: 'multi',
                    selector: 'td.select-checkbox',
                    className: 'selected bg-primary bg-lighten-5 primary'
                },
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                {{--ajax: '{{ route('admin.petty_cash.statements.list') }}',--}}
                ajax: {
                    url: '{{ route('admin.petty_cash.statements.list') }}',
                    data: function (d) {
                        d.search_hub = $('#search_hub').val();
                        d.search_zone = $('#search_zone').val();
                        d.search_creation_date = $('input[name="creation_date_formatted"]').val();
                        d.search_date_from = $('input[name="search_date_from_formatted"]').val();
                        d.search_date_to = $('input[name="search_date_to_formatted"]').val();
                    }
                },
                rowId: 'statement_id',
                order: [9, 'desc'],
                columns: [
                    {data: 'statement_id', orderable: false, searchable: false, class: 'text-center align-middle select select-checkbox p-1', targets: 0, render: function (data, type, row) {return '';}},
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'statement_link', name: 'petty_cash_statements.id', class: 'align-middle statement_link'},
                    {data: 'origin_hub_name', name: 'o.name', class: 'align-middle origin_hub_name'},
                    {data: 'destination_hub_name', name: 'd.name', class: 'align-middle destination_hub_name'},
                    {data: 'reference_no', name: 'petty_cash_statements.reference_no', class: 'align-middle reference_no'},
                    {data: 'date', name: 'date', class: 'align-middle date', orderable:false},
                    {data: 'total_amount', name: 'petty_cash_statements.total_amount', class: 'align-middle total_amount'},
                    {data: 'created_by', name: 'cb.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'petty_cash_statements.created_at', class: 'align-middle created_at'},
                    {data: 'station_approved_by', name: 'sab.name', class: 'align-middle station_approved_by'},
                    {data: 'station_approved_at', name: 'petty_cash_statements.station_approved_at', class: 'align-middle station_approved_at'},
                    {data: 'operation_approved_by', name: 'oab.name', class: 'align-middle operation_approved_by'},
                    {data: 'operation_approved_at', name: 'petty_cash_statements.operation_approved_at', class: 'align-middle operation_approved_at'},
                    {data: 'status', name: 'petty_cash_statements.status', class: 'align-middle status'},
                    {data: 'tracking_number_link', name: 'shipments.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'checked_at', name: 'petty_cash_statements.checked_at', class: 'align-middle checked_at'},
                    {data: 'checked_by', name: 'petty_cash_statements.checked_by', class: 'align-middle checked_by'},
                    {data: 'sdn_update_logs', name: '', class: 'text-center align-middle sdn_update_logs', orderable: false, searchable: false},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();

                    $('td:eq(1)', row).html(index + 1 + info.page * info.length);
                    if ($.inArray(data.id, selected_rows) !== -1) {
                        table.row(row).select();
                    }


                },
                initComplete: function() {
                    var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Created</option>' +
                        '<option value="1">Station Approved</option>' +
                        '<option value="2">Operation Approved</option>' +
                        '<option value="7">Received Statement</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.select') || $(header).is('.action') || $(header).is('.serial_number') || $(header).is('.rate_status') || $(header).is('.duplicate') || $(header).is('.sdn_update_logs')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.status')){
                            $(drop_select).appendTo($(search))
                                .on( 'change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                } ).wrap(td);
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

                    $("#status_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Status",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
                var id = parseInt($(this).parent('tr').attr('id'));

                var index = $.inArray(id, selected_rows);

                if (index === -1) {
                    selected_rows.push(id);
                }
                else {
                    selected_rows.splice(index, 1);
                }
                if (selected_rows.length > 0) {
                    table.button('.finance').enable();
                    table.button('.station').enable();
                    table.button('.operation').enable();
                    table.button('.check').enable();
                }
                else {
                    table.button('.finance').disable();
                    table.button('.station').disable();
                    table.button('.operation').disable();
                    table.button('.check').disable();
                }
            });

            var sdn_log_datatable = $('#sdn_log_datatable').DataTable({
                dom: 'ltipr',
                scrollX: false,
                autoWidth : false,
                paging:false,
                columns: [
                    {name: 'serial_number', orderable: false, searchable: false, class: 'align-middle serial_number'},
                    {name: 'sdn_id', class: 'align-middle', orderable: false},
                    {name: 'admin', class: 'align-middle', orderable: false},
                    {name: 'timestamp', class: 'align-middle', orderable: false},
                ],
                rowCallback: function(row, data, index) {
                    var info = sdn_log_datatable.page.info();

                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);

                },
                initComplete: function() {
                    this.api().table().columns.adjust();
                }
            });

            $('body').on('click','.sdn_update_logs button.sdn_logs',function(){
                var id = $(this).parents('tr').attr('id');
                $.ajax({
                    url: '{!! route('admin.petty_cash.sdn_logs') !!}',
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'statement_id': id
                    }
                }).done(function(data){
                    if(data.status == 0){
                        var sdn_logs = data.logs;
                        $.each(sdn_logs, function (index, value) {
                            console.log(value);
                            sdn_log_datatable.row.add([0, value.sdn, value.admin, value.timestamp]);
                            sdn_log_datatable.draw(true);
                        });

                        $("#SDNLogModal").modal('show');
                    }else{
                        toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                    }
                });
            });

            $('#SDNLogModal').on('hide.bs.modal', function (e) {
                sdn_log_datatable.clear().draw();
            });

            $('body').on('click','button.approve',function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
                    swal({
                        title: 'Are You Sure?',
                        text: 'Select Yes to approve petty cash statement!',
                        icon: 'warning',
                        buttons: {
                            cancel: {
                                text: 'No',
                                value: null,
                                visible: true,
                                closeModal: true,
                            },
                            confirm: {
                                text: 'Yes',
                                value: true,
                                visible: true,
                                closeModal: true
                            }
                        },
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true
                    }).then(function (confirm) {
                        if (confirm) {
                            $.ajax({
                                url: '{!! route('admin.petty_cash.statements.approve') !!}',
                                method: 'POST',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'statement_id': id
                                }
                            }).done(function(data){
                                if(data.status){
                                    table.draw(true);
                                    toastr.success(data.success, 'Success!', {positionClass: 'toast-bottom-center', containerId: 'toast-bottom-center'});
                                }else{
                                    toastr.error(data.error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                                }
                            });
                        }
                    });
                }else{
                    var error = 'Statement ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
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
        $('#datatable tbody').on('click', 'tr td.select-checkbox', function() {
            var id = parseInt($(this).parent('tr').attr('statement_id'));
            console.log(statement_id);
            var index = $.inArray(statement_id, selected_rows);

            if (index === -1) {
                selected_rows.push(statement_id);
            }
            else {
                selected_rows.splice(index, 1);
            }
        });
    </script>
@endsection