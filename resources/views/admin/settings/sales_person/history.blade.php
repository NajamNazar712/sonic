
@extends('admin.layout.master')
@section('title','Sales Person Targets History')

@section('content')
    <h1 class="mb-1">
        Sales Person Targets History
    </h1>

    <div class="card">
        <div class="card-content" aria-expanded="true">
            <div class="card-body">
                @include('admin.inc.messages')

                <table class="table table-bordered datatable" id="datatable" style="z-index: 3;">
                    <thead>
                    <tr role="row" class="bg-primary white">

                        <th class="border-primary border-darken-1">S. No.</th>
                        <th class="border-primary border-darken-1">Sales Person</th>
                        <th class="border-primary border-darken-1">Segment</th>
                        <th class="border-primary border-darken-1">Start Date</th>
                        <th class="border-primary border-darken-1">End Date</th>
                        <th class="border-primary border-darken-1">Target Shipments/Day</th>
                        <th class="border-primary border-darken-1">Target Shipments/Month</th>
                        <th class="border-primary border-darken-1">Average Revenue</th>
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

@endsection

@section('js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/datatable_buttons.js')}}" type="text/javascript"></script>

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
                        url: '{{ route('admin.settings.sales.history.list') }}',
                        data: params,
                        success: function (result) {
                            head = [];

                            head.push('S.No');
                            head.push('Sales Person');
                            head.push('Segment');
                            head.push('Start Date');
                            head.push('End Date');
                            head.push('Target Shipments/Day');
                            head.push('Target Shipments/Month');
                            head.push('Average Revenue');

                            $.each(result.data, function(index, values) {
                                row = [];


                                row.push(index + 1);
                                row.push(values.sales_person);
                                row.push(values.segments);
                                row.push(values.start_date);
                                row.push(values.end_date);
                                row.push(values.target_days);
                                row.push(values.target_month);
                                
                                row.push(values.average_revenue);

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
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Sales Person Targets History',
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
                ajax: '{{ route('admin.settings.sales.history.list') }}',
                // order: [[1, 'desc']],
                columns: [
                    {orderable: false, searchable: false, name: 'serial_number', class: 'align-middle serial_number', targets: 0, render: function (data, type, row) {return '';}},
                    {data: 'sales_person', name:'a.name', class: 'align-middle sales_person'},
                    {data: 'segments', name:'segments', class: 'align-middle segments'},
                    {data: 'start_date', name: 'sale_person_target_logs.start_date', class: 'align-middle start_date'},
                    {data: 'end_date', name: 'sale_person_target_logs.end_date', class: 'align-middle end_date'},
                    {data: 'target_days', name: 'sale_person_target_logs.target_days', class: 'align-middle target_days'},
                    {data: 'target_month', name: 'sale_person_target_logs.target_month', class: 'align-middle target_month'},
                    {data: 'average_revenue', name: 'sale_person_target_logs.average_revenue', class: 'align-middle average_revenue'}

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
                    this.api().columns().every(function(column_id) {
                        var column = this;
                        var header = column.header();


                        if ($(header).is('.serial_number') || $(header).is('.status')) {
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



        });
    </script>
@endsection