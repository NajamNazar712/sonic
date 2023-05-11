
@extends('admin.layout.master')
@section('title','Intercept Rebook History')

@section('content')
    <h1 class="mb-1">
        Intercept Rebook History
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')
                <div class="row mb-2 justify-content-center">
                     <div class="col-4 ">
                         <div class="form-group input-group">
                             <div class="input-group-prepend">
                             <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                 <span class="la la-calendar-o"></span>
                             </span>
                             </div>
                             <input type="text" name="from_date" class="form-control bg-primary border-primary white rounded-right" id="from_date" placeholder="Date From">
                         </div>
                     </div>
                     <div class="col-4">
                         <div class="form-group input-group">
                             <div class="input-group-prepend">
                             <span class="input-group-text bg-primary bg-darken-2 border-primary white rounded-left">
                                 <span class="la la-calendar-o"></span>
                             </span>
                             </div>
                             <input type="text" name="to_date" class="form-control bg-primary border-primary white rounded-right" id="to_date" placeholder="Date To">
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
                        <th class="border-primary border-darken-1">Tracking No.</th>
                        <th class="border-primary border-darken-1">Shipper</th>
                        <th class="border-primary border-darken-1">Changed On</th>
                        <th class="border-primary border-darken-1">Origin</th>
                        <th class="border-primary border-darken-1">Agent</th>
                        <th class="border-primary border-darken-1">Old Consignee City</th>
                        <th class="border-primary border-darken-1">Old Consignee Area</th>
                        <th class="border-primary border-darken-1">Old Consignee Name</th>
                        <th class="border-primary border-darken-1">Old Consignee Address</th>
                        <th class="border-primary border-darken-1">Old Consignee Phone 1</th>
                        <th class="border-primary border-darken-1">Old Consignee Phone 2</th>
                        <th class="border-primary border-darken-1">Old Consignee Email</th>
                        <th class="border-primary border-darken-1">Old Amount</th>
                        <th class="border-primary border-darken-1">New Consignee City</th>
                        <th class="border-primary border-darken-1">New Consignee Area</th>
                        <th class="border-primary border-darken-1">New Consignee Name</th>
                        <th class="border-primary border-darken-1">New Consignee Address</th>
                        <th class="border-primary border-darken-1">New Consignee Phone 1</th>
                        <th class="border-primary border-darken-1">New Consignee Phone 2</th>
                        <th class="border-primary border-darken-1">New Consignee Email</th>
                        <th class="border-primary border-darken-1">New Amount</th>
                        <th class="border-primary border-darken-1">Intercept Type </th>
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
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {


            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.delivery.intercept.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Tracking .No');
                            head.push('Shipper');
                            head.push('Changed On');
                            head.push('Origin');
                            head.push('Agent');
                            head.push('Old Consignee City');
                            head.push('Old Consignee Area');
                            head.push('Old Consignee Name');
                            head.push('Old Consignee Address');
                            head.push('Old Consignee Phone 1');
                            head.push('Old Consignee Phone 2');
                            head.push('Old Consignee Email');
                            head.push('Old Amount');
                            head.push('New Consignee City');
                            head.push('New Consignee Area');
                            head.push('New Consignee Name');
                            head.push('New Consignee Address');
                            head.push('New Consignee Phone 1');
                            head.push('New Consignee Phone 2');
                            head.push('New Consignee Email');
                            head.push('New Amount');
                            head.push('Intercept Type');
                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.tracking_number);
                                row.push(values.shipper);
                                row.push(values.created_at);
                                row.push(values.origin);
                                row.push(values.agent);
                                row.push(values.old_consignee_city);
                                row.push(values.old_consignee_area);
                                row.push(values.old_consignee_name);
                                row.push(values.old_consignee_address);
                                row.push(values.old_consignee_phone_number_1);
                                row.push(values.old_consignee_phone_number_2);
                                row.push(values.old_consignee_email);
                                row.push(values.old_amount);
                                row.push(values.new_consignee_city);
                                row.push(values.new_consignee_area);
                                row.push(values.new_consignee_name);
                                row.push(values.new_consignee_address);
                                row.push(values.new_consignee_phone_number_1);
                                row.push(values.new_consignee_phone_number_2);
                                row.push(values.new_consignee_email);
                                row.push(values.new_amount);
                                row.push(values.type);
                                body.push(row);
                            });
                        },
                        async: false
                    });

                    return {body: body, header: head};
                }
            } );

            var from_date = $('#from_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 00:00:00',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#from_date_root').css('top','40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#to_date').pickadate('picker').set('min', $('#from_date').pickadate('picker').get('select'));
                    }
                }
            });
            var to_date = $('#to_date').pickadate({
                firstDay: 1,
                clear: 'Clear',
                format:'dd mmmm, yyyy',
                selectYears: true,
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd 23:59:59',
                hiddenSuffix: '_formatted',
                onOpen: function() {
                    $('#to_date_root').css('top', '40px');
                },
                onSet: function(context) {
                    if (context.select) {
                        $('#from_date').pickadate('picker').set('max', $('#to_date').pickadate('picker').get('select'));
                    }
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Intercept Rebook History',
                        className: 'btn btn-primary',
                        text: '<i class="la la-file-excel-o"></i> Excel',
                    },'reset',
                ],
                scrollX: true, scrollY: '500px',
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                language: {
                    processing: data_table_loader
                },
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.delivery.intercept.history.list') }}',
                    data: function (d) {
                        d.search_from = $('input[name="from_date_formatted"]').val();
                        d.search_to = $('input[name="to_date_formatted"]').val();
                    }
                },
                order: [[3, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'tracking_number_link', name: 's.tracking_number', class: 'align-middle tracking_number_link'},
                    {data: 'shipper', name: 'u.name', class: 'align-middle shipper'},
                    {data: 'created_at', name: 'intercept_re_book_request_histories.created_at', class: 'align-middle created_at'},
                    {data: 'origin', name: 'oc.name', class: 'align-middle origin'},
                    {data: 'agent', name: 'agent.name', class: 'align-middle agent'},
                    {data: 'old_consignee_city', name: 'odc.name', class: 'align-middle old_consignee_city'},
                    {data: 'old_consignee_area', name: 'cas2.name', class: 'align-middle old_consignee_area'},
                    {data: 'old_consignee_name', name: 'intercept_re_book_request_histories.old_consignee_name', class: 'align-middle old_consignee_name'},
                    {data: 'old_consignee_address', name: 'intercept_re_book_request_histories.old_consignee_address', class: 'align-middle old_consignee_address'},
                    {data: 'old_consignee_phone_number_1', name: 'intercept_re_book_request_histories.old_consignee_phone_number_1', class: 'align-middle old_consignee_phone_number_1'},
                    {data: 'old_consignee_phone_number_2', name: 'intercept_re_book_request_histories.old_consignee_phone_number_2', class: 'align-middle old_consignee_phone_number_2'},
                    {data: 'old_consignee_email', name: 'intercept_re_book_request_histories.old_consignee_email', class: 'align-middle old_consignee_email'},
                    {data: 'old_amount', name: 'old_amount', class: 'align-middle old_amount'},
                    {data: 'new_consignee_city', name: 'nc.name', class: 'align-middle new_consignee_city'},
                    {data: 'new_consignee_area', name: 'cas.name', class: 'align-middle new_consignee_area'},
                    {data: 'new_consignee_name', name: 'intercept_re_book_request_histories.new_consignee_name', class: 'align-middle new_consignee_name'},
                    {data: 'new_consignee_address', name: 'intercept_re_book_request_histories.new_consignee_address', class: 'align-middle new_consignee_address'},
                    {data: 'new_consignee_phone_number_1', name: 'intercept_re_book_request_histories.new_consignee_phone_number_1', class: 'align-middle new_consignee_phone_number_1'},
                    {data: 'new_consignee_phone_number_2', name: 'intercept_re_book_request_histories.new_consignee_phone_number_2', class: 'align-middle new_consignee_phone_number_2'},
                    {data: 'new_consignee_email', name: 'intercept_re_book_request_histories.new_consignee_email', class: 'align-middle new_consignee_email'},
                    {data: 'new_amount', name: 'new_amount', class: 'align-middle new_amount'},
                    {data: 'type', name: 'intercept_re_book_request_histories.intercept_type', class: 'align-middle type'},
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
                    var consignee_drop_select = '<select name="service_select" id="consignee_select" class="select2 form-control">' +
                        '<option value="2">Same Consignee</option>'+
                        '<option value="1">Different Consignee</option>'+
                        '</select>';
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.action') || $(header).is('.serial_number') || $(header).is('.status')) {
                            $(td).appendTo($(search));
                        }else if($(header).is('.type') ) {
                            $(consignee_drop_select).appendTo($(search))
                                .on('change', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                }).wrap(td);
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
                    $("#consignee_select").prepend('<option value="" selected></option>').select2({
                        placeholder: "Select Intercept Type",
                        width:'100%',
                        containerCssClass: 'select-xs',
                        dropdownCssClass: 'form-control-sm p-0'
                    });
                    this.api().table().columns.adjust();
                }
            });

            $('#search_filter_btn').on('click',function () {
               table.draw();
            });

        });
    </script>
@endsection