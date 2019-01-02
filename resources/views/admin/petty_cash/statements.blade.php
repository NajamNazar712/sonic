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
                <div class="row mb-2 justify-content-center">
                    <div class="col-4">
                        <fieldset class="form-group">
                            <select name="search_hub" id="search_hub" class="form-control select2">
                                @foreach($hubs as $city)
                                    <option value="{{$city->id}}">{{$city->name}}</option>
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
                            <input type="text" name="creation_date" class="form-control bg-primary border-primary white rounded-right" id="creation_date" placeholder="Transit Date" data-value="">
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
                        <th class="border-primary border-darken-1">Date</th>
                        <th class="border-primary border-darken-1">Created By</th>
                        <th class="border-primary border-darken-1">Created At</th>
                        <th class="border-primary border-darken-1">Station Approved By</th>
                        <th class="border-primary border-darken-1">Station Approved At</th>
                        <th class="border-primary border-darken-1">Operation Approved By</th>
                        <th class="border-primary border-darken-1">Operation Approved At</th>
                        <th class="border-primary border-darken-1">Finance Approved By</th>
                        <th class="border-primary border-darken-1">Finance Approved At</th>
                        <th class="border-primary border-darken-1">Status</th>
                        <th class="border-primary border-darken-1"></th>

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

    <script type="text/javascript">
        $(document).ready(function () {
            var search_hub = $('#search_hub').prepend('<option value="" selected="selected"></option>').select2({
                placeholder:'Search Hub',
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
                    $('#transit_date_root').css('top','40px');
                }
            });
            $('#search_filter_btn').on('click',function () {
                table.draw();
            });
            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];

                    var jsonResult = $.ajax({
                        url: '{{ route('admin.petty_cash.statements.list') }}',
                        data: {
                            'page': 'all',
                        },
                        success: function (result) {
                            head = [];
                            head.push('S.No');
                            head.push('Statement No.');
                            head.push('Hub');
                            head.push('Statement Reference No.');
                            head.push('Date');
                            head.push('Created By');
                            head.push('Created At');
                            head.push('Station Approved By');
                            head.push('Station Approved At');
                            head.push('Operation Approved By');
                            head.push('Operation Approved At');
                            head.push('Finance Approved By');
                            head.push('Finance Approved At');
                            head.push('Status');


                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.statement_id);
                                row.push(values.hub_name);
                                row.push(values.reference_no);
                                row.push(values.date);
                                row.push(values.created_by);
                                row.push(values.created_at);
                                row.push(values.station_approved_by);
                                row.push(values.station_approved_at);
                                row.push(values.operation_approved_by);
                                row.push(values.operation_approved_at);
                                row.push(values.finance_approved_by);
                                row.push(values.finance_approved_at);
                                row.push(values.status);

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
                buttons:[{
                    extend: 'excel',
                    title: 'Petty Cash Statements',
                    className: 'btn btn-primary',
                    text: '<i class="la la-file-excel-o"></i> Excel',
                }],
                scrollX: true, scrollY: '350px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                {{--ajax: '{{ route('admin.petty_cash.statements.list') }}',--}}
                ajax: {
                    url: '{{ route('admin.petty_cash.statements.list') }}',
                    data: function (d) {
                        d.search_hub = $('#search_hub').val();
                        d.search_creation_date = $('input[name="creation_date_formatted"]').val();
                    }
                },
                rowId: 'statement_id',
                order: [1, 'asc'],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'statement_id', name: 'petty_cash_statements.id', class: 'align-middle statement_id'},
                    {data: 'hub_name', name: 'h.name', class: 'align-middle hub_name'},
                    {data: 'reference_no', name: 'petty_cash_statements.reference_no', class: 'align-middle reference_no'},
                    {data: 'date', name: 'date', class: 'align-middle date'},
                    {data: 'created_by', name: 'cb.name', class: 'align-middle created_by'},
                    {data: 'created_at', name: 'petty_cash_statements.created_at', class: 'align-middle created_at'},
                    {data: 'station_approved_by', name: 'sab.name', class: 'align-middle station_approved_by'},
                    {data: 'station_approved_at', name: 'petty_cash_statements.station_approved_at', class: 'align-middle station_approved_at'},
                    {data: 'operation_approved_by', name: 'oab.name', class: 'align-middle operation_approved_by'},
                    {data: 'operation_approved_at', name: 'petty_cash_statements.operation_approved_at', class: 'align-middle operation_approved_at'},
                    {data: 'finance_approved_by', name: 'fab.name', class: 'align-middle finance_approved_by'},
                    {data: 'finance_approved_at', name: 'petty_cash_statements.finance_approved_at', class: 'align-middle finance_approved_at'},
                    {data: 'status', name: 'petty_cash_statements.status', class: 'align-middle status'},
                    {data: 'action', name: 'action', class: 'text-center align-middle action p-1', orderable: false, searchable: false}

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
                    var drop_select = '<select name="status_select" id="status_select" class="select2 form-control">' +
                        '<option value="0">Created</option>' +
                        '<option value="1">Station Approved</option>' +
                        '<option value="2">Operation Approved</option>' +
                        '<option value="3">Finance Approved</option>' +
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();

                        if ($(header).is('.serial_number') ||  $(header).is('.action') ) {
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
            $('body').on('click','button.approve',function () {
                var id = $(this).parents('tr').attr('id');
                if(id){
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

                        }
                    });
                }else{
                    var error = 'Statement ID Not Found, Please Try again!';
                    toastr.error(error, 'Error!', {positionClass: 'toast-top-center', containerId: 'toast-top-center'});
                }
            });


        });
    </script>
@endsection