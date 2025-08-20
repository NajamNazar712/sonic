@extends('admin.layout.master')

@section('title', 'Parent Product Percentage Log')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <h1 class="mb-1">
                    Parent Product Percentage Log
                </h1>

                <div class="card">
                    <div class="card-content" aria-expanded="true">
                        <div class="card-body">
                            @include('client.inc.messages')

                            <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                                <thead>
                                    <tr class="bg-primary white">
                                        <th class="border-primary border-darken-1">S. No</th>
                                        <th class="border-primary border-darken-1">Name</th>
                                        <th class="border-primary border-darken-1">Field Changed</th>
                                        <th class="border-primary border-darken-1">Old Value</th>
                                        <th class="border-primary border-darken-1">New Value</th>
                                        <th class="border-primary border-darken-1">Removed Shippers</th>
                                        <th class="border-primary border-darken-1">Changed By</th>
                                        <th class="border-primary border-darken-1">Changed At</th>
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
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/selectize.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/pickers/daterange/daterange.min.css')}}">

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>
    <script src="{{asset('/app-assets/vendors/js/forms/extended/inputmask/jquery.inputmask.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/selectize.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}" type="text/javascript"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                if ( this.context.length ) {
                    blockPagePermanently();
                    body = [];
                    var params = table.ajax.params();
                    params.start = 0;
                    params.length = -1;
                    params.excel = true;
                    var jsonResult = $.ajax({
                        url: '{{ route('admin.settings.product_tax_logs.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];
                            footer = [];

                            head.push('S. No');
                            head.push('Parent Product Name');
                            head.push('Field Changed');
                            head.push('Old Value');
                            head.push('New Value');
                            head.push('Removed Shippers');
                            head.push('Changed By');
                            head.push('Changed At');

                            $.each(result.data, function(index, values) {
                                row = [];
                                row.push(index + 1);
                                row.push(values.parent_product_name);
                                row.push(values.field_changed);
                                row.push(values.old_value);
                                row.push(values.new_value);
                                row.push(values.shippers);
                                row.push(values.updated_by_name);
                                row.push(values.created_at);
                                body.push(row);

                            });

                        },
                        async: false
                    });
                    UnblockPagePermanently();

                    return {body: body, header: head};
                }
            });

            var table = $('#datatable').DataTable({
                dom: '<"d-inline-block"l><"pull-right"B>tipr',
                order: [[6, "desc" ]],
                scrollX: false, scrollY: '500px',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-primary',
                        title: 'Parent Product Percentage Log',
                        text: '<i class="la la-file-excel-o"></i> Excel',

                    }
                ],
                lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, 'All']],
                pageLength: 50,
                pagingType: 'full_numbers',
                processing: true,
                serverSide: true,
                language: {
                    processing: data_table_loader
                },
                ajax:{
                    url: '{{ route('admin.settings.product_tax_logs.list') }}',
                },
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'parent_product_name', name: 'pp.name', class: 'align-middle parent_product_name'},
                    {data: 'field_changed', name: 'pl.field_changed', class: 'align-middle field_changed'},
                    {data: 'old_value', name: 'pl.old_value', class: 'align-middle old_value'},
                    {data: 'new_value', name: 'pl.new_value', class: 'align-middle new_value'},
                    {data: 'shippers', name: 'pl.text', class: 'align-middle shippers'},
                    {data: 'updated_by_name', name: 'u.name as updated_by_name', class: 'align-middle updated_by_name'},
                    {data: 'created_at', name: 'pl.created_at', class: 'align-middle created_at'},
                ],
                rowCallback: function(row, data, index) {
                    var info = table.page.info();
                    $('td:eq(0)', row).html(index + 1 + info.page * info.length);
                },
                initComplete: function() {
                    // var search = $('<tr role="row" class="bg-primary bg-lighten-1 search"></tr>').appendTo(this.api().table().header());

                    // var td = '<td style="padding:5px;" class="border-primary border-lighten-2"><fieldset class="form-group m-0 position-relative has-icon-right"></fieldset></td>';
                    // var input = '<input type="text" class="form-control form-control-sm input-sm primary">';
                    // var icon = '<div class="form-control-position primary"><i class="la la-search"></i></div>';

                    // this.api().columns().every(function(column_id) {
                    //     var column = this;
                    //     var header = column.header();

                    //     // if ($(header).is('.serial_number')) {
                    //     //     $(td).appendTo($(search));
                    //     // }
                    //     // else {
                    //     //     var current = $(input).appendTo($(search)).on('change', function() {
                    //     //         column.search($(this).val(), false, false, true).draw();
                    //     //     }).wrap(td).after(icon);

                    //     //     if (column.search()) {
                    //     //         current.val(column.search());
                    //     //     }

                    //     // }
                    // });

                    this.api().table().columns.adjust();
                }
            });

        });

    </script>

@endsection

